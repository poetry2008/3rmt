<?php
require(dirname(__FILE__) . '/../_common/header.php');
/* @var $ex FLEA_Exception */
if(preg_match('/linkcheck\.php/',$_SERVER['REQUEST_URI'])){
header("Location: ".$_SERVER['SCRIPT_NAME']);
}else{
header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
//header("Location: /link/error404.php");
}
exit;
?>
