<?php
class DbRecord 
{
  var $id;

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
    
  }

/* -------------------------------------
    功能: 获得db对象 
    参数: $id(int) 值   
    返回值: 无 
------------------------------------ */
  function getFromDb($id)
  {
  }

/* -------------------------------------
    功能: 获得结果对象 
    参数: $sql(string) sql语句   
    参数: $obj(string) 对象   
    返回值: 对象(object) 
------------------------------------ */
  function getResultObject($sql,$obj)
  {
    return tep_db_fetch_object($this->query($sql),$obj);
    
  }

/* -------------------------------------
    功能: 获得结果对象的集合 
    参数: $sql(string) sql语句   
    参数: $obj(string) 对象   
    返回值: 结果集合(array) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得查询结果资源符 
    参数: $sql(string) sql语句   
    返回值: 查询结果资源符(resource) 
------------------------------------ */
  function query($sql)
  {
    return tep_db_query($sql);


  }
}
