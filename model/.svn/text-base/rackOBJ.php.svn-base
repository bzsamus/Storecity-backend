<?php
class rackOBJ{
	var $userId;
	var $tableName = 'rack_log';#日誌表
	var $mem_del = 'rackMark';#不删除标记
	var $memStart = 'rank_day_';#每日记录
	var $cache_able = true;
	var $currTime = 0;#当前时间戳
	var $currBase = 0;#当前最近的时间点
	var $maxbyDay = 30;#每日没人被点上限

	function __construct($id){
		$this->userId = strval($id);
		$this->currTime = time();
		$tmp0 = mktime(0,0,0,
			date('m',$this->currTime),
			date('d',$this->currTime),
			date('Y',$this->currTime)
			);
		$tmp = floor( ($this->currTime-$tmp0)/(60*60*6)	);
		$this->currBase = $tmp0+$tmp*(60*60*6);		
	}
	
	/*
	 * 添加用户的新信息
	 * @return int 1：成功加经验，2：成功不加经验，3：失败
	 */
	public function addCommit(&$item){
		$memName = $this->memStart . (string)$item['buid'] . '_'.date('ymd');
		#验证每日30次
		$thisDay = knightlover::cache()->get($memName);
		if($thisDay){#如果该用户当天已经满了maxbyDay次，直接返回成功不加经验值
			if($thisDay>=$this->maxbyDay){
				return 2;
			}
		}else{
			$thisDay = 0;
		}

		$_canDo = true;
		#獲得貨架信息
		$rs = $this->getRackInfo(
			(string)$item['buid'],
			(string)$item['cid'],
			(string)$item['iid']
			);
		//print_r($rs);
		#貨架上限驗證
		if(count($rs)>=3){$_canDo = false;}
		#個人評論驗證
		$tmp = 0;
		foreach ($rs as $t){
			if($t['auid']==$this->userId){$tmp++;}
		}
		if($tmp>0){$_canDo = false;}
		#執行
		if($_canDo){
			$item['auid'] = (string)$this->userId;
			$item['t'] = (int)time();
			$coll = knightlover::db()->getCollection($this->tableName);
			$options = array(
				'safe' => true
			);
	    	$rs = $coll->insert($item,$options);
			if($rs['ok']=='1'){
				#加memcache
				knightlover::cache()->set($memName,$thisDay+1,60*60*24);
				return 1;
			}else{
				return 3;
			}
		}
		return 2;
	}
	/**
	 * 内部用的用来帮助添加评论是检查是否该货架评论已满等
	 * @param unknown_type $buid
	 * @param unknown_type $cid
	 * @param unknown_type $iid
	 * @return array 
	 */
	private function getRackInfo($buid,$cid,$iid){
		#读数据
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array(
			'buid' => (string)$buid,
			'cid' => (string)$cid,
			'iid' => (string)$iid,
			't'=>array('$gte'=>$this->currBase)
		);
		//print_r($filter);
		$item = array('auid'=>true);
		$ret = $col->find($filter,$item);
		return $ret;
	}
	
	/**
	 * 获得自己的被动信息
	 * @return array 包含着当前用户貨架信息的数组
	 */
	public function getInfo(){
		#delete old
		if($this->cache_able){# 判断缓冲
			$mark = knightlover::cache()->get($this->mem_del);
			if(!$mark){
				$this->delOldItems();
				knightlover::cache()->set($this->mem_del,true,60*60);#60分中缓冲
			}
		}else{# 直接删除
			$this->delOldItems();
		}
		
		#当前人的memcache
		$memName = $this->memStart . (string)$this->userId . '_'.date('ymd');
		#获取当日数据
		$thisDay = knightlover::cache()->get($memName);
		if(!$thisDay){
			$thisDay = 0;
		}		
		
		# 獲得當前玩家貨架評價信息
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array(
			'buid' => (string)$this->userId,
			't'=>array('$gte'=>$this->currBase)
		);
		$item = array('t'=>true,'auid'=>true,'cid'=>true,'iid'=>true,'act'=>true);
		$rs = $col->find($filter,$item)->sort(array('t'=>1));
		//$ret = iterator_to_array($rs);
		$ret = array();
		while($rs->hasNext()){
			$r = $rs->getNext();
			$item = array();
			$item['dbId'] = $r['cid'];#7位的物品ID
			$item['productId'] = $r['iid'];#數據庫中的索引ID
			$item['userUID'] = $r['auid'];#主動用戶ID
			$item['time'] = $r['t'];#時間戳
			$item['flag'] = $r['act'];#動作ID
			$ret[] = $item;
		}
		#返回
		$data = array();
		$data['data'] = $ret;
		$data['count'] = $thisDay;
		return $data;
	}
	/**
	 * 清空自己的某货架信息
	 * 
	 */
	public function ClearRack($cid,$iid,$time){
		#用户表
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array(
			't' => array('$lte'=>(int)$time),
			'cid'=>	(string)$cid,
			'iid'=> (string)$iid,
			'buid'=> (string)$this->userId	
		);
		$options = array(
			'safe' => true
		);
		#判斷是否存在
		$count = $col->find($filter,array('t'=>true))->count();
		if(!$count || $count<1){
			return false;
		}
		#刪除
		$rs = $col->remove($filter,$options);
		if($rs['ok']=='1'){
			return true;
		}else{
			return false;
		}
	}
	
	/*
	 * 删除过期信息
	 * @return void
	 */
	private function delOldItems(){
		#用户表
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array(
			't' => array('$lt'=>$this->currBase)		
		);
		$options = array(
			'safe' => true
		);
		$col->remove($filter,$options);
	}
}