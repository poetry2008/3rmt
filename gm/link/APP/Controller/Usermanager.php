<?php
FLEA::loadClass("Controller_Base");
class Controller_UserManager extends Controller_Base{
	var $model_User;
	/* @var $model_User Model_User */
	var $model_Role;
	/* @var $model_User Model_Role */
	function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$view->template_dir = APP_DIR.DS."View".DS."Admin";
		$this->model_User = FLEA::getSingleton('Model_User');
		/* @var $model_User Model_User */
		$this->model_Role = FLEA::getSingleton('Model_Role');
		/* @var $model_Role Model_Role */

	}
	/**
	 * 用户管理启始页　显示所有用户
	 *
	 */
	function actionIndex(){
//		dump($_SESSION);
		$this->_setBack();
		$model_User = FLEA::getSingleton('Model_User');
		/* @var $model_User Model_User */
		//		$users = $model_User->findAll();
		$viewData = array(
		"users"=>$model_User->findAll(array(array("username","admin","!="))),
		);
		$this->executeView("userList.html",$viewData);

	}


	/**
	 * 显示添加用户页
	 *
	 */
	function actioncreate(){

		$viewData = array();
		$this->executeView("userAdd.html",$viewData);
	}

	/**
	 * 添加用户动作 添加用户并添加同名用户组
	 *
	 */
	function actionAddUserDo(){
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);
		//		dump($username,$password);
		//$this->model_User->create()
		$row = array(
		"username"=>$username,
		"password"=>$password,
		);
		if($this->model_User->create($row)){
			echo _T('success');
		}else {
			echo _T('failed');
		}
	}
	/**
	 * 显示用户权限，要集合所有控制器在内如果想做更细再重新做说明
	 * 要读取当前人的权限，重新整理后再写回
	 * 读取权限很费力，可以先不做，只重设
	 *
	 */
	function actionUserAuthority(){
		$user_id = htmlspecialchars($_GET['user_id']);
		$this->model_User->enableLinks();
		$user = $this->model_User->find($user_id);
		$roles_user = $user['roles'];
		$roles = $this->model_Role->findAll();
		foreach ($roles as $key=>$value) {
			if (in_array($value,$roles_user)){
				$roles[$key]['checked']="checked";
			}
		}
		//		dump($roles,"roles");
		$viewData = array(
		'roles'=>$roles ,
		'user'=>$user,
		);

		$this->executeView("userAuthority.html",$viewData);
	}
	function actionUserAuthorityDo(){
		$user_id = h($_POST['user_id']);
		$roles = $_POST['roles'];
		//dump($roles);
		foreach ($roles as $value){
			$role[]=array("role_id"=>$value);
		}
		$row = array(
		"user_id"=>$user_id,
		"roles"=>$role,
		);
		//dump($row);
		if($this->model_User->update($row)){
			$this->addMsg(_T('success'));
		}else {
			$this->addMsg(_T('failed'));
		}
		$this->_goBack();
	}


	function actionSave(){
		$username = h($_POST['username']);
		$password = h($_POST['password']);
		$user_id = h($_POST['user_id']);
		$row = array(
		"user_id"=>$user_id?$user_id:null,
		"username"=>$username,
		"password"=>$password,
		"roles"=>array(
		"role_id"=>2,
		),
		);
		if($this->model_User->save($row)){
			$this->addMsg(_T('success'));
		}else {
			$this->addMsg(_T('failed'));
		}
		$this->_goBack();

	}

	/**
	 * 遍历文件夹
	 *
	 * @param unknown_type $dir
	 * @return unknown
	 */
	function actionUserRemove(){
		$user_id = h($_GET['user_id']);
		if($this->model_User->removeByPkv($user_id)){
			$this->addMsg(_T('success'));
		}else {
			$this->addMsg(_T('failed'));
		}
		$this->_goBack();
	}
	function actionUserEdit(){
		$userid = h($_GET['user_id']);
		if($user = $this->model_User->find($userid)){
			if($user['username']=="admin"){
				$this->addMsg(_t("msg_usernotexist"));
				$this->_goBack();
			}//如果是管理员 则返回
			unset($user['password']);
//			dump($user);
			$viewData = array(
			"user"=>$user,
			);
			$this->executeView("userAdd.html",$viewData);
		} else {
			$this->addMsg(_T('no_user'));
			$this->_goBack();
		}
	}
	function actionResetPassword(){
		$user_id = h($_GET['user_id']);
		$model_User = FLEA::getSingleton('Model_User');
		/* @var $model_User Model_User */
		$user = $model_User->find($user_id);
		if(!$user or $user['username']=='admin'){
			$this->addMsg(_t("msg_usernotexist"));
			$this->_goBack();
		}
		if($model_User->updatePasswordById($user_id,"123456")){
			$this->addMsg(_T("success"));
		}else{
			$this->addMsg(_T("faild"));
		}
		$this->_goBack();
	}




}
