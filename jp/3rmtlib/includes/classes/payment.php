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
/*----------------------
 功能：支付方法
 参数：$site_id(string) SITE_ID 值
 返回值：无
 ---------------------*/
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
/*----------------------------
 功能：获取实例
 参数：$site_id(site_id) SITE_ID值
 返回值：返回对象实例(string)
 ---------------------------*/
  public static function getInstance($site_id=0)
  { //如果对象实例还没有被创建，则创建一个新的实例

    global $language;
    $language = tep_get_default_language();
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
/*--------------------------
 功能：获取模块对象
 参数：$payment(string) 支付的方法名称
 返回值：返回模块对象或者FALSE(obj/boolean)
 -------------------------*/
  public function getModule($payment)
  {
        foreach ($this->modules as $module){
          if( $module instanceof $payment){
              return $module;
            }
        }
    return false;
  }
/*-------------------------
 功能：判断是否为开启状态
 参数：$site_id(string) SITE_ID值
 返回值：无
 ------------------------*/
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
/*----------------------------
 功能：判断支付方法是否被 enabled
 参数：$module(string) 模块名
 返回值：支付方法是否被 enabled true/false(boolean)
 ---------------------------*/
  public function moduleIsEnabled($module){
    foreach($this->payment_enabled as $value){
      if ($value['class'] == strtoupper($module)){
        return true;
      }
    }
    return false;
  }
/*---------------------------
 功能：初始化模块
 参数：无
 返回值: 无
 --------------------------*/
  function initModules()  {
    global $language;
    foreach($this->payment_enabled as $key=>$value){
      $languageFile = DIR_WS_LANGUAGES . $language . '/modules/payment/' . $value['file'];
      $classFile = DIR_WS_MODULES . 'payment/' .$value['file'];
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
  
/*-----------------------------
 功能：判断是否显示给 对应的用户
 参数：$payment(string) 支付方法名称
 参数：$userType(string) 用户类型
 返回值：是否显示对应的用户 true/false(boolean)
 ----------------------------*/
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

/*--------------------------------
 功能：判断 支付方法是否可以支付对应的 帐目
 参数：$payment(string) 支付方法名称
 参数：$total(string) 总数
 返回值：支付对应的账目 (string)
  ------------------------------*/
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
  
/*------------------------------
 功能：集中输出各个支付方法的js验证
 参数：$num(number) 数目
 返回值: 输出JS验证的支付方法(string)
 -----------------------------*/
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
      foreach($this->modules as $value){
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
        '  if (error == 1) {' . "\n" .
        '    alert(error_message);' . "\n" .
        '    return false;' . "\n" .
        '  } else {' . "\n" .
        '    return true;' . "\n" .
        '  }' . "\n" .
        '}' . "\n" .
        '--></script>' . "\n";
    }
    return $js;
  }

/*-------------------------
  功能：选择支付方法
  参数：$type(string) 型号
  返回值：支付方法(string)
  -----------------------*/
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
/*--------------------------
 功能：管理选择支付方法
 参数：无
 返回值：支付方法
 -------------------------*/ 
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
 /*-------------------------
  功能：后台管理确认检查模块
  参数：$payment(string) 支付方法
  返回值：判断是否检查模块(string/boolean)
  ------------------------*/ 
  function admin_confirmation_check($payment) {
    $module = $this->getModule($payment);
    $s = $this->admin_selection(); 
    if($module){
        return $module->validate_selection($s[strtoupper($payment)],$_POST,true);
    } else {
      return false;
    }
  }
/*-------------------------
 功能：后台管理添加的额外信息
 参数：$sql_data_array(string) SQL数据数组
 参数：$payment(string) 支付方法
 返回值：无
 ------------------------*/  
  function admin_add_additional_info(&$sql_data_array, $payment) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_add_additional_info')) {
        $module->admin_add_additional_info($sql_data_array); 
      }
    }
  }
/*------------------------
 功能：后台管理支付的电子邮件
 参数：$payment(string) 支付方法
 参数：$order(string) 订单
 参数：$total_price_mail(string) 总价邮件
 返回值：判断管理支付的电子邮件(string/boolean)
 -----------------------*/ 
  function admin_process_pay_email($payment,$order,$total_price_mail) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_process_pay_email')) {
        return $module->admin_process_pay_email($order,$total_price_mail); 
      }
    }
    return false;
  }
