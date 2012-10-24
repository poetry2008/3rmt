<?php
/*
 * 住所作成 action_ajax
 */
require('includes/application_top.php');

//生成随即16位name
function rand_str(){
  
  $rand_string = '';
  $rand_array = range('a','z');
  $rand_count = count($rand_array);
  for($rand_i = 0;$rand_i < 16;$rand_i++){

    $rand_string .= $rand_array[mt_rand(0,$rand_count-1)]; 
  }
  return $rand_string;
}
$action = $_GET['action'];

if(isset($action) && $action != ''){

  switch($action){ 
  case 'save': $address_id = tep_db_prepare_input($_POST['cid']);
    $address_title = tep_db_prepare_input($_POST['title']);
    $address_name = tep_db_prepare_input($_POST['name']);
    $address_type = tep_db_prepare_input($_POST['type']);
    $address_comment = tep_db_prepare_input($_POST['comment']);
    $address_option_comment = tep_db_prepare_input($_POST['option_comment']);
    $address_text_type = tep_db_prepare_input($_POST['text_type']);
    $address_limit = tep_db_prepare_input($_POST['limit']);
    $address_limit_min = tep_db_prepare_input($_POST['limit_min']);
    $address_required = tep_db_prepare_input($_POST['required']);
    $address_sort = tep_db_prepare_input($_POST['sort']);
    $address_sort = $address_sort == '' ? 0 : $address_sort;
    $address_option_value = tep_db_prepare_input($_POST['option_value']);
    $parent_option = tep_db_prepare_input($_POST['parent_option']);
    $show_title = tep_db_prepare_input($_POST['show_title']);
    //生成随即16位name

    

    //guolv $address_option_comment kongzhi
    //过滤 $address_option_comment 空值
    $array_temp = array();
    foreach($address_option_comment as $key=>$value){

      if(trim($value) != ''){
        
        $array_temp[$key] = $value;
      }
    }
    unset($address_option_comment);
    $address_option_comment = array();
    $address_option_comment = $array_temp;

    $sql_array = array();

    if($address_type == 'textarea'){
      
      $sql_array['rows'] = $address_option_comment[0];
      $sql_array['type_limit'] = $address_text_type;
      $sql_array['set_value'] = $address_option_comment[1];
     
    }elseif($address_type == 'option'){
      if($parent_option[0] == '0' || $parent_option[0] == ''){
        $sql_array = array('option_list'=>$address_option_comment,'select_value'=>$address_option_comment[$address_option_value]); 
      }else{ 
        if($address_id == ''){  
          $sql_array[$parent_option[1]] = array('parent_id'=>$parent_option[0],'parent_name'=>$parent_option[1],'option_list'=>$address_option_comment,'select_value'=>$address_option_comment[$address_option_value]);
        }else{
          $address_option_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=". $address_id);
          $address_option_row = tep_db_fetch_array($address_option_query);
          $address_option_array = unserialize($address_option_row['type_comment']);
          $address_option_array[$parent_option[1]] = array('parent_id'=>$parent_option[0],'parent_name'=>$parent_option[1],'option_list'=>$address_option_comment,'select_value'=>$address_option_comment[$address_option_value]);
        tep_db_free_result($address_option_query);
          $sql_array = $address_option_array;
        }
      }
    }else{

      $address_limit = 0;
      $address_required = 'false';
    }

    $sql_option_str = '';
    $sql_option_str = serialize($sql_array);

    $sql_str = '';
    if($address_limit != '' || $address_limit_min != ''){
      if($address_id == ''){
        $sql_str = ','.$address_limit;
        $sql_str .= ','.$address_limit_min;
      }else{
        $sql_str = ',num_limit='.$address_limit; 
        $sql_str .= ',num_limit_min='.$address_limit_min;
      }
    }else{
      if($address_id == ''){
        $sql_str .= ',0';
        $sql_str .= ',0';
      }else{
        $sql_str = ',num_limit=0'; 
        $sql_str .= ',num_limit_min=0';
      }

    }
    
    if($address_required != ''){
      if($address_id == ''){
        $sql_str .= ',\''.$address_required ."'";
 
      }else{
        $sql_str .= ',required=\''.$address_required ."'";
      }
    }else{
      if($address_id == ''){
        $sql_str .= ',\'true\'';
      }else{
        $sql_str .= ',required=\'true\'';
      }
 
    }
    

    //这里判断是添加，还是修改
    if($address_id == ''){

       $address_sql = "insert into ". TABLE_ADDRESS .
                   " values(NULL,".
                   "'". $address_title .
                   "','". $address_name .
                   "','". rand_str() .
                   "','". $address_comment .
                   "','". $address_type .
                   "','". addslashes($sql_option_str) .
                   "'". $sql_str .
                   ",". $address_sort .
                   ",'". $show_title .
                   "','0','0')";
    }else{
      $address_sql = "update ". TABLE_ADDRESS .
                   " set ".
                   "title='". $address_title .
                   "',name='". $address_name .
                   "',comment='". $address_comment .
                   "',type='". $address_type .
                   "',type_comment='". addslashes($sql_option_str) .
                   "'". $sql_str .
                   ",sort=". $address_sort .
                   ",show_title='". $show_title .
                   "' where id=". $address_id;
    }
    $address_update_query = tep_db_query($address_sql);

    if($address_update_query == true){
        
      tep_db_free_result($address_update_query);
      tep_db_close();
      header("location:address.php");
    }

    break;
  case 'del':
    if(isset($_GET['id']) && $_GET['id']){
      $address_id = $_GET['id'];
      $address_sql = "update ". TABLE_ADDRESS .
                   " set status='1' where id=".$address_id;
      $address_del_query = tep_db_query($address_sql);

      if($address_del_query == true){
      
        tep_db_free_result($address_del_query);
        tep_db_close();
        header("location:address.php");
      }

    }else{
      $address_id = $_POST['cid'];
      $address_sql = "delete from ". TABLE_ADDRESS .
        " where id=".$address_id;
      $address_del_query = tep_db_query($address_sql);

      if($address_del_query == true){
      
        tep_db_free_result($address_del_query);
        tep_db_close();
        header("location:address.php");
      }
   }
    break;
  case 'res':

    $address_id = $_GET['id'];
    $address_sql = "update ". TABLE_ADDRESS .
                   " set status='0' where id=".$address_id;
    $address_del_query = tep_db_query($address_sql);

    if($address_del_query == true){
      
      tep_db_free_result($address_del_query);
      tep_db_close();
      header("location:address.php");
    }
    break;

  }  
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<style type="text/css">
div#show {
  left:18%;
  width:70%;
  position:absolute;
}
</style>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
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
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation --> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof -->
    </td></tr></table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><?php echo $notes;?><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><div id="show" style="display:none"></div><table border="0" width="100%" cellspacing="0" cellpadding="2" id="group_list_box">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_1; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_2; ?></td>
                <td class="dataTableHeadingContent" width="30%"><?php echo TABLE_TITLE_3; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_4; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_5; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_6; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_7; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_8; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_TITLE_9; ?>&nbsp;</td>
              </tr>
