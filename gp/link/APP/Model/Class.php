<?php
FLEA::loadClass("Model_Nodes");
class Model_Class extends Model_Nodes
{
  function __construct(){
    parent::FLEA_Db_TableDataGateway();
    $this->disableLinks();
  }
  /**
     * 数据表名称
     *
     * @var string
     */
  var $tableName = 'rg_classes';

  /**
     * 主键字段名
     *
     * @var string
     */
  var $primaryKey = 'class_id';
  var $hasMany    = array(
  //子分类
        array(
            'tableClass'=>'Model_Class',
            'mappingName'=>'children',
            'foreignKey'=>'parent_id'
        ),
  //分类下的站点
        array(
            'tableClass'=>'Model_Site',
            'mappingName'=>'sites',
            'foreignKey'=>'class_id'
        ),
  );
    var $hasOne = array(
        array(
            'tableClass'=>'Model_FrequentClass',
            'mappingName'=>'frequentClass',
            'foreignKey'=>'class_id',
        ),
        array(
            'tableClass'=>'Model_FrequentSpecial',
            'mappingName'=>'frequentSpecia',
            'foreignKey'=>'class_id',
        ),
    );

  /**
     * 取得所有类别
     *
     * @return array
     */
  function getAllClasses() {
    return $this->getAllNodes();

  }

  function getAllClassesSelector() {
          $classSelector = '<select name="class" size="7" class="categoey_selector">';

          $allTop = $this->getAllTopClasses();

          foreach($allTop as $item)
          {
            $classSelector .= $this->getClassSelector($item['class_id']);
          }
          $classSelector .= '</select>';

          return $classSelector;

  }
        function getClassSelector($classId, $indent="&nbsp;&nbsp;")
        {
          $class = $this->getClass($classId);

          if ($class)
          { 
            $selector = '';
            $selector .= '<option value="'.$class["class_id"].'">';
            $selector .= $indent . $class['name'];
            $selector .= '</option>';

            if ($class['children'])
            { 
              foreach($class['children'] as $item)
              {
                $selector .= $this->getClassSelector($item['class_id'],
                    $indent.$indent);
              }
            }

            return $selector;
          }

          return $selector;
        }


  /**
     * 取得所有顶级分类
     *
     * @return array
     */
  function getAllTopClasses() {
    return $this->getAllTopNodes();
  }

  function getAllTopClassesOrder() {
    $conditions = "parent_id = 0";
    $sort = '`order` DESC';
    return $this->findAll($conditions, $sort);
  }

  /**
     * 取得指定 ID 的分类
     *
     * @param int $classId
     *
     * @return array
     */
  function getClass($classId) {
    return $this->find((int)$classId);
  }

  /**
     * 获得带关联的分类
     */
  function getClassWithLinks($classId)
  {
    $this->enableLinks();
    $class = $this->find($classId);
    $this->disableLinks();
    return $class;
  }


  /**
     * 取得指定分类的所有直接子分类
     *
     * @param array $class
     *
     * @return array
     */
  function getSubClasses($class) {
    return $this->getSubNodes($class);
  }

  /**
     * 创建新分类，并返回新分类的 ID
     *
     * @param array $class
     * @param int $parentId
     *
     * @return int
     */
  function createClass($class, $parentId) {
    return $this->create($class, $parentId);
  }

  /**
     * 更新分类信息
     *
     * @param array $class
     *
     * @return boolean
     */
  function updateClass($class) {
    return $this->update($class);
  }

  /**
     * 删除指定的分类及其子分类树
     *
     * @param array $class
     *
     * @return boolean
     */
  function removeClass($class) {
    return $this->remove($class);
  }

  /**
     * 删除指定 ID 的分类及其子分类树
     *
     * @param int $classId
     *
     * @return boolean
     */
  function removeClassById($classId) {
    return $this->removeByPkv($classId);
  }

  /**
   * 删除分类 要删除所有子类，并将所有子类站点改变状态（或删除）
   * 并删除常用的内容
   *
   * @param unknown_type $classID
   * @return unknown
   */
  function removeSiteByClassId($classID) {
    $node = parent::find((int)$classID);
    if (!$node) {
      FLEA::loadClass('Exception_NodeNotFound');
      __THROW(new Exception_NodeNotFound($nodeId));
      return false;
    }
    $condictions = array(
    array("left_value",$node['left_value'],">=","AND"),
    array("right_value",$node['right_value'],"<=","AND"),
    );
    $nodes = $this->findAll($condictions,null,null,"class_id");
    $model_FrequentClass = & FLEA::getSingleton('Model_FrequentClass');
    /* @var $model_FrequentClass Model_FrequentClass */
    $model_FrequentClass->removeFrequentClass($nodes);
    $model_Site = & FLEA::getSingleton('Model_Site');
    /* @var $model_Site Model_Site */
    $model_Site->removeSiteByClassId($nodes);
    //return $this->remove($node);
    return true;
  }

  /**
     * 获取指定分类同级别的所有分类
     *
     * @param array $node
     *
     * @return array
     */
  function getCurrentLevelClasses($class) {
    return $this->getCurrentLevelNodes($class);
  }

  /**
     * 计算所有子分类的总数
     *
     * @param array $class
     *
     * @return int
     */
  //    function calcAllChildCount($class) {
  //        return $this->calcAllChildCount($class);
  //    }
        /**
          * 取得当前类别的父类
          *
          * @param 
          * $classId current class id
          */ 
}
