<?php
/*
 *
 *  class to handel system mails to user
 *
 */
class sysMail{

	function checkMail($param){
		$uid = $param;
		$time = time();
		$col = knightlover::db()->getCollection('sys_mail');
		$cursor = $col->find(
			array(
				'start' => array('$lt' => $time),
				'stop' => array('$gt' => $time),
				'status' => 1
			)
		);
		$cursor->snapshot();
		// getting current valid system mails
		$sysmails = array();
		foreach($cursor as $c){
			$sysmails[] = $c;
		}
		// getting user received mails
		$col2 = knightlover::db()->getCollection('sys_mail_log');	
		$obj = $col2->findOne(
                        array(
                                'userId' => $uid,
                        )
                );
		$rs['mails'] = $obj['mails'];
		//
		if(!is_array($rs['mails'])){
			$rs['mails'] = array();
		}
		knightlover::load_model('inboxFactory');
		knightlover::load_model('inboxmailFactory');
                $inbox = inboxFactory::getInstance($uid);
		/*
		$mail = inboxmailFactory::getInstance();
		$mail->fromUid = 0;
		$mail->fromUsername = 'system';
		$mail->read = 0;
		*/
		foreach($sysmails as $s){
			if(!in_array($s['id'],$rs['mails'])){
				// send mail to inbox
				$mail = inboxmailFactory::getInstance();
				$mail->fromUid = 0;
				$mail->fromUsername = 'system';
				$mail->read = 0;			
				$mail->time = time();
				$mail->message = $s['content'];
				$mail->coin = $s['cash']?intval($s['cash']):0;
				$mail->token = $s['token']?intval($s['token']):0;
				$mail->attachment = $s['items']?$s['items']:null;
				$inbox->receiveMail($mail);
				// update sent mail count
				$filter = array('id' => $s['id']);
				$update = array(
					'$inc' => array('num' => 1)
				);
				$options = array(
					'multiple' => false
				);
				$col->update($filter,$update,$option);
				$rs['mails'][] = $s['id'];
			}
		}
		// update mail log
		$filter = array('userId' => $uid);
    		$update = array(
      			'$set'  =>  $rs
    		);
    		$options= array(
      			'mltiple' => false,
      			'upsert'  => true,
      			'fsync'    => true
    		);
    		$col2->update($filter,$update,$options);
	}

}

?>