/*---------------------------
 功能：后台管理的处理意见
 参数：$payment(string) 支付方法
 返回值：处理意见(string)
 --------------------------*/
  function admin_deal_comment($payment) {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_deal_comment')) {
        return $module->admin_deal_comment($_SESSION['create_preorder']['orders']); 
      }
    } 
    return ''; 
  }
/*---------------------------
 功能：后台管理显示支付信息 
 参数：$payment(string) 支付方法
 返回值：判断显示的支付信息(string)
 --------------------------*/ 
  function admin_show_payment_info($payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      return $module->show_payment_info; 
    } 
    return 0; 
  }
/*--------------------------
 功能：确认检查支付方法
 参数：$payment(string) 支付方法
 返回值：返回检查支付方法或者false(string/boolean)
 -------------------------*/
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
/*-----------------------------
 功能：预约确认检查支付方法
 参数：$payment(string) 支付方法
 返回值：返回检查支付方法(string)
 ----------------------------*/
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
/*---------------------------
 功能：得到预约错误
 参数：$payment(string) 支付方法
 参数：$sn_type(string) 错误类型
 返回值：返回预约错误(string)
 --------------------------*/
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
/*--------------------------------
 功能：确认支付方法 
 参数：$payment(string) 支付方法
 返回值：支付方法(string)
 -------------------------------*/    
  function confirmation($payment) {
    $p = $this->getModule($payment);
    if(method_exists($p,'confirmation')){
    return $p->confirmation();
    }
  }
/*------------------------------
 功能：检查方法是否存在
 参数：$payment(string) 支付方法
 返回值：支付方法的名称(string)
 -----------------------------*/    
  function specialOutput($payment) {
    $p = $this->getModule($payment);
    if(method_exists($p,'specialOutput')){
      return $p->specialOutput($this->session_paymentvalue_name);
    }
    return ''; 
  }
/*-----------------------------
 功能：支付过程按钮
 参数：$payment(string) 支付方法 
 返回值：支付过程按钮(string)
 ----------------------------*/
  function process_button($payment) {
    $p = $this->getModule($payment);
    return $p->process_button();
  }
/*---------------------------
 功能：处理前面的方法
 参数：$payment(string) 支付方法
 返回值：处理方法(string)
 --------------------------*/
  function before_process($payment) {
    $p = $this->getModule($payment);
    return $p->before_process();
  }
/*--------------------------
 功能：处理后面的方法
 参数：$payment(string) 支付方法
 返回值：处理方法(string)
 -------------------------*/
  function after_process($payment) {
    $p = $this->getModule($payment);
    return $p->after_process();
  }
/*------------------------
 功能：获取错误 
 参数：$payment(string) 支付方法
 返回值：返回错误(string)
 -----------------------*/
  function get_error($payment) {
    $p = $this->getModule($payment);
    return $p->get_error();
  }
/*-----------------------
 功能: 快速获取支付方法金额
 参数：$payment(string) 支付方法
 参数：$amt(string)  订单金额
 返回值：获取的金额(string)
 ----------------------*/
  function getExpress($payment,$amt,$token){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'getExpress')){
        return $p->getExpress($amt,$token);
      }
    }
    return null;
  }
/*----------------------
 功能：处理没有的方法
 参数：$payment(string)支付方法
 参数：$sqldata(string) SQL数据
 返回值：处理完的方法(string)
 ---------------------*/
  function dealUnknow($payment,&$sqldata){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'dealUnknow')){
        return $p->dealUnknow($sqldata);
      }
    }
    return null;
  }
/*-----------------------
 功能：处理评论 
 参数: $payment(string) 支付方法
 参数：$comment(string) 评论
 返回值：处理完之后的评论(string)
 ----------------------*/
  function dealComment($payment,$comment){
    if($p = $this->getModule($payment)){
      if(method_exists($p,'dealComment')){
      $comment = $p->dealComment($comment, $this->session_paymentvalue_name);
      }
    }
    return $comment;
  }
/*----------------------
  功能：获取订单电子邮件的字符串
  参数：$payment(string) 支付方法
  参数：$option(string) 选项
  返回值：电子邮件的字符串(array)
 ---------------------*/
  function getOrderMailString($payment,$option){
    
    $mailstring = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment)."_MAILSTRING",$this->site_id);
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }
/*----------------------
 功能：获取订单打印电子邮件的字符串
 参数：$payment(string) 支付方法
 参数：$option(string) 选项
 返回值：电子邮件的字符串(array)
 ---------------------*/
  function getOrderPrintMailString($payment,$option){
    $mailstring = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($payment).
        "_PRINT_MAILSTRING",$this->site_id);
    foreach ($option as $key=>$value){
      $mailstring = str_replace('${'.strtoupper($key).'}',$value,$mailstring);
    }
    return $mailstring;
  }


