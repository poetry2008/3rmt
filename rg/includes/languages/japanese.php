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
// on RedHat try 'en_US'
// on FreeBSD try 'en_US.ISO_8859-1'
// on Windows try 'en', or 'English'
//@setlocale(LC_TIME, 'ja_JP');
@setlocale(LC_TIME, 'en_US');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Y年%m月%d日 %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
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

// if USE_DEFAULT_LANGUAGE_CURRENCY is true, use the following currency, instead of the applications default currency (used when changing language)
define('LANGUAGE_CURRENCY', 'JPY');

// Global entries for the <html> tag
define('HTML_PARAMS','dir="LTR" lang="ja"');

// charset for web pages and emails
define('CHARSET', 'UTF-8');    // Shift_JIS / euc-jp / iso-2022-jp

// page title
define('TITLE', STORE_NAME);  //ショップ名等を記述してください。ブラウザの表示タイトルになります。

define('META_TAGS',
 '<meta name = "keywords" content ="'.C_KEYWORDS.'">'."\n"
.'<meta name = "description" content ="'.C_DESCRIPTION.'">'."\n"
.'<meta name = "robots" content ="'.C_ROBOTS.'">'."\n"
.'<meta name = "copyright" content ="'.C_AUTHER.'">'
);

// header text in includes/header.php
define('HEADER_TITLE_CREATE_ACCOUNT', '会員登録');
define('HEADER_TITLE_ABOUT_US','会社概要');
define('HEADER_TITLE_MY_ACCOUNT', 'お客様情報');
define('HEADER_TITLE_CART_CONTENTS', 'カートを見る');
define('HEADER_TITLE_CHECKOUT', 'レジに進む');
define('HEADER_TITLE_TOP', 'RMT');
define('HEADER_TITLE_CATALOG', 'カタログ');
define('HEADER_TITLE_LOGOFF', 'ログアウト');
define('HEADER_TITLE_LOGIN', 'ログイン');
define('HEADER_TITLE_SITEMAP', 'サイトマップ');
define('HEADER_TITLE_NEWS', '最新情報');
define('MYACCOUNT_EDIT', 'お客様情報の編集');
define('MYACCOUNT_ADDRESS', 'アドレス帳');
define('MYACCOUNT_HISTORY', '注文履歴');
define('MYACCOUNT_NOTIFICATION', 'ショップからのお知らせ');
define('MENU_MU','メーカー一覧');

// footer text in includes/footer.php
define('FOOTER_TEXT_REQUESTS_SINCE', 'リクエスト ('); // 'requests since'
define('FOOTER_TEXT_REQUESTS_SINCE_ADD', ' より)'); // 'requests since' Add Japanese osCommerce

// text for gender
define('MALE', '男性');
define('FEMALE', '女性');
define('MALE_ADDRESS', ''); //Mr.
define('FEMALE_ADDRESS', '');   //Ms.

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// categories box text in includes/boxes/categories.php
define('BOX_HEADING_CATEGORIES', 'カテゴリー');

// manufacturers box text in includes/boxes/manufacturers.php
define('BOX_HEADING_MANUFACTURERS', 'メーカー');

// whats_new box text in includes/boxes/whats_new.php
define('BOX_HEADING_WHATS_NEW', '新着商品');

// quick_find box text in includes/boxes/quick_find.php
define('BOX_HEADING_SEARCH', '商品検索');
define('BOX_SEARCH_TEXT', 'キーワードを入力して商品を探せます');
define('BOX_SEARCH_ADVANCED_SEARCH', '詳細検索');

// specials box text in includes/boxes/specials.php
define('BOX_HEADING_SPECIALS', '特価商品');

// reviews box text in includes/boxes/reviews.php
define('BOX_HEADING_REVIEWS', 'レビュー');
define('BOX_REVIEWS_WRITE_REVIEW', 'レビューを書く');
define('BOX_REVIEWS_NO_REVIEWS', '現在レビューはありません');
define('BOX_REVIEWS_TEXT_OF_5_STARS', '5点中の %s点!');

// shopping_cart box text in includes/boxes/shopping_cart.php
define('BOX_HEADING_SHOPPING_CART', 'ショッピングカート');
define('BOX_SHOPPING_CART_EMPTY', 'カートは空です...');

