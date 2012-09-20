<?php
  include_once('Cache_Abstract.php');

  class Cache_Eaccelerator extends Cache_Abstract{
    
    public function set($id, $data, $expire=0){
        return eaccelerator_put($id, $data, $expire);
    }

    public function get($id){
        return eaccelerator_get($id);
    }

    public function flush($id){
        return eaccelerator_rm($id);
    }

    public function flushAll(){
        //delete expired content then delete all
        eaccelerator_gc();

        $idkeys = eaccelerator_list_keys();

        foreach($idkeys as $k)
            $this->flush(substr($k['name'], 1));
    }
  }

?>
