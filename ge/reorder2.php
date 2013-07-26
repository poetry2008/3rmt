<?php
/*
 $Id$
*/
require('includes/application_top.php');

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
  <!-- header_eof -->
  <!-- body -->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- body_text -->
  <div id="content">
    <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; ');?></div>
    <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
    <?php if($_POST){
          $date     = tep_db_prepare_input($_POST['date']);
          $hour     = tep_db_prepare_input($_POST['hour']);
          $minute   = tep_db_prepare_input($_POST['minute']);

          $name      = tep_db_prepare_input($_POST['name']);
          $product   = tep_db_prepare_input($_POST['product']);
          $comment   = tep_db_prepare_input($_POST['comment']);
          $email = tep_db_prepare_input($_POST['email']);
          $email = str_replace("\xe2\x80\x8b", '',$email);

          $datetime = $date.' '.$hour.':'.$minute;
          $time     = strtotime($datetime);
          if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
            // time error
            echo '<div><div class="comment">お届け時間は前もって一時間以上に設定してください <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt=""></a></div></div>';
            $email_error = false;
          } else if($name==''||$date==''||$minute==''||$hour==''|| $product==''){
            $email_error = true;
          } else if(!tep_validate_email($email)){
            $email_error = true;
          } else {
            echo '<div><div class="comment" style="width:95%; margin-top:10px;">注文内容の変更を承りました。電子メールをご確認ください。 <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" alt="TOPに戻る" title="TOPに戻る"></a></div></div>';

            $email_order = '';
            $mail_info = tep_get_mail_templates('REORDER_MAIL_CONTENT_TWO', 0);
            $mail_title = $mail_info['title'];  
            $mail_content = $mail_info['contents']; 
            
            $user_info = SENDMAIL_TEXT_IP_ADDRESS.$_SERVER['REMOTE_ADDR']."\n";
            $user_info .= SENDMAIL_TEXT_HOST.@gethostbyaddr($_SERVER['REMOTE_ADDR'])."\n"; 
            $user_info .= SENDMAIL_TEXT_USER_AGENT.$_SERVER['HTTP_USER_AGENT']."\n"; 
            $admin_user_info = tep_get_admin_user_info(); 
            
            $replace_array = array(
                '${SITE_NAME}', 
                '${SITE_URL}', 
                '${COMPANY_NAME}', 
                '${COMPANY_ADDRESS}', 
                '${COMPANY_TEL}', 
                '${SUPPORT_EMAIL}', 
                '${STAFF_MAIL}', 
                '${STAFF_NAME}', 
                '${SIGNATURE}', 
                '${USER_NAME}', 
                '${USER_MAIL}', 
                '${USER_INFO}', 
                '${YEAR}', 
                '${MONTH}', 
                '${DAY}', 
                '${PRODUCTS_NAME}', 
                '${ORDER_COMMENT}', 
                '${CHANGE_TIME}', 
                ); 
            $new_replace_array = array(
                STORE_NAME,
                HTTP_SERVER,
                COMPANY_NAME,
                STORE_NAME_ADDRESS,
                STORE_NAME_TEL,
                SUPPORT_EMAIL_ADDRESS,
                $admin_user_info[0],
                $admin_user_info[1],
                C_EMAIL_FOOTER,
                $name,
                $email,
                $user_info,
                date('Y'),
                date('m'),
                date('d'),
                $product,
                $comment,
                $datetime 
                ); 
            $mail_title = str_replace($replace_array, $new_replace_array ,$mail_title);
            $mail_content = str_replace($replace_array, $new_replace_array ,$mail_content);
            $email_order = $mail_content; 
            
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
        <table class="size_body">
          <tr>
            <td width='120'>お名前</td>
            <td>
              <input type='text'  name='name' value='<?php
  if(isset($name)&&$name){
    echo $name;
  }?>' id='new_name' class="input_text" >
              <span id='name_error'><?php
  if(isset($name)&&$name==''){
    echo TEXT_REORDER2_MUST_INPUT;
  }?></span></td>
          </tr>
          <tr>
            <td width='120'>メールアドレス</td>
            <td>
              <input type='text'  name='email' <?php
  if(isset($email)&&$email){
    if(preg_match('/\'/',$email)){
      echo ' value="'.$email.'" ';
    }else{
      echo ' value=\''.$email.'\' ';
    }
  }?> id='new_email' class="input_text" ><span id='email_error'>&nbsp;<?php 
 if(isset($email)&&$email==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span><?php
 if(isset($email_error)&&$email_error&&$email!=''&&!tep_validate_email($email)){
   echo "<br>";
   echo "<font color='red'>入力されたメールアドレスは不正です!</font>";
 }?></td>
          </tr>
          <tr>
            <td width='120'>ゲームタイトル</td>
            <td>
              <input type='text'  name='product' value='<?php
  if(isset($product)&&$product){
    echo $product;
  }?>' id='new_product' class="input_text" ><span id='product_error'>&nbsp;<?php
 if(isset($product)&&$product==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span></td>
 </tr>
 <tr>
  <td width='120' valign="top">お届け日時</td>
  <td>
   <select class='margin_clear' name='date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
<?php for($i=0;$i<7;$i++){?>
    <option value='<?php echo date('Y-m-d', time()+($i*86400));?>'><?php echo tep_date_long(time()+($i*86400));?></option>
<?php }?>
   </select>
   <select name='hour' onChange="selectHour('<?php echo date('H');?>', '<?php echo date('i');?>')">
    <option value=''>--</option>
   </select>:
   <select name='minute'>
    <option value=''>--</option>
   </select>
   <div><span id='date_error'><?php
   if($hour==''||$date==''||$minute==''){
     echo TEXT_REORDER2_TORIHIKI_ERROR;
   }?></span>
              <font color="red">ご希望のお時間に添えない場合は、弊社より「お届け時間」をご連絡させていただきます。</font></div>
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
        <table width="100%">
<td width="120">&nbsp;</td>
        <td>
          <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="確定" title="確定" >
          <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="クリア" title="クリア" onclick='javascript:document.order.reset();return false;' >
        </td>
</table>
      </form>
      <?php }?>
    </div>
    <p class="pageBottom"></p>
  </div>
  <!-- body_text_eof -->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
    <!-- right_navigation_eof -->
  </div>
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
