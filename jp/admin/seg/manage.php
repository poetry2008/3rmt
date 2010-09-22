<?php

//加载公用库页面
require_once("./config/db.config.php");
require_once("./class/db.php");

$action = isset($_GET['action'])?$_GET['action']:'index';
$controller = isset($_GET['controller'])?$_GET['controller']:'default';
$actionfile = "./controller/".$controller."/".$action.".php";

if (file_exists($actionfile)){
  require_once($actionfile);
    }else {
  die($actionfile);
}




