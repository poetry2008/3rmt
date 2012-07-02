<?php
FLEA::loadClass("Controller_Base");
class Controller_FrequentClass extends Controller_Base{
	function __construct(){
		parent::__construct();
		$this->model_FrequentClass = FLEA::getSingleton('Model_FrequentClass');
	}

    /**
     * 后台
     * 列出全部常用分类
     */
	function actionIndex(){
        $classes = $this->model_FrequentClass->findAll(null,'`order` DESC');
        $viewData = array(
            'classes'=>$classes,
        );
        $this->executeView("Admin".DS."frequentClassIndex.html",$viewData);
    }

    /**
     * 添加一个分类到常用分类
     * @param   $id
     */
    function actionAdd()
    {
        $id = (int)$_GET['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentclass','index'));
        }
        $data = array(
            'class_id'=>$id,
            'order' => 0,
        );

        if($this->model_FrequentClass->find($id)){
            $this->addMsg(_T('このカテゴリはすでに常用カテゴリに存在しています'));
            redirect(url('frequentclass','index'));
        }
        $id = $this->model_FrequentClass->create($data);
        if($id){
            $this->_edit($id);
        }else{
            $this->addMsg(_T('frequentclass_add_failed'));
            redirect(url('frequentclass','index'));
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
            redirect(url('frequentclass','index'));
        }
        $this->_edit($id);
    }



    function _edit($id)
    {
        $class = $this->model_FrequentClass->find($id);
        $viewData = array(
            'class'=>$class,
        );
        $this->executeView("Admin".DS."frequentClassEdit.html",$viewData);
    }

    /**
     *
     */
    function actionEditDo()
    {
        $id = (int)$_POST['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentclass','index'));
        }
        $data = array(
            'class_id'=>$id,
            'order'=>(int)$_POST['order'],
            'show'=>(int)$_POST['show'],
            'more'=>(int)$_POST['more'],
        );
        if($this->model_FrequentClass->update($data)){
            $this->addMsg(_T('frequentclass_edit_success'));
            redirect(url('FrequentClass','index'));
        }else{
            $this->addMsg(_T('frequentclass_edit_failed'));
            redirect(url('FrequentClass','Edit',array('id'=>$id)));
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
            redirect(url('frequentclass','index'));
        }
        $this->model_FrequentClass->disableLinks();
        $this->addMsg(_T($this->model_FrequentClass->removeByPkv($id)?'frequentclass_del_success':'frequentclass_del_failed'));
        redirect(url('frequentclass','index'));
    }
}

