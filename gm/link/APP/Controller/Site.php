<?php
FLEA::loadClass("Controller_Base");
class Controller_Site extends Controller_Base
{
  /*
     var $method_arr = array('すべての語を含む'=>'and'
     ,'いずれかの語を含む'=>'or');
     var $engine_arr = array('オレンジ不動産リンク集' => 'Nezumy',
     'Yahoo!で' => 'Yahoo',
     'Googleで' => 'Google',
     'Infoseekで' => 'InfoSeek',
     'gooで' => 'Goo',
     'Exciteで' => 'Excite Japan',
     'フレッシュアイで' => 'FRESHEYE',
     '-----------------' => '',
     '楽天市場で' => '楽天市場',
     'bk1で' => 'BK1',
     'amazon.co.jp(ISBN検索)で' => 'amazon'
     );
     var $day_arr = array('指定しない' => '',
     '本日'=> 'today',
     '1日以内' => '1',
     '3日以内' => '3',
     '7日以内' => '7',
     '14日以内' => '14',
     '30日以内' => '30'
     );
     var $type_arr = array('次ページで開く' => '0','別窓で開く' => '1'
     );
   */
  var $seo = array();
  var $bread;
  var $host_dir;
  function __construct(){
    parent::__construct();
    $global = &FLEA::getSingleton('Model_Global');
    $dir = $global->find('name = "set_new_dir"');
    $this->host_dir = $dir['value'];
  }

  /**
   * 主页
   */
  function actionIndex(){
    /*
    //全部一级分类以及分类下的n个子类
    //dump($_COOKIE,'cookie');
    $model_Class = &FLEA::getSingleton('Model_Class');
    $model_Class->enableLink('children');
    $topClass = $model_Class->getAllTopClassesOrder();
    $classSelector = $model_Class->getAllClassesSelector();
    //全部常用分类
    $model_Frequent_Class = &FLEA::getSingleton('Model_FrequentClass');
    $frequentClass = $model_Frequent_Class->findAll(null,'`order` DESC');
    //dump($frequentClass,'frequentClass');
    //全部常用站点
    $model_Frequent_Site = &FLEA::getSingleton('Model_FrequentSite');
    $frequentSite = $model_Frequent_Site->findAll(null,'`order` DESC');
    //dump($frequentSite,'frequentSite');
    //全部主题
    $model_Frequent_Special = &FLEA::getSingleton('Model_FrequentSpecial');
    $frequentSpecial = $model_Frequent_Special->findAll(null,'`order` DESC');
    //全部主题
    $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
    $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
    //dump($frequentSpecial2,'frequentSpecial');
    $total = $model_Class->findCount();

    $viewData = array(
    'top' => $topClass,
    'classSelector' => $classSelector,
    'frequentClass' => $frequentClass,
    'frequentSite' => $frequentSite,
    'frequentSpecial' => $frequentSpecial,
    'frequentSpecial2' => $frequentSpecial2,
    'total' => $total,
    );
     */
    $model_Class = &FLEA::getSingleton('Model_Class');
    $model_Class->enableLink('children');
    $topClass = $model_Class->getAllTopClassesOrder();
    //全部主题
    $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
    $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
    $class_str = array();
    $global = &FLEA::getSingleton('Model_Global');
    $dir = $global->find('name = "set_new_dir"');
    foreach($topClass  as $k => $v){
      $str = '';
      $value = $v['children'];
      $count = count($value);
      for($i=0;$i<$count;$i++){
        $str.="<a href = '/".$dir['value']."/class_id_";
        $str.=$value[$i]['class_id'].".html'>";
        $str.=$value[$i]['name'];
        $str.="</a>";
        $str.=",";
      }
      $str = substr($str,0,-1);
      $class_str[$k] = array('string'=>$str);
    }
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $viewData = array( 'hostlink' => $this->host_dir,
        'bread' => $bread,
        'seo' => $seo,
        'kind' => '一覧',
        'class' => $topClass,
        'str' => $class_str,
        'frequentSpecial2' => $frequentSpecial2,
        );
    $this->executeView("Site/index.html",$viewData);
  }

