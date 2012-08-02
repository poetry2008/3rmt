<?php
// 3rmt over
/*
  $Id$

  商品隸・ｮｺ隸ｦ扈・｡ｵ
*/
  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/send_success.php');
  $breadcrumb->add(HEADING_TITLE);
?>
<?php page_head();?>
</head>
<body>  
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <div id="main"> 
      <div id="l_menu"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </div> 
      <!-- body_text --> 
      <div id="content"> 
        <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1> 
        <div> 
          <table border="0" cellspacing="0" cellpadding="0" class="box_des" width="95%"> 
          <tr>
            <td>
            <table class="box_des">
              <?php
              if (!isset($_GET['show'])) { 
              ?>
              <tr>
                <td colspan="3"><?php echo SEND_MAIL_READ_TEXT;?></td>
              </tr>
              <?php }?> 
              <tr>
                <td colspan="3"><img src="images/design/send_mail_top.gif" alt="" width="444" height="194"></td>
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
        </div></div> 
      <!-- body_text_eof --> 
      <div id="r_menu"> <!-- right_navigation --> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof --> </div> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?></div>
  <!-- footer_eof --> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
