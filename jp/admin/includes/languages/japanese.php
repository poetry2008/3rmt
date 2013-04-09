<?php
/*
  $Id$
*/

//
// mb_internal_encoding() is set for PHP-4.3.x(Zend Multibyte)
//
// A compatible module is loaded for environment without mbstring-extension
//
if (extension_loaded('mbstring')) {
  mb_internal_encoding('UTF-8'); // 指定内部代码
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'ja_JP.UTF-8');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Y年%B%e日 %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'Y/m/d H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('DATE_TIME_FORMAT_TORIHIKI', '%Y/%m/%d %H:%M');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 8, 2) . substr($date, 5, 2) . substr($date, 0, 4);
  } else {
    return substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);
  }
}

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="ja"');

// charset for web pages and emails
define('CHARSET', 'UTF-8'); 

// page title
define('TITLE', STORE_NAME);  //请记述商店名。

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'トップ');
define('HEADER_TITLE_SUPPORT_SITE', 'サポートサイト');
define('HEADER_TITLE_ONLINE_CATALOG', 'オンラインカタログ');
define('HEADER_TITLE_ADMINISTRATION', '管理メニュー');

// text for gender
define('MALE', '男性');
define('FEMALE', '女性');

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', '基本設定');
define('BOX_CONFIGURATION_MYSTORE', 'ショップ');
define('BOX_CONFIGURATION_LOGGING', 'ログ');
define('BOX_CONFIGURATION_CACHE', 'キャッシュ');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', 'モジュール設定');
define('BOX_MODULES_PAYMENT', '支払モジュール');
define('BOX_MODULES_SHIPPING', '配送モジュール');
define('BOX_MODULES_ORDER_TOTAL', '合計モジュール');
define('BOX_MODULES_METASEO', 'Meta SEO');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', 'カタログ管理');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', 'カテゴリー/商品登録');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', '商品オプション登録');
define('BOX_CATALOG_MANUFACTURERS', 'メーカー登録');
define('BOX_CATALOG_REVIEWS', 'レビュー管理');
define('BOX_CATALOG_SPECIALS', '特価商品登録');
define('BOX_CATALOG_PRODUCTS_EXPECTED', '入荷予定商品管理');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', '顧客管理');
define('BOX_CUSTOMERS_CUSTOMERS', '顧客管理');
define('BOX_CUSTOMERS_ORDERS', '注文管理');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', '地域 / 税率設定');
define('BOX_TAXES_COUNTRIES', '国名設定');
define('BOX_TAXES_ZONES', '地域設定');
define('BOX_TAXES_GEO_ZONES', '地域税設定');
define('BOX_TAXES_TAX_CLASSES', '税種別設定');
define('BOX_TAXES_TAX_RATES', '税率設定');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', 'レポート');
define('BOX_REPORTS_PRODUCTS_VIEWED', '商品別の閲覧回数');
define('BOX_REPORTS_PRODUCTS_PURCHASED', '商品別の販売数');
define('BOX_REPORTS_ORDERS_TOTAL', '顧客別の売上順位');
define('BOX_REPORTS_SALES_REPORT2', '売上管理');
define('BOX_REPORTS_NEW_CUSTOMERS', '新規顧客');
define('BOX_REPORTS_ASSETS', '資産管理');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', '各種ツール');
define('BOX_TOOLS_BACKUP', 'DBバックアップ管理');
define('BOX_TOOLS_BANNER_MANAGER', 'バナー管理');
define('BOX_TOOLS_CACHE', 'キャッシュコントロール');
define('BOX_TOOLS_DEFINE_LANGUAGE', '言語ファイル管理');
define('BOX_TOOLS_FILE_MANAGER', 'ファイル管理');
define('BOX_TOOLS_MAIL', 'E-Mail 送信');
define('BOX_TOOLS_NEWSLETTER_MANAGER', 'メールマガジン管理');
define('BOX_TOOLS_SERVER_INFO', 'サーバー情報');
define('BOX_TOOLS_WHOS_ONLINE', 'オンラインユーザ');
define('BOX_TOOLS_PRESENT','プレゼント機能');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', 'ローカライズ');
define('BOX_LOCALIZATION_CURRENCIES', '通貨設定');
define('BOX_LOCALIZATION_LANGUAGES', '言語設定');
define('BOX_LOCALIZATION_ORDERS_STATUS', '注文ステータス設定');

// javascript messages
define('JS_ERROR', 'フォームの処理中にエラーが発生しました!\n下記の修正を行ってください:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* 新しい商品属性の価格を指定してください。\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* 新しい商品属性の価格の接頭辞を指定してください。\n');

define('JS_PRODUCTS_NAME', '* 新しい商品の名前を指定してください。\n');
define('JS_PRODUCTS_DESCRIPTION', '* 新しい商品の説明文を入力してください。\n');
define('JS_PRODUCTS_PRICE', '* 新しい商品の価格を指定してください。\n');
define('JS_PRODUCTS_WEIGHT', '* 新しい商品の重量を指定してください。\n');
define('JS_PRODUCTS_QUANTITY', '* 新しい商品の数量を指定してください。\n');
define('JS_PRODUCTS_MODEL', '* 新しい商品の型番を指定してください。\n');
define('JS_PRODUCTS_IMAGE', '* 新しい商品のイメージ画像を指定してください。\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* この商品の新しい価格を指定してください。\n');

define('JS_GENDER', '* \'性別\' が選択されていません。\n');
define('JS_FIRST_NAME', '* \'名前\' は少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_LAST_NAME', '* \'姓\' は少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');

define('JS_FIRST_NAME_F', '* \'名前(フリガナ)\' は少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_LAST_NAME_F', '* \'姓(フリガナ)\' は少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');

