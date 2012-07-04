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
<div style="margin-top:15px;margin-left:6px">


    <p class="login_inc"><?php 
    echo sprintf(TEXT_OST_LOGIN_INC_LOGIN_INC_INFO,'<a href="open.php">'.TEXT_OST_LOGIN_INC_NEW_CONTACT.'</a>');?></p>
    <span class="error"><font color="red"><?=Format::htmlchars($loginmsg)?></font></span>
    <form action="contact_us_login.php" method="post">
    <table cellspacing="1" cellpadding="0" border="0" width="100%" class="open_login">
        <tr> 
            <th width="30%"><?php echo TEXT_OST_LOGIN_INC_MAILL_ADDRESS;?></th>
            <td width="70%"><input id="input_text" type="text" name="lemail" size="25" value="<?=$e?>" style="width:300px;"></td>
             <td rowspan="2"valign="bottom" align="left"><input class="button" type="image" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif"></td>
            </tr>
        <tr>
            <th width="30%"><?php echo TEXT_OST_LOGIN_INC_CONTACT_NUM;?></th>
            <td width="70%"><input id="input_text" type="text" name="lticket" size="10" value="<?=$t?>" style="width:300px;"></td>
        </tr>
    </table>
    </form>
</div>
