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
} else if ($_GET['action'] == 'read_flag') {
  /*------------------------------------------
 功能: 读取标志 
 参数: $_POST['user'] 用户
 参数: $_POST['flag'] 标志
 参数: $_POST['id'] memo编号
 -----------------------------------------*/
  $users_name = $_POST['user'];
  $read_flag = $_POST['flag'];
  $memo_id = $_POST['id'];
  $read_flag_query = tep_db_query("select read_flag from ". TABLE_BUSINESS_MEMO ." where id='".$memo_id."'");
  $read_flag_array = tep_db_fetch_array($read_flag_query);
  tep_db_free_result($read_flag_query);
  if($read_flag_array['read_flag'] == ''){

    if($read_flag == 0){
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$users_name."' where id='".$memo_id."'"); 
    }
  }else{

    $read_flag_str_array = explode(',',$read_flag_array['read_flag']);
    if(!in_array($users_name,$read_flag_str_array) && $read_flag == 0){
      $read_flag_add = $read_flag_array['read_flag'].','.$users_name;
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$read_flag_add."' where id='".$memo_id."'");
    }else{

      unset($read_flag_str_array[array_search($users_name,$read_flag_str_array)]);
      $read_flag_string = implode(',',$read_flag_str_array);
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$read_flag_string."' where id='".$id."'");
    }
  }
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
	$messages_header = tep_db_query(
        	'select * from messages where recipient_id = "'.$_POST['sender_id'].'" and header_status = "0" order by time desc'
        );
	$messages_header_all = array();
	while($new_messages = tep_db_fetch_array($messages_header)){
		$new_messages['time'] = date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_MESSAGE_HOUR_STR.'i'.TEXT_MESSAGE_MIN_STR, strtotime($new_messages['time']));
		$new_messages['content'] = str_replace('>','&gt',str_replace('<','&lt',mb_substr($new_messages['content'], 0, 20)));
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
		$messages_header_all[] = $new_messages;
	}
	if(empty($messages_header_all)){
        	$messages_header_all = '0';
		echo $messages_header_all;
	}else{
		echo json_encode($messages_header_all);
	}
	//die(var_dump($messages_header_all));
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
}
 
?>
