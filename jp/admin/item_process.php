<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_POST['type'])) {
    $classname = 'HM_Item_'.ucfirst($_POST['type']); 
    require_once('oa/'.$classname.'.php'); 
    $item_instance = new $classname(); 
    echo $item_instance->prepareFormWithParent($_POST['eid']);
    echo $item_instance->prepareForm($_POST['eid']); 
  }
