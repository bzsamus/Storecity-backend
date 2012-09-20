<?php

class systemInfo{

  var $maxcoin = 6;

  function ulevel($lv){
    $ulevel = knightlover::cache()->get('ulevel');
    if(!$ulevel){
      $rs = knightlover::db()->getCollection('ulevel')->find();
      while($rs->hasNext()){
          $item = $rs->getNext();
          unset($item['_id']);
          foreach($item as $i=>$it){
            $item[$i] = intval($item[$i]);
          }
          $ulevel[$item['level']] = $item;
      }
      knightlover::cache()->set('ulevel',$ulevel,_CACHE_TIME_LONG,true);
    }
    return $ulevel[$lv];
  }


  function daynight($time){
    $t = date('g',$time);
    if(intval($t) > 5 && intval($t) < 12){
      return 'n';
    }
    else{
      return 'd';
    }
  }


/**
 *
 * users can shake the trees outside of the shop and sometimes coin will drop out
 * this function will track the number of coins available for an user in certain shop
 *
 **/

  function getCoins($userId,$shopUserId){
    $maxcoin = 6;
    $key = md5($userId.$shopUserId);
    $rs = knightlover::cache()->get($key);
    if($rs){
      // check the expire time
      if(time()-$rs['expiretime'] > 0){
        // expired
        knightlover::cache()->flush($key);
        return $maxcoin;
      }
      else{
        return max(0,$maxcoin - $rs['usedcoin']);
      }
    }
    else{
      // no record return max number
      return $maxcoin;
    }
  }


  function getCoin($userId,$shopUserId){
    $maxcoin = 6;
    $key = md5($userId.$shopUserId);
    $rs = knightlover::cache()->get($key);
    if($rs){
      // check expire time
      if(time()-$rs['expiretime'] > 0){
        // reset cache
        $rs['usedcoin'] = 1;
        $rs['expiretime'] = time() + 60*60*24; // set expire to 1 day
        knightlover::cache()->set($key,$rs,_CACHE_TIME_LONG);
        return true;
      }
      // check still coins left
      elseif($maxcoin - $rs['usedcoin'] > 0){
        $rs['usedcoin'] += 1;
        knightlover::cache()->set($key,$rs,_CACHE_TIME_LONG);
        return true;
      }
      else{
        return false;
      }
    }
    else{
      $rs['usedcoin'] = 1;
      $rs['expiretime'] = time() + 60*60*24; // set expire to 1 day
      knightlover::cache()->set($key,$rs,_CACHE_TIME_LONG);
      return true;
    }
  }


/**
 *
 *  system keep a daily list of hottest goods
 *
 **/

  function getHotGoods(){
    $goods = knightlover::cache()->get('hotgoods');
    if(!$goods){
      $index = knightlover::cache()->get('hotgoodsindex');
      if(!$index){
        $index = 0;
      }
      $data = knightlover::db()->getCollection("hotgoods")->findOne(array('type' => 'goods'));
      $goods = explode(',',$data['data'][$index]);
      if($index < sizeof($data['data'])-1){
        $index++;
      }
      else{
        $index = 0;
      }
      knightlover::cache()->set('hotgoodsindex',$index,0);
      knightlover::cache()->set('hotgoods',$goods,60*60*6);
    }
    return $goods;
  }

  function getHotGoodsIndex(){
    $index = knightlover::cache()->get('hotgoodsindex');
    if(!$index){
      $index = 0;
    }
    return $index;
  }

  function getHotGoodsByIndex($index){
    $data = knightlover::db()->getCollection("hotgoods")->findOne(array('type' => 'goods'));
    if($index > sizeof($data['data'])-1){
      $index = 0;
    }
    $goods = explode(',',$data['data'][$index]);
  }

/**
 *
 * weighted random function
 *
**/  
  private function w_rand($values, $weights) {
    $count = count($values);
    $i = 0;
    $n = 0;
    $num = mt_rand(0, array_sum($weights));
    while($i < $count){
        $n += $weights[$i];
        if($n >= $num){
            break;
        }
        $i++;
    }
    return $values[$i];
  }

/**
 *
 *  signature functions for determine multiple login
 *
**/
  function getSig($uid){
    return knightlover::cache()->get($uid.'_sig');
  }

  function newSig($uid){
    $sig = md5($uid.mt_rand(1,9999));
    knightlover::cache()->set($uid.'_sig',$sig,0);
    return $sig;
  }

/**
 *
 * sequence functions for determine multiple communication
 *
**/
  function getSeq($uid){
    return knightlover::cache()->get($uid.'_seq');
  }

