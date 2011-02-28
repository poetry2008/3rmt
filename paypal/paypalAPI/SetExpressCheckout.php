<?php
require('paypal-api-conf.php');
/** SetExpressCheckout NVP example; last modified 08MAY23.
 *
 *  Initiate an Express Checkout transaction. 
*/

$environment = 'sandbox';	// or 'beta-sandbox' or 'live'テストをするために必要

/**
 * Send HTTP POST Request
 *
 * @param	string	The API method name
 * @param	string	The POST Message fields in &name=value pair format
 * @return	array	Parsed HTTP Response body
 */
function PPHttpPost($methodName_, $nvpStr_) {
	global $environment;

	// Set up your API credentials, PayPal end point, and API version.
	$API_UserName = urlencode(my_api_username);
	$API_Password = urlencode(my_api_password);
	$API_Signature = urlencode(my_api_signature);
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if("sandbox" === $environment || "beta-sandbox" === $environment) {
		$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
	}
	$version = urlencode('51.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// Get response from the server.
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
}

// Set request-specific fields.

$paymentAmount = urlencode(htmlspecialchars($_REQUEST['amount']));//合計金額？
$currencyID = urlencode('JPY');							// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
$paymentType = urlencode('Sale');				// or 'Sale' or 'Order'

//$itemNumber   = '623083';//商品番号
//$itemDesc     = 'サイズ';//商品サイズ
$itemNumAMT   = urlencode(htmlspecialchars($_REQUEST['amount']));//商品単価
$itemQuantity = urlencode('1');//商品数量

$returnURL = urlencode(htmlspecialchars($_REQUEST['return']));
$cancelURL = urlencode(htmlspecialchars($_REQUEST['cancel_return']));

$pageDefault = urlencode('Billing'); //デフォルトでクレジットカード入力欄
$location = urlencode('JP');//国コード

//$customOption = urlencode('123456-789');//&CUSTOM=$customOption
// Add request-specific fields to the request string.
$nvpStr = "&AMT=$paymentAmount
		   &ReturnUrl=$returnURL
		   &CANCELURL=$cancelURL
		   &PAYMENTACTION=$paymentType
		   &CURRENCYCODE=$currencyID
		   &LOCALECODE=$location
		   &LandingPage=$pageDefault
		   &L_AMT0=$itemNumAMT
		   &L_QTY0=$itemQuantity
		   &NOSHIPPING=1";

// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $nvpStr);

if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	// Redirect to paypal.com.
	$token = urldecode($httpParsedResponseAr["TOKEN"]);
	$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
	if("sandbox" === $environment || "beta-sandbox" === $environment) {
		$payPalURL = "https://www.$environment.paypal.com/webscr&cmd=_express-checkout&token=$token&useraction=commit";
	}
	header("Location: $payPalURL");
	exit;
} else  {
	exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
}

?>