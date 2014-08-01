<?php
/*
  $Id$
*/
include("includes/application_top.php");

//删除过期未允许数据
$date = date('Ymd',time());
tep_db_query("delete from  ". TABLE_ATTENDANCE_DETAIL_REPLACE ." where allow_status =0 and date<".$date);

$month = $_GET['m']?$_GET['m']:date('n');
$year = $_GET['y']?$_GET['y']:date('Y');

$next_year_text = $year+1;
$prev_year_text = $year-1;
$str_next_year = '?y='.$next_year_text.'&m='.$month;
$str_prev_year = '?y='.$prev_year_text.'&m='.$month;
$str_str = '?y='.$year.'&m=';

if(isset($_GET['action'])){
  switch($_GET['action']){
    case 'save_att_date':
      $att_start = $_POST['att_start_hour'].':'.$_POST['att_start_minute_a'].$_POST['att_start_minute_b'];
      $att_end = $_POST['att_end_hour'].':'.$_POST['att_end_minute_a'].$_POST['att_end_minute_b'];
      $sql_att_info = "select * from ".TABLE_ATTENDANCE." where id='".$_POST['aid']."'";
      $query_att_info = tep_db_query($sql_att_info);
      if($row_att_info = tep_db_fetch_array($query_att_info)){
        $att_login_start = substr($row_att_info['login_time'],0,11);
        $att_login_end = substr($row_att_info['login_time'],16,3);
        $att_logout_start = substr($row_att_info['logout_time'],0,11);
        $att_logout_end = substr($row_att_info['logout_time'],16,3);
        $login = $att_login_start.$att_start.$att_login_end; 
        $logout = $att_logout_start.$att_end.$att_logout_end; 
      }
      $sql_update = "update ".TABLE_ATTENDANCE." set
        login_time='".$login."',logout_time='".$logout."' where
        id='".$_POST['aid']."'";
      tep_db_query($sql_update);
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'save_as_user_list':
      $date_info = tep_date_info($_POST['get_date']);
      $user = $_SESSION['user_name'];
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        $a_id_arr = $_POST['has_attendance_id'];
        if(isset($_POST['has_user'])&&!empty($_POST['has_user'])){
          $user_arr = $_POST['has_user'];
        }else{
          $user_arr = $_POST['has_group_hidden'];
        }
        $type_arr = $_POST['has_type'];
		
		foreach($_POST['has_space'] as $k => $val) {
			if(empty($val) || $type_arr[$k]!=1){
		        $_POST['has_space'][$k] = "0";	
			}	
		}
        foreach($a_id_arr as $key => $value){
               $sql_arr = array(
                  'week' => $date_info['week'],
                  'week_index' => $date_info['week_index'],
                  'attendance_detail_id' => $value,
                  'user_id' => $user_arr[$key],
                  'type' => $type_arr[$key],
                  'update_user' => $user,
                  'update_time' => 'now()',
			      'space' => $_POST['has_space'][$key],
              );
          if(isset($_POST['default_uid'])&&$_POST['default_uid']!=''){
            $sql_arr['user_id'] = $_POST['default_uid'];
          }
          $sql_arr['is_user'] = 1;

	      if($_POST['type_array'][$key]!= $type_arr[$key]){
            $sql_arr['date'] =  $_POST['get_date'];
            $sql_arr['month'] =  $date_info['month'];
            $sql_arr['day'] =  $date_info['day']; 
                
		  }
          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
        }
      }
      if(isset($_POST['attendance_id'])
          &&is_array($_POST['attendance_id'])
          &&!empty($_POST['attendance_id'])){
			  var_dump($_POST);
        $a_id_arr = $_POST['attendance_id'];
        if(isset($_POST['user'])&&!empty($_POST['user'])){
          $user_arr = $_POST['user'];
        }else{
          $user_arr = $_POST['user_hidden'];
        }
        $type_arr = $_POST['type'];

		foreach($_POST['space'] as $k => $val) {
			if(empty($val)|| $type_arr[$k]!=1){
		        $_POST['space'][$k] = "0";	
			}	
		}
        foreach($a_id_arr as $key => $value){
			if($_POST['type_array'][$k]= $type_arr[$key]){
			
			}
          $sql_arr = array(
              'date' => $_POST['get_date'],
              'month' => $date_info['month'],
              'day' => $date_info['day'],
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'user_id' => $user_arr[$key],
              'type' => $type_arr[$key],
              'add_user' => $user,
              'add_time' => 'now()',
			  'space' => $_POST['space'][$key],
              );
          if(isset($_POST['default_uid'])&&$_POST['default_uid']!=''){
            $sql_arr['user_id'] = $_POST['default_uid'];
          }
          if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
            &&!empty($_POST['data_as'])){
            $sql_other_arr = array(
                'update_user' => $user,
                'update_time' => 'now()',
              );
            $sql_arr = tep_array_merge($sql_arr,$sql_other_arr);
          }
          $sql_arr['is_user'] = 1;
          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr);
        }
      }
      if(isset($_POST['del_as'])&&!empty($_POST['del_as'])){
        foreach($_POST['del_as'] as $del_as){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where
              id="'.$del_as.'"');
        }
      }
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'save_as_list':
      $date_info = tep_date_info($_POST['get_date']);
      $user = $_SESSION['user_name'];
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        $a_id_arr = $_POST['has_attendance_id'];
        if(isset($_POST['has_group'])&&!empty($_POST['has_group'])){
          $group_arr = $_POST['has_group'];
        }else{
          $group_arr = $_POST['has_group_hidden'];
        }
        $type_arr = $_POST['has_type'];

		foreach($_POST['has_space'] as $k => $val) {
			if(empty($val)|| $type_arr[$k]!=1){
		        $_POST['has_space'][$k] = 0;	
			}	
		}
		
        foreach($a_id_arr as $key => $value){
          $sql_arr = array(
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'group_id' => $group_arr[$key],
              'type' => $type_arr[$key],
              'update_user' => $user,
              'update_time' => 'now()',
			  'space' => $_POST['has_space'][$key],
              );
          if(isset($_POST['default_gid'])&&$_POST['default_gid']!=''){
            $sql_arr['group_id'] = $_POST['default_gid'];
          }
	      if($_POST['type_array'][$key]!= $type_arr[$key]){
            $sql_arr['date'] =  $_POST['get_date'];
            $sql_arr['month'] =  $date_info['month'];
            $sql_arr['day'] =  $date_info['day']; 
                
		  }

          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
        }
      }
      if(isset($_POST['attendance_id'])
          &&is_array($_POST['attendance_id'])
          &&!empty($_POST['attendance_id'])){
        $a_id_arr = $_POST['attendance_id'];
        if(isset($_POST['group'])&&!empty($_POST['group'])){
          $group_arr = $_POST['group'];
        }else{
          $group_arr = $_POST['group_hidden'];
        }
        $type_arr = $_POST['type'];

		foreach($_POST['space'] as $k => $val) {
			if(empty($val) || $type_arr[$k]!=1){
		        $_POST['space'][$k] = "0";	
			}	
		}

        foreach($a_id_arr as $key => $value){
          $sql_arr = array(
              'date' => $_POST['get_date'],
              'month' => $date_info['month'],
              'day' => $date_info['day'],
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'group_id' => $group_arr[$key],
              'type' => $type_arr[$key],
              'add_user' => $user,
              'add_time' => 'now()',
			  'space' => $_POST['space'][$key],
              );
          if(isset($_POST['default_gid'])&&$_POST['default_gid']!=''){
            $sql_arr['group_id'] = $_POST['default_gid'];
          }
          if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
            &&!empty($_POST['data_as'])){
            $sql_other_arr = array(
                'update_user' => $user,
                'update_time' => 'now()',
              );
            $sql_arr = tep_array_merge($sql_arr,$sql_other_arr);
          }
          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr);
        }
      }
      if(isset($_POST['del_as'])&&!empty($_POST['del_as'])){
        foreach($_POST['del_as'] as $del_as){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where
              id="'.$del_as.'"');
        }
      }
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'delete_as_user_list':
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        foreach($_POST['data_as'] as $add_id){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where id="'.$add_id.'"');
        }
      }
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'delete_as_list':
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        foreach($_POST['data_as'] as $add_id){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where id="'.$add_id.'"');
        }
      }
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'delete_as_replace':
      tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_REPLACE.' where
          id="'.$_POST['replace_id'].'"');
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'save_as_replace':
      $user = $_SESSION['user_name'];
      $date = $_POST['get_date'];
      if(isset($_POST['attendance_detail_id'])&&$_POST['attendance_detail_id']!=''){
        $attendance_detail_id = $_POST['attendance_detail_id'];
      }else{
        $attendance_detail_id = $_POST['attendance_detail_id_hidden'];
      }
      $user_id = $_POST['user_id'];
      $replace_attendance_detail_id = $_POST['replace_attendance_detail_id'];
      $allow_status = $_POST['allow_status'];
      $leave_start = $_POST['leave_start_hour'].':'.$_POST['leave_start_minute_a'].$_POST['leave_start_minute_b'];
      $leave_end = $_POST['leave_end_hour'].':'.$_POST['leave_end_minute_a'].$_POST['leave_end_minute_b'];
      $allow_user = implode('|||',$_POST['allow_user']);
      $text_info = $_POST['text_info'];
      if(isset($_POST['replace_id'])&&$_POST['replace_id']!=''&&$_POST['replace_id']!=0) {
        $sql_update_arr = array(
            'replace_attendance_detail_id' => $replace_attendance_detail_id,
            'leave_start' => $leave_start,
            'leave_end' => $leave_end,
            'allow_user' => $allow_user,
            'text_info' => $text_info,
            'update_user' => $user,
            'update_time' => 'now()',
            );
        $sql_replace = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." WHERE 
          id='".$_POST['replace_id']."'";
        $query_replace = tep_db_query($sql_replace);
        if($row_replace = tep_db_fetch_array($query_replace)){
          $u_list = explode('|||',$row_replace['allow_user']);
          if(in_array($ocertify->auth_user,$u_list)||$ocertify->npermission>10){
            $sql_update_arr['allow_status'] = $allow_status;
          }
        }
        tep_db_perform(TABLE_ATTENDANCE_DETAIL_REPLACE,$sql_update_arr,'update','id=\''.$_POST['replace_id'].'\'');
      }else{
        $sql_insert_arr = array(
            'date' => $date,
            'user' => $user_id,
            'attendance_detail_id' => $attendance_detail_id,
            'replace_attendance_detail_id' => $replace_attendance_detail_id,
            'allow_status' => $allow_status,
            'leave_start' => $leave_start,
            'leave_end' => $leave_end,
            'allow_user' => $allow_user,
            'text_info' => $text_info,
            'add_user' => $user,
            'add_time' => 'now()',
            );
        tep_db_perform(TABLE_ATTENDANCE_DETAIL_REPLACE,$sql_insert_arr);
      }
      if(isset($_POST['get_date'])&&$_POST['get_date']!=''){
        $date_info = tep_date_info($_POST['get_date']);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,'y='.$date_info['year'].'&m='.$date_info['month']));
      }else{
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS));
      }
      break;
    case 'update_show_user':
      $operator_id = $ocertify->auth_user;
      if(isset($_POST['show_group_user_list'])&&
          is_array($_POST['show_group_user_list'])&&
          !empty($_POST['show_group_user_list'])){
        //删除当组数据
        //修改其他组是否显示
        $del_sql = "delete from ".TABLE_ATTENDANCE_GROUP_SHOW." WHERE gid='".$_POST['show_group']."' and operator_id='".$operator_id."'";
        tep_db_query($del_sql);
        $update_sql = "update ".TABLE_ATTENDANCE_GROUP_SHOW." set is_select=0 where operator_id='".$operator_id."'";
        tep_db_query($update_sql);
        //重新插入数据
        $insert_arr = array();
        foreach($_POST['show_group_user_list'] as $user_id_tmp){
          $insert_arr['gid'] = $_POST['show_group'];
          $insert_arr['user_id'] = $user_id_tmp;
          $insert_arr['is_select'] = '1';
          $insert_arr['operator_id'] = $operator_id;
          tep_db_perform(TABLE_ATTENDANCE_GROUP_SHOW,$insert_arr);
        }
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
      }elseif(empty($_POST['show_group_user_list'])) {
        //当没有选择用户的时候
		//操作原有数据
        $del_sql = "delete from ".TABLE_ATTENDANCE_GROUP_SHOW." WHERE gid='".$_POST['show_group']."' and operator_id='".$operator_id."'";
        tep_db_query($del_sql);
        $update_sql = "update ".TABLE_ATTENDANCE_GROUP_SHOW." set is_select=0 where operator_id='".$operator_id."'";
        tep_db_query($update_sql);
       //添加新数据
        $insert_arr = array();
        $insert_arr['gid'] = $_POST['show_group'];
        $insert_arr['user_id'] = '';
        $insert_arr['is_select'] = '1';
        $insert_arr['operator_id'] = $operator_id;
        tep_db_perform(TABLE_ATTENDANCE_GROUP_SHOW,$insert_arr);

        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
	  }
      break;
	  /**
	   *attendance_detail
	   */