  function newSeq($uid){
    $seq = mt_rand(0,10);
    $seqHash = md5($uid.mt_rand(1,9999));
    knightlover::cache()->set($uid.'_seq',$seq,0);
    knightlover::cache()->set($uid.'_seqhash',$seqHash,0);
    return array($seq,$seqHash);
  }

  function incSeq($uid,$fseq){
    $seq = $this->getSeq($uid);
    $seqHash = knightlover::cache()->get($uid.'_seqhash');
    if(md5($seq.$seqHash) == $fseq){
      knightlover::cache()->increment($uid.'_seq',1);
      $seqHash = md5($uid.mt_rand(1,9999));
      knightlover::cache()->set($uid.'_seqhash',$seqHash,0);
      return array($seq+1,$seqHash);
    }
    else{
      return false;
    }
  }
/**
 *
 * save offline calculation pass by flash
 *
**/
  function saveOfflineData($uid,$data){
    $door = 0;
    if(is_array($data['door'])){
      foreach($data['door'] as $d){
        $door += $d['total'];
      }
    }
    if(is_array($data['cashDesk'])){
      foreach($data['cashDesk'] as $d){
        $counter += $d['total'];
	$tmpcounter[strval($d['dbId'])] = array($d['total'],$d['operatorDBId']);
    }
    }
    if(is_array($data['shelf'])){
      foreach($data['shelf'] as $d){
        $shelf++;
	$tmpshelf[strval($d['dbId'])] = $d['total'];
      }
    }
    if(is_array($data['speShelf'])){
      foreach($data['speShelf'] as $d){
        $speshelf += $d['total'];
	$tmpspeshelf[strval($d['dbId'])] = array($d['total'],$d['operatorDBId']);
      }
    }
    $tmp['door'] = $door;
    $tmp['counterNum'] = $counter;
    $tmp['shelfNum'] = $shelf;
    $tmp['speShelfNum'] = $speshelf;
    $tmp['shelf'] = $tmpshelf;
    $tmp['speshelf'] = $tmpspeshelf;
    $tmp['counter'] = $tmpcounter;
    $tmp['hotgoodindex'] = $this->getHotGoodsIndex();
    knightlover::cache()->set('offline_'.$uid,$tmp,0);
  }

  function getOfflineData($uid){
    return knightlover::cache()->get('offline_'.$uid);
  }


  function lvMaxCharm($lv){
    $lvtable = array( '0' => 8,
                      '1' => 8,
                      '2' => 12.8,
                      '3' => 12.8,
                      '4' => 12.8,
                      '5' => 12.8,
                      '6' => 12.8,
                      '7' => 17.6,
                      '8' => 22.4,
                      '9' => 27.2,
                      '10' => 32,
                      '11' => 36.8,
                      '12' => 41.6,
                      '13' => 46.4,
                      '14' => 51.2,
                      '15' => 56,
                      '16' => 60.8,
                      '17' => 65.6,
                      '18' => 70.4,
                      '19' => 75.2,
                      '20' => 80,
                      '21' => 84.8,
                      '22' => 89.6,
                      '23' => 94.4,
                      '24' => 99.2,
                      '25' => 100
    );
    if($lv > 25){
      return 100;
    }
    else{
      return $lvtable[$lv];
    }
  }
/**
 *
 * lazy table for calculate P duration for offline calculation
 *
**/
  function lvToMaxIncome($lv){
    $lvtable = array( '0' => 37,
                      '1' => 37,
                      '2' => 37,
                      '3' => 37,
                      '4' => 37,
                      '5' => 37,
                      '6' => 37,
                      '7' => 40,
                      '8' => 42,
                      '9' => 44,
                      '10' => 46,
                      '11' => 48,
                      '12' => 51,
                      '13' => 53,
                      '14' => 57,
                      '15' => 60,
                      '16' => 64,
                      '17' => 69,
                      '18' => 74,
                      '19' => 81,
                      '20' => 88,
                      '21' => 98,
                      '22' => 109,
                      '23' => 123,
                      '24' => 142,
                      '25' => 149
    );
    if($lv > 25){
      return 149;
    }
    else{
      return $lvtable[$lv];
    }
  }

  function itemnumLv($lv){
    $rs = $this->ulevel($lv);
    return $rs['top_goods'];
  }

  function getMaxLevel(){
    return 40;
  }

  function logTokenItem($itemId,$iscoin=0){
    $date = date('Y-m-d');
    $col = knightlover::db()->getCollection('tmp_token_stat');
    $filter = array('id' => $itemId,'date' => $date);
    $update = array('$inc' => array('num' => 1),'$set' => array('date' => $date,'co'=>$iscoin));
    $options = array('upsert' => true);
    $col->update($filter,$update,$options);
  }
}
?>
