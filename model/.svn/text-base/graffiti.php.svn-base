<?php
/*
include_once('../config.php');
include_once('../knightlover.php');

ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

$gra = new graffiti();

$arr = array();
$arr['x'] = 11;
$arr['y'] = 22;
$arr['dbId'] = 1212121;
$gra->addGraffiti('1149318559',$arr);

$gra->clearGraffiti('1149318559',11,22);

print_r($gra->getGraffiti('1149318559'));
*/

class graffiti{
	var $userId;
	var $tableName = 'graffiti';#用户资料表
	var $mem_start = 'graff_';#memcache开头
	var $mem_d_start = 'graff_d_';#memcache中记录每日信息的开头
	var $max = 10;#上限
	var $maxbyDay = 10;#每人每日上限
	//var $cache_able = true;
	
	/*
	 * 添加新涂鸦
	 */
	public function addGraffiti($uid,&$item){
		$memName = $this->mem_d_start . (string)$uid . '_'.date('ymd');
		#验证每日10次
		$thisDay = knightlover::cache()->get($memName);
		if($thisDay){#如果该用户当天已经满了maxbyDay次，直接返回成功不加经验值
			if($thisDay>=$this->maxbyDay){
				return false;
			}
		}else{
			$thisDay = 0;
		}		
		
		$coll = knightlover::db()->getCollection($this->tableName);
		#是否超过上限
		$filter = array('uid'=>(string)$uid);
		$tmp = $coll->find($filter)->count();
		if($tmp>=$this->max){
			return false;
		}
		#插入
		$item['t'] = (int)time();
		$item['uid'] = (string)$uid;
		$item['x'] = (int)$item['x'];
		$item['y'] = (int)$item['y'];
		$item['dbId'] = (int)$item['dbId'];
		$options = array(
			'safe' => true
		);
    	$rs = $coll->insert($item,$options);
		if($rs['ok']=='1'){
			#清空memcache
			knightlover::cache()->flush($this->mem_start.$uid);
			#写每日memcache
			knightlover::cache()->set($memName,$thisDay+1,60*60*24);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 清除某信息
	 * @return bool
	 */
	public function clearGraffiti($uid,$x,$y){
		$coll = knightlover::db()->getCollection($this->tableName);
		$filter = array('uid'=>(string)$uid,'x'=>(int)$x,'y'=>(int)$y);
		$rs = $coll->findOne($filter);
		if($rs){
			$filter = array('uid'=>(string)$uid,'x'=>(int)$x,'y'=>(int)$y);
			$options = array('safe'=>true,'justOne'=>true);			
			$coll->remove($filter,$options);
			#清空memcache
			knightlover::cache()->flush($this->mem_start.$uid);
			return true;
		}else{
			return false;
		}
	}
	
	/*
	 * 获取某人的
	 * @return array
	 */
	public function getGraffiti($uid){
		#当前人的memcache(当日)
		$memName = $this->mem_d_start . (string)$uid . '_'.date('ymd');
		#获取当日数据
		$thisDay = knightlover::cache()->get($memName);
		if(!$thisDay){
			$thisDay = 0;
		}		
		#读memcache，如果有，直接返回
		$tmp = knightlover::cache()->get($this->mem_start.$uid);
		if(is_array($tmp)){
			$data = array();
			$data['data'] = $tmp;
			$data['count'] = $thisDay;
			return $data;			
		}
		
		#实际去数据库读
		$coll = knightlover::db()->getCollection($this->tableName);
		$filter = array('uid'=>(string)$uid);
		$field = array('x'=>true,'y'=>true,'dbId'=>true);
		$rs = $coll->find($filter,$field);
		$ret = array();
		while($rs->hasNext()){
			$r = $rs->getNext();
			$item = array();
			$item['x'] = $r['x'];
			$item['y'] = $r['y'];
			$item['dbId'] = $r['dbId'];
			$ret[] = $item;
		}
		#写memcache
		knightlover::cache()->set($this->mem_start.$uid,$ret,0);
		
		#返回
		$data = array();
		$data['data'] = $ret;
		$data['count'] = $thisDay;
		return $data;
	}	
}
