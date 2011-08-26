<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  if (isset($_GET['products_id'])) {
    forward404(); 
  }
  require(DIR_WS_CLASSES. 'payment.php'); 
  $payment_modules = new payment;
  
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
  if (isset($_GET['promaji'])) {
//ccdd
    $product_info_query = tep_db_query("
        select pd.products_id, pd.products_name 
        from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        where pd.products_status != '0' and pd.products_status != '3'  
          and pd.romaji = '" .  $_GET['promaji'] . "' 
          and pd.language_id = '" . $languages_id . "' 
          and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
        order by pd.site_id DESC
        limit 1
    ");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }
  
  if (!$valid_product) {
    forward404(); 
  }
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER);

  $product_info = tep_db_fetch_array($product_info_query);
  $breadcrumb->add($product_info['products_name'] . 'を予約する');
  $po_game_c = ds_tep_get_categories($product_info['products_id'],1);
?>
<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$("input:radio").each(function(){
if($("input[name=pre_payment]").length == 1){
  $("input[name=pre_payment]").each(function(index){
      $(this).attr('checked','true');
    });
}
  if ($(this).attr("checked") == true) {
    if ($(this).attr('name') != 'bank_kamoku') { 
      if ($(this).val() == 'convenience_store') {
        $("#cemail").css("display", "block");
        $("#caemail").css("display", "block");
      } else {
        $("#cemail").css("display", "none");
        $("#caemail").css("display", "none");
      }
    }
  }
})
$("input:radio").click(function(){
  if ($(this).val() == 'convenience_store') {
    $("#cemail").css("display", "block");
    $("#caemail").css("display", "block");
  } else {
    if ($(this).attr('name') != 'bank_kamoku') { 
      $("#cemail").css("display", "none");
      $("#caemail").css("display", "none");
    } 
  }
});
$(".moduleRow").click(function(){
  if ($(this).find('input:radio').val() == 'convenience_store') {
    $("#cemail").css("display", "block");
    $("#caemail").css("display", "block");
  } else {
    if ($(this).find('input:radio').attr('name') != 'bank_kamoku') {
      $("#cemail").css("display", "none");
      $("#caemail").css("display", "none");
    }
  }
});
});
</script>
<script type="text/javascript"><!--
var selected;

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.preorder_product.pre_payment[0]) {
    document.preorder_product.pre_payment[buttonSelect].checked=true;
  } else {
    document.preorder_product.pre_payment.checked=true;
  }
}

