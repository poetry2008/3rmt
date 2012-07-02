<?php
FLEA::loadClass("Controller_Base");
class Controller_Setseo extends Controller_Base{
    var $setseo;
    var $path;
    var $model_Setseo;
    function __construct(){
		parent::__construct();
        $this->model_Setseo = FLEA::getSingleton('Model_Setseo');

	}

    /**
    * 列出全局变量
    */
        function actionIndex()
        {
        $seo = $this->db2Arr();
        $global = &FLEA::getSingleton('Model_Global');
        $dir = $global->find('name = "set_new_dir"');
        $viewData = array(
            'seo' => $seo,
            'host_url' => $_SERVER['HTTP_HOST'].'/'.$dir['value'].'/',
        );
        $this->executeView("Admin/setseoIndex.html",$viewData);
	}

    /* 这个是添加方法 使用的模板和 edit基本一样
    function actionAdd(){
      $this->executeView("Admin".DS."setseoAdd.html",$viewData);
    }
    */

    function actionEdit(){
      $setseos = $this->model_Setseo->find("id ='".$_GET['id']."'");
        $viewData = array(
            'seo' => $setseos,
        );
      $this->executeView("Admin".DS."setseoEdit.html",$viewData);
    }

    /**
    * 修改全局变量
    */
    function actionAddDo(){
      $infArr = $_POST;
      $this->model_Setseo->save($infArr);
      redirect(url('setseo','index'));
    }

    /**
    *
    */
    function db2Arr()
    { 
        $setseos = $this->model_Setseo->findAll();
        //dump($setseos);
        return $setseos;
    }


}
