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
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      </td>
      <td valign="top" id="contents">
        <div class="pageHeading">
        <h1>
        <?php echo EXTEND_PREORDER_TIME_SUCCESS_TEXT;?>
        </h1>
        </div>
                <div class="comment">
        <table border="0" width="100%" class="product_info_box" cellspacing="0" cellpadding="0">
          <tr>
            <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                      <td align="right" class="main">
                      </td> 
                    </tr> 
                  </table></td> 
          </tr>
        </table>
                <p class="pageBottom"></p>
      </td>
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      </td>
    </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
