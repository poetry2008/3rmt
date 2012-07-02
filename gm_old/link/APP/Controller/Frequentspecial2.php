<?php
FLEA::loadClass("Controller_Base");
class Controller_FrequentSpecial2 extends Controller_Base{
	function __construct(){
		parent::__construct();
		$this->model_FrequentSpecial2 = FLEA::getSingleton('Model_FrequentSpecial2');
	}

    /**
     * 后台
     * 列出全部常用专题
     */
	function actionIndex(){
        $classes = $this->model_FrequentSpecial2->findAll(null,'`order` DESC');
        $viewData = array(
            'classes'=>$classes,
        );
        $this->executeView("Admin".DS."frequentSpecial2Index.html",$viewData);
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
        if($this->model_FrequentSpecial2->find($id)){
            $this->addMsg(_T('このカテゴリはすでに常用カテゴリに存在しています'));
            redirect(url('frequentspecial2','index'));
        }
        $id = $this->model_FrequentSpecial2->create($data);
        if($id){
            $this->_edit($id);
        }else{
            $this->addMsg(_T('frequentspecial_add_failed'));
            redirect(url('frequentspecial2','index'));
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
            redirect(url('frequentspecial2','index'));
        }
        $this->_edit($id);
    }



    function _edit($id)
    {
        $class = $this->model_FrequentSpecial2->find($id);
        $viewData = array(
            'class'=>$class,
        );
        $this->executeView("Admin".DS."frequentSpecial2Edit.html",$viewData);
    }

    /**
     *
     */
    function actionEditDo()
    {
        $id = (int)$_POST['id'];
        if(!$id){
            $this->addMsg(_T('param_error'));
            redirect(url('frequentspecial2','index'));
        }
        $data = array(
            'class_id'=>$id,
            'order'=>(int)$_POST['order'],
            'show'=>(int)$_POST['show'],
        );
        if($this->model_FrequentSpecial2->update($data)){
            $this->addMsg(_T('frequentspecial_edit_success'));
            redirect(url('FrequentSpecial2','index'));
        }else{
            $this->addMsg(_T('frequentspecial_edit_failed'));
            redirect(url('FrequentSpecial2','Edit',array('id'=>$id)));
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
            redirect(url('frequentspecial2','index'));
        }
        $this->model_FrequentSpecial2->disableLinks();
        $this->addMsg(_T($this->model_FrequentSpecial2->removeByPkv($id)?'frequentspecial_del_success':'frequentspecial_del_failed'));
        redirect(url('frequentspecial2','index'));
    }
}

