<?php
FLEA::loadClass("Controller_Base");
class Controller_ActManager extends Controller_Base{
	var $model_User;
	var $model_Act;
	/* @var $model_Act Model_Act */
	//	var $model_User;
	function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$view->template_dir = APP_DIR.DS."View".DS."Admin";
		$this->model_User = FLEA::getSingleton('Model_User');
		$this->model_Act = FLEA::getSingleton("Model_Act");
	}

	function actionIndex(){
		redirect(url("ActManager","List"));
	}
	/**
	 * 刷新权限表，当有新的controller 和 action加入时，通过此动作更新权限表，但是仅是更新，
	 * 之后还有对其进行设置 
	 *
	 */
	function actionFlushAuth(){
		$controllers = $this->directory_traverse(APP_DIR.DS."Controller");
		foreach ($controllers as $key=>$value){
			if($value=="BASE.php"){
				continue;
			} else {
				$controllerName = str_replace(".php","",$value);
				$acts[$controllerName]["actions"]=$this->getAction($value);
				$acts[$controllerName]["deny"]=RBAC_NULL;
				$acts[$controllerName]["allow"]=RBAC_NULL;

			}
		}
		$model_Act = FLEA::getSingleton('Model_Act');
		/* @var $model_Act Model_Act */
		$act_database = $model_Act->getAllAct();
		/**
		 * 先去除不存在的controller 和 action
		 */
		foreach ($act_database as $key=>$value){
			if(!array_key_exists($key,$acts)){
				//				echo $key;
				$this->model_Act->removeByPkv($key);
				unset($act_database[$key]); //去除不存在的controller
			}else {//存在这样的controller 要去掉不存在的action
				if(is_array($value['actions'])) {
					foreach ($value['actions'] as $key_action=>$value_action){
						if(!array_key_exists($key_action,$acts[$key]['actions'])){
							unset($act_database[$key]['actions'][$key_action]);
						}
					}
				}
			}
		}
		//		dump($act_database,"asdfasdf");
		/**
		 * 写回数据库，完成时，数据库内没有不存在的controller 和 action
		 */
		foreach ($act_database as $key=>$value){
			$this->model_Act->setAct($key,$value);
		}
		$act_database = $this->model_Act->getAllAct();
		foreach ($acts as $controller=>$act) {
			if(!@array_key_exists($controller,$act_database)){ //如果文件取得有，但不存在于数据库中，加到数据库里
				$act_database[$controller]=$act;
			}else { //如果数据库里有，查看action里是否有
				if(count($act['actions'])){
					foreach ($act['actions'] as $action=>$action_act){
						if(!@array_key_exists($action,$act_database[$controller]['actions'])){//如果action 不存在，加入
							$act_database[$controller]['actions'][$action] = $action_act;
						}
					}
				}
			}
		}
		/**
		 * 写回数据库，完成时，数据库内和文件同步
		 * 
		 */
		foreach ($act_database as $key=>$value){
			$this->model_Act->setAct($key,$value);
		}
		$act_database = $this->model_Act->getAllAct();
		chmod(APP_DIR.DS."Config".DS."Global.php",0777);
		$this->_goBack();
	}

	/**
	 * 列出所有权限 控制表
	 *
	 */
	function actionList() {
		$this->_setBack();
		$acts = $this->model_Act->getAllAct();
		//		dump($acts);
		$viewData = array(
		"acts"=>$acts,
		);
		$this->executeView("actList.html",$viewData);
	}
	/**
	 * 编辑权限
	 *
	 */
	function actionAuthEdit(){
		if(isset($_GET['controlle'])){
			$controller = htmlspecialchars($_GET["controlle"]);
		}else {
			echo "404";
			exit;
		}
		if(isset($_GET['actio'])){
			$action = htmlspecialchars($_GET["actio"]);
		}
		$acts = $this->model_Act->getAct($controller);
		if(isset($action)){//只有controller,仅查出controller
			$acts = $acts['actions'][$action];
		}
		if($acts){
			$viewData = array(
			"acts"=>$acts,
			"controller"=>$controller,
			"action"=>$action,
			);
			$this->executeView("authEdit.html",$viewData);
		}
	}

	function actionAuthEditDo(){
		if(isset($_GET['controlle'])){
			$controller = htmlspecialchars($_GET["controlle"]);
		}else {
			echo "404";
			exit;
		}
		if(isset($_GET['actio'])){
			$action = htmlspecialchars($_GET["actio"]);
		}
		//echo $action;echo $controller;
		if(isset($_POST['denyList'])){
			$denyList = htmlspecialchars($_POST["denyList"]);
		}
		if(isset($_POST['allowList'])){
			$allowList = htmlspecialchars($_POST["allowList"]);
		}
		$act = array("allow"=>(htmlspecialchars($_POST["allow"])=="UESR_DEFINE")?$allowList:htmlspecialchars($_POST["allow"]),"deny"=>(htmlspecialchars($_POST["deny"])=="UESR_DEFINE")?$denyList:htmlspecialchars($_POST["deny"]));
		$act_database = $this->model_Act->getAct($controller);
		if(isset($action)){
			$act_database['actions'][$action]=$act;
		}else{
			$act_database = array_merge($act_database,$act);
		}
		$this->model_Act->setAct($controller,$act_database);
		$this->_goBack();
	}

	function directory_traverse($dir){

		if(is_dir($dir)){
			if($dir_handle = opendir($dir)){

				while (false !== ($file_name = readdir($dir_handle)) ){

					$file_type = filetype($dir.'/'.$file_name);

					if($file_name=='.' or $file_name =='..'){
						continue;
					}
					else{
						if('dir' == $file_type){
							continue;
							//directory_traverse($dir.'/'.$file_name);
						}
						elseif('file' == $file_type){
							$filenames[] = ($file_name);
						}
					}

				}

			}
			return $filenames;
		}
		return false ;

	}
	/**
	 * 从controller 文件中读取所有action
	 *
	 * @param unknown_type $file_name
	 * @return unknown
	 */
	function getAction($file_name){
		$dir = APP_DIR.DS."Controller";
		$file_name = $dir.DS.$file_name;
		if(is_file($file_name)){
			$file = file_get_contents($file_name);
			preg_match_all("'function\saction([^(]*)'",$file,$actions);
			//			return $actions[1];
			foreach ($actions[1] as $key=>$value) {
				$act[$value]=array("allow"=>RBAC_EVERYONE.',',"deny"=>RBAC_NULL.',');
			}
			return $act;
		}
		return false;
	}
	function actiongetActToFile(){
		$controllers = $this->directory_traverse(APP_DIR.DS."Controller");
		foreach ($controllers as $key=>$value){
			if($value=="BASE.php"){
				continue;
			} else {
				$controllerName = str_replace(".php","",$value);
				$acts[$controllerName]["actions"]=$this->getAction($value);
				if(!$acts[$controllerName]["actions"])
				unset($acts[$controllerName]["actions"]);
				$acts[$controllerName]["deny"]=RBAC_NULL.',';
				$acts[$controllerName]["allow"]=RBAC_NULL.',';

			}
		}
		$str_acts = dump($acts,'',true);
		$str_acts= str_replace(array(")","[","]","<pre>","</pre>","&gt; "),array("),","\"","\"","","",">"),$str_acts);
		$str_acts = substr($str_acts,0,strlen($str_acts)-4);
		
		//将文件写入
		
		
	}




}