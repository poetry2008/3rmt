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
   case 'delete' 删除选中的bulletin 或回复
------------------------------------------------------*/
	case 'create_bulletin':
	 $id_raw=tep_db_query("select id from bulletin_board ");
	 while($id_row=tep_db_fetch_array($id_raw)){
		 if($id<$id_row['id'])$id=$id_row['id'];
	 }
	 $id+=1;
	 $author=$ocertify->auth_user;
	 $update_author=$author;
	 $content=$_POST['content'];
	 $collect=0;
	 $allow="";
	 if($_POST['select_all'])$allow="all";
	 if($_POST['select_group'])$allow="group:";
	 if($_POST['select_id'])$allow="id:";
	foreach($_POST['selected_staff'] as $value){
		if(strlen($allow)>6)$allow.=",".$value;
		else $allow.=$value;
	}
	 $manager=$_POST['manager'];
	 $mark="";
	 foreach($_POST['pic_icon'] as $icon){
		 if(strlen($mark)){
			 $mark.=",".$icon;
		 }else{
			 $mark.=$icon;
		 }
	 }
	 $reply_number=1;
	 $title=$_POST['title'];
	 $file_path="";
	 foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 $file_name=$_FILES['bulletin_file']['name'][$fk];
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 $file_name=date("Ymd_").$file_name;
		 $file_path.=$file_name;
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name);
	 }
		$bulletin_sql="insert into ".TABLE_BULLETIN_BOARD." values($id,'$author','$content',now(),'$allow','$manager','$mark',$collect,$reply_number,now(),'$title','$file_path','$update_author')";
		$rid_row=tep_db_fetch_array(tep_db_query("select id from ".TABLE_BULLETIN_BOARD_REPLY." order by id desc limit 1"));
		$rid=$rid_row['id']+1;
		$bulletin_reply_sql="insert into ".TABLE_BULLETIN_BOARD_REPLY."  values($rid,$id,'$author',now(),'$content','$author','$mark','$file_path',now(),0)";
		tep_db_query($bulletin_sql);
		tep_db_query($bulletin_reply_sql);
		//添加提醒和日志
		$nid_raw=tep_db_fetch_array(tep_db_query("select id from notice order by id desc limit 1"));
		$nid=$nid_raw['id'] + 1;
		tep_db_query("insert into notice values($nid,1,'$title',now(),$id,'$author',now(),1,'')");
		$parm=isset($_GET['order_sort'])?'order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		if(isset($_GET['page']))$parm.="&page=".$_GET['page'];
		tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,$parm));
	 break;


	case 'update_bulletin':
	 $id=$_GET['bulletin_id'];
	 $bulletin_info_raw=tep_db_query("select * from bulletin_board where id=$id");
	 $bulletin_info_row=tep_db_fetch_array($bulletin_info_raw);
	 $update_author=$ocertify->auth_user;
	 if($update_author!=$bulletin_info_row['manager']&&$ocertify->npermission<15&&$update_author!=$bulletin_info_row['author']){
		tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD));
			 }
	 $content=$_POST['content'];
	 $collect=0;
	 $allow="";
	 if($_POST['select_all'])$allow="all";
	 if($_POST['select_group'])$allow="group:";
	 if($_POST['select_id'])$allow="id:";
	foreach($_POST['selected_staff'] as $value){
		if(strlen($allow)>6)$allow.=",".$value;
		else $allow.=$value;
	}
	 $manager=$_POST['manager'];
	 $mark="";
	 foreach($_POST['pic_icon'] as $icon){
		 if(strlen($mark)){
			 $mark.=",".$icon;
		 }else{
			 $mark.=$icon;
		 }
	 }
	 $title=$_POST['title'];
	 $file_path=$bulletin_info_row['file_path'];
	 if($_POST['delete_file']){
		 foreach($_POST['delete_file'] as $value){
			 if($value!=''){
				 $file_path=str_replace("$value","",$file_path);
				 $file_path=str_replace("||||||","|||",$file_path);
				 if(substr($file_path,0,3)=="|||")$file_path=substr($file_path,3);
				 if(substr($file_path,-3,3)=="|||")$file_path=substr($file_path,0,-3);
				 unlink(PATH_BULLETIN_BOARD_UPLOAD.$value);
			 }
		 }
	 }
	 foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 $file_name=$_FILES['bulletin_file']['name'][$fk];
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 $file_name=date("Ymd").$file_name;
		 $file_path.=$file_name;
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	 }
	 $bulletin_sql="update  ".TABLE_BULLETIN_BOARD." set update_author='$update_author',content='$content',allow='$allow',title='$title',mark='$mark',file_path='$file_path',manager='$manager',update_time=now() where id=$id";
		tep_db_query($bulletin_sql);
		$page=isset($_GET['page'])?'page='.$_GET['page']:1;
		$parm=isset($_GET['order_sort'])?'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,$page.$parm));
	 break;

	case 'create_bulletin_reply':
	 $bulletin_id=$_GET['bulletin_id'];
	 $id_sql='select id from '.TABLE_BULLETIN_BOARD_REPLY.' order by id desc limit 1';
	 $id_raw=tep_db_query($id_sql);
	 $id_row=tep_db_fetch_array($id_raw);
	 $id=$id_row['id']+1;
	 $content=$_POST['content'];
	 $title=mb_strlen($content) > 30 ? mb_substr($content, 0, 30).'...' : $content;
	 $mark="";
	 foreach($_POST['pic_icon'] as $value){
		 if(strlen($mark)<1)$mark.=$value;
		 else $mark.=",".$value;
	 }
	 $name=$ocertify->auth_user;
	 $author_row=tep_db_fetch_array(tep_db_query('select * from '.TABLE_BULLETIN_BOARD.' where id='.$bulletin_id.' limit 1'));
	 $author=$author_row['author'];
	 $file_path="";
	 foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 $file_name=$_FILES['bulletin_file']['name'][$fk];
		 $file_name=$file_name[1];
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 $file_name=date("Ymd").$file_name;
		 $file_path.=$file_name;
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	 }
	 $bulletin_reply_sql="insert into ".TABLE_BULLETIN_BOARD_REPLY." values($id,$bulletin_id,'$author',now(),'$content','$name','$mark','$file_path',now(),0)";
	 if(tep_db_query($bulletin_reply_sql))tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number+1 where id=$bulletin_id");
	//添加提醒和日志
	 $nid_raw=tep_db_fetch_array(tep_db_query("select id from notice order by id desc limit 1"));
	 $nid=$nid_raw['id'] + 1;
	 tep_db_query("insert into notice values($nid,2,'$title',now(),$id,'$name',now(),1,'')");
		$page=isset($_GET['page'])?'page='.$_GET['page']:1;
		$parm=isset($_GET['order_sort'])?'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		 tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,"action=show_reply&bulletin_id=$bulletin_id&".$page.$parm));
	 break;
	 case 'update_bulletin_reply':
	 $id=$_GET['id'];
	 $bulletin_info_row=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id"));
	 $content=$_POST['old_content'];
	 $content=str_replace("\n","\n>",$content);
	 $content= $_POST['content']==''?'>'.$content : $_POST['content'].'\n>'.$content;
	 $mark="";
	 foreach($_POST['pic_icon'] as $value){
		 if(strlen($mark)<1)$mark.=$value;
		 else $mark.=",".$value;
	 }
	 $author=$bulletin_info_row['author'];
	 $update_author=$ocertify->auth_user;
	 $file_path=$bulletin_info_row['file_path'];
	 if($_POST['delete_file']){
		 foreach($_POST['delete_file'] as $value){
			 if($value!=''){
				 $file_path=str_replace("$value","",$file_path);
				 $file_path=str_replace("||||||","|||",$file_path);
				 if(substr($file_path,0,3)=="|||")$file_path=substr($file_path,3);
				 if(substr($file_path,-3,3)=="|||")$file_path=substr($file_path,0,-3);
				 unlink(PATH_BULLETIN_BOARD.$value);
			 }
		 }
	 }
	 foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 $file_name=$_FILES['bulletin_file']['name'][$fk];
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 $file_name=date("Ymd").$file_name;
		 $file_path.=$file_name;
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	 }
	 $id_row=tep_db_fetch_array(tep_db_query("select id from ".TABLE_BULLETIN_BOARD_REPLY." order by id desc limit 1"));
	 $id=$id_row['id']+1;
	 $bulletin_id=$bulletin_info_row['bulletin_id'];
	 $bulletin_reply_sql="insert into ".TABLE_BULLETIN_BOARD_REPLY." values($id,$bulletin_id,'$author',now(),'$content','$update_author','$mark','$file_path',now(),0)";
	 if(tep_db_query($bulletin_reply_sql))tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number+1 where id=$bulletin_id");
	 $bulletin_id=$bulletin_info_row['bulletin_id'];
	 $page=isset($_GET['page'])?$_GET['page']:1;
	 $parm=isset($_GET['order_sort'])?'order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
	 tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,"action=show_reply&bulletin_id=$bulletin_id&page=$page&$parm"));
	 break;
	  
	 case 'search':
		$search_text=$_GET['search_text'];
		$_GET['action']=$_GET['search_type']=='show_reply'?'show_reply':'';
		break;


      case 'delete':
		if(isset($_GET['id']))$bulletin_id_list[]=$_GET['id'];
		else $bulletin_id_list = tep_db_prepare_input($_POST['bulletin_list_id']);
        $param_str = $_GET['page'];
        foreach($bulletin_id_list as $id){
         if($_GET['delete_type']=='show_reply' && (tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id and (author='$ocertify->auth_user'or update_author='$ocertify->auth_user')"))>0|| $ocertify->npermission>=15)) {
			 $reply_number_row=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id"));
			 tep_db_query("delete from ".TABLE_BULLETIN_BOARD_REPLY." where id=".$id);
			 $bulletin_id=$reply_number_row['bulletin_id'];
			 tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number-1 where id=$bulletin_id");
			 tep_db_query("delete from ".TABLE_NOTICE." where from_notice=$id and type=2");
		 }
		 else if(tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where id=$id and (author='$ocertify->auth_user' or manager='$ocertify->auth_user')"))>0|| $ocertify->npermission>=15 ){
			 tep_db_query("delete from ".TABLE_BULLETIN_BOARD." where id=".$id);
			 tep_db_query("delete from notice where (from_notice=$id and type=1) or (from_notice in (select id from ".TABLE_BULLETIN_BOARD_REPLY." where bulletin_id=$id) and type=2)");
			 tep_db_query("delete from ".TABLE_BULLETIN_BOARD_REPLY." where bulletin_id=".$id);
		 }
        }
        if($_GET['delete_type']=='show_reply')tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, 'action=show_reply&bulletin_id='.$_GET['bulletin_id'].'&page='.$param_str));
		else tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, 'page='.$param_str));
        break;
      case 'end':
        $bulletin_id = $_GET['end_id'];
        $param_str = tep_db_prepare_input($_POST['param_str']);

        tep_db_query("update " . TABLE_NOTICE . " set is_show='0' where from_notice = '" . tep_db_input($bulletin_id) . "' and type='1'");
        tep_db_query("update " . TABLE_BUSINESS_MEMO . " set finished='1',user_update='".$_SESSION['user_name']."',date_update=now() where id = '" . tep_db_input($bulletin_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, $param_str));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_BULLETIN_BOARD; ?></title>
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
    if (confirm('<?php echo TEXT_BULLETIN_EDIT_CONFIRM;?>')) {
      if (c_permission == 31) {
        document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete&delete_type='.$_GET['action'].($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
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


<?php //编辑bulletin的上一个，下一个信息?>
function show_link_bulletin_info(bulletin_id)
{
  $.ajax({
    url: 'ajax_bulletin_board.php?action=edit_bulletin',      
    data: 'bulletin_id='+bulletin_id,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}

<?php //编辑bulletin_reply的上一个，下一个信息?>
function show_link_reply_info(id,bulletin_id)
{
  $.ajax({
    url: 'ajax_bulletin_board.php?action=edit_bulletin_reply',      
    data: 'bulletin_id='+bulletin_id+"&id="+id,
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
<?php //新建bulletin?>
function create_bulletin(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax_bulletin_board.php?action=new_bulletin&order_sort=<?php echo $_GET["order_sort"];?>&order_type=<?php echo $_GET["order_type"];?>',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
	  $('#show_popup_info').css('top',$('#bulletin_list_box').position().top).show();
      $('#show_popup_info').show(); 
      setting_users(0);
    }
  }); 
}


<?php //改变收藏状态 ?>
function change_collect_status(id,user){
  var bulletin_id = document.getElementById("bulletin_board_collect_"+id);
  var bulletin_id_src = bulletin_id.src;
  var flag=0;
  var type="<?php echo $_GET['action'];?>";
  if (bulletin_id_src.match("green")){
	  bulletin_id.src='images/icons/gray_right.gif';
  }else {
	  bulletin_id.src='images/icons/green_right.gif';
	  flag=1;
  }
  $.ajax({
    url: 'ajax_bulletin_board.php?action=change_collect_status',      
    data: 'id='+id+"&collect="+flag+"&collect_type="+type+"&user_id="+user,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
	if(data!="");
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
	}else{
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
        if(obj!='bulletin_file'){
	  $('#'+obj+'_boder').remove();
        }
}

function edit_bulletin(obj,id){
  var tmp =obj;
  obj = obj.parentNode;
  origin_offset_symbol = 1;
  $.ajax({
	url: 'ajax_bulletin_board.php?action=edit_bulletin<?php echo isset($_GET['order_sort'])?'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';?><?php echo isset($_GET['page'])?'&page='.$_GET['page']:'';?>',      
    data: 'bulletin_id='+id+'&obj='+tmp,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-obj.offsetTop) < obj.offsetTop) {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      o_submit_single = true;
    }
  }); 
}


function create_bulletin_reply(obj,id){
  $.ajax({
    url: 'ajax_bulletin_board.php?action=new_bulletin_reply<?php echo "&page=".$_GET['page']."&order_sort=".$_GET['order_sort']."&order_type=".$_GET['order_type'];?>',      
    data: 'bulletin_id='+id,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
	  $('#show_popup_info').css('top',$('#bulletin_list_box').position().top).show();
      $('#show_popup_info').show(); 
      setting_users(0);
    }
  }); 
}

function reply_bulletin(obj,id,bulletin_id){
  obj = obj.parentNode;
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax_bulletin_board.php?action=edit_bulletin_reply<?php echo "&page=".$_GET['page']."&order_sort=".$_GET['order_sort']."&order_type=".$_GET['order_type'];?>',      
    data: 'id='+id,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-obj.offsetTop) < obj.offsetTop) {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = obj.offsetTop+$('#bulletin_list_box').position().top+obj.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      o_submit_single = true;
    }
  }); 
}

