<?php
/*
 $Id$
*/
require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . 'reorder2.php');

$breadcrumb->add(TEXT_BREADCRUMB_TITLE, tep_href_link('reorder2.php'));
?>
<?php page_head();?>
<script src='./js/order.js'></script>
</head>
<body>
<div id="main">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
    <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  <!-- body_text //-->
  <div class="yui3-u" id="layout">
    <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> ');?></div>
	 <?php include('includes/search_include.php');?>
	 <div id="main-content">
    <h2><?php echo HEADING_TITLE; ?></h2>
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
            echo '<div><div class="comment">'.TEXT_INFO_FOR_TRADE.' <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" alt=""></a></div></div>';
            $email_error = false;
          } else if($name==''||$date==''||$minute==''||$hour==''|| $product==''){
            $email_error = true;
          } else if(!tep_validate_email($email)){
            $email_error = true;
          } else {
            echo '<div><div class="comment"
              style="width:100%; margin-top:15px; padding-left:6px;">'.TEXT_CHANGE_ORDER_CONFIRM_EMAIL.'
              <div align="right" class="botton-continue"><a href="/"><img
              src="includes/languages/japanese/images/buttons/button_back_home.gif"
               onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back_home.gif\'"
               onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_home_hover.gif\'"
               alt="'.TEXT_TOP_CON.'" title="'.TEXT_TOP_CON.'"></a></div></div>';

            $email_order = '';
            $email_order .= $name . TEXT_REORDER_LIKE."\n";
            $email_order .= "\n";
            $email_order .= TEXT_REORDER_THANK_TO_CONTACT."\n";
            $email_order .= TEXT_REORDER_RE_DELIVERY."\n";
            $email_order .= "\n";
            $email_order .= "=====================================\n";
            $email_order .= "\n";
            $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
            $email_order .= TEXT_REORDER_NAME_EMAIL . $name . "\n";
            $email_order .= TEXT_REORDER_EMAIL_EMAIL . $email . "\n";
            $email_order .= TEXT_REORDER_PRODUCT_EMAIL. $product . "\n";
            $email_order .= TEXT_REORDER_DATETIME_EMAIL. $datetime . "\n";
            $email_order .= TEXT_REORDER_COMMENT_TITLE_EMAIL. "\n";
            $email_order .= $comment . "\n";
            $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
            $email_order .= "\n";
            $email_order .= "=====================================\n\n\n\n";
            
            $email_order .= TEXT_REORDER_INFO1_EMAIL."\n";
            $email_order .= TEXT_REORDER_INFO2_EMAIL."\n\n";

            $email_order .= TEXT_REORDER_INFO3_EMAIL."━━━━━━━━━━━━\n";
            $email_order .= TEXT_REORDER_INFO4_EMAIL."\n";
            $email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
            $email_order .= HTTP_SERVER . "\n";
            $email_order .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            
            //$email_title = str_replace(array(), array(), $email_title);
            $mail_title = TEXT_REORDER_TITLE_EMAIL;
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
        <table class="information_table" width="100%">
          <tr>
            <td width="20%"><?php echo TEXT_REORDER_OID_NAME;?></td>
            <td>
              <input type='text'  name='name' value='<?php
  if(isset($name)&&$name){
    echo $name;
  }?>' id='new_name' class="input_text" style="width:42.5%; margin-bottom:5px;">
              <span id='name_error'><?php
  if(isset($name)&&$name==''){
    echo TEXT_REORDER2_MUST_INPUT;
  }?></span></td>
          </tr>
          <tr>
            <td><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
            <td>
              <input type='text'  name='email' <?php
  if(isset($email)&&$email){
    if(preg_match('/\'/',$email)){
      echo ' value="'.$email.'" ';
    }else{
      echo ' value=\''.$email.'\' ';
    }
  }?> id='new_email' class="input_text" style="width:42.5%;
          margin-bottom:5px;"><span id='email_error'>&nbsp;<?php 
 if(isset($email)&&$email==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span><?php
 if(isset($email_error)&&$email_error&&$email!=''&&!tep_validate_email($email)){
   echo "<br>";
   echo "<font color='red'>".TEXT_REORDER_EMAIL_ERROR."</font>";
 }?></td>

          </tr>
          <tr>
            <td><?php echo TEXT_REORDER_GAME_TITLE?></td>
            <td>
              <input type='text'  name='product' value='<?php
  if(isset($product)&&$product){
    echo $product;
  }?>' id='new_product' class="input_text" style="width:42.5%;
          margin-bottom:5px;"><span id='product_error'>&nbsp;<?php
 if(isset($product)&&$product==''){
   echo TEXT_REORDER2_MUST_INPUT;
 }?></span></td>
          </tr>
          <tr>
            <td valign="top"><?php echo TEXT_REORDER_TRADE_DATE;?></td>
            <td>
              <select class="margin_clear" name='date' id='new_date' onChange="selectDate('<?php echo date('H');?>', '<?php echo date('i');?>')">
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
              <span class='date_error'><?php
   if($hour==''||$date==''||$minute==''){
     echo TEXT_REORDER2_TRADE_TIME_ERROR;
   }?></span>
              <br >
              <font color="red"><?php echo TEXT_REORDER_TREADE_TEXT;?></font>
            </td>
          </tr>
          <tr>
            <td valign="top"><?php echo TEXT_REORDER_COMMENT_TITLE;?></td>
            <td>
              <textarea name='comment' id='comment' style="width:80%;" rows="10"><?php
              if(isset($comment)&&$comment){
                  echo $comment;
              }?></textarea>
            </td>
          </tr>
        </table>
        <div align="center" class="botton-continue">
          <input type='image'
          src="includes/languages/japanese/images/buttons/button_submit2.gif"
            onmouseout="this.src='includes/languages/japanese/images/buttons/button_submit2.gif'"
            onmouseover="this.src='includes/languages/japanese/images/buttons/button_submit2_hover.gif'"
            alt="<?php echo TEXT_REORDER_CONFIRE;?>" title="<?php echo
            TEXT_REORDER_CONFIRE;?>" onclick='return check()' >
          <input type='image'
            onmouseout="this.src='includes/languages/japanese/images/buttons/button_reset.gif'"
            onmouseover="this.src='includes/languages/japanese/images/buttons/button_reset_hover.gif'"
            src="includes/languages/japanese/images/buttons/button_reset.gif"
            alt="<?php echo TEXT_REORDER_CLEAR;?>" title="<?php echo
            TEXT_REORDER_CLEAR;?>" onclick='javascript:document.order.reset();return false;' >
        </div>
      </form>
      <?php }?>
    </div>
    <p class="pageBottom"></p>
  </div>
	</div>
	      <?php include('includes/float-box.php');?>
		  </div>
  <!-- body_text_eof //-->
    <?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
