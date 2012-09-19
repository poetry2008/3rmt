<?php
// 3rmt over
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
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <div id="main"> 
<div id="layout" class="yui3-u">        <div id="current"><?php echo
$breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
		<?php include('includes/search_include.php');?>


	<div id="main-content">
    <h2><?php echo HEADING_TITLE ; ?></h2>
        <div> 
        <?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
          <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center"> 
          <tr>
            <td>
            <table width="100%">
              <?php
              if (!isset($_GET['show'])) { 
              ?>
              <tr>
                <td colspan="3"><?php echo SEND_MAIL_READ_TEXT;?></td>
              </tr>
              <?php }?> 
              <tr>
                <td colspan="3"  align="center"><img src="images/design/send_mail_top.gif" alt=""></td>
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
          </table></form>
        </div></div> </div>
 <?php include('includes/float-box.php');?>
        </div>
        <?php include('includes/new.php');?>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
