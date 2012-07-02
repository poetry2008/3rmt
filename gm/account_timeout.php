<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/account_timeout.php');
  
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
            <h2><?php echo HEADING_TITLE; ?></h2>  
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td>
              <?php echo ACCOUNT_TIMEOUT_COMMENT;?>  
              </td>
            </tr>
            <tr> 
              <td><br> 
                <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_DEFAULT) . '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
        </div></div>
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
