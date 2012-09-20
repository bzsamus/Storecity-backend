<?php

class mailbox{
  var $userId;

  function __construct($id){
    $this->userId = strval($id);
  }

  public function __get($var){
    if(!isset($this->$var)){
      if($var == 'dbdata'){
        knightlover::db()->getCollection(get_class($this));
        $filter = array('userId' => $this->userId);
        $obj = knightlover::db()->findOne($filter);
        $this->$var = &$obj;
      }
      else{
        //$this->$var = knightlover::objhandler()->getObject($var);
        eval('$this->_load'.$var.'();');
      }
    }
    return $this->$var;
  } 

  function _loadmails(){
    $this->mails = $this->dbdata['mails'];
    if(!isset($this->mails)){
      $this->mails = array();
    }
  }

  function _loadid(){
    $this->id = $this->dbdata['id'];
    if(!isset($this->id)){
      $this->id = 1;
    }
  }

  public function inject(){
    if(isset($this->dbdata))
      unset($this->dbdata);
    $objs = get_object_vars($this);
    foreach($objs as $i=>$u){
      if($u === NULL){
        unset($objs[$i]);
      }
    }
    $filter = array('userId' => $this->userId);
    $update = array(
      '$set'  =>  $objs
    );
    $options = array(
      'multiple' => false,
      'upsert'   => true
    );
    knightlover::db()->getCollection(get_class($this));
    $rs = knightlover::db()->update($filter,$update,$options)?true:false;
    return $rs;
  }

  function markMail($inboxmail){
    try{
      $id = $inboxmail;
      if(isset($this->mails[$id])){
        // delete mails with attachment after read
        if(isset($this->mails[$id]['attachment']) || $this->mails[$id]['coin'] > 0 || $this->mails[$id]['token'] > 0){
          $this->getAttachment($inboxmail);
        }
        else{
          // normal mails just mark the mail
          $this->mails[$id]['read'] = 1;
        }
        if($this->inject()){
          return true;
        }
        throw new Exception('db update error');
      }
      throw new Exception('mail not found');
    }
    catch(Exception $e){
      return false;
    }
  }

  function purgeMail($inboxmail){
    try{
      $id = $inboxmail;
      if(isset($this->mails[$id])){
        unset($this->mails[$id]);
        if($this->inject()){
          return true;
        }
        throw new Exception('db update error');
      }
      throw new Exception('mail not found');
    }
    catch(Exception $e){
      return false;
    }
  }

  function sendMail($inboxmail,$touid){
    knightlover::load_model('inboxFactory');
    $inbox = inboxFactory::getInstance($touid);
    return $inbox->receiveMail($inboxmail);
  }

  function receiveMail($inboxmail){
    try{
      if($this->validate($inboxmail)){
        $id = $this->id;
        $this->id++;
        $inboxmail->id = $id;
	$inboxmail->time = time();
	$inboxmail->read = 0;
        $this->mails; // weird but only this will trigger __get function
        $this->mails[$id] = $inboxmail;
        if(!$this->inject()){
          throw new Exception('write db failed');
        }
        return true;
      }
      throw new Exception('validation failed');
    }
    catch(Exception $e){
      return false;
    }
  }

  function getAttachment($inboxmail){
    $id = $inboxmail;
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($this->userId);
    if($this->mails[$id]['attachment']){
    	$items = explode(',',$this->mails[$id]['attachment']);
	if($items){
		knightlover::load_model('inventoryItem');
		foreach($items as $i){
        		$item = new InventoryItem($i);
			$s->inventory->addItem($item);
		}
	}
    }
    if($this->mails[$id]['coin']){
	$s->userInfo->increaseCash($this->mails[$id]['coin']);
	
    }
    if($this->mails[$id]['token']){
	$s->userInfo->incToken($this->mails[$id]['token']);
    }
    if($s->inject(true)){
      $this->mails[$id]['attachment'] = '';
      $this->mails[$id]['coin'] = '';
      $this->mails[$id]['token'] = '';
      $this->mails[$id]['read'] = 1;
    }
  }

  function validate($inboxmail){
    // TODO: validate $inboxmail is a valid mail format
    return true;
  }

  function checkExpire(){
    $now = time();
    if(is_array($this->mails)){
      foreach($this->mails as &$m){
	 if($now - $m['time'] > 60*60*24*10 && $m['fromUid'] > 0){
          unset($m);
        }
      }
    }
    $this->inject();
  }

  function getMails(){
    $this->checkExpire();
    $rs = $this->mails;
    foreach($rs as $i=>&$m){
	$m['time'] = date('d/m/Y',$m['time']);
    }
    sort($rs);
    return $rs;
  }
}

?>
