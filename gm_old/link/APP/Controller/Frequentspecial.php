<?php
FLEA::loadClass("Controller_Base");
class Controller_FrequentSpecial extends Controller_Base{
	function __construct(){
		parent::__construct();
		$this->model_FrequentSpecial = FLEA::getSingleton('Model_FrequentSpecial');
	}

    /**
     * 后台
     * 列出全部常用专题
     */
	function actionIndex(){
        $classes = $this->model_FrequentSpecial->findAll(null,'`order` DESC');
        $viewData = array(
            'classes'=>$classes,
        );
        $this->executeView("Admin".DS."frequentSpecialIndex.html",$viewData);
    }

    /**
     * 添加一个分类到常用专题
     * @param   $id
     */
    function actionAdd()
    {
        $id = (int)$_GET['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentspecial','index'));
        }
        $data = array(
            'class_id'=>$id,
            'order' => 0,
        );
        if($this->model_FrequentSpecial->find($id)){
            $this->addMsg(_T('このカテゴリはすでに常用カテゴリに存在しています'));
            redirect(url('frequentspecial','index'));
        }
        $id = $this->model_FrequentSpecial->create($data);
        if($id){
            $this->_edit($id);
        }else{
            $this->addMsg(_T('frequentspecial_add_failed'));
            redirect(url('frequentspecial','index'));
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
            redirect(url('frequentspecial','index'));
        }
        $this->_edit($id);
    }



    function _edit($id)
    {
        $class = $this->model_FrequentSpecial->find($id);
        $viewData = array(
            'class'=>$class,
        );
        $this->executeView("Admin".DS."frequentSpecialEdit.html",$viewData);
    }

    /**
     *
     */
    function actionEditDo()
    {
        $id = (int)$_POST['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentspecial','index'));
        }
        $data = array(
            'class_id'=>$id,
            'order'=>(int)$_POST['order'],
            'show'=>(int)$_POST['show'],
        );
        if($this->model_FrequentSpecial->update($data)){
            $this->addMsg(_T('frequentspecial_edit_success'));
            redirect(url('FrequentSpecial','index'));
        }else{
            $this->addMsg(_T('frequentspecial_edit_failed'));
            redirect(url('FrequentSpecial','Edit',array('id'=>$id)));
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
            redirect(url('frequentspecial','index'));
        }
        $this->model_FrequentSpecial->disableLinks();
        $this->addMsg(_T($this->model_FrequentSpecial->removeByPkv($id)?'frequentspecial_del_success':'frequentspecial_del_failed'));
        redirect(url('frequentspecial','index'));
    }
}

