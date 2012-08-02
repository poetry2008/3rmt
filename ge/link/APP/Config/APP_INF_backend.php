<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set("display_errors", "On");

// 定义应用程序设置
// 更多设置参考 FleaPHP 文档
return array(
/**
     * 是否将 url 参数中包含的控制器名字和动作名字强制转为小写
     * 可以避免在 url 中输入大写字符导致找不到控制器
     */
     'urlLowerChar'              => true,
     


     /**
     * FleaPHP 内部及 cache 系列函数使用的缓存目录
     */
     'internalCacheDir'          => APP_DIR . DS . '_Cache',

     /**
     * Url 重写 现设为
     */
//     'urlMode'          => URL_REWRITE,

     /**
      * View 设置
      */
      'view'=>'FLEA_View_Smarty',
      'viewConfig'=>array(
      'smartyDir'=>'./Smarty',
      'template_dir'=> APP_DIR.'/View',
      'compile_dir'=> APP_DIR.'/View/View_c',
      'left_delimiter'=>'<!--{',
      'right_delimiter'=>'}-->',
      'force_compile'=>true,
      'cache'=>false,
      ),
      /**
       * DefaultController 设置
       */
       'defaultController' => 'admin',

       /**
		* 启用多语言支持
 		*/
 		'multiLanguageSupport' => true,
 		/**
     * 指定语言文件所在目录
     */
     'languageFilesDir' => APP_DIR. DS.'Languages',
     //'defaultLanguage'=>'chinese-utf8',
     'defaultLanguage'=>'japaness-utf8',
     'languages' => array(
     '简体中文' => 'chinese-utf8',
     '日语' => 'japaness-utf8',
     '英文' => 'en-utf8',
     ),

     /**
     * 指定要使用的调度器
     */
     'dispatcher' => 'FLEA_Dispatcher_Auth',
     /**
     * 指示 RBAC 组件用什么键名在 session 中保存用户数据
     */
     'RBACSessionKey' => 'CMS',
     /**
     * 使用默认的控制器 ACT 文件
     *
     * 这样可以避免为每一个控制器都编写 ACT 文件
     */
     'defaultControllerACTFile' => dirname(__FILE__) . DS . 'DefaultACT.php',

     /**
     * 必须设置该选项为 true，才能启用默认的控制器 ACT 文件
     */
     'autoQueryDefaultACTFile' => true,

     /**
     * Url 重写 现设为
     */
     //'urlMode'          => URL_REWRITE,

     /**
      * 权限控制 相关
      */
//      'dispatcherAuthFailedCallback' => 'ON_ACCESS_DENIED',
//      'dispatcherFailedCallback'=>'ON_ACCESS_DENIED',
//      'controllerACTLoadWarning'=>false,



      );
