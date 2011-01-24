<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  if (!isset($_GET['s_y'])){
    $_GET['s_y'] = date('Y');
  }
  if (!isset($_GET['s_m'])){
    $_GET['s_m'] = date('m');
  }
  if (!isset($_GET['s_d'])){
    $_GET['s_d'] = date('d');
  }
  if (!isset($_GET['e_y'])){
    $_GET['e_y'] = date('Y');
  }
  if (!isset($_GET['e_m'])){
    $_GET['e_m'] = date('m');
  }
  if (!isset($_GET['e_d'])){
    $_GET['e_d'] = date('d');
  }
  $startTime = $_GET['s_y'].'-'.$_GET['s_m'].'-'.$_GET['s_d'].' '.'00:00:00';
  $endTime   = $_GET['e_y'].'-'.$_GET['e_m'].'-'.$_GET['e_d'].' '.'23:59:59';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
          <tr><?php echo tep_draw_form('search', FILENAME_NEW_CUSTOMERS, tep_get_all_get_params(), 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          </form></tr>
        </table>
    <!--ORDER EXPORT SCRIPT //-->
    <form action="<?php echo tep_href_link(FILENAME_NEW_CUSTOMERS) ; ?>" method="get">
    <table  border="0" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <td class="smallText" width='150'>
      サイト:
      <?php echo tep_site_pull_down_menu_with_all(isset($_GET['site_id']) ? $_GET['site_id'] :'', false);?>
      </td>
      <td class="smallText">
      開始日:
      <select name="s_y">
      <?php for($i=2005; $i<2020; $i++) { 
        if (isset($_GET['s_y']) && $i == $_GET['s_y']) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        }else{ 
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
      年
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { 
        if (isset($_GET['s_m']) && $i == $_GET['s_m']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{ 
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; 
        }  
      } ?>    
      </select>
      月
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if (isset($_GET['s_d']) && $i == $_GET['s_d']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
      <td width="80" align="center">～</td>
      <td class="smallText">終了日
      <select name="e_y">
      <?php
      for($i=2005; $i<2020; $i++) {
        if (isset($_GET['e_y']) && $i == $_GET['e_y']) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if (isset($_GET['e_m']) && $i == $_GET['e_m']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if (isset($_GET['e_d']) && $i == $_GET['e_d']) {
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
      <td>&nbsp;</td>
      <td><input type="submit" value="Submit"></td>
      </tr>
    </table>
    </form>
    <!--ORDER EXPORT SCRIPT EOF //-->
        
        </td>
      </tr>
      <tr><td>
        <?php tep_site_filter(FILENAME_NEW_CUSTOMERS);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="60"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent" width="80"><?php echo TABLE_HEADING_MEMBER_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></td>
              </tr>
<?php
          /*
    $search = '';
    if ( isset($_GET['search']) && ($_GET['search']) && (tep_not_null($_GET['search'])) ) {
      $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
      $search = "and (c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%' or c.customers_firstname_f like '%" . $keywords . "%'  or c.customers_lastname_f like '%" . $keywords . "%')";
    }
    $customers_query_raw = "
      select c.customers_id, 
             c.site_id,
             c.customers_lastname, 
             c.customers_firstname, 
             c.customers_email_address, 
             a.entry_country_id, 
             c.customers_guest_chk,
             ci.customers_info_date_account_created as date_account_created, 
             ci.customers_info_date_account_last_modified as date_account_last_modified, 
             ci.customers_info_date_of_last_logon as date_last_logon, 
             ci.customers_info_number_of_logons as number_of_logons 
      from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci
        where c.customers_id = ci.customers_info_id
          and ci.customers_info_date_account_created > '" . $startTime . "'
          and ci.customers_info_date_account_created < '" . $endTime . "'
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and c.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
        " . $search . " 
      order by ci.customers_info_date_account_created desc
    ";
    */
    $customers_query_raw = "
      SELECT o.customers_id,
             c.customers_guest_chk,
             c.customers_lastname, 
             c.customers_firstname, 
             c.site_id,
             c.customers_email_address,
             ci.customers_info_date_account_created as date_account_created
      FROM orders o LEFT JOIN orders_status_history as osh ON osh.orders_id = o.orders_id AND osh.orders_status_id in (2,5), customers c, ".TABLE_CUSTOMERS_INFO." ci
      WHERE c.customers_id = ci.customers_info_id
        AND o.customers_id = c.customers_id
        AND o.orders_status in (2,5)
      GROUP BY o.customers_id
      HAVING sum( osh.`date_added` < '" . $startTime . "' ) = 0
      AND sum( osh.`date_added` < '" . $endTime . "' AND osh.`date_added` > '" . $startTime . "' ) > 0
      " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " AND c.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
      ORDER BY osh.date_added DESC 
    ";
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    while ($customers = tep_db_fetch_array($customers_query)) {
      if($customers['customers_guest_chk'] == 1) {
        $type = TABLE_HEADING_MEMBER_TYPE_GUEST;
      } else {
        $type = TABLE_HEADING_MEMBER_TYPE_MEMBER;
      }

    echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'">' . "\n";

?>
                <td class="dataTableContent"><?php echo tep_get_site_romaji_by_id($customers['site_id']); ?></td>
                <td class="dataTableContent"><?php echo $type; ?></td>
                <td class="dataTableContent"><?php echo htmlspecialchars($customers['customers_lastname']); ?></td>
                <td class="dataTableContent"><?php echo htmlspecialchars($customers['customers_firstname']); ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($customers['date_account_created']); ?></td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                  </tr>
<?php
                                                                      if (isset($_GET['search']) and tep_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS) . '">' . tep_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
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
