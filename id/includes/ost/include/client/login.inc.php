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
<div class="login_inc_warpper">
    <div class="login_inc03">お問い合わせが初めてのお客様、または問合番号を忘れた場合は、<a href="open.php">新規お問い合わせ</a>をクリックしてください。</div>
    <form action="contact_us_login.php" method="post">
           <div style="font-size:11px;text-align:center;color:red;"><?=Format::htmlchars($loginmsg)?></div>
    <table cellspacing="0" cellpadding="0"  border="0" class="open_login" align="center">
         <tr> 
            <td valign="top" class="open_login_info"><span>メールアドレス</span><input type="text" name="lemail" size="25" value="<?=$e?>"></td>
         </tr>
         <tr>   
            <td class="open_login_info"><span>お問い合わせ番号</span><input type="text" name="lticket" size="10" value="<?=$t?>"></td>
         </tr>

    </table>
    <div class="login_inc_button"><input type="image" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif"></div>
    </form>
</div>