// order_history box text in includes/boxes/order_history.php
define('BOX_HEADING_CUSTOMER_ORDERS', 'ご注文履歴');

// best_sellers box text in includes/boxes/best_sellers.php
define('BOX_HEADING_BESTSELLERS', 'ランキング');
define('BOX_HEADING_BESTSELLERS_IN', '&nbsp;&nbsp;<br>のランキング');

// notifications box text in includes/boxes/products_notifications.php
define('BOX_HEADING_NOTIFICATIONS', 'メールでお知らせ');
define('BOX_NOTIFICATIONS_NOTIFY', '<b>%s</b>の最新情報を知らせて!');
define('BOX_NOTIFICATIONS_NOTIFY_REMOVE', '<b>%s</b>の最新情報を知らせないで');

// manufacturer box text
define('BOX_HEADING_MANUFACTURER_INFO', 'メーカー情報');
define('BOX_MANUFACTURER_INFO_HOMEPAGE', '%s ホームページ');
define('BOX_MANUFACTURER_INFO_OTHER_PRODUCTS', 'その他の商品');

// languages box text in includes/boxes/languages.php
define('BOX_HEADING_LANGUAGES', '言語');

// currencies box text in includes/boxes/currencies.php
define('BOX_HEADING_CURRENCIES', '通貨');

// information box text in includes/boxes/information.php
define('BOX_HEADING_INFORMATION', 'インフォメーション');

define('BOX_INFORMATION_PRIVACY', 'プライバシーについて');
define('BOX_INFORMATION_COMPANY', '会社概要');
define('BOX_INFORMATION_CONDITIONS', 'ご利用方法');
define('BOX_INFORMATION_SHIPPING', '配送と返品について');
define('BOX_INFORMATION_PAYMENT', 'お支払いについて');
define('BOX_INFORMATION_FAQ', 'よくある質問');
define('BOX_INFORMATION_CONTACT', 'お問い合わせ');


// tell a friend box text in includes/boxes/tell_a_friend.php
define('BOX_HEADING_TELL_A_FRIEND', '友達に知らせる');
define('BOX_TELL_A_FRIEND_TEXT', 'この商品のURLを友達にメールする');

// checkout procedure text
define('CHECKOUT_BAR_PRODUCTS',    'キャラクター名');
define('CHECKOUT_BAR_DELIVERY',    '取引日時');
define('CHECKOUT_BAR_PAYMENT',     '支払方法');
define('CHECKOUT_BAR_CONFIRMATION','最終確認');
define('CHECKOUT_BAR_FINISHED',    '手続完了!');

// pull down default text
define('PULL_DOWN_DEFAULT', '選択してください');
define('TYPE_BELOW', '下に入力');

// javascript messages
define('JS_ERROR', '入力フォームでエラーが起きています!\n次の項目を修正してください:\n\n');

