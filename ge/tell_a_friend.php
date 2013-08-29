<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_TELL_A_FRIEND);
 
  if (isset($_GET['action'])) {
    //检查表单信息是否正确    
    if ($_GET['action'] == 'check_form') {
      $form_info_error = array(); 
      $form_info_error['yourname'] = ''; 
      $form_info_error['from_email'] = ''; 
      $form_info_error['firendname'] = ''; 
      $form_info_error['friendemail'] = ''; 
      $form_info_error['random_code'] = ''; 
      $error_single = false;
      
      if ($_POST['is_login_single'] == '0') {
        if (empty($_POST['yourname'])) {
          $form_info_error['yourname'] = TEXT_REQUIRED; 
          $error_single = true;
        }
        if (!tep_validate_email(trim($_POST['from_email']))) {
          $form_info_error['from_email'] = strip_tags(ENTRY_EMAIL_ADDRESS_CHECK_ERROR); 
          $error_single = true;
        }
      } 
        
      if (empty($_POST['friendname'])) {
        $form_info_error['firendname'] = TEXT_REQUIRED; 
        $error_single = true;
      }
      
      if (!tep_validate_email(trim($_POST['friendemail']))) {
        $form_info_error['friendemail'] = strip_tags(ENTRY_EMAIL_ADDRESS_CHECK_ERROR); 
        $error_single = true;
      }
       
      if (empty($_POST['random_code'])) {
        $form_info_error['random_code'] = VALIDATE_RANDOM_CODE_IS_NULL; 
        $error_single = true;
      } else {
        if (md5(strtolower($_POST['random_code'])) != $_SESSION['random_code']) {
          $form_info_error['random_code'] = VALIDATE_RANDOM_CODE_NOT_SAME; 
          $error_single = true;
        } else {
          if ($error_single) {
            $form_info_error['random_code'] = VALIDATE_RANDOM_CODE_IS_NULL; 
          }
        }
      }
      if ($error_single) {
        $form_info_error['time'] = tep_create_random_value(3).time(); 
        echo implode('|||', $form_info_error);
      } else {
        echo 'success'; 
      }
      exit;
    }
  }
  if (tep_session_is_registered('customer_id')) {
    $account = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_email_address 
        from " . TABLE_CUSTOMERS . " 
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
    $product_info_query = tep_db_query("
        select pd.products_name
        from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        where pd.products_status != '0' and pd.products_status != '3'
          and pd.products_id = '" .  (int)$_GET['products_id'] . "' 
          and pd.language_id = '" . $languages_id . "' 
          and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
        order by pd.site_id DESC
        limit 1
    ");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL;
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_TELL_A_FRIEND, 'send_to=' . $_GET['send_to'] . '&products_id=' . $_GET['products_id']));
?>
<?php page_head();?>
<?php
header('Content-Type: text/html; charset=UTF-8');
header('Pragma: public');         
header('Expires: '.gmdate('D, d M Y H:i:s', 0).'GMT');
@header('Last-modified: ' . gmdate('D, d M Y H:i:s') . ' gmt');
@header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: private, post-check=0, pre-check=0, max-age=0', FALSE);
header('Pragma: no-cache');
header('Expires: 0');
?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
<?php //检查表单信息是否正确?>
function check_form_error()
{
  <?php 
    if (tep_session_is_registered('customer_id')) {
  ?>
  var is_login_single = '1';  
  <?php
    } else { 
  ?>
  var is_login_single = '0';  
  <?php
    }
  ?>
  if (is_login_single == '0') {
    var yourname_value = document.getElementsByName('yourname')[0].value; 
    var from_value = document.getElementsByName('from')[0].value; 
    var friendname_value = document.getElementsByName('friendname')[0].value; 
    var friendemail_value = document.getElementsByName('friendemail')[0].value; 
    var random_code_value = document.getElementsByName('random_code')[0].value; 
  } else {
    var yourname_value = ''; 
    var from_value = ''; 
    var friendname_value = document.getElementsByName('friendname')[0].value; 
    var friendemail_value = document.getElementsByName('friendemail')[0].value; 
    var random_code_value = document.getElementsByName('random_code')[0].value; 
  }
  
  $.ajax({
    url: '<?php echo FILENAME_TELL_A_FRIEND.'?action=check_form';?>',
    type: 'POST',
    dataType: 'text',
    data: 'yourname='+yourname_value+'&from_email='+from_value+'&friendname='+friendname_value+'&friendemail='+friendemail_value+'&random_code='+random_code_value+'&is_login_single='+is_login_single, 
    async: false,
    success: function(msg) {
      if (msg != 'success') {
        msg_array = msg.split('|||');
        $('#yourname_error').html(msg_array[0]); 
        $('#from_error').html(msg_array[1]); 
        $('#friendname_error').html(msg_array[2]); 
        $('#friendemail_error').html(msg_array[3]); 
        $('#code_error').html(msg_array[4]); 
        document.getElementsByName('random_code')[0].value = '';
        $('.img_box').children('img:first').attr("src", 'random_code.php?t='+msg_array[5]);
      } else {
        document.forms.email_friend.submit(); 
      }
    }
  });
}
</script>
</head>
<body>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<div id="main">
  <!-- left_navigation -->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- left_navigation_eof -->
  <!-- body_text -->
  <div id="content">
    <?php
  if ($valid_product == false) {
?>
	<h1 class="pageHeading"><?php echo HEADING_TITLE_ERROR; ?></h1>
      <p><?php echo ERROR_INVALID_PRODUCT; ?> </p>
    <?php
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
?>
    <div class="headerNavigation">
      <?php echo $breadcrumb->trail(' &raquo; '); ?>
    </div>
    <h1 class="pageHeading"><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h1>
    <?php
    $error = false;

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && !tep_validate_email(trim($_POST['friendemail']))) {
      $friendemail_error = true;
      $error = true;
    } else {
      $friendemail_error = false;
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['friendname'])) {
      $friendname_error = true;
      $error = true;
    } else {
      $friendname_error = false;
    }

    if (tep_session_is_registered('customer_id')) {
      $from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
      $from_email_address = $account_values['customers_email_address'];
    } else {
      if (!isset($_POST['yourname'])) $_POST['yourname'] = NULL; 
      $from_name = $_POST['yourname'];
      if (!isset($_POST['from'])) $_POST['from'] = NULL; 
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

    $random_code_info = '';
    if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
      if (empty($_POST['random_code'])) {
        //验证码为空 
        $random_code_error = true;
        $random_code_info = VALIDATE_RANDOM_CODE_IS_NULL; 
        $error = true;
      } else {
        if (md5(strtolower($_POST['random_code'])) != $_SESSION['random_code']) {
          //验证码不一致 
          $random_code_error = true;
          $random_code_info = VALIDATE_RANDOM_CODE_NOT_SAME; 
          $error = true;
        } else {
          if ($error == true) {
            $random_code_error = true;
            $random_code_info = VALIDATE_RANDOM_CODE_IS_NULL; 
            $error = true;
          } else {
            $random_code_error = false;
          }
        }
      }
    } else {
      $random_code_error = false;
    }
    
    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $tell_friend_mail_templates = tep_get_mail_templates('TELL_FRIEND_MAIL_TEMPLATES','0');
      $replace_array = array(
            '${FROM}',
            '${TO}', 
            '${PRODUCTS_NAME}',
            '${COMMENTS}',
            '${PRODUCTS_URL}'
          ); 
      
      $new_replace_array = array(
            $from_name,
            $_POST['friendname'], 
            $_POST['products_name'],
            $_POST['yourmessage'],
            tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id'])
          );
      
      $email_subject = str_replace($replace_array, $new_replace_array, $tell_friend_mail_templates['title']);
      $email_body = str_replace($replace_array, $new_replace_array, $tell_friend_mail_templates['contents']);
      
      $email_subject = tep_replace_mail_templates($email_subject, $from_name, $from_email_address);
      $email_body = tep_replace_mail_templates($email_body, $from_name, $from_email_address);

      tep_mail($_POST['friendname'], $_POST['friendemail'], $email_subject, stripslashes($email_body), '', $from_email_address);
      tep_redirect(tep_href_link(FILENAME_TELL_A_FRIEND_SUCCESS, 'products_id='.$_GET['products_id'].'&friendemail='.$_POST['friendemail']));
