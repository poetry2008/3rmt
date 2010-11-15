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
  //    <div id="bd"><div id="inf"><strong>bobhero</strong> で検索した結果　1～10件目 / 約452件 - 0.38秒</div>
  //  var $countPreg = "/<div\sid=\"bd\"><div\sid=\"inf\"><strong>.*<\/strong>(.*)<\/div>/";
  //var $countPreg = "/<div\s+id=resultStats>約\s+(.*)件中\s+\d+\s+ページ目<nobr>\s+.(.*)秒.&nbsp;<\/nobr><\/div>/";
  var $countPreg = "/<div id=resultStats>(.*)<nobr>/";
  var $searchEnter = 'http://search.yahoo.co.jp/search?p={{keyword}}&search.x=1&fr=top_ga1_sa&tid=top_ga1_sa&ei=UTF-8&aq=&oq=jg';
  var $currentPageNumber = 1;
  var $pageCountNumber =0;

  function init($keyword,$countLimit=10){
    $this->keyword = $keyword;
    $this->keywordi =     preg_replace("/ +/","+",$keyword);
    $this->countLimit = $countLimit;
    //    $this->preSearch();
  }

  function search(){
    $this->tmpResult = $this->preSearch();
    $this->currentResult = '';
    $this->currentPage = 1;
    
    while($this->currentPage < $this->pageCount){
      $tmpPage = file_get_contents($this->makeUrl($this->currentPage));
      $this->dbdriver->saveResult($this->parseResult($tmpPage));
    }

    
  }
  function makeUrl($page=1){
    //http://search.yahoo.co.jp/search?p=bobhero&aq=-1&ei=UTF-8&pstart=1&fr=top_ga1_sa&b=11
    // 2 http://search.yahoo.co.jp/search?p=bobhero&aq=-1&ei=UTF-8&pstart=1&fr=top_ga1_sa&b=11
    // http://search.yahoo.co.jp/search?p=link%3Ahttp%3A%2F%2Frmt.gvx.co.jp+リンク&search.x=1&fr=top_ga1_sa&tid=top_ga1_sa&ei=UTF-8&aq=&oq=
    // http://search.yahoo.co.jp/search?p=link%3Ahttp%3A%2F%2Frmt.gvx.co.jp+%E3%83%AA%E3%83%B3%E3%82%AF&aq=-1&ei=UTF-8&pstart=1&fr=top_ga1_sa&dups=1&b=251
//    $nextpage = "http://search.yahoo.co.jp/search?p={{keyword}}&aq=-1&ei=UTF-8&pstart=1&fr=top_ga1_sa&dups=1&b={{pager}}1";
    if($page >=1){
    $page = $page-1;
    }else {
    $page = 0;
    }
    //$nextpage =
    //  "http://www.google.co.jp/search?as_oq={{keyword}}&aq=f&ie=utf-8&pstart=1&fr=top_ga1_sa&start={{pager}}&hl=ja&num=10";
    $nextpage =
      "http://www.google.co.jp/search?q={{keyword}}&aq=f&ie=utf-8&pstart=1&fr=top_ga1_sa&start={{pager}}&hl=ja&num=10";
    //$nextpage = 
    //  "http://www.google.co.jp/search?q=FF14+RMT&hl=ja&newwindow=1&ei=EGvPTIWsK4yKuAOpuo3VBg&start=10&sa=N";
      //      return str_replace('{{keyword}}',urlencode($this->keywordi),$this->searchEnter);
    $url = str_replace('{{keyword}}',urlencode($this->keywordi),$nextpage);
    $url = str_replace('{{pager}}',$page,$url);
    $this->currentUrl = $url;
    return $url;
  }
  //找出有多少结果,算出有多少页
  function preSearch(){
    $this->currentHtml = @iconv("SHIFT-JIS","UTF-8//TRANSLIT//IGNORE",file_get_contents($this->makeUrl()));
    $this->currentPageNumber = 1;
    $this->getPageCount();
  }
  

  function getPageCount(){
    //    <div id="bd"><div id="inf"><strong>bobhero</strong> で検索した結果　1～10件目 / 約452件 - 0.38秒</div>
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
    $cutStart  = "<ol>";
    $cutEnd  = '</ol></div>';
    $html = @iconv("SHIFT-JIS","UTF-8//TRANSLIT//IGNORE",$html);
    $html = getMiddle($cutStart,$cutEnd,$html);
    $resultArray = explode('<li class=g',$html);
//    $parsePreg = "/<a\shref=\"(.*)\">(.*)<\/a><div>(.*)<\/div>.*/";
    $parsePreg =
      '/<h3\s+class="r\s{0,}[0-9a-zA-Z_]{0,}"><a\s.*href=\"(.*)\"(.*)<\/a><\/h3>.*<div\s+class="s\s{0,}[0-9a-zA-Z_]{0,}">(.*)<\/div>.*/';
    $recordArray = array();
    $count = 1;
    unset($resultArray[0]);
    $i=0;
    foreach ($resultArray as $result ){
      $i++;
      if($i > 10){
        break;
      }
      preg_match($parsePreg,$result,$match);
      preg_match("/>(.*)$/",$match[2],$title);
      $recordArray[] = array(
                             'keyword'=>$this->keyword,
                             'title'=>$title[1],
                             'fullurl'=>$match[1],
                             'description'=>$match[3],
                             'page_number'=>$this->currentPageNumber,
                             'order_number'=>$count,
                             'order_total_number'=>10*($this->currentPageNumber-1)+$count,
                             'created_at'=>time(),
                             'siteurl'=>getSiteUrl($match[1]),
                             );
      $count ++;
    }
    return $recordArray;
  }
  

  function getCurrentPageResult(){
    $this->currentHtml = file_get_contents($this->makeUrl($this->currentPageNumber));
    return $this->parseResult($this->currentHtml);
  }

}


function getMiddle($start,$end,$html)
{
  $first =  explode($start,$html);
  $first = $first[1];
  $first = explode($end,$first);
  return $first[0];

}
function getSiteUrl($url){
  return getMiddle('//','/',$url);
}
