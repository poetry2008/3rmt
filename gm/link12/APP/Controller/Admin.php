<?php
FLEA::loadClass("Controller_Base");
class Controller_Admin extends Controller_Base{
	function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$view->template_dir = APP_DIR.DS."View".DS."Admin";
	}


	function actionIndex(){
		//		dump($_SESSION);
		$this->_setBack();
		//		$this->addMsg("heloo");
		if(isset($_SESSION['CMS'])) {
			$model_Class = FLEA::getSingleton('Model_Class');
			/* @var $model_Class Model_Class */
			$count_class = $model_Class->findCount();
			$model_Site = FLEA::getSingleton("Model_Site");
			$count_site = $model_Site->findCount();
			$count_each_site = $model_Site->findCount("state='1' or is_recommend ='1'");
			$model_Submit = FLEA::getSingleton('Model_Submit');
			/* @var $model_Submit Model_Submit */
			$count_Submit = $model_Submit->findCount();
			//			dump($_SESSION);
			$viewData = array(
			"count_class"=>$count_class,
			"count_site"=>$count_site,
      "count_each_site"=>$count_each_site,
			"count_submit"=>$count_Submit,
			'username'=>$_SESSION['CMS']['USERNAME'],
			);
			$this->executeView("main.html",$viewData);
		}else {
			$viewData = array();
			$this->executeView("login.html",$viewData);
		}
	}

	function actionLogin(){
		$username=h($_POST['username']);
		$password=h($_POST['password']);
		$code = $_POST['imgcode'];
		$imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
		//如果验证码不正确
		//	$objResponse->alert($value);
		if (0&!$imgcode->check($code)) {
			$this->addMsg(_T("login_img_wrong"));
						$this->_goBack();
						exit;
		}

		$model_User = FLEA::getSingleton('Model_User');
		/* @var $model_User Model_User */
		if($model_User->login($username,$password)){
			//登录成功
			$this->addMsg(_T('login_success'));
		}else {
			//登录不成功
			$this->addMsg(_T('login_wrong'));
		}

		redirect(url("admin"));

	}
	function actionLogout(){
		$rbac = & FLEA::getSingleton('FLEA_Com_RBAC');
		/* @var $rbac FLEA_Com_RBAC */
		$rbac->clearUser();
		redirect(url("admin"));
	}
	/**
	 * 生成验证码
	 *
	 */
	function actionImgCode(){
		$imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
		$imgcode->clear();
		$length = FLEA::getAppInf("imgcode_length");
		$imgcode->image();
	}
	/**
	 * 更改验证码
	 *
	 */
	function actionChangeCode(){
		$imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
		$imgcode->clear();
		$this->_goBack();
	}

	function actionUpdatePassword(){
		$this->_setBack();
		$viewData = array(
		//"user_"=>$_SESSION['CMS']['USERID'],
		);
		$this->executeView("updatePassword.html",$viewData);

	}
	function actionUpdatePasswordDo(){
                $model_User = FLEA::getSingleton('Model_User');
		$user_id = $_SESSION['CMS']['USERID'];
		$oldpwd = h($_POST['oldpwd']);
		$newpwd = h($_POST['newpwd']);
		$repwd = h($_POST['repwd']);
		if($newpwd!=$repwd){
			$this->addMsg(_T("msg_password_notmach"));
			$this->_goBack();
		}else {
			if($user = $model_User->find($user_id)){
				if($model_User->checkPassword($oldpwd,$user['password'])){
					if($model_User->updatePassword($user['username'],$newpwd)){
						$this->addMsg(_T("msg_password_success"));
					}else {
						$this->addMsg(_T("msg_password_faild"));
					}
				}else {
					$this->addMsg(_T("msg_password_pwdwrong"));
				}
			}else {
				$this->addMsg(_T("msg_password_deled"));
			}
		}
		$this->_goBack();
	}
}
