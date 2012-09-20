<?
include_once('../config.php');
include_once('../knightlover.php');

class npc_lili{
	private $npc_id = '100001582621163';
	/*
	public function getInfobak(){
		$touid = $this->npc_id;
		//$touid = '1149318559';
		$rs = knightlover::fb()->api('/'.$touid.'?locale=en_US');
		knightlover::load_model('userFactory');
		knightlover::load_model('adapterClass');
		$s = userFactory::getInstance($touid);
		$s->username = $rs['name'];
		$s->profilePic = 'http://graph.facebook.com/'.$touid.'/picture';
		$s->userInfo->gender = $rs['gender'];
		$tmp = new userBasic($s,1);
		return $tmp;
	}
	*/
	private function getStreet(){
		$touid = $this->npc_id;
		$rs = knightlover::fb()->api('/'.$touid.'?locale=en_US');
	        knightlover::load_model('userFactory');
	        knightlover::load_model('adapterClass');
        	$s = userFactory::getInstance($touid);
	        $s->username = $rs['name'];
	        $s->profilePic = 'http://graph.facebook.com/'.$rs['id'].'/picture';
	        $user = new userBasic($s);
	        //$user->coins = knightlover::systemInfo()->getCoins($touid,$rs['id']);
		return $user;
	}

	private function getShop(){
		$touid = $this->npc_id;
		$rs = knightlover::fb()->api('/'.$touid.'?locale=en_US');
		knightlover::load_model('userFactory');
		knightlover::load_model('adapterClass');
		$s = userFactory::getInstance($touid);
		$s->username = $rs['name'];
		$s->profilePic = 'http://graph.facebook.com/'.$touid.'/picture';
		$s->userInfo->gender = $rs['gender'];
		$tmp = new userBasic($s,2);
		//$tmp->coins = knightlover::systemInfo()->getCoins($uid,$touid);
		$tmp->ulevel = knightlover::systemInfo()->ulevel(intval($s->userInfo->level));
		//$tmp->coins = knightlover::systemInfo()->getCoins($uid,$touid);
		//$tmp->servertime = $this->servertime();
		return $tmp;

	}
	
	public function getInfo($func=0){
		if($func==0){
			return $this->getStreet();
		}else{
			return $this->getShop();
		}
	}

}

//$npc = new npc_lili;
//$rs = $npc->getInfo();
//$rs = $npc->getShop();
//$rs = $npc->getStreet();
//print_r($rs);
?>
