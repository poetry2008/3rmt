<?php
  $listing_numrows_sql = $listing_sql;
  $listing_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
// fix counted products
  $listing_numrows = tep_db_query($listing_numrows_sql);
  $listing_numrows = tep_db_num_rows($listing_numrows);

  if ( ($listing_numrows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="0"> 
  <tr> 
     <td> <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
         <tr> 
          <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td> 
          <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td> 
        </tr> 
       </table></td> 
   </tr> 
</table> 
<?php
  }
  
  if ($listing_numrows > 0) {
	echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">'."\n" ;
	echo '<tr>'."\n";
    $row = 0 ;
	$col = 0 ;

	$listing_query = tep_db_query($listing_sql);
	while ($listing = tep_db_fetch_array($listing_query)) {
	  //price
      if (tep_not_null($listing['specials_new_products_price'])) {
        $price = '<s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $price = $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
      }
      
       //Image
       $image = 'colors/' . $listing['color_image'];
	   //$color_link = '&color_id='.$HTTP_GET_VARS['colors'];
	   $color_link = '';
	   
	   // edit 2009.5.14 maker
	   //$description_array = explode("|-#-|", $listing['products_description']);
	   //$description = strip_tags(mb_substr ($description_array[0],0,60));
	   $description = strip_tags(mb_substr ($listing['products_description'],0,60));

       $row++;
	   $col++;
	   
	   echo '
		<td width="50%"><!-- products_id '.$listing['products_id'].'--> 
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
			<tr> 
			  <td width="120" style="padding-right:8px; " align="center" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id'] . $color_link) . '">'.tep_image(DIR_WS_IMAGES . $image, $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a></td> 
			  <td valign="top" style="padding-left:5px; "><p class="main"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id'] . $color_link) . '">'.$listing['products_name'].'</a><br> 
				  <span class="red">'.$price.'</span><br> 
				  <span class="smallText">'.$description.'...</span></p></td> 
			</tr> 
			<tr> 
			  <td colspan="2" style="padding-top:5px; " align="right"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id'] . $color_link) . '"><img src="images/design/button/button_description.jpg" border="0" alt="詳細はこちら" title=" 詳細はこちら "></a></td> 
			</tr> 
		  </table> 
		  <br> 
		  <div class="dot">&nbsp;</div></td> 
		<td><img src="images/pixel_trans.gif" border="0" alt="" width="10" height="1"></td> 
	   ';
	
      if ($col > 2) {
	    echo '</tr>'."\n".'<tr>'."\n";
        $col = 0;
        $row ++;
      }
	}
	echo '</tr>';
	echo '</table>' ;
  } else {
  echo '
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr class="productListing-odd">
      <td class="smallText">&nbsp;'. TEXT_NO_COLORS.'&nbsp;</td>
    </tr>
  </table>
  ';
  }
?> 
<?php
  if ( ($listing_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?> 
<table border="0" width="100%" cellspacing="0" cellpadding="2"> 
  <tr> 
    <td class="smallText"><?php echo $listing_split->display_count($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td> 
    <td align="right" class="smallText">&nbsp;<?php echo TEXT_RESULT_PAGE; ?> <?php echo $listing_split->display_links($listing_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>&nbsp;</td> 
  </tr> 
</table> 
<?php
  }
?> 
