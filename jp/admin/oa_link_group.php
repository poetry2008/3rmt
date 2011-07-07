<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
    case 'checkname':
      $oa_group =  tep_db_query('select count(*) cnt from '.TABLE_OA_GROUP.' where name="'.$_GET['name'].'"');
      //      die('select count(*) cnt from '.TABLE_OA_GROUP.' where name="'.$_GET['name'].'"');
      $oa_group_res = tep_db_fetch_array($oa_group); 
      echo  $oa_group_res['cnt'];
      die('');
      break;
      case 'insert':
        tep_db_query("insert into `".TABLE_OA_GROUP."` values(NULL, '".tep_db_prepare_input($_POST['gname'])."', '".tep_db_prepare_input($_POST['goption'])."', '0')"); 
        tep_redirect(tep_href_link(FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
      case 'update':
        tep_db_query("update `".TABLE_OA_GROUP."` set `name` = '".tep_db_prepare_input($_POST['gname'])."'  where id = '".$_GET['gid']."'");        
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
	   //           tep_db_query("delete from `".TABLE_OA_FORM_GROUP."` where `form_id` = '".$form_res['id']."'"); 
           foreach ($insert_group_arr as $ikey => $ivalue) {
             tep_db_query("insert into `".TABLE_OA_FORM_GROUP."` values(NULL, '".$form_res['id']."', '".$ivalue."',0)"); 
           }
         }
       }
       tep_redirect(tep_href_link(FILENAME_OA_LINK_GROUP,
             '&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&msg=success')); 
       break;
    }
  }
  
  if ($_GET['action'] == 'edit') {
    $oa_group_raw = tep_db_query("select * from ".TABLE_OA_GROUP." where id = '".$_GET['gid']."' order by id"); 
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
    <tr><td class="pageHeading" height="40">グループ管理</td></tr>
      <tr>
        <td>
          <?php
          if ($_GET['action'] == 'edit') { 
          ?>
<script type='text/javascript'>

    function editorder (ele){
    x = $(ele).parent().parent();      
    oid = x.attr('id').substr(1);
    oid = parseInt(oid);
    up = false;
    if ($(ele).attr('class') == 'up'){
      up  = true;
      oid -= 1;
    }else{
      oid += 1;
    }
    count = x.parent().children().length ;

    if (oid == 0 || oid >= count + 1)
      {

      }else {
      oid = 'o'+oid;
      if( up){
        x.insertBefore($("#"+oid));
      }else {
        x.insertAfter($("#"+oid));
      }

    }
    count = x.parent().children().each(
                                       function (e,key){
                                         if($(this).attr('id') && ($(this).attr('id')!='o'+e) ){
                                         $(this).attr('id','o'+e);
                                         ajaxUpdate($(this).attr('class'),$(this).attr('id'));
                                         }
                                       }
                                       );

  }
function ajaxUpdate(id,order){
  $.ajax({
  url: "oa_ajax.php",
  data: "id="+id+"&order="+order+"&action=updateitemorder&random="+ new Date().getTime(),
  async : false,
  success: function(){
    $(this).addClass("done");
  }
});
}
</script>
<input type="button" onclick="window.location.href='<?php echo tep_href_link(FILENAME_OA_ITEM,'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'])?>'" value="<?php echo BUTTON_ADD_ITEM_TEXT;?>"/>
          <table border="1" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td><?php echo TABLE_HEADING_ITEM_TITLE;?></td> 
              <td><?php echo TABLE_HEADING_ITEM_TYPE;?></td> 
              <td><?php echo TABLE_HEADING_OAGROUP_OPERATE;?></td> 
              <td><?php echo TABLE_HEADING_OAGROUP_ORDER;?></td> 
            </tr>
            <?php
               $has_item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where group_id = '".$_GET['gid']."' order by ordernumber"); 
               while ($has_item_res = tep_db_fetch_array($has_item_raw)) { 
            ?>
                 <tr class='<?php echo $has_item_res['id']; ?>' id = 'o<?php echo $has_item_res['ordernumber'];?>'>
              <td>
              <?php echo $has_item_res['title'];?> 
              </td>
              <td>
                 
              <?php 
                   require_once 'enableditem.php';
                 echo $enabled_item_array[ucfirst($has_item_res['type'])];
                 ?> 
              </td>
              <td>
              <a href="<?php echo tep_href_link(FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&eid='.$has_item_res['id'].'&return=oa_link&action=edit');?>"><?php echo EDIT_ITEM_LINK_TEXT;?></a> 
              <a onclick="return confirm('<?php echo $has_item_res['title'];?>を削除しますか?')" href="<?php echo tep_href_link(FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&eid='.$has_item_res['id'].'&action=del');?>"><?php echo DEL_ITEM_LINK_TEXT;?></a> 
              </td>
              <td><?php
            echo '<input type="button" class="up" value=\'↑\' onclick="editorder(this)">';
            echo '<input type="button" class="down" value=\'↓\' onclick="editorder(this)">';
?>
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
<script type='text/javascript'>

    function editorder (ele){
    x = $(ele).parent().parent();      
    oid = x.attr('id').substr(1);
    oid = parseInt(oid);
    up = false;
    if ($(ele).attr('class') == 'up'){
      up  = true;
      oid -= 1;
    }else{
      oid += 1;
    }
    count = x.parent().children().length ;

    if (oid == 0 || oid >= count + 1)
      {

      }else {
      oid = 'o'+oid;
      if( up){
        x.insertBefore($("#"+oid));
      }else {
        x.insertAfter($("#"+oid));
      }

    }
    count = x.parent().children().each(
                                       function (e,key){
                                         if($(this).attr('id') && ($(this).attr('id')!='o'+e) ){
                                         $(this).attr('id','o'+e);
                                         ajaxUpdate($(this).attr('class'),$(this).attr('id'));
                                         }
                                       }
                                       );

  }
function ajaxUpdate(id,order){
  $.ajax({
  url: "oa_ajax.php",
  data: "id="+id+"&order="+order+"&action=updategrouporder&random="+ new Date().getTime(),
  async : false,
  success: function(){
    $(this).addClass("done");
  }
});
}

</script>
           <?php
           if ($_GET['msg'] == 'success') {
             echo '追加完了'; 
           }
           ?>
           <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent">
              <input type="checkbox" name="allgroup" value="" onclick="select_all_group();"><?php echo OAGROUP_SELECT_ALL_TEXT;?> 
              </td>
              <td class="dataTableHeadingContent">
              <?php echo TABLE_HEADING_OAGROUP_NAME;?> 
              </td>
              <td class="dataTableHeadingContent">
              <?php echo TABLE_HEADING_OAGROUP_OPERATE;?> 
              </td>
              <td class="dataTableHeadingContent">
              <?php echo TABLE_HEADING_OAGROUP_ORDER;?> 
              </td>
            </tr>
<script type='text/javascript'>
	      $(document).ready(function(){
		  $(".oa_bg02").dblclick(doubleClickme);
		  $(".oa_bg").dblclick(doubleClickme);
          $(".oa_bg02").hover(function(){if(!$(this).hasClass('dataTableRowSelected')){$(this).removeClass('oa_bg02').addClass('dataTableRowOver')}},function(){ if(!$(this).hasClass('dataTableRowSelected')){$(this).removeClass('dataTableRowOver').addClass('oa_bg02')}});
          $(".oa_bg").hover(function(){if(!($(this).hasClass('dataTableRowSelected'))){$(this).removeClass('oa_bg').addClass('dataTableRowOver')}},function(){ if(!$(this).hasClass('dataTableRowSelected')){$(this).removeClass('dataTableRowOver').addClass('oa_bg')}});
		});



</script>
            <?php
               $form_has_query =  tep_db_query("select * from ".TABLE_OA_FORM." where
                   payment_romaji = '".$_GET['pcode']."' and formtype =
                   '".$_GET['type']."'");
               $form_has_row = tep_db_fetch_array($form_has_query);
               $oa_group_query = tep_db_query("select * from ".TABLE_OA_FORM_GROUP." where
                   form_id = '".$form_has_row['id']."' order  by id");
               $has_group_arr = array();
               while($oa_group_row = tep_db_fetch_array($oa_group_query)){
                 $has_group_arr[] = $oa_group_row['group_id'];
               }

              $g_num = 0; 
              $group_list_raw = tep_db_query("select * from ".TABLE_OA_GROUP." order by ordernumber"); 
              while ($group_list_res = tep_db_fetch_array($group_list_raw)) {
if ($g_num % 2 == 0) {
  $g_tr_class = 'oa_bg';
} else {
  $g_tr_class = 'oa_bg02';
}
                   if(in_array($group_list_res['id'],$has_group_arr)){
                     continue;
                   }
?>
                <tr class='<?php echo $group_list_res['id'].' '.$g_tr_class; ?>' id = 'o<?php echo $group_list_res['ordernumber'];?>'>
<?php
                $g_num++; 
               echo '<td><input  class="checkbox_ag" type="checkbox" name="ag[]" value="'.$group_list_res['id'].'"></td>'; 
                echo '<td>'.$group_list_res['name'].'</td>'; 
                echo '<td>';
                echo '<a href="'.tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.EDIT_GROUP_TEXT.'</a>'; 
                echo '&nbsp;&nbsp;'; 
                echo '<a onclick="return confirm(\''.$group_list_res['name'].'を削除しますか?\')"'.'href="'.tep_href_link(FILENAME_OA_GROUP, 'action=del&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.DEL_GROUP_TEXT.'</a>'; 
                echo '</td>'; 
                echo '<td>';
                echo '<input type="button" class="up" value=\'↑\' onclick="editorder(this)">';
                echo '<input type="button" class="down" value=\'↓\' onclick="editorder(this)">';
                echo '</td>'; 
                echo '</tr>'; 
              }
            ?>
            </table>
<script type='text/javascript'>

                function checkclicked(){

                var couldSubmit = false;
                $('.checkbox_ag').each(function(ele){
                    if(this.checked==true){
                      couldSubmit = true;
                    }
                  });
                if(!couldSubmit){
                  alert('選択されていないためテンプレートを追加できません。');
                }
                return couldSubmit;
              }
</script>
            <?php
              if (tep_db_num_rows($group_list_raw)) {
            ?>
                <input type="submit" onclick='return checkclicked()' value="<?php echo 'テンプレート追加';?>"> 
<input type="button" onclick="window.location.href='<?php echo tep_href_link(FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&return=special&type='.$_GET['type']);?>'" value="<?php echo ADD_GROUP;?>"/> 
            <?php
              }
            ?>
            </form> 
          <?php }?> 
        </td>
      </tr>
      <tr>
        <td>
  
        <input type="button" onclick='window.location.href="<?php echo tep_href_link(FILENAME_OA_FORM,'pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"' value="<?php echo IMAGE_BACK;?>">
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
