<?php
FLEA::loadClass("Controller_Base");
class Controller_Search extends Controller_Base{
    var $url = array(
        'Yahoo'    => 'http://search.yahoo.co.jp/search?p=',
        'Google'   => 'http://www.google.com/search?&q=',
        '雅虎词典' => 'http://dic.yahoo.co.jp/dsearch?enc=UTF-8&stype=0&dtype=2&p=',
        'InfoSeek' => 'http://search.www.infoseek.co.jp/Web?qt=',
        'Goo'      => 'http://search.goo.ne.jp/web.jsp?from=searchtop_web&MT=',
        'Excite Japan' => 'http://www.excite.co.jp/search.gw?search=',
        'Vector'   => 'http://search.vector.co.jp/search?query=',
        '楽天市場' => 'http://esearch.rakuten.co.jp/rms/sd/esearch/vc?sv=2&sitem=',
        'BK1'      => 'http://www.bk1.jp/webap/user/SchBibList.do?keyword=',
        'amazon'   => 'http://www.amazon.co.jp/s/ref=nb_ss_gw?field-keywords=',
        //'Nezumy'   => url('site','search',array('keywords'=>'')),
    );

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->url['Nezumy'] = url('site','sitesearch').'/word/';
     }

	function actionIndex(){
?>
    <form action='<?=url('search','search')?>' method='post' target='_blank'>
    <input type='text' name='keywords' />
    <input type='submit' /><br>

    <!--<input type='checkbox' name='sites[]' value='yahoo' />Yahoo
    <input type='checkbox' name='sites[]' value='google' />Google-->

<?  foreach($this->url as $k=>$u){?>
    <input type='checkbox' name='sites[]' value='<?=$k?>' /><?=$k?>
<?  }?>
    </form>
<?
	}

    function actionSearch(){
        $keywords = urlencode($_POST['keywords']);
        //$log = unserialize(file_get_contents('./search_log.php'));
        //dump($_POST);
?>
<script>
window.onload = function(){
    //dump($this->url[$_POST['sites'][$i]]);
  <?
    if($_POST['engine']!='Nezumy'){
?>
    window.location="<?=$this->url[$_POST['engine']].$keywords?>";
<?  }else{
?>
    window.location="<?=$this->url[$_POST['engine']].$keywords.'/method/'.$_POST['method']?>";
<?
  }
?>
}
</script>
<?
} 
    function actionSearch_old(){
        $keywords = urlencode($_POST['keywords']);
        //$log = unserialize(file_get_contents('./search_log.php'));
        //dump($_POST);
        $flag=true;
?>
<script>
window.onload = function(){
<? for($i=0;$i<count($_POST['sites']);$i++){
    //dump($this->url[$_POST['sites'][$i]]);
    if($_POST['sites'][$i]!='Nezumy'){
?>
    window.open("<?=$this->url[$_POST['sites'][$i]].$keywords?>","<?=$i+1?>");
<?  }else{
     $flag=false;  
?>
    window.location="<?=$this->url[$_POST['sites'][$i]].$keywords?>";
<?  }
}
  if($flag){
?>
    window.location="<?=url('site','search');?>";
<?
  }
?>
}
</script>
<?
}
/*
   function actionDetailsearch(){
     $word = h($_POST['word']);
     $method = h($_POST['method']);
     $words = explode(' ' ,$word);
     foreach($words as $key=>$keyword){
       $words[$key] = "(name like '%".$keyword."%'";
       $words[$key] .= " or comment like '%".$keyword."%')";
     }
     if ('and' == $method){
       $cond = '(';
       $cond .= implode(' and ', $words);
       $cond .= ')';
     }else if ('or' == $method){
       $cond = '(';
       $cond .= implode(' or ', $words);
       $cond .= ')';
     }
     if($_POST['search_kt']!=''){
     if($_POST['search_kt_ex']=="-b_all"){
       $cond .= " and (`class_id` <> '".h($_POST['search_kt']."')");
     }else{
       $cond .= " and (`class_id` = '".h($_POST['search_kt'])."')";
     }
     }
     if($_POST['search_day']=='today'){
       $timestmp = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
       $cond .= " and (`created` > '".$timestmp."')";
     }else if($_POST['search_day']!=''){
       $day = $_POST['search_day'];
       $timestmp = mktime(0, 0, 0, date("m") , date("d")-$day, date("Y"));
       $cond .= " and (`created` > '".$timestmp."')";
     }
     if($_POST['search_day_ex']!=''&&$_POST['kt_search']=='on'){
       $cond .= " and (`name` like'%".$_POST['search_day_ex']."%')";   
     }
     $cond .= " and (is_custom = '1')";
     $model_Site = &FLEA::getSingleton('Model_Site');
     FLEA::loadHelper('Pager');
     $sort = "id";
     $page = $_GET['page']?$_GET['page']:1;
     $show = $_POST['hyouji']?$_POST['hyouji']:10;
     $pager = & new FLEA_Helper_Pager($model_Site, $page, $show, $cond, 
         $sort,'1');
     $viewData = array(
         'pager' => $pager,
         'word' => $word,
         'method' => $method
         );
     session_start;
     $_SESSION['post_save'] = $_POST;
     $this->executeView("Site".DS."sitesearchresult.html", $viewData);    
     if($_POST['engine']=='Nezumy'){
     }
     else{
      $keywords = urlencode($word);
      ?>
      <script>
      window.onload = function(){
      window.location="<?=$this->url[$_POST['engine']].$keywords?>";
      }
      </script>
      <?
     }
   }
*/
}
