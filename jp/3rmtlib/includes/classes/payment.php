<?php
/*
  $Id$
*/
define('PAYMENT_LIST_TYPE_ROMAJI',1);
define('PAYMENT_LIST_TYPE_HAIJI',2);
define('PAYMENT_LIST_TYPE_BOTH',3);
class payment {

  var $site_id, $modules, $selected_module;
  public static $payment_array;
  public static $payment_method_array;
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
	//          ar_dump($GLOBALS[$class]);
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
      return $GLOBALS[$this->selected_module]->getexpress($amt,$token);
    }
    return null;
  }
  function dealUnknow(&$sqldata){
    if (method_exists($GLOBALS[$this->selected_module], 'dealUnknow')) {
      return $GLOBALS[$this->selected_module]->dealUnknow($sqldata);
    }
    return null;
  }
  function dealComment($comment){
    if (method_exists($GLOBALS[$this->selected_module], 'dealComment')) {
      return $GLOBALS[$this->selected_module]->dealComment($comment);
    }else{
      return $comment;
    }
  }
  function getOrderMailString($option){
    /*
      ▼注文番号　       ${ORDER_ID}
      ▼注文日           ${ORDER_DATE}
      ▼お名前           ${USER_NAME}
      ▼メールアドレス   ${USER_MAILACCOUNT}
      ▼お支払金額       ${ORDER_TOTAL}
      ▼お支払方法　     ${ORDER_PAYMENT}
      ▼取引日時         ${ORDER_TTIME}
      ▼備考             ${ORDER_COMMENT}
      注文商品           ${ORDER_PRODUCTS}
      取引方法           ${ORDER_TMETHOD}
      //個数               ${ORDER_COUNT}
      //小計               ${ORDER_LTOTAL}
      //キャラクター名　   ${ORDER_ACTORNAME}
      サイト名           ${SITE_NAME}
      ショップメールアドレス  ${SITE_MAIL}
      ショップURL        ${SITE_URL}
    */

    $mailstring = constant("MODULE_PAYMENT_".strtoupper($this->selected_module)."_MAILSTRING");
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }


  /*
    相互转换romaji 和 支付方法日文
   */
  static function change($key){
    
  }
  public static   function getPaymentList($type='') {
    global $language;
    $payment_directory = DIR_FS_CATALOG_MODULES .'payment/';
    $payment_array = array();
    $payment_list_str = '';

    if ($dh = @dir($payment_directory)) {
      while ($payment_file = $dh->read()) {
        if (!is_dir($payment_directory.$payment_file)) {
          if (substr($payment_file, strrpos($payment_file, '.')) == '.php') {
            $payment_array[] = $payment_file; 
          }
        }
      }
      sort($payment_array);
      $dh->close();
    }

    $payment_method_array = array();
    for ($i = 0, $n = sizeof($payment_array); $i < $n; $i++) {
      $payment_filename = $payment_array[$i]; 
      include(DIR_WS_LANGUAGES . $language . '/modules/payment/' . $payment_filename); 
      include($payment_directory . $payment_filename); 
      $payment_class = substr($payment_filename, 0, strrpos($payment_filename, '.'));
      if (tep_class_exists($payment_class)) {
        $payment_module = new $payment_class; 
        $payment_method_array[$payment_class] = $payment_module;
        $payment_list_str[] = $payment_module->title;
        $payment_list_code[] = $payment_module->code;
      }
    }
    self::$payment_method_array = $payment_method_array;
    self::$payment_array = array($payment_list_code,$payment_list_str);
    if($type==PAYMENT_LIST_TYPE_HAIJI){
      return $payment_list_str;
    }
    if($type==PAYMENT_LIST_TYPE_ROMAJI){
      return $payment_list_code;
    }
    return array($payment_list_code,$payment_list_str);
  }
  public static   function makePaymentListPullDownMenu($payment_method = "") {
    //修改 变量名称
    if(empty(self::$payment_array)){
      $payment_text = self::getPaymentList(); 
    }else{
      $payment_text = self::$payment_array;
    }
    $payment_array = $payment_text;
    //$payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
    for($i=0; $i<sizeof($payment_array[0]); $i++) {
      $payment_list[] = array('id' => $payment_array[0][$i],
          'text' => $payment_array[1][$i]);
    }
    return tep_draw_pull_down_menu('payment_method', $payment_list, $payment_method);
  }
  public static function changeRomaji($string,$res_type=''){
    //获取 支付方法列表
    if(empty(self::$payment_array)){
      $payment_text = self::getPaymentList(); 
    }else{
      $payment_text = self::$payment_array;
    }
    $payment_array_code = $payment_text[0];
    $payment_array_title = $payment_text[1];
    //判断 返回信息
    if(preg_match("/^[a-zA-Z0-9_]*$/",$string)){
      //去掉 payment_
      if(preg_match("/^payment_/",$string)){
        $string = mb_substr($string,8,mb_strlen($string));
      }
      $return_type = "title";
      $payment_array = $payment_text[0];
    }else{
      $return_type = "code";
      $payment_array = $payment_text[1];
    }
    for($i=0; $i<sizeof($payment_array); $i++) {
      if($string == $payment_array[$i]){
        if($res_type){
          if($res_type=='code'){
            return $payment_array_code[$i];
          }else if($res_type='title'){
            return $payment_array_title[$i];
          }else{
            return false;
          }
        }else{
          if($return_type == 'code'){
            return $payment_array_code[$i];
          }else if($return_type == 'title'){
            return $payment_array_title[$i];
          }else{
            return false;
          }
        }
      }
    }

  }

  public static function calc_fee($payment_method,$total_cost){
    //判断是否 初始化 支付方法
    if(empty(self::$payment_method_array)){
      self::getPaymentList(); 
    }
    //通过 静态变量 调用 手续费处理方法
    self::$payment_method_array[self::changeRomaji($payment_method,'code')]->calc_fee($total_cost);
    return
      self::$payment_method_array[self::changeRomaji($payment_method,'code')]->n_fee;
  }

}
?>
