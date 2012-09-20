<?php

class user{
  private $facebookInfo;
  private $userInfo;
  private $shop;
  private $ingameItem;
  private $inventory;
  private $userId;

  public function __construct($id){
    $this->userId = strval($id);
  }

/**
 * ------------------------------------------
 * override __get for lazy loading
 * ------------------------------------------
 */
  public function __get($var){
    if(!isset($this->$var)){
      if($var == 'dbdata'){
        //fetching from cache or database;
        $obj = knightlover::cache()->get('fb_'.$this->userId);
        if(!$obj){
          $hash = (string)intval(substr($this->userId,-2));
	  knightlover::db()->getCollection(get_class($this).$hash);
          $filter = array('userId' => $this->userId);
          $obj = knightlover::db()->findOne($filter);
          if($obj){
            // should cache the obj
            knightlover::cache()->set('fb_'.$this->userId,$obj,_CACHE_TIME_NORMAL);
          }
          else{
            // user not exist, use dummy instead
            $obj = null;
          }
        }
        $this->$var = &$obj;
      }
      else{
        $this->$var = knightlover::objhandler()->getObject($var);
        eval('$this->_load'.$var.'();'); 
      }
    }
    return $this->$var;
  }
/**
 * ----------------------------------------
 *  loading functions triggered from _get()
 * ----------------------------------------
 */
  function _loaduserInfo(){
    //populate userinfo from dbdata
    if(isset($this->userInfo))
      $this->userInfo->setVars($this->dbdata['userInfo']);
  }

  function _loadinventory(){
    //populate inventory from dbdata
    $this->inventory->setVars($this->dbdata['inventory']['items']);
  }

  function _loadingameItem(){
    //populate ingame item from dbdata
    $this->ingameItem->setVars($this->dbdata['ingameItem']['items']);
    $this->ingameItem->streetItems = $this->dbdata['ingameItem']['streetItems'];
  }

  function _loadshop(){
    //knightlover::load_model('shopFactory');
    //$shop = shopFactory::getInstance();
    $this->shop->floors = $this->dbdata['shop']['floors'];
    $this->shop->employees = $this->dbdata['shop']['employees'];
    $this->shop->shopName = $this->dbdata['shop']['shopName'];
    $this->shop->w = $this->dbdata['shop']['w'];
    $this->shop->l = $this->dbdata['shop']['l'];
    //$this->shop->graffiti = $this->dbdata['shop']['graffiti'];
  }

