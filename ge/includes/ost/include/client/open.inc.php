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
<div>必要な情報をご入力ください.</div>
<form action="open.php" method="POST" enctype="multipart/form-data">
<table class="open_table" align="left" cellpadding=2 cellspacing=1 width="100%">
    <tr>
        <th width="27%" align="left">お名前</th>
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
        <th align="left" >メールアドレス</th>
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
        <th align="left">件名</th>
        <td>
            <input type="text" name="subject" size="35" value="<?=isset($info['subject'])?$info['subject']:(isset($_GET['products'])?$_GET['products'].'について':(isset($_GET['pname'])?$_GET['pname'].'の確保期限について':''))?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <th align="left" valign="top">ご質問内容</th>
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
        <th valign="top" align="left">添付ファイル</th>
        <td>
            <input type="file" name="attachment">
            <br><font color="#ffffff" size="2">許可されているファイル形式は、拡張子が<?php echo $allow_file_show;?>のいずれかとなるものです。<br>ファイル名に「.(ドット)」を2つ以上含むファイルは添付できません。</font>
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
        <th valign="top" align="left">認証コード</th>
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
        <td>
            <button type="submit" class="button" style="padding:0;background:none;border:none;" value="送信"><img src="includes/languages/japanese/images/buttons/button_send_mail.gif" /></button>
            <button type="reset"  class="button" style="padding:0;background:none;border:none;" value="リセット"><img src="includes/languages/japanese/images/buttons/open_users01.gif" /></button>
            <button type="button" class="button" style="padding:0;background:none;border:none;" value="キャンセル" onClick='window.location.href="<?php echo FILENAME_CONTACT_US;?>";'><img src="includes/languages/japanese/images/buttons/open_users02.gif" /></button>
      
        </td>
    </tr>
</table>
</form>