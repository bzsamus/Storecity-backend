<?php
  include_once('../config.php');
  include_once('../knightlover.php');

class sauna{
  private function log_memory($fname,$uid=''){
    //$logger = knightlover::logger();
    //$logger['memory']->log($uid.':'.$fname.':'.memory_get_usage().'/'.memory_get_peak_usage(),3);
    //$logger['memory']->writeLog();
  }

  function getStreetUser($uid){
    knightlover::load_model('userFactory');
    knightlover::load_model('adapterClass');
    $s = userFactory::getInstance($uid);
    $rs = new StreetUser($s);
    return $rs?$rs:null;
  }

  private function tokenToUid($token){
    // get token from cache first
      $uid = knightlover::platform()->tokenToUid($token);
      return $uid;
  }

  function parse($token,$action,$fsig,$fseq){
    try{
      $uid = $this->tokenToUid($token);
      knightlover::load_class('ActionController');
      knightlover::load_model('userFactory');
      if(!is_array($action)){
        $action = json_decode($action);
      }
      $s = userFactory::getInstance($uid);
      // validate user
      if(!$s->userInfo->valid){
        throw new Exception("oops you got banned");
      }
      // validate request
      $seq = knightlover::systemInfo()->getSeq($uid);
      $sig = knightlover::systemInfo()->getSig($uid);
      // signature check for multiple login
      if(!md5($sig.strval($seq)) == $fsig){
        throw new Exception('invalid signature');
      }
      // sequence check for multiple communication
      if(!$newseq = knightlover::systemInfo()->incSeq($uid,$fseq)){
        throw new Exception('invalid sequence');
      }
      else{
        $rs[0]['seq'] = $newseq[0];
        $rs[0]['seqHash'] = $newseq[1];
      }

      // trigger pre-parse hook
      knightlover::hook()->_call_hook('pre_parse');

      $s->shop->employeeTick();
      $tmp =  ActionController::run($s,$action);
      $dexp = $s->userInfo->exp; // exp after parse
      $ulevel = knightlover::systemInfo()->ulevel($s->userInfo->level);
      $unextlevel = knightlover::systemInfo()->ulevel(intval($s->userInfo->level)+1);
      $rs[0]['servertime'] = $this->servertime();
      $rs[0]['ok'] = $tmp['result'];
      $rs[0]['v'] = knightlover::conf()->version;
      // TODO special events
      $hotgoods = new stdclass();
      $hotgoods->id = 1001;
      $hotgoods->data = knightlover::systemInfo()->getHotGoods();
      $rs[1] = $hotgoods;
      if($dexp >= $unextlevel['need_exp']){
        // level up TODO dirty code should move into model functions
        $s->userInfo->level += 1;
        if($unextlevel['can_extend'] == 1){
          $s->userInfo->incExpt(1);
        }
        if($unextlevel['give_coin'] > 0){
          $s->userInfo->increaseCash($unextlevel['give_coin']);
        }
        if(isset($unextlevel['give_items'])){
          $items = explode(',',$unextlevel['give_items']);
          if(is_array($items)){
            foreach($items as $i){
              $objtmp = new stdclass();
              $objtmp->globalItemId = $i;
              $s->inventory->addItem($objtmp);
          }
          }
        }
        $s->inject();

	// trigger post_parse hook
        knightlover::hook()->_call_hook('post_parse');

        $nextlv = new stdclass();
        $nextlv->id = 1101;
        $nextlv->data = knightlover::systemInfo()->ulevel(intval($s->userInfo->level)+1);
        $rs[] = $nextlv;
      }
      if(is_array($tmp['return'])){
        foreach($tmp['return'] as $i=>$t){
	  $obj = new stdClass();
          $obj->id = $i;
          $obj->data = $t;
          $rs[] = $obj;
        }
      }
      if(__SITE__ENV == 'dev'){
        $rs['debug']['userinfo'] = $s->userInfo;
        $rs['debug']['employee'] = $s->shop->employees;
        $rs['debug']['mem'] = memory_get_peak_usage();
      }
      $this->log_memory(__FUNCTION__,$uid);
      return $rs;
    }
    catch(Exception $e){
     if(__SITE__ENV == 'dev'){
        $rs = $e->getMessage();
      }
      else{
        $rs = 'code[0001]';
      }
      return $rs;
     }
  }

  function getUserInfo($token,$touid,$data=1){
    try{
      $uid = $this->tokenToUid($token);
      $touid = sprintf("%.0f",$touid);
      $rs = knightlover::platform()->getUserInfo($touid);
      knightlover::load_model('userFactory');
      knightlover::load_model('adapterClass');
      $s = userFactory::getInstance($touid);
      $s->username = $rs['name'];
      $s->profilePic = $rs['profilePic'];
      $s->userInfo->gender = $rs['gender'];
      $tmp = new userBasic($s,$data);
      $tmp->coins = knightlover::systemInfo()->getCoins($uid,$touid);
      //
      $tmp->ulevel = knightlover::systemInfo()->ulevel(intval($s->userInfo->level));
      //$tmp->coins = knightlover::systemInfo()->getCoins($uid,$touid);
      $tmp->servertime = $this->servertime();
      #貨架信息
      knightlover::load_model('rackFactory');
      $rack = rackFactory::getInstance($touid);
      $r = $rack->getInfo();
      $tmp->rack = $r['data'];
      $tmp->rackCount = (int)$r['count'];
      #remove not in use
      unset($tmp->gender);
      unset($tmp->offineShard);
      unset($tmp->playCount);
      #return
      return $tmp;
    }
    catch(Exception $e){
     if(__SITE__ENV == 'dev'){
        $rs = $e->getMessage();
      }
      else{
        $rs = 'code[0001]';
      }
     return $rs;
     }
  }