  function _loadfacebookInfo(){
    knightlover::load_model('facebookInfoFactory');
    $this->facebookInfo = facebookInfoFactory::getInstance($this->userId);
  }

/**
 * ----------------------------------
 * model controlling functions below
 * ----------------------------------
**/

/**
 *  initialize a new user
 *  do not use for exist user
**/
  public function init(){
    knightlover::load_model('userInfoFactory');
    $this->userInfo = userInfoFactory::getInstance(array(
      'token'=>0,
      'cash' => 0,
      'level' => 0,
      'exp' => 0,
      'like' => 5,
      'lastaccess' => time(),
      'playcount' => 1,
      'tutorial' => -1,
      'expt' => 0,
      'valid' => 1
    ));
    knightlover::load_model('facebookInfoFactory');
    $this->facebookInfo = facebookInfoFactory::getInstance($this->userId);
    knightlover::load_model('shopFactory');
    $this->shop = shopFactory::getNewShop($this->facebookInfo->username);
    knightlover::load_model('employeeFactory');
    $firstEmployee = array();
    $firstEmployee['id'] = $this->userId;
    $firstEmployee['clothes'] = array();
    $firstEmployee['task'] = 0;
    $firstEmployee['notify'] = false;
    $this->shop->addEmployee(array($firstEmployee));
    knightlover::load_model('inventoryFactory');
    $this->inventory = inventoryFactory::getInstance();
    knightlover::load_model('ingameItemFactory');
    $this->ingameItem = ingameItemFactory::getInstance(array(
        '0' => array('id'=>'0','globalItemId'=>'4060012','positionX'=>0,'positionY'=>0,'data'=>0),
        '1' => array('id'=>'1','globalItemId'=>'4060012','positionX'=>0,'positionY'=>0,'data'=>1),
        '2' => array('id'=>'2','globalItemId'=>'2050002','positionX'=>0,'positionY'=>5,'data'=>2),
        '3' => array('id'=>'3','globalItemId'=>'2010004','positionX'=>4,'positionY'=>4,'data'=>0),
	'4' => array('id'=>'4','globalItemId'=>'4090001','positionX'=>3,'positionY'=>-1),
    ));
    $this->ingameItem->streetItems = array(
      '5' => array('id'=>'5','globalItemId'=>'5010001','positionX'=>0,'positionY'=>0),
      '6' => array('id'=>'6','globalItemId'=>'5070001','positionX'=>0,'positionY'=>0),
      '7' => array('id'=>'7','globalItemId'=>'5020026','positionX'=>0,'positionY'=>0),
    );
    // save
    unset($this->dbdata);
    unset($this->facebookInfo);
    return $this->save();
  }

/**
 * fetch user attributes from data source and infuse
 * to current object
 *
**/
  public function infuse(){

  }


/**
 * 
 * inject current object data back to database
 *
**/
  public function inject($async=false){
    if(isset($this->dbdata))
      unset($this->dbdata);
    if(isset($this->facebookInfo))
      unset($this->facebookInfo);
    $user = get_object_vars($this);
    foreach($user as $i=>$u){
      if($u === NULL){
        unset($user[$i]);
      }
    }
    $filter = array('userId' => $this->userId);
    $update = array(
      '$set'  =>  $user
    );
    $options = array(
      'multiple' => false
    );
    if($async){
      $options['fsync'] = true;
    }
    $hash = (string)intval(substr($this->userId,-2));
    knightlover::db()->getCollection(get_class($this).$hash);
    $rs = knightlover::db()->update($filter,$update,$options)?true:false;
    if($rs){
      // cached data out of sync, flush it down to the toliet
      knightlover::cache()->flush('fb_'.$this->userId);
    }
    return $rs;
  }


/**
 *
 *  save function used by init()
 *  used upsert method which will insert new entry when update failed
 *
**/
  public function save(){
    $user = get_object_vars($this);
    
    $filter = array('userId' => $this->userId);
    $update = array(
      '$set'  =>  $user
    );
    
    $options= array(
      'mltiple' => false,
      'upsert'  => true,
      'fsync'    => true
    );
    $hash = (string)intval(substr($this->userId,-2));
    knightlover::db()->getCollection(get_class($this).$hash);
    return knightlover::db()->update($filter,$update,$options)?true:false;
  }


  public function remove(){
    $filter = array('userId' => $this->userId);
    $options = array(
      'fsync' =>  true
    );
    $hash = (string)intval(substr($this->userId,-2));
    knightlover::db()->getCollection(get_class($this).$hash);
    return knightlover::db()->remove($filter,$update,$options)?true:false;
  }
/**
 *
 * immediate offline earning calculation
 *
**/
  public function offlineEarning(){
	$lasttime = $this->userInfo->lastaccess;
	$currenttime = time();
	$dt = $currenttime - $lasttime;	
	$dt = min($dt,60*60*6); //max 6 hr
	$dayCounterLifeTime = array(); 
	$nightCounterLifeTime = array();
	$daySpeshelfLifeTime = array(); 
	$nightSpeshelfLifeTime = array(); 
	$counterLifeTime = array();
	$speshelfLifeTime = array();

	$dayTime = ($lasttime+28800)%43200;
	if($dayTime < 21600){
	   $dayNight = 0;
	   $leftTime = 21600 - $dayTime;
	}else{
	   $dayNight = 1;
	   $leftTime = 43200 - $dayTime;
	}
	 $tempArray = $this->dayNightTime($dt,$leftTime,$dayNight);
	 $dayDt = $tempArray[0]; 
	 $nightDt = $tempArray[1];

	$data = knightlover::systemInfo()->getOfflineData($this->userId);
	if(is_array($data['counter'])){
		foreach($data['counter'] as $c){
			$counterLifeTime[] = $this->shop->employees[$c[1]]['lifetime'];
			if($this->shop->employees[$c[1]]['lifetime'] > $et){
            			$et = $this->shop->employees[$c[1]]['lifetime'];
          		}
        	}
    	}
	if(is_array($data['speshelf'])){
        	foreach($data['speshelf'] as $s){
          		$speshelfLifeTime[] = $this->shop->employees[$s[1]]['lifetime'];
          		if($this->shop->employees[$s[1]]['lifetime'] > $et){
            			$et = $this->shop->employees[$s[1]]['lifetime'];
          		}
        	}
    	}

	foreach($counterLifeTime as $cTime){
	 $tempArray = $this->dayNightTime($cTime,$leftTime,$dayNight);
	 $dayCounterLifeTime[] =  $tempArray[0];
	 $nightCounterLifeTime[] = $tempArray[1];
	}
	foreach( $speshelfLifeTime as $sTime){
	  $tempArray = $this->dayNightTime($cTime,$leftTime,$dayNight);
	  $daySpeshelfLifeTime[] =  $tempArray[0];
	  $nightSpeshelfLifeTime[] = $tempArray[1];
	}
    
	$dayResult = $this->offlineCalculate($dayDt,'d',$dayCounterLifeTime,$daySpeshelfLifeTime);
	$nightResult = $this->offlineCalculate($nightDt,'n',$nightCounterLifeTime,$nightSpeshelfLifeTime);
    $offlineCash = $dayResult['cash'] + $nightResult['cash'];
    $offlineExp = $dayResult['exp'] + $nightResult['exp'];
    // calculate employee lifetime
    $result = array('cash' => $offlineCash,
	         'exp' => $offlineExp
		);
    if(__SITE__ENV == 'dev'){
      $result['day'] = $dayResult;
      $result['night'] = $nightResult;
    }
    $this->shop->employeeTick();
    $this->userInfo->lastaccess = $currenttime;
    $this->userInfo->increaseCash($offlineCash);
    $this->userInfo->increaseExp($offlineExp);
    $this->inject();
    return $result;
  }

