<?php
if(!defined('OSTCLIENTINC') || !is_object($ticket)) die('Kwaheri rafiki!'); //Say bye to our friend..

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
24時間経過しても返答が届かない場合は、以下のことを必ずご確認ください。<br><br>
＜迷惑メールフォルダの確認＞<br>
弊社のメールが 「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br><br>
＜メールドメインの受信制限を設定している＞<br>
<?php echo STORE_DOMAIN;?>のメールドメインを受信するように設定をお願いいたします。<br><br>
＜それでも届かないときは、メールアドレス変更＞<br>
別のメールアドレスをご入力いただき、お問い合わせをお願いいたします。
</div>
<div style="text-align:right;padding-top:20px;"><a href="/index.php"><img
alt="次に進む"
onmouseout="this.src='includes/languages/japanese/images/buttons/button_continue.gif'"
onmouseover="this.src='includes/languages/japanese/images/buttons/button_continue_hover.gif'" src="includes/languages/japanese/images/buttons/button_continue.gif"></a></div>
<?
unset($_POST); //clear to avoid re-posting on back button??
?>
