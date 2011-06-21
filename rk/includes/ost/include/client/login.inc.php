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
       お問い合わせが初めてのお客様、または問合番号を忘れた場合は、<a href="open.php">新規お問い合わせ</a>をクリックしてください。
    </p>
    <div class="error"><?=Format::htmlchars($loginmsg)?></div>
    <form action="contact_us_login.php" method="post">
    <table cellspacing="1" cellpadding="0" align="center" width="100%" class="open_login">
        <tr> 
            <td>メールアドレス</td>
            <td><input type="text" name="lemail" size="25" value="<?=$e?>"></td>
         </tr>
         <tr>   
            <td>お問い合わせ番号</td>
            <td><input type="text" name="lticket" size="10" value="<?=$t?>"></td>
            </tr>
        </tr>
    </table>
     <div class="login_inc_bottom"><input class="button" type="submit" value="送信"></div>
    </form>
</div>
