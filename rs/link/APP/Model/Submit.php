<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Submit extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  var $tableName = 'ff_submit';
  var $primaryKey = 'id';

    /**
    *
    */
    function _delete($submitId)
    {
        $this->removeByPkv($submitId);
    }

}
