<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
   } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_CONTENTS);
     $sql_site_where = 'site_id in ('.$show_list_str.')';
     $show_list_array = explode(',',$show_list_str);
   }
  if(isset($_GET['site_id'])&&$_GET['site_id']==''){
     $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_CONTENTS));
   }
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
   while($userslist= tep_db_fetch_array($sites_id_sql)){
    $site_arr = $userslist['site_permission'];
   }
    $site_array = explode(',',$site_arr); 
  if(!isset($_GET['type']) || $_GET['type'] == ''){
      $_GET['type'] = 'asc';
  } 
  if($contents_type == ''){
    $contents_type = 'asc';
  }
  if(!isset($_GET['sort']) || $_GET['sort'] == ''){
     $contents_str = 'i.sort_id, i.heading_title'; 
  }else if($_GET['sort'] == 'site_romaji'){
     if($_GET['type'] == 'desc'){
       $contents_str = 's.romaji desc'; 
       $contents_type = 'asc';
     }else{
       $contents_str = 's.romaji asc'; 
       $contents_type = 'desc';
     }
  }else if($_GET['sort'] == 'title'){
     if($_GET['type'] == 'desc'){
       $contents_str = 'i.heading_title desc'; 
       $contents_type = 'asc';
     }else{
       $contents_str = 'i.heading_title asc'; 
       $contents_type = 'desc';
     }
  }else if($_GET['sort'] == 'status'){
     if($_GET['type'] == 'desc'){
       $contents_str = 'i.status desc'; 
       $contents_type = 'asc';
     }else{
       $contents_str = 'i.status asc'; 
       $contents_type = 'desc';
     }
  }else if($_GET['sort'] == 'sort_id'){
     if($_GET['type'] == 'desc'){
       $contents_str = 'i.sort_id desc'; 
       $contents_type = 'asc';
     }else{
       $contents_str = 'i.sort_id asc'; 
       $contents_type = 'desc';
     }
  }else if($_GET['sort'] == 'date_update'){
     if($_GET['type'] == 'desc'){
       $contents_str = 'i.date_update desc'; 
       $contents_type = 'asc';
     }else{
       $contents_str = 'i.date_update asc'; 
       $contents_type = 'desc';
     }
  }
  if (isset($_GET['act']) && $_GET['act']) {
    switch ($_GET['act']) {
/* -----------------------------------------------------
   case 'update' 更新内容   
   case 'insert' 新建内容   
   case 'setflag' 设置内容的状态    
   case 'deleteconfirm' 删除内容    
------------------------------------------------------*/
      case 'update':
        $site_id = (isset($_POST['action_sid']))?$_POST['action_sid']:0;
        if(isset($_SESSION['site_permission'])) {
          //权限判断
          $site_arr=$_SESSION['site_permission'];
        } else {
          $site_arr="";
        }
        forward401Unless(editPermission($site_arr, $site_id));

        $an_cols = array('navbar_title','heading_title','text_information');
        $error = false; 
        foreach ($an_cols as $col) {
          $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
        }
        $cID              = $_GET['cID'];
        $navbar_title     = tep_db_prepare_input($_POST['navbar_title']);
        $heading_title    = tep_db_prepare_input($_POST['heading_title']);
        $text_information = tep_db_prepare_input($_POST['text_information']);
        $status           = tep_db_prepare_input($_POST['status']);
        $sort_id          = tep_db_prepare_input($_POST['sort_id']);
        $page             = tep_db_prepare_input($_POST['page']);
        $romaji           = tep_db_prepare_input($_POST['romaji']);
        if (empty($romaji)) {
         $error = true;
         $error_message = ROMAJI_NOT_NULL;
         $_GET['action'] = 'edit';
        }
        $exists_romaji_query = tep_db_query("
            select * 
            from ".TABLE_INFORMATION_PAGE." 
            where romaji = '".$romaji."' 
            and pID != '".$cID."'
            and site_id = '".$site_id."'
        "); 

        $sql_data_array = array('pID' => $cID,
                                'navbar_title' => $navbar_title,
                                'heading_title' => $heading_title,
                                'text_information' => $text_information,
                                'status' => $status,
                                'romaji' => $romaji,
                                'sort_id' => $sort_id,
				'user_update' =>$_POST['user_update'],
				'date_update' =>'now()'
                                );
        if ($error == false) {
          tep_db_perform(TABLE_INFORMATION_PAGE, $sql_data_array, 'update', "pID = '" . tep_db_input($cID) . "'");
          tep_redirect(tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID .  '&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$page.((isset($_GET['site_id'])&&$_GET['site_id'])?'&site_id='.$_GET['site_id']:'')));
        }
        break;
      case 'insert':
        $an_cols = array('navbar_title','heading_title','text_information');
        $error = false; 
        foreach ($an_cols as $col) {
          $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
        }

        $navbar_title     = tep_db_prepare_input($_POST['navbar_title']);
        $heading_title    = tep_db_prepare_input($_POST['heading_title']);
        $text_information = tep_db_prepare_input($_POST['text_information']);
        $status           = tep_db_prepare_input($_POST['status']);
        $sort_id          = tep_db_prepare_input($_POST['sort_id']);
        $romaji           = tep_db_prepare_input($_POST['romaji']);
        $site_id          = tep_db_prepare_input($_POST['site_id']);
        if (empty($site_id)) {
         $error = true;
         $error_message = SITE_ID_NOT_NULL;
         $_GET['action'] = 'edit';
        }
        if (empty($romaji)) {
         $error = true;
         $error_message = ROMAJI_NOT_NULL;
         $_GET['action'] = 'insert';
        }
        $exists_romaji_query = tep_db_query("
            select * 
            from ".TABLE_INFORMATION_PAGE." 
            where romaji = '".$romaji."' and site_id='".$site_id."'"); 
        $exists_romaji_num = tep_db_num_rows($exists_romaji_query); 
        if ($exists_romaji_num > 0) {
          $error_message = ROMAJI_EXISTS; 
          $error = true;
          $_GET['action'] = 'insert';
        }

        $sql_data_array = array('navbar_title' => $navbar_title,
                                'heading_title' => $heading_title,
                                'text_information' => $text_information,
                                'status' => $status,
                                'romaji' => $romaji,
                                'sort_id' => $sort_id,
				'user_added' => $_POST['user_added'],
				'user_update'=> $_POST['user_update'],
				'date_added' =>'now()',
				'date_update' =>'now()',
                                'site_id' => $site_id);
        if ($error == false) {
          tep_db_perform(TABLE_INFORMATION_PAGE, $sql_data_array);
          tep_redirect(tep_href_link(FILENAME_CONTENTS,'cID='.tep_db_insert_id().'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id']));
          exit;
        }
          tep_redirect(tep_href_link(FILENAME_CONTENTS,'page='.$_GET['page'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&site_id='.$_GET['site_id']));
        break;    
    case 'setflag':
      $status = tep_db_prepare_input($_GET['flag']);
      $cID    = tep_db_prepare_input($_GET['cID']);
      $page   = tep_db_prepare_input($_GET['page']);
      tep_db_query("
          update ".TABLE_INFORMATION_PAGE." 
          set status = '".$status."',date_update=now()
          where pID = '".tep_db_input($cID)."'
      ");
      tep_redirect(tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID .  '&page='.$page.'&site_id='.$_GET['site_id']));
      break;
    case 'deleteconfirm':
        if(isset($_POST['contents_id'])&&!empty($_POST['contents_id'])){
          foreach($_POST['contents_id'] as $ge_key => $ge_value){
            tep_db_query("delete from " . TABLE_INFORMATION_PAGE . " where pID = '" . $ge_value . "'");
          }
        }
        $cID = tep_db_prepare_input($_GET['cID']);

        tep_db_query("delete from " . TABLE_INFORMATION_PAGE . " where pID = '" . tep_db_input($cID) . "'");
        tep_redirect(tep_href_link(FILENAME_CONTENTS,'page='.$page.'&site_id='.$_GET['site_id'])); 
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
$(document).ready(function() { 
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_contents').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_contents').css('display') != 'none') {
            if (o_submit_single){
              cid = $("#cid").val();
              $("#button_save").trigger("click");
            }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_contents').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_contents').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_contents(ele,cID,page,action_sid){
 site_id = '<?php echo (isset($_GET['site_id'])&&$_GET['site_id']!=''?($_GET['site_id']):'-1');?>';
 var sort = $("#sort").val();
 var type = $("#type").val();
 $.ajax({
 url: 'ajax.php?&action=edit_contents',
 data: {cID:cID,page:page,site_id:site_id,action_sid:action_sid,sort:sort,type:type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_contents").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(cID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_contents').height()){
offset = ele.offsetTop+$("#show_contents_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_contents').height()) > $('.box_warp').height())&&($('#show_contents').height()<ele.offsetTop+parseInt(head_top)-$("#show_contents_list").position().top-1)) {
offset = ele.offsetTop+$("#show_contents_list").position().top-1-$('#show_contents').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_contents_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_contents_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_contents').height()) > $('.box_warp').height())&&($('#show_contents').height()<ele.offsetTop+parseInt(head_top)-$("#show_contents_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_contents_list").position().top-1-$('#show_contents').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_contents_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_contents_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
  offset = offset+2;
} 
$('#show_contents').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_contents').height()) > $('.box_warp').height())&&($('#show_contents').height()<ele.offsetTop+parseInt(head_top)-$("#show_contents_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_contents_list").position().top-1-$('#show_contents').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_contents_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_contents_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_contents_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_contents').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_contents').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(cID == -1){
  $('#show_contents').css('top',$('#show_contents_list').offset().top);
}
$('#show_contents').css('z-index','1');
$('#show_contents').css('left',leftset);
$('#show_contents').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function all_select_contents(contents_str){
     var check_flag = document.del_contents.all_check.checked;
      if (document.del_contents.elements[contents_str]) {
          if (document.del_contents.elements[contents_str].length == null){
               if (check_flag == true) {
                 document.del_contents.elements[contents_str].checked = true;
               } else {
                 document.del_contents.elements[contents_str].checked = false;
               }
             } else {
                for (i = 0; i <
                  document.del_contents.elements[contents_str].length; i++){
                     if (!document.del_contents.elements[contents_str][i].disabled) { 
                          if (check_flag == true) {
                             document.del_contents.elements[contents_str][i].checked = true;
                            } else {
                               document.del_contents.elements[contents_str][i].checked = false;
                                                   }
                                              }
                                        }
                                  }
                    }
}
function hidden_info_box(){
   $('#show_contents').css('display','none');
}

function delete_select_contents(contents_str, c_permission){
      sel_num = 0;
      if (document.del_contents.elements[contents_str].length == null) {
           if (document.del_contents.elements[contents_str].checked == true){
               sel_num = 1;
            }
      } else {
           for (i = 0; i < document.del_contents.elements[contents_str].length; i++) {
           if(document.del_contents.elements[contents_str][i].checked == true) {
             sel_num = 1;
             break;
            }
           }
      }
      if (sel_num == 1) {
        if (confirm('<?php echo TEXT_DEL_CONTENTS;?>')) {
          if (c_permission == 31) {
            document.forms.del_contents.submit(); 
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
                  document.forms.del_contents.submit(); 
                } else {
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_contents.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_contents.submit(); 
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
        }else{
          document.getElementsByName('customers_action')[0].value = 0;
        }
      } else {
        document.getElementsByName('customers_action')[0].value = 0;
        alert('<?php echo TEXT_CONTENTS_MUST_SELECT;?>'); 
      }
}
function check_del(c_permission,cID,page,site_id){
  if (c_permission == 31) {
     window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?page="+page+"&site_id="+site_id+"&cID="+cID+"&act=deleteconfirm";
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
          window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?page="+page+"&site_id="+site_id+"&cID="+cID+"&act=deleteconfirm";
        } else {
          $("#button_save").attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_CONTENTS);?>?page='+page+'&site_id='+site_id+'&cID='+cID+'&act=deleteconfirm'),
              async: false,
              success: function(msg_info) {
                window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?page="+page+"&site_id="+site_id+"&cID="+cID+"&act=deleteconfirm";
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  } 
}
function check_image(c_permission,pID,page,flag){
  if (c_permission == 31) {
     window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?act=setflag&flag="+flag+"&cID="+pID+"&page="+page+"&site_id=<?php echo $_GET['site_id'];?>";
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
          window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?act=setflag&flag="+flag+"&cID="+pID+"&page="+page+"&site_id=<?php echo $_GET['site_id'];?>";
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_CONTENTS);?>?act=setflag&flag='+flag+'&cID='+pID+'&page='+page+'&site_id=<?php echo $_GET['site_id'];?>'),
              async: false,
              success: function(msg_info) {
                window.location.href="<?php echo tep_href_link(FILENAME_CONTENTS);?>?act=setflag&flag="+flag+"&cID="+pID+"&page="+page+"&site_id=<?php echo $_GET['site_id'];?>";
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
<?php //检查提交的罗马字?>
function check_contents(c_permission){
  post_romaji = $("#romaji").val(); 
  heading_title = $("#heading_title").val();
  var check_romaji = 'false';
  var error = 'false';
  var update_value = $("#romaji_hidden_value").val(); 
  if(update_value == 'insert'){
  $.ajax({
    url: 'ajax.php?action=check_romaji',
    data: {post_romaji:post_romaji} ,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(data){
     if(data != 0){
       check_romaji = 'true';  
     }
    }
   });
  }
 if(heading_title == ''){
   $("#heading_title_error").html("<?php echo TEXT_ERROR_NULL;?>");
   error = 'true';
 }else{
   $("#heading_title_error").html("");
 }
 if(post_romaji == ''){
   $("#error_romaji_info").html("<?php echo ROMAJI_NOT_NULL;?>"); 
   error = 'true';
 }else{
   $("#error_romaji_info").html(""); 
 }
 if(check_romaji == 'true'){
   $("#error_romaji").html("<?php echo ROMAJI_EXISTS;?>");
   error = 'true';
 }else{
   $("#error_romaji").html("");
 } 
 if(error != 'true'){
  if (c_permission == 31) {
    document.forms.content_form.submit();
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
          document.forms.content_form.submit();
        } else {
          $("#button_save").attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.content_form.action),
              async: false,
              success: function(msg_info) {
                document.forms.content_form.submit();
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
    
 }
 
}
<?php //选择动作?>
  function contents_change_action(r_value, r_str) {
   if (r_value == '1') {
     delete_select_contents(r_str, '<?php echo $ocertify->npermission;?>');
   }
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
preg_match_all('/act=[^&]+/',$belong,$belong_act_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/cID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != '' && $belong_temp_array[0][0] == 'action=edit'){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    if($belong_temp_array[0][0] == 'action=insert'){
      $belong = $href_url.'?'.$belong_temp_array[0][0];
    }else{
      $belong = $href_url;
    }
  }
}else{
  if($belong_act_array[0][0] == 'act=insert'){
    $belong = $href_url.'?action=insert';
  }else{
    $belong = $href_url;
  }
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();"> 
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<input id="show_info_id" type="hidden" name="show_info_id" value="show_contents">
<div id="show_contents" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content"> 
  <tr> 
    <td width="<?php echo BOX_WIDTH; ?>" id="categories_right_td" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"> 
        <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> 
      </table></td> 
    <!-- body_text --> 
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
<?php
  if (!isset($_GET['action']) && $_GET['action'] != 'insert' && $_GET['action'] != 'edit') {
  //内容列表 
      $cID = trim(isset($_GET['cID'])?$_GET['cID']:'');
?> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td>
          <?php tep_show_site_filter(FILENAME_CONTENTS,false,array(0));?>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_contents_list"> 
              <tr> 
                <td valign="top">
          <?php 
           echo '<input type="hidden" id="sort" value="'.$_GET['sort'].'"><input type="hidden" id="type" value="'.$_GET['type'].'">';
           if($_GET['sort'] == 'site_romaji'){
             if($_GET['type'] == 'desc'){
                $site_romaji = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
             }else{
                $site_romaji = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
             }
           }
           if($_GET['sort'] == 'title'){
             if($_GET['type'] == 'desc'){
                $heading_contents_title = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
             }else{
                $heading_contents_title = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
             }
           }
           if($_GET['sort'] == 'status'){
             if($_GET['type'] == 'desc'){
                $contents_status = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
             }else{
                $contents_status = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
             }
           }
           if($_GET['sort'] == 'sort_id'){
             if($_GET['type'] == 'desc'){
                $contents_sort_id = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
             }else{
                $contents_sort_id = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
             }
           }
           if($_GET['sort'] == 'date_update'){
             if($_GET['type'] == 'desc'){
                $contents_date_update = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
             }else{
                $contents_date_update = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
             }
           }
           $contents_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
           $notice_box = new notice_box('','',$contents_table_params);
           $contents_table_row = array(); 
           $contents_title_row = array();
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<input type="checkbox" name="all_check" onclick="all_select_contents(\'contents_id[]\');">');
           if(isset($_GET['sort']) && $_GET['sort'] == 'site_romaji'){
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" ','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=site_romaji&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type='.$contents_type).'">'.TABLE_HEADING_SITE.$site_romaji.'</a>');
           }else{
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" ','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=site_romaji&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type=desc').'">'.TABLE_HEADING_SITE.$site_romaji.'</a>');
           }
           if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=title&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type='.$contents_type).'">'.TABLE_HEADING_CONTENTS_TITLE.$heading_contents_title.'</a>');
           }else{
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=title&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type=desc').'">'.TABLE_HEADING_CONTENTS_TITLE.$heading_contents_title.'</a>');
           }
           if(isset($_GET['sort']) && $_GET['sort'] == 'status'){
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=status&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type='.$contents_type).'">'.TABLE_HEADING_CONTENTS_STATUS.$contents_status.'</a>');
           }else{
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=status&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type=desc').'">'.TABLE_HEADING_CONTENTS_STATUS.$contents_status.'</a>');
           }
           if(isset($_GET['sort']) && $_GET['sort'] == 'date_update'){
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=date_update&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type='.$contents_type).'">'.TABLE_HEADING_ACTION.$contents_date_update.'</a>');
           }else{
           $contents_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_CONTENTS,'sort=date_update&page='.$_GET['page'].(isset($_GET['cID']) && $_GET['cID']?'&cID='.$_GET['cID']:'').'&type=desc').'">'.TABLE_HEADING_ACTION.$contents_date_update.'</a>');
           }
           $contents_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $contents_title_row);
    $search = '';
    $count = 0;
    $contents_query_raw = "
      select i.pID,
             i.navbar_title,
             i.heading_title,
             i.text_information,
             i.status,
             i.sort_id,
             i.romaji,
             i.site_id,
             i.date_added,
             i.date_update,
             i.show_status,
             s.romaji as sromaji
      from ".TABLE_INFORMATION_PAGE." i , ".TABLE_SITES." s
      where s.id = i.site_id and ".$sql_site_where."
      order by ".$contents_str;
    $contents_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $contents_query_raw, $contents_query_numrows);
    $contents_query = tep_db_query($contents_query_raw);
    $contents_num = tep_db_num_rows($contents_query);
    while ($contents = tep_db_fetch_array($contents_query)) {
    $count++;
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if ( ((isset($cID) && $contents['pID'] == $cID) || ((!isset($_GET['cID']) || !$_GET['cID']) && $count == 1)) ) {
        $contents_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
        if(!isset($_GET['cID']) || !$_GET['cID']) {
          $cID = $contents['pID'];
        }
      } else {
        $contents_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
    $contents_info = array();
    if(in_array($contents['site_id'],$site_array)){
       if($contents['show_status'] == '1'){
       $contents_checkbox = '<input type="checkbox" disabled="disabled" name="contents_id[]" value="'.$contents['pID'].'">';
       }else{
       $contents_checkbox = '<input type="checkbox" name="contents_id[]" value="'.$contents['pID'].'">';
       }
    }else{
       $contents_checkbox = '<input type="checkbox" disabled="disabled" name="contents_id[]" value="'.$contents['pID'].'">';
    }
    $contents_info[] = array(
         'params' => 'class="dataTableContent"',
         'text'   => $contents_checkbox 
     );
    $contents_info[] = array(
         'params' => 'class="dataTableContent"',
         'text'   => $contents['sromaji']
     );
     $contents_info[] = array(
         'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CONTENTS,'cID='.$contents['pID'].'&page='.$_GET['page'].'&sort='.$_GET['sort'].'&type='.$_GET['type']).'\';"',
         'text'   => htmlspecialchars($contents['heading_title']) 
     );
     if ($contents['status'] == '1') {
       if($contents['show_status'] == '1'){
        $image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;'.tep_image(DIR_WS_IMAGES .  'icon_status_gray_light.gif', IMAGE_ICON_STATUS_GRAY_LIGHT);
       }else if(in_array($contents['site_id'],$site_array)){
        $image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="check_image('.$ocertify->npermission.','.$contents['pID'].','.$_GET['page'].',0)">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
       }else{
        $image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT,'','','disabled="disabled"');
       }
     }else {
      if(in_array($contents['site_id'],$site_array)){
       $image = '<a href="javascript:void(0)" onclick="check_image('.$ocertify->npermission.','.$contents['pID'].','.$_GET['page'].',1)">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
      }else{
       $image = tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT,'','','disabled="disabled"') . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
      }
     }
     $contents_info[] = array(
         'params' => 'class="dataTableContent" align="center"',
         'text'   => $image
     );
     $contents_info[] = array(
         'params' => 'class="dataTableContent" align="right"',
         'text'   => '<a href="javascript:void(0)" onclick="show_contents(this,'.$contents['pID'].','.$_GET['page'].','.$contents['site_id'].')">'.tep_get_signal_pic_info(isset($contents['date_update']) && $contents['date_update'] != null?$contents['date_update']:$contents['date_added']).'</a>'
     );
    $contents_table_row[] = array('params' => $contents_params, 'text' => $contents_info);
   }
   $contents_form = tep_draw_form('del_contents',FILENAME_CONTENTS,'act=deleteconfirm&site_id='.$_GET['site_id'].'&page='.$_GET['page'].(isset($_GET['search'])?'&search='.$_GET['search']:''));
   $notice_box->get_form($contents_form);
   $notice_box->get_contents($contents_table_row);
   $notice_box->get_eof(tep_eof_hidden());
   echo $notice_box->show_notice();
?> 
 
           <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;"> 
             <tr>
               <td>
                 <?php 
                   if($contents_num > 0){
                     if($ocertify->npermission >= 15){
                        echo '<select name="customers_action" onchange="contents_change_action(this.value, \'contents_id[]\');">';
                        echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';
                        echo '<option value="1">'.TEXT_CONTENTS_DELETE_ACTION.'</option>';
                        echo '</select>';
                     }
                    }else{
                       echo TEXT_DATA_EMPTY;
                    }
                  ?>
               </td>
             </tr>
                          <tr> 
                            <td class="smallText" valign="top"><?php echo $contents_split->display_count($contents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS); ?></td> 
                            <td class="smallText" align="right"><div class="td_box"><?php echo $contents_split->display_links($contents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></div></td> 
                          </tr> 
                    
          <tr>
            <td align="right" colspan="2"><div class="td_button">
            <?php if(($site_array[0] != '' && $site_array[0] != 0) || $site_array[1] != ''){  ?>
            <a href="javascript:void(0)" onclick="show_contents(this,-1,<?php echo $_GET['page'];?>,-1)"><?php echo tep_html_element_button(IMAGE_NEW_PROJECT); ?></a></div></td>
            <?php }else{
             echo '&nbsp;' .tep_html_element_button(IMAGE_NEW_PROJECT,'id="create_customers" disabled="disabled"');
            }?> 
          </tr>
		   </table>
				  </td> 
                <?php
  if($cID && tep_not_null($cID)) {
  $cquery = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cID."'");
  $cresult = tep_db_fetch_array($cquery);
  $c_title = $cresult['heading_title'];
  } else {
  $c_title = '&nbsp;';
  }
    
  $heading = array();
  $contents = array();
?> 
              </tr> 
            </table></td> 
        </tr> 
        <?php
  }
?> 
      </table>
      </div> 
      </div>
      </td> 
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