define('JS_REVIEW_TEXT', '* レビューの文章は少なくても ' . REVIEW_TEXT_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_REVIEW_RATING', '* 商品の採点をしてください。\n');

define('JS_GENDER', '* \'性別\' が選択されていません。\n');
define('JS_FIRST_NAME', '* \'名前\' は少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_LAST_NAME', '* \'姓\' は少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');

define('JS_FIRST_NAME_F', '* \'名前(フリガナ)\' は少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_LAST_NAME_F', '* \'姓(フリガナ)\' は少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上必要です。\n');

define('JS_DOB', '* \'生年月日\' は次の形式で入力してください: xxxx/xx/xx (年/月/日).\n');
define('JS_EMAIL_ADDRESS', '* \'メールアドレス\' は少なくても ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_ADDRESS', '* \'住所1\' は少なくても ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_POST_CODE', '* \'郵便番号\' は少なくても ' . ENTRY_POSTCODE_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_CITY', '* \'市区町村\' は少なくても ' . ENTRY_CITY_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_STATE', '* \'都道府県\' を選択または入力してください。\n');
define('JS_COUNTRY', '* \'国\' を選択してください。');
define('JS_TELEPHONE', '* \'電話番号\' は少なくても ' . ENTRY_TELEPHONE_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_PASSWORD', '* \'パスワード\' と \'パスワードを再入力\' は一致していて ' . ENTRY_PASSWORD_MIN_LENGTH . ' 文字以上必要です。\n');
define('JS_AGREEMENT', '* \'利用規約\' を同意してください。');

define('JS_ERROR_NO_PAYMENT_MODULE_SELECTED', '* ご注文商品のお支払方法を選択してください。\n');
define('JS_ERROR_SUBMITTED', 'このフォームは既に送信されています。Okボタンを押し処理が完了するまでもうしばらくお待ちください。');

define('ERROR_NO_PAYMENT_MODULE_SELECTED', '* ご注文商品のお支払方法を選択してください。');

define('CATEGORY_COMPANY', '会社情報');
define('CATEGORY_PERSONAL', '個人情報');
define('CATEGORY_ADDRESS', 'ご住所');
define('CATEGORY_CONTACT', 'ご連絡先');
define('CATEGORY_OPTIONS', 'オプション');
define('CATEGORY_PASSWORD', 'パスワード');
define('CATEGORY_AGREEMENT', '利用規約');
define('ENTRY_COMPANY', '会社/部署名:');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_COMPANY_TEXT', '');
define('ENTRY_GENDER', '性別:');
define('ENTRY_GENDER_ERROR', '&nbsp;<small><font color="#AABBDD">が必要です。</font></small>');
define('ENTRY_GENDER_TEXT', '&nbsp;<small><font color="#AABBDD">必須</font></small>');
define('ENTRY_FIRST_NAME', '名:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_FIRST_NAME_TEXT', '&nbsp;<small>(例. 太郎) <font color="#FC0000">必須</font></small>');
define('ENTRY_LAST_NAME', '姓:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_LAST_NAME_TEXT', '&nbsp;<small>(例. 田中) <font color="#FC0000">必須</font></small>');

define('ENTRY_FIRST_NAME_F', '名(フリガナ):');
define('ENTRY_FIRST_NAME_F_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_FIRST_NAME_F_TEXT', '&nbsp;<small>(例. タロウ) <font color="#AABBDD">必須</font></small>');
define('ENTRY_LAST_NAME_F', '姓(フリガナ):');
define('ENTRY_LAST_NAME_F_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_LAST_NAME_F_TEXT', '&nbsp;<small>(例. ヤマダ) <font color="#AABBDD">必須</font></small>');

define('ENTRY_DATE_OF_BIRTH', '生年月日:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<small><font color="#FF0000">(例. 1970/05/21)</font></small>');
define('ENTRY_DATE_OF_BIRTH_TEXT', '&nbsp;<small>(例. 1970/05/21) <font color="#AABBDD">必須</font></small>');
define('ENTRY_EMAIL_ADDRESS', 'メールアドレス:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<small><font color="#FF0000">入力されたメールアドレスは不正です!</font></small>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<small><font color="#FF0000">メールアドレスはすでに存在しています!</font></small>');
define('ENTRY_EMAIL_ADDRESS_TEXT', '&nbsp;<small>(例. sample@example.com) <font color="#FC0000">必須</font></small>');
define('ENTRY_STREET_ADDRESS', '住所１:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_STREET_ADDRESS_TEXT', '&nbsp;<small>(例. 1-15-6) <font color="#AABBDD">必須</font></small>');
define('ENTRY_SUBURB', '住所２:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_SUBURB_TEXT', '');
define('ENTRY_POST_CODE', '郵便番号:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_POSTCODE_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_POST_CODE_TEXT', '&nbsp;<small>(例. 331-0814) <font color="#AABBDD">必須</font></small>');
define('ENTRY_CITY', '市区町村:');
define('ENTRY_CITY_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_CITY_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_CITY_TEXT', '&nbsp;<small>(例. さいたま市) <font color="#AABBDD">必須</font></small>');
define('ENTRY_STATE', '都道府県:');
define('ENTRY_STATE_ERROR', '&nbsp;<small><font color="#FF0000">必須</font></small>');
define('ENTRY_STATE_TEXT', '&nbsp;<small>(例. 埼玉県) <font color="#AABBDD">必須</font></small>');
define('ENTRY_COUNTRY', '国名:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_COUNTRY_TEXT', '&nbsp;<small><font color="#AABBDD">必須</font></small>');
define('ENTRY_TELEPHONE_NUMBER', '電話番号:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_TELEPHONE_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_TELEPHONE_NUMBER_TEXT', '&nbsp;<small>(例. 012-345-6789) <font color="#AABBDD">必須</font></small>');
define('ENTRY_FAX_NUMBER', 'ファクス番号:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_FAX_NUMBER_TEXT', '');
define('ENTRY_NEWSLETTER', 'メールマガジン:');
define('ENTRY_NEWSLETTER_TEXT', '');
define('ENTRY_NEWSLETTER_YES', '購読する');
define('ENTRY_NEWSLETTER_NO', '購読しない');
define('ENTRY_NEWSLETTER_ERROR', '');
define('ENTRY_PASSWORD', 'パスワード:');
define('ENTRY_PASSWORD_CONFIRMATION', 'パスワードを再入力:');
define('ENTRY_PASSWORD_CONFIRMATION_TEXT', '&nbsp;<small>英数字' . ENTRY_PASSWORD_MIN_LENGTH . '文字以上&nbsp;(例. abcdef) <font color="#FC0000">必須</font></small>');
define('ENTRY_PASSWORD_ERROR', '&nbsp;<small><font color="#FF0000">少なくても ' . ENTRY_PASSWORD_MIN_LENGTH . ' 文字</font></small>');
define('ENTRY_PASSWORD_TEXT', '&nbsp;<small>英数字' . ENTRY_PASSWORD_MIN_LENGTH . '文字以上&nbsp;(例. abcdef) <font color="#FC0000">必須</font></small>');
define('PASSWORD_HIDDEN', '********');
define('ENTRY_AGREEMENT_TEXT', '同意する');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'ページ:');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> ある商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> ある注文のうち)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> あるレビューのうち)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> ある新着商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> ある特価商品のうち)');
define('TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> あるお知らせのうち)');

