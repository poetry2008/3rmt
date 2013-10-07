<?php
set_time_limit(0);
include('includes/configure.php');
$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");
echo 'start...<br>';

$seo_title_array = array(
      'A_TAG' => 'タグ', 
      'ACCOUNT' => 'お客様情報', 
      'ACCOUNT_EDIT' => 'お客様情報の編集', 
      'ACCOUNT_HISTORY' => '注文履歴', 
      'ACCOUNT_HISTORY_INFO' => '注文情報', 
      'ADVANCED_SEARCH' => '商品検索', 
      'ADVANCED_SEARCH_RESULT' => '検索結果', 
      'BROWSER_IE6X' => 'ブラウザの設定', 
      'CATEGORY' => 'カテゴリ情報', 
      'CHANGE_PREORDER_SUCCESS' => '予約注文の手続きが完了しました', 
      'CHECKOUT_CONFIRMATION' => '最終確認', 
      'CHECKOUT_PAYMENT' => '支払方法', 
      'CHECKOUT_SHIPPING' => '取引日時の指定', 
      'CHECKOUT_SUCCESS' => 'ご注文の手続きが完了しました', 
      'CONTACT_US' => 'お問い合わせ', 
      'CREATE_ACCOUNT' => '会員登録', 
      'CREATE_ACCOUNT_PROCESS' => '会員登録手続き', 
      'CREATE_ACCOUNT_SUCCESS' => '会員登録完了', 
      'DEFAULT_PAGE' => 'ホームページ', 
      'EMAIL_TROUBLE' => 'フリーメールでメールが受け取れない方へ', 
      'FAQ' => 'FAQトップページ', 
      'LOGIN' => 'ログイン', 
      'LOGOFF' => 'ログアウト', 
      'MAIL_MAGAZINE' => 'メールマガジン', 
      'MANUFACTURER' => 'メーカー', 
      'MANUFACTURERS' => 'ゲームメーカー一覧', 
      'MEMBER_AUTH' => 'メール認証(会員)', 
      'NEWS' => 'お知らせ', 
      'NEWS_INFO' => 'お知らせ内容', 
      'NON_MEMBER_AUTH' => 'メール認証(ゲスト)', 
      'PAGE' => 'コンテンツ', 
      'PASSWORD_FORGOTTEN' => 'パスワード再発行', 
      'POPUP_SEARCH_HELP' => '詳細検索の使い方', 
      'PREORDER' => '予約', 
      'PRESENT' => 'プレゼント', 
      'PRESENT_CONFIRMATION' => 'プレゼント応募', 
      'PRESENT_ORDER' => 'プレゼント応募確認画面', 
      'PRESENT_SUCCESS' => 'プレゼント応募完了', 
      'PRODUCT_INFO' => '商品情報', 
      'PRODUCT_NOTIFICATIONS' => '商品のお知らせ', 
      'PRODUCT_REVIEWS' => '商品のレビュー', 
      'PRODUCT_REVIEWS_INFO' => 'レビュー', 
      'PRODUCT_REVIEWS_WRITE' => '商品のレビューを書く', 
      'PRODUCTS_NEW' => '新着商品', 
      'REORDER' => '再配達フォーム', 
      'REORDER2' => '再配達フォーム2', 
      'REVIEWS' => 'レビュー一覧', 
      'SEND_MAIL' => 'メール受信テスト', 
      'SEND_SUCCESS' => 'パスワード再発行手続き', 
      'SHOPPING_CART' => 'ショッピングカート', 
      'SITEMAP' => 'サイトマップ', 
      'SPECIALS' => 'お買い得商品', 
      'TAGS' => 'タグ一覧', 
      'TELL_A_FRIEND' => '友達に知らせる'
    );

