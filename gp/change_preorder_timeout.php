<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES.$language.'/change_preorder_timeout.php');
  $breadcrumb->add(CPREORDER_TIMEOUT_NAVBAR_TITLE, '');
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
        <?php echo CPREORDER_TIMEOUT_HEADING_TITLE ; ?>
        </h1>
        </div>
                <div class="comment">
        <table border="0" width="100%" class="product_info_box" cellspacing="0" cellpadding="0">
          <tr>
            <td>
            <?php 
            echo sprintf(CPREORDER_TIMEOUT_INFO, '<a href="'.tep_href_link('open.php', 'pname='.urlencode($_GET['pname'])).'">'.CPREORDER_CONTACT_US.'</a>');
            ?> 
            </td>
          </tr>
        </table>
                </div>
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
