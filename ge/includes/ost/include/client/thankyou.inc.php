<?php
if(!defined('OSTCLIENTINC') || !is_object($ticket)) die('Kwaheri rafiki!'); //Say bye to our friend..

//Please customize the message below to fit your organization speak!
?>
<div style="padding:0 0 0 24px;">
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="left" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div style="padding:0 16px 0 34px;font-size:14px;">
<?php echo sprintf(TEXT_OST_THANKYOU_INFO_TEXT,STORE_DOMAIN);?>
</div>
<div style="text-align:right;padding-right:14px;"><a href="<?php echo tep_href_link(FILENAME_DEFAULT);
?>"><img alt="<?php echo IMAGE_BUTTON_CONTINUE;?>" src="includes/languages/japanese/images/buttons/button_continue.gif"></a></div>
<?
unset($_POST); //clear to avoid re-posting on back button??
?>
