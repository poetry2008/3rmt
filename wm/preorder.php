<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (tep_session_is_registered('customer_id')) {
//ccdd
    $account = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_email_address 
        from " .  TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "' 
          and site_id = '".SITE_ID."'
    ");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
//ccdd
    $product_info_query = tep_db_query("
        select pd.products_name 
        from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        where p.products_status != '0' 
          and p.products_id = '" .  (int)$_GET['products_id'] . "' 
          and p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
        order by pd.site_id DESC
        limit 1
    ");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER);

  $product_info = tep_db_fetch_array($product_info_query);
  $breadcrumb->add($product_info['products_name'] . 'を予約する', tep_href_link(FILENAME_PREORDER, 'products_id=' . intval($_GET['products_id'])));
  $po_game_c = ds_tep_get_categories((int)$_GET['products_id'],1);
?>
<?php page_head();?>
</head>
<body>
<div align="center">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
      <!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
    </td>
    <!-- body_text //-->
    <td valign="top" id="contents">
<?php
  if ($valid_product == false) {
?>
      <p class="main">
        <?php echo HEADING_TITLE_ERROR; ?><br><?php echo ERROR_INVALID_PRODUCT; ?>
      </p>
<?php
  } else {
    //$product_info = tep_db_fetch_array($product_info_query);
?>
      <h1 class="pageHeading"><?php echo $po_game_c . '&nbsp;' . $product_info['products_name']; ?>を予約する</h1>
            <div class="comment">
      <p>
        RMTワールドマネーでは、<?php echo $po_game_c; ?>の予約サービスを行っております。<br>
        ご希望する数量が弊社在庫にある場合は「<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '" target="_blank">' . $product_info['products_name']; ?></a>」をクリックしてお手続きください。
      </p>
<?php
    $error = false;
  
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['quantity'])) {
      $quantity_error = true;
      $error = true;
    } else {
      $quantity_error = false;
    }
    
    if (tep_session_is_registered('customer_id')) {
      $from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
      $from_email_address = $account_values['customers_email_address'];
    } else {
if (!isset($_POST['yourname'])) $_POST['yourname'] = NULL; //del notice
if (!isset($_POST['from'])) $_POST['from'] = NULL; //del notice
      $from_name = $_POST['yourname'];
      $from_email_address = $_POST['from'];
    }
    
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
        $fromemail_error = true;
        $error = true;
        } else {
          $fromemail_error = false;
        }
      }
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($from_name)) {
      $fromname_error = true;
      $error = true;
    } else {
      $fromname_error = false;
    }
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $product_info['products_name'], STORE_NAME);
      $email_body = sprintf(TEXT_EMAIL_INTRO, $from_name, STORE_NAME, $from_name, $from_email_address, $_POST['products_name'], $_POST['quantity'], $_POST['timelimit'], STORE_NAME) . "\n\n";
    
      if (tep_not_null($_POST['yourmessage'])) {
        $email_body .= '▼ご要望' . "\n" . $_POST['yourmessage'] . "\n\n";
      }
    
      $email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id']))) . "\n\n" .
      sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");
    
      tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $email_subject, stripslashes($email_body), $from_name, $from_email_address);
      tep_mail('', $from_email_address, $email_subject, stripslashes($email_body), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
?>
      <div>
        <?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $from_email_address, stripslashes($_POST['products_name']), $_POST['quantity'], $_POST['timelimit']); ?>
        <div align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
      </div>
<?php
    } else {
      if (tep_session_is_registered('customer_id')) {
        $your_name_prompt = tep_output_string_protected(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
if (!isset($_POST['yourname'])) $_POST['yourname'] = NULL; //del notice
if (!isset($_GET['yourname'])) $_GET['yourname'] = NULL; //del notice
        $your_name_prompt = tep_draw_input_field('yourname', (($fromname_error == true) ? $_POST['yourname'] : $_GET['yourname']), 'class="input_text"');
        if ($fromname_error == true) $your_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
if (!isset($_GET['from'])) $_GET['from'] = NULL; //del notice
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']) , 'size="30" class="input_text"') . '&nbsp;&nbsp;携帯電話メールアドレス推奨';
        if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?>
      <?php echo tep_draw_form('email_friend', tep_href_link(FILENAME_PREORDER, 'action=process&products_id=' . intval($_GET['products_id']))) . tep_draw_hidden_field('products_name', $product_info['products_name']); ?>

      <p>
        弊社在庫にお客様がご希望する数量がない場合は、下記の必要事項をご入力の上お申し込みください。<br>
        予約手続きが完了いたしますと、入荷次第、お客様へ優先的にご案内いたします。
      </p>
      <p class="red"><b>ご予約・お見積りは無料ですので、お気軽にお問い合わせください。</b></p>
<?php
      if($error == true) {
        echo '<span class="errorText"><b>入力した内容に誤りがございます。正しく入力してください。</span></b><br><br>';
      }
?>
      <h3 class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></h3>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>  
          <td class="main"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td>
          <td class="main"><?php echo $your_name_prompt; ?></td>
        </tr>
        <tr>
          <td class="main"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
          <td class="main"><?php echo $your_email_address_prompt; ?></td>
        </tr>
        <tr> 
          <td colspan="2" class="main">お取り置き期限がございます。いつも使用しているメールアドレスをご入力ください。</td>
        </tr>
      </table><br>
      <h3 class="formAreaTitle"><?php echo FORM_TITLE_FRIEND_DETAILS; ?></h3>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>
          <td class="main" valign="top">商品名:</td>
          <td class="main"><strong><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '" target="_blank">' . $po_game_c . '&nbsp;/&nbsp;' . $product_info['products_name']; ?></a></strong></td>
        </tr>
        <tr>
          <td class="main"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
          <td class="main">
<?php
if (!isset($_POST['quantity'])) $_POST['quantity'] = NULL; //del notice
if (!isset($_GET['quantity'])) $_GET['quantity'] = NULL; //del notice
            echo tep_draw_input_field('quantity', (($quantity_error == true) ? $_POST['quantity'] : $_GET['quantity']) , 'size="7" maxlength="15" class="input_text_short"');
            echo '&nbsp;&nbsp;個';
      if ($quantity_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
?>
          </td>
        </tr>
        <tr>
          <td class="main"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td>
          <td class="main">
<?php
if (!isset($timelimit_error)) $timelimit_error = NULL; //del notice
if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL; //del notice
            echo tep_draw_input_field('timelimit', (($timelimit_error == true) ? $_POST['timelimit'] : $_GET['send_to']) , 'size="30" maxlength="50" class="input_text"');
            echo '&nbsp;&nbsp;(例.&nbsp;20日までに届けて欲しい。)';
?>
          </td>
        </tr>
      </table>
      <br>
      <h3 class="formAreaTitle"><?php echo $po_game_c; ?>についてのご要望</h3>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
        <tr><td class="main"><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td></tr>
      </table>
      <br>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="main">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
          </td>
          <td align="right" class="main">
            <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
          </td>
        </tr>
      </table>
    </form>
<?php
    }
  }
?>
    </div>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
