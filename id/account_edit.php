<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_3RMTLIB.'address_info/AD_Option.php');
  require(DIR_FS_3RMTLIB.'address_info/AD_Option_Group.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EDIT);
  $hm_option = new AD_Option();
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
  //start
  // 全角的英数字改成半角
  $save_flag = false;
  $del_flag = false; 
  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    if (!isset($_POST[$col])) $_POST[$col] =NULL;
    $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
  }
  if(isset($_GET['act']) && $_GET['act'] !=''){

    $address_flag_id_num = $_GET['act'];
    $address_del_sql = "delete from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_flag_id_num ."' and customers_id='". $_SESSION['customer_id'] ."'";
    tep_db_query($address_del_sql);
    $del_flag = true;
  }
switch($_POST['action']){
case 'per':
  if (!isset($_POST['gender'])) $_POST['gender'] =NULL;
  $gender = tep_db_prepare_input($_POST['gender']);
  $firstname = tep_db_prepare_input($_POST['firstname']);
  $lastname = tep_db_prepare_input($_POST['lastname']);
  //add
  if (!isset($_POST['firstname_f'])) $_POST['firstname_f'] =NULL;
  $firstname_f = tep_db_prepare_input($_POST['firstname_f']);
  if (!isset($_POST['lastname_f'])) $_POST['lastname_f'] =NULL;
  $lastname_f = tep_db_prepare_input($_POST['lastname_f']);
  $dob = tep_db_prepare_input($_POST['dob']);
  $email_address = tep_db_prepare_input($_POST['email_address']);
  $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
  $old_email_address = tep_db_prepare_input($_POST['old_email']);
  $telephone = tep_db_prepare_input($_POST['telephone']);
  $fax = tep_db_prepare_input($_POST['fax']);
  //$newsletter = tep_db_prepare_input($_POST['newsletter']);
  //$password = tep_db_prepare_input($_POST['password']);
  //$confirmation = tep_db_prepare_input($_POST['confirmation']);
  if (!isset($_POST['street_address'])) $_POST['street_address'] =NULL;
  $street_address = tep_db_prepare_input($_POST['street_address']);
  if (!isset($_POST['company'])) $_POST['company'] =NULL;
  $company = tep_db_prepare_input($_POST['company']);
  if (!isset($_POST['suburb'])) $_POST['suburb'] =NULL;
  $suburb = tep_db_prepare_input($_POST['suburb']);
  $postcode = tep_db_prepare_input($_POST['postcode']);
  if (!isset($_POST['city'])) $_POST['city'] =NULL;
  $city = tep_db_prepare_input($_POST['city']);
  if (!isset($_POST['zone_id'])) $_POST['zone_id'] =NULL;
  $zone_id = tep_db_prepare_input($_POST['zone_id']);
  if (!isset($_POST['state'])) $_POST['state'] =NULL;
  $state = tep_db_prepare_input($_POST['state']);
  $country = tep_db_prepare_input($_POST['country']);

  $error = false; // reset error flag
  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }


  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  } 