define('JS_DOB', '* \'生年月日\' は次の形式で入力ください: xxxx/xx/xx (年/月/日)。\n');
define('JS_EMAIL_ADDRESS', '* \'E-Mail アドレス\' は少なくても ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_EMAIL_ADDRESS_MATCH_ERROR','*  入力されたメールアドレスは不正です!');
define('JS_ADDRESS', '* \'住所１\' は少なくても ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_POST_CODE', '* \'郵便番号\' は少なくても ' . ENTRY_POSTCODE_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_CITY', '* \'市区町村\' は少なくても ' . ENTRY_CITY_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_STATE', '* \'都道府県\' が選択されていません。\n');
define('JS_STATE_SELECT', '-- 上から選択 --');
define('JS_ZONE', '* \'都道府県\' をリストから選択してください。');
define('JS_COUNTRY', '* \'国\' を選択してください。\n');
define('JS_TELEPHONE', '* \'電話番号\' は少なくても ' . ENTRY_TELEPHONE_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_PASSWORD', '* \'パスワード\' と \'パスワードを再入力\' は少なくとも ' . ENTRY_PASSWORD_MIN_LENGTH . ' 文字以上必要です。\n');

define('JS_ORDER_DOES_NOT_EXIST', '注文番号 %s が存在しません!');

define('CATEGORY_PERSONAL', '個人情報');
define('CATEGORY_ADDRESS', 'ご住所');
define('CATEGORY_CONTACT', 'ご連絡先');
define('CATEGORY_COMPANY', '会社名');
define('CATEGORY_PASSWORD', 'パスワード');
define('CATEGORY_OPTIONS', 'オプション');
define('ENTRY_GENDER', '性別:');
define('ENTRY_FIRST_NAME', '名:');
define('ENTRY_LAST_NAME', '姓:');
//add
define('TEXT_ADDRESS','住所');
define('TEXT_CLEAR','クリア');
define('TABLE_OPTION_NEW','登録先に届ける');
define('TABLE_OPTION_OLD','過去のお届け先を指定する'); 
define('TABLE_ADDRESS_SHOW','お届け先リストから選ぶ:');
define('ENTRY_FIRST_NAME_F', '名(フリガナ):');
define('ENTRY_LAST_NAME_F', '姓(フリガナ):');
define('ENTRY_DATE_OF_BIRTH', '生年月日:');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail アドレス:');
define('ENTRY_QUITED_DATE','退会日時:');
define('ENTRY_COMPANY', '会社名:');
define('ENTRY_STREET_ADDRESS', '住所1:');
define('ENTRY_SUBURB', '住所2:');
define('ENTRY_POST_CODE', '郵便番号:');
define('ENTRY_CITY', '市区町村:');
define('ENTRY_STATE', '都道府県:');
define('ENTRY_COUNTRY', '国名:');
define('ENTRY_TELEPHONE_NUMBER', '電話番号:');
define('ENTRY_FAX_NUMBER', 'ファックス番号:');
define('ENTRY_NEWSLETTER', 'メールマガジン:');
define('ENTRY_NEWSLETTER_YES', '購読する');
define('ENTRY_NEWSLETTER_NO', '購読しない');
define('ENTRY_PASSWORD', 'パスワード:');
define('ENTRY_PASSWORD_CONFIRMATION', 'パスワード再入力:');
define('PASSWORD_HIDDEN', '********');

// images
define('IMAGE_ANI_SEND_EMAIL', 'E-Mail送信');
define('IMAGE_BACK', '戻る');
define('IMAGE_NEXT', '次へ');
define('IMAGE_BACKUP', 'バックアップ');
define('IMAGE_CANCEL', '取り消し');
define('IMAGE_CONFIRM', '確認');
define('IMAGE_CONFIRM_NEXT', '次へ進む');
define('IMAGE_COPY', 'コピー');
define('IMAGE_COPY_TO', 'コピー先');
define('IMAGE_DEFINE', '定義');
define('IMAGE_DELETE', '削除');
define('IMAGE_EDIT', '編集');
define('IMAGE_EMAIL', 'E-Mail');
define('IMAGE_FILE_MANAGER', 'ファイル管理');
define('IMAGE_ICON_STATUS_GREEN', '有効');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', '有効にする');
define('IMAGE_ICON_STATUS_RED', '無効');
define('IMAGE_ICON_STATUS_RED_LIGHT', '無効にする');
define('IMAGE_ICON_INFO', '情報');
define('IMAGE_INSERT', '挿入');
define('IMAGE_LOCK', 'ロック');
define('IMAGE_MOVE', '移動');
define('IMAGE_NEW_PROJECT','新規作成');
define('IMAGE_NEW_CATEGORY', '新しいカテゴリー');
define('IMAGE_NEW_COUNTRY', '新しい国名');
define('IMAGE_NEW_CURRENCY', '新しい通貨');
define('IMAGE_NEW_FILE', '新しいファイル');
define('IMAGE_NEW_FOLDER', '新しいフォルダ');
define('IMAGE_NEW_LANGUAGE', '新しい言語');
define('IMAGE_NEW_PRODUCT', '新しい商品');
define('IMAGE_NEW_TAX_CLASS', '新しい税種別');
define('IMAGE_NEW_TAX_RATE', '新しい税率');
define('IMAGE_NEW_TAX_ZONE', '新しい税地域');
define('IMAGE_NEW_ZONE', '新しい地域');
define('IMAGE_NEW_TAG', '新标签'); 
define('IMAGE_ORDERS', '注文');
define('IMAGE_ORDERS_INVOICE', '納品書');
define('IMAGE_ORDERS_PACKINGSLIP', '配送票');
define('IMAGE_PREVIEW', 'プレビュー');
define('IMAGE_RESTORE', '復元');
define('IMAGE_RESET', 'リセット');
define('IMAGE_SAVE', '保存');
define('IMAGE_SEARCH', '検索');
define('IMAGE_SELECT', '選択');
define('IMAGE_SEND', '送信');
define('IMAGE_SEND_EMAIL', 'E-Mail送信');
define('IMAGE_UNLOCK', 'ロック解除');
define('IMAGE_UPDATE', '更新');
define('IMAGE_UPDATE_CURRENCIES', '為替レートの更新');
define('IMAGE_UPLOAD', 'アップロード');
define('IMAGE_EFFECT', '有効');
define('IMAGE_DEFFECT', '無効');