define('PREVNEXT_TITLE_FIRST_PAGE', '最初のページ');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', '前ページ');
define('PREVNEXT_TITLE_NEXT_PAGE', '次ページ');
define('PREVNEXT_TITLE_LAST_PAGE', '最後のページ');
define('PREVNEXT_TITLE_PAGE_NO', 'ページ %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', '前 %d ページ');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', '次 %d ページ');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;最初');
define('PREVNEXT_BUTTON_PREV', '前のページ');
define('PREVNEXT_BUTTON_NEXT', '次のページ');
define('PREVNEXT_BUTTON_LAST', '最後&gt;&gt;');

define('IMAGE_BUTTON_ADD_ADDRESS', 'アドレスを追加');
define('IMAGE_BUTTON_ADDRESS_BOOK', 'アドレス帳');
define('IMAGE_BUTTON_BACK', '前に戻る');
define('IMAGE_BUTTON_CHANGE_ADDRESS', 'アドレスを変更');
define('IMAGE_BUTTON_CHECKOUT', 'レジに進む');
define('IMAGE_BUTTON_CONFIRM_ORDER', '注文する!');
define('IMAGE_BUTTON_CONTINUE', '次へ進む');
define('IMAGE_BUTTON_CONTINUE_SHOPPING', 'ショッピングを続ける');
define('IMAGE_BUTTON_DELETE', '削除する');
define('IMAGE_BUTTON_EDIT_ACCOUNT', 'お客様情報の編集');
define('IMAGE_BUTTON_HISTORY', 'ご注文履歴');
define('IMAGE_BUTTON_LOGIN', 'ログイン');
define('IMAGE_BUTTON_IN_CART', 'カートに入れる');
define('IMAGE_BUTTON_NOTIFICATIONS', 'お知らせの設定');
define('IMAGE_BUTTON_QUICK_FIND', '商品検索');
define('IMAGE_BUTTON_REMOVE_NOTIFICATIONS', 'お知らせを取消す');
define('IMAGE_BUTTON_REVIEWS', 'レビューを読む');
define('IMAGE_BUTTON_SEARCH', '検索する');
define('IMAGE_BUTTON_SHIPPING_OPTIONS', '配送オプション');
define('IMAGE_BUTTON_TELL_A_FRIEND', '友達に知らせる');
define('IMAGE_BUTTON_UPDATE', '更新する');
define('IMAGE_BUTTON_UPDATE_CART', 'カートを更新');
define('IMAGE_BUTTON_WRITE_REVIEW', 'レビューを書く');
define('IMAGE_BUTTON_PRESENT','応募する');//add present
define('IMAGE_BUTTON_QUT','店長に質問');//add present
define('IMAGE_BUTTON_DEC','詳細はこちら');//add present

