<?php
class DbRecord 
{
  var $id;
  function __construct()
  {
    
  }
  function getFromDb($id)
  {
  }
  function getResultObject($sql,$obj)
  {
    return tep_db_fetch_object($this->query($sql),$obj);
    
  }
  function getResultObjects($sql,$obj)
  {
    $resouse = $this->query($sql);
    $result = array();
    while( $singleObj = tep_db_fetch_object($resouse,$obj))
      {
       $result[] = $singleObj;
      }
    return $result;
    
  }
  function query($sql)
  {
    return tep_db_query($sql);


  }
}
