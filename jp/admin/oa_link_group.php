<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'checkname' 检查名字是否唯一    
   case 'insert' 新建组    
   case 'update' 更新组    
   case 'del' 删除组    
   case 'link_group' 把组和表单关联    
------------------------------------------------------*/
    case 'checkname':
      $oa_group =  tep_db_query('select count(*) cnt from '.TABLE_OA_GROUP.' where name="'.$_GET['name'].'"');
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
<title><?php echo TEXT_GROUP_MANAGE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript">
<?php //全选/反选?>
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
<?php //执行动作?>
function toggle_oa_group_form(c_permission, l_url_str)
{
  if (c_permission == 31) {
    window.location.href = l_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = l_url_str; 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(l_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = l_url_str; 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      }
    });
  }
  return false;
}
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = str_replace('&','|||',$belong);
preg_match_all('/pcode=([^&]+)/',$belong,$pcode_array);
$belong_temp = $belong;
require("includes/note_js.php");
$belong = str_replace($pcode_array[1][0],urlencode($pcode_array[1][0]),$belong);
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
    <div class="compatible">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr><td class="pageHeading" height="40"><?php echo TEXT_GROUP_MANAGE;?></td></tr>
      <tr>
        <td>
          <?php
          if ($_GET['action'] == 'edit') { 
          ?>
<script type='text/javascript'>
    <?php //排序?>
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
<?php //更新序号值?>
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
              <?php
                if ($ocertify->npermission >= 15) {
              ?>
              <a onclick="if(confirm('<?php echo $has_item_res['title'].TEXT_DELETE_CONFRIM;?>')) return toggle_oa_group_form('<?php echo $ocertify->npermission?>', '<?php echo tep_href_link(FILENAME_OA_ITEM, 'gid='.$_GET['gid'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&eid='.$has_item_res['id'].'&action=del');?>');" href="javascript:void(0);"><?php echo DEL_ITEM_LINK_TEXT;?></a> 
              <?php }?> 
              </td>
              <td><?php
            echo '<input type="button" class="up" value=\''.TEXT_UP.'\' onclick="editorder(this)">';
            echo '<input type="button" class="down" value=\''.TEXT_DOWN.'\' onclick="editorder(this)">';
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
          <?php echo TEXT_SELECT_GROUP_READ;?> 
          <?php echo tep_draw_form('selt_group', FILENAME_OA_GROUP, 'pcode='.$_GET['pcode'].'&type='.$_GET['type'].'&action=link_group');?> 
<script type='text/javascript'>
    <?php //排序?>
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
<?php //更新序号值?>
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
             echo TEXT_ADD_FINISH; 
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
                echo '<a href="'.tep_href_link(FILENAME_OA_GROUP, 'action=edit&return=oa_link&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'">'.EDIT_GROUP_TEXT.'</a>'; 
                if ($ocertify->npermission >= 15) {
                  echo '&nbsp;&nbsp;'; 
                  echo '<a onclick="if (confirm(\''.$group_list_res['name'].TEXT_DELETE_CONFRIM.'\')) toggle_oa_group_form(\''.$ocertify->npermission.'\', \''.tep_href_link(FILENAME_OA_GROUP, 'action=del&gid='.$group_list_res['id'].'&pcode='.$_GET['pcode'].'&type='.$_GET['type']).'\')"'.'href="javascript:void(0);">'.DEL_GROUP_TEXT.'</a>'; 
                } 
                echo '</td>'; 
                echo '<td>';
                echo '<input type="button" class="up" value=\''.TEXT_UP.'\' onclick="editorder(this)">';
                echo '<input type="button" class="down" value=\''.TEXT_DOWN.'\' onclick="editorder(this)">';
                echo '</td>'; 
                echo '</tr>'; 
              }
            ?>
            </table>
<script type='text/javascript'>
                <?php //检查是否选择?>
                function checkclicked(c_permission){

                var couldSubmit = false;
                $('.checkbox_ag').each(function(ele){
                    if(this.checked==true){
                      couldSubmit = true;
                    }
                  });
                if(!couldSubmit){
                  alert('<?php echo TEXT_SELECT;?>');
                }
                if (couldSubmit == true) {
                  if (c_permission == 31) {
                    document.forms.selt_group.submit();  
                  } else {
                    $.ajax({
                      url: 'ajax_orders.php?action=getallpwd',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
                      async: false,
                      success: function(msg) {
                        var tmp_msg_arr = msg.split('|||'); 
                        var pwd_list_array = tmp_msg_arr[1].split(',');
                        if (tmp_msg_arr[0] == '0') {
                          document.forms.selt_group.submit();  
                        } else {
                          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                          if (in_array(input_pwd_str, pwd_list_array)) {
                            $.ajax({
                              url: 'ajax_orders.php?action=record_pwd_log',   
                              type: 'POST',
                              dataType: 'text',
                              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.selt_group.action),
                              async: false,
                              success: function(msg_info) {
                                document.forms.selt_group.submit();  
                              }
                            }); 
                          } else {
                            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                          }
                        }
                      }
                    });
                  }
                }
                 
                }
</script>
            <?php
              if (tep_db_num_rows($group_list_raw)) {
            ?>
                <input type="button" onclick="checkclicked('<?php echo $ocertify->npermission;?>')" value="<?php echo TEXT_TEMPLATE_ADD;?>"> 
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
