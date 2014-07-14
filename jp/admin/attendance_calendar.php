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
      tep_redirect(tep_href_link(FILENAME_ATTENDANCE_CALENDAR));
      break;
    case 'delete_as_list':
      if(isset($_POST['data_as'])&&is_array($_POST['data_as'])
          &&!empty($_POST['data_as'])){
        foreach($_POST['data_as'] as $add_id){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where id="'.$add_id.'"');
        }
      }
      tep_redirect(tep_href_link(FILENAME_ATTENDANCE_CALENDAR));
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo BANK_CL_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/admin_attendance.js"></script>
<script language="javascript">
var href_attendance_calendar = '<?php echo HTTP_SERVER.DIR_WS_ADMIN.FILENAME_ATTENDANCE_CALENDAR;?>'';
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
            <td class="pageHeading"><?php echo BANK_CL_TITLE_TEXT; ?></td>
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
        $attendance_str .= '</tr></table>'
      ?>
      <tr>
        <td><div id="toggle_width" style="min-width:726px;"></div><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
            <td class="main" align="right">
              <?php echo $status_str;?> 
            </td>
          </tr><tr>
            <td class="main" align="right">
              <?php echo $attendance_str;?> 
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr bgcolor="#3C7FB1">
            <td class="date_title" align="center">
            <a href="<?php echo FILENAME_ATTENDANCE_CALENDAR.$str_prev_str;?>"><b><<</b></a>
            &nbsp;&nbsp;<?php echo $year.' / '.$month; ?>&nbsp;&nbsp;
            <a href="<?php echo FILENAME_ATTENDANCE_CALENDAR.$str_next_str;?>"><b>>></b></a></td>
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
<table width="100%" border="0" cellspacing="1" cellpadding="1">
<tr>
<?php 
echo '
        <td align="middle" bgcolor="#ccffff" height="15"><font size="2">'.CL_TEXT_DATE_MONDAY.'</font></td>
        <td align="middle" bgcolor="#ccffff" height="15"><font size="2">'.CL_TEXT_DATE_TUESDAY.'</font></td>
        <td align="middle" bgcolor="#ccffff" height="15"><font size="2">'.CL_TEXT_DATE_WEDNESDAY.'</font></td>
        <td align="middle" bgcolor="#ccffff" height="15"><font size="2">'.CL_TEXT_DATE_THURSDAY.'</font></td>
        <td align="middle" bgcolor="#ccffff" height="15"><font size="2">'.CL_TEXT_DATE_FRIDAY.'</font></td>
        <td align="middle" bgcolor="#fc9acd" height="15"><font size="2">'.CL_TEXT_DATE_STATURDAY.'</font></td>
        <td align="middle" bgcolor="#fc9acd" height="15"><font size="2">'.CL_TEXT_DATE_SUNDAY.'</font></td>
        ';
        ?>
</tr>
<tr>
<?php
for($i = 1; $i<$start_week; $i++)
{
  echo "<td></td>";
}

$j=1;

while($j<=$day_num)
{
  $date = $year.tep_add_front_zone($month).tep_add_front_zone($j);
  echo "<td style='cursor:pointer;' onclick='attendance_setting(\"".$date."\",this)'>";
  echo $j;
  echo "<br>";
  echo "</td>";
  $week = ($start_week+$j-2)%7;

  if($week ==6){
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
