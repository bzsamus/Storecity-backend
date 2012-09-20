<?php
include_once('model.php');

class shop extends model{


  public function __get($var){
    if(!isset($this->$var)){
      $this->$var = knightlover::objhandler()->getObject($var);
    }
    return $this->$var;
  }

  public function __set($var,$val){
    $this->$var = $val;
  }

  public function incWidth($amount){
    $this->w += $amount;
  }

  public function incLength($amount){
    $this->l += $amount;
  }

  public function getLayout(){
    return $this->w.','.$this->l;
  }

  public function addEmployee($user){
    if(!is_array($user)){
      return false;
    }
    $success = true;
    foreach($user as &$u){
      if(!is_string($u['id'])){
        $u['id'] = sprintf("%.0f", $u['id']); // handle bigint
      }
      $u['lifetime'] = 60*60*4; // employee can work for 4hr?
      $u['timeflag'] = 0;
      unset($u['_explicitType']);
      if(isset($this->employees)){
        if(!isset($this->employees[strval($u['id'])])){
          $this->employees[strval($u['id'])] = $u;
        }
        else{
          $success = false;
        }
      }
      else{
        $this->employees[strval($u['id'])] = $u;
      }
    }
    $this->checkEmployee(); //check for currupt data
    return $success;
  }
 
  public function employeeNum(){
    return sizeof($this->employees);
  }

  public function refreshEmployee($employee,$refreshtime){
    $employee = sprintf("%.0f", $employee); // handle bigint
      if(isset($this->employees)){
	$this->checkEmployee(); // check for currupt data
        if(isset($this->employees[$employee])){
          // calculate time since last calculation
          $dt = 0;
          if($this->employees[$employee]['task'] > 0){
            $currtime = time();
            $dt = $currtime - $this->employees[$employee]['timeflag'];
            $this->employees[$employee]['lifetime'] = max(0,$this->employees[$employee]['lifetime'] - $dt);
            $this->employees[$employee]['timeflag'] = $currtime;
          }
          $this->employees[$employee]['lifetime'] = min(21600,$this->employees[$employee]['lifetime'] + $refreshtime); //MARK 21600
          return true;
        }
        else{
          return false;
        }
      }
      else{
        return false;
      } 
  }
  
  public function removeEmployee($user){
    $this->checkEmployee(); // check for currupt data
    if(!is_array($user)){
      return false;
    }
    $success = true;
    foreach($user as $u){
      if(!is_string($u['id'])){
        $u['id'] = sprintf("%.0f", $u['id']); // handle bigint
      }
      if(isset($this->employees)){
        if(isset($this->employees[strval($u['id'])])){
          unset($this->employees[strval($u['id'])]);
        }
        else{
          $success = false;
        }
      }
      else{
        $success = false;
      }
    }
    return $success;
  }

  public function checkEmployee(){
    foreach($this->employees as $i=>$e){
      if($i != $e['id']){
        $this->employees[$e['id']] = $e;
        unset($this->employees[$i]);
      }
    }
  }

  public function updateEmployee($user){
    if(!is_array($user)){
      return false;
    }
    $this->checkEmployee(); //check for currupt data
    $success = true;
    foreach($user as $u){
      if(!is_string($u['id'])){
        $u['id'] = sprintf("%.0f", $u['id']); // handle bigint
      }
      unset($u['_explicitType']);
      /*
      if($u['task'] > 0){
        $u['timeflag'] = time();
      }
      else{
        $u['timeflag'] = 0;
      }*/
      $u['timeflag'] = time();
      if(isset($this->employees)){
        if(isset($this->employees[$u['id']])){
          
          $this->employees[$u['id']]['task'] = $u['task'];
          $this->employees[$u['id']]['clothes'] = $u['clothes'];
          $this->employees[$u['id']]['timeflag'] = $u['timeflag'];
        
        }
        else{
          $success = false;
        }
      }
      else{
        $success = false;
      }
    }
    return $success;
  }

  public function employeeTick(){
    knightlover::load_model('employee');
    if(is_array($this->employees)){
      $currtime = time();
      foreach($this->employees as &$e){
        $dt = $currtime - $e['timeflag'];
	if($e['task'] > 0){
	  // employee is working, decrease lifetime
          $e['lifetime'] = max(0,$e['lifetime']-$dt);
          $e['timeflag'] = $currtime;
        }
	else{
	  // employee is resting increase lifetime
          $e['lifetime'] = min(21600,$e['lifetime']+($dt*2));
          $e['timeflag'] = $currtime;
	}
      }
    }
  }

  public function getEmployees(){
    knightlover::load_model('employee');
    $employees = array();
    if(is_array($this->employees)){
      foreach($this->employees as $e){
        $e['lifetime'] = $e['lifetime']*1000; //flash time unit is in ms
        $tmp = new employee($e);
        $tmp->_explicitType = 'var.www.saunabeta.model.Employee';
        $employees[] = $tmp;
      }
    }
    return $employees;
  }

  public function hasFloor(&$floorId){
    if(in_array($floorId,$this->floors)){
      return true;
    }
    else{
      return false;
    }
  }

  public function updateFloor(&$floors){
    $this->floors = $floors;
  }
}

?>
