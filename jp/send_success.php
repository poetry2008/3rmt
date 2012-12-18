<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/send_success.php');
  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL'));
  $breadcrumb->add(HEADING_TITLE);
?>
<?php page_head();?>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1> 
        
        <div class="comment"> 
          <table border="0" cellspacing="0" cellpadding="0" class="information_table"> 
          <tr>
            <td>
            <table class="information_table">
              <?php
              if (!isset($_GET['show'])) { 
              ?>
              <tr>
                <td colspan="3"><?php echo SEND_MAIL_READ_TEXT;?></td>
              </tr>
              <?php }?> 
              <tr>
                <td colspan="3"><img src="images/design/send_mail_top.gif" alt=""></td>
              </tr>
              <tr>
                <td colspan="3"  class="information_color">
                <?php 
                  echo sprintf(NOTICE_SEND_TO_EMAIL_TEXT, tep_db_prepare_input(rawurldecode($_GET['send_mail']))); 
                ?>
                </td>
              </tr>
              <tr>
                <td colspan="3">
                <br><?php echo ACTIVE_INFO_END_COMMENT;?>
                </td>
              </tr>
            </table>
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center"><img src="images/design/send_mail_bottom.gif" alt=""></td>
          </tr>
          <tr>
            <td>
            <?php echo ACTIVE_INFO_EMAIL_READ;?> 
            </td>
          </tr>
            <tr> 
              <td><br> 
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main" align="right">
                    </td> 
                    <td align="right" class="main">
                    </td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
        </div></td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--> </td> 
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
