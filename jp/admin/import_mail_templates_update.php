<?php
//追加邮件模板数据
set_time_limit(0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set("display_errors", "Off");
include("includes/configure.php");

$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '</head>';
echo '<body>';
echo 'start......<br><br>';

//追加订单状态邮件模板

mysql_query("update mail_templates set contents_description=concat(contents_description,'<br>手数料：$\{COMMISSION\}<br>注文商品：$\{ORDER_PRODUCTS\}<br>配送料：$\{SHIPPING_FEE\}<br>住所情報：$\{USER_ADDRESS\}<br>備考：$\{ORDER_COMMENT\}<br>お届け方法：$\{SHIPPING_METHOD\}<br>ポイント：$\{POINT\}<br>合計：$\{TOTAL\}<br>カスタム費用：$\{CUSTOMIZED_FEE\}') where flag like 'ORDERS_STATUS_MAIL_TEMPLATES_%'");

echo 'NO1 追加了订单状态邮件模板的内容说明<br>';

//追加预约状态邮件模板

mysql_query("update mail_templates set contents_description=concat(contents_description,'<br>カスタム費用：$\{CUSTOMIZED_FEE\}') where flag like 'PREORDERS_STATUS_MAIL_TEMPLATES_%'");

echo 'NO2 追加了预约状态邮件模板的内容说明<br>';

echo '<br><br>finish';
echo '</body>';
echo '</html>';
