<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
  if (IsSet($_GET['cmail'])) {
    $cmail_arr = explode('|||', $_GET['cmail']);
    $_GET['Customer_mail'] = $cmail_arr[0]; 
    $_GET['site_id'] = $cmail_arr[1];
  }
  
  if (IsSet($_GET['Customer'])) {
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer'] . "'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer'] . "'");
    $address = tep_db_fetch_array($address_query);
  } elseif (IsSet($_GET['Customer_nr'])) {
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer_nr'] . "'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer_nr'] . "'");
    $address = tep_db_fetch_array($address_query);
  } elseif (IsSet($_GET['Customer_mail'])) {
    $site_id = isset($_GET['site_id']) ? $_GET['site_id']: 0;
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $_GET['Customer_mail'] . "' and site_id = '".$site_id."'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer . "'");
    $address = tep_db_fetch_array($address_query);
    if (tep_db_num_rows($account_query) == 0) {
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'email_address=' . $_GET['Customer_mail'], 'SSL'));
    }
  }
// #### Generate Page
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo CREATE_ORDER_TITLE_TEXT;?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top">
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><b><?php echo CREATE_ORDER_STEP_ONE;?></b></font></td>
      </tr>
    </table>
  <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
  echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TWO_TEXT.'<br>';
  //echo 'メールアドレス:&nbsp;<input type="text" name="Customer_mail" size="40">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="  検索  "></p>' . "\n";
  echo CREATE_ORDER_YEZHE_NAME_TEXT.tep_customer_list_pull_down_menu().'&nbsp;&nbsp;<input type="submit" value="  検索  "></p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <?php echo tep_draw_form('create_order', FILENAME_CREATE_ORDER_PROCESS2, '', 'post', '', '') . tep_draw_hidden_field('customers_id', isset($account['customers_id'])?$account['customers_id']:''); ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_CREATE; ?></td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
  </table>
<?php
  //変数挿入
    $customer_id    = isset($account['customers_id'])           ? $account['customers_id']:'';
    $firstname      = isset($account['customers_firstname'])    ? $account['customers_firstname']:'';
    $lastname       = isset($account['customers_lastname'])     ? $account['customers_lastname']:'';
    $email_address  = isset($account['customers_email_address'])? $account['customers_email_address']:'';
    $telephone      = isset($account['customers_telephone'])    ? $account['customers_telephone']:'';
    $fax            = isset($account['customers_fax'])          ? $account['customers_fax']:'';
    $zone_id        = isset($account['entry_zone_id'])          ? $account['entry_zone_id']:'';
    $site_id        = isset($account['site_id'])                ? $account['site_id']:'';

    $street_address = isset($address['entry_street_address'])   ? $address['entry_street_address']:'';
    $company        = isset($address['entry_company'])          ? $address['entry_company']:'';
    $suburb         = isset($address['entry_suburb'])           ? $address['entry_suburb']:'';
    $postcode       = isset($address['entry_postcode'])         ? $address['entry_postcode']:'';
    $city           = isset($address['entry_city'])             ? $address['entry_city']:'';
    $state          = isset($address['entry_zone_id'])          ? tep_get_zone_name($address['entry_zone_id']):'';
    $country        = isset($address['entry_country_id'])       ? tep_get_country_name($address['entry_country_id']):'';
