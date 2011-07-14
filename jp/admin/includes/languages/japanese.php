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
  mb_internal_encoding('UTF-8'); // 内部コードを指定
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
define('CHARSET', 'UTF-8');    // Shift_JIS / euc-jp / iso-2022-jp

// page title
define('TITLE', STORE_NAME);  //ショップ名を記述してください。

// header text in includes/header.php
define('HEADER_TITLE_TOP', '管理メニュー');
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
define('ENTRY_FIRST_NAME_F', '名(フリガナ):');
define('ENTRY_LAST_NAME_F', '姓(フリガナ):');
define('ENTRY_DATE_OF_BIRTH', '生年月日:');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail アドレス:');
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
define('IMAGE_NEW_BANNER', '新しいバナー');
define('IMAGE_NEW_CATEGORY', '新しいカテゴリー');
define('IMAGE_NEW_COUNTRY', '新しい国名');
define('IMAGE_NEW_CURRENCY', '新しい通貨');
define('IMAGE_NEW_FILE', '新しいファイル');
define('IMAGE_NEW_FOLDER', '新しいフォルダ');
define('IMAGE_NEW_LANGUAGE', '新しい言語');
define('IMAGE_NEW_NEWSLETTER', '新しいメールマガジン');
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

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);  //Add Japanese osCommerce

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
define('BANK_CL_TITLE_TEXT', 'カレンダー編集');
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
define('SET_BAIRITU_TITLE', '無題ドキュメント');
define('SET_BAIRITU_CURSET', '倍率設定：');
define('SET_BAIRITU_SINGLE_PRICE', '単価の差額');
define('SET_BAIRITU_PERCENT', 'パーセント：');
define('SET_BAIRITU_SPRICE', '特別価格設定の計算');
define('SET_BAIRITU_CAL', '計算：');
define('SET_BAIRITU_CAL_SET', '計算設定');
define('SET_BAIRITU_BESTSELLER', '人気商品アイコンの表示');
define('SET_BAIRITU_BESTSELLER_READ', '日以内に注文があれば人気とする<br>0を入力するとアイコンは表示されません。');
define('SET_BAIRITU_UPDATE_NOTICE', '更新されました。');
define('SET_COMMENT_TITLE', '担当者登録');
define('SET_COMMENT_USER', '担当者:');
define('SET_COMMENT_SINGLE', '単価ルール:');
define('SET_COMMENT_COMMENT_TEXT', 'コメント:');
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
define('FILENAME_PRODUCTS_TAGS_TEXT','タグ関連設定');
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
define('FILENAME_BANK_CL_TEXT','銀行営業日');
define('FILENAME_PW_MANAGER_TEXT','ID管理');
define('FILENAME_COMPUTERS_TEXT','PC管理');
define('FILENAME_MAG_UP_TEXT','メールマガジン一括登録');
define('FILENAME_MAG_DL_TEXT','メールマガジンデータDL');