define('ICON_CROSS', '偽(False)');
define('ICON_CURRENT_FOLDER', '現在のフォルダ');
define('ICON_DELETE', '削除');
define('ICON_ERROR', 'エラー');
define('ICON_FILE', 'ファイル');
define('ICON_FILE_DOWNLOAD', 'ダウンロード');
define('ICON_FOLDER', 'フォルダ');
define('ICON_LOCKED', 'ロック');
define('ICON_PREVIOUS_LEVEL', '前のレベル');
define('ICON_PREVIEW', 'プレビュー');
define('ICON_STATISTICS', '統計');
define('ICON_SUCCESS', '成功');
define('ICON_TICK', '真(True)');
define('ICON_UNLOCKED', 'ロック解除');
define('ICON_WARNING', '警告');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', ' %s / %d ページ');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_ITEM', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の未使用項目のうち)');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_OPTION', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の未使用オプションのうち)');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のオプションのうち)');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_ITEM', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の項目のうち)');
define('TEXT_DISPLAY_NUMBER_OF_ADDRESS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の項目のうち)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_FEE', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の国名のうち)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_AREA', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の地域のうち)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_CITY', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の都道府県のうち)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_TIME', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の項目のうち)');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の予約状況のうち)');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の予約のうち)');
define('TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の最新情報のうち)');
define('TEXT_DISPLAY_NUMBER_OF_CAMPAIGN', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のキャンペーンコードのうち)');
define('TEXT_DISPLAY_NUMBER_OF_HELP_INFO', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のコンテンツのうち)');
define('TEXT_DISPLAY_NUMBER_OF_CATEGORIES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のバナーのうち)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の国のうち)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の顧客のうち)');
define('TEXT_DISPLAY_NUMBER_OF_FAQ', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のFAQのうち)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の通貨のうち)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の言語のうち)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のメーカーのうち)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のメールマガジンのうち)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の注文のうち)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGERS', '<b>%d</b> &sim; <b>%d</b> 番目を表示(<b>%d</b> のパスワードのうち)');
define('TEXT_DISPLAY_NUMBER_OF_MAIL', '<b>%d</b> &sim; <b>%d</b> 番目を表示(<b>%d</b> のページのうち)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGER_LOG', '<b>%d</b> &sim; <b>%d</b>番目を表示(<b>%d</b> のパスワード履歴のうち)');
define('TEXT_DISPLAY_NUMBER_OF_NIVENTORY', '<b>%d</b> &sim; <b>%d</b> 番目を表示 ( 総合： <b>%d</b> )');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の注文状況のうち)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の入荷予定商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の商品レビューのうち)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の特価商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の税種別のうち)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の税地域のうち)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の税率のうち)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> の地域のうち)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');
define('BUTTON_PREV', '前へ');
define('BUTTON_NEXT', '次へ');
//define('PREVNEXT_BUTTON_PREV', '前のページ');
//define('PREVNEXT_BUTTON_NEXT', '次のページ');

define('PREVNEXT_TITLE_FIRST_PAGE', '最初のページ');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', '前ページ');
define('PREVNEXT_TITLE_NEXT_PAGE', '次ページ');
define('PREVNEXT_TITLE_LAST_PAGE', '最後のページ');
define('PREVNEXT_TITLE_PAGE_NO', 'ページ %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', '前 %d ページ');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', '次 %d ページ');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;最初');
define('PREVNEXT_BUTTON_LAST', '最後&gt;&gt;');

define('TEXT_DEFAULT', 'デフォルト');
define('TEXT_SET_DEFAULT', 'デフォルトに設定');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* 必須</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'エラー: 基本となる通貨が設定されていません。 管理メニュー->ローカライズ->通貨設定: で設定を確認してください。');

define('TEXT_CACHE_CATEGORIES', 'カテゴリーボックス');
define('TEXT_CACHE_MANUFACTURERS', 'メーカーボックス');
define('TEXT_CACHE_ALSO_PURCHASED', '関連の商品モジュール');

define('TEXT_NONE', '--なし--');
define('TEXT_TOP', 'トップ');

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);

//Add languages
//------------------------
//contents
define('BOX_TOOLS_CONTENTS', 'コンテンツ管理');
define('TEXT_DISPLAY_NUMBER_OF_CONTENS', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> のコンテンツのうち)');

//latest news
define('BOX_TOOLS_LATEST_NEWS', '新着情報管理');

//faq
define('BOX_TOOLS_FAQ', 'FAQ Manager');

//leftbox
define('BOX_CATALOG_PRODUCTS_UP', '商品データアップロード');
define('BOX_CATALOG_PRODUCTS_DL', '商品データダウンロード');
define('BOX_TOOLS_CL', 'カレンダー');
define('BOX_CATALOG_PRODUCTS_TAGS', 'タグ登録');
define('BOX_CATALOG_IMAGE_DOCUMENT', 'imageファイル管理');


define('TABLE_HEADING_SITE', 'サイト');

define('IMAGE_BUTTON_BACK', '');
define('IMAGE_BUTTON_CONFIRM', '');
define('IMAGE_DETAILS', '詳細');

define('CATEGORY_SITE', '所属サイト');
define('ENTRY_SITE', 'サイト');
define('ENTRY_SITE_TEXT', '所属サイト');

define('TEXT_IMAGE_NONEXISTENT', '画像が存在しません');
define('SITE_ID_NOT_NULL', 'サイトを選んでください');
define('IMAGE_NEW_DOCUMENT_TYPE', '');
define('MSG_UPLOAD_IMG', '');
define('JS_ERROR_SUBMITTED', '');

