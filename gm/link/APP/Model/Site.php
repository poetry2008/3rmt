<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Site extends FLEA_Db_TableDataGateway
{
    function __construct(){
    parent::FLEA_Db_TableDataGateway();
    //$this->disableLinks();
  }
  var $tableName = 'gm_site';
  var $primaryKey = 'id';
  var $count = 30;
  var $format='m-d';
    var $hasOne = array(
        array(
            'tableClass'=>'Model_FrequentSite',
            'mappingName'=>'frequent',
            'foreignKey'=>'site_id',
        ),
    );

  /**
   * 取得需要的数据
   *
   * @param unknown_type $config
   */
  function listing($config,$cond = null){
    $pageNo=$config['pageNo'];
    $pageSize=$config['pageSize'];
    $sortName='`'.$config['sortName'].'`';
    $sortOrder=$config['sortOrder'];
    $category=$config['category'];

    $sort=$sortName.' '.$sortOrder;
    $limit=array($pageSize,$pageNo*$pageSize);
    $data=$this->findAll($cond,$sort,$limit);
    foreach($data as $key=>$value){
      $data[$key]['created']=date($this->format,$value['created']);
      $data[$key]['updated']=date($this->format,$value['updated']);
    }
    return array(
      'count'=>$this->findCount($cond),
      'data'=>$data
    );
  }

    /**
     * 根据类 删除站点
     * 其中$class_ids 的数据型式 为 class 的findAll的结果型式
     *
     * @param unknown_type $class_ids
     */
    function removeSiteByClassId($class_ids){
      $this->enableLink("frequent");
      $condiction = array();
      foreach ($class_ids as $key=>$value) {
        $condiction[]=array("class_id",$value['class_id'],"=","OR");
//      $pkv[] = $value['class_id'];
      }
      return $this->removeByConditions($condiction);
    }



}
