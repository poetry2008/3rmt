<?php
/*
  $Id: privacy.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if($HTTP_GET_VARS['goods_id']) {
    $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$HTTP_GET_VARS['goods_id']."'") ;
	$present = tep_db_fetch_array($present_query) ;
  }	
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRESENT));
?>
<?php page_head();?>
<script language="javascript" type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"><!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents"><h1 class="pageHeading"> <?php echo ($HTTP_GET_VARS['goods_id'] && $HTTP_GET_VARS['goods_id'] != '' ) ? $present['title'] : HEADING_TITLE ; ?> </h1>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td><?php
		  ######################
		  ##    詳細ページ    ##
		  ######################
		  if($HTTP_GET_VARS['goods_id'] && !empty($HTTP_GET_VARS['goods_id'])) {
		  $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$HTTP_GET_VARS['goods_id']."'") ;
		  $present = tep_db_fetch_array($present_query) ;
		  ?>
          <p align="right" class="main">応募期間 <?php echo tep_date_long($present['start_date']) . '&nbsp;&nbsp;&nbsp;〜&nbsp;&nbsp;&nbsp;' . tep_date_long($present['limit_date']) ; ?></p>
          <table border="0" cellspacing="0" cellpadding="2" align="right">
            <tr>
              <td align="center" class="smallText"><script type="text/javascript"><!--
			document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link('present_popup_image.php', 'pID=' . (int)$HTTP_GET_VARS['goods_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>イメージを拡大<\'+\'/a>'; ?>');
			//--></script>
              <noscript>
              <?php echo tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right"'); ?>
              </noscript>
              </td>
            </tr>
          </table>
          <p class="main">
            <?php 
				  if($present['html_check'] == '1') {
				    echo stripslashes($present['text']); 
                  }else{
				    echo nl2br($present['text']); 
			      }
				 ?>
          </p>
          <br>
          <table width="100%" border="0" cellpadding="2" cellspacing="0">
            <tr>
              <td><a href="javascript:history.back()"><?php echo tep_image_button('button_back.gif', IMAGE_BUTTON_BACK); ?></a> </td>
              <td align="right"><a href="<?php echo tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$HTTP_GET_VARS['goods_id'],SSL); ?>"><?php echo tep_image_button('button_present.gif', IMAGE_BUTTON_PRESENT); ?></a></td>
            </tr>
          </table>
          <?php
		  ######################
		  ##    一覧ページ    ##
		  ######################
		  } else {
		  ?>
          <?php
			  $today = date("Y-m-d", time());
			  $present_query_raw = "select * from ".TABLE_PRESENT_GOODS." order by start_date DESC";
			  $present_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $present_query_raw, $present_numrows);
			  $present_query = tep_db_query($present_query_raw);
	?>
          <br>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $present_split->display_count($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRESENT); ?></td>
              <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $present_split->display_links($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table>
          <div class="underline">&nbsp;</div>
          <table border="0" width="100%" cellspacing="1" cellpadding="2">
            <?php 
	 
			  while($present = tep_db_fetch_array($present_query)){
			    $row ++ ;
			  /*  if (($row/2) == floor($row/2)) {
				  $_class = "productListing-even";
			    } else {
				  $_class = "productListing-odd" ;
			    }
			  */	
			  ?>
            <tr>
              <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>"><?php echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],NONSSL).'">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"') . '</a>'; ?></td>
              <td class="main"><b><?php echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],NONSSL).'">'. $present['title'].'</a>' ; ?></b> <br>
              応募期間:<?php echo tep_date_long($present['start_date']) .'〜'. tep_date_long($present['limit_date']); ?>
              <p class="smallText"><?php echo substr(strip_tags($present['text']),0,100) ; ?>..</p></td>
            </tr>
            <?php
			  }
			  ?>
          </table>
          <?php
		  }
		  ?>
          </td>
        </tr>
        <?php
		if (!$HTTP_GET_VARS['goods_id'] && ($present_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
		?>
        <tr>
          <td><div class="underline">&nbsp;</div>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $present_split->display_count($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_PRESENT); ?></td>
              <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $present_split->display_links($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table></td>
        </tr>
        <?php
		}
		?>
      </table></td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"><!-- right_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      <!-- right_navigation_eof //-->
      </td>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
