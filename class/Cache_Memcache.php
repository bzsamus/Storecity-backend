<?php
  include_once('Cache_Abstract.php');

  class Cache_Memcache extends Cache_Abstract{
    protected $_memcache;
    protected $_config;

    public function  __construct($conf=Null) {
        $this->_memcache = new Memcache();
        $this->_config = $conf;

        // host, port, persistent, weight
        if($conf!==Null){
            foreach ($conf as $c){
                $result = $this->_memcache->addServer($c[0], $c[1], $c[2], $c[3]);
            }
        }
        else{
            $this->_memcache->addServer('localhost', 11211);
        }
    }

    public function set($id, $data, $expire=0, $compressed=false){
      try{
        if($compressed)
            return $this->_memcache->set($id, $data, MEMCACHE_COMPRESSED, $expire);
        else
            return $this->_memcache->set($id, $data, 0, $expire);
      }
      catch(Exception $e){
        return false;
      }
    }

    public function get($id){
      try{
        return $this->_memcache->get($id);
      }
      catch(Exception $e){
        return false;
      }
    }

    public function flush($id){
      try{
        $this->_memcache->delete($id,0);
      }
      catch(Exception $e){
        return false;
      }
    }

    public function flushAll(){
        return $this->_memcache->flush();
    }
    
    public function increment($id,$val){
      return $this->_memcache->increment($id,$val);
    }
  }
?>
