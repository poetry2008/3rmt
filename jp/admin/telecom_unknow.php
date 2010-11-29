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
      <td class="pageHeading">不明クレジットカードリスト</td>
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
        select *
        from telecom_unknow
        order by date_added DESC
      ";

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows);
    //echo $orders_query_raw;
    $orders_query = tep_db_query($orders_query_raw);

    while ($orders = tep_db_fetch_array($orders_query)) {

  
    echo '    <tr class="dataTableRow" >' . "\n";

?>
    
    
    <td style="border-bottom:1px solid #000000;" class="dataTableContent"><?php echo tep_datetime_short($orders['date_added']); ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['username']; ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['telno']; ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['email']; ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['money']; ?></td>
    
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