case 'insert':
case 'update':
	 tep_isset_eof();
	$id = $_POST['id'];
	 $title = tep_db_prepare_input($_POST['title']);
	 $short_language = tep_db_prepare_input($_POST['short_language']);
     $param_a = tep_db_prepare_input($_POST['param_a']);
     $param_b = tep_db_prepare_input($_POST['param_b']);
	 $sort = tep_db_prepare_input($_POST['sort']);
	 $scheduling_type = $_POST['scheduling_type'];
	 $set_time = tep_db_prepare_input($_POST['set_time']);
	 $work_start=$_POST['work_start_hour'].':'.$_POST['work_start_minute_a'].$_POST['work_start_minute_b'];
	 $work_end=$_POST['work_end_hour'].':'.$_POST['work_end_minute_a'].$_POST['work_end_minute_b'];
	 $rest_start=$_POST['rest_start_hour'].':'.$_POST['rest_start_minute_a'].$_POST['rest_start_minute_b'];
	 $rest_end=$_POST['rest_end_hour'].':'.$_POST['rest_end_minute_a'].$_POST['rest_end_minute_b'];
	 $work_hours=tep_db_prepare_input($_POST['work_hours']);
	 $rest_hours=tep_db_prepare_input($_POST['rest_hours']);
	 $user_info = tep_get_user_info($ocertify->auth_user);
	 $add_user=$user_info['name'];
	 $add_time=date('Y-m-d H:i:s',time());
	 $update_user=$user_info['name'];
	 $update_time=date('Y-m-d H:i:s',time());

	 if($scheduling_type ==0){
	 
	 //上传图片
	 $src_image = tep_get_uploaded_file('src_image');
     	  if (!empty($src_image['name'])) {
             $pic_rpos = strrpos($src_image['name'], ".");
             $pic_ext = substr($src_image['name'], $pic_rpos+1);
             $tep_image_name = 'attendance'.time().".".$pic_ext;
             $src_image['name'] = $tep_image_name;

	         $image_directory = tep_get_local_path(DIR_FS_CATALOG.'images/');
             $path = 'roster_records/';

             if (is_uploaded_file($src_image['tmp_name'])) {
			     //删除之前的图片
			     $sql_image = "select src_text from `".TABLE_ATTENDANCE_DETAIL."` where id=".$id;
			     $tep_res = tep_db_query($sql_image);
		         $row=  tep_db_fetch_array($tep_res);
			     if(count($row)){
			         unlink($image_directory.'/'.$row['src_text']);
			     }
			 
			     $src_text = $path.$tep_image_name;
			     tep_copy_uploaded_file($src_image, $image_directory.  '/roster_records/');

	         }	
	 
         } else {
             $src_text = $_POST['src_image_input'];
          }


	 }elseif($scheduling_type==1) {
	     $src_text = $_POST['scheduling_type_color'];
	 }


	 $sql_data_array =array(
	   'title' => $title,
	   'short_language' => $short_language,
	   'src_text'=> $src_text,
	   'param_a' => $param_a, 
	   'param_b' => $param_b, 
       'sort' => $sort,
	   'scheduling_type' => $scheduling_type,
	   'set_time' => $set_time,
       'work_start' => $work_start,
	   'work_end' => $work_end,
	   'rest_start' => $rest_start,
	   'rest_end' => $rest_end,
	   'work_hours' => $work_hours,
	   'rest_hours' => $rest_hours,
	   'add_user' => $add_user,
	   'add_time' => $add_time,
	   'update_user' => $update_user,
	   'update_time' => $update_time
	 );

	 if($_GET['action']=='insert'){
	 tep_db_perform(TABLE_ATTENDANCE_DETAIL, $sql_data_array);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
	 }elseif ($_GET['action']=='update'){
	 
	 tep_db_perform(TABLE_ATTENDANCE_DETAIL, $sql_data_array, 'update',  "id = '" .$id  . "'");
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
	 }
	 break;

  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ROSTER_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/admin_roster_records.js"></script>

