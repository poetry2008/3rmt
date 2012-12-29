<?php
/*
    $Id$
*/
require('staff.inc.php');
require(INCLUDE_DIR.'class.captcha.php');
$captcha = new Captcha(5,12,SCP_DIR.'images/captcha/');
echo $captcha->getImage();
?>
