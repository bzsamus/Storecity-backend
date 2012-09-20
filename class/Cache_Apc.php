<?php
  include_once('Cache_Abstract.php');

  class Cache_Apc extends Cache_Abstract{
    
    public function set($id, $data, $expire=0){
        return apc_store($id, $data, $expire);
    }

    public function get($id){
        return apc_fetch($id);
    }

    public function flush($id){
        return apc_delete($id);
    }

    public function flushAll(){
        return apc_clear_cache('user');
    }

}

?>
