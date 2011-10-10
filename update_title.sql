UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：API Keys' WHERE `configuration`.`configuration_key` = 'API_KEYS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証のタイトル（会員）' WHERE `configuration`.`configuration_key` = 'ACTIVE_ACCOUNT_EMAIL_TITLE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証の内容（会員）' WHERE `configuration`.`configuration_key` = 'ACTIVE_ACCOUNT_EMAIL_CONTENT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証のタイトル（ゲスト）' WHERE `configuration`.`configuration_key` = 'GUEST_LOGIN_EMAIL_TITLE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証の内容（ゲスト）' WHERE `configuration`.`configuration_key` = 'GUEST_LOGIN_EMAIL_CONTENT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証のタイトル（会員編集）' WHERE `configuration`.`configuration_key` = 'ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証の内容（会員編集）' WHERE `configuration`.`configuration_key` = 'ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT';


--
-- preorder
--

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証のタイトル（予約注文)' WHERE `configuration`.`configuration_key` = 'PREORDER_MAIL_ACTIVE_SUBJECT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メール認証の内容（予約注文）' WHERE `configuration`.`configuration_key` = 'PREORDER_MAIL_ACTIVE_CONTENT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：予約完了メールのタイトル' WHERE `configuration`.`configuration_key` = 'PREORDER_MAIL_SUBJECT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：予約完了メールの内容' WHERE `configuration`.`configuration_key` = 'PREORDER_MAIL_CONTENT';

--
-- end
--

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ホームページタイトル' WHERE `configuration`.`configuration_key` = 'C_TITLE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ホームページキーワード' WHERE `configuration`.`configuration_key` = 'C_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ホームページ説明' WHERE `configuration`.`configuration_key` = 'C_DESCRIPTION';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ロボット' WHERE `configuration`.`configuration_key` = 'C_ROBOTS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ホームページ著作者' WHERE `configuration`.`configuration_key` = 'C_AUTHER';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：Eメール署名' WHERE `configuration`.`configuration_key` = 'C_EMAIL_FOOTER';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：会員登録メール' WHERE `configuration`.`configuration_key` = 'C_CREAT_ACCOUNT';

UPDATE  `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：注文メール',`configuration_key` = 'C_ORDER' WHERE `configuration`.`configuration_key` = 'C_ORDER';

UPDATE  `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：代金引換' WHERE `configuration`.`configuration_key` = 'C_COD_TABLE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：銀行振込' WHERE `configuration`.`configuration_key` = 'C_BANK';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：郵便振替' WHERE `configuration`.`configuration_key` = 'C_POSTAL';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：クレジットカード' WHERE `configuration`.`configuration_key` = 'C_CC';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：フッターコピーライト' WHERE `configuration`.`configuration_key` = 'C_FOOTER_COPY_RIGHT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：コンビニ決済' WHERE `configuration`.`configuration_key` = 'C_CONVENIENCE_STORE';

--
-- 乐天
--

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：楽天銀行' WHERE `configuration`.`configuration_key` = 'C_RAKUTEN_BANK';

--
-- end
--


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：トップページのヘッダー内容' WHERE `configuration`.`configuration_key` = 'DEFAULT_PAGE_TOP_CONTENTS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：トップページのフッター内容 ' WHERE `configuration`.`configuration_key` = 'DEFAULT_PAGE_BOTTOM_CONTENTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ポイント（買い取り）' WHERE `configuration`.`configuration_key` = 'PAYMENT_POINT_DESCRIPTION';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：来店支払い' WHERE `configuration`.`configuration_key` = 'PAYMENT_FETCH_GOOD_DESCRIPTION';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：支払いなし' WHERE `configuration`.`configuration_key` = 'PAYMENT_FREE_PAYMENT_DESCRIPTION';


UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：注文管理画面リロード時間設定' WHERE `configuration`.`configuration_key` = 'DS_ADMIN_ORDER_RELOAD';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：取引方法の設定' WHERE `configuration`.`configuration_key` = 'DS_TORIHIKI_HOUHOU';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：NEW表示期限' WHERE `configuration`.`configuration_key` = 'DS_LATEST_NEWS_NEW_LIMIT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：キャッシュを使用' WHERE `configuration`.`configuration_key` = 'USE_CACHE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：キャッシュ・ディレクトリ' WHERE `configuration`.`configuration_key` = 'DIR_FS_CACHE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：GZip圧縮を使用する' WHERE `configuration`.`configuration_key` = 'GZIP_COMPRESSION';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：圧縮レベル' WHERE `configuration`.`configuration_key` = 'GZIP_LEVEL';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：Force Cookie Use' WHERE `configuration`.`configuration_key` = 'SESSION_FORCE_COOKIE_USE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：セッションの再生成' WHERE `configuration`.`configuration_key` = 'SESSION_RECREATE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：レビューの最小間隔時間' WHERE `configuration`.`configuration_key` = 'REVIEWS_TIME_LIMIT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：毎日レビューの回数' WHERE `configuration`.`configuration_key` = 'REVIEWS_DAY_LIMIT';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：性別' WHERE `configuration`.`configuration_key` = 'ACCOUNT_GENDER';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：生年月日' WHERE `configuration`.`configuration_key` = 'ACCOUNT_DOB';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：会社' WHERE `configuration`.`configuration_key` = 'ACCOUNT_COMPANY';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：住所2' WHERE `configuration`.`configuration_key` = 'ACCOUNT_SUBURB';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：都道府県' WHERE `configuration`.`configuration_key` = 'ACCOUNT_STATE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：国名コード' WHERE `configuration`.`configuration_key` = 'STORE_ORIGIN_COUNTRY';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：地域コード' WHERE `configuration`.`configuration_key` = 'STORE_ORIGIN_ZONE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：郵便番号' WHERE `configuration`.`configuration_key` = 'STORE_ORIGIN_ZIP';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：配送可能な最大パッケージ重量' WHERE `configuration`.`configuration_key` = 'SHIPPING_MAX_WEIGHT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：パッケージ重量' WHERE `configuration`.`configuration_key` = 'SHIPPING_BOX_WEIGHT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：大パッケージ - 増加率(%)' WHERE `configuration`.`configuration_key` = 'SHIPPING_BOX_PADDING';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：在庫水準のチェック' WHERE `configuration`.`configuration_key` = 'STOCK_CHECK';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：在庫数から引き算' WHERE `configuration`.`configuration_key` = 'STOCK_LIMITED';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：精算を許可(変更不可)' WHERE `configuration`.`configuration_key` = 'STOCK_ALLOW_CHECKOUT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：在庫切れ商品のサイン' WHERE `configuration`.`configuration_key` = 'STOCK_MARK_PRODUCT_OUT_OF_STOCK';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：在庫の再注文水準' WHERE `configuration`.`configuration_key` = 'STOCK_REORDER_LEVEL';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ページ・パース時間を記録' WHERE `configuration`.`configuration_key` = 'STORE_PAGE_PARSE_TIME';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ログの格納先' WHERE `configuration`.`configuration_key` = 'STORE_PAGE_PARSE_TIME_LOG';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ログ日付形式' WHERE `configuration`.`configuration_key` = 'STORE_PARSE_DATE_TIME_FORMAT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ページ・パース時間を表示' WHERE `configuration`.`configuration_key` = 'DISPLAY_PAGE_PARSE_TIME';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：データベース問い合わせを記録' WHERE `configuration`.`configuration_key` = 'STORE_DB_TRANSACTIONS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mail送信設定' WHERE `configuration`.`configuration_key` = 'EMAIL_TRANSPORT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mailの改行' WHERE `configuration`.`configuration_key` = 'EMAIL_LINEFEED';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：メール送信にMIME HTMLを使用' WHERE `configuration`.`configuration_key` = 'EMAIL_USE_HTML';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-MailアドレスをDNSで確認' WHERE `configuration`.`configuration_key` = 'ENTRY_EMAIL_ADDRESS_CHECK';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mailを送信' WHERE `configuration`.`configuration_key` = 'SEND_EMAILS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ダウンロードを有効化' WHERE `configuration`.`configuration_key` = 'DOWNLOAD_ENABLED';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：リダイレクトによるダウンロード' WHERE `configuration`.`configuration_key` = 'DOWNLOAD_BY_REDIRECT';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：有効期限(日)' WHERE `configuration`.`configuration_key` = 'DOWNLOAD_MAX_DAYS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ダウンロード最高回数' WHERE `configuration`.`configuration_key` = 'DOWNLOAD_MAX_COUNT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mailアドレス' WHERE `configuration`.`configuration_key` = 'AFFILIATE_EMAIL_ADDRESS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：会員ごとの還元率の設定' WHERE `configuration`.`configuration_key` = 'AFFILATE_INDIVIDUAL_PERCENTAGE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：下位階層の有効化' WHERE `configuration`.`configuration_key` = 'AFFILATE_USE_TIER';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：IPアドレス警告文字列' WHERE `configuration`.`configuration_key` = 'IP_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ユーザーエージェント警告文字列' WHERE `configuration`.`configuration_key` = 'USER_AGENT_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ホスト名警告文字列' WHERE `configuration`.`configuration_key` = 'HOST_NAME_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：OS警告文字列' WHERE `configuration`.`configuration_key` = 'OS_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ブラウザの種類警告文字列' WHERE `configuration`.`configuration_key` = 'BROWSER_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ブラウザの言語警告文字列' WHERE `configuration`.`configuration_key` = 'HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：パソコンの言語環境警告文字列' WHERE `configuration`.`configuration_key` = 'SYSTEM_LANGUAGE_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ユーザーの言語環境警告文字列' WHERE `configuration`.`configuration_key` = 'USER_LANGUAGE_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：画面の解像度警告文字列' WHERE `configuration`.`configuration_key` = 'SCREEN_RESOLUTION_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：画面の色警告文字列' WHERE `configuration`.`configuration_key` = 'COLOR_DEPTH_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Flash警告文字列' WHERE `configuration`.`configuration_key` = 'FLASH_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Flashのバージョン警告文字列' WHERE `configuration`.`configuration_key` = 'FLASH_VERSION_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Director警告文字列' WHERE `configuration`.`configuration_key` = 'DIRECTOR_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Quick time警告文字列' WHERE `configuration`.`configuration_key` = 'QUICK_TIME_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Real player警告文字列' WHERE `configuration`.`configuration_key` = 'REAL_PLAYER_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Windows media警告文字列' WHERE `configuration`.`configuration_key` = 'WINDOWS_MEDIA_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：PDF警告文字列' WHERE `configuration`.`configuration_key` = 'PDF_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：JAVA警告文字列' WHERE `configuration`.`configuration_key` = 'JAVA_LIGHT_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ブラックリスト' WHERE `configuration`.`configuration_key` = 'TELNO_KEYWORDS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：取り扱い注意' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_TRANS_NOTICE';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：取引待ち' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_TRANS_WAIT';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：入力済み' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_INPUT_FINISH';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Order Info' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_ORDER_INFO';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Customer Info' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_CUSTOMER_INFO';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Referer Info' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_REFERER_INFO';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Order History' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_ORDER_HISTORY';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：信用調査' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_REPUTAION_SEARCH';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：数量/商品名' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_PRODUCT_LIST';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：Order Comment' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_ORDER_COMMENT';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：顧客名' WHERE `configuration`.`configuration_key` = 'ORDER_INFO_BASIC_TEXT';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：全てのゲームのRSSアドレス' WHERE `configuration`.`configuration_key` = 'ALL_GAME_RSS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：リダイレクトドメイン' WHERE `configuration`.`configuration_key` = 'IDPW_START_URL';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ドメイン' WHERE `configuration`.`configuration_key` = 'STORE_DOMAIN';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：全てのゲームのRSSアドレス' WHERE `configuration`.`configuration_key` = 'ALL_GAME_RSS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ショップ名' WHERE `configuration`.`configuration_key` = 'STORE_NAME';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ショップ・オーナー名' WHERE `configuration`.`configuration_key` = 'STORE_OWNER';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mail アドレス' WHERE `configuration`.`configuration_key` = 'STORE_OWNER_EMAIL_ADDRESS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mail の差出人' WHERE `configuration`.`configuration_key` = 'EMAIL_FROM';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：国名' WHERE `configuration`.`configuration_key` = 'STORE_COUNTRY';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：地域' WHERE `configuration`.`configuration_key` = 'STORE_ZONE';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：入荷予定商品のソート順' WHERE `configuration`.`configuration_key` = 'EXPECTED_PRODUCTS_SORT';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：入荷予定商品のソート・フィールド' WHERE `configuration`.`configuration_key` = 'EXPECTED_PRODUCTS_FIELD';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：デフォルト言語/通貨の変更' WHERE `configuration`.`configuration_key` = 'USE_DEFAULT_LANGUAGE_CURRENCY';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：注文通知メールの送付先' WHERE `configuration`.`configuration_key` = 'SEND_EXTRA_ORDER_EMAILS_TO';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：サーチエンジン対策のURLを使用(開発中)' WHERE `configuration`.`configuration_key` = 'SEARCH_ENGINE_FRIENDLY_URLS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：商品の追加後にカートを表示' WHERE `configuration`.`configuration_key` = 'DISPLAY_CART';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ゲストに「友達に知らせる」を許可' WHERE `configuration`.`configuration_key` = 'ALLOW_GUEST_TO_TELL_A_FRIEND';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：デフォルトの検索演算子' WHERE `configuration`.`configuration_key` = 'ADVANCED_SEARCH_DEFAULT_OPERATOR';


UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ショップの住所と電話番号' WHERE `configuration`.`configuration_key` = 'STORE_NAME_ADDRESS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：カテゴリー内の商品数を表示' WHERE `configuration`.`configuration_key` = 'SHOW_COUNTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：税額の小数点位置' WHERE `configuration`.`configuration_key` = 'TAX_DECIMAL_PLACES';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：税込みの金額を表示' WHERE `configuration`.`configuration_key` = 'DISPLAY_PRICE_WITH_TAX';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：税額の端数処理' WHERE `configuration`.`configuration_key` = 'TAX_ROUND_OPTION';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：管理画面ロゴ' WHERE `configuration`.`configuration_key` = 'ADMINPAGE_LOGO_IMAGE';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：お問い合わせ用メールアドレス' WHERE `configuration`.`configuration_key` = 'SUPPORT_EMAIL_ADDRESS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：送信済メール' WHERE `configuration`.`configuration_key` = 'SENTMAIL_ADDRESS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：プリントメール' WHERE `configuration`.`configuration_key` = 'PRINT_EMAIL_ADDRESS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：カラー検索機能' WHERE `configuration`.`configuration_key` = 'COLOR_SEARCH_BOX_TF';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：最低販売金額設定' WHERE `configuration`.`configuration_key` = 'LIMIT_MIN_PRICE';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：名前' WHERE `configuration`.`configuration_key` = 'ENTRY_FIRST_NAME_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：姓' WHERE `configuration`.`configuration_key` = 'ENTRY_LAST_NAME_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：生年月日' WHERE `configuration`.`configuration_key` = 'ENTRY_DOB_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：E-Mail アドレス' WHERE `configuration`.`configuration_key` = 'ENTRY_EMAIL_ADDRESS_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：住所1' WHERE `configuration`.`configuration_key` = 'ENTRY_STREET_ADDRESS_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：会社' WHERE `configuration`.`configuration_key` = 'ENTRY_COMPANY_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：郵便番号' WHERE `configuration`.`configuration_key` = 'ENTRY_POSTCODE_MIN_LENGTH';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：市区町村' WHERE `configuration`.`configuration_key` = 'ENTRY_CITY_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：都道府県' WHERE `configuration`.`configuration_key` = 'ENTRY_STATE_MIN_LENGTH';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：電話番号' WHERE `configuration`.`configuration_key` = 'ENTRY_TELEPHONE_MIN_LENGTH';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：パスワード' WHERE `configuration`.`configuration_key` = 'ENTRY_PASSWORD_MIN_LENGTH';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：クレジットカード所有者名' WHERE `configuration`.`configuration_key` = 'CC_OWNER_MIN_LENGTH';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：クレジットカード番号' WHERE `configuration`.`configuration_key` = 'CC_NUMBER_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：レビューの文章' WHERE `configuration`.`configuration_key` = 'REVIEW_TEXT_MIN_LENGTH';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ベストセラー' WHERE `configuration`.`configuration_key` = 'MIN_DISPLAY_BESTSELLERS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：同時購入' WHERE `configuration`.`configuration_key` = 'MIN_DISPLAY_ALSO_PURCHASED';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新着商品日数条件' WHERE `configuration`.`configuration_key` = 'BOX_NEW_PRODUCTS_DAY_LIMIT';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：買い忘れバナー最大表示数' WHERE `configuration`.`configuration_key` = 'CART_TAG_PRODUCTS_MAX';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：顧客へ E-Mail送信' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_CUSTOMER_MAIL_RESULTS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：ID管理表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PW_MANAGER_RESULTS';

UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：FAQ表示の最大値' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_FAQ_ADMIN';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：最新情報' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_LATEST_NEWS';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：トップページのゲームニュースの表示数　' WHERE `configuration`.`configuration_key` = 'GAME_NEWS_MAX_DISPLAY';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：カテゴリのゲームニュース表示数' WHERE `configuration`.`configuration_key` = 'CATEGORIES_GAME_NEWS_MAX_DISPLAY';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：アドレス帳の登録数' WHERE `configuration`.`configuration_key` = 'MAX_ADDRESS_BOOK_ENTRIES';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：注文上限金額設定' WHERE `configuration`.`configuration_key` = 'DS_LIMIT_PRICE';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：検索結果表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_SEARCH_RESULTS';


UPDATE `configuration` SET  `configuration_title`  = 'バックエンド：注文表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_ORDERS_RESULTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：ページ・リンク数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PAGE_LINKS';



UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：特価商品表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_SPECIAL_PRODUCTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新着商品表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_NEW_PRODUCTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：入荷予定商品表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_UPCOMING_PRODUCTS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メーカー・リスト表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_MANUFACTURERS_IN_A_LIST';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メーカー選択サイズ' WHERE `configuration`.`configuration_key` = 'MAX_MANUFACTURERS_LIST';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：メーカー名の長さ' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_MANUFACTURER_NAME_LEN';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新しいレビュー' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_NEW_REVIEWS';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新着商品のランダム選択数' WHERE `configuration`.`configuration_key` = 'MAX_RANDOM_SELECT_NEW';



UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：１行に表示するカテゴリー数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_CATEGORIES_PER_ROW';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新着商品一覧' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PRODUCTS_NEW';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：ベストセラー' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_BESTSELLERS';



UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：同時購入' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_ALSO_PURCHASED';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：顧客の注文履歴ボックス' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX';

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：注文履歴' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_ORDER_HISTORY';


UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド：新着商品表示数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PRODUCTS_RESULTS'; 

UPDATE `configuration` SET  `configuration_title`  = 'フロントエンド・バックエンド：商品リスト表示件数' WHERE `configuration`.`configuration_key` = 'MAX_DISPLAY_PRODUCTS_ADMIN';

	

