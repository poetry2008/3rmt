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
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td valign="top" class="left_colum_border">
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      </td>
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo TEXT_UNSUCCESS; ?></h1>
                <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box">
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
                <p class="pageBottom"></p>
      </td>
    </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
