<?php
  /**
   * $Id$
   *
   * 备忘录管理
   */
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');
  //获取当前用户的网站管理权限
  $sites_id_sql = tep_db_query("select site_permission from ".TABLE_PERMISSIONS." where userid= '".$ocertify->auth_user."'");
  $userslist= tep_db_fetch_array($sites_id_sql);
  tep_db_free_result($sites_id_sql);
  $site_permission_array = explode(',',$userslist['site_permission']); 
  $site_permission_flag = false;
  if(in_array('0',$site_permission_array)){

    $site_permission_flag = true;
  }

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'insert' 新建memo     
   case 'save' 更新memo     
   case 'deleteconfirm' 删除memo      
   case 'delete' 删除选中的bulletin
   case 'end' 终止memo
   case 'search' 搜索
------------------------------------------------------*/
      case 'insert':
        $from = tep_db_prepare_input($_POST['from']);
        $users_list = tep_db_prepare_input($_POST['users_id_select']);
        $users_id = tep_db_prepare_input($_POST['users_id']);
        $users_id_array = array();
        $users_id_str = '';
        if($users_list == '1'){

          $users_id_array = array_unique(array_filter($users_id));
          $users_id_str = implode(',',$users_id_array);
        }

        $is_show = tep_db_prepare_input($_POST['is_show']);
        $pic_icon = tep_db_prepare_input($_POST['pic_icon']);
        $contents = tep_db_prepare_input($_POST['contents']);

        $sql_data_array = array(
           '`from`' => $from,
           '`to`' => $users_id_str, 
           'is_show' => $is_show,
           'icon' => $pic_icon,
           'contents' => $contents,
           'user_added' => $_SESSION['user_name'],
           'date_added'=> 'now()'
           );  
        tep_db_perform(TABLE_BUSINESS_MEMO, $sql_data_array);

        $bulletin_id = tep_db_insert_id();
           
        $sql_data_array = array(
           'type' => 1,
           'title' => $contents,
           'set_time' => 'now()',
           'from_notice' => $bulletin_id,
           'user' => $_SESSION['user_name'],
           'created_at' => 'now()'
          ); 
        tep_db_perform(TABLE_NOTICE, $sql_data_array);
  
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD));
        break;
      case 'save':
        $is_show = tep_db_prepare_input($_POST['is_show']);
        $pic_icon = tep_db_prepare_input($_POST['pic_icon']);
        $contents = tep_db_prepare_input($_POST['contents']); 
        $bulletin_id = tep_db_prepare_input($_POST['bulletin_id']);
        $param_str = tep_db_prepare_input($_POST['param_str']);

        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set read_flag = '', is_show='".$is_show."',icon='".$pic_icon."',contents='".$contents."',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($bulletin_id) . "'");

        tep_db_query("update " . TABLE_NOTICE . " set title='".$contents."' where from_notice = '" . tep_db_input($bulletin_id) . "' and type='1'");
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, $param_str));
        break;
      case 'deleteconfirm':
        $bulletin_id = tep_db_prepare_input($_POST['bulletin_id']);
        $param_str = $_POST['param_str'];
        tep_db_query("update " . TABLE_NOTICE . " set is_show='0' where from_notice = '" . tep_db_input($bulletin_id) . "' and type='1'");
        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set deleted='1',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($bulletin_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, $param_str));
        break;
      case 'delete':
        $bulletin_id_list = tep_db_prepare_input($_POST['bulletin_list_id']);
        $param_str = $_GET['page'];

        foreach($bulletin_id_list as $bulletin_id){
          tep_db_query("delete from ".TABLE_BULLETIN_BOARD." where id=".$bulletin_id);
        }
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, 'page='.$param_str));
        break;
      case 'end':
        $bulletin_id = $_GET['end_id'];
        $param_str = tep_db_prepare_input($_POST['param_str']);

        tep_db_query("update " . TABLE_NOTICE . " set is_show='0' where from_notice = '" . tep_db_input($bulletin_id) . "' and type='1'");
        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set finished='1',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($bulletin_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, $param_str));
        break;

	  case 'search':
		$text=$_GET['search_text'];
		
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
<script language="javascript">
<?php //快捷键监听?>
$(document).ready(function() { 
var box_warp_height = $(".box_warp").height();
  $(document).keyup(function(event) {
    if (event.which == 27) {
      if ($("#show_popup_info").css("display") != "none") {
        hidden_info_box();     
        o_submit_single = true;
      }
    }
    if (event.which == 13) {
      if ($("#show_popup_info").css("display") != "none") {
        if (o_submit_single) {
          $("#button_save").trigger("click");  
        }
      }
    }
    
    if (event.ctrlKey && event.which == 37) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#memo_prev")) {
          $("#memo_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#memo_next")) {
          $("#memo_next").trigger("click");
        }
      }
    }
  });    
});
var origin_offset_symbol = 0;
window.onresize = resize_option_page;
var o_submit_single = true;
<?php //窗口缩放事件?>
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
  box_warp_height = $(".box_warp").height(); 
}

