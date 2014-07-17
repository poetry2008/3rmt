<?php
/*
  $Id$
*/
include("includes/application_top.php");

$month = $_GET['m']?$_GET['m']:date('n');
$year = $_GET['y']?$_GET['y']:date('Y');
if($month==12){
  $next_month = 1;
  $next_year = $year+1;
  $prev_month = $month-1;
  $prev_year = $year;
}else if($month==1){
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
$str_next_str = '?y='.$next_year.'&m='.$next_month;
$str_prev_str = '?y='.$prev_year.'&m='.$prev_month;
if(isset($_GET['action'])){
  switch($_GET['action']){
    case 'save_as_list':
      $date_info = tep_date_info($_POST['get_date']);
      $user = $_SESSION['user_name'];
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        $a_id_arr = $_POST['has_attendance_id'];
        $group_arr = $_POST['has_group'];
        $type_arr = $_POST['has_type'];
        foreach($a_id_arr as $key => $value){
          $sql_arr = array(
              'date' => $_POST['get_date'],
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'group_id' => $group_arr[$key],
              'type' => $type_arr[$key],
              'update_user' => $user,
              'update_time' => 'now()',
              );
          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
        }
      }
      if(isset($_POST['attendance_id'])
          &&is_array($_POST['attendance_id'])
          &&!empty($_POST['attendance_id'])){
        $a_id_arr = $_POST['attendance_id'];
        $group_arr = $_POST['group'];
        $type_arr = $_POST['type'];
        foreach($a_id_arr as $key => $value){
          $sql_arr = array(
              'date' => $_POST['get_date'],
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'group_id' => $group_arr[$key],
              'type' => $type_arr[$key],
              'add_user' => $user,
              'add_time' => 'now()',
              );
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
    case 'save_as_replace':
      $user = $_SESSION['user_name'];
      $date = $_POST['get_date'];
      $attendance_detail_id = $_POST['attendance_detail_id'];
      $user_id = $ocertify->auth_user;
      $replace_attendance_detail_id = $_POST['replace_attendance_detail_id'];
      $allow_satus = $_POST['allow_satus'];
      $leave_start = $_POST['leave_start_hour'].':'.$_POST['leave_start_minute_a'].$_POST['leave_start_minute_b'];
      $leave_end = $_POST['leave_end_hour'].':'.$_POST['leave_end_minute_a'].$_POST['leave_end_minute_b'];
      $allow_user = implode('|||',$_POST['allow_user']);
      if(isset($_POST['replace_id'])&&$_POST['replace_id']!=''&&$_POST['replace_id']!=0) {
        $sql_update_arr = array(
            'replace_attendance_detail_id' => $replace_attendance_detail_id,
            'leave_start' => $leave_start,
            'leave_end' => $leave_end,
            'allow_user' => $allow_user,
            'update_user' => $user,
            'update_time' => 'now()',
            );
        $sql_replace = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." WHERE 
          id='".$_POST['replace_id']."'";
        $query_replace = tep_db_query($sql_replace);
        if($row_replace = tep_db_fetch_array($query_replace)){
          $u_list = explode('|||',$row_replace['allow_user']);
          if(in_array($user_id,$u_list)||$ocertify->npermission=='31'){
            $sql_update_arr['allow_status'] = $allow_satus;
          }
        }
        tep_db_perform(TABLE_ATTENDANCE_DETAIL_REPLACE,$sql_update_arr,'update','id=\''.$_POST['replace_id'].'\'');
      }else{
        $sql_insert_arr = array(
            'date' => $date,
            'user' => $user_id,
            'attendance_detail_id' => $attendance_detail_id,
            'replace_attendance_detail_id' => $replace_attendance_detail_id,
            'allow_status' => $allow_satus,
            'leave_start' => $leave_start,
            'leave_end' => $leave_end,
            'allow_user' => $allow_user,
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
      if(isset($_POST['show_group_user_list'])&&
          is_array($_POST['show_group_user_list'])&&
          !empty($_POST['show_group_user_list'])){
        //删除当组数据
        //修改其他组是否显示
        $del_sql = "delete from ".TABLE_ATTENDANCE_GROUP_SHOW." WHERE gid='".$_POST['show_group']."'";
        tep_db_query($del_sql);
        $update_sql = "update ".TABLE_ATTENDANCE_GROUP_SHOW." set is_select=0";
        tep_db_query($update_sql);
        //重新插入数据
        $insert_arr = array();
        foreach($_POST['show_group_user_list'] as $user_id_tmp){
          $insert_arr['gid'] = $_POST['show_group'];
          $insert_arr['user_id'] = $user_id_tmp;
          $insert_arr['is_select'] = '1';
          tep_db_perform(TABLE_ATTENDANCE_GROUP_SHOW,$insert_arr);
        }
      }
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
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
	 $approve_person = tep_db_prepare_input($_POST['approve_person']);
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
             $path = 'attendance/';

             if (is_uploaded_file($src_image['tmp_name'])) {
			     //删除之前的图片
			     $sql_image = "select src_text from `".TABLE_ATTENDANCE_DETAIL."` where id=".$id;
			     $tep_res = tep_db_query($sql_image);
		         $row=  tep_db_fetch_array($tep_res);
			     if(count($row)){
			         unlink($image_directory.'/'.$row['src_text']);
			     }
			 
			     $src_text = $path.$tep_image_name;
			     tep_copy_uploaded_file($src_image, $image_directory. '/attendance/');

	         }	
	 
         } else {
             $src_text = $_POST['src_image_input'];
          }


	 }elseif($scheduling_type==1) {
	     $src_text = $_POST['scheduling_type_color'];
	 }

	 if(count($_POST['add_approve_person'])!=0){
        $_POST['add_approve_person']= array_unique($_POST['add_approve_person']);
		 for($i=0;$i<count($_POST['add_approve_person']);$i++) {
			 if($i==count($_POST['add_approve_person'])-1) {
			 
		 $str_tep .= $_POST['add_approve_person'][$i];
			 }else{
			 
		 $str_tep .= $_POST['add_approve_person'][$i].',';
			 }
		 }

      $approve_person = $str_tep;
	 }

	 $sql_data_array =array(
	   'title' => $title,
	   'short_language' => $short_language,
	   'src_text'=> $src_text,
	   'param_a' => $param_a, 
	   'param_b' => $param_b, 
       'sort' => $sort,
	   'approve_person' => $approve_person,
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
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
	 }elseif ($_GET['action']=='update'){
	 
	 tep_db_perform(TABLE_ATTENDANCE_DETAIL, $sql_data_array, 'update',  "id = '" .$id  . "'");
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
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
var attendance_del_confirm = '<?php echo ATTENDANCE_DELETE_REMIND;?>';
var error_text = '<?php echo TEP_ERROR_NULL;?>';
var href_attendance_calendar = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_ROSTER_RECORDS;?>';
$(document).ready(function() {
  <?php //监听按键?>
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?>
      if ($('#show_date_edit').css('display') != 'none') {
        hidden_info_box();
      }
    }
    if (event.which == 13) {
      <?php //回车?>
      if ($('#show_date_edit').css('display') != 'none') {
        $("#button_save").trigger("click");
      }
    }
  });
});
</script>
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><div id="show_date_edit" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 60%; display:none;"></div><table border="0" width="100%" cellspacing="0" cellpadding="1">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo ROSTER_TITLE_TEXT; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <?php
        $status_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $status_str .= '<td>userlist</td>';
        $status_str .= '</tr></table>';
        $attendance_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $attendance_str .= '<td>attendance</td>';
        $attendance_str .= '</tr></table>';


        $group_list = tep_get_group_tree();
        $show_group_id=0;
        $show_group_user = array();
        $show_select_group_user = array();
        $show_group_sql = "select * from ".TABLE_ATTENDANCE_GROUP_SHOW." WHERE is_select='1'";
        $show_group_query = tep_db_query($show_group_sql);
        while($show_group_row = tep_db_fetch_array($show_group_query)){
          $show_group_id = $show_group_row['gid'];
          $show_select_group_user[] = $show_group_row['user_id'];
        }
        if($show_group_id==0){
          $user_sql = 'select * from '.TABLE_USERS;
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
        $group_str = '<form action="'.
        tep_href_link(FILENAME_ROSTER_RECORDS,'action=update_show_user'.
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')).'" method="post">';
        $group_str .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        $group_str .= '<tr >';
        $group_str .= '<td width="150" align="left">';
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
        $group_str .= '<td align="left">';
        $group_str .= TEXT_GROUP_USER_LIST;
        $group_str .= '</td>';
        $group_str .= '<td align="left">';
        $group_str .= '<div id="show_user_list">';
        foreach($show_group_user as $show_list_uid){
          $group_str .= '<input type="checkbox" name="show_group_user_list[]" ';
          if(in_array($show_list_uid,$show_select_group_user)){
            $group_str .= ' checked="checked" ';
          }
          $group_str .= ' value="'.$show_list_uid.'" >';
          $user_info = tep_get_user_info($show_list_uid);
          $group_str .=  $user_info['name'];
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
<table  style=" margin-top: -30px; min-width: 450px;" width="65%">
<tr>
<td align="left">
<ul style="padding: 0px;">
<?php 

$att_select_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
$tep_result = tep_db_query($att_select_sql);

 $attendance_list=array();
 while($rows= tep_db_fetch_array($tep_result)) {
   $attendance_list[] = $rows;
 }
 $num = count($attendance_list);
 $i=0;
 foreach($attendance_list as $k=>$val) {
 if($val['scheduling_type']==0){
    $image_directory = 'images';
    $image_dir = $image_directory.'/'.$val['src_text'];
	echo "<li style='float:left; list-style-type:none; margin: 5px;'><img src='".$image_dir."' style='width: 16px;'>"; 
}elseif($val['scheduling_type']==1){
     echo '<li style="float:left; list-style-type:none; margin: 5px;"><div style="float: left; background-color:'.$val['src_text'].'; border: 1px solid #CCCCCC; padding: 6px;"></div>';
 }
echo  '<a onclick="show_attendance_info(this, '.$val['id'].')" href="javascript:void(0);" style="text-decoration: underline;"> >> '.$val['title'].'</a></li>';
 }

echo '</ul>';
echo ' </td><td valign="top">';
echo '<ul style="padding: 0px;"><li style="list-style-type:none;"><a onclick="show_attendance_info(this,0)" href="javascript:void(0);">' .tep_html_element_button(IMAGE_NEW_ATTENDANCE,'id="create_attendance" ').' </a></li></ul></td>';
 
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="date_title_color">
          <tr bgcolor="#3C7FB1">
            <td class="date_title" align="center">
            <a href="<?php echo FILENAME_ROSTER_RECORDS.$str_prev_str;?>"><b><<</b></a>
            &nbsp;&nbsp;<font color="#FFF"><?php echo $year.' / '.$month; ?></font>&nbsp;&nbsp;
            <a href="<?php echo FILENAME_ROSTER_RECORDS.$str_next_str;?>"><b>>></b></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
<?php
//每月的出勤信息 根据设置信息


$start_week = date('w',mktime(0,0,0,$month,1,$year));
$day_num = date('t',mktime(0,0,0,$month,1,$year));
$end = false;
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
$today = date('Ymd',time());
while($j<=$day_num)
{
  $date = $year.tep_add_front_zone($month).tep_add_front_zone($j);
  echo "<td style='cursor:pointer;' onclick='attendance_setting(\"".$date."\",this)'
    valign='top'>";
  $att_arr = tep_get_attendance($date,$show_group_id,false);
  echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
  echo "<tr><td align='left' style='font-size:14px; border-width:0px;'>";
  if($date == date('Ymd',time())){
    echo "<div class='dataTable_hight_red'>";
    echo $j;
    echo "</div>";
  }else{
    echo $j;
  }
  echo "</td></tr>";
  foreach($att_arr as $att_row){
    $att_info_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." where id='".$att_row['attendance_detail_id']."' limit 1";
    $att_info_query = tep_db_query($att_info_sql);
    if($att_info = tep_db_fetch_array($att_info_query)){
    echo "<tr>";
    if($att_info['scheduling_type'] == 1){
      echo "<td bgcolor='".$att_info['src_text']."'>";
      echo "<div>";
      echo $att_info['short_language'];
      echo "</div>";
    }else{
      echo "<td >";
      echo "<div>";
      echo "<img src='".$att_info['src_text']."' alt='".$att_info['alt_text']."'>";
      echo "</div>";
    }
    if(!empty($show_select_group_user)&&$date<$today){
      echo "<div>";
      foreach($show_select_group_user as $u_list){
        echo tep_valadate_attendance($u_list,$date,$att_info,$att_info['src_text']);
      }
      echo "</div>";
    }
    $sql_replace_att = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." WHERE 
      `date` = '".$date."'";
    $query_replace_att = tep_db_query($sql_replace_att);
    while($row_replace_att = tep_db_fetch_array($query_replace_att)){
      echo '<div>';
      $u_info = tep_get_user_info($row_replace_att['user']);
      $att_date_info = tep_get_attendance_by_id($row_replace_att['attendance_detail_id']);
      if(!empty($u_info)){
        echo $u_info['name'];
      }
      if(!empty($att_date_info)){
        if($att_date_info['scheduling_type'] == 1){
          echo $att_date_info['title'];
        }else{
          echo "<img src='".$att_date_info['src_text']."' alt='".$att_date_info['alt_text']."'>";
        }
      }
      echo '</div>';
    }
    echo "</td>";
    echo "</tr>";
    }
  }
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
