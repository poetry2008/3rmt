<?php
/*
  $Id$
*/

  require('includes/application_top.php');

?>
<?php page_head();?>
<script type="text/javascript" src="js/left_search_category.js"></script>
</script>
</head>
<body>
<div align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <div class="pageHeading">
        <img align="top" alt="img" src="images/menu_ico.gif"><h1>text</h1></div>
                <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td>
            <a href="javascript:void(0);" onclick="left_search_category('search_category.php?ra=r');">ok</a> 
            <div id="showca" style="display:none;"></div> 
            </td>
          </tr>
        </table>
                </div>
                <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
