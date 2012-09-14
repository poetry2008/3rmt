<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  if (!isset($_POST['products_id'])) {
    forward404(); 
  }
  require(DIR_WS_CLASSES. 'payment.php'); 
  $payment_modules = payment::getInstance(SITE_ID);
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
      
      $("input[name=pre_payment]").each(function(index){
	if ($(this).val() != $(radio).val()) {
          $(this).parent().parent().removeClass(); 
          $(this).parent().parent().addClass("box_content_title"); 
        }
      });
      
      $(radio).parent().parent().removeClass(); 
      $(radio).parent().parent().addClass("box_content_title box_content_title_selected"); 
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
  });
</script>
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
?>
      <div class="pageHeading"><?php echo $po_game_c . '&nbsp;' . $product_info['products_name'].TEXT_PREORDER_BOOK; ?></div>
            <div class="comment">
            	<table class="preorder_info_box" cellspacing="0" cellspacing="0" border="0" width="100%">
            <tr><td>
      <p>
        <?php echo STORE_NAME.TEXT_PREORDER_IN;?><?php echo $po_game_c.TEXT_PREORDER_BOOK_INFO; ?>
        <?php 
        if ($product_info['products_status'] == 0 || $product_info['products_status'] == 3)  {
          echo $product_info['products_name']; 
        } else {
          echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $product_info['products_id']) . '">' .  $product_info['products_name'].'</a>';
        }
        echo TEXT_PREORDER_BOOK_INFO_END;
        ?>
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
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['pre_payment'])) {
      $payment_error = true;
      $error = true;
    } else {
      $payment_error = false;
    }
     
    if (!empty($_POST['pre_payment'])) {
      $sn_type = $payment_modules->preorder_confirmation_check($_POST['pre_payment']); 
      if ($sn_type) {
        $sn_error_info = $payment_modules->get_preorder_error($_POST['pre_payment'], $sn_type); 
        $error = true; 
        $payment_error = true;
        $payment_error_str = $sn_error_info; 
      } else {
        $payment_error = false;
      }
    }
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']); 
      $preorder_id = date('Ymd').'-'.date('His').tep_get_preorder_end_num(); 
      $redirect_single = 0; 
      $max_op_len = 0;
      $max_op_array = array();
      $mail_option_str = '';
      foreach ($_POST as $mo_key => $mo_value) {
        $m_op_str = substr($mo_key, 0, 3);
        if ($m_op_str == 'op_') {
          $m_op_info = explode('_', $mo_key); 
          $item_m_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$m_op_info['1']."' and id = '".$m_op_info[3]."'"); 
          $item_m_res = tep_db_fetch_array($item_m_raw);
          if ($item_m_res) {
            $max_op_array[] = mb_strlen($item_m_res['front_title'], 'utf-8'); 
          }
        }
      }
      
      if (!empty($max_op_array)) {
        $max_op_len = max($max_op_array);
      }
      foreach ($_POST as $mao_key => $mao_value) {
        $ma_op_str = substr($mao_key, 0, 3);
        if ($ma_op_str == 'op_') {
          $ma_op_info = explode('_', $mao_key); 
          $item_f_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$ma_op_info['1']."' and id = '".$ma_op_info[3]."'"); 
          $item_f_res = tep_db_fetch_array($item_f_raw);
          if ($item_f_res) {
            $mail_option_str .= $item_f_res['front_title'].str_repeat('　', intval($max_op_len - mb_strlen($item_f_res['front_title'], 'utf-8'))).'：'.str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", stripslashes($mao_value))."\n"; 
          }
        }
      }
      
      if (tep_session_is_registered('customer_id')) {
          $preorder_email_text = PREORDER_MAIL_CONTENT; 
          
          $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}'); 
        
          $payment_name_class = new $_POST['pre_payment'];
          $payment_name_str = $payment_name_class->title;
          
          $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'], $payment_name_str, tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']), STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage'], $mail_option_str);
          
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
            
            $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}'); 
            
            $payment_name_class = new $_POST['pre_payment'];
            $payment_name_str = $payment_name_class->title;
              
            $pre_replace_info_arr = array($_POST['products_name'], $_POST['quantity'], $payment_name_str, $from_name, STORE_NAME, HTTP_SERVER, $preorder_id, $_POST['yourmessage'], $mail_option_str);
            
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
        <?php echo TEXT_PREORDER_BOOK_TEXT;?>
      </p>
        <p class="red"><b><?php echo TEXT_PREORDER_BOOK_TEXT_END;?></b></p>
