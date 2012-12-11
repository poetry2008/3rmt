<?php
require('includes/application_top.php');
require('includes/modules/payment/paypal.php');
/** SetExpressCheckout NVP example; last modified 08MAY23.
 *
 *  Initiate an Express Checkout transaction. 
*/

// Set request-specific fields.
$paymentAmount = urlencode(htmlspecialchars($_REQUEST['amount']));//合计金额
$currencyID = urlencode('JPY');
$paymentType = urlencode('Sale');
$itemNumAMT   = urlencode(htmlspecialchars($_REQUEST['amount']));//商品单价
$itemQuantity = urlencode('1');//商品数量
$returnURL = urlencode(htmlspecialchars($_REQUEST['RETURNURL']));
$cancelURL = urlencode(htmlspecialchars($_REQUEST['CANCELURL']));
$pageDefault = urlencode('Billing'); //默认的信用卡输入框
$location = urlencode('JP');//国家码
//$customOption = urlencode('123456-789');//&CUSTOM=$customOption
// Add request-specific fields to the request string.

$nvpStr = "&AMT=$paymentAmount".
  "&RETURNURL=$returnURL".
  "&CANCELURL=$cancelURL".
  "&PAYMENTACTION=$paymentType".
  "&L_AMT0=$itemNumAMT".
  "&L_QTY0=$itemQuantity".
  "&CURRENCYCODE=$currencyID".
  "&LOCALECODE=$location".
  "&LandingPage=$pageDefault".
  "&NOSHIPPING=1";



// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $nvpStr);
if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
  // Redirect to paypal.com.
  $token = urldecode($httpParsedResponseAr["TOKEN"]);
        $_SESSION['paypaltoken']=$token;
  //$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
    $payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=".$token."&useraction=commit";



  header("Location: $payPalURL");
  exit;
} else  {
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS, 'msg=paypal_error'.(isset($_POST['cpre_type'])?'&pre_type=1':'')));
  //  exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));

}

?>
