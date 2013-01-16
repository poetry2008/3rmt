<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/create_preorder.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/edit_preorders.php');
  require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/edit_preorders.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);
  if (IsSet($_GET['cmail'])) {
    $cmail_arr = explode('|||', $_GET['cmail']);
    $_GET['Customer_mail'] = $cmail_arr[0]; 
    $_GET['site_id'] = $cmail_arr[1];
  }
    
  if (IsSet($_GET['Customer'])) {
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer'] . "'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer'] . "'");
    $address = tep_db_fetch_array($address_query);
  } elseif (IsSet($_GET['Customer_nr'])) {
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['Customer_nr'] . "'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $_GET['Customer_nr'] . "'");
    $address = tep_db_fetch_array($address_query);
  } elseif (IsSet($_GET['Customer_mail'])) {
    $site_id = isset($_GET['site_id']) ? $_GET['site_id']: 0;
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $_GET['Customer_mail'] . "' and site_id = '".$site_id."' and is_active='1'");
    $account = tep_db_fetch_array($account_query);
    $customer = $account['customers_id'];
    $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer . "'");
    $address = tep_db_fetch_array($address_query);
    if (tep_db_num_rows($account_query) == 0) {
      $url_action = isset($_GET['oID']) ? '&oID='.$_GET['oID'] : '';
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'email_address=' . $_GET['Customer_mail'].$url_action, 'SSL'));
    }
  }
  // 2. ADD A PRODUCT ###############################################################################################
  if(isset($_GET['action']) && $_GET['action'] == 'add_product'){
    $step = $_POST['step'];  
    if($step == 5)
    {
      // 2.1 GET ORDER INFO #####
      
      //$oID = tep_db_prepare_input($_SESSION['create_preorder']['orders']['orders_id']);
      $oID = tep_db_prepare_input($_GET['oID']);
      //$order = $_SESSION['create_preorder']['orders']; 
      $currency_text  = DEFAULT_CURRENCY . ",1";
      $currency_array = explode(",", $currency_text);
      $currency = $currency_array[0];
      $currency_value = $currency_array[1];
      $order = array('delivery_country'=>'',
                     'delivery_state'=>'', 
                     'currency'=>$currency, 
                     );

      if (isset($_POST['add_product_options'])) {
        $add_product_options = $_POST['add_product_options'];
      }
      $AddedOptionsPrice = 0;

      $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>", "'", "\"");
      // 2.1.1 Get Product Attribute Info
      foreach ($_POST as $op_key => $op_value) {
        $op_pos = substr($op_key, 0, 3);
        if ($op_pos == 'op_') {
            $op_tmp_value = str_replace(' ', '', $op_value);
            $op_tmp_value = str_replace('　', '', $op_value);
            if ($op_tmp_value == '') {
              continue; 
            }
            $op_info_array = explode('_', $op_key); 
            $op_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_info_array[1]."' and id = '".$op_info_array[3]."'"); 
            $op_item_res = tep_db_fetch_array($op_item_query);
            if ($op_item_res) {
              if ($op_item_res['type'] == 'radio') {
                    $o_option_array = @unserialize($op_item_res['option']);
                    if (!empty($o_option_array['radio_image'])) {
                      foreach ($o_option_array['radio_image'] as $or_key => $or_value) {
                        if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                          $AddedOptionsPrice += $or_value['money'];
                          break;
                        }
                      }
                    }
              } else {
                    $AddedOptionsPrice += $op_item_res['price'];
              }
            }
        }
      }
      // 2.1.2 Get Product Info
      $InfoQuery = "
        select p.products_model, 
               p.products_price, 
               pd.products_name, 
               p.products_tax_class_id, 
               p.products_small_sum,
               p.products_price_offset
        from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id 
        where p.products_id='$add_product_products_id' 
          and pd.site_id = '0'
          and pd.language_id = '" . (int)$languages_id . "'";
      $result = tep_db_query($InfoQuery);

      $row = tep_db_fetch_array($result);
      extract($row, EXTR_PREFIX_ALL, "p");
      
      // 特価を適用
      $p_products_price =
        tep_get_bflag_by_product_id($add_product_products_id)?0-$_POST['add_product_price']:$_POST['add_product_price'];

      // Following functions are defined at the bottom of this file
      $CountryID = tep_get_country_id($order["delivery_country"]);
      $ZoneID = tep_get_zone_id($CountryID, $order["delivery_state"]);
      
      $ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);
      
      // 2.2 UPDATE ORDER #####
      foreach ($_SESSION['create_preorder']['orders_products'] as $pkey => $pvalue) {
        if ($pvalue['orders_id'] != $oID) {
          unset($_SESSION['create_preorder']['orders_products'][$pkey]); 
        }
      }
      $_SESSION['create_preorder']['orders_products'][$add_product_products_id] = array(
        'orders_id' => $oID,
        'products_id' => $add_product_products_id,
        'products_model' => $p_products_model,
        'products_name' => str_replace("'", "&#39;", $p_products_name),
        'products_price' => $p_products_price,
        'final_price' => 0,
        'products_tax' => $ProductsTax,
        'site_id' => tep_get_pre_site_id_by_orders_id($oID),
        'products_rate' => tep_get_products_rate($add_product_products_id),
        'products_quantity' => $add_product_quantity
      );

      
      unset($_SESSION['create_preorder']['orders_products_attributes'][$add_product_products_id]); 
      foreach($_POST as $op_i_key => $op_i_value) {
        $op_pos = substr($op_i_key, 0, 3);
        if ($op_pos == 'op_') {
          $op_i_tmp_value = str_replace(' ', '', $op_i_value);
          $op_i_tmp_value = str_replace('　', '', $op_i_value);
          if ($op_i_tmp_value == '') {
            continue; 
          }
          $i_op_array = explode('_', $op_i_key);
          $ioption_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$i_op_array[1]."' and id = '".$i_op_array[3]."'");
          $ioption_item_res = tep_db_fetch_array($ioption_item_query); 
          if ($ioption_item_res) {
            $input_option_array = array('title' => $ioption_item_res['front_title'], 'value' => str_replace("<BR>", "<br>", stripslashes($op_i_value))); 
            $op_price = 0; 
            if ($ioption_item_res['type'] == 'radio') {
              $io_option_array = @unserialize($ioption_item_res['option']);
              if (!empty($io_option_array['radio_image'])) {
                foreach ($io_option_array['radio_image'] as $ior_key => $ior_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ior_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_i_value))))) {
                    $op_price = $ior_value['money']; 
                    break; 
                  }
                }
              }
            } else {
              $op_price = $ioption_item_res['price']; 
            }
           $_SESSION['create_preorder']['orders_products_attributes'][$add_product_products_id][] = array(
              'orders_id' => $oID,
              'orders_products_id'      => $new_product_id,
              'options_values_price'    => 0,
              'option_group_id'           => $ioption_item_res['group_id'],
              'option_item_id'           => $ioption_item_res['id'],
              'option_info'           => $input_option_array,
            ); 
          }
        }
      }

      tep_redirect(tep_href_link("create_preorder.php", tep_get_all_get_params(array('action'))));
    }
  }
