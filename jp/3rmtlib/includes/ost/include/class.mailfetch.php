<?php
/*********************************************************************
  class.mailfetch.php

  mail fetcher class. Uses IMAP ext for now.

  Peter Rotich <peter@osticket.com>
  Copyright (c)  2006-2010 osTicket
  http://www.osticket.com

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
  $Id$
 **********************************************************************/
require_once(INCLUDE_DIR.'class.mailparse.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.dept.php');
class MailFetcher {
  var $hostname;
  var $username;
  var $password;

  var $port;
  var $protocol;
  var $encryption;

  var $mbox;

  var $charset= 'UTF-8';


  function MailFetcher($username,$password,$hostname,$port,$protocol,$encryption='') {
    if(!strcasecmp($protocol,'pop')) //force pop3
      $protocol='pop3';
    $this->hostname=$hostname;
    $this->username=$username;
    $this->password=$password;
    $this->protocol=strtolower($protocol);
    $this->port = $port;
    $this->encryption = $encryption;

    $this->serverstr=sprintf('{%s:%d/%s',$this->hostname,$this->port,strtolower($this->protocol));
      if(!strcasecmp($this->encryption,'SSL')){
        $this->serverstr.='/ssl';
      }
      $this->serverstr.='/novalidate-cert}INBOX'; //add other flags here as needed.

      //echo $this->serverstr;
      //Charset to convert the mail to.
      $this->charset='UTF-8';
      //Set timeouts 
      //       if(function_exists('imap_timeout'))
      //            imap_timeout(1,20); //Open timeout.
  }

  function connect() {
    return $this->open()?true:false;
  }

  function open() {

    //echo $this->serverstr;
    if($this->mbox && imap_ping($this->mbox))
      return $this->mbox;

    $this->mbox =@imap_open($this->serverstr,$this->username,$this->password);

    return $this->mbox;
  }

  function close() {
    imap_close($this->mbox,CL_EXPUNGE);
  }

  function mailcount(){
    return count(imap_headers($this->mbox));
  }


  function decode($encoding,$text) {

    switch($encoding) {
      case 0:
        $text=imap_8bit($text);
        break;
      case 1:
        $text=imap_8bit($text);
        break;
      case 2:
        $text=imap_binary($text);
        break;
      case 3:
        $text=imap_base64($text);
        break;
      case 4:
        $text=imap_qprint($text);
        break;
      case 5:
      default:
        $text=$text;
    } 
    return $text;
  }

  //Convert text to desired encoding..defaults to utf8
  function mime_encode($text,$charset=null,$enc='utf-8') { //Thank in part to afterburner  
    $charset = strtoupper($charset);
    if ($charset=='' || $charset=='ISO-2022-JP'||$charset=='SHIFT-JIS'||$charset=='EUC-JP'){
       $result = noLCode($text);
       return $result;
    }
    $encodings=array('SHIFT-JIS','ISO-2022-JP','GB2312','GBK','WINDOWS-1251','ISO-8859-5',
                     'ISO-8859-1','KOI8-R','GB2312');
    if(function_exists("iconv") and $text) {
      if($charset)
      {
	$result = my_iconv($charset,$enc,$text);
	if($result!=false){
	  return $result;
	}
        foreach($encodings as  $key=>$value){

          $result = my_iconv($value,$enc,$text);
          if($result!=false){
            break;
          }
          if($key == count($encodings)-1){
             $result = iconv($charset,$enc.'//IGNORE',$text);
             break;
          }
        }
	return $result;
      }elseif(function_exists("mb_detect_encoding")){
        $result =iconv(mb_detect_encoding($text,$encodings),$enc,$text);
      }
      return utf8_encode($result);
    }
    return utf8_encode($text);
  }

