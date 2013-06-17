<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  //require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_MANUFAXTURERS);

  define('NAVBAR_TITLE', MENU_MU);
  define('HEADING_TITLE', MENU_MU);
  define('TEXT_MORE', TEXT_MANUFACTURERS_PRODUCT_LIST);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('manufacturers.php'));
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content">
<h2><?php echo HEADING_TITLE ; ?></h2> 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php 
  //$manufacturer_query_raw = "select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi  where  m.manufacturers_id = mi.manufacturers_id and languages_id = '" . $languages_id . "' order by manufacturers_name" ;
  $manufacturer_query_raw = "select * from (select pd.products_id, pd.products_status, pd.site_id, m.manufacturers_id, m.manufacturers_name, m.manufacturers_alt,m.manufacturers_image, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m, " . TABLE_MANUFACTURERS_INFO . " mi, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id not in".tep_not_in_disabled_products()." and p.manufacturers_id  = m.manufacturers_id and p.products_id = pd.products_id and m.manufacturers_id = mi.manufacturers_id and languages_id = '" . $languages_id . "' order by pd.site_id DESC) p where site_id = '".SITE_ID."' or site_id = '0' group by manufacturers_id having p.products_status != '0' and p.products_status != '3' order by manufacturers_name" ;
  $manufacturer_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturer_query_raw, $manufacturer_numrows);
  //ccdd
  $manufacturer_query = tep_db_query($manufacturer_query_raw);
 
  if (($manufacturer_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
      <tr>
        <td>
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $manufacturer_split->display_count($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
            </tr>
          </table>
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $manufacturer_split->display_links($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table>
          <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
        </td>
      </tr>
<?php
  }
?>
              <tr>
                <td>
<?php
  while($manufacturer = tep_db_fetch_array($manufacturer_query)){
    //ccdd
  $products_query = tep_db_query("select * from (select p.products_id, p.products_image, p.products_bflag, p.products_tax_class_id, p.products_price, p.products_price_offset, p.products_small_sum ,pd.site_id, pd.products_status, p.products_date_added  from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd where  p.products_id not in".tep_not_in_disabled_products()." and p.products_id = pd.products_id and manufacturers_id = '".$manufacturer['manufacturers_id']."' order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc limit 5 ");
    if(tep_db_num_rows($products_query)) {

echo '<table class="box_des" width="100%" border="0" cellspacing="0" cellpadding="0">'."\n".
  '<tr>'."\n".  '<td width="120" class="smallText" valign="top" align="center">';
    if(isset($manufacturer['manufacturers_alt'])&&$manufacturer['manufacturers_alt']!=''){
      $m_alt = $manufacturer['manufacturers_alt'];
    }else{
      if(isset($manufacturer['manufacturers_name'])&&$manufacturer['manufacturers_name']!=''){
        $m_alt = $manufacturer['manufacturers_name'];
      }else{
        $m_alt = 'img';
      }
    }
    echo '    <td width="120" class="smallText" valign="top" align="center">';
    if(isset($manufacturer['manufacturers_url'])&&$manufacturer['manufacturers_url']!=''){
    echo '<a target="_blank" href="'.substr(strip_tags($manufacturer['manufacturers_url']),0,100) .'">';
    }else{
      echo '<font color="#2864B4">';
    }
    echo '<h3 style="text-align:left"><strong>'.$manufacturer['manufacturers_name'].'</strong></h3>';
    if(isset($manufacturer['manufacturers_url'])&&$manufacturer['manufacturers_url']!=''){
    echo '</a>';
    }else{
      echo '</font>';
    }
    $configuration_width = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MANUFACTURERS_WIDTH' and site_id =".SITE_ID));
    $configuration_height = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MANUFACTURERS_height' and site_id =".SITE_ID));
    echo '<br> '.  tep_image_new(DIR_WS_IMAGES.'manufacturers/'.$manufacturer['manufacturers_image'],$m_alt,$configuration_width['configuration_value'],$configuration_height['configuration_value']).' </td>' . "\n";
    echo '<td>'."\n"
  ;
     echo '<table class="box_des" width="100%" border="0" cellspacing="2" cellpadding="0">'."\n".'<tr>'."\n";
     while($products = tep_db_fetch_array($products_query)) {
       $products['products_name'] = tep_get_products_name($products['products_id']);
       $products['products_description'] = tep_get_products_description($products['products_id']);
       if (tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) {
         $products_price = '<s>' .
           $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'], $products['products_bflag']), tep_get_tax_rate($products['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id'])) . '</span>';
       } else {
         $products_price = $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'], $products['products_bflag']), tep_get_tax_rate($products['products_tax_class_id']));
       }
     echo '<td align="center" valign="top" class="smallText" width="20%"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $products['products_id']) . '">'.tep_image2(DIR_WS_IMAGES.'products/'.$products['products_image'],$products['products_name'],60, 60,'class="image_border"').'<br>' .$products['products_name'] . '</a><br>'.$products_price.'</td>'."\n";
         //$col ++;
     /*     if ($col > 2) {
         echo '</tr>'."\n".'<tr>';
            $col = 0;
            $row ++;
          }*/
     }
    echo '</tr>'; 
    echo '</table>'."\n";
echo '  
  </td>'."\n".
  '</tr>'."\n".
  '<tr>'."\n".
    '<td colspan="2" align="right" class="smallText">'."\n".
  '<a href="'.tep_href_link(FILENAME_DEFAULT,'manufacturers_id=' . $manufacturer['manufacturers_id']).'">'.TEXT_MORE.'</a>'."\n"
	.'</td>'."\n".
  '</tr>'."\n".
'</table><hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none; margin:10px 0;">'."\n" ;
}

}
?>
                </td>
              </tr>
              <tr>
                <td>
                    <?php
  if (tep_db_num_rows($manufacturer_query)) {
?>
                    <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td class="smallText"><?php echo $manufacturer_split->display_count($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                      </tr>
                    </table>
                    <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td class="smallText"><?php echo $manufacturer_split->display_links($manufacturer_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                      </tr>
                    </table>
                    <?php
  }
?>
                    
                  </td>
              </tr>
            </table></div>
      </div>
      <?php include('includes/float-box.php');?>
</div>
      <!-- body_text_eof //--> 
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
