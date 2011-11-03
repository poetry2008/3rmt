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
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td valign="top" id="contents">
        <h1 class="pageHeading">
        <?php echo PREORDER_TIMEOUT_TITLE;?>
        </h1>
        <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:11px;">
          <tr>
            <td>
              <?php echo PREORDER_TIMEOUT_TEXT;?> 
            </td>
          </tr>
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
