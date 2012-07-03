<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/non-member_auth.php');
  
  $breadcrumb->add(NAVBAR_TITLE);

?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
        <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
		<div id="main-content">
    <h2><?php echo HEADING_TITLE_FINISH; ?></h2> 

         <div class="auth_finish_left"><?php echo CHECK_FINISH_TEXT;?></div>
		<div class="auth_finish_right"><?php echo '<a href="' .  tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
       </div> 
</div>
<?php include('includes/float-box.php');?>
</div>
      <!-- body_text_eof //--> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
