<?php
  include_once('DB_interface.php');

  class Database_Mongo implements DBI{
 
    private $conn,$db,$col;
    private $conn_slave,$db_slave,$col_slave;
    private $dictionary,$translate,$dictionaryr,$translater;

    function __construct($host,$host_slave=''){
	$this->conn = new Mongo($host);
	if($host_slave){
        	$this->conn_slave = new Mongo($host_slave);
	}
      $dictionary[0] = 'ingameItem';      $translate[0] = 'iI';
      $dictionary[1] = 'items';           $translate[1] = 'i';
      $dictionary[2] = 'globalItemId';    $translate[2] = 'gII';
      $dictionary[3] = 'positionX';       $translate[3] = 'pX';
      $dictionary[4] = 'positionY';       $translate[4] = 'pY';
      $dictionary[5] = 'data';            $translate[5] = 'da';
      $dictionary[6] = 'streetItems';     $translate[6] = 'sI';
      $dictionary[7] = 'inventory';       $translate[7] = 'in';
      $dictionary[8] = 'shopName';        $translate[8] = 'sN';
      $dictionary[9] = 'floors';          $translate[9] = 'fl';
      $dictionary[10] = 'employees';      $translate[10] = 'em';
      $dictionary[11] = 'clothes';        $translate[11] = 'cl';
      $dictionary[12] = 'task';           $translate[12] = 'ta';
      $dictionary[13] = 'notify';         $translate[13] = 'no';
      $dictionary[14] = 'lifetime';       $translate[14] = 'lt';
      $dictionary[15] = 'timeflag';       $translate[15] = 'tf';
      $dictionary[16] = 'shop';           $translate[16] = 'sh';
      $dictionary[17] = 'userInfo';       $translate[17] = 'uI';
      $dictionary[18] = 'token';          $translate[18] = 'to';
      $dictionary[19] = 'cash';           $translate[19] = 'ca';
      $dictionary[20] = 'level';          $translate[20] = 'lv';
      $dictionary[21] = 'like';           $translate[21] = 'li';
      $dictionary[22] = 'lastaccess';     $translate[22] = 'la';
      $dictionary[23] = 'playcount';      $translate[23] = 'pc';
      $dictionary[24] = 'tutorial';       $translate[24] = 'tu';
      $dictionary[25] = 'valid';          $translate[25] = 'va';
// inbox
      $dictionary[26] = 'mails';          $translate[26] = 'ma';
      $dictionary[27] = 'fromUid';        $translate[27] = 'fU';
      $dictionary[28] = 'fromUserName';   $translate[28] = 'fUN';
      $dictionary[29] = 'time';           $translate[29] = 'ti';
      $dictionary[30] = 'title';          $translate[30] = 'tit';
      $dictionary[31] = 'message';        $translate[31] = 'msg';
      $dictionary[32] = 'attachment';     $translate[32] = 'at';
      $dictionary[33] = 'coin';           $translate[33] = 'co';
      $dictionary[34] = 'read';           $translate[34] = 're';

      //$dictionary[0] = '/\b\b/ie';            $translate[0] = '';
      
      $this->dictionary = $dictionary;
      $this->translate = $translate;
      foreach($this->dictionary as &$d){
	$this->translater[] = $d;
        $d = '/\b'.$d.'\b/ie';
      }
      foreach($translate as $t){
        $this->dictionaryr[] = '/\b'.$t.'\b/ie';
      }
    }

    function connect($dbname){
      $this->db = $this->conn->selectDB($dbname);
      if($this->conn_slave){
        $this->db_slave = $this->conn_slave->selectDB($dbname);
      }
    }

    function close(){

    }
  
    function insert(&$data,$safe_insert){
      $id = $data['userId'];
      $result = $this->col->insert($data,$safe_insert);
      if($result['ok']){
        // cache user
        knightlover::cache()->set($id, $data, knightlover::conf()->cache['keeptime'], true);
        return true;
      }
      else{
        return false;
      }
    }

    function getCollection($name){
      $this->col = $this->db->selectCollection($name);
      if($this->db_slave){
        $this->col_slave = $this->db_slave->selectCollection($name);
      }
      return $this->col;
    }


    /*
     *
     *  mixed function findOne($filter)
     *  
     */
    function findOne($filter){
      // search cache
      $id = $filter['userId'];
      //$item = knightlover::cache()->get($id);
      if($this->col_slave){
        $item = $this->col_slave->findOne($filter);
      }
      else{
        $item =  $this->col->findOne($filter);
      }
      if($item){
        $this->decompress($item);
      }
      return $item;
    }

    function update($filter,&$update,$options){
      $update['$set'] = $this->compress($update['$set']);
      $id = $filter['userId'];
      $result = $this->col->update(
        $filter,
        $update,
        $options
        );
      return $result;
    }

    function remove($filter,$options){
      $id = $filter['userId'];
      $this->col->remove($filter,$options);
      knightlover::cache()->flush('fb_'.$id);
    }

    function lasterror(){
      return $this->db->lastError();
    }

    function compress(&$data){
      $tmp = @json_encode($data); //TODO @ used to hide warning for json_encode when flash passed NaN value cause amfphp to break 
      $tmp =  preg_replace($this->dictionary, $this->translate, $tmp); 
      $tmp = json_decode($tmp);
      if(isset($data['shop'])){
        $tmp->sh->sN = $data['shop']->shopName;
      }
      return $tmp;
    }

    function decompress(&$data){
      $tmp = @json_encode($data); //TODO @ used to hide warning for json_encode when flash passed NaN value cause amfphp to break
      $tmp = @preg_replace($this->dictionaryr,$this->translater, $tmp);
      $tmp = json_decode($tmp,true);
      if(isset($data['sh'])){
        $tmp['shop']['shopName'] = $data['sh']['sN'];
      }
      $data = $tmp;
    }

  }

?>
