<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_FrequentSpecial2 extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    //$this->disableLinks();
  }
  var $tableName = 'rk_frequent_special2';
  var $primaryKey = 'class_id';
    var $belongsTo = array(
        //关联相对应分类
        array(
            'tableClass'=>'Model_Class',
            'mappingName'=>'class',
            'foreignKey'=>'class_id',
        ),

    );
    var $hasMany = array(
        //关联相对应站
        array(
            'tableClass'=>'Model_Site',
            'mappingName'=>'sites',
        ),
    );
}
