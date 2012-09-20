<?php
class monitorOBJ{
	var $userId;
	var $tableName = 'monitor';#用户资料表
	var $sysTableName = 'monitor_sys';#系统资料表
	var $mem_sys = 'monitor_sys';#系统的mem
	var $mem_del = 'monitor_del_hold';#不删除标记
	var $cache_able = true;

	function __construct($id){
		$this->userId = strval($id);
	}
	
	/*
	 * 添加用户的新信息
	 */
	public function addItem(&$item){
		$item['ts'] = (int)time();
		$item['time'] = (string)date('Y/m/d H:i:s',time() );
		knightlover::db()->getCollection($this->tableName);
		$options = array(
			'safe"' => true
		);
    	$rs = knightlover::db()->insert($item,$options)?true:false;
    	return $rs;
	}
	
	/**
	 * 获得自己的信息
	 * @return array 包含着当前用户监视器信息的数组
	 */
	public function getUserItems(){
		#delete old
		if($this->cache_able){# 判断缓冲
			$mark = knightlover::cache()->get($this->mem_del);
			if(!$mark){
				$this->delOldItems();
				//knightlover::cache()->set($this->mem_del,true,_CACHE_TIME_NORMAL);
				knightlover::cache()->set($this->mem_del,true,60*30);#30分中缓冲
			}
		}else{# 直接删除
			$this->delOldItems();
		}
		# 获得玩家和玩家之间的监视器信息
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array('touid' => $this->userId);
		//$item = array('k'=>true,'v'=>true,'tm'=>true,'fU'=>true,'fUN'=>true);
		$rs = $col->find($filter);
		$ret = array();
		while($rs->hasNext()){
			$r = $rs->getNext();
			$user = knightlover::platform()->getUserInfo($r['fuid']);
			$item = array();
			$item['type'] = $r['type'];
			$item['k'] = $r['k'];
			$item['v'] = $r['v'];
			$item['fuid'] = $r['fuid'];
			$item['funame'] = $r['funame'];
			$item['time'] = $r['time'];	
			$item['icon'] = $user['profilePic'];
			$ret[] = $item;
		}
		# 获得系统 监视器信息
		$sysArr = $this->getSysMonitor();
		# 合并两类信息
		if(is_array($sysArr)){
			foreach ($sysArr as $item){
				$ret[] = $item;
			}
		}
		#返回
		return $ret;
	}
	
	/*
	 * 删除两类信息中过期信息
	 * @return void
	 */
	private function delOldItems(){
		$d_time = time()-(5*86400);//5 day
		#用户表
		$col = knightlover::db()->getCollection($this->tableName);
		$filter = array(
			'ts' => array('$lt'=>$d_time)		
		);
		$options = array(
			'safe"' => true
		);
		$col->remove($filter,$options);
		#系统表
		$col = knightlover::db()->getCollection($this->sysTableName);
		$filter = array(
			'time' => array('$lt'=>$d_time),
			'case' => 'temp'	
		);
		$options = array(
			'safe"' => true
		);
		$col->remove($filter,$options);
	}
	
	/*
	 * 获取系统监视器信息
	 * @return array 结果数组
	 */
	private function getSysMonitor(){
		#到memcache取
		if($this->cache_able){
			$array = knightlover::cache()->get($this->mem_sys);
			if(is_array($array)){
				return $array;
			}
		}
		# 做日常信息添加操作
		$col = knightlover::db()->getCollection($this->sysTableName);
		$rs = $col->findOne(array('case'=>'daily','status'=>1));
		if($rs){//如果有日常记录
			$today=getdate(); 
			$day_this=mktime(0,0,0,$today['mon'],$today['mday'],$today['year']);//今天凌晨的时间戳
			if(time()>=$day_this+$rs['time']){#如果时间到了
				#判断有没有今天的
				$rs_check = $rs = $col->findOne(array('case'=>'temp','time'=>(int)($day_this+$rs['time'])));
					if(!$rs){
					#写当天的
					$new_item = array();
					$new_item['case'] = 'temp';
					$new_item['time'] = (int)($day_this+$rs['time']);
					$new_item['type'] = $rs['type'];
					$new_item['k'] = $rs['k'];
					$new_item['v'] = $rs['v'];
					$new_item['fuid'] = $rs['fuid'];
					$new_item['funame'] = $rs['funame'];
					$new_item['status'] = 1;
					$new_item['id'] = time().rand(100,999);
					
					$rs_add = $col->update(
						array('time'=>$new_item['time']),
						$new_item,
						array("safe"=>true,"upsert"=>true)
					);
				}
			}			
		}
		#获得系统信息结果集
		$rs = $col->find(
			array(
				'case'=>'temp',
				'status'=>1,
				'time'=>array('$lte'=>time()),//当前时间之后的不显示出来
			)
		);
		$rs = iterator_to_array($rs);
		$ret = array();
		foreach ($rs as $r){
			if($r['v']==''){continue;}#应蒋科要求，加这个判断
			$user = knightlover::platform()->getUserInfo($r['fuid']);
			$item = array();
			$item['type'] = (int)$r['type'];
			$item['k'] = $r['k'];
			$item['v'] = $r['v'];
			$item['fuid'] = $r['fuid'];
			$item['funame'] = $r['funame'];
			$item['time'] = (string)date('Y/m/d H:i:s',$r['time'] );
			$item['icon'] = $user['profilePic'];
			$ret[] = $item;
		}
		# 写缓冲 memcache
		if($this->cache_able){
			//knightlover::cache()->set($this->mem_sys,$ret,_CACHE_TIME_NORMAL);
			knightlover::cache()->set($this->mem_sys,$ret,60*30);#30分中缓冲
		}
		# 返回数组
		return $ret;		
	}
}