// #### Generate Page
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo CREATE_ORDER_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script type="text/javascript">
function submit_next(){

  var fax_flag = document.getElementsByName('fax_flag')[0].value;
  var fax = document.getElementsByName('fax')[0]; 
  fax.value = fax_flag;
  document.create_order.submit();  
}
function submit_check(){
  var qty = document.getElementById('add_product_quantity').value;

  var products_id = document.getElementById('add_product_products_id').value;

  $.ajax({
    dataType: 'text',
    url: 'ajax_orders_weight.php?action=edit_preorder',
    data: 'qty='+qty+'&products_id='+products_id, 
    type:'POST',
    async: false,
    success: function(data) {
      if(data != ''){

        if(confirm(data)){

          document.edit_order_id.submit();
        }      
      }else{

         document.edit_order_id.submit(); 
      }
    }
  });
    
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
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
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
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
  <div class="compatible">
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><b><?php echo CREATE_ORDER_STEP_ONE;?></b></font></td>
      </tr>
    </table>
  <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
  $url_action = isset($_GET['oID']) ? '<input type="hidden" name="oID" value="'.$_GET['oID'].'">' : '';
  echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>';
  echo CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" id="keyword" name="Customer_mail" size="40" value="'.$_GET['Customer_mail'].'">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="  '.CREATE_ORDER_SEARCH_BUTTON_TEXT.'  ">'.$url_action.'</p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_CREATE; ?></td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
  </table>
<?php
  //変数挿入
    $customer_id    = isset($account['customers_id'])           ? $account['customers_id']:'';
    $firstname      = isset($account['customers_firstname'])    ? $account['customers_firstname']:'';
    $lastname       = isset($account['customers_lastname'])     ? $account['customers_lastname']:'';
    $email_address  = isset($account['customers_email_address'])? $account['customers_email_address']:'';
    $telephone      = isset($account['customers_telephone'])    ? $account['customers_telephone']:'';
    $fax            = isset($account['customers_fax'])          ? $account['customers_fax']:'';
    $zone_id        = isset($account['entry_zone_id'])          ? $account['entry_zone_id']:'';
    $site_id        = isset($account['site_id'])                ? $account['site_id']:'';

    $street_address = isset($address['entry_street_address'])   ? $address['entry_street_address']:'';
    $company        = isset($address['entry_company'])          ? $address['entry_company']:'';
    $suburb         = isset($address['entry_suburb'])           ? $address['entry_suburb']:'';
    $postcode       = isset($address['entry_postcode'])         ? $address['entry_postcode']:'';
    $city           = isset($address['entry_city'])             ? $address['entry_city']:'';
    $state          = isset($address['entry_zone_id'])          ? tep_get_zone_name($address['entry_zone_id']):'';
    $country        = isset($address['entry_country_id'])       ? tep_get_country_name($address['entry_country_id']):'';
    $url_action = isset($_GET['oID']) ? '?oID='.$_GET['oID'] : '';
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php echo tep_draw_form('create_order', 'create_preorder_process.php'.$url_action, '', 'post', '', '') . tep_draw_hidden_field('customers_id', isset($account['customers_id'])?$account['customers_id']:''); ?>
<?php
/*
   $Id$
  
*/

    tep_draw_hidden_field($customer_id);    
?>
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_CORRECT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID; ?></td>
                <td class="main">&nbsp;<?php  echo tep_draw_hidden_field('customers_id', $customer_id) . $customer_id; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE?><?php if (isset($entry_firstname_error) && $entry_firstname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE?><?php if (isset($entry_lastname_error) && $entry_lastname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>'; ?><?php if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_SITE; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_SITE; ?>:</td>
                <td class="main">&nbsp;<?php echo isset($account) && $account?( '<font color="#FF0000"><b>'.tep_get_site_romaji_by_id($account['site_id']).'</b></font>'.tep_draw_hidden_field('site_id', $account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT); ?></td>
              </tr>
            </table></td>
        </tr>
      </table><input type="hidden" name="fax" value="<?php echo $fax;?>"></td>
  </tr>
  <?php
  if (ACCOUNT_COMPANY == 'true' && false) {
?>
  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_COMPANY; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('company', $company) . '&nbsp;' . ENTRY_COMPANY_TEXT; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  }
?> 
  </form>
  <tr><td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td class="pageHeading"><?php echo ADDING_TITLE; ?>:</td>
    </tr>
  </table>
  </td></tr>
<?php
if(isset($_GET['oID']) && $_GET['oID'] != ''){
  $oID = $_GET['oID'];
}else{
  $oID = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
  unset($_SESSION['create_preorder']);
}
$PHP_SELF = 'create_preorder.php';
if(isset($Customer_mail) && $Customer_mail != '' && isset($site_id) && $site_id != ''){
  $param_str = "&Customer_mail=$Customer_mail&site_id=$site_id";
}
//start
?>
<?php
if(isset($_SESSION['create_preorder']['orders_products']) && !empty($_SESSION['create_preorder']['orders_products']) && isset($_GET['oID'])){
?>
<tr><td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
</tr>
<tr>
<td class="formAreaTitle"><?php echo ORDERS_PRODUCTS;?></td>
</tr>
</table>
</td></tr>
<tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><input type="hidden" name="oID" value="<?php echo $oID;?>">
  
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr style="background-color: #e1f9fe;">
            <td class="dataTableContent" colspan="2" width="35%">&nbsp;<?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_CURRENICY;?></td>
            <td class="dataTableContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER;?></td>
            </tr>

          <?php
          $currency_text  = DEFAULT_CURRENCY . ",1";
          $currency_array = explode(",", $currency_text);
          $currency = $currency_array[0];
          $currency_value = $currency_array[1];
          foreach ($_SESSION['create_preorder']['orders_products'] as $new_products_temp_add) {
            $orders_products_id = ''; 
            $RowStyle = "dataTableContent";
            $new_products_temp_add['products_quantity'] = isset($_SESSION['preorder_products'][$_GET['oID']]['qty']) ? $_SESSION['preorder_products'][$_GET['oID']]['qty'] : $new_products_temp_add['products_quantity'];
            $porducts_qty = $new_products_temp_add['products_quantity'];
            echo '<tr>' . "\n" .
                 '<td class="' . $RowStyle . '" align="left" valign="top" width="20">&nbsp;'
                 .$porducts_qty."&nbsp;x</td>\n" .  '<td class="' . $RowStyle . '">' . $new_products_temp_add['products_name'] . "\n"; 
            // Has Attributes?
            if (sizeof($_SESSION['create_preorder']['orders_products_attributes']) > 0) { 
              $orders_products_attributes_array = $_SESSION['create_preorder']['orders_products_attributes'][$new_products_temp_add['products_id']];
              for ($j=0; $j<sizeof($orders_products_attributes_array); $j++) {
                echo '<div class="order_option_list"><small>&nbsp;<i><div
                  class="order_option_info"><div class="order_option_title"> - ' .str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($orders_products_attributes_array[$j]['option_info']['title'], array("'"=>"&quot;"))) . ': ' . 
                  '</div><div class="order_option_value">' . 
                  str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($orders_products_attributes_array[$j]['option_info']['value'], array("'"=>"&quot;"))); 
                echo '</div></div>';
                echo '<div class="order_option_price">';
                echo isset($_SESSION['preorder_products'][$_GET['oID']]['attr'][$j]) ? $_SESSION['preorder_products'][$_GET['oID']]['attr'][$j] : (int)$orders_products_attributes_array[$j]['options_values_price'];
                echo TEXT_MONEY_SYMBOL;
                echo '</div>';
                echo '</i></small></div>';
              }
            }

                echo '</td>' . "\n" .
                     '<td class="' . $RowStyle . '">' . $new_products_temp_add['products_model'] . '</td>' . "\n" .
                     '<td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($new_products_temp_add['products_tax']) . '%</td>' . "\n";
            $new_products_temp_add['products_price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['price']) ? $_SESSION['preorder_products'][$_GET['oID']]['price'] : $new_products_temp_add['products_price'];
            if($new_products_temp_add['products_price'] < 0){
              $orders_products_price = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['products_price']), 2))).'</font>'; 
            }else{
              $orders_products_price = $currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['products_price']), 2))); 
            }
            echo '<td class="'.$RowStyle.'" align="right">'.str_replace(TEXT_MONEY_SYMBOL,'',$orders_products_price).TEXT_MONEY_SYMBOL.'</td>'; 
            $new_products_temp_add['final_price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['final_price']) ? $_SESSION['preorder_products'][$_GET['oID']]['final_price'] : $new_products_temp_add['final_price'];
            if($new_products_temp_add['final_price'] < 0){
              $orders_products_tax_price = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['final_price']),2))).'</font>'; 
            }else{
              $orders_products_tax_price = $currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['final_price']),2))); 
            }
                echo '<td class="' . $RowStyle . '" align="right">' .
                     str_replace(TEXT_MONEY_SYMBOL,'',$orders_products_tax_price).TEXT_MONEY_SYMBOL ."\n" . '</td>' . "\n" . 
                     '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']), true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']), true, $currency, $currency_value);
            }
            echo '</div></td>' . "\n" . 
              '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($new_products_temp_add['final_price'] * $new_products_temp_add['products_quantity'], true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($new_products_temp_add['final_price'] * $new_products_temp_add['products_quantity'], true, $currency, $currency_value);
            }
            echo '</div></td>' . "\n" . 
                 '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]"><b>';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']) * $new_products_temp_add['products_quantity'], true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']) * $new_products_temp_add['products_quantity'], true, $currency, $currency_value);
            }
            echo '</b></div></td>' . "\n" . 
                 '</tr>' . "\n";
          }
          ?>
        </table>
        </td>
        </tr>     
        </table>
        </td>
        </tr>
