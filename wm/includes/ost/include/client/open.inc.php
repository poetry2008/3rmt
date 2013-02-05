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
<p class="login_inc">必要な情報をご入力ください.</p>
<form name="open_form" action="open.php" method="POST" enctype="multipart/form-data">
<table cellpadding=2 cellspacing=1 width="100%" class="open_users">
    <tr>
        <th width="21%">お名前</th>
        <td>
            <?if ($thisclient && ($name=$thisclient->getName())) {
                ?>
                <input type="hidden" name="name" value="<?=$name?>"><?=$name?>
            <?}else {?>
                <input type="text" name="name" size="25" value="<?=$info['name']?>">
          <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$errors['name']?></font>
        </td>
    </tr>
    <tr>
        <th nowrap >メールアドレス</th>
        <td>
            <?if ($thisclient && ($email=$thisclient->getEmail())) {
                ?>
                <input type="hidden" name="email" size="25" value="<?=$email?>"><?=$email?>
            <?}else {?>             
                <input type="text" name="email" size="25" value="<?=$info['email']?>">
            <?}?>
            &nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font>
        </td>
    </tr>
    <tr>
        <th>件名</th>
        <td>
            <input type="text" name="subject" size="35" value="<?=isset($info['subject'])?$info['subject']:(isset($_GET['products'])?$_GET['products'].'について':(isset($_GET['pname'])?$_GET['pname'].'の確保期限について':''))?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <th valign="top">ご質問内容</th>
        <td>
            <textarea name="message" cols="35" rows="8" wrap="soft" style="width:85%"><?=$info['message']?></textarea>
            <? if($errors['message']) {?> <font class="error">*&nbsp;<?=$errors['message']?></font><br/><?}?>
        </td>
    </tr>
    <?
    if($cfg->allowPriorityChange() ) {
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' WHERE ispublic=1 ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <td>重要度</td>
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
        <th valign="top">添付ファイル</th>
        <td>
            <input type="file" name="attachment">
            <br><font color="#656565" size="2">許可されているファイル形式は、拡張子が<?php echo $allow_file_show;?>のいずれかとなるものです。<br>ファイル名に「.(ドット)」を2つ以上含むファイルは添付できません。</font>
            <?php if(isset($errors['attachment'])&&$errors['attachment']){ ?>
            <br><font class="error"><?php echo $errors['attachment']?></font>
            <?php } ?>
        </td>
    </tr>
    <?}?>
    <?if($cfg && $cfg->enableCaptcha()/* && (!$thisclient ||
                                         !$thisclient->isValid())*/) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']='必須項目エラー';
        ?>
    <tr>
        <th valign="top">認証コード</th>
        <td style="line-height: 21px;"> 
          <img src="captcha.php" border="0" align="left">&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i class="captcha_comment">認証画像の内容をご入力ください.</i>
        <?php if($errors['captcha']){ ?>
          <br />
                <font class="error"><?=$errors['captcha']?></font>
        <?php } ?>
        </td>
    </tr>
    <?}?>
    <tr>
    <td></td>
    <td align="left" class="open_button">
      <a href="javascript:void(0);" onclick="document.open_form.submit();"><img src="includes/languages/japanese/images/buttons/button_send_mail.gif" /></a>
      <a href="javascript:void(0);" onclick="document.open_form.reset();"><img src="includes/languages/japanese/images/buttons/open_users01.gif" /></a>
      <a href="javascript:void(0);" onClick='window.location.href="<?php echo FILENAME_CONTACT_US;?>";'><img src="includes/languages/japanese/images/buttons/open_users02.gif" /></a>
    </td>
    </tr> 
</table> 
</form>
