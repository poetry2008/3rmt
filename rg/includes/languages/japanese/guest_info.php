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
define('GUEST_SUCCESS_INFO_COMMENT', STORE_NAME.'をご利用いただくには、メール認証を行う必要があります。<br>メール内にあるURLをクリックしていただいてから引き続き '.STORE_NAME.'をご利用くださいませ。<br><br><br><b>正常に受信できる場合：</b>「送信」後5分以内でRMT学園からの確認メールが届きます。 <br><b>受信できない場合：</b>「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。 <br><br><br>メール受信の設定に関しては、 「<a href="'.tep_href_link('email_trouble.php').'">フリーメールでメールが受け取れない方へ</a>」 をご参考ください。');
?>
