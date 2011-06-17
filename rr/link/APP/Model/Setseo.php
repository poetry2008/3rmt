<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Setseo extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  var $tableName = 'rr_setseo';
  var $primaryKey = 'id';


}
