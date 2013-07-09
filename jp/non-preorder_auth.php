<?php
/*
  $Id$

*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/non-preorder_auth.php');
  $error = false;
  $pe_email = '';
  $preorder_id = 0;  
  
  if (isset($_SESSION['send_preorder_id'])) {
    $preorder_id = $_SESSION['send_preorder_id'];
  }
  
  if (!$preorder_id) {
    $error = true;
    $error_msg = ALREADY_SEND_PREMAIL_TEXT;
  }
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$preorder_id."' and site_id = '".SITE_ID."'"); 
  $preorder = tep_db_fetch_array($preorder_raw);
  
  if ($preorder) {
    $pe_email = $preorder['customers_email_address']; 
    if ($_POST['action'] == 'send') {
      if (empty($_POST['pemail'])) {
        $error = true; 
      } else if (!tep_validate_email($_POST['pemail'])) {
        $error = true; 
        $error_msg = PREORDER_EMAIL_PATTENR_WRONG; 
      } else if ($preorder['is_active']) {
        $error = true; 
        $error_msg = ALREADY_SEND_PREMAIL_TEXT; 
      } else if (tep_check_exists_cu_email($_POST['pemail'], $preorder['customers_id'], 1)) {
        $error = true; 
        $error_msg = CHECK_EMAIL_EXISTS_ERROR; 
      }
      

      if (!$error) {
        //预约邮件认证
        $preorders_mail_array = tep_get_mail_templates('PREORDER_MAIL_ACTIVE_CONTENT',SITE_ID);
        $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, $preorders_mail_array['title']); 
        $encode_param_str = md5(time().$preorder['customers_id'].$_POST['pemail']); 
        $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$encode_param_str; 
        
        $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
        $new_str_array = array(
            $active_url, 
            $preorder['customers_name'], 
            STORE_NAME,
            HTTP_SERVER
            ); 
        $preorder_email_text = str_replace($old_str_array, $new_str_array, $preorders_mail_array['contents']); 
        tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$encode_param_str."', `customers_email_address` = '".$_POST['pemail']."' where customers_id = '".$preorder['customers_id']."' and site_id = '".SITE_ID."'");  
        tep_db_query("update `".TABLE_PREORDERS."` set `customers_email_address` = '".$_POST['pemail']."' where orders_id = '".$preorder['orders_id']."' and site_id = '".SITE_ID."'"); 
        
        tep_mail($preorder['customers_name'], $_POST['pemail'], $preorder_email_subject, $preorder_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
      }
    }
  }
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'");
  $preorder_product = tep_db_fetch_array($preorder_product_raw);
  $categories_name = '';

  $ca_path = tep_get_product_path($preorder_product['products_id']);
  
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
        $categories_info = tep_db_fetch_array($categories_query); 
        
        if ($cnum == 0) {
          $categories_name = $categories_info['categories_name']; 
        }
        
        $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
      } else {
        break;
      }
    }
  }
  
  $breadcrumb->add($preorder_product['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product['products_id'])); 
  $breadcrumb->add(PREORDER_SUCCESS_UNACTIVE_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      </td>
      <td valign="top" id="contents">
        <h1 class="pageHeading">
        <?php 
          echo PREORDER_SUCCESS_UNACTIVE_HEAD_TITLE;
        ?>
        </h1>
        <div class="comment"> 
        <?php
        if ($error == true) {
          if (isset($error_msg)) {
            if ($error_msg == ALREADY_SEND_PREMAIL_TEXT) {
            ?>
            <script type="text/javascript">
            alert('<?php echo $error_msg;?>'); 
            window.location.href="<?php echo HTTP_SERVER;?>"; 
            </script>
            <?php
            } else {
              echo '<div style="color:ff0000;">'.$error_msg.'</div>'; 
            }
          }
        }
        echo tep_draw_form('form', tep_href_link('non-preorder_auth.php')); 
        echo tep_draw_hidden_field('action', 'send'); 
        ?>
        <table border="0" cellspacing="0" cellpadding="0" class="information_table">
          <tr>
            <td>
              <table class="information_table">
                <tr>
                	<td colspan="3"><img src="images/design/mail_top.gif" alt=""></td>
                </tr>
                <tr>
                  <td colspan="3" class="information_color">
                  <?php
                    echo sprintf(PREORDER_NOTICE_SEND_TO_EMAIL_TEXT, (isset($_POST['pemail'])?$_POST['pemail']:$pe_email)); 
                  ?></div>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                  <br><?php
                    echo PREORDER_ACTIVE_INFO_FRONT_COMMENT; 
                  ?><br><br>
                  </td>
                </tr>
                <tr>
                  <td width="85">
				  <b><?php echo INPUT_PREORDER_SEND_MAIL;?></b>
                  </td>
                  <td width="360">
                  <?php echo tep_draw_input_field('pemail',
                      (isset($_POST['pemail'])?$_POST['pemail']:$pe_email),'size="56"');?> 
                  </td>
                  <td align="right">
                  <?php echo tep_image_submit('button_send_mail.gif', 'mail');?>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                  <br><?php
                    echo PREORDER_ACTIVE_INFO_END_COMMENT; 
                  ?>
                  </td>
                </tr> 
              </table>
            </td>
          </tr>
            <tr>
              <td colspan="3" align="center"><img src="images/design/mail_bottom.gif" alt=""></td>
            </tr>
          <tr>
            <td>
            <?php echo PREORDER_ACTIVE_INFO_EMAIL_READ;?> 
            </td>
          </tr>
        </table>
        </form> 
        </div>
      </td>
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      </td>
    </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
