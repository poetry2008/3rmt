<?php
$allow_file_show = $cfg->config['allowed_filetypes'];
if(!defined('OSTCLIENTINC')) die('Kwaheri rafiki!'); //Say bye to our friend..

$info=($_POST && $errors)?Format::input($_POST):array(); //on error...use the post data
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div><?php echo TEXT_OPEN_REQUIRED_INFORMATION;?></div>
<form name="open_form" action="<?php echo tep_href_link('open.php','','SSL')?>" method="POST" enctype="multipart/form-data">
<table class="open_table" align="left" cellpadding=2 cellspacing=1 width="100%">
    <tr>
        <th width="27%" align="left"><?php echo TEXT_OPEN_YOUR_NAME;?></th>
        <td>
            <?if ($thisclient && ($name=$thisclient->getName())) {
                ?>
                <input type="hidden" name="name" value="<?=$name?>"><?=$name?>
            <?}else {?>
                <input type="text" name="name" size="25" value="<?=$info['name']?>"><?}?>&nbsp;<font class="error">*&nbsp;<?=$errors['name']?></font>
        </td>
    </tr>
    <tr>
        <th align="left" ><?php echo TEXT_OPEN_EMAIL_ADDRESS;?></th>
        <td>
            <?if ($thisclient && ($email=$thisclient->getEmail())) {
                ?>
                <input type="hidden" name="email" size="25" value="<?=$email?>"><?=$email?>
            <?}else {?>             
                <input type="text" name="email" size="25" value="<?=$info['email']?>"><?}?>&nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font>
        </td>
    </tr>
    <tr>
        <th align="left"><?php echo TEXT_OPEN_SUBJECT;?></th>
        <td>
            <input type="text" name="subject" size="25" value="<?=isset($info['subject'])?$info['subject']:(isset($_GET['products'])?$_GET['products'].TEXT_OPEN_PART1:(isset($_GET['pname'])?$_GET['pname'].TEXT_OPEN_PART2:''))?>">&nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <th align="left" valign="top"><?php echo TEXT_OPEN_YOUR_QUESTION;?></th>
        <td>
            <textarea name="message" cols="35" rows="8" wrap="soft" style="width:80%"><?=$info['message']?></textarea>
            <? if($errors['message']) {?> <font class="error">*&nbsp;<?=$errors['message']?></font><br/><?}?>
        </td>
    </tr>
    <?
    if($cfg->allowPriorityChange() ) {
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' WHERE ispublic=1 ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <td><?php echo TEXT_OPEN_SEVERITY;?></td>
        <td>
            <select name="pri">
              <?
                $info['pri']=$info['pri']?$info['pri']:$cfg->getDefaultPriorityId(); //use system's default priority.
                while($row=db_fetch_array($priorities)){ ?>
                    <option value="<?=$row['priority_id']?>" <?=$info['pri']==$row['priority_id']?'selected':''?> ><?=$row['priority_desc']?></option>
              <?}?>
            </select>
        </td>
       </tr>
    <? }
    }?>

    <?if(($cfg->allowOnlineAttachments() && !$cfg->allowAttachmentsOnlogin())  
                || ($cfg->allowAttachmentsOnlogin() && ($thisclient && $thisclient->isValid()))){
        
        ?>
    <tr>
        <th valign="top" align="left"><?php echo TEXT_OPEN_ATTACHMENT;?></th>
        <td>
            <input type="file" name="attachment">
            <br><font color="#ffffff" size="2"><?php echo sprintf(TEXT_OPEN_DES_PART,$allow_file_show);?></font>
            <?php if(isset($errors['attachment'])&&$errors['attachment']){ ?>
            <br><font class="error"><?php echo $errors['attachment']?></font>
            <?php } ?>
        </td>
    </tr>
    <?}?>
    <?if($cfg && $cfg->enableCaptcha()/* && (!$thisclient ||
                                         !$thisclient->isValid())*/) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']=TEXT_OPEN_MANDATORY_ERROR;
        ?>
    <tr>
        <th valign="top" align="left"><?php echo TEXT_OPEN_CODE;?></th>
        <td>
        <div class="img_clear"><img src="captcha.php" border="0" align="left">&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i class="captcha_comment"><?php 
        echo TEXT_OPEN_ENTER_CONTENTS;?></i>
        <?php if($errors['captcha']){ ?>
                <br>
                <font class="error"><?=$errors['captcha']?></font>
        <?php } ?>
		</div>
        </td>
    </tr>
    <?}?>
    <tr>
	<th></th>
        <td align="left" class="open_button">
            <a href="javascript:void(0);" onclick="document.open_form.submit();"><img src="includes/languages/japanese/images/buttons/button_send_mail.gif" /></a>
            <a href="javascript:void(0);" onclick="document.open_form.reset();"><img src="includes/languages/japanese/images/buttons/open_users01.gif" /></a>
            <a href="javascript:void(0);" onClick='window.location.href="<?php echo FILENAME_CONTACT_US;?>";'><img src="includes/languages/japanese/images/buttons/open_users02.gif" /></a>
        </td>
    </tr>
</table>
</form>
