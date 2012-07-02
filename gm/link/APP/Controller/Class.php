<?php
FLEA::loadClass("Class_Table");
FLEA::loadClass("Controller_Base");
class Controller_Class extends Controller_Base{
	var $model_Class;
	var $controller;	//当前controller
	var $action;		//当前action
	var $smarty;		//当前动作使用的smarty对象

    function __construct(){
		parent::__construct();
		$view = & $this->_getView();
		$view->template_dir = APP_DIR.DS."View".DS."Admin";
		$this->model_Class = FLEA::getSingleton('Model_Class');
		$this->smarty=&$this->_getView();
		$this->controller=$_GET['controller'];
		$this->action=$_GET['action'];
		$this->smarty->assign('controller',$this->controller);
		$this->smarty->assign('action',$this->action);
		$this->smarty->parent=$this;

	}

	/**
    * 树形列出全部分类
    */
	function actionIndex(){
		/**
         * 读取指定父分类下的直接子分类
         */
		$parentId = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;
		if ($parentId) {
			$parent = $this->model_Class->getClass($parentId);
			if (!$parent) {
				js_alert(sprintf(_T('ui_c_invalid_parent_id'), $parentId),
				'', $this->_url());
			}
			$subClasses = $this->model_Class->getSubClasses($parent);

			/**
             * 确定当前分类到顶级分类的完整路径
             */
			$path = $this->model_Class->getPath($parent);
			$path[] = $parent;
		} else {
			$parent = null;
			$path = null;
			$subClasses = $this->model_Class->getAllTopClasses();
		}

		foreach ($subClasses as $offset => $class) {
			$subClasses[$offset]['child_count'] = $this->model_Class->calcAllChildCount($class);
		}

		$this->_setBack();
        $conditions = array(
            'class_id'=>$parentId,
        );

		$config=array(	//需要12项配置信息
			'smarty'=>$this->smarty,
			'name'=>'sites'.$parentId,						//本表格的助记
			'title'=>_T('sites_in_class'),			//表格和页面标题
			'no'=>false,								//是否允许行号
			'multi'=>true,							//是否允许多选
			'add'=>false,							//是否允许添加
			'del'=>false,							//是否允许删除
			'edit'=>false,							//是否允许编辑
			'show'=>false,							//是否允许查看
			'operate'=>array(						//全表操作
                array(
					'title'=>_T('add_to_frequentsite'),
					'controller'=>'FrequentSite',
                    'action'=>'allTo'
                ),
                array(
					'title'=>_T('del_frequentsites'),
					'action'=>'allDel'
                ),
			),
			'fields'=>array(						//要显示的字段列表
				'id'=>'ID',
				'name'=>_T('site_name'),
                'url'=>'URL',
                'order'=>_T('order'),
				'updated'=>_T('updated'),
			),
			'defaultSortName'=>'order',
			'defaultSortOrder'=>'desc'
		);

		$t=new Class_Table($config);
		//为显示表格获取数据
		$model=&FLEA::getSingleton('Model_Site');
        //dump($model->findAll($conditions));
		$data=$model->listing($t->getListingConfig(),$conditions);
        //dump($data);
        for($i=0; $i<count($data['data']); $i++)
        {
            $data['data'][$i]['operate'] = array(
                array(
                    'controller'=>'site',
                    'action'=>'edit',
                    'title'=>_T('edit'),
                ),
                array(
                    'controller'=>'site',
                    'action'=>'del',
                    'title'=>_T('del'),
                ),
                array(
                    'controller'=>'frequentsite',
                    'action'=>'add',
                    'title'=>_T('add_to_frequentsite'),
                ),
            );
        }
//dump($data);
		$t->listing($data);
		$viewData = array(
            "subClasses"=>$subClasses,
            "subClassescount"=>count($subClasses),
            "parent"=>$parent,
            "path"=>$path,
		);
		$this->executeView("classIndex.html",$viewData);
	}

    /**
     *
     */
    function actionIndexDo()
    {
        //dump($_POST);
        if($_POST['id']){
            $model_Site = FLEA::getSingleton('Model_Site');
            $model_Site->enableLinks();
            $model_FrequentSite = FLEA::getSingleton('Model_FrequentSite');
            if($_POST['operate'] == 'allDel'){
                $model_Site->removeByPkvs($_POST['id']);
                $this->addMsg(_T('site_del_success'));
                redirect(url('class','index'));
            }elseif($_POST['operate'] == 'allTo'){
                foreach($_POST['id'] as $id){
                    $data = array(
                        'site_id'=>$id,
                        'order' => 0,
                    );
                    $model_FrequentSite->create($data);
                }
                $this->addMsg(_T('frequentsite_add_success'));
                redirect(url('frequentsite','index'));
            }
        }
    }


