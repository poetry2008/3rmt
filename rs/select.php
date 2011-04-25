<?php
/*
  $Id$
*/
  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

  if(isset($_POST['categories_id'])) {
    setcookie('quick_categories_id', intval($_POST['categories_id']), time()+(86400*30), '/');
  }

  header('location:/');
