<?php
/*
  $Id$
*/

require('includes/application_top.php');
require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id_sql)){
        $site_arr = $userslist['site_permission'];
  }
 if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 's.id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
 } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_PRESENT);
     $sql_site_where = 's.id in ('.$show_list_str.')';
     $show_list_array = explode(',',$show_list_str);
 }
  if(!isset($_GET['action']) || $_GET['action'] == ''){
                      if(!isset($_GET['type']) || $_GET['type'] == ''){
                         $_GET['type'] = 'asc';
                      }
                      if($present_type == ''){
                         $present_type = 'asc';
                      }
                      if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                         $present_str = 'date_update desc';
                      }else if($_GET['sort'] == 'site_name'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'romaji desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'romaji asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'title'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'title desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'title asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'limit_date'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'limit_date desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'limit_date asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'date_update'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'date_update desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'date_update asc';
                                $present_type = 'desc';
                             }
                      }
                      if($_GET['sort'] == 'site_name'){
                            if($_GET['type'] == 'desc'){
                               $present_site_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_site_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                      if($_GET['sort'] == 'title'){
                            if($_GET['type'] == 'desc'){
                               $present_title = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_title = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                      if($_GET['sort'] == 'limit_date'){
                            if($_GET['type'] == 'desc'){
                               $present_limit_date = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_limit_date = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                      if($_GET['sort'] == 'date_update'){
                            if($_GET['type'] == 'desc'){
                               $present_date_update = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_date_update = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }

  }else if($_GET['action'] == 'list'){
                      if(!isset($_GET['type']) || $_GET['type'] == ''){
                         $_GET['type'] = 'asc';
                      }
                      if($present_type == ''){
                         $present_type = 'asc';
                      }
                      if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                         $present_str = 'tourokubi desc';
                      }else if($_GET['sort'] == 'family_name'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'family_name desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'family_name asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'first_name'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'first_name desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'first_name asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'tourokubi'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'tourokubi desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'tourokubi asc';
                                $present_type = 'desc';
                             }
                      }else if($_GET['sort'] == 'date_update'){
                            if($_GET['type'] == 'desc'){
                                $present_str = 'tourokubi desc';
                                $present_type = 'asc';
                             }else{
                                $present_str = 'tourokubi asc';
                                $present_type = 'desc';
                             }
                      }

                     if($_GET['sort'] == 'family_name'){
                            if($_GET['type'] == 'desc'){
                               $present_family_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_family_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                     if($_GET['sort'] == 'first_name'){
                            if($_GET['type'] == 'desc'){
                               $present_first_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_first_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                     if($_GET['sort'] == 'tourokubi'){
                            if($_GET['type'] == 'desc'){
                               $present_tourokubi = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_tourokubi = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
                     if($_GET['sort'] == 'date_update'){
                            if($_GET['type'] == 'desc'){
                               $present_date_update = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                            }else{
                               $present_date_update = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                            }
                      }
  }
  $site_array = explode(',',$site_arr);
//获取年月日
  $today = getdate();
  $yyyy = $today['year'];
  $mm = $today['mon'];
  $dd = $today['mday'];
  $pd = $dd + 1;

/* -----------------------------------------------------
    功能: 获取文件的扩展名 
    参数: $filepath(string) 文件名
    返回值: 文件的扩展名(string)
 -----------------------------------------------------*/
  function GetExt($filepath){
    $f = strrev($filepath);
    $ext = substr($f,0,strpos($f,"."));
    return strrev($ext);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'insert'){
    //新建赠品
    $site_id  = tep_db_prepare_input($_POST['site_id']);
    $ins_title = tep_db_prepare_input($_POST['title']);

    if($_FILES['file']['tmp_name'] != ""){
      $filepath = tep_get_upload_dir($site_id) ."present/"."image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      $filepath2 = "image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'],$filepath);
    }else{
      $filepath2 = "";
    }

    $ins_ht   = tep_db_prepare_input($_POST['ht']);
    $ins_text = addslashes($_POST['text']);
    $ins_period1 = tep_db_prepare_input($_POST['start_y']).tep_db_prepare_input($_POST['start_m']).tep_db_prepare_input($_POST['start_d']);
    $ins_period2 = tep_db_prepare_input($_POST['limit_y']).tep_db_prepare_input($_POST['limit_m']).tep_db_prepare_input($_POST['limit_d']);

    $ins = "insert into ".TABLE_PRESENT_GOODS."(
              title, 
              html_check, 
              image, 
              text, 
              start_date, 
              limit_date,
	      user_added,
	      user_update,
	      date_added,
	      date_update,
              site_id
            ) values (
              '".$ins_title."',
              '".$ins_ht."',
              '".$filepath2."',
              '".$ins_text."',
              '".$ins_period1."',
              '".$ins_period2."',
	      '".$_POST['user_added']."',
'".$_POST['user_update']."',
now(),
now(),
              '".$site_id."')";

          
    $mess = mysql_query($ins) or die(DATA_APPEND_ERROR);
    if($mess == true){
    }
    header("location: present.php?page=".$_GET['page']);
  }

  if(isset($_GET['action']) && $_GET['action'] == 'update'){
    //更新赠品 
    $up_id = tep_db_prepare_input($_GET['cID']);
    $up_ht = tep_db_prepare_input($_POST['ht']);
    $up_title = tep_db_prepare_input($_POST['title']);
    $present = tep_get_present_by_id($_GET['cID']);
  $site_id=$present['site_id'];
    if(!$site_id) $site_id=0;
   if(isset($_SESSION['site_permission'])) {
     //权限判断
     $site_arr=$_SESSION['site_permission'];
   } else {
     $site_arr="";
   }
   forward401Unless(editPermission($site_arr, $site_id));
    if($_FILES['file']['tmp_name'] != ""){
      $filepath = tep_get_upload_dir($present['site_id'])."present/"."image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      $filepath2 = "image".".".date("YmdHis").".".GetExt($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'],$filepath);
    }else{
      $filepath2 = "";
    }

    $up_text = addslashes($_POST['text']);
    $up_period1 = tep_db_prepare_input($_POST['start_y']).tep_db_prepare_input($_POST['start_m']).tep_db_prepare_input($_POST['start_d']);
    $up_period2 = tep_db_prepare_input($_POST['limit_y']).tep_db_prepare_input($_POST['limit_m']).tep_db_prepare_input($_POST['limit_d']);

      if($filepath2 == ""){
      $up = "update ".TABLE_PRESENT_GOODS." set title='".$up_title."', html_check='".$up_ht."', text='".$up_text."',start_date='".$up_period1."',limit_date='".$up_period2."',user_update='".$_SESSION['user_name']."',date_update=now() where goods_id='".$up_id."'";
      }else{
      echo $up = "update ".TABLE_PRESENT_GOODS." set title='".$up_title."', html_check='".$up_ht."', image='".$filepath2."',text='".$up_text."',start_date='".$up_period1."',limit_date='".$up_period2."',user_update='".$_SESSION['user_name']."',date_update=now() where goods_id='".$up_id."'";
      }
    $mess = mysql_query($up) or die(DATA_APPEND_ERROR);
      if($mess == true){
      }
    header("location: present.php");
  }

  if(isset($_GET['action']) && $_GET['action'] == 'delete'){
    if(!empty($_POST['present_id'])){
       foreach($_POST['present_id'] as $ge_key => $ge_value){
         $dele = "delete from ".TABLE_PRESENT_GOODS." where goods_id = '".$ge_value."'";
         $mess = mysql_query($dele) or die(DATA_APPEND_ERROR);
       } 
    }
    //删除赠品 
    $dele_id = tep_db_prepare_input($_GET['cID']);
    $dele = "delete from ".TABLE_PRESENT_GOODS." where goods_id = '".$dele_id."'";
    $mess = mysql_query($dele) or die(DATA_APPEND_ERROR);
      if($mess == true){
      }
    header("location: present.php");
  }
  if(isset($_GET['action']) && $_GET['action'] == 'list_delete'){
    if(!empty($_POST['present_id'])){
       foreach($_POST['present_id'] as $ge_key => $ge_value){
         $dele = "delete from ".TABLE_PRESENT_APPLICANT." where id = '".$ge_value."'";
         $mess = mysql_query($dele) or die(DATA_APPEND_ERROR);
       } 
    }
    $dele_id = tep_db_prepare_input($_GET['list_id']);
    $dele = "delete from ".TABLE_PRESENT_APPLICANT." where id = '".$dele_id."'";
    $mess = mysql_query($dele) or die(DATA_APPEND_ERROR);
    header("location: present.php?action=list&cID=".$_GET['cID'].'&site_id='.$_GET['site_id'].'&page='.$_GET['page'].($_GET['sort']?'&sort='.$_GET['sort']:'').($_GET['type']?'&type='.$_GET['type']:'').($_GET['list_id']?'&list_id='.$_GET['list_id']:''));
  }

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
  if(isset($_GET['action']) && $_GET['action']=='list'){
 echo  HEADING_TITLE2;
  }else if(isset($_GET['action']) && $_GET['action']=='listview'){
 echo PRESENT_CUSTOMER_TITLE ;
  }else{
  echo HEADING_TITLE;
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/cID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != '' && $belong_temp_array[0][0] != 'action=deleform'){

    $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_temp_array[0][0];
  }else{
    if($belong_temp_array[0][0] == 'action=input'){
      $belong = $href_url.'?'.$belong_temp_array[0][0];
    }else{
      $belong = $href_url;
    }
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<script language="javascript">
function view_delete(c_permission,cID){
   if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
    if (c_permission == 31) {
     location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=delete&cID='+cID; 
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
            location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=delete&cID='+cID; 
          } else {
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent( location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=delete&cID='+cID), 
                async: false,
                success: function(msg_info) {
                  location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=delete&cID='+cID; 
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
function list_delete(c_permission,cID,list_id){
   if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
    if (c_permission == 31) {
     location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=list_delete&cID='+cID+'&list_id='+list_id; 
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
            location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=list_delete&cID='+cID+'&list_id='+list_id; 
          } else {
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent( location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=list_delete&cID='+cID+'&list_id='+list_id),
                async: false,
                success: function(msg_info) {
                location.href = '<?php echo tep_href_link(FILENAME_PRESENT);?>?action=list_delete&cID='+cID+'&list_id='+list_id; 
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
function all_select_present(present_str){
     var check_flag = document.del_present.all_check.checked;
        if (document.del_present.elements[present_str]) {
            if (document.del_present.elements[present_str].length == null){
                  if (check_flag == true) {
                     document.del_present.elements[present_str].checked = true;
                  } else {
                     document.del_present.elements[present_str].checked = false;
                  }
            } else {
                  for (i = 0; i < document.del_present.elements[present_str].length; i++){
                      if (!document.del_present.elements[present_str][i].disabled) { 
                           if (check_flag == true) {
                             document.del_present.elements[present_str][i].checked = true;
                           } else {
                             document.del_present.elements[present_str][i].checked = false;
                           }
                      }
                  }
            }
        }
}
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_present').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_present').css('display') != 'none') {
            if (o_submit_single){
                cid = $("#cid").val();
                $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_present').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_present').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
function show_present(ele,cID,site_id,type,page,list_id){
 var sql = '<?php echo $sql_site_where;?>';
 var str = '<?php echo $present_str;?>';
 $.ajax({
 url: 'ajax.php?&action=edit_present',
 data: {cID:cID,site_id:site_id,type:type,sql:sql,str:str,page:page,list_id:list_id,list_id:list_id} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_present").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_present').height()){
offset = ele.offsetTop+$("#show_present_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_present').height()) > $('.box_warp').height())&&($('#show_present').height()<ele.offsetTop+parseInt(head_top)-$("#show_present_list").position().top-1)) {
offset = ele.offsetTop+$("#show_present_list").position().top-1-$('#show_present').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_present_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_present_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_present').height()) > $('.box_warp').height())&&($('#show_present').height()<ele.offsetTop+parseInt(head_top)-$("#show_present_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_present_list").position().top-1-$('#show_present').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_present_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_present_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
if(!(!+[1,])){
   offset = offset+3;
} 
$('#show_present').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_present').height()) > $('.box_warp').height())&&($('#show_present').height()<ele.offsetTop+parseInt(head_top)-$("#show_present_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_present_list").position().top-1-$('#show_present').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_present_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_present_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_present_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_present').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_present').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(type == 'input'){
    $('#show_present').css('top', $('#show_present_list').offset().top);
}
$('#show_present').css('z-index','1');
$('#show_present').css('left',leftset);
$('#show_present').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_present').css('display','none');
}
<?php //新建时的表单验证?>
function msg(c_permission){
  p_error = false; 
  if(document.apply.title.value == ""){
    $("#title_error").html("<?php echo PRESENT_PLEASE_ENTER_TITLE;?>");
    document.apply.title.focus();
    p_error = true; 
  }else{
    $("#title_error").html("");
    document.apply.title.focus();
  }
  if(document.apply.text.value == ""){
    $("#text_error").html("<?php echo PRESENT_PLEASE_INPUT_TEXT;?>");
    document.apply.text.focus();
    p_error = true; 
  }else{
    $("#text_error").html("");
    document.apply.text.focus();
  }
  if((document.apply.start_y.value + document.apply.start_m.value + document.apply.start_d.value) > (document.apply.limit_y.value + document.apply.limit_m.value +document.apply.limit_d.value)){
    $("#select_error").html("<?php echo PRESENT_PLEASE_START_DATE_END_DATE;?>");
    document.apply.start_y.focus();
    p_error = true; 
  }else{
    $("#select_error").html("");
    document.apply.start_y.focus();
  }
  if (p_error == false) {
    if (c_permission == 31) {
      document.forms.apply.submit(); 
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
            document.forms.apply.submit(); 
          } else {
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.apply.action),
                async: false,
                success: function(msg_info) {
                  document.forms.apply.submit(); 
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
<?php //更新时的表单验证?>
function msg2(c_permission){
  p_error = false; 
  if(document.view.title.value == ""){
    $("#title_error").html("<?php echo PRESENT_PLEASE_ENTER_TITLE;?>");
    document.view.title.focus();
    p_error = true; 
  }else{
    $("#title_error").html("");
    document.view.title.focus();
  }
  if(document.view.text.value == ""){
    $("#text_error").html("<?php echo PRESENT_PLEASE_INPUT_TEXT;?>");
    document.view.text.focus();
    p_error = true; 
  }else{
    $("#text_error").html("");
    document.view.text.focus();
  }
  if((document.view.start_y.value + document.view.start_m.value + document.view.start_d.value) > (document.view.limit_y.value + document.view.limit_m.value +document.view.limit_d.value)){
    alert("<?php echo PRESENT_PLEASE_START_DATE_END_DATE; ?>");
    document.view.start_y.focus();
    p_error = true; 
  }

  if (p_error == false) {
    if (c_permission == 31) {
      document.forms.apply.submit(); 
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
            document.forms.apply.submit(); 
          } else {
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.apply.action),
                async: false,
                success: function(msg_info) {
                  document.forms.apply.submit(); 
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
<?php //提交动作?>
function check_present_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.present.submit(); 
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
          document.forms.present.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.present.action),
              async: false,
              success: function(msg_info) {
                document.forms.present.submit(); 
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
function delete_select_present(present_str, c_permission){
     sel_num = 0;
     if (document.del_present.elements[present_str].length == null) {
         if (document.del_present.elements[present_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_present.elements[present_str].length; i++) {
             if(document.del_present.elements[present_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
         if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
           if (c_permission == 31) {
             document.forms.del_present.submit(); 
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
                  document.forms.del_present.submit(); 
                } else {
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_present.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_present.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('present_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('present_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('present_action')[0].value = 0;
             alert('<?php echo TEXT_NEWS_MUST_SELECT;?>'); 
          }
}
function present_change_action(r_value, r_str) {
   if (r_value == '1') {
       delete_select_present(r_str, '<?php echo $ocertify->npermission;?>');
   }
}
</script>
</head>
<body>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<input id="show_info_id" type="hidden" name="show_info_id" value="show_present">
<div id="show_present" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation -->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof -->
      </table></td>
    <!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible">
    <div class="compatible">
    <table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr>
          <td><!-- insert -->
            <?php
switch(isset($_GET['action'])?$_GET['action']:''){
case 'list' :
$c_id = tep_db_prepare_input($_GET['cID']);
?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE2; ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
          <?php 
          if($_GET['site_id'] == ''){
          $_GET['site_id'] =  $_SESSION['site_id'];
          }
          $site_query = tep_db_query("select * from ".TABLE_SITES." where id !=".$_GET['site_id']);
          $site_list_array = array();
              $site_list_array[] = '0';
          while($site_array = tep_db_fetch_array($site_query)){
              $site_list_array[] = $site_array['id'];
          }
          $_SESSION['site_id'] = $_GET['site_id'];
          tep_show_site_filter(FILENAME_PRESENT,'ture',$site_list_array);
          ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_present_list">
              <tr>
                <td valign="top">
                <?php 
                 $list_id = ($_GET['list_id']?'&list_id='.$_GET['list_id']:'');
                 $present_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0'); 
                 $notice_box = new notice_box('','',$present_table_params);
                 $present_table_row = array();
                 $present_title_row = array();
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_present(\'present_id[]\');">'); 
                 if(isset($_GET['sort']) && $_GET['sort'] == 'family_name'){
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=family_name&page='.$_GET['page'].'&type='.$present_type.$list_id).'">'.PRESENT_CUSTOMER_TABLE_SURNAME.$present_family_name.'</a>'); 
                 }else{
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=family_name&page='.$_GET['page'].'&type=desc'.$list_id).'">'.PRESENT_CUSTOMER_TABLE_SURNAME.$present_family_name.'</a>'); 
                 }
                 if(isset($_GET['sort']) && $_GET['sort'] == 'first_name'){
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=first_name&page='.$_GET['page'].'&type='.$present_type.$list_id).'">'.PRESENT_CUSTOMER_TABLE_NAME.$present_first_name.'</a>'); 
                 }else{
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=first_name&page='.$_GET['page'].'&type=desc'.$list_id).'">'.PRESENT_CUSTOMER_TABLE_NAME.$present_first_name.'</a>'); 
                 }
                 if(isset($_GET['sort']) && $_GET['sort'] == 'tourokubi'){
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=tourokubi&page='.$_GET['page'].'&type='.$present_type.$list_id).'">'.PRESENT_CUSTOMER_TABLE_APPLYDAY.$present_tourokubi.'</a>'); 
                 }else{
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=tourokubi&page='.$_GET['page'].'&type=desc'.$list_id).'">'.PRESENT_CUSTOMER_TABLE_APPLYDAY.$present_tourokubi.'</a>'); 
                 }
                 if(isset($_GET['sort']) && $_GET['sort'] == 'date_update'){
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=date_update&page='.$_GET['page'].'&type='.$present_type.$list_id).'">'.PRESENT_CUSTOMER_TABLE_OPERATE.$present_date_update.'</a>'); 
                 }else{
                 $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'cID='.$_GET['cID'].'&action='.$_GET['action'].'&site_id='.$_GET['site_id'].'&sort=date_update&page='.$_GET['page'].'&type=desc'.$list_id).'">'.PRESENT_CUSTOMER_TABLE_OPERATE.$present_date_update.'</a>'); 
                 }
                 $present_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $present_title_row);
    $search = '';
    $count = 0;
    $list_query_raw = "
      select p.id ,
             p.goods_id, 
             p.customer_id, 
             p.family_name, 
             p.first_name, 
             p.mail, 
             p.postcode, 
             p.prefectures, 
             p.cities, 
             p.address1, 
             p.address2, 
             p.phone, 
             p.tourokubi, 
             s.romaji,
             g.site_id
      from ".TABLE_PRESENT_APPLICANT." p , ".TABLE_SITES." s, ".TABLE_PRESENT_GOODS." g
      where p.goods_id='".$c_id."' 
        and g.goods_id = p.goods_id
        and s.id = g.site_id
      order by ".$present_str;
    $list_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $list_query_raw, $list_query_numrows);
    $list_query = tep_db_query($list_query_raw);
    $present_num = tep_db_num_rows($list_query);
    while ($list = tep_db_fetch_array($list_query)) {
      $count++;
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( ((isset($_GET['list_id']) && $list['id'] == $_GET['list_id']) || ((!isset($_GET['list_id']) || !$_GET['list_id']) && $count == 1)) ) {
      $present_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ' . "\n";
      if(!isset($_GET['list_id']) || !$_GET['list_id']) {

        $list_id = $list['id'];
      }
      } else {
      $present_params =  'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" ' . "\n";
      }
      $onclick = 'onclick="document.location.href=\'' .  tep_href_link(FILENAME_PRESENT, tep_get_all_get_params(array('list_id')) .  'list_id=' . $list['id']) . '\'"';
      $site_array = explode(',',$site_arr);
      if(in_array($list['site_id'],$site_array)){
           $present_checkbox = '<input type="checkbox"  name="present_id[]" value="'.$list['id'].'">';
      }else{
           $present_checkbox = '<input disabled="disabled" type="checkbox" name="present_id[]" value="'.$list['id'].'">';
      }
      $present_info = array();
      $present_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $present_checkbox
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($list['family_name'])
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($list['first_name'])
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($list['tourokubi']).''
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<a href="javascript:void(0)" onclick="show_present(this,'.$_GET['cID'].','.$list['site_id'].',\'listview\','.$_GET['page'].','.$list['id'].')">'.tep_get_signal_pic_info($list['tourokubi']).'</a>'
          );
      $present_table_row[] = array('params' => $present_params, 'text' => $present_info);
    }
  //While结束
    $present_form = tep_draw_form('del_present',FILENAME_PRESENT,'action=list_delete&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&page='.$_GET['page'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&list_id='.$_GET['list_id']);
    $notice_box->get_form($present_form);
    $notice_box->get_contents($present_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
  ?>
                                <tr>
                                  <td>
                                    <?php 
                                      if($present_num > 0){
                                          if($ocertify->npermission >= 15){
                                             echo '<select name="present_action" onchange="present_change_action(this.value, \'present_id[]\');">';
                                             echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                                             echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                                             echo '</select>';
                                           }
                                       }else{
                                             echo TEXT_DATA_EMPTY;
                                       }
                                    ?>
                                  </td>
                                </tr>
 
                    <tr>
                      <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="smallText" valign="top"><?php echo $list_split->display_count($list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS_PRESENT_LIST); ?></td>
                            <td class="smallText" align="right"><?php echo $list_split->display_links($list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                <?php
break;
default:

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
                <?php tep_show_site_filter(FILENAME_PRESENT,'ture',array(0));?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_present_list">
                    <tr>
                      <td valign="top">
                      <?php 
                      $present_cid = ($_GET['cID']?'&cID='.$_GET['cID']:'');
                      $present_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0'); 
                      $notice_box = new notice_box('','',$present_table_params);
                      $present_table_row = array();
                      $present_title_row = array();
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_present(\'present_id[]\');">'); 
                      if(isset($_GET['sort']) && $_GET['sort'] == 'site_name'){
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=site_name&page='.$_GET['page'].'&type='.$present_type.$present_cid).'">'.TABLE_HEADING_SITE.$present_site_name.'</a>'); 
                      }else{
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=site_name&page='.$_GET['page'].'&type=desc'.$present_cid).'">'.TABLE_HEADING_SITE.$present_site_name.'</a>'); 
                      }
                      if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=title&page='.$_GET['page'].'&type='.$present_type.$present_cid).'">'.PRESENT_NAME_TEXT.$present_title.'</a>'); 
                      }else{
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=title&page='.$_GET['page'].'&type=desc'.$present_cid).'">'.PRESENT_NAME_TEXT.$present_title.'</a>'); 
                      }
                      if(isset($_GET['sort']) && $_GET['sort'] == 'limit_date'){
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=limit_date&page='.$_GET['page'].'&type='.$present_type.$present_cid).'">'.PRESENT_DATE_TEXT.$present_limit_date.'</a>'); 
                      }else{
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=limit_date&page='.$_GET['page'].'&type=desc'.$present_cid).'">'.PRESENT_DATE_TEXT.$present_limit_date.'</a>'); 
                      }
                      if(isset($_GET['sort']) && $_GET['sort'] == 'date_update'){
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=date_update&page='.$_GET['page'].'&type='.$present_type.$present_cid).'">'.PRESENT_CUSTOMER_TABLE_OPERATE.$present_date_update.'</a>'); 
                      }else{
                      $present_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_PRESENT,'sort=date_update&page='.$_GET['page'].'&type=desc'.$present_cid).'">'.PRESENT_CUSTOMER_TABLE_OPERATE.$present_date_update.'</a>'); 
                      }
                      $present_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $present_title_row);
    $search = '';
    $count = 0;
    $present_query_raw = "
      select g.goods_id,
             g.html_check,
             g.title,
             g.image,
             g.text,
             g.start_date,
             g.limit_date,
             s.romaji,
             g.site_id,
             g.goods_id,
             g.date_added,
             g.date_update
      from ".TABLE_PRESENT_GOODS." g , ".TABLE_SITES." s
      where s.id = g.site_id  and ".$sql_site_where. " order by ".$present_str;
    $present_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $present_query_raw, $present_query_numrows);
    $present_query = tep_db_query($present_query_raw);
    $present_num = tep_db_num_rows($present_query);
    while ($present = tep_db_fetch_array($present_query)) {
      $count++;
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( ((isset($cID) && $present['goods_id'] == $cID) || ((!isset($_GET['cID']) || !$_GET['cID']) && $count == 1)) ) {
        $present_params =  'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
        
        if(!isset($_GET['cID']) || !$_GET['cID']) {
          $cID = $present['goods_id'];
        }
      } else {
        $present_params =  'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
      if(in_array($present['site_id'],$site_array)){
           $present_checkbox = '<input type="checkbox"  name="present_id[]" value="'.$present['goods_id'].'">';
      }else{
           $present_checkbox = '<input disabled="disabled" type="checkbox" name="present_id[]" value="'.$present['goods_id'].'">';
      }
      $onclick = 'onclick="document.location.href=\'' .  tep_href_link(FILENAME_PRESENT,'sort='.$_GET['sort'].'&page='.$_GET['page'].'&type='.$_GET['type'].'&cID='.$present['goods_id']) . '\'"';
      $present_info = array();
      $present_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $present_checkbox
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => tep_get_site_romaji_by_id($present['site_id'])
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => htmlspecialchars($present['title'])
          );
      $st_date = htmlspecialchars($present['start_date']);
      $st_ymd = substr($st_date,0,4)."/".substr($st_date,5,2)."/".substr($st_date,8,2);
      $li_date = htmlspecialchars($present['limit_date']);
      $li_ymd = substr($li_date,0,4)."/".substr($li_date,5,2)."/".substr($li_date,8,2);
      $present_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => "$st_ymd - $li_ymd"
          );
      $present_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<a href="javascript:void(0)" onclick="show_present(this,'.$present['goods_id'].','.$present['site_id'].',\'view\','.$_GET['page'].')">'.tep_get_signal_pic_info(isset($present['date_update']) && $present['date_update'] != null?$present['date_update']:$present['date_added']).'</a>'
          );
      $present_table_row[] = array('params' => $present_params, 'text' => $present_info);
    }
    $present_form = tep_draw_form('del_present',FILENAME_PRESENT,'action=delete');
    $notice_box->get_form($present_form);
    $notice_box->get_contents($present_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
?>
						<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                                <tr>
                                  <td>
                                    <?php 
                                      if($present_num > 0){
                                          if($ocertify->npermission >= 15){
                                             echo '<select name="present_action" onchange="present_change_action(this.value, \'present_id[]\');">';
                                             echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                                             echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                                             echo '</select>';
                                           }
                                       }else{
                                             echo TEXT_DATA_EMPTY;
                                       }
                                    ?>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="smallText" valign="top"><?php echo $present_split->display_count($present_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS_PRESENT); ?></td>
                                  <td class="smallText" align="right"><div class="td_box"><?php echo $present_split->display_links($present_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></div></td>
                                </tr>
								 <tr>
                            <td align="right" colspan="2"><div class="td_button"><a href="javascript:void(0)" onclick="show_present(this,-1,-1,'input','<?php echo $_GET['page'];?>')"><?php echo tep_html_element_button(IMAGE_NEW_PROJECT); ?></a></div></td>
                          </tr>
                              </table>
						</td>
                      <?php
          if(isset($cID) && $cID && tep_not_null($cID)) {
          $cquery = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".$cID."'");
          $cresult = tep_db_fetch_array($cquery);
          $c_title = $cresult['title'];
          } else {
          $c_title = '&nbsp;';
          }


          if ( (tep_not_null($heading)) && (tep_not_null($present)) ) {
          echo '            <td width="25%" valign="top">' . "\n";
        
          $box = new box;
          echo $box->infoBox($heading, $present);
        
          echo '            </td>' . "\n";
          }
        ?>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></div></td>
    <!-- body_text_eof -->
  </tr>
</table>
<?php
break;
}
?>
</tr>
</table>
</td>
</tr>
</table>
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
</body>
</html>
