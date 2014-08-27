<?php
if (extension_loaded('mbstring')) {
  mb_internal_encoding('UTF-8'); // 指定内部代码
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}
define('TEXT_BULLETIN_BOARD','公告栏');
define('SEARCH','搜索');
define('TEXT_COLLECT','收藏');
define('TEXT_MARK','标记');
define('TEXT_TITLE','标题');
define('TEXT_MANAGER','管理员');
define('TEXT_TO','TO');
define('TEXT_REPLY_NUMBER','回复数');
define('TEXT_UPDATE_TIME','更新日');
define('TEXT_BULLETIN_EDIT_SELECT','请选择');
define('TEXT_BULLETIN_EDIT_DELETE','删除勾选项目');
define('TEXT_DISPLAY_NUMBER_OF_BULLETIN_BOARD', '当前显示<b>%d</b> &sim; <b>%d</b>（共<b>%d</b>项） ');
define('TEXT_DISPLAY_NUMBER_OF_BULLETIN_BOARD_REPLY', '当前显示<b>%d</b> &sim; <b>%d</b>（共<b>%d</b>项） ');
define('TEXT_BACK','返回');
define('TEXT_CREATE_BULLETIN','新建');
define('TEXT_MUST_INPUT','');
define('TEXT_SUBMIT','保存');
define('TEXT_RESET','删除');
define('TEXT_ADDFILE','附件');
define('TEXT_CONTENT','内容');
define('TEXT_GROUP_SELECT','组选择');
define('TEXT_SELECT_ID','id选择');
define('DELETE_STAFF','删除');
define('BUTTON_ADD_TEXT','添加');
define('ADD_STAFF','添加');
define('TEXT_TO_BODY','浏览者');
define('TEXT_STAFF_LIST','员工列表');
define('TEXT_AUTHOR','创建者');
define('TEXT_DONE_TIME','创建日');
define('TEXT_UPDATE_AUTHOR','更新者');
define('TEXT_FILE_DOWNLOAD_URL','附件');
define('TEXT_EDIT_BULLETIN','编辑帖子');
define('TEXT_CREATE_BULLETIN_ERPLY','回复');
define('TEXT_EDIT_BULLETIN_ERPLY','跟帖回复');
define('TEXT_DELETE','删除');
define('TEXT_LAST_BULLETIN','上一个帖');
define('TEXT_NEXT_BULLETIN','下一个贴');
define('TEXT_MUST_WRITE','必填');
define('TEXT_WARNING_EMPTY','请填写');
define('TEXT_CONTENT_REPLY','内容');
define('TEXT_CONTENT_REPLY_LAST','回复');
define('TEXT_CONTENT_NEW','内容');
define('TEXT_CREATE_BULLETIN_REPLY','回复');
define('TEXT_LAST','上一项');
define('TEXT_NEXT','下一项');
?>