$seo_url_array = array(
      'A_TAG' => 'tags/t-1.html', 
      'ACCOUNT' => 'account.php', 
      'ACCOUNT_EDIT' => 'account_edit.php', 
      'ACCOUNT_HISTORY' => 'account_history.php', 
      'ACCOUNT_HISTORY_INFO' => 'account_history_info.php', 
      'ADVANCED_SEARCH' => 'advanced_search.php', 
      'ADVANCED_SEARCH_RESULT' => 'advanced_search_result.php?keywords=RMT&x=40&y=13&inc_subcat=1', 
      'BROWSER_IE6X' => 'browser_ie6x.php', 
      'CATEGORY' => 'rmt/c-168_172.html', 
      'CHANGE_PREORDER_SUCCESS' => 'change_preorder_success.php', 
      'CHECKOUT_CONFIRMATION' => 'checkout_confirmation.php', 
      'CHECKOUT_PAYMENT' => 'checkout_payment.php', 
      'CHECKOUT_SHIPPING' => 'checkout_shipping.php', 
      'CHECKOUT_SUCCESS' => 'checkout_success.php', 
      'CONTACT_US' => 'contact_us.php', 
      'CREATE_ACCOUNT' => 'create_account.php', 
      'CREATE_ACCOUNT_PROCESS' => 'create_account_process.php', 
      'CREATE_ACCOUNT_SUCCESS' => 'create_account_success.php', 
      'DEFAULT_PAGE' => 'index.php', 
      'EMAIL_TROUBLE' => 'email_trouble.php', 
      'FAQ' => 'faq.php', 
      'LOGIN' => 'login.php', 
      'LOGOFF' => 'logoff.php', 
      'MAIL_MAGAZINE' => 'mail_magazine.php', 
      'MANUFACTURER' => 'game/m-43.html', 
      'MANUFACTURERS' => 'manufacturers.php', 
      'MEMBER_AUTH' => 'member_auth.php', 
      'NEWS' => 'news/', 
      'NEWS_INFO' => 'news/1.html', 
      'NON_MEMBER_AUTH' => 'non-member_auth.php', 
      'PAGE' => 'info/orderflow.html', 
      'PASSWORD_FORGOTTEN' => 'password_forgotten.php', 
      'POPUP_SEARCH_HELP' => 'popup_search_help.php', 
      'PREORDER' => 'preorder.php?products_id=31693', 
      'PRESENT' => 'present.php', 
      'PRESENT_CONFIRMATION' => 'present_confirmation.php', 
      'PRESENT_ORDER' => 'present_order.php', 
      'PRESENT_SUCCESS' => 'present_success.php', 
      'PRODUCT_INFO' => 'item/p-31693.html', 
      'PRODUCT_NOTIFICATIONS' => 'product_notifications.php', 
      'PRODUCT_REVIEWS' => 'reviews/pr-31693/', 
      'PRODUCT_REVIEWS_INFO' => 'reviews/pr-32683/230.html', 
      'PRODUCT_REVIEWS_WRITE' => 'product_reviews_write.php?products_id=31693', 
      'PRODUCTS_NEW' => 'products_new.php', 
      'REORDER' => 'reorder.php', 
      'REORDER2' => 'reorder2.php', 
      'REVIEWS' => 'reviews/', 
      'SEND_MAIL' => 'send_mail.php', 
      'SEND_SUCCESS' => 'send_success.php', 
      'SHOPPING_CART' => 'shopping_cart.php', 
      'SITEMAP' => 'sitemap.php', 
      'SPECIALS' => 'specials.php', 
      'TAGS' => 'tags/', 
      'TELL_A_FRIEND' => 'tell_a_friend.php?products_id=31693'
    );

    
