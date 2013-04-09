<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  $obj_id = $_POST['id'];
  $se_arr = array(); 
  if (isset($_SESSION['l_select_box'])) {
    $se_arr = explode(',', $_SESSION['l_select_box']); 
  }else{
    $_SESSION['l_select_box']='';
  }
  switch ($_POST['action']) {
/* -----------------------------------------------------
   case 'insert' 记录左侧栏打开的窗口   
   case 'del' 删除左侧关闭的窗口   
------------------------------------------------------*/
    case 'insert':
      $se_arr[] = $obj_id;
      $tmp_arr = array_unique($se_arr);
      $l_select_box = implode(',', $tmp_arr);
      if(tep_session_register('l_select_box')){
        $_SESSION['l_select_box'] = $l_select_box;
      }
      break; 
    case 'del':
      $tmp_arr = array(); 
      foreach ($se_arr as $se_key => $se_value) {
        if (($se_value != $obj_id) && !empty($se_value)) {
          $tmp_arr[] = $se_value; 
        }
      }
      $l_select_box = implode(',', $tmp_arr);
      tep_session_register('l_select_box'); 
      if(tep_session_register('l_select_box')){
        $_SESSION['l_select_box'] = $l_select_box;
      }
      break; 
  }
