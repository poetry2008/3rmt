<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_MAGAZINE);
  
  //-----------------
  // Process
  //-----------------
  switch($_GET['action']) {
  case 'regist':
    //注册
    $mag_mail = tep_db_prepare_input(tep_an_zen_to_han($_POST['email']));
    $mag_name = tep_db_prepare_input($_POST['name']);
    
    //session_start
    tep_session_register('mag_mail');
    tep_session_register('mag_name');
    
    if(empty($mag_name)) {
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_NAME_ERROR), 'SSL'));
    }
    if (!tep_validate_email($mag_mail)) {
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_EMAIL_ERROR), 'SSL'));
    }
    
     
    $regist_query = tep_db_query("select count(*) as cnt from " .  TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
    $regist_result = tep_db_fetch_array($regist_query);
    if($regist_result['cnt'] == '0') {
      //注册
 
    tep_db_query("insert into " . TABLE_MAIL_MAGAZINE . "(mag_id, mag_email, mag_name, site_id) values ('', '".$mag_mail."', '".$mag_name."', '".SITE_ID."')");
    
    tep_session_unregister('mag_mail');
    tep_session_unregister('mag_name');
      tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'info_message='.urlencode(TEXT_MAGAZINE_SUCCESS), 'SSL'));
    } else {
      //注册完毕
    
    tep_session_unregister('mag_mail');
    tep_session_unregister('mag_name');
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_ALREADY), 'SSL'));
    }
    break;
    
    case 'stop':
    //删除注册信息（退会）
    $mag_mail = tep_db_prepare_input(tep_an_zen_to_han($_POST['email']));
    if (!tep_validate_email($mag_mail)) {
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_EMAIL_ERROR), 'SSL'));
    }

     
    $regist_query = tep_db_query("select count(*) as cnt from " .  TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
    $regist_result = tep_db_fetch_array($regist_query);
    if($regist_result['cnt'] == '0') {
      //没有注册信息：错误
    
    tep_session_unregister('mag_mail');
    tep_session_unregister('mag_name');
      tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_STOP_ERROR), 'SSL'));
    } else {
      //删除注册信息处理（退会）
  
    tep_db_query("delete from " . TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
    
    tep_session_unregister('mag_mail');
    tep_session_unregister('mag_name');
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'info_message='.urlencode(TEXT_MAGAZINE_STOP), 'SSL'));
    }
    
    break;  
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_MAGAZINE));
?>
<?php page_head();?>
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text -->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content"><h2><?php echo HEADING_TITLE ; ?></h2>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"> 
  	<tr> 
    	<td colspan="2" valign="top"><h3><?php echo HEADING_MAGAZINE_TITLE_ACCOUNT; ?></h3></td>
    </tr>
    <tr> 
    	<td><?php echo TEXT_TITLE_ACCOUNT ; ?></td> 
    </tr> 
    <tr> 
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
    </tr>
    <tr>  
    	<td align="right" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . tep_image_button('button_edit_account.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
    </tr>
    <tr> 
    	<td colspan="2" valign="top"><h3><?php echo HEADING_MAGAZINE_TITLE_REG; ?></h3></td> 
    </tr>  
	<tr>
        	<td>
    <form method="post" action="<?php echo tep_href_link(FILENAME_MAGAZINE, 'action=regist', 'NONSSL'); ?>"> 
            	<table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0" class="box_des"> 
                                    <tr> 
                                      <td colspan="2"><?php echo TEXT_TITLE_REG; ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td width="20%"><?php echo TEXT_MAGAZINE_TITLE_NAME_REG; ?></td> 
                                      <td><input style="width:35%;" type="text" name="name" value="<?php echo $mag_name; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td><?php echo TEXT_MAGAZINE_TITLE_MAIL_REG; ?></td> 
                                      <td><input style="width:35%;" type="text" name="email" value="<?php echo $mag_mail; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr>
                                    <tr>
                                     <td align="right" valign="top" colspan="2"><?php echo tep_image_submit('button_continue.gif', TEXT_MAGAZINE_BUTTON_REG); ?></td> 
                                    </tr> 
            	</table>
     </form>
     </td>
     	</tr>
     <tr> 
        <td colspan="2"><h3><?php echo HEADING_MAGAZINE_TITLE_STP; ?></h3></td> 
     </tr> 
     <tr> 
     	<td>
     <form method="post" action="<?php echo tep_href_link(FILENAME_MAGAZINE, 'action=stop', 'NONSSL'); ?>"> 
                              <table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0" class="box_des"> 
                                    <tr> 
                                      <td colspan="2"><?php echo TEXT_TITLE_STP; ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td width="20%"><?php echo TEXT_MAGAZINE_TITLE_MAIL_STP; ?></td> 
                                      <td><input style="width:35%;" type="text" name="email" value="<?php echo $mag_mail; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr>
                                    <tr>
                                    <td colspan="2" align="right" valign="top"><?php echo tep_image_submit('button_delete.gif', TEXT_MAGAZINE_BUTTON_STP); ?></td> 
                                    </tr> 
                                  </table>
                      </form>
           </td> 
                              </tr>  
                    <?php
          //session_unregister
          tep_session_unregister('mag_mail');
          tep_session_unregister('mag_name');
          ?></td> 
                </tr> 
              </table></td> 
          </tr> 
        </table></div>
        </div>
          <div id="float-box">
				<div id="l-box"><img alt="img" src="images/header_left_flower_bg.png"></div>
				<div id="r-box"><img alt="img" src="images/header_right_flower_bg.png"></div>
		    </div>
</div>
      <!-- body_text_eof --> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof -->  
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
