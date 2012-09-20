<?php
class gamelogic{

/**
        '0'   =>  'dummy',
        '3'   =>  'buyInventory',
        '4'   =>  'sellIngame',
        '5'   =>  'toIngame',
        '11'  =>  'addGraffiti',
        '15'  =>  'hireEmployee',
        '18'  =>  'cleanGraffiti',
        '19'  =>  'sellInventory',
        '22'  =>  'buyIngame',
        '23'  =>  'saveItem',
        '27'  =>  'saveFloor',
        '36'  =>  'updateEmployee',
        '39'  =>  'refreshEmployee',
        '44'  =>  'fireEmployee',
        '49'  =>  'offlineEarning',
        '51'  =>  'toInventory',
        '55'  =>  'updateTutorial',
        '66'  =>  'pickCoin',
        '70'  =>  'expendShop',
        '73'  =>  'repairCounter',
        '82'  =>  'setShopName',
        '98'  =>  'saveData',
        '99'  =>  'datasync'
**/

/**
 * ---------------------------------------------
 *  add graffiti to user shop
 * ---------------------------------------------
 *
**/
  
    function addGraffiti(&$user,&$g,&$returnvar){
      knightlover::load_model('userFactory');
      $s = userFactory::getInstance($g['uid']);
      unset($g['uid']);
      if($s->addGraffiti($g)){
        //echo '1';
        $user->userInfo->increaseExp(15);#�ɹ��Ӿ���
        $user->inject();#ֻ�����@�e�Ȍ�������һ��
        echo '5';
      }else{
      	//echo '2';
      	echo '6';
      }
    }

/**
 * ----------------------------------------------
 *  clean graffiti
 * ----------------------------------------------
**/
    function cleanGraffiti(&$user,$g,&$returnvar){
      if($user->cleanGraffiti($g)){
        //echo '1';
        $user->userInfo->increaseCash(25);#�ɹ��ӽ��
        $user->inject();#ֻ�����@�e�Ȍ�������һ��
        echo '5';
      }
      else{
        //echo '2';
        echo '6';
      }
    }

/**
 * ---------------------------------------------
 *  refresh employees life
 * ---------------------------------------------
 *
**/

    function refreshEmployee(&$user,&$employee,&$returnvar){
       $foodtype = array(
                  '1' =>  array('cost' => 100, 'time' => 3600),
                  '2' =>  array('cost' => 270, 'time' => 10800),
                  '3' =>  array('cost' => 525, 'time' => 21600)
      );
      $result = 1;
      foreach($employee as $e){
      if($user->userInfo->cash > $foodtype[$e['type']]['cost']){
        if($user->shop->refreshEmployee($e['id'],$foodtype[$e['type']]['time'])){
          $user->userInfo->reduceCash($foodtype[$e['type']]['cost']);//edited by ryan
        }
        else{
          $result = 0; 
        }
      }
      else{
        $result = 0;
      }
      }
	echo $result;
    }


/**
 * ---------------------------------------------
 *  shop expension
 * ---------------------------------------------
 *
**/

