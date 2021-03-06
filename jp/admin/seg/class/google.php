<?php
require_once('engine.php');
require_once('resultSaver.php');
class google implements engine {
  var $encoding = 'utf-8';
  var $keyword;
  var $countLimit;
  var $pageCount;
  var $resultCount;
  var $dbdriver;
  var $context;
  //  var $countPreg = "/<div\sid=\"bd\"><div\sid=\"inf\"><strong>.*<\/strong>(.*)<\/div>/";
  //var $countPreg = "/<div\s+id=resultStats>約\s+(.*)件中\s+\d+\s+ページ目<nobr>\s+.(.*)秒.&nbsp;<\/nobr><\/div>/";
  var $countPreg = "/<div id=\"resultStats\">[^<]*<nobr>/";
  var $searchEnter = 'http://search.yahoo.co.jp/search?p={{keyword}}&search.x=1&fr=top_ga1_sa&tid=top_ga1_sa&ei=UTF-8&aq=&oq=jg';
  var $currentPageNumber = 1;
  var $pageCountNumber =0;

  function init($keyword,$countLimit=10){
    $this->keyword = $keyword;
    $this->keywordi =     preg_replace("/ +/","+",$keyword);
    $this->countLimit = $countLimit;
    $opts=array('http'=> array('user_agent'=>'Mozilla/5.0 (Windows NT 6.1)
        AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.112 Safari/534.30'));
    $this->context = stream_context_create($opts);
    //    $this->preSearch();
  }

  function search(){
    $this->tmpResult = $this->preSearch();
    $this->currentResult = '';
    $this->currentPage = 1;
    
    while($this->currentPage < $this->pageCount){
      $tmpPage =
        file_get_contents($this->makeUrl($this->currentPage),false,$this->context);
      $this->dbdriver->saveResult($this->parseResult($tmpPage));
    }

    
  }
  function makeUrl($page=1){
    //由于GOOGLE 采用的是 查询开始 所以每个页面要 乘以 10 首条记录为0
    if($page >=1){
    $page = $page-1;
    }else {
    $page = 0;
    }
    $page = intval($page) * 10;
    $nextpage =
      "http://www.google.co.jp/search?q={{keyword}}&ie=utf-8&start={{pager}}&hl=ja&num=10";
    $url = str_replace('{{keyword}}',urlencode($this->keyword),$nextpage);
    $url = str_replace('{{pager}}',$page,$url);
    $this->currentUrl = $url;
    //var_dump($url);
    return $url;
  }
  //找出有多少结果,算出有多少页
  function preSearch(){
    $this->currentHtml = file_get_contents($this->makeUrl(),false,$this->context);
    $this->currentPageNumber = 1;
    $this->getPageCount();
  }
  

  function getPageCount(){
    //    preg_match("(.*)",$this->currentHtml,$match);
    if(preg_match($this->countPreg,$this->currentHtml,$match)){
    preg_match("/(\d+,{0,})+/",$match[0],$match1);
    $this->resultCountNumber = str_replace(',','',$match1[0]);
    $this->pageCountNumber = ceil($this->resultCountNumber/10);
    }else{
    $this->resultCountNumber = '';
    $this->pageCountNumber = 0;
    }

    //    return $match;
  }
  function parseResult($html){
  	//截取 搜索 结果列表
    $cutStart  = "<div id=\"ires\">";
    $cutEnd  = '</ol>';
    $html = getMiddle($cutStart,$cutEnd,$html);
    //区分 是否是自己的搜索结果
    if(preg_match("/.*\<hr[^>]*\>(.*)/",$html,$tmp_html)){
       $html = $tmp_html[1];
    }
    //$html = @iconv("SHIFT-JIS","UTF-8//TRANSLIT//IGNORE",$html);
    //var_dump($html);
    //分割 
    $resultArray = explode('<li class="g"',$html);
//    $parsePreg = "/<a\shref=\"(.*)\">(.*)<\/a><div>(.*)<\/div>.*/";
    $parsePreg =
      '/<h3.*?>.*?<\/h3>.*?<span class=\"st\">(.*)<\/span>/is';
    $recordArray = array();
    $count = 1;
    unset($resultArray[0]);
    $i_res=1;
    foreach ($resultArray as $result ){
      //if(preg_match('/\<li class/',$result)){
          //$tmp_result = explode('<li class',$result);
          //$result = $tmp_result[0];
      //}
      if($i_res > 10){
        break;
      }
      if(preg_match($parsePreg,$result,$match)&&
          !preg_match('/(imagebox|videobox)/',$result)){
      //根据正则判断 该记录是否 有效搜索结果
      preg_match('/<a.*?href=\"\/url\?q=(.*?)\&amp.*?>(.*?)<\/a>/is',$match[0],$title);
      $recordArray[] = array(
                             'keyword'=>$this->keyword,
                             'title'=>$title[2],
                             'fullurl'=>$title[1],
                             'description'=>$match[1],
                             'page_number'=>$this->currentPageNumber,
                             'order_number'=>$count,
                             'order_total_number'=>10*($this->currentPageNumber-1)+$count,
                             'created_at'=>time(),
                             'siteurl'=>getSiteUrl($title[1]),
                             );
      }else{
        continue;
      }
      $i_res++;
      $count ++;
    }
    //print_r($recordArray);
    //exit;
    return $recordArray;
  }
  

  function getCurrentPageResult(){
  	//读取每个页面
    $this->currentHtml =
      file_get_contents($this->makeUrl($this->currentPageNumber),false);
    return $this->parseResult($this->currentHtml);
  }

}


function getMiddle($start,$end,$html)
{
  $first = @explode($start,$html);
  $first = @$first[1];
  $first = @explode($end,$first);
  return $first[0];

}
function getSiteUrl($url){
  return getMiddle('//','/',$url);
}