<?php //删除动作?>
function select_bulletin_change(value,bulletin_list_id,c_permission)
{
  sel_num = 0;
  if (document.edit_bulletin_form.elements[bulletin_list_id].length == null) {
    if (document.edit_bulletin_form.elements[bulletin_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_bulletin_form.elements[bulletin_list_id].length; i++) {
      if (document.edit_bulletin_form.elements[bulletin_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm('<?php echo TEXT_MEMO_EDIT_CONFIRM;?>')) {
      if (c_permission == 31) {
        document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
        document.edit_bulletin_form.submit(); 
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
              document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
              document.edit_bulletin_form.submit(); 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>'),
                  async: false,
                  success: function(msg_info) {
                    document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete'.($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
                    document.edit_bulletin_form.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("edit_memo_list")[0].value = 0;
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              }
            }
          }
        });
      }
    }else{

      document.getElementsByName("edit_memo_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_memo_list")[0].value = 0;
    alert("<?php echo TEXT_MEMO_EDIT_MUST_SELECT;?>"); 
  }
}

<?php //全选动作?>
function all_select_memo(bulletin_list_id)
{
  var check_flag = document.edit_bulletin_form.all_check.checked;
  if (document.edit_bulletin_form.elements[bulletin_list_id]) {
    if (document.edit_bulletin_form.elements[bulletin_list_id].length == null) {
      if (check_flag == true) {
        document.edit_bulletin_form.elements[bulletin_list_id].checked = true;
      } else {
        document.edit_bulletin_form.elements[bulletin_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_bulletin_form.elements[bulletin_list_id].length; i++) {
        if (check_flag == true) {
          if(!(document.edit_bulletin_form.elements[bulletin_list_id][i].disabled)){
            document.edit_bulletin_form.elements[bulletin_list_id][i].checked = true;
          }
        } else {
          document.edit_bulletin_form.elements[bulletin_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //选中，取消单选按钮?>
function check_radio_status(r_ele)
{
  var s_radio_value = $("#s_radio").val(); 
  var n_radio_value = $(r_ele).val(); 
  
  if (s_radio_value == n_radio_value) {
    $(".table_img_list input[type='radio']").each(function(){
      $(this).attr("checked", false); 
    });
    $("#s_radio").val(''); 
  } else {
    $("#s_radio").val(n_radio_value); 
  } 
}

<?php //追加个别或所有用户?>
function setting_users(value){

  var users_list = document.getElementsByName("users_id[]");
  var users_list_length = users_list.length;

  if(value == 0){ 
    for(var i=0;i<=users_list_length-1;i++){
    
      document.getElementsByName("users_id[]")[i].disabled = true;
    }
    $("#add_users").attr('disabled',true);
  }else{
    for(var i=0;i<=users_list_length-1;i++){
    
      document.getElementsByName("users_id[]")[i].disabled = false;
    } 
    $("#add_users").attr('disabled',false);
  }
}

<?php //追加选项?>
function add_users_select(ele){

  var users_list = $("#users_list").html();
  var html_str = '';
  html_str += '<tr>';
  html_str += '<td align="left" width="25%" nowrap="nowrap">&nbsp;</td>';
  html_str += '<td align="left" nowrap="nowrap">'+users_list+'</td>';
  html_str += '<td align="left" nowrap="nowrap">&nbsp;</td>'; 
  html_str += '</tr>';
  $(ele).parent().parent().parent().before(html_str);
}

<?php //等待元素隐藏?> 
function read_time(){
    
  $("#wait").hide();
}

<?php //给memo加标识?> 
function change_read(id,user){
  var bulletin_id = document.getElementById("memo_"+id); 
  var bulletin_id_src = bulletin_id.src;
  var bulletin_id_src_array = new Array();
  var flag = 0;
  bulletin_id_src_array = bulletin_id_src.split("/"); 
  if(bulletin_id_src_array[bulletin_id_src_array.length-1] == 'green_right.gif'){

    flag = 1;
  }
  $.ajax({
         type: "POST",
         data: 'id='+id+'&user='+user+'&flag='+flag,
         beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
         async:false,
         url: 'ajax.php?action=read_flag',
         success: function(msg) {
         if(flag == 0){
             bulletin_id.src="images/icons/green_right.gif";
             bulletin_id.title=" <?php echo TEXT_FLAG_CHECKED;?> ";
             bulletin_id.alt="<?php echo TEXT_FLAG_CHECKED;?>";
         }else{
             bulletin_id.src="images/icons/gray_right.gif";
             bulletin_id.title=" <?php echo TEXT_FLAG_UNCHECK;?> ";
             bulletin_id.alt="<?php echo TEXT_FLAG_UNCHECK;?>";
         }
         $('body').css('cursor','');
         setTimeout('read_time()',500);
         }
  }); 
}
<?php //编辑memo信息?>

<?php //编辑memo的上一个，下一个信息?>
function show_link_memo_info(bulletin_id, param_str)
{
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_memo',      
    data: 'bulletin_id='+bulletin_id+'&param_str='+param_str+'<?php echo isset($_GET['order_sort']) ? '&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type'] : '';?>',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}

<?php //隐藏弹出页面?>
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}

<?php //memo内容添加?>
function create_memo_check(c_permission){
  if (c_permission == 31) {
    document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=insert');?>';
    document.create_memo_form.submit();
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
          document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=insert');?>';
          document.create_memo_form.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=insert');?>'),
              async: false,
              success: function(msg_info) {
                document.create_memo_form.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=insert');?>';
                document.create_memo_form.submit();
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
}


<?php //memo内容编辑?>
function edit_memo_check(c_permission){
  if (c_permission == 31) {
    document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=save');?>';
    document.edit_memo.submit();
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
          document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=save');?>';
          document.edit_memo.submit();
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=save');?>'),
              async: false,
              success: function(msg_info) {
                document.edit_memo.action = '<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=save');?>';
                document.edit_memo.submit();
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
}


<?php //新建bulletin?>
function create_bulletin(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax_bulletin_board.php?action=new_bulletin',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
      setting_users(0);
    }
  }); 
}


<?php //改变收藏状态 ?>
function change_collect_status(id){
  var bulletin_id = document.getElementById("bulletin_board_collect_"+id);
  var bulletin_id_src = bulletin_id.src;
  var falg=0;
  if (bulletin_id_src.match("green")){
	  bulletin_id.src='images/icons/gray_right.gif';
  }else {
	  bulletin_id.src='images/icons/green_right.gif';
	  flag=1;
  }
  $.ajax({
    url: 'ajax_bulletin_board.php?action=change_collect_status',      
    data: 'id='+id+"&collect="+flag,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
	if(data!="")alert(data);
    }
  }); 
}


var messages_checked_event_delete_to = '';
var messages_checked_event_add_to = '';
function checkbox_event(obj,event){
   if(!$('#message_to_all').attr('checked')){
	var is_checked = 0;
	
	if(event.ctrlKey && event.which == 1){
		if($(obj).parent().attr('id') == 'delete_to'){
			messages_checked_event_delete_to = $(obj);
		}else{
			messages_checked_event_add_to = $(obj);
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
					if($(this).children().attr('value') == messages_checked_event_add_to.children().attr('value')){
						return false;
					}
				});
				messages_checked_event_add_to.css('background','blue');
				messages_checked_event_add_to.css('color','#FFF');
				messages_checked_event_add_to.children().attr('checked',true);
				if(m >= o){
					messages_checked_event_add_to.prevUntil($(obj)).css('background','blue');
					messages_checked_event_add_to.prevUntil($(obj)).css('color','#FFF');
					messages_checked_event_add_to.prevUntil($(obj)).children().attr('checked',true);
				}else{
					messages_checked_event_add_to.nextUntil($(obj)).css('background','blue');
					messages_checked_event_add_to.nextUntil($(obj)).css('color','#FFF');
					messages_checked_event_add_to.nextUntil($(obj)).children().attr('checked',true);
				}
			}
		}
	}else{
		if($(obj).parent().attr('id') == 'delete_to'){
			messages_checked_event_delete_to = $(obj);
		}else{
			messages_checked_event_add_to = $(obj);
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

function add_select_user(num){
	var obj;
	switch(num){
		case 0:obj="#group_add";break;
		case 1:obj="#user_add";break;
	}
	$('input[name=all_staff]').each(function() {	
		if ($(this).attr("checked")) {
		 	$(obj).append('<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'+$(this).parent().attr("value")+'"><input value="'+this.value+'" type="hidden" name="selected_staff[]">'+$(this).parent().attr("value")+'</div>');
			$(this).parent().remove();	
		}
	});
}

function delete_select_user(num){
	var obj;
	switch(num){
		case 0:obj="#group_delete_to";break;
		case 1:obj="#user_delete_to";break;
	}
	$('input[name="selected_staff[]"]').each(function() {	
		if ($(this).attr("checked")) {
		 	$(obj).append('<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'+$(this).parent().attr("value")+'"><input hidden value="'+this.value+'" type="checkbox" name="all_staff" >'+$(this).parent().attr("value")+'</div>');
			$(this).parent().remove();	
		}
	});
}

function select_allow(obj,num){
	switch(num){
		case 0:
			document.getElementById("select_user").style.display ="none";
			document.getElementById("select_group").style.display ="none";
			document.getElementById("select_all_radio").checked=true;
			document.getElementById("select_group_radio").checked=false;
			document.getElementById("select_id_radio").checked=false;
			break;
		case 1:
			document.getElementById("select_user").style.display ="none";
			document.getElementById("select_group").style.display ="inline";
			document.getElementById("select_all_radio").checked=false;
			document.getElementById("select_group_radio").checked=true;
			document.getElementById("select_id_radio").checked=false;
			break;
		case 2:
			document.getElementById("select_user").style.display ="inline";
			document.getElementById("select_group").style.display ="none";
			document.getElementById("select_all_radio").checked=false;
			document.getElementById("select_group_radio").checked=false;
			document.getElementById("select_id_radio").checked=true;
			break;
	}
	if(tempradio== obj){
		tempradio.checked=false;  
		tempradio=null;
		alert("onclick");
	}else{
		alert(obj);
		obj = checkedRadio;
	}  
}

function add_email_file(b_id){
  var index = 0;
  var last_id = b_id;
  $("input[name='"+b_id+"[]']").each( 
      function(){
      index++;
      last_id = $(this).attr('id');
      }
      );
  var new_id = b_id+'_'+index;
  var add_div_str = '<div id="'+new_id+'_boder"><input type="file" id="'+new_id+'" name="'+b_id+'[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\''+new_id+'\')">'+'<?php echo DELETE_STAFF;?>'+'</a>';
  $('#'+last_id+'_boder').after(add_div_str);
}


function file_cancel(obj){
	$('#'+obj).attr('value','');
        if(obj!='messages_file'&&obj!='messages_file_back'){
	  $('#'+obj+'_boder').remove();
        }
}

function edit_bulletin(id){
  $.ajax({
    url: 'ajax_bulletin_board.php?action=edit_bulletin',      
    data: 'bulletin_id='+id,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
      setting_users(0);
    }
  }); 
}

</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="60%" class="pageHeading"><?php echo NETWORK_GAME_NEWS; ?></td>
			<form method="get" action="bulletin_board.php">
			<td width="30%" align="right"><input type="text" id="search_text" name="search_text"></td>
			<td width="10%" align="left">
				<input type="submit" value="<?php echo SEARCH;?>">
				<input type="hidden" name="action" value="search">
			</td>
			</form>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
<?php
  $site_query = tep_db_query("select id from ".TABLE_SITES);
  $site_list_array = array();
  while($site_array = tep_db_fetch_array($site_query)){

    $site_list_array[] = $site_array['id'];
  }
  
  tep_db_free_result($site_query);
  echo tep_new_site_filter(FILENAME_BULLETIN_BOARD,false,$site_list_array);
?>
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="memo_list_box">
          <tr>
            <td valign="top">
<?php
  echo tep_draw_form('edit_bulletin_form',FILENAME_BULLETIN_BOARD, '', 'post');
  $bulletin_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => ''); 
  $notice_box = new notice_box('', '', $bulletin_table_params); 
  $bulletin_table_row = array();
  $bulletin_title_row = array();
                  
  //bulletin列表  
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_memo(\'bulletin_list_id[]\');" name="all_check"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=icon&order_type='.($_GET['order_sort'] == 'icon' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_COLLECT.($_GET['order_sort'] == 'icon' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'icon' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=read&order_type='.($_GET['order_sort'] == 'read' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MARK.($_GET['order_sort'] == 'read' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'read' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=from&order_type='.($_GET['order_sort'] == 'from' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TITLE.($_GET['order_sort'] == 'from' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'from' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=to&order_type='.($_GET['order_sort'] == 'to' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MANAGER.($_GET['order_sort'] == 'to' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'to' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=content&order_type='.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TO.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=date&order_type='.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_REPLY_NUMBER.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=date&order_type='.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_UPDATE_TIME.($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'date' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('align' => 'left','params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=action&order_type='.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TABLE_HEADING_ACTION.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
                    
  $bulletin_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $bulletin_title_row);   

  //获取图标信息
  $icon_list_array = array();
  $icon_query = tep_db_query("select id,pic_name,pic_alt from ". TABLE_CUSTOMERS_PIC_LIST);
  while($icon_array = tep_db_fetch_array($icon_query)){

    $icon_list_array[$icon_array['id']] = array('name'=>$icon_array['pic_name'],'alt'=>$icon_array['pic_alt']);
  }
  tep_db_free_result($icon_query);

  if(isset($_GET['order_sort']) && $_GET['order_sort'] != '' && isset($_GET['order_type']) && $_GET['order_type'] != ''){
    switch($_GET['order_sort']){

    case 'date':
      $order_sort = 'date_added';
      $order_type = $_GET['order_type'];
      break;
    case 'content':
      $order_sort = 'contents';
      $order_type = $_GET['order_type'];
      break;
    case 'to':
      $order_sort = '`to`';
      $order_type = $_GET['order_type'];
      break;
    case 'from':
      $order_sort = '`from`';
      $order_type = $_GET['order_type'];
      break;
    case 'read':
      $order_sort = 'read_flag';
      $order_type = $_GET['order_type'];
      break;
    case 'icon':
      $order_sort = 'icon';
      $order_type = $_GET['order_type'];
      break;
    case 'action':
      $order_sort = 'date_update';
      $order_type = $_GET['order_type'];
      break;
    }
  }else{
    $order_sort = 'id';
    $order_type = ''; 
  }
  $bulletin_query_str = $ocertify->npermission == 31 ? '' : "where (`author`='".$ocertify->auth_user."' or `manager`='".$ocertify->auth_user."' or `allow` like '".$ocertify->auth_user.",%' or `allow` like '%,".$ocertify->auth_user.",%' or `allow` like '%,".$ocertify->auth_user."') and ";
  $bulletin_query_raw = "select * from " . TABLE_BULLETIN_BOARD .$bulletin_query_str."  order by ".$order_sort." ".$order_type;
  $bulletin_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $bulletin_query_raw, $bulletin_query_numrows);
  $bulletin_query = tep_db_query($bulletin_query_raw);
  if(tep_db_num_rows($bulletin_query) == 0){
    $bulletin_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
    $bulletin_table_row[] = array('params' => '', 'text' => $bulletin_data_row);  
  }
  while ($bulletin = tep_db_fetch_array($bulletin_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $bulletin['id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($bulletin);
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($bulletin['id'] == $cInfo->id) ) {
      $bulletin_item_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $bulletin_item_params = '<tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $bulletin_item_info = array();  
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="bulletin_list_id[]"  value="'.$bulletin["id"].'">'   
                          );

	//收藏
	$collect_status = $bulletin['collect']==0 ? '<img onclick="change_collect_status('.$bulletin['id'].')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/gray_right.gif" border="0">': '<img onclick="change_collect_status('.$bulletin['id'].')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/green_right.gif" border="0">';
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $collect_status
						  );

    $read_flag_str_array = array();
    $read_flag_str_array = explode(',',$bulletin['read_flag']);
    if($bulletin['read_flag'] == ''){
      if($bulletin['finished'] == 0 && $site_permission_flag == true){
        $bulletin_read = '<a onclick="change_read(\''.$bulletin['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>'; 
      }else{

        $bulletin_read = '<img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif">';
      }
    }else{

      if(in_array($ocertify->auth_user,$read_flag_str_array)){

        if($bulletin['finished'] == 0 && $site_permission_flag == true){
          $bulletin_read = '<a onclick="change_read(\''.$bulletin['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif"></a>';
        }else{

          $bulletin_read = '<img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif">';
        }
      }else{

        if($bulletin['finished'] == 0 && $site_permission_flag == true){
          $bulletin_read = '<a onclick="change_read(\''.$bulletin['id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>';
        }else{

          $bulletin_read = '<img id="memo_'.$bulletin['id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif">';
        }
      }
    }    
	$mark_html = '';
	if($bulletin['mark'] != ''){
		$mark_array = explode(',',$bulletin['mark']);
		foreach($mark_array as $value){
			$mark_handle = strlen($value)> 1 ? $value : '0'.$value;
			$mark_html .= '<img src="images/icon_list/icon_'.$mark_handle.'.gif" border="0 alt="'.$icon_list_array[$value].'"title="'.$icon_list_array[$value].'">';
		}
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $mark_html 
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $bulletin['title'] 
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&cID=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['manager']
                        );
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&cID=' . $bulletin['id']) . '\'"', 
                          'text' => str_replace(",","  ",$bulletin['allow'])
			);

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&cID=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['reply_number']
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&cID=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['update_time'] 
                        );

    $bulletin_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
						  'text' => '<a id="m_696" onclick="edit_bulletin('.$bulletin["id"].')" href="javascript:void(0)"><img border="0" title=" 2014/07/23 14:43:47 " alt="2014/07/23 14:43:47" src="images/icons/info_blink.gif"></a>'
                          ); 
                      
    $bulletin_table_row[] = array('params' => $bulletin_item_params, 'text' => $bulletin_item_info);

  }

  $form_str = tep_draw_form('bulletin_list', FILENAME_BULLETIN_BOARD, tep_get_all_get_params(array('action')).'action=del_select_bulletin');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($bulletin_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                  <td class="smallText" valign="top">
                  <?php
                  if($ocertify->npermission >= 15 && tep_db_num_rows($bulletin_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select name="edit_bulletin_list" onchange="select_bulletin_change(this.value,\'bulletin_list_id[]\',\''.$ocertify->npermission.'\');"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>';
                    echo '<option value="0">'.TEXT_BULLETIN_EDIT_SELECT.'</option>';
                    echo '<option value="1" >'.TEXT_BULLETIN_EDIT_DELETE.'</option>';
                    echo '</select>';
                    echo '</div>';
                  }
                  ?>
                  </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $bulletin_split->display_count($bulletin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BULLETIN_BOARD); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $bulletin_split->display_links($bulletin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('x', 'y', 'page'))); ?></div></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php echo '<a href="javascript:void(0);" onclick="back(this);">' .tep_html_element_button(TEXT_BACK,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>';echo '<a href="javascript:void(0);" onclick="create_bulletin(this);">' .tep_html_element_button(IMAGE_NEW_PROJECT,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>'; ?></div></td>
                  </tr>
          </table>
	  </td>
          </tr>
        </table></form></td>
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
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
