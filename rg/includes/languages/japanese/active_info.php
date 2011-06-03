<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
define('NAVBAR_TITLE', 'メール認証手続き');
define('HEADING_TITLE', 'メール送信完了');
define('ACTIVE_INFO_COMMENT', '<font color="#ff0000">*メールが届かない場合は上記送信フォームより再度メールを送信してください</font><br><br>'.STORE_NAME.'をご利用いただくには、メール認証を行う必要があります。<br>メール内に記載してあるURLをクリックしメール認証を完了させてから'.STORE_NAME.'を利用することが出来ます。<br><br><br><b>正常に受信できる場合：</b>「送信」後5分以内で'.STORE_NAME.'からの確認メールが届きます。<br><b>受信できない場合：</b>「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。<br><br><br>メール受信の設定に関しては、 「<a href="'.tep_href_link('email_trouble.php').'"><font color="#656565" size="2">フリーメールでメールが受け取れない方へ</font></a>」 をご参考ください。');
?>
