<?php
  include_once('knightlover.php');
  include_once('phprpc/phprpc_server.php');
  
  function getip(){
    if (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $myip = $_SERVER['REMOTE_ADDR'];
    } else {
      $myip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $myip = $myip[0];
    }
    return $myip;
  }

  function addToken($uid,$amount){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->incToken($amount);
    $s->inject(true);
    //log
    $ip = getip();
    $logger = knightlover::logger();
    $logger['token']->log($ip.' '.$uid.':'.$amount.' token',3);
    $logger['token']->writeLog();
  }

  function addItem($uid,$item){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    if(is_array($item)){
      $it = new stdClass();
      foreach($item as $i){
        $it->globalItemId = $i;
        $s->inventory->addItem($it);
      }
    }
    else{
      $it = new stdClass();
      $it->globalItemId = $item;
      $s->inventory->addItem($it);
    }
    $s->inject(true);
  }

  function addCash($uid,$amount){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->increaseCash($amount);
    $s->inject(true);
    $ip = getip();
    $logger = knightlover::logger();
    $logger['token']->log($ip.' '.$uid.':'.$amount.' cash',3);
    $logger['token']->writeLog();
  }

  function banUser($uid){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->ban();
    $s->inject(true);
    // log
    $ip = getip();
    $logger = knightlover::logger();
    $logger['banlog']->log($ip.' '.$uid.':'.' ban',3);
    $logger['banlog']->writeLog();
  }

  function unbanUser($uid){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->unban();
    $s->inject(true);
    // log
    $ip = getip();
    $logger = knightlover::logger();
    $logger['banlog']->log($ip.' '.$uid.':'.' unban',3);
    $logger['banlog']->writeLog();
  }
/*
  function sendGift($users,$gid,$title,$message){
    knightlover::load_model('inboxmailFactory');
    // only send out gift mail here
    $mail = inboxmailFactory::getInstance();
    $mail->fromUid = 0;
    $mail->fromUsername = 'system';
    $mail->time = time();
    $mail->title = $title;
    $mail->message = $message;
    $mail->attachment = $gid;
    $mail->read = 0;
    knightlover::load_model('inboxFactory');#added by Ryan
    foreach($users as &$u){
      $inbox = inboxFactory::getInstance($u);
      $inbox->receiveMail($mail);
    }
  }
*/
	/**
	* 系统向用户简单的站内信
	* by Ryan
	* @param array $users 包裹着多个用户ID的数组 
	* @param string $title='untitled' 邮件标题
	* @param string $message='n/a' 邮件正文
	* @param string $goods=null 物件提示，逗号隔开的物件id
	* @param int $cash=null 金币提示
	* @param int $token=null 米币提示
	* @param string $message 邮件正文
	* @return bool status
	*/
	function sendSysMail($users,$title='untitled',$message='n/a',$goods=null,$cash=null,$token=null){
		if(!is_array($users) || count($users)<1){return false;}
		knightlover::load_model('inboxmailFactory');
		$mail = inboxmailFactory::getInstance();
		$mail->fromUid = 0;
		$mail->fromUsername = 'system';
		$mail->time = time();
		$mail->title = $title;
		$mail->message = $message;
		if(null!=$goods){
			$mail->attachment = $goods;
		}
		if(null!=$cash){
			$mail->coin = $cash;
		}
		if(null!=$token){
			$mail->token = $token;
		}
		$mail->read = 0;
		knightlover::load_model('inboxFactory');
		foreach($users as &$u){
			$inbox = inboxFactory::getInstance($u);
			$inbox->receiveMail($mail);
		}
		return true;
	} 
	/**
	 * 单独给某用户发信
	* by Ryan
	 * @param string $fromUid 发信者ID
	 * @param string $toUdi 收信者ID
	 * @param string $title='untitled' 邮件标题
	 * @param string $message='n/a' 邮件正文
	 * @param string $goods=null 物件提示，逗号隔开的物件id
	 * @param int $cash=null 金币提示
         * @param int $token=null 米币提示
	 * @return bool status
	 */
	function sendMailtoUser($fromUid,$toUid,$title='untitlled',$message='n/a',$goods=null,$cash=null,$token=null){
		if(!is_numeric($fromUid)||!is_numeric($toUid)){return false;}
		knightlover::load_model('userFactory');
		$s = userFactory::getInstance($fromUid);
		knightlover::load_model('inboxmailFactory');
		$mail = inboxmailFactory::getInstance();
		$mail->fromUid = $fromUid;
		$mail->fromUsername = $s->facebookInfo->username;;
		$mail->time = time();
		$mail->title = $title;
		$mail->message = $message;
		if(null!=$goods){
			$mail->attachment = $goods;
		}
		if(null!=$cash){
			$mail->coin = $cash;
		}
		if(null!=$token){
			$mail->token = $token;
		}
		$mail->read = 0;
		knightlover::load_model('inboxFactory');
		$inbox = inboxFactory::getInstance($toUid);
		$inbox->receiveMail($mail);
		return true;
	}

  function userInfo($uid){
    knightlover::load_model('userFactory');
    knightlover::load_model('adapterClass');
    $s = userFactory::getInstance($uid);
    $s->username = $s->facebookInfo->username;
    $s->profilePic = $s->facebookInfo->profilePic;
    $s->userInfo->gender = $s->facebookInfo->gender; 
    $user = new stdClass();
    $user->cash = $s->userInfo->cash;
    $user->token = $s->userInfo->token;
    $user->level = $s->userInfo->level;
    $user->exp = $s->userInfo->exp;
    $user->like = $s->userInfo->like;
    $user->lastaccess = $s->userInfo->lastaccess;
    $user->playcount = $s->userInfo->playcount;
    $user->tutorial = $s->userInfo->tutorial;
    $user->expt = $s->userInfo->expt;
    $user->valid = $s->userInfo->valid;
    $user->username = $s->username;
    $user->profilePic = $s->profilePic;
    $user->gender = $s->userInfo->gender;
    return $user;
  }

  function compensate($uid,$cash,$items,$level,$exp,$expt,$tutorial){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->remove();
    // reset all user data
    $s->init();
    // compensate
    if(intval($cash) > 0){
      $s->userInfo->increaseCash($cash); 
    }
    if(is_array($items)){
      $it = new stdClass();
      foreach($items as $i){
        $it->globalItemId = $i;
        $s->inventory->addItem($it);
      }
    }
    $s->userInfo->level = intval($level);
    $s->userInfo->exp = intval($exp);
    $s->userInfo->expt = intval($expt);
    if($tutorial){
      $s->userInfo->tutorial = 0;
    }
    $rs = $s->inject(true);
    return $rs;
  }

  function setLevel($uid,$level){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->level = intval($level);
    return $s->inject(true);
  }

  function setExp($uid,$exp){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->exp = intval($exp);
    return $s->inject(true);
  }

  function setExpt($uid,$expt){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->expt = intval($expt);
    return $s->inject(true);
  }

  function setTutorial($uid,$tutorial){
    knightlover::load_model('userFactory');
    $s = userFactory::getInstance($uid);
    $s->userInfo->tutorial = $tutorial;
    return $s->inject(true);
  }

  $server = new PHPRPC_Server();
  $server->add(array('serverecho','addToken','addItem','addCash','banUser','unbanUser','sendSysMail','sendMailtoUser','userInfo','compensate','setLevel','setExp','setExpt','setTutorial'));
  $server->setDebugMode(false); 
  $server->start();
?>