function rowOverEffect(object) {
  //if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  //if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
//--></script>
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
        <?php echo STORE_NAME;?>では、<?php echo $po_game_c; ?>の予約サービスを行っております。<br> ご希望する数量が弊社在庫にある場合は「<?php echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '">' . $product_info['products_name']; ?></a>」をクリックしてお手続きください。
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
if (!isset($_POST['firstname'])) $_POST['firstname'] = NULL; //del notice
if (!isset($_POST['lastname'])) $_POST['lastname'] = NULL; //del notice
if (!isset($_POST['from'])) $_POST['from'] = NULL; //del notice
      $first_name = $_POST['firstname'];
      $last_name = $_POST['lastname'];
      $from_name = tep_get_fullname($_POST['firstname'], $_POST['lastname']); 
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
    
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($last_name)) {
        $lastname_error = true;
        $error = true;
      } else {
        $lasttname_error = false;
      }
      
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($first_name)) {
        $firstname_error = true;
        $error = true;
      } else {
        $firstname_error = false;
      }
    } 
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['predate'])) {
      $predate_error = true;
      $error = true;
    } else {
      $predate_error = false;
    }
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['pre_payment'])) {
      $payment_error = true;
      $error = true;
    } else {
      $payment_error = false;
    }
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $preorder_id = date('Ymd').'-'.date('His').tep_get_preorder_end_num(); 
      if (tep_session_is_registered('customer_id')) {
          $preorder_email_text = PREORDER_MAIL_CONTENT; 
          $preorder_email_subject = PREORDER_MAIL_SUBJECT; 
           tep_mail(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), $account_values['customers_email_address'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
      } else {
        $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".$_POST['from']."' and site_id = '".SITE_ID."'");    
        if (tep_db_num_rows($exists_customer_raw)) {
          $exists_customer_res = tep_db_fetch_array($exists_customer_raw); 
          $preorder_email_text = PREORDER_MAIL_CONTENT; 
          $preorder_email_subject = PREORDER_MAIL_SUBJECT; 
          $exists_email_single = true;     
        } else {
          $tmp_customer_id = tep_create_tmp_guest($_POST['from'], $_POST['lastname'], $_POST['firstname']); 
          $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$preorder_id; 
          $preorder_email_text = str_replace('${URL}', $active_url, PREORDER_MAIL_ACTIVE_CONTENT); 
          $preorder_email_subject = PREORDER_MAIL_ACTIVE_SUBJECT; 
        }
        tep_mail($from_name, $_POST['from'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
      }
      
      $send_preorder_id = $preorder_id;
      tep_session_register('send_preorder_id');
      if (isset($exists_email_single)) {
        tep_create_preorder_info($_POST, $preorder_id, $exists_customer_res['customers_id'], $tmp_customer_id, true); 
      } else {
        tep_create_preorder_info($_POST, $preorder_id, $customer_id, $tmp_customer_id); 
      }
      tep_redirect(tep_href_link(FILENAME_PREORDER_SUCCESS));
?>
      <div>
        <?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $from_email_address, stripslashes($_POST['products_name']), $_POST['quantity'], $_POST['timelimit']); ?>
        <div align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
      </div>
<?php
    } else {
      if (tep_session_is_registered('customer_id')) {
        $last_name_prompt = $account_values['customers_lastname'];
        $first_name_prompt = $account_values['customers_firstname'];
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
if (!isset($_POST['lastname'])) $_POST['lastname'] = NULL; //del notice
if (!isset($_POST['firstname'])) $_POST['firstname'] = NULL; //del notice
if (!isset($_GET['lastname'])) $_GET['lastname'] = NULL; //del notice
if (!isset($_GET['firstname'])) $_GET['firstname'] = NULL; //del notice
        $last_name_prompt = tep_draw_input_field('lastname', (($lastname_error == true) ? $_POST['lastname'] : $_GET['lastname']), 'class="input_text"');
        $first_name_prompt = tep_draw_input_field('firstname', (($firstname_error == true) ? $_POST['firstname'] : $_GET['firstname']), 'class="input_text"');
        if ($lastname_error == true) $last_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
        if ($firstname_error == true) $first_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
if (!isset($_GET['from'])) $_GET['from'] = NULL; //del notice
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']) , 'size="30" class="input_text"') . '&nbsp;&nbsp;携帯電話メールアドレス推奨';
        if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?>
      <?php echo tep_draw_form('preorder_product', tep_preorder_href_link($_GET['promaji'], 'action=process')) .  tep_draw_hidden_field('products_id', $product_info['products_id']).tep_draw_hidden_field('products_name', $product_info['products_name']); ?>

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
          <td class="main"><?php echo FORM_FIELD_CUSTOMER_LASTNAME; ?></td>
          <td class="main"><?php echo $last_name_prompt; ?></td>
        </tr>
        <tr>  
          <td class="main"><?php echo FORM_FIELD_CUSTOMER_FIRSTNAME; ?></td>
          <td class="main"><?php echo $first_name_prompt; ?></td>
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
          <td class="main"><strong><?php echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '" target="_blank">' . $po_game_c . '&nbsp;/&nbsp;' . $product_info['products_name']; ?></a></strong></td>
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
      if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL; //del notice
?>
          </td>
        </tr>
        <?php
        if (false) { 
        ?>
        <tr>
          <td class="main"><?php echo FORM_FIELD_PREORDER_FIXTIME; ?></td>
          <td class="main">
<?php
//echo tep_get_torihiki_select_by_pre_products($product_info['products_id']);
?>
          </td>
        </tr>
        <?php }?> 
        <tr>
          <td class="main"><?php echo FORM_FIELD_PREORDER_FIXDAY; ?></td>
          <td class="main">
<?php
    $today = getdate();
      $m_num = $today['mon'];
      $d_num = $today['mday'];
      $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="predate">
    <option value=""><?php echo PREORDER_SELECT_EMPTY_OPTION;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(PREORDER_MONDAY_TEXT, PREORDER_TUESDAY_TEXT, PREORDER_WENSDAY_TEXT, PREORDER_THIRSDAY_TEXT, PREORDER_FRIDAY_TEXT, PREORDER_STATURDAY_TEXT, PREORDER_SUNDAY_TEXT);
    for($i=0; $i<7; $i++) {
      if ($_POST['predate'] == date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year))) {
        $check_str = 'selected'; 
      } else {
        $check_str = ''; 
      }
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'" '.$check_str.'>'.str_replace($oarr, $newarr,date("Y".PREORDER_YEAR_TEXT."m".PREORDER_MONTH_TEXT."d".PREORDER_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
    }
    ?>
  </select>
          <?php
          if ($predate_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
          ?>
          </td>
        </tr>
      </table>
      <br> 
      <?php
      $selection = $payment_modules->selection(); 
    if (sizeof($selection) > 1) { 
      ?>
      <h3 class="formAreaTitle"><?php echo FORM_FIELD_PREORDER_PAYMENT; ?></h3>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
          <?php
          if ($payment_error == true) echo '<tr><td style="font-size:11px;"><span class="errorText">' .  TEXT_REQUIRED . '</span></td></tr>';
          ?>
        <?php
        $radio_buttons = 0; 
        for ($i=0, $n=sizeof($selection); $i<$n; $i++) { 
          if ($selection[$i]['id'] == 'buying' || $selection[$i]['id'] == 'buyingpoint' || $selection[$i]['id'] == 'fetchgood' || $selection[$i]['id'] == 'freepayment') {
            continue; 
          }
          if ($selection[$i]['id'] == 'moneyorder' && MODULE_PAYMENT_MONEYORDER_PREORDER_SHOW == 'False') {
            continue; 
          }
          if ($selection[$i]['id'] == 'postalmoneyorder' && MODULE_PAYMENT_POSTALMONEYORDER_PREORDER_SHOW == 'False') {
            continue; 
          }
          if ($selection[$i]['id'] == 'convenience_store' && MODULE_PAYMENT_CONVENIENCE_STORE_PREORDER_SHOW == 'False') {
            continue; 
          }
          if ($selection[$i]['id'] == 'telecom' && MODULE_PAYMENT_TELECOM_PREORDER_SHOW == 'False') {
            continue; 
          }
          if ($selection[$i]['id'] == 'paypal' && MODULE_PAYMENT_PAYPAL_PREORDER_SHOW == 'False') {
            continue; 
          }
        ?>
        <tr>
          <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" class="box_des02">
          <?php
          if ($n == 1) {
            echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          } else {
            echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
          }
          ?>
                              <td width="10"></td> 
                                <td class="main" colspan="3"><b><?php echo $selection[$i]['module']; ?></b></td> 
                                <td class="main" align="right"><?php
    if (sizeof($selection) > 1) {
      echo tep_draw_radio_field('pre_payment', $selection[$i]['id']);
    } else {
      echo tep_draw_hidden_field('pre_payment', $selection[$i]['id']);
    }
?> </td> 
                                <td width="10"></td> 
                              </tr> <?php
    if (isset($selection[$i]['error'])) {
?> 
                              <tr> 
                                <td width="10"></td> 
                                <td class="main" colspan="4"><?php echo $selection[$i]['error']; ?></td> 
                                <td width="10"></td> 
                              </tr> 
                              <?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?> 
                              <tr> 
                                <td width="10"></td> 
                                <td colspan="4"><table border="0" cellspacing="0" cellpadding="2"> 
                                    <?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?> 
                                    <tr> 
                                      <td width="10"></td> 
                                      <td class="main"><?php echo $selection[$i]['fields'][$j]['title']; ?></td> 
                                      <td></td> 
                                      <td class="main"><?php echo $selection[$i]['fields'][$j]['field']; ?></td> 
                                      <td width="10"></td> 
                                    </tr> 
                                    <?php
      }
?> 
                                  </table></td> 
                                <td width="10"></td> 
                              </tr> 
                              <?php
    }
?> 
            </table>
          </td> 
        </tr>
        <?php 
        $radio_buttons++; 
        }
        ?> 
      </table> 
      <?php }?> 
      <br>
      <h3 class="formAreaTitle"><?php echo $po_game_c.PREORDER_EXPECT_CTITLE; ?></h3>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
        <tr><td class="main"><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td></tr>
      </table>
      <br>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="main">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
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
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
