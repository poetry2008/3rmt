<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  define('FILENAME_MESSAGES', 'messages.php');
  if($_GET['action']== 'change_read_status'){
	if($_POST['img'] == 'images/icons/email.png'){
		tep_db_query('update messages set read_status = "1" where id = "'.$_POST['id'].'"');
		echo '1'; 
	}else if($_POST['img'] == 'images/icons/email_open.png'){
		tep_db_query('update messages set read_status = "0" where id = "'.$_POST['id'].'"');
		echo '0';
	}
	exit;
  }
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id_sql)){
    $site_arr = $userslist['site_permission']; 
  }
 if($_GET['action']== 'delete_messages'){
	if(!empty($_POST['messages_id'])){
		foreach($_POST['messages_id'] as $value_messages_id){
			tep_db_query('delete from messages where id = '.$value_messages_id);
		}
	}
	if($_GET['messages_sort'] == ''){
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php'));
		}else{
			tep_redirect(tep_href_link('messages.php?page='.$_GET['page']));
		}
	}else{
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type']));
		}else{
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page']));
		}
	}
 }
 if($_GET['action']== 'new_messages'){
//	die(var_dump($_POST['selected_staff']));
    if(!empty($_POST['selected_staff'])){	
	$messages_file_name = '';
	$messages_file_status = '0';
	if ($_FILES['messages_file']['error'] > 0){
	}else{
		$messages_file_name = base64_encode($_FILES['messages_file']['name'].'|||'.$ocertify->auth_user.'|||'.time());
		$messages_file_status = '1';
		if (file_exists("messages_upload/" . $_FILES["messages_file"]["name"])){
      		}else{
      			$file_success = move_uploaded_file($_FILES["messages_file"]["tmp_name"],"messages_upload/" . $messages_file_name);
			//die(var_dump($file_success));
      		}
	}
	if(!empty($_POST['pic_icon'])){
		$pic_icon_str = implode(',',$_POST['pic_icon']);
	}else{
		$pic_icon_str = '';
	}
	foreach($_POST['selected_staff'] as $key => $value){
		$sql_data_array = array(
				     	'read_status' => '0',
					'mark' => $pic_icon_str,
					'sender_id' => $ocertify->auth_user,
					'recipient_id' => $value,
					'reply_status' => '0',
                                      	'content' => $_POST['contents'],
					'attach_file' => $messages_file_status,
					'file_name' => $messages_file_name,
					'opt' => '0',
					'sender_name' => $_SESSION['user_name'],
					'time' => date("Y/m/d H:i:s"),
                               );
         	tep_db_perform('messages', $sql_data_array);
		unset($sql_data_array);
	//	var_dump($sql_data_array);
	}
	if($_GET['messages_sort'] == ''){
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php'));
		}else{
			tep_redirect(tep_href_link('messages.php?page='.$_GET['page']));
		}
	}else{
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type']));
		}else{
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page']));
		}
	}
     }
  }  
  if($_GET['action']== 'back_messages'){
	//die(var_dump($_POST['selected_staff']));
    if(!empty($_POST['selected_staff'])){	
	$messages_file_name = '';
	$messages_file_status = '0';
	if ($_FILES['messages_file_back']['error'] > 0){
	}else{
		$messages_file_name = base64_encode($_FILES['messages_file_back']['name'].'|||'.$ocertify->auth_user.'|||'.time());
		$messages_file_status = '1';
		if (file_exists("messages_upload/" . $_FILES["messages_file_back"]["name"])){
      		}else{
      			$file_success = move_uploaded_file($_FILES["messages_file_back"]["tmp_name"],"messages_upload/" . $messages_file_name);
			//die(var_dump($file_success));
      		}
	}
	if(!empty($_POST['pic_icon'])){
		$pic_icon_str = implode(',',$_POST['pic_icon']);
	}else{
		$pic_icon_str = '';
	}
	foreach($_POST['selected_staff'] as $key => $value){
		$sql_data_array = array(
				     	'read_status' => '0',
					'mark' => $pic_icon_str,
					'sender_id' => $ocertify->auth_user,
					'recipient_id' => $value,
					'reply_status' => '1',
                                      	'content' => $_POST['back_contents'],
					'attach_file' => $messages_file_status,
					'file_name' => $messages_file_name,
					'opt' => '0',
					'sender_name' => $_SESSION['user_name'],
					'time' => date("Y/m/d H:i:s"),
                               );
         	tep_db_perform('messages', $sql_data_array);
		unset($sql_data_array);
	//	var_dump($sql_data_array);
	}
	tep_db_query('update messages set opt = "1" where id = '.$_GET['id']);
	if($_GET['messages_sort'] == ''){
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php'));
		}else{
			tep_redirect(tep_href_link('messages.php?page='.$_GET['page']));
		}
	}else{
		if($_GET['page'] == ''){
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type']));
		}else{
			tep_redirect(tep_href_link('messages.php?messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page']));
		}
	}
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
<script>
var o_submit_single = true;
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_latest_news').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_latest_news').css('display') != 'none') {
          if (o_submit_single) {
            $("#button_save").trigger("click");  
          }
        }
     }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_latest_news').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

 function check_news_info(){
       var headline = document.getElementById('headline').value; 
       var content  = document.getElementById('content').value;
       var news_image_description = document.getElementById('news_image_description').value;
       var s_single = false; 
       
       if (document.getElementById('site_type_hidden')) {
         var site_type = document.getElementById('site_type_hidden').value; 
         if (site_type == 0) {
           if (document.new_latest_news.elements['site_id_info[]']) {
             if (document.new_latest_news.elements['site_id_info[]'].length == null) {
               if (document.new_latest_news.elements['site_id_info[]'].checked == true) {
                 s_single = true; 
               }
             } else {
               for (var u = 0; u < document.new_latest_news.elements['site_id_info[]'].length; u++) {
                 if (document.new_latest_news.elements['site_id_info[]'][u].checked == true) {
                   s_single = true; 
                   break; 
                 }
               }
             }
           } else {
             s_single = true; 
           }
         } else {
           s_single = true; 
         }
       } else {
         s_single = true; 
       }
       
       $.ajax({
         url: 'ajax.php?action=edit_latest_news',
         type: 'POST',
         dataType: 'text',
         data:'headline='+headline+'&content='+content+'&news_image_description='+news_image_description, 
         async:false,
         success: function (data){
          if (headline != '' && s_single == true) {
            <?php
            if ($ocertify->npermission == 31) {
            ?>
            document.forms.new_latest_news.submit(); 
            <?php
            } else {
            ?>
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
                  document.forms.new_latest_news.submit(); 
                } else {
                  $('#button_save').attr('id', 'tmp_button_save'); 
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.new_latest_news.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.new_latest_news.submit(); 
                      }
                    }); 
                  } else {
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                    setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
                  }
                }
              }
            });
            <?php
            }
            ?>
          }else{
            if (headline != '') {
              $("#title_error").html(''); 
            } else {
              $("#title_error").html('<?php echo TEXT_NEWS_ERROR_NULL;?>'); 
            }
            if (s_single == false) {
              $("#site_error").html('<?php echo TEXT_ERROR_SITE;?>'); 
            } else {
              if ($("#site_error")) {
                $("#site_error").html(''); 
              }
            }
          }
         }
        });
}
function all_select_messages(messages_str){
	if($(messages_str).attr('checked')){
		$('input[name="messages_id[]"]').each(function() {
			$(this).attr("checked",true);
		});
	}else{
		$('input[name="messages_id[]"]').each(function() {
			$(this).attr("checked",false);
		});
	}
}

