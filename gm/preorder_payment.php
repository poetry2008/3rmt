<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  if (!isset($_POST['products_id'])) {
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
  
  $products_id = $_POST['products_id']; 
  
  $product_info_query = tep_db_query("
      select pd.products_id, pd.products_name, pd.products_status, pd.romaji, pd.preorder_status 
      from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
      where pd.products_id = '" .  $products_id . "' 
        and pd.language_id = '" . $languages_id . "' 
        and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
      order by pd.site_id DESC
      limit 1
  ");
  $valid_product = (tep_db_num_rows($product_info_query) > 0);
  
  if (!$valid_product) {
    forward404(); 
  }
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER_PAYMENT);
  $product_info = tep_db_fetch_array($product_info_query);
 
  if ($product_info['preorder_status'] != '1') {
    forward404(); 
  }
  $ca_path = tep_get_product_path($product_info['products_id']);
  if (tep_not_null($ca_path)) {
    $ca_path_array = tep_parse_category_path($ca_path); 
  }
  if (isset($ca_path_array)) {
    for ($cnum = 0, $ctnum=sizeof($ca_path_array); $cnum<$ctnum; $cnum++) {
      $categories_query = tep_db_query("
          select categories_name 
          from " .  TABLE_CATEGORIES_DESCRIPTION . " 
          where categories_id = '" .  $ca_path_array[$cnum] . "' 
            and language_id='" . $languages_id . "' 
            and (site_id = ".SITE_ID." or site_id = 0)
          order by site_id DESC
          limit 1" 
      );
      if (tep_db_num_rows($categories_query) > 0) {
        $categories_info = tep_db_fetch_array($categories_query); $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
      } else {
        break;
      }
    }
  }
  
  $breadcrumb->add($product_info['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$product_info['products_id']));
  $breadcrumb->add(sprintf(HEADING_TITLE, $product_info['products_name']));
  $po_game_c = ds_tep_get_categories($product_info['products_id'],1);
?>
<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function triggerHide(radio)
{
 if ($(radio).attr("checked") == true) {
      $(".rowHide").hide();
      $(".rowHide").find("input").attr("disabled","true");
      $(".rowHide_"+$(radio).val()).show();
      $(".rowHide_"+$(radio).val()).find("input").removeAttr("disabled");
     }
}
$(document).ready(function(){
    if($("input[name=pre_payment]").length == 1){
      $("input[name=pre_payment]").each(function(index){
	  $(this).attr('checked','true');
	});
    }
    $("input[name=pre_payment]").click(function(index){
	  triggerHide(this);
    });
    $("input[name=pre_payment]").each(function(index){
	if ($(this).attr('checked') == true) {
	  triggerHide(this);
	}
      });
    $(".moduleRow").click(function(){
	triggerHide($(this).find("input:radio")[0]);
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
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
    <div id="l_menu">
      <!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
    </div>
    <!-- body_text //-->
    <div id="content">
<?php
  if ($valid_product == false) {
?>
      <p class="main">
        <?php echo HEADING_TITLE_ERROR; ?><br><?php echo ERROR_INVALID_PRODUCT; ?>
      </p>
<?php
  } else {
?>
      <h1 class="pageHeading"><?php echo $po_game_c . '&nbsp;' . $product_info['products_name']; ?>を予約する</h1>
            <div class="comment_preoder">
      <p>
        <?php echo STORE_NAME;?>では、<?php echo $po_game_c; ?>の予約サービスを行っております。<br> ご希望する数量が弊社在庫にある場合は「
        <?php 
        if ($product_info['products_status'] == 0 || $product_info['products_status'] == 3)  {
          echo $product_info['products_name']; 
        } else {
          echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $product_info['products_id']) . '">' .  $product_info['products_name'].'</a>';
        }
        ?>
        」をクリックしてお手続きください。
      </p>
<?php
    $error = false;
  
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['quantity'])) {
      $quantity_error = true;
      $error = true;
    } else {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !is_numeric(tep_an_zen_to_han($_POST['quantity']))) {
        $quantity_error = true;
        $error = true;
      } else {
       if (isset($_GET['action']) && ($_GET['action'] == 'process') && (tep_an_zen_to_han($_POST['quantity']) <= 0)) {
        $quantity_error = true;
        $error = true;
       } else {
        $quantity_error = false;
       }
      }
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
     
    if (!empty($_POST['pre_payment'])) {
      $sel_payment_module = new $_POST['pre_payment']; 
      
      if (method_exists($sel_payment_module, 'preorder_confirmation_check')) {
        $sn_type = $sel_payment_module->preorder_confirmation_check(); 
        if ($sn_type) {
          $sn_error_info = $sel_payment_module->get_preorder_error($sn_type); 
          $error = true; 
          $payment_error = true;
          $payment_error_str = $sn_error_info; 
        } else {
          $payment_error = false;
        }
      } else {
        $payment_error = false;
      }
    }
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']); 
      $preorder_id = date('Ymd').'-'.date('His').tep_get_preorder_end_num(); 
      $redirect_single = 0; 
      if (tep_session_is_registered('customer_id')) {
          $preorder_email_text = PREORDER_MAIL_CONTENT; 
          
          $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${EFFECTIVE_TIME}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}'); 
          $predate_str_arr = explode('-', $_POST['predate']);
          $predate_str = $predate_str_arr[0].PREORDER_YEAR_TEXT.$predate_str_arr[1].PREORDER_MONTH_TEXT.$predate_str_arr[2].PREORDER_MONTH_TEXT;
        
          $payment_name_class = new $_POST['pre_payment'];
          $payment_name_str = $payment_name_class->title;
          
          $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'], $predate_str, $payment_name_str, tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage']);
          
          $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
          
          $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_SUBJECT); 
          
          tep_mail(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), $account_values['customers_email_address'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
          tep_mail('', SENTMAIL_ADDRESS, $preorder_email_subject, $preorder_email_text, tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), $account_values['customers_email_address']);
      } else {
        $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".$_POST['from']."' and site_id = '".SITE_ID."'");    
        if (tep_db_num_rows($exists_customer_raw)) {
          $exists_customer_res = tep_db_fetch_array($exists_customer_raw); 
          if ($exists_customer_res['is_active'] == 0) {
            $redirect_single = 1; 
            $tmp_customer_id = $exists_customer_res['customers_id']; 
            $encode_param_str = md5(time().$exists_customer_res['customers_id'].$_POST['from']); 
            $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$encode_param_str; 
            $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
            $new_str_array = array(
                $active_url, 
                $from_name, 
                STORE_NAME,
                HTTP_SERVER
                ); 
            
            $preorder_email_text = str_replace($old_str_array, $new_str_array, PREORDER_MAIL_ACTIVE_CONTENT); 
            $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_ACTIVE_SUBJECT); 
            $unactive_customers_single = true; 
            $send_to_owner = true; 
            tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$encode_param_str."' where customers_id = '".$exists_customer_res['customers_id']."'");  
          } else {
            $preorder_email_text = PREORDER_MAIL_CONTENT; 
            
            $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${EFFECTIVE_TIME}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}'); 
            $predate_str_arr = explode('-', $_POST['predate']);
            $predate_str = $predate_str_arr[0].PREORDER_YEAR_TEXT.$predate_str_arr[1].PREORDER_MONTH_TEXT.$predate_str_arr[2].PREORDER_MONTH_TEXT;
            
            $payment_name_class = new $_POST['pre_payment'];
            $payment_name_str = $payment_name_class->title;
              
            $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'], $predate_str, $payment_name_str, $from_name, STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage']);
            
            $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
            
            $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_SUBJECT); 
            $exists_email_single = true;     
          }
        } else {
          $tmp_customer_id = tep_create_tmp_guest($_POST['from'], $_POST['lastname'], $_POST['firstname']); 
          $redirect_single = 1; 
          $send_to_owner = true; 
          $encode_param_str = md5(time().$tmp_customer_id.$_POST['from']); 
          $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$encode_param_str; 
          
          $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
          $new_str_array = array(
              $active_url, 
              $from_name, 
              STORE_NAME,
              HTTP_SERVER
              ); 
          $preorder_email_text = str_replace($old_str_array, $new_str_array, PREORDER_MAIL_ACTIVE_CONTENT); 
          $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_ACTIVE_SUBJECT); 
          tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$encode_param_str."' where customers_id = '".$tmp_customer_id."'");  
        }
        tep_mail($from_name, $_POST['from'], $preorder_email_subject, $preorder_email_text, STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS); 
        if (isset($send_to_owner)) {
          tep_mail('', SENTMAIL_ADDRESS, $preorder_email_subject, $preorder_email_text, $from_name, $_POST['from']); 
        }
      }
      
      $send_preorder_id = $preorder_id;
      tep_session_register('send_preorder_id');
      if (isset($exists_email_single)) {
        tep_create_preorder_info($_POST, $preorder_id, $exists_customer_res['customers_id'], $tmp_customer_id, true); 
      } else {
        if (isset($unactive_customers_single)) {
          tep_create_preorder_info($_POST, $preorder_id, $customer_id, $tmp_customer_id, true); 
        } else {
          tep_create_preorder_info($_POST, $preorder_id, $customer_id, $tmp_customer_id); 
        }
      }
      if (!$redirect_single) {
        tep_redirect(tep_href_link(FILENAME_PREORDER_SUCCESS));
      } else {
        tep_redirect(tep_href_link('non-preorder_auth.php'));
      }
