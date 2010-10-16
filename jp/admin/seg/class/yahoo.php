<?php
require_once('engine.php');
require_once('resultSaver.php');
class yahoo implements engine {
  var $encoding = 'utf-8';
  var $keyword;
  var $countLimit;
  var $pageCount;
  var $resultCount;
  var $dbdriver;
  //    <div id="bd"><div id="inf"><strong>bobhero</strong> で検索した結果　1～10件目 / 約452件 - 0.38秒</div>
  //  var $countPreg = "/<div\sid=\"bd\"><div\sid=\"inf\"><strong>.*<\/strong>(.*)<\/div>/";
  var $countPreg = "/<div id=\"bd\"><div id=\"inf\"><strong>.*<\/strong> で検索した結果　1～\d*件目 \/ 約(.*)件.*<\/div>/";
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
    $nextpage = "http://search.yahoo.co.jp/search?p={{keyword}}&aq=-1&ei=UTF-8&pstart=1&fr=top_ga1_sa&dups=1&b={{pager}}1";
    if ($page == 1){
      //      return str_replace('{{keyword}}',urlencode($this->keywordi),$this->searchEnter);
      $url =  str_replace('{{keyword}}',$this->keywordi,$this->searchEnter);
    }else{
    $url = str_replace('{{keyword}}',$this->keywordi,$nextpage);
    $url = str_replace('{{pager}}',$page+1,$url);
    }
    $this->currentUrl = $url;
    return $url;
  }
  //找出有多少结果,算出有多少页
  function preSearch(){
    $this->currentHtml = file_get_contents($this->makeUrl());
    file_put_contents('/tmp/a.txt',$this->currentHtml);
    $this->currentPageNumber = 1;
    $this->getPageCount();

  }
  

  function getPageCount(){
    //    <div id="bd"><div id="inf"><strong>bobhero</strong> で検索した結果　1～10件目 / 約452件 - 0.38秒</div>
    //    preg_match("(.*)",$this->currentHtml,$match);
    preg_match($this->countPreg,$this->currentHtml,$match);
    $this->resultCountNumber = str_replace(',','',$match[1]);
    $this->pageCountNumber = ceil($this->resultCountNumber/10);

    //    return $match;
  }
  function parseResult($html){
    $cutStart  = "<ol>";
    $cutEnd  = '</ol></div>';
    $html = getMiddle($cutStart,$cutEnd,$html);
    $resultArray = explode('</li>',$html);
    $parsePreg = "/<a\shref=\"(.*)\">(.*)<\/a><div>(.*)<\/div>.*/";
    $recordArray = array();
    $count = 1;
    unset($resultArray[count($resultArray)-1]);
    foreach ($resultArray as $result ){

      preg_match($parsePreg,$result,$match);
      $recordArray[] = array(
                             'keyword'=>$this->keyword,
                             'title'=>$match[2],
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