  /**
   * 分类页
   * 显示子分类和当前分类下的站点
   * 所需参数:
   *   $classId|$className
   */
  function actionClass()
  {

    $classId = (int)$_GET['id'];
    if(!$classId){
      $this->addMsg(_T('param_error'));
      redirect(url('',''));
    }

    $model_Site = &FLEA::getSingleton('Model_Site');

    FLEA::loadClass('FLEA_Helper_Pager');
    $page       = (isset($_GET['page']))?(int)$_GET['page']:1;
    $pageSize   = '20'; 
    $conditions = "class_id='".$classId."' and 
      ((is_custom='1' and show_state='1') or 
       is_custom <> '1')";
    $sort = "updated,is_recommend desc";
    $pager = & new FLEA_Helper_Pager($model_Site, $page, $pageSize, $conditions, $sort);
    $pager->setBasePageIndex(1);  // 起始页码设为1

    //    $pager->_conditions = array_merge($pager->_conditions,array('state'=>'1'));
    $sites = $pager->findAll();
    $model_Class = &FLEA::getSingleton('Model_Class');
    $class = $model_Class->find($classId);
    if(!$class||(isset($class)&&$class['class_id']==1)){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    //dump($class);
    $path = $model_Class->getPath($class);
    //dump($path);
    //dump($sites);
    $id = (int) $_GET['id'];
    if(!$id){
      $this->addMsg(_T('param_error'));
      redirect(url('',''));
    }
    $model_Class = &FLEA::getSingleton('Model_Class');
    $classkind = $model_Class->getClassWithLinks($id);
    $classAdd = $this->addTd($classkind['children'],5);
    //dump($classAdd);
    //过滤自定义的，但没有难通过的站点
    if (is_array($class['sites']))
    {
      $sites_array = array();
      foreach ($classkind['sites'] as $key => $site)
      {
        if ($site['is_custom'] == '1' && $site['show_state'] == '0')
        {
          continue;
        }
        $sites_array[] = $site;
      }
      $class['sites'] = $sites_array;
    }
    //var_dump($class['sites']);exit;
    $sitesAdd = $this->addTd($classkind['sites'],5);
    //dump($sitesAdd);
    //dump($class,'class Info');
    //当前分类的信息
    //关联当前分类的子分类
    //关联当前分类的子站
    //全部主题
    /*
       $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
       $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
    //        dump($class);
    $viewData = array(
    'class' => $class,
    'classAdd' => $classAdd,
    'sitesAdd' => $sitesAdd,
    'frequentSpecial2' => $frequentSpecial2,
    );

     */
    if($page==1){
      $pagestart = 1;
    }else{
      $tmp = $pager->pageSize;
      $pagestart = $tmp*($page-1)+1;
    }
    if($page*$pager->pageSize > $pager->count){
      $pageend = $pager->count;
    }else{
      $pageend = $page*$pager->pageSize;
    } 
    $pagestr = $pagestart.' - '.$pageend;
    if($classkind['parent_id']>0){
      $classkind_f = $model_Class->find($classkind['parent_id']);
    }
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $global = &FLEA::getSingleton('Model_Global');
    $dir = $global->find('name = "set_new_dir"');
    if($classkind['parent_id']>0){
      $this->bread->add($classkind_f['name'],'/'.$dir['value'].
          '/class_id='.$classkind_f['class_id'].'.html');
    }
    $this->bread->add($classkind['name'],'/'.$dir['value'].
        '/class_id='.$classkind['class_id'].'.html');
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $viewData = array( 'hostlink' => $this->host_dir,
        'seo' => $seo,
        'bread' => $bread,
        'classkind_f'=> $classkind_f,
        'classkind' => $classkind,
        'classAdd' => $classAdd,
        'sitesAdd' => $sitesAdd,
        'frequentSpecial2' => $frequentSpecial2,
        'class'=>$class,
        'path'=>$path,
        'sites'=>$sites,
        'pager'=>$pager->getPagerData(),
        'Navbar'=>$pager->getNavbarIndexs($page,FLEA::getAppInf('admin_site_page_num')),
        'controller'=>'site',
        'action'=>'class',
        'pagenum'=>$page,
        'pagestr'=>$pagestr
        );
    //$this->executeView("Admin/siteIndex.html",$viewData);
    $this->executeView("Site/class.html",$viewData);
  }

  /*
  function actionAll()
  {
    $model_Class = &FLEA::getSingleton('Model_Class');
    $model_Class->enableLink('children');
    $topClass = $model_Class->getAllTopClassesOrder();
    //全部主题
    $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
    $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    //dump($topClass);
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $viewData = array( 'hostlink' => $this->host_dir,
        'bread' => $bread,
        'seo' => $seo,
        'kind' => '一覧',
        'class' => $topClass,
        'frequentSpecial2' => $frequentSpecial2,
        );
    $this->executeView("Site/all.html",$viewData);
  }
  */

  /**
   *
   */
  function actionDetail()
  {
    $id = (int)$_GET['id'];
    if(!$id){
      $this->addMsg(_T('param_error'));
      redirect(url());
    }
    $model_Site = &FLEA::getSingleton('Model_Site');
    $site = $model_Site->find($id);
    //dump($site);
    $site['comment'] = nl2br($site['comment']);
    $viewData = array( 'hostlink' => $this->host_dir,
        'seo' => $this->seo,
        'site' => $site,
        );
    $this->executeView("Site/web.html",$viewData);
  }


  /**
   * 表格后补<td>的个数
   */
  function addTd($arr,$col)
  {
    $c = count($arr);
    $add = $col - ($c % $col);
    if($add >= $col)
    {
      $add = $add-$col;
    }
    return $add;
  }


  /**
   * 后台
   * 列出指定分类下的全部站点
   * 所需参数:
   *   $classId
   */
  function actionList()
  {
    $classId = (int)$_GET['id'];
    if(!$classId){
      $this->addMsg(_T('param_error'));
      redirect(url('',''));
    }

    $model_Site = &FLEA::getSingleton('Model_Site');

    FLEA::loadClass('FLEA_Helper_Pager');
    $page       = (isset($_GET['page']))?(int)$_GET['page']:1;
    $pageSize   = FLEA::getAppInf('admin_site_num');
    $conditions = array(
        'class_id'=>$classId,
        );
    $sort       = "`order` DESC";
    $pager = & new FLEA_Helper_Pager($model_Site, $page, $pageSize, $conditions, $sort);
    $pager->setBasePageIndex(1);  // 起始页码设为1

    $sites = $pager->findAll();
    $model_Class = &FLEA::getSingleton('Model_Class');
    $class = $model_Class->find($classId);
    //dump($class);
    $path = $model_Class->getPath($class);
    //dump($path);
    //dump($sites);
    $viewData = array( 'hostlink' => $this->host_dir,
        'class'=>$class,
        'path'=>$path,
        'sites'=>$sites,
        'pager'=>$pager->getPagerData(),
        'Navbar'=>$pager->getNavbarIndexs($page,FLEA::getAppInf('admin_site_page_num')),
        'controller'=>'site',
        'action'=>'list',
        );
    $this->executeView("Admin/siteIndex.html",$viewData);
  }

  /**
   * 后台
   * 在指定分类下添加一个站点
   * 所需参数:
   *   $classId
   */
  function actionAdd()
  {
    $classId = (int)$_GET['id'];
    $site = array(
        'name' => '',
        'url'  => 'http://',
        'comment' => '',
        'class_id'=>$classId,
        'order'=>'0',
        );
    $this->_editSite($site);

  }

  /**
   * 后台
   * 编辑站点
   */
  function actionEdit()
  {
    $siteId = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $site = $model_Site->find($siteId);
    $this->_editSite($site);
  }

  function _editSite($site)
  {

    $model_Class = &FLEA::getSingleton('Model_Class');
    $classes = $model_Class->getAllClasses();

    if (count($classes) == 0) {
      //如果无分类提示添加分类
      //js_alert(_T('ui_p_create_class_first'), '', url('BoProductClasses'));
    }
    $str_classes = '';
    $right = array();
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
    $str_classes .= '<option value="'.$class['class_id'].'"'.($class['class_id']==$site['class_id']?'selected':'').'>'.$className."</option>\n";
    endforeach;
    $site_url = FLEA::getAppInf('site_url');
    $viewData = array( 'hostlink' => $this->host_dir,
        'site_url'=>$site_url,
        'str_classes'=>$str_classes,
        'site'=>$site,
        );
    $this->executeView("Admin".DS."siteEdit.html",$viewData);
  }

  /**
   *
   */
  function actionEditDo()
  {
    $data = array(
        'id'       => (int)$_POST['id'],
        'name'     => h($_POST['name']),
        'url'      => h($_POST['url']),
        'comment'  => h($_POST['comment']),
        'class_id' => (int)$_POST['class'],
        'linkpage_url' => h($_POST['linkpage_url']?$_POST['linkpage_url']:NULL),
        'order'    => (int)$_POST['order'],
        );


    $model_Site = &FLEA::getSingleton('Model_Site');
    if($model_Site->save($data)){
      $this->addMsg(_T($data['id']?'site_edit_success':'site_create_success'));
    }else{
      $this->addMsg(_T($data['id']?'site_edit_failed':'site_create_failed'));
    }
    redirect(url('class','index',array('parent_id'=>(int)$_POST['class'])));
  }

  function actionlinkchecksubmit()
  { 
    $week_arr = array('1'=>'月','2'=>'火','3'=>'水',
        '4'=>'木','5'=>'金','6'=>'土',
        '7'=>'日');
    $preview = $_POST['preview'];
    if ($preview == 'on')
    {
      $model_Class = &FLEA::getSingleton('Model_Class');
      $class = $model_Class->find($_POST['class']);
      $model_Setseo = FLEA::getSingleton('Model_Setseo');
      $seo = $model_Setseo->find("action ='".$_GET['action']."_confirm'");
      if(!$seo){
        $seo = $model_Setseo->find("action ='index'");
      }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $top_info['h1'] = 'regist_h1';
    $top_info['text'] = 'regist_text';
      $viewData = array( 'hostlink' => $this->host_dir,
          'top_info' => $top_info,
          'bread' => $bread,
          'seo' => $seo,
          'fname'=>h($_POST['fname']),
          'femail'=>h($_POST['femail']),
          'fpass' =>h($_POST['fpass']),
          'name' => h($_POST['name']),
          'url'=> h(trim($_POST['url'])),
          'comment'=>h($_POST['comment']),
          'class' => h($_POST['class']),
          'class_name' => $class['name'],
          'linkpage_url' => h(trim($_POST['linkpage_url'])),
          'to_admin' => h($_POST['to_admin']),
          );

      $this->executeView("Site".DS."linkcheckpreview.html", $viewData); 
    }
    else
    {

      $bln = $this->linkcheck(trim($_POST['url']), trim($_POST['linkpage_url']));
      $bln['state']?$state='1':$state='0';
      $bln['is_recommend']?$is_recommend='1':$is_recommend='0';
      $data = array(
          'id'       => (int)$_POST['id'],
          'name'     => h($_POST['name']),
          'url'      => h(trim($_POST['url'])),
          'comment'  => h($_POST['comment']),
          'class_id' => (int)$_POST['class'],
          'linkpage_url' => trim($_POST['linkpage_url']),
          'is_custom' => '1',
          'state' => $state,
          'is_recommend' => $is_recommend,
          'to_admin' => h($_POST['to_admin']),
          'order'    => (int)$_POST['order'],
          );

      $model_Site = &FLEA::getSingleton('Model_Site');
      if($data['url']!=''){
        $new_site=$model_Site->save($data);

        $model_Consumer = &FLEA::getSingleton('Model_Consumer');
        $c_data = array( 
            'consumer_name' => h($_POST['fname']),
            'consumer_email' => h($_POST['femail']),
            'consumer_pass' => hash('md5', h($_POST['fpass'])),
            'site_id' => $new_site,
            );
        $model_Consumer->save($c_data);

    $global = &FLEA::getSingleton('Model_Global');
    $email_foot = $global->find('name = "email_foot"');
    $email_foot_str = str_replace("\r\n","<br>",$email_foot['value']);
        $this->addMsg(_T($data['id']?'site_edit_success':'site_create_success')); 
        $model_Class = &FLEA::getSingleton('Model_Class');
        $class = $model_Class->find($_POST['class']);
        $model_Setseo = FLEA::getSingleton('Model_Setseo');
        $seo = $model_Setseo->find("action ='".$_GET['action']."_success'");
        if(!$seo){
          $seo = $model_Setseo->find("action ='index'");
        }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $top_info['h1'] = 'regist_h1';
    $top_info['text'] = 'regist_text';
        $viewData = array( 'hostlink' => $this->host_dir,
            'top_info' => $top_info,
            'bread' => $bread,
            'seo' => $seo,
            'id' => $new_site,
            'fname' => h($_POST['fname']),
            'femail' => h($_POST['femail']),
            'fpass' => h($_POST['fpass']),
            'name' => h($_POST['name']),
            'url' => h($_POST['url']),
            'comment' => h($_POST['comment']),
            'class' => h($_POST['class']),
            'class_name' => $class['name'],
            'linkpage_url' => h($_POST['linkpage_url']),
            'to_admin' => h($_POST['to_admin']),
            );
        $to = $c_data['consumer_email'];
        $subject = "=?UTF-8?B?".
          base64_encode('情報交換サイト！相互リンク集').
          "?=";
        //        $subject = '情報交換サイト！ 新規登録完了通知';
        $message .= "このたびは、";
        $message .= "相互リンク集";
        $message .= "へのご登録ありがとうございます。<br>";
        $message .= "<br>";
        $message .= '登録内容は以下のとおりですので、ご確認ください。'."<br>";
        $message .= '*************************************************'."<br>";
        $message .= '・登録日時：';
        $message .= date('Y/m/d')." (";
        $message .= $week_arr[date('N')];
        $message .= ") ".date('H:i')."<br>";
        $message .= '・登録者のIPアドレス：'.$_SERVER['REMOTE_ADDR']."<br>";
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $message .= '・登録者のホスト名：'.$hostname."<br>";
        $message .= '・参照元：'.url('site','regist')."<br>";
        $message .= '*************************************************'."<br>";
        $message .= "<br>";
        $message .= '■ID'."<br>";
        $message .= $new_site."<br>";
        $message .= '■お名前'."<br>";
        $message .= $viewData['fname']."<br>";
        $message .= '■Ｅメール'."<br>";
        $message .= $viewData['femail']."<br>";
        $message .= '■タイトル'."<br>";
        $message .= $viewData['name']."<br>";
        $message .= '■登録したカテゴリ'."<br>";
        $message .= $viewData['class_name']."<br>";
        $message .= "<br>";
        $message .= '■紹介文'."<br>";
        $message .= $viewData['comment']."<br>";
        $message .= '■URL'."<br>";
        $message .= $viewData['url']."<br>";
        $message .= '■管理パスワード'."<br>";
        $message .= $viewData['fpass']."<br>";
        $message .= '■管理人へのメッセージ'."<br>";
        $message .= $viewData['to_admin']."<br>";
        $message .= "<br>";
        $message .= '■登録内容変更用URL'."<br>";
        $message .= url('site', 'editsite', 'id='.$new_site)."<br>";
        $message .= "<br>";
        $message .= '今後登録内容の修正や削除する場合には、管理パスワード'."<br>";
        $message .=
          'にて全て行うことができますので、パスワードは大切に保管しておいて下さい。.';
        $message .= "<br>";
        $message .= "<br>";
        $message .= 'これからもどうぞよろしくお願いします。'."<br>";
        $message .= '+-------------------------------------+'."<br>";
        $message .= $email_foot_str."<br>";
        $message .= '+-------------------------------------+'."<br>";

        $message = wordwrap($message, 70);

        $Consumer_Id = $_POST['id'];
        /*
           $Model_Consumer = &FLEA::getSingleton("Model_Consumer");
           $cond = "site_id = '".$Consumer_Id."'";
           $Consumer = $Model_Consumer->find($cond);
           $From_Mail = $Consumer['consumer_email'];
         */
        $Model_User = &FLEA::getSingleton("Model_User");
        $cond = "username = 'haomai'";
        $User = $Model_User->find($cond);
        $From_Mail = $User['email'];
        $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";  
        $headers .= 'From: '.$From_Mail. "\r\n";

        mail($to, $subject, $message, $headers);

        $this->executeView("Site".DS."linkchecksuccess.html", $viewData); 

      }else{
        $this->addMsg(_T($data['id']?'site_edit_failed':'site_create_failed')); 
        redirect(url('site','regist'));
      }
    }
  }


  /**
   * 删除一个站点
   * 所需参数:
   *   $id
   */
  function actionDel()
  {
    $id = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $model_Site->enableLinks();
    $this->addMsg(_T($model_Site->removeByPkv($id)?'site_del_success':'site_del_failed'));
    $model_Site->disableLinks();
    redirect(url('class','index'));
  }
  function actionSearch(){
    if(isset($_GET['keywords'])&&$_GET['keywords']!=''){
      $keyword = h($_GET['keywords']);
    }else{
      $keyword = h($_POST['keywords']);
    }
    $model_Site = &FLEA::getSingleton('Model_Site');
    $condiction = array(array("name",'%'.$keyword.'%',"like",'or'),array("comment",'%'.$keyword.'%',"like",'or'));
    $arr = $model_Site->findAll($condiction);
    $result = array();
    foreach($arr as $key => $v){
      if($v['show_state']=='1'){
        $result[] = $v;
      }
    }
    //    	dump($result);
    $class = array("name"=>_T("search_result"),"sites"=>$result);
    $viewData = array( 'hostlink' => $this->host_dir,
        'seo' => $this->seo,
        "class"=>$class,
        );
    $this->executeView("Site/class.html",$viewData);
  }

  /**
   * 设置网站背景图片
   */
  function actionSetbackgroundimage()
  {
    $this->executeView("Site".DS."setbackgroundimage.html", array()); 
  }

  /**
   * 设置我的站点
   */
  function actionSetmysite()
  {

    $this->executeView("Site".DS."setmysite.html", array()); 
  }

  /**
   * 设置站点
   */
  function actionSetsite()
  {
    $this->executeView("Site".DS."setsite.html", array()); 
  }

  /**
   * 设置链接站点
   */
  function actionRegist()
  {

    $model_Class = &FLEA::getSingleton('Model_Class');
    $classes = $model_Class->getAllClasses();
    $str_classes = '';
    $right = array();
    foreach ($classes as $key=>$class):
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
    if (!$_POST['class'])
    {
      if ($key == 0)
      {
        $str_classes .= '<option selected="selected" value="'.$class['class_id'].'"'.'>'.$className."</option>\n";
      }else
      {
        $str_classes .= '<option value="'.$class['class_id'].'"'.'>'.$className."</option>\n";
      }
    }
    else
    {
      if ($_POST['class'] == $class['class_id'])
      {
        $str_classes .= '<option selected="selected" value="'.$class['class_id'].'"'.'>'.$className."</option>\n";

      }
      else
      {
        $str_classes .= '<option value="'.$class['class_id'].'"'.'>'.$className."</option>\n";
      }
    }

    endforeach;
    $link_url = '&lt;a href="'.FLEA::getAppInf('site_url').'"&gt; '.FLEA::getAppInf('host_word').' &lt;/a&gt;';
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $top_info['h1'] = 'regist_h1';
    $top_info['text'] = 'regist_text';
    $viewData = array( 'hostlink' => $this->host_dir,
        'top_info' => $top_info,
        'bread' => $bread,
        'seo' => $seo,
        'classSelector' => $str_classes,
        'link_url' => $link_url,
        'fname' => $_POST['fname']?$_POST['fname']:'',
        'femail' => $_POST['femail']?$_POST['femail']:'',
        'fpass' => $_POST['fpass']?$_POST['fpass']:'',
        'name' => $_POST['name']?$_POST['name']:'',
        'comment' => $_POST['comment']?$_POST['comment']:'',
        'url' => $_POST['url']?$_POST['url']:'http://',
        'linkpage_url' => $_POST['linkpage_url']?$_POST['linkpage_url']:'http://',
        'class' => $_POST['class']?$_POST['class']:'',
        'to_admin' => $_POST['to_admin']?$_POST['to_admin']:'',
        );
    $this->executeView("Site".DS."regist.html", $viewData); 
  }

  /**
   * 用户删除站点
   */
  function actionDeletesite()
  {
    $viewData = array( 'hostlink' => $this->host_dir,
        'seo' => $this->seo,
        'state' => '1',
        'email' => '',
        'pass' => '',
        );
    $this->executeView("Site".DS."deletesite.html", $viewData); 
  }

  /**
   * 删除站点
   */
  function actionSitedel()
  {
    $site_id = h($_POST['id']);
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    if(isset($_POST['del_check']) && $_POST['del_check'] == '1')
    {
      $model_Consumer = &FLEA::getSingleton('Model_Consumer');
      $model_Site = &FLEA::getSingleton('Model_Site');

      $pass = hash('md5', h($_POST['pass']));
      $c_cond = "site_id='".$site_id."' and consumer_pass='".$pass."'";

      if ($consumer = $model_Consumer->find($c_cond))
      {
        $s_cond = "id='".$consumer['site_id']."'";
        $model_Site->removeByConditions($s_cond);
        $model_Consumer->removeByPkv($consumer['consumer_id']);
        $viewData = array( 'hostlink' => $this->host_dir,
            'bread' => $bread,
            'seo' => $seo,
            'id' => $site_id,
            'state' => '1',
            );
        $this->executeView("Site".DS."deletesiteresult.html", $viewData); 
      }
      else
      {
        $viewData = array( 'hostlink' => $this->host_dir,
            'bread' => $bread,
            'seo' => $seo,
            'id' => $site_id,
            'state' => '2',
            );
        $this->executeView("Site".DS."deletesiteresult.html", $viewData); 
      }
    }
    else
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'bread' => $bread,
          'seo' => $seo,
          'id' => $site_id,
          'state' => '3',
          );
      $this->executeView("Site".DS."deletesiteresult.html", $viewData); 
    }
  }

  /**
   * 用户删除站点
   */

  /**
   * 站点列表
   */
  function actionRenew()
  {
    FLEA::loadHelper('Pager');

    $model_Site = &FLEA::getSingleton('Model_Site');
    $cond = "is_custom = '1' and show_state='1'";
    $sort = "is_recommend desc, updated desc";

    if (isset($_GET['page'])&&$_GET['page']<2){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $page = $_GET['page']?$_GET['page']:1;
    $pager = & new FLEA_Helper_Pager($model_Site, $page, 10, $cond, $sort,'1');
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $page_info = $pager->getPagerData();
    $last_page = array_pop($page_info['pagesNumber']);
    if (isset($last_page)&&$page > $last_page){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $top_info['h1'] = 'renew_h1';
    $top_info['text'] = 'renew_text';
    $viewData = array( 'hostlink' => $this->host_dir,
        'top_info' => $top_info,
        'bread' => $bread,
        'seo' => $seo,
        'pager' => $pager,
        );

    $this->executeView("Site".DS."renew.html", $viewData); 

  }
  /*
     互相连接的网站列表
   */
  function actionSitestate()
  {
    FLEA::loadHelper('Pager');

    $model_Site = &FLEA::getSingleton('Model_Site');
    $cond = "is_custom = '1' and show_state='1'";
    $sort = "updated,is_recommend desc";

    $page = $_GET['page']?$_GET['page']:1;
    $pager = & new FLEA_Helper_Pager($model_Site, $page, 10, $cond, $sort,'1');
    $viewData = array( 'hostlink' => $this->host_dir,
        'seo' => $this->seo,
        'pager' => $pager,
        );

    $this->executeView("Site".DS."sitestate.html", $viewData); 

  }
  /*
   * 新添加的网站
   */
  function actionNew()
  {
    FLEA::loadHelper('Pager');

    $global = &FLEA::getSingleton('Model_Global');
    $date = $global->find('name = "set_new_date"');
    $time = mktime(date('H')-$date['value'],date('i'),date('s'),
        date('m'),date('d'),date('Y'));
    $model_Site = &FLEA::getSingleton('Model_Site');
    $cond = "show_state = '1' and created > '".$time."'";
    $sort = "is_recommend,updated desc";

    if (isset($_GET['page'])&&$_GET['page']<2){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $page = $_GET['page']?$_GET['page']:1;
    $pager = & new FLEA_Helper_Pager($model_Site, $page, 10, $cond, $sort,'1');
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $page_info = $pager->getPagerData();
    $last_page = array_pop($page_info['pagesNumber']);
    if (isset($last_page)&&$page > $last_page){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $top_info['h1'] = 'new_h1';
    $top_info['text'] = 'new_text';
    $top_info['time'] =
      '<p>'.$date['value'].'時間以内に登録された新着サイトです。</p>';
    $viewData = array( 'hostlink' => $this->host_dir,
        'top_info' => $top_info,
        'bread' => $bread,
        'seo' => $seo,
        'pager' => $pager,
        );

    $this->executeView("Site".DS."new.html", $viewData); 

  }
  /*
   *推荐网站
   */
  function actionRecommend(){
    FLEA::loadHelper('Pager');
    $model_frequentsite = &FLEA::getSingleton('Model_FrequentSite');
    $arr = $model_frequentsite->findAll(null,'`order` DESC');
    $site_id = '';
    foreach($arr as $v){
      $site_id .= $v['site_id'].',';
    }
    $site_id = substr($site_id,0,-1);
    $model_Site = &FLEA::getSingleton('Model_Site');
    //$cond = "is_custom = '1' and id in (".$site_id.")";
    $cond = "(is_recommend = '1')"; 
    $sort = "updated desc";
    if (isset($_GET['page'])&&$_GET['page']<2){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $page = $_GET['page']?$_GET['page']:1;
    $pager = & new FLEA_Helper_Pager($model_Site, $page, 10, $cond, $sort,'1');
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $page_info = $pager->getPagerData();
    $last_page = array_pop($page_info['pagesNumber']);
    if (isset($last_page)&&$page > $last_page){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $top_info['h1'] = 'recommend_h1';
    $top_info['text'] = 'recommend_text';
    $viewData = array( 'hostlink' => $this->host_dir,
        'top_info' => $top_info,
        'bread' => $bread,
        'seo' => $seo,
        'pager' => $pager,
        );

    $this->executeView("Site".DS."recommend.html", $viewData); 


  }

  /**
   * 编辑删除站点
   */
  function actionEditsite()
  {
    if ($id = h($_GET['id']))
    {
      $model_Site = &FLEA::getSingleton('Model_Site'); 
      $site = $model_Site->find($id);
      $model_Setseo = FLEA::getSingleton('Model_Setseo');
      $seo = $model_Setseo->find("action ='".$_GET['action']."'");
      if(!$seo){
        $seo = $model_Setseo->find("action ='index'");
      }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
      $viewData = array( 'hostlink' => $this->host_dir,
          'bread' => $bread,
          'seo' => $seo,
          'site' => $site,
          );
      $this->executeView("Site".DS."siteedit.html", $viewData); 
    }
    else
    {
      redirect(url('site', 'renew'));
    }
  }

  /**
   * 编辑站点
   */
  function actionSiteupdate()
  {
    $site_id = h($_POST['id']);
    $pass = hash('md5', h($_POST['pass']));
    $model_Consumer = &FLEA::getSingleton('Model_Consumer'); 
    $cond = "site_id='".$site_id."' and consumer_pass='".$pass."'";
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);

    if($consumer = $model_Consumer->find($cond))
    { 
      $model_Site = &FLEA::getSingleton('Model_Site'); 
      $site = $model_Site->find($site_id);

      $model_Class = &FLEA::getSingleton('Model_Class');
      $classes = $model_Class->getAllClasses();
      $str_classes = '';
      $right = array();
      foreach ($classes as $key=>$class):
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
      if ($site['class_id'] == $class['class_id'])
      {
        $str_classes .= '<option selected="selected" value="'.$class['class_id'].'"'.'>'.$className."</option>\n";

      }
      else
      {
        $str_classes .= '<option value="'.$class['class_id'].'"'.'>'.$className."</option>\n";
      }

      endforeach;

      $viewData = array( 'hostlink' => $this->host_dir,
          'bread' => $bread,
          'seo' => $seo,
          'consumer' => $consumer,
          'site' => $site,
          'classSelector' => $str_classes,
          );
      $this->executeView("Site".DS."siteupdate.html", $viewData); 

    }
    else
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'seo' => $seo,
          'id' => $site_id,
          'state' => '1',
          );
      $this->executeView("Site".DS."siteupdateresult.html", $viewData); 
    }
  } 

  function actionSiteupdatesubmit()
  {
    $c_data = array(
        'consumer_id' => h($_POST['c_id']),
        'consumer_name' =>h($_POST['fname']),
        'consumer_email' => h($_POST['femail']),
        );
    $model_Consumer = &FLEA::getSingleton('Model_Consumer');
    $model_Consumer->save($c_data);

    $s_data = array(
        'id' => h($_POST['id']), 
        'name' => h($_POST['name']), 
        'url' => h(trim($_POST['url'])), 
        'comment' => h($_POST['comment']), 
        'linkpage_url' => h(trim($_POST['linkpage_url'])), 
        'to_admin' => h($_POST['to_admin']), 
        'class_id' => h($_POST['class']), 
        );

    $model_Site = &FLEA::getSingleton('Model_Site');
    if($model_Site->save($s_data)){
      $model_Setseo = FLEA::getSingleton('Model_Setseo');
      $seo = $model_Setseo->find("action ='".$_GET['action']."'");
      if(!$seo){
        $seo = $model_Setseo->find("action ='index'");
      }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
      $viewData = array( 'hostlink' => $this->host_dir,
          'bread' => $bread,
          'seo' => $seo,
          'state' => '2',
          'id' => h($_POST['id']),
          );
      $this->executeView("Site".DS."siteupdateresult.html", $viewData); 
    }
    else
    {
      $this->redirect(url('site', 'index'));
    }

  }


  function actionPassupdatesubmit()
  {
    $c_data = array(
        'consumer_id' => h($_POST['c_id']),
        'consumer_pass' => hash('md5', h($_POST['pass'])),
        );
    $model_Consumer = &FLEA::getSingleton('Model_Consumer');
    $site_id = h($_POST['id']);
    if($model_Consumer->save($c_data))
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'seo' => $this->seo,
          'state' => '1',
          'id' => $site_id,
          );
      $this->executeView("Site".DS."passupdateresult.html", $viewData); 
    }
    else
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'seo' => $this->seo,
          'state' => '2',
          'id' => $site_id,
          );
      $this->executeView("Site".DS."passupdateresult.html", $viewData); 
    }

  }

  function actionCheckurl()
  {
    $url = trim($_POST['url']);
    $model_Site = &FLEA::getSingleton('Model_Site');
    $cond = "url='".$url."'";
    if(isset($_POST['site_id'])){
      $cond .= " and id <> ".$_POST['site_id'];
    }
    if($site = $model_Site->find($cond))
    {
      if(isset($_POST['site_id'])&&$_POST['site_id']!=$site['id']){
      echo '2';
      }else if(!isset($_POST['site_id'])){
      echo '2';
      }
    }
    else if(isset($_POST['image_code'])){
      $imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
      if(!$imgcode->check($_POST['image_code'])){
        echo '3';
      }else{
        echo '1';
      }
    }
  }

  /**
   * 检索站点
   */
  function actionSitesearch()
  {
    session_start;
    header("Cache-control: private");
    session_cache_limiter('private');
    if('POST' == $_SERVER['REQUEST_METHOD'])
    {
      $word = h($_POST['word']);
      $word = $this->make_semiangle($word);
      $word = mb_convert_kana($word);
      $method = h($_POST['method']); 
      $words = explode(' ' ,$word);
    }
    else
    {
      $word = h($_GET['word']);
      $word = $this->make_semiangle($word);
      $word = mb_convert_kana($word);
      $method = h($_GET['method']); 
      $words = explode('+' ,$word);
    }
    foreach($words as $key=>$keyword)
    {
      $words[$key] = "(name like '%".$keyword."%'";
      $words[$key] .= " or comment like '%".$keyword."%')";
    }

    if ('and' == $method)
    {
      $cond = '(';
      $cond .= implode(' and ', $words);
      $cond .= ')';
    }
    else if ('or' == $method)
    {
      $cond = '(';
      $cond .= implode(' or ', $words);
      $cond .= ')';
    }
    session_start;
    if($cond){
      $_SESSION['cond'] = $cond;
      $cond .= " and show_state='1'";
      /*
    }else if(isset($_GET['page'])&&$_GET['page']!=''){
      */
    }else{
      $cond = $_SESSION['cond'];
      $cond .= " and show_state='1'";
      /*
    }else{
      unset($_SESSION['cond']);
      $cond = 'false';
      */
    }

    $model_Site = &FLEA::getSingleton('Model_Site');
    FLEA::loadHelper('Pager');
    $sort = "updated,is_recommend desc";
    if (isset($_GET['page'])&&$_GET['page']<2){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $page = $_GET['page']?$_GET['page']:1;
    $show = $_POST['hyouji']?$_POST['hyouji']:10;
    $pager = & new FLEA_Helper_Pager($model_Site, $page, $show, $cond, $sort,'1');
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $page_info = $pager->getPagerData();
    $last_page = array_pop($page_info['pagesNumber']);
    if (isset($last_page)&&$page > $last_page){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
    }
    $viewData = array( 'hostlink' => $this->host_dir,
        'bread' => $bread,
        'seo' => $seo,
        'pager' => $pager,
        'word' => $word,
        'method' => $method,
        );

    $this->executeView("Site".DS."sitesearchresult.html", $viewData); 


  }


  /**
   * send mail to admin 
   */
  function actionMailadmin()
  {
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    $viewData = array( 'hostlink' => $this->host_dir,
        'bread' => $bread,
        'seo' => $seo,
        'id' => $_GET['id'],
        );
    $this->executeView("Site".DS."mailtoadmin.html", $viewData); 
  }

  function actionMailadminsubmit()
  {
    $Model_User = &FLEA::getSingleton("Model_User");
    $cond = "username = 'haomai'";
    $User = $Model_User->find($cond);
    $to = $User['email'];
    $subject = "=?UTF-8?B?".
      base64_encode('通知').
      "?=";
    $message = '通知:'."<br>";
    $message .= "<br>";
    $message .= 'ID:'."<br>";
    $message .= $_POST['id']."<br>";
    $message .= "<br>";
    $message .= '通知種別:'."<br>";
    $message .= $_POST['no_link']?$_POST['no_link']."<br>":'';
    $message .= $_POST['move']?$_POST['move']."<br>":'';
    $message .= $_POST['bana_no_link']?$_POST['bana_no_link']."<br>":'';
    $message .= $_POST['ill']?$_POST['ill']."<br>":'';
    $message .= $_POST['other']?$_POST['other']."<br>":'';
    $message .= "<br>";
    $message .= 'コメント:'."<br>";
    $message .= $_POST['com'] . "<br>";
    if (!empty($_POST['fname']))
    {
      $message .= 'お名前:'."<br>"; 
      $message .= $_POST['fname']. "<br>";
    }

    if (!empty($_POST['femail']))
    {
      $message .= 'E-Mail:'."<br>"; 
      $message .= $_POST['femail']. "<br>";
    }
    if (!empty($_POST['femail'])){
      $From_Mail = $_POST['femail']; 
    }else{
      $Model_User = &FLEA::getSingleton("Model_User");
      $cond = "username = 'haomai'";
      $User = $Model_User->find($cond);
      $From_Mail = $User['email'];
    }
    $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";  
    $headers .= 'From: '.$From_Mail. "\r\n";
    $model_Setseo = FLEA::getSingleton('Model_Setseo');
    $seo = $model_Setseo->find("action ='".$_GET['action']."'");
    if(!$seo){
      $seo = $model_Setseo->find("action ='index'");
    }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
    if (mail($to, $subject, $message, $headers))
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'bread' => $bread,
          'seo' => $seo,
          'state' => 1,
          );
    }
    else
    {
      $viewData = array( 'hostlink' => $this->host_dir,
          'seo' => $seo,
          'state' => 2,
          );
    }

    $this->executeView("Site".DS."mailtoadminsuccess.html", $viewData); 
  }
  function actionHelppage()
  {
    $this->executeView("Site".DS."helppage.html", array()); 
  }
  /*
     function actionDetailed()
     {
     session_start;
     if(!$_SESSION['post_save']){
     $model_Class = &FLEA::getSingleton('Model_Class');
     $model_Class->enableLink('children');
     $topClass = $model_Class->getAllTopClassesOrder();
//全部主题
$model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
$frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
$str = '';
$str .= '<option value="" selected>指定しない</option>';
foreach($topClass  as $k => $v){
$value = $v['children'];
$count = count($value);
$str .="<option value='".$v['class_id']."'>";
$str .=$v['name'];
$str .="</option>";
for($i=0;$i<$count;$i++){
$str.="<option value='".$value[$i]['class_id']."'>";
$str.=$v['name']."・".$value[$i]['name'];
$str.="</option>";
}
}
$method_str = $this->mkoptionstr($this->method_arr);
$engine_str = $this->mkoptionstr($this->engine_arr);
$day_str = $this->mkoptionstr($this->day_arr);
$type_str = $this->mkoptionstr($this->type_arr);
}else{
$model_Class = &FLEA::getSingleton('Model_Class');
$model_Class->enableLink('children');
$topClass = $model_Class->getAllTopClassesOrder();
  //全部主题
  $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
  $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
  $str = '';
  if($_SESSION['save_post']['search_kt']==''){
  $str .= '<option selected="selected" value="" >指定しない</option>';
  }else{
  $str .= '<option value="" >指定しない</option>';
  }
  foreach($topClass  as $k => $v){
  $value = $v['children'];
  $count = count($value);
  $str .="<option value='".$v['class_id']."'>";
  $str .=$v['name'];
  $str .="</option>";
  for($i=0;$i<$count;$i++){
  if($value[$i]==$_SESSION['save_post']['search_kt']){
  $str.="<option selected='selected' value='".$value[$i]['class_id']."'>";
  }else{
  $str.="<option value='".$value[$i]['class_id']."'>";
  }
  $str.=$v['name']."・".$value[$i]['name'];
  $str.="</option>";
  }
  }
  $method_str = $this->mkoptionstr($this->method_arr,
  $_SESSION['post_save']['method']);
  $engine_str = $this->mkoptionstr($this->engine_arr,
  $_SESSION['post_save']['engine']);
  $day_str = $this->mkoptionstr($this->day_arr,
  $_SESSION['post_save']['search_day']);
  $type_str = $this->mkoptionstr($this->type_arr,
  $_SESSION['post_save']['open_type']);
  }
  $blank = isset($_GET['window'])?"_blank":"";
  $this->executeView("Site".DS."detail.html", 
  array('blank' =>$blank,
  'option_str' => $str,
'method_str' => $method_str,
  'engine_str' => $engine_str,
  'day_str' => $day_str,
  'type_str' => $type_str,
  'his' => isset($_SESSION['post_save'])?$_SESSION['post_save']:''
  )); 
  }
  */
