<?php
/*
  $Id: index.php,v 1.1.1.1 2003/02/20 01:03:52 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
// <meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
*/

  require('includes/application_top.php');
  
  // Include OSC-AFFILIATE 
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  // ������˥塼�ʥ����åդˤ���ɽ����
  if ($ocertify->npermission >= 10) {
  $cat = array(array('title' => BOX_CUSTOMERS_ORDERS,
                     'image' => 'modules.gif',
                     'href' => tep_href_link(FILENAME_ORDERS, ''),
                     'children' => array(array('title' => BOX_CUSTOMERS_CUSTOMERS, 'link' => tep_href_link(FILENAME_CUSTOMERS, '')),
                                         array('title' => CATALOG_CONTENTS, 'link' => tep_href_link(FILENAME_CATEGORIES, '')),
                                         array('title' => BOX_TOOLS_LATEST_NEWS, 'link' => tep_href_link(FILENAME_LATEST_NEWS, '')))),
			   array('title' => BOX_CATALOG_REVIEWS,
                     'image' => 'tools.gif',
                     'href' => tep_href_link(FILENAME_REVIEWS, ''),
                     'children' => array(array('title' => BOX_TOOLS_PRESENT, 'link' => tep_href_link(FILENAME_PRESENT, '')),
                                         array('title' => BOX_CATALOG_MANUFACTURERS, 'link' => tep_href_link(FILENAME_MANUFACTURERS, '')))),
               array('title' => BOX_HEADING_REPORTS,
                     'image' => 'reports.gif',
                     'href' => tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, ''),
                     'children' => array(array('title' => BOX_REPORTS_PRODUCTS_VIEWED, 'link' => tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '')),
                                         array('title' => BOX_REPORTS_ORDERS_TOTAL, 'link' => tep_href_link(FILENAME_STATS_CUSTOMERS, '')),
										 array('title' => BOX_TOOLS_WHOS_ONLINE, 'link' => tep_href_link(FILENAME_WHOS_ONLINE, '')))));
  } else {
  $cat = array(array('title' => BOX_CUSTOMERS_ORDERS,
                     'image' => 'modules.gif',
                     'href' => tep_href_link(FILENAME_ORDERS, ''),
                     'children' => array(array('title' => BOX_CUSTOMERS_CUSTOMERS, 'link' => tep_href_link(FILENAME_CUSTOMERS, '')),
                                         array('title' => CATALOG_CONTENTS, 'link' => tep_href_link(FILENAME_CATEGORIES, '')),
                                         array('title' => BOX_TOOLS_LATEST_NEWS, 'link' => tep_href_link(FILENAME_LATEST_NEWS, '')))));
  }

  $languages = tep_get_languages();
  $languages_array = array();
  $languages_selected = DEFAULT_LANGUAGE;
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['code'],
                               'text' => $languages[$i]['name']);
    if ($languages[$i]['directory'] == $language) {
      $languages_selected = $languages[$i]['code'];
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<style type="text/css">
<!--
a { color:#080381; text-decoration:none; }
a:hover { color:#aabbdd; text-decoration:underline; }
a.text:link, a.text:visited { color: #000000; text-decoration: none; }
a:text:hover { color: #000000; text-decoration: underline; }
a.main:link, a.main:visited { color: #333333; text-decoration: none; }
A.main:hover { color: #6D6D6D; text-decoration: underline; }
a.sub:link, a.sub:visited { color: #6d6d6d; text-decoration: none; }
A.sub:hover { color: #dddddd; text-decoration: underline; }
.heading { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold; line-height: 1.5; color: #001682; }
.main { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 17px; font-weight: bold; line-height: 1.5; color: #ffffff; }
.sub { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; line-height: 1.5; color: #dddddd; }
.text { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; line-height: 1.5; color: #000000; }
.menuBoxHeading { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color: #ffffff; font-weight: bold; background-color: #7187bb; border-color: #7187bb; border-style: solid; border-width: 1px; }
.infoBox { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; color: #080381; background-color: #f2f4ff; border-color: #7187bb; border-style: solid; border-width: 1px; }
.smallText { font-family: Verdana, Arial, sans-serif; font-size: 10px; }
//--></style>
</head>
<body>
<table width="700" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table border="0" width="700" height="390" cellspacing="0" cellpadding="1">
      <tr bgcolor="#000000">
        <td><table border="0" width="100%" height="390" cellspacing="0" cellpadding="0">
          <tr bgcolor="#ffffff" height="50">
            <td height="50"><?php echo tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce', '204', '50'); ?></td>
            <td align="right" class="text" nowrap><?php echo '|&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . HEADER_TITLE_ADMINISTRATION . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="' . tep_catalog_href_link() . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a>'; ?>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><img src="images/pixel_trans.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td colspan="2" bgcolor="#FFFFFF"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr valign="top">
                <td width="160"><table border="0" width="160" height="390" cellspacing="0" cellpadding="2">
                  <tr>
                    <td><br>
<?php
	$impofile = '';
	$adminimpo = '';
	$impofile = file("../includes/languages/japanese/important.php");
	foreach($impofile as $key => $value) {
		$adminimpo .= $value;
	}

  $heading = array();
  $contents = array();

  $heading[] = array('params' => 'class="menuBoxHeading"',
                     'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=important.php') . '"><font color="#ff6600">���פʤ��Τ餻</font></a>');

  $contents[] = array('params' => 'class="infoBox"',
                      'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/important.php")) . '</font><br>' . nl2br(strip_tags($adminimpo)));

  $box = new box;
  echo $box->menuBox($heading, $contents);

  echo '<br>';

  $orders_contents = '';
  $orders_status_query = tep_db_query("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_pending_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . $orders_status['orders_status_id'] . "'");
    $orders_pending = tep_db_fetch_array($orders_pending_query);
    $orders_contents .= '<a href="' . tep_href_link(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id']) . '">' . $orders_status['orders_status_name'] . '</a>: ' . $orders_pending['count'] . '<br>';
  }
  $orders_contents = substr($orders_contents, 0, -4);

  $heading = array();
  $contents = array();

  $heading[] = array('params' => 'class="menuBoxHeading"',
                     'text'  => BOX_TITLE_ORDERS);

  $contents[] = array('params' => 'class="infoBox"',
                      'text'  => $orders_contents);

  $box = new box;
  echo $box->menuBox($heading, $contents);

  echo '<br>';

  $customers_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS);
  $customers = tep_db_fetch_array($customers_query);
  $products_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'");
  $products = tep_db_fetch_array($products_query);
  $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS);
  $reviews = tep_db_fetch_array($reviews_query);

  $heading = array();
  $contents = array();

  $heading[] = array('params' => 'class="menuBoxHeading"',
                     'text'  => BOX_TITLE_STATISTICS);

  $contents[] = array('params' => 'class="infoBox"',
                      'text'  => BOX_ENTRY_CUSTOMERS . ' ' . $customers['count'] . '<br>' .
                                 BOX_ENTRY_PRODUCTS . ' ' . $products['count'] . '<br>' .
                                 BOX_ENTRY_REVIEWS . ' ' . $reviews['count']);

  $box = new box;
  echo $box->menuBox($heading, $contents);

  echo '<br>';

// Include OSC-AFFILIATE
$affiliate_sales_raw = "select count(*) as count, sum(affiliate_value) as total, sum(affiliate_payment) as payment from " . TABLE_AFFILIATE_SALES . " ";
$affiliate_sales_query= tep_db_query($affiliate_sales_raw);
$affiliate_sales= tep_db_fetch_array($affiliate_sales_query);

$affiliate_clickthroughs_raw = "select count(*) as count from " . TABLE_AFFILIATE_CLICKTHROUGHS . " ";
$affiliate_clickthroughs_query=tep_db_query($affiliate_clickthroughs_raw);
$affiliate_clickthroughs= tep_db_fetch_array($affiliate_clickthroughs_query);
$affiliate_clickthroughs=$affiliate_clickthroughs['count'];

$affiliate_transactions=$affiliate_sales['count'];
if ($affiliate_transactions>0) {
	$affiliate_conversions = tep_round($affiliate_transactions/$affiliate_clickthroughs,6)."%";
}
else $affiliate_conversions="n/a";

$affiliate_amount=$affiliate_sales['total'];
if ($affiliate_transactions>0) {
	$affiliate_average=tep_round($affiliate_amount/$affiliate_transactions,2);
}
else {
	$affiliate_average="n/a";
}
$affiliate_commission=$affiliate_sales['payment'];

$affiliates_raw = "select count(*) as count from " . TABLE_AFFILIATE . "";
$affiliates_raw_query=tep_db_query($affiliates_raw);
$affiliates_raw = tep_db_fetch_array($affiliates_raw_query);
$affiliate_number= $affiliates_raw['count'];


  $heading = array();
  $contents = array();

  $heading[] = array('params' => 'class="menuBoxHeading"',
                     'text'  => BOX_TITLE_AFFILIATES);

  $contents[] = array('params' => 'class="infoBox"',
                      'text'  => BOX_ENTRY_AFFILIATES . ' ' . $affiliate_number . '<br>' .
                                 BOX_ENTRY_CONVERSION . ' ' . $affiliate_conversions . '<br>' .
                                 BOX_ENTRY_COMMISSION . ' ' . $currencies->display_price($affiliate_commission, ''));

  $box = new box;
  echo $box->menuBox($heading, $contents);

  echo '<br>';
  
  
  $contents = array();

  if (getenv('HTTPS') == 'on') {
    $size = ((getenv('SSL_CIPHER_ALGKEYSIZE')) ? getenv('SSL_CIPHER_ALGKEYSIZE') . '-bit' : '<i>' . BOX_CONNECTION_UNKNOWN . '</i>');
    $contents[] = array('params' => 'class="infoBox"',
                        'text' => tep_image(DIR_WS_ICONS . 'locked.gif', ICON_LOCKED, '', '', 'align="right"') . sprintf(BOX_CONNECTION_PROTECTED, $size));
  } else {
    $contents[] = array('params' => 'class="infoBox"',
                        'text' => tep_image(DIR_WS_ICONS . 'unlocked.gif', ICON_UNLOCKED, '', '', 'align="right"') . BOX_CONNECTION_UNPROTECTED);
  }

  $box = new box;
  echo $box->tableBlock($contents);

  echo '<br>';
?>
                    </td>
                  </tr>
                </table></td>
                <td width="100%">
					<br>
					<table border="0" width="100%" cellspacing="0" cellpadding="2">
                  		<tr>
                    		<td colspan="2">
								<table border="0" width="100%" cellspacing="0" cellpadding="2">
                      				<tr><?php echo tep_draw_form('languages', 'index.php', '', 'get'); ?>
                        				<td class="heading"><?php echo HEADING_TITLE; ?></td>
                        				<td align="right"><?php echo tep_draw_pull_down_menu('language', $languages_array, $languages_selected, 'onChange="this.form.submit();"'); ?></td>
                      					</form>
									</tr>
                    			</table>
							</td>
                  		</tr>
<?php
  $col = 2;
  $counter = 0;
  for ($i = 0, $n = sizeof($cat); $i < $n; $i++) {
    $counter++;
    if ($counter < $col) {
      echo '                  <tr>' . "\n";
    }

    echo '                    <td width="50%" height="80"><table border="0" cellspacing="0" cellpadding="2">' . "\n" .
         '                      <tr>' . "\n" .
         '                        <td><a href="' . $cat[$i]['href'] . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $cat[$i]['image'], $cat[$i]['title'], '48', '48') . '</a></td>' . "\n" .
         '                        <td><table border="0" cellspacing="0" cellpadding="2">' . "\n" .
         '                          <tr>' . "\n" .
         '                            <td class="main"><a href="' . $cat[$i]['href'] . '" class="main">' . $cat[$i]['title'] . '</a></td>' . "\n" .
         '                          </tr>' . "\n" .
         '                          <tr>' . "\n" .
         '                            <td class="sub">';

    $children = '';
    for ($j = 0, $k = sizeof($cat[$i]['children']); $j < $k; $j++) {
      $children .= '<a href="' . $cat[$i]['children'][$j]['link'] . '" class="sub">' . $cat[$i]['children'][$j]['title'] . '</a>, ';
    }
    echo substr($children, 0, -2);

    echo '</td> ' . "\n" .
         '                          </tr>' . "\n" .
         '                        </table></td>' . "\n" .
         '                      </tr>' . "\n" .
         '                    </table></td>' . "\n";

    if ($counter >= $col) {
      echo '                  </tr>' . "\n";
      $counter = 0;
    }
  }
?>
				</table>
				<br>
				<span class="heading">RMT����롼��</span>
<?php
	// FF11����롼��
	$buyfile = '';
	$buyprice = '';
	$buyfile = file("../includes/languages/japanese/ff11_buy.php");
	foreach($buyfile as $key => $value) {
		$buyprice .= $value;
	}

	$sellfile = '';
	$sellprice = '';
	$sellfile = file("../includes/languages/japanese/ff11_sell.php");
	foreach($sellfile as $key => $value) {
		$sellprice .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=ff11_buy.php') . '"><font color="#ffffff">FF11����</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/ff11_buy.php")) . '</font>');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($buyprice)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=ff11_sell.php') . '"><font color="#ffffff">FF11���</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/ff11_sell.php")) . '</font>');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($sellprice)));

	$b_box = new box;
	$s_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="50%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="50%">' . $s_box->menuBox($s_heading, $s_contents) . '</td></tr></table>';
	echo '<br>';
	
	// L2����롼��
	$buyfile = '';
	$buyprice = '';
	$buyfile = file("../includes/languages/japanese/l2_buy.php");
	foreach($buyfile as $key => $value) {
		$buyprice .= $value;
	}

	$sellfile = '';
	$sellprice = '';
	$sellfile = file("../includes/languages/japanese/l2_sell.php");
	foreach($sellfile as $key => $value) {
		$sellprice .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=l2_buy.php') . '"><font color="#ffffff">L2����</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/l2_buy.php")) . '</font>');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($buyprice)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=l2_sell.php') . '"><font color="#ffffff">L2���</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/l2_sell.php")) . '</font>');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($sellprice)));

	$b_box = new box;
	$s_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="50%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="50%">' . $s_box->menuBox($s_heading, $s_contents) . '</td></tr></table>';
	echo '<br>';

	// L1����롼��
	$buyfile = '';
	$buyprice = '';
	$buyfile = file("../includes/languages/japanese/l1_buy.php");
	foreach($buyfile as $key => $value) {
		$buyprice .= $value;
	}

	$sellfile = '';
	$sellprice = '';
	$sellfile = file("../includes/languages/japanese/l1_sell.php");
	foreach($sellfile as $key => $value) {
		$sellprice .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=l1_buy.php') . '"><font color="#ffffff">L1����</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/l1_buy.php")) . '</font>');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($buyprice)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=l1_sell.php') . '"><font color="#ffffff">L1���</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/l1_sell.php")) . '</font>');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($sellprice)));

	$b_box = new box;
	$s_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="50%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="50%">' . $s_box->menuBox($s_heading, $s_contents) . '</td></tr></table>';
	echo '<br>';

	// RO����롼��
	$buyfile = '';
	$buyprice = '';
	$buyfile = file("../includes/languages/japanese/ro_buy.php");
	foreach($buyfile as $key => $value) {
		$buyprice .= $value;
	}

	$sellfile = '';
	$sellprice = '';
	$sellfile = file("../includes/languages/japanese/ro_sell.php");
	foreach($sellfile as $key => $value) {
		$sellprice .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=ro_buy.php') . '"><font color="#ffffff">RO����</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/ro_buy.php")) . '</font>');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($buyprice)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=ro_sell.php') . '"><font color="#ffffff">RO���</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/ro_sell.php")) . '</font>');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($sellprice)));

	$b_box = new box;
	$s_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="50%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="50%">' . $s_box->menuBox($s_heading, $s_contents) . '</td></tr></table>';
	echo '<br>';

	// ����¾����롼��
	$buyfile = '';
	$buyprice = '';
	$buyfile = file("../includes/languages/japanese/other_buy.php");
	foreach($buyfile as $key => $value) {
		$buyprice .= $value;
	}

	$sellfile = '';
	$sellprice = '';
	$sellfile = file("../includes/languages/japanese/other_sell.php");
	foreach($sellfile as $key => $value) {
		$sellprice .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=other_buy.php') . '"><font color="#ffffff">����¾����</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/other_buy.php")) . '</font>');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($buyprice)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=other_sell.php') . '"><font color="#ffffff">����¾���</font></a>&nbsp;&raquo;&nbsp;<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/other_sell.php")) . '</font>');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => nl2br(strip_tags($sellprice)));

	$b_box = new box;
	$s_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="50%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="50%">' . $s_box->menuBox($s_heading, $s_contents) . '</td></tr></table>';
	echo '<br>';

	// ������ɥե����ޡ�1��3
	$sellprice01file = '';
	$sellprice01 = '';
	$sellprice01file = file("../includes/languages/japanese/sellprice01.php");
	foreach($sellprice01file as $key => $value) {
		$sellprice01 .= $value;
	}

	$sellprice02file = '';
	$sellprice02 = '';
	$sellprice02file = file("../includes/languages/japanese/sellprice02.php");
	foreach($sellprice02file as $key => $value) {
		$sellprice02 .= $value;
	}

	$sellprice03file = '';
	$sellprice03 = '';
	$sellprice03file = file("../includes/languages/japanese/sellprice03.php");
	foreach($sellprice03file as $key => $value) {
		$sellprice03 .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice01.php') . '"><font color="#ffffff">GF01</font></a>&nbsp;&raquo;');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice01.php")) . '</font><br>' . nl2br(strip_tags($sellprice01)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice02.php') . '"><font color="#ffffff">GF02</font></a>&nbsp;&raquo;');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice02.php")) . '</font><br>' . nl2br(strip_tags($sellprice02)));

	$w_heading = array();
	$w_contents = array();
	$w_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice03.php') . '"><font color="#ffffff">GF03</font></a>&nbsp;&raquo;');
	$w_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice03.php")) . '</font><br>' . nl2br(strip_tags($sellprice03)));

	$b_box = new box;
	$s_box = new box;
	$w_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="33%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="33%">' . $s_box->menuBox($s_heading, $s_contents) . '</td><td width="33%">' . $w_box->menuBox($w_heading, $w_contents) . '</td></tr></table>';
	echo '<br>';

	// ������ɥե����ޡ�4��6
	$sellprice01file = '';
	$sellprice01 = '';
	$sellprice01file = file("../includes/languages/japanese/sellprice04.php");
	foreach($sellprice01file as $key => $value) {
		$sellprice01 .= $value;
	}

	$sellprice02file = '';
	$sellprice02 = '';
	$sellprice02file = file("../includes/languages/japanese/sellprice05.php");
	foreach($sellprice02file as $key => $value) {
		$sellprice02 .= $value;
	}

	$sellprice03file = '';
	$sellprice03 = '';
	$sellprice03file = file("../includes/languages/japanese/sellprice06.php");
	foreach($sellprice03file as $key => $value) {
		$sellprice03 .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice04.php') . '"><font color="#ffffff">GF04</font></a>&nbsp;&raquo;');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice04.php")) . '</font><br>' . nl2br(strip_tags($sellprice01)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice05.php') . '"><font color="#ffffff">GF05</font></a>&nbsp;&raquo;');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice05.php")) . '</font><br>' . nl2br(strip_tags($sellprice02)));

	$w_heading = array();
	$w_contents = array();
	$w_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice06.php') . '"><font color="#ffffff">GF06</font></a>&nbsp;&raquo;');
	$w_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice06.php")) . '</font><br>' . nl2br(strip_tags($sellprice03)));

	$b_box = new box;
	$s_box = new box;
	$w_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="33%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="33%">' . $s_box->menuBox($s_heading, $s_contents) . '</td><td width="33%">' . $w_box->menuBox($w_heading, $w_contents) . '</td></tr></table>';
	echo '<br>';

	// ������ɥե����ޡ�7��9
	$sellprice01file = '';
	$sellprice01 = '';
	$sellprice01file = file("../includes/languages/japanese/sellprice07.php");
	foreach($sellprice01file as $key => $value) {
		$sellprice01 .= $value;
	}

	$sellprice02file = '';
	$sellprice02 = '';
	$sellprice02file = file("../includes/languages/japanese/sellprice08.php");
	foreach($sellprice02file as $key => $value) {
		$sellprice02 .= $value;
	}

	$sellprice03file = '';
	$sellprice03 = '';
	$sellprice03file = file("../includes/languages/japanese/sellprice09.php");
	foreach($sellprice03file as $key => $value) {
		$sellprice03 .= $value;
	}

	$b_heading = array();
	$b_contents = array();
	$b_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice07.php') . '"><font color="#ffffff">GF07</font></a>&nbsp;&raquo;');
	$b_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice07.php")) . '</font><br>' . nl2br(strip_tags($sellprice01)));

	$s_heading = array();
	$s_contents = array();
	$s_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice08.php') . '"><font color="#ffffff">GF08</font></a>&nbsp;&raquo;');
	$s_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice08.php")) . '</font><br>' . nl2br(strip_tags($sellprice02)));

	$w_heading = array();
	$w_contents = array();
	$w_heading[] = array('params' => 'class="menuBoxHeading"',
						'text'  => '<a href="' . tep_href_link('define_language.php' , 'lngdir=japanese&filename=sellprice09.php') . '"><font color="#ffffff">GF09</font></a>&nbsp;&raquo;');
	$w_contents[] = array('params' => 'class="infoBox"',
						'text'  => '<font color="black">' . date("Y-n-j G:i:s" , filemtime("../includes/languages/japanese/sellprice09.php")) . '</font><br>' . nl2br(strip_tags($sellprice03)));

	$b_box = new box;
	$s_box = new box;
	$w_box = new box;

	echo '<table width="100%"><tr valign="top"><td width="33%">' . $b_box->menuBox($b_heading, $b_contents) . '</td><td width="33%">' . $s_box->menuBox($s_heading, $s_contents) . '</td><td width="33%">' . $w_box->menuBox($w_heading, $w_contents) . '</td></tr></table>';
	echo '<br>';
?>
				</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php require(DIR_WS_INCLUDES . 'footer.php'); ?></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>