//ccdd
  $check_email_query = tep_db_query("select count(*) as total from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id != '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
  $check_email = tep_db_fetch_array($check_email_query);
  if ($check_email['total'] > 0) {
    $error = true;
    $entry_email_address_exists = true;
  } else {
    $entry_email_address_exists = false;
  }

  if($error == false){
    $sql_data_array = array('new_customers_firstname' => $firstname,
                            'new_customers_lastname' => $lastname,
                            'new_email_address' => $email_address,
                            'send_mail_time' => time()
                            );
    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
   
    $edit_cus_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = ".tep_db_input($customer_id)." and site_id = '".SITE_ID."'");
    $edit_cus_res = tep_db_fetch_array($edit_cus_raw);
    if ($edit_cus_res) {
      if ($edit_cus_res['customers_email_address'] == $email_address) {
        $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_telephone' => $telephone,
                                'customers_email_address' => $old_email_address,
                                'send_mail_time' => time()
                                );

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");

        $sql_data_array = array('entry_street_address' => $street_address,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_postcode' => $postcode,
                                'entry_city' => $city,
                                'entry_country_id' => $country,
                                'entry_telephone' => $telephone);

        if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
        if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
        if (ACCOUNT_STATE == 'true') {
          if ($zone_id > 0) {
            $sql_data_array['entry_zone_id'] = $zone_id;
          $sql_data_array['entry_state'] = '';
        } else {
          $sql_data_array['entry_zone_id'] = '0';
          $sql_data_array['entry_state'] = $state;
        }
      }

      // ccdd
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customer_id) . "' and address_book_id = '" . tep_db_input($customer_default_address_id) . "'");
        $save_flag = true;
        //tep_redirect(tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
      }else{

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");

    $customer_country_id = $country;
    $customer_zone_id = $zone_id;
    
    $mail_name = tep_get_fullname($firstname, $lastname);  
    $ac_email_srandom = md5(time().$customer_id.$email_address); 
    
    tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".tep_db_input($customer_id)."'"); 
    
    $old_str_array = array('${MAIL_CONFIRM_URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
    $new_str_array = array(
        HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom,
        $mail_name, 
        STORE_NAME,
        HTTP_SERVER
        ); 
    //会员编辑邮件认证
    $edit_users_mail_array = tep_get_mail_templates('ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT',SITE_ID); 
    $email_text = str_replace($old_str_array, $new_str_array, $edit_users_mail_array['contents']);  
    $ed_email_text = str_replace('${SITE_NAME}', STORE_NAME, $edit_users_mail_array['title']); 
    $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name); 
    tep_mail($mail_name, $email_address, $ed_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    
    $acu_cud = $customer_id;
    tep_session_register('acu_cud');
    tep_redirect(tep_href_link('ac_mail_finish.php', '', 'SSL'));

      }
    }     
  }
  break;
  case 'address':
  //住所信息处理

if(isset($_POST['action_flag']) && $_POST['action_flag'] == 1){
  if (!isset($_POST['gender'])) $_POST['gender'] =NULL;
  $gender = tep_db_prepare_input($_POST['gender']);
  $firstname = tep_db_prepare_input($_POST['firstname']);
  $lastname = tep_db_prepare_input($_POST['lastname']);
  //add
  if (!isset($_POST['firstname_f'])) $_POST['firstname_f'] =NULL;
  $firstname_f = tep_db_prepare_input($_POST['firstname_f']);
  if (!isset($_POST['lastname_f'])) $_POST['lastname_f'] =NULL;
  $lastname_f = tep_db_prepare_input($_POST['lastname_f']);
  $dob = tep_db_prepare_input($_POST['dob']);
  $email_address = tep_db_prepare_input($_POST['email_address']);
  $email_address  = str_replace("\xe2\x80\x8b", '', $email_address);
  $old_email_address = tep_db_prepare_input($_POST['old_email']);
  $telephone = tep_db_prepare_input($_POST['telephone']);
  $fax = tep_db_prepare_input($_POST['fax']);
  $newsletter = tep_db_prepare_input($_POST['newsletter']);
  $password = tep_db_prepare_input($_POST['password']);
  $confirmation = tep_db_prepare_input($_POST['confirmation']);
  if (!isset($_POST['street_address'])) $_POST['street_address'] =NULL;
  $street_address = tep_db_prepare_input($_POST['street_address']);
  if (!isset($_POST['company'])) $_POST['company'] =NULL;
  $company = tep_db_prepare_input($_POST['company']);
  if (!isset($_POST['suburb'])) $_POST['suburb'] =NULL;
  $suburb = tep_db_prepare_input($_POST['suburb']);
  $postcode = tep_db_prepare_input($_POST['postcode']);
  if (!isset($_POST['city'])) $_POST['city'] =NULL;
  $city = tep_db_prepare_input($_POST['city']);
  if (!isset($_POST['zone_id'])) $_POST['zone_id'] =NULL;
  $zone_id = tep_db_prepare_input($_POST['zone_id']);
  if (!isset($_POST['state'])) $_POST['state'] =NULL;
  $state = tep_db_prepare_input($_POST['state']);
  $country = tep_db_prepare_input($_POST['country']);

  $error = false; // reset error flag
/*
  if (ACCOUNT_GENDER == 'true') {
    if ( ($gender == 'm') || ($gender == 'f') ) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
  }
 */

  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }
  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  }

  if(!(preg_match('/[a-zA-Z]/',$password) && preg_match('/[0-9]/',$password))){
      $error = true;
      $error_pwd = true;
      $entry_password_english_error = true;
  }else{
      $entry_password_english_error = false; 
  }
  if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
    $error = true;
    $error_pwd = true;
    $entry_password_error = true;
  } else {
    $entry_password_error = false;
  }
  if ($password !== $confirmation) {
    $error = true;
    $error_pwd = true;
    $entry_password_confirmation_error = true;
  }

  $check_pwd_query = tep_db_query("select customers_password from ". TABLE_CUSTOMERS ." where customers_id='". tep_db_input($customer_id) ."'");
  $check_pwd_array = tep_db_fetch_array($check_pwd_query);
  tep_db_free_result($check_pwd_query);

  if(tep_validate_password($password,$check_pwd_array['customers_password'])){
    $error = true;
    $error_pwd = true;
    $entry_password_old_error = true;
  }
  
