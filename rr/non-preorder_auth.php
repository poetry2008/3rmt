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
      }
    
      if ($preorder['is_active']) {
        $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_SUBJECT); 
        
        $preorder_email_text = PREORDER_MAIL_CONTENT; 
        $pre_name = '';
        $pre_num = 0;
        $pre_date = '';
        $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${EFFECTIVE_TIME}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}'); 
        
        $pre_date_str = strtotime($preorder['predate']);
        $pre_date = date('Y', $pre_date_str).PREORDER_YEAR_TEXT.date('m', $pre_date_str).PREORDER_MONTH_TEXT.date('d', $pre_date_str).PREORDER_DAY_TEXT;
        $preorder_product_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'");
        $preorder_product_res = tep_db_fetch_array($preorder_product_query); 
        
        if ($preorder_product_res) {
          $pre_name = $preorder_product_res['products_name'];
          $pre_num = $preorder_product_res['products_quantity']; 
        }
        $pre_replace_info_arr = array($pre_name, $pre_num, $pre_date, $preorder['payment_method'], $preorder['customers_name'], STORE_NAME, HTTP_SERVER, $preorder['orders_id'], $preorder['comment_msg']);
        
        $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
      } else {
        $preorder_email_subject = str_replace('${SITE_NAME}', STORE_NAME, PREORDER_MAIL_ACTIVE_SUBJECT); 
        $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$preorder_id; 
        $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
        $new_str_array = array(
            $active_url,   
            $preorder['customers_name'], 
            STORE_NAME,
            HTTP_SERVER
            ); 
        $preorder_email_text = str_replace($old_str_array, $new_str_array, PREORDER_MAIL_ACTIVE_CONTENT); 
      }
      tep_mail($preorder['customers_name'], $_POST['pemail'], $preorder_email_subject, $preorder_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
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
      <td valign="top" class="left_colum_border">
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
      echo PREORDER_SUCCESS_ACTIVE_INFO_TEXT; 
      echo '<br><br>'; 
      echo tep_draw_hidden_field('action', 'send'); 
      ?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box">
          <tr>
            <td>
              <table>
                <tr>
                  <td style="font-size:11px; color:#ff0000;"><?php echo INPUT_PREORDER_SEND_MAIL;?></td> 
                  <td>
                  <?php echo tep_draw_input_field('pemail', (isset($_POST['pemail'])?$_POST['pemail']:$pe_email));?> 
                  </td>
                  <td>
                  <?php echo tep_image_submit('button_send_mail.gif', 'mail');?> 
                  </td>
                </tr>
              </table><br>
            </td>
          </tr>
          <tr>
            <td><font size="2">
            <?php echo sprintf(PREORDER_SUCCESS_TEXT, '<a href="'.tep_href_link('email_trouble.php').'">'.PREORDER_SUCCESS_TEXT_LINK.'</a>');?></font> 
            </td>
          </tr>
          <tr>
            <td><br>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                      <td align="right" class="main">
                      </td> 
                    </tr> 
                  </table></td> 
          </tr>
        </table>
        </form>         
        </div>
                <p class="pageBottom"></p>
      </td>
    </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
