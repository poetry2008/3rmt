<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Class extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  var $tableName  = 'gp_class';
  var $primaryKey = 'id';
    var $hasMany    = array(
        //子分类
        array(
            'tableClass'=>'Model_Class',
            'mappingName'=>'children',
            'foreignKey'=>'parent'
        ),
        //分类下的站点
        array(
            'tableClass'=>'Model_Site',
            'mappingName'=>'sites',
            'foreignKey'=>'classId'
        ),
    );

    /**
    *
    */
    function _create($name,$parentId = 0,$order = 0,$thumb = '')
    {

    }

    /**
    *
    */
    function _get($classId)
    {

    }


    /**
    *
    */
    function _update($classId,$name,$parentId,$order,$thumb)
    {

    }

    /**
    * 删除分类及分类下的子分类和站点
    */
    function _delete($classId)
    {

    }


    /**
    * 访问量+1
    * 父类访问+1?????????????
    */
    function _visit($classId)
    {

    }



}
