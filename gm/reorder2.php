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
<script src='./js/order.js'></script>
</head>
<body>
<div id="main">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- body_text //-->
  <div id="content">
    <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; ');?></div>
    <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
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
            echo '<div><div class="comment">取引時間は前もって一時間以上に設定してください <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt=""></a></div></div>';
            $email_error = false;
          } else if(!tep_validate_email($email)){
            $email_error = true;
          } else {
            echo '<div><div class="comment" style="width:95%">注文内容の変更を承りました。電子メールをご確認ください。 <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="TOPに戻る" title="TOPに戻る"></a></div></div>';

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
      <form action="reorder2.php" method="post" name="order">
        <input type="hidden" name="dummy" value="あいうえお眉幅">
        <table class="information_table">
          <tr>
            <td width='120'>お名前</td>
            <td>
              <input type='text'  name='name' value='<?php
  if(isset($name)&&$name){
    echo $name;
  }?>' id='new_name' class="input_text" >
              <span id='name_error'></span></td>
          </tr>
          <tr>
            <td>メールアドレス</td>
            <td>
              <input type='text'  name='email' value='<?php
  if(isset($email)&&$email){
    echo $email;
  }?>' id='new_email' class="input_text" >
              <span id='email_error'></span><?php
 if(isset($email_error)&&$email_error){
   echo "<br>";
   echo "<font color='red'>入力されたメールアドレスは不正です!</font>";
 }?></td>
          </tr>
          <tr>
            <td>ゲームタイトル</td>
            <td>
              <input type='text'  name='product' value='<?php
  if(isset($product)&&$product){
    echo $product;
  }?>' id='new_product' class="input_text" >
              <span id='product_error'></span></td>
          </tr>
          <tr>
            <td>キャラクター名</td>
            <td>
              <input type='text'  name='character' value='<?php
  if(isset($character)&&$character){
    echo $character;
  }?>' id='new_character' class="input_text" >
              <span id='character_error'></span></td>
          </tr>
          <tr>
            <td>取引日時</td>
            <td>
              <select name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
                <?php for($i=0;$i<7;$i++){?>
                <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo tep_date_long(time()+($i*86400));?></option>
                <?php }?>
              </select>
              <select name='hour' id='new_hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
                <option value=''>--</option>
              </select>
              :
              <select name='minute' id='new_minute'>
                <option value=''>--</option>
              </select>
              <span id='date_error'></span>
              <br >
              <font color="red">ご希望のお時間に添えない場合は、弊社より「取引時間」をご連絡させていただきます。</font>
            </td>
          </tr>
          <tr>
            <td>備考</td>
            <td>
              <textarea name='comment' id='comment'><?php
              if(isset($comment)&&$comment){
                  echo $comment;
              }?></textarea>
            </td>
          </tr>
        </table>
        <br>
        <p align="center">
          <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="確定する" title="確定する" onclick='return check()' >
          <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="クリア" title="クリア" onclick='javascript:document.order.reset();return false;' >
        </p>
      </form>
      <?php }?>
    </div>
    <script type="text/javascript">
function check(){
  commit = true;
  document.getElementById('name_error').innerHTML = '';
  document.getElementById('date_error').innerHTML = '';
  document.getElementById('product_error').innerHTML = '';
  //document.getElementById('comment_error').innerHTML = '';
  document.getElementById('email_error').innerHTML = '';
  document.getElementById('character_error').innerHTML = '';
  
  if((document.getElementById('new_date').selectedIndex != 0 || document.getElementById('new_hour').selectedIndex != 0 || document.getElementById('new_minute').selectedIndex != 0) && !(document.getElementById('new_date').selectedIndex != 0 && document.getElementById('new_hour').selectedIndex != 0 && document.getElementById('new_minute').selectedIndex != 0)){
    document.getElementById('date_error').innerHTML = "<br><font color='red'>【取引日時】を選択してください。</font>";
    commit = false;
  }
  if(document.getElementById('new_date').selectedIndex == 0 && document.getElementById('new_hour').selectedIndex == 0 && document.getElementById('new_minute').selectedIndex == 0){
    document.getElementById('date_error').innerHTML = "<font color='red'>必須項目</font>";
    commit = false;
  }
  if(document.getElementById('new_name').value == ''){
    document.getElementById('name_error').innerHTML = "<font color='red'>必須項目</font>";
    commit = false;
  }
  if(document.getElementById('new_email').value == ''){
    document.getElementById('email_error').innerHTML = "<font color='red'>必須項目</font>";
    commit = false;
  }else{
    if(!/(\S)+[@]{1}(\S)+[.]{1}(\w)+/.test(document.getElementById('new_email').value)){
      document.getElementById('email_error').innerHTML = "<br><font color='red'>メールアドレスを正しくご入力ください</font>";
      commit = false;
    }
  }
  if(document.getElementById('new_product').value == ''){
    document.getElementById('product_error').innerHTML = "<font color='red'>必須項目</font>";
    commit = false;
  }
  if(document.getElementById('new_character').value == ''){
    document.getElementById('character_error').innerHTML = "<font color='red'>必須項目</font>";
    commit = false;
  }
  if (commit){
    return true;
  } else {
    return false
  }
}
</script>
    <p class="pageBottom"></p>
  </div>
  <!-- body_text_eof //-->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
    <!-- right_navigation_eof //-->
  </div>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
