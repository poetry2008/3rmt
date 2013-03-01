<?php
/*
 * 个人设定
 */
require('includes/application_top.php');

if($_GET['action'] == 'update'){
  //更新设定
  $orders_site = tep_db_prepare_input($_POST['orders_site']);
  $orders_work = tep_db_prepare_input($_POST['orders_work']);
  $orders_sort_list = tep_db_prepare_input($_POST['orders_sort_list']);
  $orders_sort = tep_db_prepare_input($_POST['orders_sort']);
  $preorders_site = tep_db_prepare_input($_POST['preorders_site']);
  $preorders_work = tep_db_prepare_input($_POST['preorders_work']);
  $preorders_sort_list = tep_db_prepare_input($_POST['preorders_sort_list']);
  $preorders_sort = tep_db_prepare_input($_POST['preorders_sort']);
  $personal_language = tep_db_prepare_input($_POST['personal_language']);
  $is_transaction = tep_db_prepare_input($_POST['is_transaction']);
  
  $error = false;
  //订单管理页是否选择网站 
  if(empty($orders_site)){
    $error = true;
    $orders_site_error = TEXT_PERSONAL_SETTING_SITE_ERROR; 
  }
  //订单管理页是否选择工作组 
  if(empty($orders_work)){
    $error = true;
    $orders_work_error = TEXT_PERSONAL_SETTING_SITE_ERROR; 
  }
  //订单管理页的排序值和排序的方式要么全选,要么全不选 
  if(($orders_sort_list != '' && $orders_sort == '') || ($orders_sort_list == '' && $orders_sort != '')){
    $error = true;
    $orders_sort_error = TEXT_PERSONAL_SETTING_SORT_ERROR;
  }

  //预约管理页是否选择网站 
  if(empty($preorders_site)){
    $error = true;
    $preorders_site_error = TEXT_PERSONAL_SETTING_SITE_ERROR; 
  }
  //预约管理页是否选择工作组 
  if(empty($preorders_work)){
    $error = true;
    $preorders_work_error = TEXT_PERSONAL_SETTING_SITE_ERROR; 
  }
  //预约管理页的排序值和排序的方式要么全选,要么全不选 
  if(($preorders_sort_list != '' && $preorders_sort == '') || ($preorders_sort_list == '' && $preorders_sort != '')){
    $error = true;
    $preorders_sort_error = TEXT_PERSONAL_SETTING_SORT_ERROR;
  }

  if($error == false){
    $personal_language_temp_array = array();
    if(PERSONAL_SETTING_LANGUAGE == ''){
      $personal_language_temp_array = array($ocertify->auth_user=>$personal_language);
    }else{
      $personal_language_str_array = unserialize(PERSONAL_SETTING_LANGUAGE); 
      $personal_language_str_array[$ocertify->auth_user] = $personal_language;
      $personal_language_temp_array = $personal_language_str_array;
    }
    $personal_language_str = serialize($personal_language_temp_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$personal_language_str."' where configuration_key='PERSONAL_SETTING_LANGUAGE'");
    $user_info = tep_get_user_info($ocertify->auth_user);
    $orders_site_temp_array = array();
    $orders_site_setting_str = implode('|',$orders_site);
    if(PERSONAL_SETTING_ORDERS_SITE == ''){
      $orders_site_temp_array = array($user_info['name']=>$orders_site_setting_str);
    }else{
      $orders_site_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
      $orders_site_setting_array[$user_info['name']] = $orders_site_setting_str;      
      $orders_site_temp_array = $orders_site_setting_array;
    }
    $orders_site_str = serialize($orders_site_temp_array);
    //把当前用户的更新日期，存入到数据库
    $configuration_row = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='PERSONAL_SETTING_ORDERS_SITE'"));
    $update_user_array = array();
    if($configuration_row['configuration_description'] != ''){

      $update_user_array = unserialize($configuration_row['configuration_description']);
      $update_user_array[$ocertify->auth_user]['user'] = $user_info['name'];
      $update_user_array[$ocertify->auth_user]['time'] = date('Y-m-d H:i:s',time()); 
      $orders_site_update_str = serialize($update_user_array);
    }else{
      $update_user_array[$ocertify->auth_user]['user'] = $user_info['name'];
      $update_user_array[$ocertify->auth_user]['time'] = date('Y-m-d H:i:s',time()); 
      $orders_site_update_str = serialize($update_user_array); 
    }
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_site_str."',configuration_description='".$orders_site_update_str."',user_update='".$_SESSION['user_name']."',last_modified='".date('Y-m-d H:i:s',time())."'  where configuration_key='PERSONAL_SETTING_ORDERS_SITE'");
    $orders_work_temp_array = array();
    $orders_work_setting_str = implode('|',$orders_work);
    if(PERSONAL_SETTING_ORDERS_WORK == ''){
      $orders_work_temp_array = array($user_info['name']=>$orders_work_setting_str);
    }else{
      $orders_work_setting_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
      $orders_work_setting_array[$user_info['name']] = $orders_work_setting_str;      
      $orders_work_temp_array = $orders_work_setting_array;
    }
    $orders_work_str = serialize($orders_work_temp_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_work_str."' where configuration_key='PERSONAL_SETTING_ORDERS_WORK'");
    if($orders_sort_list == '' && $orders_sort == ''){
      $orders_sort_str = ''; 
    }else{
      $orders_sort_temp_array = array();
      $orders_sort_setting_str = $orders_sort_list.'|'.$orders_sort;
      if(PERSONAL_SETTING_ORDERS_SORT == ''){
        $orders_sort_temp_array = array($user_info['name']=>$orders_sort_setting_str);
      }else{
        $orders_sort_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SORT);
        $orders_sort_setting_array[$user_info['name']] = $orders_sort_setting_str;      
        $orders_sort_temp_array = $orders_sort_setting_array;
      }
      $orders_sort_str = serialize($orders_sort_temp_array); 
    }
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_sort_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SORT'");
    $preorders_site_temp_array = array();
    $preorders_site_setting_str = implode('|',$preorders_site);
    if(PERSONAL_SETTING_PREORDERS_SITE == ''){
      $preorders_site_temp_array = array($user_info['name']=>$preorders_site_setting_str);
    }else{
      $preorders_site_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
      $preorders_site_setting_array[$user_info['name']] = $preorders_site_setting_str;      
      $preorders_site_temp_array = $preorders_site_setting_array;
    }
    $preorders_site_str = serialize($preorders_site_temp_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_site_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SITE'");
    $preorders_work_temp_array = array();
    $preorders_work_setting_str = implode('|',$preorders_work);
    if(PERSONAL_SETTING_PREORDERS_WORK == ''){
      $preorders_work_temp_array = array($user_info['name']=>$preorders_work_setting_str);
    }else{
      $preorders_work_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_WORK);
      $preorders_work_setting_array[$user_info['name']] = $preorders_work_setting_str;      
      $preorders_work_temp_array = $preorders_work_setting_array;
    }
    $preorders_work_str = serialize($preorders_work_temp_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_work_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_WORK'");
    if($preorders_sort_list == '' && $preorders_sort == ''){
      $preorders_sort_str = ''; 
    }else{
      $preorders_sort_temp_array = array();
      $preorders_sort_setting_str = $preorders_sort_list.'|'.$preorders_sort;
      if(PERSONAL_SETTING_PREORDERS_SORT == ''){
        $preorders_sort_temp_array = array($user_info['name']=>$preorders_sort_setting_str);
      }else{
        $preorders_sort_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SORT);
        $preorders_sort_setting_array[$user_info['name']] = $preorders_sort_setting_str;      
        $preorders_sort_temp_array = $preorders_sort_setting_array;
      }
      $preorders_sort_str = serialize($preorders_sort_temp_array); 
    } 
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_sort_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SORT'"); 
    
    $personal_is_transaction_temp_array = array();
    if(PERSONAL_SETTING_TRANSACTION_FINISH == ''){
      $personal_is_transaction_temp_array = array($ocertify->auth_user=>$is_transaction);
    }else{
      $personal_is_transaction_array = unserialize(PERSONAL_SETTING_TRANSACTION_FINISH); 
      $personal_is_transaction_array[$ocertify->auth_user] = $is_transaction;
      $personal_is_transaction_temp_array = $personal_is_transaction_array;
    }
    $personal_is_transaction_str = serialize($personal_is_transaction_temp_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$personal_is_transaction_str."' where configuration_key='PERSONAL_SETTING_TRANSACTION_FINISH'");
    
    $messageStack->add_session(TEXT_ONE_TIME_CONFIG_SAVE, 'success');
    tep_redirect(tep_href_link(FILENAME_PERSONAL_SETTING,''));
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation --> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof -->
    </td></tr></table></td>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