//ccdd
  $check_email_query = tep_db_query("select count(*) as total from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id != '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
  $check_email = tep_db_fetch_array($check_email_query);
  if ($check_email['total'] > 0) {
    $error = true;
    $entry_email_address_exists = true;
  } else {
    $entry_email_address_exists = false;
  }

  $options_comment = array();
  $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
  while($address_required = tep_db_fetch_array($address_query)){
    
    $options_comment[$address_required['name_flag']] = $address_required['comment'];
  }
  tep_db_free_result($address_query);

if($_POST['num_rows'] > 0){
  //住所信息验证
  $option_info_array = array(); 
  if (!$hm_option->check()) {
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        if($options_comment[substr($p_key,3)] == $p_value){

          $p_value = '';
        }
        $option_info_array[$p_key] = tep_db_input($p_value); 
      } 
    }
  }else{
    $error = true;
  }
}
  if($error == true){ break; }  
  if($error == false){
    //住所信息入库
    if($_POST['num_rows'] > 0){
      $address_flag_id = tep_db_prepare_input($_POST['address_flag_id']);
      $add_list_array = array();
      $add_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0'");
      while($add_show_list_array = tep_db_fetch_array($add_show_list_query)){

        $add_list_array[$add_show_list_array['name_flag']] = $add_show_list_array['id'];
      }
      tep_db_free_result($add_show_list_query);
    if($address_flag_id == ''){
      
      $rand_num = date('Ymd-His',time()).floor(microtime()*1000);
      foreach($option_info_array as $address_key=>$address_value){
        $address_sql = "insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'{$rand_num}',{$_SESSION['customer_id']},{$add_list_array[substr($address_key,3)]},'". substr($address_key,3) ."','{$address_value}')";
        tep_db_query($address_sql);
      }
    }else{
      tep_db_query("delete from ". TABLE_ADDRESS_HISTORY ." where customers_id={$_SESSION['customer_id']} and orders_id='". $address_flag_id ."'");
      foreach($option_info_array as $address_key=>$address_value){
         $address_sql = "insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'{$address_flag_id}',{$_SESSION['customer_id']},{$add_list_array[substr($address_key,3)]},'". substr($address_key,3) ."','{$address_value}')";
        //$address_sql = "update ". TABLE_ADDRESS_HISTORY ." set value='". $address_value ."' where customers_id={$_SESSION['customer_id']} and orders_id='". $address_flag_id ."' and name='". substr($address_key,3) ."'";
        tep_db_query($address_sql);
      }
    }
  }
    $sql_data_array = array('new_customers_firstname' => $firstname,
                            'new_customers_lastname' => $lastname,
                            'new_customers_newsletter' => $newsletter,
                            'new_email_address' => $email_address,
                            'send_mail_time' => time(),
                            'new_customers_password' => tep_encrypt_password($password));
    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
   
    $edit_cus_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = ".tep_db_input($customer_id)." and site_id = '".SITE_ID."'");
    $edit_cus_res = tep_db_fetch_array($edit_cus_raw);
    if ($edit_cus_res) {
      if ($edit_cus_res['customers_email_address'] == $email_address) {
        $sql_data_array = array('customers_firstname' => $firstname,
                                'customers_lastname' => $lastname,
                                'customers_firstname_f' => $firstname_f,
                                'customers_lastname_f' => $lastname_f,
                                'customers_email_address' => $old_email_address,
                                'customers_telephone' => $telephone,
                                'customers_newsletter' => $newsletter,
                                'new_email_address' => $email_address,
                                'send_mail_time' => time(),
                                'customers_password' => tep_encrypt_password($password));

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
        if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

        // ccdd
        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");

        $sql_data_array = array('entry_street_address' => $street_address,
                                'entry_firstname' => $firstname,
                                'entry_lastname' => $lastname,
                                'entry_postcode' => $postcode,
                                'entry_city' => $city,
                                'entry_country_id' => $country,
                                'entry_telephone' => $telephone);

        if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
        if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
        if (ACCOUNT_STATE == 'true') {
          if ($zone_id > 0) {
            $sql_data_array['entry_zone_id'] = $zone_id;
          $sql_data_array['entry_state'] = '';
        } else {
          $sql_data_array['entry_zone_id'] = '0';
          $sql_data_array['entry_state'] = $state;
        }
      }

      // ccdd
        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customer_id) . "' and address_book_id = '" . tep_db_input($customer_default_address_id) . "'");
        $save_flag = true;
        //tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
      }else{
    
    tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");

    $customer_country_id = $country;
    $customer_zone_id = $zone_id;
    
    $mail_name = tep_get_fullname($firstname, $lastname);  
    $ac_email_srandom = md5(time().$customer_id.$email_address); 
    
    tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".tep_db_input($customer_id)."'"); 
    
    $old_str_array = array('${MAIL_CONFIRM_URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
    $new_str_array = array(
        HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom,
        $mail_name, 
        STORE_NAME,
        HTTP_SERVER
        ); 
    //会员编辑邮件认证
    $edit_users_mail_array = tep_get_mail_templates('ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT',SITE_ID); 
    $email_text = str_replace($old_str_array, $new_str_array, $edit_users_mail_array['contents']);  
    $ed_email_text = str_replace('${SITE_NAME}', STORE_NAME, $edit_users_mail_array['title']); 
    $email_text = tep_replace_mail_templates($email_text,$email_address,$mail_name);
    tep_mail($mail_name, $email_address, $ed_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    
    $acu_cud = $customer_id;
    tep_session_register('acu_cud');
    tep_redirect(tep_href_link('ac_mail_finish.php', '', 'SSL'));
      } 
   } 
 }  
}else{
  $options_comment = array();
  $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
  while($address_required = tep_db_fetch_array($address_query)){
    
    $options_comment[$address_required['name_flag']] = $address_required['comment'];
  }
  tep_db_free_result($address_query);


  $error_str = false;
  $option_info_array = array(); 
  if (!$hm_option->check()) {
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        if($options_comment[substr($p_key,3)] == $p_value){

          $p_value = '';
        }
        $option_info_array[$p_key] = tep_db_input($p_value); 
      } 
    }
  }else{
    $error_str = true;
  } 
 
  //住所信息入库
  if($error_str == false){
      $address_flag_id = tep_db_prepare_input($_POST['address_flag_id']);
      $add_list_array = array();
      $add_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0'");
      while($add_show_list_array = tep_db_fetch_array($add_show_list_query)){

        $add_list_array[$add_show_list_array['name_flag']] = $add_show_list_array['id'];
      }
      tep_db_free_result($add_show_list_query); 
    if($address_flag_id == ''){
      
      $rand_num = date('Ymd-His',time()).floor(microtime()*1000);
      foreach($option_info_array as $address_key=>$address_value){
        $address_sql = "insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'{$rand_num}',{$_SESSION['customer_id']},{$add_list_array[substr($address_key,3)]},'". substr($address_key,3) ."','{$address_value}')";
        tep_db_query($address_sql);
      }
      $save_flag = true; 
    }else{
      tep_db_query("delete from ". TABLE_ADDRESS_HISTORY ." where customers_id={$_SESSION['customer_id']} and orders_id='". $address_flag_id ."'");
      foreach($option_info_array as $address_key=>$address_value){

         $address_sql = "insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'{$address_flag_id}',{$_SESSION['customer_id']},{$add_list_array[substr($address_key,3)]},'". substr($address_key,3) ."','{$address_value}')";
        //$address_sql = "update ". TABLE_ADDRESS_HISTORY ." set value='". $address_value ."' where customers_id={$_SESSION['customer_id']} and orders_id='". $address_flag_id ."' and name='". substr($address_key,3) ."'";
        tep_db_query($address_sql);
      }
    }
    $save_flag = true;
  }
}
  break;
  case 'options':
    $newsletter = tep_db_prepare_input($_POST['newsletter']);
    tep_db_query("update ". TABLE_CUSTOMERS ." set new_customers_newsletter='". $newsletter ."' where customers_id='". $_SESSION['customer_id'] ."'");
    tep_db_query("update ". TABLE_CUSTOMERS ." set customers_newsletter='". $newsletter ."' where customers_id='". $_SESSION['customer_id'] ."'");
    tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");
    $save_flag = true;
  break;
  case 'pwd': 
    $error_pwd = false;
    $password = tep_db_prepare_input($_POST['password']);
    $confirmation = tep_db_prepare_input($_POST['confirmation']); 
    if(!(preg_match('/[a-zA-Z]/',$password) && preg_match('/[0-9]/',$password))){
      $error_pwd = true;
      $entry_password_english_error = true;
    }else{
      $entry_password_english_error = false; 
    }
    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
      $error_pwd = true;
      $entry_password_error = true;
    } else {
      $entry_password_error = false;
    }

    if ($password !== $confirmation) {
      $error_pwd = true;
      $entry_password_confirmation_error = true;
    } else {
      $entry_password_confirmation_error = false;
    }

   $check_pwd_query = tep_db_query("select customers_password from ". TABLE_CUSTOMERS ." where customers_id='". tep_db_input($customer_id) ."'");
   $check_pwd_array = tep_db_fetch_array($check_pwd_query);
   tep_db_free_result($check_pwd_query);

   if(tep_validate_password($password,$check_pwd_array['customers_password'])){
     $error_pwd = true;
     $entry_password_old_error = true;
   }else{
      $entry_password_old_error = false;
   }
    if($error_pwd == false){

      tep_db_query("update ". TABLE_CUSTOMERS ." set new_customers_password='".tep_encrypt_password($password) ."',customers_password='". tep_encrypt_password($password) ."' where customers_id='". $_SESSION['customer_id'] ."'");  
      $save_flag = true;
    }
  break;
}

  //end

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
</head>
<body>
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="table"> 
    <tr> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <h1 class="pageHeading">
          <span class="game_t">
            <?php echo HEADING_TITLE ; ?>
          </span>
        </h1> 
        
        <div class="comment">
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="table"> 
            <tr> 
              <td> <?php
