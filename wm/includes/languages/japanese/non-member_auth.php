<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
define('NAVBAR_TITLE', 'メール認証');
define('HEADING_TITLE', 'メール認証');
define('HEADING_TITLE_FINISH', 'メール認証完了');
define('GUEST_SUCCESS_INFO_COMMENT', '<font color="#ff0000">メールアドレスに間違いがあった場合は上記、送信フォームより正しいメールアドレスに修正後、再度メールを送信してください。</font><br><br><b>正常に受信できる場合：</b>「送信」後5分以内で'.STORE_NAME.'からの確認メールが届きます。<br><b>受信できない場合：</b>「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。<br><br>メール受信の設定に関しては、 「<a href="'.tep_href_link('email_trouble.php').'">フリーメールでメールが受け取れない方へ</a>」 をご参考ください。');
define('CHECK_FINISH_TEXT', 'メール認証が完了いたしました。<br>「次へ進む」をクリックしてショッピングを続けてください。');
define('WRONG_EMAIL_PATTERN_NOTICE', 'メールアドレスが無効です。使用できない文字が含まれているか、「＠」の前に「.」が有りませんか、または、「.」「.」が連続である可能性があります。');
define('EMAIL_NAME_COMMENT_LINK', ' 様 ');
define('EMAIL_RED_TEXT_INFO', 'メールを送信致しました');
define('EMAIL_READ_INFO_TEXT', STORE_NAME.'をご利用いただくには、メール認証を行う必要があります。<br>メール内に記載してあるURLをクリックしメール認証を完了させてから'.STORE_NAME.'を利用することが出来ます。');
?>