function delete_select_messages(messages_str, c_permission){
        sel_num = 0;
	$('input[name="messages_id[]"]').each(function() {
		if ($(this).attr("checked")) {
			sel_num = 1;
		}
	});	
        if (sel_num == 1) {
           if (confirm('<?php echo TEXT_DEL_NEWS;?>')) {
		document.forms.messages_checkbox.submit();	
           }else{
              document.getElementsByName('messages_action')[0].value = 0;
           }
         } else {
            document.getElementsByName('messages_action')[0].value = 0;
            alert('<?php echo TEXT_NEWS_MUST_SELECT;?>'); 
         }
}
function show_latest_messages(ele,page,latest_messages_id,sender_id,messages_sort,messages_sort_type,sender_name){
 var self_page = "<?php echo $_SERVER['PHP_SELF'];?>"
 if(latest_messages_id >0){
	$('#read_status_'+latest_messages_id).attr('src', 'images/icons/email_open.png');
 }
 $.ajax({
 url: 'ajax.php?&action=new_messages',
 data: {page:page,latest_messages_id:latest_messages_id,sender_id:sender_id,messages_sort:messages_sort,messages_sort_type:messages_sort_type,sender_name:sender_name} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_latest_news").html(data);
	if($('#info_'+latest_messages_id).prev().attr('id') != '' && $('#info_'+latest_messages_id).prev().attr('id') != null){
		$('#next_prev').append('<a id="messages_prev" onclick="'+$('#info_'+latest_messages_id).prev().children().find('a').attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">&lt<?php echo MESSAGES_PREV ?></a>&nbsp&nbsp');
	}
	if($('#info_'+latest_messages_id).next().attr('id') != '' && $('#info_'+latest_messages_id).next().attr('id') != null){
		$('#next_prev').append('<a id="messages_next" onclick="'+$('#info_'+latest_messages_id).next().children().find('a').attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);"><?php echo MESSAGES_NEXT ?>&gt</a>&nbsp&nbsp');
	}
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(latest_messages_id != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_latest_news').height()){
offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_latest_news').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_latest_news').height()) > $('.box_warp').height())&&($('#show_latest_news').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_latest_news').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_latest_news').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_latest_news').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(latest_messages_id == -1){
  $('#show_latest_news').css('top', $('#show_text_list').offset().top);
}
$('#show_latest_news').css('z-index','1');
$('#show_latest_news').css('left',leftset);
$('#show_latest_news').css('display', 'block');
o_submit_single = true;
	
  }
  }); 
}
function hidden_info_box(){
   $('#show_latest_news').css('display','none');
   o_submit_single = true;
}
<?php //选择动作?>
function messages_change_action(r_value, r_str) {
 if (r_value == '1') {
     delete_select_messages(r_str, '<?php echo $ocertify->npermission;?>');
   }
}
<?php //动作链接?>
function toggle_news_action(news_url_str) 
{
  <?php
    if ($ocertify->npermission == 31) {
  ?>
  window.location.href = news_url_str;  
  <?php
    } else {
  ?>
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
        window.location.href = news_url_str;  
      } else {
        if ($('#button_save')) {
          $('#button_save').attr('id', 'tmp_button_save'); 
        }
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(news_url_str),
            async: false,
            success: function(msg_info) {
              window.location.href = news_url_str;  
            }
          }); 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          if ($('#tmp_button_save')) {
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    }
  });
  <?php
    }
  ?>
}
<?php //全选?>
function select_all_news_site()
{
  var is_select_value = document.getElementById('is_select').value; 
  if (document.new_latest_news.elements['site_id_info[]']) {
    if (document.new_latest_news.elements['site_id_info[]'].length == null) {
      if (is_select_value == '0') {
        document.new_latest_news.elements['site_id_info[]'].checked = true;
        document.getElementById('is_select').value = '1'; 
      } else {
        document.new_latest_news.elements['site_id_info[]'].checked = false;
        document.getElementById('is_select').value = '0'; 
      }
    } else {
      if (is_select_value == '0') {
        for (var i = 0; i < document.new_latest_news.elements['site_id_info[]'].length; i++) {
          if (!document.new_latest_news.elements['site_id_info[]'][i].disabled) {
            document.new_latest_news.elements['site_id_info[]'][i].checked = true;
          }
        }
        document.getElementById('is_select').value = '1'; 
      } else {
        for (var i = 0; i < document.new_latest_news.elements['site_id_info[]'].length; i++) {
          if (!document.new_latest_news.elements['site_id_info[]'][i].disabled) {
            document.new_latest_news.elements['site_id_info[]'][i].checked = false;
          } 
        }
        document.getElementById('is_select').value = '0'; 
      }
    }
  }
}
<?php //选择网站?>
function change_site_type(site_type, site_list)
{
  var site_list_array = site_list.split(','); 
  if (site_type == 0) {
    $('#site_type_hidden').val('0'); 
    $('#select_site').find(':checkbox').each(function() {
      for (var i = 0; i < site_list_array.length; i++) {
        if ($(this).val() == site_list_array[i]) {
          $(this).removeAttr('disabled'); 
        }
      }
    }); 
    $('#all_site_button').removeAttr('disabled'); 
  } else {
    $('#site_type_hidden').val('1'); 
    $('#select_site').find(':checkbox').each(function() {
      $(this).attr('disabled', 'disabled'); 
    }); 
    $('#all_site_button').attr('disabled', 'disabled'); 
  }
}
var messages_checked_event_delete_to = '';
var messages_checked_event_send_to = '';
function checkbox_event(obj,event){
   if(!$('#message_to_all').attr('checked')){
	var is_checked = 0;
	
	if(event.ctrlKey && event.which == 1){
		if($(obj).parent().attr('id') == 'delete_to'){
			messages_checked_event_delete_to = $(obj);
		}else{
			messages_checked_event_send_to = $(obj);
		}
		if($(obj).children().attr('checked')){
			$(obj).css('background','#FFF');
			$(obj).css('color','black');
			$(obj).children().attr('checked',false);
		}else{
			$(obj).css('background','blue');
			$(obj).css('color','#FFF');
			$(obj).children().attr('checked',true);
		}
	}else if(event.shiftKey && event.which == 1){
		var shift_key_status = 0;
		$(obj).siblings().each(function(){
			if($(this).children().attr('checked')){
				shift_key_status = 1;
			}
		})
		if(shift_key_status == 0){
			$(obj).siblings().css('background','#FFF');
			$(obj).siblings().css('color','black');
			$(obj).siblings().children().attr('checked',false);
			$(obj).css('background','blue');
			$(obj).css('color','#FFF');
			$(obj).children().attr('checked',true);
			$(obj).parent().children().each(function(){
				if($(this).children().attr('value') == $(obj).children().attr('value')){
					return false;
				}else{
					$(this).css('background','blue');
					$(this).css('color','#FFF');
					$(this).children().attr('checked',true);
				}
			});
		}else{
			$(obj).siblings().css('background','#FFF');
			$(obj).siblings().css('color','black');
			$(obj).siblings().children().attr('checked',false);
			$(obj).css('background','blue');
			$(obj).css('color','#FFF');
			$(obj).children().attr('checked',true);
			var o = 0;
			var m = 0;
			$(obj).parent().children().each(function(){
				o++
				if($(this).children().attr('value') == $(obj).children().attr('value')){
					return false;
				}
			});
			if($(obj).parent().attr('id') == 'delete_to'){
				$(obj).parent().children().each(function(){
					m++
					if($(this).children().attr('value') == messages_checked_event_delete_to.children().attr('value')){
						return false;
					}
				});
				messages_checked_event_delete_to.css('background','blue');
				messages_checked_event_delete_to.css('color','#FFF');
				messages_checked_event_delete_to.children().attr('checked',true);
				if(m >= o){
					messages_checked_event_delete_to.prevUntil($(obj)).css('background','blue');
					messages_checked_event_delete_to.prevUntil($(obj)).css('color','#FFF');
					messages_checked_event_delete_to.prevUntil($(obj)).children().attr('checked',true);
				}else{
					messages_checked_event_delete_to.nextUntil($(obj)).css('background','blue');
					messages_checked_event_delete_to.nextUntil($(obj)).css('color','#FFF');
					messages_checked_event_delete_to.nextUntil($(obj)).children().attr('checked',true);
				}
			}else{
				$(obj).parent().children().each(function(){
					m++
					if($(this).children().attr('value') == messages_checked_event_send_to.children().attr('value')){
						return false;
					}
				});
				messages_checked_event_send_to.css('background','blue');
				messages_checked_event_send_to.css('color','#FFF');
				messages_checked_event_send_to.children().attr('checked',true);
				if(m >= o){
					messages_checked_event_send_to.prevUntil($(obj)).css('background','blue');
					messages_checked_event_send_to.prevUntil($(obj)).css('color','#FFF');
					messages_checked_event_send_to.prevUntil($(obj)).children().attr('checked',true);
				}else{
					messages_checked_event_send_to.nextUntil($(obj)).css('background','blue');
					messages_checked_event_send_to.nextUntil($(obj)).css('color','#FFF');
					messages_checked_event_send_to.nextUntil($(obj)).children().attr('checked',true);
				}
			}
		}
	}else{
		if($(obj).parent().attr('id') == 'delete_to'){
			messages_checked_event_delete_to = $(obj);
		}else{
			messages_checked_event_send_to = $(obj);
		}
		if($(obj).children().attr('checked')){
			$(obj).siblings().each(function(){
				if($(this).children().attr('checked')){
					is_checked = 1;
				}
			});
			if(is_checked == 1){
				$(obj).siblings().css('background','#FFF');
				$(obj).siblings().css('color','black');
				$(obj).siblings().children().attr('checked',false);
			}else{
				$(obj).css('background','#FFF');
				$(obj).css('color','black');
				$(obj).children().attr('checked',false);
				$(obj).siblings().css('background','#FFF');
				$(obj).siblings().css('color','black');
				$(obj).siblings().children().attr('checked',false);
			}
		}else{
			$(obj).css('background','blue');
			$(obj).css('color','#FFF');
			$(obj).children().attr('checked',true);
			$(obj).siblings().css('background','#FFF');
			$(obj).siblings().css('color','black');
			$(obj).siblings().children().attr('checked',false);
		}
	}
   }
}
var messages_radio_all = '';
function messages_to_all_radio(){
	$('#send_to').children().css('background','#FFF');
	$('#send_to').children().css('color','black');
	$('#send_to').children().children().attr('checked',false);
	messages_radio_all = $('#send_to').children();
	$('#delete_to').children().css('background','#FFF');
	$('#delete_to').children().css('color','black');
	$('#delete_to').children().children().attr('checked',false);
	$('#delete_to').children().children().attr('name','selected_staff[]');
	$('#send_to').append($('#delete_to').children());
	$('#send_to').children().css('background', '#E0E0E0');
	$('#select_user').css('display', 'none');
}
function messages_to_appoint_radio(){
	$('#send_to').children().css('background', '#FFF');
	$('#send_to').children().css('color','black');
	$('#send_to').children().children().attr('checked',false);
	$('#send_to').children().children().attr('name','all_staff');
	$('#delete_to').append($('#send_to').children());
	$('#delete_to').children().css('background','#FFF');
        $('#delete_to').children().css('color','black');
        $('#delete_to').children().children().attr('checked',false);
	messages_radio_all.css('background','#FFF');
	messages_radio_all.css('color','black');
	messages_radio_all.children().attr('checked',false);
	messages_radio_all.children().attr('name','selected_staff[]');
	$('#send_to').append(messages_radio_all);
	$('#select_user').css('display', '');	
}
function add_select_user(){
	$('input[name=all_staff]').each(function() {	
		if ($(this).attr("checked")) {
		 	$('#send_to').append('<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'+$(this).parent().attr("value")+'"><input hidden value="'+this.value+'" type="checkbox" name="selected_staff[]">'+$(this).parent().attr("value")+'</div>');
			$(this).parent().remove();	
		}
	});
}
function delete_select_user(){
	$('input[name="selected_staff[]"]').each(function() {	
		if ($(this).attr("checked")) {
		 	$('#delete_to').append('<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'+$(this).parent().attr("value")+'"><input hidden value="'+this.value+'" type="checkbox" name="all_staff" >'+$(this).parent().attr("value")+'</div>');
			$(this).parent().remove();	
		}
	});
}
function messages_check(is_back){
	var error_status_select = 0;
	var error_status_contents = 0;
	var error_status_back_contents = 1;
	var reg = /^\s*$/g;
	$('input[name="selected_staff[]"]').each(function() {
		$(this).attr("checked","");
	});
	$('input[name="selected_staff[]"]').each(function() {
		if($(this).attr("checked")) {
			error_status_select = 1;
		}
	});
	if(is_back == 1){
		error_status_back_contents = 0;
		if(!reg.test($('[name=back_contents]').val())){
			error_status_back_contents = 1;
		}
	}
	if(!reg.test($('[name=contents]').val())){
		error_status_contents = 1;
	}
	if(error_status_select == 0){
		$('#messages_to_must_select').css('display','');
	}
	if(error_status_contents == 0){
		$('#messages_must_write').css('display','');
	}
	if(error_status_back_contents == 0){
		$('#messages_back_must_write').css('display','');
	}
	if(error_status_select == 1 && error_status_contents == 1 && error_status_back_contents == 1){
		console.log('ok');
		document.forms.new_latest_messages.submit();
	}
}
function file_cancel(obj){
	$(obj).prev().attr('value','');
}
function change_read_status(obj,id){
	$.post(
		'messages.php?action=change_read_status',
		{
			id:id,
			img:$(obj).attr('src'),
		},
		function(data){
			if(data == '1'){
				$(obj).attr('src', 'images/icons/email_open.png');
			}else if(data == '0'){
				$(obj).attr('src', 'images/icons/email.png');
			}
		}
	)
}
function messages_selected(obj){
	$(obj).attr('onmouseover_last',$(obj).attr('onmouseover'));
	$(obj).attr('onmouseout_last',$(obj).attr('onmouseout'));
	$(obj).css('cusor','hand');
	$(obj).attr('onmouseover',false);
	$(obj).attr('onmouseout',false);
	$(obj).attr('class','dataTableRowSelected');
	$(obj).siblings().each(function(){
		if($(this).attr('class') == 'dataTableRowSelected'){
			$(this).attr('onmouseover',$(obj).attr('onmouseover_last'));
			$(this).attr('onmouseout',$(obj).attr('onmouseout_last'));
			$(this).attr('onmouseover_last',false);
			$(this).attr('onmouseout_last',false);
			$(this).mouseout();
		}
	});
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=new_latest_news/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/latest_news_id=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url.'?'.$belong_temp_array[0][0];
  }
}else{

  $belong = $href_url;
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
<input type="hidden" name="show_info_id" value="show_latest_news" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_latest_news"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_text_list">
          <tr>
            <td valign="top">
             <?php
	       $messages_sort_default = 'time';
               $messages_sort_type = 'desc';  
                
               if (isset($_GET['messages_sort_type'])) {
                 if ($_GET['messages_sort_type'] == 'asc') {
                   $messages_sort_type = 'asc'; 
                 } else {
                   $messages_sort_type = 'desc'; 
                 }
               }
		$messages_sort = '';
		if (isset($_GET['messages_sort'])) {
			switch($_GET['messages_sort']){
				case 'read_status': $messages_sort = 'read_status';break;
				case 'mark': $messages_sort = 'mark';break;
				case 'sender_id': $messages_sort = 'sender_id';break;
				case 'recipient_id': $messages_sort = 'recipient_id';break;
				case 'reply_status': $messages_sort = 'reply_status';break;
				case 'content': $messages_sort = 'content';break;
				case 'attach_file': $messages_sort = 'attach_file';break;
				case 'time': $messages_sort = 'time';break;
				case 'opt': $messages_sort = 'opt';break;
			}
		}
		
              $form_str = tep_draw_form('messages_checkbox', 'messages.php','action=delete_messages&messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"'); 
                if($messages_sort == '' || $messages_sort != 'read_status'){ 
			$messages_read_status = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=read_status&messages_sort_type=desc').'">'.READ_STATUS.'</a>'; 
		}else{
			if($messages_sort == 'read_status' && $messages_sort_type == 'desc'){
				$messages_read_status = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=read_status&messages_sort_type=asc').'">'.READ_STATUS.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_read_status = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=read_status&messages_sort_type=desc').'">'.READ_STATUS.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'mark'){ 
			$messages_mark = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=mark&messages_sort_type=desc').'">'.MESSAGES_MARK.'</a>'; 
		}else{
			if($messages_sort == 'mark' && $messages_sort_type == 'desc'){
				$messages_mark = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=mark&messages_sort_type=asc').'">'.MESSAGES_MARK.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_mark = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=mark&messages_sort_type=desc').'">'.MESSAGES_MARK.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'sender_id'){ 
			$messages_from = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=sender_id&messages_sort_type=desc').'">'.MESSAGES_FROM.'</a>'; 
		}else{
			if($messages_sort == 'sender_id' && $messages_sort_type == 'desc'){
				$messages_from = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=sender_id&messages_sort_type=asc').'">'.MESSAGES_FROM.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_from = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=sender_id&messages_sort_type=desc').'">'.MESSAGES_FROM.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'recipient_id'){ 
			$messages_to = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=recipient_id&messages_sort_type=desc').'">'.MESSAGES_TO.'</a>'; 
		}else{
			if($messages_sort == 'recipient_id' && $messages_sort_type == 'desc'){
				$messages_to = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=recipient_id&messages_sort_type=asc').'">'.MESSAGES_TO.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_to = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=recipient_id&messages_sort_type=desc').'">'.MESSAGES_TO.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'reply_status'){ 
			$messages_back = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=reply_status&messages_sort_type=desc').'">'.MESSAGES_BACK.'</a>'; 
		}else{
			if($messages_sort == 'reply_status' && $messages_sort_type == 'desc'){
				$messages_back = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=reply_status&messages_sort_type=asc').'">'.MESSAGES_BACK.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_back = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=reply_status&messages_sort_type=desc').'">'.MESSAGES_BACK.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'content'){ 
			$messages_content = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=content&messages_sort_type=desc').'">'.MESSAGES_CONTENT.'</a>'; 
		}else{
			if($messages_sort == 'content' && $messages_sort_type == 'desc'){
				$messages_content = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=content&messages_sort_type=asc').'">'.MESSAGES_CONTENT.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_content = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=content&messages_sort_type=desc').'">'.MESSAGES_CONTENT.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'attach_file'){ 
			$messages_add_file = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=attach_file&messages_sort_type=desc').'">'.ADD_FILE.'</a>'; 
		}else{
			if($messages_sort == 'attach_file' && $messages_sort_type == 'desc'){
				$messages_add_file = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=attach_file&messages_sort_type=asc').'">'.ADD_FILE.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_add_file = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=attach_file&messages_sort_type=desc').'">'.ADD_FILE.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'time'){ 
			$messages_date = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=time&messages_sort_type=desc').'">'.MESSAGES_DATE.'</a>'; 
		}else{
			if($messages_sort == 'time' && $messages_sort_type == 'desc'){
				$messages_date = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=time&messages_sort_type=asc').'">'.MESSAGES_DATE.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_date = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=time&messages_sort_type=desc').'">'.MESSAGES_DATE.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
		if($messages_sort == '' || $messages_sort != 'opt'){ 
			$messages_opt = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=opt&messages_sort_type=desc').'">'.MESSAGES_OPT.'</a>'; 
		}else{
			if($messages_sort == 'opt' && $messages_sort_type == 'desc'){
				$messages_opt = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=opt&messages_sort_type=asc').'">'.MESSAGES_OPT.'
				<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font></a>';
			}else{
				$messages_opt = '<a href="'.tep_href_link(FILENAME_MESSAGES,'messages_sort=opt&messages_sort_type=desc').'">'.MESSAGES_OPT.'
				<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font></a>';
			}
		}
               
               $messages_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
               $notice_box = new notice_box('','',$messages_table_params);       
               $messages_table_row = array();
               $messages_title_row = array();
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_messages(this);">');
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_read_status);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_mark);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_from);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_to);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_back);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="40%"','text' => $messages_content);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_add_file);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_date);
               $messages_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $messages_opt);
               $messages_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $messages_title_row);
    $rows = 0;
    if($messages_sort == ''){
	$messages_sort_sql = $messages_sort_default;
    }else{
	$messages_sort_sql = $messages_sort;
    }
    $messages_page = $_GET['page'];
    $latest_messages_query_raw = '
        select * 
        from messages where recipient_id = "'.$ocertify->auth_user.'" order by '.$messages_sort_sql.' '.$messages_sort_type;
    $latest_messages_split = new splitPageResults($messages_page, MAX_DISPLAY_SEARCH_RESULTS, $latest_messages_query_raw, $latest_messages_query_numrows);
    $latest_messages_query = tep_db_query($latest_messages_query_raw);
    while ($latest_messages = tep_db_fetch_array($latest_messages_query)) {
	$rows++;
	$even = 'dataTableSecondRow';
	$odd  = 'dataTableRow';
	if (isset($nowColor) && $nowColor == $odd) {
		$nowColor = $even;
	} else {
		$nowColor = $odd;
	}
	$messages_params = 'id="info_'.$latest_messages['id'].'" class="'.$nowColor.'" onclick="messages_selected(this)" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
	$messages_info = array();
	$messages_checkbox = '<input type="checkbox" name="messages_id[]" value="'.$latest_messages['id'].'">';
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $messages_checkbox		
	);
	$messages_read_status = $latest_messages['read_status']==0 ? '<img onclick="change_read_status(this,'.$latest_messages['id'].')" id="read_status_'.$latest_messages['id'].'" src="images/icons/email.png" border="0">' : '<img onclick="change_read_status(this,'.$latest_messages['id'].')" id="read_status_'.$latest_messages['id'].'" src="images/icons/email_open.png" border="0">';
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $messages_read_status
	);
	$mark_html = '';
	if($latest_messages['mark'] != ''){
		$mark_array = explode(',',$latest_messages['mark']);
		foreach($mark_array as $value){
			$mark_handle = strlen($value) > 1 ? $value : '0'.$value;
			$mark_html .= '<img src="images/icon_list/icon_'.$mark_handle.'.gif" border="0">';
		}
	}
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $mark_html
	);
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $latest_messages['sender_name']
	);
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $_SESSION['user_name']
	);
	$messages_reply_status = $latest_messages['reply_status']==0 ? '' : '<img src="images/icons/reply_icon.png" border="0">';
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $messages_reply_status
	);
	$messages_info[] = array(
		'params' => 'class="dataTableContent" width="300px"',
		'text'   => '<p style="max-height:38px;overflow:hidden;margin:5px 0px 5px 0px ">'.str_replace('>','&gt',str_replace('<','&lt',$latest_messages['content'])).'</p>'
	);