<div class="compatible">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADER_TEXT_PERSONAL_SETTING; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <form name="personal_setting" method="post" action="<?php echo FILENAME_PERSONAL_SETTING;?>?action=update">
            <table border="0" width="100%" cellspacing="0" cellpadding="5">
              <tr><td>
              <b><?php echo TEXT_PERSONAL_SETTING_LANGUAGE_OPTION;?></b> 
              <?php
               if(PERSONAL_SETTING_LANGUAGE == ''){
                 $language_flag = 'jp';
               }else{
                 $personal_language_array = unserialize(PERSONAL_SETTING_LANGUAGE);
                 if(array_key_exists($ocertify->auth_user,$personal_language_array)){
                   $language_flag = $personal_language_array[$ocertify->auth_user]; 
                 }else{
                   $language_flag = 'jp'; 
                 }
               }
              ?>
              <select name="personal_language">
              <option value="jp"<?php echo $language_flag == 'jp' ? 'selected' : '';?>>JP</option>
              <option value="ch"<?php echo $language_flag == 'ch' ? 'selected' : '';?>>CH</option>
              </select>
              </td></tr>
              <tr><td><b><?php echo TEXT_ORDERS_SETTING;?></b></td></tr>
              <tr>
              <?php
               $orders_site_array = array();
               foreach(tep_get_sites() as $site_value){
                  $orders_site_array[$site_value['id']] = $site_value['romaji'];               
               }
               if(!isset($_POST['orders_site']) && !isset($_GET['action'])){
                 $orders_site_default = implode('|',array_keys($orders_site_array)); 
                 if(PERSONAL_SETTING_ORDERS_SITE != ''){
                   $orders_site_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
                   if(array_key_exists($user_info['name'],$orders_site_setting_array)){

                     $orders_site_str = $orders_site_setting_array[$user_info['name']]; 
                   }else{
                     $orders_site_str = $orders_site_default; 
                   }
                 }else{
                   $orders_site_str = $orders_site_default;
                 }
               }else{
                 $orders_site_str = implode('|',$_POST['orders_site']); 
               }
               $site_array = explode('|',$orders_site_str); 
              ?>
               <td>
               <?php
                 $checked = ''; 
                 foreach($orders_site_array as $key=>$value){ 
                   $checked = in_array($key,$site_array) ? ' checked="checked"' : ''; 
                   echo '<input type="checkbox" name="orders_site[]" value="'.$key.'"'.$checked.'>'.$value.'&nbsp;';
                   $checked = '';
                 }
                 if(isset($orders_site_error) && $orders_site_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$orders_site_error.'</font>'; 
                 }
               ?>
               </td> 
              </tr>
              <tr>
              <?php
                 $orders_work_default = '0|1|2|3|4';
                 if(!isset($_POST['orders_work']) && !isset($_GET['action'])){
                   if(PERSONAL_SETTING_ORDERS_WORK != ''){
                     $orders_work_setting_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
                     if(array_key_exists($user_info['name'],$orders_work_setting_array)){

                       $orders_work_str = $orders_work_setting_array[$user_info['name']];
                     }else{
                       $orders_work_str = $orders_work_default; 
                     }
                   }else{
                     $orders_work_str = $orders_work_default; 
                   }
                 }else{
                   $orders_work_str = implode('|',$_POST['orders_work']); 
                 }
                 if($orders_work_str != ''){
                   $work_array = explode('|',$orders_work_str); 
                 }
                 $orders_work_array = array(
                   "0"=>"&nbsp;",
                   "1"=>"A",
                   "2"=>"B",
                   "3"=>"C",
                   "4"=>"D"                                             
                 );
              ?>
               <td>
               <?php
                 $checked = '';
                 foreach($orders_work_array as $work_key=>$work_value){
                   $checked = in_array($work_key,$work_array) ? ' checked="checked"' : '';
                   echo '<input type="checkbox" name="orders_work[]" value="'.$work_key.'"'.$checked.'>'.$work_value.'&nbsp;'; 
                 }
                 if(isset($orders_work_error) && $orders_work_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$orders_work_error.'</font>'; 
                 } 
               ?>
               </td> 
              </tr> 
              <tr>
               <td>
               <?php
                 if(PERSONAL_SETTING_ORDERS_SORT != ''){
                   $orders_sort_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SORT);
                   if(array_key_exists($user_info['name'],$orders_sort_setting_array)){
                     $orders_sort_str = $orders_sort_setting_array[$user_info['name']];
                   }else{
                     $orders_sort_str = ''; 
                   }
                 }else{
                   $orders_sort_str = '';
                 }
                 $sort_list = '';
                 $sort = '';
                 if(isset($_POST['orders_sort_list']) && isset($_POST['orders_sort'])){
                   $sort_list = $_POST['orders_sort_list'];
                   $sort = $_POST['orders_sort'];
                 }else{
                   if($orders_sort_str != ''){
                     $sort_array = explode('|',$orders_sort_str);
                     $sort_list = $sort_array[0];
                     $sort = $sort_array[1]; 
                   }
                 }
               ?>
               <select name="orders_sort_list">
                 <option value="">--</option>
                 <option value="0"<?php echo $sort_list == '0' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_SITE;?></option>
                 <option value="1"<?php echo $sort_list == '1' ? ' selected' : '';?>><?php echo TEXT_PREORDERS_SELECT_CUSTOMER;?></option>
                 <option value="2"<?php echo $sort_list == '2' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_PRICE?></option>
                 <option value="3"<?php echo $sort_list == '3' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_TIME;?></option>
                 <option value="4"<?php echo $sort_list == '4' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_DATE?></option> 
                 <option value="5"<?php echo $sort_list == '5' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_STATUS;?></option>
               </select>
               <select name="orders_sort">
                 <option value="">--</option>
                 <option value="0"<?php echo $sort == '0' ? ' selected' : '';?>><?php echo TEXT_SELECT_ASC;?></option>
                 <option value="1"<?php echo $sort == '1' ? ' selected' : '';?>><?php echo TEXT_SELECT_DESC?></option>
               </select>
               <?php
               if (PERSONAL_SETTING_TRANSACTION_FINISH != '') {
                 $personal_is_transaction_array = unserialize(PERSONAL_SETTING_TRANSACTION_FINISH);
                 if(array_key_exists($ocertify->auth_user, $personal_is_transaction_array)){
                   $is_transaction = $personal_is_transaction_array[$ocertify->auth_user]; 
                 }else{
                   $is_transaction = '0'; 
                 }
               } else {
                 $is_transaction = '0'; 
               }
               if (isset($_POST['is_transaction'])) {
                 $is_transaction = $_POST['is_transaction']; 
               }
               ?>
               <select name="is_transaction">
                 <option value="0"<?php echo $is_transaction == '0' ? ' selected' : '';?>><?php echo TEXT_PERSONAL_SETTING_HIDE_TRANSACTION_FINISH;?></option> 
                 <option value="1"<?php echo $is_transaction == '1' ? ' selected' : '';?>><?php echo TEXT_PERSONAL_SETTING_SHOW_TRANSACTION_FINISH;?></option> 
               </select>
               <?php
                 if(isset($orders_sort_error) && $orders_sort_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$orders_sort_error.'</font>'; 
                 }
               ?><br><br>
               </td>
              </tr>
              <tr><td><b><?php echo TEXT_PREORDERS_SETTING;?></b></td></tr>
              <tr>
              <?php  
               if(!isset($_POST['preorders_site']) && !isset($_GET['action'])){
                 $preorders_site_default = $orders_site_default; 
                 if(PERSONAL_SETTING_PREORDERS_SITE != ''){
                   $preorders_site_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
                   if(array_key_exists($user_info['name'],$preorders_site_setting_array)){

                     $preorders_site_str = $preorders_site_setting_array[$user_info['name']]; 
                   }else{
                     $preorders_site_str = $preorders_site_default; 
                   }
                 }else{
                   $preorders_site_str = $preorders_site_default;
                 } 
               }else{
                 $preorders_site_str = implode('|',$_POST['preorders_site']); 
               }
                 $preorders_site_array = explode('|',$preorders_site_str); 
              ?>
               <td>
               <?php
                 $checked = ''; 
                 foreach($orders_site_array as $key=>$value){ 
                   $checked = in_array($key,$preorders_site_array) ? ' checked="checked"' : ''; 
                   echo '<input type="checkbox" name="preorders_site[]" value="'.$key.'"'.$checked.'>'.$value.'&nbsp;';
                   $checked = '';
                 }
                 if(isset($preorders_site_error) && $preorders_site_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$preorders_site_error.'</font>'; 
                 }
               ?>
               </td> 
              </tr>
              <tr>
              <?php
                 $preorders_work_default = '0|1|2|3|4'; 
                 if(!isset($_POST['preorders_work']) && !isset($_GET['action'])){
                   if(PERSONAL_SETTING_PREORDERS_WORK != ''){
                     $preorders_work_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_WORK);
                     if(array_key_exists($user_info['name'],$preorders_work_setting_array)){

                       $preorders_work_str = $preorders_work_setting_array[$user_info['name']];
                     }else{
                       $preorders_work_str = $preorders_work_default; 
                     }
                   }else{
                     $preorders_work_str = $preorders_work_default; 
                   } 
                 }else{
                   $preorders_work_str = implode('|',$_POST['preorders_work']); 
                 }
                 if($preorders_work_str != ''){
                   $preorders_work_array = explode('|',$preorders_work_str);  
                 }
              ?>
               <td>
               <?php
                 $checked = '';
                 foreach($orders_work_array as $work_key=>$work_value){
                   $checked = in_array($work_key,$preorders_work_array) ? ' checked="checked"' : '';
                   echo '<input type="checkbox" name="preorders_work[]" value="'.$work_key.'"'.$checked.'>'.$work_value.'&nbsp;'; 
                 }
                 if(isset($preorders_work_error) && $preorders_work_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$preorders_work_error.'</font>'; 
                 } 
               ?>
               </td> 
              </tr> 
              <tr>
               <td>
               <?php
                 if(PERSONAL_SETTING_PREORDERS_SORT != ''){
                   $preorders_sort_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SORT);
                   if(array_key_exists($user_info['name'],$preorders_sort_setting_array)){
                     $preorders_sort_str = $preorders_sort_setting_array[$user_info['name']];
                   }else{
                     $preorders_sort_str = ''; 
                   }
                 }else{
                   $preorders_sort_str = '';
                 }
                 $preorders_sort_list = '';
                 $preorders_sort = '';
                 if(isset($_POST['preorders_sort_list']) && isset($_POST['preorders_sort'])){
                   $preorders_sort_list = $_POST['preorders_sort_list'];
                   $preorders_sort = $_POST['preorders_sort'];
                 }else{
                   if($preorders_sort_str != ''){
                     $preorders_sort_array = explode('|',$preorders_sort_str);
                     $preorders_sort_list = $preorders_sort_array[0];
                     $preorders_sort = $preorders_sort_array[1]; 
                   }
                 }
               ?>
               <select name="preorders_sort_list">
                 <option value="">--
                 <option value="0"<?php echo $preorders_sort_list == '0' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_SITE;?>
                 <option value="1"<?php echo $preorders_sort_list == '1' ? ' selected' : '';?>><?php echo TEXT_PREORDERS_SELECT_CUSTOMER;?>
                 <option value="2"<?php echo $preorders_sort_list == '2' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_PRICE?>
                 <option value="3"<?php echo $preorders_sort_list == '3' ? ' selected' : '';?>><?php echo TEXT_PREORDERS_SELECT_DATE?> 
                 <option value="4"<?php echo $preorders_sort_list == '4' ? ' selected' : '';?>><?php echo TEXT_ORDERS_SELECT_STATUS;?>
               </select>
               <select name="preorders_sort">
                 <option value="">--
                 <option value="0"<?php echo $preorders_sort == '0' ? ' selected' : '';?>><?php echo TEXT_SELECT_ASC;?>
                 <option value="1"<?php echo $preorders_sort == '1' ? ' selected' : '';?>><?php echo TEXT_SELECT_DESC?>
               </select>
               <?php
                 if(isset($preorders_sort_error) && $preorders_sort_error != ''){
                   echo '&nbsp;<font color="#FF0000">'.$preorders_sort_error.'</font>'; 
                 }
               ?>
               </td>
              </tr>
              <tr><td align="right"><input type="submit" value="<?php echo TEXT_SAVE;?>"></td></tr>
              <?php 
               //读取当前用户的更新日期
               $configuration = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where  configuration_key='PERSONAL_SETTING_ORDERS_SITE'"));  
               $create_users = TEXT_UNSET_DATA;
               $create_time = TEXT_UNSET_DATA;
               $update_users = TEXT_UNSET_DATA;
               $update_time = TEXT_UNSET_DATA;
               if($configuration['configuration_description'] != ''){
                 $update_users_array = unserialize($configuration['configuration_description']);
                 if(array_key_exists($ocertify->auth_user,$update_users_array)){

                   if(isset($update_users_array[$ocertify->auth_user]['user']) && $update_users_array[$ocertify->auth_user]['user'] != ''){
                     $update_users = $update_users_array[$ocertify->auth_user]['user'];
                   }
                   if(isset($update_users_array[$ocertify->auth_user]['time']) && $update_users_array[$ocertify->auth_user]['time'] != ''){
                     $update_time = $update_users_array[$ocertify->auth_user]['time'];
                   }
                   if(isset($update_users_array[$ocertify->auth_user]['create_users']) && $update_users_array[$ocertify->auth_user]['create_users'] != ''){
                     $create_users = $update_users_array[$ocertify->auth_user]['create_users'];
                   }
                   if(isset($update_users_array[$ocertify->auth_user]['create_time']) && $update_users_array[$ocertify->auth_user]['create_time'] != ''){
                     $create_time = $update_users_array[$ocertify->auth_user]['create_time']; 
                   }
                 }
               }
               echo '<tr><td>'.TEXT_USER_ADDED.'&nbsp;'.$create_users.'</td></tr>';
               echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.$create_time.'</td></tr>';
               echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.$update_users.'</td></tr>';
               echo '<tr><td>'.TEXT_DATE_UPDATE.'&nbsp;'.$update_time.'</td></tr>';
              ?>
</table>
</form>
</td></tr></table></td></tr>
</table>
</div></div></td>
</tr>
</table></td>
<!-- body_text_eof -->
</tr>
</table>

<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
