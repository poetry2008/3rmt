<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/create_preorder.php');
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
      tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'email_address=' . $_GET['Customer_mail'], 'SSL'));
    }
  }
// #### Generate Page
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/datePicker.js"></script>
<script type="text/javascript">
function hidden_payment()
{
  var idx = document.create_order.elements['payment_method'].selectedIndex;
  var CI= document.create_order.elements['payment_method'].options[idx].value;
  
  if (CI == 'コンビニ決済') {
    document.getElementById('copass1').style.display = ""; 
  } else {
    document.getElementById('copass1').style.display = "none"; 
  }
  
  if (CI == '楽天銀行') {
    document.getElementById('rakpass1').style.display = ""; 
  } else {
    document.getElementById('rakpass1').style.display = "none"; 
  }
}
$(function() {
$.datePicker.setDateFormat('ymd', '-');
$('#predate').datePicker();
});
</script>
<style type="text/css">
a.date-picker{
display:block;
float:none;
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
        <td class="main"><font color="#ffffff"><b><?php echo CREATE_ORDER_STEP_ONE;?></b></font></td>
      </tr>
    </table>
  <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
  echo '<form action="' . $PHP_SELF . '" method="GET">' . "\n";
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>';
  echo 'メールアドレス:&nbsp;<input type="text" name="Customer_mail" size="40">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="  検索  "></p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <?php echo tep_draw_form('create_order', 'create_preorder_process.php', '', 'post', '', '') . tep_draw_hidden_field('customers_id', isset($account['customers_id'])?$account['customers_id']:''); ?>
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
?>
<?php
/*
   $Id$
  
   3rmt over
*/

    tep_draw_hidden_field($customer_id);    
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
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
      </table></td>
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

<?php
  $payment_text = tep_get_list_pre_payment();
  $payment_array = explode("\n", $payment_text);
  $payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
  for($pnum = 0; $pnum<sizeof($payment_array); $pnum++) {
    $payment_list[] = array('id' => $payment_array[$pnum], 'text' => $payment_array[$pnum]); 
  }
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_PAYMENT_TITLE;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main">
          <table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_PAYMENT_TITLE;?>:</td>
                <td class="main">
                <?php echo tep_draw_pull_down_menu('payment_method', $payment_list, '', 'onchange="hidden_payment()"');?>  
                </td>
              </tr>
              <tr id="copass1" style="display:none;">
                <td class="main">
                <?php echo CREATE_ORDER_PC_TEXT;?>  
                </td>
                <td class="main">
                <?php echo tep_draw_input_field('con_email', '');?> 
                </td>
              </tr>
              <tr id="rakpass1" style="display:none;">
                <td class="main">
                <?php echo CREATE_ORDER_TEL_TEXT;?>  
                </td>
                <td class="main">
                <?php echo tep_draw_input_field('rak_tel', '');?> 
                </td>
              </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_PREORDER_PREDATE;?></td>
  </tr>
  <tr>
    <td class="main">
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main">
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
            &nbsp;<?php echo CREATE_PREORDER_PREDATE;?>: 
            </td>
            <td class="main">
            <?php echo tep_draw_input_field('predate', '', 'id="predate"');?> 
            </td>
          </tr>
        </table>
        </td>
      </tr>
    </table> 
    </td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('fax', $fax, 'size="60" maxlength="255"'); ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
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
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><?php echo tep_html_element_submit(IMAGE_CONFIRM); ?></td>
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
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