define('BOX_CATALOG_COLORS', '商品カラー登録');
define('BOX_CATALOG_CATEGORIES_ADMIN', '商品卸価格管理');
//define('HEADING_TITLE', '商品卸価格管理');
define('HEADING_TITLE_SEARCH', '検索');
define('HEADING_TITLE_GOTO', 'ジャンプ');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'カテゴリー / 商品');
define('OROSHI_DATA_MANAGE','データ');
define('BOX_ONCE_PWD_LOG','ログ');
define('BANK_CL_TITLE_TEXT', 'カレンダー設定');
define('BANK_CL_COMMON_WORK_TIME', '通常営業');
define('BANK_CL_REST_TIME', '銀行休業');
define('BANK_CL_SEND_MAIL', 'メール返信休業');
define('HISTORY_TITLE_ONE', '同業者の履歴登録');
define('HISTORY_TITLE_TWO', '卸業者の履歴表示');
define('HISTORY_TITLE_THREE', '卸業者の履歴登録');
define('KEYWORDS_TITLE_TEXT', 'キーワードランキング');
define('KEYWORDS_SEARCH_START_TEXT', '開始日:');
define('KEYWORDS_SEARCH_END_TEXT', '終了日:');
define('KEYWORDS_TABLE_COLUMN_ONE_TEXT', 'キーワード');
define('KEYWORDS_TABLE_COLUMN_TWO_TEXT', '件数');
define('KEYWORDS_TABLE_COLUMN_THREE_TEXT', '順位');
define('LIST_DISPLAY_PRODUCT_SELECT', '商品選択');
define('LIST_DISPLAY_JIAKONGZAIKU', '架空在庫');
define('LIST_DISPLAY_YEZHE_PRICE', '業者単価');
define('MAG_DL_TITLE_TEXT', 'メールマガジン購読者データダウンロード');
define('MAG_UP_TITLE_TEXT', 'メールマガジン購読者一括アップロード');
define('PRODUCTS_TO_TAGS_TITLE', 'タグ関連設定');
define('REFERER_TITLE_TEXT', 'アクセスランキング');
define('REFERER_TITLE_URL', 'アクセス来たソース');
define('REFERER_TITLE_NUM', '件数');
define('REFERER_TITLE_SORT_NUM', '順位');
define('TELECOM_UNKNOW_TITLE', '決算管理');
define('TELECOM_UNKNOW_SEARCH_SUCCESS', '成功');
define('TELECOM_UNKNOW_SEARCH_FAIL', '失敗');
define('TELECOM_UNKNOW_TABLE_CAL_METHOD', '決算方法');
define('TELECOM_UNKNOW_TABLE_TIME', '時間');
define('TELECOM_UNKNOW_TABLE_CAL', '決算');
define('TELECOM_UNKNOW_TABLE_YIN', '引当');
define('TELECOM_UNKNOW_TABLE_SURNAME', '氏名');
define('TELECOM_UNKNOW_TABLE_TEL', '電話');
define('TELECOM_UNKNOW_TABLE_EMAIL', 'メールアドレス');
define('TELECOM_UNKNOW_TABLE_PRICE', '金額');
define('TELECOM_UNKNOW_SELECT_NOTICE', '選択した行を非表示にしますか？');
define('TELECOM_UNKNOW_TABLE_DISPLAY','一括非表示');
define('CLEATE_DOUGYOUSYA_TITLE', '同業者の名前設定');
define('CLEATE_DOUGYOUSYA_ADD_BUTTON', '入力フォーム追加');
define('CLEATE_DOUGYOUSYA_TONGYE', '同業者：');
define('CLEATE_DOUGYOUSYA_EDIT', '編集');
define('CLEATE_DOUGYOUSYA_DEL', '削除');
define('CLEATE_DOUGYOUSYA_HISTORY', '履歴');
define('CLEATE_DOUGYOUSYA_LOGIN', '同業者登録');
define('CLEATE_DOUGYOUSYA_UPDATE_SORT', '順序を更新する');
define('CLEATE_LIST_TITLE', '卸業者のデータ登録');
define('CLEATE_LIST_SETNAME_BUTTON', '卸業者の名前設定');
define('CLEATE_LIST_LOGIN_BUTTON', '卸業者登録');
define('CUSTOMERS_CSVEXE_TITLE', '顧客データダウンロード');
define('CUSTOMERS_CSVEXE_READ_TEXT', 'ダウンロード中はサーバに対して高負荷となります。アクセスの少ない時間に実行してください。');
define('YEAR_TEXT', '年');
define('MONTH_TEXT', '月');
define('DAY_TEXT', '日');
define('CUSTOMERS_CSV_EXE_NOTICE_TITLE', '顧客情報のうち以下の情報がCSVファイルとしてダウンロードされます。');
define('CUSTOMERS_CSV_EXE_READ_ONE', '<tr> <td width="20" align="center" class="infoBoxContent">&nbsp;</td> <td width="120" class="menuBoxHeading">項目</td> <td class="menuBoxHeading">説明</td> </tr> <tr> <td align="center" class="infoBoxContent">A</td> <td class="menuBoxHeading">アカウント作成日</td> <td class="menuBoxHeading">アカウントを作成した日時を出力します（形式：2005/11/11 10:15:32）</td> </tr> <tr> <td align="center" class="infoBoxContent">B</td> <td class="menuBoxHeading">性別</td> <td class="menuBoxHeading">顧客の性別を「男性」/「女性」と出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">C</td> <td class="menuBoxHeading">姓</td> <td class="menuBoxHeading">顧客の苗字を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">D</td> <td class="menuBoxHeading">名</td> <td class="menuBoxHeading">顧客の名前を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">E</td> <td class="menuBoxHeading">生年月日</td> <td class="menuBoxHeading">顧客の生年月日を出力します（形式：1999/11/11）</td> </tr> <tr> <td align="center" class="infoBoxContent">F</td> <td class="menuBoxHeading">メールアドレス</td> <td class="menuBoxHeading">メールアドレスを出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">G</td> <td class="menuBoxHeading">会社名</td> <td class="menuBoxHeading">会社名が入力されていれば出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">H</td> <td class="menuBoxHeading">郵便番号</td> <td class="menuBoxHeading">郵便番号を出力します。</td> </tr> <tr> <td align="center" class="infoBoxContent">I</td> <td class="menuBoxHeading">都道府県</td> <td class="menuBoxHeading">都道府県名（例：東京都）を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">J</td> <td class="menuBoxHeading">市区町村</td> <td class="menuBoxHeading">市区町村名（例：港区）を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">K</td> <td class="menuBoxHeading">住所1</td> <td class="menuBoxHeading">自宅（会社）住所を出力します（例： 芝公園〇〇 ）</td> </tr> <tr> <td align="center" class="infoBoxContent">L</td> <td class="menuBoxHeading">住所2</td> <td class="menuBoxHeading">ビル・マンション名が入力されていれば出力します（例：〇〇ビル5F）</td> </tr> <tr> <td align="center" class="infoBoxContent">M</td> <td class="menuBoxHeading">国名</td> <td class="menuBoxHeading">国名（Japan等）を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">N</td> <td class="menuBoxHeading">電話番号</td> <td class="menuBoxHeading">電話番号を出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">O</td>'); 