function delete_file(id,file_name){
	var str='<?php echo TEXT_DELETE;?>';
	str+="?";
	if(confirm(str)){
		var html_str='<input type="hidden" name="delete_file[]" value="';
		html_str+=file_name;
		html_str+='">';
		$("#"+id).html(html_str);
	}
}

function check_value(type){
	var flag=0;
	if($("#current_contents").val()==''){
		document.getElementById("popup_content").style.display="inline";
		flag=1;
	}
	if(type==0 && $("#bulletin_title").val()==''){
		document.getElementById("popup_title").style.display="inline";
		flag=1;
	}
	if(flag==1)return false;
	else return true;
}

function bulletin_board_select(id,type){
	var str='<?php echo FILENAME_BULLETIN_BOARD;?>'+'?';
	if(type==1)str+='action=show_reply&bulletin_id=<?php echo $_GET["bulletin_id"];?>&';
	str+='c_id='+id+'<?echo isset($_GET["page"])?"&page=".$_GET["page"]:"";?>';
	window.location.href=str;
}


function delete_bulletin(id,type){
	if(confirm('<?php echo TEXT_BULLETIN_EDIT_CONFIRM; ?>'))
    window.location.href='bulletin_board.php?action=delete&delete_type='+type+'&id='+id+'&bulletin_id=<?php echo $_GET['bulletin_id'];?>';
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
<?php
	//设置标题
	$group_raw=tep_db_fetch_array(tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user') limit 1"));
	$group_name=$group_raw['name'];
	$header_title_sql="select * from bulletin_board where (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))";
	$last_id_sql="select * from bulletin_board where id>0 ";
	$last_id_sql.=$ocertify->npermission <15 ? " and (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow  like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))":"";	
	$next_id_sql=$last_id_sql;
	if(isset($_GET['bulletin_id']) && $_GET['action']=='show_reply'){
		if($_GET['bulletin_id']<1)$_GET['bulletin_id']=1;
		if($_GET['from']=='last')$header_title_sql.=" and id >=".$_GET['bulletin_id']." order by id asc";
		else $header_title_sql.=" and id <=".$_GET['bulletin_id']." order by id desc";
		$header_title_sql.= " limit 1";
		$header_title_raw=tep_db_query($header_title_sql);
		$header_title_row=tep_db_fetch_array($header_title_raw);
		$header_id=$header_title_row['id'];
		$header_content=$header_title_row['title'];
		$last_id_row=tep_db_fetch_array(tep_db_query($last_id_sql." and id > $header_id order by id asc limit 1"));
		$next_id_row=tep_db_fetch_array(tep_db_query($next_id_sql." and id < $header_id order by id desc limit 1"));
		$last_id=$last_id_row['id'];
		$next_id=$next_id_row['id'];
		$header_title_html='';
		if($last_id&&tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name'))) and id>=".$last_id))!=0)$header_title_html.='<a href="bulletin_board.php?action=show_reply&bulletin_id='.$last_id.'&from=last"><img src="images/icons/icon_last.gif" title="'.TEXT_LAST_BULLETIN.'" alt="'.TEXT_LAST_BULLETIN.'"></a>';
		if($next_id>0&&tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name'))) and id<=".$next_id))!=0)$header_title_html.='<a href="bulletin_board.php?action=show_reply&bulletin_id='.$next_id.'&from=next" ><img src="images/icons/icon_next.gif" title="'.TEXT_NEXT_BULLETIN.'" alt="'.TEXT_NEXT_BULLETIN.'"></a>';
		$header_title_html.=$header_content.'';
	}else $header_title_html=TEXT_BULLETIN_BOARD;
?>
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo $header_title_html; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
			<td class="pageHeading" align="right">
			<form method="get" action="bulletin_board.php">
				<input type="text" id="search_text" name="search_text">
				<input type="submit" value="<?php echo SEARCH;?>">
				<input type="hidden" name="action" value="search">
				<input type="hidden" name="bulletin_id" value="<?php echo $_GET['bulletin_id'];?>">
				<input type="hidden" name="search_type" value="<?php echo $_GET['action']=='show_reply'? 'show_reply':'show';?>">
			</form>
			</td>
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
	echo '<div id="tep_new_site_filter"><ul><li><img src="images/icons/common_stiles_gray.gif" alt="シングル・マルチモードの切り替え" title="シングル・マルチモードの切り替え"></li><li id="site_0" title="共用データ"><img src="images/icons/common_whitepoint.gif" alt="共用データ"></li></a><li id="site_1" class="site_filter_unselected" title="RMTジャックポット">jp</li><li id="site_2" class="site_filter_unselected" title="RMTゲームマネー">gm</li><li id="site_3" class="site_filter_unselected" title="RMTワールドマネー">wm</li><li id="site_4" class="site_filter_unselected" title="RMTアイテムデポ">id</li><li id="site_5" class="site_filter_unselected" title="RMTカメズ">rk</li><li id="site_6" class="site_filter_unselected" title="RMT学園">rg</li><li id="site_7" class="site_filter_unselected" title="RedStone-RMT.com">rr</li><li id="site_8" class="site_filter_unselected" title="FF14-RMT.com">14</li><li id="site_9" class="site_filter_unselected" title="RMTゲームプラネット">gp</li><li id="site_10" class="site_filter_unselected" title="GM-Exchange">ge</li><input type="hidden" id="unshow_site_list" value=""></ul></div>'
?>
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" id="bulletin_list_box">
          <tr>
            <td valign="top">
<?php
  echo tep_draw_form('edit_bulletin_form',FILENAME_BULLETIN_BOARD, '', 'post');
  $bulletin_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => ''); 
  $notice_box = new notice_box('', '', $bulletin_table_params); 
  $bulletin_table_row = array();

  // 回复内容处理
  if(isset($_GET['action'])&& $_GET['action']=='show_reply'){
  $bulletin_title_row = array();                
  //bulletin列表  
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_memo(\'bulletin_list_id[]\');" name="all_check"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=collect&order_type='.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_COLLECT.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=mark&order_type='.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MARK.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap" style="width:40%;"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=content&order_type='.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_CONTENT.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=author&order_type='.($_GET['order_sort'] == 'author' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'."From".($_GET['order_sort'] == 'author' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'author' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=update_time&order_type='.($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_UPDATE_TIME.($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('align' => 'left','params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=action&order_type='.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TABLE_HEADING_ACTION.($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'action' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
                    
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

    case 'mark':
      $order_sort = 'order by r.mark';
      $order_type = $_GET['order_type'];
      break;
    case 'content':
      $order_sort = 'order by r.content';
      $order_type = $_GET['order_type'];
      break;
    case 'manager':
      $order_sort = 'order by r.manager';
      $order_type = $_GET['order_type'];
      break;
    case 'title':
      $order_sort = 'order by r.title';
      $order_type = $_GET['order_type'];
      break;
    case 'author':
      $order_sort = 'order by r.author';
      $order_type = $_GET['order_type'];
      break;
    case 'collect':
      $order_sort = 'order by r.collect';
      $order_type = $_GET['order_type'];
      break;
    case 'action':
      $order_sort = 'order by r.update_time';
      $order_type = $_GET['order_type'];
      break;
    case 'update_time':
      $order_sort = 'order by r.update_time';
      $order_type = $_GET['order_type'];
	  break;
    }
  }else{
    $order_sort = 'order by r.id';
    $order_type = 'desc'; 
  }
  $group_raw=tep_db_fetch_array(tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user')"));
  $group_name=$group_raw['name'];
  $bulletin_query_str = 'and r.bulletin_id='.$_GET['bulletin_id']." and(b.author='$ocertify->auth_user' or b.manager='$ocertify->auth_user' or b.allow='all' or b.allow like '%:$ocertify->auth_user' or b.allow like '%:$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user'  or b.allow like '%:$group_name' or b.allow like '%:$group_name,%' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')";
  if(isset($_GET['search_text'])&& $_GET['search_text']){
	  $bulletin_query_str.=" and (r.content like '%".$search_text."%' )";
  }
  $bulletin_query_raw = "select r.update_author update_author, r.id id, r.content content, r.file_path file_path ,r.update_time update_time ,r.author ,r.collect collect ,r.mark mark,r.bulletin_id bulletin_id, b.id bid,b.allow ,b.manager ,b.author from " . TABLE_BULLETIN_BOARD_REPLY ." r ,".TABLE_BULLETIN_BOARD." b where r.bulletin_id=b.id  ".$bulletin_query_str." ";
  if($order_sort=='collect'){
	  $user_collect = $bulletin_query_raw."and r.id in ( select id from ".TABLE_BULLETIN_BOARD_REPLY." where (b.allow='all' or ((b.allow like 'id:%' and( b.allow like '%:$ocertify->auth_user,%' or b.allow like '%:$ocertify->auth_user' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user') ) or (b.allow like 'group:%' and (b.allow like '%:$group_name,%' or b.allow like '%:$group_name' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')))) and (r.collect='$ocertify->auth_user' or r.collect like '$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user'))";
$user_not_collect=$bulletin_query_raw."and r.id not in ( select id from ".TABLE_BULLETIN_BOARD_REPLY." where (b.allow='all' or ((b.allow like 'id:%' and( b.allow like '%:$ocertify->auth_user,%' or b.allow like '%:$ocertify->auth_user' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user') ) or (b.allow like 'group:%' and (b.allow like '%:$group_name,%' or b.allow like '%:$group_name' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')))) and (r.collect='$ocertify->auth_user' or r.collect like '$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user'))";
  if($order_type=='desc')$bulletin_query_raw=$user_collect." union ".$user_not_collect;
  else $bulletin_query_raw=$user_not_collect." union ".$user_collect;
  }else  $bulletin_query_raw .=  " ".$order_sort." ".$order_type;
  $bulletin_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $bulletin_query_raw, $bulletin_query_numrows);
  $bulletin_query = tep_db_query($bulletin_query_raw);
  if(tep_db_num_rows($bulletin_query) == 0){
    $bulletin_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
    $bulletin_table_row[] = array('params' => '', 'text' => $bulletin_data_row);  
  }
  while ($bulletin = tep_db_fetch_array($bulletin_query)) {
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if ($bulletin['id']==$_GET['c_id']) {
      $bulletin_item_params = 'class="dataTableRowSelected"  onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $bulletin_item_params = 'class="'.$nowColor.'"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $bulletin_item_info = array();  
	if($ocertify->npermission <15){
		$select_html='disabled="disabled"';
	}else{
		$select_html='';
		$show_flag=1;
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="bulletin_list_id[]" '.$select_html.' value="'.$bulletin["id"].'">'   
                          );

	//收藏
	$collect_status = in_array($ocertify->auth_user,explode(",",$bulletin['collect']))? '<img onclick="change_collect_status('.$bulletin['id'].',\''.$ocertify->auth_user.'\')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/green_right.gif" border="0">': '<img onclick="change_collect_status('.$bulletin['id'].',\''.$ocertify->auth_user.'\')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/gray_right.gif" border="0">';
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $collect_status
						  );
	$mark_html = '';
	if($bulletin['mark'] != ''){
		$mark_array = explode(',',$bulletin['mark']);
		foreach($mark_array as $value){
			$mark_handle = strlen($value)> 1 ? $value : '0'.$value;
			$mark_html .= '<img src="images/icon_list/icon_'.$mark_handle.'.gif" border="0 alt="'.$icon_list_array[$value].'"title="'.$icon_list_array[$value].'">';
		}
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $mark_html 
                        );
	$title=explode(">",$bulletin["content"]);
	$title=$title[0];
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  width="300px" onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => '<p style="max-height:36px;overflow:hidden;margin:0px 0px 0px 0px " title="'.$title.'">'.$title.'</p>'
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $bulletin['update_author']
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $bulletin['update_time'] 
                        );

    $bulletin_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
						  'text' => '<a id="m_696" onclick="reply_bulletin(this,'.$bulletin["id"].','.$bulletin["bulletin_id"].')" href="javascript:void(0)">'.tep_get_signal_pic_info($bulletin['update_time']).'</a>'
                          ); 
                      
    $bulletin_table_row[] = array('params' => $bulletin_item_params, 'text' => $bulletin_item_info);

  }

  $form_str = tep_draw_form('bulletin_list', FILENAME_BULLETIN_BOARD, tep_get_all_get_params(array('action')).'action=del_select_bulletin');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($bulletin_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
  }else{
  $bulletin_title_row = array();                
  //bulletin列表  
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_memo(\'bulletin_list_id[]\');" name="all_check"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=collect&order_type='.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_COLLECT.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=mark&order_type='.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MARK.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=title&order_type='.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TITLE.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=manager&order_type='.($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MANAGER.($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=allow&order_type='.($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TO.($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=reply_number&order_type='.($_GET['order_sort'] == 'reply_number' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_REPLY_NUMBER.($_GET['order_sort'] == 'reply_number' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'reply_number' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=update_time&order_type='.($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_UPDATE_TIME.($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'update_time' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
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

    case 'mark':
      $order_sort = 'mark';
      $order_type = $_GET['order_type'];
      break;
    case 'content':
      $order_sort = 'content';
      $order_type = $_GET['order_type'];
      break;
    case 'manager':
      $order_sort = 'manager';
      $order_type = $_GET['order_type'];
      break;
    case 'title':
      $order_sort = 'title';
      $order_type = $_GET['order_type'];
      break;
    case 'author':
      $order_sort = 'author';
      $order_type = $_GET['order_type'];
      break;
    case 'collect':
      $order_sort = 'collect';
      $order_type = $_GET['order_type'];
      break;
    case 'action':
      $order_sort = 'update_time';
      $order_type = $_GET['order_type'];
      break;
    }
  }else{
    $order_sort = 'id';
    $order_type = 'desc'; 
  }

  $group_raw=tep_db_fetch_array(tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user')"));
  $group_name=$group_raw['name'];
	
  $bulletin_query_str = $ocertify->npermission == 31 ? ' where id>0 ' : " where (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))";
  if(isset($_GET['action'])&& $_GET['search_text']){
	  $bulletin_query_str.=" and (content like '%".$search_text."%' or title like '%".$search_text."%')";
  }
  $bulletin_query_raw = "select * from " . TABLE_BULLETIN_BOARD .$bulletin_query_str;
  if($order_sort=='collect'){
	  $user_collect = $bulletin_query_raw."and id in ( select id from ".TABLE_BULLETIN_BOARD." where (allow='all' or ((allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))) and (collect='$ocertify->auth_user' or collect like '$ocertify->auth_user,%' or collect like '%,$ocertify->auth_user,%' or collect like '%,$ocertify->auth_user'))";
$user_not_collect=$bulletin_query_raw."and id not in ( select id from ".TABLE_BULLETIN_BOARD." where (allow='all' or ((allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))) and (collect='$ocertify->auth_user' or collect like '$ocertify->auth_user,%' or collect like '%,$ocertify->auth_user,%' or collect like '%,$ocertify->auth_user'))";
  if($order_type=='desc')$bulletin_query_raw=$user_collect." union ".$user_not_collect;
  else $bulletin_query_raw=$user_not_collect." union ".$user_collect;
  }else  $bulletin_query_raw .=  "  order by ".$order_sort." ".$order_type;
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

    if ($bulletin['id']==$_GET['c_id']||$bulletin['id']==$_GET['bulletin_id']) {
      $bulletin_item_params = 'class="dataTableRowSelected"   onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $bulletin_item_params = 'class="'.$nowColor.'"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
	if(($ocertify->auth_user!=$bulletin["manager"])&&($ocertify->auth_user!=$bulletin["author"])&&($ocertify->npermission <15)){
		$select_html='disabled="disabled"';
	}else{
		$select_html='';
		$show_flag=1;
	}
    $bulletin_item_info = array();  
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="bulletin_list_id[]" '.$select_html.' value="'.$bulletin["id"].'">'   
                          );

	//收藏
	$collect_status =in_array($ocertify->auth_user,explode(",",$bulletin['collect']))? '<img onclick="change_collect_status('.$bulletin['id'].',\''.$ocertify->auth_user.'\')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/green_right.gif" border="0">': '<img onclick="change_collect_status('.$bulletin['id'].',\''.$ocertify->auth_user.'\')" id="bulletin_board_collect_'.$bulletin['id'].'" src="images/icons/gray_right.gif" border="0">';
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $collect_status
						  );
	$mark_html = '';
	if($bulletin['mark'] != ''){
		$mark_array = explode(',',$bulletin['mark']);
		foreach($mark_array as $value){
			$mark_handle = strlen($value)> 1 ? $value : '0'.$value;
			$mark_html .= '<img src="images/icon_list/icon_'.$mark_handle.'.gif" border="0 alt="'.$icon_list_array[$value].'"title="'.$icon_list_array[$value].'">';
		}
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $mark_html 
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="bulletin_board.php?action=show_reply&bulletin_id='.$bulletin["id"].'">'.$bulletin["title"].'</a>'
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['manager']
                        );
	$allow=explode(":",$bulletin['allow']);
	$allow=$allow[1]?$allow[1]:$allow[0];
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => str_replace(",","  ",$allow)
			);

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['reply_number']
                        );

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['update_time'] 
                        );

    $bulletin_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
						  'text' => '<a id="m_696" onclick="edit_bulletin(this,'.$bulletin["id"].')" href="javascript:void(0)">'.tep_get_signal_pic_info($bulletin['update_time']).'</a>'
                          ); 
                      
    $bulletin_table_row[] = array('params' => $bulletin_item_params, 'text' => $bulletin_item_info);

  }

  $form_str = tep_draw_form('bulletin_list', FILENAME_BULLETIN_BOARD, tep_get_all_get_params(array('action')).'action=del_select_bulletin');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($bulletin_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
  }
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                  <td class="smallText" valign="top">
                  <?php
                  if($ocertify->npermission >= 0 && tep_db_num_rows($bulletin_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select onchange="select_bulletin_change(this.value,\'bulletin_list_id[]\',\''.$ocertify->npermission.'\')" name="edit_bulletin_list" '.(($site_permission_flag == true ||$show_flag==1 ) ? '':'disabled="disabled"').'>';
                    echo '<option value="0">'.TEXT_BULLETIN_EDIT_SELECT.'</option>';
                    echo '<option value="1">'.TEXT_BULLETIN_EDIT_DELETE.'</option>';
                    echo '</select>';
                    echo '</div>';
                  }
                  ?>
                  </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $bulletin_split->display_count($bulletin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], $_GET['action']=='show_reply'?TEXT_DISPLAY_NUMBER_OF_BULLETIN_BOARD_REPLY:TEXT_DISPLAY_NUMBER_OF_BULLETIN_BOARD); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $bulletin_split->display_links($bulletin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('x', 'y', 'page'))); ?></div></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php 
					if($_GET['action']=='show_reply'){
						echo '<a href="'.FILENAME_BULLETIN_BOARD.'" onclick="back(this);">' .tep_html_element_button(TEXT_BACK,$site_permission_flag == false ? '' : '') . '</a>';
						echo '<a href="javascript:void(0);" onclick="create_bulletin_reply(this,'.$_GET["bulletin_id"].');">' .tep_html_element_button(TEXT_CREATE_BULLETIN_REPLY,$site_permission_flag == false ? '' : '') . '</a>'; 
					}
					else{
						echo '<a href="'.FILENAME_BULLETIN_BOARD.'" onclick="back(this);">' .tep_html_element_button(TEXT_BACK,$site_permission_flag == false ? '' : '') . '</a>';
						echo '<a href="javascript:void(0);" onclick="create_bulletin(this);">' .tep_html_element_button(TEXT_CREATE_BULLETIN,$site_permission_flag == false ? '' : '') . '</a>'; 
					}?></div></td>
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