/*-------------------------
 功能：获取支付方法列表
 参数：$type(string) 类型
 返回值：返回支付方法列表(string)
 ------------------------*/
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
      $language = tep_get_default_language();
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
/*-----------------------------
 功能：支付方法列表的下拉菜单 
 参数：$payment_method(string) 支付方式
 返回值：返回下拉菜单(string)
 ----------------------------*/
  public static   function makePaymentListPullDownMenu(&$payment_method =
      "",$site_id='') {
    //修改 变量名称
    $payment_method_abled = array();
    if(empty(self::$payment_array)){
      $payment_text = self::getPaymentList(); 
    }else{
      $payment_text = self::$payment_array;
    }
    $payment_array = $payment_text;
    //$payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
    for($i=0; $i<sizeof($payment_array[0]); $i++) {
      $continue_flag = false;
      if($site_id!=''){
        $payment_status = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment_array[0][$i]).'_STATUS',$site_id);
        if($payment_status == 'False'){
          $payment_status = false;
        }else{
          $customer_info = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment_array[0][$i]).'_LIMIT_SHOW',$site_id);
          $customer_arr = @unserialize($customer_info);
          if(in_array('1',$customer_arr)){
            $payment_status = true;
          }else{
            $payment_status = false;
          }
        }
        if(!$payment_status){
          $continue_flag = true;
        }
      }
      if($continue_flag){
        continue;
      }
      $payment_method_abled[] = $payment_array[0][$i];
      $payment_list[] = array('id' => $payment_array[0][$i],
                              'text' => $payment_array[1][$i]);
    }
    if(!in_array($payment_method,$payment_method_abled)){
      $payment_method = $payment_method_abled[0];
    }
    return tep_draw_pull_down_menu('payment_method', $payment_list, $payment_method);
  }
/*----------------------------
 功能：更改支付方法罗马字
 参数：$string(string) 字符串
 参数：$res_type(string) 类型
 返回值：返回罗马字(string)
 ---------------------------*/
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

/*--------------------------------
 功能：计算手续费   
 参数：$payment_method(string) 支付方式
 参数：$total_cost(string) 总花费
 返回值：总花费的手续费(string)
 -------------------------------*/
  public function handle_calc_fee($payment_method,$total_cost){
    $p = $this->getModule($payment_method);
    return $p->calc_fee($total_cost);
  }
/*-------------------------------
 功能：传递到SESSION
 参数：无
 返回值：$_POST(string)
 ------------------------------*/
  function postToSession(){
    $_SESSION[$this->session_paymentvalue_name] = $_POST;
    return $_POST;
  }
/*-----------------------------
 功能: 未设置SESSION
 参数：$value(string) 名称
 返回值：无
 ----------------------------*/
  function unsetPostSession($value){
    unset($_SESSION[$this->session_paymentvalue_name][$value]);
  }
/*----------------------------
 功能：获取SESSION 
 参数：无
 返回值：SESSION 支付方法名称(string)
 ---------------------------*/
  function getPostSession(){
    return $_SESSION[$this->session_paymentvalue_name];
  }
/*---------------------------
 功能：处理预约附加的信息
 参数：$pInfo(string) 预约信息
 参数：$sql_data_array(string) SQL数据
 返回值：预约的附加信息(string)
 --------------------------*/
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
/*--------------------------
 功能：预约支付按钮 
 参数：$payment(string) 支付方法
 参数：$pid(string) 预约ID
 参数：$total_param(string) 总参数
 返回值：支付按钮(string)
 -------------------------*/ 
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
/*-------------------------------
 功能：预约支付方法处理 
 参数：$sql_data_array(string) SQL数据
 参数：$payment(string) 支付方法
 返回值：预约的支付方法(string)
 ------------------------------*/  
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
/*----------------------------------
 功能：获取之前的方法 
 参数：$total_value(string) 总价值
 参数：$oid(string) ID 值
 参数：$payment(string) 支付方法
 返回值：支付方法(string)
 ---------------------------------*/ 
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
/*----------------------------
 功能：处理预约的邮件选项
 参数：$mailoption(string) 邮件选项
 参数：$payment(string) 支付方法
 参数: $pInfo(string) 预约信息
 返回值：预约的邮件选项(string)
 ---------------------------*/
  function preorder_deal_mailoption(&$mailoption, $payment, $pInfo)
  {
    if($p = $this->getModule($payment)){
      if(method_exists($p,'preorder_deal_mailoption')){
        $p->preorder_deal_mailoption($mailoption, $pInfo);
      }
    }
  }
