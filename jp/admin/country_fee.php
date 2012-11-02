<?php
/*
 * 料金設定
 */
require('includes/application_top.php');

$action = $_GET['action'];

if(isset($action) && $action != ''){

  switch($action){

  case 'save':
    $country_fee_id = tep_db_prepare_input($_POST['cid']);
    $country_fee_title = tep_db_prepare_input($_POST['title']); $country_fee_name = tep_db_prepare_input($_POST['name']); $free_value = tep_db_prepare_input($_POST['free_value']);
    $weight_fee_name = tep_db_prepare_input($_POST['weight_fee_name']);
    $weight_fee = tep_db_prepare_input($_POST['weight_fee']);
    $weight_limit = tep_db_prepare_input($_POST['weight_limit']);
    $email_comment= tep_db_prepare_input($_POST['email_comment']);
    $email_comment_1 = tep_db_prepare_input($_POST['email_comment_1']);
        
    $sql_str = '';
    $sql_array = array();

    foreach($weight_fee_name as $weight_key=>$weight_value){

      $sql_array[$weight_value] = $weight_fee[$weight_key]; 
    }

    $sql_str = serialize($sql_array); 
    //这里判断是添加，还是修改
    if($country_fee_id == ''){

       $country_fee_sql = "insert into ". TABLE_COUNTRY_FEE .
                   " values(NULL,".
                   "'". $country_fee_title .
                   "','". $country_fee_name .
                   "','". $free_value .
                   "','". addslashes($sql_str) .
                   "','". $weight_limit .
                   "','". $email_comment .
                   "','". $email_comment_1 .
                  "','0','".$ocertify->auth_user."','".date('Y-m-d
                       H:i:s',time())."','".$ocertify->auth_user."','".date('Y-m-d
                       H:i:s',time())."')";

    }else{
      $country_fee_sql = "update ". TABLE_COUNTRY_FEE .
                   " set ".
                   "title='". $country_fee_title .
                   "',name='". $country_fee_name .
                   "',free_value='". $free_value .
                   "',weight_fee='". addslashes($sql_str) .
                   "',weight_limit='". $weight_limit .
                   "',email_comment='". $email_comment .
                   "',email_comment_1='". $email_comment_1 .
                   "',user_update='".$ocertify->auth_user.
                   "',date_update='".date('Y-m-d H:i:s',time()).
                   "' where id=". $country_fee_id;
    }
    
    $country_fee_query = tep_db_query($country_fee_sql);

    if($country_fee_query == true){
      
      tep_db_free_result($country_fee_query);
      tep_db_close();
      header("location:country_fee.php");

    }

    break;
  case 'del':
    if(isset($_GET['id']) && $_GET['id']){
      $country_id = $_GET['id'];
      $country_sql = "update ". TABLE_COUNTRY_FEE .
                   " set status='1' where id=".$country_id;
      $country_del_query = tep_db_query($country_sql);

      if($country_del_query == true){
      
        tep_db_free_result($country_del_query);
        tep_db_close();
        header("location:country_fee.php");
      }

    }else{
      $country_id = $_POST['cid'];
      $country_sql = "delete from ". TABLE_COUNTRY_FEE .
                   " where id=".$country_id;
      $country_del_query = tep_db_query($country_sql);
      $country_city_query = tep_db_query("select id from ". TABLE_COUNTRY_AREA ." where fid=$country_id"); 
      while($country_city_array = tep_db_fetch_array($country_city_query)){

        tep_db_query("delete from ". TABLE_COUNTRY_CITY ." where fid={$country_city_array['id']}");
      }
      tep_db_free_result($country_city_query); 
      tep_db_query("delete from ". TABLE_COUNTRY_AREA ." where fid=$country_id");

      if($country_del_query == true){
      
        tep_db_free_result($country_del_query);
        tep_db_close();
        header("location:country_fee.php");
      }
   }
    break;
  case 'res':

    $country_id = $_GET['id'];
    $country_sql = "update ". TABLE_COUNTRY_FEE .
                   " set status='0' where id=".$country_id;
    $country_del_query = tep_db_query($country_sql);

    if($country_del_query == true){
      
      tep_db_free_result($country_del_query);
      tep_db_close();
      header("location:country_fee.php");
    }
    break;

  }  
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
div#show {
  left:18%;
  width:70%;
  position:absolute;
}
</style>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
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
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation //--> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof //-->
    </td></tr></table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><div id="show" style="display:none"></div><table border="0" width="100%" cellspacing="0" cellpadding="2" id="group_list_box">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_1; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_2; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_3; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_4; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_TITLE_6; ?></td>
              </tr>
