<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES.$language.'/extend_time_success.php');
  $breadcrumb->add(EXTEND_PREORDER_TIME_SUCCESS_TEXT);
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
<?php echo EXTEND_PREORDER_TIME_SUCCESS_TEXT;?>
</h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
                <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:11px;">
                  <tr>
                    <td>
                          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                            <tr> 
                              <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                              <td align="right" class="main">
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
<div id="r_menu">
  <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
