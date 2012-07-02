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
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
        <div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
<?php if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) { ?>
      <tr>
        <td><br><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table></td>
      </tr>
<?php } ?>
      <tr>
        <td>
<?php require(DIR_WS_MODULES  . 'reviews.php'); ?>
        </td>
      </tr>
<?php if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) { ?>
      <tr>
        <td><br><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table></td>
      </tr>
<?php } ?>
    </table></div></div>
      <!-- body_text_eof //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
