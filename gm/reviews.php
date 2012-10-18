<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS . 'reviews.php');
?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div class="yui3-u" id="layout">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
      <?php include('includes/search_include.php');?>
	  	  <div id="main-content">
<h2><?php echo HEADING_TITLE; ?></h2>
        <div> 
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
<?php if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) { ?>
      <tr>
        <td><br>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table>
        </td>
      </tr>
<?php } ?>
      <tr>
        <td>
<?php require(DIR_WS_MODULES  . 'reviews.php'); ?>
        </td>
      </tr>
<?php if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) { ?>
      <tr>
        <td><br>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table>
        </td>
      </tr>
<?php } ?>
    </table></div></div></div>
	<?php include('includes/float-box.php');?>
      </div>
      <!-- body_text_eof //--> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <!-- footer_eof //--> 
</div>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
