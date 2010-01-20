<?php
/*
  $Id: privacy.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
// if the customer is not logged on, redirect them to the present page
  if (!tep_session_is_registered('pc_id')) {
   $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$HTTP_GET_VARS['goods_id']));
  }


  if($HTTP_GET_VARS['goods_id']) {
    $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$HTTP_GET_VARS['goods_id']."'") ;
	$present = tep_db_fetch_array($present_query) ;
  }else{
    tep_redirect(tep_href_link(FILENAME_PRESENT, 'error_message='.urlencode(TEXT_PRESENT_ERROR_NOT_SELECTED), 'SSL'));	
  }
  
  //process
if (!isset($HTTP_GET_VARS['action'])) $HTTP_GET_VARS['action']=NULL;
  switch($HTTP_GET_VARS['action']) {
    case 'process'://申し込みプロセス
	  //現在の日時
	  $now = date("Y/m/d H:i:s", time());
	  
	  //insert present_aplicant
	  $sql_data_array = array('goods_id' => tep_db_prepare_input($HTTP_GET_VARS['goods_id']),
	  						  'customer_id' => tep_db_prepare_input($pc_id),
							  'family_name' => tep_db_prepare_input($lastname),
							  'first_name' => tep_db_prepare_input($firstname),
							  'mail' => tep_db_prepare_input($email_address),
							  'postcode' => tep_db_prepare_input($postcode),
							  'prefectures' => tep_db_prepare_input(tep_get_zone_name('107',$state, $zone)),
							  'cities' => tep_db_prepare_input($city),
							  'address1' => tep_db_prepare_input($street_address),
							  'address2' => tep_db_prepare_input($suburb),
							  'phone' => tep_db_prepare_input($telephone),
							  'tourokubi' => tep_db_prepare_input($now));
	  
      tep_db_perform(TABLE_PRESENT_APPLICANT, $sql_data_array);
	  
	  //check pre insert - customers
	  if($pc_id != '0') {
	    $cmcnt_query = tep_db_query("select count(*) as cnt from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_prepare_input($email_address)."'");
	    $cmcnt_result = tep_db_fetch_array($cmcnt_query);
		
		$cmcnt = $cmcnt_result['cnt'];
		
		//update mail_mag
		if($cmcnt != 0) {
		  tep_db_query("update ".TABLE_CUSTOMERS." set customers_newsletter = '1' where customers_email_address = '".tep_db_prepare_input($email_address)."'");
		}
	  } else {
	    $cmcnt = 0;
	  }
	  
	  //check pre insert - main_magazine
	  $mgcnt_query = tep_db_query("select count(*) as cnt from ".TABLE_MAIL_MAGAZINE." where mag_email = '".tep_db_prepare_input($email_address)."'");
	  $mgcnt_result = tep_db_fetch_array($mgcnt_query);
	  
	  //insert mail_magazine ** customers=0 & mail_magazine=0
	  if($cmcnt == 0 && $mgcnt_result['cnt'] == 0) {
	    $sql_data_array2 = array('mag_email' => tep_db_prepare_input($email_address),
	  						     'mag_name' => tep_get_fullname($firstname, $lastname));
		
		tep_db_perform(TABLE_MAIL_MAGAZINE, $sql_data_array2);
	  }
	  
	  
	  tep_redirect(tep_href_link(FILENAME_PRESENT_SUCCESS,'goods_id='.$HTTP_GET_VARS['goods_id']));
	  break;
	
	case 'update'://申込者情報変更
	  $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);
	  $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);
	  $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
	  $telephone = tep_db_prepare_input($HTTP_POST_VARS['telephone']);
	  $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);
	  $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);
	  $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);
	  $city = tep_db_prepare_input($HTTP_POST_VARS['city']);
	  $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);

	  $error = false;
	  
	  //first_name
	  if (empty($firstname)) {
		$error = true;
	  }
	
	  //last_name
	  if (empty($lastname)) {
		$error = true;
	  }
	  
	  //email-1
	  if (empty($email_address)) {
		$entry_email_address_error = false;
	  }
	
	  //email-2
	  if (!tep_validate_email($email_address)) {
		$error = true;
	  }
	
	  //street_address
	  if (empty($street_address)) {
		$error = true;
	  }
	
	  //postcode
	  if (empty($postcode)) {
		$error = true;
	  }
	
	  //city
	  if (empty($city)) {
		$error = true;
	  }
	
	  //telephone
	  if (empty($telephone)) {
		$error = true;
	  }
	  
	  if($error == false) {
	    //セッションを一時的に開放
	    tep_session_unregister('firstname');
	    tep_session_unregister('lastname');
	    tep_session_unregister('email_address');
	    tep_session_unregister('telephone');
	    tep_session_unregister('street_address');
	    tep_session_unregister('suburb');
	    tep_session_unregister('postcode');
	    tep_session_unregister('city');
	    tep_session_unregister('zone_id');
	  
	    //セッション更新
	    tep_session_register('firstname');
	    tep_session_register('lastname');
	    tep_session_register('email_address');
	    tep_session_register('telephone');
	    tep_session_register('street_address');
	    tep_session_register('suburb');
	    tep_session_register('postcode');
	    tep_session_register('city');
	    tep_session_register('zone_id');
		
		tep_redirect(tep_href_link(FILENAME_PRESENT_CONFIRMATION,'goods_id='.$HTTP_GET_VARS['goods_id']));
	  }
	  break;  
        default:
          if (!tep_session_is_registered('firstname'))
          {
          $account_query = tep_db_query("select c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_firstname_f, c.customers_lastname_f, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_zone_id, a.entry_state, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " a where c.customers_id = '" . $customer_id . "' and a.customers_id = c.customers_id and a.address_book_id = '" . $customer_default_address_id . "'");
          $account = tep_db_fetch_array($account_query);
          $firstname = $account['customers_firstname'];
          $lastname = $account['customers_lastname'];
          $email_address = $account['customers_email_address'];
          $postcode = $account['entry_postcode'];
          $zone_id = $account['entry_zone_id'];
          $city = $account['entry_city'];
          $street_address = $account['entry_street_address'];
          $telephone = $account['customers_telephone'];
          $suburb = $account['entry_suburb'];
          
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
  }
  
    

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_ORDER);

  $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_PRESENT));
  $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_PRESENT,'good_id='.$HTTP_GET_VARS['goods_id']));
  $breadcrumb->add(NAVBAR_TITLE3, tep_href_link(FILENAME_PRESENT_ORDER));

?>
<?php page_head();?>
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
            <td> <div class="contents"> 
              <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                <tr> 
                  <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="33%" align="center"><table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="11"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="center" width="33%" class="checkoutBarFrom">応募者情報</td> 
                  <td align="center" width="33%" class="checkoutBarCurrent">確認画面</td> 
                  <td align="center" width="33%" class="checkoutBarFrom">応募完了</td> 
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          <tr> 
            <td class="main"><?php
			
			$name = tep_get_fullname($firstname, $lastname);
			$email = $email_address;
			$postcode = $postcode;
			$state = $zone_id;
			$address1 = $city . $street_address;
			$address2 = $suburb;
			$tel = $telephone;
			
		    function tep_get_zone_list2($name, $selected = '', $country_code = '107') {
			  $zones_query = tep_db_query("select zone_name, zone_id from ".TABLE_ZONES." where zone_country_id = '107' order by zone_code");
			  $string = '<select name="'.$name.'">';
			  while ($zones_values = tep_db_fetch_array($zones_query)) {
			    $string .= '<option value="'.$zones_values['zone_id'].'"';
				if($zones_values['zone_id'] == $selected) $string .= ' selected';
				$string .= '>'.$zones_values['zone_name'].'</option>';
			  }
			  $string .= '</select>';
			  return $string;
		    }		  
		  ?> 
              <table width="100%" cellpadding="1" cellspacing="0" class="infoBox" border="0"> 
                <tr> 
                  <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxContents"> 
                      <tr class="<?php echo $_class ; ?>"> 
                        <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>"><?php echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],'NONSSL').'">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT) . '</a>'; ?></td> 
                        <td class="main"><b><?php echo $present['title'] ; ?></b><br> 
                          応募期間:<?php echo tep_date_long($present['start_date']) .'〜'. tep_date_long($present['limit_date']); ?> </td> 
                      </tr> 
                    </table></td> 
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          <?php 
		if(!$HTTP_GET_VARS['action'] || $HTTP_GET_VARS['action'] != 'update') {
		echo tep_draw_form('process', tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$HTTP_GET_VARS['goods_id'].'&action=process', 'SSL')); ?> 
          <tr> 
            <td class="main"><table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"><b>応募者情報の確認</b> 
                    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="1" class="infoBox"> 
                      <tr> 
                        <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                            <tr> 
                              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">お名前</td> 
                              <td class="main"><?php echo $name; ?></td> 
                            </tr> 
                            <tr> 
                              <td>メールアドレス</td> 
                              <td><?php echo $email; ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">郵便番号</td> 
                              <td class="main"><?php echo $postcode; ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">都道府県</td> 
                              <?php if(!isset($zone)) $zone=NULL;?>
                              <td class="main"><?php echo tep_get_zone_name('107',$state, $zone); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">住所１</td> 
                              <td class="main"><?php echo $address1; ?></td> 
                            </tr> 
                            <?php if(!empty($address2)) { ?> 
                            <tr> 
                              <td class="main">住所2</td> 
                              <td class="main"><?php echo $address2; ?></td> 
                            </tr> 
                            <?php } ?> 
                            <tr> 
                              <td class="main">電話番号</td> 
                              <td class="main"><?php echo $tel; ?></td> 
                            </tr> 
                            <tr> 
                              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                            </tr> 
                          </table>
                          </td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="right"><br> 
                    <?php echo tep_image_submit('button_present.gif', IMAGE_BUTTON_PRESENT); ?></td> 
                </tr> 
              </table></td> 
          </tr> 
          </form> 
           <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          <?php 
		}
		echo tep_draw_form('process', tep_href_link(FILENAME_PRESENT_CONFIRMATION, 'goods_id='.$HTTP_GET_VARS['goods_id'].'&action=update', 'SSL')); ?> 
          <tr> 
            <td class="main"><table width="100%"  border="0" cellspacing="0" cellpadding="2"> 
                <tr> 
                  <td class="main"><b>応募者情報の編集</b><br> 
                    登録情報の変更を行う場合は下記フォームより変更を行ってください
                    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="1" class="infoBox"> 
                      <tr> 
                        <td><table border="0" width="100%" height="100%" cellspacing="0" cellpadding="2" class="infoBoxContents"> 
                            <tr> 
                              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">姓</td> 
                              <td class="main"><?php echo tep_draw_input_field('lastname', $lastname); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">名</td> 
                              <td class="main"><?php echo tep_draw_input_field('firstname', $firstname); ?></td> 
                            </tr> 
                            <tr> 
                              <td>メールアドレス</td> 
                              <td><?php echo tep_draw_input_field('email_address', $email_address); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">郵便番号</td> 
                              <td class="main"><?php echo tep_draw_input_field('postcode', $postcode); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">都道府県</td> 
                              <td class="main"><?php echo tep_get_zone_list2('zone_id', $zone_id); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">市区町村</td> 
                              <td class="main"><?php echo tep_draw_input_field('city', $city); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">住所1</td> 
                              <td class="main"><?php echo tep_draw_input_field('street_address', $street_address); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">住所2</td> 
                              <td class="main"><?php echo tep_draw_input_field('suburb', $suburb); ?></td> 
                            </tr> 
                            <tr> 
                              <td class="main">電話番号</td> 
                              <td class="main"><?php echo tep_draw_input_field('telephone', $telephone); ?></td> 
                            </tr> 
                            <tr> 
                              <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                            </tr> 
                          </table></td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="right"><br> 
                    <?php echo tep_image_submit('button_update.gif', IMAGE_BUTTON_UPDATE); ?></td> 
                </tr> 
              </table></td> 
          </tr> 
          </form> 
           </table></div></td> 
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
