<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/edit_preorders.php');
  require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/edit_preorders.php'); 

  if (isset($_GET['Customer_mail'])) {
    $_POST['site_id'] = isset($_GET['site_id']) ? $_GET['site_id']: 0;
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $_GET['Customer_mail'] . "' and site_id = '".$_POST['site_id']."' and is_active='1'");
    $account = tep_db_fetch_array($account_query);
    $_POST['preorder_customers_id'] = $account['customers_id'];
    $_POST['preorder_firstname'] = $account['customers_firstname']; 
    $_POST['preorder_lastname'] = $account['customers_lastname'];
    $_POST['preorder_email_address'] = $account['customers_email_address'];
    
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$account['customers_id'] . "'");
    $address = tep_db_fetch_array($address_query);
  }
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/create_preorder_process.php');

  
  $preorder_customer_id    = tep_db_prepare_input($_POST['preorder_customers_id']);
  $preorder_firstname      = tep_db_prepare_input($_POST['preorder_firstname']);
  $preorder_lastname       = tep_db_prepare_input($_POST['preorder_lastname']);
  $preorder_email_address  = tep_db_prepare_input($_POST['preorder_email_address']);
  $con_email  = tep_db_prepare_input($_POST['con_email']);
  $rak_tel  = tep_db_prepare_input($_POST['rak_tel']);
  $telephone      = isset($_POST['telephone']) ? tep_db_prepare_input($_POST['telephone']) : '';
  $fax            = tep_db_prepare_input($_POST['fax']);
  $fax = !isset($_POST['fax']) ? $account['customers_fax'] : $fax;
  $street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']) : '';
  $company        = isset($_POST['company']) ? tep_db_prepare_input($_POST['company']) : '';
  $suburb         = isset($_POST['suburb']) ? tep_db_prepare_input($_POST['suburb']) : '';
  $postcode       = isset($_POST['postcode']) ? tep_db_prepare_input($_POST['postcode']) : '';
  $city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']) : '';
  $zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']) : '';
  $state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']) : '';
  $country        = isset($_POST['country']) ? tep_db_prepare_input($_POST['country']) : '';
  $site_id        = tep_db_prepare_input($_POST['site_id']);
  $format_id      = "1";
  $size           = "1";
  $new_value      = "1";
  $error          = false; // reset error flag
  $temp_amount    = "0";
  $temp_amount    = number_format($temp_amount, 2, '.', '');
  
  $currency_text  = DEFAULT_CURRENCY . ",1";
  if(isset($_POST['Currency']) && !empty($_POST['Currency']))
  {
    $currency_text = tep_db_prepare_input($_POST['Currency']);
  }
  
  $error = false;
  
  //customer_id check
  if($preorder_customer_id == '') {
    $error = true;
  } elseif(!is_numeric($preorder_customer_id)) {
    $error = true;
  }

  if (strlen($preorder_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($preorder_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }

  if (strlen($preorder_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($preorder_email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  }

  $orders_products_list_error = false;
  if((!isset($_SESSION['create_preorder']['orders_products']) || empty($_SESSION['create_preorder']['orders_products'])) && $_GET['action'] != 'add_product'){

    $orders_products_list_error = true;
    $error = true;
  }
   
  if($error == true || !isset($_POST['fax'])) {
    require(DIR_WS_CLASSES . 'currencies.php');
    $currencies = new currencies(2);  
// #### Get Available Customers

  $query = tep_db_query("select customers_id, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " ORDER BY customers_lastname");
    $result = $query;

  
  if (tep_db_num_rows($result) > 0)
  {
    // Query Successful
    $SelectCustomerBox = "<select name='Customer'><option value=''>" . TEXT_SELECT_CUST . "</option>\n";
    while($db_Row = tep_db_fetch_array($result))
    { $SelectCustomerBox .= "<option value='" . $db_Row["customers_id"] . "'";
      if(IsSet($_GET['Customer']) and $db_Row["customers_id"]==$_GET['Customer'])
      $SelectCustomerBox .= " SELECTED ";
      $SelectCustomerBox .= ">" . $db_Row["customers_lastname"] . " , " . $db_Row["customers_firstname"] . "</option>\n";
    
    }
    
    $SelectCustomerBox .= "</select>\n";
  }
  
  $query = tep_db_query("select code, value from " . TABLE_CURRENCIES . " ORDER BY code");
  $result = $query;
  
  if (tep_db_num_rows($result) > 0)
  {
    // Query Successful
    $SelectCurrencyBox = "<select name='Currency'><option value='' SELECTED>" . TEXT_SELECT_CURRENCY . "</option>\n";
    while($db_Row = tep_db_fetch_array($result))
    { 
      $SelectCurrencyBox .= "<option value='" . $db_Row["code"] . " , " . $db_Row["value"] . "'";
        $SelectCurrencyBox .= ">" . $db_Row["code"] . "</option>\n";
    }
    
    $SelectCurrencyBox .= "</select>\n";
  }

  if(IsSet($_GET['Customer']))
  {
  $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer'] . "'");
  $account = tep_db_fetch_array($account_query);
  $customer = $account['customers_id'];
  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer'] . "'");
  $address = tep_db_fetch_array($address_query);
  } elseif (IsSet($_GET['Customer_nr']))
  {
  $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer_nr'] . "'");
  $account = tep_db_fetch_array($account_query);
  $customer = $account['customers_id'];
  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer_nr'] . "'");
  $address = tep_db_fetch_array($address_query);
  }

  $from_page = 'create_preorder_process';
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/create_preorder.php');
  
// #### Generate Page
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script type="text/javascript">
<?php //检测相应网站下的电子邮箱是否存在?>
function search_email_check(){

  var email = $("#keyword").val(); 
  email = email.replace(/\s/g,"");
  if(email == ''){

    alert('<?php echo TEXT_MUST_ENTER;?>');
    $("#keyword").focus();
  }else{
    var site_id = document.getElementsByName("site_id")[0];
    site_id = site_id.value;
    $.ajax({
        url: 'ajax.php?action=check_email_exists',      
        data: 'email='+email+'&site_id='+site_id,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {

          if(data == '1'){ 
            alert("<?php echo TEXT_EMAIL_ADDRESS_ERROR;?>");
          }else if(data == '0'){

            if(confirm('<?php echo TEXT_CREATE_CUSTOMERS_CONFIRM;?>')){

              location.href="<?php echo FILENAME_CUSTOMERS;?>?email_address="+email+"&sid="+site_id;
            }
          }else{
            document.email_check.action = 'create_preorder.php';
            document.email_check.submit();
          }  
        }
    });
  }
}

<?php //下一步提交?>
function submit_next(){

  var fax_flag = document.getElementsByName('fax_flag')[0].value;
  var fax = document.getElementsByName('fax')[0]; 
  fax.value = fax_flag;
  document.create_order.submit();  
}
$(function() {
      function format(group) {
          return group.name;
      }
      $("#keyword").autocomplete('ajax_create_order.php?action=search_email', {
        multipleSeparator: '',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format(item);
        }
      }).result(function(e, item) {
      });
});
</script>
<style type="text/css">
.number{
font-size:24px;
font-weight:bold;
width:20px;
text-align:center;
}
form{
margin:0;
padding:0;
}
.alarm_input{
width:80px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px;
  font-size:12px;
}
.log .alarm{
  display:none;
  font-size:10px;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:100px;
  *width:120px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  text-align:right;
}
.info02{
width:50px;
}
.log .action{
text-align:center;
  font-size:10px;
}
.edit_action{
  display:none;
  font-size:10px;
line-height:24px;
padding-right:5px;
}
.action a{
padding:0 3px;
}
textarea,input{
  font-size:12px;
}
textarea{
  width:100%;
}
.alarm_on{
  border:2px solid #ff8e90;
  background:#ffe6e6;
}
.clr{
clear:both;
width:100%;
height:5px;
overflow:hidden;
}
</style>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
if($belong == 'create_preorder_process.php'){

  $belong = 'create_preorder.php';
}
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->


<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><?php echo TEXT_STEP_1 ?></font></td>
      </tr>
    </table>
 <?php if (empty($preorder_customer_id)) {?> 
    <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
  $url_action = isset($_GET['oID']) ? '<input type="hidden" name="oID" value="'.$_GET['oID'].'">' : '';
  echo '<form name="email_check" action="' . $PHP_SELF . '" method="GET">' . "\n";
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>';
  echo CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" id="keyword" name="Customer_mail" size="40" value="'.$_GET['Customer_mail'].'">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="button" value="  '.CREATE_ORDER_SEARCH_BUTTON_TEXT.'  " onclick="search_email_check();">'.$url_action.'</p>' . "\n";
  echo '</form>' . "\n";
?>
<?php 
     } 
?>  
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><font color="red"><?php echo CREATE_ORDER_RED_TITLE_TEXT;?></font></td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
  </table>
  <?php 
  require(DIR_WS_INCLUDES . 'step-by-step/create_preorder_details.php');
  ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
        <td class="main" align="right"><?php echo tep_html_element_button(IMAGE_CONFIRM_NEXT,'onclick="submit_next();"'); ?></td>
      </tr>
    </table>
  </div>
  </td>
  </tr>
</table>
<!-- body_eof -->


<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->

<br>
</body>
</html>
<?php
  } else {
  $currency_array = explode(",", $currency_text);
   
  $currency = $currency_array[0];
  $currency_value = $currency_array[1];
  $insert_id = $_GET['oID'];
   
  $sql_data_array = array('orders_id'     => $insert_id,
            'customers_id'                => $preorder_customer_id,
            'customers_name'              => tep_get_fullname($preorder_firstname,$preorder_lastname),
            'customers_company'           => $company,
            'customers_street_address'    => $street_address,
            'customers_suburb'            => $suburb,
            'customers_city'              => $city,
            'customers_postcode'          => $postcode,
            'customers_state'             => $state,
            'customers_country'           => $country,
            'customers_telephone'         => $telephone,
            'customers_email_address'     => $preorder_email_address,
            'customers_address_format_id' => $format_id,
            'delivery_company'            => $company,
            'delivery_street_address'     => $street_address,
            'delivery_suburb'             => $suburb,
            'delivery_city'               => $city,
            'delivery_postcode'           => $postcode,
            'delivery_state'              => $state,
            'delivery_country'            => $country,
            'delivery_address_format_id'  => $format_id,
            'billing_name'                => tep_get_fullname($preorder_firstname,$preorder_lastname),
            'billing_company'             => $company,
            'billing_street_address'      => $street_address,
            'billing_suburb'              => $suburb,
            'billing_city'                => $city,
            'billing_postcode'            => $postcode,
            'billing_state'               => $state,
            'billing_country'             => $country,
            'billing_address_format_id'   => $format_id,
            'last_modified'               => 'now()',
            'date_purchased'              => 'now()', 
            'orders_status'               => '1',
            'currency'                    => $currency,
            'currency_value'              => $currency_value,
            'site_id'                     => $site_id,
            'is_active'                     => '1',
            'orders_wait_flag'            => '1',
            'user_added'                  => $_SESSION['user_name'],
            'user_update'                  => $_SESSION['user_name']
            ); 
  if(isset($_SESSION['create_preorder']['orders']['payment_method'])){

    $payment_method = $_SESSION['create_preorder']['orders']['payment_method'];
  } 
  $_SESSION['create_preorder']['orders'] = $sql_data_array;
  
  //insert into order total

  
  
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies;
  
  $module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
  $module_type = 'order_total';
  $ot_tax_status = false;

  if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
    $thismodules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

    reset($thismodules);
    while (list(, $value) = each($thismodules)) {
      if($value != 'ot_tax.php') {
        include(DIR_WS_LANGUAGES . $language . '/modules/' . $module_type . '/' . $value);
        include($module_directory . $value);

        $class = substr($value, 0, strrpos($value, '.'));
        $GLOBALS[$class] = new $class;
      } elseif($value == 'ot_tax.php') {
        $ot_tax_status = true;
      }
    }
  }
  
  $order_total_array = array();
  if (is_array($thismodules)) {
    reset($thismodules);
    while (list(, $value) = each($thismodules)) {
      $class = substr($value, 0, strrpos($value, '.'));
      if ($GLOBALS[$class]->enabled) {
        $GLOBALS[$class]->process();

        for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
          if (tep_not_null($GLOBALS[$class]->output[$i]['title'])) {
            $order_total_array[] = array('code' => $GLOBALS[$class]->code,
                                         'title' => $GLOBALS[$class]->output[$i]['title'],
                                         'text' => "",
                                         'value' => $GLOBALS[$class]->output[$i]['value'],
                                         'sort_order' => $GLOBALS[$class]->sort_order);
          }
        }
      }
    }
  }
  
  $order_totals = $order_total_array;
    for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
      $sql_data_array = array('orders_id'  => $insert_id,
                              'title'      => $order_totals[$i]['title'],
                              'text'       => "",
                              'value'      => $order_totals[$i]['value'], 
                              'class'      => $order_totals[$i]['code'], 
                              'sort_order' => $order_totals[$i]['sort_order']);
      $_SESSION['create_preorder']['orders_total'][$order_totals[$i]['code']] = $sql_data_array;
    }
  
    if($ot_tax_status == true) {
      include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/' . $module_type . '/ot_tax.php');
      include($module_directory . 'ot_tax.php');
      $ot_tax = new ot_tax;
      
      $sql_data_array = array('orders_id'  => $insert_id,
                              'title'      => $ot_tax->title,
                              'text'       => "",
                              'value'      => 0, 
                              'class'      => $ot_tax->code, 
                              'sort_order' => $ot_tax->sort_order);
      $_SESSION['create_preorder']['orders_total'][$ot_tax->code] = $sql_data_array;
    }
    $_SESSION['create_preorder']['customer_fax'] = $fax;
    // 2.2.2 Calculate Tax and Sub-Totals
      $RunningSubTotal = 0;
      $RunningTax = 0;

      foreach ($_SESSION['create_preorder']['orders_products'] as $pid => $order_products) {
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $RunningSubTotal += (tep_add_tax(($order_products['products_quantity'] * $order_products['final_price']), $order_products['products_tax']));
        } else {
          $RunningSubTotal += ($order_products['products_quantity'] * $order_products['final_price']);
        }
        $RunningTax += (($order_products['products_tax'] / 100) * ($order_products['products_quantity'] * $order_products['final_price']));     
      }
      
      $new_subtotal = $RunningSubTotal;
      $new_tax = $RunningTax;
      
      //subtotal
      
      $_SESSION['create_preorder']['orders_total']['ot_subtotal']['value'] = tep_insert_currency_value($new_subtotal);
      $_SESSION['create_preorder']['orders_total']['ot_subtotal']['text']  = tep_insert_currency_text($currencies->format($new_subtotal, true, $order['currency']));
      
      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_PREORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      if($plustax['cnt'] > 0) {
        $_SESSION['create_preorder']['orders_total']['ot_tax']['value'] = tep_insert_currency_value($new_tax);
        $_SESSION['create_preorder']['orders_total']['ot_tax']['text']  = tep_insert_currency_text($currencies->format($new_tax, true, $order['currency']));
      }
      
      //total
      $total_value = 0;
      foreach ($_SESSION['create_preorder']['orders_total'] as $code => $orders_total) {
        if ($code !== 'ot_total') {
          $total_value += $orders_total['value'];
        }
      }

      if($plustax['cnt'] == 0) {
        $newtotal = $total_value + $new_tax;
      } else {
        if(DISPLAY_PRICE_WITH_TAX == 'true') {
          $newtotal = $total_value - $new_tax;
        } else {
          $newtotal = $total_value;
        }
      }
    $order = $_SESSION['create_preorder']['orders'];
    $handle_fee = 0;
    $newtotal = $newtotal+$handle_fee;
    $_SESSION['create_preorder']['orders_total']['ot_total']['value'] = intval(floor($newtotal));
    $_SESSION['create_preorder']['orders_total']['ot_total']['text']  = $currencies->ot_total_format(intval(floor($newtotal)), true, $order['currency']);
    $_SESSION['create_preorder']['orders']['code_fee'] = $handle_fee;
    if(isset($payment_method)){
      $_SESSION['create_preorder']['orders']['payment_method'] = $payment_method;
    }
  
    tep_redirect(tep_href_link('edit_new_preorders.php', 'oID=' . $insert_id, 'SSL'));
  }
  require(DIR_WS_INCLUDES . 'application_bottom.php');
