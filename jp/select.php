<?php
/*
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

  if(isset($HTTP_POST_VARS['categories_id'])) {
    setcookie('quick_categories_id', intval($HTTP_POST_VARS['categories_id']), time()+(86400*30), '/');
  }

  header('location:/');
