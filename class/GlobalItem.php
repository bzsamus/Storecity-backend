<?php

class GlobalItem{

  var $xmlinfo;

  function __construct(){
      // populate global item data from cache
      $this->xmlinfo = knightlover::cache()->get('xmlinfo');
      if(!$this->xmlinfo){
        $rs = knightlover::db()->getCollection('xmlinfo')->find();
        while($rs->hasNext()){
          $item = $rs->getNext();
          $tmp[$item['item_id']] = array('name' => $item['name'],
                                         'cost' => intval($item['cost']),
          				 'token' => intval($item['token']),//added by ryan
                                         'dv' =>  intval($item['dv']),
					 'nv' => intval($item['nv']),
                                         'price' => intval($item['price']),
                                         'exp'  => intval($item['exp']),
                                         'limitedlevel' => intval($item['limitedlevel']),
					 'decorValue' => intval($item['decorValue']),
					 'checkoutTime' => intval($item['checkoutTime']),
                                         'item_id'  =>  $item['item_id']);
        }
        knightlover::cache()->set('xmlinfo',$tmp,_CACHE_TIME_LONG,true);
        $this->xmlinfo = $tmp;
      }
  }

  function getItem($id){
    return $this->xmlinfo[$id];
  }
}

?>
