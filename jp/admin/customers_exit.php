<?php
/*
  $Id$
*/

  require('includes/application_top.php');
switch ($_GET['action'])  {
case "delete":
$array_count = count($_POST["ck"]);
for($i=0;$i<$array_count;$i++){
$delete_sql = "delete from ".TABLE_CUSTOMERS_EXIT." where customers_id='".$_POST['ck'][$i]."'";
tep_db_query($delete_sql);
}
tep_redirect(tep_href_link(FILENAME_CUSTOMERS_EXIT,tep_get_all_get_params(array('cID','action'))));

	break;
}
      ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo HEADING_TITLE; ?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="Javascript" type="text/javascript">
function checkall(){
var ckall= document.getElementById("ckall");

var ele = document.getElementsByName("ck[]");

for (var i=0;i<ele.length;i++){
var e = ele[i];
if(ckall.checked==true){
e.checked = true; 

}else{
 e.checked = false; 
}
}
}
</script>


</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><?php echo tep_draw_form('search', FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(), 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right"><?php //echo tep_draw_hidden_field('d', '龠');?><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search'); ?>
      <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
            <br><?php echo CUSTOMER_SEARCH_READ_TITLE;?> 
      </td>
          </form></tr>
        </table></td>
      </tr>
      <tr><td>
        <?php tep_site_filter(FILENAME_CUSTOMERS_EXIT);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
<form action="customers_exit.php?action=delete" method="post" name="form1">

          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><input type="checkbox" id="ckall" onclick="checkall();"></td>
                <td class="dataTableHeadingContent" width="60"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_CUSTOMERS_EMAIL; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_QUITED_DATE; ?>&nbsp;</td>
              </tr>
<?php
    $search = '';
   $site_id = isset($_GET['site_id']) ? $_GET['site_id'] :0;
    $customers_query_raw = "
      select customers_id, 
             site_id,
             customers_lastname, 
             customers_firstname, 
             customers_email_address, 
	     quited_date,
             customers_info_date_account_created as date_account_created 
             from  customers_exit 
	     ";
if($site_id!=0) {
$customers_query_raw .= "where site_id='".$site_id."'";
}
if ( isset($_GET['search']) && ($_GET['search']) && (tep_not_null($_GET['search'])) ) {
	if($site_id==0){
	$customers_query_raw .= " where "; 
	}else{
	$customers_query_raw .= " and ";
	}
      $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
      $search = "  (customers_lastname like '%" . $keywords . "%' or customers_firstname like '%" . $keywords . "%' or customers_email_address like '%" . $keywords . "%' or customers_firstname_f like '%" . $keywords . "%'  or customers_lastname_f like '%" . $keywords . "%')";
      $customers_query_raw .= $search;
    }
$customers_query_raw .= " order by customers_lastname, customers_firstname";
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    while ($customers = tep_db_fetch_array($customers_query)) {
if (
          ((!isset($_GET['cID']) || !$_GET['cID']) || (@$_GET['cID'] == $customers['customers_id'])) 
          && (!isset($cInfo) || !$cInfo)
        ) {
        $country_query = tep_db_query("
            select countries_name 
            from " . TABLE_COUNTRIES . " 
            where countries_id = '" . $customers['entry_country_id'] . "'
        ");
        $country = tep_db_fetch_array($country_query);

        $reviews_query = tep_db_query("
            select count(*) as number_of_reviews 
            from " . TABLE_REVIEWS . " 
            where customers_id = '" . $customers['customers_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);

        $customer_info = tep_array_merge($country, $customers, $reviews);

        $cInfo_array = tep_array_merge($customers, $customer_info);
        $cInfo = new objectInfo($cInfo_array);
      }

//	    $cInfo = new objectInfo($customers);
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
      if ( (isset($cInfo) && is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" >' . "\n";
      } else {
        echo '          <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" >' . "\n";
    }
?>
	<td class="dataTableContent"><input type="checkbox" value="<?php echo $customers['customers_id'] ?>" name="ck[]"></td>

		<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><?php echo tep_get_site_romaji_by_id($customers['site_id']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><a href="<?php echo tep_href_link('orders.php', 'cID=' .
            tep_output_string_protected($customers['customers_id']));?>"><?php
            echo tep_image(DIR_WS_ICONS . 'search.gif', TEXT_ORDER_HISTORY_ORDER);?></a>
<?php echo htmlspecialchars($customers['customers_lastname']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><?php echo htmlspecialchars($customers['customers_firstname']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><?php echo htmlspecialchars($customers['customers_email_address']); ?></td>

                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><?php echo tep_date_short($customers['date_account_created']); ?></td>
		<td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CUSTOMERS_EXIT, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']); ?>';"><?php echo tep_datetime_short($customers['quited_date']); ?></td>

	      </tr>
<?php
    }
?>

              <tr>
                <td colspan="7" ><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                  </tr>
<tr align="right">
<td colspan="7" align="right">
		 <input type="submit"  onclick="return confirm('本当に削除しますか? ');" value="削除"> 
</td>
</tr>
</form>
<?php
                                                                      if (isset($_GET['search']) and tep_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a class = "new_product_reset"  href="' . tep_href_link(FILENAME_CUSTOMERS_EXIT) . '">' . tep_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
            </table></td>
<?php
      
?>
          </tr>
        </table></td>
      </tr>
<?php
//  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