    function expendShop(&$user,&$direction,&$returnvar){
      if($user->userInfo->expt > 0){
        switch(intval($direction[0])){
          case 1: // width
            $user->shop->incWidth(1);
            $user->userInfo->decExpt();
          break;
          case 2: // length
            $user->shop->incLength(1);
            $user->userInfo->decExpt();
          break;
        }
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  pickup coin from tree
 * ---------------------------------------------
 *
**/

    function pickCoin(&$user,&$shopUid,&$returnvar){
      if(!is_array($shopUid)){
        echo '0';
      }
      foreach($shopUid as $s){
        if(knightlover::systemInfo()->getCoin($user->userId,$s)){
          $coin = 3; // each coin worth $3?
          $user->userInfo->increaseCash($coin);
          echo '1';
        }
        else{
          echo '0';
        }
      }
    }

/**
 * ---------------------------------------------
 *  update tutorial users has done
 * ---------------------------------------------
 *
**/

    function updateTutorial(&$user,&$step,&$returnvar){
      if($user->userInfo->tutorial == -1 && $step == 0){
	$award = 5000;
	$user->userInfo->increaseCash($award);
      }
      $user->userInfo->tutorial = $step;
      echo '1';
    }


/**
 * ---------------------------------------------
 *  setting user shop name
 * ---------------------------------------------
 *
**/

    function setShopName(&$user,&$name,&$returnvar){
      $user->shop->shopName = $name;
      echo '1';
    }

/**
 * ---------------------------------------------
 *  saving shelf info
 * ---------------------------------------------
 *
**/
    function saveData(&$user,&$data,&$returnvar){
      knightlover::systemInfo()->saveOfflineData($user->userId,$data);
      $user->userInfo;
      $user->shop;
      $user->ingameItem;
      $returnvar['1011'] = $user->offlineEarning();
      $returnvar['1012'] = $user->shop->getEmployees();
      echo "1"; 
    }


/**
 * ---------------------------------------------
 *  fix cash counter 
 * ---------------------------------------------
 *
**/
    function repairCounter(&$user,&$counter,&$returnvar){
      $id = $counter->id;
      $fixlevel = $counter->fixlevel;
    }

/**
 * ---------------------------------------------
 *  datasync alpha
 * ---------------------------------------------
 *
**/
    function datasync(&$user,&$userInfo,&$returnvar){
      //
      $uid = $user->userId;
      $counter = knightlover::cache()->get('tran_'.$uid);
      if(!$counter){
        $counter = 0;
        knightlover::cache()->set('tran_'.$uid,$counter,60);
      }
      $incash = 0;
      $maxincome = knightlover::systemInfo()->lvToMaxIncome($user->userInfo->level);
	if(is_array($userInfo)){
          foreach($userInfo as $u){
            $itemId = $u['globalItemId'];
            $like = min(2,$u['like']);
            if($itemId > 0){
              // transaction done
              $item = knightlover::globalitem()->getItem($itemId);
	      $incash += intval($item['price']);
	      if($incash + $counter < $maxincome){
                $user->userInfo->increaseCash($item['price']);
                $user->userInfo->increaseExp($item['exp']);
              }
	      else{
	        echo 'error12';
	        exit;
	      }
            }
            else{
              // like event occured
              $maxlike = 50;
              $minlike = 5;
              $user->userInfo->like = min($maxlike,max($minlike,$user->userInfo->like+$like));
	      $ulevel = knightlover::systemInfo()->ulevel(intval($user->userInfo->level));
	      $user->userInfo->like = min($ulevel['top_like'],$user->userInfo->like);
            }
          }
          echo '1';
        }
        else{
          echo '0';
        }
      knightlover::cache()->increment('tran_'.$uid,$incash);
    }

/**
 * ---------------------------------------------
 *  move an ingame item into inventory
 * ---------------------------------------------
 *
**/
    function toInventory(&$user,&$item,&$returnvar){
      if(is_array($item)){
        knightlover::load_model('ownedItem');
        $item = knightlover::array_to_object($item);
      }
      $tmp = $user->ingameItem->removeItem($item);
      if($tmp){
        knightlover::load_model('inventoryItem');
        $tmp = new InventoryItem($tmp->globalItemId);
        $user->inventory->addItem($tmp);
        echo '1';
      }
      // ingame item not exist, check floors.
      else if($tmp = $user->shop->hasFloor($item->globalItemId)){
        knightlover::load_model('inventoryItem');
        $tmp =  new InventoryItem($item->globalItemId);
        $user->inventory->addItem($tmp);
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  move an inventory item to ingame
 * ---------------------------------------------
 *
**/
    function toIngame(&$user,&$item,&$returnvar){
      if(is_array($item)){
        $item = knightlover::array_to_object($item);
      }
      if(!isset($item->number))
        $item->number = 1;
      $tmp = $user->inventory->removeItem($item);
      if($tmp){
        $user->ingameItem->addItem($tmp);
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  buy ingame item
 * ---------------------------------------------
 *
**/
    function buyIngame(&$user,&$item,&$returnvar){
      // manually type cast item to object incase it got transformed into array
     if(is_array($item)){
        $item = knightlover::array_to_object($item);
      }
      $globalitem = knightlover::globalitem()->getItem($item->globalItemId);
      if($user->userInfo->cash >= $globalitem['cost'] && $user->userInfo->token >= $globalitem['token']){
        //  add time to item type which has life time
        if($globalitem['total_life_time'] > 0){
          $item->timer = $globalitem['total_life_time'];
          $item->timestamp = time();
        }
        //
        if($user->ingameItem->addItem($item)){
		          	
          	if($globalitem['token'] > 0){
          		// logging 0--for token items
		    	knightlover::systemInfo()->logTokenItem($item->globalItemId,0);  
            	// token item
            	$user->userInfo->reduceToken($globalitem['token']);
          	}
	  		if($globalitem['cost'] > 0){
          		// logging 1--for coin items
		    	knightlover::systemInfo()->logTokenItem($item->globalItemId,1);  
	    		$user->userInfo->reduceCash($globalitem['cost']);
          	}
	  		echo '1';
        }
        else
          echo '0';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  buy inventory item
 * ---------------------------------------------
 *
**/
     function buyInventory(&$user,&$item,&$returnvar){
      if(is_array($item)){
        $item = knightlover::array_to_object($item);
      }
      $globalitem = knightlover::globalitem()->getItem($item->globalItemId);
      if($user->userInfo->cash >= $globalitem['cost'] && $user->userInfo->token >= $globalitem['token']){
        if($user->inventory->addItem($item)){
	  if($globalitem['token'] > 0){
	    $user->userInfo->reduceToken($globalitem['token']);
	    // logging
	    knightlover::systemInfo()->logTokenItem($item->globalItemId);
	  }
	  if($globalitem['cost'] > 0){
            $user->userInfo->reduceCash($globalitem['cost']);
          }
	  echo '1';
        }
        else
          echo '0';
      }
      else{
        echo '0';
      }
    }
 
/**
 * ---------------------------------------------
 *  save ingame item
 * ---------------------------------------------
 *
**/
    function saveItem(&$user,&$item,&$returnvar){
      if($user->ingameItem->replaceItem($item)){
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  save floor item
 * ---------------------------------------------
 *
**/
    function saveFloor(&$user,&$floors,&$returnvar){
      $user->shop->updateFloor($floors);
      echo '1';
    }

/**
 * ---------------------------------------------
 *  sell ingame item
 * ---------------------------------------------
 *
**/
    function sellIngame(&$user,&$item,&$returnvar){
      // manually type cast item to object incase it got transformed into array
      if(is_array($item)){
        knightlover::load_model('ownedItem');
        $item = new OwnedItem($item);
      }
      $globalitem = knightlover::globalitem()->getItem($item->globalItemId);
      if($user->ingameItem->removeItem($item)){
        $user->userInfo->increaseCash(ceil($globalitem['cost']/3));
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------
 *  sell inventory item
 * ---------------------------------------------
 *
**/
    function sellInventory(&$user,&$item,&$returnvar){
      if(is_array($item)){
        $tmp = new stdClass();
        $tmp->globalItemId = $item['globalItemId'];
        $tmp->number = $item['number'];
        $item = &$tmp;
      }
      $inumber = $item->number;
      $globalitem = knightlover::globalitem()->getItem($item->globalItemId);
      if($user->inventory->removeItem($item)){
        $user->userInfo->increaseCash(ceil($globalitem['cost']*$inumber/3));
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------------------
 *  hire employee
 * ---------------------------------------------------------
 *
**/
    function hireEmployee(&$user,&$player,&$returnvar){
      $ulevel = knightlover::systemInfo()->ulevel(intval($user->userInfo->level));
      if($user->shop->employeeNum() < $ulevel['top_employee']){
        if($user->shop->addEmployee($player)){
          echo '1';
        }
        else{
          echo '0';
        }
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------------------
 *  fire employee
 * ---------------------------------------------------------
 *
**/
    function fireEmployee(&$user,&$player,&$returnvar){
      if($user->shop->removeEmployee($player)){
        $user->userInfo->reduceCash(400); //TODO get this value from data backend
        echo '1';
      }
      else{
        echo '0';
      }
    }

/**
 * ---------------------------------------------------------
 *  update employee
 * ---------------------------------------------------------
 *
**/
    function updateEmployee(&$user,&$player,&$returnvar){
      if($user->shop->updateEmployee($player)){
        echo '1';
      }
      else{
        echo '0';
      }  
    }

/**
 * ---------------------------------------------------------
 *  dummy function used when failed to map a valid function 
 * ---------------------------------------------------------
 *
**/
    function dummy(){
      echo '0';
    }

}
?>
