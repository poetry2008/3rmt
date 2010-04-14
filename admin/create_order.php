<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
  
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
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top">
	<table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><b>ステップ 1 - 顧客を検索します</b></font></td>
      </tr>
    </table>
	<p class="pageHeading">登録データの有無を確認:</p>
<?php
	echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
	echo '<p class=main>メールアドレスを入力し「検索」ボタンをクリックしてください。<br>メールアドレス:&nbsp;<input type="text" name="Customer_mail" size="40">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="  検索  "></p>' . "\n";
	echo '</form>' . "\n";
?>
	<br>
	<?php echo tep_draw_form('create_order', FILENAME_CREATE_ORDER_PROCESS, '', 'post', '', '') . tep_draw_hidden_field('customers_id', isset($account['customers_id'])?$account['customers_id']:''); ?>
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
    require(DIR_WS_INCLUDES . 'step-by-step/create_order_details.php');
?>
	<br>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
        <td class="main" align="right"><?php echo tep_image_submit('button_confirm.gif', IMAGE_BUTTON_CONFIRM); ?></td>
      </tr>
    </table>
	</form>
	</td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<br>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
