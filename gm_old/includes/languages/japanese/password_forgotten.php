<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_FIRST', 'ログイン');
define('NAVBAR_TITLE_SECOND', 'パスワード再発行');
define('HEADING_TITLE', 'パスワード再発行手続き');
define('ENTRY_FORGOTTEN_EMAIL_ADDRESS', 'ご登録のメールアドレス:'); // 2003.03.06 nagata Edit Japanese osCommerce
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>ご注意:</b></font> ご入力されたメールアドレスは見つかりませんでした。もう一度入力してください。');
define('EMAIL_PASSWORD_REMINDER_SUBJECT', 'パスワード再発行手続き');
define('EMAIL_PASSWORD_REMINDER_BODY',
'パスワード再発行依頼がIPアドレス %s からありました。'."\n".
'このメールに関してお心当たりがない場合は、弊社までご連絡ください。'."\n\n".
'以下のURLをクリックしパスワード再発行ページへ移動してください。'."\n".
'なお、このURLは送信時刻から72時間有効となっています。期間経過後は再度'."\n".
'のお手続きをお願いします。'."\n".
'-----------------------------------------------------------'."\n".
'■パスワード再発行URL'."\n\n".
'%s'."\n\n".
'-----------------------------------------------------------'."\n\n\n".
'＜ご確認事項＞'."\n".
'※ URLをクリックしても、画面が正しく表示されないときは、'."\n".
'　 上記URLをコピーしブラウザのアドレス入力欄に貼り付けてください。'."\n\n".
'※ パスワード再発行をキャンセルする場合はこのまま破棄してください。'."\n\n".
'※ こちらのメールは送信専用のメールアドレスからお送りしています。'."\n".
'　 ご返信には、ご返答できかねますのでご了承ください。'."\n\n\n".
'ログインできない等、問題がある場合は、ご遠慮なくご連絡ください。今後と
も、弊社のサービスをよろしくお願いします。'."\n"
);
define('TEXT_PASSWORD_SENT', '新しいパスワードをご登録のメールアドレスに送信しました。');
define('PASSWORD_USER_EMAIL_ERROR','<font color="#ff0000"><b>ご注意:</b></font> 入力されたメールアドレスは不正です!');
?>
