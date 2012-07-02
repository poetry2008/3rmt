<?php
/*********************************************************************
    open.php

    New tickets handle.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/
//require('client.inc.php');
$_noemailclass = true;
  require_once('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_OPEN);
//require('includes/configure.php');
if(isset($_POST)&&$_POST){
  foreach($_POST as $pos_key => $pos_val){
    if(is_array($pos_val)){
      continue;
    }else{
      $_POST[$pos_key] = tep_db_prepare_input($_POST[$pos_key]);
    }
  }
  $_POST['email'] = str_replace("\xe2\x80\x8b", '',$_POST['email']);
}
require_once(DIR_OST.'client.inc.php');
define('SOURCE','Web'); //Ticket source.
$inc='open.inc.php';    //default include.
$errors=array();
if($_POST):
    //$_POST['deptId']=$_POST['emailId']=0; //Just Making sure we don't accept crap...only topicId is expected.
    $_POST['emailId']=0; //Just Making sure we don't accept crap...only topicId is expected.
    if(!$thisuser && $cfg->enableCaptcha()){
        if(!$_POST['captcha'])
            $errors['captcha']=TEXT_OPEN_INPUT_CAPTCHA;
        elseif(strcmp($_SESSION['captcha'],md5(strtolower($_POST['captcha']))))
            $errors['captcha']=TEXT_OPEN_INPUT_CAPTCHA_AGAIN;
    }
    //Ticket::create...checks for errors..
    if(($ticket=Ticket::create($_POST,$errors,SOURCE))){
              $msg = sprintf(TEXT_OPEN_MAIL_SEND,STORE_NAME);

        //Thank the user and promise speedy resolution!
        $inc='thankyou.inc.php';
    }else{
        $errors['err']=$errors['err']?$errors['err']:'<font color="#FF0000">'.TEXT_OPEN_INPUT_ERROR.'</font>';
    }
endif;

//page

$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_BROWSER_IE6X));

mysql_select_db(DB_DATABASE);
require_once(CLIENTINC_DIR.'header.inc.php');
require_once(CLIENTINC_DIR.$inc);
require_once(CLIENTINC_DIR.'footer.inc.php');