  function getFriendList($token){
    try{
      $rs = knightlover::platform()->getFriendList($token);
      $uid = $this->tokenToUid($token);
      /*
        getting items owned by friends
        tuning required
      */ 
      foreach($rs['data'] as $r){
        knightlover::load_model('userFactory');
        knightlover::load_model('adapterClass');
        $s = userFactory::getInstance($r['id']);
        $s->username = $r['name'];
        $s->profilePic = $r['profilePic'];
        if(!$s->userInfo->lastaccess){
        	$user = array();
        	$user['id'] = $r['id'];
        	$user['fullName'] = $r['name'];
        	$user['imageUrl'] = $r['profilePic'];
        }else{
	        $user = new userBasic($s);
	        //unset the not in use
	        unset($user->floors);
	        unset($user->employees);
	        unset($user->playCount);
	        unset($user->gender);
	        unset($user->offlineShard);
	        $user->coins = knightlover::systemInfo()->getCoins($uid,$r['id']);
        }
        $tmp[] = $user;
      }
      $this->log_memory(__FUNCTION__);
      return $tmp;
    }
    catch(Exception $e){
     if(__SITE__ENV == 'dev'){
        $rs = $e->getMessage();
      }
      else{
        $rs = 'code[0001]';
      }
     } 
    return $rs;
  }

  function init($token){
    try{
	$uid = $this->tokenToUid($token);
	if($uid){
        knightlover::load_model('userFactory');
        knightlover::load_model('adapterClass');
        $s = userFactory::getInstance($uid);

		// trigger hook init
		knightlover::hook()->_call_hook('init',array($uid));
        
		$lasttime = $s->userInfo->lastaccess;
        $currenttime = time();
        if(!$s->userInfo->lastaccess){
          $newU = true;
          $s->init();
        }else{
          $s->ingameItem;
          $s->userInfo->playcount += 1;
          $s->shop;
          $earning = $s->offlineEarning();
        }
        // additional infos passed on first packet
        // TODO: special events
		$fp = new initInfo($s);
		$fp->earning = $earning;
		$fp->cash = max(0,$fp->cash-$earning['cash']);
		$fp->exp = max(0,$fp->exp-$earning['exp']);
        $fp->ulevel = array(
                     knightlover::systemInfo()->ulevel(intval($s->userInfo->level)),
                     knightlover::systemInfo()->ulevel(intval($s->userInfo->level)+1)
                     );
        // TODO: hottest goods
        $fp->hotgoods = knightlover::systemInfo()->getHotGoods();
        // number of coin on trees 
        $fp->coins = knightlover::systemInfo()->getCoins($uid,$uid);
        $fp->servertime = $this->servertime();
        // multiple login and communication sequence generation
        $fp->sig = knightlover::systemInfo()->newSig($uid);
        $tmp = knightlover::systemInfo()->newSeq($uid);
        $fp->seq = $tmp[0];
        $fp->seqHash = $tmp[1];
        $this->log_memory(__FUNCTION__,$uid);
	    #貨架信息
	    knightlover::load_model('rackFactory');
	    $rack = rackFactory::getInstance($uid);
	    $r = $rack->getInfo();
	    $fp->rack = $r['data'];
	    $fp->rackCount = (int)$r['count'];       
        #每日登录
        if($newU){
        	$fp->dailyLogin = false;
        }else{
        	$fp->dailyLogin = $this->dailyCheck($uid);
        }
        
        return $fp;
      }
      else{
        throw new Exception('cannot get user id.');
      }
    }
    catch(Exception $e){
      if(__SITE__ENV == 'dev'){
	$rs = $e->getMessage();
      }
      else{
	$rs = 'code[0001]';
      }
    }
    return $rs;
  }

  function servertime(){
    // flash side
    // var my_date: Date = new Date(1970, 0, 1, 0, 0, 0);
    // my_date.setSeconds($time);
    $time = date('Y.m.d G:i:s');
    $this->log_memory(__FUNCTION__);
    return $time;
  }

  /**
   * 检查今天是否已经登录过
   * 目前给init用
   * @param $uid
   * @return bool
   */
  private function dailyCheck($uid){
  	$t = knightlover::cache()->get('daily_'.$uid);
  	if(!$t){
  		return true;
  	}else{
  		if($t!=date('ymd')){
  			return true;
  		}else{
  			return false;
  		}
  	}
  }
  /**
   * 给每日登录加金币
   * @param string $token
   * @param int $case 类型 0:忽略，1：要金币
   * @return bool
   */
  public function dailyLogin($token,$case=0){
  	$uid = $this->tokenToUid($token);
  	if($case==1){
	  	if($this->dailyCheck($uid)){//可以加
	      	knightlover::load_model('userFactory');
			$s = userFactory::getInstance($uid);
			$s->userInfo->increaseCash(1000);#成功加金币
	        $s->inject();#只能在這裡先寫數據庫一下
	        knightlover::cache()->set('daily_'.$uid,date('ymd'),60*60*24);#缓冲24小时
	        return true;
	  	}else{#不能加
	  		return false;
	  	}
  	}else{
  		knightlover::cache()->set('daily_'.$uid,date('ymd'),60*60*24);#缓冲24小时
  		return true;
  	}
  }
}
?>
