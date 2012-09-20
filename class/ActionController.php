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
 * Name: ActionController.php
 * Use:  Mapping action array to class and function
 *       $action = array(array('gamelogic','buy',10001,2),arra(...));
 *       will call class gamelogic function buy with parameters 10001, 2
 * Ver.: KnightLover 0.0.1
 * Stat: Current
 *
**/

class ActionController{

public static function run(&$user,&$action){
  ob_start();
  foreach($action as $a){
    if(is_array($a)){
      $class = array_shift($a);
      $functmp = array_shift($a);
      if(array_key_exists($functmp,knightlover::conf()->actions)){
        $func = knightlover::conf()->actions[$functmp];
      }
      else{
        $func = 'dummy';
      }
      if($func != 'dummy' && $func != null){ 
        array_unshift($a,$user);
        // expected to be a reference, value given error dirty solution
        $a[0] = &$a[0];
        $a[1] = &$a[1];
        if(!class_exists($class)){
          $filename = __PATH__ACT.$class.'.php';
          if(file_exists($filename)){
            include($filename);
          }
        }
        $instance = self::instantiate_class(new $class($a));
        if(method_exists($instance,$func)){
          $returnvar = null;
          $a[] = &$returnvar;
          call_user_func_array(array($instance,$func),$a);
        }
      }
    }
    else{
      echo "init $class class error\r\n";
    }
  }
  $rs = ob_get_contents();
  //return $rs; //for debugging
  ob_end_clean();
  $resarr = preg_split('//', $rs, -1, PREG_SPLIT_NO_EMPTY);
  foreach($resarr as &$r){
    $r = intval($r);
  }
  if(in_array(1,$resarr)){
    // update lastaccess time
    $user->userInfo->lastaccess = time();
    $user->inject();
  }
  $result['result'] = $resarr;
  if($returnvar){
    $result['return'] = $returnvar;
  }
  return $result;
}

public static function &instantiate_class(&$class_object)
  {
    return $class_object;
  }
}
?>
