<?php
/*
  $Id$
*/
include("includes/application_top.php");

switch($_GET['action']){
/* -----------------------------------------------------
   case 'status_add' 新建状态 
   case 'status_edit' 更新状态 
   case 'status_delete' 删除状态 
   case 'date' 新建日期 
   case 'date_edit' 更新日期  
------------------------------------------------------*/
  case 'status_add':
    $title = tep_db_prepare_input($_POST['title']);
    $name = tep_db_prepare_input($_POST['name']);
    $front_desk_show = tep_db_prepare_input($_POST['front_desk_show']);
    $front_desk_comment_show = tep_db_prepare_input($_POST['front_desk_comment_show']);
    $color = tep_db_prepare_input($_POST['color']);
    $is_handle = tep_db_prepare_input($_POST['is_handle']);
    $start_time = tep_db_prepare_input($_POST['start_time']);
    $start_time = $start_time == '' ? '-1' : $start_time;
    $start_left_min = tep_db_prepare_input($_POST['start_left_min']);
    $start_left_min = $start_left_min == '' ? '-1' : $start_left_min;
    $start_right_min = tep_db_prepare_input($_POST['start_right_min']);
    $start_right_min = $start_right_min == '' ? '-1' : $start_right_min;
    $end_time = tep_db_prepare_input($_POST['end_time']);
    $end_time = $end_time == '' ? '-1' : $end_time;
    $end_left_min = tep_db_prepare_input($_POST['end_left_min']);
    $end_left_min = $end_left_min == '' ? '-1' : $end_left_min;
    $end_right_min = tep_db_prepare_input($_POST['end_right_min']);
    $end_right_min = $end_right_min == '' ? '-1' : $end_right_min;
    $is_show = tep_db_prepare_input($_POST['is_show']);
    $sort = tep_db_prepare_input($_POST['sort']);

    $sql_data_array = array('title'=>$title,
                             'name'=>$name,
                             'front_desk_show'=>$front_desk_show,
                             'front_desk_comment_show'=>$front_desk_comment_show,
                             'color'=>$color, 
                             'is_handle'=>$is_handle,
                             'start_time'=>$start_time,
                             'start_min'=>intval($start_left_min.$start_right_min),
                             'end_time'=>$end_time,
                             'end_min'=>intval($end_left_min.$end_right_min),
                             'is_show'=>$is_show,
                             'sort'=>$sort,
                             'user_added'=>$_SESSION['user_name'],
                             'date_added'=>'now()',
                             'user_update'=>'',
                             'date_update'=>'now()'
                           );
    tep_db_perform(TABLE_CALENDAR_STATUS, $sql_data_array);
    tep_redirect(tep_href_link(FILENAME_BANK_CL));
    break;
  case 'status_edit':
    $cl_id = tep_db_prepare_input($_POST['cl_id']); 
    $title = tep_db_prepare_input($_POST['title']);
    $name = tep_db_prepare_input($_POST['name']);
    $front_desk_show = tep_db_prepare_input($_POST['front_desk_show']);
    $front_desk_comment_show = tep_db_prepare_input($_POST['front_desk_comment_show']);
    $color = tep_db_prepare_input($_POST['color']);
    $is_handle = tep_db_prepare_input($_POST['is_handle']);
    $start_time = tep_db_prepare_input($_POST['start_time']);
    $start_left_min = tep_db_prepare_input($_POST['start_left_min']);
    $start_right_min = tep_db_prepare_input($_POST['start_right_min']);
    $end_time = tep_db_prepare_input($_POST['end_time']);
    $end_left_min = tep_db_prepare_input($_POST['end_left_min']);
    $end_right_min = tep_db_prepare_input($_POST['end_right_min']);
    $is_show = tep_db_prepare_input($_POST['is_show']);
    $sort = tep_db_prepare_input($_POST['sort']);

    $sql_data_array = array('title'=>$title,
                             'name'=>$name,
                             'front_desk_show'=>$front_desk_show,
                             'front_desk_comment_show'=>$front_desk_comment_show,
                             'color'=>$color, 
                             'is_handle'=>$is_handle,
                             'start_time'=>$start_time,
                             'start_min'=>intval($start_left_min.$start_right_min),
                             'end_time'=>$end_time,
                             'end_min'=>intval($end_left_min.$end_right_min),
                             'is_show'=>$is_show,
                             'sort'=>$sort,
                             'user_update'=>$_SESSION['user_name'],
                             'date_update'=>'now()'
                           );
    tep_db_perform(TABLE_CALENDAR_STATUS, $sql_data_array, 'update', 'id=\''.$cl_id.'\'');
    tep_redirect(tep_href_link(FILENAME_BANK_CL));
    break;
  case 'status_delete':
    $cl_id = tep_db_prepare_input($_POST['cl_id']);  
    tep_db_query("delete from ". TABLE_CALENDAR_DATE ." where type='".$cl_id."'");
    tep_db_query("delete from ". TABLE_CALENDAR_STATUS ." where id='".$cl_id."'");
    tep_redirect(tep_href_link(FILENAME_BANK_CL));
    break;
  case 'date':
    $cl_date = tep_db_prepare_input($_POST['cl_date']);
    $is_special = tep_db_prepare_input($_POST['is_special']);
    $cl_type = tep_db_prepare_input($_POST['type']);   
    $repeat_type = tep_db_prepare_input($_POST['repeat_type']); 
    $is_show = tep_db_prepare_input($_POST['is_show']);

    $sql_data_array = array('cl_date'=>$cl_date,
                             'is_special'=>$is_special, 
                             'type'=>$cl_type,
                             'repeat_type'=>$repeat_type,
                             'is_show'=>$is_show,
                             'user_added'=>$_SESSION['user_name'],
                             'date_added'=>'now()',
                             'user_update'=>'',
                             'date_update'=>'now()'
                           );
    tep_db_perform(TABLE_CALENDAR_DATE, $sql_data_array);
    tep_redirect(tep_href_link(FILENAME_BANK_CL,(isset($_GET['y']) ? 'y='.$_GET['y'] : '')));
    break;
  case 'date_edit':
    $cl_date = tep_db_prepare_input($_POST['cl_date']);
    $is_special = tep_db_prepare_input($_POST['is_special']);
    $cl_type = tep_db_prepare_input($_POST['type']);   
    $repeat_type = tep_db_prepare_input($_POST['repeat_type']); 
    $is_show = tep_db_prepare_input($_POST['is_show']);

    $sql_data_array = array('cl_date'=>$cl_date,
                             'is_special'=>$is_special, 
                             'type'=>$cl_type,
                             'repeat_type'=>$repeat_type,
                             'is_show'=>$is_show,
                             'user_update'=>$_SESSION['user_name'],
                             'date_update'=>'now()'
                           );
    tep_db_perform(TABLE_CALENDAR_DATE, $sql_data_array, 'update', "cl_date='".$cl_date."'");
    tep_redirect(tep_href_link(FILENAME_BANK_CL,(isset($_GET['y']) ? 'y='.$_GET['y'] : '')));
    break;  
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
<script language="javascript" src="js2php.php?path=includes|javascript&name=calendar&type=js"></script>
<script language="javascript">
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
        $cl_status_array = array();
        $calendar_status_query = tep_db_query("select id,title,color,is_show from ". TABLE_CALENDAR_STATUS ." order by sort asc,id asc");
        while($calendar_status_array = tep_db_fetch_array($calendar_status_query)){

          $status_str .= '<td class="main" style="cursor:pointer;" onclick="status_edit('.$calendar_status_array['id'].',this);">';
          $status_str .= '<div style="float: left; background-color: '.$calendar_status_array['color'].'; border: 1px solid #CCCCCC; padding: 6px;"></div>&nbsp;<font color="#000000">&raquo;<u>'.$calendar_status_array['title'].'</u></font>&nbsp;&nbsp;';
          $status_str .= '</td>';
          $cl_status_array[$calendar_status_array['id']] = array('is_show'=>$calendar_status_array['is_show'],'color'=>$calendar_status_array['color']);
        }
        tep_db_free_result($calendar_status_query);
        $status_str .= '<td onclick="status_add(this);">&nbsp;&nbsp;<input type="button" value="'.TEXT_CALENDAR_STATUS_ADD.'">&nbsp;&nbsp;</td>';
        $status_str .= '</tr></table>';
      ?>
      <tr>
        <td><div id="toggle_width" style="min-width:726px;"></div><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
            <td class="main" align="right">
              <?php echo $status_str;?> 
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <?php 
            $repeat_array = array(); 
            $repeat_date_query = tep_db_query("select id,cl_date,type,repeat_type,is_show,date_update from ". TABLE_CALENDAR_DATE ." where repeat_type!=0 order by date_update asc");    
            while($repeat_date_array = tep_db_fetch_array($repeat_date_query)){

              $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$repeat_date_array['type']."'");
              $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
              tep_db_free_result($repeat_sort_query);
              $repeat_array[$repeat_date_array['id']] = array('cl_date'=>$repeat_date_array['cl_date'],'repeat'=>$repeat_date_array['repeat_type'],'type'=>$repeat_date_array['type'],'is_show'=>$repeat_date_array['is_show'],'date_update'=>$repeat_date_array['date_update'],'sort'=>($repeat_date_array['type'] == 0 ? -1 : $repeat_sort_array['sort']));
            }
            tep_db_free_result($repeat_date_query);
            //分类处理特殊重复设置

            foreach($repeat_array as $cl_key=>$cl_value){

              if($cl_value['repeat'] == 1){

                $cl_repeat_array[1][$cl_key] = tep_get_repeat_date(1,$cl_value['cl_date']);
              }

              if($cl_value['repeat'] == 2){

                $cl_repeat_array[2][$cl_key] = tep_get_repeat_date(2,$cl_value['cl_date']);
              }

              if($cl_value['repeat'] == 3){

                $cl_repeat_array[3][$cl_key] = tep_get_repeat_date(3,$cl_value['cl_date']);
              }

              if($cl_value['repeat'] == 4){

                $cl_repeat_array[4][$cl_key] = tep_get_repeat_date(4,$cl_value['cl_date']);
              }
            }

            //重复周
            $cl_week_array = array();
            foreach($cl_repeat_array[1] as $cl_week_key=>$cl_week_value){

              $cl_week_array[$cl_week_value] = $cl_week_key;
            }

            //每月重复的日
            $cl_month_day_array = array();
            foreach($cl_repeat_array[2] as $cl_month_key=>$cl_month_value){

              $cl_month_day_array[$cl_month_value] = $cl_month_key;
            }

            //每月重复固定周
            $cl_month_week_array = array();
            foreach($cl_repeat_array[3] as $cl_month_week_key=>$cl_month_week_value){

              $cl_month_week_array[$cl_month_week_value[0]][$cl_month_week_value[1]] = $cl_month_week_key;
            }

            //每年重复的月日
            $cl_year_month_array = array();
            foreach($cl_repeat_array[4] as $cl_year_month_key=>$cl_year_month_value){

              $cl_year_month_array[$cl_year_month_value] = $cl_year_month_key; 
            }

            $select_year = isset($_GET['y']) ? $_GET['y'] : date("Y");
            $calendar_date_query = tep_db_query("select cl_date,type,is_show,date_update from ". TABLE_CALENDAR_DATE ." where cl_date like '".$select_year."%'");
            $cl_array = array();
            while($calendar_date_array = tep_db_fetch_array($calendar_date_query)){

              $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$calendar_date_array['type']."'");
              $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
              tep_db_free_result($repeat_sort_query);
              $cl_array[$calendar_date_array['cl_date']] = array('type'=>$calendar_date_array['type'],'is_show'=>$calendar_date_array['is_show'],'date_update'=>$calendar_date_array['date_update'],'sort'=>$repeat_sort_array['sort']);
            }
            tep_db_free_result($calendar_date_query);
          ?>
            <td class="date_title" align="center"><a href="calendar.php?y=<?php echo $select_year-1;?>"><b><<</b></a>&nbsp;&nbsp;<?php echo $select_year; ?>&nbsp;&nbsp;<a href="calendar.php?y=<?php echo $select_year+1;?>"><b>>></b></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <?php
    $default_bgcolor = '#FFFFFF';
    $search_year = isset($_GET['y']) ? $_GET['y'] : date('Y');
    $now = $search_year.'-1';
    $m_num = date('m',strtotime($now.'-1 00:00:00')) - 1;
    $year = date('Y',strtotime($now.'-1 00:00:00'));

    for($ii = 1;$ii<=12;$ii++){
    $today2 = getdate(mktime(0,0,0,$m_num+$ii,1,$year));
    $m_num2 = $today2['mon'];
    $year2 = $today2['year'];
    // 第一天对应的星期
    $f_today2 = getdate(mktime(0,0,0,$m_num2,1,$year2));
    $wday2 = $f_today2['wday'];
     
    echo '
      <td valign="top"><table border="0" cellspacing="1" cellpadding="1" width="90%" class="date_bgcolor">
      <tr bgcolor="#3C7FB1">
      <td nowrap colspan="7" align="center"><font size="2" color="#FFFFFF"><b>'.$m_num2.MONTH_TEXT.'</b></font></td> 
      </tr>
      <tr bgcolor="#EEEEEE">
        <td colspan="7">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_SUNDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_MONDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_TUESDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_WEDNESDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_THURSDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_FRIDAY.'</font></td>
        <td align="middle" height="15"><font size="2">'.CL_TEXT_DATE_STATURDAY.'</font></td>
        </tr>
        </table>
        </td>
      </tr>
      <tr bgcolor="#ffffff">
    ';
    
    for ($i=0; $i<$wday2; $i++) { // Blank
      echo "<td align=center bgcolor='#ffffff'>　</td>\n"; 
    }
    
    $day2 = 1;
    while(checkdate($m_num2,$day2,$year2)){
      $cl_date_str = $year2.($m_num2 <= 9 ? '0'.$m_num2 : $m_num2).($day2 <= 9 ? '0'.$day2 : $day2); 
      $cl_bgcolor = array_key_exists($cl_date_str,$cl_array) && $cl_status_array[$cl_array[$cl_date_str]['type']]['is_show'] == 1 && $cl_array[$cl_date_str]['is_show'] == 1 ? ' bgcolor="'.$cl_status_array[$cl_array[$cl_date_str]['type']]['color'].'"' : ' bgcolor="'.$default_bgcolor.'"';
 
      $cl_day = $day2 <= 9 ? '0'.$day2 : $day2; 

      $temp_num_week = ceil($day2/7);
    
      $temp_year_month_day = ($m_num2 < 10 ? '0'.$m_num2 : $m_num2).($day2 < 10 ? '0'.$day2 : $day2);
 
      //状态重复设置，冲突时，以状态排序最小的一个为准 
      $date_time_array = array();
      $date_time_array = array('month'=>$repeat_array[$cl_month_day_array[$cl_day]]['sort'],
                               'week'=>$repeat_array[$cl_week_array[$wday2]]['sort'],                     
                               'month_week'=>$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'],
                               'year'=>$repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']
                              );
      arsort($date_time_array);
      $date_time_array = array_filter($date_time_array);
      $first_value_array = array_slice($date_time_array,0,1);
      $first_value_type = array_keys($first_value_array);
      if($first_value_array[$first_value_type[0]] != ''){
        
        switch($first_value_type[0]){

          case 'month':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_month_day_array[$cl_day]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] != '') ||($repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1)){
              $cl_bgcolor = array_key_exists($cl_day,$cl_month_day_array) && $cl_status_array[$repeat_array[$cl_month_day_array[$cl_day]]['type']]['is_show'] == 1 && $repeat_array[$cl_month_day_array[$cl_day]]['is_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_month_day_array[$cl_day]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"';
              if($repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            } 
            break;
          case 'week':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_week_array[$wday2]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_week_array[$wday2]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_week_array[$wday2]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_week_array[$wday2]]['sort'] != '') || ($repeat_array[$cl_week_array[$wday2]]['sort'] == -1)){
              $cl_bgcolor = array_key_exists($wday2,$cl_week_array) && $cl_status_array[$repeat_array[$cl_week_array[$wday2]]['type']]['is_show'] == 1 && $repeat_array[$cl_week_array[$wday2]]['is_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_week_array[$wday2]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"'; 
              if($repeat_array[$cl_week_array[$wday2]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"'; 
              }
            }
            break;
          case 'month_week':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] != '') || ($repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1)){ 
              $temp_week_array = array_slice($cl_month_week_array,0,1);
              $cl_bgcolor = is_array($cl_month_week_array[$temp_num_week]) && array_key_exists($wday2,$temp_week_array[0]) && $cl_status_array[$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['type']]['is_show'] == 1 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['is_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"';

              if($repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            }
            break;
          case 'year':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1 ? true : false;
            if(!isset($cl_array[$cl_date_str]['sort']) || ($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']) || ($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1)){ 
              $cl_bgcolor = array_key_exists($temp_year_month_day,$cl_year_month_array) && $cl_status_array[$repeat_array[$cl_year_month_array[$temp_year_month_day]]['type']]['is_show'] == 1 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['is_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_year_month_array[$temp_year_month_day]]['type']]['color'].'"' : $cl_bgcolor;

              if($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            }
            break;
        }
      }
      echo "<td align=center style='cursor:pointer;' onclick='status_setting(\"".$year2.($m_num2 <= 9 ? '0'.$m_num2 : $m_num2).($day2 <= 9 ? '0'.$day2 : $day2)."\",this);'".$cl_bgcolor.">".(date("Ymd") == $cl_date_str ? '<div class="date_today">' : '')."<font size=2>$day2</font>".(date("Ymd") == $cl_date_str ? '</div>' : '')."</td>\n";
      // 换行
      if($wday2 == 6 && checkdate($m_num2,$day2+1,$year2)) echo "</tr><tr>";
      $day2++;
      $wday2++;
      $wday2 = $wday2 % 7;
    }
    if($wday2 > 0){
      while($wday2 < 7) { // Blank
      echo "<td align=center bgcolor='#ffffff'>　</td>\n";
      $wday2++;
      }
    } 
    echo '</tr></table><br></td>' . "\n";
    if($ii % 3 == 0){

      echo '</tr><tr>';
    }
    }
   ?>
    </tr>
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