function actionSitemap()
{
  $model_Class = &FLEA::getSingleton('Model_Class');
  $model_Class->enableLink('children');
  $topClass = $model_Class->getAllTopClassesOrder();
  //全部主题
  $model_Frequent_Special2 = &FLEA::getSingleton('Model_FrequentSpecial2');
  $frequentSpecial2 = $model_Frequent_Special2->findAll(null,'`order` DESC');
  //dump($topClass);
  $model_Setseo = FLEA::getSingleton('Model_Setseo');
  $seo = $model_Setseo->find("action ='".$_GET['action']."'");
  if(!$seo){
    $seo = $model_Setseo->find("action ='index'");
  }
    $this->getBreadcrumb();
    $bread =  $this->bread->trail(' &raquo; ');
    $seo = $this->replace_seo($this->bread,$seo);
  $viewData = array( 'hostlink' => $this->host_dir,
      'bread' => $bread,
      'seo' => $seo,
      'class' => $topClass,
      'frequentSpecial2' => $frequentSpecial2,
      );
  $this->executeView("Site".DS."sitemap.html",$viewData);
}
function mkoptionstr($arr ,$sel=null)
{
  $str = '';
  $flag = true;
  foreach($arr as $k => $v){
    if($sel == null && $flag){
      $falg=false;
      $str .= "<option selected='selected' value='";
      $str .=$v."'>";
      $str .=$k."</option>";
    }else if($v==$sel && $flag){
      $falg=false;
      $str .= "<option selected='selected' value='";
      $str .=$v."'>";
      $str .=$k."</option>";
    }else{
      $str .= "<option value='";
      $str .=$v."'>";
      $str .=$k."</option>";
    }
  }
  return $str;
}


