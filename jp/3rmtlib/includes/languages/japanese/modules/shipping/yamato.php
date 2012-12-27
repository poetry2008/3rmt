<?php
define('MODULE_SHIPPING_YAMATO_TEXT_TITLE',       'ヤマト運輸');
define('MODULE_SHIPPING_YAMATO_TEXT_DESCRIPTION', 'ヤマト宅急便による配送料金');

define('MODULE_SHIPPING_YAMATO_TEXT_WAY_NORMAL','宅急便');
define('MODULE_SHIPPING_YAMATO_TEXT_WAY_COOL',  'クール宅急便');

define('MODULE_SHIPPING_YAMATO_TEXT_NOTAVAILABLE', '要求のあったサービスは、選択された地域間では提供されません.');
define('MODULE_SHIPPING_YAMATO_TEXT_OVERSIZE',     '重量またはサイズが制限を超えています.');
define('MODULE_SHIPPING_YAMATO_TEXT_ILLEGAL_ZONE', '指定された都道府県が不正です.');
define('MODULE_SHIPPING_YAMATO_TEXT_OUT_OF_AREA',  '配達区域外です.');

// 到达时间段设置
$GLOBALS['a_yamato_time']=array(
  array('id'=>'希望なし',  'text'=>'希望なし'),
  array('id'=>'午前中',    'text'=>'午前中'),
  array('id'=>'12時〜14時','text'=>'12時~14時'),
  array('id'=>'14時〜16時','text'=>'14時~16時'),
  array('id'=>'16時〜18時','text'=>'16時~18時'),
  array('id'=>'18時〜20時','text'=>'18時~20時'),
  array('id'=>'20時〜21時','text'=>'20時~21時'),
);
?>