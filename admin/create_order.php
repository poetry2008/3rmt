<?php
/*
	JP、GM共通ファイル
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
  

// #### Get Available Customers
/*
	$query = tep_db_query("select customers_id, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " ORDER BY customers_lastname");
    $result = $query;

	
	if (tep_db_num_rows($result) > 0)
	{
 		// Query Successful
 		$SelectCustomerBox = "<select name='Customer'><option value=''>" . TEXT_SELECT_CUST . "</option>\n";
 		while($db_Row = tep_db_fetch_array($result))
 		{ $SelectCustomerBox .= "<option value='" . $db_Row["customers_id"] . "'";
		  if(IsSet($HTTP_GET_VARS['Customer']) and $db_Row["customers_id"]==$HTTP_GET_VARS['Customer'])
			$SelectCustomerBox .= " SELECTED ";
		  //$SelectCustomerBox .= ">" . $db_Row["customers_lastname"] . " , " . $db_Row["customers_firstname"] . " - " . $db_Row["customers_id"] . "</option>\n"; 
		  $SelectCustomerBox .= ">" . $db_Row["customers_lastname"] . " " . $db_Row["customers_firstname"] . "</option>\n";
		
		}
		
		$SelectCustomerBox .= "</select>\n";
	}
	
	$query = tep_db_query("select code, value from " . TABLE_CURRENCIES . " ORDER BY code");
	$result = $query;
	
	if (tep_db_num_rows($result) > 0)
	{
 		// Query Successful
 		$SelectCurrencyBox = "<select name='Currency'><option value=''>" . TEXT_SELECT_CURRENCY . "</option>\n";
 		while($db_Row = tep_db_fetch_array($result))
 		{ 
			$SelectCurrencyBox .= "<option value='" . $db_Row["code"] . "," . $db_Row["value"] . "'";
			if ($db_Row["code"] == 'JPY') {
				$SelectCurrencyBox .= ' selected';
			}
		  	$SelectCurrencyBox .= ">" . $db_Row["code"] . "</option>\n";
		}
		
		$SelectCurrencyBox .= "</select>\n";
	}
*/

	if (IsSet($HTTP_GET_VARS['Customer'])) {
 	$account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $HTTP_GET_VARS['Customer'] . "'");
 	$account = tep_db_fetch_array($account_query);
 	$customer = $account['customers_id'];
 	$address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $HTTP_GET_VARS['Customer'] . "'");
 	$address = tep_db_fetch_array($address_query);
 	//$customer = $account['customers_id'];
	} elseif (IsSet($HTTP_GET_VARS['Customer_nr'])) {
 	$account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $HTTP_GET_VARS['Customer_nr'] . "'");
 	$account = tep_db_fetch_array($account_query);
 	$customer = $account['customers_id'];
 	$address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $HTTP_GET_VARS['Customer_nr'] . "'");
 	$address = tep_db_fetch_array($address_query);
 	//$customer = $account['customers_id'];
	} elseif (IsSet($HTTP_GET_VARS['Customer_mail'])) {
 	$account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $HTTP_GET_VARS['Customer_mail'] . "'");
 	$account = tep_db_fetch_array($account_query);
 	$customer = $account['customers_id'];
 	$address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer . "'");
 	$address = tep_db_fetch_array($address_query);
	if (tep_db_num_rows($account_query) == 0) {
		tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'email_address=' . $HTTP_GET_VARS['Customer_mail'], 'SSL'));
	}
 	//$customer = $account['customers_id'];
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
/*
	echo "<form action='$PHP_SELF' method='GET'>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td><br>$SelectCustomerBox</td>\n";
	echo "<td valign='bottom'><input type='submit' value=\"" . BUTTON_SUBMIT . "\"></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";

	echo "<form action='$PHP_SELF' method='GET'>\n";
	echo "<table border='0'>\n";
	echo "<tr>\n";
	echo "<td><font class=main><b><br>" . TEXT_OR_BY . "</b></font><br><br><input type=text name='Customer_nr'></td>\n";
	echo "<td valign='bottom'><input type='submit' value=\"" . BUTTON_SUBMIT . "\"></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
*/
	echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
	echo '<p class=main>メールアドレスを入力し「検索」ボタンをクリックしてください。<br>メールアドレス:&nbsp;<input type="text" name="Customer_mail" size="40">&nbsp;&nbsp;<input type="submit" value="  検索  "></p>' . "\n";
	echo '</form>' . "\n";
?>
	<br>
	<?php echo tep_draw_form('create_order', FILENAME_CREATE_ORDER_PROCESS, '', 'post', '', '') . tep_draw_hidden_field('customers_id', $account->customers_id); ?>
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
    $customer_id = $account['customers_id'];
    $firstname = $account['customers_firstname'];
    $lastname = $account['customers_lastname'];
    $email_address = $account['customers_email_address'];
    $telephone = $account['customers_telephone'];
    $fax = $account['customers_fax'];
    $street_address = $address['entry_street_address'];
    $company = $address['entry_company'];
    $suburb = $address['entry_suburb'];
    $postcode = $address['entry_postcode'];
    $city = $address['entry_city'];
    $zone_id = $account['entry_zone_id'];
    $state = tep_get_zone_name($address['entry_zone_id']);
    $country = tep_get_country_name($address['entry_country_id']);
	
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