<script language="javascript">
var warn_attendance_type_diff = '<?php echo TEXT_WARN_ATTENDANCE_TYPE_DIFF;?>';
var js_remind_delete = '<?php echo TEXT_DELETE_REMIND;?>';
var js_text_input_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
var js_text_onetime_pwd_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
var error_text = '<?php echo TEP_ERROR_NULL;?>';
var href_attendance_calendar = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_ROSTER_RECORDS;?>';
var admin_id = '<?php echo $ocertify->auth_user;?>';
var admin_npermission = '<?php echo $ocertify->npermission;?>';
$(document).ready(function() {
  <?php //监听按键?>
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?>
      if ($('#show_attendance_edit').css('display') != 'none') {
        hidden_info_box();
      }
    }
    if (event.which == 13) {
      <?php //回车?>
      if ($('#show_attendance_edit').css('display') != 'none') {
        $("#button_save").trigger("click");
      }
    }
  });
});
</script>

<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>

</head>
<body bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><div id="show_attendance_edit" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 60%; display:none;"></div><table border="0" width="100%" cellspacing="0" cellpadding="1">
    <div id="show_delete_box" style="min-width: 450px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 60%; display:none;"></div>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo ROSTER_TITLE_TEXT; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <?php
      //判断用户是否打过卡
        $user_atted = array();
        $status_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $status_str .= '<td>userlist</td>';
        $status_str .= '</tr></table>';
        $attendance_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $attendance_str .= '<td>attendance</td>';
        $attendance_str .= '</tr></table>';


        $user_info = tep_get_user_info($ocertify->auth_user);
        $group_list = tep_get_group_tree();
        $show_group_id=0;
        $show_group_user = array();
        $show_select_group_user = array();
        $show_group_sql = "select * from ".TABLE_ATTENDANCE_GROUP_SHOW." WHERE is_select='1' and operator_id='".$ocertify->auth_user."'";
        $show_group_query = tep_db_query($show_group_sql);
        $has_default = false;
        while($show_group_row = tep_db_fetch_array($show_group_query)){
          $has_default = true;
          $show_group_id = $show_group_row['gid'];
          if($show_group_row['user_id']!=''){
            $show_select_group_user[] = $show_group_row['user_id'];
          }
        }
        if($has_default){
          if($show_group_id==0){
            $user_sql = "select * from ".TABLE_USERS." where status='1'";
            $user_query = tep_db_query($user_sql);
            while($user_row = tep_db_fetch_array($user_query)){
              $show_group_user[] = $user_row['userid'];
            }
          } else {
            $user_sql = "select * from ".TABLE_GROUPS." 
               where id='".$show_group_id."'";
            $user_query = tep_db_query($user_sql);
            if($user_row = tep_db_fetch_array($user_query)){
              $show_group_user = explode('|||',$user_row['all_users_id']);
            }
          }
        }else{
          if($ocertify->npermission>10){
            $user_sql = "select * from ".TABLE_USERS." where status='1'";
            $user_query = tep_db_query($user_sql);
            while($user_row = tep_db_fetch_array($user_query)){
              $show_group_user[] = $user_row['userid'];
              $show_select_group_user[] = $user_row['userid'];
            }
          }else{
            $prent_group = tep_get_groups_by_user($ocertify->auth_user);
            if(!empty($prent_group)){
              $show_group_id = $prent_group[0];
              if($show_group_id==0){
                $user_sql = "select * from ".TABLE_USERS." where status='1'";
                $user_query = tep_db_query($user_sql);
                while($user_row = tep_db_fetch_array($user_query)){
                  $show_group_user[] = $user_row['userid'];
                }
              } else {
                $user_sql = "select * from ".TABLE_GROUPS." 
                   where id='".$show_group_id."'";
                $user_query = tep_db_query($user_sql);
                if($user_row = tep_db_fetch_array($user_query)){
                  $show_group_user = explode('|||',$user_row['all_users_id']);
                }
              }
            }else{
              $user_sql = "select * from ".TABLE_USERS." where status='1'";
              $user_query = tep_db_query($user_sql);
              while($user_row = tep_db_fetch_array($user_query)){
                $show_group_user[] = $user_row['userid'];
              }
            }
          }
          $show_select_group_user[] = $ocertify->auth_user;
        }
        $show_select_group_user = array_unique($show_select_group_user);
        $group_str = '<form action="'.
        tep_href_link(FILENAME_ROSTER_RECORDS,'action=update_show_user'.
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')).'" method="post">';
        $group_str .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        $group_str .= '<tr >';
        $group_str .= '<td width="15%" align="left">';
        $group_str .= TEXT_GROUP_SELECT;
        $group_str .= '</td>';
        $group_str .= '<td colspan="2" align="left">';
        $group_str .= '<select name="show_group" onchange="change_user_list(this)">';
        $group_str .= '<option value="0" ';
        if($show_group_id==0){
          $group_str .= ' selected ';
        }
        $group_str .= ' >'.TEXT_ALL_GROUP.'</option>';
        foreach($group_list as $group){
          $group_str .= '<option value="'.$group['id'].'"';
          if($show_group_id == $group['id']){
            $group_str .= ' selected ';
          }
          $group_str .= '>'.$group['text'].'</oprion>';
        }
        $group_str .= '</select>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '<tr>';
        $group_str .= '<td valign="top">';
        $group_str .= TEXT_GROUP_USER_LIST;
        $group_str .= '</td>';
        $group_str .= '<td align="left">';
        $group_str .= '<div id="show_user_list">';
        foreach($show_group_user as $show_list_uid){
          if($show_list_uid!=''){
			$tep_array= tep_get_user_info($show_list_uid);
			$uname_arr[] = $tep_array['name'];

          }
        }
		$group_user_list = array_combine($show_group_user,$uname_arr);
		asort($group_user_list);

		foreach($group_user_list as $key=>$val) {
          $group_str .= '<input type="checkbox" name="show_group_user_list[]" id="'.$key.'"';
          if(in_array($key,$show_select_group_user)){
            $group_str .= ' checked="checked" ';
            $user_atted[$key] = tep_is_attenandced_date($key);
          }
          $group_str .= ' value="'.$key.'" >';
          $group_str .=  '<label for="'.$key.'">'.$val.'</label>';
          $group_str .= '&nbsp;&nbsp;&nbsp;';
		}

        $group_str .= '</div>';
        $group_str .= '</td>';
        $group_str .= '<td align="right">';
        $group_str .= '<input type="submit" value="'.TEXT_UPDATE.'">';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '</table></form>';
      ?>
      <tr>
        <td><div id="toggle_width" style="min-width:726px;"></div><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
            <td class="main" align="right">
<table  style=" margin-top: -30px; min-width: 450px;" width="85%">
<tr>
<td align="left">
<ul style="padding: 0px;">
<?php 

$param_attendance = $_SERVER['QUERY_STRING'];
$param_tep = explode('&',$param_attendance);
if($param_tep[0]!=''){
	if(count($param_tep)>1){
    $param .=','.$param_tep[0].','.$param_tep[1];
	}
}

$att_select_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
$tep_result = tep_db_query($att_select_sql);

 $attendance_list=array();
 while($rows= tep_db_fetch_array($tep_result)) {
   $attendance_list[] = $rows;
 }
$all_user_info = array();
$all_user_sql = "select * from ". TABLE_USERS ." where status='1'";
$all_user_query = tep_db_query($all_user_sql);
while($user_info_row = tep_db_fetch_array($all_user_query)){
  $all_user_info[] = $user_info_row['userid'];
}

 $num = count($attendance_list);
 $i=0;
 foreach($attendance_list as $k=>$val) {
 if($val['scheduling_type']==0){
    $image_directory = 'images';
    $image_dir = $image_directory.'/'.$val['src_text'];
	echo "<li style='float:right; height:16px; list-style-type:none; margin-right: 10px; margin-top:5px;'><img src='".$image_dir."' style='width: 16px;'>"; 
}elseif($val['scheduling_type']==1){
     echo '<li style="float:right; height:16px; list-style-type:none; margin-right: 10px; margin-top:5px;"><div style="float: left; background-color:'.$val['src_text'].'; border: 1px solid #CCCCCC; padding: 6px;"></div>';
 }
echo  '<a onclick="show_attendance_info(this, '.$val['id'].$param.')" href="javascript:void(0);" style="text-decoration: underline;"> >> '.$val['title'].'</a></li>';
 }

echo '</ul>';
echo ' </td><td valign="top">';

if($ocertify->npermission>'10'){
    echo '<ul style="padding: 0px;"><li style="list-style-type:none;"><a onclick="show_attendance_info(this,0'.$param.')" href="javascript:void(0);">' .tep_html_element_button(IMAGE_NEW_ATTENDANCE,'id="create_attendance" ').' </a></li></ul></td>';
}
 
?> 
</table>
            </td>
          </tr><tr>
            <td class="main" align="left">
              <?php echo $group_str;?> 
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="date_title_color">
          <tr bgcolor="#3C7FB1">
            <td class="date_title" align="center">
            <a href="<?php echo FILENAME_ROSTER_RECORDS.$str_prev_str;?>"><b></b></a>
			<?php $month= substr($month,0,1)==0?substr($month,1,2):$month;?>

            &nbsp;&nbsp;<font color="#FFF"><?php echo $year.' / '.$month; ?></font>&nbsp;&nbsp;
            <a href="<?php echo FILENAME_ROSTER_RECORDS.$str_next_str;?>"><b></b></a></td>
          </tr>
		</table>
<table  border="0" width="100%" cellspacing="0" cellpadding="0">
<tr class="date_month">
		<td width="80%">
			<ul>
			<li id="date_month_frist"><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_prev_year?>">&lt;&lt;<?php echo TEXT_PRE_YEAR;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'1'; ?>"><?php echo TEXT_MONTH_JANUARY;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'2'; ?>"><?php echo TEXT_MONTH_FEBRUARY;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'3'; ?>"><?php echo TEXT_MONTH_MARCH;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'4'; ?>"><?php echo TEXT_MONTH_APRIL;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'5'; ?>"><?php echo TEXT_MONTH_MAY;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'6'; ?>"><?php echo TEXT_MONTH_JUNE;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'7'; ?>"><?php echo TEXT_MONTH_JULY;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'8'; ?>"><?php echo TEXT_MONTH_AUGUST;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'9'; ?>"><?php echo TEXT_MONTH_SEPTEMBER;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'10'; ?>"><?php echo TEXT_MONTH_OCTOBER;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'11'; ?>"><?php echo TEXT_MONTH_NOVEMBER;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_str.'12'; ?>"><?php echo TEXT_MONTH_DECEMBER;?></a></li>
			<li><a href="<?php echo FILENAME_ROSTER_RECORDS.$str_next_year?>"><?php echo TEXT_NEXT_YEAR;?>&gt;&gt;</a></li>
            </ul>
        </td>
        <td align="right">
		<?php  
			
$today = date('Ymd',time());
$today_date= tep_date_info($today);

$year_tep=$today_date['year']; 
$month_tep=$today_date['month']; 

if($month==12){
   $next_month = 1;
   $next_year = $year+1;
   $prev_month = $month-1;
  $prev_year = $year;
 }else if($month_tep==1){
   $next_month = $month+1;
   $next_year = $year;
   $prev_month = 12;
   $prev_year = $year-1;
}else{
   $next_month = $month+1;
   $next_year = $year;
   $prev_month = $month-1;
   $prev_year = $year;
 }
$str_next_month = '?y='.$next_year.'&m='.$next_month;
$str_prev_month = '?y='.$prev_year.'&m='.$prev_month;

?>
		<input type="button" value="<<" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ROSTER_RECORDS. $str_prev_month)?>'">
		<input type="button" value="<?php echo TEXT_NOW_MONTH;?>" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ROSTER_RECORDS.'?y='.$today_date['year'].'&m='.$today_date['month'])?>'">
		<input type="button" value=">>" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ROSTER_RECORDS.$str_next_month)?>'">
        
        </td>
      </tr>