  private function dayNightTime($dtFun,$leftTimeFun,$dayNightFun){
     if($dtFun <= $leftTimeFun){
	    
		$firstTempTime = $dtFun;
		$secondTempTime = 0;
		
	}else{
	   
	   $subTempTime = $dtFun-$leftTimeFun;
	   $dayNightNum = floor($subTempTime/21600);
	   
       $secondTempNum = round($dayNightNum/2);
	   $firstTempNum =  $dayNightNum - $secondTempNum;
		
	   
	   $lastDayTime = $subTempTime%21600;
	 
	  
	   $firstTempTime = $leftTimeFun + $firstTempNum*21600;
	   $secondTempTime = $secondTempNum*21600;
	  
	  if($dayNightNum%2 == 0){
	      $secondTempTime += $lastDayTime;
	  }else{
	    $firstTempTime += $lastDayTime;
	  }
	  
	
	}
	
    if($dayNightFun > 0){
	    return array($secondTempTime,$firstTempTime);
    }else{
	   return array($firstTempTime,$secondTempTime);
	}
  
  }

  public function offlineCalculate($dt,$dn,$counterLifeTime,$speshelfLifeTime){
    $data = knightlover::systemInfo()->getOfflineData($this->userId);
    $doorNum = 0;
    if($dt < 60 || $data['door'] == 0 || (($data['shelfNum'] == 0 || $data['counterNum'] == 0) && $data['speShelfNum'] == 0)){  // lastaccess > 1min to determine user has gone offline
      $this->shop->employeeTick();
      $this->userInfo->lastaccess = $currenttime;
      $this->inject();
      return array( 'cash' => 0,
                  'exp'  => 0
                ); 
    }
    $maxcash = 45000;
    $maxexp = 75000;
    // first get Ik
    $ik = 0;
    // then we get Ek
    $ek = 0;
    $tik = 0;
    $tek = 0;
    $et = 0;
    $dk = 0;
    $dkt = 0;
	$shelfDemand = 0;
	$totalGoodsNum = 0;
	$vGoodsNum = 0;
	
	$counterCheckoutTime = array();
	$speshelfCheckoutTime = array();
	$speshelfDemand = array();
	$speshelfik = array();
	$speshelfek =array();
	
    foreach($counterLifeTime as $c){
      if($c > $et){
        $et = $c;
      }
    }
    foreach($speshelfLifeTime as $s){
      if($s > $et){
        $et = $s;
      }
    }
    if($et < $dt){
      $dt = $et;
    }
    $dt = min($dt,60*60*6); //max 6 hr
    // step 2
    // step 3
    $gtotal = 0;
    $shelfnum = 0;
    $speshelf = 0;
    $decorVal = 0;
    $dn = knightlover::systemInfo()->daynight($this->userInfo->lastaccess).'v';
    $goods = array();
    $hots = array();
    $todayhot = knightlover::systemInfo()->getHotGoods();
    
    foreach($this->ingameItem->items as $i){
      $key = substr($i['globalItemId'],0,2);
      $good = knightlover::globalitem()->getItem($i['globalItemId']);
      if($good['decorValue'] > 0){
        $decorVal += $good['decorValue'];
      }
    
      // get all the goods in use
      if($key == '10' && $data['shelf'][strval($i['employeeId'])] && $data['counterNum'] > 0){
          if(in_array($i['globalItemId'],$todayhot)){
	    $good[$dn] += 5;
            $hots[$i['globalItemId']] = $good;
          }
	  $ik += $good['price'] * $good[$dn];
          $ek += $good['exp'] * $good[$dn];
          $tik += $good['price'];
          $tek += $good['exp'];
	      $gtotal += $good[$dn];
	    if($good[$dn] > 0){
		    $vGoodsNum ++;
            $goods[$i['globalItemId']] = $good;
		  }
		  $totalGoodsNum ++;
	  $dk += $good[$dn];
	  $dkt += $good[$dn];
          $shelfDemand += $good[$dn];
      }
      // shelf
      $key = substr($i['globalItemId'],0,3);
      if($key == '201'){
	     $shelfnum++;
      }
      if($key == '205' && $data['counter'][strval($i['id'])]){
	  
	      $counterCheckoutTime[] = $good['checkoutTime']/22;
          //$good['checkoutTime']; cash counter checkout time
      }
      if($key == '301' && $data['speshelf'][strval($i['id'])]){
          	$speshelf++; 
		$speshelfik[] = $good['price'];
		$speshelfek[] = $good['exp'];
          	$tik += $good['price'];
          	$tek += $good['exp'];
	      	$dk += $good[$dn];
          	$dkt += $good[$dn];
		if($good[$dn] > 0){
			$vGoodsNum++;
		}
		$totalGoodsNum++;
		$gtotal += $good[$dn];
		$speshelfDemand[] = $good[$dn];
	      	$speshelfCheckoutTime[] = $good['checkoutTime']/22;
			
	  // $good['checkoutTime']; special shelf checkout time
      }
      if($key == '409'){
        $doorNum++;
      }
    }
    $decorVal = $decorVal?$decorVal/5:0;
    $maxCharm = knightlover::systemInfo()->lvMaxCharm($this->userInfo->level);
    $charm = min($maxCharm,$this->userInfo->like * 1.6 + $decorVal);
	$tempCharm = $charm;
	if($charm>=100){
	   $tempCharm = 100.8;
	}
    $tdur = 10-floor(($tempCharm - 8)*10/16)*0.13;
    $x = knightlover::systemInfo()->itemnumLv($this->userInfo->level);
    $y = $data['counterNum']?sizeof($goods):0;
    $z = $data['counterNum']?sizeof($hots):0;
    // step 4
    if($x > 0 && $tdur > 0 && $doorNum > 0 && $data['door'] > 0){
      $ys = ($y+$data['speShelfNum'])/$x > 1?1:($y+$data['speShelfNum'])/$x;
	$tempGoodsPer=0;
	if($totalGoodsNum>0){
		$tempGoodsPer=$vGoodsNum/$totalGoodsNum;
	}
	$ps = min(1,($ys * 0.8)) * pow(1.02,$z) * $tempGoodsPer;
      if( $doorNum > 0){
	$n = floor($dt/$tdur * $ps * $data['door']/$doorNum);
      }
      if($ps > 0 && $data['door'] > 0 ){
        $tin = $tdur/$ps * ($doorNum/$data['door']);
      }
      else{
	$tin = 0;
      }
    }
    else{
      $ps = 0;
      $n = 0;
      $tin = 0;
    }
    //step 5
    if($gtotal > 0){
      $shelfPk = $shelfDemand / $gtotal;
    }

    // step 6 TODO available spaces infront of shelf
	
    $pkdash = 1;
	
    // step 7 TODO counter checkout time calculation
	
	$pkdashdash = 0;
	
	
    if($shelfPk > 0){
      $ttin = $tin/$shelfPk;
    }
	$boo = 1;
	foreach($counterLifeTime as $lt){
	    if($lt < $dt){
		   $boo = 0;
		   break;
		}	   
	}
	$counterN = count($counterCheckoutTime);
	if($boo > 0 && $counterN >0 ){
	   $totalCt = array_sum($counterCheckoutTime);
	   $avCt = $totalCt / ($counterN * $counterN);
	   if( $avCt <= $ttin ){
	       $pkdashdash = 1;
	   }else if($avCt > 0){
	       $pkdashdash = $ttin / $avCt;
	   }
	   
	}else{
	    asort($counterLifeTime);
		$timeP = array();
		$tempT = 0;
		$counterCheckoutTimeSort = array();
		$maxCounterLifeTime = 0;
		foreach($counterLifeTime as $kct=>$ctt){
		  $timeP[] = $ctt - $tempT;
		  $tempT = $ctt;
		  $counterCheckoutTimeSort[] = $counterCheckoutTime[$kct];
		  $maxCounterLifeTime = $ctt;
	    }
		
		foreach($counterCheckoutTime as $kk=>$ag){
		  $totalCt = array_sum($counterCheckoutTimeSort);
		  array_shift($counterCheckoutTimeSort);
		  if($counterN > 0){
		  	$avCt = $totalCt / ($counterN * $counterN);
		  }
		  if( $avCt <= $ttin ){
	            $pkdashPoint = 1;
	          }else if($avCt > 0){
	            $pkdashPoint = $ttin / $avCt;
	          }
		  if($maxCounterLifeTime > 0){
		    $pkdashdash += $pkdashPoint* $timeP[$kk]/$maxCounterLifeTime;
		  }
		  $counterN--;
		}
		
	}
    // step 8 here are the Nk
    $nk = $n * $shelfPk * $pkdash * $pkdashdash;
    $gtotal = max(1,$gtotal);
    $reduce = 0.8;
    $tik = max(1,$tik);
    $tek = max(1,$tek);
    $offlineCash = 0;
    $offlineExp = 0;
    if($shelfDemand > 0){
      $offlineCash = ($ik/$shelfDemand) * $nk ;
      $offlineExp = ($ek/$shelfDemand) * $nk ;
    }
	foreach($speshelfLifeTime as $kk=>$slt){
	  if($slt >= $dt){
	      $speP = 1;
	  }else{
	      $speP = $slt / $dt;
	  }
	  $offlineCash += $speshelfik[$kk]*($speshelfDemand[$kk]/$gtotal)*$n*$speP;
	  $offlineExp += $speshelfek[$kk]*($speshelfDemand[$kk]/$gtotal)*$n*$speP;
	  
	}
    $offlineCash = min($maxcash,intval($offlineCash * $reduce));
    $offlineExp = min($maxexp,intval($offlineExp * $reduce));
    return array( 'cash' => $offlineCash,
                  'exp'  => $offlineExp,
		  'dt' => $dt,
		  'counterCheckoutTime' => $counterCheckoutTime,
		  'speshelfCheckoutTime' => $speshelfCheckoutTime,
		  'shelfDemand' => $shelfDemand,
		  'ik' => $ik,
		  'ek' => $ek,
		  'charm' => $charm,
		  'x' => $x,
		  'y' => $y,
		  'z' => $z,
		  'ps' => $ps,
		  'n' => $n,
		  'tin' => $tin,
		  'shelfPk' => $shelfPk,
		  'pkdashdash' => $pkdashdash,
		  'speshelfik' => $speshelfik,
		  'speshelfek' => $speshelfek,
		  'speshelfDemand' => $speshelfDemand
                );

  }


/**
 *
 * graffiti functions.. can't think of a better place to put it
 *
**/
	public function getGraffiti(){
		knightlover::load_model('graffiti');
		$graff = new graffiti;
		return $graff->getGraffiti($this->userId);
	}  
	public function addGraffiti($g){
		knightlover::load_model('graffiti');
		$graff = new graffiti;
		return $graff->addGraffiti($this->userId,$g);
	}
	public function cleanGraffiti($g){
		knightlover::load_model('graffiti');
		$graff = new graffiti;
		return $graff->clearGraffiti($this->userId,$g['x'],$g['y']);			
	}
  /*
  public function getGraffiti(){
    $graffiti = knightlover::cache()->get(md5('graffiti'.$this->userId));
    if(!$graffiti){
      return array();
    }
    else{
      return $graffiti;
    }
  }

  public function addGraffiti($g){
    $key = md5('graffiti'.$this->userId);
    $maxnum = 10;
    $graffiti = knightlover::cache()->get($key);
    if(!$graffiti){
      $graffiti = array();
    }
    if(sizeof($graffiti) >= $maxnum){
      //array_shift($graffiti);
      return false;
    }
    array_push($graffiti,$g);
    knightlover::cache()->set($key,$graffiti,0);
    return true;
  }

  public function cleanGraffiti($g){
    $key = md5('graffiti'.$this->userId);
    $graffiti = knightlover::cache()->get($key);
    if(is_array($graffiti)){
      foreach($graffiti as $i=>$gr){
        $valid = false;
        if($gr['x'] == $g['x'] && $gr['y'] == $g['y']){
          unset($graffiti[$i]);
          $valid = true;
          break;
        }
      }
      knightlover::cache()->set($key,$graffiti,0);
      return $valid;
    }
    else{
      return false;
    }
  }*/
}
?>