/**
 *检测url
 */
function linkcheck($url,$linkpage_url,$admin_mode = true){
  if(substr($url,-1,1)!='/'){
    $url.='/';
  }
  if(substr($linkpage_url,-1,1)!='/'){
    $linkpage_url.='/';
  }
  $bln = array();
  $bln[error_message] = "";
  $my_site_url = FLEA::getAppInf('site_url');

  //临时修改为http://orangehousing.jp
  $global = &FLEA::getSingleton('Model_Global');
  $dir = $global->find('name = "set_new_dir"');
  $anchor = $dir['value'];
  //临时设置anchor为空
  $anchor = '';
  $linktag = '&lt;a href=&quot;'.$my_site_url.'&quot; target=&quot;_blank&quot;&gt;'.$anchor.'&lt;/a&gt;';
  $UnixSockString = "";

  // 相互リンク設置URLのhttp://を削除
  $ChkrelinkURL = str_replace("http://", "", $linkpage_url);

  // 相互リンク設置URLのHostをゲット
  $Host = substr($ChkrelinkURL, 0, strpos($ChkrelinkURL, "/"));

  // 相互リンク設置URLのPathをゲット
  $Path = substr($ChkrelinkURL, strpos($ChkrelinkURL, "/"));

  if(strpos($Path, "/") == "0"){
    $Path_check = substr($Path, 1);
  } else{
    $Path_check = $Path;
  }
    $tmp_path_arr = explode('/',$Path);
    if($Path!='/'&&array_pop($tmp_path_arr)==''){
     $Path = substr($Path,0,-1);
    }


  // サイトURLのhttp://を削除
  $site_url_nonhttp = str_replace("http://", "", $url);

  // サイトURLのHostをゲット
  $site_url_host = substr($site_url_nonhttp, 0, strpos($site_url_nonhttp, "/"));

  // サイトURLURLのPathをゲット
  $site_url_path = substr($site_url_nonhttp, strpos($site_url_nonhttp, "/"));

  //自サイトURL
  $my_site_url = str_replace("http://", "", $my_site_url);
  $my_site_url = substr($my_site_url, 0, strpos($my_site_url, "/"));

  // 登録URLとリンク設置URLが同じか確認
  if ($Host != $site_url_host) {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>サイトURLと相互リンク設置URLのドメインが違います。</li>";
      $bln[err_flag] = true;
    }
    return $bln;
    exit;
  }

  //80接続
  @$fp = fsockopen($Host, 80, $ErrNo, $ErrStr, 10);
  if (!$fp) {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>相互リンク先が見つかりません。</li>";
      $bln[err_flag] = true;
    }
  }
  else {
    // 読み込みのタイムアウト設定
    socket_set_timeout($fp, 2);
    fputs($fp, "GET ". $Path . " HTTP/1.0\r\nHost:" . $Host .
        "\r\nReferer:".url('site','linkcheck_submit')."\r\n\r\n");
    while(!feof($fp))
      $UnixSockString.=fgets($fp, 128);
    // タイムアウトしたか調べる
    $stat = socket_get_status($fp);
    if ($stat["timed_out"]) {
      $bln[state] = false;
      if(!$admin_mode){
        $bln[error_message] .= "<li>相互リンク設置先がタイムアウトしました。</li>";
        $bln[err_flag] = true;
      }
    }
  }
  @fclose($fp);
  //@$pos = strpos($UnixSockString, $my_site_url);
  $preg_str = '/<a [^>]*href=[\"\']{0,1}http:(\/\/){0,1}';
  $url_sub_arr = explode('.',$my_site_url);
  foreach($url_sub_arr as $value){
    $preg_str .= $value.'\.';
  }
  $preg_str = substr($preg_str,0,-2);
  $preg_str_start = $preg_str;
  $preg_str .= '[\/]{0,1}[\"\']{0,1}[^>]*>/i';
  @$pos = preg_match($preg_str
//    preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.iimy\.co\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}>/i'
        ,$UnixSockString);
  //リンク済みの場合True
  if ($pos > 0) {
    $bln[state] = true;
  } else {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>相互リンクが完了していません。<br />あなた様のサイトへ下記のリンクタグ<br />"."$linktag"."<br />をそのまま貼り付けてください。<br />タグを改変するとリンクされません。</li>";
      $bln[err_flag] = true;
    }
  }

  if($bln[state] == false){

    $tmp_path_arr = explode('/',$Path);
    if($Path!='/'&&array_pop($tmp_path_arr)==''){
     $Path = substr($Path,0,-1);
    }
  // 登録URLとリンク設置URLが同じか確認
  if ($Host != $site_url_host) {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>サイトURLと相互リンク設置URLのドメインが違います。</li>";
      $bln[err_flag] = true;
    }
    return $bln;
    exit;
  }
   $UnixSockString ='';
  //80接続
  @$fp = fsockopen($Host, 80, $ErrNo, $ErrStr, 10);
  if (!$fp) {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>相互リンク先が見つかりません。</li>";
      $bln[err_flag] = true;
    }
  }
  else {
    // 読み込みのタイムアウト設定
    socket_set_timeout($fp, 2);
    fputs($fp, "GET ". $Path . " HTTP/1.0\r\nHost:" . $Host .
        "\r\nReferer:".url('site','linkcheck_submit')."\r\n\r\n");
    while(!feof($fp))
      $UnixSockString.=fgets($fp, 128);
    // タイムアウトしたか調べる
    $stat = socket_get_status($fp);
    if ($stat["timed_out"]) {
      $bln[state] = false;
      if(!$admin_mode){
        $bln[error_message] .= "<li>相互リンク設置先がタイムアウトしました。</li>";
        $bln[err_flag] = true;
      }
    }
  }
  @fclose($fp);
  //@$pos = strpos($UnixSockString, $my_site_url);
  @$pos = preg_match($preg_str
//    preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.iimy\.co\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}>/i'
        ,$UnixSockString);
  //リンク済みの場合True
  if ($pos > 0) {
    $bln[state] = true;
  } else {
    $bln[state] = false;
    if(!$admin_mode){
      $bln[error_message] .= "<li>相互リンクが完了していません。<br />あなた様のサイトへ下記のリンクタグ<br />"."$linktag"."<br />をそのまま貼り付けてください。<br />タグを改変するとリンクされません。</li>";
      $bln[err_flag] = true;
    }
  }
  }

  $UnixSockString2 = "";
    $site_url_host = trim($site_url_host);
    $UnixSockString2 = @file_get_contents('http://'.$site_url_host,false,$header);
    $regExp = $preg_str_start.'[^>]*>/i';
    //  '/<a\s{0,}(class=(\'|")\w+(\'|")){0,}\s+(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp(\/\w+){0,}(\.(html|php)){0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}>/i';
    $pos2 = preg_match($regExp,$UnixSockString2);
      if ($pos2>0) {
        $bln[is_recommend] = true;
      } else {
        $bln[is_recommend] = false;
        if(!$admin_mode){
          $bln[error_message] .= $Path_check."<li>登録サイトURLに相互リンク設置URLへのリンクがありません。</li>";
          $bln[err_flag] = true;
        }

      }


  //リンク済みの場合True
  /*
  if($bln[state] == true && $url != $linkpage_url){

    //80接続
    $fp2 = fsockopen($site_url_host, 80, $ErrNo, $ErrStr, 10);
    if (!$fp2) {
      $bln[state] = false;
      if(!$admin_mode){
        $bln[error_message] .= "<li>登録サイトが見つかりません。</li>";
        $bln[err_flag] = true;
      }
      return $bln;
      exit;
    }
    else {
      // 読み込みのタイムアウト設定
      socket_set_timeout($fp2, 2);
      while(!feof($fp))
        fputs($fp2, "GET ".$site_url_path ." HTTP/1.0\r\nHost:".$site_url_host."\r\nReferer:".url('site','index')."\r\n\r\n");
      while(!feof($fp2))
        $UnixSockString2.=fgets($fp2, 128);
      // タイムアウトしたか調べる
      $stat = socket_get_status($fp2);
      if ($stat["timed_out"]) {
        $bln[state] = false;
        if(!$admin_mode){
          $bln[error_message] .= "<li>登録サイトがタイムアウトしました。</li>";
          $bln[err_flag] = true;
        }
        exit;
      }
    }
    fclose($fp2);
    $pos2 =
      preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}>/i'
          ,$UnixSockString2);
    //  $pos2 = strpos($UnixSockString2, $Path_check);

    //リンク済みの場合True
    if ($pos2 > 0) {
      $bln[state] = true;
    } else {
      $bln[state] = false;
      if(!$admin_mode){
        $bln[error_message] .= $Path_check."<li>登録サイトURLに相互リンク設置URLへのリンクがありません。</li>";
        $bln[err_flag] = true;
      }

    }

  }
  */
  return $bln;
}
function getSEO($action=''){

}
function getBreadcrumb()
{
  $bread = new breadcrumb();
  $bread->add(FLEA::getAppInf('p_name'),'/');
  $script_name_arr = explode('/',$_SERVER['SCRIPT_NAME']);
  $bread->add('相互リンク集','/'.$script_name_arr[1]);
  $arr = array(
      'all' => '一覧',
      'search' => '検索結果',
      'regist' => '新規登録',
      'linkcheck_submit' => '新規登録完了',
      'renew' => '更新されたサイト',
      'siteupdate' => '修正サイト',
      'sitedel' =>'削除サイト',
      'mailadmin' => '送信',
      'mailadminsubmit'=> '送信完了',
      'helppage'=>'ヘルプ',
      'sitemap'=>'サイトマップ',
      'detailed'=>'詳細検索',
      'new'=>'新着サイト',
      'recommend'=>'おすすめサイト',
      'editsite'=>"修正・削除",
      'sitesearch' => "検索結果",
      );
  if(isset($_POST['preview'])&&$_POST['preview']=='on'){
    $arr['linkcheck_submit'] = '新規登録確認';
  }
  if(isset($_GET['action'])&&$_GET['action']!=''&&$_GET['action']!='class'){
    if($show = $arr[$_GET['action']]){
      $bread->add($show,$_SERVER['REQUEST_URI']);
    }
  }
  $this->bread = $bread;
}
  function replace_seo($bread,$seo){
    $replace = $search = array();
    $breadcrumb_str = $bread->trail_title(' &raquo; ');
    $breadcrumb_lat = '';
    $breadcrumb_second = ''; 
    $breadcrumb_arr = explode('&raquo;', $breadcrumb_str);
    if (is_array($breadcrumb_arr)) {
      $bread_num = count($breadcrumb_arr); 
      $breadcrumb_lat = trim($breadcrumb_arr[$bread_num-1]);  
      if (isset($breadcrumb_arr[1])) {
        $breadcrumb_second = trim($breadcrumb_arr[1]); 
      }
    }
    array_shift($breadcrumb_arr);
    if(count($breadcrumb_arr)>2){
    array_shift($breadcrumb_arr);
    }
    $breadcrumb_str = implode(' &raquo; ',$breadcrumb_arr);
    
    $breadcrumb_keywords = str_replace(' &raquo; ', ',', trim($breadcrumb_str)); 
    
    $search  = array_merge(array('#STORE_NAME#','#BREADCRUMB#', '#PAGE_TITLE#', '#BREADCRUMB_KEYWORDS#', '#BREADCRUMB_FIRST#'), $search);
    $replace = array_merge(array(FLEA::getAppInf('p_name'), $breadcrumb_str, $breadcrumb_lat, $breadcrumb_keywords, $breadcrumb_second), $replace);
    if (!in_array('#SEO_PAGE#', $search)) {
      $c_page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
      $search = array_merge(array('#SEO_PAGE#'), $search); 
      $replace = array_merge(array($c_page.'ページ目'), $replace); 
    }
    $seo['title']       = str_replace($search, $replace, $seo['title']);
    $seo['keywords']    = str_replace($search, $replace, $seo['keywords']);
    $seo['description'] = str_replace($search, $replace, $seo['description']);
    
    $seo['title'] = str_replace(' &raquo; ', ' ', $seo['title']); 
    $seo['keywords'] = str_replace(' &raquo; ', ' ', $seo['keywords']); 
    $seo['description'] = str_replace(' &raquo; ', ' ', $seo['description']); 

    return $seo; 
  }

