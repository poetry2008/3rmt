<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  if (isset($_GET['Customer_mail'])) {
    $_POST['site_id'] = isset($_GET['site_id']) ? $_GET['site_id']: 0;
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $_GET['Customer_mail'] . "' and site_id = '".$_POST['site_id']."' and is_active='1'");
    $account = tep_db_fetch_array($account_query);
    $_POST['customers_id'] = $account['customers_id'];
    $_POST['firstname'] = $account['customers_firstname']; 
    $_POST['lastname'] = $account['customers_lastname'];
    $_POST['email_address'] = $account['customers_email_address'];
    
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$account['customers_id'] . "'");
    $address = tep_db_fetch_array($address_query);
    if (tep_db_num_rows($account_query) == 0) {
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'email_address=' . $_GET['Customer_mail'], 'SSL'));
    }  
  }
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/create_preorder_process.php');
  $cpayment = payment::getInstance((int)$_POST['site_id']);


  
  $customer_id    = tep_db_prepare_input($_POST['customers_id']);
  $payment_method = tep_db_prepare_input($_POST['payment_method']);
  $firstname      = tep_db_prepare_input($_POST['firstname']);
  $lastname       = tep_db_prepare_input($_POST['lastname']);
  $email_address  = tep_db_prepare_input($_POST['email_address']);
  $con_email  = tep_db_prepare_input($_POST['con_email']);
  $rak_tel  = tep_db_prepare_input($_POST['rak_tel']);
  $telephone      = isset($_POST['telephone']) ? tep_db_prepare_input($_POST['telephone']) : '';
  $fax            = tep_db_prepare_input($_POST['fax']);
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
  
  //Add input string check - 2006.4.14 ds-style
  $error = false;
  
  //customer_id check
  if($customer_id == '') {
    $error = true;
  } elseif(!is_numeric($customer_id)) {
    $error = true;
  }

  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }

  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  }

  if ($payment_method == '') {
    $error = true;
    $entry_payment_method_error = true;
  } else {
    $entry_payment_method_error = false;
  }
  
  $selection = $cpayment->admin_selection();
  if (!empty($_POST['payment_method'])) {
    $validateModule = $cpayment->admin_confirmation_check($_POST['payment_method']); 
    if ($validateModule['validated'] == false) {
      $selection[strtoupper($_POST['payment_method'])] = $validateModule; 
      $error = true;
    } 
  } 

  if($error == true) {
  
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
  //$customer = $account['customers_id'];
  } elseif (IsSet($_GET['Customer_nr']))
  {
  $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer_nr'] . "'");
  $account = tep_db_fetch_array($account_query);
  $customer = $account['customers_id'];
  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer_nr'] . "'");
  $address = tep_db_fetch_array($address_query);
  //$customer = $account['customers_id'];
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
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script type="text/javascript">
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

function open_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    $('#toggle_open').val('1'); 
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};


    if ($("#predate").val() != '') {
      date_info = $("#predate").val().split('-'); 
    } else {
      date_info_str = '<?php echo date('Y-m-d', time())?>';  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar",
            width:'170px',
            date: new_date
        }).render();
     
     if (rules != '') {
       month_tmp = date_info[1].substr(0, 1);
       if (month_tmp == '0') {
         month_tmp = date_info[1].substr(1);
         month_tmp = month_tmp-1;
       } else {
         month_tmp = date_info[1]-1; 
       }
       day_tmp = date_info[2].substr(0, 1);
       
       if (day_tmp == '0') {
         day_tmp = date_info[2].substr(1);
       } else {
         day_tmp = date_info[2];   
       }
       data_tmp_str = date_info[0]+'-'+month_tmp+'-'+day_tmp;
       
       calendar.set("customRenderer", {
            rules: rules,
               filterFunction: function (date, node, rules) {
                 cmp_tmp_str = date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
                 if (cmp_tmp_str == data_tmp_str) {
                   node.addClass("redtext"); 
                 }
               }
       });
     }
        var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        tmp_show_date = dtdate.format(newDate); 
        tmp_show_date_array = tmp_show_date.split('-');
        $("#predate_year").val(tmp_show_date_array[0]); 
        $("#predate_month").val(tmp_show_date_array[1]); 
        $("#predate_day").val(tmp_show_date_array[2]); 
        $("#predate").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
function hidden_payment()
{
  var idx = document.create_order.elements['payment_method'].selectedIndex; 
  var CI = document.create_order.elements['payment_method'].options[idx].value; 
  $(".rowHide").hide(); 
  $(".rowHide_"+CI).show();
}
$(function () {
  var CI = '<?php echo $payment_method;?>'; 
  if (CI != '') {
    $(".rowHide_"+CI).show();
  }
});

