<?php
  /**
   * $Id$
   *
   * 备忘录管理
   */
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');
  //获取系统允许上传文件的大小
  $php_upload_max_filesize= size_to_b(ini_get('upload_max_filesize'));
  $php_post_max_size = size_to_b(ini_get('post_max_size'));
  $php_memmory_list = size_to_b(ini_get('memory_limit'));
  $min_size_str = ini_get('memory_limit'); 
  $min_size = $php_memmory_list;
  if($min_size > $php_post_max_size){
    $min_size = $php_post_max_size;
    $min_size_str = ini_get('post_max_size');
  }
  if($min_size > $php_upload_max_filesize){
    $min_size = $php_upload_max_filesize;
    $min_size_str = ini_get('upload_max_filesize');
  }
  //获取当前用户的网站管理权限
  $sites_id_sql = tep_db_query("select site_permission from ".TABLE_PERMISSIONS." where userid= '".$ocertify->auth_user."'");
  $userslist= tep_db_fetch_array($sites_id_sql);
  tep_db_free_result($sites_id_sql);
  $site_permission_array = explode(',',$userslist['site_permission']); 
  $site_permission_flag = true;
  if(in_array('0',$site_permission_array)||$ocertify->npermission==31){
    $site_permission_flag = true;
  }

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'delete' 删除选中的bulletin 或回复
------------------------------------------------------*/
	case 'create_bulletin':
	 $author=$ocertify->auth_user;
	 $user_info = tep_get_user_info($author);
	 $update_user=$user_info['name'];
	 $content=$_POST['content'];
	 $collect='';
	 $allow="";
	 if($_POST['select_all'])$allow="all";
	 if($_POST['select_group'])$allow="group:";
	 if($_POST['select_id'])$allow="id:";
	foreach($_POST['selected_staff'] as $value){
		if(strlen($allow)>6){
				$allow.=",".$value;
		}else{
				$allow.=$value;
		}
	}
	if($allow=='id:'||$allow=='group:')$allow='all';

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
		 $file_name= base64_encode($_FILES['bulletin_file']['name'][$fk].'|||'.time().'|||'.$fk);
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 if($_FILES['bulletin_file']['name'][$fk]!=''){
			 $file_path.=$file_name;
		 }
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name);
	   }
	 $sql_add_bullention = array(
	    'content' => $content,
	    'allow' => $allow,
	    'manager' => $manager,
		'mark' => $mark,
		'collect' => $collect,
		'reply_number' => $reply_number, 
	    'title' => $title,
		'file_path' => $file_path,
		'add_time'=> 'now()',
		'update_time'=> 'now()',
	    'add_user' => $update_user,
	    'update_user' => $update_user
	 );
	 tep_db_perform(TABLE_BULLETIN_BOARD,$sql_add_bullention);
	 //notice记录帖子的id
	 $insert_board_id = tep_db_insert_id();

	 $sql_add_bullention = array(
	    'content' => $content,
	    'bulletin_id' => $insert_board_id,
		'mark' => $mark,
		'collect' => $collect,
		'file_path' => $file_path,
		'add_time'=> 'now()',
		'update_time'=> 'now()',
	    'add_user' => $update_user,
	    'update_user' => $update_user
	 );
	 tep_db_perform(TABLE_BULLETIN_BOARD_REPLY,$sql_add_bullention);
		//添加提醒和日志
	 $sql_add_notice = array(
	    'type' => 1,
	    'title' => $title,
		'set_time' => 'now()',
		'from_notice' => $insert_board_id,
		'user' => $add_user,
		'created_at'=> 'now()',
		'is_show' =>1,
	    'deleted' =>''	
	 );

	 tep_db_perform(TABLE_NOTICE,$sql_add_notice);
		$parm=isset($_GET['order_sort'])?'order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		if(isset($_GET['page']))$parm.="&page=".$_GET['page'];
		tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,$parm));
	 break;


	case 'update_bulletin':
	 $id=$_GET['bulletin_id'];
	 $bulletin_info_raw=tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where id=$id");
	 $bulletin_info_row=tep_db_fetch_array($bulletin_info_raw);
	 $author=$ocertify->auth_user;
	 $user_info = tep_get_user_info($author);
	 $update_user=$user_info['name'];
	 $content=$_POST['content'];
	 $collect=0;
	 $allow="";
	 if($_POST['select_all'])$allow="all";
	 if($_POST['select_group'])$allow="group:";
	 if($_POST['select_id'])$allow="id:";
	foreach($_POST['selected_staff'] as $value){
		if(strlen($allow)>6){
				$allow.=",".$value;
		}else{
				$allow.=$value;
		}
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
		 //$file_name=$_FILES['bulletin_file']['name'][$fk];
		 $file_name= base64_encode($_FILES['bulletin_file']['name'][$fk].'|||'.time().'|||'.$fk);
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 if($_FILES['bulletin_file']['name'][$fk]!=''){
		   $file_path.=$file_name;
		 }
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	 }
	 $sql_add_bullention = array(
	    'content' => $content,
	    'allow' => $allow,
	    'manager' => $manager,
		'mark' => $mark,
		'collect' => $collect,
//		'reply_number' => $reply_number, 
	    'title' => $title,
		'file_path' => $file_path,
		'update_time'=> 'now()',
	    'update_user' => $update_user
	 );
         tep_db_perform(TABLE_NOTICE,array('title'=>$title),'update',"created_at='".$bulletin_info_row['add_time']."'
             and title='".$bulletin_info_row['title']."'");
	 tep_db_perform(TABLE_BULLETIN_BOARD, $sql_add_bullention, 'update',  "id = '" .$id  . "'");
		$page=isset($_GET['page'])?'page='.$_GET['page']:1;
		$parm=isset($_GET['order_sort'])?'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,$page.$parm));
	 break;

	case 'create_bulletin_reply':
	 $bulletin_id=$_GET['bulletin_id'];
	 $content=$_POST['content'];
	 $title=mb_strlen($content) > 30 ? mb_substr($content, 0, 30).'...' : $content;
	 $mark="";
	 foreach($_POST['pic_icon'] as $value){
		 if(strlen($mark)<1){
				 $mark.=$value;
		 }else{
				 $mark.=",".$value;
		 }
	 }
	 $author=$ocertify->auth_user;
	 $user_info = tep_get_user_info($author);
	 $update_user=$user_info['name'];
	 $author_row=tep_db_fetch_array(tep_db_query('select * from '.TABLE_BULLETIN_BOARD.' where id='.$bulletin_id.' limit 1'));
	 $add_user=$author_row['add_user'];
	 $add_time= $author_row['add_time'];
	 $file_path="";
	   foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 //$file_name=$_FILES['bulletin_file']['name'][$fk];
		 $file_name= base64_encode($_FILES['bulletin_file']['name'][$fk].'|||'.time().'|||'.$fk);
		 $file_name=$file_name;
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 if($_FILES['bulletin_file']['name'][$fk]!=''){
		   $file_path.=$file_name;
		 }
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	   }
	 $sql_add_bullention = array(
		 'bulletin_id' => $bulletin_id,
	    'content' => $content,
		'mark' => $mark,
		'collect' => $collect,
		'file_path' => $file_path,
		'add_time'=> $add_time,
		'update_time'=> 'now()',
	    'add_user' => $add_user,
	    'update_user' => $update_user
	 );
	 tep_db_perform(TABLE_BULLETIN_BOARD_REPLY,$sql_add_bullention);
	 $insert_reply_id = tep_db_insert_id();
	 tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number+1 where id=$bulletin_id");

	//添加提醒和日志
	 $sql_add_notice = array(
	    'type' => 2,
	    'title' => $title,
		'set_time' => 'now()',
		'from_notice' => $insert_reply_id,
		'user' => $add_user,
		'created_at'=> 'now()',
		'is_show' =>1,
	    'deleted' =>''	
	 );

	 tep_db_perform(TABLE_NOTICE,$sql_add_notice);
		$page=isset($_GET['page'])?'page='.$_GET['page']:1;
		$parm=isset($_GET['order_sort'])?'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type']:'';
		 tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD,"action=show_reply&bulletin_id=$bulletin_id&".$page.$parm));
	 break;
	 case 'update_bulletin_reply':
	 $id=$_GET['id'];
	 $bulletin_info_row=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id"));
	 $content=$_POST['old_content'];
         if($_POST['content']!=''){
           $_POST['content'] = str_replace("\n","\n>",$_POST['content']);
           $content = $_POST['content']."\n>".$content;
         }else{
           $content = "\n>".$content;    
         }
	 $mark="";
	 foreach($_POST['pic_icon'] as $value){
		 if(strlen($mark)<1)$mark.=$value;
		 else $mark.=",".$value;
	 }
	 $add_user=$bulletin_info_row['add_user'];
	 $add_time=$bulletin_info_row['add_time'];
	 $author=$ocertify->auth_user;
	 $user_info = tep_get_user_info($author);
	 $update_user=$user_info['name'];
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
		 //$file_name=$_FILES['bulletin_file']['name'][$fk];
		 $file_name= base64_encode($_FILES['bulletin_file']['name'][$fk].'|||'.time().'|||'.$fk);
		 if($file_name=='')continue;
		 if(strlen($file_path)!=0)$file_path.="|||";
		 if($_FILES['bulletin_file']['name'][$fk]!=''){
		   $file_path.=$file_name;
		 }
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],PATH_BULLETIN_BOARD_UPLOAD.$file_name); 
	   }
	 $bulletin_id=$bulletin_info_row['bulletin_id'];
	 $sql_add_bullention = array(
		 'bulletin_id' => $bulletin_id,
	    'content' => tep_db_prepare_input($content),
		'mark' => $mark,
		'collect' => $collect,
		'file_path' => $file_path,
		'add_time'=> $add_time,
		'update_time'=> 'now()',
	    'add_user' => $add_user,
	    'update_user' => $update_user
	 );
	 tep_db_perform(TABLE_BULLETIN_BOARD_REPLY,$sql_add_bullention);
	 $update_reply_id = tep_db_insert_id();
     tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number+1 where id=$bulletin_id");
	 $content=explode(">",$content);
	 $content=$content[0];
	 $title=mb_strlen($content) > 30 ? mb_substr($content, 0, 30).'...' : $content;
	 $sql_add_notice = array(
	    'type' => 2,
	    'title' => $title,
		'set_time' => 'now()',
		'from_notice' => $update_reply_id,
		'user' => $add_user,
		'created_at'=> 'now()',
		'is_show' =>1,
	    'deleted' =>''
	 );

	 tep_db_perform(TABLE_NOTICE,$sql_add_notice);
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
		if(isset($_GET['id'])){
				$bulletin_id_list[]=$_GET['id'];
		}else{
				$bulletin_id_list = tep_db_prepare_input($_POST['bulletin_list_id']);
		}
    $param_str = $_GET['page'];
    foreach($bulletin_id_list as $id){
			if($_GET['delete_type']=='show_reply'){
				if($ocertify->npermission>=15 || 
								tep_db_num_rows(tep_db_query("select br.id from ".TABLE_BULLETIN_BOARD_REPLY." br,".TABLE_BULLETIN_BOARD." bb where br.id=$id and br.bulletin_id=bb.id and bb.manager='$ocertify->auth_user'"))>=1) {
						$reply_number_row=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id  and content!='deleted'"));
						if(tep_db_query("update ".TABLE_BULLETIN_BOARD_REPLY." set file_path='',content='deleted' where  content!='deleted' and id=".$id)){
								$file_list=explode("|||",$reply_number_row['file_path']);
								foreach($file_list as $value){
										@unlink(PATH_BULLETIN_BOARD.$value);
								}
								$bulletin_id=$reply_number_row['bulletin_id'];
								$_GET['bulletin_id']=$bulletin_id;
								tep_db_query("update ".TABLE_BULLETIN_BOARD." set reply_number=reply_number-1 where id=$bulletin_id");
								tep_db_query("delete from ".TABLE_NOTICE." where from_notice=$id and type=2");
						}
				}
		 }else if(tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where id=$id and  manager='$ocertify->auth_user'"))>0|| 
						 $ocertify->npermission>=15 ){
				 tep_db_query("delete from ".TABLE_BULLETIN_BOARD." where id=".$id);
				 tep_db_query("delete from notice where (from_notice=$id and type=1) or (from_notice in (select id from ".TABLE_BULLETIN_BOARD_REPLY." where bulletin_id=$id) and type=2)");
				 tep_db_query("delete from ".TABLE_BULLETIN_BOARD_REPLY." where bulletin_id=".$id);
		 }
		}
		if($_GET['delete_type']=='show_reply'){
				tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, 'action=show_reply&bulletin_id='.$_GET['bulletin_id'].'&page='.$param_str));
		}else{
				tep_redirect(tep_href_link(FILENAME_BULLETIN_BOARD, 'page='.$param_str));
		}
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
var bulletin_prev = '<?php echo IMAGE_PREV;?>';
var bulletin_next = '<?php echo IMAGE_NEXT;?>';
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
        if ($("#bulletin_prev")) {
          $("#bulletin_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#bulletin_next")) {
          $("#bulletin_next").trigger("click");
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

<?php //上传文件大小验证?>
function save_check(){

  var all_size = 0;
  var file_size = 0;
  $("input[name='bulletin_file[]']").each(
    function(){
      if($(this).val()){
        t= this.files;
        if(t){
          file_size = t[0].size; 
        }
        all_size+= file_size; 
      }
    }
  );
  if(all_size > <?php echo $min_size;?>){
      alert('<?php echo sprintf(TEXT_UPLOAD_FILE_EXXEEDS_THE_LIMIT,$min_size_str);?>');
      return false;
  }
  return true;
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
      if (c_permission >=15) {
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
              document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete&delete_type='.$_GET['action'].($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
              document.edit_bulletin_form.submit(); 
            } else {
              var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent('<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete&delete_type='.$_GET['action'].($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>'),
                  async: false,
                  success: function(msg_info) {
                    document.edit_bulletin_form.action = "<?php echo tep_href_link(FILENAME_BULLETIN_BOARD, 'action=delete&delete_type='.$_GET['action'].($_GET['page'] != '' ? '&page='.$_GET['page'] : ''));?>";
                    document.edit_bulletin_form.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("edit_bulletin_list")[0].value = 0;
                alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
              }
            }
          }
        });
      }
    }else{

      document.getElementsByName("edit_bulletin_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_bulletin_list")[0].value = 0;
    alert("<?php echo TEXT_MEMO_EDIT_MUST_SELECT;?>"); 
  }
}

