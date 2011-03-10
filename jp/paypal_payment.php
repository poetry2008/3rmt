<?php
require('includes/application_top.php');
require('paypal-api-conf.php');
/** SetExpressCheckout NVP example; last modified 08MAY23.
 *
 *  Initiate an Express Checkout transaction. 
*/


// Set request-specific fields.

$paymentAmount = urlencode(htmlspecialchars($_REQUEST['amount']));//合計金額？
$currencyID = urlencode('JPY');							// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
$paymentType = urlencode('Sale');				// or 'Sale' or 'Order'
//$itemNumber   = '623083';//商品番号
//$itemDesc     = 'サイズ';//商品サイズ
$itemNumAMT   = urlencode(htmlspecialchars($_REQUEST['amount']));//商品単価
$itemQuantity = urlencode('1');//商品数量

$returnURL = urlencode(htmlspecialchars($_REQUEST['RETURNURL']));
$cancelURL = urlencode(htmlspecialchars($_REQUEST['CANCELURL']));

$pageDefault = urlencode('Billing'); //デフォルトでクレジットカード入力欄
$location = urlencode('JP');//国コード

//$customOption = urlencode('123456-789');//&CUSTOM=$customOption
// Add request-specific fields to the request string.

/*
      //                 tep_draw_hidden_field('method', 'SetExpressCheckout').
      //                 tep_draw_hidden_field('business', 'bobher_1299564524_biz@gmail.com').
      //                 tep_draw_hidden_field('paymentaction', 'authorization').
      //                 tep_draw_hidden_field('PWD', '1299564532').
      //                 tep_draw_hidden_field('USER', 'bobher_1299564524_biz_api1.gmail.com').
      //                 tep_draw_hidden_field('SIGNATURE', 'AHbu1UVi7OHLerk7cyw7SE57-EvSANiOenfnho-SXzWVX0EQFAHvySxI').
                       tep_draw_hidden_field('amount','10000') .//旧money
      //                 tep_draw_hidden_field('version','51').
      //                 tep_draw_hidden_field('currency_code', "JPY") 
                 //tep_draw_hidden_field('return','http://jp.gamelife.jp/GetExpressCheckoutDetails.php' ) .//return
                 tep_draw_hidden_field('RETURNURL', tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL')) .//return
                 tep_draw_hidden_field('CANCELURL', tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL'));//return
 */

$nvpStr = "&AMT=$paymentAmount".
		   "&RETURNURL=$returnURL".
           "&CANCELURL=$cancelURL".
		   "&PAYMENTACTION=$paymentType".
		   "&L_AMT0=$itemNumAMT".
		   "&L_QTY0=$itemQuantity".
           "&CURRENCYCODE=$currencyID".
		   "&NOSHIPPING=1";

//		   &LOCALECODE=$location
//		   &LandingPage=$pageDefault
// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $nvpStr);
if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	// Redirect to paypal.com.
	$token = urldecode($httpParsedResponseAr["TOKEN"]);
    $_SESSION['paypaltoken']=$token;
	$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
    $environment = 'sandbox';
	if("sandbox" === $environment || "beta-sandbox" === $environment) {
		$payPalURL = "https://www.$environment.paypal.com/webscr&cmd=_express-checkout&token=$token&useraction=commit";
	}
	header("Location: $payPalURL");
	exit;
} else  {
	exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
}

?>
