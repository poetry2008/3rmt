<?php
/*
  $Id: product_listing.php,v 1.1.1.1 2003/02/20 01:03:54 ptosh Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
  <meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
*/

	// Product_listing.php Add
	define('LISTING_DISPLAY_OPTION','ɽ������:');
	define('LISTING_SORT_BY','�¤��ؤ�:');
	define('LISTING_PRICE_LOW','���ʤ��¤�');
	define('LISTING_PRICE_HIGHT','���ʤ��⤤');
	define('LISTING_TITLE_A_TO_Z','�����ȥ� A - Z');
	define('LISTING_TITLE_Z_TO_A','�����ȥ� Z - A');
	
	define('SORT_BY_IMAGE_TEXT','�����ȥ�Ȳ���');
	define('SORT_BY_IMAGE','�����Τ�');
?>
<!--select searach -->
<table width="689"  border="0" cellpadding="1" cellspacing="1" bgcolor="#C0CEDD">
	<tr>
		<td height="25" align="center" <?php echo ($HTTP_GET_VARS['sort'] == '4a') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($HTTP_GET_VARS['sort'] == '4a') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4a') ; ?>">�����ȥ��(A��)���¤٤�</a></td>
		<td height="25" align="center" <?php echo ($HTTP_GET_VARS['sort'] == '4d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($HTTP_GET_VARS['sort'] == '4d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=4d') ; ?>">�����ȥ��(Z��)���¤٤�</a></td>
		<td height="25" align="center" <?php echo ($HTTP_GET_VARS['sort'] == '5a') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($HTTP_GET_VARS['sort'] == '5a') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5a') ; ?>">���ʽ�(�¤�)���¤٤�</a></td>
		<td height="25" align="center" <?php echo ($HTTP_GET_VARS['sort'] == '5d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($HTTP_GET_VARS['sort'] == '5d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=5d') ; ?>">���ʽ�(�⤤)���¤٤�</a></td>
		<td height="25" align="center" <?php echo ($HTTP_GET_VARS['sort'] == '9d') ? 'bgcolor="#E2F8FD"' : 'bgcolor="#FFFFFF"' ; ?> class="main" title="" onMouseOver="this.bgColor = '#E2F8FD';" onMouseOut ="this.bgColor = '<?php echo ($HTTP_GET_VARS['sort'] == '9d') ? '#E2F8FD' : '#FFFFFF' ; ?>'"><img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt=""><a href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=9d') ; ?>">�͵�����¤٤�</a></td>
	</tr>
</table>
<?php
	$listing_numrows_sql = $listing_sql;
	$listing_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
	// fix counted products
	$listing_numrows = tep_db_query($listing_numrows_sql);
	$listing_numrows = tep_db_num_rows($listing_numrows);
	
	if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="689" cellspacing="0" cellpadding="2">
	<tr>
		<td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
		<td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
	</tr>
</table>
<?php
	}
?>
<!--select searach_eof// -->
<div class="underline">&nbsp;</div>
<table border="0" width="689" cellspacing="0" cellpadding="0">
	<tr>
		<td>
<?php
	if ($listing_numrows > 0) {
	
		$listing_query = tep_db_query($listing_sql);
		//echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">'."\n" ;
		//echo   '<tr>'."\n";
		$row = 0 ;
		$col = 0 ;
		
		while ($listing = tep_db_fetch_array($listing_query)) {
			//price
			if (tep_not_null($listing['specials_new_products_price'])) {
				$price = '<s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
			} else {
				$price = $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
			}
			//buynow
			if($listing['products_quantity'] > 0) {
				$BUY_NOW = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', TEXT_BUY . $listing['products_name'] . TEXT_NOW) . '</a>&nbsp;';
			} else {
				$BUY_NOW = STOCK_MARK_PRODUCT_OUT_OF_STOCK;
			}
			//product_name
			if(mb_strlen($listing['products_name']) > 40) {
				$products_name = mb_substr($listing['products_name'],0,40);
				$ten = '..';
			}else{
				$products_name = $listing['products_name'];
				$ten = '';
			}
			// edit 2009.5.14 maker
			//product_description
			//$description_array = explode("|-#-|", $listing['products_description_'.ABBR_SITENAME]);
			//$description = strip_tags(mb_substr ($description_array[0],0,60));//maker
			$description = strip_tags(mb_substr ($listing['products_description'],0,60));//maker
		
			$row++;
			$col++;
?>
			<!-- products_id <?=$listing['products_id']?>-->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td rowspan="2" width="<?=SMALL_IMAGE_WIDTH?>" style="padding-right:8px; " align="center">
						<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>' ; ?>
					</td>
					<td class="main" style="padding-left:5px; ">
						<h3>
							<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
							<strong><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">'.$products_name.$ten.'</a>'; ?></strong>
						</h3>
					</td>
					<td class="main" align="right">
						<p>1��<?php echo $price; ?>����</p>
					</td>
					<td class="main" align="right">
						<p><?php echo '�Ĥ�&nbsp;<b>' . $listing['products_quantity'] . '</b>&nbsp;��'; ?></p>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-left:5px; ">
						<p class="smallText">
<?php
	if($listing['products_bflag'] == '1') {
		# ��辦��
		echo $description . '..';
	} elseif ($listing['products_cflag'] == '0') {
		echo $description . '..';
	} else {
		# ���侦��
		echo $description . '..<br>ɽ���߸˰ʾ����ʸ�ϡ�<a href="' . tep_href_link(FILENAME_PREORDER, 'products_id=' . $listing['products_id']) . '">' . $products_name . $ten . '��ͽ��</a>�פ��餪��³������������';
	}
?>
						</p>
					</td>
					<td align="right">
						<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_order.gif',IMAGE_BUTTON_DEC);?></a>
					</td>
				</tr>
			</table>
			<br>
			<div class="dot">&nbsp;</div>
<?php
	}
	//echo '</tr>';
	echo '		</td>' . "\n" . '	</tr>' . "\n";
	} else {
?>
	<tr class="productListing-odd">
		<td class="smallText">&nbsp;<?php echo (isset($HTTP_GET_VARS['manufacturers_id']) ? TEXT_NO_PRODUCTS2 : TEXT_NO_PRODUCTS); ?>&nbsp;</td>
	</tr>
<?php
	}
?>
	<tr>
		<td><div class="underline">&nbsp;</div></td>
	</tr>
<?php
	if ( ($listing_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
	<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
					<td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
	}
?>
</table>
<div id="dis_clist"></div>