function make_semiangle($str)  
 {  
     $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',  
                  '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',  
                  'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',  
                  'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',  
                  'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',  
                  'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',  
                  'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',  
                  'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',  
                  'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',  
                  'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',  
                  'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',  
                  'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',  
                  'ｙ' => 'y', 'ｚ' => 'z',  
                  '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',  
                  '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',  
                  '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',  
                  '》' => '>',  
                  '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',  
                  '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',  
                  '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',  
                  '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',  
                  '　' => ' ');  
    return strtr($str, $arr);  
 }
}

class breadcrumb {
  var $_trail;
  function breadcrumb() {
    $this->reset();
  }

  function reset() {
    $this->_trail = array();
  }

  function add($title, $link = '') {
    $this->_trail[] = array('title' => $title, 'link' => $link);
  }

  function trail($separator = ' - ') {
    $trail_string = '';

    for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
      if (isset($this->_trail[$i]['link']) && $this->tep_not_null($this->_trail[$i]['link'])&&$i<$n-1) {
        $trail_string .= '<a href="' . $this->_trail[$i]['link'] . '" class="headerNavigation">' . $this->_trail[$i]['title'] . '</a>';
      } else {
        $trail_string .= $this->_trail[$i]['title'];
      }

      if (($i+1) < $n) $trail_string .= $separator;
    }

    return $trail_string;
  }

  function trail_title($separator = ' - ') {
    $trail_string = '';

    for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
      $trail_string .= $this->_trail[$i]['title'];

      if (($i+1) < $n) $trail_string .= $separator;
    }

    return $trail_string;
  }
  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }
}