<?php
$even = 'dataTableSecondRow';
$odd  = 'dataTableRow';
$select_class = 'dataTableRowSelected';
$country_fee_sql = "select * from ". TABLE_COUNTRY_FEE;

$country_fee_page = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $country_fee_sql, $country_fee_query_numrows);
$country_fee_query = tep_db_query($country_fee_sql);
$i = 0;
while($country_fee_array = tep_db_fetch_array($country_fee_query)){
  if((int)$_GET['id']  == $country_fee_array['id'] || ($i == 0 && !isset($_GET['id']))){
    $nowColor = $select_class;
    $onmouseover = 'onmouseover="this.className=\'dataTableRowSelected\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$select_class.'\'"';
  }else{
    $nowColor = $i % 2 == 1 ? $even : $odd;
    $onmouseover = 'onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
  }
  
  $country_fee_option_array = unserialize($country_fee_array['weight_fee']);

  $fee = current($country_fee_option_array);
  echo '<tr class="'.$nowColor.'" '. $onmouseover .'>' . "\n";
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $country_fee_array['id'] .'\'"><a href="country_area.php?fid='. $country_fee_array['id'] .'"><img border="0" title="'.TEXT_FOLDER.'" alt="'.TEXT_FOLDER.'" src="images/icons/folder.gif"></a>&nbsp;&nbsp;'.$country_fee_array['title'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $country_fee_array['id'] .'\'">'.$country_fee_array['name'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $country_fee_array['id'] .'\'">'. $fee .'</td>';
  echo '<td>';
  if($country_fee_array['status'] == 0){
?>
  <img border="0" src="images/icon_status_green.gif" alt="" title="<?php echo TEXT_ENABLE;?>">
  <a title="<?php echo TEXT_DISABLE;?>" onclick="if(confirm('<?php echo TEXT_WANT_DISABLE;?>')){check_on_fee('del',<?php echo $country_fee_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_red_light.gif"></a>

<?php
}else{
?>
<a title="<?php echo TEXT_ENABLE;?>" onclick="if(confirm('<?php echo TEXT_WANT_ENABLE;?>')){check_on_fee('res',<?php echo $country_fee_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_green_light.gif"></a>
<img border="0" alt="" src="images/icon_status_red.gif" title="<?php echo TEXT_DISABLE;?>">

<?php
}
?>
</td>
<?php
  echo '<td align="right"><a href="javascript:void(0);" onclick="show_text_fee('. $country_fee_array['id'] .',this);">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a></td>';
  echo '</tr>';
  $i++;
}

tep_db_free_result($country_fee_query);
tep_db_close();
?>
<tr>
<td colspan="4">
<?php echo $country_fee_page->display_count($country_fee_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRY_FEE); ?>
</td>
<td colspan="5" align="right">
<?php echo $country_fee_page->display_links($country_fee_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page'))); ?>
</td>
</tr>
<tr><td align="right" colspan="9"><button onclick="show_text_fee(0,this);"><?php echo TABLE_BUTTON;?></button></td></tr>
</table></td></tr></table></td></tr>
</table></td>
</tr>
</table>
</div>
</div>
</td>
<!-- body_text_eof //-->
</tr>
</table>

<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