/*---------------------------
 功能：处理邮件选项
 参数：$mailoption(string) 邮件选项
 参数：$payment(string) 支付方法
 返回值：邮件选项(string)
 --------------------------*/
  function deal_mailoption(&$mailoption, $payment)
  {
    if($p = $this->getModule($payment)){
      if(method_exists($p,'deal_mailoption')){
        $p->deal_mailoption($mailoption, $this->session_paymentvalue_name);
      }
    }
  }
/*-------------------------
 功能：处理预约信息 
 参数：$pInfo(string) 预约信息
 参数: $sql_data_array(string) SQL数据
 返回值：预约信息(string)
 ------------------------*/
  function deal_preorder_info($pInfo, &$sql_data_array)
  {
    $module = $this->getModule($pInfo['pre_payment']);
    if ($module) {
      if (method_exists($module, 'deal_preorder_info')) {
        $module->deal_preorder_info($pInfo, $sql_data_array); 
      }
    }
  }
/*-----------------------
 功能：后台管理邮件处理选项
 参数：$mailoption(string) 邮件选项
 参数：$payment(string) 支付方法 
 返回值：邮件选项(string)
 ----------------------*/ 
  function admin_deal_mailoption(&$mailoption, $oID, $payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_deal_mailoption')) {
        $module->admin_deal_mailoption($mailoption, $oID); 
      }
    }
  }
/*----------------------
 功能：处理其他信息
 参数：$payment(string) 支付方法
 参数：$pInfo(string) 信息
 返回值：返回信息(string)
 ---------------------*/
  function deal_other_info($payment, $pInfo)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'deal_other_info')) {
        $module->deal_other_info($pInfo); 
      }
    }
  }
/*----------------------
 功能：获取预约添加信息 
 参数：$payment(string) 支付方法
 参数：$order_info(string) 订单信息
 返回值：预约的添加信息(string)
 ---------------------*/
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
/*---------------------
 功能：显示支付方法目录
 参数：$payment(string) 支付方法
 参数：$pay_info_array(string) 支付信息的数组 
 返回值：支付方法的目录(string)
 --------------------*/
  function admin_show_payment_list($payment,$pay_info_array,$site_id=''){

    $module = $this->getModule($payment);
    $show_flag = true;
    if ($site_id!=''){
      $payment_status = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment).'_STATUS',$site_id);
      if($payment_status == 'False'){
        $payment_status = false;
      }else{
        $customer_info = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment).'_LIMIT_SHOW',$site_id);
        $customer_arr = @unserialize($customer_info);
        if(in_array('1',$customer_arr)){
          $payment_status = true;
        }else{
          $payment_status = false;
        }
      }
      if(!$payment_status){
        $show_flag = false;
      }
    }
    if ($module&&$show_flag) {
      if (method_exists($module, 'admin_show_payment_list')) {
         $module->admin_show_payment_list($pay_info_array); 
      }
    }    
  }
/*----------------------
 功能：获取到的点
 参数：$payment(string) 支付方法
 参数：$point_value(string) 点值
 返回值：返回的点值(string)
 ---------------------*/
  function admin_get_point($payment,$point_value){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_point')) {
         return $module->admin_get_point($point_value); 
      }
    }    

    return 0;
  }
/*---------------------------
 功能：获取顾客的点数 
 参数：$payment(string) 支付方法
 参数：$point_value(string) 点值
 参数：$customer_id(string) 顾客ID
 返回值：顾客的点数(string)
 --------------------------*/
  function admin_get_customer_point($payment,$point_value,$customer_id){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_customer_point')) {
         $module->admin_get_customer_point($point_value,$customer_id); 
      }
    }    
  }
/*------------------------
 功能：获取订单点数 
 参数：$payment(string) 支付方法
 参数：$orders_id(string) 订单ID
 返回值：订单点数(string)
 -----------------------*/
  function admin_get_orders_point($payment,$orders_id){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_orders_point')) {
         return $module->admin_get_orders_point($orders_id); 
      }
    }    
    return 0;
  }
/*-------------------------
 功能：获得取点 
 参数: $payment(string) 支付方法
 参数：$point_value(string) 点值
 返回值：返回取点(string)
 ------------------------*/
  function admin_get_fetch_point($payment,$point_value){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_fetch_point')) {
         return 0; 
      }
    }    

    return abs($point_value);
  }
