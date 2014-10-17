<?php
/*
  $Id$
*/
include("includes/application_top.php");
include_once(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
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
      $attendance_start = $_POST['att_start_hour'].':'.$_POST['att_start_minute_a'].$_POST['att_start_minute_b'];
      $attendance_end = $_POST['att_end_hour'].':'.$_POST['att_end_minute_a'].$_POST['att_end_minute_b'];
      $sql_att_info = "select * from " .TABLE_ATTENDANCE_RECORD. " where id='".$_POST['aid']."'";
      $query_att_info = tep_db_query($sql_att_info);
      if($row_att_info = tep_db_fetch_array($query_att_info)){
        $attendance_login_start = substr($row_att_info['login_time'],0,11);
        $attendance_login_end = substr($row_att_info['login_time'],16,3);
        $attendance_logout_start = substr($row_att_info['logout_time'],0,11);
        $attendance_logout_end = substr($row_att_info['logout_time'],16,3);
        $login = $attendance_login_start.$attendance_start.$attendance_login_end; 
        $logout = $attendance_logout_start.$attendance_end.$attendance_logout_end; 
      }
      $date_info = tep_date_info($_POST['get_date']);
      $start_str = $date_info['year'].'-'.$date_info['month'].'-'.$date_info['day'];
      if(isset($_POST['aid'])&&$_POST['aid']!=''){
        $sql_update = "update " .TABLE_ATTENDANCE_RECORD. " set
          login_time='".$login."',logout_time='".$logout."' where
          id='".$_POST['aid']."'";
        tep_db_query($sql_update);
      }else{
        $sql_last_index  = "select nums from " .TABLE_ATTENDANCE_RECORD. " where user_name='".$_POST['uid']."' and nums < 100 and date='".$_POST['get_date']."' order by nums desc limit 1";
        $query_last_index = tep_db_query($sql_last_index);
        $index=1;
        if($t_row = tep_db_fetch_array($query_last_index)){
          $index = $t_row['nums']+1;
        }
        $sql_insert = array(
            'user_name' => $_POST['uid'],
            'login_time' => $start_str.' '.$attendance_start.':00',
            'logout_time' => $start_str.' '.$attendance_end.':00',
            'date' => $_POST['get_date'],
            'nums' => $index,
            );
          tep_db_perform(TABLE_ATTENDANCE_RECORD,$sql_insert);
      }
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
		        $_POST['has_space'][$k] = 0;	
			}	
		}

        foreach($a_id_arr as $key => $value){
			if($type_arr[$key]==8){
			  $type_arr[$key]=1;
			}
		$u_key = $_POST['u_group'][$key];	
                $update_flag = true;
			foreach($_POST['has_user'][$u_key] as $k=>$user_id){
          $update_date = false;
          if(!empty($_POST['has_user']['new'][$u_key])){
            $update_date = true;
          }
          if(!$update_date){
            $old_attendance_detail_sql = "select * from ".TABLE_ATTENDANCE_DETAIL_DATE. " WHERE id ='".$_POST['data_as'][$u_key][$k]."' limit 1";
            $old_attendance_detail_query = tep_db_query($old_attendance_detail_sql);
            if($temp_row = tep_db_fetch_array($old_attendance_detail_query)){
              if($user_id != $temp_row['user_id']){
                $update_date = true;
              }
              if($type_arr[$key] != $temp_row['type']){
                $update_date = true;
              }
              if($value != $temp_row['attendance_detail_id']){
                $update_date = true;
              }
              if($_POST['has_space'][$key] != $temp_row['space']){
                $update_date = true;
              }
              if($_POST['get_date'] == $temp_row['date']){
                $update_date = false;
              }
            }
          }
          if($update_date==false){
            $update_flag = $update_date;
          }

        }
        $update_insert_id = '';                        
        foreach($_POST['has_user'][$u_key] as $k=>$user_id){
               $sql_arr = array(
                  'week' => $date_info['week'],
                  'week_index' => $date_info['week_index'],
                  'attendance_detail_id' => $value,
                  'user_id' => $user_id,
				  'u_group' => $u_key, 
                  'type' => $type_arr[$key],
                  'update_user' => $user,
                  'update_time' => 'now()',
			      'space' => $_POST['has_space'][$key],
              );
          if(isset($_POST['default_uid'])&&$_POST['default_uid']!=''){
            $sql_arr['user_id'] = $_POST['default_uid'];
          }
          $sql_arr['is_user'] = 1;
          if($_POST['type_array'][$key]!= $type_arr[$key]||$update_date){
            $sql_arr['date'] =  $_POST['get_date'];
            $sql_arr['month'] =  $date_info['month'];
            $sql_arr['day'] =  $date_info['day']; 
                
          }
        if($update_flag){
            $temp_arr['valid_date'] = $_POST['get_date'];
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$temp_arr,'update','id=\''.$_POST['data_as'][$u_key][$k].'\'');
            $sql_arr['add_user'] = $user;
            $sql_arr['add_time'] = 'now()';
            $sql_arr['parent_id'] = $_POST['data_as'][$u_key][$k];
            $sql_arr['u_group'] = $update_insert_id;
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr);
            if($update_insert_id == ''){
              $update_insert_id = tep_db_insert_id();
               tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,array('u_group'=>$update_insert_id),'update','id=\''.$update_insert_id.'\'');
               $sql_arr['u_group'] = $update_insert_id;
            }
        }else{
            $sql_arr['u_group'] = $update_insert_id;
            if($update_insert_id==''){
              $update_insert_id = $_POST['data_as'][$u_key][$k];
              $sql_arr['u_group'] = $update_insert_id;
            }
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$u_key][$k].'\'');
        }
        }
			$old_info_list[$u_key]=$sql_arr;
			}

		$sql_new_arr = array();
		foreach($_POST['has_user']['new'] as $k=>$userlist) {
			for($i=0;$i<count($userlist);$i++){
				$sql_new_has_arr = $old_info_list[$k]; 
				$sql_new_has_arr['user_id']=$userlist[$i];
				$sql_new_has_arr['add_time']=date("Y-m-d H:i:s");
				$sql_new_has_arr['add_user']=$user;
                $sql_new_has_arr['date'] =  $_POST['get_date'];
                $sql_new_has_arr['month'] =  $date_info['month'];
                $sql_new_has_arr['day'] =  $date_info['day']; 
             tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_new_has_arr);
            }
		}
		
      }
      if(isset($_POST['attendance_id'])
          &&is_array($_POST['attendance_id'])
          &&!empty($_POST['attendance_id'])){
        $a_id_arr = $_POST['attendance_id'];
        if(isset($_POST['user'])&&!empty($_POST['user'])){
          $user_arr = $_POST['user'];
        }else{
          $user_arr = $_POST['user_hidden'];
        }
        $type_arr = $_POST['type'];

		foreach($_POST['space'] as $k => $val) {
			if(empty($val)|| $type_arr[$k]!=1){
		        $_POST['space'][$k] = 0;	
			}	
		}
        foreach($a_id_arr as $key => $value){
			if($type_arr[$key]==8){
			  $type_arr[$key]=1;
			}

                        $update_insert_id = '';
			foreach($_POST['user'][$key+1] as $k=>$user_new){
				for($j=0;$j<count($user_new);$j++){

          $sql_arr = array(
              'date' => $_POST['get_date'],
              'month' => $date_info['month'],
              'day' => $date_info['day'],
              'week' => $date_info['week'],
              'week_index' => $date_info['week_index'],
              'attendance_detail_id' => $value,
              'user_id' => $user_new,
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
          $sql_arr['u_group'] = $update_insert_id;
          tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr);
          if($update_insert_id == ''){
            $update_insert_id = tep_db_insert_id();
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,array('u_group'=>$update_insert_id),'update','id=\''.$update_insert_id.'\'');
          }


				
				}
			}
        }
      }
      if(isset($_POST['del_as'])&&!empty($_POST['del_as'])){
        foreach($_POST['del_as'] as $del_as){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where
              id="'.$del_as.'"');
        }
      }
      if(isset($_POST['del_group'])&&!empty($_POST['del_group'])){
        foreach($_POST['del_group'] as $del_group){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where u_group ="'.$del_group.'"');
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
			if($type_arr[$key]==8){
			  $type_arr[$key]=1;
			}
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
          $old_attendance_detail_sql = "select * from
            ".TABLE_ATTENDANCE_DETAIL_DATE. " WHERE id ='".$_POST['data_as'][$key]."' limit 1";
          $old_attendance_detail_query = tep_db_query($old_attendance_detail_sql);
          $update_date = false;
          if($temp_row = tep_db_fetch_array($old_attendance_detail_query)){
            if($group_arr[$key] != $temp_row['group_id']){
              $update_date = true;
            }
            if($type_arr[$key] != $temp_row['type']){
              $update_date = true;
            }
            if($value != $temp_row['attendance_detail_id']){
              $update_date = true;
            }
            if($_POST['has_space'][$key] != $temp_row['space']){
              $update_date = true;
            }
            if($_POST['get_date'] == $temp_row['date']){
              $update_date = false;
            }
          }
	      if($_POST['type_array'][$key]!= $type_arr[$key]||$update_date){
            $sql_arr['date'] =  $_POST['get_date'];
            $sql_arr['month'] =  $date_info['month'];
            $sql_arr['day'] =  $date_info['day']; 
                
		  }


          if($update_date){
            $temp_arr['valid_date'] = $_POST['get_date'];
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$temp_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
            $sql_arr['add_user'] = $user;
            $sql_arr['add_time'] = 'now()';
            $sql_arr['parent_id'] = $_POST['data_as'][$key];
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr);
//            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
          }else{
            tep_db_perform(TABLE_ATTENDANCE_DETAIL_DATE,$sql_arr,'update','id=\''.$_POST['data_as'][$key].'\'');
          }
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
		        $_POST['space'][$k] = 0;	
			}	
		}

        foreach($a_id_arr as $key => $value){
			if($type_arr[$key]==8){
			  $type_arr[$key]=1;
			}
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
          //修改 原来的时间
          $old_attandance_detail_date_sql = " select * from ".TABLE_ATTENDANCE_DETAIL_DATE." WHERE id='".$del_as."'";
          $old_attandance_detail_date_query = tep_db_query($old_attandance_detail_date_sql);
          if($old_res_temp = tep_db_fetch_array($old_attandance_detail_date_query)){
            $old_valid_date = $old_res_temp['valid_date'];
            $update_parent_id_sql = "update ".TABLE_ATTENDANCE_DETAIL_DATE." set parent_id =".$old_res_temp['parent_id']." where parent_id='".$del_as."'";
            $update_valid_date_sql = "update ".TABLE_ATTENDANCE_DETAIL_DATE." set valid_date=".$old_valid_date." where id='".$old_res_temp['parent_id']."'";
            tep_db_query($update_parent_id_sql);
            tep_db_query($update_valid_date_sql);
          }
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where id="'.$del_as.'"');
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
        foreach($_POST['data_as'] as $key => $add_id){
          tep_db_query('delete from '.TABLE_ATTENDANCE_DETAIL_DATE.' where u_group="'.$key.'"');
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
      $allow_user_str = implode('|||',$_POST['allow_user']);
      $text_info = $_POST['text_info'];
      //变更前模板名称
      $attendance_name = array();
      $attendance_name_query = tep_db_query("select id,title from ".TABLE_ATTENDANCE_DETAIL." where id='".$attendance_detail_id."' or id='".$replace_attendance_detail_id."'");
      while($attendance_name_array = tep_db_fetch_array($attendance_name_query)){


        $attendance_name[$attendance_name_array['id']] = $attendance_name_array['title'];
      }
      tep_db_free_result($attendance_name_query);
      //发送请假邮件
      if($_POST['allow_status']==0){ 
        for($i=0;$i<count($_POST['allow_user']);$i++){
            $leave_email = tep_get_mail_templates('LEAVE_MAIL_TEMPLATES','0');
            $mail_model_tep = $leave_email['contents'];
	    $allow_user = tep_get_user_info($_POST['allow_user'][$i]);
            $staff_info = tep_get_user_info($_POST['user_id']);
            $mail_model_tep = str_replace(array(
	      '${URL}',
              '${STAFF_NAME}',
              '${APPROVER}',
              '${WORK_START}',
              '${WORK_END}',
              '${ALTERED_START}',
              '${ALTERED_END}',
              '${DATE}',
              '${COMMENT}',
              '${STATUS}', 
              '${BEFORE}', 
              '${AFTER}' 
              ),array(
	      $_SERVER['HTTP_REFERER'],
	      $staff_info['name'],
	      $allow_user['name'],
	      $_POST['email_work_start'],
	      $_POST['email_work_end'],
	      $leave_start,
	      $leave_end,
              date('Y-m-d',strtotime($date)),
              $_POST['text_info'],
              SENDMAIL_ROSTER_STATUS_CONFIRM,
              $attendance_name[$attendance_detail_id],
              $attendance_name[$replace_attendance_detail_id]
            ),$mail_model_tep);
            $mail_model_tep = tep_replace_mail_templates($mail_model_tep);  
            tep_mail($allow_user['name'],$allow_user['email'],$leave_email['title'],$mail_model_tep,get_configuration_by_site_id('STORE_OWNER', 0), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', 0));
	}
      }else{
        $leave_email = tep_get_mail_templates('LEAVE_REPLY_MAIL_TEMPLATES','0');
        $mail_model_tep = $leave_email['contents'];
        $staff_info = tep_get_user_info($_POST['user_id']);
        //许可者
        $allow_user_info = array();
        foreach($_POST['allow_user'] as $allow_user_value){

          $allow_user_name = tep_get_user_info($allow_user_value);
          $allow_user_info[] = $allow_user_name['name'];
        }
        $mail_model_tep = str_replace(array(
	      '${URL}',
              '${STAFF_NAME}',
              '${APPROVER}',
              '${WORK_START}',
              '${WORK_END}',
              '${ALTERED_START}',
              '${ALTERED_END}',
	      '${DATE}', 
              '${COMMENT}', 
              '${STATUS}', 
              '${BEFORE}', 
              '${AFTER}'
              ),array(
	      $_SERVER['HTTP_REFERER'],
	      $staff_info['name'],
	      implode(' ',$allow_user_info),
	      $_POST['email_work_start'],
	      $_POST['email_work_end'],
	      $leave_start,
	      $leave_end, 
              date('Y-m-d',strtotime($date)),
              $_POST['text_info'],
              SENDMAIL_ROSTER_STATUS_ALLOW,
              $attendance_name[$attendance_detail_id],
              $attendance_name[$replace_attendance_detail_id]
              ),$mail_model_tep);
        $mail_model_tep = tep_replace_mail_templates($mail_model_tep);
        tep_mail($staff_info['name'],$staff_info['email'],$leave_email['title'],$mail_model_tep,get_configuration_by_site_id('STORE_OWNER', 0), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', 0));
      }

      if(isset($_POST['replace_id'])&&$_POST['replace_id']!=''&&$_POST['replace_id']!=0) {
        $sql_update_arr = array(
            'replace_attendance_detail_id' => $replace_attendance_detail_id,
            'leave_start' => $leave_start,
            'leave_end' => $leave_end,
            'allow_user' => $allow_user_str,
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
            'allow_user' => $allow_user_str,
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
      $error = true;
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
          $insert_arr['att_status'] = $_POST['att_status'];
          $perform_flag =tep_db_perform(TABLE_ATTENDANCE_GROUP_SHOW,$insert_arr);
          if($error){
             $error = $perform_flag;
          }
        }
        if($error){
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
        }
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
        $insert_arr['att_status'] = $_POST['att_status'];
        $perform_flag =tep_db_perform(TABLE_ATTENDANCE_GROUP_SHOW,$insert_arr);
        if($error){
           $error = $perform_flag;
        }

        if($error){
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
        }
	  }
      break;
	  /**
	   *attendance_detail
	   */
case 'insert':
case 'update':
	 tep_isset_eof();
	$id = $_POST['id'];
	 $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " where id=".$id;
	 $attendance_info_tep = tep_db_query($attendance_select_sql);
     $attendance_info_res = tep_db_fetch_array($attendance_info_tep);
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
	   'title' => $title==''?$attendance_info_res['title']:$title,
	   'short_language' => $short_language==''?$attendance_info_res['short_language']:$short_language,
	   'src_text'=> $src_text==''?$attendance_info_res['src_text']:$src_text,
	   'param_a' => $param_a==''?$attendance_info_res['param_a']:$param_a, 
	   'param_b' => $param_b==''?$attendance_info_res['param_b']:$param_b, 
       'sort' => $sort==''?$attendance_info_res['sort']:$sort,
	   'scheduling_type' => $scheduling_type==''?$attendance_info_res['scheduling_type']:$scheduling_type,
	   'set_time' => $set_time==''?$attendance_info_res['set_time']:$set_time,
       'work_start' => $work_start==''?$attendance_info_res['work_start']:$work_start,
	   'work_end' => $work_end==''?$attendance_info_res['work_end']:$work_end,
	   'rest_start' => $rest_start==''?$attendance_info_res['rest_start']:$rest_start,
	   'rest_end' => $rest_end==''?$attendance_info_res['rest_end']:$rest_end,
	   'work_hours' => $work_hours==''?$attendance_info_res['work_hours']:$work_hours,
	   'rest_hours' => $rest_hours==''?$attendance_info_res['rest_hours']:$rest_hours,
	   'add_user' => $add_user==''?$attendance_info_res['add_user']:$add_user,
	   'add_time' => $add_time==''?$attendance_info_res['add_time']:$add_time,
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
    case 'save_att_status':
        $update_sql = "update ".TABLE_ATTENDANCE_GROUP_SHOW." set att_status='".$_GET['att_status']."' where is_select='1' and  operator_id='".$ocertify->auth_user."'";
        tep_db_query($update_sql);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
         break;
    case 'save_type':
        $update_sql = "update ".TABLE_ATTENDANCE_GROUP_SHOW." set show_type='".$_GET['show_type']."' where is_select='1' and  operator_id='".$ocertify->auth_user."'";
        tep_db_query($update_sql);
        tep_redirect(tep_href_link(FILENAME_ROSTER_RECORDS,
            ((isset($_GET['y'])&&$_GET['y']!='')?'y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')));
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
<script language="javascript" src="includes/3.4.1/build/yui/yui.js?v=<?php echo $back_rand_info?>"></script>

<script language="javascript">
var js_cale_date = '<?php echo date('Y-m-d', time())?>'; 
var warn_change_attendance_error = '<?php echo TEXT_WARN_CHANGE_ATTENDANCE_OVERLAP;?>'
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
<style type="text/css">
a.dpicker {
width: 16px;
height: 18px;
border: none;
color: #fff;
padding: 0;
margin:0;
overflow: hidden;
display:block;
cursor: pointer;
background: url(./includes/calendar.png) no-repeat;
}
#new_yui3 {
  margin-left:-168px;
  *margin-left:-28px;
  margin-left:-170px\9;
position: absolute;
          z-index:200px;
          margin-top:15px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
#new_yui3{
position: absolute;
          z-index:200px;
          margin-top:17px;
}
}
.yui3-skin-sam img,.yui3-skin-sam input,.date_box{ float:left;}
.yui3-skin-sam .redtext {
color:#0066CC;
}
</style>
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
//时间参数
$param_attendance = $_SERVER['QUERY_STRING'];
$param_tep = explode('&',$param_attendance);
if($param_tep[0]!=''){
	if(count($param_tep)>1){
    $param .=','.$param_tep[0].','.$param_tep[1];
	}
}


      //判断用户是否打过卡
        $user_atted = array();
        $status_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $status_str .= '<td>userlist</td>';
        $status_str .= '</tr></table>';
        $attendance_str = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
        $attendance_str .= '<td>attendance</td>';
        $attendance_str .= '</tr></table>';


        $self_href = tep_href_link(FILENAME_ROSTER_RECORDS);
        $user_info = tep_get_user_info($ocertify->auth_user);
        $group_list = tep_get_group_tree();
        $show_group_id=0;
        $show_checked_user_list = array();
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
		  $show_att_status =$show_group_row['att_status'];
                  $show_type = $show_group_row['show_type'];
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
        $group_str ='';
        $group_str .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        /*
        $group_str = '<form action="'.
        tep_href_link(FILENAME_ROSTER_RECORDS,'action=update_show_user'.
            ((isset($_GET['y'])&&$_GET['y']!='')?'&y='.$_GET['y']:'').
            ((isset($_GET['m'])&&$_GET['m']!='')?'&m='.$_GET['m']:'')).'" method="post">';
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
          $group_str .= '>'.$group['text'].'</option>';
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
            $show_checked_user_list[] = $key;
          }
          $group_str .= ' value="'.$key.'" >';
          $group_str .=  '<label for="'.$key.'">'.$val.'</label>';
          $group_str .= '&nbsp;&nbsp;&nbsp;';
		}

        $group_str .= '</div>';
        $group_str .= '</td>';
        $group_str .= '<td align="right">';
        $group_str .= '<input type="submit" value="'.IMAGE_UPDATE.'">';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        */

        //new 各种设定
        $group_str .= '<tr>';
        $group_str .= '<td valign="top">';
        $group_str .= TEXT_ATTENDANCE_SETTING;
        $group_sr .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
        $group_str .= '<tr>';
        $group_str .= '<td width="20%">';
        $group_str .= '<a href="javascript:void(0);" onclick="show_user_attendance_info(this,\'\',\'\',\'\',\'\',\'\');"><u>'.TEXT_ATTENDANCE_SETTING_USER.'</u></a>';
        $group_str .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<a href="javascript:void(0);" onclick="show_group_attendance_info(this,\'\',\'\',\'\',\'\');"><u>'.TEXT_ATTENDANCE_SETTING_GROUP.'</u></a>';
        $group_str .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<a href="javascript:void(0);" onclick="show_replace_attendance_info(this,\'\',\'\',\'\',\'\');"><u>'.TEXT_ATTENDANCE_SETTING_CHANGE.'</u></a>';
        $group_str .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<a style="text-decoration: underline;" href="javascript:void(0);" onclick="set_attendance_info(this, 0,0'.$param.')">'.TEXT_ATTENDANCE_SETTING_MOVE.'</a>';
        $group_str .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<a style="text-decoration: underline;" href="javascript:void(0);" onclick="set_attendance_info(this, 0,1'.$param.')">'.TEXT_ATTENDANCE_SETTING_PAYROLLS.'</a>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '</table>';
        $group_str .= '</td>';
        $group_str .= '</tr>';

        //new 显示设定
        $group_str .= '<tr>';
        $group_str .= '<td valign="top">';
        $group_str .= TEXT_ATTENDANCE_SETTING_SHOW;
        $group_sr .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
        $group_str .= '<tr>';
        $group_str .= '<td width="20%">';
        $group_str .= '<a style="text-decoration: underline;" href="javascript:void(0);" onclick="set_attendance_info(this,'.$show_group_id.',2'.$param.')">'.TEXT_GROUP_USER_LIST.'</a>';
        $group_str .= '</td>';
        $group_str .= '<td>';
        $group_str .= '<select onchange="save_type(this,\''.$self_href.'\')" name="show_mode">';
        $group_str .= '<option value="0" '.($show_type==0?' selected ':'').'>'.TEXT_ATTENDANCE_TABLE_MODE.'</option>';
        $group_str .= '<option value="1" '.($show_type==1?' selected ':'').'>'.TEXT_ATTENDANCE_CALENDAR_MODE.'</option>';
        $group_str .= '</select>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '</table>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        
        $group_str .= '<tr>';

        $group_str .= '<td valign="top">';
        $group_str .= TEXT_ADL_SELECT_USER_TEXT;
        $group_sr .= '</td>';
        $group_str .= '<td>';
		if($show_att_status==1){
	      $status_error = 'checked ="ckecked"';
		}elseif($show_att_status==2) {
	      $status_null = 'checked ="ckecked"';
		}else{
	      $status_all = 'checked ="ckecked"';
		}
        $group_str .= '<input type="radio" onclick="save_att_status(\''.$self_href.'\')" name="att_status" id="show_all" value="0" '.$status_all.' ><label for="show_all">'.SHOW_ALL_ATT_STATUS.'</label>';
        $group_str .= '<input type="radio" onclick="save_att_status(\''.$self_href.'\')" name="att_status" id="show_error" value="1" '.$status_error.'><label for="show_error">'.SHOW_ERROR_ATT_STATUS.'</label>';
        $group_str .= '<input type="radio" onclick="save_att_status(\''.$self_href.'\')" name="att_status" id="show_null" value="2" '.$status_null.'><label for="show_null">'.SHOW_NULL_ATT_STATUS.'</label>';
        $group_sr .= '</td>';

        $group_str .= '</tr>';
        $group_str .= '</table></form>';
        $group_str .= '<input type="hidden" id="hidden_year" value="'.$_GET['y'].'">';
        $group_str .= '<input type="hidden" id="hidden_month" value="'.$_GET['m'].'">';
        $group_str .= '<input type="hidden" id="hidden_user" value="'.$ocertify->auth_user.'">';
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

$attendance_select_sql = "select attendance_detail_id from ".TABLE_ATTENDANCE_DETAIL_DATE." where "; 
$replace_select_sql = '';
if(!empty($show_select_group_user)){
  if($show_group_id == 0){
    $attendance_select_sql .= " is_user = 0 ";
  }else{
    $attendance_select_sql .= "(is_user=0 and group_id = '".$show_group_id."')";
  }
  $replace_select_sql = "select replace_attendance_detail_id from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where user in ('".implode("','",$show_select_group_user)."')";
  $attendance_select_sql .= " or (is_user=1 and user_id in ('".implode("','",$show_select_group_user)."'))";
}else{
  $attendance_select_sql .= ' false ';
}
$attendance_select_query = tep_db_query($attendance_select_sql);
$attendance_select_array = array();
while($rows = tep_db_fetch_array($attendance_select_query)){
  $attendance_select_array[] = $rows['attendance_detail_id'];
}
if($replace_select_sql != ''){
  $replace_select_query = tep_db_query($replace_select_sql);
  while($rows = tep_db_fetch_array($replace_select_query)){
    $attendance_select_array[] = $rows['replace_attendance_detail_id'];
  }
}
$attendance_select_array = array_unique($attendance_select_array);


$attendance_select_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
$tep_result = tep_db_query($attendance_select_sql);
 $attendance_list=array();
 while($rows= tep_db_fetch_array($tep_result)) {
   if(in_array($rows['id'],$attendance_select_array)){
     $attendance_list[] = $rows;
   }
 }
$all_user_info = array();
$all_user_name_info;
$all_user_sql = "select * from ". TABLE_USERS ." where status='1'";
$all_user_query = tep_db_query($all_user_sql);
while($user_info_row = tep_db_fetch_array($all_user_query)){
  $all_user_info[] = $user_info_row['userid'];
  $all_user_name_info[$user_info_row['userid']] = $user_info_row['name'];
}

 $num = count($attendance_list);
 $i=0;
 /*
 foreach($attendance_list as $k=>$val) {
 if($val['scheduling_type']==0){
    $image_directory = 'images';
    $image_dir = $image_directory.'/'.$val['src_text'];
	echo "<li style='float:right; height:16px; list-style-type:none; margin-right: 10px; margin-top:5px;'>";
        if($val['src_text']!=''&&file_exists($image_dir)){
          echo "<img src='".$image_dir."' style='width: 16px;'>"; 
        }
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
 
*/
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
        <td align="right" style="line-height:23px;">
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
$now_time = date('Hi',time());
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
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="dataTable_border">
<?php if($show_type==1){?>
<tr>
<?php 
echo '  <td width="9%">&nbsp;</td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_SUNDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_MONDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_TUESDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_WEDNESDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_THURSDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_FRIDAY.'</font></td>
        <td width="13%" align="middle" bgcolor="#eeeeee" height="15"><font size="2">'.CL_TEXT_DATE_STATURDAY.'</font></td>
        ';
        ?>
</tr>
<tr>
<td>&nbsp;</td>
<?php
for($i = 0; $i<$start_week; $i++)
{
  echo "<td></td>";
}
$end_day = $day_num+(7-($day_num+$start_week)%7);
$j=1;
while($j<=$end_day)
{ 
  $edit_replace = false;
  if($j<=$day_num){
    $date_temp = $year.tep_add_front_zone($month).tep_add_front_zone($j); //日期
    echo "<td id='date_td_".$j."'  valign='top' align='center'"; 
    if($today <= $date_temp){
      $edit_replace = true;
    }
    if($ocertify->npermission>10||tep_is_group_manager($ocertify->auth_user)){
      if($show_group_id!=0){
        echo " onclick='show_group_attendance_info(this,\"".$date_temp."\",\"".$j."\",\"".$show_group_id."\")' >";
      }else{
        echo " onclick='show_group_attendance_info(this,\"".$date_temp."\",\"".$j."\",\"\")' >";
      }
    }else{
      if($today <= $date_temp){
        echo " onclick='show_replace_attendance_info(this,\"".$date_temp."\",\"".$j."\",\"\")' >";
      }else{
        echo " >";
      }
    }
    echo $j."</td>";
  }else{
    echo '<td>&nbsp;</td>'; 
  }
  $week = ($start_week+$j-1)%7;

  if($week == 6){
    echo "</tr>";
    foreach($show_select_group_user as $user_value){

      $users_info = tep_get_user_info($user_value);
      //下面的一行代码，为了适应以前显示排班，临时加的，以后可以去掉
      $show_select_group_users = array($user_value);
      echo '<tr>';
      echo '<td>'.$users_info['name'].'</td>';
      if($j == 7 - $start_week){
        for($i = 0; $i<$start_week; $i++){
          echo "<td>&nbsp;</td>";
        }
      }
      $week_temp = $j == 7 - $start_week ? 6-$start_week : 6;
      for($k=$j-$week_temp;$k<=$j;$k++){
        if($k<=$day_num){
          $uid = $user_value; //用户ID
          $date = $year.tep_add_front_zone($month).tep_add_front_zone($k); //日期

  echo '<td>';
  echo '<div id ="table_div_databox_minsize"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="info_table_small">';
  echo "<tr><td align='right' style='font-size:14px; border-width:0px; cursor:pointer;' ";
  echo ">";
  if($date == $today){
    echo "<div class='dataTable_hight_red'>";
  }
  $temp_user_attenande = tep_all_attenande_by_uid($user_value,$date);
  if(empty($temp_user_attenande)){
    echo "&nbsp;";
  }
  //个人的所有排班
  $info_td_attendance_str = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="info_table_small">';
  foreach($temp_user_attenande as $user_attenande){
    //排班信息输出
    $attendance_info =  $all_att_arr[$user_attenande['attendance_detail_id']];
    $info_td_attendance_str .= "<tr>";
    if($attendance_info['scheduling_type'] == 0){
      $info_td_attendance_str .=  '<td style="border-width:0px; padding-top:6px;">';
      if($user_attenande['group_id']!=0){
        $info_td_attendance_str .=  "<span onclick='show_group_attendance_info(this,\"".$date."\",\"".$k."\",\"".$user_attenande['group_id']."\",\"".$user_attenande['aid']."\")' style=".$style.">";
      }else{
        $info_td_attendance_str .= "<span onclick='show_user_attendance_info(this,\"".$date."\",\"".$k."\",\"".$user_attenande['user_id']."\",\"".$user_attenande['aid']."\",\"".$user_attenande['attendance_detail_id']."\")' style='cursor:pointer;'>";
      }
      $info_td_attendance_str .=  $attendance_info['short_language'];
      if(file_exists("images/".$attendance_info['src_text'])&&$attendance_info['src_text']!=''){
        $info_td_attendance_str .=  '<img style="width:16px;" src="images/'.$attendance_info['src_text'].'" alt="'.$attendance_info['title'].'">';
      }
    }else{
      $info_td_attendance_str .=  "<td style='border-width:0px; padding-top:6px;".($attendance_info['scheduling_type'] == 1 && $attendance_info['src_text'] == '#000000' ? 'color:#FFFFFF;' : '')."' bgcolor='".$attendance_info['src_text']."'>";
      if($user_attenande['group_id']!=0){
        $info_td_attendance_str .=  "<span onclick='show_group_attendance_info(this,\"".$date."\",\"".$k."\",\"".$user_attenande['group_id']."\",\"".$user_attenande['aid']."\")' style=".$style.">";
      }else{
        $info_td_attendance_str .= "<span onclick='show_user_attendance_info(this,\"".$date."\",\"".$k."\",\"".$user_attenande['user_id']."\",\"".$user_attenande['aid']."\",\"".$user_attenande['attendance_detail_id']."\")' style='cursor:pointer;'>";
      }
      $info_td_attendance_str .=  $attendance_info['short_language'];
    }
    $info_td_attendance_str .=  "</span>";
    //是否在勤
    $temp_is_work_str = '';
    if($date==$today){
       $is_work = tep_check_show_login_logout($user_value);
       if($attendance_info['set_time']==0){
          $attendance_start = str_replace(':','',$attendance_info['work_start']);
          $attendance_end = str_replace(':','',$attendance_info['work_end']);
          if((($now_time> $attendance_start && $now_time < $attendance_end)||
                ($attendance_start > $attendance_end&& !($now_time<$attendance_start&&$now_time>$attendance_end)))&&$is_work==1){
           $temp_is_work_str .= "<img src='images/icons/working.jpg' alt='working'>&nbsp;";
          }
       }else{
         if($is_work==1){
           $temp_is_work_str .= "<img src='images/icons/working.jpg' alt='working'>&nbsp;";
         }
       }
      
    }
    //是否迟到早退
    $work_time_str = '';
    if($date<=$today&&$temp_is_work_str==''){
      $all_att_info = tep_validate_user_attenandced($show_checked_user_list,$date,$show_group_id);
      $work_time_str .= tep_show_att_time($all_att_info[$user_value][$user_attenande['attendance_detail_id']],$user_value,$date,$attendance_info['src_text'],$j,$show_att_status,'1');
    }
    $info_td_attendance_str .= '<span>'.$work_time_str.'</span>';
    $info_td_attendance_str .= '<span>'.$temp_is_work_str.'</span>';
    //请假信息输出
    $replace_sql = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." 
      WHERE user='".$user_value."' 
      and attendance_detail_id='".$user_attenande['attendance_detail_id']."' 
      and date='".$date."'";
    $replace_query = tep_db_query($replace_sql);
    if($replace_user_row = tep_db_fetch_array($replace_query)){
      $info_td_attendance_str .= "<span onclick='show_replace_attendance_info(this,\"".$date."\",\"".$j."\",\"".$user_value."\",\"".$user_attenande['attendance_detail_id']."\")'>";
      $replace_attendance_info = $all_att_arr[$replace_user_row['replace_attendance_detail_id']];
      if($replace_attendance_info['scheduling_type']==0){
        if(file_exists("images/".$replace_attendance_info['src_text'])&&$replace_attendance_info['src_text']!=''){
          $info_td_attendance_str .=  '<img style="width:16px;" src="images/'.$replace_attendance_info['src_text'].'" alt="'.$replace_attendance_info['title'].'">';
        }
        
      }else{
        $info_td_attendance_str .= '<span class="rectangle" style="background-color:'.$replace_attendance_info['src_text'].';">&nbsp;</span>';
      }
      if($replace_user_row['allow_status']==0&& 
          (in_array($ocertify->auth_user,explode('|||',$replace_user_row['allow_user']))||
           $ocertify->auth_user==$replace_user_row['user'])){
        $info_td_attendance_str .= "<img src='images/icons/mark.gif' alt='UNALLOW'>";
      }
      $info_td_attendance_str .= '</span>';
    }

    $info_td_attendance_str .=  "</td>";
    $info_td_attendance_str .=  "</tr>";
  }
  //请假的排班
  $other_replace_sql = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." 
    WHERE user='".$user_value."' 
    and attendance_detail_id='0' 
    and date='".$date."'";
  $other_replace_query = tep_db_query($other_replace_sql);
  while($other_replace_user_row = tep_db_fetch_array($other_replace_query)){
    $replace_attendance_info = $all_att_arr[$other_replace_user_row['replace_attendance_detail_id']];
    $info_td_attendance_str .= "<tr>";
    if($replace_attendance_info['scheduling_type'] == 0){
      $info_td_attendance_str .=  '<td style="border-width:0px; padding-top:6px;">';
      $info_td_attendance_str .= "<span onclick='show_replace_attendance_info(this,\"".$date."\",\"".$j."\",\"".$user_value."\",\"".$other_replace_user_row['attendance_detail_id']."\")' >";
      $info_td_attendance_str .=  $replace_attendance_info['short_language'];
      if(file_exists("images/".$replace_attendance_info['src_text'])&&$replace_attendance_info['src_text']!=''){
        $info_td_attendance_str .=  '<img style="width:16px;"
          src="images/'.$replace_attendance_info['src_text'].'" alt="'.$replace_attendance_info['title'].'">';
      }
    }else{
      $info_td_attendance_str .=  "<td style='border-width:0px; padding-top:6px;".($replace_attendance_info['scheduling_type'] == 1 && $replace_attendance_info['src_text'] == '#000000' ? 'color:#FFFFFF;' : '')."' bgcolor='".$replace_attendance_info['src_text']."'>";
      $info_td_attendance_str .= "<span onclick='show_replace_attendance_info(this,\"".$date."\",\"".$j."\",\"".$user_value."\",\"".$other_replace_user_row['attendance_detail_id']."\")' >";
      $info_td_attendance_str .=  $replace_attendance_info['short_language'];
    }
    if($other_replace_user_row['allow_status']==0&& 
        (in_array($ocertify->auth_user,explode('|||',$other_replace_user_row['allow_user']))||
         $ocertify->auth_user==$other_replace_user_row['user'])){
      $info_td_attendance_str .= "<img src='images/icons/mark.gif' alt='UNALLOW'>";
    }
    $info_td_attendance_str .= "</span>";
    $info_td_attendance_str .= "</td>";
    $info_td_attendance_str .= "</tr>";
  }
  $info_td_attendance_str .= '</table>';
  echo $info_td_attendance_str;
  if($date == $today){
    echo "</div>";
  }
  echo "</td></tr>";
    echo "</table>";
    echo "</div>";
    echo "</td>";
    //end
        }else{
          echo '<td>&nbsp;</td>'; 
        }
      }
      echo '</tr>';
    }
    if($j<$day_num){
      echo "<tr><td>&nbsp;</td>";
    }
  }
  $j++;
}
}else{
  ?>
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
  $attendance_arr = tep_get_attendance($date,$show_group_id,false);
  $user_att_arr = tep_get_attendance_user($date,'',false);
  $all_user_list = array();
  $all_user_att_info = array();
  if($show_att_status!=2){
    if($date<=$today){
      $all_att_info = tep_validate_user_attenandced($show_checked_user_list,$date,$show_group_id);
    }
  }
  foreach($user_att_arr as $t_value){
    $all_user_list[] = $t_value['user_id'];
    $all_user_att_info[$t_value['user_id']][] = $t_value;
  }
  $user_att_arr_source = $user_att_arr;
  $user_att_arr = array();
  $tmp_user_att_arr = array();
  foreach($user_att_arr_source as $uaas_value){
    $tmp_user_att_arr[$uaas_value['attendance_detail_id']][] = $uaas_value;
  }
  foreach($tmp_user_att_arr as $tuaa_value){
    foreach($tuaa_value as $tmp_value){
      $user_att_arr[] = $tmp_value;
    }
  }
  $sql_replace_att = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." WHERE 
    `date` = '".$date."'";
  $query_replace_att = tep_db_query($sql_replace_att);
  $all_replace_att = array();
  while($row_replace_res = tep_db_fetch_array($query_replace_att)){
    $all_replace_att[] = $row_replace_res;
  }
  if(!empty($show_att_user_list)){
    asort($show_att_user_list);
  }
  $style= (empty($attendance_arr)) ? '':'cursor:pointer;';
  echo "<td id='date_td_".$j."'  valign='top' >";
  echo '<div id ="table_div_databox_minsize"><table width="100%" border="0"
    cellspacing="0" cellpadding="0" class="info_table_small">';
  echo "<tr><td align='right' style='font-size:14px; border-width:0px; cursor:pointer;' ";
  if($ocertify->npermission>10||tep_is_group_manager($ocertify->auth_user)){
    if($show_group_id!=0){
      echo " onclick='attendance_setting(\"".$date."\",\"".$j."\",\"".$show_group_id."\")' >";
    }else{
      echo " onclick='attendance_setting(\"".$date."\",\"".$j."\",\"\")' >";
    }
  }else{
    if($today <= $date){
      echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$ocertify->auth_user."\")' >";
    }else{
      echo " >";
    }
  }
  if($date == $today){
    echo "<div class='dataTable_hight_red'>";
    echo '&nbsp;'.$j;
    echo "</div>";
  }else{
    echo $j;
  }
  echo "</td></tr>";
  $user_worker_list = array();
  $user_att_info = array();
  $attendance_arr = tep_sort_attendance($attendance_arr,$all_att_arr);
  foreach($attendance_arr as $attendance_row){
    $info_td_attendance_str = '';
    $show_info_td_attendance_str = false;
    $attendance_info = $all_att_arr[$attendance_row['attendance_detail_id']];
    if(!empty($attendance_info)){
    if(!empty($show_select_group_user)&&$date){
    if(tep_is_show_att($attendance_row['id'],$date)){
      $info_td_attendance_str .= "<tr>";
      if($attendance_info['scheduling_type'] == 0){
        $info_td_attendance_str .=  '<td style="border-width:0px; padding-top:6px;">';
        $info_td_attendance_str .=  "<div onclick='attendance_setting(\"".$date."\",\"".$j."\",\"".$attendance_row['group_id']."\",\"".$attendance_row['id']."\")' style=".$style.">";
        $info_td_attendance_str .=  $attendance_info['short_language'];
        if(file_exists("images/".$attendance_info['src_text'])&&$attendance_info['src_text']!=''){
          $info_td_attendance_str .=  '<img style="width:16px;" src="images/'.$attendance_info['src_text'].'" alt="'.$attendance_info['title'].'">';
        }
      }else{
        $info_td_attendance_str .=  "<td style='border-width:0px; padding-top:6px;".($attendance_info['scheduling_type'] == 1 && $attendance_info['src_text'] == '#000000' ? 'color:#FFFFFF;' : '')."' bgcolor='".$attendance_info['src_text']."'>";
        $info_td_attendance_str .=  "<div onclick='attendance_setting(\"".$date."\",\"".$j."\",\"".$attendance_row['group_id']."\",\"".$attendance_row['id']."\")' style=".$style.">";
        $info_td_attendance_str .=  $attendance_info['short_language'];
      }
      $info_td_attendance_str .=  "</div>";
      foreach($show_select_group_user as $u_list){
        //去除 单人排班的
        if(in_array($u_list,$all_user_list)){
          $show_user_flag = false;
          foreach($all_user_att_info[$u_list] as $u_att_info){
            $tmp_uai = $all_att_arr[$u_att_info['attendance_detail_id']];
            if($tmp_uai == $attendance_info){
              $show_user_flag = true;
              break;
            }
            if(validate_two_time($attendance_info['work_start'],$attendance_info['work_end'],
                  $tmp_uai['work_start'],$tmp_uai['work_end'])&&$tmp_uai['set_time']==0&&$attendance_info['set_time']==0){
              $show_user_flag = true;
              break;
            }
          }
          foreach($all_replace_att as $row_replace_att){
            if(validate_two_time($attendance_info['work_start'],$attendance_info['work_end'],$row_replace_att['leave_start'],$row_replace_att['leave_end'])&&$attendance_info['set_time']==0){
              $show_user_flag = true;
              break;
            }
          }
          if($show_user_flag){
            continue;
          }
        }
        $replace_str = '';
        $v_att=false;
        if(in_array($attendance_row['group_id'],tep_get_groups_by_user($u_list))){
          $show_info_td_attendance_str = true;
          if($date<= $today){
            if($date == $today){
              $is_work = tep_check_show_login_logout($u_list);
              if($all_att_arr[$attendance_row['attendance_detail_id']]['set_time']==0){
                $attendance_start = str_replace(':','',$all_att_arr[$attendance_row['attendance_detail_id']]['work_start']);
                $attendance_end = str_replace(':','',$all_att_arr[$attendance_row['attendance_detail_id']]['work_end']);
                if((($now_time> $attendance_start && $now_time < $attendance_end)||($attendance_start > $attendance_end&&!($now_time<$attendance_start&&$now_time>$attendance_end)))&&$is_work==1){
                  $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
                  $v_att = false;
                }else if($now_time>$attendance_end&&$now_time>$attendance_start){
                  $v_att = tep_show_att_time($all_att_info[$u_list][$attendance_info['id']],$u_list,$date,$attendance_info['src_text'],$j,$show_att_status);
                }
              }else{
                if($is_work==1){
                  $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
                  $v_att = false;
                }else{
                  $v_att = tep_show_att_time($all_att_info[$u_list][$attendance_info['id']],$u_list,$date,$attendance_info['src_text'],$j,$show_att_status);
                }
              }
            }else{
              $v_att = tep_show_att_time($all_att_info[$u_list][$attendance_info['id']],$u_list,$date,$attendance_info['src_text'],$j,$show_att_status);
            }
          }else{
            $v_att = false;
          }
        $user_replace = tep_get_replace_by_uid_date($u_list,$date,$attendance_row['attendance_detail_id']);
        $info_td_attendance_str .=  "<span>";
        if(!empty($user_replace)){
          if($user_replace['allow_status']==1){
             continue;  
          }
        }
        $info_td_attendance_str .=  "<a href='javascript:void(0)' ";
        $manager_list = tep_get_user_list_by_userid($u_list);
        if($ocertify->auth_user==$u_list||$ocertify->npermission>'10'||in_array($ocertify->auth_user,$manager_list)){
          if($date>=$today||!empty($user_replace)){
            $info_td_attendance_str .=  " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$u_list."\",\"".$attendance_row['attendance_detail_id']."\")' ";
          }
        }else{
          $v_att = false;
        }
        $info_td_attendance_str .=  ($attendance_info['scheduling_type'] == 1 && $attendance_info['src_text'] == '#000000' ? ' style="color:#FFFFFF;"' : '').">";
        if($v_att!=false){
          $info_td_attendance_str .=  preg_replace("/$/",$replace_str.'',$v_att);
        }else{
          $temp_user_sql = "select * from ".TABLE_GROUPS." 
            where id='".$attendance_row['group_id']."'";
          $temp_user_query = tep_db_query($temp_user_sql);
          if($temp_user_row = tep_db_fetch_array($temp_user_query)){
            $temp_show_group_user = explode('|||',$temp_user_row['all_users_id']);
          }
          if(in_array($u_list,$temp_show_group_user)){
            $t_info = tep_get_user_info($u_list);
            $info_td_attendance_str .=  $t_info['name'].$replace_str;
          }
        }
        $info_td_attendance_str .=  "</a>";
        }
        $info_td_attendance_str .= "</span>";
      }
    }

    $info_td_attendance_str .=  "</td>";
    $info_td_attendance_str .=  "</tr>";
    }
    }
    if($show_info_td_attendance_str){
      echo $info_td_attendance_str;
    }
  }
  // 个人排班显示
  $last_att_id = 0;
  $show_att_div = true;
  $show_ulist_flag = false;
  $user_att_arr = tep_sort_attendance($user_att_arr,$all_att_arr);
  foreach($user_att_arr as $uatt_arr){
    $attendance_user_row = $uatt_arr;
    $attendance_info = $all_att_arr[$attendance_user_row['attendance_detail_id']];
    $show_user_flag = false;
    $user_replace = tep_get_replace_by_uid_date($uatt_arr['user_id'],$date,$uatt_arr['attendance_detail_id']);
    foreach($all_replace_att as $row_replace_att){
      if($row_replace_att['user'] != $uatt_arr['user_id']){
        continue;
      }
      if(validate_two_time($attendance_info['work_start'],$attendance_info['work_end'],$row_replace_att['leave_start'],$row_replace_att['leave_end'])&&$attendance_info['set_time']==0){
        $show_user_flag = true;
        break;
      }
    }
    if($show_user_flag){
      continue;
    }
    if(tep_is_show_att($uatt_arr['id'],$date)&&!empty($uatt_arr)&&in_array($uatt_arr['user_id'],$show_select_group_user)&&
        (empty($user_replace)||$user_replace['allow_status']==0)){
    if($last_att_id==0||$last_att_id!=$uatt_arr['attendance_detail_id']){
      $last_att_id = $uatt_arr['attendance_detail_id'];
      $show_att_div = true;
    }else{
      $show_att_div = false;
    }
    if($last_att_id!=0&&$last_att_id!=$uatt_arr['attendance_detail_id']){
      echo "</td>";
      echo "</tr>";
    }
      if($show_att_div){
        $show_ulist_flag = true;
      echo "<tr>";
      if($attendance_info['scheduling_type'] == 0){
        echo '<td style="border-width:0px; padding-top:6px;">';
        echo "<div onclick='attendance_setting_user(\"".$date."\",\"".$j."\",\"".$attendance_user_row['user_id']."\",\"".$attendance_user_row['id']."\",\"".$attendance_user_row['attendance_detail_id']."\")' style='cursor:pointer;'>";
        echo $attendance_info['short_language'];
        if(file_exists("images/".$attendance_info['src_text'])&&$attendance_info['src_text']!=''){
          echo '<img style="width:16px;" src="images/'.$attendance_info['src_text'].'" alt="'.$attendance_info['title'].'">';
        }
      }else{
        echo "<td style='border-width:0px; padding-top:6px;".($attendance_info['scheduling_type'] == 1 && $attendance_info['src_text'] == '#000000' ? 'color:#FFFFFF;' : '')."' bgcolor='".$attendance_info['src_text']."'>";
        echo "<div onclick='attendance_setting_user(\"".$date."\",\"".$j."\",\"".$attendance_user_row['user_id']."\",\"".$attendance_user_row['id']."\",\"".$attendance_user_row['attendance_detail_id']."\")' style='cursor:pointer;'>";
        echo $attendance_info['short_language'];
      }
      echo "</div>";
      }

      $replace_str ='';
      $v_att=false;
      if($date<= $today){
        $time_flag = false;
        if($date == $today){
          $is_work = tep_check_show_login_logout($uatt_arr['user_id']);
          if($attendance_info['set_time']==0){
            $attendance_start = str_replace(':','',$attendance_info['work_start']);
            $attendance_end = str_replace(':','',$attendance_info['work_end']);
            if((($now_time> $attendance_start && $now_time < $attendance_end)||($attendance_start > $attendance_end&&!($now_time<$attendance_start&&$now_time>$attendance_end)))&&$is_work==1){
              $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
              $v_att = false;
            }else if($now_time>$attendance_end||$is_work==0){
              $v_att = tep_show_att_time($all_att_info[$uatt_arr['user_id']][$attendance_info['id']],$uatt_arr['user_id'],$date,$attendance_info['src_text'],$j,$show_att_status);
            }
          }else{
            if($is_work==1){
              $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
              $v_att = false;
            }else{
              $v_att = tep_show_att_time($all_att_info[$uatt_arr['user_id']][$attendance_info['id']],$uatt_arr['user_id'],$date,$attendance_info['src_text'],$j,$show_att_status);
            }
          }
        }else{
          $v_att = tep_show_att_time($all_att_info[$uatt_arr['user_id']][$attendance_info['id']],$uatt_arr['user_id'],$date,$attendance_info['src_text'],$j,$show_att_status);
        }
      }else{
        $v_att = false;
      }
      echo "<span>";
      /*
      if($user_replace['allow_status']==0&& (in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user'])){
        $replace_str .= "<img src='images/icons/mark.gif' alt='UNALLOW'>";
      }
      */

      echo "<a href='javascript:void(0)' ";
      $manager_list = tep_get_user_list_by_userid($uatt_arr['user_id']);
      if($ocertify->auth_user==$uatt_arr['user_id']||$ocertify->npermission>'10'){
        if($date>=$today||!empty($user_replace)){
          echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$uatt_arr['user_id']."\",\"".$attendance_user_row['attendance_detail_id']."\")' ";
        }
      }else{
        $v_att =false;
      }
      echo ($attendance_info['scheduling_type'] == 1 && $attendance_info['src_text'] == '#000000' ? ' style="color:#FFFFFF;"' : '').">";
      if($v_att!=false){
        echo $v_att;
      }else{
        echo $all_user_name_info[$uatt_arr['user_id']].$replace_str."&nbsp;";
      }
      echo "</a>";

      echo "</span>";
      if($show_att_div){
      }
    }
  }
if($show_ulist_flag){
      echo "</td>";
      echo "</tr>";
}



  //不在排班组的请假
    echo "<tr><td style='padding-top:6px; border-width:0px;'>";
    echo '<div>';
    $show_replace_array = array();
    foreach($all_replace_att as $row_replace_att){
      if(!in_array($row_replace_att['user'],$user_worker_list)&&in_array($row_replace_att['user'],$show_select_group_user)){
      $user_replace = tep_get_replace_by_uid_date($row_replace_att['user'],$date,0,$show_replace_array);
      $manager_list = tep_get_user_list_by_userid($row_replace_att['user']);
      if((!empty($user_replace))&&($ocertify->auth_user==$row_replace_att['user']||$ocertify->npermission>'10'||in_array($ocertify->auth_user,$manager_list))){
      $show_replace_array[] = $user_replace['id'];
      $show_flag = true;
      $u_info = tep_get_user_info($row_replace_att['user']);
      $attendance_date_info = tep_get_attendance_by_id($row_replace_att['replace_attendance_detail_id']);
      echo "<span>";
      echo "<a href='javascript:void(0)' ";
      echo " onclick='attendance_replace(\"".$date."\",\"".$j."\",\"".$row_replace_att['user']."\",\"".$row_replace_att['attendance_detail_id']."\")' ";
      echo " >";
      if($show_flag||in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user']){
      if(!empty($u_info)){
      if($row_replace_att['attendance_detail_id']!=''&&$row_replace_att['attendance_detail_id']!=0){
        $attendance_info = $all_att_arr[$row_replace_att['attendance_detail_id']];
      }else{
        $attendance_info = $all_att_arr[$row_replace_att['replace_attendance_detail_id']];
      }
      $replace_str = '';
      $v_att=false;
      if($date<= $today){
        $time_flag = false;
        if($date == $today){
          $is_work = tep_check_show_login_logout($row_replace_att['user']);
          if($attendance_info['set_time']==0){
            $attendance_start = str_replace(':','',$row_replace_att['leave_start']);
            $attendance_end = str_replace(':','',$row_replace_att['leave_end']);
            if((($now_time> $attendance_start && $now_time < $attendance_end)||($attendance_start > $attendance_end&&!($now_time<$attendance_start&&$now_time>$attendance_end)))&&$is_work==1){
              $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
              $v_att = false;
            }else if($now_time>$attendance_end||$is_work==0){
              $v_att = tep_show_att_time($all_att_info[$row_replace_att['user']][$attendance_info['id']],$row_replace_att['user'],$date,$attendance_info['src_text'],$j,$show_att_status);
            }
          }else{
            if($is_work==1){
              $replace_str .= "<img src='images/icons/working.jpg' alt='working'>";
              $v_att = false;
            }else{
              $v_att = tep_show_att_time($all_att_info[$row_replace_att['user']][$attendance_info['id']],$row_replace_att['user'],$date,$attendance_info['src_text'],$j,$show_att_status);
            }
          }
        }else{
          $v_att = tep_show_att_time($all_att_info[$row_replace_att['user']][$attendance_info['id']],$row_replace_att['user'],$date,$attendance_info['src_text'],$j,$show_att_status);
        }
      }else{
        $v_att = false;
      }
      if($v_att!=false){
        echo preg_replace('/<br>$/','',$v_att);
      }else{
        echo $u_info['name'].$replace_str;
      }
      if(!empty($attendance_date_info)){
        if($attendance_date_info['scheduling_type'] == 1){
          echo '<span class="rectangle" style="background-color:'.$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text'].';">&nbsp;</span>';
        }else{
          if(file_exists("images/".$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text'])&&$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text']!=''){
            echo "<img src='images/".$all_att_arr[$user_replace['replace_attendance_detail_id']]['src_text']."' alt='".$all_att_arr[$user_replace['replace_attendance_detail_id']]['alt_text']."' style='width: 16px;'>";
          }
        }
      }
      if($user_replace['allow_status']==0&& (in_array($ocertify->auth_user,explode('|||',$user_replace['allow_user']))||$ocertify->auth_user==$user_replace['user'])){
        echo "<img src='images/icons/mark.gif' alt='UNALLOW'>";
      }
      }
      }
      echo "</a>";
      echo "</span>";
      if($v_att!=false){
        echo "<br>";
      }
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
}
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
