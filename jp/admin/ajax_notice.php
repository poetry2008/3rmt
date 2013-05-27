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

if ($ocertify->npermission == 31) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    forward401();
  }
} else {
  if (count($request_one_time_arr) == 1 && $request_one_time_arr[0] == 'admin' && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!$request_one_time_flag && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!in_array('onetime', $request_one_time_arr) && $ocertify->npermission != 15) {
    if(!(in_array('chief', $request_one_time_arr) && in_array('staff', $request_one_time_arr))) {
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
      if ($ocertify->npermission == 10 && in_array('staff', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
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
  $notice_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='0' and n.user = '".$ocertify->auth_user."'"; 

  $notice_micro_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted,bm.`to` to_users,bm.`from` from_users,bm.icon icon,bm.id bm_id from ".TABLE_NOTICE." n,".TABLE_BUSINESS_MEMO." bm where n.from_notice=bm.id and n.type = '1' and n.is_show='1' and bm.is_show='1' and bm.deleted='0'"; 

  $notice_micro_query = tep_db_query($notice_micro_sql);
  $notice_id_array = array();
  $memo_id_array = array();
  $memo_cid_array = array();
  while($notice_micro_array = tep_db_fetch_array($notice_micro_query)){

    if($notice_micro_array['to_users'] == ''){

      $notice_id_array[] = $notice_micro_array['id'];
      $memo_id_array[$notice_micro_array['id']] = $notice_micro_array['icon'];
      $memo_cid_array[$notice_micro_array['id']] = $notice_micro_array['bm_id'];
    }else{

      $users_id_array = array();
      $users_id_array = explode(',',$notice_micro_array['to_users']);
      array_push($users_id_array,$notice_micro_array['from_users']);

      if(in_array($ocertify->auth_user,$users_id_array)){

        $notice_id_array[] = $notice_micro_array['id'];
        $memo_id_array[$notice_micro_array['id']] = $notice_micro_array['icon'];
        $memo_cid_array[$notice_micro_array['id']] = $notice_micro_array['bm_id'];
      }
    }
  }
  tep_db_free_result($notice_micro_query);
  $notice_id_str = implode(',',$notice_id_array);

  if($notice_id_str != ''){
    $notice_micro_sqls = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n where n.id in (".$notice_id_str.")";
  }

  //警告提示
  $alarm_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='1'";
  
  $notice_total_sql = "select * from (".$notice_order_sql.($notice_id_str != '' ? " union ".$notice_micro_sqls : '')." union ".$alarm_order_sql.") taf where id != '".$_POST['aid']."' order by created_at desc,set_time asc, type asc"; 
  
  $notice_list_raw = tep_db_query($notice_total_sql);
  
  $now_time = strtotime(date('Y-m-d H:i:00', time()));

  //获取图标信息
  $icon_list_array = array();
  $icon_query = tep_db_query("select id,pic_name,pic_alt from ". TABLE_CUSTOMERS_PIC_LIST);
  while($icon_array = tep_db_fetch_array($icon_query)){

    $icon_list_array[$icon_array['id']] = array('name'=>$icon_array['pic_name'],'alt'=>$icon_array['pic_alt']);
  }
  tep_db_free_result($icon_query);

  if (tep_db_num_rows($notice_list_raw) > 0) {
    echo '<table cellspacing="0" cellpadding="0" border="0"  width="100%">'; 
    while ($notice_list = tep_db_fetch_array($notice_list_raw)) {
      if($notice_list['deleted'] != ''){
          
        $notice_users_array = array();
        $notice_users_array = explode(',',$notice_list['deleted']);
        
        if(in_array($ocertify->auth_user,$notice_users_array)){
          continue;
        }
      }
      echo '<tr id="alarm_delete_'.$notice_list['from_notice'].'">'; 
      $check_notice_query = tep_db_query("select a.alarm_flag from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.id='".$_POST['aid']."'");
      $check_notice_array = tep_db_fetch_array($check_notice_query);
      tep_db_free_result($check_notice_query);
      echo '<td width="'.($check_notice_array['alarm_flag'] == '1' ? "174" : "142" ).'">'; 
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
      echo '<div style="float:left; width:136px;">'; 
      echo '<span>'.date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_TORIHIKI_HOUR_STR.'i'.TEXT_TORIHIKI_MIN_STR,strtotime($notice_list['created_at'])).'</span>';
      echo '</div>'; 

      if(in_array($notice_list['id'],$notice_id_array)){
        echo  '<div style="float:left; width:16px;'.($icon_list_array[$memo_id_array[$notice_list['id']]]['name'] != '' ? "margin:3px 8px 0 8px;" : "margin-top: 3px;padding: 0px 8px;").'">';
        echo $icon_list_array[$memo_id_array[$notice_list['id']]]['name'] != '' ? tep_image(DIR_WS_IMAGES.'icon_list/'.$icon_list_array[$memo_id_array[$notice_list['id']]]['name'],$icon_list_array[$memo_id_array[$notice_list['id']]]['alt']) : '';
        echo  '</div>';
      }else{
        echo  '<div style="float:left; width:16px;margin-top: 3px;padding: 0px 8px;">'; 
        echo '</div>';
      }

      echo '<div style="float:left;margin-right: 35px;">';
      if ($notice_list['type'] == '0') {
        $alarm_raw = tep_db_query("select orders_id from ".TABLE_ALARM." where alarm_id = '".$notice_list['from_notice']."'"); 
        $alarm = tep_db_fetch_array($alarm_raw); 
        if($alarm_flag_array['alarm_flag'] == '0'){
          echo '<a href="'.tep_href_link(FILENAME_ORDERS, 'oID='.$alarm['orders_id'].'&action=edit').'">'.(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']).'</a>'; 
        }else{
          if($alarm_flag_array['orders_flag'] == '1'){

            $filename_str = FILENAME_ORDERS;
          }else{
            $filename_str = FILENAME_PREORDERS; 
          }
          echo '<a href="'.tep_href_link($filename_str, 'oID='.$alarm['orders_id'].'&action=edit').'">'.$alarm['orders_id'].'</a>'; 
        }
      } else {
        echo '<a href="'.tep_href_link(FILENAME_BUSINESS_MEMO,'cID='.$memo_cid_array[$notice_list['id']]).'">'.(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']).'</a>'; 
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        echo '&nbsp;'; 
        echo '<span style="display:none;">'.$leave_date.'</span>';
      }
      echo '</div>';
      if ($notice_list['type'] == '0') {
      if($alarm_flag_array['alarm_flag'] == '1'){
        echo '<div style="float:left;" id="alarm_user_'.$notice_list['from_notice'].'">';
        echo $notice_list['user'].'&nbsp;'.HEADER_TEXT_ALERT_LINK;
        echo '</div>';
        echo '<div style="float:left;">';
        if($alarm_flag_array['alarm_show'] == '1'){
          echo '&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">ON</span>';
        }else{
          echo '&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">OFF</span>'; 
        }
        echo '</div>';
      }else{
        echo '<div style="float:left;">';
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
  $notice_raw = tep_db_query("select deleted from ".TABLE_NOTICE." where id = '".$_POST['nid']."' and type = '0'");
  $notice = tep_db_fetch_array($notice_raw);

  $notice_users_str = ''; 
  if ($notice) {

    if($notice['deleted'] == ''){

      $notice_users_str = $ocertify->auth_user; 
    }else{

      $notice_users_str = $notice['deleted'].','.$ocertify->auth_user;
    }
    tep_db_query("update ".TABLE_NOTICE." set deleted='".$notice_users_str."' where id = '".$_POST['nid']."'");
  }
} else if (isset($_GET['action'])&&$_GET['action']=='delete_micro') {
/* -----------------------------------------------------
    功能: 把指定的micro_log的id和当前用户关联
    参数: $_POST['nid'] micro_log的id值 
 -----------------------------------------------------*/
  $notice_raw = tep_db_query("select * from ".TABLE_NOTICE." where id = '".$_POST['nid']."' and type = '1'");
  $notice = tep_db_fetch_array($notice_raw);

  $notice_users_str = ''; 
  if ($notice) {

    if($notice['deleted'] == ''){

      $notice_users_str = $ocertify->auth_user; 
    }else{

      $notice_users_str = $notice['deleted'].','.$ocertify->auth_user;
    }
    tep_db_query("update ".TABLE_NOTICE." set deleted='".$notice_users_str."' where id = '".$_POST['nid']."'");
  }
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
