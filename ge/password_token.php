<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'password_token.php');
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
      <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <?php echo tep_draw_form('password_forgotten', tep_href_link('password_token.php', 'action=process&pud='.$_GET['pud'])); ?>
          <table border="0" width="95%" cellspacing="0" cellpadding="2" class="box_des"> 
            <?php 
            if (isset($error_msg)) {
            ?>
            <tr>
              <td colspan="2" class="main">
              <?php echo '<font color="#ff0000">'.$error_msg.'</font>';?> 
              </td> 
            </tr>
            <?php }?> 
            <tr>
				<td class="main"><?php echo UPDATE_ENTRY_PASSWORD_TEXT;?></td>
				<td class="main"><?php echo tep_draw_password_field('u_password', '', 'class="input_text"'); ?>&nbsp;&nbsp;<?php echo UPDATE_ENTRY_PASSWORD_READ;?></td>
			  </tr>
			  <tr>
				<td class="main"><?php echo UPDATE_ENTRY_PASSWORD_AGAINTEXT;?></td>
				<td class="main"><?php echo tep_draw_password_field('up_password', '', 'class="input_text"'); ?>&nbsp;&nbsp;<?php echo UPDATE_ENTRY_PASSWORD_READ;?></td>
			  </tr>
          <tr>
            <td colspan="2">
            <?php echo UPDATE_PASSWORD_INFORM_TEXT;?> 
            </td>
          </tr>
          <tr>
            <td colspan="2"><br><table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top"></td>
                <td align="right" valign="top"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
              </tr>
            </table></td>
          </tr>

        </table></form></div> 
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
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
