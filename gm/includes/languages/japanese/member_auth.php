<?php
/*
  $Id$

*/
define('NAVBAR_TITLE', 'メール認証');
define('HEADING_TITLE', 'メール認証');
define('ACTIVE_INFO_COMMENT', '<font color="#ff0000">メールアドレスに間違いがあった場合は上記、送信フォームより正しいメールアドレスに修正後、再度メールを送信してください。</font><br><br><b>正常に受信できる場合：</b>「送信」後5分以内で'.STORE_NAME.'からの確認メールが届きます。<br><b>受信できない場合：</b>「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。<br><br>メール受信の設定に関しては、 「<a href="'.tep_href_link('email_trouble.php').'">フリーメールでメールが受け取れない方へ</a>」 をご参考ください。');
define('WRONG_EMAIL_PATTERN_NOTICE', 'メールアドレスが間違っています。使用できない文字が含まれている可能性があります。');
define('EMAIL_NAME_COMMENT_LINK', ' 様 ');
define('EMAIL_RED_TEXT_INFO', 'メールを送信致しました');
define('EMAIL_READ_INFO_TEXT', STORE_NAME.'をご利用いただくには、メール認証を行う必要があります。<br>メール内に記載してあるURLをクリックしメール認証を完了させてから'.STORE_NAME.'を利用することが出来ます。');
define('NOTICE_SEND_TO_EMAIL_TEXT', '<b>%sに確認メールを送信しました</b>');
define('ACTIVE_INFO_FRONT_COMMENT', '<font color="red">メールアドレスに間違いがあった場合は、下記フォームより正しいメールアドレスに修正後「送信」ボタンをクリックしてください。</font>');
define('ACTIVE_INFO_END_COMMENT', 'メールが届きましたら、メールの本文の「メール認証URL」をクリックしてください。<br>URLをクリックしても、画面が正しく表示されないときは、メール内のURLをコピーしブラウザのアドレス入力欄に貼り付けてください。');
define('ACTIVE_INFO_EMAIL_READ', '<b>メールが受信できない場合</b><br> Yahoo!メール、Hotmail、Gmail等のフリーメールをご利用の際は迷惑メールフォルダーに振り分けられる場合がございますので、届かない場合は迷惑メールフォルダーをご確認ください。<br> <br> ※ メールが届かない場合には、「'.STORE_DOMAIN.'」からのメールを受信できる設定であることをご確認ください。<br> ※ フリーメールをご利用の場合、メールの受信までにお時間がかかる場合がございます。<br> <br> それでも届かない場合は上記フォームよりメールアドレスを変更してください。「送信」ボタンをクリックするとメールが送信されます。');
?>
