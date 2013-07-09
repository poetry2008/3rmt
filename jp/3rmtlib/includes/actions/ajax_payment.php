<?php

require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);
if (isset($_GET['action']) && ($_GET['action'] == 'check_payment')) {
  if (!$payment_modules->moduleIsEnabled($_POST['payment'])) {
    //判断该支付方法是否可用 
    $_SESSION['payment_error'] = ERROR_NO_PAYMENT_MODULE_SELECTED;
    echo tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'); 
  } else {
    echo '1'; 
  }
}
