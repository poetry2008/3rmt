<?php

/**
 * 定义 FLEA_Com_RBAC_Exception_InvalidACT 异常，是 FLEA_Rbac_Exception_InvalidACT 的别名
 */

FLEA::loadClass('FLEA_Rbac_Exception_InvalidACT');

/**
 * 开发者应该直接使用 FLEA_Rbac_Exception_InvalidACT 类
 */
class FLEA_Com_RBAC_Exception_InvalidACT extends FLEA_Rbac_Exception_InvalidACT
{
}