/*-----------------------
 功能：获取支付方法标志  
 参数：$payment(string) 支付方法
 返回值: 支付方法标志(string)
 ----------------------*/
  function admin_get_payment_symbol($payment){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_symbol')) {
         return $module->admin_get_payment_symbol();; 
      }
    }    

    return 0;
  }
/*-----------------------
 功能：获取支付购买方法 
 参数：$payment(string) 支付方法
 参数：$mailoption(string) 邮件选项
 参数：$comment_arr(string) 评论
 返回值: 支付购买方法 (string)
 ----------------------*/
  function admin_get_payment_buying($payment,&$mailoption,$comment_arr){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_buying')) {
         $module->admin_get_payment_buying(&$mailoption,$comment_arr); 
      }
    } 
  }
/*------------------------------
 功能：获取支付购买方法类型 
 参数: $payment(string) 支付方法
 参数：$buying_type(string) 购买类型
 返回值：支付购买方法类型(string)
 -----------------------------*/
  function admin_get_payment_buying_type($payment,$buying_type){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_buying_type')) {
         return $module->admin_get_payment_buying_type($buying_type); 
      }
    } 

    return false;
  }
/*-----------------------------
 功能：获取支付方法信息 
 参数：$payment(string) 支付方法
 参数：$payment_info(string) 支付方法信息
 返回值：支付方法信息(string)
 ----------------------------*/
  function admin_get_payment_info($payment,$payment_info){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_info')) {
         return $module->admin_get_payment_info($payment_info); 
      }
    }
    return '';
  }
/*--------------------------
 功能：获取支付方法信息评论 
 参数：$payment(string) 支付方法
 参数：$customers_email(string) 客户的电子邮件
 参数：$site_id(string) SITE_ID 值
 参数：$orders_type(string) 订单类型
 返回值：支付方法的信息评论(string)
 -------------------------*/
  function admin_get_payment_info_comment($payment,$customers_email,$site_id,$orders_type=1){

    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_payment_info_comment')) {
         return $module->admin_get_payment_info_comment($customers_email,$site_id,$orders_type); 
      }
    }
    return '';
  }
/*--------------------------
 功能：获取点值 
 参数：$payment(string) 支付方法
 返回值：点值 (string)
 -------------------------*/
  function is_get_point($payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'is_get_point')) {
        return $module->is_get_point(); 
      }
    }
    return false; 
  }
/*--------------------------
 功能：后台获取点数 
 参数：$payment(string) 支付方法
 参数：$site_id(string) SITE_ID值
 返回值：点数(string)
 -------------------------*/  
  function admin_is_get_point($payment, $site_id)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_is_get_point')) {
        return $module->admin_is_get_point($site_id); 
      }
    }
    return 0; 
  }
/*--------------------------
 功能：后台获取点率
 参数：$payment(string) 支付方法
 参数：$site_id(string) SITE_ID值
 返回值：点率(string)
 -------------------------*/  
  function admin_get_point_rate($payment, $site_id)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_point_rate')) {
        return $module->admin_get_point_rate($site_id); 
      }
    }
    return 0; 
  }
/*-----------------------
 功能：后台得到点数 
 参数：$payment(string) 支付方法
 参数：$orders_id(string) 订单ID
 参数：$point_rate(string) 点率
 参数：$site_id(string) SITE_ID值
 返回值：点数 (string)
 -----------------------*/  
  function admin_calc_get_point($payment, $orders_id, $point_rate, $site_id)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_calc_get_point')) {
        return $module->admin_calc_get_point($orders_id, $point_rate, $site_id); 
      }
    }
    return 0; 
  }
/*----------------------------
 功能：获取点率 
 参数：$payment(string) 支付方法
 返回值：点率(string)
 ---------------------------*/ 
  function get_point_rate($payment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'get_point_rate')) {
        return $module->get_point_rate(); 
      }
    }
    return 0; 
  }
/*---------------------------
 功能：后台获取评论 
 参数：$payment(string) 支付方法
 参数：$comment(string) 评论
 返回值：返回评论信息(string)
 --------------------------*/
  function admin_get_comment($payment,$comment)
  {
    $module = $this->getModule($payment);
    if ($module) {
      if (method_exists($module, 'admin_get_comment')) {
        return $module->admin_get_comment($comment); 
      }
    }
    return ''; 
  }
}
?>