function is_date(dateval)
{
  var arr = new Array();
  if(dateval.indexOf("-") != -1){
    arr = dateval.toString().split("-");
  }else if(dateval.indexOf("/") != -1){
    arr = dateval.toString().split("/");
  }else{
    return false;
  }
  if(arr[0].length==4){
    var date = new Date(arr[0],arr[1]-1,arr[2]);
    if(date.getFullYear()==arr[0] && date.getMonth()==arr[1]-1 && date.getDate()==arr[2]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[1]-1,arr[0]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[1]-1 && date.getDate()==arr[0]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[0]-1,arr[1]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[0]-1 && date.getDate()==arr[1]) {
      return true;
    }
  }
 
  return false;
}
function change_predate_date() {
  predate_str = $("#predate_year").val()+"-"+$("#predate_month").val()+"-"+$("#predate_day").val(); 
  if (!is_date(predate_str)) {
    alert('<?php echo ERROR_INPUT_RIGHT_DATE;?>'); 
  } else {
    $("#predate").val(predate_str); 
  }
}
</script>
<style type="text/css">
.yui3-skin-sam .redtext {
    color:#0066CC;
}

.yui3-skin-sam input {
  float:left;
}
a.dpicker {
	width: 16px;
	height: 16px;
	border: none;
	color: #fff;
	padding: 0;
	margin: 0;
	overflow: hidden;
        display:block;	
        cursor: pointer;
	background: url(./includes/calendar.png) no-repeat; 
	float:left;
}
#new_yui3{ 
	position:absolute;
}
.popup-calendar {
top:20px;
}
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
.popup-calendar-wrapper{
float:left;
}
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top">
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><b><?php echo TEXT_STEP_1 ?></b></font></td>
      </tr>
    </table>
 <?php if (empty($customer_id)) {?> 
    <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
  echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>';
  echo CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" id="keyword" name="Customer_mail" size="40" value="'.$_GET['Customer_mail'].'">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="  '.CREATE_ORDER_SEARCH_BUTTON_TEXT.'  "></p>' . "\n";
  echo '</form>' . "\n";
?>
  <?php }?> 
  <?php echo tep_draw_form('create_order', 'create_preorder_process.php', '', 'post', '', '') . tep_draw_hidden_field('customers_id', $account->customers_id); ?>
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
        <td class="main" align="right"><?php echo tep_html_element_submit(IMAGE_CONFIRM); ?></td>
      </tr>
    </table>
  </form>
  </td>
  </tr>
</table>
<!-- body_eof //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<br>
</body>
</html>
<?php
  //Add input string check - OK insert order data - d2006.4.14 ds-style
  } else {
  unset($_SESSION['create_preorder']); 
  $currency_array = explode(",", $currency_text);
   
  $currency = $currency_array[0];
  $currency_value = $currency_array[1];
  //$insert_id = date("Ymd") . '-' . date("His") . '00';
  $insert_id = date("Ymd") . '-' . date("His") . tep_get_preorder_end_num();
  
  $payment_method_info = payment::changeRomaji($payment_method, PAYMENT_LIST_TYPE_HAIJI);
  
  $sql_data_array = array('orders_id'     => $insert_id,
            'customers_id'                => $customer_id,
            'customers_name'              => tep_get_fullname($firstname,$lastname),
            'customers_company'           => $company,
            'customers_street_address'    => $street_address,
            'customers_suburb'            => $suburb,
            'customers_city'              => $city,
            'customers_postcode'          => $postcode,
            'customers_state'             => $state,
            'customers_country'           => $country,
            'customers_telephone'         => $telephone,
            'customers_email_address'     => $email_address,
            'customers_address_format_id' => $format_id,
            'delivery_company'            => $company,
            'delivery_street_address'     => $street_address,
            'delivery_suburb'             => $suburb,
            'delivery_city'               => $city,
            'delivery_postcode'           => $postcode,
            'delivery_state'              => $state,
            'delivery_country'            => $country,
            'delivery_address_format_id'  => $format_id,
            'billing_name'                => tep_get_fullname($firstname,$lastname),
            'billing_company'             => $company,
            'billing_street_address'      => $street_address,
            'billing_suburb'              => $suburb,
            'billing_city'                => $city,
            'billing_postcode'            => $postcode,
            'billing_state'               => $state,
            'billing_country'             => $country,
            'billing_address_format_id'   => $format_id,
            'date_purchased'              => 'now()', 
            'orders_status'               => '1',
            'currency'                    => $currency,
            'currency_value'              => $currency_value,
            'payment_method'              => $payment_method_info,
            'site_id'                     => $site_id,
            'is_active'                     => '1',
            'orders_wait_flag'            => '1'
            ); 
   
  $cpayment->admin_add_additional_info($sql_data_array, $_POST['payment_method']); 

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
  
    tep_redirect(tep_href_link('edit_new_preorders.php', 'oID=' . $insert_id . '&action=add_product&step=1', 'SSL'));
  }
  require(DIR_WS_INCLUDES . 'application_bottom.php');
