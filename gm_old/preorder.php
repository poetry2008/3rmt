<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  if (isset($_GET['products_id'])) {
    forward404(); 
  }
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
  
  $products_id = tep_preorder_get_products_id_by_param(); 
 
  if (!$products_id) {
    forward404(); 
  }
  
  $product_info_query = tep_db_query("
      select pd.products_id, pd.products_name, pd.products_status, pd.romaji, pd.preorder_status 
      from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
      where pd.products_id = '" . $products_id . "' 
        and pd.language_id = '" . $languages_id . "' 
        and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
      order by pd.site_id DESC
      limit 1
  ");
  $valid_product = (tep_db_num_rows($product_info_query) > 0);
  
  if (!$valid_product) {
    forward404(); 
  }
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER);
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
  function check_products_num(pid)  {
 var products_num = document.getElementById('quantity').value;
 $.ajax({
	 url:'ajax_check_products_num.php',
         type:'POST',
	 dataType: 'text',
	 data: 'pid='+pid+'&quantity='+products_num,
	 async:false,
	 success:function(data){
	$('#preorder_info_message').html(data);
	 }
 })
  }
</script>

</head>
<body>
<?php
  if ($valid_product == false) {
?>
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

      <p class="main">
        <?php echo HEADING_TITLE_ERROR; ?><br><?php echo ERROR_INVALID_PRODUCT; ?>
      </p>
<?php
  } else {
    //$product_info = tep_db_fetch_array($product_info_query);
?>
	<?php
    $error = false;
    $products_num_query = mysql_query("select products_real_quantity,products_virtual_quantity from products where products_id='".$_POST['products_id']."'");
$products_num_array = mysql_fetch_array($products_num_query);
//$products_num = $products_num_array['products_real_quantity'];
 $products_num = $products_num_array['products_real_quantity'] + $products_num_array['products_virtual_quantity'];

    if (isset($_POST['action']) && ($_POST['action'] == 'process') && empty($_POST['quantity'])) {
      $quantity_error = true;
      $error = true;
    } else {
      if (isset($_POST['action']) && ($_POST['action'] == 'process') && !is_numeric(tep_an_zen_to_han($_POST['quantity']))) {
        $quantity_error = true;
        $error = true;
      } else {
       if (isset($_POST['action']) && ($_POST['action'] == 'process') && (tep_an_zen_to_han($_POST['quantity']) <= 0)) {
        $quantity_error = true;
        $error = true;
       } else {
	       if (isset($_POST['action']) && ($_POST['action'] == 'process') && ($products_num >= $_POST['quantity'])){
 $num_error = true;

//	 $quantity_error = true;
        $error = true;
      
	       }else{
        $quantity_error = false;
	       }

    //    $quantity_error = false;
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
      if (isset($_POST['action']) && ($_POST['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
        $fromemail_error = true;
        $error = true;
        } else {
          $fromemail_error = false;
        }
      }
    
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_POST['action']) && ($_POST['action'] == 'process') && empty($last_name)) {
        $lastname_error = true;
        $error = true;
      } else {
        $lasttname_error = false;
      }
      
      if (isset($_POST['action']) && ($_POST['action'] == 'process') && empty($first_name)) {
        $firstname_error = true;
        $error = true;
      } else {
        $firstname_error = false;
      }
    } 
    if (isset($_POST['action']) && ($_POST['action'] == 'process') && empty($_POST['predate'])) {
      $predate_error = true;
      $error = true;
    } else {
      $predate_error = false;
    }
    
    if (isset($_POST['action']) && ($_POST['action'] == 'process') && ($error == false)) {
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']);
      echo tep_draw_form('pform', tep_href_link(FILENAME_PREORDER_PAYMENT));
      foreach ($_POST as $p_key => $p_value) {
        if ($p_key != 'x' && $p_key != 'y') {
          echo tep_draw_hidden_field($p_key, $p_value); 
        }
      }
      $product_query = tep_db_query("select products_price, products_price_offset, products_tax_class_id, products_small_sum from ".TABLE_PRODUCTS." where products_id = '".$_POST['products_id']."'"); 
      $product_res = tep_db_fetch_array($product_query);
      $preorder_subtotal = 0; 
      if ($product_res) {
        $products_tax = tep_get_tax_rate($product_res['products_tax_class_id']); 
        $products_price = tep_get_final_price($product_res['products_price'], $product_res['products_price_offset'], $product_res['products_small_sum'], $_POST['quantity']); 
        $preorder_subtotal = tep_add_tax($products_price, $products_tax) * $_POST['quantity']; 
      }
      echo tep_draw_hidden_field('preorder_subtotal', $preorder_subtotal); 
      echo '</form>';
      echo '<script type="text/javascript">';
      echo 'document.forms.pform.submit();'; 
      echo '</script>';
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
        if ($fromemail_error == true) $your_email_address_prompt .= "<br>".ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?>
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
  
      <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
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

      <?php echo tep_draw_form('preorder_product', tep_preorder_href_link($product_info['products_id'], $product_info['romaji'])) .  tep_draw_hidden_field('products_id', $product_info['products_id']).tep_draw_hidden_field('action', 'process'); ?>
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
      <div class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></div>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>  
          <td class="main" width="120"><?php echo FORM_FIELD_CUSTOMER_LASTNAME; ?></td>
          <td class="formArea_td_info"><?php echo $last_name_prompt; ?></td>
        </tr>
        <tr>  
          <td class="main" width="120"><?php echo FORM_FIELD_CUSTOMER_FIRSTNAME; ?></td>
          <td class="main"><?php echo $first_name_prompt; ?></td>
        </tr>
        <tr>
          <td class="main" width="120"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
          <td class="main"><?php echo $your_email_address_prompt; ?></td>
        </tr>
        <tr> 
          <td colspan="2" class="main">お取り置き期限がございます。いつも使用しているメールアドレスをご入力ください。</td>
        </tr>
      </table><br>
      <div class="formAreaTitle"><?php echo FORM_TITLE_FRIEND_DETAILS; ?></div>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>
          <td class="formArea_td" valign="top">商品名:</td>
          <td class="main">
          <strong>
          <?php 
          if ($product_info['products_status'] == 0 || $product_info['products_status'] == 3)  {
            echo $po_game_c.'&nbsp;&nbsp;'.$product_info['products_name']; 
          } else {
            echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '" target="_blank">' . $po_game_c . '&nbsp;/&nbsp;' . $product_info['products_name'].'</a>'; 
            
          }
            ?>
          </strong>
          </td>
        </tr>
        <tr>
          <td class="main" width="120"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
          <td class="main">
<?php
if (!isset($_POST['quantity'])) $_POST['quantity'] = NULL; //del notice
//echo tep_draw_input_field('quantity', (($quantity_error == true) ? $_POST['quantity'] : $_GET['quantity']) , 'size="7" maxlength="15" id="quantity" class="input_text_short" onblur="check_products_num('.$product_info['products_id'].');"');
echo tep_draw_input_field('quantity', (($quantity_error == true) ? $_POST['quantity'] : $_GET['quantity']) , 'size="7" maxlength="15" id="quantity" class="input_text_short" ');

            echo '&nbsp;&nbsp;個';
if ($quantity_error == true) { echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';}
if ($num_error == true){echo '<span id="preorder_info_message" class="errorText"><br>予約の必要がございません。<br>'.(int)$_POST['quantity'].'個は注文可能です。<a href='.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id) .'>コチラ</a> からお手続きください。 </span>' ;}
 

       if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL; //del notice
?>
          </td>
        </tr>
        <?php
        if (false) { 
        ?>
        <tr>
          <td class="main" width="120"><?php echo FORM_FIELD_PREORDER_FIXTIME; ?></td>
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