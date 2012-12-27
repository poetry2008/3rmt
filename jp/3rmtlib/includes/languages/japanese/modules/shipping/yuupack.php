<?php
define('MODULE_SHIPPING_YUUPACK_TEXT_TITLE',       '一般小包郵便物');
define('MODULE_SHIPPING_YUUPACK_TEXT_DESCRIPTION', '一般小包郵便物(ゆうパック)による配送料金');

define('MODULE_SHIPPING_YUUPACK_TEXT_WAY_NORMAL','ゆうパック');

define('MODULE_SHIPPING_YUUPACK_TEXT_NOTAVAILABLE','要求のあったサービスは、選択された地域間では提供されません.');
define('MODULE_SHIPPING_YUUPACK_TEXT_OVERSIZE',    '重量またはサイズが制限を超えています.');
define('MODULE_SHIPPING_YUUPACK_TEXT_ILLEGAL_ZONE','指定された都道府県が不正です.');
define('MODULE_SHIPPING_YUUPACK_TEXT_OUT_OF_AREA', '配達区域外です.');
define('MODULE_SHIPPING_YUUPACK_TEXT_CONFIG_ERROR','設定が不正です.');

// 配送时间段指定设置
$GLOBALS['a_yuupack_time']=array(
  array('id'=>'希望なし',            'text'=>'希望なし'),
  array('id'=>'午前(9時〜12時ごろ)', 'text'=>'午前(9時〜12時ごろ)'),
  array('id'=>'午後(13時〜16時ごろ)','text'=>'午後(13時〜16時ごろ)'),
  array('id'=>'夕方(17時〜19時ごろ)','text'=>'夕方(17時〜19時ごろ)'),
  array('id'=>'夜間(19時〜21時ごろ)','text'=>'夜間(19時〜21時ごろ)'),
);
?>