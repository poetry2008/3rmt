<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require_once('enableditem.php');
function prepareInsert()
{
        $na_list_arr = array(); 
        $va_list_arr = array(); 
        foreach ($_POST as $pokey => $povalue) {
          if (preg_match('/^(na_|va_)\d{1,}/', $pokey)) {
            $head_str = substr($pokey, 0, 3); 
            if ($head_str == 'na_') {
              $na_list_arr[] = $povalue;    
            } else {
              $va_list_arr[] = $povalue;    
            }
          }
        }
        $option_info_arr = array(); 
        foreach ($_POST  as $pskey => $psvalue) {
          if ((preg_match('/^(na_|va_)\d{1,}/', $pskey)) || ($pskey == 'ititle') || ($pskey == 'iname') || ($pskey == 'icomment') || ($pskey == 'itype')) {
            continue; 
          }
          if ($pskey == 'size') {
            $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',   '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9');
            $psvalue = strtr($psvalue, $arr); 
            $option_info_arr[$pskey] = (int)$psvalue; 
          } else {
            $option_info_arr[$pskey] = $psvalue; 
          }
        }
        $option_info_arr['mname'] = $na_list_arr; 
        $option_info_arr['mvalue'] = $va_list_arr; 
        $option_info_arr['form_id'] = $origin_form_res['id']; 
        //去掉 选项里的空值
        foreach($option_info_arr as $key=>$value){
          if (is_array($value)){
            $option_info_arr[$key] = array_filter($value ,'filter_trim_empty');
          }
        }
        $option_info_arr['eid'] = $_GET['eid'];        
        return $option_info_arr;
}
function filter_trim_empty($value){
  $value = trim($value);
  return !empty($value);
}
function insertItem()
{
         $option_info_arr = prepareInsert();
          tep_db_query("insert into `".TABLE_OA_ITEM."` values(NULL,
            '".$_GET['gid']."', '".tep_db_prepare_input($_POST['ititle'])."',
            '".tep_db_prepare_input(tep_get_random_item_name())."',
            '".tep_db_prepare_input($_POST['icomment'])."',
            '".tep_db_prepare_input(serialize($option_info_arr))."',
            '".tep_db_prepare_input(strtolower($_POST['itype']))."',".time().")"); 
          $item_id = tep_db_insert_id(); 
          $option_info_arr['eid'] = $item_id;        
          tep_db_query("update `".TABLE_OA_ITEM."` SET `option` = '".tep_db_prepare_input(serialize($option_info_arr))."' where `id` = '".$item_id."';"); 
}
function deleteItem()
{
  $item_info_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".$_GET['eid']."'"); 
  $item_info_res = tep_db_fetch_array($item_info_raw); 
  $type = ucfirst($item_info_res['type']);
  $class= 'HM_Item_'.$type;
  require_once "oa/".$class.'.php';
  //var_dump($class);
  if(method_exists($class,'deleteTrigger')){
    call_user_func(array($class,'deleteTrigger'),$_GET['eid'])     ;
  }
  tep_db_query("delete from ".TABLE_OA_ITEM." where id = '".$_GET['eid']."'"); 
  tep_db_query("delete from ".TABLE_OA_FORMVALUE." where item_id = '".$_GET['eid']."'"); 
}
function updateItem()
{
  
  $item_info_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".$_GET['eid']."'"); 
  $item_form_res = tep_db_fetch_array($item_info_raw); 
  if($item_form_res['type']!=$POST['itype']){

    deleteItem();
    insertItem();
  }else{
  $option_info_arr = prepareInsert();
  tep_db_query("update `".TABLE_OA_ITEM."` SET `title` = '".tep_db_prepare_input($_POST['ititle'])."', `comment` = '".tep_db_prepare_input($_POST['icomment'])."' ,`type` = '".tep_db_prepare_input(strtolower($_POST['itype']))."' , `option` = '".tep_db_prepare_input(serialize($option_info_arr))."' where `id` = '".$_GET['eid']."';");  
  }

}
$origin_form_raw = tep_db_query("select * from ".TABLE_OA_FORM." where payment_romaji = '".$_GET['pcode']."' and formtype = '".$_GET['type']."'"); 


  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'insert':
        insertItem();
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
      case 'update':
        updateItem();
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
      case 'del':
        deleteItem();
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
    }
  }
  $sel_type_str = 'Text'; 
  if ($_GET['action'] == 'edit') {
    $item_info_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".$_GET['eid']."'"); 
    $item_info_res = tep_db_fetch_array($item_info_raw); 
    foreach ($enabled_item_array as $takey => $tavalue) {
      if (strtolower($takey) == $item_info_res['type']) {
        $sel_type_str = $takey;
        break;
      }
    }
    
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script type="text/javascript">
<?php
if ($_GET['type'] == '4') {
?>
$(document).ready(function() {
  $.ajax({
    url: 'preorder_item_process.php',
    type: 'POST',
    <?php
    if ($_GET['action'] == 'edit') {
    ?>
    data: "type=<?php echo $sel_type_str;?>"+"&eid=<?php echo $_GET['eid']?>", 
    <?php
    } else {
    ?>
    data: "type=<?php echo $sel_type_str;?>", 
    <?php
    }
    ?>
    async : false,
    success: function(msg) {
      $('#show_option').html(msg); 
    }
  });
});
function change_item_type()
{
  $.ajax({
    url: 'preorder_item_process.php',
    type: 'POST',
    data: "type="+$('#itype').val(), 
    async : false,
    success: function(msg) {
      $('#show_option').html(msg); 
    }
  });
}
<?php
} else {
?>
$(document).ready(function() {
  $.ajax({
    url: 'item_process.php',
    type: 'POST',
    <?php
    if ($_GET['action'] == 'edit') {
    ?>
    data: "type=<?php echo $sel_type_str;?>"+"&eid=<?php echo $_GET['eid']?>", 
    <?php
    } else {
    ?>
    data: "type=<?php echo $sel_type_str;?>", 
    <?php
    }
    ?>
    async : false,
    success: function(msg) {
      $('#show_option').html(msg); 
    }
  });
});
function change_item_type()
{
  $.ajax({
    url: 'item_process.php',
    type: 'POST',
    data: "type="+$('#itype').val(), 
    async : false,
    success: function(msg) {
      $('#show_option').html(msg); 
    }
  });
}
<?php
}
?>
function add_option() {    
  var $table = $("#tab tr"); 
  var len = $table.length;
  $("#tab").append("<tr id="+(len+1)+"><td><input type=\"text\" name=\"na_"+(len+1)+"\"></td><td><input type=\"text\" name=\"va_"+(len+1)+"\"></td><td><a href=\"javascript:void(0);\" onclick=\"deltr('"+(len+1)+"')\"><?php echo DEL_TR_DATA;?></a></td></tr>");
}
function deltr(index)
{
  $table = $("#tab tr");
  $("tr[id=\'"+index+"\']").remove();
}
</script>
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
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
    	<td class="pageHeading" height="40"><?php echo TEXT_ITEM_MANAGE;?></td>
     </tr>
     <tr>
      <td>
        <?php 
        if ($_GET['action'] == 'edit') {
          echo tep_draw_form('form', FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=update&eid='.$_GET['eid']);
        } else {
          echo tep_draw_form('form', FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=insert');
        }
        ?> 
        <table border="0" width="100%" cellpadding="2" cellspacing="1" class="oa_item_bg">
          <tr>
            <td width="150">
            <?php echo OA_ITEM_TITLE_TEXT;?>
            </td>
            <td>
            <?php
            echo tep_draw_input_field('ititle', ((isset($item_info_res['title'])?$item_info_res['title']:'')), 'size="56"'); 
            ?>
            </td>
          </tr>
          <tr style="display:none;">
            <td>
            <?php echo OA_ITEM_NAME_TEXT;?> 
            </td>
            <td>
            <?php
            echo tep_draw_input_field('iname', ((isset($item_info_res['name'])?$item_info_res['name']:''))); 
            ?>
            </td>
          </tr>
          <tr style="display:none;">
            <td>
            <?php echo OA_ITEM_COMMENT_TEXT;?> 
            </td>
            <td>
            <?php
            echo tep_draw_textarea_field('icomment', 'soft', '30', '15', ((isset($item_info_res['comment'])?$item_info_res['comment']:''))); 
            ?>
            </td>
          </tr>
          <tr>
            <td>
            <?php echo OA_ITEM_TYPE_TEXT;?> 
            </td>
            <td>
            <select id="itype" name="itype" onchange="change_item_type();">
              <?php
                  foreach ($enabled_item_array as $tkey => $tvalue) {
                    $check_str = '';
                    if ((isset($item_info_res['type'])) && ($item_info_res['type'] == strtolower($tkey))) {
                             $check_str = ' selected'; 
                    }
                    echo '<option value="'.$tkey.'"'.$check_str.'>'.strtolower($tvalue).'</option>'; 
                  }
              ?>
            </select>
            </td>
          </tr>
          <tr>
            <td>
            <?php echo OA_ITEM_TYPE_OPTION_TEXT;?> 
            </td>
            <td>
            <div id="show_option">
            </div>
            <!--<a href="javascript:void(0);" onclick="add_option();"><?php echo ADD_OPTION_LINK;?></a>--> 
            <table id="tab">
            <?php
            if (isset($item_info_res['option'])) {
              $option_arr = @unserialize($item_info_res['option']); 
              if (isset($option_arr['mname']) && isset($option_arr['mvalue'])) {
                $o_num = 1; 
                foreach ($option_arr['mname'] as $mkey => $mvalue) {
                  echo '<tr id="'.$o_num.'"><td>'.tep_draw_input_field('na_'.$o_num, $mvalue).'</td><td>'.tep_draw_input_field('va_'.$o_num, $option_arr['mvalue'][$mkey]).'</td><td><a href="javascript:void(0);" onclick="deltr(\''.$o_num.'\')">'.DEL_TR_DATA.'</a></td></tr>';  
                  $o_num++; 
                }
              }
            }
            ?>
            </table>
            </td>
          </tr>
        </table>
        <input type="submit" value="<?php echo IMAGE_SAVE;?>">
<?php
  if($_GET['return']=='oa_link'){
?>

<input onclick = 'window.location.href=" <?php echo tep_href_link(FILENAME_OA_LINK_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"' type="button" value="<?php echo IMAGE_BACK?>">
<?php
  }else{
?>
<input onclick = 'window.location.href=" <?php echo tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"' type="button" value="<?php echo IMAGE_BACK?>">
<?php
      }?>
        </form> 
      </td>
    </tr>
    </table>
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
