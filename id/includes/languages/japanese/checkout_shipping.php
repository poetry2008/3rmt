<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'レジ');
define('NAVBAR_TITLE_2', '取引日時の指定');

define('HEADING_TITLE', '取引日時の指定');

define('TABLE_HEADING_SHIPPING_ADDRESS', 'ご希望の取引日時を指定してください');
define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'お届け先のご住所をご確認ください。<br>（下のボタンをクリックして、お届け先を変更することもできます。）');
define('TITLE_SHIPPING_ADDRESS', 'お届け先:');

define('TABLE_HEADING_SHIPPING_METHOD', '配送方法');
define('TEXT_CHOOSE_SHIPPING_METHOD', '配送方法を選んでください。');
define('TITLE_PLEASE_SELECT', '選んでください');
define('TEXT_ENTER_SHIPPING_INFORMATION', '下記の配送方法で商品をお届けします。');

define('TABLE_HEADING_COMMENTS', 'ご注文についてのコメント');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'ご注文の手続きを進めてください。');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '「次へ進む」をクリックして取引日時の選択へ。');

# Add ds-style
define('TEXT_CARACTOR', 'お届け先キャラクター名:');
define('TEXT_TORIHIKIHOUHOU', 'オプション:');
define('TEXT_TORIHIKIKIBOUBI', '取引希望日:');
define('TEXT_TORIHIKIKIBOUJIKAN', '取引希望時間:');

define('TEXT_CHECK_EIJI', '(英字)');
define('TEXT_CHECK_24JI', '<b>(24時間表記)</b>');
define('TEXT_PRESE_SELECT', '選択してください');

define('TEXT_ERROR_BAHAMUTO', '<span class="errorText">【'.mb_substr(TEXT_CARACTOR,0,(mb_strlen(TEXT_CARACTOR)-1)).'】が入力されていません</span>');
define('TEXT_ERROR_BAHAMUTO_EIJI', '<span class="errorText">【'.mb_substr(TEXT_CARACTOR,0,(mb_strlen(TEXT_CARACTOR)-1)).'】で使用できる文字は半角英字のみです</span>');
define('TEXT_ERROR_TORIHIKIHOUHOU', '<span class="errorText">【'.mb_substr(TEXT_TORIHIKIHOUHOU,0,(mb_strlen(TEXT_TORIHIKIHOUHOU)-1)).'】を選択してください。</span>');
define('TEXT_ERROR_DATE', '<span class="errorText">【'.mb_substr(TEXT_TORIHIKIKIBOUBI,0,(mb_strlen(TEXT_TORIHIKIKIBOUBI)-1)).'】を選択してください。</span>');
define('TEXT_ERROR_JIKAN', '<span class="errorText">【'.mb_substr(TEXT_TORIHIKIKIBOUJIKAN,0,(mb_strlen(TEXT_TORIHIKIKIBOUJIKAN)-1)).'】を選択してください。</span>');
?>