define('ICON_ARROW_RIGHT', '全商品表示'); //more
define('ICON_CART', 'カートに入れる');
define('ICON_WARNING', '警告');

define('TEXT_GREETING_PERSONAL', 'いらっしゃいませ、<span class="greetUser">%s さん</span>。 <a href="%s"><u>新着商品</u></a> をご覧になりますか？');
define('TEXT_GREETING_PERSONAL_RELOGON', '<small>もしあなたが %s さんでなければ、お客様情報を入力して <a href="%s"><u>ログイン</u></a> してください。</small>');
define('TEXT_GREETING_GUEST', '<span class="greetUser">ゲストさん</span>、いらっしゃいませ。 <a href="%s"><u>ログイン</u></a>しますか？ それとも、<a href="%s"><u>会員登録</u></a>をしますか？');

define('TEXT_SORT_PRODUCTS', '商品を並び替える ');
define('TEXT_DESCENDINGLY', '降順');
define('TEXT_ASCENDINGLY', '昇順');
define('TEXT_BY', ' &sim; ');   //by

define('TEXT_REVIEW_BY', '投稿者： %s');
define('TEXT_REVIEW_WORD_COUNT', '%s 文字');
define('TEXT_REVIEW_RATING', '評価: %s [%s]');
define('TEXT_REVIEW_DATE_ADDED', '投稿日: %s');
define('TEXT_NO_REVIEWS', 'まだレビューはありません...');

define('TEXT_NO_NEW_PRODUCTS', '現在商品は登録されていません...');

define('TEXT_UNKNOWN_TAX_RATE', '税率不明');

define('TEXT_REQUIRED', '必須');

define('TEXT_TIME_SPECIFY', 'お届けする時間帯: '); // add for Japanese update

define('ERROR_TEP_MAIL', '<font face="Verdana, Arial" size="2" color="#ff0000"><b><small>エラー:</small> 指定されたSMTP サーバからメールを送信できません。 php.ini のSMTP サーバ設定を確認して、必要があれば修正してください。</b></font>');
define('WARNING_INSTALL_DIRECTORY_EXISTS', '警告: インストール・ディレクトリ(/install)が存在したままです: ' . dirname($_SERVER['SCRIPT_FILENAME']) . '/install. ディレクトリはセキュリティ上の危険がありますので削除してください。');
define('WARNING_CONFIG_FILE_WRITEABLE', '警告: 設定ファイル(/includes/configure.php)に書き込み権限が設定されたままです: ' . dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php. ファイルのユーザ権限を変更してください。');
define('WARNING_SESSION_DIRECTORY_NON_EXISTENT', '警告: セッション・ディレクトリが存在しません: ' . tep_session_save_path() . '. セッションを利用するためにディレクトリを作成してください。');
define('WARNING_SESSION_DIRECTORY_NOT_WRITEABLE', '警告: セッション・ディレクトリに書き込みができません: ' . tep_session_save_path() . '. セッション・ディレクトリに正しいユーザ権限を設定してください。');
define('WARNING_SESSION_AUTO_START', '警告: セッション・オートスタートが有効になっています。設定ファイル（php.ini）で無効に設定し、ウェブサーバをリスタートしてください。');
define('WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT', '警告: ダウンロード販売商品ディレクトリが存在しません: ' . DIR_FS_DOWNLOAD . '. このディレクトリを作成しない場合ダウンロード販売商品の取扱いが出来ません。');

define('TEXT_CCVAL_ERROR_INVALID_DATE', 'クレジットカード有効期限が正しくありません。<br>ご確認後もう一度入力してください。');
define('TEXT_CCVAL_ERROR_INVALID_NUMBER', 'クレジットカードナンバーが正しくありません。<br>ご確認後もう一度入力してください。');
define('TEXT_CCVAL_ERROR_UNKNOWN_CARD', '入力したクレジットカードナンバーの最初の4桁は: %s です。<br>ナンバーが正しい場合このカードの取扱いがありません。<br>間違っている場合はご確認後もう一度入力してください。');

/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/
define('FOOTER_TEXT_BODY', C_FOOTER_COPY_RIGHT);

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);  //Add Japanese osCommerce

