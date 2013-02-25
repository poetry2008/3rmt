<?php
class AD_Option_DbRecord 
{
  var $id;
  function __construct()
  {
    
  }
/*---------------------------
 功能: 获取数据库
 参数：$id(number) ID值
 返回值：无
 --------------------------*/
  function getFromDb($id)
  {
  }
/*---------------------------
 功能：获取结果对象
 参数：$sql(resource) SQL语句
 参数：$obj(string) 类的名字
 返回值：结果对象
 --------------------------*/
  function getResultObject($sql,$obj)
  {
    return tep_db_fetch_object($this->query($sql),$obj);
    
  }
/*--------------------------
 功能：得到的结果对象
 参数：$sql(resource) SQL语句
 参数：$obj(string) 类的名字
 返回值: 结果集的对象集合(array)
 -------------------------*/
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
/*-------------------------
 功能：查询SQL 
 参数：$sql(resource) SQL语句
 返回值：MySQL查询值
 ------------------------*/
  function query($sql)
  {
    return tep_db_query($sql);


  }
}
