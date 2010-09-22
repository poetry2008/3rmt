<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//加载公用库页面
require_once("./config/db.config.php");
require_once("./class/db.php");
require_once("./class/template.php");

$action = isset($_GET['action'])?$_GET['action']:'index';
$controller = isset($_GET['controller'])?$_GET['controller']:'default';

$actionfile = "./controller/".$controller."/".$action.".php";

$template = new template;

if (file_exists($actionfile)){
  require_once($actionfile);
    }else {
  die($actionfile);
}