    /**
    * 新闻列表
    */
//	function actionSite(){
//		$config=array(	//需要12项配置信息
//			'smarty'=>$this->smarty,
//			'name'=>'News',						//本表格的助记
//			'title'=>'this is a title',			//表格和页面标题
//			'no'=>true,								//是否允许行号
//			'multi'=>true,							//是否允许多选
//			'add'=>false,							//是否允许添加
//			'del'=>true,							//是否允许删除
//			'edit'=>true,							//是否允许编辑
//			'show'=>true,							//是否允许查看
//			'operate'=>array(						//全表操作
//				array(
//					'title'=>_T('news_del_all'),
//					'action'=>'NewsDelAll'
//				)
//			),
//			'fields'=>array(						//要显示的字段列表
//				'id'=>'ID',
//				'name'=>'name',
//				'updated'=>'updated',
//			),
//			'defaultSortName'=>'updated',
//			'defaultSortOrder'=>'desc'
//		);
//
//		$t=new Class_Table($config);
//		//为显示表格获取数据
//		$model=&FLEA::getSingleton('Model_Site');
//		$data=$model->listing($t->getListingConfig());
//		$t->listing($data);
//	}

	/**
     * 创建新分类
     */
	function actionCreate() {
		$class = array(
		'class_id'  => null,
		'name'      => null,
		'parent_id' => isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0,
		);
		$this->_editClass($class);
	}

	/**
     * 修改分类
     */
	function actionEdit() {
		$class = $this->model_Class->getClass((int)$_GET['class_id']);
		if (!$class) {
			js_alert(sprintf(_T('ui_c_invalid_class_id'), $_GET['class_id']),
			'', $this->_getBack());
		}
		$this->_editClass($class);
	}

	/**
     * 显示添加或修改分类信息页面
     *
     * @param array $class
     */
	function _editClass($class) {
		$parentId = $class['parent_id'];
		if ($parentId) {
			$parent = $this->model_Class->getClass($parentId);
			if (!$parent) {
				js_alert(sprintf(_T('ui_c_invalid_parent_id'), $parentId),
				'', $this->_url());
			}

			/**
             * 确定当前分类到顶级分类的完整路径
             */

            $path = $this->model_Class->getPath($parent);
            $path[] = $parent;
        } else {
            $parent = array(
                'class_id' => 0,
                'name' => _T('ui_c_new_top_class'),
            );
            $path = array($parent);
        }
        $viewData = array(
            "path"=>$path,
            "parent"=>$parent,
            "parentId"=>$parentId,
            'class'=>$class,
            //'part'=>$part,
        );
        $this->executeView("classEdit.html",$viewData);

//        include(APP_DIR . DS . View .DS. Admin.DS.'BoProductClassesEdit.php');
    }

	/**
     * 保存分类信息到数据库
     */
	function actionSave() {
		$class = array(
		'name' => $_POST['name'],
		);
        //dump($_POST);
        //dump($class);
		FLEA::loadClass("FLEA_Helper_FileUploader");
		$uploadDir = INDEX_DIR.DS."images".DS.'thumb';
		$uploader = new FLEA_Helper_FileUploader();
		$files =& $uploader->getFiles();
		$allowExts = 'jpg,png,gif';
		$maxSize = 1024 * 1024;
		foreach ($files as $file) {
			if (!$file->check($allowExts, $maxSize)) {
				// 上传的文件类型不符或者超过了大小限制。
				$this->addMsg(_T('upload_error'));
				$this->_getBack();
			}
			// 生成唯一的文件名（重复的可能性极小）
			$id = md5(time() . $file->getFilename() . $file->getSize() . $file->getTmpName());
			$filename = $id . '.' . strtolower($file->getExt());
			$file->move($uploadDir . DS . $filename);
			@chmod($uploadDir . DS . $filename,777);
			$this->addMsg(_T('upload_success'));
		}
		__TRY();
		if ($_POST['class_id']) {
			// 更新分类
			$class['class_id'] = $_POST['class_id'];
			if($filename)
			$class['thumb']=$filename;
			$this->model_Class->updateClass($class);
		} else {
			// 创建分类
			$this->model_Class->createClass($class, $_POST['parent_id']);
		}

		$ex = __CATCH();
		if (__IS_EXCEPTION($ex)) {
			js_alert($ex->getMessage(), '', $this->_getBack());
		}
		$this->_goBack();
	}

	/**
     * 删除分类
     */

    function actionRemove() {
        __TRY();

        /**
         * 由于采用了先进的算法，所以此处要另处理
         */
		$this->model_Class->removeSiteByClassId($_GET['class_id']);
		$this->model_Class->removeClassById($_GET['class_id']);
		$ex = __CATCH();
		if (__IS_EXCEPTION($ex)) {
			js_alert($ex->getMessage(), '', $this->_getBack());
		}
		$this->_goBack();
	}

	function actionSearch()	{
		$keyword = h($_POST['keyword']);
		$condiction = array(
		array("name","%".$keyword."%","like",'or'),
		);
		$class=$this->model_Class->findAll($condiction);
		$viewData=array(
		'subClass'=>$class,
		);
		$this->executeView("classSearch.html",$viewData);
	}


}

