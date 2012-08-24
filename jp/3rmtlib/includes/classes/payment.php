<?php
/*
  $Id$
*/
define('PAYMENT_LIST_TYPE_ROMAJI',1);
define('PAYMENT_LIST_TYPE_HAIJI',2);
define('PAYMENT_LIST_TYPE_BOTH',3);
define('PAYMENT_RETURN_TYPE_CODE','code');
define('PAYMENT_RETURN_TYPE_TITLE','title');
class payment {
  static private  $instance = NULL;
  var $site_id, $modules, $selected_module;
  public static $payment_array;
  public static $payment_method_array;
  var $session_error_name = 'payment_selection_error';
  var $session_paymentvalue_name = 'payment_value';
  function payment($site_id = 0) {
    
    $this->site_id = $site_id;
    $this->loadSettings($site_id);
    $this->initModules();
    
    if (( count($this->modules) == 1) && (!is_object($GLOBALS[$payment])) ) {
      $payment = $include_modules[0]['class'];
    }
    if ( (tep_not_null($module)) && (in_array($module, $this->modules)) && (isset($GLOBALS[$module]->form_action_url)) ) {
      $this->form_action_url = $GLOBALS[$module]->form_action_url;
    }

  }
  public static function getInstance($site_id=0)
  { //如果对象实例还没有被创建，则创建一个新的实例

    global $language;
    if(self::$instance == NULL)
      {
        self::$instance =new payment($site_id);
      } //返回对象实例
    
    $payment_con_file = DIR_WS_LANGUAGES . $language .  '/modules/payment/payment.php';
    require_once $payment_con_file; 
    
    foreach(self::$instance->payment_enabled as $key=>$value){
      $languageFile = DIR_WS_LANGUAGES . $language . '/modules/payment/' . $value['file'];
      $classFile = DIR_WS_MODULES . 'payment/' .$value['file'];
      require_once $languageFile;
      require_once $classFile;
    }
    return self::$instance;
  } 
  
  public function getModule($payment)
  {
        foreach ($this->modules as $module){
          if( $module instanceof $payment){
              return $module;
            }
        }
    return false;
  }
  public function loadSettings($site_id=0)
  {
    //安装了哪些支付方法
    //moneyorder.php;postalmoneyorder.php;convenience_store.php;telecom.php;paypal.php;buyingpoint.php;fetch_good.php;free_payment.php;rakuten_bank.php;buying.php;guidance.php
    //取得安装了哪些支付方法
    $paymentString =  get_configuration_by_site_id_or_default('MODULE_PAYMENT_INSTALLED',$site_id);
    $paymentStringArray = explode(";",$paymentString);
    $class = '';

    foreach($paymentStringArray as $value){
      $class = strtoupper(substr($value,0,strpos($value,'.')));
      //判断是否为开启状态 经过此步以后  得到的payment_installed是所有可用的
      if(get_configuration_by_site_id_or_default("MODULE_PAYMENT_".$class."_STATUS",$site_id) == "True"){
        $this->payment_enabled[] = array(
                                         "file"=>$value,
                                         "class"=>$class,
                                         );
        
      }
    }
  }

  //判断支付方法是否被 enabled
  public function moduleIsEnabled($module){
    foreach($this->payment_enabled as $value){
      if ($value['class'] == strtoupper($module)){
        return true;
      }
    }
    return false;
  }

  function initModules()  {
    global $language;
    foreach($this->payment_enabled as $key=>$value){
      $languageFile = DIR_WS_LANGUAGES . $language . '/modules/payment/' . $value['file'];
      $classFile = DIR_WS_MODULES . 'payment/' .$value['file'];
      //      if(file_exists($languageFile) and file_exists($classFile)){
      require_once $languageFile;
      require_once $classFile;
      $class = strtolower($value['class']);
      $p = new $class($this->site_id);;
      if($p instanceof basePayment){
        $this->modules[$value['class']] = $p;
      }else {
        die($value['class'].' not correct');
      }

    }

  }
  