// Include OSC-AFFILIATE 
//include("affiliate_japanese.php");

//Add languages
//------------------------

//create_account
define('ENTRY_DATE_OF_BIRTH_ERROR2', '&nbsp;<small><font color="#FF0000">18歳未満の方の登録はご遠慮ください。</font></small>');

//page - インフォメーションページが見つからなかった時に表示
define('PAGE_TEXT_NOT_FOUND', 'ページが見つかりません...');
define('PAGE_ERR_NAVBER_TITLE', 'ページが見つかりません...');

//latest_news
define('TABLE_HEADING_LATEST_NEWS', '新着情報'); //Add for latest_news

//product_listing
define('LIST_FILTER', '表示オプション');
define('LISTING_DISPLAY', '-- 表示方法 --');
define('LISTING_DEFAULT', 'タイトルと画像');
define('LISTING_IMAGE', '画像のみ');
define('LISTING_TEXT', 'テキストのみ');

// calender box text in includes/boxes/cl.php
define('BOX_HEADING_CL', '&nbsp;の営業日');
define('BOX_CL_COLOR01', '&nbsp;&raquo;&nbsp;店舗休業日');
define('BOX_CL_COLOR02', '&nbsp;&raquo;&nbsp;メール返信休業日');

//add present
define('BOX_HEADING_PRESENT','プレゼント商品！');
define('TEXT_DISPLAY_NUMBER_OF_PRESENT', '<b>%d</b> - <b>%d</b> 番目を表示 (<b>%d</b> あるプレゼント商品のうち)');

define('ENTRY_GUEST', '会員登録:');
define('ENTRY_ACCOUNT_MEMBER', '会員登録をする');
define('ENTRY_ACCOUNT_GUEST', '会員登録をしない');

# 注文状現金額を超えたときのメッセージ
define('DS_LIMIT_PRICE_OVER_ERROR', '一度に%s以上を注文することはできません。<br>合計金額を%s以下にしてから再度お申し込みください。');


define('INPUT_SEND_MAIL', 'メールアドレス');
define('EMAIL_PATTERN_WRONG', 'メールアドレスを正しくご入力下さい。');
define('SENDMAIL_BUTTON', '送信');
define('LINK_SENDMAIL_TEXT', 'メール受信テストをする');
define('LINK_SENDMAIL_TITLE', 'メール受信テスト');
define('SENDMAIL_SUCCESS_TEXT', 'メールが送信されました。RMT学園からの受信をご確認ください。');
define('SENDMAIL_READ_TEXT', '<b>'.STORE_NAME.'からのメールを</b> <br><b>正常に受信できる場合：</b>「送信」後5分以内で<b>'.STORE_NAME.'</b>からの確認メールが届きます。 <br><b>受信できない場合：</b>「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。');
define('SENDMAIL_TROUBLE_PRE', 'メール受信の設定に関しては、');
define('SENDMAIL_TROUBLE_LINK', '<b>「フリーメールでメールが受け取れない方へ」</b>');
define('SENDMAIL_TROUBLE_END', 'をご参考ください。');
define('PAGE_NEW_TITLE', 'インフォメーション');
!defined('TEXT_NO_PRODUCTS')&&define('TEXT_NO_PRODUCTS', '現在商品は登録されていません...');
define('SEND_MAIL_HEADING_TITLE', 'メール受信テスト');

define('TEXT_NO_LATEST_NEWS', 'お知らせはありません');
define('NOTICE_MUST_BUY_TEXT', 'ショッピングカートに商品が有りません、商品を入れてから押してください。');
define('LEFT_SEARCH_TITLE', '商品検索');
define('RIGHT_ORDER_TEXT', '再配達依頼');
define('TEXT_TAGS', 'タグ一覧');
?>
