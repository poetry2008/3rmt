<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  if (sizeof($navigation->snapshot) > 0) {
    $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
    $navigation->clear_snapshot();
  } else {
    $origin_href = HTTP_SERVER.'?'.tep_session_name().'='.tep_session_id();
  }
?>
<?php page_head();?>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          	<tr>
          		<td align="right"><?php echo '<a href="' . $origin_href . '">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
        	</tr>
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="game_list_content">
            <tr>
              <td valign="top" class="main"><?php echo TEXT_ACCOUNT_CREATED; ?></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="right"><?php echo '<a href="' . $origin_href . '">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
        </tr>
      </table>
      </div>
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
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
