<?php
define('MODULE_SHIPPING_NIPPONEX_TEXT_TITLE',       'ペリカン便');
define('MODULE_SHIPPING_NIPPONEX_TEXT_DESCRIPTION', 'ペリカン便による配送料金');
define('MODULE_SHIPPING_NIPPONEX_TEXT_WAY_NORMAL',  '宅配便');
define('MODULE_SHIPPING_NIPPONEX_TEXT_WAY_COOL',    'クールペリカン便');
define('MODULE_SHIPPING_NIPPONEX_TEXT_WAY_TOP',     'スーパーペリカン便');
define('MODULE_SHIPPING_NIPPONEX_TEXT_NOTAVAILABLE', '要求のあったサービスは、選択された地域間では提供されません.');
define('MODULE_SHIPPING_NIPPONEX_TEXT_OVERSIZE',     '重量またはサイズが制限を超えています.');
define('MODULE_SHIPPING_NIPPONEX_TEXT_ILLEGAL_ZONE', '指定された都道府県が不正です.');
define('MODULE_SHIPPING_NIPPONEX_TEXT_OUT_OF_AREA',  '配達区域外です.');

// 時間帯サービス
$GLOBALS['a_nipponex_time']=array(
  array('id'=>'希望なし',  'text'=>'希望なし'),
  array('id'=>'08時〜12時','text'=>'08時〜12時'),
  array('id'=>'12時〜16時','text'=>'12時〜16時'),
  array('id'=>'16時〜19時','text'=>'16時〜19時'),
  array('id'=>'19時〜22時','text'=>'19時〜22時'),
);
?>