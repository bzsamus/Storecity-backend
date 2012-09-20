<?php

class shopFactory{
  public static function getInstance(){
    $obj = knightlover::objhandler()->getObject('shop');
    return $obj;
  }

  public static function getNewShop($name){
    $obj = knightlover::objhandler()->getObject('shop');
    //knightlover::load_model('shopExteriorFactory');
    //knightlover::load_model('shopInteriorFactory');
    knightlover::load_model('employeeFactory');
    //$obj->shopExterior = shopExteriorFactory::getInstance();
    //$obj->shopInterior = shopInteriorFactory::getNewInstance();
    $obj->floors = array(4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001,
                         4050001,4050001,4050001,4050001,4050001,4050001,4050001
    );
    $obj->w = 7; //shop floor initial width
    $obj->l = 7; //shop floor initial length
    $obj->employees = array();
    $obj->shopName = "$name";
    return $obj;
  }
}

?>
