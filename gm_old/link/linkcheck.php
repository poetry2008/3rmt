<?php
error_reporting("E_ALL");
ini_set("display_errors","Off");
define('NO_LEGACY_FLEAPHP', true);
define('DEPLOY_MODE', false);
require('./Fleaphp/FLEA.php');
define('INDEX_DIR',dirname(__FILE__));
define('APP_DIR', dirname(__FILE__) . DS . 'APP');
FLEA::loadAppInf(APP_DIR . '/Config/APP_INF_backend.php');
FLEA::loadAppInf(APP_DIR . '/Config/DATABASE.php');
FLEA::loadAppInf(APP_DIR . '/Config/Global.php');
FLEA::import(APP_DIR);
FLEA::runMVC();
function ON_ACCESS_DENIED($controller, $action)
{
	echo "您不被允许访问该页 或该页不存在";
	
//	if(isset($_SESSION['BACKURL'])){
//		redirect($_SESSION['BACKURL']);
//	}else {
		redirect("/index.php");
//	}
}
