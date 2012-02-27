<?php

require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);
if (isset($_GET['action']) && ($_GET['action'] == 'check_payment')) {
  if (!$payment_modules->moduleIsEnabled($_POST['payment'])) {
    echo tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message='.urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'); 
  } else {
    echo '1'; 
  }
}
