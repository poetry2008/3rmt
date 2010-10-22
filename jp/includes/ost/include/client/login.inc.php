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
        
         <a href="open.php">新規お問い合わせ　</a>をクリックして、新しく
    お問い合わせを作成するか、
    <br/>
下記の欄で再ログインしてください。

    </p>
    <span class="error"><?=Format::htmlchars($loginmsg)?></span>
    <form action="contact_us_login.php" method="post">
    <table cellspacing="1" cellpadding="0" align="center" width="100%" class="open_login">
        <tr> 
            <th width="20%">メールアドレス:</th>
            <td><input type="text" name="lemail" size="25" value="<?=$e?>">
            </td>
            </tr>
            <tr>
            <th>問合番号:</th>
            <td><input type="text" name="lticket" size="10" value="<?=$t?>">
            </td>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td><input class="button" type="submit" value="送信"></td>
            </tr>
    </table>
    </form>
</div>
