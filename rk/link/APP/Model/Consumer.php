<?php
FLEA::loadClass('FLEA_Rbac_UsersManager');
class Model_Consumer extends FLEA_Rbac_UsersManager
{
  function __construct(){
    parent::FLEA_Db_TableDataGateway();
  }

  var $tableName = 'rk_consumer';
  var $primaryKey = 'consumer_id'; 


}
