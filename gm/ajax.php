<?php
  /*
   $Id$
   */
  require('includes/application_top.php');
  
  echo '<?xml version="1.0" encoding="utf-8"?>';
  
  $id = tep_db_prepare_input($_GET['id']);
  
  if(file_exists('ajax/php/'.$id.'.php')) {
    include('ajax/php/'.$id.'.php');
  } else {
    echo '';
  }