  /*
    判断是否显示给 对应的用户
    取到对应的值 MODULE_PAYMENT_{$romaji}_LIMIT_SHOW
  */
  function showToUser($payment,$userType){
    $payment_arr = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment)."_LIMIT_SHOW",$this->site_id);
    $payment_arr = unserialize($payment_arr);

    if (empty($payment_arr)) {
      return false; 
    }
    if (count($payment_arr) == 1) { 
      if ($payment_arr[0] == 1) {
        if ($userType != 0) {
          return false; 
        }
      } else {
        if ($userType == 0) {
          return false; 
        }
      }
    }
    return true;
    
  }

  /*
    判断 支付方法是否可以支付对应的 帐目

    先判断 是否为可用 支付方法
    从数据 取到  MODULE_PAYMENT_{romaji}_MONEY_LIMIT  并进行计算 返回结果 

   */
  function moneyInRange($payment,$total){
    
    if($this->moduleIsEnabled($payment)){
      $range = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment)."_MONEY_LIMIT",$this->site_id);
    }
    $limit_arr = explode(",", $range); 
    if(count($limit_arr)!=2){
      return false;
    }
    $a = $limit_arr[0];
    $b = $limit_arr[1];
    if(!is_numeric($a) or !is_numeric($b) or !is_numeric($total)){

      return false;
    }
    $a = toNumber($a);
    $b = toNumber($b);
    $total = toNumber($total);

    return !($a<=$total and $total<=$b);
  }
  /*
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
  */

  //todo:查看下面两个常量的定义位置  去掉global
  
  /*
    集中输出各个支付方法的js验证
  */
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
        '  if ("point" in document.checkout_payment) {gold_value = document.checkout_payment.point.value}' . "\n" .
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
      //while (list(, $value) = each($this->modules)) {
      foreach($this->modules as $value){
        //$class = substr($value, 0, strrpos($value, '.'));
        $class = $value->code;
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
        '    error_message = error_message + "' .
            JS_ERROR_POINT . '";' . "\n" .
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


  //todo: 完成函数
  function selection($type = 0) {
    if (!$type) { 
      global $order, $currencies; 
      if($_SERVER['REQUEST_METHOD']=='POST'){
        $theData = $this->postToSession();
      }else {
        $theData = $this->getPostSession();
      }
    } else {
      $theData = $_POST; 
    } 
    $selection_array = array();
    foreach($this->modules as $key=>$value){
      $total_cost = $value->calc_fee($order->info['total']); 
      $selection_array[$key] = array(
                                     "id"=>$value->code,
                                     'module' => isset($value->additional_title)?$value->additional_title:$value->title,
                                     'description'=>$value->explain,
                                     'fields_description'=>$value->fields_description,
                                     'footer'=>$value->footer,
                                     'fields' => $value->fields($theData),
      );
      if (!$type) { 
        if ($total_cost > 0) {
          $selection_array[$key]['codefee'] = constant("TS_MODULE_PAYMENT_".strtoupper($value->code)."_TEXT_FEE").$currencies->format($total_cost); 
        } 
      } 
    }
    $this->static_selection = $selection_array;
    return $selection_array;
  }
  
  function admin_selection() {
    $selection_array = array();
    $theData = $_POST; 
    foreach($this->modules as $key=>$value){
    if (empty($value->code)) {
      continue; 
    }
    $selection_array[strtoupper($key)] = array('id'=>$value->code,
                            'module' => $value->title,
                            'fields' => $value->fields($theData, true)
    );
    } 

    return $selection_array;
  }
  
  function admin_confirmation_check($payment) {
    $module = $this->getModule($payment);
    $s = $this->admin_selection(); 
    if($module){
        return $module->validate_selection($s[strtoupper($payment)],$_POST,true);
    } else {
      return false;
    }
  }
  
  function admin_add_additional_info(&$sql_data_array, $payment) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_add_additional_info')) {
        $module->admin_add_additional_info($sql_data_array); 
      }
    }
  }
  
  function admin_process_pay_email($payment,$order,$total_price_mail) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_process_pay_email')) {
        return $module->admin_process_pay_email($order,$total_price_mail); 
      }
    }
    return false;
  }

  function admin_deal_comment($payment) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_deal_comment')) {
        return $module->admin_deal_comment($_SESSION['create_preorder']['orders']); 
      }
    } 
    return ''; 
  }
 
  function admin_show_payment_info($payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      return $module->show_payment_info; 
    } 
    return 0; 
  }

  function pre_confirmation_check($payment) {
    $module = $this->getModule($payment);
    if($module){
      $pre_check = $module->pre_confirmation_check($back);
      if($pre_check == true){
        $s = $this->selection();
        return $module->validate_selection($s[strtoupper($payment)],$_POST,$back);
      }else{
        $s = $this->selection();
        $_SESSION['payment_error'] = constant(strtoupper('TS_MODULE_PAYMENT_'.$payment.'_ERROR'));
        return false;
      }
    }else {
      return false;
    }
  }

  function preorder_confirmation_check($payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'preorder_confirmation_check')) {
        return $module->preorder_confirmation_check(); 
      }
    } 
    return 0; 
  }
  
  function get_preorder_error($payment, $sn_type)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'get_preorder_error')) {
        return $module->get_preorder_error($sn_type); 
      }
    } 
    return ''; 
  }
    
  function confirmation($payment) {
    $p = $this->getModule($payment);
    if(method_exists($p,'confirmation')){
    return $p->confirmation();
    }
  }
    
  function specialOutput($payment) {
    $p = $this->getModule($payment);
    if(method_exists($p,'specialOutput')){
      return $p->specialOutput($this->session_paymentvalue_name);
    }
    return ''; 
  }
  function process_button($payment) {
    $p = $this->getModule($payment);
    return $p->process_button();
  }

  function before_process($payment) {
    $p = $this->getModule($payment);
    return $p->before_process();
  }

  function after_process($payment) {
    $p = $this->getModule($payment);
    return $p->after_process();
  }

  function get_error($payment) {
    $p = $this->getModule($payment);
    return $p->get_error();
  }
	
  function getExpress($payment,$amt,$token){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'getExpress')){
        return $p->getExpress($amt,$token);
      }
    }
    return null;
  }
  function dealUnknow($payment,&$sqldata){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'dealUnknow')){
        return $p->dealUnknow($sqldata);
      }
    }
    return null;
  }
  function dealComment($payment,$comment){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'dealComment')){
      $comment = $p->dealComment($comment, $this->session_paymentvalue_name);
      }
    }
    return $comment;
  }
  function getOrderMailString($payment,$option){
    
    $mailstring = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment)."_MAILSTRING",$this->site_id);
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }

  function getOrderPrintMailString($payment,$option){
    $mailstring = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment).
        "_PRINT_MAILSTRING",$this->site_id);
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }



  public static function getPaymentList($type='') {
    global $language;
    $payment_directory =DIR_FS_3RMTLIB. DIR_WS_MODULES .'payment/';
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
      require_once DIR_WS_LANGUAGES . $language . '/modules/payment/' . $payment_filename; 
      require_once $payment_directory . $payment_filename; 
      $payment_class = substr($payment_filename, 0, strrpos($payment_filename, '.'));
     if (class_exists($payment_class)) {
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

  /*
    计算手续费   
   */
  public function handle_calc_fee($payment_method,$total_cost){
    $p = $this->getModule($payment_method);
    return $p->calc_fee($total_cost);
  }
  function postToSession(){
    $_SESSION[$this->session_paymentvalue_name] = $_POST;
    return $_POST;
  }
  function unsetPostSession($value){
    unset($_SESSION[$this->session_paymentvalue_name][$value]);
  }
  function getPostSession(){
    return $_SESSION[$this->session_paymentvalue_name];
  }

  function deal_preorder_additional($pInfo, &$sql_data_array)
  {
    $module = $this->getModule($pInfo['pre_payment']);
    if ($module) {
      if (method_exists($module, 'deal_preorder_additional')) {
        return $module->deal_preorder_additional($pInfo, $sql_data_array); 
      }
    }
    return $pInfo['yourmessage']; 
  }
  
  function preorder_process_button($payment, $pid, $total_param)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'preorder_process_button')) {
        return $module->preorder_process_button($pid, $total_param); 
      }
    }
    return ''; 
  }
  
  function preorderDealUnknow(&$sql_data_array, $payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'preorderDealUnknow')) {
        return $module->preorderDealUnknow($sql_data_array); 
      }
    }
    return false; 
  }
  
  function getPreexpress($total_value, $oid, $payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'getpreexpress')) {
        return $module->getpreexpress($total_value, $oid); 
      }
    }
    return null; 
  }

  function preorder_deal_mailoption(&$mailoption, $payment, $pInfo)
  {
    if($p = $this->getModule($payment)){
      if(method_exists($p,'preorder_deal_mailoption')){
        $p->preorder_deal_mailoption($mailoption, $pInfo);
      }
    }
  }

  function deal_mailoption(&$mailoption, $payment)
  {
    if($p = $this->getModule($payment)){
      if(method_exists($p,'deal_mailoption')){
        $p->deal_mailoption($mailoption, $this->session_paymentvalue_name);
      }
    }
  }

  function deal_preorder_info($pInfo, &$sql_data_array)
  {
    $module = $this->getModule($pInfo['pre_payment']);
    if ($module) {
      if (method_exists($module, 'deal_preorder_info')) {
        $module->deal_preorder_info($pInfo, $sql_data_array); 
      }
    }
  }
  
  function admin_deal_mailoption(&$mailoption, $oID, $payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_deal_mailoption')) {
        $module->admin_deal_mailoption($mailoption, $oID); 
      }
    }
  }

  function deal_other_info($payment, $pInfo)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'deal_other_info')) {
        $module->deal_other_info($pInfo); 
      }
    }
  }

  function get_preorder_add_info($payment, $order_info)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'get_preorder_add_info')) {
        return $module->get_preorder_add_info($order_info); 
      }
    }
    return $order_info['comment_msg']; 
  }

  function admin_show_payment_list($payment,$pay_comment){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_show_payment_list')) {
         $module->admin_show_payment_list($pay_comment); 
      }
    }    
  }

  function admin_get_point($payment,$point_value){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_point')) {
         return $module->admin_get_point($point_value); 
      }
    }    

    return 0;
  }

  function admin_get_customer_point($payment,$point_value,$customer_id){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_customer_point')) {
         $module->admin_get_customer_point($point_value,$customer_id); 
      }
    }    
  }

  function admin_get_orders_point($payment,$orders_id){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_orders_point')) {
         return $module->admin_get_orders_point($orders_id); 
      }
    }    
    return 0;
  }

  function admin_get_fetch_point($payment,$point_value){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_fetch_point')) {
         return 0; 
      }
    }    

    return abs($point_value);
  }

  function admin_get_payment_symbol($payment){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_symbol')) {
         return $module->admin_get_payment_symbol();; 
      }
    }    

    return 0;
  }

  function admin_get_payment_buying($payment,&$mailoption,$comment_arr){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_buying')) {
         $module->admin_get_payment_buying(&$mailoption,$comment_arr); 
      }
    } 
  }

  function admin_get_payment_buying_type($payment,$buying_type){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_buying_type')) {
         return $module->admin_get_payment_buying_type($buying_type); 
      }
    } 

    return false;
  }
}
?>
