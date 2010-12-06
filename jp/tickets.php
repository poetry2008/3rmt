<?php
/*********************************************************************
    tickets.php

    Main client/user interface.
    Note that we are using external ID. The real (local) ids are hidden from user.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/

$_noemailclass = true;

require_once('includes/application_top.php');
require_once('includes/ost/secure.inc.php');
$breadcrumb->add('お問い合わせ', tep_href_link(FILENAME_CONTACT_US));

if(!is_object($thisclient) || !$thisclient->isValid()) die('Access denied'); //Double check again.

require_once(INCLUDE_DIR.'class.ticket.php');

$ticket=null;
$inc='tickets.inc.php'; //Default page...show all tickets.
//Check if any id is given...

if(($id=$_REQUEST['id']?$_REQUEST['id']:$_POST['ticket_id']) && is_numeric($id)) {
    //id given fetch the ticket info and check perm.

  $ticket= new Ticket(Ticket::getIdByExtId((int)$id,true));
    if(!$ticket or !$ticket->getEmail()) {
        $ticket=null; //clear.
        $errors['err']='Access Denied. Possibly invalid ticket ID';
    }elseif(strcasecmp($thisclient->getEmail(),$ticket->getEmail())){
        $errors['err']='Security violation. Repeated violations will result in your account being locked.';
        $ticket=null; //clear.
    }else{
        //Everything checked out.
        $inc='viewticket.inc.php';
    }
}

//Process post...depends on $ticket object above.
if($_POST && is_object($ticket) && $ticket->getId()):
    $errors=array();
    switch(strtolower($_POST['a'])){
    case 'postmessage':
        if(strcasecmp($thisclient->getEmail(),$ticket->getEmail())) { //double check perm again!
            $errors['err']='Access Denied. Possibly invalid ticket ID';
            $inc='tickets.inc.php'; //Show the tickets.               
        }

        if(!$_POST['message'])
            $errors['message']='必須';
        //check attachment..if any is set
        if($_FILES['attachment']['name']) {
            if(!$cfg->allowOnlineAttachments()) //Something wrong with the form...user shouldn't have an option to attach
                $errors['attachment']='File [ '.$_FILES['attachment']['name'].' ] rejected';
            elseif(!$cfg->canUploadFileType($_FILES['attachment']['name']))
                $errors['attachment']='Invalid file type [ '.$_FILES['attachment']['name'].' ]';
            elseif($_FILES['attachment']['size']>$cfg->getMaxFileSize())
                $errors['attachment']='File is too big. Max '.$cfg->getMaxFileSize().' bytes allowed';
        }
                    
        if(!$errors){
            //Everything checked out...do the magic.
            if(($msgid=$ticket->postMessage($_POST['message'],'Web'))) {
                if($_FILES['attachment']['name'] && $cfg->canUploadFiles() && $cfg->allowOnlineAttachments())
                    $ticket->uploadAttachment($_FILES['attachment'],$msgid,'M');
                $msg='送信完了';
                // 跳转之后就不显示信息了
                tep_redirect($_SERVER['REQUEST_URI']);
            }else{
                $errors['err']='Unable to post the message. Try again';
            }
        }else{
            $errors['err']=$errors['err']?$errors['err']:'送信エラー。問合内容を再入力してください';
        }
        break;
    default:
        $errors['err']='Uknown action';
    }
    $ticket->reload();
endif;

mysql_select_db(DB_DATABASE);
include(CLIENTINC_DIR.'header.inc.php');
mysql_select_db(DBNAME);
include(CLIENTINC_DIR.$inc);
mysql_select_db(DB_DATABASE);
include(CLIENTINC_DIR.'footer.inc.php');
