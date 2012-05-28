<?php
/*
 $Id$
*/
require('includes/application_top.php');
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_MANUFAXTURERS);

define('HEADING_TITLE', '再配達依頼');
define('MINUTES', 30);

$breadcrumb->add('再配達フォーム', tep_href_link('reorder2.php'));
?>
<?php page_head();?>
<script type="text/javascript" src='./js/order.js'></script>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td id="contents" valign="top">
        <h1 class="pageHeading"><span><?php echo HEADING_TITLE; ?></span></h1>
        <?php if($_POST){
          $date     = tep_db_prepare_input($_POST['date']);
          $hour     = tep_db_prepare_input($_POST['hour']);
          $minute   = tep_db_prepare_input($_POST['minute']);

          $name      = tep_db_prepare_input($_POST['name']);
          $character = tep_db_prepare_input($_POST['character']);
          $product   = tep_db_prepare_input($_POST['product']);
          $comment   = tep_db_prepare_input($_POST['comment']);
          $email = tep_db_prepare_input($_POST['email']);
          $email = str_replace("\xe2\x80\x8b", '',$email);

          $datetime = $date.' '.$hour.':'.$minute;
          $time     = strtotime($datetime);
          if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
            // time error
            echo '<div class="comment">取引時間は前もって一時間以上に設定してください <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="70" height="25" alt=""></a></div></div>';
            $email_error = false;
          } else if($name==''||$date==''||$minute==''||$hour==''||
              $character==''||$product==''){
            $email_error = true;
          } else if(!tep_validate_email($email)){
            $email_error = true;
          } else {
            echo '<div class="comment"><div class="product_info_box">注文内容の変更を承りました。電子メールをご確認ください。 <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" width="63" height="18" alt="TOPに戻る" title="TOPに戻る"></a></div></div></div>';

            $email_order = '';
            $email_order .= $name . "様\n";
            $email_order .= "\n";
            $email_order .= "この度は、ご連絡いただき誠にありがとうございます。\n";
            $email_order .= "お客様から、下記の注文内容にて再配達を承りました。\n";
            $email_order .= "\n";
            $email_order .= "=====================================\n";
            $email_order .= "\n";
            $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
            $email_order .= '▼お名前 : ' . $name . "\n";
            $email_order .= '▼メールアドレス : ' . $email . "\n";
            $email_order .= '▼ゲームタイトル : ' . $product . "\n";
            $email_order .= '▼キャラクター名 : ' . $character . "\n";
            $email_order .= '▼変更希望の日時 : ' . $datetime . "\n";
            $email_order .= "▼備考 : \n";
            $email_order .= $comment . "\n";
            $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
            $email_order .= "\n";
            $email_order .= "=====================================\n\n\n\n";
            
            $email_order .= "ご不明な点がございましたら、注文番号をご確認の上、\n";
            $email_order .= "お問い合わせください。\n\n";

            $email_order .= "[ご連絡・お問い合わせ先]━━━━━━━━━━━━\n";
            $email_order .= "株式会社 iimy\n";
            $email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
            $email_order .= HTTP_SERVER . "\n";
            //$email_order .= "〒761-0445 香川県高松市西植田町2925番地\n";
            //$email_order .= "電話番号： 087-862-1173\n";
            $email_order .= "━━━━━━━━━━━━━━━━━━━━━━━\n";

            //$email_title = str_replace(array(), array(), $email_title);
            $mail_title = "再配達確認メール【" . STORE_NAME . "】";
            //$email_order = str_replace(array('${NAME}', '${TIME}', '${CONTENT}'), array($name, date('Y-m-d H:i:s'), $email_order), $mail_content);
            
            tep_mail($name, $email, $mail_title, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');

            if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
              tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $mail_title, $email_order,'','', '');
            }
            last_customer_action();
            $email_error = false;
          }
         }
if(!isset($email_error)||$email_error == true){?>
<div class="comment">
<div class="product_info_box"><?php
echo tep_draw_form('order', tep_href_link('reorder2.php'));
?>
<input type="hidden" name="dummy" value="あいうえお眉幅">
<table class="information_table">
 <tr>
  <td bgcolor="#333333" width='120'>お名前</td>
  <td bgcolor="#333333"><input type='text'  name='name' value='<?php
  if(isset($name)&&$name){
    echo $name;
  }?>' id='new_name' class="input_text" ><span id='name_error'><?php
  if(isset($name)&&$name==''){
    echo TEXT_REORDER2_MUST_INPUT;
  }?></span></td>
 </tr>
 <tr>
  <td bgcolor="#333333">メールアドレス</td>
  <td bgcolor="#333333"><input type='text'  name='email' <?php
  if(isset($email)&&$email){
    if(preg_match('/\'/',$email)){
      echo ' value="'.$email.'" ';
    }else{
      echo ' value=\''.$email.'\' ';
    }
  }?> id='new_email' class="input_text" ><span id='email_error'></span><?php
 if(isset($email_error)&&$email_error&&$email!=''&&!tep_validate_email($email)){
   echo "<br>";
   echo "<font color='red'>入力されたメールアドレスは不正です!</font>";
 }?></td>
 </tr>
 <tr>
  <td bgcolor="#333333">ゲームタイトル</td>
  <td bgcolor="#333333"><input type='text'  name='product' value='<?php
  if(isset($product)&&$product){
    echo $product;
  }?>' id='new_product' class="input_text" ><span id='product_error'><?php
 if(isset($product)&&$product==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span></td>
 </tr>
 <tr>
  <td bgcolor="#333333">キャラクター名</td>
  <td bgcolor="#333333"><input type='text'  name='character' value='<?php
  if(isset($character)&&$character){
    echo $character;
  }?>' id='new_character' class="input_text" ><span id='character_error'><?php
 if(isset($character)&&$character==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span></td>
 </tr>
 <tr>
  <td bgcolor="#333333">取引日時</td>
  <td bgcolor="#333333">
   <select name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
<?php for($i=0;$i<7;$i++){?>
    <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo tep_date_long(time()+($i*86400));?></option>
<?php }?>
   </select>
   <select name='hour' id='new_hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
   </select>:
   <select name='minute' id='new_minute'>
    <option value=''>--</option>
   </select>
   <span id='date_error'><?php
   if($hour==''||$date==''||$minute==''){
     echo TEXT_REORDER2_TORIHIKI_ERROR;
   }?></span>
   <br >
   <font color="red">ご希望のお時間に添えない場合は、弊社より「取引時間」をご連絡させていただきます。</font>
  </td>
 </tr>
<tr>
<td bgcolor="#333333">備考</td>
<td bgcolor="#333333"><textarea name='comment' id='comment' cols="3" rows="3"><?php
if(isset($comment)&&$comment){
  echo $comment;
}?></textarea></td>
</tr>
</table>
<br>
<p align="center">
  <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="確定する" title="確定する" >
  <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="クリア" title="クリア" onClick='javascript:document.order.reset();return false;' >
</p>
</form>
<?php }?></div></div>
        <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>           
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
