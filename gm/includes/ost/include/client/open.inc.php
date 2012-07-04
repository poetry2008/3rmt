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
<div class="open_spacing"><?php echo TEXT_OPEN_REQUIRED_INFORMATION; ?></div>
<form action="open.php" method="POST" enctype="multipart/form-data" name="form_2">
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="open_spacing">
    <tr>
        <td width="20%" align="left"><?php echo TEXT_OPEN_YOUR_NAME;?></td>
        <td>
            <?if ($thisclient && ($name=$thisclient->getName())) {
                ?>
               <div style="float:left"><input id="input_text" type="hidden" name="name" value="<?php
               echo $name; ?>"><?php echo $name ;?></div>
            <?}else {?>
              <div><input id="input_text" type="text" name="name"
                value="<?=$info['name']?>"></div>
          <?}?>
            &nbsp;<div class="error">&nbsp;*&nbsp;<?=$errors['name']?></div>
        </td>
    </tr>
    <tr>
        <td align="left" ><?php echo TEXT_OPEN_EMAIL_ADDRESS;?></td>
        <td>
            <?if ($thisclient && ($email=$thisclient->getEmail())) {
                ?>
               <div style="float:left"><input id="input_text" type="hidden" name="email"
               value="<?=$email?>"><?=$email?></div>
            <?}else {?>             
              <div><input id="input_text" type="text" name="email"
                value="<?=$info['email']?>"></div>
            <?}?>
            &nbsp;<div class="error">&nbsp;*&nbsp;<?=$errors['email']?></div>
        </td>
    </tr>
    <tr>
        <td align="left"><?php echo TEXT_OPEN_SUBJECT;?></td>
        <td>
            <input id="input_text" type="text" name="subject"
            value="<?=isset($info['subject'])?$info['subject']:(isset($_GET['products'])?$_GET['products'].TEXT_OPEN_PART1:(isset($_GET['pname'])?$_GET['pname'].TEXT_OPEN_PART2:''))?>">
            &nbsp;<div class="error">&nbsp;*&nbsp;<?=$errors['subject']?></div>
        </td>
    </tr>
    <tr>
        <td align="left" valign="top"><?php echo TEXT_OPEN_YOUR_QUESTION;?></td>
        <td>
            <textarea id="input_text" name="message" cols="35" rows="8" wrap="soft" style="width:85%"><?=$info['message']?></textarea>
            <? if($errors['message']) {?> <div
              class="error" >&nbsp;*&nbsp;<?=$errors['message']?></div><?}?>
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

    <?if(($cfg->allowOnlineAttachments() && !$cfg->allowAttachmentsOnlogin()) || ($cfg->allowAttachmentsOnlogin() && ($thisclient && $thisclient->isValid()))){
        
        ?>
    <tr>
        <td valign="top" align="left"><?php echo  TEXT_OPEN_ATTACHMENT;?></td>
        <td>
            <input id="input_text" type="file" name="attachment">
            <br><div class="open_info"><?php echo
            TEXT_OPEN_DES_PART1.$allow_file_show.TEXT_OPEN_DES_PART2;?><br><?php
            echo TEXT_OPEN_DES_PART3;?></div>
            <?php if(isset($errors['attachment'])&&$errors['attachment']){ ?>
            <div class="error"><?php echo $errors['attachment']?></div>
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
        <td valign="top" align="left"><?php echo TEXT_OPEN_CODE;?></td>
        <td><img src="captcha.php" border="0" align="left">
        <span>&nbsp;&nbsp;<input id="input_text" type="text" name="captcha"
        value="">&nbsp;<i class="captcha_comment"><?php echo
        TEXT_OPEN_ENTER_CONTENTS;?></i></span>
        <?php if($errors['captcha']){ ?>
          <br />
                <div class="error_information" style="margin-top:5px">&nbsp;<?=$errors['captcha']?></div>
        <?php } ?>
        </td>
    </tr>
    <?}?>
    <tr>
        <td></td>
        <td style="padding-top:35px;">
           <a <?php echo $void_href;?> onclick="document.form_2.submit();" class="button"
                 style="padding:0;background:none;border:none;" value="<?php echo
                 TEXT_OPEN_SEND_EMAIL?>">
                 <img onmouseout="this.src='includes/languages/japanese/images/buttons/button_send_mail.gif'"
                 onmouseover="this.src='includes/languages/japanese/images/buttons/button_send_mail_hover.gif'"
                 src="includes/languages/japanese/images/buttons/button_send_mail.gif"
                 /></a>
           <a <?php echo $void_href;?> class="button" value="<?php echo
           TEXT_OPEN_RESET;?>"style="padding:0;background:none;border:none;"
                     onclick="document.form_2.reset()">
                  <img onmouseout="this.src='includes/languages/japanese/images/buttons/open_users01.gif'"  onmouseover="this.src='includes/languages/japanese/images/buttons/open_users01_hover.gif'" src="includes/languages/japanese/images/buttons/open_users01.gif"></a>
            <a <?php echo $void_href;?> class="button"
            style="padding:0;background:none;border:none;" value="<?php echo
            TEXT_OPEN_CANCELED;?>"
            onClick='window.location.href="<?php echo FILENAME_CONTACT_US;?>";'><img
            onmouseout="this.src='includes/languages/japanese/images/buttons/open_users02.gif'"
            onmouseover="this.src='includes/languages/japanese/images/buttons/open_users02_hover.gif'"
            src="includes/languages/japanese/images/buttons/open_users02.gif" /></a>
      
        </td>
    </tr>
</table>
</form>
