<?php
FLEA::loadClass("Controller_Base");
class Controller_Custom extends Controller_Base{
	var $model_Submit;
	function __construct(){
		parent::__construct();
		$this->model_Submit = FLEA::getSingleton('Model_Submit');
	}

	function actionIndex(){

    }

    /**
     *
     */
    function actionSave()
    {
        //dump($_POST,'post');
        //dump($_COOKIE,'cookie');
        for($i=0;$i<10;$i++){
            setcookie('sites['.$i.'][url]',$_POST['s'][$i]['url'],null,'/');
            setcookie('sites['.$i.'][name]',$_POST['s'][$i]['name'],null,'/');
        }
        setcookie('img',$_POST['img'],null,'/');
        redirect(url());
    }

    /**
     *
     */
    function actionClearCookie()
    {
        dump($_COOKIE,'cookie');
        for($i=0;$i<10;$i++){
            setcookie('sites['.$i.'][url]','',null,'/');
            setcookie('sites['.$i.'][name]','',null,'/');
        }
    }



}