<?php
      if($error == true) {
        echo '<span class="errorText"><b>'.TEXT_INPUT_ERROR_INFO.'</span></b><br><br>';
      }
?>
    <?php
      $selection = $payment_modules->selection(1); 
      if (sizeof($selection) > 1) { 
        if ($payment_error == true) {
            echo '<div class="box_waring">'; 
            if (isset($payment_error_str)) {
              echo $payment_error_str; 
            } else {
              echo TEXT_REQUIRED;
            }
            echo '</div><br>'; 
        }
      }
    ?>
     
    <div class="formAreaTitle"><?php echo FORM_FIELD_PREORDER_PAYMENT; ?></div>
    <div class="checkout_payment_info">  
    <?php
    if (sizeof($selection) > 1) { 
      ?>
      <?php
        foreach ($selection as $key => $singleSelection) { 
          if (defined('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_PREORDER_SHOW'))) {
            if (constant('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_PREORDER_SHOW')) == 'False') {
              continue; 
            }
            
            if (!tep_whether_show_preorder_payment(constant('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_LIMIT_SHOW')))) {
              continue; 
            }
            
            if ($payment_modules->moneyInRange($singleSelection['id'], $_POST['preorder_subtotal'])) {
              continue; 
            }
          } else {
            continue; 
          }
        ?>
        <div>
          <div class="box_content_title <?php if ($_POST['pre_payment'] == $singleSelection['id']) {echo 'box_content_title_selected';};?>"> 
            <div class="frame_w70"><b><?php echo $singleSelection['module'];?></b></div> 
            <div class="float_right">
            <?php echo tep_draw_radio_field('pre_payment', $singleSelection['id'], $_POST['pre_payment'] == $singleSelection['id']);?> 
            </div>
          </div>
          <div>
            <p class="cp_description"><?php echo $singleSelection['description'];?></p>
            <div class="cp_content">
              <div style="display:none;" class="rowHide rowHide_<?php echo $singleSelection['id'];?>">
              <?php 
                echo $singleSelection['fields_description']; 
                foreach ($singleSelection['fields'] as $key2 => $field) {
              ?>
                <div class="txt_input_box">
                  <?php if ($field['title']) {?>
                  <div class="frame_title"><?php echo $field['title'];?></div> 
                  <?php }?>
                  <div class="float_left"><?php echo $field['field']?><small><font color="#AEOE30"><?php echo $field['message'];?></font></small></div> 
                </div>
              <?php
                }
              ?> 
               <?php echo $singleSelection['footer'];?> 
              </div>
            </div>
          </div>
        </div>
        <?php 
        }
        ?>
      <?php }?> 
      </div>
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
              echo tep_draw_hidden_field('preorder_subtotal', $_POST['preorder_subtotal']); 
              foreach ($_POST as $op_s_key => $op_s_value) {
                $ops_single_str = substr($op_s_key, 0, 3);
                if ($ops_single_str == 'op_') {
                  echo tep_draw_hidden_field($op_s_key, stripslashes($op_s_value)); 
                }
              }
            ?>
            <?php echo tep_image_submit('button_continue02.gif', IMAGE_BUTTON_CONTINUE); ?>
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
       foreach ($_POST as $op_key => $op_value) {
         $op_single_str = substr($op_key, 0, 3);
         if ($op_single_str == 'op_') {
           echo tep_draw_hidden_field($op_key, stripslashes($op_value)); 
         }
       }
    ?>
    </form>
<?php
    }
  }
?>
        <p class="pageBottom"></p>
        </td></tr></table>
        </div>
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
