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

  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');
  
  $belong_option_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$product_info['products_id']."'");
  $belong_option = tep_db_fetch_array($belong_option_raw); 
  
  $hm_option = new HM_Option();
?>
<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/option.js"></script>
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
// $products_num = $products_num_array['products_real_quantity'];
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
	       if (isset($_POST['action']) && ($_POST['action'] == 'process') && ($products_num >= (int)$_POST['quantity'])){
	 $num_error = true;
        $error = true;
      
	       }else{
        $quantity_error = false;
	       }
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
    
    if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
      if ($hm_option->check()) {
        $error = true;
      }
    } 
    
    if(isset($_POST['action']) && ($_POST['action'] == 'process') && !empty($_POST['quantity'])){

      $weight_count = 0;
      $products_weight_error = false;
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']); 
      $product_weight_query = tep_db_query("select products_weight from ".TABLE_PRODUCTS." where products_id = '".$_POST['products_id']."'"); 
      $product_weight_array = tep_db_fetch_array($product_weight_query);
      $weight_count = $product_weight_array['products_weight'] * $_POST['quantity'];
      tep_db_free_result($product_weight_query);

      $country_max_fee = 0; 
      $country_fee_max_array = array();
      $country_fee_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where status='0'");
      while($country_fee_array = tep_db_fetch_array($country_fee_query)){

        $country_fee_max_array[] = $country_fee_array['weight_limit'];
      }
      tep_db_free_result($country_fee_query);
      $country_max_fee = max($country_fee_max_array);

      $country_max_area = 0; 
      $country_area_max_array = array();
      $country_area_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where status='0'");
      while($country_area_array = tep_db_fetch_array($country_area_query)){

        $country_area_max_array[] = $country_area_array['weight_limit'];
      }
      tep_db_free_result($country_area_query);
      $country_max_area = max($country_area_max_array);

      $country_max_city = 0; 
      $country_city_max_array = array();
      $country_city_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where status='0'");
      while($country_city_array = tep_db_fetch_array($country_city_query)){

        $country_city_max_array[] = $country_city_array['weight_limit'];
      }
      tep_db_free_result($country_city_query);
      $country_max_city = max($country_city_max_array);

      $weight_count_limit = max($country_max_fee,$country_max_area,$country_max_city);

      $products_num = $weight_count_limit / $product_weight_array['products_weight'];

      $products_num = (int)$products_num;

      if($weight_count > $weight_count_limit){

        $error = true; 
        $products_weight_error = true;
      }
    }  
    if (isset($_POST['action']) && ($_POST['action'] == 'process') && ($error == false)) {
      $_POST['quantity'] = tep_an_zen_to_han($_POST['quantity']);
      echo tep_draw_form('pform', tep_href_link(FILENAME_PREORDER_PAYMENT));
      foreach ($_POST as $p_key => $p_value) {
        if ($p_key != 'x' && $p_key != 'y') {
          echo tep_draw_hidden_field($p_key, stripslashes($p_value)); 
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
        if ($lastname_error == true) $last_name_prompt .= '&nbsp;<span class="errorText">' . PREORDER_TEXT_REQUIRED . '</span>';
        if ($firstname_error == true) $first_name_prompt .= '&nbsp;<span class="errorText">' . PREORDER_TEXT_REQUIRED . '</span>';
if (!isset($_GET['from'])) $_GET['from'] = NULL; //del notice
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']) , 'size="30" class="input_text"') . TEXT_PHONE_EMAIL_ADDRESS;
        if ($fromemail_error == true) $your_email_address_prompt .="<br>".ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?>
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
      <div class="pageHeading"><?php echo $po_game_c . '&nbsp;' . $product_info['products_name'].TEXT_PREORDER_BOOK; ?></div>
            <div class="comment">
            	<table class="preorder_info_box" cellspacing="0" cellspacing="0" border="0" width="100%">
            	<tr>
            		<td>
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
      <?php echo tep_draw_form('preorder_product', tep_preorder_href_link($product_info['products_id'], $product_info['romaji'])) .  tep_draw_hidden_field('products_id', $product_info['products_id']).tep_draw_hidden_field('action', 'process'); ?>

      <p>
        <?php echo TEXT_PREORDER_BOOK_TEXT;?>
      </p>
        <p class="red"><b><?php echo TEXT_PREORDER_BOOK_TEXT_END;?></b></p>
<?php
      if($error == true) {
        echo '<span class="errorText"><b>'.TEXT_INPUT_ERROR_INFO.'</span></b><br><br>';
      }
?>
      <div class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></div>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>  
          <td class="main" width="120"><?php echo FORM_FIELD_CUSTOMER_LASTNAME; ?></td>
          <td class="formArea_td_info"><?php echo $last_name_prompt; ?></td>
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
        <td colspan="2" class="main"><?php echo PREORDER_FINAL_EAMIL;?></td>
        </tr>
      </table><br>
      <div class="formAreaTitle"><?php echo FORM_TITLE_FRIEND_DETAILS; ?></div>
      <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>
        <td class="main" valign="top" width="120"><?php echo PREORDER_PRODUCTS_NAME;?></td>
          <td class="formArea_td_info">
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
          <td colspan="2" class="preorder_option">
          <?php 
          $p_cflag = tep_get_cflag_by_product_id($product_info['products_id']);
          $hm_option->render($belong_option['belong_to_option'], false, 0, '', '', $p_cflag);
          ?> 
          </td>
        </tr>
        <tr>
          <td class="main"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
          <td class="formArea_td_info">
<?php
if (!isset($_POST['quantity'])) $_POST['quantity'] = NULL; //del notice
if (!isset($_GET['quantity'])) $_GET['quantity'] = NULL; //del notice
            echo tep_draw_input_field('quantity', (($quantity_error == true) ? $_POST['quantity'] : $_GET['quantity']) , 'size="7" maxlength="15" class="input_text_short"');
            echo '&nbsp;&nbsp;'.PREORDER_QTY.'&nbsp;';
            if($products_weight_error == true){ 
              echo '<span class="markProductOutOfStock"><a style="color:#CC0033" href="'.tep_href_link('open.php', 'products_name='.urlencode($product_info['products_name'])).'">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</a></span>';
            }
      if ($quantity_error == true) echo '&nbsp;<span class="errorText">' . PREORDER_TEXT_REQUIRED . '</span>';
      if ($num_error == true){echo '<span id="preorder_info_message" class="errorText"><br>'.TEXT_NEED_RETENTION.'<br>'.(int)$_POST['quantity'].TEXT_ORDERS_QTY.'<a href='.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id) .'>'.TEXT_PREORDER_HERE.'</a>'.TEXT_PREORDER_FROM.'</span>' ;}
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
          <td class="formArea_td_info">
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
          $newarr = array(TEXT_DATE_MONDAY, TEXT_DATE_TUESDAY, TEXT_DATE_WENSDAY, TEXT_DATE_THIRSDAY, TEXT_DATE_FRIDAY, TEXT_DATE_STATURDAY, TEXT_DATE_SUNDAY);
    for($i=0; $i<7; $i++) {
      if ($_POST['predate'] == date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year))) {
        $check_str = 'selected'; 
      } else {
        $check_str = ''; 
      }
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'" '.$check_str.'>'.str_replace($oarr, $newarr,date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
    }
    ?>
  </select>
          <?php
          if ($predate_error == true) echo '&nbsp;<span class="errorText">' . PREORDER_TEXT_REQUIRED . '</span>';
          ?>
          </td>
        </tr>
        <?php
        if($products_weight_error == true){
        ?>
        <tr><td class="main" colspan="2" align="center"><?php echo '<span class="stockWarning">' . TEXT_WEIGHT_ERROR . $products_num . TEXT_WEIGHT_ERROR_ONE .'</span>';?></td></tr>
        <?php
        }
        ?>
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
	</td></tr></table>
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
