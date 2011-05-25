<?php
FLEA::loadClass("FLEA_Db_TableDataGateway");
class Model_Act extends FLEA_Db_TableDataGateway
{
  var $tableName = '14_acts';
  var $primaryKey = 'controller_name';

  function getACT($controllerName) {
    
    $row = parent::find(array($this->primaryKey => strtoupper($controllerName)));
//    dump($row);
//    dump(unserialize($row['act']));
    return unserialize($row['act']);
  }

  function setACT($controllerName, $ACT) {
    $row = array(
    $this->primaryKey => strtoupper($controllerName),
    'act' => serialize($ACT)
    );
    if($this->findcount(array($this->primaryKey=>$row[$this->primaryKey]))){
      return $this->update($row);
    }else{
      return $this->create($row);
    }

  }
  function getAllAct(){
    $rows = $this->findAll();
    foreach ($rows as $value){
      $acts[strtoupper($value[$this->primaryKey])]=unserialize($value['act']);
    }
    return $acts;
  }
}
