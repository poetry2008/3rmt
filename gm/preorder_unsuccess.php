<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER_UNSUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
?>
<?php page_head();?>
</head>
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<h2">
<?php echo TEXT_UNSUCCESS; ?>
</h2>
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
          <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
            <tr> 
              <td>
              <font size="2"><?php echo TEXT_PAY_UNSUCCESS;?></font></td> 
            </tr> 
            <tr> 
              <td align="right">
              <a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo tep_image_button('button_continue.gif', '');?></a> 
              </td> 
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