<?php
}
if(!isset($_SESSION['create_preorder']['orders_products']) || empty($_SESSION['create_preorder']['orders_products']) || !isset($_GET['oID'])){
?> 
        <tr>
        <td width="100%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="formAreaTitle"><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
            </tr>
          </table>
        </td>
      </tr>
<?php
  // ############################################################################
  //   Get List of All Products
  // ############################################################################

    $result = tep_db_query("
        SELECT products_name, 
               p.products_id, 
               cd.categories_name, 
               ptc.categories_id 
        FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id 
        where pd.language_id = '" . (int)$languages_id . "' 
          and cd.site_id = '0'
          and pd.site_id = '0'
        ORDER BY categories_name");
    while($row = tep_db_fetch_array($result))
    {
      extract($row,EXTR_PREFIX_ALL,"db");
      $ProductList[$db_categories_id][$db_products_id] = $db_products_name;
      $CategoryList[$db_categories_id] = $db_categories_name;
      $LastCategory = $db_categories_name;
    }
    
    // ksort($ProductList);
    
    $LastOptionTag = "";
    $ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
    $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
    foreach($ProductList as $Category => $Products)
    {
      $ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
      $ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
      asort($Products);
      foreach($Products as $Product_ID => $Product_Name)
      {
        $ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
      }
      
      if($Category != $LastCategory)
      {
        $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
        $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
      }
    }
  
  
  // ############################################################################
  //   Add Products Steps
  // ############################################################################
  
    print "<tr><td><table border='0' width='100%' class='option_box_space' cellspacing='1' cellpadding='2'>\n";
    
    // Set Defaults
      if(!IsSet($add_product_categories_id))
      $add_product_categories_id = 0;

      if(!IsSet($add_product_products_id))
      $add_product_products_id = 0;
    
    // Step 1: Choose Category
      print "<tr>\n";
      print "<td class='dataTableContent' width='70'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 1:</b></td>\n";
      print "<td class='dataTableContent' valign='top'>";
      echo "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST'>";
      echo "<table>";
      echo "<tr>";
      print '<td width="150">';
      print ADDPRODUCT_TEXT_STEP1;
      print '</td>';
      print '<td>';
      echo ' ' . tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
      print "<input type='hidden' name='step' value='2'>";
      print '<td></tr>';
      print '</table>';
      print "</form>";
      print "</td>\n";
      print "<td class='dataTableContent'>";
      if($orders_products_list_error == true){
        print "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>".ORDERS_PRODUCT_ERROR."</font>";
      }
      echo "</td>\n";
      print "</tr>\n";

    // Step 2: Choose Product
    if(($step > 1) && ($add_product_categories_id > 0))
    {
      print "<tr>\n";
      print "<td class='dataTableContent'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 2: </b></td>\n";
      print "<td class='dataTableContent' valign='top'>";
      echo "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST'>";
      print "<table>";
      print "<tr><td width='150'>";
      print ADDPRODUCT_TEXT_STEP2."</td>";
      print "<td>";
      print "<select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
      $ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
      asort($ProductList[$add_product_categories_id]);
      foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
      {
      $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
      }
      $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
      print $ProductOptions;
      print "</select>\n";
      print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      print "<input type='hidden' name='step' value='3'>\n";
      print "<input type='hidden' name='cstep' value='1'>\n";
      print "</td>";
      print "</tr>";
      print "</table>";
      print "</form>";
      print "</td>\n";
      print "<td class='dataTableContent' align='right'>&nbsp;</td>\n";
      print "</tr>\n";
    }
    require('option/HM_Option.php');
    require('option/HM_Option_Group.php');
    $hm_option = new HM_Option();
    
    if (($step == 3) && ($add_product_products_id > 0) && isset($_POST['action_process'])) {
      if (!$hm_option->check()) {
        $step = 4; 
      }
    }
    // Step 3: Choose Options
    if(($step > 2) && ($add_product_products_id > 0))
    {
      $option_product_raw = tep_db_query("select products_cflag, belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'"); 
      $option_product = tep_db_fetch_array($option_product_raw); 
      if(!$hm_option->admin_whether_show($option_product['belong_to_option'], 1, $option_product['products_cflag']))
      {
        print "<tr>\n"; 
        print "<td class=\"dataTableContent\" valign='top'>&nbsp;<b>".ADDPRODUCT_TEXT_STEP." 3: </b></td>\n"; 
        print "<td class=\"dataTableContent\" valign='top' colspan='2'><i>".ADDPRODUCT_TEXT_OPTIONS_NOTEXIST."</i></td>\n"; 
        print "</tr>\n"; 
        $step = 4; 
      }
      else
      {
        
      
        $p_cflag = tep_get_cflag_by_product_id($add_product_products_id);
        print "<tr>";
        print "<td class='option_title_space' valign='top'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td><td class='dataTableContent' valign='top'>";
        print "<div class=\"pro_option\">"; 
        print "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST' name='aform'>\n";
        
        print $hm_option->render($option_product['belong_to_option'], false, 0, '', '', $p_cflag); 
        
        print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
        print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
        print "<input type='hidden' name='step' value='3'>";
        print "<input type='hidden' name='action_process' value='1'>";
        print "</form>";
        print "</div>"; 
        print "</td>";
        print "</tr>\n";
        print "<tr><td colspan='3' align='right'><input type='button' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "' onclick='document.forms.aform.submit();'>";
        print "</td>\n";
        print "</tr>\n";
      }

    }

    // Step 4: Confirm
    if($step > 3)
    {
      $products_query = tep_db_query("select products_price from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'");
      $products_array = tep_db_fetch_array($products_query);
      tep_db_free_result($products_query);
      echo "<tr><form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST' id='edit_order_id' name='edit_order_id'>\n";
      echo "<td class='dataTableContent'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP .  " 4: </b></td>";
      $products_num = isset($_POST['add_product_quantity']) ? $_POST['add_product_quantity'] : 1;
      $products_price = isset($_POST['add_product_price']) ? $_POST['add_product_price'] : 0;
      echo '<td class="dataTableContent">&nbsp;' .
        ADDPRODUCT_TEXT_CONFIRM_QUANTITY . '<input id="add_product_quantity" name="add_product_quantity" size="2" value="'.$products_num.'" onkeyup="clearLibNum(this);">&nbsp;'.EDIT_ORDERS_NUM_UNIT.'<input type="hidden" name="add_product_price" id="add_product_price" size="4" value="0">'; 
      echo '</td>';
      echo '<td class="dataTableContent" align="right"><input type="button" value="' . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . '" onclick="submit_check();">';
       
      foreach ($_POST as $op_key => $op_value) {
        $op_pos = substr($op_key, 0, 3);
        if ($op_pos == 'op_') {
          echo "<input type='hidden' name='".$op_key."' value='".tep_parse_input_field_data(stripslashes($op_value), array("'" => "&quot;"))."'>"; 
        }
      }
      echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      echo "<input type='hidden' id='add_product_products_id' name='add_product_products_id' value='$add_product_products_id'>";
      echo "<input type='hidden' name='step' value='5'>";
      echo "</td>\n";
      echo "</form></tr>\n";
    }
    
    echo "</table></td></tr>\n";
}
//end
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" nowrap valign="top">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<textarea name='fax_flag' style='width:400px;height:42px;*height:40px;'><?php echo $fax;?></textarea>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
              </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_ONE;?></td>
        </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<b><?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_TWO;?></b></td>
        </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
</table>

  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><?php echo tep_html_element_button(IMAGE_CONFIRM_NEXT,'onclick="submit_next();"'); ?></td>
      </tr>
    </table>
  </div>
  </div>
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
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
