<?php
/*
  $Id$
*/
require('includes/application_top.php');
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'japanese/'.FILENAME_ATTENDANCE);

if (isset($_GET['action'])) {
     switch ($_GET['action']) {

/*---------------------------------
@20170709添加出勤信息 
case:insert,update
----------------------------------*/

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
          } else {
             $tep_image_name = '';
          }


	     $image_directory = tep_get_local_path(tep_get_upload_dir() . 'attendance/');
         $path = 'attendance/';

         if (is_uploaded_file($src_image['tmp_name'])) {
			 $src_image=$path.'/'.$src_image['name'];
			 
			 //删除之前的图片
			 $sql_image = "select src_text from `".TABLE_ATTENDANCE_DETAIL."` where id=".$id;
			 $tep_res = tep_db_query($sql_image);
		     $row=  tep_db_fetch_array($tep_res);
			 if(count($row)){
			     unlink($image_directory.$row['src_text']);
			 }
             //更新新的图片
			 $src_text = $path.$tep_image_name;
		     tep_copy_uploaded_file($src_image, $image_directory. 'attendance/');
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
	 tep_redirect(tep_href_link('attendance.php'));
	 $attendance_id = tep_db_insert_id();
	 }elseif ($_GET['action']=='update'){
	 
	 tep_db_perform(TABLE_ATTENDANCE_DETAIL, $sql_data_array, 'update',  "id = '" .$id  . "'");
	 tep_redirect(tep_href_link('attendance.php'));
	 }
	 break;
	 

	 }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo ATTENDANCE_HEAD_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/admin_attendance.js"></script>
<script language="javascript">
var href_attendance = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_ATTENDANCE;?>';
 var attendance_del_confirm = '<?php echo ATTENDANCE_DELETE_REMIND;?>';
var error_text = '<?php echo TEP_ERROR_NULL;?>';
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
?>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body //-->
<div id="show_attendance" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text //-->
<td width="100%" valign="top">
<div class="compatible">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
?>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo ATTENDANCE_HEAD_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <table id="attendance_table_list" border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr class="dataTableHeadingRow">
            <td  class="dataTableHeadingContent">
            <input type="checkbox" onclick="chg_tr_color(this);" value="20140611-09421567" name="chk[]">
            </td>
            <td class="dataTableHeadingContent" height="24"><?php echo ATTENDANCE_TITLE;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_WORK_START;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_WORK_END;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_REST_START;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_REST_END;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_ADD_USER;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo ATTENDANCE_ADD_TIME;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo '操作';?></td>
		</tr>
       <?php 
$att_select_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
$latest_news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $att_select_sql, $latest_news_query_numrows);
$tep_result = tep_db_query($att_select_sql);

$attendance_list=array();
while($rows= tep_db_fetch_array($tep_result)) {
$attendance_list[] = $rows;
}

foreach($attendance_list as $info) {
//	var_dump($info);
?> 
        <tr class="dataTableRow" onmouseout="this.className='dataTableRow'" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'">
            <td style="border-bottom:1px solid #000000">
            <input type="checkbox" onclick="chg_tr_color(this);" value="20140611-09421567" name="chk[]">
            </td>
            <td style="border-bottom:1px solid #000000" height="24"><?php echo $info['title']; ?> </td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['work_start'];?></td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['work_end'];?></td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['rest_start'];?></td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['rest_end'];?></td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['add_user'];?></td>
			<td style="border-bottom:1px solid #000000" align="right"><?php echo $info['add_time'];?></td>
            <td style="border-bottom:1px solid #000000" align="right">
			<a onclick="show_attendance_info(<?php echo $info['id']; ?>)" href="javascript:void(0);">
                  <img border="0" title=" 2014/06/13 13:44:25 " alt="2014/06/13 13:44:25" src="images/icons/info_blink.gif">
                </a>
            </td>
        </tr>
<?php }?>

        </table></td>
      </tr>
      <tr>
      <td>

<table border="0" width="100%" cellspacing="3" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                     <?php 
                      if($ocertify->npermission >= 15){
                           echo '<select name="customers_action" onchange="customers_change_action(this.value, \'customers_id[]\');">';
                           echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                           echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                           echo '</select>';
                       }
                   ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $latest_news_split->display_count($latest_news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $latest_news_split->display_links($latest_news_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'latest_news_id'))); ?></div></td>
                  </tr>
                  <tr>
                     <td align="right" colspan="2">
                       <?php  
	                    //新建
                       echo '<a href="javascript:void();" onclick="show_attendance_info(0)">' .tep_html_element_button(IMAGE_NEW_PROJECT,'id="create_attendance" ').'</a>';
                       ?>
                     </td>
                  </tr>
				</table>


      </td>
      </tr>
    </table>
    </div>
    </td>
<!-- body_text_eof //-->
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
<?php



