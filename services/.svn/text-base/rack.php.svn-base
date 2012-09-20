<?php
/*
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
*/
include_once('../config.php');
include_once('../knightlover.php');

class rack{
	private function tokenToUserArray($token){
		// get token from cache first
		/*$uid = knightlover::cache()->get('fb_token_'.$token);
		if(!$uid){
			$rs = knightlover::fb()->api('/me',array('access_token' => $token));
			$uid = $rs['id'];
			if(!$uid){
				throw new Exception('failed to optain uid');
			}else{
          		knightlover::cache()->set('fb_token_'.$token,$uid,_CACHE_TIME_LONG);
			}
		}*/
		$uid = knightlover::platform()->tokenToUid($token); // added by Sam: platform independence tokenToUid
                return $uid;
    }
	/*
    private function validateUser($uid,$fsig,$fseq){
      	$seq = knightlover::systemInfo()->getSeq($uid);
      	$sig = knightlover::systemInfo()->getSig($uid);
      	// signature check for multiple login
      	if(!md5($sig.strval($seq)) == $fsig){
        	throw new Exception('invalid signature');
      	}
      	// sequence check for multiple communication
      	if(!$newseq = knightlover::systemInfo()->incSeq($uid,$fseq)){
       		throw new Exception('invalid sequence');
      	}else{
        	$rs[0]['seq'] = $newseq[0];
        	$rs[0]['seqHash'] = $newseq[1];
      	}
      	return $rs;
    } 
		#验证合法性
	    try{
	        $ret = $this->validateUser($userId,$fsig,$fseq);
      	}catch(Exception $e){
       		if(__SITE__ENV == 'dev'){
         		$rs = $e->getMessage();
       		}else{
         		$rs = false;
       		}
      		return $rs;
      	}    
    */   
    
    public function addCommit($token,$buid,$cid,$iid,$act){
		$userId = $this->tokenToUserArray($token);
		knightlover::load_model('userFactory');
		$ret = array();
		$ret['hostUID'] = (string)$buid;
		$ret['userUID'] = (string)$userId;
		$ret['dbId'] = (int)$cid;
		$ret['productId'] = (int)$iid;
		//$ret['act'] = (string)$act;
		$ret['flag'] = 3;
      	#初步验证有效性
		if(
			!is_numeric($buid)
			|| !is_numeric($cid)
			|| !is_numeric($iid)
			|| !is_numeric($act)
		){
			return $ret;
		}
		#驗證貨架有效性，把被動者資料拿出來比
		$bUser = userFactory::getInstance($buid);
		if(!$bUser->userInfo->lastaccess){
			return $ret;
		}
		if(is_array($bUser->ingameItem->items)){
			$tmp = $bUser->ingameItem->items[(int)$iid];
			//print_r($tmp);
			if($tmp){
				if($tmp['globalItemId']!=$cid){
					return $ret;
				}
			}else{
				return $ret;
			}
		}else{
			return $ret;
		}
		#实做
		$tmp = array();
		$tmp['buid'] = (string)$buid;
		$tmp['cid'] = (string)$cid;
		$tmp['iid'] = (string)$iid;
		$tmp['act'] = (string)$act;
		$rest = $this->add($userId,$tmp);
		$ret['flag'] = (int)$rest;
		#加经验
		if($rest==1){
		    $s = userFactory::getInstance($userId);
		    $s->userInfo->increaseExp(15);#成功加经验
		    $s->inject(true);
		}
		#返回
		return $ret;
	}
	
	/**
	 * 内部用的
	 */
	private function add($userId,$arr){
		knightlover::load_model('rackFactory');
		$rack = rackFactory::getInstance($userId);
		$rs = $rack->addCommit($arr);
		return $rs;
	}
	
	public function getRack($token,$fuid){
		$userId = $this->tokenToUserArray($token);
		if(!is_numeric($userId)||!is_numeric($fuid)){
			return false;
		}
		knightlover::load_model('rackFactory');
		if($fuid<1){
			$rack = rackFactory::getInstance($userId);
		}else{
			$rack = rackFactory::getInstance($fuid);
		}
		$rs = $rack->getInfo();
		return $rs;
	}
	
	public function clearRack($token,$t,$cid,$iid){
		$userId = $this->tokenToUserArray($token);
		knightlover::load_model('rackFactory');
		$rack = rackFactory::getInstance($userId);
		$rs = $rack->ClearRack(
			(string)$cid,
			(string)$iid,
			(int)$t
			);
		if($rs){#如果成功加金幣
			knightlover::load_model('userFactory');
		    $s = userFactory::getInstance($userId);
		    $s->userInfo->increaseCash(25);#成功加金币
		    $s->inject(true);			
		}
		
		$ret = array();
		$ret['hostUID'] = (string)$userId;
		$ret['dbId'] = (int)$cid;
		$ret['productId'] = (int)$iid;
		$ret['flag'] = ($rs)?3:1;#3成功；1失敗	
		
		return $ret;
	}
}
/*
//for test
$tmpU = '125665090805210|2.Ofk4hOkn0tieU_6DIfl_aQ__.3600.1285221600-1149318559|OHrysjXHquKSKCz1Ta-EksMa5uU';
$rack = new rack;

$rs = $rack->addCommit(
	$tmpU,
	'1149318559','2010014',531,'99'
	);
var_dump($rs);
	
var_dump($rack->clearRack($tmpU,time(),'2010014','531')	);

$rs = $rack->getRack($tmpU,'1149318559');
echo '<pre>';
print_r($rs);
echo '</pre>';
*/