<?php //全选动作?>
function all_select_bulletin(bulletin_list_id)
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
      //prev next
      if($('#bulletin_'+id).prev().attr('id') != '' && $('#bulletin_'+id).prev().attr('id') != null){
        var bulletin_prev_id = $('#bulletin_'+id).prev().attr('id');
        bulletin_prev_id = bulletin_prev_id.split('_');

        if(bulletin_prev_id[0] == 'bulletin' && bulletin_prev_id[1] != ''){
          var bulletin_id = $('#bulletin_'+id).prev().attr('id');
          bulletin_id = bulletin_id.split('_');
          $('#next_prev').append('<a id="bulletin_prev" onclick="'+$('#click_'+bulletin_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">&lt'+bulletin_prev+'</a>&nbsp&nbsp');
        }
      }
      if($('#bulletin_'+id).next().attr('id') != '' && $('#bulletin_'+id).next().attr('id') != null){
        var bulletin_next_id = $('#bulletin_'+id).next().attr('id');
        bulletin_next_id = bulletin_next_id.split('_');
     
        if(bulletin_next_id[0] == 'bulletin' && bulletin_next_id[1] != ''){
          var bulletin_id = $('#bulletin_'+id).next().attr('id');
          bulletin_id = bulletin_id.split('_');
          $('#next_prev').append('<a id="bulletin_next" onclick="'+$('#click_'+bulletin_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">'+bulletin_next+'&gt</a>&nbsp&nbsp');
        }
      }else{
        $('#next_prev').append('<font color="#000000">'+bulletin_next+'&gt</font>&nbsp&nbsp'); 
      } 
      //end
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
      //prev next
      if($('#bulletin_'+id).prev().attr('id') != '' && $('#bulletin_'+id).prev().attr('id') != null){
        var bulletin_prev_id = $('#bulletin_'+id).prev().attr('id');
        bulletin_prev_id = bulletin_prev_id.split('_');

        if(bulletin_prev_id[0] == 'bulletin' && bulletin_prev_id[1] != ''){
          var bulletin_id = $('#bulletin_'+id).prev().attr('id');
          bulletin_id = bulletin_id.split('_');
          $('#next_prev').append('<a id="bulletin_prev" onclick="'+$('#click_'+bulletin_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">&lt'+bulletin_prev+'</a>&nbsp&nbsp');
        }
      }
      if($('#bulletin_'+id).next().attr('id') != '' && $('#bulletin_'+id).next().attr('id') != null){
        var bulletin_next_id = $('#bulletin_'+id).next().attr('id');
        bulletin_next_id = bulletin_next_id.split('_');
     
        if(bulletin_next_id[0] == 'bulletin' && bulletin_next_id[1] != ''){
          var bulletin_id = $('#bulletin_'+id).next().attr('id');
          bulletin_id = bulletin_id.split('_');
          $('#next_prev').append('<a id="bulletin_next" onclick="'+$('#click_'+bulletin_id[1]).attr('onclick').replace('this','\'\'')+'" href="javascript:void(0);">'+bulletin_next+'&gt</a>&nbsp&nbsp');
        }
      }else{
        $('#next_prev').append('<font color="#000000">'+bulletin_next+'&gt</font>&nbsp&nbsp'); 
      } 
      //end
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
	if(type==0 && 
					$("#user_add").html()=='' && $("#group_add").html()=='' &&
					!document.getElementById("select_all_radio").checked ){
		document.getElementById("popup_user_select").style.display="inline";
		flag=1;
        }
        if(!save_check()){

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
	$header_title_sql="select * from  ".TABLE_BULLETIN_BOARD;
	if($ocertify->npermission <15){
			$header_title_sql.="  where (manager='$ocertify->auth_user' or add_user='$ocertify->auth_user' or allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name'))) and";
	}else{
			$header_title_sql.=" where";
	}
$last_id_sql="select * from  ".TABLE_BULLETIN_BOARD." where id>0 ";
	$last_id_sql.=$ocertify->npermission <15 ? " and (manager='$ocertify->auth_user' or add_user='$ocertify->auth_user' or allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow  like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))":"";	
	$next_id_sql=$last_id_sql;
	if(isset($_GET['bulletin_id']) && $_GET['action']=='show_reply'){
		
		$header_title_sql.=" id =".$_GET['bulletin_id'];
		$header_title_raw=tep_db_query($header_title_sql);
		$header_title_row=tep_db_fetch_array($header_title_raw);
		$header_id=$header_title_row['id'];
		$content=$header_title_row['title'];
	        $header_content=mb_strlen($content) > 30 ? mb_substr($content, 0, 30).'...' : $content;
		if(!$header_id)$header_id=$_GET['bulletin_id'];
		$last_id_row=tep_db_fetch_array(tep_db_query($last_id_sql." and id > $header_id order by id asc limit 1"));
		$next_id_row=tep_db_fetch_array(tep_db_query($next_id_sql." and id < $header_id order by id desc limit 1"));
		$last_id=$last_id_row['id'];
		$next_id=$next_id_row['id'];
		$header_title_html='';
		if($last_id&&tep_db_num_rows(tep_db_query($last_id_sql." and id>=".$last_id))!=0){
					$header_title_html.='<a href="bulletin_board.php?action=show_reply&bulletin_id='.$last_id.'&from=last"><img src="images/icons/icon_last.gif" title="'.TEXT_LAST_BULLETIN.'" alt="'.TEXT_LAST_BULLETIN.'"></a>';
	}else {
			$header_title_html.='&nbsp&nbsp&nbsp&nbsp';
	}
		if($next_id>0&&tep_db_num_rows(tep_db_query($next_id_sql." and id<=".$next_id))!=0){
				$header_title_html.='<a href="bulletin_board.php?action=show_reply&bulletin_id='.$next_id.'&from=next" ><img src="images/icons/icon_next.gif" title="'.TEXT_NEXT_BULLETIN.'" alt="'.TEXT_NEXT_BULLETIN.'"></a>';
		}else{
				$header_title_html.='&nbsp&nbsp&nbsp&nbsp';
		}
		$header_title_html.=$header_content.'';
	}else{
		$header_title_html=TEXT_BULLETIN_BOARD;
	}
?>
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php 
            echo $header_title_html; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
			<td class="pageHeading" align="right">
			<form method="get" action="bulletin_board.php">
				<input type="text" id="search_text" name="search_text">
				<input type="submit" value="<?php echo HEADING_TITLE_SEARCH;?>">
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
  echo tep_new_site_filter(FILENAME_BULLETIN_BOARD,false,array(),true);
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
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_bulletin(\'bulletin_list_id[]\');" name="all_check"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=collect&order_type='.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_COLLECT.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=mark&order_type='.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MARK.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap" style="width:40%;"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=content&order_type='.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_CONTENT_REPLY.($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'content' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap" ', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=add_file&order_type='.($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_ADDFILE.($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'&order_sort=add_user&order_type='.($_GET['order_sort'] == 'add_user' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'."From".($_GET['order_sort'] == 'add_user' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'add_user' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
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
    case 'add_user':
      $order_sort = 'order by r.add_user';
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
    case 'add_file':
      $order_sort = 'order by r.file_path';
      $order_type = $_GET['order_type'];
	  break;
    }
  }else{
    $order_sort = 'order by r.update_time';
    $order_type = 'desc'; 
  }

  $group_raw=tep_db_fetch_array(tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user')"));
  $group_name=$group_raw['name'];
  $bulletin_query_str = 'and r.bulletin_id='.$_GET['bulletin_id'];
	//回复权限控制：admin、root可以查看全部，管理者、作者、阅览者可以查看
	if($ocertify->npermission <15){
			//当不是root和admin时 ，判断是否为帖子管理者、作者、阅览者
			$bulletin_query_str.=" and(b.add_user='$ocertify->auth_user' or b.manager='$ocertify->auth_user' or b.allow='all' 
					or b.allow like '%:$ocertify->auth_user' or b.allow like '%:$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user'  
					or b.allow like '%:$group_name' or b.allow like '%:$group_name,%' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')";
	}
	//搜索处理
	if(isset($_GET['search_text'])&& $_GET['search_text']){
	  $bulletin_query_str.=" and (r.content like '%".$search_text."%' )";
  }

	//回复列表内容sql
  $bulletin_query_raw = "select r.update_user update_user, r.id id, r.content content, r.file_path file_path ,r.update_time update_time ,r.add_user ,r.collect collect ,r.mark mark,r.bulletin_id bulletin_id, b.id bid,b.allow ,b.manager ,b.add_user from " . TABLE_BULLETIN_BOARD_REPLY ." r ,".TABLE_BULLETIN_BOARD." b where r.bulletin_id=b.id  ".$bulletin_query_str." ";
  
	//收藏排序处理
	if($order_sort=='collect'){
		//当前用户收藏的记录
	  $user_collect = $bulletin_query_raw."and r.id in ( select id from ".TABLE_BULLETIN_BOARD_REPLY." where (b.allow='all' or ((b.allow like 'id:%' and( b.allow like '%:$ocertify->auth_user,%' or b.allow like '%:$ocertify->auth_user' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user') ) or (b.allow like 'group:%' and (b.allow like '%:$group_name,%' or b.allow like '%:$group_name' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')))) and (r.collect='$ocertify->auth_user' or r.collect like '$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user'))";
		//当前用户未收藏的记录
		$user_not_collect=$bulletin_query_raw."and r.id not in ( select id from ".TABLE_BULLETIN_BOARD_REPLY." where (b.allow='all' or ((b.allow like 'id:%' and( b.allow like '%:$ocertify->auth_user,%' or b.allow like '%:$ocertify->auth_user' or b.allow like '%,$ocertify->auth_user,%' or b.allow like '%,$ocertify->auth_user') ) or (b.allow like 'group:%' and (b.allow like '%:$group_name,%' or b.allow like '%:$group_name' or b.allow like '%,$group_name,%' or b.allow like '%,$group_name')))) and (r.collect='$ocertify->auth_user' or r.collect like '$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user,%' or r.collect like '%,$ocertify->auth_user'))";
		if($order_type=='desc'){
				//收藏倒序sql
				$bulletin_query_raw=$user_collect." union ".$user_not_collect;
		}else {
				//收藏正序sql
			$bulletin_query_raw=$user_not_collect." union ".$user_collect;
		}
	}else{
			$bulletin_query_raw .=  " ".$order_sort." ".$order_type;
	}
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
      $bulletin_item_params = ($bulletin["content"]=='deleted' ? '' : 'id="bulletin_'.$bulletin["id"].'" ').'class="dataTableRowSelected"  onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $bulletin_item_params = ($bulletin["content"]=='deleted' ? '' : 'id="bulletin_'.$bulletin["id"].'" ').'class="'.$nowColor.'"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

    $bulletin_item_info = array();  
	if(($ocertify->npermission <15 && $ocertify->auth_user!=$bulletin['manager'])||$bulletin["content"]=='deleted'||!$site_permission_flag){
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
	if($bulletin["content"]=='deleted'){
		$title=TEXT_DELETED_INFO;
	}else{
		$title=explode(">",$bulletin["content"]);
		$title=$title[0];
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" title="'.$title.'"  width="70%" onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $title
                        );
	$add_file_html='';
	$file_list=explode("|||",$bulletin["file_path"]);
	foreach($file_list as $f){
		if($f=='')continue;
		$file_name= $f;
		 $file_name = str_replace('*','/',$file_name);
         $file_name = base64_decode($file_name);
         $file_name = explode('|||',$file_name);
		//$url=base64_encode($f);
		//$url=str_replace("+","000ADD",$url);
		$add_file_html.='<a href="bulletin_file_download.php?file_id='.$f.'"><img src="images/icons/attach.png" alt="'.$file_name[0].'" title="'.$file_name[0].'"></a>';
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => $add_file_html
                        );
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $bulletin['update_user']
                        );

    if(date('Y-m-d') == date('Y-m-d',strtotime($bulletin['update_time']))){
      $time_str = date('H:i',strtotime($bulletin['update_time']));
    //如果不是当天，但是当年
    }else if(date('Y') == date('Y',strtotime($bulletin['update_time']))){
      $time_str = date('m'.MONTH_TEXT.'d'.DAY_TEXT,strtotime($bulletin['update_time']));
    }else{
      $time_str = date('Y/m/d',strtotime($bulletin['update_time']));
    }
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  onclick="bulletin_board_select('.$bulletin["id"].',1)"', 
                          'text' => $time_str 
                        );
	if($bulletin["content"]=='deleted'){
		$edit_html='<img src="images/icons/info_gray.gif">';
	}else{
		$edit_html='<a id="click_'.$bulletin["id"].'" onclick="reply_bulletin(this,'.$bulletin["id"].','.$bulletin["bulletin_id"].')" href="javascript:void(0)">'.tep_get_signal_pic_info($bulletin['update_time']).'</a>';
	}
    $bulletin_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
						  'text' => $edit_html
                          ); 
                      
    $bulletin_table_row[] = array('params' => $bulletin_item_params, 'text' => $bulletin_item_info);

  }

  $form_str = tep_draw_form('bulletin_list', FILENAME_BULLETIN_BOARD, tep_get_all_get_params(array('action')).'action=del_select_bulletin');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($bulletin_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();

//回复处理结束


  }else{
//帖子显示处理
  $bulletin_title_row = array();                
  //bulletin列表  
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent" nowrap="nowrap"', 'text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_bulletin(\'bulletin_list_id[]\');" name="all_check"'.($site_permission_flag == false ? ' disabled="disabled"' : '').'>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=collect&order_type='.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_COLLECT.($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'collect' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" ', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=mark&order_type='.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MARK.($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'mark' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=title&order_type='.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TITLE.($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'title' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=add_file&order_type='.($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_ADDFILE.($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'add_file' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=manager&order_type='.($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_MANAGER.($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'manager' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>'); 
  $bulletin_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap="nowrap"', 'text' => '<a href="'.tep_href_link(FILENAME_BULLETIN_BOARD,tep_get_all_get_params(array('x', 'y', 'order_type','order_sort')).'order_sort=allow&order_type='.($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'desc' ? 'asc' : 'desc')).'">'.TEXT_TO_BODY.($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'desc'? '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>' : ($_GET['order_sort'] == 'allow' && $_GET['order_type'] == 'asc' ? '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>' : '')).'</a>');
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
      $order_sort = 'bb.mark';
      $order_type = $_GET['order_type'];
      break;
    case 'content':
      $order_sort = 'bb.content';
      $order_type = $_GET['order_type'];
      break;
    case 'manager':
      $order_sort = 'u.name';
      $order_type = $_GET['order_type'];
      break;
    case 'title':
      $order_sort = 'bb.title';
      $order_type = $_GET['order_type'];
      break;
    case 'add_user':
      $order_sort = 'bb.add_user';
      $order_type = $_GET['order_type'];
      break;
    case 'collect':
      $order_sort = 'bb.collect';
      $order_type = $_GET['order_type'];
      break;
    case 'allow':
      $order_sort = 'bb.allow';
      $order_type = $_GET['order_type'];
      break;
    case 'action':
      $order_sort = 'bb.update_time';
      $order_type = $_GET['order_type'];
      break;
    case 'add_file':
      $order_sort = 'bb.file_path';
      $order_type = $_GET['order_type'];
	  break;
    }
  }else{
    $order_sort = 'update_time';
    $order_type = 'desc'; 
  }

  $where_str = '1 ';
  $where_group_query = tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user')");
  $where_group_arr = array();
  while($where_group_res = tep_db_fetch_array($where_group_query)){
    $where_group_arr[] = $where_group_res['name'];
  }
  if($ocertify->npermission<15){
    $where_str .= " and ( ";
    $where_str .= " (bb.manager ='".$ocertify->auth_user."' ) ";
    $where_str .= " or (bb.allow='all' or 
      (bb.allow = 'id:".$ocertify->auth_user."' 
      or bb.allow like 'id:".$ocertify->auth_user.",%' 
      or bb.allow like 'id:%,".$ocertify->auth_user.",%' 
      or bb.allow like 'id:%,".$ocertify->auth_user."' 
      )";
    if(!empty($where_group_arr)){
      foreach($where_group_arr as $temp_group){
        $where_str .= " or bb.allow like 'group:%".$temp_group."%'";
      }
    }
    $where_str .= ")";
    $where_str .= ")"; 
  }
  if($order_sort=='bb.collect'){
    $bulletin_query_raw  = "select *,if(bb.collect like '%".$ocertify->auth_user."%',1,0) as is_collect 
      from ". TABLE_BULLETIN_BOARD ." bb where ".$where_str." order by is_collect ".$order_type;
  }else if ($order_sort=='u.name'){
    $bulletin_query_raw  = "select *,if(u.userid = null or u.userid is null,bb.manager,u.name) as real_name 
      from ". TABLE_BULLETIN_BOARD ." bb left join " .TABLE_USERS. " u ON
      bb.manager=u.userid where ".$where_str." order by real_name ".$order_type;
  }else if ($order_sort == 'bb.allow'){
    $bulletin_query_raw  = "select *,if(bb.allow!='all',(if(bb.allow like
      'id:%',substring(bb.allow,4),substring(bb.allow,7))),bb.allow) as real_allow 
      from ". TABLE_BULLETIN_BOARD ." bb where ".$where_str." order by real_allow ".$order_type;
  }else if ($order_sort == 'bb.file_path'){
    $bulletin_query_raw  = "select *,if( file_path is null or file_path = '',0,
      (CHAR_LENGTH(replace(file_path,'|||','||||'))-CHAR_LENGTH(file_path))+1) as file_num
      from ". TABLE_BULLETIN_BOARD ." bb where ".$where_str." order by file_num ".$order_type;
  }else{
    $bulletin_query_raw  = "select * 
      from ". TABLE_BULLETIN_BOARD ." bb where ".$where_str." order by ".$order_sort." ".$order_type;
  }

  $bulletin_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $bulletin_query_raw, $bulletin_query_numrows);
  $bulletin_query = tep_db_query($bulletin_query_raw);

	//记录数为0显示信息
  if(tep_db_num_rows($bulletin_query) == 0){
    $bulletin_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
    $bulletin_table_row[] = array('params' => '', 'text' => $bulletin_data_row);  
  }

	//显示帖子列表
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
		//选中效果处理
    if ($bulletin['id']==$_GET['c_id']||$bulletin['id']==$_GET['bulletin_id']) {
      $bulletin_item_params = 'id="bulletin_'.$bulletin['id'].'" class="dataTableRowSelected" valign="top"   onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $bulletin_item_params = 'id="bulletin_'.$bulletin['id'].'" class="'.$nowColor.'" valign="top"  onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }

		//权限控制：root、admin、管理者 可删除
	if(($ocertify->auth_user!=$bulletin["manager"])&&($ocertify->npermission <15)|| !$site_permission_flag){
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

	$title=$bulletin['title'];
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"  width="70%" title="'.$title.'"', 
                          'text' => '<a href="bulletin_board.php?action=show_reply&bulletin_id='.$bulletin["id"].'">'.$title.'</a>'
                        );
	$add_file_html='';
	$file_list_arr = explode("|||",$bulletin['file_path']); 
	foreach($file_list_arr as $k=>$f){
		if($f=='')continue;
		$file_name= $f;
		 $file_name = str_replace('*','/',$file_name);
         $file_name = base64_decode($file_name);
         $file_name = explode('|||',$file_name);
		$add_file_html.='<a href="bulletin_file_download.php?file_id='.$f.'"><img src="images/icons/attach.png" alt="'.$file_name[0].'" title="'.$file_name[0].'"></a>';
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' =>$add_file_html
                        );
    if($bulletin['manager']!=''){
    $user_info = tep_get_user_info($bulletin['manager']);
    $user_name = $user_info['name'];
	if(!$user_name)$user_name=$bulletin['manager'];
    }else{
      $user_name = '--';
    }
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $user_name
                        );
	$allow=explode(":",$bulletin['allow']);
	$user_list='';
	if($allow[0]=='id'){
			$allow=$allow[1]?$allow[1]:$allow[0];
			$allow=explode(",",$allow);
			$array_count=count($allow);
			for($i=0;$i<$array_count;$i++){
					$user_info=tep_get_user_info($allow[$i]);
					$user_name=$user_info['name'];
					if($user_name){
							$user_list.=$user_name.';&nbsp;';
					}else{
							$user_list.=$allow[$i].';&nbsp;';
					}
			}
	}else{
			$user_list.=$allow[1]?$allow[1]:$allow[0];
			$user_list=str_replace(",","; ",$user_list);
	}
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $user_list
			);

    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $bulletin['reply_number']
                        );

    //发帖时间格式化
    //如果是当天
    if(date('Y-m-d') == date('Y-m-d',strtotime($bulletin['update_time']))){
      $time_str = date('H:i',strtotime($bulletin['update_time']));
    //如果不是当天，但是当年
    }else if(date('Y') == date('Y',strtotime($bulletin['update_time']))){
      $time_str = date('m'.MONTH_TEXT.'d'.DAY_TEXT,strtotime($bulletin['update_time']));
    }else{
      $time_str = date('Y/m/d',strtotime($bulletin['update_time']));
    }
    $bulletin_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_BULLETIN_BOARD, 'page=' . $_GET['page'] . '&c_id=' . $bulletin['id']) . '\'"', 
                          'text' => $time_str 
                        );

    $bulletin_item_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
						  'text' => '<a id="click_'.$bulletin['id'].'" onclick="edit_bulletin(this,'.$bulletin["id"].')" href="javascript:void(0)">'.tep_get_signal_pic_info($bulletin['update_time']).'</a>'
                          ); 
                      
    $bulletin_table_row[] = array('params' => $bulletin_item_params, 'text' => $bulletin_item_info);

  }

  $form_str = tep_draw_form('bulletin_list', FILENAME_BULLETIN_BOARD, tep_get_all_get_params(array('action')).'action=del_select_bulletin');  
  $notice_box->get_form($form_str); 
  $notice_box->get_contents($bulletin_table_row);
  $notice_box->get_eof(tep_eof_hidden()); 
  echo $notice_box->show_notice();
	
	//帖子列表显示结束
  }
?>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                  <td class="smallText" valign="top">
                  <?php
                  if(tep_db_num_rows($bulletin_query) > 0){
                    echo '<div class="td_box">';
                    echo '<select onchange="select_bulletin_change(this.value,\'bulletin_list_id[]\',\''.$ocertify->npermission.'\')" name="edit_bulletin_list" '.(($site_permission_flag == true && $show_flag==1 ) ? '':'disabled="disabled"').'>';
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
						echo '<a href="'.FILENAME_BULLETIN_BOARD.'" onclick="back(this);">' .tep_html_element_button(TEXT_BACK,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>';
						echo '<a href="javascript:void(0);" onclick="create_bulletin_reply(this,'.$_GET["bulletin_id"].');">' .tep_html_element_button(TEXT_CREATE_BULLETIN_REPLY,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>'; 
					}else{
						echo '<a href="'.FILENAME_BULLETIN_BOARD.'" onclick="back(this);">' .tep_html_element_button(TEXT_BACK,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>';
						echo '<a href="javascript:void(0);" onclick="create_bulletin(this);">' .tep_html_element_button(TEXT_CREATE_BULLETIN,$site_permission_flag == false ? 'disabled="disabled"' : '') . '</a>'; 
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
