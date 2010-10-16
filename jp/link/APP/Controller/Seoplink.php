<?php
FLEA::loadClass("Controller_Base");
class Controller_Seoplink extends Controller_Base{
  function __construct()
  {
    parent::__construct();
    $view = & $this->_getView();
    $view->template_dir = APP_DIR.DS."View".DS."Admin";
    $this->model_Site = FLEA::getSingleton('Model_Site');
  } 
  function actionIndex()
  {
  } 
#admin.php?mode=admin&admin_mode=admin_top
  function actionadmintop()
  {
    $cond = 'is_custom = "1"';
    $order = 'state, `order` DESC';
    $sites = $this->model_Site->findAll();
    $model_Class = FLEA::getSingleton('Model_Class');
    foreach($sites as $key=>$site)
    { 
      //$class_array = array(
          //array('id' => "ルート"),
          //);
      $class = $model_Class->find($site['class_id']);
      $sites[$key]['class_name'] = $class['name'];

    }

    $viewData = array(
        'sites' => $sites,
        );

    $this->executeView("seoplinkadmintop.html",$viewData);
  }

  /*
   *delete one site as same as site/del
   */

  function actionLinkcheckdelete()
  {
    $id = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $model_Consumer = &FLEA::getSingleton('Model_Consumer');
    $c_cond = "site_id='".$id."'";
    $model_Consumer->removeByConditions($c_cond);
    $model_Site->enableLinks();
    $this->addMsg(_T($model_Site->removeByPkv($id)?'site_del_success':'site_del_failed'));
    $model_Site->disableLinks();
    redirect(url('seoplink','admintop'));
  }

  /*
   *update one site linkcheck
   */

  function actionLinkcheckupdate()
  {
    $siteId = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $site = $model_Site->find($siteId);
    $blt = $this->linkcheck($site['url'], $site['linkpage_url']);
    $state = $blt['state']?'1':'0';
    $is_recommend = $blt['is_recommend']?'1':'0';
    if(isset($_GET['show'])){
    $data = array(
        'id' => $site['id'],
        'state' => $state,
        'show_state' => $_GET['show'],
        );
    }else if(isset($_GET['king'])){
    $data = array(
        'id' => $site['id'],
        'state' => $state,
        'is_king' => $_GET['king'],
        );
    }else if(isset($_GET['recommend'])){
    $data = array(
        'id' => $site['id'],
        'state' => $state,
        'is_recommend' => $_GET['recommend'],
        );
    }else {
    $data = array(
        'id' => $site['id'],
        'state' => $state,
        'is_recommend' => $is_recommend,
        );
    }
    $model_Site->save($data);
    if($state){
      $this->addMsg(_T('リンクチェック完了'));
    }else{
      $this->addMsg(_T('リンクチェックできませんでした'));
    }

    /*
    if(isset($_GET['from1'])&&isset($_GET['from2'])
        &&$_GET['from1']!=''&&$_GET['from2']!=''){
    redirect(url($_GET['from1'],$_GET['from2']));
    }else{
    redirect(url('seoplink','admintop'));
    }
    */
    redirect($_SERVER['HTTP_REFERER']);
  }
  function actionLinkcheckstate()
  {
    $blt = $this->linkcheck($_POST['url'], $_POST['linkpage_url']);
    $state = $blt['state']?'1':'0';
    echo $state; 
  }

  function actionLinkcheck()
  {
    $link = isset($_GET['link'])&&$_GET['link']?$_GET['link']:'/';
    $viewData = array('link' => $link);
    $this->executeView("runlink.html",$viewData);
  }