define('CUSTOMERS_CSV_EXE_READ_TWO', '<td class="menuBoxHeading">FAX番号</td> <td class="menuBoxHeading">FAX番号が入力されていれば出力します</td> </tr> <tr> <td align="center" class="infoBoxContent">P</td> <td class="menuBoxHeading">メールマガジン</td> <td class="menuBoxHeading">メールマガジンの行動区状況を出力します。<br> 購読の場合：「購読」｜未購読の場合：「未購読」</td> </tr> <tr> <td align="center" class="infoBoxContent">Q</td> <td class="menuBoxHeading">ポイント</td> <td class="menuBoxHeading">顧客の現在持っているポイント数を出力します。</td> </tr>');
define('BOX_TOOLS_POINT_EMAIL_MANAGER','ポイントお知らせ');
define('BOX_CAL_SITES_INFO_TEXT', '統計');

//catalog language
define('FILENAME_CLEATE_OROSHI_TEXT','卸業者の名前設定');
define('FILENAME_CLEATE_DOUGYOUSYA_TEXT','同業者の名前設定');
define('FILENAME_CATEGORIES_ADMIN_TEXT','商品卸価格管理');

//coustomers language
define('FILENAME_TELECOM_UNKNOW_TEXT','決算管理');
define('FILENAME_BILL_TEMPLATES_TEXT','請求書のテンプレート');

//reports language
define('FILENAME_REFERER_TEXT','アクセスランキング');
define('FILENAME_KEYWORDS_TEXT','キーワードランキング');

//tools language 
define('FILENAME_BANK_CL_TEXT','カレンダー設定');
define('FILENAME_PW_MANAGER_TEXT','ID管理');
define('FILENAME_COMPUTERS_TEXT','ボタン管理');
define('FILENAME_MAG_UP_TEXT','メールマガジン一括登録');
define('FILENAME_MAG_DL_TEXT','メールマガジンデータDL');

//header language
define('HEADER_TEXT_SITE_NAME',COMPANY_NAME);
define('HEADER_TEXT_LOGINED','でログインしています。');
define('HEADER_TEXT_ORDERS','注文一覧');
define('HEADER_TEXT_TELECOM_UNKNOW','決算履歴');
define('HEADER_TEXT_TUTORIALS','商品調整▼');
define('HEADER_TEXT_CATEGORIES','商品登録');
define('HEADER_TEXT_LOGOUT','ログアウト');
define('HEADER_TEXT_REDIRECTURL','サイトへ移動▼');
define('HEADER_TEXT_USERS','パスワード変更');
define('HEADER_TEXT_PW_MANAGER','ID管理');
define('HEADER_TEXT_MANAGERMENU','ツール▼');
define('HEADER_TEXT_MICRO_LOG','引継メモ');
define('HEADER_TEXT_LATEST_NEWS','新着情報');
define('HEADER_TEXT_CUSTOMERS','顧客一覧');
define('HEADER_TEXT_CREATE_ORDER2','仕入作成');
define('HEADER_TEXT_CREATE_ORDER','注文作成');
define('HEADER_TEXT_ORDERMENU','注文書▼');
define('HEADER_TEXT_INVENTORY','在庫水準');
define('HEADER_TEXT_CATEGORIES_ADMIN','価格調整');
//footer start 
define('TEXT_FOOTER_ONE_TIME','チェックされている権限のワンタイムを使えばアクセスできる');
define('TEXT_FOOTER_CHECK_SAVE','保存');
//footer end
define('RIGHT_ORDER_INFO_ORDER_FROM', '注文書サイト：');
define('RIGHT_ORDER_INFO_ORDER_FETCH_TIME', 'お届け日時：');
define('RIGHT_ORDER_INFO_ORDER_OPTION', 'オプション：');
define('RIGHT_ORDER_INFO_ORDER_ID', 'ご注文番号：');
define('RIGHT_ORDER_INFO_ORDER_DATE', '注文日：');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE', '顧客種別：');
define('RIGHT_CUSTOMER_INFO_ORDER_IP', 'IPアドレス：');
define('RIGHT_CUSTOMER_INFO_ORDER_HOST', 'ホスト名：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT', 'ユーザーエージェント：');
define('RIGHT_CUSTOMER_INFO_ORDER_OS', 'OS：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE', 'ブラウザの種類：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN', 'ブラウザの言語：');
define('RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN', 'パソコンの言語環境：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_LAN', 'ユーザーの言語環境：');
define('RIGHT_CUSTOMER_INFO_ORDER_PIXEL', '画面の解像度：');
define('RIGHT_CUSTOMER_INFO_ORDER_COLOR', '画面の色：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH', 'Flash：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION', 'Flashのバージョン：');
define('RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR', 'Director：');
define('RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME', 'Quick time：');
define('RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER', 'Real player：');
define('RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA', 'Windows media：');
define('RIGHT_CUSTOMER_INFO_ORDER_PDF', 'Pdf：');
define('RIGHT_CUSTOMER_INFO_ORDER_JAVA', 'Java：');
define('RIGHT_TICKIT_ID_TITLE', '問合番号を新規作成します');
define('RIGHT_TICKIT_EMAIL', 'メール');
define('RIGHT_TICKIT_CARD', 'クレカ');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME', '顧客名：');
define('RIGHT_ORDER_INFO_ORDER_EMAIL', 'E-Mail アドレス：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE', 'クレジットカード種別：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER', 'クレジットカード所有者：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID', 'クレジットカード番号：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME', 'クレジットカード有効期限：');
define('RIGHT_ORDER_INFO_TRANS_NOTICE', '取り扱い注意');
define('RIGHT_ORDER_INFO_TRANS_WAIT', '取引待ち');
define('RIGHT_ORDER_INFO_INPUT_FINISH', '入力済み');
define('RIGHT_ORDER_INFO_REPUTAION_SEARCH', '信用調査：');
//user pama
define('TEXT_ECECUTE_PASSWORD_USER','パスワード変更');
define('RIGHT_ORDER_COMMENT_TITLE', 'コメント：');
define('BOX_LOCALIZATION_PREORDERS_STATUS', '予約ステータス設定');
define('HEADER_TEXT_PREORDERS', '予約一覧');


