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
      <td valign="top" id="contents"> <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div class="comment">
        <?php
        if (isset($error_msg)) {
          echo '<font color="#ff0000">'.$error_msg.'</font><br>'; 
        }
        ?>
        <?php echo tep_draw_form('password_forgotten', tep_href_link('password_token.php', 'action=process&pud='.$_GET['pud'])); ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
          <tr>
            <td class="main" style="width:110px;"><?php echo UPDATE_ENTRY_PASSWORD_TEXT;?></td>
            <td class="main"><?php echo tep_draw_password_field('u_password', '', 'class="input_text"'); ?>&nbsp;&nbsp;<?php echo UPDATE_ENTRY_PASSWORD_READ;?></td>
          </tr>
          <tr>
			<td class="main"><?php echo UPDATE_ENTRY_PASSWORD_AGAINTEXT;?></td>
            <td class="main"><?php echo tep_draw_password_field('up_password', '', 'class="input_text"'); ?>&nbsp;&nbsp;<?php echo UPDATE_ENTRY_PASSWORD_READ;?></td>
          </tr>
          <tr>
            <td colspan="2" class="main">
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
		<p class="pageBottom"></p>
		</td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
