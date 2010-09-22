<?php

class template {
  var $controller;
  var $action;
  var $title = '';
  function __construct(){
    $this->controller = $GLOBALS['controller'];
    $this->action     = $GLOBALS['action'];
  }
  function set_title($title){
    $this->title = $title;
  }
  function get_title(){
    return $this->title;
  }
  function display($parameters = ''){
    if (is_array($parameters)) {
      foreach($parameters as $name => $value){
        $$name = $value;
      }
    }
    include 'template/'.$this->controller.'/'.$this->action.'.php';
  }
  function partial($file,$parameters = ''){
    if (is_array($parameters)) {
      foreach($parameters as $name => $value){
        $$name = $value;
      }
    }
    include 'template/' . $file . '.php';
  }
}






