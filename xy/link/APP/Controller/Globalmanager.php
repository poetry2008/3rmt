<?php
FLEA::loadClass("Controller_Base");
class Controller_GlobalManager extends Controller_Base {
	var $global;
	var $path;
	var $model_Global;
	function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$this->model_Global = FLEA::getSingleton('Model_Global');
		$this->path   = APP_DIR . '/Config/Global.php';
		//$this->global = include($this->path);

	}

	/**
    * 列出全局变量
    */
	function actionIndex(){
		$this->global = $this->model_Global->findAll();
        ?>
	Global Manager
	<hr>
<form action='<?=url('globalmanager','add')?>' method='get'>
添加<input name='num' value='1'>个全局变量<input type='submit'><br>
</form>
<form action='<?=url('globalmanager','update')?>' method='post'>
<? foreach($this->global as $g){?>
    <?=$g['name']?>
    <input name='<?=$g['name']?>[comment]' value='<?=$g['comment']?>' />
    <input name='<?=$g['name']?>[value]'   value='<?=$g['value']?>'   />
    <a href='<?=url('globalmanager','deletedo',array('name'=>$g['name']))?>'>delete</a><br>
<? }?>
<hr>
<input type='submit' >
</form>
        <?
	}

	/**
    * 修改全局变量
    */
	function actionUpdate()
	{
		$infArr = $_POST;
		//dump($infArr);
		foreach($infArr as $key=>$val)
		{
			//if($this->global[$key]['value']!==$val['value'] or $this->global[$key]['comment']!==$val['comment']){
			$this->model_Global->_set($key,$val['value'],$val['comment']);
			//}
		}
		$globalNew = $this->db2Arr();
		return $this->arr2File($globalNew,$this->path);
	}

	/**
    *
    */
	function actionAdd()
	{
		$num = isset($_GET['num'])?$_GET['num']:1;
        ?>
<form action='<?=url('globalManager','addDo')?>' method='post'>
<? for($i=0;$i<$num;$i++){?>
    名称<input name='<?=$i?>[name]' />
    值<input name='<?=$i?>[value]' />
    注释<input name='<?=$i?>[comment]' /><br>
<? }?>
    <hr>
    <input type='submit' />
</form>
        <?
	}

	/**
    *
    */

    function actionAddDo()
    {
        $newInf = $_POST;
        dump($newInf);
        foreach($newInf as $key=>$val)
        {
            //忽略是否已存在
            echo $this->model_Global->_create($val['name'],$val['value'],$val['comment']);
            //echo("\$this->model_Global->_set(\$val['name'],\$val['value'],\$val['comment']);");
        }

	}

    /**
    *
    */
    function actionDeleteDo()
    {
        $name = $_GET['name'];
        echo $this->model_Global->removeByPkv($name);
    }

    /**
    *
    */
	function db2Arr()
	{
		$globals = $this->model_Global->findAll();
		//dump($globals);
		return $globals;
	}

	/**
    * 重新生成配置文件
    */
	function arr2File($arr,$path)
	{
		$str  = "<?php\n";
		$str .= "return array(\n";
		foreach($arr as $key=>$val)
		{
			$str .= "\t'".$val['name']."' => '".$val['value']."',//".$val['comment']."\n";
		}
		$str .= ");";
		return file_put_contents($path,$str);
	}

}