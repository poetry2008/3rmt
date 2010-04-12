<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

$faq_c_url ='';
$faq_c_name ='';

if($_GET['cPath']) {
	$categories_path = explode('_', $_GET['cPath']);
	$faq_c_url = 'faq' . $categories_path[0] . '/';
	$_categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path[0]."' and language_id = '".$languages_id."' and site_id = '".SITE_ID."'");
	$_categories = tep_db_fetch_array($_categories_query);
	$faq_c_name = $_categories['categories_name'];
} else {
	$faq_c_url ='info-7.html';
	$faq_c_name = 'RMT';
}
?>
<!-- faq //-->
<a href="<?php echo tep_href_link($faq_c_url); ?>"><img src="images/banners/r_banner_faq.jpg" width="131" height="134" border="0" alt="<?php echo $faq_c_name; ?>のよくある質問"></a>
<!-- faq_eof //-->
