<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_success.php');
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
        $preorder_email_subject = PREORDER_MAIL_SUBJECT; 
        $preorder_email_text = PREORDER_MAIL_CONTENT; 
      } else {
        $preorder_email_subject = PREORDER_MAIL_ACTIVE_SUBJECT; 
        $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$preorder_id; 
        $preorder_email_text = str_replace('${URL}', $active_url, PREORDER_MAIL_ACTIVE_CONTENT); 
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
  if (!$preorder['is_active']) { 
    $breadcrumb->add(PREORDER_SUCCESS_UNACTIVE_TITLE, '');
  } else {
    $breadcrumb->add(PREORDER_SUCCESS_TITLE, '');
  }
?>
<?php page_head();?>
</head>
<body><div align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <h1 class="pageHeading">
      <?php 
      if (!$preorder['is_active']) { 
        echo PREORDER_SUCCESS_UNACTIVE_HEAD_TITLE;
      } else {
        echo PREORDER_SUCCESS_HEAD_TITLE;
      }
      ?></h1> 
      <div class="comment">
      <?php
      if (!$preorder['is_active']) { 
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
      echo tep_draw_form('form', tep_href_link('preorder_success.php')); 
      echo tep_draw_hidden_field('action', 'send'); 
      ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">
        <tr>
          <td>
            <table>
              <tr>
                <td style="font-size:11px;"><?php echo INPUT_PREORDER_SEND_MAIL;?></td> 
                <td>
                <?php echo tep_draw_input_field('pemail', (isset($_POST['pemail'])?$_POST['pemail']:$pe_email));?> 
                </td>
                <td>
                <?php echo tep_image_submit('button_send_mail.gif', 'mail');?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
          <?php echo sprintf(PREORDER_SUCCESS_TEXT, '<a href="'.tep_href_link('email_trouble.php').'">'.PREORDER_SUCCESS_TEXT_LINK.'</a>');?> 
          </td>
        </tr>
        <tr>
          <td>
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
      <?php } else {?> 
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">
        <tr>
          <td>
          <?php 
          $preorder_product_status_query = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$preorder_product['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
          $preorder_product_status = tep_db_fetch_array($preorder_product_status_query);
          if ($preorder_product_status['products_status'] == 0) {
            echo sprintf(PREORDER_SUCCESS_READ_INFO, $categories_name, $preorder_product['products_name']);
          } else {
            echo sprintf(PREORDER_SUCCESS_READ_INFO, $categories_name, '<a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product['products_id']).'">'.$preorder_product['products_name'].'</a>');
          }
          ?> 
          </td>
        </tr>
        <tr>
          <td>
          <br>
          <br>
          <?php echo PREORDER_SUCCESS_APPOINT_CONTENT;?>
          <br>
          <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_NAME.$preorder_product['products_name'];?> 
          <br>
          <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_NUM.$preorder_product['products_quantity'].PREORDER_SUCCESS_UNIT_TEXT;?> 
          <br>
          <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_DATE.date('Y'.PREORDER_SUCCESS_YEAR_TEXT.'m'.PREORDER_SUCCESS_MONTH_TEXT.'d'.PREORDER_SUCCESS_DAY_TEXT, strtotime($preorder['predate']))?>
          </td>
        </tr>
        <tr>
          <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                    <td align="right" class="main">
                    </td> 
                  </tr> 
                </table></td> 
        </tr>
      </table> 
      <?php }?> 
      </div>
      <p class="pageBottom"></p> 
      </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
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
