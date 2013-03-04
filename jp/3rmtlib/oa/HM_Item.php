<?php
class HM_Item extends DbRecord
{
  var $type;
  var $id;
  var $name;
  var $group_id;
  var $form_id;

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
    parent::__construct();
  }

/* -------------------------------------
    功能: 初始化 
    参数: 无   
    返回值: 对象(obj) 
------------------------------------ */
  function init()
  {
    $this->getInstance();
    return $this;
  }

/* -------------------------------------
    功能: 输出元素的html和js 
    参数: $m(boolean) 标识   
    返回值: 无 
------------------------------------ */
  function render($m=false)
  {
    $this->instance->render($m);
    if (method_exists($this->instance,'renderScript')){
      $this->instance->renderScript($m);
    }
  }

/* -------------------------------------
    功能: 获取实例对象 
    参数: 无   
    返回值: 实例对象(obj) 
------------------------------------ */
  function getInstance()
  {
    if(!$this->instance){
    $instanceClass = "HM_Item_".ucfirst($this->type);
    require_once $instanceClass.".php";
    $this->instance = new $instanceClass();
    $this->instance->name = $this->name;
    $this->instance->id= $this->id;
    $this->instance->group_id= $this->group_id;
    $this->instance->form_id= $this->form_id;
    $this->instance->init($this->option);
    }
    return $this->instance;
  }

/* -------------------------------------
    功能: 格式化信息 
    参数: $option(string) 信息   
    返回值: 无 
------------------------------------ */
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }

/* -------------------------------------
    功能: 载入默认值 
    参数: $order_id(string) 订单id   
    参数: $form_id(string) 表单id   
    参数: $group_id(string) 组id   
    返回值: 无 
------------------------------------ */
  function loadDefaultValue($order_id,$form_id,$group_id)
  {
    //        var_dump($order_id,$form_id,$group_id);

    //echo $order_id,'form',$form_id,'group',$group_id;
    define("TABLE_OA_FORMVALUE",'oa_formvalue');
    $sql = 'select value from '.TABLE_OA_FORMVALUE.' where ';
    $sql .= ' orders_id="' .$order_id.'"';
    $sql .= 'and  group_id="' .$group_id.'"';
    $sql .= 'and  form_id="' .$form_id.'"';
    $sql .= 'and  item_id="' .$this->id.'"';
    $result = (tep_db_fetch_array(tep_db_query($sql)));
    if(!$result){
    //如果转变了表单类型会找不到数据
      $sql = 'select * from '.TABLE_OA_FORMVALUE.' where ';
      $sql .= ' orders_id="' .$order_id.'"';
      $sql .= 'and  group_id="' .$group_id.'"';
      $sql .= 'and  item_id="' .$this->id.'" order by id desc limit 1';
      $result = (tep_db_fetch_array(tep_db_query($sql)));
      if($result){
      //如果有结果  把这条数据的 form_id 修改成现在的form_id
        $sql = 'update '.TABLE_OA_FORMVALUE .' set form_id="'.$form_id.'"'.' where id = '.$result['id'] ;
        tep_db_query($sql);
      }
    }


    if ($result){
    $this->instance->order_id = $order_id;
    $this->instance->loadedValue = $result['value'];
    $this->instance->loaded = true;
    }else {
      if (method_exists($this->instance,'initDefaultValue')){
        $this->instance->initDefaultValue($order_id,$form_id,$group_id);
      }
    }
  }
}
