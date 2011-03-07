<?php
/*
  $Id$
*/
 
define('HEADING_TITLE', 'カテゴリー / 商品');
define('HEADING_TITLE_SEARCH', '検索:');
define('HEADING_TITLE_GOTO', 'ジャンプ:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'カテゴリー / 商品');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_STATUS', 'ステータス');

define('TEXT_NEW_PRODUCT', '&quot;%s&quot; への商品登録');
define('TEXT_CATEGORIES', 'カテゴリー:');
define('TEXT_SUBCATEGORIES', 'サブカテゴリー数:');
define('TEXT_PRODUCTS', '商品数:');
define('TEXT_PRODUCTS_PRICE_INFO', '価格:');
define('TEXT_PRODUCTS_TAX_CLASS', '税種別:');
define('TEXT_PRODUCTS_AVERAGE_RATING', '平均点:');
define('TEXT_PRODUCTS_QUANTITY_INFO', '数量:');
define('TEXT_PRODUCTS_REAL_QUANTITY', '実在庫:');
define('TEXT_PRODUCTS_VIRTUAL_QUANTITY', '架空在庫:');
define('TEXT_DATE_ADDED', '登録日:');
define('TEXT_DATE_AVAILABLE', '発売日:');
define('TEXT_LAST_MODIFIED', '更新日:');
define('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS', '新しいカテゴリーまたは商品を追加してください<br>&nbsp;<br><b>%s</b>');
define('TEXT_PRODUCT_MORE_INFORMATION', 'もっと詳しい情報は、この商品の<a class="order_link" href="http://%s" target="blank">ウェブページ</u>をご覧ください</a>。');
define('TEXT_PRODUCT_DATE_ADDED', 'この商品は %s にカタログに登録されました。');
define('TEXT_PRODUCT_DATE_AVAILABLE', 'この商品は %s に入荷予定です。');

define('TEXT_EDIT_INTRO', '必要な変更を加えてください');
define('TEXT_EDIT_CATEGORIES_ID', 'カテゴリーID:');
define('TEXT_EDIT_CATEGORIES_NAME', 'カテゴリー名:');
define('TEXT_EDIT_CATEGORIES_IMAGE', 'カテゴリー画像:');
define('TEXT_EDIT_SORT_ORDER', '整列順:');

define('TEXT_INFO_COPY_TO_INTRO', 'この商品をコピーする先のカテゴリーを選んでください');
define('TEXT_INFO_CURRENT_CATEGORIES', '現在のカテゴリー:');

define('TEXT_INFO_HEADING_NEW_CATEGORY', '新しいカテゴリー');
define('TEXT_INFO_HEADING_EDIT_CATEGORY', 'カテゴリーを編集');
define('TEXT_INFO_HEADING_DELETE_CATEGORY', 'カテゴリーを削除');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'カテゴリーを移動');
define('TEXT_INFO_HEADING_DELETE_PRODUCT', '商品を削除');
define('TEXT_INFO_HEADING_MOVE_PRODUCT', '商品を移動');
define('TEXT_INFO_HEADING_COPY_TO', 'コピー先');

define('TEXT_DELETE_CATEGORY_INTRO', '本当にこのカテゴリーを削除しますか？');
define('TEXT_DELETE_PRODUCT_INTRO', '本当にこの商品を削除しますか？');

define('TEXT_DELETE_WARNING_CHILDS', '<b>警告:</b> このカテゴリーには %s 個のサブカテゴリーがリンクされています!');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>警告:</b> このカテゴリーには %s 個の商品がリンクされています!');

define('TEXT_MOVE_PRODUCTS_INTRO', '<b>%s</b> を移動する先のカテゴリーを選んでください');
define('TEXT_MOVE_CATEGORIES_INTRO', '<b>%s</b> を移動する先のカテゴリーを選んでください');
define('TEXT_MOVE', '<b>%s</b> の移動先:');

define('TEXT_NEW_CATEGORY_INTRO', '新しいカテゴリーの情報を入力してください');
define('TEXT_CATEGORIES_NAME', '新しいカテゴリー:');
define('TEXT_CATEGORIES_IMAGE', 'カテゴリー画像:');
define('TEXT_SORT_ORDER', '整列順:');

