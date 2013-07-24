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
  $check_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address ='".$_POST['post_email']."' and site_id ='".$_POST['post_site']."'");
  $check_num = tep_db_num_rows($check_query);
  tep_db_free_result($check_query);
  if(!tep_validate_email($_POST['post_email'])){
     $check_email_error = '1';
  }else{
     $check_email_error = '0';
  }
  echo  $check_email_error.','.$check_num;
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
}else if(isset($_GET['action']) && $_GET['action'] == 'check_mag_up'){
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'data_management.php');
    /* $dat[0] => ID $dat[1] => 邮件地址 $dat[2] => 姓名 */
    // CSV文件检查
    $chk_csv = true;
    $filename = isset($_POST['products_csv'])?$_POST['products_csv']:'';
    if(substr($filename, strrpos($filename,".")+1)!="csv") $chk_csv = false;
    // 文件名参考检查
    if(!$chk_csv){
    echo 'error';
    }
}
?>
