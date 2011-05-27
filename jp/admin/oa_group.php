<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'insert':
        tep_db_query("insert into `".TABLE_OA_GROUP."` values(NULL, '".tep_db_prepare_input($_POST['gname'])."', '".tep_db_prepare_input($_POST['goption'])."', '".(int)$_POST['gsort']."')"); 
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
      case 'update':
        tep_db_query("update `".TABLE_OA_GROUP."` set `name` = '".tep_db_prepare_input($_POST['gname'])."', `option` = '".tep_db_prepare_input($_POST['goption'])."', `sort` = '".(int)$_POST['gsort']."' where id = '".$_GET['gid']."'");        
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
       break;
      case 'del':
        tep_db_query("delete from `".TABLE_OA_GROUP."` where id = '".$_GET['gid']."'");  
        tep_db_query("delete from `".TABLE_OA_FORM_GROUP."` where group_id = '".$_GET['gid']."'"); 
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, '&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
       break;
      case 'link_group': 
       if (!empty($_POST['ag'])) {
         $form_raw = tep_db_query("select * from ".TABLE_OA_FORM." where payment_romaji = '".$_GET['pcode']."' and formtype = '".$_GET['type']."'");  
         $form_res = tep_db_fetch_array($form_raw); 
         
         if ($form_res) {
           $insert_group_arr = $_POST['ag'];
           tep_db_query("delete from `".TABLE_OA_FORM_GROUP."` where `form_id` = '".$form_res['id']."'"); 
           foreach ($insert_group_arr as $ikey => $ivalue) {
             tep_db_query("insert into `".TABLE_OA_FORM_GROUP."` values(NULL, '".$form_res['id']."', '".$ivalue."')"); 
           }
         }
       }
       tep_redirect(tep_href_link(FILENAME_OA_GROUP, '&pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
       break;
    }
  }
  
  if ($_GET['action'] == 'edit') {
    $oa_group_raw = tep_db_query("select * from ".TABLE_OA_GROUP." where id = '".$_GET['gid']."'"); 
    $oa_group_res = tep_db_fetch_array($oa_group_raw); 
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
function select_all_group()
{
   var chk_flag = document.selt_group.allgroup.checked;

   if (chk_flag == true) {
     for (var i = 0; i < document.selt_group.elements["ag[]"].length; i++) {
        document.selt_group.elements["ag[]"][i].checked = true;
     }
   } else {
     for (var i = 0; i < document.selt_group.elements["ag[]"].length; i++) {
        document.selt_group.elements["ag[]"][i].checked = false;
     }
   }
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
        <td>
          <?php
            if ($_GET['action'] == 'edit') {
              echo tep_draw_form('oagroup', FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=update&gid='.$_GET['gid']);          
            } else {
              echo tep_draw_form('oagroup', FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=insert');          
            }
          ?>
          <table>
            <tr>
              <td><?php echo OA_GROUP_NAME_TEXT;?></td> 
              <td>
              <?php
                echo tep_draw_input_field('gname', ((isset($oa_group_res['name'])?$oa_group_res['name']:''))); 
              ?>
              </td> 
            </tr>
            <tr style="display:none;">
              <td><?php echo OA_GROUP_OPTION_TEXT;?></td> 
              <td>
              <?php echo tep_draw_textarea_field('goption', 'soft', '70', '15', ((isset($oa_group_res['option'])?$oa_group_res['option']:'')));?> 
              </td> 
            </tr>
            <tr>
              <td><?php echo OA_GROUP_SORT_TEXT;?></td> 
              <td>
              <?php
                echo tep_draw_input_field('gsort', ((isset($oa_group_res['sort'])?$oa_group_res['sort']:''))); 
              ?>
              </td> 
            </tr>
            <tr>
              <td colspan="2">
              <input type="submit" value="<?php echo IMAGE_SAVE;?>"> 
              </td>
            </tr>
          </table>
          </form> 
        </td>
      </tr>
      <tr>
        <td>
          <?php
          if ($_GET['action'] == 'edit') { 
          ?>
          <a href="<?php echo tep_href_link(FILENAME_OA_ITEM,
            'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"><input type="button" value="<?php echo BUTTON_ADD_ITEM_TEXT;?>"></a> 
          <table border="1">
            <tr>
              <td><?php echo TABLE_HEADING_ITEM_TITLE;?></td> 
              <td><?php echo TABLE_HEADING_OAGROUP_OPERATE;?></td> 
            </tr>
            <?php
               $has_item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where group_id = '".$_GET['gid']."'"); 
               while ($has_item_res = tep_db_fetch_array($has_item_raw)) { 
            ?>
            <tr>
              <td>
              <?php echo $has_item_res['title'];?> 
              </td>
              <td>
              <a href="<?php echo tep_href_link(FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&eid='.$has_item_res['id'].'&action=edit');?>"><?php echo EDIT_ITEM_LINK_TEXT;?></a> 
              <a href="<?php echo tep_href_link(FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&eid='.$has_item_res['id'].'&action=del');?>"><?php echo DEL_ITEM_LINK_TEXT;?></a> 
              </td>
            </tr>
            <?php
            } 
            ?>
          </table>
          <?php
          } else { 
          ?>
          <?php echo tep_draw_form('selt_group', FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=link_group');?> 
          <table border="1">
            <tr>
              <td>
              <input type="checkbox" name="allgroup" value="" onclick="select_all_group();"><?php echo OAGROUP_SELECT_ALL_TEXT;?> 
              </td>
              <td>
              <?php echo TABLE_HEADING_OAGROUP_NAME;?> 
              </td>
              <td>
              <?php echo TABLE_HEADING_OAGROUP_OPERATE;?> 
              </td>
            </tr>
            <?php
              $group_list_raw = tep_db_query("select * from ".TABLE_OA_GROUP." order by sort"); 
              while ($group_list_res = tep_db_fetch_array($group_list_raw)) {
                echo '<tr>'; 
                echo '<td><input type="checkbox" name="ag[]" value="'.$group_list_res['id'].'"></td>'; 
                echo '<td>'.$group_list_res['name'].'</td>'; 
                echo '<td>';
                echo '<a href="'.tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.EDIT_GROUP_TEXT.'</a>'; 
                echo '&nbsp;&nbsp;'; 
                echo '<a href="'.tep_href_link(FILENAME_OA_GROUP, 'action=del&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.DEL_GROUP_TEXT.'</a>'; 
                echo '</td>'; 
                echo '</tr>'; 
              }
            ?>
            </table>
            <?php
              if (tep_db_num_rows($group_list_raw)) {
            ?>
                <input type="submit" value="<?php echo IMAGE_SAVE;?>"> 
            <?php
              }
            ?>
            </form> 
          <?php }?> 
        </td>
      </tr>
      <tr>
        <td>
        <a href="<?php echo tep_href_link(FILENAME_OA_FORM,'pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"><input type="button" value="<?php echo IMAGE_BACK;?>"></a> 
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
