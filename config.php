<?php
/*
 * directory constant
 */
  define('__SITE__ROOT',  dirname(__FILE__).'/');
  define('__PATH__CLS',   __SITE__ROOT.'class/');
  define('__PATH__LIB',   __SITE__ROOT.'libs/');
  define('__PATH__MOD',   __SITE__ROOT.'model/');
  define('__PATH__ACT',   __SITE__ROOT.'action/');
  define('__PATH__LOG',   __SITE__ROOT.'logs/');
  define('__SITE__ENV', 'dev'); // dev = development, prod = production
/*
 * cache time constant
 */
  define('_CACHE_TIME_SHORT', 60);
  define('_CACHE_TIME_NORMAL', 600);
  define('_CACHE_TIME_LONG', 60*60*24);

  class config{
    function __construct(){
      $this->version = '1';
      $this->platform = 'facebook';  //facebook,opensocial,plinga
      $this->enable_hooks = 'true';
      $this->database = array(
        'type' => 'mongo',
        'dbname' =>'sauna',
        'host'  =>  array(
                    'mongodb://name:pass@localhost:27017', // master
                    //'mongodb://name:pass@localhost:20000' //slave
		    //'mongodb://localhost:50003',
                    )
      );
      $this->app['apikey'] = 'fbapikey';
      $this->app['appid'] = 'fbappid';
      $this->app['secret'] = 'fbappsecret';
      $this->log = array(
        'memory_usage' => 'memory.log',
        'debug'        => 'debug.log',
        'token'        => 'token.log',
        'banlog'       => 'banlog.log'
      );
      $this->cache = array(
        'type' => 'memcache',
        'keeptime' => 600,
        'compress' => true,
        'server' => array(
          array('127.0.0.1', '11211', true, 40)
        )
      );
      $this->actions = array(  //todo break this intp separate config file
        '0'   =>  'dummy',
        '3'   =>  'buyInventory',
        '4'   =>  'sellIngame',
        '5'   =>  'toIngame',
        '11'  =>  'addGraffiti',
        '15'  =>  'hireEmployee',
        '18'  =>  'cleanGraffiti',
        '19'  =>  'sellInventory',
        '22'  =>  'buyIngame',
        '23'  =>  'saveItem',
        '27'  =>  'saveFloor',
        '36'  =>  'updateEmployee',
        '39'  =>  'refreshEmployee',
        '44'  =>  'fireEmployee',
        '49'  =>  'offlineEarning',
        '51'  =>  'toInventory',
        '55'  =>  'updateTutorial',
        '66'  =>  'pickCoin',
        '70'  =>  'expendShop',
        '73'  =>  'repairCounter',
        '82'  =>  'setShopName',
        '98'  =>  'saveData',
        '99'  =>  'datasync'
      );
	
    }
  }
?>
