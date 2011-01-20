<?php
FLEA::loadClass("Controller_Base");
class Controller_Global extends Controller_Base{
    var $global;
    var $path;
    var $model_Global;
    function __construct(){
		parent::__construct();
        $this->model_Global = FLEA::getSingleton('Model_Global');
        $this->path   = APP_DIR . '/Config/Global.php';
        $this->global = include($this->path);

	}

    /**
    * 列出全局变量
    */
	function actionIndex(){
         $arr = $this->db2Arr();
         foreach($arr as $v){
           $this->global[$v['name']] = $v['value'];
         }
        $viewData = array(
            'global'=>$this->global,
        );
        $this->executeView("Admin/globalIndex.html",$viewData);
	}

    /**
    * 修改全局变量
    */
    function actionUpdate()
    {
        $infArr = $_POST;
        foreach ($infArr as $k => $v){
        $data = array(
             'name' => $k,
             'value' => $v
            );
        $this->model_Global->update($data);
        }
        foreach($infArr as $key=>$val)
        {
            if($this->global[$key]!==$val){
                $this->model_Global->_setValue($key,$val);
            }
        }
        $globalNew = $this->db2Arr();
        $this->addMsg(_T($this->arr2File($globalNew,$this->path)?'global_update_success':'global_update_failed'));
        redirect(url('global','index'));
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
        foreach($arr as $val)
        {
            $str .= "\t'".$val['name']."' => '".$val['value']."',//".$val['comment']."\n";
        }
        $str .= ");";
        return file_put_contents($path,$str);
    }

}
