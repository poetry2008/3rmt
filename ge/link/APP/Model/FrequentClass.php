<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_FrequentClass extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    //$this->disableLinks();
  }
  var $tableName  = 'gm_frequent_class';
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
    /**
     * 根据参数删除记录 参数类型为
     * Array
  (
    [0] => Array
        (
            [class_id] => 6
        )

    [1] => Array
        (
            [class_id] => 7
        )

    [2] => Array
        (
            [class_id] => 16
        )

)

     *
     * @param unknown_type $class_id
     */
  function removeFrequentClass($class_id){
    $condictions = array();
    foreach ($class_id as $key=>$value) {
      $condictions[]=array("class_id",$value['class_id'],"=","OR");
    }
    return $this->removeByConditions($condictions);
  }
}