  function actionLinkcheckshow()
  {
    $global = &FLEA::getSingleton('Model_Global');
    $date = $global->find('name = "set_new_date"');
    $siteId = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $site = $model_Site->find($siteId);
    $model_Class = &FLEA::getSingleton('Model_Class');
    $class = $model_Class->find($site['class_id']);
    $model_Consumer = &FLEA::getSingleton('Model_Consumer');
    $consumer = $model_Consumer->find("site_id = ".$site['id']);
    $created = date("Y-m-d H:i",$site['created']);
    $flag_new = false;
    if(time()-intval($site['created']) < intval($date['value'])*60*60){
      $flag_new = true;
    }
    $viewData = array(
        'site' => $site,
        'class' => $class,
        'consumer'=> $consumer,
        'created'=>$created,
        'flag_new'=>$flag_new,
        );
    $this->executeView("seoplinkcheckshow.html",$viewData);
  }


  //修改也 回显数据
  function actionLinkcheckedit()
  {
    $siteId = (int)$_GET['id'];
    $model_Site = &FLEA::getSingleton('Model_Site');
    $site = $model_Site->find($siteId);
    $model_Class = &FLEA::getSingleton('Model_Class');
    $class = $model_Class->find($site['class_id']);
    $classes = $model_Class->getAllClasses();

    if (count($classes) == 0) {
      //如果无分类提示添加分类
      //js_alert(_T('ui_p_create_class_first'), '', url('BoProductClasses'));
    }
    $str_classes = '';
    $right = array();
    foreach ($classes as $class_row):
      $c = count($right);
    if ($c > 0) {
      while ($c > 0 && $right[$c - 1] < $class_row['right_value'])
      {
        array_pop($right);
        $c = count($right);
      }
    }
    $className = t(str_repeat('  ', $c) . $class_row['name'] . '      ');
    $right[] = $class_row['right_value'];
    $str_classes .= '<option
      value="'.$class_row['class_id'].'"'.($class_row['class_id']==$site['class_id']?' selected="selected"':'').'>'.$className."</option>\n";
    endforeach;

    $global = &FLEA::getSingleton('Model_Global');
    $date = $global->find('name = "set_new_date"');
    $model_Consumer = &FLEA::getSingleton('Model_Consumer');
    $consumer = $model_Consumer->find("site_id = ".$site['id']);
    $created = date("Y-m-d H:i",$site['created']);
    $flag_new = false;
    if(time()-intval($site['created']) < intval($date['value'])*60*60){
      $flag_new = true;
    }
    $viewData = array(
        'site' => $site,
        'class' => $class,
        'str_classes' => $str_classes,
        'consumer'=> $consumer,
        'created'=>$created,
        'flag_new'=>$flag_new,
        );
    $this->executeView("seoplinkcheckedit.html",$viewData);
  }
  /*
     *check link all
     */
  function actionLinkcheckupdateall()
  {
    $cond = 'is_custom = "1"';
    $sites = $this->model_Site->findAll($cond,'`order` DESC');

    foreach ($sites as $site)
    {
      $blt = $this->linkcheck($site['url'], $site['linkpage_url']);
      $state = $blt['state']?'1':'0';
      $is_recommend = $blt['is_recommend']?'1':'0';
      $data = array(
          'id' => $site['id'],
          'state' => $state,
          'is_recommend' => $is_recommend,
          );
      $this->model_Site->save($data);
    }
    redirect(url('seoplink','admintop'));
  }

  function actionEditDo(){
    $post = $_POST;
    $date_time_arr = explode(' ',trim($post['created']));
    $date_arr = explode('-',$date_time_arr[0]);
    $time_arr = explode(':',$date_time_arr[1]);
    $new_timestamp = mktime($time_arr[0],$time_arr[1],0,$date_arr[1],$date_arr[2],$date_arr[0]);
    $data = array(
          'id' => $post['id'],
          'name' => trim($post['name']),
          'url' => trim($post['url']),
          'comment' => trim($post['comment']),
          'class_id' => $post['class'],
          'linkpage_url' => trim($post['linkpage_url']),
          'created' => $new_timestamp,
        );
    $this->model_Site->save($data);
    redirect(url('seoplink','admintop'));
  }
  /**
    *batch
    */
  function actionbatchdo()
  {
    $ids = $_POST['ids'];
    $action = $_POST['batchdo_action'];

    switch ($action)
    {
      case 'delete':
        $this->deleteBatch($ids);
        break;
      case 'update':
        $this->updateBatch($ids);
        break;
    }

    redirect(url('seoplink','admintop'));

  }