?>
<?php
/*
   $Id$
  
   3rmt over
*/

    tep_draw_hidden_field($customer_id);    
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_CORRECT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID; ?></td>
                <td class="main">&nbsp;<?php  echo tep_draw_hidden_field('customers_id', $customer_id) . $customer_id; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE?><?php if (isset($entry_firstname_error) && $entry_firstname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE?><?php if (isset($entry_lastname_error) && $entry_lastname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>'; ?><?php if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_SITE; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_SITE; ?>:</td>
                <td class="main">&nbsp;<?php echo isset($account) && $account?( '<font color="#FF0000"><b>'.tep_get_site_romaji_by_id($account['site_id']).'</b></font>'.tep_draw_hidden_field('site_id', $account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT); ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  if (ACCOUNT_COMPANY == 'true' && false) {
?>
  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_COMPANY; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('company', $company) . '&nbsp;' . ENTRY_COMPANY_TEXT; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  }
?>

<?php
  // オプションのリスト作成
  $torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
  $torihiki_list[] = array('id' => '', 'text' => '選択してください');
  for($i=0; $i<sizeof($torihiki_array); $i++) {
    $torihiki_list[] = array('id' => $torihiki_array[$i],
                'text' => $torihiki_array[$i]);
  }
  // 取引日のリスト作成
  $today = getdate();
  $m_num = $today['mon'];
  $d_num = $today['mday'];
  $year = $today['year'];
  $date_list[] = array('id' => '', 'text' => '取引日を選択してください');
  for($i=0; $i<14; $i++) {
    $date_list[] = array('id' => date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)),
              'text' => strftime("%Y年%m月%d日（%a）", mktime(0,0,0,$m_num,$d_num+$i,$year)));
  }
  // 取引時間のリスト作成
  $hour_list[] = array('id' => '', 'text' => '--');
  for($i=0; $i<24; $i++) {
    $hour_num = str_pad($i, 2, "0", STR_PAD_LEFT);
    $hour_list[] = array('id' => $hour_num,
              'text' => $hour_num);
  }
  
  $min_list[] = array('id' => '', 'text' => '--');
  for($i=0; $i<6; $i++) {
    $min_num = str_pad($i, 2, "0", STR_PAD_RIGHT);
    $min_list[] = array('id' => $min_num,
              'text' => $min_num);
  }
  // 支払方法のリスト作成 
  /*
  $payment_text = "銀行振込\nクレジットカード決済\n銀行振込(買い取り)\nコンビニ決済\nゆうちょ銀行（郵便局）\nその他の支払い";
  $payment_array = explode("\n", $payment_text);
  $payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
  for($i=0; $i<sizeof($payment_array); $i++) {
    if ($payment_array[$i] == '銀行振込(買い取り)')
    $payment_list[] = array('id' => $payment_array[$i],
                'text' => $payment_array[$i]);
  }

  $payment_list = array(
    array(
      'id' => '0',
      'text' => '銀行振込(買い取り)'
    )
  );
  */

?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_PAYMENT_TITLE;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_PAYMENT_TITLE;?>:</td>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_SHIRU_TEXT;?><input type="hidden" name="payment_method" value="<?php echo payment::changeRomaji('buying', PAYMENT_RETURN_TYPE_TITLE);?>"></td>
              </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_FETCH_TIME_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
          <?php
            $getTimeInfo = getReachTime(); 
          ?>
          <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_DATE_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('date', $date_list, isset($date)?$date:$getTimeInfo[0]); ?><?php if (isset($entry_date_error) && $entry_date_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_TIME_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('hour', $hour_list, isset($hour)?$hour:$getTimeInfo[1]); ?>&nbsp;時&nbsp;<?php echo tep_draw_pull_down_menu('min', $min_list, isset($min)?$min:$getTimeInfo[2]); ?>&nbsp;分&nbsp;<b><?php echo CREATE_ORDER_FETCH_ALLTIME_TEXT;?></b><?php if (isset($entry_tardetime_error ) && $entry_tardetime_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_TIME_SELECT_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('torihikihouhou', tep_get_all_torihiki(), isset($torihikihouhou)?$torihikihouhou:'指定した時間どおりに取引して欲しい'); ?><?php if (isset($entry_torihikihouhou_error) && $entry_torihikihouhou_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('fax', $fax, 'size="60" maxlength="255"'); ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
              </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_ONE;?></td>
        </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<b><?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_TWO;?></b></td>
        </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
</table>

  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><?php echo tep_html_element_submit(IMAGE_CONFIRM); ?></td>
      </tr>
    </table>
  </form>
  </td>
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->

<br>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
