<?php
/**
 **********************************************************
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 **********************************************************
 *
 * Author: Sam
 *
 * Name: knightlover.php
 * Ver.: 1.0
 * Stat: closed
 *
**/

  include_once('config.php');

  if(__SITE__ENV == 'dev'){
    error_reporting(E_ALL);
  }
  else{
    error_reporting(0);
  }

  function __autoload($class){
      if(file_exists(__PATH__CLS."$class.php"))
        include_once(__PATH__CLS."$class.php");
  }

  class knightlover{

    protected static $_conf; //configurations
    protected static $_cache; // public useable cache
    protected static $_objcache;  // cache used by objhandler
    protected static $_objecthandler; 
    protected static $_dbhandler; // handler to user db data
    protected static $_vardbhdl;  // global variable db handler
    protected static $_facebook;  // facebook object
    protected static $_globalitem; //global items
    protected static $_hook;
    protected static $_logger; // logger
    protected static $_systemInfo; // all your misc system stuffs goes here
    protected static $_platform;

    public static function platform(){
      if(self::$_platform===NULL){
	$platform = self::conf()->platform;
	$platform = 'platform_'.$platform;
	self::$_platform = new $platform;
      }
      return self::$_platform;
    }

    public static function systemInfo(){
      if(self::$_systemInfo===NULL){
        self::$_systemInfo = new systemInfo();  
      }
      return self::$_systemInfo;
    }

    public static function globalitem(){
      if(self::$_globalitem===NULL){
        self::$_globalitem = new GlobalItem();
      }
      return self::$_globalitem;
    }

    public static function logger(){
      if(self::$_logger===NULL){
        self::$_logger['memory'] = new ALogger(dirname(__FILE__).'/log',self::conf()->log['memory_usage']);
        self::$_logger['debug'] = new ALogger(dirname(__FILE__).'/log',self::conf()->log['debug']);
        self::$_logger['token'] = new ALogger(dirname(__FILE__).'/log',self::conf()->log['token']);
        self::$_logger['banlog'] = new ALogger(dirname(__FILE__).'/log',self::conf()->log['banlog']);
      }
      return self::$_logger;
    }

    public static function cache(){
      if(self::$_cache===NULL){
          self::$_cache = new Cache_Memcache(self::conf()->cache['server']);
        }
        return self::$_cache;
    }

    public static function db(){
      if(self::$_dbhandler===NULL){
        if(self::conf()->database['type'] == 'mongo'){
          self::$_dbhandler = new Database_Mongo(self::conf()->database['host'][0],self::conf()->database['host'][1]);
          self::$_dbhandler->connect(self::conf()->database['dbname']);
        }
      }
      return self::$_dbhandler;
    }

    public static function hook(){
      if(self::$_hook === NULL){
        self::$_hook = new Hooks();
      }
      return self::$_hook;
    }

    public static function objhandler(){
      if(self::$_objecthandler===NULL){
        self::$_objecthandler = new Objecthandler;
      }
        return self::$_objecthandler;
    }

    public static function objcache(){
      if(self::$_objcache===NULL){
        // TODO bad idea to use memcache here, try to use apc or eaccelerator pre 0.9.5 in the future
        self::$_objcache = new Cache_Memcache();
      }
      return self::$_objcache;
    }

    public static function fb(){
      if(self::$_facebook===NULL){
        self::load_library('graphapi');
        self::$_facebook = new facebook(array(
          'appId' => self::conf()->app['appid'],
          'secret' => self::conf()->app['secret']
        ));
      }
      return self::$_facebook;
    }

    public static function conf(){
      if(self::$_conf===NULL)
        self::$_conf = new config;
      return self::$_conf;
    }

  public static function load_class($class){
      static $objects = array();

      if(isset($objects[$class]))
        return $objects[$class];

        $path = __PATH__CLS;

      $fname = $path.$class.'.php';
      if(file_exists($fname))
        include_once($fname);
      else
        return self::load_model('Dummy');
  }

  public static function load_library($class){
    static $objects = array();

    if(isset($objects[$class]))
        return $objects[$class];

        $path = __PATH__LIB;

      $fname = $path.$class.'.php';
      if(file_exists($fname))
        include_once($fname);
      else
        return self::load_model('Dummy');
  }
 
  public static function load_model($class){
      static $objects = array();

      if(isset($objects[$class]))
        return $objects[$class];

        $path = __PATH__MOD;

      $fname = $path.$class.'.php';
      if(file_exists($fname))
        include_once($fname);
      else
        return self::load_model('Dummy');

      //$objects[$class] = &self::instantiate_class(new $class());
      //return $objects[$class];
  }

  public static function &instantiate_class(&$class_object)
  {
    return $class_object;
  }

  public static function array_to_object($array = array()) {
    if (!empty($array)) {
        $data = new stdClass();
        foreach ($array as $akey => $aval) {
            if($akey != '_explicitType'){
              $data -> {$akey} = $aval;
            }
        }
        return $data;
    }
    return false;
  }
 }

?>
