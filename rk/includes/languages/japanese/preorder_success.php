<?php
/*
  $Id$
*/
define('INPUT_PREORDER_SEND_MAIL', 'メールアドレス:');
define('PREORDER_SUCCESS_TEXT', 'メールアドレスに間違いがあった場合は上記、送信フォームより正しいメールアドレスに修正後、再度メールを送信してください。<br>サイト名をご利用いただくには、メール認証を行う必要があります。<br>メール内に記載してあるURLをクリックしメール認証を完了させてから'.STORE_NAME.'を利用することが出来ます。<br>正常に受信できる場合：「送信」後5分以内で'.STORE_NAME.'からの確認メールが届きます。<br>受信できない場合：「送信」後5分以上経過しても受信できない場合は、スパムフィルター等で受け取りを拒否されている可能性が高いです。<br>メール受信の設定に関しては、 %sをご参考ください。');
define('ALREADY_SEND_PREMAIL_TEXT', 'エラーが発生しました。\nお手数をお掛けいたしますが、トップページに戻り再度やり直してください');
define('PREORDER_EMAIL_PATTENR_WRONG', 'メールアドレスを正しくご入力下さい。');
define('PREORDER_SUCCESS_TITLE', '予約注文手続完了');
define('PREORDER_SUCCESS_HEAD_TITLE', '予約注文の手続が完了しました！！');
define('PREORDER_SUCCESS_APPOINT_CONTENT', '予約内容');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_NAME', '商品名：');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_NUM', '希望個数：');
define('PREORDER_SUCCESS_APPOINT_PRODUCT_DATE', '期限：');
define('PREORDER_SUCCESS_UNIT_TEXT', '個');
define('PREORDER_SUCCESS_YEAR_TEXT', '年');
define('PREORDER_SUCCESS_MONTH_TEXT', '月');
define('PREORDER_SUCCESS_DAY_TEXT', '日');
define('PREORDER_SUCCESS_READ_INFO', STORE_NAME.'では、%sの予約サービスを行っております。 <br>ご希望する数量が弊社在庫にある場合は「%s」をクリックしてお手続きください。<br><br>「予約注文受付」メールをお送りしましたのでご確認ください。<br>納期に付きましては、24時間以内にご回答いたします。<br><br>今すぐ電子メールをご確認ください。「予約注文受付」メールが届いていない場合は、受付が完了しておりません。<br>メールアドレスをご確認の上、再度お申し込みをお願いいたします。<br><br>10分経過してもメールが届かない場合は、再送いたしますのでご連絡ください。<br>注意：メールが届かないときは、以下のことを必ずご確認ください。<br>＜迷惑メールフォルダの確認＞弊社のメールが
    「迷惑メールフォルダ」や「ゴミ箱」に振り分けされ見落としていませんか？<br>＜メールドメインの受信制限を設定している＞<br>'.STORE_DOMAIN.'のメールドメインを受信するように設定をお願いいたします。<br>＜それでも届かないときは、メールアドレス変更＞<br>お客様情報から今すぐご登録メールアドレスの変更をお願いいたします。');
define('PREORDER_SUCCESS_UNACTIVE_HEAD_TITLE', 'メール認証');
define('PREORDER_SUCCESS_UNACTIVE_TITLE', 'メール認証');
define('PREORDER_SUCCESS_TEXT_LINK', '「フリーメールでメールが受け取れない方へ」');
