<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/active_success.php');
  
  
  $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".base64_decode($_GET['aid'])."' and site_id = '".SITE_ID."'");
  $customers_res = tep_db_fetch_array($customers_raw);
  
  if ($customers_res) {
    $email_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']); 
    
    $email_text = stripslashes($customers_res['customers_lastname'].' '.$customers_res['customers_firstname']).EMAIL_NAME_COMMENT_LINK . "\n\n"; 
    
    $email_text .= C_CREAT_ACCOUNT;
    $email_text = str_replace(array('${MAIL}', '${PASS}'), array($customers_res['customers_email_address'], ''), $email_text); 
    if ($customers_res['is_active'] == 0) {
      tep_mail($email_name, $customers_res['customers_email_address'], EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
    }
    tep_db_query("update ".TABLE_CUSTOMERS." set `is_active` = 1 where customers_id = '".base64_decode($_GET['aid'])."' and site_id = '".SITE_ID."'"); 
  }

  $breadcrumb->add(NAVBAR_TITLE);
?>
<?php page_head();?>
</head>
<body><div class="body_shadow" align="center"> 
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
      <div class="pageHeading"><img align="top" src="images/menu_ico_a.gif" alt=""><h1><?php echo HEADING_TITLE ; ?></h1></div> 
        
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;"> 
            <tr> 
              <td>
              <?php echo ACTIVE_SUCCESS_TEXT;?> 
              </td>
            </tr>
        <tr>
          <td align="right"><br><?php echo '<a href="'.tep_href_link(FILENAME_DEFAULT).'">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
