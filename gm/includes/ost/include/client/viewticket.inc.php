<?php
$cfg_1= new Config(1);
$allow_file_show = $cfg_1->config['allowed_filetypes'];
if(!defined('OSTCLIENTINC') || !is_object($thisclient) || !is_object($ticket)) die('Kwaheri'); //bye..see ya
//Double check access one last time...
if(strcasecmp($thisclient->getEmail(),$ticket->getEmail())) die('Access Denied');

$info=($_POST && $errors)?Format::input($_POST):array(); //Re-use the post info on error...savekeyboards.org

$dept = $ticket->getDept();
//Making sure we don't leak out internal dept names
$dept=($dept && $dept->isPublic())?$dept:$cfg->getDefaultDept();
//We roll like that...
?>
<table  class="view_contents" width="100%">
  <tr>
    <td class="msg"><h3><?=TEXT_VIEW_NUM_QUERY.$ticket->getExtId()?>&nbsp;<a href="<?php echo tep_href_link('view.php','id='.$ticket->getExtId(),'SSL');?>" title="Reload"><img style="vertical-align:middle;" src="images/ico/refresh.gif" /></a></h3></td>
  </tr> 
  <tr>
    <td>  
        <table class="infotable" width="100%">
          <tr>
            <th width="20%"><?php echo TEXT_VIEW_STATUS;?></th>
            <td><?php
            $_status = '_'.$ticket->getStatus();
            $_open = TEXT_VIEW_OPEN;
            $_closed = TEXT_VIEW_CLOSED; 
            echo $$_status;
        ?>
            </td>
          </tr>
          <tr>
            <th><?php echo TEXT_VIEW_CREATE_DATE?></th>
            <td><?=$ticket->getCreateDate()?></td>
          </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td>
        <table width="100%">
            <tr>
                <th width="20%"><?php echo TEXT_VIEW_YOUR_NAME;?></th>
                <td><?=Format::htmlchars($ticket->getName())?></td>
            </tr>
            <tr>
                <th width="20%"><?php echo TEXT_VIEW_EMAIL?></th>
                <td><?=$ticket->getEmail()?></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%">
          <tr>
             <td width="20%"><?php echo TEXT_VIEW_SUBJECT?></td><td><?=Format::htmlchars($ticket->getSubject())?></td>
          </tr>
        </table>
    </td>
  </tr>
</table>
<div class="prompt">
    <?if($errors['err']) {?>
        <?=$errors['err']?>
    <?}elseif($msg) {?>
       <?=$msg?>
    <?}?>
</div>
<div align="left">
    <span class="Icon thread"><h3><?php echo TEXT_VIEW_ANSWER;?></h3></span>
    <div id="ticketthread">
        <?
      //get messages
        $sql='SELECT msg.*, count(attach_id) as attachments  FROM '.TICKET_MESSAGE_TABLE.' msg '.
            ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  msg.ticket_id=attach.ticket_id AND msg.msg_id=attach.ref_id AND ref_type=\'M\' '.
            ' WHERE  msg.ticket_id='.db_input($ticket->getId()).
            ' GROUP BY msg.msg_id ORDER BY created';
      $msgres =db_query($sql);
      while ($msg_row = db_fetch_array($msgres)):
        ?>
        <table class="message" cellspacing="0" cellpadding="1" width="100%" border=0>
            <tr><th><?=$msg_row['created']?><hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none; margin:10px 0;"></th></tr>
                <?if($msg_row['attachments']>0){ ?>
        <tr class="header"><td><?=$ticket->getAttachmentStr($msg_row['msg_id'],'M',0)?></td></tr> 
                <?}?>
                <tr class="info">
                    <td><?=Format::display($msg_row['message'])?></td></tr>
        </table>
            <?
            //get answers for messages
            $sql='SELECT resp.*,count(attach_id) as attachments FROM '.TICKET_RESPONSE_TABLE.' resp '.
                ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type=\'R\' '.
                ' WHERE msg_id='.db_input($msg_row['msg_id']).' AND resp.ticket_id='.db_input($ticket->getId()).
                ' GROUP BY resp.response_id ORDER BY created';
            //echo $sql;
        $resp =db_query($sql);
        while ($resp_row = db_fetch_array($resp)) {
                $respID=$resp_row['response_id'];
                $name=$cfg->hideStaffName()?'staff':Format::htmlchars($resp_row['staff_name']);
                ?>
            <table align="center" class="response" cellspacing="0" cellpadding="1" width="100%" border=0>
                <tr>
                  <th><?=$resp_row['created']?>&nbsp;-&nbsp;<?=$name?></th></tr>
                    <?if($resp_row['attachments']>0){ ?>
                    <tr class="header">
            <td><?=$ticket->getAttachmentStr($respID,'R',0)?></td></tr>
                                    
                    <?}?>
              <tr class="info">
                <td> <?=Format::display($resp_row['response'])?></td></tr>
            </table>
        <?
        } //endwhile...response loop.
            $msgid =$msg_row['msg_id'];
        endwhile; //message loop.
     ?>
    </div>