<?php
$even = 'dataTableSecondRow';
$odd  = 'dataTableRow';
$select_class = 'dataTableRowSelected';
$address_sql = "select * from ". TABLE_ADDRESS ." order by sort,id asc";

$address_page = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $address_sql, $address_query_numrows);
$address_query = tep_db_query($address_sql);
$i = 0;
while($address_array = tep_db_fetch_array($address_query)){
  if((int)$_GET['id'] == $address_array['id'] || ($i == 0 && !isset($_GET['id']))){
    $nowColor = $select_class;
    $onmouseover = 'onmouseover="this.className=\'dataTableRowSelected\';this.style.cursor=\'hand\';" onmouseout="this.className=\''.$select_class.'\'"';
  }else{
    $nowColor = $i % 2 == 1 ? $even : $odd; 
    $onmouseover = 'onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\';" onmouseout="this.className=\''.$nowColor.'\'"'; 
  }
  switch($address_array['type']){

  case 'text':
    $address_type_str = TABLE_TEXT;
    break;
  case 'textarea':
    $address_type_str = TABLE_TEXTAREA;
    break;
  case 'option':
    $address_type_str = TABLE_SELECT;
    break;
  }
  //$status = $address_array['status'] == 0 ? '<font color="blue">'. TABLE_STATUS .'</font>' : '<font color="red">'. TABLE_STATUS .'</font>';
  
  //if($address_array['status'] == 0){

    //$status = '<a title="del" href="javascript:check(\'del\');"><img border="0" alt="" src="images/icon_status_blue.gif"></a>'; 
  //}
  echo '<tr class="'.$nowColor.'" '. $onmouseover .'>' . "\n";
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['title'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['name'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['comment'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_type_str.'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['num_limit'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['required'].'</td>';
  echo '<td onclick="document.location.href=\'?page='. $_GET['page'] .'&id='. $address_array['id'] .'\'">'.$address_array['sort'].'</td>';
  echo '<td >';
  if($address_array['status'] == 0){
?>
  <img border="0" src="images/icon_status_green.gif" alt="<?php echo TEXT_ENABLE;?>" title="<?php echo TEXT_ENABLE;?>">
  <a title="<?php echo TEXT_DISABLE;?>" onclick="if(confirm('<?php echo TEXT_WANT_DISABLE;?>')){check_on('del',<?php echo $address_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_red_light.gif"></a>

<?php
}else{
?>
<a title="<?php echo TEXT_ENABLE;?>" onclick="if(confirm('<?php echo TEXT_WANT_ENABLE;?>')){check_on('res',<?php echo $address_array['id'];?>);}else{return false;}" href="javascript:void(0);"><img border="0" alt="" src="images/icon_status_green_light.gif"></a>
<img border="0" alt="<?php echo TEXT_DISABLE;?>" src="images/icon_status_red.gif" title="<?php echo TEXT_DISABLE;?>">

<?php
}
?>
<?php
  echo '</td>';
  echo '<td><a href="javascript:void(0);" onclick="show_text('. $address_array['id'] .',this,\''. $address_array['type'] .'\',0);">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a></td>';
  echo '</tr>';
  $i++;
}

tep_db_free_result($address_query);
tep_db_close();
?>
<tr>
<td colspan="4">
<?php echo $address_page->display_count($address_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ADDRESS); ?>
</td>
<td colspan="5" align="right">
<?php echo $address_page->display_links($address_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page'))); ?>
</td>
</tr>
<tr><td align="right" colspan="9"><button onclick="show_text(0,this,'text',0);"><?php echo TABLE_BUTTON;?></button></td></tr>
</table></td></tr></table></td></tr>
</table></td>
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