//order div 

define('TEXT_FUNCTION_ORDER_ORDER_DATE','取引日：');
define('TEXT_FUNCTION_HEADING_CUSTOMERS', '顧客名：');
define('TEXT_FUNCTION_HEADING_ORDER_TOTAL', '注文総額：');
define('TEXT_FUNCTION_HEADING_DATE_PURCHASED', '注文日：');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER','会員');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER','ゲスト');


define('TEXT_MONEY_SYMBOL','円');

define('FILENAME_ORDER_DOWNLOAD','注文データエクスポート');
define('FRONT_CONFIGURATION_TITLE_TEXT', 'フロントエンド：');
define('ADMIN_CONFIGURATION_TITLE_TEXT', 'バックエンド：');
define('FRONT_OR_ADMIN_CONFIGURATION_TITLE_TEXT', 'フロントエンド・バックエンド：');
define('HEADER_TEXT_ORDER_INFO', '注文情報▼');


//note

define('TEXT_ADD_NOTE','メモ追加');
define('TEXT_COMMENT_NOTE','内容');
define('TEXT_COLOR','メモの色');
define('TEXT_TITLE_NOTE','タイトル');
define('TEXT_ATTRIBUTE','プロパティ');
define('TEXT_ATTRIBUTE_PUBLIC','パブリック');
define('TEXT_ATTRIBUTE_PRIVATE','プライベート');
define('HEADER_TEXT_CREATE_PREORDER', '予約作成');

define('TEXT_TORIHIKI_REPLACE_STR','～');
define('TEXT_TORIHIKI_HOUR_STR','時');
define('TEXT_TORIHIKI_MIN_STR','分');
define('TEXT_PREORDER_PAYMENT_METHOD', '支払方法：');
define('TEXT_PREORDER_NOT_COST', '入金まだ');
define('TEXT_PREORDER_COST_DATE', '入金日：');
define('TEXT_PREORDER_PRODUCTS_NAME', '商品：');
define('TEXT_PREORDER_PRODUCTS_NOENTRANCE', '未'); 
define('TEXT_PREORDER_PRODUCTS_ENTRANCE', '入');
define('TEXT_PREORDER_PRODUCTS_NUM', '個数：');
define('TEXT_PREORDER_PRODUCTS_UNIT', '個');




define('TEXT_PAYMENT_NULL_TXT','支払方法を選択してください');
define('TEXT_TORIHIKI_LIST_DEFAULT_TXT','選択してください');
define('BOX_TOOLS_CAMPAIGN', 'キャンペーンコード設置');
define('TEXT_CURRENT_CHARACTER_NAME', 'メールに記載する注意書き：');
define('BOX_CATALOG_SHOW_USELESS_OPTION','未使用オプション削除');
define('TEXT_ORDER_ALARM_LINK', 'アラーム');
define('HOUR_TEXT', '時');
define('MINUTE_TEXT', '分');
define('NOTICE_EXTEND_TITLE', '引継メモ');
define('NOTICE_ALARM_TITLE', 'アラーム');
define('NOTICE_DIFF_TIME_TEXT', '残り');
define('TEXT_DISPLAY_NUMBER_OF_MANUAL', '<b>%d</b> &sim; <b>%d</b> 番目を表示 (<b>%d</b> 件のうち)');
define('FILENAME_FILENAME_RESET_PWD_TEXT','一括パスワードリセット');
define('FILENAME_CUSTOMERS_EXIT_TEXT','退会顧客管理');
define('NEXT_ORDER_TEXT', '次の注文');
define('BEFORE_ORDER_TEXT', '過去の注文');
define('CUSTOMER_INFO_TEXT', '顧客情報');
define('BOX_CREATE_ADDRESS', '住所作成');
define('BOX_COUNTRY_FEE', '料金設定');
define('BOX_SHIPPING_TIME', '商品届け時間');
define('TEXT_REQUIRED', '必須');
define('TEXT_TIME_LINK', 'から');
define('TEXT_DATE_MONDAY', '月曜日');
define('TEXT_DATE_TUESDAY', '火曜日');
define('TEXT_DATE_WEDNESDAY', '水曜日');
define('TEXT_DATE_THURSDAY', '木曜日');
define('TEXT_DATE_FRIDAY', '金曜日');
define('TEXT_DATE_STATURDAY', '土曜日');
define('TEXT_DATE_SUNDAY', '日曜日');
define('ERROR_INPUT_RIGHT_DATE', '正しく日付を入力してください。');
define('TEXT_BUTTON_ADD','入力フォームを追加');
define('TEXT_ONE_TIME_CONFIG_SAVE','保存しました');
define('TEXT_ONE_TIME_ERROR','エラー');
define('TEXT_ONE_TIME_CONFIRM','チェックがありません。チェックを入れてください');
define('TEXT_ONE_TIME_ADMIN_CONFIRM','Adminのチェックを入れてください');
define('TEXT_SITE_COPYRIGHT' ,'Copyright © %s Haomai');

define('SECOND_TEXT','秒');
define('PAYMENT_METHOD','支払方法：');
define('DEPOSIT_STILL','入金まだ');
define('PAYMENT_DAY','入金日：');
define('PRODUCT','商品：');
define('INPUT','「入」');
define('NOT','「未」');
define('MANUAL','マニュアル');
define('NUMBERS','個数：');
define('MONTHS','個');
define('OPTION','オプション:');

