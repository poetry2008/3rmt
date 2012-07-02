<?php
//需要两个图片来表示升序和降序  需要放在固定位置
//需要一个视图表显示  需要放在固定位置
require_once('Smarty/Smarty.class.php');
class Class_Table{
	//表格对象的配置信息,以及部分默认值
	public $config=array(
		'no'=>false,
		'multi'=>false,
		'add'=>false,
		'del'=>false,
		'edit'=>false,
		'show'=>false,
		'id'=>'id',
		'categoryList'=>null,
		'return'=>false
	);		//对象的配置
	public $controller;	//控制器
	public $action;		//动作

	//以下*表示类生成时的初始配置
	public $name;			//表格的助记,尽量以短的英文形式表示 *
	public $title;			//表格和页面的标题	*

	public $no;			//是否允许行号	*
	public $multi;			//是否允许多选 	*
	public $categoryList;	//分类的列表		*

	public $return;			//返回的地址	*
	public $add;			//是否允许添加	*
	public $del;			//是否允许删除	*
	public $edit;			//是否允许编辑	*
	public $show;			//是否允许查看	*

	public $operate;		//全表操作		*

	public $id;			//ID列的字段名	*
	public $fields;		//字段列表		*

	public $defaultSortName;		//默认的排序列 *
	public $defaultSortOrder;		//默认的排序顺序 *

	public $ascImgPath='images/Table/asc.gif';		//升序图标
	public $descImgPath='images/Table/desc.gif';		//降序图标

	public $pageNo;			//当前页号
	public $pageSize;		//页面大小
	public $sortName;		//排序字段名称
	public $sortOrder;		//升序还是降序
	public $category;		//当前分类

	public $smarty;		//用到的smarty对象		*

	/**
	 * 构造方法,为对象的一些通用属性赋值
	 *
	 */
	public function __construct($config){
		$this->controller=$this->getValue('controller');	//本表格的控制程序在哪个控制器
		$this->action=$this->getValue('action');	//本表格的列表动作,其它动作以此为前缀

		foreach($config as $key=>$value)
			$this->config[$key]=$value;

		$this->smarty=$this->config['smarty'];
		$this->name=$this->config['name'];		//本对象的助记符,要求每个表格唯一,以便Cookie记忆
		$this->title=$this->config['title'];		//标题

		$this->no=$this->config['no'];			//是否显示序号
		$this->multi=$this->config['multi'];		//是否允许多选
		$this->categoryList=$this->config['categoryList'];//分类

		$this->return=$this->config['return'];	//返回的地址
		$this->add=$this->config['add'];			//是否允许增加
		$this->del=$this->config['del'];			//是否允许删除
		$this->edit=$this->config['edit'];		//是否允许编辑
		$this->show=$this->config['show'];		//是否允许查看

		$this->operate=$this->config['operate'];	//多选结果的操作 包括title,action

		$this->id=$this->config['id'];			//ID字段
		$this->fields=$this->config['fields'];	//要显示的字段,包括name=>alias,title,type

		$this->pageNo=$this->getValue('pageNo_'.$this->name);			//页码
		if(!$this->pageNo)$this->pageNo=0;

		$this->pageSize=$this->getValue('pageSize_'.$this->name);		//页大小
		if(!$this->pageSize)$this->pageSize=10;

		$this->sortName=$this->getValue('sortName_'.$this->name);	//排序列
		if(!$this->sortName)$this->sortName=$this->config['defaultSortName'];

		$this->sortOrder=$this->getValue('sortOrder_'.$this->name);		//排序方向
		if(!$this->sortOrder)$this->sortOrder=$this->config['defaultSortOrder'];

		$this->category=$this->getValue('category_'.$this->name);		//分类

		//echo 'sortName:'.$this->sortName.'  sortOrder:'.$this->sortOrder.'<br/>';
	}

	//为外部程序提供,输出当前的分页,排序,分类相关信息
	public function getListingConfig(){
		return array(
			'pageNo'=>$this->pageNo,
			'pageSize'=>$this->pageSize,
			'sortName'=>$this->sortName,
			'sortOrder'=>$this->sortOrder,
			'category'=>$this->category
		);
	}

	/**
	 * 显示表格'
	 * 入口参数是一个数组
	 * 	count	总记录数
	 *  data	本页要显示的数据
	 */
	public function listing($data){
		$dataCount=$data['count'];		//总记录数
		$data=$data['data'];			//当前要显示的记录
		$pageCount=intval(($dataCount-1)/$this->pageSize)+1;  //计算总页数
		//echo "dataCount:$dataCount  pageSize:$this->pageSize pageNo:$this->pageNo pageCount:$pageCount <br/>";

		//$this->smarty=new Smarty();
		$this->smarty->assign('dataCount',$dataCount);
		$this->smarty->assign('data',$data);
		$this->smarty->assign('pageCount',$pageCount);
		$this->smarty->assign('config',$this);
		//$this->smarty->template_dir='APP/View/Table';

		if(isset($_SESSION['msg'])){
			$this->smarty->assign('msgs',$_SESSION['msg']);
			unset($_SESSION['msg']);
		}

		$this->smarty->caching=false;
		//$this->smarty->display('listing.tpl');
	}

	//获得一个POST或者GET或者Cookie的值,如果不存在,返回false
	private function getValue($name){
		if(isset($_POST[$name])) return trim($_POST[$name]);
		if(isset($_GET[$name]))return trim($_GET[$name]);
		if(isset($_COOKIE[$name]))return trim($_COOKIE[$name]);
		return false;
	}
}

?>
