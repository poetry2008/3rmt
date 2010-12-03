<?php
if(!defined('OSTCLIENTINC')) die('Kwaheri');

$e=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$t=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div>
    <p class="login_inc">
      問い合わせが初めてのお客様、または問合番号を忘れた場合は、<a href="open.php">新規お問い合わせ</a>をクリックしてください。
    </p>
    <span class="error"><?=Format::htmlchars($loginmsg)?></span>
    <form action="contact_us_login.php" method="post">
    <table cellspacing="1" cellpadding="0" border="0" class="open_login">
        <tr> 
            <th width="25%">メールアドレス</th>
            <td><input type="text" name="lemail" size="25" value="<?=$e?>"></td>
        </tr>
        <tr>
            <th>問合番号</th>
            <td><input type="text" name="lticket" size="10" value="<?=$t?>"></td>
        </tr>
        <tr>
        	<td></td>
            <td><input class="button" type="image" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif"></td>
        </tr>
    </table>
    </form>
</div>
