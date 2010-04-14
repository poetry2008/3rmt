	  <td valign="top" id="contents">
<?php 
  //todo: 这里需要修改成设置
// ccdd
	$contents1 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '10' and site_id = '".SITE_ID."'");  //top1
	$result1 = tep_db_fetch_array($contents1) ;
  // ccdd
	$contents2 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '".SITE_ID."'");  //top2
	$result2 = tep_db_fetch_array($contents2) ;
	
	echo $result1['text_information'];
	include(DIR_WS_MODULES . 'categories_banner_text.php');
	//include("ajax/php/tab.php") ;
	include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);
	//include(DIR_WS_MODULES . 'new_products.php');
	//include(DIR_WS_MODULES . 'upcoming_products.php');
	echo $result2['text_information'];
?>
      </td>
	  <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
