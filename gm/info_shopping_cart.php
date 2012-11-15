<?php
/*
  $Id$

*/

  require("includes/application_top.php");

  $navigation->remove_current_page();

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_INFO_SHOPPING_CART);
?>
<?php page_head();?>
</head>
<body>

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
<?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post', 'onSubmit="return check_confirm_payment(\''.$payment.'\')"');?>
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>


<div id="main-content">
<h2><b><?php echo HEADING_TITLE; ?></b></h2>
<p class="box_des"><b><i><?php echo SUB_HEADING_TITLE_1; ?></i></b><br><?php echo SUB_HEADING_TEXT_1; ?></p><br>
<p class="box_des"><b><i><?php echo SUB_HEADING_TITLE_2; ?></i></b><br><?php echo SUB_HEADING_TEXT_2; ?></p><br>
<p class="box_des"><b><i><?php echo SUB_HEADING_TITLE_3; ?></i></b><br><?php echo SUB_HEADING_TEXT_3; ?></p>
<p align="right" class="box_des"><a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo TEXT_CLOSE_WINDOW; ?></a></p>

     </div>
     </div>
<?php include('includes/float-box.php');?>

    </div>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>




</body>
</html>
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
