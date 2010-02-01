<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if($HTTP_GET_VARS['goods_id']) {
    $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$HTTP_GET_VARS['goods_id']."' and site_id = '".SITE_ID."'") ;
	$present = tep_db_fetch_array($present_query) ;
  }else{
    tep_redirect(tep_href_link(FILENAME_PRESENT, 'error_message='.urlencode(TEXT_PRESENT_ERROR_NOT_SELECTED), 'SSL'));	
  }

  //ログイン済みの場合は確認画面へリダイレクト
  if(tep_session_is_registered('customer_id')) {
    $pc_id = $customer_id;
    tep_session_register('pc_id');
	tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.(int)$HTTP_GET_VARS['goods_id'], 'SSL'));
  }
  
  //セッション内に「pc_id」が入っていた場合は確認画面へリダイレクト
  if(tep_session_is_registered('pc_id')) {
	tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.(int)$HTTP_GET_VARS['goods_id'], 'SSL'));
  }
 
 
  
if (!isset($HTTP_GET_VARS['action'])) $HTTP_GET_VARS['action'] = NULL;
  switch($HTTP_GET_VARS['action']) {
    //既会員ログイン
	case 'login':
      $HTTP_POST_VARS['email_address'] = tep_an_zen_to_han($HTTP_POST_VARS['email_address']);

      $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
      $password = tep_db_prepare_input($HTTP_POST_VARS['password']);
	  $goods_id = $HTTP_GET_VARS['goods_id'];
      
	  //check
	  $login_error = false;
	  $check_customer_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_password, customers_email_address, customers_default_address_id from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and site_id = '".SITE_ID."'");
      if (tep_db_num_rows($check_customer_query)) {
        $check_customer = tep_db_fetch_array($check_customer_query);
        // Check that password is good
        if (tep_validate_password($password, $check_customer['customers_password'])) {
          $pc_id = $check_customer['customers_id'];
		  tep_session_register('pc_id');
		  
		  $customers_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$pc_id."' and site_id = '".SITE_ID."'");
		  $customers_result = tep_db_fetch_array($customers_query);
		  
		  $address_query = tep_db_query("select * from ".TABLE_ADDRESS_BOOK." where customers_id = '".$pc_id."' and address_book_id = '1'");
		  $address_result = tep_db_fetch_array($address_query);
			
		  $firstname = $customers_result['customers_firstname'];
		  $lastname = $customers_result['customers_lastname'];
		  $email_address = $customers_result['customers_email_address'];
		  $telephone = $customers_result['customers_telephone'];
		  $street_address = $address_result['entry_street_address'];
		  $suburb = $address_result['entry_suburb'];
		  $postcode = $address_result['entry_postcode'];
		  $city = $address_result['entry_city'];
		  $zone_id = $address_result['entry_zone_id'];

	      //セッション内に情報を一時的に挿入
		  tep_session_register('firstname');
		  tep_session_register('lastname');
		  tep_session_register('email_address');
		  tep_session_register('telephone');
		  tep_session_register('street_address');
		  tep_session_register('suburb');
		  tep_session_register('postcode');
		  tep_session_register('city');
		  tep_session_register('zone_id');
		  
	    } else {
		  $login_error = true;
		  $HTTP_GET_VARS['login'] = 'fail';
		}
	  } else {
	    $login_error = true;
		$HTTP_GET_VARS['login'] = 'fail';
	  }
	  
	  if($login_error == false) tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
	  
	  break;
  
    //ゲストまたは新規会員
	case 'process':
	  $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);
	  $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
	  $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
	  $dob = tep_db_prepare_input($HTTP_POST_VARS['dob']);
	  $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
	  $telephone = tep_db_prepare_input($HTTP_POST_VARS['telephone']);
	  $fax = tep_db_prepare_input($HTTP_POST_VARS['fax']);
	  $newsletter = tep_db_prepare_input($HTTP_POST_VARS['newsletter']);
	  $password = tep_db_prepare_input($HTTP_POST_VARS['password']);
	  $confirmation = tep_db_prepare_input($HTTP_POST_VARS['confirmation']);
	  $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);
	  $company = tep_db_prepare_input($HTTP_POST_VARS['company']);
	  $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);
	  $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);
	  $city = tep_db_prepare_input($HTTP_POST_VARS['city']);
	  $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);
	  $state = tep_db_prepare_input($HTTP_POST_VARS['state']);
	  $country = tep_db_prepare_input($HTTP_POST_VARS['country']);
	  
	  $goods_id = $HTTP_GET_VARS['goods_id'];
	  
	  // start check
	  $error = false;
	  //-------------------------------------------------------
	  
	  //gender
	  if (ACCOUNT_GENDER == 'true') {
		if (($gender == 'm') || ($gender == 'f')) {
		  $entry_gender_error = false;
		} else {
		  $error = true;
		  $entry_gender_error = true;
		}
	  }

	  //first_name
	  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;
		$entry_firstname_error = true;
	  } else {
		$entry_firstname_error = false;
	  }
	
	  //last_name
	  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;
		$entry_lastname_error = true;
	  } else {
		$entry_lastname_error = false;
	  }
	  
	  //email-1
	  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
		$entry_email_address_error = true;
	  } else {
		$entry_email_address_error = false;
	  }
	
	  //email-2
	  if (!tep_validate_email($email_address)) {
		$error = true;
		$entry_email_address_check_error = true;
	  } else {
		$entry_email_address_check_error = false;
	  }
	
	  //street_address
	  if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;
		$entry_street_address_error = true;
	  } else {
		$entry_street_address_error = false;
	  }
	
	  //postcode
	  if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;
		$entry_post_code_error = true;
	  } else {
		$entry_post_code_error = false;
	  }
	
	  //city
	  if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;
		$entry_city_error = true;
	  } else {
		$entry_city_error = false;
	  }
	
	  //state
	  if (ACCOUNT_STATE == 'true') {
		if ($entry_country_error == true) {
		  $entry_state_error = true;
		} else {
		  $zone_id = 0;
		  $entry_state_error = false;
		  $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "'");
		  $check_value = tep_db_fetch_array($check_query);
		  $entry_state_has_zones = ($check_value['total'] > 0);
		  if ($entry_state_has_zones == true) {
			$zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "' and zone_name = '" . tep_db_input($state) . "'");
			if (tep_db_num_rows($zone_query) == 1) {
			  $zone_values = tep_db_fetch_array($zone_query);
			  $zone_id = $zone_values['zone_id'];
			} else {
			  $zone_query = tep_db_query("select zone_id from " . TABLE_ZONES . " where zone_country_id = '" . tep_db_input($country) . "' and zone_code = '" . tep_db_input($state) . "'");
			  if (tep_db_num_rows($zone_query) == 1) {
				$zone_values = tep_db_fetch_array($zone_query);
				$zone_id = $zone_values['zone_id'];
			  } else {
				$error = true;
				$entry_state_error = true;
			  }
			}
		  } else {
			if ($state == false) {
			  $error = true;
			  $entry_state_error = true;
			}
		  }
		}
	  }
	
	  //telephone
	  if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;
		$entry_telephone_error = true;
	  } else {
		$entry_telephone_error = false;
	  }
	  
	  //password check
	  if(!empty($password)) {
	    //password( lengh )
		$passlen = strlen($password);
	    if ($passlen < ENTRY_PASSWORD_MIN_LENGTH) {
		  $error = true;
		  $entry_password_error = true;
	    } else {
		  $entry_password_error = false;
	    }
		
		//password confirmation check
	    if ($password != $confirmation) {
		  $error = true;
		  $entry_password_error = true;
	    }
	  }
	
	  //check_email_count for regist user
	  if(!empty($password)) {
	    $check_email = tep_db_query("select customers_email_address from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id <> '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
	    if (tep_db_num_rows($check_email)) {
		  $error = true;
		  $entry_email_address_exists = true;
	    } else {
		  $entry_email_address_exists = false;
	    }
	  }
	  //-----------------------------------
	  // end check
	  if($error == false) {
	    //会員登録希望（パスワードが入力されていた場合）
	    if(!empty($password)) {
	      //会員登録処理
		  $sql_data_array = array('customers_firstname' => $firstname,
								  'customers_lastname' => $lastname,
								  'customers_email_address' => $email_address,
								  'customers_telephone' => $telephone,
								  'customers_fax' => $fax,
								  //'customers_newsletter' => $newsletter,
								  'customers_newsletter' => 1,
								  'customers_password' => tep_encrypt_password($password),
								  'customers_default_address_id' => 1);
	
		  if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
		  if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);
	
		  tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);
	
		  $customer_id = tep_db_insert_id();
	
	      // 2003-06-06 add_telephone
		  $sql_data_array = array('customers_id' => $customer_id,
								  'address_book_id' => 1,
								  'entry_firstname' => $firstname,
								  'entry_lastname' => $lastname,
								  'entry_street_address' => $street_address,
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
	
		  tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
	
		  tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . tep_db_input($customer_id) . "', '0', now())");
	
		  if (SESSION_RECREATE == 'True') { // 2004/04/25 Add session management
		    tep_session_recreate();
		  }
		
		  $pc_id = $customer_id;
		  tep_session_register('pc_id');
		  
	      //セッション内に情報を一時的に挿入
		  tep_session_register('firstname');
		  tep_session_register('lastname');
		  tep_session_register('email_address');
		  tep_session_register('telephone');
		  tep_session_register('street_address');
		  tep_session_register('suburb');
		  tep_session_register('postcode');
		  tep_session_register('city');
		  tep_session_register('zone_id');
		  
		  tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
	    } 
	    //ゲスト（該当する回の応募のみ）
	    else {
	      $pc_id = 0;
		  tep_session_register('pc_id');
		  
		  //セッション内に情報を一時的に挿入
		  tep_session_register('firstname');
		  tep_session_register('lastname');
		  tep_session_register('email_address');
		  tep_session_register('telephone');
		  tep_session_register('street_address');
		  tep_session_register('suburb');
		  tep_session_register('postcode');
		  tep_session_register('city');
		  tep_session_register('zone_id');
	    }
		tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$goods_id, 'SSL'));
	  }
	  
	  break;
  }
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_ORDER);

  $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_PRESENT));
  $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_PRESENT,'good_id='.$HTTP_GET_VARS['goods_id']));
  $breadcrumb->add(NAVBAR_TITLE3, tep_href_link(FILENAME_PRESENT_ORDER));

