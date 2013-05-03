<?php
require('includes/application_top.php');
//one time pwd 
$http_referer = $_SERVER['HTTP_REFERER'];
$http_referer_arr = explode('?',$_SERVER['HTTP_REFERER']);
$http_referer_arr = explode('admin',$http_referer_arr[0]);
$request_page_name = '/admin'.$http_referer_arr[1];
$request_one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$request_page_name."'";
$request_one_time_query = tep_db_query($request_one_time_sql);
$request_one_time_arr = array();
$request_one_time_flag = false; 
while($request_one_time_row = tep_db_fetch_array($request_one_time_query)){
  $request_one_time_arr[] = $request_one_time_row['check_value'];
  $request_one_time_flag = true; 
}

if(count($request_one_time_arr)==1&&$request_one_time_arr[0]=='admin'&&$_SESSION['user_permission']!=15){
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
    forward401();
  }
}
if (!$request_one_time_flag && $_SESSION['user_permission']!=15) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    forward401();
  }
}
if(!in_array('onetime',$request_one_time_arr)&&$_SESSION['user_permission']!=15){
  if(!(in_array('chief',$request_one_time_arr)&&in_array('staff',$request_one_time_arr))){
  if($_SESSION['user_permission']==7&&in_array('chief',$request_one_time_arr)){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if($_SESSION['user_permission']==10&&in_array('staff',$request_one_time_arr)){
  $micro_notice_raw = tep_db_query("select id, title, set_time, from_notice from ".TABLE_NOTICE." where type = '1' and id not in (select notice_id from ".TABLE_NOTICE_TO_MICRO_USER." n where n.user != '".$ocertify->auth_user."') order by set_time asc, created_at desc limit 2");
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  }
}
//end one time pwd

if (isset($_GET['action'])&&$_GET['action']=='show_all_notice') {
/* -----------------------------------------------------
    功能: 显示该用户的所有的notice
    参数: 无 
 -----------------------------------------------------*/
  $notice_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and a.alarm_flag='0' and n.user = '".$ocertify->auth_user."'"; 
  
  $notice_micro_sql = "select * from ".TABLE_NOTICE." where type = '1' and id not in (select notice_id from ".TABLE_NOTICE_TO_MICRO_USER." n where n.user = '".$ocertify->auth_user."')";

  //警告提示
  $alarm_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and a.alarm_flag='1'";
  
  $notice_total_sql = "select * from (".$notice_order_sql." union ".$notice_micro_sql." union ".$alarm_order_sql.") taf where id != '".$_POST['aid']."' order by created_at desc,set_time asc, type asc"; 
  
  $notice_list_raw = tep_db_query($notice_total_sql);
  
  $now_time = strtotime(date('Y-m-d H:i:00', time()));

  if (tep_db_num_rows($notice_list_raw) > 0) {
    echo '<table cellspacing="0" cellpadding="0" border="0"  width="100%">'; 
    while ($notice_list = tep_db_fetch_array($notice_list_raw)) {
      echo '<tr id="alarm_delete_'.$notice_list['from_notice'].'">'; 
      echo '<td width="200">'; 
      if ($notice_list['type'] == '0') {
        $alarm_flag_query = tep_db_query("select alarm_flag,alarm_show,orders_flag from ".TABLE_ALARM." where alarm_id='".$notice_list['from_notice']."'");
        $alarm_flag_array = tep_db_fetch_array($alarm_flag_query);
        tep_db_free_result($alarm_flag_query);
      }
      if ($notice_list['type'] == '0') { 
        if($alarm_flag_array['orders_flag'] == '1'){

          $title_str = HEADER_TEXT_ALERT_TITLE;
        }else{
          $title_str = HEADER_TEXT_ALERT_TITLE_PREORDERS; 
        }
        if($alarm_flag_array['alarm_flag'] == '0'){
          echo '&nbsp;'.NOTICE_ALARM_TITLE; 
        }else{
          echo '&nbsp;'.$title_str; 
        }
      } else {
        echo '&nbsp;'.NOTICE_EXTEND_TITLE; 
      }
      echo '</td>'; 
      echo '<td class="notice_info">'; 
      $set_time = strtotime($notice_list['set_time']);
      $leave_time = $set_time - $now_time;
      if ($leave_time > 0) {
        $leave_time_day = floor($leave_time/(3600*24));
        $leave_time_tmp = $leave_time % (3600*24);
        $leave_time_seconds = $leave_time_tmp % 3600;
        $leave_time_hour = ($leave_time_tmp - $leave_time_seconds) / 3600;
        $leave_time_minute = $leave_time_seconds % 60;
        $leave_time_minute = ($leave_time_seconds - $leave_time_minute) / 60;
        $leave_date = sprintf('%02d', $leave_time_day).DAY_TEXT.sprintf('%02d', $leave_time_hour).HOUR_TEXT.sprintf('%02d', $leave_time_minute).MINUTE_TEXT;
      } else {
        $leave_date = '00'.DAY_TEXT.'00'.HOUR_TEXT.'00'.MINUTE_TEXT; 
      }
      echo '<div style="float:left; width:150px;">'; 
      echo '<span>'.date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_TORIHIKI_HOUR_STR.'i'.TEXT_TORIHIKI_MIN_STR,strtotime($notice_list['created_at'])).'</span>';
      echo '</div>'; 
      echo '<div style="float:left;">';
      if ($notice_list['type'] == '0') {
        $alarm_raw = tep_db_query("select orders_id from ".TABLE_ALARM." where alarm_id = '".$notice_list['from_notice']."'"); 
        $alarm = tep_db_fetch_array($alarm_raw); 
        if($alarm_flag_array['alarm_flag'] == '0'){
          echo '<a href="'.tep_href_link(FILENAME_ORDERS, 'oID='.$alarm['orders_id'].'&action=edit').'">'.$notice_list['title'].'</a>'; 
        }else{
          if($alarm_flag_array['orders_flag'] == '1'){

            $filename_str = FILENAME_ORDERS;
          }else{
            $filename_str = FILENAME_PREORDERS; 
          }
          echo '<a href="'.tep_href_link($filename_str, 'oID='.$alarm['orders_id'].'&action=edit').'">'.$alarm['orders_id'].'</a>'; 
        }
      } else {
        echo '<a href="'.tep_href_link('micro_log.php').'">'.$notice_list['title'].'</a>'; 
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo NOTICE_DIFF_TIME_TEXT.'&nbsp;'; 
        echo '<span>'.$leave_date.'</span>';
      }
      echo '</div>';
      if ($notice_list['type'] == '0') {
      if($alarm_flag_array['alarm_flag'] == '1'){
        echo '<div style="float:left;" id="alarm_user_'.$notice_list['from_notice'].'">';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$notice_list['user'].'&nbsp;'.HEADER_TEXT_ALERT_LINK;
        echo '</div>';
        echo '<div style="float:left;">';
        if($alarm_flag_array['alarm_show'] == '1'){
          echo '&nbsp;'.str_replace('${ALERT_TITLE}',$notice_list['title'],HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">ON</span>';
        }else{
          echo '&nbsp;'.str_replace('${ALERT_TITLE}',$notice_list['title'],HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">OFF</span>'; 
        }
        echo '</div>';
      }else{
        echo '<div style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo NOTICE_DIFF_TIME_TEXT.'&nbsp;'; 
        echo '<span>'.$leave_date.'</span>';
        echo '</div>';
      }
      }
      echo '</td>'; 
      echo '<td align="right">'; 
      if ($notice_list['type'] == '0') {
        echo '&nbsp;<a href="javascript:void(0);" onclick="delete_alarm_notice(\''.$notice_list['id'].'\', \'1\');"><img src="images/icons/del_img.gif" alt="close"></a>'; 
      } else {
        echo '&nbsp;<a href="javascript:void(0);" onclick="delete_micro_notice(\''.$notice_list['id'].'\', \'1\');"><img src="images/icons/del_img.gif" alt="close"></a>'; 
      }
      echo '</td>'; 
      echo '</tr>'; 
    }
    echo '</table>'; 
  }
} else if (isset($_GET['action'])&&$_GET['action']=='delete_alarm') {
/* -----------------------------------------------------
    功能: 删除指定的notice
    参数: $_POST['nid'] notcie的id值 
 -----------------------------------------------------*/
  $notice_raw = tep_db_query("select * from ".TABLE_NOTICE." where id = '".$_POST['nid']."' and type = '0'");
  $notice = tep_db_fetch_array($notice_raw);

  $alert_orders_query = tep_db_query("select orders_id,orders_flag from ".TABLE_ALARM." where alarm_id = '".$notice['from_notice']."'");
  $alert_orders_array = tep_db_fetch_array($alert_orders_query);
  tep_db_free_result($alert_orders_query);
  if ($notice) {
    tep_db_query("delete from ".TABLE_ALARM." where alarm_id = '".$notice['from_notice']."'"); 
    tep_db_query("delete from ".TABLE_NOTICE." where id = '".$_POST['nid']."'"); 

    echo $alarm_name_array['computers_id'];
  }
} else if (isset($_GET['action'])&&$_GET['action']=='delete_micro') {
/* -----------------------------------------------------
    功能: 把指定的micro_log的id和当前用户关联
    参数: $_POST['nid'] micro_log的id值 
 -----------------------------------------------------*/
  tep_db_query("insert into `".TABLE_NOTICE_TO_MICRO_USER."` values('".$_POST['nid']."', '".$ocertify->auth_user."')");
} else if (isset($_GET['action'])&&$_GET['action']=='show_head_notice') {
/* -----------------------------------------------------
    功能: 获取头部的notcie信息
    参数: 无 
 -----------------------------------------------------*/
  echo tep_get_notice_info(1);
} else if (isset($_GET['action'])&&$_GET['action']=='is_show_alarm') {
/* -----------------------------------------------------
    功能: 显示，关闭警告提示 
    参数: $_POST['nid'] notcie的id值 
 -----------------------------------------------------*/
 tep_db_query("update ".TABLE_ALARM." set alarm_show='".$_POST['is_show']."' where alarm_id = '".$_POST['nid']."'");  
}