$site_list_raw = mysql_query("select * from sites");
while ($site_list = mysql_fetch_array($site_list_raw)) {
  foreach ($seo_title_array as $seo_key => $seo_value) {
    $seo_title_str = '';
    $seo_title_des_str = '';
    
    $seo_keyword_str = ''; 
    $seo_keyword_des_str = ''; 
    
    $seo_des_str = ''; 
    $seo_des_des_str = ''; 
   
    $seo_robots_str = '';
    $seo_copyright_str = '';
    
    $seo_title_raw = mysql_query("select * from configuration where configuration_key = 'MODULE_METASEO_".$seo_key."_TITLE' and (site_id = '".$site_list['id']."' or site_id = 0) order by site_id desc limit 1");
    $seo_title_res = mysql_fetch_array($seo_title_raw);  
    $seo_title_str = $seo_title_res['configuration_value'];
    if ($seo_key == 'CATEGORY') {
      $seo_title_des_str = '#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else if ($seo_key == 'DEFAULT_PAGE') {
      $seo_title_des_str = '#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else {
      $seo_title_des_str = $seo_title_res['configuration_description'];
    }

    
    $seo_keyword_raw = mysql_query("select * from configuration where configuration_key = 'MODULE_METASEO_".$seo_key."_KEYWORDS' and (site_id = '".$site_list['id']."' or site_id = 0) order by site_id desc limit 1");
    $seo_keyword_res = mysql_fetch_array($seo_keyword_raw);  
    $seo_keyword_str = $seo_keyword_res['configuration_value'];
    
    if ($seo_key == 'CATEGORY') {
      $seo_keyword_des_str = '#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else if ($seo_key == 'DEFAULT_PAGE') {
      $seo_keyword_des_str = '#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else {
      $seo_keyword_des_str = $seo_keyword_res['configuration_description'];
    }
    
    $seo_des_raw = mysql_query("select * from configuration where configuration_key = 'MODULE_METASEO_".$seo_key."_DESCRIPTION' and (site_id = '".$site_list['id']."' or site_id = 0) order by site_id desc limit 1");
    $seo_des_res = mysql_fetch_array($seo_des_raw);  
    $seo_des_str = $seo_des_res['configuration_value'];
    
    if ($seo_key == 'CATEGORY') {
      $seo_des_des_str = '#CATEGORIES_NAME#<br>#SEO_NAME#<br>#SEO_DESCRIPTION#<br>#CATEGORIES_META_TEXT#<br>#CATEGORIES_HEADER_TEXT#<br>#CATEGORIES_FOOTER_TEXT#<br>#TEXT_INFORMATION#<br>#META_KEYWORDS#<br>#META_DESCRIPTION#<br>#CATEGORIES_ID#<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else if ($seo_key == 'DEFAULT_PAGE') {
      $seo_des_des_str = '#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#';
    } else {
      $seo_des_des_str = $seo_des_res['configuration_description'];
    }
    
    $seo_robots_raw = mysql_query("select * from configuration where configuration_key = 'MODULE_METASEO_".$seo_key."_ROBOTS' and (site_id = '".$site_list['id']."' or site_id = 0) order by site_id desc limit 1");
    $seo_robots_res = mysql_fetch_array($seo_robots_raw);  
    $seo_robots_str = $seo_robots_res['configuration_value'];
    
    $seo_copyright_raw = mysql_query("select * from configuration where configuration_key = 'MODULE_METASEO_".$seo_key."_COPYRIGHT' and (site_id = '".$site_list['id']."' or site_id = 0) order by site_id desc limit 1");
    $seo_copyright_res = mysql_fetch_array($seo_copyright_raw);  
    $seo_copyright_str = $seo_copyright_res['configuration_value'];
 

    $sql = "insert into `configuration_meta` values(NULL, '".addslashes($seo_value)."', '".addslashes($seo_title_str)."', '".addslashes($seo_title_des_str)."', '".addslashes($seo_keyword_str)."', '".addslashes($seo_keyword_des_str)."', '".addslashes($seo_des_str)."', '".addslashes($seo_des_des_str)."', '".addslashes($seo_robots_str)."', '".addslashes($seo_copyright_str)."', '".$site_list['id']."', '".addslashes($seo_url_array[$seo_key])."', 'MODULE_METASEO_".$seo_key."',NULL, NULL, NULL, NULL)";
    mysql_query($sql); 
  }
}

echo 'finish';
