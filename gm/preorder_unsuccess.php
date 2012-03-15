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
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading">
<?php echo TEXT_UNSUCCESS; ?>
</h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
          <table border="0" width="95%" cellspacing="0" cellpadding="0" align="center">
            <tr> 
              <td>
              <font size="2"><?php echo TEXT_PAY_UNSUCCESS;?></font></td> 
            </tr> 
            <tr> 
              <td align="right" class="main">
              <a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo tep_image_button('button_continue.gif', '');?></a> 
              </td> 
            </tr> 
          </table> 
      </div>
    </td>
  </tr>
</table>
</div>
<div id="r_menu">
  <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
