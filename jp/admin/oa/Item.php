<?php
/**
 * RMT 后台订单问题测试文件
 *
 * @file   Item.php
 * @author bobhero <bobhero.chen@gmail.com>
 * @date   Tue Apr 26 14:12:46 2011
 */

/** 
 * 定义Item接口 
 * 
 * @author bobhero <bobhero.chen@gmail.com>
 * 
 */
interface Item
{
  /** 
   * 输出表单元素
   * 
   * @return string;
   */
  function render();
  
  /** 
   * 取得类型
   *
   * @return string
   */
  //  function getType();
  /** 
   * 取得值
   *
   * @return string 
   */
  //  function getValue();
  
}
class HM_Form
{
  function __contruct($option)
  {
    $this->id = $option->id;
    if (is_array($option['subitem'])){
      foreach ($option['subitem'] as $key=>$subitem{
	  if(!is_a($subitem,'HM_Form_Group')){
	    
	  }
      }
    }
  }
  var $items = array();
  function render($actionPage)
  {
    echo "<form action='$actionPage'>";
    foreach($this->items as $key=>$item)
      {
	$item->render();
      }
    echo "</form>";
  }
}

/** 
 * 元件基本类
 * 
 * @author bobhero <bobhero.chen@gmail.com>
 */
class HM_Item_Basic extends DbRecord
{
  public static $id_array = array();
  public static $sid_array = array();
  public static $item_count = 0;
  var $id ='';				/* id 从数据库获得 */  
  var $defaultValue = '';		/* 默认值 */
  var $title = '';			/* 名字日文 */
  var $isMulti = false;			/* 是否包括子项目 */
  var $required = false;
  var $cName ='';
  var $name = '';
  var $sid = '';
  function __construct($option_array)
  {
    $this->setOption($option_array);
    if (method_exists($this,'init')){
      $this->init();
    }
  }
  function getRandomId()
  {
    $id = rand(1000,999999);
    while(in_array($id,self::$id_array)){
      $id = rand(1000,999999);
    }
    self::$id_array[] = $id;
    return $id;
  }
  function getRandomSID()
  {
    $id = rand(1000,999999);
    while(in_array($id,self::$sid_array)){
      $id = rand(1000,999999);
    }
    self::$sid_array[] = $id;
    return 's'.$id;
  }

  function setOption($option_array)
  {
    if (is_array($option_array)){
      foreach($option_array as $key=>$value){
	$this->$key = $value;
      }
    }
    /* 如果没有设置ID说明是临时创建的，需要给个随机ID */
    if (empty($this->id)){
      $this->id = $this->getRandomId();
    }
    if (empty($this->name)){
      $this->name = $this->id;
    }
    $this->sid =  $this->getRandomSID();
  }
  /** 
   * 实现接口方法
   * 
   * @return value
   */
  function getValue()
  {
  }

  /** 
   * 实现接口方法
   * 
   * @return type
   */

  function getType()
  {
  }
}



/** 
 * 日期形式的如  [ ] __月__日
 * 
 * 
 * 
 */
/*class HM_Item_Date extends HM_Item_Basic implements Item
{

}
*/


class HM_Item_CheckBox extends HM_Item_Basic implements Item
{
  function render()
  {
    echo "$this->title <input id=$this->sid type='checkbox' name='item_$this->id' value='$this->defaultValue'>\n";
  }
}

class HM_Item_Basic_Multi extends HM_Item_Basic
{
  var $isMulti = true;		/* 包括子项目 */  
  function getChildren()
  {
    
  }
}
class HM_Item_Group 
{
  
}


class HM_Item_Text extends HM_Item_Basic  implements Item
{
  function render()
  {
    echo "$this->title <input id=\"$this->sid\" type='text' name='item_$this->id'/>\n";
  }
}

/** 
 * 
 * 按钮组
 * 
 * 
 */

class HM_Item_Radio  extends HM_Item_Basic
{
  /** 
   * 循环children 输出数据
   * 
   * @return void
   */
  function render()
  {

      echo "<input id='",$this->sid,"' type='radio' value='".$this->cValue."' name='item_".$this->name."' ";
      if($this->checked){
	echo "checked";
      }
      echo " /> ",$this->cName."\n";

  }
    
}
/** 
 * Ync YES NO|text
 * 
 * 格式如  ()yes ()no | text
 * 
 */
class HM_Item_Ync extends HM_Item_Basic_Multi implements Item
{
  var $childY ;
  var $childN ;
  var $childCheckbox ;
  var $option;
  function init()
  {
    $this->childY   = new HM_Item_Radio($this->option['radioY']);
    $this->childN   = new HM_Item_Radio($this->option['radioN']);
    $this->childCheckbox = new HM_Item_Checkbox($this->option['checkBoxChild']);
  }
  function render()
  //  function render($option_array)
  {
    echo $this->title;
    $this->childY->render();
    $this->childN->render();
    $this->childCheckbox->render();
    $this->renderScript();
  }
  /** 
   * 输出脚本 默认已经有JQUERY了
   * 
   * 
   * 
   */
  function renderScript()
  {
    echo $this->childY->sid;
    echo "|";
    echo $this->childN->sid;
    echo $this->childCheckbox->sid;
    echo "<script type='text/javascript' >";
    echo "$('#".$this->childCheckbox->sid."').change(function(){";
    echo "if($(this).attr('checked')){";
    echo "$('#".$this->childN->sid."').attr('checked','true');";
    echo "   }";
    echo "});";
    echo "$('#".$this->childY->sid."').change(function(){";
    //    echo "if($(this).attr('checked')){"
    echo "$('#".$this->childCheckbox->sid."').removeAttr('checked');";
    //    echo "   }";
    echo "});";
    echo "</script>";
  }
  
}
$testData = array(
		  "id"=>1,
		  "type"=>'Ync',
		  "title"=>'测试的下个东西',
		  "required"=>1,
		  "option"=>array(

				  'radioY'=>array(
						  "cValue"=>'YES',
						  "cName"=>'YES',
						  "checked"=>true,
						  "name"=>'Ync_1_radio',
						  ),
				  'radioN'=>array(
						  "cValue"=>'NO',
						  "cName"=>'NO',
						  "name"=>'Ync_1_radio',
						  "checked"=>false,
						  ),

				  "checkBoxChild"=>array("title"=>'why','name'=>'Ync_1_Checkbox')
				  ),
		  );
		  

$id = 44;
?>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<body>
<?php
$itemC = new HM_Item_Ync($testData);
//var_dump($itemC);
$itemC->render();
?>
</body>
</html>
