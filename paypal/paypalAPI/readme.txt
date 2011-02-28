新しいクレジット決済としてペイパルを導入する
現在テストサイトには一応実装が完了しています
追加したファイルと変更したファイルをhaomaiが理解してください

予定としては、今週中にhaomaiがペイパルを理解して
来週中に本サイトへの実装をするという予定になっています

必要な情報や、わからない事があれば聞いてください

ファイル名は自由に変更してください

ファイルを設置している場所もhaomaiがわかり易い場所に変更してください

ペイパルAPIリファレンスURL
SetExpressCheckout API
https://cms.paypal.com/jp/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout

GetExpressCheckoutDetails API
https://cms.paypal.com/jp/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails

DoExpressCheckoutPayment API
https://cms.paypal.com/jp/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment

ペイパル基本設定
https://cms.paypal.com/jp/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_NVPAPIBasics



ペイパルの流れ
旧（テレコム）
checkout_payment.php
↓
telecom.php
↓
checkout_confirmation.php
↓
テレコムサイト
↓
checkout_process.php
↓
注文完了画面

新（ペイパル）
checkout_payment.php
↓
telecom.php
↓
checkout_confirmation.php
↓
SetExpressCheckout.php
SetExpressCheckoutAPI実行
クレジットカード決済準備
↓
ペイパルサイト
↓
checkout_process.php
このファイル内で
GetExpressCheckoutDetailsAPI
DoExpressCheckoutPaymentAPI
上記２つのAPIを実行してクレジットカード決済を終了させる
↓
注文完了画面



ペイパルを導入するに当たって改造したところ

1.telecm.php
254行目から
 $process_button_string =
 このボタンの内容を変更
 

2.checkout_process.php
require('paypal-api.php');
if($order_totals[$i]['code'] =='ot_total' &&  array_key_exists('token', $_REQUEST)){
function getexpress($amt,$token){

上記3種コード追加


ペイパル導入のために追加したファイル

1.
SetExpressCheckout.php

2.
paypal-api.php
ペイパルAPIを動かす関数
SetExpressCheckout.phpのファイル内にも書かれているので
haomaiでSetExpressCheckout.phpのfunction PPHttpPostを削除して
paypal-api.phpをincludeしてください

3.
paypal-api-conf.php
ペイパルAPIを使用するためのユーザIDやパスワードと証明書
telecomで言う番組コードみたいな役割がある


return-value.txtこのファイルは、Defaultでペイパルから返ってくる値を書いています
○は、現在DBに保存されている値
★は、DBに追加で保存する値
haomaiは★の値をDBに保存する
その際にテーブルは作成してはいけない
