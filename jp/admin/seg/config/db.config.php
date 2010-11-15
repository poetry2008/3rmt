<?php

define('DB_USERNAME','root');
define('DB_PASSWORD','123456');
define('DB_DATABASE','maker_3rmt');
define('DB_HOST','localhost');

if(!defined("PRO_ROOT_DIR")){
define('PRO_ROOT_DIR','/home/maker/project/3rmt/jp/admin/seg');
}
define('CLASS_DIR',PRO_ROOT_DIR.'/class/');
define('LOG_DIR',PRO_ROOT_DIR.'/log/');
define("CLI", !isset($_SERVER['HTTP_USER_AGENT']));
