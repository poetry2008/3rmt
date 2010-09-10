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
        $arr = $this->db2Arr();
        var_dump($arr);
        $viewData = array(
            'seo' => $arr,
        );
        $this->executeView("Admin/setseoIndex.html",$viewData);
	}
    function actionAdd(){
      $this->executeView("Admin".DS."setseoAdd.html",$viewData);
    }

    function actionEdit(){
      $setseos = $this->model_Setseo->findAll("action ='".$_GET['act']."'");

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
