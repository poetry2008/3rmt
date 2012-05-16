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
<table width="100%" cellpadding="1" cellspacing="0" border="0" class="viewticket_warpper">
    <tr><td colspan=2 width=100% class="msg">問合番号<?=$ticket->getExtId()?> 
        &nbsp;<a href="view.php?id=<?=$ticket->getExtId()?>" title="Reload"><span class="Icon refresh">&nbsp;</span></a></td></tr> 
    <tr>
       <td width=43%> 
        <table align="center" class="infotable" cellspacing="1" cellpadding="3" width="100%" border=0>
          <tr>
        <th width="70" >ステータス</th>
        <td><?php
            $_status = '_'.$ticket->getStatus();
            $_open = 'オープン';
            $_closed = 'クローズ'; 
            echo $$_status;
        ?></td>
      </tr>
      <tr>
                <th>作成日時</th>
                <td><?=$ticket->getCreateDate()?></td>
            </tr>
    </table>
     </td>
     <td width=57% valign="top">
        <table align="center" class="infotable" cellspacing="1" cellpadding="3" width="100%" border=0>
            <tr>
                <th width="100">お名前</th>
                <td><?=Format::htmlchars($ticket->getName())?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?=$ticket->getEmail()?></td>
            </tr>
        </table>
       </td>
    </tr>
</table>
<div class="msg">件名<?=Format::htmlchars($ticket->getSubject())?></div>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}?>
</div>
<br>
<div align="left">
    <span class="Icon thread">回答一覧</span>
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
        <table align="center" class="message" cellspacing="0" cellpadding="1" width="100%" border=0>
            <tr><th><?=$msg_row['created']?></th></tr>
                <?if($msg_row['attachments']>0){ ?>
                <tr class="header"><td><?=$ticket->getAttachmentStr($msg_row['msg_id'],'M')?></td></tr> 
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
                        <td><?=$ticket->getAttachmentStr($respID,'R')?></td></tr>
                                    
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
    <div align="center">
        <?if($_POST && $errors['err']) {?>
            <p align="center" id="errormessage"><?=$errors['err']?></p>
        <?}elseif($msg) {?>
            <p align="center" id="infomessage"><?=$msg?></p>
        <?}?>
    </div> 
    <div id="reply">
        <form action="view.php?id=<?=$id?>#reply" name="reply" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$ticket->getExtId()?>">
            <input type="hidden" name="respid" value="<?=$respID?>">
            <input type="hidden" name="a" value="postmessage">
            <div align="left" style="font-size:12px;">
               返信する場合は、内容を入力し「送信」ボタンをクリックしてください。 <font class="error">*&nbsp;<?=$errors['message']?></font><br/>
                <textarea name="message" id="message" cols="60" rows="7" wrap="soft"><?=$info['message']?></textarea>
            </div>
            <? if($cfg->allowOnlineAttachments()) {?>
            <div align="left" style="font-size:12px;">
                添付ファイル<br><input type="file" name="attachment" id="attachment" size=30px value="<?=$info['attachment']?>" /> 
                <br><font color="#ffffff">許可されているファイル形式は、拡張子が<?php echo $allow_file_show;?>のいずれかとなるものです。<br>ファイル名に「.(ドット)」を2つ以上含むファイルは添付できません。</font>
                <?php if(isset($errors['attachment'])&&$errors['attachment']){?>
                    <br><font class="error"><?php echo $errors['attachment']?></font>
                <?php } ?>
            </div>
            <?}?>
            <div align="left"  style="padding:10px 0 10px 0;">
                <input class="button" type='submit' value='送信' />
                <input class="button" type='reset' value='リセット' />
                <input class="button" type='button' value='キャンセル' onClick='window.location.href="view.php"' />
            </div>
        </form>
    </div>
</div>
<br><br>
