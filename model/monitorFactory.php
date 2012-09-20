<?php
class monitorFactory{
	public static function getInstance($id){
		$obj = knightlover::objhandler()->getObject('monitorOBJ');
		$obj->__construct($id);
		return $obj;
	}
}
?>
