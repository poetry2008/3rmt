<?php
/*
   $Id$

   osCommerce, Open Source E-Commerce Solutions
   http://www.oscommerce.com

   Copyright (c) 2003 osCommerce

   Released under the GNU General Public License
 */
define('SHIPPING_LIST_TYPE_ROMAJI',1);
define('SHIPPING_LIST_TYPE_HAIJI',2);
define('SHIPPING_LIST_TYPE_BOTH',3);
define('SHIPPING_RETURN_TYPE_CODE','code');
define('SHIPPING_RETURN_TYPE_TITLE','title');

class shipping {
  static private $instance = null;
  var $modules,$site_id,$products_id;
  public static $shipping_array;
  public static $shipping_method_array;
  var $session_error_name = 'shipping_selection_error';
  var $session_shippingvalue_name = 'shipping_value';

  // class constructor
  function shipping($site_id=0){
    $this->site_id = $site_id;
    $this->loadSettings($site_id);
    $this->initModules();

    if((count($this->modules) == 1)&&(!is_object($GLOBALS[$shipping]))){
      $shipping = $incluede_modules[0]['class'];
    }
    if( (tep_not_null($module)) && (in_array($module, $this->modules))&& 
        (isset($GLOBALS[$module]->form_action_url)) ){
      $this->form_action_url = $GLOBALS[$module]->form_action_url;
    }

  }

  public function loadSettings($site_id=0){
    $class = '';
    // 取得 安装了那些 配送方法
    $shippingString = get_configuration_by_site_id_or_default('MODULE_SHIPPING_INSTALLED',$site_id);
    $shippingStringArray = explode(';',$shippingString);
    foreach($shippingStringArray as $value){
      $class = strtoupper(substr($value,0,strpos($value,'.')));
      if(get_configuration_by_site_id_or_default("MODULE_SHIPPING_".$class."_STATUS",$site_id)
          =="True"){
        $this->shipping_enabled[] = array(
            "file"=>$value,
            "class"=>$class,
            );
      }
    }
  }

  //如果对象实例还没有被创建，则创建一个新的实例
  public static function getInstance($site_id=0){
    global $language;
    if(self::$instance == null){
      self::$instance =new shipping($site_id);
    }
    foreach(self::$instance->shipping_enabled as $key => $value){
      $languageFile = DIR_WS_LANGUAGES . $language . '/modules/shipping/' .
        $value['file'];
      $classFile = DIR_WS_MODULES . 'shipping/' .$value['file'];
      require_once $languageFile;
      require_once $classFile;
    }
    return self::$instance;
  }
 
   public function getModule($shipping){
    foreach($this->modules as $module){
      if($module instanceof $shipping){
        return $module;
      }
    }
    return false;
  }

  //判断 配送 是否 enabled
  public function moduleIsEnabled($module){
    foreach($this->shipping_enabled as $value){
      if($value['class'] == strtoupper($module)){
        return true;
      }
    }
    return false;
  }

  function initModules(){
    global $language;
    foreach($this->shipping_enabled as $key=>$value){
      $languageFile = DIR_WS_LANGUAGES . $language . '/modules/shipping/' .
        $value['file'];
      $classFile = DIR_WS_MODULES . 'shipping/' .$value['file'];
      require_once $languageFile;
      require_once $classFile;
      $class = strtolower($value['class']);
      $p = new $class($this->site_id);
      if($p instanceof baseShipping){
        $this->modules[$value['class']] = $p;
      }else{
        die($value['class'].' not correct');
      }
    }
  }
  /*

   */
  function showToUser($shipping,$userType){
    $shipping_arr = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($shipping)."_LIMIT_SHOW",$this->site_id);
    $shipping_arr = unserialize($shipping_arr);

    if (empty($shipping_arr)) {
      return false; 
    }   
    if (count($shipping_arr) == 1) { 
      if ($shipping_arr[0] == 1) {
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

  function selection(){
    if($_SERVER['REQUEST_METHOD']=='POST'){
      $theData = $this->postToSession();
    }else{
      $theData = $this->getPostSession();
    }
  }

  function postToSession(){
    $_SESSION[$this->session_shippingvalue_name] = $_POST;
    return $_POST;
  }

  function unsetPostSession($value){
    unset($_SESSION[$this->session_shippingvalue_name][$value]);
  }

  function getPostSession(){
    return $_SESSION[$this->session_shippingvalue_name];
  }

  function quote($method = '', $module = '') {
    global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

    $quotes_array = array();

    if (is_array($this->modules)) {
      $shipping_quoted = '';
      $shipping_num_boxes = 1;
      $shipping_weight = $total_weight;

      if ($total_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
        $shipping_num_boxes = ceil($total_weight/SHIPPING_MAX_WEIGHT);
        $shipping_weight = $total_weight/$shipping_num_boxes;
      }

      if (SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {
        $shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;
      } else {
        $shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);
      }

      $include_quotes = array();

      reset($this->modules);
      while (list(, $value) = each($this->modules)) {
        $class = substr($value, 0, strrpos($value, '.'));
        if (tep_not_null($module)) {
          if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
            $include_quotes[] = $class;
          }
        } elseif ($GLOBALS[$class]->enabled) {
          $include_quotes[] = $class;
        }
      }

      $size = sizeof($include_quotes);
      for ($i=0; $i<$size; $i++) {
        $quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
        if (is_array($quotes)) $quotes_array[] = $quotes;
      }
    }

    return $quotes_array;
  }

  function cheapest() {
    if (is_array($this->modules)) {
      $rates = array();

      reset($this->modules);
      while (list(, $value) = each($this->modules)) {
        $class = substr($value, 0, strrpos($value, '.'));
        if ($GLOBALS[$class]->enabled) {
          $quotes = $GLOBALS[$class]->quotes;
          $size = sizeof($quotes['methods']);
          for ($i=0; $i<$size; $i++) {
            if ($quotes['methods'][$i]['cost']) {
              $rates[] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
                  'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
                    'cost' => $quotes['methods'][$i]['cost']);
                  }
                  }
                  }
                  }

                  $cheapest = false;
                  $size = sizeof($rates);
                  for ($i=0; $i<$size; $i++) {
                  if (is_array($cheapest)) {
                  if ($rates[$i]['cost'] < $cheapest['cost']) {
                  $cheapest = $rates[$i];
                  }
                  } else {
                  $cheapest = $rates[$i];
                  }
                  }

                  return $cheapest;
    }
  }