  //Generic decoder - mirrors imap_utf8
  function mime_decode($text,$is_subject=false) {

    $a = imap_mime_header_decode($text);
    $str = '';
    /*
       foreach ($a as $k => $part)
       $str.= $part->text;
    //add by bobhero {{
    foreach ($a as $k => $part){
    if(!$part->charset){
    $str.= $part->text;
    }else{
    $str.= mb_convert_encoding($part->text,'UTF-8', $part->charset);
    }
    }

     */

    foreach ($a as $k => $part)
      $str.= $part->text;

    $explodeStr = explode("?",$text);
    if(strlen($explodeStr[1])){
      if($is_subject){
        if(!preg_match('/('.chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42')).'|'.chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42')).')/',$str)){
       $str = $this->mime_encode($str,$explodeStr[1]);
        }
      }else{
        $str  =  iconv($explodeStr[1],'UTF-8',$str);
      }
    }


    return $str?$str:imap_utf8($text);
  }

  function getLastError(){
    return imap_last_error();
  }

  function getMimeType($struct) {
    $mimeType = array('TEXT', 'MULTIPART', 'MESSAGE', 'APPLICATION', 'AUDIO', 'IMAGE', 'VIDEO', 'OTHER');
    if(!$struct || !$struct->subtype)
      return 'TEXT/PLAIN';

    return $mimeType[(int) $struct->type].'/'.$struct->subtype;
  }

  function getHeaderInfo($mid) {

    $headerinfo=imap_headerinfo($this->mbox,$mid);
    $sender=$headerinfo->from[0];

    //Parse what we need...
    $header=array(
        'from'   =>array('name'  =>@$sender->personal,'email' =>strtolower($sender->mailbox).'@'.$sender->host),
        'subject'=>@$headerinfo->subject,
        'mid'    =>$headerinfo->message_id);
    return $header;
  }

  //search for specific mime type parts....encoding is the desired encoding.
  function getPart($mid,$mimeType,$encoding=false,$struct=null,$partNumber=false){
    if(!$struct && $mid)
      $struct=@imap_fetchstructure($this->mbox, $mid);
    //Match the mime type.
    if($struct && !$struct->ifdparameters && strcasecmp($mimeType,$this->getMimeType($struct))==0){
      $partNumber=$partNumber?$partNumber:1;
      if(($text=imap_fetchbody($this->mbox, $mid, $partNumber))){
	        if($struct->encoding==3 or $struct->encoding==4) //base64 and qp decode.
		  {
		    $text=$this->decode($struct->encoding,$text);
		  }
		$charset=null;
	
        if($encoding) { //Convert text to desired mime encoding...
          if($struct->ifparameters){
            $charsetkey=0;
            foreach ($struct->parameters as $key=>$value){
              if(!strcasecmp($value->attribute,'CHARSET')){
                $charsetkey = $key;
                break;
              }
            }
            if(!strcasecmp($struct->parameters[$charsetkey]->attribute,'CHARSET') && strcasecmp($struct->parameters[$charsetkey]->value,'US-ASCII'))
              $charset=trim($struct->parameters[$charsetkey]->value);
          }
	  //	  echo $charset.$encoding.$text;

	  if(strtolower($charset)=='utf-8' and strtolower($encoding)=='utf-8'){
	    //
	  }else{

	    $text=$this->mime_encode($text,$charset,$encoding);

	  }

        }

        return $text;
      }
    }
    //Do recursive search
    $text='';
    if($struct && $struct->parts){
      while(list($i, $substruct) = each($struct->parts)) {
        if($partNumber) 
          $prefix = $partNumber . '.';
        if(($result=$this->getPart($mid,$mimeType,$encoding,$substruct,$prefix.($i+1))))
          $text.=$result;
      }
    }
    return $text;
  }

  function getHeader($mid){
    return imap_fetchheader($this->mbox, $mid,FT_PREFETCHTEXT);
  }


  function getPriority($mid){
    return Mail_Parse::parsePriority($this->getHeader($mid));
  }

  function getBody($mid) {
    $body ='';

    if(!($body = $this->getpart($mid,'TEXT/PLAIN',$this->charset))) {
      if(($body = $this->getPart($mid,'TEXT/HTML',$this->charset))) {
        //Convert tags of interest before we striptags
        $body=str_replace("</DIV><DIV>", "\n", $body);
        $body=str_replace(array("<br>", "<br />", "<BR>", "<BR />"), "\n", $body);
        $body=Format::striptags($body); //Strip tags??
      }
    }
    return $body;
  }

  function createTicket($mid,$emailid=0){
    global $cfg;

    $mailinfo=$this->getHeaderInfo($mid);

    //Make sure the email is NOT one of the undeleted emails.
    if($mailinfo['mid'] && ($id=Ticket::getIdByMessageId(trim($mailinfo['mid']),$mailinfo['from']['email']))){
      //TODO: Move emails to a fetched folder when delete is false?? 
            return false;
    }
    $var['name']=$this->mime_decode($mailinfo['from']['name']);
    $var['email']=$mailinfo['from']['email'];
//    var_dump("<<".$mailinfo['subject'].">>");
    $var['subject']=$mailinfo['subject']?$this->mime_decode($mailinfo['subject'],true):'[No Subject]';
    $var['message']=Format::stripEmptyLines($this->getBody($mid))?Format::stripEmptyLines($this->getBody($mid)):" ";
    $var['header']=$this->getHeader($mid);
    $var['emailId']=$emailid?$emailid:$cfg->getDefaultEmailId(); //ok to default?
    $var['name']=$var['name']?$var['name']:$var['email']; //No name? use email
    $var['mid']=$mailinfo['mid'];
    if($cfg->useEmailPriority())
      $var['pri']=$this->getPriority($mid);

    $ticket=null;
    $newticket=true;
    //Check the subject line for possible ID.
    if(preg_match ("[[#][0-9]{1,10}]",$var['subject'],$regs)) {
      $extid=trim(preg_replace("/[^0-9]/", "", $regs[0]));
      $ticket= new Ticket(Ticket::getIdByExtId($extid));
      //Allow mismatched emails?? For now NO.
      if(!$ticket || strcasecmp($ticket->getEmail(),$var['email']))
        $ticket=null;
    }

    $errors=array();
    if(!$ticket) {
      if(!($ticket=Ticket::create($var,$errors,'Email')) || $errors)
        return null;
      $msgid=$ticket->getLastMsgId();
    }else{
      $message=$var['message'];
      //Strip quoted reply...TODO: figure out how mail clients do it without special tag..
      if($cfg->stripQuotedReply() && ($tag=$cfg->getReplySeparator()) && strpos($var['message'],$tag))
        list($message)=split($tag,$var['message']);
      $msgid=$ticket->postMessage($message,'Email',$var['mid'],$var['header']);
    }
    //Save attachments if any.
    if($msgid && $cfg->allowEmailAttachments()){
      if(($struct = imap_fetchstructure($this->mbox,$mid)) && $struct->parts) {
        if($ticket->getLastMsgId()!=$msgid)
          $ticket->setLastMsgId($msgid);
        $this->saveAttachments($ticket,$mid,$struct);

      }
    } 
    return $ticket;
  }

  function saveAttachments($ticket,$mid,$part,$index=0) {
    global $cfg;

    if($part && $part->ifdparameters && ($filename=$part->dparameters[0]->value)){ //attachment
      $index=$index?$index:1;
      if($ticket && $cfg->canUploadFileType($filename) && $cfg->getMaxFileSize()>=$part->bytes) {
        //extract the attachments...and do the magic.
        $data=$this->decode($part->encoding, imap_fetchbody($this->mbox,$mid,$index));
        $ticket->saveAttachment($filename,$data,$ticket->getLastMsgId(),'M');
        return;
      }
      //TODO: Log failure??
    }

    //Recursive attachment search!
    if($part && $part->parts) {
      foreach($part->parts as $k=>$struct) {
        if($index) $prefix = $index.'.';
        $this->saveAttachments($ticket,$mid,$struct,$prefix.($k+1));
      }
    }

  }

  function fetchTickets($emailid,$max=20,$deletemsgs=false){
    $nummsgs=imap_num_msg($this->mbox);
    //echo "New Emails:  $nummsgs\n";
    $msgs=$errors=0;
    for($i=$nummsgs; $i>0; $i--){ //process messages in reverse. Latest first. FILO.
      if($this->createTicket($i,$emailid)){
        imap_setflag_full($this->mbox, imap_uid($this->mbox,$i), "\\Seen", ST_UID); //IMAP only??
        if($deletemsgs)
          imap_delete($this->mbox,$i);
        $msgs++;
        $errors=0; //We are only interested in consecutive errors.
      }else{
        $errors++;
      }
      if(($max && $msgs>=$max) || $errors>20)
        break;
    }
    @imap_expunge($this->mbox);

    return $msgs;
  }

  function fetchMail(){
    global $cfg;
    //
    //       if(!$cfg->canFetchMail())
    //          return;
    //We require imap ext to fetch emails via IMAP/POP3
    if(!function_exists('imap_open')) {
      $msg='PHP must be compiled with IMAP extension enabled for IMAP/POP3 fetch to work!';
      Sys::log(LOG_WARN,'Mail Fetch Error',$msg);
      return;
    }
    $MAX_ERRORS=800005; //Max errors before we start delayed fetch attempts - hardcoded for now.

    //$sql=' SELECT email_id,mail_host,mail_port,mail_protocol,mail_encryption,mail_delete,mail_errors,userid,userpass FROM '.EMAIL_TABLE.
    //    ' WHERE mail_active=1 AND (mail_errors<='.$MAX_ERRORS.' OR (TIME_TO_SEC(TIMEDIFF(NOW(),mail_lasterror))>5*60) )'.
    //   ' AND (mail_lastfetch IS NULL OR TIME_TO_SEC(TIMEDIFF(NOW(),mail_lastfetch))>mail_fetchfreq*60) ';
    $sql=' SELECT email_id,mail_host,mail_port,mail_protocol,mail_encryption,mail_delete,mail_errors,userid,userpass FROM '.EMAIL_TABLE.
      ' WHERE mail_active=1'; 
    if(!($accounts=db_query($sql)) || !db_num_rows($accounts))
      //j          var_dump(db_num_rows($accounts));
      return;

    //TODO: Lock the table here??
    while($row=db_fetch_array($accounts)) {
      $fetcher = new MailFetcher($row['userid'],Misc::decrypt($row['userpass'],SECRET_SALT),
          $row['mail_host'],$row['mail_port'],$row['mail_protocol'],$row['mail_encryption']);
      if($fetcher->connect()){   
        $fetcher->fetchTickets($row['email_id'],$row['mail_fetchmax'],$row['mail_delete']?true:false);
        $fetcher->close();
        db_query('UPDATE '.EMAIL_TABLE.' SET mail_errors=0, mail_lastfetch=NOW() WHERE email_id='.db_input($row['email_id']));
      }else{
        $errors=$row['mail_errors']+1;
        db_query('UPDATE '.EMAIL_TABLE.' SET mail_errors=mail_errors+1, mail_lasterror=NOW() WHERE email_id='.db_input($row['email_id']));
        if($errors>=$MAX_ERRORS){
          //We've reached the MAX consecutive errors...will attempt logins at delayed intervals
          $msg="\nThe system is having trouble fetching emails from the following mail account: \n".
            "\nUser: ".$row['userid'].
            "\nHost: ".$row['mail_host'].
            "\nError: ".$fetcher->getLastError().
            "\n\n ".$errors.' consecutive errors. Maximum of '.$MAX_ERRORS. ' allowed'.
            "\n\n This could be connection issues related to the host. Next delayed login attempt in aprox. 10 minutes";
          Sys::alertAdmin('Mail Fetch Failure Alert',$msg,true);
        }
      }
    }
  }
  }




  function outString($longString)
  { 
    $len = strlen($longString);
    $j=1;
    for($i=0;$i<3;$i++){
      //echo dechex(ord($longString{$i}));
    }
      for ($i=3;$i<$len-3;$i+=2)
      {
        echo "***".$j."***";
    $j++;
      echo dechex(ord($longString{$i}));
      echo dechex(ord($longString{$i+1}));
      echo  chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42'));
      echo $longString{$i};
      echo $longString{$i+1};
      echo  chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42'));
      echo '</br>';
      }

  }
