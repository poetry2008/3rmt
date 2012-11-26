<?php
/**
 * 定义 FLEA_Rbac_RolesManager 类
 */

// {{{ includes
FLEA::loadClass('FLEA_Db_TableDataGateway');
// }}}

/**
 * FLEA_Rbac_RolesManager 派生自 FLEA_Db_TableDataGateway，
 * 用于访问保存角色信息的数据表
 *
 * 如果数据表的名字不同，应该从 FLEA_Rbac_RolesManager
 * 派生类并使用自定义的数据表名字、主键字段名等。
 *
 * @package Core
 */
class FLEA_Rbac_RolesManager extends FLEA_Db_TableDataGateway
{
    /**
     * 主键字段名
     *
     * @var string
     */
    var $primaryKey = 'role_id';

    /**
     * 数据表名字
     *
     * @var string
     */
    var $tableName = 'nezumy_roles';

    /**
     * 角色名字段
     *
     * @var string
     */
    var $rolesNameField = 'rolename';

    /**
     * 构造函数
     *
     * @param array $params
     *
     * @return FLEA_Rbac_RolesManager
     */
    function FLEA_Rbac_RolesManager($params = null)
    {
        parent::FLEA_Db_TableDataGateway($params);
    }
}
