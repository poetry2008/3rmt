<?php
class Controller_Base extends FLEA_Controller_Action
{
	/**
     * 初始化项目设置
     * 1.用_load_language 重新设置语言
     *
     */
	function __construct(){
		$this->_load_language();
	}

	/**
     * 如果用户没有强行指定用其它语种
     * 取得当前用户浏览器所用第一语言，对应设置为默认语言
     * 否则按用户强行指定语种显示
     */
	function _load_language(){
		//如果用户没有设置自己的语言
          /*
		if(!isset($_SESSION['userLanguage'])) {
			$langs = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			switch (strtolower($langs[0])) {
				case "zh-cn":
					$language = "chinese-utf8";
					break;

				case "ja":
                          case 'ja-jp':
					$language = "japaness-utf8";
					break;
				default:
					$language = "en-utf8";
			}
		} else {
			$language = $_SESSION['userLanguage'];
		}
                */
                $language = "japaness-utf8";
		FLEA::setAppInf("defaultLanguage",$language);
		load_language("ui");
	}
	function executeView($tpl,$viewData){
		if(isset($_SESSION['msg'])){
			$viewData['msgs']=$_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		/**
		 * 初始化菜单
		 */
		$menu = &FLEA::loadFile('Config_Menu.php');
		$dispatcher =& $this->_getDispatcher();

		foreach ($menu as $item) {
			$controllerName = $item[1];
			$actionName = isset($item[2]) ? $item[2] : $defaultAction;
			if (!$dispatcher->check($controllerName, $actionName)) { continue; }
			$menuOut[] = array("url"=>url($controllerName, $actionName, isset($item[3]) ? $item[3] : null),"name"=>$item[0]);
		}
//		dump($menuOut,"saf");
		$viewData['menu']=$menuOut;
		$data = $viewData;
		$this->_executeView($tpl,$data);

	}
	/**
	 * 取得全局变量
	 *
	 * @param string $value
	 * @return value
	 */
	function _g($name)
	{
		return FLEA::loadAppInf($name);
	}

	//临时改变全局变量的值
	function _s($name,$value)
	{
		return FLEA::setAppInf($name,$value);
	}

	/**
	 * 添加一条要在下一个页面显示的消息
	 *
	 * @param unknown_type 消息内容 请先国际化
	 */
	function addMsg($msg){
		if(!isset($_SESSION['msg']))
		$_SESSION['msg']=array();
		$_SESSION['msg'][]=$msg;
	}



	/**
     * 返回用 _setBack() 设置的 URL
     */
	function _goBack() {
		$url = $this->_getBack();
		unset($_SESSION['BACKURL']);
		redirect($url);
	}

	/**
     * 设置返回点 URL，稍后可以用 _goBack() 返回
     */
	function _setBack() {
		$_SESSION['BACKURL'] = encode_url_args($_GET);
	}

	/**
     * 获取返回点 URL
     *
     * @return string
     */
	function _getBack() {
		if (isset($_SESSION['BACKURL'])) {
			$url = $this->rawurl($_SESSION['BACKURL']);
		} else {
			$url = $this->_url();
		}
		return $url;
	}

	/**
     * 直接提供查询字符串，生成 URL 地址
     *
     * @param string $queryString
     *
     * @return string
     */
	function rawurl($queryString) {
		if (substr($queryString, 0, 1) == '?') {
			$queryString = substr($queryString, 1);
		}
		return $_SERVER['SCRIPT_NAME'] . '?' . $queryString;
	}
}
