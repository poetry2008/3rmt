<?php
/////////////////////////////////////////////////////////////////////////////
// FleaPHP Framework
//
// Copyright (c) 2005 - 2008 QeeYuan China Inc. (http://www.qeeyuan.com)
//
// 许可协议，请查看源代码中附带的 LICENSE.txt 文件，
// 或者访问 http://www.fleaphp.org/ 获得详细信息。
/////////////////////////////////////////////////////////////////////////////

/**
 * 定义 Table_Nodes 类
 *
 * @copyright Copyright (c) 2005 - 2008 QeeYuan China Inc. (http://www.qeeyuan.com)
 * @author 起源科技 (www.qeeyuan.com)
 * @package Example
 * @subpackage SHOP
 * @version $Id$
 */

// {{{ includes
FLEA::loadClass('FLEA_Db_TableDataGateway');
// }}}

/**
 * Table_Nodes 用“改进型先根遍历算法”在数据库中存储层次化的数据（通常所说的无限分类）
 *
 * 由于“改进型先根遍历算法”要求所有节点都是唯一一个根节点的子节点。
 * 所以 Table_Nodes 假定一个名为“_#_ROOT_NODE_#_”的节点为唯一的根节点。
 *
 * 应用程序在调用 Table_Nodes::create() 创建第一个节点时，会自动
 * 判断根节点是否存在，并创建根节点。
 *
 * 对于应用程序来说，“_#_ROOT_NODE_#_”节点是不存在的。所以，应用程序
 * 可以创建多个父节点 ID 为 0 的“顶级节点”。这些顶级节点实际上就是
 * “_#_ROOT_NODE_#_”节点的直接子节点。
 *
 * @package Example
 * @subpackage SHOP
 * @author 起源科技 (www.qeeyuan.com)
 * @version 1.0
 */
class Model_Nodes extends FLEA_Db_TableDataGateway
{
    /**
     * 数据表名称，在继承类中可能会被覆盖
     *
     * @var string
     */
    var $tableName = 'rg_nodes';

    /**
     * 主键字段名，在继承类中可能会被覆盖
     *
     * @var string
     */
    var $primaryKey = 'node_id';

    /**
     * 根节点名
     *
     * @var string
     */
    var $_rootNodeName = '_#_ROOT_NODE_#_';

    /**
     * 添加一个节点，返回该节点的 ID
     *
     * @param array $node
     * @param int $parentId
     *
     * @return int
     */
    function create($node, $parentId) {
        $parentId = (int)$parentId;
        if ($parentId) {
            $parent = parent::find($parentId);
            if (!$parent) {
                // 指定的父节点不存在
                FLEA::loadClass('Exception_NodeNotFound');
                __THROW(new Exception_NodeNotFound($parentId));
                return false;
            }
        } else {
            // 如果 $parentId 为 0 或 null，则创建一个顶级节点
            $parent = parent::find(array('name' => $this->_rootNodeName));
            if (!$parent) {
                // 如果根节点不存在，则自动创建
                $parent = array(
                    'name' => $this->_rootNodeName,
                    'left_value' => 1,
                    'right_value' => 2,
                    'parent_id' => -1,
                );
                if (!parent::create($parent)) {
                    return false;
                }
            }
            // 确保所有 _#_ROOT_NODE_#_ 的直接字节点的 parent_id 都为 0
            $parent[$this->primaryKey] = 0;
        }

        // 根据父节点的左值和右值更新数据
        $sql = "UPDATE {$this->fullTableName} SET left_value = left_value + 2 " .
               "WHERE left_value >= {$parent['right_value']}";
        $this->dbo->execute($sql);
        $sql = "UPDATE {$this->fullTableName} SET right_value = right_value + 2 " .
               "WHERE right_value >= {$parent['right_value']}";
        $this->dbo->execute($sql);

        // 插入新节点记录
        $node = array(
            'name' => $node['name'],
            'left_value' => $parent['right_value'],
            'right_value' => $parent['right_value'] + 1,
            'parent_id' => $parent[$this->primaryKey],
        );
        return parent::create($node);
    }

    /**
     * 更新节点信息
     *
     * @param array $node
     *
     * @return boolean
     */
    function update($node) {
        unset($node['left_value']);
        unset($node['right_value']);
        unset($node['parent_id']);
        return parent::update($node);
    }

    /**
     * 删除一个节点及其子节点树
     *
     * @param array $node
     *
     * @return boolean
     */
    function remove($node) {
        $span = $node['right_value'] - $node['left_value'] + 1;
        $sql = "DELETE FROM {$this->fullTableName} " .
               "WHERE left_value >= {$node['left_value']} " .
               "AND right_value <= {$node['right_value']}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }

        $sql = "UPDATE {$this->fullTableName} " .
               "SET left_value = left_value - {$span} " .
               "WHERE left_value > {$node['right_value']}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }

        $sql = "UPDATE {$this->fullTableName} " .
               "SET right_value = right_value - {$span} " .
               "WHERE right_value > {$node['right_value']}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }

        return true;
    }

    /**
     * 删除一个节点及其子节点树
     *
     * @param int $nodeId
     *
     * @return boolean
     */
    function removeByPkv($nodeId) {
        $node = parent::find((int)$nodeId);
        if (!$node) {
            FLEA::loadClass('Exception_NodeNotFound');
            __THROW(new Exception_NodeNotFound($nodeId));
            return false;
        }

        return $this->remove($node);
    }

    /**
     * 返回根节点到指定节点路径上的所有节点
     *
     * 返回的结果不包括“_#_ROOT_NODE_#_”根节点各个节点同级别的其他节点。
     * 结果集是一个二维数组，可以用 array_to_tree() 函数转换为层次结构（树型）。
     *
     * @param array $node
     *
     * @return array
     */
    function getPath($node) {
        $conditions = $this->qinto('left_value < ? AND right_value > ?', array($node['left_value'], $node['right_value']));
        $sort = 'left_value ASC';
        $rowset = $this->findAll($conditions, $sort);
        if (is_array($rowset)) {
            array_shift($rowset);
        }
        return $rowset;
    }

    /**
     * 返回指定节点的直接子节点
     *
     * @param array $node
     *
     * @return array
     */
    function getSubNodes($node) {
        $conditions = $this->qinto('parent_id = ?', array($node[$this->primaryKey]));
        $sort = 'left_value ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 返回指定节点为根的整个子节点树
     *
     * @param array $node
     *
     * @return array
     */
    function getSubTree($node) {
        $conditions = $this->qinto('left_value BETWEEN ? AND ?', array($node['left_value'], $node['right_value']));
        $sort = 'left_value ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 获取指定节点同级别的所有节点
     *
     * @param array $node
     *
     * @return array
     */
    function getCurrentLevelNodes($node) {
        $conditions = array('parent_id' => $node['parent_id']);
        $sort = 'left_value ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 取得所有节点
     *
     * @return array
     */
    function getAllNodes() {
        return parent::findAll('left_value > 1', 'left_value ASC');
    }

    /**
     * 获取所有顶级节点（既 _#_ROOT_NODE_#_ 的直接子节点）
     *
     * @return array
     */
    function getAllTopNodes() {
        $conditions = "parent_id = 0";
        $sort = 'left_value ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 计算所有子节点的总数
     *
     * @param array $node
     *
     * @return int
     */
    function calcAllChildCount($node) {
        return intval(($node['right_value'] - $node['left_value'] - 1) / 2);
    }
}
