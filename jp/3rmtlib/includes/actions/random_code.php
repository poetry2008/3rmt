<?php
/*
    $Id$
*/
require_once DIR_WS_CLASSES.'random_code.php';

$random_code_pic = new Random_code(5,12,DIR_FS_DOCUMENT_ROOT.'images/captcha/');
echo $random_code_pic->getImage();
?>