  /**
    * batch delete
    * parameter 
    * $ids  an array of site_id
    * return
    * success true
    * fail false
    */
  function deleteBatch($ids)
  {
    $no_error = true;
    foreach ($ids as $id)
    {
      $state = $this->model_Site->removeByPkv($id);
      $no_error &= $state;
      $model_Consumer = &FLEA::getSingleton('Model_Consumer'); 
      $c_cond = "site_id='".$id."'";
      $model_Consumer->removeByConditions($c_cond);
    }

    $this->addMsg(_T($no_error?'site_del_success':'site_del_failed'));

    return $no_error;
  }


  /*
   *check link
   */

  function linkcheck($url,$linkpage_url,$admin_mode = true){
    if(substr($url,-1,1)!='/'){
      $url .='/';
    }
    if(substr($linkpage_url,-1,1)!='/'){
      $linkpage_url .='/';
    }
    $bln = array();
    $bln[error_message] = "";
    $my_site_url = FLEA::getAppInf('site_url');
    $global = &FLEA::getSingleton('Model_Global');
    $dir = $global->find('name = "set_new_dir"');
    $anchor = $dir['value'];
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


    // サイトURLのhttp://を削除
    $site_url_nonhttp = str_replace("http://", "", $url);
    if(!preg_match("|/$|",$url)){
        $site_url_nonhttp .='/';
    }
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
      fputs($fp, "GET ". $Path . " HTTP/1.0\r\nHost:" . $Host .  "\r\n".
          "\r\nReferer:".url('site','index')."\r\n\r\n");
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
    /*
    $pos = preg_match('/<a\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/(\w+\/){0,}(\w+\.html){0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}")>/'
        ,$UnixSockString);
    */
  $preg_str = '/<a [^>]*href=[\"\']{0,1}http:(\/\/){0,1}';
  $url_sub_arr = explode('.',$my_site_url);
  foreach($url_sub_arr as $value){
    $preg_str .= $value.'\.';
  }
  $preg_str = substr($preg_str,0,-2);
  $preg_str_start = $preg_str;
  $preg_str .= '[\/]{0,1}[\"\']{0,1}[^>]*>/i';
    $pos = preg_match($preg_str
  //   preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"\w+"){0,}>/i'
        ,$UnixSockString);
    //$pos = strpos($UnixSockString, $my_site_url);

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
    $UnixSockString = '';
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
      fputs($fp, "GET ". $Path . " HTTP/1.0\r\nHost:" . $Host .  "\r\n".
          "\r\nReferer:".url('site','index')."\r\n\r\n");
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
    /*
    $pos = preg_match('/<a\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/(\w+\/){0,}(\w+\.html){0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}")>/'
        ,$UnixSockString);
    */
    $pos = preg_match($preg_str
//      preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"\w+"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"\w+"){0,}>/i'
        ,$UnixSockString);
    //$pos = strpos($UnixSockString, $my_site_url);

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

    $opts = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"User-Agent:Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)", 
        'Referer' =>  url('site','index'),
      )
    );
    $site_url_host = trim($site_url_host);
    $UnixSockString2 = file_get_contents('http://'.$site_url_host,false,$header);
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
      @$fp2 = fsockopen($site_url_host, 80, $ErrNo, $ErrStr, 10);
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
        fputs($fp2, "GET ". $site_url_path . " HTTP/1.0\r\nHost:" . $site_url_host.
            "\r\nReferer:".url('site','index')."\r\n\r\n");
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
      @fclose($fp2);
    $pos2 =
      preg_match('/<a\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}\s{0,}href\s{0,}=\s{0,}"http:(\/\/){0,1}www\.orangehousing\.jp\/{0,1}"\s{0,}(target\s{0,}=\s{0,}"(\_{0,1}blank){0,}"){0,}>/i'
        ,$UnixSockString2);
      //$pos2 = strpos($UnixSockString2, $my_site_url);

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


}
