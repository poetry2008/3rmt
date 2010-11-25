<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');

  $currencies          = new currencies();

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission >= 10) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
    // edit start
?>

<?php
  // edit over
  } else {
  // list start
?>
    <tr>
      <td width="100%">
  
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText"></td>
      <td align="right"></td>
    </tr>
  </table>
      </td>
    </tr>
    <tr>
      <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent">時間</td>
      <td class="dataTableHeadingContent">氏名</td>
      <td class="dataTableHeadingContent">電話</td>
      <td class="dataTableHeadingContent">メールアドレス</td>
      <td class="dataTableHeadingContent">金額</td>
    </tr>
<?php
      $orders_query_raw = "
        select distinct o.orders_status as orders_status_id, 
               o.orders_id, 
               o.torihiki_date, 
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status_name, 
               o.orders_status_image,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.orders_comment,
              o.telecom_name,
              o.telecom_tel,
              o.telecom_email,
              o.telecom_money,
              o.site_id
         from " . TABLE_ORDERS . " o
         where 
          o.language_id = '" . $languages_id . "' 
          and (
               (o.telecom_name = '' or o.telecom_name is null)
            or (o.telecom_tel = '' or o.telecom_tel is null)
            or (o.telecom_money = '' or o.telecom_money is null)
            or (o.telecom_email = '' or o.telecom_email is null)
          )
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          and o.payment_method = 'クレジットカード決済'
          and o.torihiki_date > '2010-11-24 21:00:00'
         order by o.torihiki_date DESC
      ";

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows);
    //echo $orders_query_raw;
    $orders_query = tep_db_query($orders_query_raw);
    $allorders    = $allorders_ids = array();
    while ($orders = tep_db_fetch_array($orders_query)) {
      if (!isset($orders['site_id'])) {
        $orders = tep_db_fetch_array(tep_db_query("
          select *
          from ".TABLE_ORDERS." o
          where orders_id='".$orders['orders_id']."'
        "));
      }
      $allorders[] = $orders;
      if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
        $oInfo = new objectInfo($orders);
      }
  
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" >' . "\n";

?>
    

    <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo tep_datetime_short($orders['date_purchased']); ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['telecom_name']; ?></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['telecom_tel']; ?></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['telecom_email']; ?></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['telecom_money']; ?></font></td>
    </tr>
<?php }?>
  </table>
  <!-- display add end-->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="5">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
            <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
    </tr>
  </table>
      </td>
    </tr>
<?php
  }
?>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
