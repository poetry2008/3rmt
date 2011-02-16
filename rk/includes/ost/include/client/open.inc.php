<?php
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
<div class="open_title">必要な情報をご入力ください.</div><br>
<form action="open.php" method="POST" enctype="multipart/form-data">
<table cellpadding=2 cellspacing=1 width="100%" class="open_users">
    <tr>
        <th width="20%">お名前:</th>
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
        <th nowrap >メールアドレス:</th>
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
        <th>件名:</th>
        <td>
            <input type="text" name="subject" size="35" value="<?=isset($info['subject'])?$info['subject']:(isset($_GET['products'])?$_GET['products'].'について':'')?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <th valign="top">ご質問内容: </th>
        <td>
            <? if($errors['message']) {?> <font class="error"><b>&nbsp;<?=$errors['message']?></b></font><br/><?}?>
            <textarea name="message" cols="35" rows="8" wrap="soft" style="width:85%"><?=$info['message']?></textarea></td>
    </tr>
    <?
    if($cfg->allowPriorityChange() ) {
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' WHERE ispublic=1 ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <td>重要度:</td>
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
        <th>添付ファイル:</th>
        <td>
            <input type="file" name="attachment"><font class="error">&nbsp;<?=$errors['attachment']?></font>
        </td>
    </tr>
    <?}?>
    <?if($cfg && $cfg->enableCaptcha()/* && (!$thisclient ||
                                         !$thisclient->isValid())*/) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']='必須項目エラー';
        ?>
    <tr>
        <th valign="top">認証コード:</th>
        <td><img src="captcha.php" border="0" align="left" alt="img">
        <span>&nbsp;&nbsp;<input type="text" name="captcha" size="7" value="">&nbsp;<i class="captcha_comment">認証画像の内容をご入力ください.</i></span>
        <?php if($errors['captcha']){?>
        <br/>
                <font class="error">&nbsp;<?=$errors['captcha']?></font>
        <?php }?>
        </td>
    </tr>
    <?}?>
    <tr>
        <td></td>
        <td>
            <input class="button" type="submit" name="submit_x" value="&#36865;&#20449;">
            <input class="button" type="reset" value="&#12522;&#12475;&#12483;&#12488;">
            <input class="button" type="button" name="cancel" value="&#12461;&#12515;&#12531;&#12475;&#12523;" onClick='window.location.href="<?php echo FILENAME_CONTACT_US ?>"'>
        </td>
    </tr>
</table>
</form>
