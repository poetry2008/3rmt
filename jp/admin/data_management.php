<?php
  require('includes/application_top.php');
  require("includes/jcode.phps");

  require(DIR_FS_ADMIN . 'classes/notice_box.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo TEXT_DATA_MANAGEMENT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script>
$(document).ready(function() { 
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_data').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_data').css('display') != 'none') {
            if (o_submit_single){
              cid = $("#cid").val();
              $("#button_save").trigger("click");
            }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_data').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_data').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function change_action(url){
    document.getElementById('orders_download').action=url;
    document.getElementById('orders_download').submit();
}
function check_up(c_permission){
           if (c_permission == 31) {
             document.forms.mag_up.submit(); 
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
                   document.forms.mag_up.submit(); 
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.mag_up.action),
                      async: false,
                      success: function(msg_info) {
                         document.forms.mag_up.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
 
}
function check_dl(c_permission){
           if (c_permission == 31) {
             document.forms.mag_dl.submit(); 
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
                   document.forms.mag_dl.submit(); 
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.mag_dl.action),
                      async: false,
                      success: function(msg_info) {
                         document.forms.mag_dl.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
 
}
function orders_csv_exe(c_permission){
           if (c_permission == 31) {
             change_action("<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL');?>");
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
                   change_action("<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL');?>");
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         change_action("<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL');?>");
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
}
function customers_csv_exe(c_permission){
           if (c_permission == 31) {
             change_action("<?php echo tep_href_link('customers_csv_exe.php','csv_exe=true', 'SSL');?>");
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
                   change_action("<?php echo tep_href_link('customers_csv_exe.php','csv_exe=true', 'SSL');?>");
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         change_action("<?php echo tep_href_link('customers_csv_exe.php','csv_exe=true', 'SSL');?>");
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
}
function customers_csv_import(c_permission){
           if (c_permission == 31) {
             change_action("<?php echo tep_href_link('customers_csv_import.php','csv_exe=true', 'SSL');?>");
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
                   change_action("<?php echo tep_href_link('customers_csv_import.php','csv_exe=true', 'SSL');?>");
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         change_action("<?php echo
                           tep_href_link('customers_csv_import.php','csv_exe=true', 'SSL');?>");
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
}
function preorders_csv_exe(c_permission){
           if (c_permission == 31) {
             change_action("<?php echo tep_href_link('preorders_csv_exe.php','csv_exe=true', 'SSL');?>");
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
                   change_action("<?php echo tep_href_link('preorders_csv_exe.php','csv_exe=true', 'SSL');?>");
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str,pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str,
                      async: false,
                      success: function(msg_info) {
                         change_action("<?php echo tep_href_link('preorders_csv_exe.php','csv_exe=true', 'SSL');?>");
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
}
<?php //检查上传文件格式 ?>
function check_mag_up(){
  var products_csv = $("#products_csv").val(); 
  var site_id = $("#site_id").val();
  $.ajax({
    url: 'ajax.php?action=check_mag_up',
    data: {products_csv:products_csv,site_id:site_id} ,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(data){
    if(data != ''){
       $("#mag_up_error").html("<?php echo "<font color='#CC0000' size='2'>".UNABLE_UP.'&nbsp;'.REFERENCE_CSV."</font>";?>");
    }else{
       check_up(<?php echo $ocertify->npermission;?>);
    }
    }
    });
}
function show_data(ele,type,sql_type,c_id){
 var sort = '<?php echo $_GET['sort'];?>';
 var pitch = '<?php echo $_GET['pitch'];?>';
 $.ajax({
 url: 'ajax.php?&action=edit_data',
 data: {type:type,sql_type:sql_type,sort:sort,c_id:c_id,pitch:pitch} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_data").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_data').height()){
offset = ele.offsetTop+$("#show_data_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_data').height()) > $('.box_warp').height())&&($('#show_data').height()<ele.offsetTop+parseInt(head_top)-$("#show_data_list").position().top-1)) {
offset = ele.offsetTop+$("#show_data_list").position().top-1-$('#show_data').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_data_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_data_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_data').height()) > $('.box_warp').height())&&($('#show_data').height()<ele.offsetTop+parseInt(head_top)-$("#show_data_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_data_list").position().top-1-$('#show_data').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_data_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_data_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
   offset = offset+3;
} 
$('#show_data').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_data').height()) > $('.box_warp').height())&&($('#show_data').height()<ele.offsetTop+parseInt(head_top)-$("#show_data_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_data_list").position().top-1-$('#show_data').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_data_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_data_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_data_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_data').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_data').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
$('#show_data').css('z-index','1');
$('#show_data').css('left',leftset);
$('#show_data').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_data').css('display','none');
}
</script>
<?php
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof -->
<input type="hidden" value="show_data" name="show_info_id" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_data"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?>
     <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo TEXT_DATA_MANAGEMENT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table>
       </td>
      </tr>
      <tr>
        <td>
         <?php 
         $site_query = tep_db_query("select id from ".TABLE_SITES);
         $site_list_array = array();
         while($site_array = tep_db_fetch_array($site_query)){
               $site_list_array[] = $site_array['id'];
         }
         echo tep_show_site_filter(FILENAME_DATA_MANAGEMENT,false,$site_list_array);
         ?>
         <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_data_list">
          <tr>
            <td>
            <?php
             if((!isset($_GET['type']) || $_GET['type'] == '') && (!isset($_GET['sort'])||$_GET['sort']=='')){
                  $data_str = ' last_modified asc';
             }
             if($data_type == ''){
                  $data_type = 'asc';
             }
             if($_GET['sort'] == 'contents'){
               if($_GET['type'] == 'desc'){   
               $data_str = ' last_modified desc';
               $data_type= 'asc';
               }else{
               $data_str = ' last_modified asc';
               $data_type= 'desc';
               }
             }else if($_GET['sort'] == 'update_at'){
               if($_GET['type'] == 'desc'){   
               $data_str = ' last_modified desc';
               $data_type= 'asc';
               }else{
               $data_str = ' last_modified asc';
               $data_type= 'desc';
               }
             }

             if($_GET['sort'] == 'contents'){
               if($_GET['type'] == 'desc'){
                 $contents = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                 $contents = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
             }
             if($_GET['sort'] == 'update_at'){
               if($_GET['type'] == 'desc'){
                 $update_at = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                 $update_at = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
             }
             $data_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
             $notice_box = new notice_box('','',$data_table_params);
             $data_table_row = array();
             $data_title_row = array();
             $data_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox"name="all_check">');
             if(isset($_GET['sort']) && $_GET['sort'] == 'contents'){
             $data_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_DATA_MANAGEMENT,'sort=contents&type='.$data_type).'">'.TEXT_MAG_CONTENTS.$contents.'</a>');
             }else{    
             $data_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_DATA_MANAGEMENT,'sort=contents&type=desc').'">'.TEXT_MAG_CONTENTS.$contents.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'update_at'){
             $data_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_DATA_MANAGEMENT,'sort=update_at&type='.$data_type).'">'.TABLE_HEADING_ACTION.$update_at.'</a>');
             }else{
             $data_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_DATA_MANAGEMENT,'sort=update_at&type=desc').'">'.TABLE_HEADING_ACTION.$update_at.'</a>');
             }
             $data_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $data_title_row);

             $sql_num = tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT'"));
             if($sql_num > 0){
             $data_management = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT' order by ".$data_str);   
             while($data_row = tep_db_fetch_array($data_management)){
             $even = 'dataTableSecondRow';
             $odd  = 'dataTableRow';
             if (isset($nowColor) && $nowColor == $odd) {
                 $nowColor = $even; 
             } else {
                 $nowColor = $odd; 
             }
             if($_GET['pitch'] == $data_row['configuration_value']){
             $data_orders_params = ' class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
             }else{
             $data_orders_params = 'class="'.$nowColor.'" onmouseout="this.className=\''.$nowColor.'\'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" class="dataTableRow" style=""';
             }
             $data_orders_info = array();
             $data_orders_info[] = array(
                'params' => 'class="main"',
                'text'   => '<input type="checkbox" disabled="disabled">'
             );
             if($data_row['configuration_value']=='mag_customers'){
              $data_orders_info[] = array(
                'params' => 'class="main" onclick="document.location.href=\''.tep_href_link(FILENAME_DATA_MANAGEMENT,'pitch=mag_customers').'\';"',
                'text'   => TEXT_MAG_CUSTOMERS
             );
             }else if($data_row['configuration_value']=='mag_customers_import'){
               $data_orders_info[] = array(
                'params' => 'class="main" onclick="document.location.href=\''.tep_href_link(FILENAME_DATA_MANAGEMENT,'pitch=mag_customers_import').'\';"',
                'text'   => TEXT_MAG_CUSTOMERS_IMPORT
             );
             }else{
             $data_orders_info[] = array(
                'params' => 'class="main" onclick="document.location.href=\''.tep_href_link(FILENAME_DATA_MANAGEMENT,'pitch=mag_orders').'\';"',
                'text'   => TEXT_MAG_ORDERS
             );
             }
             $data_orders_info[] = array(
                'params' => 'class="main"',
                'text'   => '<a href="javascript:void(0)" onclick="show_data(this,\''.$data_row['configuration_value'].'\',\''.$data_str.'\','.$data_row['configuration_id'].')">'.  tep_get_signal_pic_info(isset($data_row['last_modified']) && $data_row['last_modified'] !=null?$data_row['last_modified']:$data_row['date_added']).'</a>'
             );
             $data_table_row[] = array('params' => $data_orders_params, 'text' => $data_orders_info);
             }
             }
             //sql else start
             $notice_box->get_contents($data_table_row);
             $notice_box->get_eof(tep_eof_hidden());
             echo $notice_box->show_notice();
            ?>
            </td>
         </tr>
<!-- body_text_eof -->
        </table></td>
      </tr>
      <tr>
         <td>
         <?php 
           echo '<select name="customers_action" disabled="disabled">';
           echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';
           echo '<option value="1">'.TEXT_CONTENTS_DELETE_ACTION.'</option>';
           echo '</select>';
         ?>
         </td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php 
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
