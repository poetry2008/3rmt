<?php
class helper_pager{
  var $tables;
  var $dbo;
  var $_basePageIndex = 0;
  var $_conditions;
  var $_groupby;
  var $_sortby;
  var $pageSize = -1;
  var $totalCount = -1;
  var $count = -1;
  var $pageCount = -1;
  var $firstPage = -1;
  var $firstPageNumber = -1;
  var $lastPage = -1;
  var $lastPageNumber = -1;
  var $prevPage = -1;
  var $prevPageNumber = -1;
  var $nextPage = -1;
  var $nextPageNumber = -1;
  var $currentPage = -1;
  var $_currentPage = -1;
  var $currentPageNumber = -1;

  function helper_pager($tables, $currentPage, $pageSize = 20, $conditions = 1,
      $groupby=null, $sortby = null,$basePageIndex = 1)
  {
    $this->dbo = db::getConn();
    $this->_basePageIndex = $basePageIndex;
    $this->_currentPage = $this->currentPage = $currentPage;
    $this->pageSize = $pageSize;
    $this->tables = $tables;
    $this->_conditions = $conditions;
    $this->_groupby = $groupby;
    $this->_sortby = $sortby;
    if($groupby){
      $sql ="select count( DISTINCT(".$groupby.")) count from ".$tables." 
        where ".$conditions;
    }else{
      $sql ="select count(*) count from ".$tables." where ".$conditions;
    }
    $res = $this->dbo->query($sql);
    $row = $res->fetch_object();
    $this->totalCount = $this->count = $row->count;
    $this->computingPage();
  }
  function findAll($obj=null)
  {
    if($this->count == -1){
      $this->count = 20;
    }
    $offset = ($this->currentPage - $this->_basePageIndex) * $this->pageSize; 
    $sql = "select * from ".$this->tables
      ." where ".$this->_conditions;
    if($this->_groupby){
      $sql .= " group by ".$this->_groupby;
    }
    if($this->_sortby){
      $sql .= " order by ".$this->_sortby;
    }
    $sql .=" limit ".$offset.",".$this->pageSize;
    $res = $this->dbo->query($sql);
    $arr = array();
    if($obj){
      while($row = $res->fetch_object($obj)){
        $arr[] = $row;
      }
    }else{
      while($row = $res->fetch_object()){
        $arr[] = $row;
      }
    }
    return $arr;
  }
  function find($find = '*')
  {
    if($this->count == -1){
      $this->count = 20;
    }
    $offset = ($this->currentPage - $this->_basePageIndex) * $this->pageSize; 
    $sql = "select ".$find." from ".$this->tables
      ." where ".$this->_conditions;
    if($this->_groupby){
      $sql .= " group by ".$this->_groupby;
    }
    if($this->_sortby){
      $sql .= " order by ".$this->_sortby;
    }
    $sql .=" limit ".$offset.",".$this->pageSize;
    $res = $this->dbo->query($sql);
    $arr = array();
    while($row = $res->fetch_object()){
      $arr[] = $row;
    }
    return $arr;
  }
  function getPagerData($returnPageNumbers = true)
  {
    $data = array(
        'pageSize' => $this->pageSize,
        'totalCount' => $this->totalCount,
        'count' => $this->count,
        'pageCount' => $this->pageCount,
        'firstPage' => $this->firstPage,
        'firstPageNumber' => $this->firstPageNumber,
        'lastPage' => $this->lastPage,
        'lastPageNumber' => $this->lastPageNumber,
        'prevPage' => $this->prevPage,
        'prevPageNumber' => $this->prevPageNumber,
        'nextPage' => $this->nextPage,
        'nextPageNumber' => $this->nextPageNumber,
        'currentPage' => $this->currentPage,
        'currentPageNumber' => $this->currentPageNumber,
        );
    if ($returnPageNumbers) {
      $data['pagesNumber'] = array();
      for ($i = 0; $i < $this->pageCount; $i++) {
        $data['pagesNumber'][$i] = $i + 1;
      }
    }
    return $data; 
  }
  function computingPage()
  {
    $this->pageCount = ceil($this->count / $this->pageSize);
    $this->firstPage = $this->_basePageIndex;
    $this->lastPage = $this->pageCount + $this->_basePageIndex - 1;
    if ($this->lastPage < $this->firstPage) { $this->lastPage = $this->firstPage; }
    if ($this->lastPage < $this->_basePageIndex) {
      $this->lastPage = $this->_basePageIndex;
    }
    if ($this->currentPage >= $this->pageCount + $this->_basePageIndex) {
      $this->currentPage = $this->lastPage;
    }
    if ($this->currentPage < $this->_basePageIndex) {
      $this->currentPage = $this->firstPage;
    }
    if ($this->currentPage < $this->lastPage - 1) {
      $this->nextPage = $this->currentPage + 1;
    } else {
      $this->nextPage = $this->lastPage;
    }
    if ($this->currentPage > $this->_basePageIndex) {
      $this->prevPage = $this->currentPage - 1;
    } else {
      $this->prevPage = $this->_basePageIndex;
    }
    $this->firstPageNumber = $this->firstPage + 1 - $this->_basePageIndex;
    $this->lastPageNumber = $this->lastPage + 1 - $this->_basePageIndex;
    $this->nextPageNumber = $this->nextPage + 1 - $this->_basePageIndex;
    $this->prevPageNumber = $this->prevPage + 1 - $this->_basePageIndex;
    $this->currentPageNumber = $this->currentPage + 1 - $this->_basePageIndex; 
  }
}
