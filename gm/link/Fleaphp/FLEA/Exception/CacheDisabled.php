<?php
/**
 * FLEA_Exception_CacheDisabled 异常指示缓存功能被禁用
 */
class FLEA_Exception_CacheDisabled extends FLEA_Exception
{
    /**
     * 缓存目录
     */
    var $cacheDir;

    /**
     * 构造函数
     *
     * @param string $cacheDir
     *
     * @return FLEA_Exception_CacheDisabled
     */
    function FLEA_Exception_CacheDisabled($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        parent::FLEA_Exception(_ET(0x010200d));
    }
}