?>
<?php page_head();?>
<?php require('includes/present_form_check.js.php'); ?>
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <h1 class="pageHeading"> 
      <?php if (!isset($HTTP_GET_VARS['news_id'])) $HTTP_GET_VARS['news_id']=NULL;?>
          <?php if ($HTTP_GET_VARS['news_id']) { echo $latest_news['headline']; } else { echo HEADING_TITLE; } ?> 
        </h1> 
        
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td class="contents"> <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                        </tr>
                      </table></td>
                    <td width="33%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td align="center" width="33%" class="checkoutBarCurrent">応募者情報</td>
                    <td align="center" width="33%" class="checkoutBarFrom">確認画面</td>
                    <td align="center" width="33%" class="checkoutBarFrom">応募完了</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php
      if (!isset($HTTP_POST_VARS['goods_id'])) $HTTP_POST_VARS['goods_id']=NULL;
  if($HTTP_POST_VARS['goods_id']) {
    $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$HTTP_GET_VARS['goods_id']."'") ;
	$present = tep_db_fetch_array($present_query) ;
  }	
?>
                <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0" summary="table">
                  <tr>
                    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents" summary="table">
                        <tr <?php echo $_class?"class='".$_class."'":'' ; ?>>
                          <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>">
<script type="text/javascript" language="javascript"><!--
	document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link('present_popup_image.php', 'pID=' . (int)$HTTP_GET_VARS['goods_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . '<\'+\'/a>'; ?>');
--></script>
<noscript>
<?php echo tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right"'); ?>
</noscript>
                            <?php //echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],NONSSL).'">' . tep_image(DIR_WS_IMAGES.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT) . '</a>'; ?>
                          </td>
                          <td class="main"><b><?php echo $present['title'] ; ?></b> &nbsp;&nbsp; 応募期間:<?php echo tep_date_long($present['start_date']) .'〜'. tep_date_long($present['limit_date']); ?> </td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <?php
  if (isset($HTTP_GET_VARS['login']) && ($HTTP_GET_VARS['login'] == 'fail')) {
    $info_message = TEXT_LOGIN_ERROR;
  } elseif ($cart->count_contents()) {
    $info_message = TEXT_VISITORS_CART;
  }

  if (isset($info_message)) {
?>
            <tr>
              <td class="smallText"><?php echo $info_message; ?></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <?php
  }
?>
            <tr>
              <td class="main"><?php echo tep_draw_form('login', tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$HTTP_GET_VARS['goods_id'].'&action=login', 'SSL')); ?>
                <table width="100%"  border="0" cellspacing="0" cellpadding="2" summary="table">
                  <tr>
                    <td width="25%">&nbsp;</td>
                    <td class="main"><b><?php echo HEADING_RETURNING_CUSTOMER; ?></b>
                      <table border="0" width="100%" cellspacing="0" cellpadding="1" class="infoBox">
                        <tr>
                          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents">
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="main" colspan="2"><?php echo TEXT_RETURNING_CUSTOMER; ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                                <td class="main"><?php echo tep_draw_input_field('email_address'); ?></td>
                              </tr>
                              <tr>
                                <td class="main"><b><?php echo ENTRY_PASSWORD; ?></b></td>
                                <td class="main"><?php echo tep_draw_password_field('password'); ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                              <tr>
                                <td class="smallText" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . TEXT_PASSWORD_FORGOTTEN . '</a>'; ?></td>
                              </tr>
                              <tr>
                                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                              </tr>
                            </table></td>
                        </tr>
                      </table></td>
                    <td width="25%">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php echo tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN); ?></td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </form></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><?php echo TEXT1 ; ?> </td>
            </tr>
            <tr>
              <td class="main"><?php
		  
  if (isset($HTTP_GET_VARS['email_address'])) $email_address = tep_db_prepare_input($HTTP_GET_VARS['email_address']);
  $account['entry_country_id'] = STORE_COUNTRY;
    echo tep_draw_form('present_account', tep_href_link(FILENAME_PRESENT_ORDER, 'goods_id='.$HTTP_GET_VARS['goods_id'].'&action=process', 'SSL'), 'post', 'onSubmit="return check_form();"'); 
    require(DIR_WS_MODULES . 'present_account_details.php');
    echo '<div align="right">'. tep_draw_hidden_field('goods_id', $present['goods_id']) . tep_image_submit('button_continue.gif', '') .'</div>' . "\n";
    echo '</form>';

?></td>
            </tr>
          </table>
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
