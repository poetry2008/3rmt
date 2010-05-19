<?php
/*
  $Id$
*/

$faq_c_url ='';
$faq_c_name ='';
$faq_single = false;
if($HTTP_GET_VARS['cPath']) {
  $categories_path = explode('_', $HTTP_GET_VARS['cPath']);
  $faq_c_url = 'faq' . $categories_path[0] . '/';
  //ccdd
  $_categories_query = tep_db_query("
   select * from (
    select * 
    from ". TABLE_CATEGORIES_DESCRIPTION . " 
    where categories_id = '".$categories_path[0]."' 
      and language_id = '".$languages_id."'
      and (site_id = '".SITE_ID."' or site_id = '0')
    ) c
   group by categories_id
   order by site_id desc
  ");
  $_categories = tep_db_fetch_array($_categories_query);
  $faq_c_name = $_categories['categories_name'];
} else {
  
  //$faq_c_url ='info-7.html';
  //ccdd
  $information_raw_query = tep_db_query("
    select * from ".TABLE_INFORMATION_PAGE." where pID = '7' and site_id='".SITE_ID."'"); 
  $information_raw_res = tep_db_fetch_array($information_raw_query); 
  $faq_single = true; 
  $information_romaji = $information_raw_res['romaji']; 
  $faq_c_name = 'RMT';
}
?>
<!-- faq //-->
<?php 
if (!$faq_single) {
?>
<a href="<?php echo tep_href_link($faq_c_url); ?>"><img src="images/banners/r_banner_faq.jpg" width="131" height="134" border="0" alt="<?php echo $faq_c_name; ?>のよくある質問"></a>
<?php
} else {
?>
<a href="<?php echo info_tep_href_link($information_romaji); ?>"><img src="images/banners/r_banner_faq.jpg" width="131" height="134" border="0" alt="<?php echo $faq_c_name; ?>のよくある質問"></a>
<?php
}
?>
<!-- faq_eof //-->