function noLCode($longString){
  
      $startFlag =   chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42'));
      $endFlag   =   chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42'));

      $splited =  splitToArrayBySE($startFlag,$endFlag,$longString);
      $resultString = '';
      foreach($splited as $key=>$value){
        //outstring($value);
        if (strpos($value,$startFlag)===0){
//        outstring( replaceJpSp($value));
        $resultString .= replaceJpSp($value);
        }else {
        //$resultString .=iconv('ISO-2022-JP','UTF-8',$value);
        $resultString .=$value;
        }
      }
      //echo $resultString;
      return $resultString;

}
function splitToArrayBySE($startFlag,$endFlag,$stcom)
{
  $startArray = getPosInString($startFlag,$stcom,'start');
  $endArray = getPosInString($endFlag,$stcom,'end');
  
  $mixedPos = array_merge($startArray,$endArray);
  foreach($mixedPos as $value){
    $posArray[$value['pos']] = $value['flag'];
  }
  $startpos = 0;
  for ($i=0;$i<strlen($stcom);$i++){
    if (@array_key_exists($i,$posArray)){
      if ($posArray[$i] =='start'){
        $tmpArr[] = substr($stcom,$startpos);
        $startpos = $i;
        continue;
      }
      if ($posArray[$i] =='end'){
        $tmpArr[] = substr($stcom,$startpos);
        $startpos = $i+3;
        continue;
      }
    }
    if ($i==strlen($stcom)-1){
      $s = substr($stcom,$startpos); 
      $tmpArr[] = substr($stcom,$startpos);
    }

//     $tmpstr .= $stcom[$i];
  }
  //$tmpArr = array_reverse($tmpArr);

  foreach ($tmpArr as $key=>$value){

    $x =  tep_strstr($value,$tmpArr[$key+1],true);
    if($x === false||!isset($x)){
      $x = $value;
    }
    $tmpArr2[] =$x;
  }
  return $tmpArr2;
}
function getPosInString($search,$longString,$flag=''){
  $posArray = array();
  $pos = 0;
  while($pos !== false){
    @$pos = strpos($longString,$search,$pos);
    if($pos === false){
      return $posArray;
    }else{
      $posArray[]=array('pos'=>$pos,'flag'=>$flag);
      $pos += strlen($search);
      //$pos++;
    }
  }
  return $posArray;
}

  function replaceJpSp($longString){
    $len = strlen($longString);
    //$longString = str_replace(chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42')),'',$longString);
    //$longString = str_replace("\r\n",'',$longString);
    //$longString = str_replace(chr(hexdec('21')).chr(hexdec('21')),'',$longString);
    //$longString = str_replace(" ",'',$longString);
    //$longString = chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42')).$longString;
    //$longString .= chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42'));

    $replaceArray=array(
        "2d6a"=>"㈱",  // 3231
        "2d6b"=>"㈲",  // 3232
        "2d40"=>"㍉",  //  3249
        "213b"=>"〇",  //〇
        //"217b"=>"&#9675;",  //○
        //"217e"=>"&#9671;",  //◇
        //"2222"=>"&#9633;",  //□
        //"2224"=>"&#9651;",  //△
        //"2226"=>"&#9661;",  //▽
        //"2179"=>"&#9734;",  //☆
        //"217c"=>"&#9679;",  //●
        //"2221"=>"&#9670;",  //◆
        //"2223"=>"&#9632;",  //■
        //"2225"=>"&#9650;",  //▲
        //"2227"=>"&#9660;",  //▼
        //"217a"=>"★",  //★
        //"217d"=>"◎",  //◎
        //"227e"=>"◇",  //◯
        "2169"=>"♂",  //♂
        "216a"=>"♀",   //♀
        "2229"=>"〒",  //  〒
        "2261"=>"≡",  // ≡
        "2d74"=>"∑",  // ∑
        "2269"=>"∫",  //  ∫
        "2d73"=>"∮",  // ∮
        "2265"=>"√",  //  √
        "225d"=>"⊥",  // ⊥
        "225c"=>"∠",  //∠
        "2d78"=>"∟",  //   ∟
        "2d79"=>"⊿",  //   ⊿
        "2268"=>"∵",  // ∵
        "2241"=>"∩",  //  ∩    
        "2240"=>"∪",  // ∪ 
        "2d62"=>"№",     //   №
        "2d64"=>"℡",     //   ℡
        "2d63"=>"㏍",   //   33cd    
        "2d65"=>"㊤",   //   32a4
        "2d66"=>"㊥",   //   32a5   
        "2d67"=>"㊦",   //   32a6
        "2d68"=>"㊧",   //   32a7
        "2d69"=>"㊨",   //   32a8
        "2d6b"=>"㈲",   //   3232
        "2d6c"=>"㈹",   //    3239
        "2d6d"=>"㍾",   //   337e
        "2d6e"=>"㍽",   //   337d
        "2d6f"=>"㍼",   //    337c
        "2d5f"=>"㍻",   //    337b
        "2d40"=>"㍉",  //    3349
        "2d50"=>"㎜",  //    ㎜
        "2d51"=>"㎝",  //   ㎝
        "2d52"=>"㎞",  //   ㎞
        "2d53"=>"㎎",  //   ㎎
        "2d54"=>"㎏",  //   ㎏
        "2d55"=>"㏄",  // ㏄
        "2d40"=>"㍉",   //  3349
        "2d41"=>"㌔",   //   3314
        "2d42"=>"㌢",   // 3322
        "2d43"=>"㍍",  //    334d
        "2d44"=>"㌘",   //   3318
        "2d45"=>"㌧",   //   3327
        "2d46"=>"㌃",   //  3303
        "2d47"=>"㌶",     //  3336
        "2d48"=>"㍑",    //  3351
        "2d49"=>"㍗",   //  3357
        "2d4a"=>"㌍",    //  330d
        "2d4b"=>"㌦",  //  3326
        "2d4c"=>"㌣",  // 3323
        "2d4d"=>"㌫",       // 332b
        "2d4e"=>"㍊",           // 334a
        "2d4f"=>"㌻",  //  333b
        "2d21"=>"①",     //  ①
        "2d22"=>"②",     //  ②
        "2d23"=>"③",     //  ③
        "2d24"=>"④",     //  ④
        "2d25"=>"⑤",     //  ⑤
        "2d26"=>"⑥",     //  ⑥
        "2d27"=>"⑦",     //  ⑦
        "2d28"=>"⑧",     //  ⑧
        "2d29"=>"⑨",     //  ⑨
        "2d2a"=>"⑩",     //  ⑩
        "2d2b"=>"⑪",     //  ⑪
        "2d2c"=>"⑫",     //   ⑫
        "2d2d"=>"⑬",    //   ⑬
        "2d2e"=>"⑭",    //   ⑭
        "2d2f"=>"⑮",     //   ⑮ 
        "2d30"=>"⑯",    //   ⑯
        "2d31"=>"⑰",    //   ⑰
        "2d32"=>"⑱",    //   ⑱
        "2d33"=>"⑲",    //   ⑲
        "2d34"=>"⑳",    //   ⑳
        "2d35"=>"Ⅰ",    //  Ⅰ
        "2d36"=>"Ⅱ",    //  Ⅱ
        "2d37"=>"Ⅲ",    //  Ⅲ
        "2d38"=>"Ⅳ",    //  Ⅳ  
        "2d39"=>"Ⅴ",    //  Ⅴ
        "2d3a"=>"Ⅵ",    // Ⅵ
        "2d3b"=>"Ⅶ",    // Ⅶ
        "2d3c"=>"Ⅷ",    // Ⅷ
        "2d3d"=>"Ⅸ",    // Ⅸ
        "2d3e"=>"Ⅹ",    // Ⅹ  
        );
    foreach ($replaceArray as $key=>$value){
      $a = substr($key,0,2);
      $b = substr($key,2);
      $hexArray[] = chr(hexdec($a)).chr(hexdec($b));
      $toArray[] = $value;
    }
    $returnString = '';
    for($i=3;$i<$len-3;$i=$i+2){
      if (@array_key_exists(dechex(ord($longString[$i])).dechex(ord($longString[$i+1])),$replaceArray)){
        //$key = dechex(ord($longString[$i])).dechex(ord($longString[$i+1]));

       $returnString.= $replaceArray[dechex(ord($longString[$i])).dechex(ord($longString[$i+1]))];
       //$returnString.= iconv('ISO-2022-JP','UTF-8'.'//IGNORE',chrtojp(chr(hexdec('22')).chr(hexdec('28'))));
     }else {
       //echo dechex(ord($longString[$i]));
       //echo dechex( ord($longString[$i]));
       $returnString.= iconv('ISO-2022-JP','UTF-8',chrtojp($longString[$i].$longString[$i+1]));
     }
    }
    return $returnString;
   // return str_replace($hexArray,chr(hexdec('21')).chr(hexdec('7a')),$longString);
//    return str_replace($hexArray,chr(hexdec('22')).chr(hexdec('28')),$longString);
    //return str_replace($hexArray,chr(hexdec('21')).chr(hexdec('22')),$longString);
//    return str_replace($hexArray,$toArray,$longString);
  }
  function chrtojp($chr){
   //$chr =  str_replace(chr(hexdec('1b')),'',$chr);

    return chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42')).$chr.chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42'));
  }
  function is_jp($str){
        if(!preg_match('/('.chr(hexdec('1b')).chr(hexdec('28')).chr(hexdec('42')).'|'.chr(hexdec('1b')).chr(hexdec('24')).chr(hexdec('42')).')/',$str)){
          return true;
        }else{
          return false;
        }

  }


function tep_strstr($str1,$str2,$bool=false){
  if($bool){
    if(strlen($str2)>0){
    return substr($str1,0,strlen($str2)*-1);
    }else{
      return false;
    }
  }else{
    return strstr($str1,$str2);
  }
}
function my_iconv($from, $to, $string) {  
  echo $from;
  echo $to;
  echo '---';
  @trigger_error('hi', E_USER_NOTICE);  
  $result = @iconv($from, $to, $string);  
  $error = error_get_last();  
  if($error['message']!='hi') {  
       $result = $string;  
       return false;
  } else { 
    echo $result;
  return $result;  
  }
}  
