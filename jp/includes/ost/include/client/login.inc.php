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
    <p class="login_inc">お問い合わせが初めてのお客様、または問合番号を忘れた場合は、<a href="open.php">新規お問い合わせ</a>をクリックしてください。</p>
    <form action="<?php echo tep_href_link('contact_us_login.php','','SSL')?>" method="post">
           <div style="font-size:11px;text-align:center;color:red;"><?=Format::htmlchars($loginmsg)?></span>
    <table cellspacing="1" cellpadding="0" align="center" width="100%" class="open_login">
        <tr> 
            <th width="20%" bgcolor="#eeeeee">メールアドレス</th>
            <td><input class="input_text" type="text" name="lemail" size="25" value="<?=$e?>"><br>
            </td>
        </tr>
        <tr>
            <th bgcolor="#eeeeee">お問い合わせ番号</th>
            <td><input class="input_text" type="text" name="lticket" size="10" value="<?=$t?>"></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input class="button" type="image" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif"></td>
        </tr>
    </table>
    </form>
</div>
