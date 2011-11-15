<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

class payment {
  var $site_id, $modules, $selected_module;

  // class constructor
  function payment($module = '', $site_id = 0) {
    global $payment, $language, $PHP_SELF;

    $this->site_id = $site_id;

    if (defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED)) {
      $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);
      $include_modules = array();

      if ( (tep_not_null($module)) && (in_array($module . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $this->modules)) ) {
        $this->selected_module = $module;
        $include_modules[] = array('class' => $module, 'file' => $module . '.php');
      } else {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          $include_modules[] = array('class' => $class, 'file' => $value);
        }
      }
      for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {
        include(DIR_WS_LANGUAGES . $language . '/modules/payment/' . $include_modules[$i]['file']);
        include(DIR_WS_MODULES . 'payment/' . $include_modules[$i]['file']);
        $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class']($this->site_id);

      }

      // if there is only one payment method, select it as default because in
      // checkout_confirmation.php the $payment variable is being assigned the
      // $_POST['payment'] value which will be empty (no radio button selection possible)
      if ( (tep_count_payment_modules() == 1) && (!is_object($GLOBALS[$payment])) ) {
        $payment = $include_modules[0]['class'];
      }

      if ( (tep_not_null($module)) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
        $this->form_action_url = $GLOBALS[$module]->form_action_url;
      }
    }
  }

  // class methods
  /* The following method is needed in the checkout_confirmation.php page
     due to a chicken and egg problem with the payment class and order class.
     The payment modules needs the order destination data for the dynamic status
     feature, and the order class needs the payment module title.
     The following method is a work-around to implementing the method in all
     payment modules available which would break the modules in the contributions
     section. This should be looked into again post 2.2.
  */   
  function update_status() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module])) {
        if (function_exists('method_exists')) {
          if (method_exists($GLOBALS[$this->selected_module], 'update_status')) {
            $GLOBALS[$this->selected_module]->update_status();
          }
        } else { // PHP3 compatibility
          @call_user_method('update_status', $GLOBALS[$this->selected_module]);
        }
      }
    }
  }

  function javascript_validation($num) {
    $js = '';
    if( $num == "" ){
      $num = 0;
    }
    if (is_array($this->modules)) {
      $js = '<script type="text/javascript"><!-- ' . "\n" .
        'function check_form() {' . "\n" .
        '  var error = 0;' . "\n" .
        '  var error_message = "' . JS_ERROR . '";' . "\n" .
        '  var payment_value = null;' . "\n" .
        '  var gold_max = ' . $num . ';' . "\n" .
        '  var gold_value = null;' . "\n" .
        '  gold_value = document.checkout_payment.point.value;' . "\n" .
        '  if (document.checkout_payment.payment.length) {' . "\n" .
        '    for (var i=0; i<document.checkout_payment.payment.length; i++) {' . "\n" .
        '      if (document.checkout_payment.payment[i].checked) {' . "\n" .
        '        payment_value = document.checkout_payment.payment[i].value;' . "\n" .
        '      }' . "\n" .
        '    }' . "\n" .
        '  } else if (document.checkout_payment.payment.checked) {' . "\n" .
        '    payment_value = document.checkout_payment.payment.value;' . "\n" .
        '  } else if (document.checkout_payment.payment.value) {' . "\n" .
        '    payment_value = document.checkout_payment.payment.value;' . "\n" .
        '  }' . "\n\n";

      reset($this->modules);
      while (list(, $value) = each($this->modules)) {
        $class = substr($value, 0, strrpos($value, '.'));
        if ($GLOBALS[$class]->enabled) {
            
          $js .= $GLOBALS[$class]->javascript_validation();
        }else {

        }
      }

      $js .= "\n" . '  if (payment_value == null) {' . "\n" .
        '    error_message = error_message + "' . JS_ERROR_NO_PAYMENT_MODULE_SELECTED . '";' . "\n" .
        '    error = 1;' . "\n" .
        '  }' . "\n\n" .
        '  if (gold_value > gold_max || gold_value < 0 ) {' . "\n" .
        '    error_message = error_message + "' . '獲得ポイントより多くのポイントを指定しているか、マイナスの値を指定しています。' . '";' . "\n" .
        '    error = 1;' . "\n" .
        '  }' . "\n\n" .
        '  if (error == 1) {' . "\n" .
        '    alert(error_message);' . "\n" .
        '    return false;' . "\n" .
        '  } else {' . "\n" .
        '    return true;' . "\n" .
        '  }' . "\n" .
        '}' . "\n" .
        '//--></script>' . "\n";
    }
    return $js;
  }

  function selection() {
    $selection_array = array();


    if (is_array($this->modules)) {
      reset($this->modules);
        
      while (list(, $value) = each($this->modules)) {

        $class = substr($value, 0, strrpos($value, '.'));
        //          var_dump($GLOBALS[$class]);
        if ($GLOBALS[$class]->enabled) {
          $selection = $GLOBALS[$class]->selection();
          if (is_array($selection)) $selection_array[] = $selection;
        }

      }
    }
    return $selection_array;
  }

  function pre_confirmation_check() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        $GLOBALS[$this->selected_module]->pre_confirmation_check();
      }
    }
  }
    
  function confirmation() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        return $GLOBALS[$this->selected_module]->confirmation();
      }
    }
  }
    
  function specialOutput() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) && method_exists($GLOBALS[$this->selected_module],"specialOutput")) {
        return $GLOBALS[$this->selected_module]->specialOutput();
      }
    }
  }
  function process_button() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        return $GLOBALS[$this->selected_module]->process_button();
      }
    }
  }

  function before_process() {
    if (is_array($this->modules)) {
      if (isset($GLOBALS[$this->selected_module]) && is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        return $GLOBALS[$this->selected_module]->before_process();
      }
    }
  }

  function after_process() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        return $GLOBALS[$this->selected_module]->after_process();
      }
    }
  }

  function get_error() {
    if (is_array($this->modules)) {
      if (is_object($GLOBALS[$this->selected_module]) && ($GLOBALS[$this->selected_module]->enabled) ) {
        return $GLOBALS[$this->selected_module]->get_error();
      }
    }
  }
	
  function getexpress($amt,$token){
    if (method_exists($GLOBALS[$this->selected_module], 'getexpress')) {
      $GLOBALS[$this->selected_module]->getexpress($amt,$token);
    }
  }
  function dealUnknow(&$sqldata){
    if (method_exists($GLOBALS[$this->selected_module], 'dealUnknow')) {
      $GLOBALS[$this->selected_module]->dealUnknow($sqldata);
    }
  }
  function dealComment($comment){
    if (method_exists($GLOBALS[$this->selected_module], 'dealComment')) {
      return $GLOBALS[$this->selected_module]->dealComment($comment);
    }else{
      return $comment;
    }
  }
  function getOrderMailString($option,$payment_selected=null){
    if(is_null($payment_selected)){
      $selected_module = $this->selected_module;
    }else {
      switch($payment_selected){
      case '支払方法を選択してください':
        $selected_module = '';
        break;
      case '銀行振込(買い取り)':
        $selected_module = 'buying';
        break;
      case 'ポイント(買い取り)':
        $selected_module = 'buyingpoint';
        break;
      case 'コンビニ決済':
        $selected_module = 'convenience_store';
        break;
      case '来店支払い':
        $selected_module = 'fetch_good';
        break;
      case '支払いなし':
        $selected_module = 'free_payment';
        break;
      case '銀行振込':
        $selected_module = 'moneyorder';
        break;
      case 'ペイパル決済':
        $selected_module = 'paypal';
        break;
      case 'ゆうちょ銀行（郵便局）':
        $selected_module = 'postalmoneyorder';
        break;
      case '楽天銀行':
        $selected_module = 'rakuten_bank';
        break;
      case 'クレジットカード決済':
        $selected_module = 'telecom';
        break;
      }
    }
    $mailstring = constant("MODULE_PAYMENT_".strtoupper($selected_module)."_MAILSTRING");
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }

}
?>
