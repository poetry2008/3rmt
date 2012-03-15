<?php
define('MODULE_SHIPPING_SAGAWAEX_TEXT_TITLE',       '佐川急便');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_DESCRIPTION', '佐川急便による配送料金');

define('MODULE_SHIPPING_SAGAWAEX_TEXT_WAY_NORMAL',  '通常便');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_WAY_COOL',    '飛脚クール便');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_WAY_TOP',     '飛脚航空便・飛脚トップ便');

define('MODULE_SHIPPING_SAGAWAEX_TEXT_NOTAVAILABLE', '要求のあったサービスは、選択された地域間では提供されません.');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_OVERSIZE',     '重量またはサイズが制限を超えています.');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_ILLEGAL_ZONE', '指定された都道府県が不正です.');
define('MODULE_SHIPPING_SAGAWAEX_TEXT_OUT_OF_AREA',  '配達区域外です.');

// 時間帯サービス
$GLOBALS['a_sagawaex_time']=array(
  array('id'=>'希望なし',  'text'=>'希望なし'),
  array('id'=>'午前中',    'text'=>'午前中'),
  array('id'=>'12時〜15時','text'=>'12時〜15時'),
  array('id'=>'15時〜18時','text'=>'15時〜18時'),
  array('id'=>'18時〜21時','text'=>'18時〜21時'),
);
?>