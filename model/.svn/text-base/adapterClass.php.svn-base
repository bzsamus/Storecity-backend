<?php
class userBasic{
  var $id;
  var $fullName;
  var $imageUrl;
  var $ownedItem;
  var $userLevel;
  var $exp;
  var $playCount;
  var $gender;
  var $supermarketName;
  var $musicPlay;
  var $offlineShard;
  var $floors;
  var $employees;
  var $like;
  var $graffiti;

  function __construct(&$u,$data=1){
    $this->id = strval($u->userId);
    $this->fullName = $u->username;
    $this->imageUrl = $u->profilePic;
    $this->gender = $u->userInfo->gender;
    $this->userLevel = $u->userInfo->level;
    $this->exp = $u->userInfo->exp;
    $this->playCount = $u->userInfo->playcount?$u->userInfo->playcount:0;
    $this->layout = $u->shop->getLayout();
    $this->supermarketName = $u->shop->shopName;
    $this->floors = $u->shop->floors;
    $this->employees = $u->shop->getEmployees();
    $tmp = $u->getGraffiti();
    $this->graffiti = $tmp['data'];
    $this->graffitiCount = (int)$tmp['count'];
    //$this->graffiti = $u->getGraffiti();
    $this->like = $u->userInfo->like;
    switch($data){
      case 1:
        $this->ownedItem = $u->ingameItem->streetToArray();
        break;
      case 2:
        $this->ownedItem = $u->ingameItem->ingameToArray();
        break;
    }
  }
}

class ownedItem{
  var $employeeId;
  var $data;
  var $positionX;
  var $positionY;
  var $globalItemId;
  var $roomIndex;
  var $id;
  var $timer;
var $_explicitType = 'var.www.saunabeta.model.OwnedItem';

  function __construct($i){
    $this->employeeId = $i['employeeId'];
    $this->data = $i['data'];
    $this->positionX = $i['positionX'];
    $this->positionY = $i['positionY'];
    $this->globalItemId = $i['globalItemId'];
    $this->roomIndex = $i['roomIndex'];
    $this->id = $i['id'];
    $this->timer = $i['timer'];
  }
}

class inventoryItems{
  var $globalItemId;
  var $number;
  var $_explicitType = 'var.www.saunabeta.model.InventoryItem';

  function __construct($i){
    $this->globalItemId = $i['globalItemId'];
    $this->number = $i['number'];
  }
}

class initInfo{
  var $networkUid;
  var $userLevel;
  var $employees;
  var $floor;
  var $imageUrl;
  var $lastSave;
  var $fullName;
  var $playCount;
  var $gender;
  var $consecutionCount;
  var $ownedItem;
  var $inventoryItem;
  var $supermarketName;
  var $musicPlay;
  var $cash;
  var $token;
  var $like;
  var $exp;
  var $expt;
  var $tutorial;
  var $graffiti;
  var $_explicitType = 'sauna.Model.userInfo';

  function __construct($u){
    $this->networkUid = strval($u->userId);
    $this->userLevel = $u->userInfo->level;
    $this->employees = $u->shop->getEmployees();
    $this->layout = $u->shop->getLayout();
    $this->floor = $u->shop->floors;
    $tmp = $u->getGraffiti();
    $this->graffiti = $tmp['data'];
    $this->graffitiCount = (int)$tmp['count'];    
    //$this->graffiti = $u->getGraffiti();
    $this->imageUrl = $u->facebookInfo->profilePic;
    $this->lastSave = $u->userInfo->lastaccess;
    $this->fullName = $u->facebookInfo->username;
    $this->playCount = $u->userInfo->playcount;
    $this->gender = $u->facebookInfo->gender;
    //$this->consecutionCount = 
    $this->ownedItem = $u->ingameItem->toArray();
    $this->inventoryItem = $u->inventory->toArray();
    $this->supermarketName = $u->shop->shopName; 
    //$this->musicPlay = 
    $this->tutorial = $u->userInfo->tutorial;
    $this->exp = $u->userInfo->exp;
    $this->expt = $u->userInfo->expt;
    $this->cash = $u->userInfo->cash;
    $this->token = $u->userInfo->token;
    $this->like = $u->userInfo->like;
  }
}

class StreetUser{
  var $usrID;
  var $UserName;
  var $UserFace;
  var $Exp;
  var $bannerText;
  var $shopExt;

  var $_explicitType = 'sauna.Model.StreetUser';

  function __construct($u){
    $this->usrID = $u->userId;
    $this->UserName = $u->facebookInfo->username;
    $this->UserFace = $u->facebookInfo->profilePic;
    $this->Exp = $u->userInfo->expToLevel;
    $this->bannerText = $u->shop->shopName;
    $this->shopExt = $u->shop->shopExterior;
  }
}


?>