//ccdd
  $account_query = tep_db_query("select c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_firstname_f, c.customers_lastname_f, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_zone_id, a.entry_state, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter from " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " a where c.customers_id = '" . $customer_id . "' and a.customers_id = c.customers_id and a.address_book_id = '" .  $customer_default_address_id . "' and c.site_id = '".SITE_ID."'");
  $account = tep_db_fetch_array($account_query);
  $email_address = isset($_POST['email_address']) && isset($_POST['action']) && ($_POST['action'] == 'per' ||($_POST['action'] == 'address' && $_POST['action_flag'] == 1 ))? $_POST['email_address'] : $account['customers_email_address'];

  require(DIR_WS_MODULES . 'account_details_info.php');
?> 
<input type="hidden" name="old_email_1" value="<?php echo $account['customers_email_address'];?>">
</td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td>
              <table border="0" width="100%" cellspacing="0" cellpadding="2" align="center" summary="table"> 
                  <tr> 
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td class="main" align="right"><a href="javascript:void(0);" onClick="check_form();"><img src="images/design/button/all_save.gif"></a></td> 
                  </tr> 
                </table>
                </td> 
            </tr> 
          </table> 
        </div>
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> 
      </td> 
    </tr>
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<script type="text/javascript">
$(document).ready(function(){ 
<?php
  if($save_flag == true){
    echo 'alert("'.NOTICE_SAVE_ACCOUNT_SUCCESS.'");location.href="'.  FILENAME_ACCOUNT_EDIT .'";'; 
  }
  if($del_flag == true){
    echo 'alert("'.NOTICE_DELETE_ACCOUNT.'");location.href="'. FILENAME_ACCOUNT_EDIT .'";'; 
  }
?>
});
</script>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
