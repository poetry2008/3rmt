<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_timeout.php');
  $breadcrumb->add(PREORDER_TIMEOUT_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<h2>
<?php echo PREORDER_TIMEOUT_TITLE;?>
</h2>
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
        <tr>
          <td>
            <?php echo PREORDER_TIMEOUT_TEXT;?> 
          </td>
        </tr>
        <tr>
          <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                    <td align="right">
                    </td> 
                  </tr> 
                </table></td> 
        </tr>
      </table>
      </div>
    </td>
  </tr>
</table>
</div>
  <?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