</div>
<div>
    <div align="center" class="prompt">
        <?if($_POST && $errors['err']) {?>
            <?=$errors['err']?>
        <?}elseif($msg) {?>
            <?=$msg?>
        <?}?>
    </div> 
    <div id="reply">
        <form action="<?php echo tep_href_link('view.php','id='.$id.'#reply','SSL');?>" name="reply" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$ticket->getExtId()?>">
            <input type="hidden" name="respid" value="<?=$respID?>">
            <input type="hidden" name="a" value="postmessage">
            <div align="left">
              <div style="float:left;"> <?php echo
              TEXT_VIEW_RETURN;?></div><div style="float:left;color:#FF0000">&nbsp;<?=$errors['message']?></div><br/>
                <textarea name="message" id="message" style="width:80%;
                margin-top:5px" rows="10" wrap="soft"><?=$info['message']?></textarea>
            </div>
            <? if($cfg->allowOnlineAttachments()) {?>
            <div align="left">
                <?php echo TEXT_VIEW_ATTACHMENT;?><br><input type="file" name="attachment" id="attachment" value="<?=$info['attachment']?>" /> 
                <br><font color="#ffffff"><?php echo
                TEXT_VIEW_DES_PART1.$allow_file_show.TEXT_VIEW_DES_PART2;?><br><?php echo TEXT_VIEW_DES_PART3?></font>
                <?php if(isset($errors['attachment'])&&$errors['attachment']){?>
                    <br><font class="error"><?php echo $errors['attachment']?></font>
                <?php } ?>
            </div>
            <?}?>
            <div class="botton-continue">
               <a value="<?php echo TEXT_VIEW_SEND_EMAIL;?>" style="padding:0;background:none;border:none;"
               class="button" onclick="document.forms.reply.submit();" href="javascript:vold(0)">
                 <img src="includes/languages/japanese/images/buttons/button_send_mail.gif" onmouseover="this.src='includes/languages/japanese/images/buttons/button_send_mail_hover.gif'" onmouseout="this.src='includes/languages/japanese/images/buttons/button_send_mail.gif'"></a>
           <a onclick="document.forms.reply.reset()" style="padding:0;background:none;border:none;" value="<?php echo TEXT_VIEW_RESET;?>" class="button">
                  <img src="includes/languages/japanese/images/buttons/open_users01.gif" onmouseover="this.src='includes/languages/japanese/images/buttons/open_users01_hover.gif'" onmouseout="this.src='includes/languages/japanese/images/buttons/open_users01.gif'"></a>
            <a onclick="window.location.href='<?php echo tep_href_link('view.php','','SSL');?>'" value="<?php echo TEXT_VIEW_CANCELED?>" style="padding:0;background:none;border:none;" class="button" href="javascript:vold(0)"><img src="includes/languages/japanese/images/buttons/open_users02.gif" onmouseover="this.src='includes/languages/japanese/images/buttons/open_users02_hover.gif'" onmouseout="this.src='includes/languages/japanese/images/buttons/open_users02.gif'"></a>
            </div>
        </form>
    </div>
</div>