?>
    <div class="tell_msg">
      <p class="main"><?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, stripslashes($_POST['products_name']), $_POST['friendemail']); ?></p>
      <div align="right">
        <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?>
      </div>
    </div>
    <?php
    } else {
      if (tep_session_is_registered('customer_id')) {
        $your_name_prompt = tep_output_string_protected(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
        if (!isset($_GET['yourname'])) $_GET['yourname'] = NULL; 
        $your_name_prompt = tep_draw_input_field('yourname', (($fromname_error == true) ? $_POST['yourname'] : $_GET['yourname']));
        $your_name_prompt .= '&nbsp;<span id="yourname_error" class="errorText">';
        if ($fromname_error == true) {
          $your_name_prompt .= TEXT_REQUIRED;
        }
        $your_name_prompt .= '</span>';
        
        if (!isset($_GET['from'])) $_GET['from'] = NULL; 
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']));
        $your_email_address_prompt .= '<br><span id="from_error" class="errorText">';
        if ($fromemail_error == true) {
          $your_email_address_prompt .= strip_tags(ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
        }
        $your_email_address_prompt .= '</span>';
      }
?>
    <?php echo tep_draw_form('email_friend', tep_href_link(FILENAME_TELL_A_FRIEND, 'action=process&products_id=' . $_GET['products_id'])) . tep_draw_hidden_field('products_name', $product_info['products_name']); ?>
    <table class="box_des" width="95%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main">
                <table border="0" cellspacing="0" cellpadding="2" class="box_des"  width="100%">
                  <tr>
                    <td class="main" width="120"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td>
                    <td class="main"><?php echo $your_name_prompt; ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
                    <td class="main"><?php echo $your_email_address_prompt; ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br>
          <?php echo FORM_TITLE_FRIEND_DETAILS; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main">
                <table class="box_des" border="0" cellspacing="0" cellpadding="2" width="100%">
                  <tr>
                    <td class="main" width="120"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
                    <td class="main">
                    <?php 
                    if (!isset($_GET['friendname'])) $_GET['friendname'] = NULL; 
                    echo tep_draw_input_field('friendname', (($friendname_error == true) ? $_POST['friendname'] : $_GET['friendname'])); if ($friendname_error == true) 
                    echo '&nbsp;<span id="friendname_error" class="errorText">';
                    if ($friendname_error == true) {
                      echo TEXT_REQUIRED;
                    }
                    echo '</span>';
                    ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td>
                    <td class="main">
                    <?php 
                    echo tep_draw_input_field('friendemail', (($friendemail_error == true) ? $_POST['friendemail'] : $_GET['send_to'])); 
                    echo '<br><span id="friendemail_error" class="errorText">';
                    if ($friendemail_error == true) { 
                      echo strip_tags(ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
                    } 
                    echo '</span>'; 
                    ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="main">
        <br> 
        <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
              <table class="box_des" border="0" cellspacing="0" cellpadding="2"  width="100%">
                <tr>
                  <td class="main" width="120" valign="top"><?php echo VALIDATE_RANDOM_CODE_TEXT;?></td>
                  <td class="main">
                    <div class="img_box"><img src="<?php echo 'random_code.php?t='.tep_create_random_value(3).time();?>" border="0" align="left"></div>
					<input type="text" name="random_code" size="7" value="">
                    <?php
                       echo '<br><span id="code_error" class="errorText">'; 
                       if ($random_code_error == true) {
                          echo $random_code_info;
                       }
                       echo '</span>'; 
                    ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br>
          <?php echo FORM_TITLE_FRIEND_MESSAGE; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><br>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
              <td align="right" class="main"><a href="javascript:void(0);" onclick="check_form_error();"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    </form>
    <?php
    }
}
?>
  </div>
  <!-- body_text_eof -->
  <!-- right_navigation -->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
  </div>
  <!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