define('DB_CONFIGURATION_TITLE_SHOP','ショップ情報');
define('DB_CONFIGURATION_TITLE_MIN','最小値');
define('DB_CONFIGURATION_TITLE_MAX','最大値');
define('DB_CONFIGURATION_TITLE_IMAGE_DISPLAY','イメージ表示');
define('DB_CONFIGURATION_TITLE_DISPLAY_ACCOUNT','アカウント表示');
define('DB_CONFIGURATION_TITLE_MODULE_OPTIONS','モジュール・オプション');
define('DB_CONFIGURATION_TITLE_CKING_DELIVERY','配送/パッキング');
define('DB_CONFIGURATION_TITLE_PRODUCT_LIST','商品一覧表示');
define('DB_CONFIGURATION_TITLE_INVENTORY_MANAGEMENT','在庫管理');
define('DB_CONFIGURATION_TITLE_RECORDING_LOG','ログ表示/記録');
define('DB_CONFIGURATION_TITLE_PAGE_CACHE','ページキャッシュ');
define('DB_CONFIGURATION_TITLE_EMAIL','E-Mail 送信');
define('DB_CONFIGURATION_TITLE_DOWNLOAD_SALES','ダウンロード販売');
define('DB_CONFIGURATION_TITLE_GZIP','GZip 圧縮');
define('DB_CONFIGURATION_TITLE_SESSION','セッション');
define('DB_CONFIGURATION_TITLE_PROGRAM','アフィリエイトプログラム');
define('DB_CONFIGURATION_TITLE_INITIAL_SETTING_SHOP','ショップ初期設定');
define('DB_CONFIGURATION_TITLE_BUSINESS_CALENDAR','営業日カレンダー');
define('DB_CONFIGURATION_TITLE_SEO','SEO URLs');
define('DB_CONFIGURATION_TITLE_DOCUMENTS','文件管理器');
define('DB_CONFIGURATION_TITLE_TIME_SETING','時間設定');
define('DB_CONFIGURATION_TITLE_MAXIMUM_VALUE','最大値');
define('DB_CONFIGURATION_TITLE_NEW_REVIEW','レビュー新着設定');
define('DB_CONFIGURATION_TITLE_INSTALL_SAFETY_REVIEW','レビュー制限');
define('DB_CONFIGURATION_TITLE_WARNING_SETTINGS','警告文字列設定');
define('DB_CONFIGURATION_TITLE_SIMPLE_INFORMATION','簡易注文情報 ');
define('DB_CONFIGURATION_TITLE_GRAPH_SET','混雑グラフ設定');


define('DB_CONFIGURATION_DESCRIPTION_SHOP','ショップの一般情報');
define('DB_CONFIGURATION_DESCRIPTION_MAX','関数/データの最小値');
define('DB_CONFIGURATION_DESCRIPTION_MIN','関数/データの最大値');
define('DB_CONFIGURATION_DESCRIPTION_IMAGE_DISPLAY','イメージ・パラメータ');
define('DB_CONFIGURATION_DESCRIPTION_DISPLAY_ACCOUNT','顧客のアカウントの設定');
define('DB_CONFIGURATION_DESCRIPTION_MODULE_OPTIONS','設定メニューには非表示');
define('DB_CONFIGURATION_DESCRIPTION_CKING_DELIVERY','ショップで受け付けられる配送オプション');
define('DB_CONFIGURATION_DESCRIPTION_PRODUCT_LIST','商品一覧の設定');
define('DB_CONFIGURATION_DESCRIPTION_INVENTORY_MANAGEMENT','在庫の設定');
define('DB_CONFIGURATION_DESCRIPTION_RECORDING_LOG','ログの設定');
define('DB_CONFIGURATION_DESCRIPTION_PAGE_CACHE','キャッシュの設定');
define('DB_CONFIGURATION_DESCRIPTION_EMAIL','E-Mail送信とHTMLメールの一般設定');
define('DB_CONFIGURATION_DESCRIPTION_DOWNLOAD_SALES','ダウンロード販売商品のオプション');
define('DB_CONFIGURATION_DESCRIPTION_GZIP','GZip 圧縮のオプション');
define('DB_CONFIGURATION_DESCRIPTION_SESSION','セッション制御に関するオプション');
define('DB_CONFIGURATION_DESCRIPTION_PROGRAM','アフィリエイトプログラムに対するオプション');
define('DB_CONFIGURATION_DESCRIPTION_INITIAL_SETTING_SHOP','ホームページの初期設定を行います。');
define('DB_CONFIGURATION_DESCRIPTION_BUSINESS_CALENDAR','営業日カレンダーの設定を行います');
define('DB_CONFIGURATION_DESCRIPTION_SEO','Options for Ultimate SEO URLs by Chemo');
define('DB_CONFIGURATION_DESCRIPTION_DOCUMENTS','Documents display options');
define('DB_CONFIGURATION_DESCRIPTION_TIME_SETING','時間単位を指定します。単位はすべて「秒」です。');
define('DB_CONFIGURATION_DESCRIPTION_MAXIMUM_VALUE','最大値（上限値）の設定を行います');
define('DB_CONFIGURATION_DESCRIPTION_DEAL','取引方法のリストを作成します');
define('DB_CONFIGURATION_DESCRIPTION_NEW_REVIEW','NEWの表示期限（日）');
define('DB_CONFIGURATION_DESCRIPTION_INSTALL_SAFETY_REVIEW','レビューの安全を設置する');
define('DB_CONFIGURATION_DESCRIPTION_WARNING_SETTINGS','警告文字列設定');
define('DB_CONFIGURATION_DESCRIPTION_SIMPLE_INFORMATION','簡易注文情報');
define('DB_CONFIGURATION_DESCRIPTION_GRAPH_SET','混雑グラフ設定');
define('DB_CONFIGURATION_DESCRIPTION_INITIAL_SETTING_SHOPS','混雑グラフ設定');