define('TEXT_PRODUCTS_STATUS', '商品ステータス:');
define('TEXT_PRODUCTS_CHARACTER', 'キャラクター名:');
define('TEXT_PRODUCTS_BUY_AND_SELL', '売買ステータス:');
define('TEXT_PRODUCTS_SMALL_SUM', '小口割:');
define('TEXT_PRODUCTS_DATE_AVAILABLE', '発売日:');
define('TEXT_PRODUCT_AVAILABLE', '在庫あり');
define('TEXT_PRODUCT_NOT_AVAILABLE', '品切れ');
define('TEXT_PRODUCT_INDISPENSABILITY', '入力必須');
define('TEXT_PRODUCT_NOT_INDISPENSABILITY', '入力不要');
define('TEXT_PRODUCT_USUALLY', '通常商品');
define('TEXT_PRODUCT_PURCHASE', '買い取り商品');
define('TEXT_PRODUCTS_MANUFACTURER', 'メーカー名:');
define('TEXT_PRODUCTS_NAME', '商品名:');
define('TEXT_PRODUCTS_DESCRIPTION', '商品の説明:');
define('TEXT_PRODUCTS_QUANTITY', '商品の数量:');
define('TEXT_PRODUCTS_MODEL', '商品の型番:');
define('TEXT_PRODUCTS_IMAGE', '商品の画像:');
define('TEXT_PRODUCTS_URL', '商品のURL:');
define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(http:// は不要)</small>');
define('TEXT_PRODUCTS_PRICE', '商品の価格:');
define('TEXT_PRODUCTS_WEIGHT', '商品の重量:');
define('TEXT_PRODUCTS_OPTION', 'オプション:');

define('EMPTY_CATEGORY', '空カテゴリー');

define('TEXT_HOW_TO_COPY', 'コピー方法:');
define('TEXT_COPY_AS_LINK', 'リンクコピー（他カテゴリーへ）');
define('TEXT_COPY_AS_DUPLICATE', '重複コピー（同一カテゴリー可）');

define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'エラー: 同じカテゴリー内にはリンクできません。');
define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'エラー: カテゴリー画像のディレクトリに書き込みできません: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'エラー: カテゴリー画像のディレクトリが存在しません: ' . DIR_FS_CATALOG_IMAGES);

define('TEXT_PRODUCTS_TAGS', '商品タグ:');
define('TEXT_KEYWORD','キーワード');
define('TEXT_GOOGLE_SEARCH','はGOOGLEで%sがキーワードとしての検索結果');
define('TEXT_RENAME','リネーム');
define('TEXT_INFO_KEYWORD','キーワードを変更する');
define('TEXT_NO_SET_KEYWORD','キーワードを設置しない');
define('TEXT_NO_DATA','該当の情報は見つかりませんでした');
define('TEXT_LAST_SEARCH_DATA','最後から&nbsp;%s&nbsp;つの検索結果');
define('TEXT_FIND_DATA_STOP','%sをさがしましたが、表示を停止します。');
define('TEXT_NOT_ENOUGH_DATA','前からの&nbsp;50&nbsp;件検索結果に不重複な結果は&nbsp;%s&nbsp;件があります');
define('TEXT_ROMAJI_EXISTS', ' ローマ字が存在しています');
define('TEXT_PRODUCTS_ROMAJI', 'ローマ字:');
define('TEXT_ROMAJI_ERROR', 'ローマ字の格式が正しくないです!');
define('TEXT_ROMAJI_NOT_NULL', 'ローマ字を書いてください!');
define('TEXT_MAX','在庫最大');
define('TEXT_MIN','在庫最小');
define('TEXT_INFO_INVENTORY','在庫設置');
define('TEXT_INVENTORY_ERROR','入力データーをご確認ください');
define('ERROR_PRODUCT_NAME_IS_NOT_NULL', '商品名を記入してください');
define('ERROR_CATEGORY_NAME_IS_NOT_NULL', 'カテゴリー名を記入してください');
define('TEXT_PRODUCT_NOT_SHOW','非表示');
