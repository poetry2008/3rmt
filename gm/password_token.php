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
  <!-- header_eof //--> 
  <!-- body //--> 
  <div id="main"> 
<div id="layout" class="yui3-u">        <div id="current"><?php echo
$breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
		<?php include('includes/search_include.php');?>
	<div id="main-content">
    <h2><?php echo HEADING_TITLE ; ?></h2>
        
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
 <?php include('includes/float-box.php');?>
        </div>
        <?php include('includes/new.php');?>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div> 
</body>
</html>
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