</table>
</td>
	  </tr>
      <tr>
        <td>
<?php
//每月的出勤信息 根据设置信息


$start_week = date('w',mktime(0,0,0,$month,1,$year));
$day_num = date('t',mktime(0,0,0,$month,1,$year));
$end = false;


//初始化 获得所有排班
$all_att_arr = array();
$all_att_sql = "select * from ".TABLE_ATTENDANCE_DETAIL;
$all_att_auery = tep_db_query($all_att_sql);
while($all_att_row = tep_db_fetch_array($all_att_auery)){
  $all_att_arr[$all_att_row['id']] = $all_att_row;
}
?>
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="dataTable_border">
<tr>
<?php 
echo '
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_SUNDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_MONDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_TUESDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_WEDNESDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_THURSDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_FRIDAY.'</font></td>
        <td width="14%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_STATURDAY.'</font></td>
        ';
        ?>
</tr>
<tr>
<?php
for($i = 0; $i<$start_week; $i++)
{
  echo "<td></td>";
}

$j=1;
while($j<=$day_num)
{
  $date = $year.tep_add_front_zone($month).tep_add_front_zone($j);
  $att_arr = tep_get_attendance($date,$show_group_id,false);
  $user_att_arr = tep_get_attendance_user($date,'',false);
  if($j==23){
  }
  if(!empty($show_att_user_list)){
    asort($show_att_user_list);
  }
  $style= (empty($att_arr)) ? '':'cursor:pointer;';
  echo "<td id='date_td_".$j."'  valign='top' >";
  echo '<div id ="table_div_databox_minsize"><table width="100%" border="0"
    cellspacing="0" cellpadding="0" class="uroster_record">';
  echo "<tr><td align='left' style='font-size:14px; border-width:0px; cursor:pointer;' ";
  if($ocertify->npermission>10||tep_is_group_manager($ocertify->auth_user)){
    echo " onclick='attendance_setting(\"".$date."\",\"".$j."\",\"\")' >";
  }else{
    if($today <= $date){
      echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$ocertify->auth_user."\")' >";
    }else{
      echo " >";
    }
  }
  if($date == $today){
    echo "<div class='dataTable_hight_red'>";
    echo $j;
    echo "</div>";
  }else{
    echo $j;
  }
  echo "</td></tr>";
  $user_worker_list = array();
  $user_att_info = array();
  foreach($att_arr as $att_row){
    $att_info = $all_att_arr[$att_row['attendance_detail_id']];
    if(!empty($att_info)){
    if(!empty($show_select_group_user)&&$date){
    if(tep_is_show_att($att_row['id'],$date)){
      echo "<tr>";
      if($att_info['scheduling_type'] == 0){
		  echo '<td style="border-width:1px;">';
        echo "<div onclick='attendance_setting(\"".$date."\",\"".$j."\",\"".$att_row['group_id']."\",\"".$att_row['id']."\")' style=".$style.">";
        echo $att_info['short_language'].'<img style="width:16px;" src="images/'.$att_info['src_text'].'" alt="'.$att_info['title'].'">';
      }else{
        echo "<td bgcolor='".$att_info['src_text']."'>";
        echo "<div onclick='attendance_setting(\"".$date."\",\"".$j."\",\"".$att_row['group_id']."\",\"".$att_row['id']."\")' style=".$style.">";
        echo $att_info['short_language'];
      }
      echo "</div>";
      foreach($show_select_group_user as $u_list){
        //去除 单人排班的
        if(in_array($u_list,$all_user_list)){
          continue;
        }
        if(in_array($att_row['group_id'],tep_get_groups_by_user($u_list))){
          if($date<= $today){
            $v_att = tep_valadate_attendance($u_list,$date,$att_info,$att_info['src_text'],$j);
          }else{
            $v_att = false;
          }
        $replace_str ='';
        $user_replace = tep_get_replace_by_uid_date($u_list,$date,$att_row['attendance_detail_id']);
        echo "<span>";
        if(!empty($user_replace)){
          $user_worker_list[] = $u_list;
          $att_date_info = tep_get_attendance_by_id($user_replace['replace_attendance_detail_id']);
          if(in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$u_list
              ||$ocertify->npermission>'10'||in_array($ocertify->auth_user,tep_get_user_list_by_userid($user_replace['user']))){
          if($att_date_info['scheduling_type'] == 1){
            $replace_str =  '<span class="rectangle" style="background-color:'.$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text'].';">&nbsp;</span>';
          }else{
            $replace_str = "<img src='images/".$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text']."' alt='".$all_att_arr[$user_replace['replace_attendance_detail_id']]['alt_text']."' style='width: 16px;'>";
          }
          if($user_replace['allow_status']==0&&
              (in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user'])){
            $replace_str .= "<img src='images/icons/mark.gif' alt='UNALLOW'>";
          }
        }
        }
        echo "<a href='javascript:void(0)' ";
        $manager_list = tep_get_user_list_by_userid($u_list);
        if($ocertify->auth_user==$u_list||$ocertify->npermission>'10'||in_array($ocertify->auth_user,$manager_list)){
          if($date>=$today||!empty($user_replace)){
            echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$u_list."\",\"".$att_row['attendance_detail_id']."\")' ";
          }
        }else{
          $replace_str = '';
        }
        echo ">";
        if($v_att!=false){
          echo preg_replace("/<br>$/",$replace_str.'<br>',$v_att);
        }else{
          $temp_user_sql = "select * from ".TABLE_GROUPS." 
            where id='".$att_row['group_id']."'";
          $temp_user_query = tep_db_query($temp_user_sql);
          if($temp_user_row = tep_db_fetch_array($temp_user_query)){
            $temp_show_group_user = explode('|||',$temp_user_row['all_users_id']);
          }
          if(in_array($u_list,$temp_show_group_user)){
            $t_info = tep_get_user_info($u_list);
            echo $t_info['name'].$replace_str.'&nbsp;';
          }
        }
        echo "</a>";
        }
        echo "</span>";
      }
    }

    echo "</td>";
    echo "</tr>";
    }
    }
  }
  // 跟人排班显示
  foreach($user_att_arr as $uatt_arr){
    if(tep_is_show_att($uatt_arr['id'],$date)&&!empty($uatt_arr)&&in_array($uatt_arr['user_id'],$show_select_group_user)){
      $att_user_row = $uatt_arr;
      $att_info = $all_att_arr[$att_user_row['attendance_detail_id']];
      echo "<tr>";
      if($att_info['scheduling_type'] == 0){
        echo '<td style="border-width:1px;">';
        echo "<div onclick='attendance_setting_user(\"".$date."\",\"".$j."\",\"".$att_user_row['user_id']."\",\"".$att_user_row['id']."\")' style='cursor:pointer;'>";
        echo $att_info['short_language'].'<img style="width:16px;" src="images/'.$att_info['src_text'].'" alt="'.$att_info['title'].'">';
      }else{
        echo "<td bgcolor='".$att_info['src_text']."'>";
        echo "<div onclick='attendance_setting_user(\"".$date."\",\"".$j."\",\"".$att_user_row['user_id']."\",\"".$att_user_row['id']."\")' style='cursor:pointer;'>";
        echo $att_info['short_language'];
      }
      echo "</div>";

      $v_att = tep_valadate_attendance($uatt_arr['user_id'],$date,$att_info,$att_info['src_text'],$j);
      $replace_str ='';
      $user_replace = tep_get_replace_by_uid_date($uatt_arr['user_id'],$date,$att_info['attendance_detail_id']);
      echo "<span>";
      if(!empty($user_replace)){
          $user_worker_list[] = $uatt_arr['user_id'];
          $att_date_info = tep_get_attendance_by_id($user_replace['replace_attendance_detail_id']);
          if(in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$uatt_arr['user_id']
              ||$ocertify->npermission>'10'){
          if($att_date_info['scheduling_type'] == 1){
            $replace_str =  '<span class="rectangle" style="background-color:'.$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text'].';">&nbsp;</span>';
          }else{
            $replace_str = "<img src='images/".$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text']."' alt='".$all_att_arr[$user_replace['replace_attendance_detail_id']]['alt_text']."' style='width: 16px;'>";
          }
          if($user_replace['allow_status']==0&&
              (in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user'])){
            $replace_str .= "<img src='images/icons/mark.gif' alt='UNALLOW'>";
          }
        }
      }


      echo "<a href='javascript:void(0)' ";
      $manager_list = tep_get_user_list_by_userid($uatt_arr['user_id']);
      if($ocertify->auth_user==$att_uid||$ocertify->npermission>'10'){
        if($date>=$today||!empty($user_replace)){
          echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$uatt_arr['user_id']."\",\"".$att_user_row['attendance_detail_id']."\")' ";
        }
      }else{
        $replace_str = '';
      }
      echo ">";
      if($v_att!=false){
        echo preg_replace("/<br>$/",$replace_str.'<br>',$v_att);
      }else{
        echo $att_uname.$replace_str."&nbsp;";
      }
      echo "</a>";

      echo "</span>";
      echo "</td>";
      echo "</tr>";
    }
  }



  //不在排班组的请假
    echo "<tr><td style='font-size:14px; border-width:0px;'>";
    echo '<div>';
    $sql_replace_att = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." WHERE 
      `date` = '".$date."'";
    $query_replace_att = tep_db_query($sql_replace_att);
    while($row_replace_att = tep_db_fetch_array($query_replace_att)){
      if(!in_array($row_replace_att['user'],$user_worker_list)&&in_array($row_replace_att['user'],$show_select_group_user)){
      $user_replace = tep_get_replace_by_uid_date($row_replace_att['user'],$date);
      $manager_list = tep_get_user_list_by_userid($row_replace_att['user']);
      $show_flag = false;
      if(!empty($user_replace)&&$user_replace['allow_status']==1){
        $show_flag = true;
      }
      if((!empty($user_replace))&&($show_flag||$ocertify->auth_user==$row_replace_att['user']||$ocertify->npermission>'10'||in_array($ocertify->auth_user,$manager_list))){
      $u_info = tep_get_user_info($row_replace_att['user']);
      $att_date_info = tep_get_attendance_by_id($row_replace_att['replace_attendance_detail_id']);
      echo "<span>";
      echo "<a href='javascript:void(0)' ";
      echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$row_replace_att['user']."\")' ";
      echo " >";
      if($show_flag||in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user']){
      if(!empty($u_info)){
        echo $u_info['name'];
      }
      if(!empty($att_date_info)){
        if($att_date_info['scheduling_type'] == 1){
          echo '<span class="rectangle" style="background-color:'.$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text'].';">&nbsp;</span>';
        }else{
          echo "<img src='images/".$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text']."' alt='".$all_att_arr[$user_replace['replace_attendance_detail_id']]['alt_text']."' style='width: 16px;'>";
        }
      }
      if($user_replace['allow_status']==0&&
           (in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user'])){
        echo "<img src='images/icons/mark.gif' alt='UNALLOW'>";
      }
      }
      echo "</a>";
      echo "</span>";
    }
    }
    }
    echo '</div>';
    echo "</td></tr>";
  echo "</table>";
  echo "</td>";
  $week = ($start_week+$j-1)%7;

  if($week == 6){
    echo "</tr>";
    if($j != $day_num)
      echo "<tr>";
    else $end = true;
  }
  $j++;
}
while($week%7 != 6)
{
  echo "<td></td>";
  $week++;
}
if(!$end)
  echo "</tr>";
  ?>

  </table>

    </td>
      </tr> 
  </table></div></div></td>
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
