<?php
require_once('includes/application_top.php');
if(isset($_GET)){
  if($_GET['name']&&$_GET['type']=='js'&&$_GET['path']){
    // name not empty and type is js require file *.php.js
    $name = $_GET['name'];
    $path = str_replace('|','/',$_GET['path']);
    if(preg_match('/^[_a-zA-Z][A-Za-z0-9_]+$/',$name)){
      if(file_exists(DIR_WS_LANGUAGES.$language .'/javascript/'.$name.'.php')){
        require_once(DIR_WS_LANGUAGES.$language .'/javascript/'.$name.'.php');
      }
      $file_path = $path.'/'.$name.".js.php";
      if(file_exists($file_path)){
        require_once($file_path);
      }else{
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        exit;
      }
    }else{
      header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
      exit;
    }
  }
}
?>
