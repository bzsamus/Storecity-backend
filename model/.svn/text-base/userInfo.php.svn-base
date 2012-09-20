<?php
include_once('model.php');

class userInfo extends model{
  var $token;
  var $cash;
  var $level;
  var $exp;
  var $like;
  var $lastaccess;
  var $playcount;
  var $tutorial;
  var $expt; // expension points
  var $valid;

  public function reduceCash($amount){
    $this->cash = max(0,$this->cash-intval($amount));
  }

  public function increaseCash($amount){
    $this->cash += intval($amount);
  }

  public function increaseExp($amount){
    if($this->level < knightlover::systemInfo()->getMaxLevel()){
      $this->exp += intval($amount);
    }
  }

  public function incExpt($amount){
    $this->expt += $amount;
  }

  public function decExpt(){
    $this->expt = max(0,$this->expt-1);
  }

  public function incToken($amount){
    $this->token += $amount; 
  }

  public function reduceToken($amount){
    $this->token = max(0,$this->token-intval($amount));
  }

/**
 *
 * ban user functions
 *
**/
  public function ban(){
    $this->valid = 0;
  }

  public function unban(){
    $this->valid = 1;
  }  

}

?>
