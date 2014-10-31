<?php
if(isset($_GET['action']) && $_GET['action'] == 'check_file_exists'){
  /* -----------------------------------------------------
    功能: 返回指定表中，指定字段，指定数据(图片)的总数 
    参数: $_POST['table'] 表名 
    参数: $_POST['field'] 字段 
    参数: $_POST['dir'] 指定数据(图片)前缀 
    参数: $_POST['file'] 指定数据(图片)
 -----------------------------------------------------*/
  $table = $_POST['table'];
  $field = $_POST['field'];
  $dir = $_POST['dir'];
  $file = $_POST['file'];
  $check_query = tep_db_query("select ".$field." from ".$table." where ".$field."='".$dir.$file."'");
  $check_num = tep_db_num_rows($check_query);
  tep_db_free_result($check_query);
  echo $check_num;
}else if(isset($_GET['action']) && $_GET['action'] == 'check_email'){
  require('includes/step-by-step/new_application_top.php');
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_CUSTOMERS);
  $check_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address ='".$_POST['post_email']."' and site_id ='".$_POST['post_site']."'");
  $check_num = tep_db_num_rows($check_query);
  tep_db_free_result($check_query);
  $hicuizd = trim($_POST['post_email']);
  $hicuizd = preg_match('/\\\/',$hicuizd);
  if(!tep_validate_email($_POST['post_email'])){
     $check_email_error = '1';
  }else if($hicuizd){
     $check_email_error = '1'; 
  }else{
     $check_email_error = '0';
  }
  if((!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $_POST['password'])) && $_POST['password'] != ''){
      $error_password = '1'; 
      if (preg_match('/^[0-9]+$/', $password)) {
             $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM;
      } else if (preg_match('/^[a-zA-Z0-9]+$/', $password)) {
             $entry_password_error_msg = ENTRY_PASSWORD_IS_ALPHA;
      }else{
            $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM_ALPHA;
      }
  }else if((!preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{0,}$/', $_POST['once_again_password']))&& $_POST['once_again_password'] != ''){
      $error_password = '1'; 
      if (preg_match('/^[0-9]+$/', $confirmation)) {
            $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM;
      } else if (preg_match('/^[a-zA-Z0-9]+$/', $confirmation)) {
            $entry_password_error_msg = ENTRY_PASSWORD_IS_ALPHA;
      }else{
            $entry_password_error_msg = ENTRY_PASSWORD_IS_NUM_ALPHA;
      }
  }else{
      $error_password = '0'; 
  }
  echo $check_email_error.','.$check_num.','.$error_password.','.$entry_password_error_msg;
}else if(isset($_GET['action']) && $_GET['action'] == 'check_email_exists'){
  /* -----------------------------------------------------
    功能: 检测指定网站下的电子邮箱是否存在 
    参数: $_POST['email'] 电子邮箱 
    参数: $_POST['site_id'] 网站ID 
  -----------------------------------------------------*/
  require('includes/step-by-step/new_application_top.php');
  $email = $_POST['email'];
  $site_id = $_POST['site_id'];
  $customers_id = tep_get_customer_id_by_email($email,$site_id);   

  if(!tep_validate_email($email)){
   
    echo '1';
  }else if(!$customers_id){

    echo '0';
  }else{
    echo $customers_id; 
  }
}else if(isset($_GET['action']) && $_GET['action'] == 'check_romaji'){
  $check_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji='".$_POST['post_romaji']."'");
  $check_num = tep_db_num_rows($check_query);
  echo $check_num;
}else if(isset($_GET['action']) && $_GET['action'] == 'products_list'){
  /* -----------------------------------------------------
    功能: 生成相应分类下的商品列表 
    参数: $_POST['id'] 分类ID 
  -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_STATS_SALES_REPORT);
  $add_product_categories_id = $_POST['id'];
  $products_pid = $_POST['products_id'];
  $products_array = array();
  //获取指定分类下的商品列表
  $result = tep_db_query("
              SELECT products_name, 
              ptc.products_id 
              FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON ptc.products_id=pd.products_id 
              WHERE pd.language_id = '" . (int)$languages_id . "' 
              and ptc.categories_id = '".$add_product_categories_id."'
              and pd.site_id = '0'");
  while($row = tep_db_fetch_array($result)){

    $products_array[$row['products_id']] = $row['products_name'];
  }
  tep_db_free_result($result);
  
  echo '<select name="products_id" id="products_id_list" onclick="save_products_id(this.value);" style="margin-left:0;">';
  $products_list_str = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "</option>\n";
  asort($products_array);
  foreach($products_array as $products_id => $products_name){
    $products_list_str .= "<option value='".$products_id."'".($products_id == $products_pid ? ' selected' : '').">".$products_name."</option>\n";
  }
  echo $products_list_str;
  echo "</select>";
}else if(isset($_GET['action']) && $_GET['action'] == 'check_customers'){
  /* -----------------------------------------------------
    功能: 判断选中的顾客中是否有订单或预约存在 
    参数: $_POST['customers_id_list'] 选中的顾客ID列表
    参数: $_POST['customers_site_id_list'] 选中的顾客所属网站ID列表
 -----------------------------------------------------*/
  $customers_id_list = $_POST['customers_id_list']; 
  $customers_id_list_all = $_POST['customers_id_list_all'];
  $customers_site_id_all_list = $_POST['customers_site_id_list'];
  $customers_id_array = array();
  $customers_id_all_array = array();
  $customers_site_id_all_array = array();
  $customers_id_array = explode(',',$customers_id_list);
  $customers_id_all_array = explode(',',$customers_id_list_all);
  $customers_site_id_all_array = explode(',',$customers_site_id_all_list);
  $customers_id_array = array_filter($customers_id_array);
  $customers_id_all_array = array_filter($customers_id_all_array);
  $customers_site_id_all_array = array_filter($customers_site_id_all_array);

  $customers_site_id_array = array();
  foreach($customers_id_array as $value){

    $customers_key = array_search($value,$customers_id_all_array);
    $customers_site_id_array[] = $customers_site_id_all_array[$customers_key];
  }

  $customers_name_array = array(); 
  foreach($customers_id_array as $key=>$value){

    if(tep_get_preorders_by_customers_id($value,$customers_site_id_array[$key]) > 0){

      $customers_name_array[$value] = tep_customers_name($value);
    }
    if(tep_get_orders_by_customers_id($value,$customers_site_id_array[$key]) > 0){
     
      $customers_name_array[$value] = tep_customers_name($value);
    }
  } 

  if(!empty($customers_name_array)){
    echo implode("\n",$customers_name_array);
  }else{
    echo ''; 
  }
}else if(isset($_GET['action']) && $_GET['action'] == 'check_once_pwd_log'){
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_CUSTOMERS);
  $user_info = tep_get_user_info($ocertify->auth_user);
  if($_POST['type'] == 2){
   $sql = "insert into ".TABLE_ONCE_PWD_LOG." VALUES  (NULL , '".$user_info['name']."','','".sprintf(TEXT_ONE_PASSWORD,$_POST['input_pwd_str'])."', CURRENT_TIMESTAMP)"; 
   tep_db_query($sql);
  }
  if($_POST['c_permission']&&$_POST['type'] == 0){
  $sql = "insert into ".TABLE_ONCE_PWD_LOG." VALUES  (NULL , '".$user_info['name']."','','".TEXT_DOWNLOAD_INFO."', CURRENT_TIMESTAMP)";
  tep_db_query($sql);
  }else if($_POST['c_permission']&&$_POST['type'] == 1){
  $sql = "insert into ".TABLE_ONCE_PWD_LOG." VALUES  (NULL , '".$user_info['name']."','','".TEXT_DOWNLOAD."', CURRENT_TIMESTAMP)"; 
  tep_db_query($sql);
  }

}else if(isset($_GET['action']) && $_GET['action'] == 'ajax_categrories'){
  require_once(DIR_WS_CLASSES . 'category_tree.php');
  $osC_CategoryTree = new osC_CategoryTree(true,false,$_POST['cpath']); 
  echo $osC_CategoryTree->buildTree();
}else if(isset($_GET['action']) && $_GET['action'] == 'check_messages_header'){
  //内容链接跳转处理
  $latest_messages_query_raw = 'select * from messages where recipient_id = "'.$_POST['sender_id'].'" and trash_status="0" order by time desc';
  $messages_id_count = tep_db_num_rows(tep_db_query($latest_messages_query_raw)); 
  $messages_id_page = ceil($messages_id_count/MAX_DISPLAY_SEARCH_RESULTS);

  $messages_id_list = array();
  for($i = 1;$i <= $messages_id_page;$i++){
       
    $latest_messages_query_raw = 'select * from messages where recipient_id = "'.$_POST['sender_id'].'" and trash_status="0" order by time desc';
    $latest_messages_split = new splitPageResults($i, MAX_DISPLAY_SEARCH_RESULTS, $latest_messages_query_raw,$latest_messages_query_numrows);
    $messages_id_query = tep_db_query($latest_messages_query_raw);
    while($messages_id_array = tep_db_fetch_array($messages_id_query)){

      $messages_id_list[$i][] = $messages_id_array['id'];
    }
    tep_db_free_result($messages_id_query);
  }
	$messages_header = tep_db_query(
        	'select * from messages where recipient_id = "'.$_POST['sender_id'].'" and header_status = "0" and trash_status="0" order by time desc'
        );
	$messages_header_all = array();
        while($new_messages = tep_db_fetch_array($messages_header)){
          for($j = 1;$j <= $latest_messages_query_numrows;$j++){

            if(in_array($new_messages['id'],$messages_id_list[$j])){

              $new_messages['page'] = $j;
              break;
            }
          }
                $new_messages['time'] = str_replace("-","/",substr($new_messages['time'],0,19));
                $new_messages['timestamp'] = strtotime($new_messages['time']);
                //针对返信内容处理
                $contents_text = $new_messages['content'];
                $contents_text = preg_replace('/\-\-\-\-\-\-\-\-\-\- Forwarded message \-\-\-\-\-\-\-\-\-\-[\s\S]*\>.*+/','',$contents_text);
                if(trim($contents_text) != ''){
                  $contents_text = explode("\r\n",$contents_text);
                  $contents_text = $contents_text[0];
                  if(trim($contents_text) == ''){

                    $contents_text = '...';
                  }
                }else{
                  $contents_text = '...'; 
                }
		$new_messages['content'] = str_replace('>','&gt',str_replace('<','&lt',(mb_strlen($contents_text) > 30 ? mb_substr($contents_text, 0, 30).'...' : $contents_text)));
		if($new_messages['mark'] != '' && $new_messages['mark'] != null){
			$new_messages['mark'] = explode(',',$new_messages['mark']);
			$n = 0;
			foreach($new_messages['mark'] as $value){
				if(strlen($value)==1){
					$new_messages['mark'][$n] = '0'.$value;
				}
				$n++;
			}
		}
		$new_messages['type']="messages";
		$messages_header_all[] = $new_messages;
	}
	//bulletin_board 消息
        $user_info =tep_get_user_info($ocertify->auth_user); 
        //获取用户的创建时间
        $create_user_time = $user_info['date_added'];
	$notice_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='0' and n.user = '".$user_info['name']."' and n.created_at>'".$create_user_time."'"; 

	$notice_micro_sql = "select n.type type,n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted,bb.allow to_users,bb.manager from_users,bb.mark icon,bb.id bb_id from ".TABLE_NOTICE." n,".TABLE_BULLETIN_BOARD." bb where n.from_notice=bb.id and n.type = '1' and n.is_show='1' and n.created_at>'".$create_user_time."' union select n.type type,n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted,bb.allow to_users,bb.manager from_users,br.mark icon,br.bulletin_id bb_id from ".TABLE_NOTICE." n,".TABLE_BULLETIN_BOARD." bb,".TABLE_BULLETIN_BOARD_REPLY." br where n.from_notice=br.id and n.type = '2' and n.is_show='1' and bb.id=br.bulletin_id and n.created_at>'".$create_user_time."'"; 

	$notice_micro_query = tep_db_query($notice_micro_sql);
	$notice_id_array = array();
	$memo_id_array = array();
	$memo_cid_array = array();
	 while($notice_micro_array = tep_db_fetch_array($notice_micro_query)){

		if($notice_micro_array['to_users'] == 'all'){

		$notice_id_array[] = $notice_micro_array['id'];
		$memo_id_array[$notice_micro_array['id']] = $notice_micro_array['icon'];
		$memo_cid_array[$notice_micro_array['id']] = $notice_micro_array['bb_id'];
		$memo_type_array[$notice_micro_array['id']] = $notice_micro_array['type'];
		}else{

			$users_id_array = array();
			$users_id_array_tmp = explode(':',$notice_micro_array['to_users']);
			if($users_id_array_tmp[0]=='id'){
			$users_id_array=explode(',',$users_id_array_tmp[1]);
			}else{
				foreach(explode(',',$users_id_array_tmp[1]) as $group){
				$raw=tep_db_query("select * from ".TABLE_GROUPS." where name='$group'");
			    while($row=tep_db_fetch_array($raw)){
					$users_id_array=array_merge($users_id_array,explode("|||",$row['all_users_id']));
				}
				}
			}
			array_push($users_id_array,$notice_micro_array['from_users']);

			if(in_array($ocertify->auth_user,$users_id_array)){

			$notice_id_array[] = $notice_micro_array['id'];
			$memo_id_array[$notice_micro_array['id']] = $notice_micro_array['icon'];
			$memo_cid_array[$notice_micro_array['id']] = $notice_micro_array['bb_id'];
			$memo_type_array[$notice_micro_array['id']] = $notice_micro_array['type'];
		}
	 }
	}
	tep_db_free_result($notice_micro_query);
	 $notice_id_str = implode(',',$notice_id_array);

	 if($notice_id_str != ''){
	 $notice_micro_sqls = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n where n.id in (".$notice_id_str.")";
	}
  //警告提示
	$alarm_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='1' and n.created_at>'".$create_user_time."'";
  
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
	 while ($notice_list = tep_db_fetch_array($notice_list_raw)) {
		 $new_header=array();
		  if($notice_list['deleted'] != ''){
          
			$notice_users_array = array();
			$notice_users_array = explode(',',$notice_list['deleted']);
        
			if(in_array($ocertify->auth_user,$notice_users_array)){
			continue;
			}
		}
      $check_notice_query = tep_db_query("select a.alarm_flag from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.id='".$_POST['aid']."'");
      $check_notice_array = tep_db_fetch_array($check_notice_query);
      tep_db_free_result($check_notice_query);
      if ($notice_list['type'] == '0') {
        $alarm_flag_query = tep_db_query("select alarm_flag,alarm_show,orders_flag from ".TABLE_ALARM." where alarm_id='".$notice_list['from_notice']."'");
        $alarm_flag_array = tep_db_fetch_array($alarm_flag_query);
        tep_db_free_result($alarm_flag_query);
      }
      if ($notice_list['type'] == '0') { 
        if($alarm_flag_array['orders_flag'] == '1'){
		  $new_header['mark']='';
          $title_str = HEADER_TEXT_ALERT_TITLE;
        }else{
          $title_str = HEADER_TEXT_ALERT_TITLE_PREORDERS; 
        }
        if($alarm_flag_array['alarm_flag'] == '0'){
          $new_header['title']='&nbsp;'.NOTICE_ALARM_TITLE; 
        }else{
          $new_header['title']='&nbsp;'.$title_str; 
        }
        $new_header['title']='<img src="images/icons/order.png">'; 
      } else {
        $new_header['title']='<img src="images/icons/bbs.png" >'; 
      }
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
      $new_header['time']=substr(str_replace("-","/",$notice_list['created_at']),0,19);
      $new_header['timestamp'] = strtotime($notice_list['created_at']);

      if(in_array($notice_list['id'],$notice_id_array)){
		  $new_header['mark']=$memo_id_array[$notice_list['id']];
			if($new_header['mark'] != '' && $new_header['mark'] != null){
				$new_header['mark'] = explode(',',$new_header['mark']);
				$n = 0;
				foreach($new_header['mark'] as $value){
					if(strlen($value)==1){
						$new_header['mark'][$n] = '0'.$value;
					}
					$n++;
				}
			}
	  }else{
      }
      if ($notice_list['type'] == '0') {
        $alarm_raw = tep_db_query("select orders_id from ".TABLE_ALARM." where alarm_id = '".$notice_list['from_notice']."'"); 
        $alarm = tep_db_fetch_array($alarm_raw); 
        if($alarm_flag_array['alarm_flag'] == '0'){
          $new_header['content']='<a onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" style="color:#0000FF;" href="'.tep_href_link(FILENAME_ORDERS, 'oID='.$alarm['orders_id'].'&action=edit').'">'.(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']).'</a>'; 
        }else{
          if($alarm_flag_array['orders_flag'] == '1'){

            $filename_str = FILENAME_ORDERS;
          }else{
            $filename_str = FILENAME_PREORDERS; 
          }
           $new_header['content']='<a onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" style="color:#0000FF;"  href="'.tep_href_link($filename_str, 'oID='.$alarm['orders_id'].'&action=edit').'">'.$alarm['orders_id'].'</a>'; 
        }
      } else {
		$type_html="";
		if($memo_type_array[$notice_list['id']]==2){
			$type_html='type=show_reply&';
			$count_row=tep_db_num_rows(tep_db_query("select id from ".TABLE_BULLETIN_BOARD_REPLY." where id>=".$notice_list['from_notice']));
			$page=ceil($count_row/MAX_DISPLAY_SEARCH_RESULTS);
			$type_html.='page='.$page.'&c_id='.$notice_list['from_notice'].'&';
		}else{
			$count_row=tep_db_num_rows(tep_db_query("select id from ".TABLE_BULLETIN_BOARD." where id>=".$notice_list['from_notice']));
			$page=ceil($count_row/MAX_DISPLAY_SEARCH_RESULTS);
			$type_html.='page='.$page.'&';
                }
                $notice_list['title'] = str_replace(array('>','<'),array('&gt;','&lt;'),$notice_list['title']);
         $new_header['content']='<a onmousemove="mouse_on(this)" onmouseout="mouse_leave(this)" style="color:#0000FF;"  href="'.tep_href_link(FILENAME_BULLETIN_BOARD,$type_html.'bulletin_id='.$memo_cid_array[$notice_list['id']]).'">'.(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']).'</a>'; 
      }
      if ($notice_list['type'] == '0') {
      if($alarm_flag_array['alarm_flag'] == '1'){
         $new_header['content'].='<div style="float:left;" id="alarm_user_'.$notice_list['from_notice'].'">';
        $new_header['content'].=$notice_list['user'].'&nbsp;'.HEADER_TEXT_ALERT_LINK;
        $new_header['content'].= '</div>';
        $new_header['content'].= '<div style="float:left;">';
        if($alarm_flag_array['alarm_show'] == '1'){
          $new_header['content'].= '&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">ON</span>';
        }else{
          $new_header['content'].= '&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($notice_list['title'],'utf-8') > 30 ? mb_substr($notice_list['title'],0,30,'utf-8').'...' : $notice_list['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$notice_list['from_notice'].'">OFF</span>'; 
        }
        $new_header['content'].= '</div>';
      }else{
        $new_header['content'].= '<div style="float:left;">';
        $new_header['content'].= NOTICE_DIFF_TIME_TEXT.'&nbsp;'; 
        $new_header['content'].= '<span>'.$leave_date.'</span>';
        $new_header['content'].= '</div>';
      }
      }
      if ($notice_list['type'] == '0') {
		if(tep_db_num_rows(tep_db_query("select * from alarm where alarm_id=".$notice_list['from_notice']." and alarm_flag=1"))>=1){
			$new_header['type']='button';
			$new_header['title']='<img src="images/icons/alarm.png" >';
		}
		else $new_header['type']='order';
        $new_header['delete']='&nbsp;<a href="javascript:void(0);" onclick="delete_alarm_notice(\''.$notice_list['id'].'\', \'0\');"><img src="images/icons/bbs_del_one.png" alt="close" onmousemove="this.src=\'images/icons/white_bbs_del_one.png\'" onmouseout="this.src=\'images/icons/bbs_del_one.png\'"></a>'; 
      } else {
		$new_header['type']='bulletin';
        $new_header['delete']= '&nbsp;<a href="javascript:void(0);" onclick="delete_micro_notice(\''.$notice_list['id'].'\', \'0\');"><img src="images/icons/bbs_del_one.png" alt="close"  onmousemove="this.src=\'images/icons/white_bbs_del_one.png\'" onmouseout="this.src=\'images/icons/bbs_del_one.png\'"></a>'; 
      }
      $new_header['hidden']= '<input name="del_alarm_notice_id[]" value="'.$notice_list['id'].'" type="hidden">';
		$messages_header_all[] = $new_header;
    }
  }
	//bulletin_board 消息结束
	if(empty($messages_header_all)){
        	$messages_header_all = '0';
		echo $messages_header_all;
	}else{
		//按照时间排序
		$header_num=count($messages_header_all);
		for($i=0;$i<$header_num;$i++){
			$min_time=strtotime($messages_header_all[$i]['time']);
			for($j=$i;$j<$header_num;$j++){
				if(strtotime($messages_header_all[$j]['time'])>$min_time){
					$min_time=strtotime($messages_header_all[$j]['time']);
					$tmp=$messages_header_all[$j];
					$messages_header_all[$j]=$messages_header_all[$i];
					$messages_header_all[$i]=$tmp;
				}
			}
		}
		for($i=0;$i<$header_num;$i++){
			$messages_header_all[$i]['time']=substr($messages_header_all[$i]['time'],0,16);
		}
		echo json_encode($messages_header_all);
	//die(var_dump($messages_header_all));
	}
}else if(isset($_GET['action']) && $_GET['action'] == 'delete_messages_header'){
	if($_POST['id'] != '' && $_POST['id'] != null){
		$is_delete = tep_db_query('update messages set header_status = "1" where id = '.$_POST['id']);
		if($is_delete){
			echo '1';
		}
	}
}else if(isset($_GET['action']) && $_GET['action'] == 'delete_messages_header_all'){
	$messages_back_status = 1;
	if($_POST['id_all'] != '' && $_POST['id_all'] != null){
		$id_array = explode(';',$_POST['id_all']);
		foreach($id_array as $value){
			$is_delete = tep_db_query('update messages set header_status = "1" where id = '.$value);
			if(!$is_delete){
				$messages_back_status = 0;	
			}
		}
		if($messages_back_status == 1){
			echo '1';
		}
	}
}else if(isset($_GET['action']) && $_GET['action'] == 'change_users_groups'){
  $users_id = $_POST['users_id'];
  $date_now = $_POST['date'];
  //所选用户指定时间的排班
$user_all_att = tep_all_attenande_by_uid($users_id,$date_now);
if(count($user_all_att)<=1){
$disabled = 'disabled="disabled"';
}else{
$disabled = '';
}
$all_att_detail .= '<select '.$disabled.' name="attendance_detail_id" onchange="get_detail_att_time(this.value)">';
foreach($user_all_att as $key=>$val){
 $all_att_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." where id=".$val['attendance_detail_id'];
 $all_att_auery = tep_db_query($all_att_sql);
  while($all_att_row = tep_db_fetch_array($all_att_auery)){
    $all_att_detail .= '<option value="'.$all_att_row['id'].'">'.$all_att_row['title'].'</option>';
    if($all_att_row['set_time']==0 && $all_att_row['work_start']!=':'&& $all_att_row['work_end']!=':'){
          $work_time = '<span>'.$all_att_row['work_start'].'--'.$all_att_row['work_end'].'</span>';
	}else{
          $work = $all_att_row['work_hours']+$all_att_row['rest_hours'];	
          $work_time = '<span>'.$work .TELECOM_UNKNOW_TABLE_TIME. '</span>';
	}
  }
}
$all_att_detail .= '</select>|||'.$work_time;

  $users_array = tep_get_user_list_by_userid($users_id);

  //获取admin及ROOT
  $permissions_query = tep_db_query("select userid from ".TABLE_PERMISSIONS." where permission>=15 order by permission");
  while($permissions_array = tep_db_fetch_array($permissions_query)){

    $users_array[] = $permissions_array['userid'];
  }
  tep_db_free_result($permissions_query);

  $users_array = array_unique($users_array); 
  $allow_user_select .= '<select name="allow_user[]">';
  foreach($users_array as $users_value){

    $users_info = tep_get_user_info($users_value);
    $allow_user_select .= '<option value="'.$users_value.'">'.$users_info['name'].'</option>';
  }
  $allow_user_select .= '</select>&nbsp;&nbsp;<font color="red" id="allow_user_error"></font>';
  echo $allow_user_select.'|||'.$all_att_detail;
}else if(isset($_GET['action']) && $_GET['action'] == 'open_leftmenu'){

  if ($_COOKIE['tarrow'] == 'open') {
    require(DIR_WS_BOXES . 'configuration.php');
    require(DIR_WS_BOXES . 'catalog.php');
    require(DIR_WS_BOXES . 'modules.php');
    require(DIR_WS_BOXES . 'customers.php');
    require(DIR_WS_BOXES . 'localization.php');
    require(DIR_WS_BOXES . 'reports.php');
    require(DIR_WS_BOXES . 'tools.php');
    require(DIR_WS_BOXES . 'users.php');
  }
}else if(isset($_GET['action']) && $_GET['action'] == 'update_collect_info'){

  //把采集的商品价格同步到后台
  $cid = $_POST['cid'];
  $pid = $_POST['pid'];
  $category_query = "select * from (select c.categories_id,c.parent_id,cd.site_id,cd.categories_name from ".CATEGORIES_TABLE." c,".CATEGORIES_DESCRIPTION_TABLE." cd where c.categories_id =cd.categories_id and c.categories_id ='".$pid."' and cd.language_id = '4' ) c where site_id = '0' or site_id ='0'group by categories_id limit 1";
  $category_name_query= tep_db_query($category_query);
  $category_name_array = tep_db_fetch_array($category_name_query);
  $category_name = $category_name_array['categories_name'];
  $category_type_query = tep_db_query("select * from (select c.categories_id,c.parent_id,cd.site_id,cd.categories_name from ".CATEGORIES_TABLE." c, ".CATEGORIES_DESCRIPTION_TABLE." cd where c.categories_id =cd.categories_id and c.categories_id ='".$cid."' and cd.language_id = '4' ) c where site_id = '0' or site_id ='0'group by categories_id limit 1");
  $category_type_array = tep_db_fetch_array($category_type_query);
  $category_type = (strpos($category_type_array['categories_name'],'販売'))?1:0;
  mysql_select_db(DBNAME);
  $game_str_array = array('FF14'=>'FF14',
      'RO'=>'ラグナロク',
      'RS'=>'レッドストーン',
      'FF11'=>'FF11',
      'DQ10'=>'DQ10',
      'L2'=>'リネージュ2',
      'ARAD'=>'アラド戦記',
      'nobunaga'=>'信長の野望',
      'PSO2'=>'PSO2',
      'L1'=>'リネージュ',
      'TERA'=> 'TERA',
      'AION'=> 'AION',
      'CABAL'=> 'CABAL',
      'WZ'=> 'ウィザードリィ',
      'latale'=> 'ラテール',
      'blade'=> 'ブレイドアンドソウル',
      'megaten'=> '女神転生IMAGINE',
      'EWD'=> 'エルソード',
      'LH'=> 'ルーセントハート',
      'HR'=> 'マビノギ英雄伝',
      'AA'=> 'ArcheAge',
      'ThreeSeven'=> '777タウン',
      'ECO'=> 'エミルクロニクル',
      'FNO'=> 'FNO',
      'SUN'=> 'SUN',
      'talesweave'=> 'テイルズウィーバー',
      'MU'=> 'MU',
      'C9'=> 'C9',
      'MS'=> 'メイプルストーリー',
      'cronous'=> 'クロノス',
      'tenjouhi'=> '天上碑',
      'rose'=> 'ローズオンライン',
      'hzr'=> '晴空物語',
      'dekaron'=> 'デカロン',
      'fez'=> 'ファンタジーアースゼロ',
      'lakatonia'=> 'ラカトニア',
      'moe'=> 'ラカトニア',
      'mabinogi'=> 'マビノギ',
      'WF'=> '戦場のエルタ',
      'rohan'=> 'ROHAN',
      'genshin'=> '幻想神域',
      'lineage'=> 'リネージュ'
      );
  // define('CATEGORY_TABLE','category');
 $game_array = array_flip($game_str_array);
  foreach($game_array as $k=>$v){
    if(strpos($category_name,$k)!==false){
      $name = $v;
      break;
    }
  }
 $products_id_query  = tep_db_query("select pp.product_name,pp.product_id from ".PRODUCTS_PRICE_TABLE." pp left join ".PRODUCT_TABLE." ps on pp.product_id = ps.product_id where category_name = '".$name."' and `product_type` =".$category_type);
   while($products_id = tep_db_fetch_array($products_id_query)){
     $products_id_array[] = $products_id ; 
   }
  foreach($products_id_array as $key=>$val){
       $product_price_array = array();
      if(in_array($val['product_id'],array(0,-1))){
        $product_price_query = tep_db_query("select p.product_price from ".PRODUCT_TABLE." p left join ".CATEGORY_TABLE." c on p.category_id =c.category_id where c.category_name = '".$name."' and p.product_name ='".$val['product_name']."' and c.category_type = ".$category_type);
        while($product_price = tep_db_fetch_array($product_price_query)){
             $product_price_array[] = $product_price ;
         }
         sort($product_price_array);
         $price = array();
         foreach($product_price_array as $i){
           if(!in_array($i['product_price'],$price)){
              $price[] = $i['product_price'];
           }
         }
         $products_price_array[$key]['product_name'] = $val['product_name'];
         if($val['product_id'] == -1){
            $products_price_array[$key]['product_price'] = $price[0];
         }else if ($val['product_id'] == 0){
            $products_price_array[$key]['product_price'] = $price[1]; 
         }

      }else{
      $products_price_query = tep_db_query("select product_name,product_price from ".PRODUCT_TABLE." where product_id = ".$val['product_id']);
      while($products_price = tep_db_fetch_array($products_price_query)){
         $products_price_array[$key] = $products_price ;
      }
      }
  
  }
  mysql_select_db(DB_DATABASE);
  foreach($products_price_array as $k=>$v){
   $sql = "select products_id from ".PRODUCTS_DESCRIPTION_TABLE." where site_id = 0 and language_id = 4 and preorder_status=".$category_type." and products_name like '%".$v['product_name']."%'";
   $products_id_query = tep_db_query($sql); 
   $products_id_array = tep_db_fetch_array($products_id_query);
   $products_id = $products_id_array['products_id'];
   if($products_id){
      $res = tep_db_query("update ".PRODUCTS_TABLE." set collect_price = ".$v['product_price']." where products_id=".$products_id);
   }else {
      continue;
   }
 }
  echo json_encode($products_price_array);
 // print_r($category_name_array);
  /* $cid = $_POST['cid'];
  $category_name_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES." where categories_id='".$cid."' and site_id=0");
  $category_name_array = tep_db_fetch_array($category_name_query);
  tep_db_free_result($category_name_query);

  echo $category_name_array['categories_name'];*/
  /**
   *@date20141030
   *更改排班获取排班对应的时间带
   * */
}else if(isset($_GET['action']) && $_GET['action'] == 'get_detail_att_time'){
   $att_detaile_id = $_POST['att_id'];
   $att_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." where id=".$att_detaile_id;
   $att_auery = tep_db_query($att_sql);
  $att_row = tep_db_fetch_array($att_auery);
    if($att_row['set_time']==0 && $att_row['work_start']!=':'&& $att_row['work_end']!=':'){
          $work_time = '<span>'.$att_row['work_start'].'--'.$att_row['work_end'].'</span>';
	}else{
          $work = $att_row['work_hours']+$att_row['rest_hours'];	
          $work_time = '<span>'.$work .TELECOM_UNKNOW_TABLE_TIME. '</span>';
   }
   echo $work_time;


}
 
?>
