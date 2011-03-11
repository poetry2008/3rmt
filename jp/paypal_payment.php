<?php
require('includes/application_top.php');
/** SetExpressCheckout NVP example; last modified 08MAY23.
 *
 *  Initiate an Express Checkout transaction. 
*/

// Set request-specific fields.
$paymentAmount = urlencode(htmlspecialchars($_REQUEST['amount']));//合計金額？
$currencyID = urlencode('JPY');
$paymentType = urlencode('Sale');
$itemNumAMT   = urlencode(htmlspecialchars($_REQUEST['amount']));//商品単価
$itemQuantity = urlencode('1');//商品数量
$returnURL = urlencode(htmlspecialchars($_REQUEST['RETURNURL']));
$cancelURL = urlencode(htmlspecialchars($_REQUEST['CANCELURL']));
$pageDefault = urlencode('Billing'); //デフォルトでクレジットカード入力欄
$location = urlencode('JP');//国コード
//$customOption = urlencode('123456-789');//&CUSTOM=$customOption
// Add request-specific fields to the request string.

$nvpStr = "&AMT=$paymentAmount".
  "&RETURNURL=$returnURL".
  "&CANCELURL=$cancelURL".
  "&PAYMENTACTION=$paymentType".
  "&L_AMT0=$itemNumAMT".
  "&L_QTY0=$itemQuantity".
  "&CURRENCYCODE=$currencyID".
  //  "&LOCALECODE=$location".
  "&NOSHIPPING=1";
//	   &LandingPage=$pageDefault


// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $nvpStr);
if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	// Redirect to paypal.com.
	$token = urldecode($httpParsedResponseAr["TOKEN"]);
        $_SESSION['paypaltoken']=$token;
	$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
        $environment = defined('paypal_environment')?paypal_environment:'sandbox';
	if("sandbox" === $environment || "beta-sandbox" === $environment) {
		$payPalURL = "https://www.$environment.paypal.com/webscr&cmd=_express-checkout&token=$token&useraction=commit";
	}
	header("Location: $payPalURL");
	exit;
} else  {
  //	exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));

}

?>
