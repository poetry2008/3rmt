<?php
/*
  $Id$
*/

  $xx_mins_ago = (time() - 900);

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

// remove entries that have expired
  tep_db_query("
      delete from " . TABLE_WHOS_ONLINE . " 
      where time_last_click < '" . $xx_mins_ago . "'
  ");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <?php tep_site_filter(FILENAME_WHOS_ONLINE);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ONLINE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FULL_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ENTRY_TIME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAST_CLICK; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LAST_PAGE_URL; ?>&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ONLINE_SOURCE;?></td>
              </tr>
<?php
  $whos_online_query = tep_db_query("
      select w.customer_id, 
             w.full_name, 
             w.ip_address, 
             w.time_entry, 
             w.time_last_click, 
             w.last_page_url, 
             w.session_id,
             w.referer,
             s.romaji
      from " . TABLE_WHOS_ONLINE . " w, ".TABLE_SITES." s
      where s.id = w.site_id
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . "
      ");
  while ($whos_online = tep_db_fetch_array($whos_online_query)) {
    $time_online = (time() - $whos_online['time_entry']);
    if ( ((!isset($_GET['info']) || !$_GET['info']) || ($_GET['info'] == $whos_online['session_id'])) && (!isset($info) || !$info) ) {
      $info = $whos_online['session_id'];
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if ($whos_online['session_id'] == $info) {
      echo '              <tr class="dataTableRowSelected">' . "\n";
    } else {
      echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_WHOS_ONLINE, tep_get_all_get_params(array('info', 'action')) . 'info=' . $whos_online['session_id'], 'NONSSL') . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $whos_online['romaji']?></td>
                <td class="dataTableContent"><?php echo gmdate('H:i:s', $time_online); ?></td>
                <td class="dataTableContent" align="center"><?php echo $whos_online['customer_id']; ?></td>
                <td class="dataTableContent"><?php echo $whos_online['full_name']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $whos_online['ip_address']; ?></td>
                <td class="dataTableContent"><?php echo date('Y/m/d H:i:s', $whos_online['time_entry']); ?></td>
                <td class="dataTableContent" align="center"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></td>
                <td class="dataTableContent"><?php if (eregi('^(.*)' . tep_session_name() . '=[a-f,0-9]+[&]*(.*)', $whos_online['last_page_url'], $array)) { echo $array[1] . $array[2]; } else { echo $whos_online['last_page_url']; } ?>&nbsp;</td>
                <td class="dataTableContent" align="left"><?php echo $whos_online['referer']; ?></td>
              </tr>
<?php
  }
?>
              <tr>
                <td class="smallText" colspan="7"><?php echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, tep_db_num_rows($whos_online_query)); ?></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  $session_data = '';
if (isset($info)) {
    $heading[] = array('text' => '<b>' . TABLE_HEADING_SHOPPING_CART . '</b>');

    if (STORE_SESSIONS == 'mysql') {
      $session_data = tep_db_query("
          select value 
          from " . TABLE_SESSIONS . " 
          WHERE sesskey = '" . $info . "'
      ");
      $session_data = tep_db_fetch_array($session_data);
      $session_data = trim($session_data['value']);
    } else {
      if ( (file_exists(tep_session_save_path() . '/sess_' . $info)) && (filesize(tep_session_save_path() . '/sess_' . $info) > 0) ) {
        $session_data = file(tep_session_save_path() . '/sess_' . $info);
        $session_data = trim(implode('', $session_data));
      }
    }

    if ($length = strlen($session_data)) {
      if (PHP_VERSION < 4) {
        $start_id       = strpos($session_data, 'customer_id[==]s');
        $start_cart     = strpos($session_data, 'cart[==]o');
        $start_currency = strpos($session_data, 'currency[==]s');
        $start_country  = strpos($session_data, 'customer_country_id[==]s');
        $start_zone     = strpos($session_data, 'customer_zone_id[==]s');
      } else {
        $start_id       = strpos($session_data, 'customer_id|s');
        $start_cart     = strpos($session_data, 'cart|O');
        $start_currency = strpos($session_data, 'currency|s');
        $start_country  = strpos($session_data, 'customer_country_id|s');
        $start_zone     = strpos($session_data, 'customer_zone_id|s');
      }

      // long time
      for ($i=$start_cart; $i<$length; $i++) {
        if ($session_data[$i] == '{') {
          if (isset($tag)) {
            $tag++;
          } else {
            $tag = 1;
          }
        } elseif ($session_data[$i] == '}') {
          $tag--;
        } elseif ( (isset($tag)) && ($tag < 1) ) {
          break;
        }
      }

      $session_data_id = substr($session_data, $start_id, (strpos($session_data, ';', $start_id) - $start_id + 1));
      $session_data_cart = substr($session_data, $start_cart, $i);
      $session_data_currency = substr($session_data, $start_currency, (strpos($session_data, ';', $start_currency) - $start_currency + 1));
      $session_data_country = substr($session_data, $start_country, (strpos($session_data, ';', $start_country) - $start_country + 1));
      $session_data_zone = substr($session_data, $start_zone, (strpos($session_data, ';', $start_zone) - $start_zone + 1));

      session_decode($session_data_id);
      session_decode($session_data_currency);
      session_decode($session_data_country);
      session_decode($session_data_zone);
      session_decode($session_data_cart);

      if (PHP_VERSION < 4) {
        $broken_cart = $cart;
        $cart = new shoppingCart;
        $cart->unserialize($broken_cart);
      }

      if (is_object($cart)) {
        $products = $cart->get_products();
        for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
          $contents[] = array('text' => $products[$i]['quantity'] . ' x ' . $products[$i]['name']);
        }

        if (sizeof($products) > 0) {
          $contents[] = array('text' => tep_draw_separator('pixel_black.gif', '100%', '1'));
          $contents[] = array('align' => 'right', 'text'  => TEXT_SHOPPING_CART_SUBTOTAL . ' ' . $currencies->format($cart->show_total(), true, $currency));
        } else {
          $contents[] = array('text' => '&nbsp;');
        }
      }
    }
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
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
