<?php
if(!defined('OSTCLIENTINC') || !is_object($ticket)) die('Kwaheri rafiki!'); //Say bye to our friend..

?>
<div style="padding:10px 0 0 6px;">
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="left" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div style="padding:10px 0 0 6px;font-size:14px;">
<?php echo sprintf(TEXT_OST_THANKYOU_INFO_TEXT,STORE_DOMAIN);?>
</div>
<div style="text-align:right;padding-top:35px;"><a href="/index.php"><img
alt="<?php echo IMAGE_BUTTON_CONTINUE;?>"
onmouseout="this.src='includes/languages/japanese/images/buttons/button_continue.gif'"
onmouseover="this.src='includes/languages/japanese/images/buttons/button_continue_hover.gif'" src="includes/languages/japanese/images/buttons/button_continue.gif"></a></div>
<?
unset($_POST); //clear to avoid re-posting on back button??
?>