//die('<div style="max-height:38px;overflow:hidden;margin:5px 0px 5px 0px "><xmp>'.$latest_messages['content'].'</xmp></div>');
	$messages_attach_file = $latest_messages['attach_file']==0 ? '' : '<img src="images/icons/attach.png" border="0">';
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $messages_attach_file
	);
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => $latest_messages['time']
	);
	$messages_opt = $latest_messages['opt']==0 ? '<img src="images/icons/info_blink.gif" border="0">' : '<img src="images/icons/info_green.gif" border="0">';
	$messages_info[] = array(
		'params' => 'class="dataTableContent"',
		'text'   => '<a href="javascript:void(0)" onclick="show_latest_messages(this,\''.$_GET['page'].'\','.$latest_messages['id'].',\''.$latest_messages['sender_id'].'\',\''.$messages_sort.'\',\''.$messages_sort_type.'\',\''.$latest_messages['sender_name'].'\')">'.$messages_opt.'</a>'
	);
	$messages_table_row[] = array('params' => $messages_params, 'text' => $messages_info);
    }
  $notice_box->get_form($form_str);
  $notice_box->get_contents($messages_table_row);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
?>&nbsp;</td>
              </tr>

            </table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:-10px;">
<tr>                 
                    <td valign="top" class="smallText">
                    <?php 
                    echo '<select name="messages_action" onchange="messages_change_action(this.value, \'messages_id[]\');">';
                    echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                    echo '</select>';
                    ?> 
                    </td>
                    <td align="right" class="smallText">
                   </td>
                  </tr>

                  <tr>
                    <td class="smallText" valign="top"><?php echo $latest_messages_split->display_count($latest_messages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $messages_page, TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $latest_messages_split->display_links($latest_messages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $messages_page, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></div></td>
                  </tr>
                     <tr><td></td><td align="right">
                      <div class="td_button"><?php
                      //通过site_id判断是否允许新建
                     // if (trim($site_array[0]) != '') {
                      echo '&nbsp;<a href="javascript:void(0)" onclick="show_latest_messages(this,\''.$_GET['page'].'\',-1,\'\',\''.$messages_sort.'\',\''.$messages_sort_type.'\')">' .tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>';
                     // }else{
                     // echo '&nbsp;' .tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"');
                     // } 
                      ?>
                    </div>
                     </td></tr>
                                  </table>
			</td>
          </tr>
        </table></td>
      </tr>
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
