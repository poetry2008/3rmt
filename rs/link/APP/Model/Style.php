<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Class extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  var $tableName = 'rs_style';
  var $primaryKey = 'id';

}