?>
      <div>
        <?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $from_email_address, stripslashes($_POST['products_name']), $_POST['quantity'], $_POST['timelimit']); ?>
        <div align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . intval($_GET['products_id'])) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
      </div>
<?php
    } else {
      ?>
      <?php echo tep_draw_form('preorder_product', tep_href_link(FILENAME_PREORDER_PAYMENT, 'action=process')) .  tep_draw_hidden_field('products_id', $product_info['products_id']).tep_draw_hidden_field('products_name', $product_info['products_name']); ?>

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
     <?php
      $selection = $payment_modules->selection(); 
    if (sizeof($selection) > 1) { 
      ?>
      <?php
          if ($payment_error == true) {
            echo '<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice">'; 
            echo '<tr><td>'; 
            echo '<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice">'; 
            echo '<tr class="infoBoxNoticeContents">'; 
            echo '<td class="main" width="100%" valign="top">';
            if (isset($payment_error_str)) {
              echo $payment_error_str; 
            } else {
              echo TEXT_REQUIRED;
            }
            echo '</td>'; 
            echo '</tr>';
            echo '</table>'; 
            echo '</td></tr>'; 
            echo '</table><br>';
          }
      ?>
      <div class="formAreaTitle"><?php echo FORM_FIELD_PREORDER_PAYMENT; ?></div>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
        <?php
        $radio_buttons = 0; 
        for ($i=0, $n=sizeof($selection); $i<$n; $i++) { 
          if (defined('MODULE_PAYMENT_'.strtoupper($selection[$i]['id'].'_PREORDER_SHOW'))) {
            if (constant('MODULE_PAYMENT_'.strtoupper($selection[$i]['id'].'_PREORDER_SHOW')) == 'False') {
              continue; 
            }
            
            if (!tep_whether_show_preorder_payment(constant('MODULE_PAYMENT_'.strtoupper($selection[$i]['id'].'_LIMIT_SHOW')))) {
              continue; 
            }
            
            if (check_money_limit(constant('MODULE_PAYMENT_'.strtoupper($selection[$i]['id'].'_MONEY_LIMIT')), $_POST['preorder_subtotal'])) {
              continue; 
            }
          } else {
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
                                <td colspan="4"><table border="0" cellspacing="0" cellpadding="2" class="box_des"> 
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
      <div class="formAreaTitle"><?php echo $product_info['products_name'].PREORDER_EXPECT_CTITLE; ?></div>
      <table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
        <tr><td class="main"><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td></tr>
      </table>
      <br>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class="main">
            <?php echo '<a href="javascript:void(0);" onclick="document.forms.form1.submit(0);">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
          </td>
          <td align="right" class="main">
            <?php
              if (!tep_session_is_registered('customer_id')) {
                echo tep_draw_hidden_field('lastname', $_POST['lastname']); 
                echo tep_draw_hidden_field('firstname', $_POST['firstname']); 
                echo tep_draw_hidden_field('from', $_POST['from']); 
              }
              echo tep_draw_hidden_field('quantity', $_POST['quantity']); 
              echo tep_draw_hidden_field('predate', $_POST['predate']); 
              echo tep_draw_hidden_field('preorder_subtotal', $_POST['preorder_subtotal']); 
            ?>
            <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
          </td>
        </tr>
      </table>
    </form>
    <?php
       echo tep_draw_form('form1', tep_preorder_href_link($product_info['products_id'], $product_info['romaji'])); 
       if (!tep_session_is_registered('customer_id')) {
         echo tep_draw_hidden_field('lastname', $_POST['lastname']); 
         echo tep_draw_hidden_field('firstname', $_POST['firstname']); 
         echo tep_draw_hidden_field('from', $_POST['from']); 
       }
       echo tep_draw_hidden_field('quantity', $_POST['quantity']); 
       echo tep_draw_hidden_field('predate', $_POST['predate']); 
    ?>
    </form>
<?php
    }
  }
?>
    </div>
        <p class="pageBottom"></p>
    </div>      
    <!-- body_text_eof //-->
    <div id="r_menu">
      <!-- right_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      <!-- right_navigation_eof //-->
    </div>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
