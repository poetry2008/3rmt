<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_FrequentSite extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    //$this->disableLinks();
  }
  var $tableName = 'km_frequent_site';
  var $primaryKey = 'id';
    var $belongsTo = array(
        //关联相对应分类
        array(
            'tableClass'=>'Model_Site',
            'mappingName'=>'site',
            'foreignKey'=>'site_id',
        ),
    );
}
