<?php
FLEA::loadClass("Controller_Base");
class Controller_FrequentSite extends Controller_Base{
	var $model_FrequentSite;
	function __construct(){
		parent::__construct();

		$this->model_FrequentSite = FLEA::getSingleton('Model_Site');
	}

    /**
     * 后台
     * 列出全部推荐站点
     */
	function actionIndex(){
        $coud = "(is_recommend=1)";
        $sites = $this->model_FrequentSite->findAll($coud,'`created` DESC');
        //dump($sites);
        $viewData = array(
            'sites'=>$sites,
        );
        $this->executeView("Admin".DS."frequentSiteIndex.html",$viewData);
    }

    /**
     * 添加一个站点到常用站点
     * @param   $id
     */
    function actionAdd()
    {
        $id = (int)$_GET['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentsite','index'));
        }
        $data = array(
            'site_id'=>$id,
            'order' => 0,
        );
        if($this->model_FrequentSite->find(array('site_id'=>$id))){
            $this->addMsg(_T('このサイトはすでに常用サイトに存在しています'));
            redirect(url('frequentsite','index'));
        }
        $id = $this->model_FrequentSite->create($data);
        if($id){
            $this->_edit($id);
        }else{
            $this->addMsg(_T('frequentsite_add_failed'));
            redirect(url('frequentsite','index'));
        }
    }

    /**
     *
     */
    function actionEdit()
    {
        $id = (int)$_GET['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentsite','index'));
        }
        $this->_edit($id);
    }



    function _edit($id)
    {
        $site = $this->model_FrequentSite->find($id);
        $viewData = array(
            'site'=>$site,
        );
        $this->executeView("Admin".DS."frequentSiteEdit.html",$viewData);
    }

    /**
     *
     */
    function actionEditDo()
    {
        $id = (int)$_POST['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentsite','index'));
        }
        $data = array(
            'id'=>$id,
            'order'=>(int)$_POST['order'],
            'is_recommend' => (int)$_POST['is_recommend'],
        );
        if($this->model_FrequentSite->update($data)){
            $this->addMsg(_T('frequentsite_edit_success'));
            redirect(url('frequentsite','index'));
        }else{
            $this->addMsg(_T('frequentsite_edit_failed'));
            redirect(url('FrequentSite','Edit',array('id'=>$id)));
        }
    }

    /**
     *
     */
    function actionDel()
    {
        $id = (int)$_GET['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentsite','index'));
        }
        $this->addMsg(_T($this->model_FrequentSite->removeByPkv($id)?'frequentsite_del_success':'frequentsite_del_failed'));
        redirect(url('frequentsite','index'));
    }

}