define('TEXT_KEYWORD','キーワード');
define('TEXT_GOOGLE_SEARCH','はGOOGLEで%sがキーワードとしての検索結果');
define('TEXT_RENAME','リネーム');
define('TEXT_INFO_KEYWORD','キーワードを変更する');
define('TEXT_NO_SET_KEYWORD','キーワードを設置しない');
define('TEXT_NO_DATA','該当の情報は見つかりませんでした');
define('TEXT_LAST_SEARCH_DATA','最後から&nbsp;%s&nbsp;つの検索結果');
define('TEXT_FIND_DATA_STOP','%sをさがしましたが、表示を停止します。');
define('TEXT_NOT_ENOUGH_DATA','前からの&nbsp;50&nbsp;件検索結果に不重複な結果は&nbsp;%s&nbsp;件があります');
define('CLEATE_DOUGYOUSYA_ALERT', 'まず、入力フォーム追加してください');
define('BUTTON_MANUAL','マニュアル');
define('TEXT_JAVASCRIPT_ERROR','JavaScriptまたはCookieの設定がオンになっていません。お手数ですが設定を「オン」にして、ご利用ください。<br>※ 設定がオフになっていますと、ご利用いただけないサービスがあります。');
define('HEADER_TEXT_PERSONAL_SETTING','個人設定');
define('TEXT_FLAG_CHECKED','確認済');
define('TEXT_FLAG_UNCHECK','未確認');

define('BOX_HEADING_USER', 'ユーザ');
define('BOX_USER_ADMIN', 'ユーザ管理');
define('BOX_USER_LOG', 'アクセスログ');
define('BOX_USER_LOGOUT', 'ログアウト');
define('JUMP_PAGE_TEXT', 'ページへ');
define('JUMP_PAGE_BUTTON_TEXT', '移動');
// javascript language
define('JS_TEXT_ONETIME_PWD_ERROR','パスワードが違います');
define('JS_TEXT_INPUT_ONETIME_PWD','ワンタイムパスワードを入力してください\r\n');
define('JS_TEXT_POSTAL_NUMBER_ERROR','郵便番号に誤りがあります。');
// cleate_list
define('TEXT_CLEATE_LIST','リスト登録');
define('TEXT_CLEATE_HISTORY','履歴を見る');
// products_tags
define('TEXT_P_TAGS_NO_TAG','タグのデータはありません、追加してください。');
define('UPDATE_MSG_TEXT', '更新しました。');
define('CL_TEXT_DATE_MONDAY', '月');
define('CL_TEXT_DATE_TUESDAY', '火');
define('CL_TEXT_DATE_WEDNESDAY', '水');
define('CL_TEXT_DATE_THURSDAY', '木');
define('CL_TEXT_DATE_FRIDAY', '金');
define('CL_TEXT_DATE_STATURDAY', '土');
define('CL_TEXT_DATE_SUNDAY', '日');
define('BUTTON_ADD_TEXT', '追加');
define('CSV_HEADER_TEXT', 'アカウント作成日,性別,姓,名,生年月日,メールアドレス,会社名,郵便番号,都道府県,市区町村,住所1,住所2,国名,電話番号,FAX番号,メルマガ購読,ポイント');
define('CSV_EXPORT_TEXT', 'CSVエクスポート');
define('TEXT_ALL','すべて');
define('MESSAGE_FINISH_ORDER_TEXT', '注文ID%sの成功：取り引きが完了致しました');
define('TEXT_USER_ADDED','作成者:');
define('TEXT_USER_UPDATE','更新者:');
define('TEXT_DATE_ADDED','作成日:');
define('TEXT_DATE_UPDATE','更新日:');
define('TEXT_EOF_ERROR_MSG','データ送信が失敗しました。再度送信してください。');
define('TEXT_UNSET_DATA','データなし');
define('IMAGE_PREV', '前へ');
define('TEXT_POPUP_WINDOW_SHOW','旧');
define('TEXT_POPUP_WINDOW_EDIT','新');
define('SIGNAL_GREEN', '緑');
define('SIGNAL_YELLOW', '黄');
define('SIGNAL_RED', '赤');
define('NOW_TIME_TEXT', '現時点より');
define('NOW_TIME_LINK_TEXT', '時間前');
define('SIGNAL_BLNK', '点滅');
define('SIGNAL_BLINK_READ_TEXT', '赤の時刻を越えたら');
define('NOTICE_SET_WRONG_TIME', '商品更新時刻の合図の設置が間違いました。');
define('TEXT_STATUS_MAIL_TITLE_CHANGED','ステータスと送信されるメールは一致しません。送信をしますか？');
define('BOX_TOOLS_MARKS_MANAGER', 'マーク管理');
define('TEXT_OPERATE_USER', '操作者');
define('TEXT_TIMEOUT_RELOGIN','無操作が一定時間を超えた為、自動的にログアウトしました。再度ログインしてください。');
define('TEXT_PREORDER_ENSURE_DATE', '確保期限：');
define('TEXT_PREORDER_ID_NUM', '予約番号：');
define('TEXT_PREORDER_DATE_TEXT', '予約日：');
define('NOTICE_LESS_PRODUCT_OPTION_TEXT', '商品の登録内容が更新されています。この商品を変更したい場合は、削除してから再度追加してください。');
define('NOTICE_LESS_PRODUCT_PRE_OPTION_TEXT', '商品の登録内容が更新されています。この商品を変更したい場合は、最初から予約注文を作成しなおす必要があります。');
define('NOTICE_COMPLETE_ERROR_TEXT','のデータに不備があります。ページを更新し、最初からやりなおしてください。');
define('NOTICE_STOCK_ERROR_TEXT','現状がデータベースと一致しません。表示されている情報が古いので、ページを更新してから再度入力してください。');
define('BUTTON_MAG_UP','アップロード');
define('BUTTON_MAG_DL','ダウンロード');
define('REVIEWS_CHARACTER_TOTAL','レビュー文字数 ');
define('TEXT_PRODUCTS_TAGS_ALL_CHECK','すべて選択');
define('TEXT_PRODUCTS_TAGS_CHECK','少なくとも1つの商品を選択してください。');
define('TEXT_CHECK_FILE_EXISTS','このファイルは既に存在します。上書きしますか？');
define('TEXT_CHECK_FILE_EXISTS_DELETE','このファイルは他の設定で使用しています。それでも削除しますか？');
define('TEXT_TRANSACTION_EXPIRED', 'お届け忘れ警告');