  function process_button($shipping) {
    $p = $this->getModule($shipping);
    return $p->process_button();
  }

  function get_error($shipping) {
    $p = $this->getModule($shipping);
    return $p->get_error();
  }

  function confirmation($shipping) {
    $p = $this->getModule($shipping);
    return $p->confirmation();
  }

  function specialOutput($shipping) {
    $p = $this->getModule($shipping);
    if(method_exists($p,'specialOutput')){
      return $p->specialOutput();
    }
  }

  function pre_confirmation_check($shipping) {
    $module = $this->getModule($shipping);
    if($module){
      $pre_check = $module->pre_confirmation_check();
      if($pre_check == true){
        return $module->validate_selection($module->selection(),$_POST);
      }else{
        return $pre_check;
      }
    }else {
      return false;
    }
  }

  public static function getShippingList($type='') {
    global $language;
    $shipping_directory =DIR_FS_3RMTLIB. DIR_WS_MODULES .'shipping/';
    $shipping_array = array();
    $shipping_list_str = '';
    if ($dh = @dir($shipping_directory)) {
      while ($shipping_file = $dh->read()) {
        if (!is_dir($shipping_directory.$shipping_file)) {
          if (substr($shipping_file, strrpos($shipping_file, '.')) == '.php') {
            $shipping_array[] = $shipping_file; 
          }
        }
      }
      sort($shipping_array);
      $dh->close();
    }
    $shipping_method_array = array();
    for ($i = 0, $n = sizeof($shipping_array); $i < $n; $i++) {
      $shipping_filename = $shipping_array[$i]; 
      require_once DIR_WS_LANGUAGES . $language . '/modules/shipping/' . $shipping_filename; 
      require_once $shipping_directory . $shipping_filename; 
      $shipping_class = substr($shipping_filename, 0, strrpos($shipping_filename, '.'));
     if (class_exists($shipping_class)) {
        $shipping_module = new $shipping_class; 
        $shipping_method_array[$shipping_class] = $shipping_module;
        $shipping_list_str[] = $shipping_module->title;
        $shipping_list_code[] = $shipping_module->code;
      }
    }
    self::$shipping_method_array = $shipping_method_array;
    self::$shipping_array = array($shipping_list_code,$shipping_list_str);

    if($type==SHIPPING_LIST_TYPE_HAIJI){
      return $shipping_list_str;
    }
    if($type==SHIPPING_LIST_TYPE_ROMAJI){
      return $shipping_list_code;
    }
    return array($shipping_list_code,$shipping_list_str);
  }
  public static function makeShippingListPullDownMenu($shipping_method = "",$params='') {
    //修改 变量名称
    if(empty(self::$shipping_array)){
      $shipping_text = self::getshippingList(); 
    }else{
      $shipping_text = self::$shipping_array;
    }
    $shipping_array = $shipping_text;
    //$shipping_list[] = array('id' => '', 'text' => '支払方法を選択してください');
    for($i=0; $i<sizeof($shipping_array[0]); $i++) {
      $shipping_list[] = array('id' => $shipping_array[0][$i],
                              'text' => $shipping_array[1][$i]);
    }
    return tep_draw_pull_down_menu('shipping_method', $shipping_list,
        $shipping_method,$params);
  }
  public static function changeRomaji($string,$res_type=''){
    //获取 支付方法列表
    if(empty(self::$shipping_array)){
      $shipping_text = self::getshippingList(); 
    }else{
      $shipping_text = self::$shipping_array;
    }
    $shipping_array_code = $shipping_text[0];
    $shipping_array_title = $shipping_text[1];
    //判断 返回信息
    if(preg_match("/^[a-zA-Z0-9_]*$/",$string)){
      //去掉 shipping_
      if(preg_match("/^shipping_/",$string)){
        $string = mb_substr($string,8,mb_strlen($string));
      }
      $return_type = "title";
      $shipping_array = $shipping_text[0];
    }else{
      $return_type = "code";
      $shipping_array = $shipping_text[1];
    }
    for($i=0; $i<sizeof($shipping_array); $i++) {
      if($string == $shipping_array[$i]){
        if($res_type){
          if($res_type=='code'){
            return $shipping_array_code[$i];
          }else if($res_type='title'){
            return $shipping_array_title[$i];
          }else{
            return false;
          }
        }else{
          if($return_type == 'code'){
            return $shipping_array_code[$i];
          }else if($return_type == 'title'){
            return $shipping_array_title[$i];
          }else{
            return false;
          }
        }
      }
    }

   }
  public function handle_calc_fee($shipping_method,$pid,$qty,$site_id){
    $s = $this->getModules($shipping_method);
    return $s->calc_fee($pid,$qty,$site_id);
  }
  
}
?>
