<?php
include_once('../config.php');
include_once('../knightlover.php');

class monitor{
	private function tokenToUserArray($token){
		// get token from cache first
		/*
		$uid = knightlover::cache()->get('fb_token_'.$token);
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

	public function addAction($token,$myName,$touserId,$items){
		$userId = $this->tokenToUserArray($token);
		knightlover::load_model('monitorFactory');
		$monitor = monitorFactory::getInstance($userId);
		foreach($items as $item){
			/*type,k,v*/
			if(!is_numeric($item['type'])){continue;}
			if(strlen($item['k'])>20 || strlen($item['v'])>20 ){continue;}
			//$item['k'] = $obj->k;
			//$item['v'] = $obj->v;
			$item['fuid'] = (string)$userId;
			$item['funame'] = (string)$myName;
			$item['touid'] = (string)$touserId;
			$monitor->addItem($item);
		}
		return true;
	}
	
	public function getMyMonitor($token){
		$userId = $this->tokenToUserArray($token);
		knightlover::load_model('monitorFactory');
		$monitor = monitorFactory::getInstance($userId);		
		$rs = $monitor->getUserItems();
		//var_dump($rs);
		return $rs;
	}
} 

//for test
//$monitor = new monitor();
/*
$monitor->addAction(
	'125665090805210|2.G1IFWGnAulYGfRro7w9K2g__.3600.1281427200-1149318559|O0Bb64P2dyhoRoGBFRFeEdUJN1I.',
	'aa',
	'1149318559',
	array(
		array('k'=>'kk','v'=>'vv'),
		array('k'=>'kk','v'=>'vv'),
	)
);
*/
//$k = $monitor->getMyMonitor('125665090805210|2.fhfss7cye69epwItTEq5rA__.3600.1281506400-1149318559|GI7pHTGlIfNxOXGNEtenG9d3Bpg.');
//var_dump($k);
?>
