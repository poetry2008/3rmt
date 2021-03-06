<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require('includes/classes/http_client.php');
// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        break;
      }
    }
  }

// if no shipping destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('sendto')) {
    tep_session_register('sendto');
    $sendto = $customer_default_address_id;
  } else {
// verify the selected shipping address
 
    $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $sendto . "'");
    $check_address = tep_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
      $sendto = $customer_default_address_id;
      if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (!tep_session_is_registered('cartID')) tep_session_register('cartID');
  $cartID = $cart->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
    $shipping = false;
    $sendto = false;
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
      case 'both':
        $pass = true; break;
      default:
        $pass = false; break;
    }

    $free_shipping = false;
    if ( ($pass == true) && ($order->info['subtotal'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;

      include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_shipping.php');
    }
  } else {
    $free_shipping = false;
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SHIPPING);

// process the selected shipping method
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (!tep_session_is_registered('comments')) tep_session_register('comments');

    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
  //$Bahamut = tep_db_prepare_input($_POST['Bahamut']);
  $torihikihouhou = tep_db_prepare_input($_POST['torihikihouhou']);
  $date = tep_db_prepare_input($_POST['date']);
  $hour = tep_db_prepare_input($_POST['hour']);
  $min = tep_db_prepare_input($_POST['min']);
  
  $insert_torihiki_date = $date . ' ' . $hour . ':' . $min . ':00';
  
  $error = false;
  /*
  if($Bahamut == '') {
    $error = true;
    $Bahamut_error = TEXT_ERROR_BAHAMUTO;
  } elseif(!preg_match("/^[a-zA-Z]+$/", $Bahamut)) {
    $error = true;
    $Bahamut_error = TEXT_ERROR_BAHAMUTO_EIJI;
  }
  */
  
  if($torihikihouhou == '') {
    $error = true;
    $torihikihouhou_error = TEXT_ERROR_TORIHIKIHOUHOU;
  }
  
  if($date == '') {
    $error = true;
    $date_error = TEXT_ERROR_DATE;
  }
  
  if($hour == '') {
    $error = true;
    $jikan_error = TEXT_ERROR_JIKAN;
  }
  
  if($min == '') {
    $error = true;
    $jikan_error = TEXT_ERROR_JIKAN;
  }
    
  
  if($error == false) {
    //tep_session_register('Bahamut');
    tep_session_register('torihikihouhou');
    tep_session_register('date');
    tep_session_register('hour');
    tep_session_register('min');
    tep_session_register('insert_torihiki_date');
  
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 1) ) ) $shipping = $shipping_modules->cheapest();


  $breadcrumb->add(NAVBAR_TITLE_FIRST, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_SECOND, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  
  $torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
  $torihiki_list[] = array('id' => '', 'text' => TEXT_PRESE_SELECT);
  for($i=0; $i<sizeof($torihiki_array); $i++) {
    $torihiki_list[] = array('id' => $torihiki_array[$i],
                           'text' => $torihiki_array[$i]
               );
  }
  
  //print_r($_SESSION);
  //print_r($_SESSION['cart']->contents);
  $keys = array_keys($_SESSION['cart']->contents);
  $product_ids = array();
  foreach($keys as $akey){
    $arr = explode('{', $akey);
    if (!empty($arr[0])) {
      $product_ids[] = $arr[0];
    }
  }
  //print_r($_COOKIES);
?>
<?php page_head();?>
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
  if (document.checkout_address.shipping[0]) {
    document.checkout_address.shipping[buttonSelect].checked=true;
  } else {
    document.checkout_address.shipping.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
--></script>
<script type="text/javascript" src="js/data.js"></script>
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
<?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?> 
<div class="headerNavigation"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<h2><?php echo HEADING_TITLE ; ?></h2>
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link"> 
      <tr> 
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table></td> 
        <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
        <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
        <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
            </tr> 
          </table></td> 
      </tr> 
      <tr class="box_des"> 
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PRODUCTS . '</a>'; ?></td>
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
      </tr> 
    </table>
  </td> 
</tr> 
<tr> 
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
      <tr>
        <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><div style="margin-top:5px;">' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div></td> 
        <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
      </tr> 
    </table>
  </td> 
</tr>
          <tr> 
            <td>
            <table><tr><td class="main"><b><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></b></td></tr></table>
            <table class="formArea" border="0" width="100%" cellspacing="1"> 
                <tr> 
                  <td>
          
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="box_des">
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_TORIHIKIHOUHOU; ?></td>
    <td class="main"><?php echo tep_get_torihiki_select_by_products($product_ids);//tep_draw_pull_down_menu('torihikihouhou', $torihiki_list, $torihikihouhou); ?></td>
  </tr>
<?php
if (!isset($torihikihouhou_error)) $torihikihouhou_error=NULL;

  if($torihikihouhou_error != '') {
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $torihikihouhou_error; ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main" width="30%"><?php echo TEXT_TORIHIKIKIBOUBI; ?></td>
    <td class="main" width="70%">
<?php
    $today = getdate();
      $m_num = $today['mon'];
      $d_num = $today['mday'];
      $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="date" onChange="selectDate('<?php echo $hours; ?>', '<?php echo $mimutes; ?>')">
    <option value="">希望日を選択してください</option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array('月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');
    for($i=0; $i<7; $i++) {
      //echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.strftime("%Y年%m月%d日（%a）", mktime(0,0,0,$m_num,$d_num+$i,$year)).'</option>' . "\n";
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.str_replace($oarr, $newarr, date("Y年m月d日（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
    }
    ?>
  </select>
  </td>
  </tr>
<?php
if (!isset($date_error)) $date_error=NULL;
  if($date_error != '') {
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $date_error; ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_TORIHIKIKIBOUJIKAN; ?></td>
    <td class="main">
  <select name="hour" onChange="selectHour('<?php echo $hours; ?>', '<?php echo $mimutes; ?>')">
    <option value="">--</option>
  </select>
  &nbsp;時&nbsp;
  <select name="min">
    <option value="">--</option>
  </select>
  &nbsp;分&nbsp;
  <?php echo INTRODUCE_TWENTY_FOUR_SYSTEM; ?>
  </td>
  </tr>
<?php
if (!isset($jikan_error)) $jikan_error=NULL;
  if($jikan_error != '') {
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $jikan_error; ?></td>
  </tr>
<?php
  }
?>
</table>
        </td> 
      </tr> 
    </table>
  </td> 
</tr> 
<tr> 
  <td>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
    <p class="smalltext">
      「指定した時間より早くできるなら早く来てほしい」をご指定いただきましたお客様へ<br>
      ご入金確認後、最短にて目的地へお届けにまいります。<br>
      お客様がいらっしゃらない場合は、ご指定いただきました日時へ変更させていただきます。
    </p>
    <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
  </td> 
</tr>
<tr> 
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
      <tr> 
        <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><div style="margin-top:5px;">' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div></td> 
        <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
      </tr> 
    </table>
  </td> 
</tr> 
<tr> 
  <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
</table>      
</form>
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
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
