<?php
FLEA::loadClass("Controller_Base");
class Controller_Submit extends Controller_Base{
	var $model_Submit;
	function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$this->model_Submit = FLEA::getSingleton('Model_Submit');
	}

    /**
    * 网站登陆页
    */
	function actionIndex(){
        $this->executeView("Site/submit.html",null);
    }

    /**
    * 登陆操作
    */
    function actionSubmit()
    {
        $data = array(
            'name' => htmlspecialchars($_POST['name']),
            'url' => htmlspecialchars($_POST['url']),
            'comment' => htmlspecialchars($_POST['comment']),
            'writer' => ip2long($this->get_ip()),
        );

        $this->addMsg(_T($this->model_Submit->create($data)?'site_submit_success':'site_submit_failed'));
        redirect(url());
    }


    /**
     * 后台
     * 管理员查看提交的站点
     */
    function actionList()
    {
        FLEA::loadClass('FLEA_Helper_Pager');
        $page       = (isset($_GET['page']))?(int)$_GET['page']:1;
        $pageSize   = FLEA::getAppInf('admin_submit_num');
        $conditions = array();
        $sort       = "`id` DESC";
        $pager = & new FLEA_Helper_Pager($this->model_Submit, $page, $pageSize, $conditions, $sort);
        $submits = $pager->findAll();

        $viewData = array(
            'submits'=>$submits,
            'pager'=>$pager->getPagerData(),
            'Navbar'=>$pager->getNavbarIndexs($page,FLEA::getAppInf('admin_submit_page_num')),
            'controller'=>'submit',
            'action'=>'list',
        );
        $this->executeView("Admin/submitIndex.html",$viewData);
    }

    /**
     * 删除
     * $id
     */
    function actionDel()
    {
        $id = (int)$_GET['id'];
        if(!$id)
        {
            $this-addMsg(_T('param_error'));
            redirect(url());
        }

        $this->addMsg(_T($this->model_Submit->_delete($id)?'submit_del_success':'submit_del_failed'));
        redirect(url());
    }

    /**
     * 管理员编辑并添加站点
     *      $submitId
     */
    function actionEdit()
    {
        $submitId = (int)$_GET['id'];
        if(!$submitId){
            $this->addMsg(_T('param_error'));
            redirect(url('admin','index'));
        }
        $right = array();
        $site = $this->model_Submit->find($submitId);
        $model_Class = &FLEA::getSingleton('Model_Class');
        $classes = $model_Class->getAllClasses();
        if (count($classes) == 0) {
            //如果无分类提示添加分类
            js_alert(_T('ui_p_create_class_first'), '', url('class'));
        }
        $str_classes = '';
        foreach ($classes as $class):
            $c = count($right);
            if ($c > 0) {
                while ($c > 0 && $right[$c - 1] < $class['right_value'])
                {
                    array_pop($right);
                    $c = count($right);
                }
            }
            $className = t(str_repeat('  ', $c) . $class['name'] . '      ');
            $right[] = $class['right_value'];
            $str_classes .= '<option value="'.$class['class_id'].'">'.$className."</option>\n";
        endforeach;
        $viewData = array(
            'site'=>$site,
            'str_classes'=>$str_classes,
        );
        $this->executeView("Admin/submitEdit.html",$viewData);
    }

    /**
     *
     */
    function actionEditDo()
    {
        $data = array(
            'name'     => htmlspecialchars($_POST['name']),
            'url'      => htmlspecialchars($_POST['url']),
            'class_id' => (int)$_POST['class'],
            'order'    => (int)$_POST['order'],
        );

        $model_Site = &FLEA::getSingleton('Model_Site');

        if($model_Site->create($data)){
            $this->model_Submit->removeByPkv((int)$_POST['submit_id']);
            $this->addMsg(_T('site_submit_success'));
            redirect(url('submit','list'));
        }else{
            $this->addMsg(_T('site_submit_failed'));
            redirect(url('submit','edit',array('id',(int)$_POST['submit_id'])));
        }
    }


    /**
     * 获得IP地址
     */
    function get_ip()
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), '')){
            $onlineip = getenv('HTTP_CLIENT_IP');
        }elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), '')){
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        }elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), '')){
            $onlineip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], '')){
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        $onlineip = addslashes($onlineip);
        @preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : '';
        return $onlineip;
    }

}
