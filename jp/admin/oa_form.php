<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'update':
        tep_db_query("update `".TABLE_OA_FORM."` set `option` = '".tep_db_prepare_input($_POST['option'])."' where id = '".$_GET['form_id']."'");  
        tep_redirect(tep_href_link(FILENAME_MODULES, 'set=payment')); 
        break;
      case 'del_link_group':
        tep_db_query("delete from ".TABLE_OA_FORM_GROUP." where form_id = '".$_GET['fid']."' and group_id = '".$_GET['gid']."'"); 
        tep_redirect(tep_href_link(FILENAME_OA_FORM, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'])); 
        break;
    }
  }

  $oaform_exists_raw = tep_db_query("select * from ".TABLE_OA_FORM." where payment_romaji = '".$_GET['pcode']."' and formtype = '".$_GET['type']."'"); 
  $oaform_exists_res = tep_db_fetch_array($oaform_exists_raw);
  
  $form_option = '';
  if ($oaform_exists_res) {
    $form_id = $oaform_exists_res['id'];
    $form_option = $oaform_exists_res['option'];
  } else {
    tep_db_query("insert into `".TABLE_OA_FORM."` values(NULL, '".$_GET['pcode']."', '".$_GET['type']."', '')"); 
    $form_id = tep_db_insert_id(); 
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<h1>テンプレート管理</h1>
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
        <div style="display:none;"> 
        <?php echo tep_draw_form('form', FILENAME_OA_FORM, 'type='.$_GET['type'].'&action=update&form_id='.$form_id);?> 
        <table> 
        <?php echo FORM_OPTION;?><?php echo tep_draw_textarea_field('option', 'soft', '70', '15', $form_option);?> 
        <input type="submit" value="<?php echo IMAGE_SAVE;?>"> 
        </table> 
        </form> 
        </div> 
      </td>
    </tr>
    <tr>
      <td>
        <?php
          $form_group_raw = tep_db_query("select ofg.id ofgid,g.id, g.name from ".TABLE_OA_GROUP." g, ".TABLE_OA_FORM_GROUP." ofg where g.id = ofg.group_id and ofg.form_id = '".$form_id."' order by ofg.ordernumber"); 
        ?>
        <a href="<?php echo tep_href_link(FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type']);?>"><?php echo ADD_GROUP;?></a> 



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
  data: "id="+id+"&order="+order+"&action=updateoaorder&random="+ new Date().getTime(),
  success: function(){
    $(this).addClass("done");
  }
});
}
</script>

        <table border="1" style="clear:both;"> 
          <tr>
            <td><?php echo GROUP_NAME;?></td> 
            <td><?php echo GROUP_OPERATE;?></td> 
            <td><?php echo GROUP_ORDER;?></td> 
          </tr>
        <?php
            $order = 1;
          while ($form_group_res = tep_db_fetch_array($form_group_raw)) {
            echo '<tr id ="o'.$order.'" ` class="'.$form_group_res['ofgid'].'">'; 
            $order +=1;
            echo '<td>'.$form_group_res['name'].'</td>'; 
            echo '<td>'; 
            echo '<a href="'.tep_href_link(FILENAME_OA_GROUP, 'action=edit&gid='.$form_group_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.GROUP_EDIT.'</a>'; 
            echo '&nbsp;<a onclick="return confirm(\''.$form_group_res['name'].'を削除しますか？\')" href="'.tep_href_link(FILENAME_OA_FORM, 'action=del_link_group&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&gid='.$form_group_res['id']).'&fid='.$form_id.'">'.DEL_LINK_GROUP.'</a>';
            echo '<td>';
            echo '<input type="button" class="up" value="↑" onclick="editorder(this)">';
            echo '<input type="button" class="down" value="↓" onclick="editorder(this)">';
            echo '</td>';
            echo '</td>'; 
            echo '</tr>'; 
          }
        ?>
        </table> 
<input onclick='window.location.href("<?php echo tep_href_link(FILENAME_MODULES, 'set=payment&module='.$_GET['pcode']);?>")' type="button" value="<?php echo IMAGE_BACK;?>">
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
