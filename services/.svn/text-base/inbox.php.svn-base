<?php
  include_once('../config.php');
  include_once('../knightlover.php');

  class inbox{

    private function tokenToUid($token){
      $uid = knightlover::platform()->tokenToUid($token);
      return $uid;
    }

    private function validateUser($uid,$fsig,$fseq){
      $seq = knightlover::systemInfo()->getSeq($uid);
      $sig = knightlover::systemInfo()->getSig($uid);
      // signature check for multiple login
      if(!md5($sig.strval($seq)) == $fsig){
        throw new Exception('invalid signature');
      }
      // sequence check for multiple communication
      if(!$newseq = knightlover::systemInfo()->incSeq($uid,$fseq)){
        throw new Exception('invalid sequence');
      }
      else{
        $rs[0]['seq'] = $newseq[0];
        $rs[0]['seqHash'] = $newseq[1];
      }
      return $rs;
    }

    function getMails($token,$fsig="",$fseq=""){
      try{
        $uid = $this->tokenToUid($token);
        $rs = $this->validateUser($uid,$fsig,$fseq);
        knightlover::load_model('inboxFactory');
        $inbox = inboxFactory::getInstance($uid);
        $rs[] = $inbox->getMails();
        return $rs;
      }
     catch(Exception $e){
       if(__SITE__ENV == 'dev'){
         $rs = $e->getMessage();
       }
       else{
         $rs = false;
       }
      return $rs;
      }
    }

    function sendMail($token,$touid,$title,$message,$fsig="",$fseq=""){
      try{
        $uid = $this->tokenToUid($token);
        $rs = $this->validateUser($uid,$fsig,$fseq);
        knightlover::load_model('userFactory');
        $s = userFactory::getInstance($uid);
        knightlover::load_model('inboxFactory');
        knightlover::load_model('inboxmailFactory');
        $mail = inboxmailFactory::getInstance();
        $mail->fromUid = $uid;
        $mail->fromUsername = $s->facebookInfo->username;
        $mail->title = $title;
        $mail->message = $message;
        $inbox = inboxFactory::getInstance($touid);
        $rs[] = $inbox->receiveMail($mail);
        return $rs;
      }
      catch(Exception $e){
        if(__SITE__ENV == 'dev'){
          $rs = $e->getMessage();
        }
        else{
          $rs = false;
        }
        return $rs;
      }
    }

    function markMail($token,$mailId,$fsig="",$fseq=""){
      try{
        $uid = $this->tokenToUid($token); 
        $rs = $this->validateUser($uid,$fsig,$fseq);
        knightlover::load_model('inboxFactory');
        $inbox = inboxFactory::getInstance($uid);
        $rs[] = $inbox->markMail($mailId);
        return $rs;
      }
      catch(Exception $e){
        if(__SITE__ENV == 'dev'){
          $rs = $e->getMessage();
        }
        else{
          $rs = false;
        }
        return $rs;
      }
    }

    function purgeMail($token,$mailId,$fsig="",$fseq=""){
      try{
        $uid = $this->tokenToUid($token);
        $rs = $this->validateUser($uid,$fsig,$fseq);
        knightlover::load_model('inboxFactory');
        $inbox = inboxFactory::getInstance($uid);
        $rs[] = $inbox->purgeMail($mailId);
        return $rs;
      }
      catch(Exception $e){
        if(__SITE__ENV == 'dev'){
          $rs = $e->getMessage();
        }
        else{
          $rs = false;
        }
        return $rs;
      }
    }
  } 

?>
