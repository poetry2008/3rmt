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
    //新規登録
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
    
    //ccdd
    $regist_query = tep_db_query("select count(*) as cnt from " .  TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
    $regist_result = tep_db_fetch_array($regist_query);
    if($regist_result['cnt'] == '0') {
      //新規登録
      //ccdd
      tep_db_query("insert into " . TABLE_MAIL_MAGAZINE . "(mag_id, mag_email, mag_name, site_id) values ('', '".$mag_mail."', '".$mag_name."', '".SITE_ID."')");
      
      tep_session_unregister('mag_mail');
      tep_session_unregister('mag_name');
      tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'info_message='.urlencode(TEXT_MAGAZINE_SUCCESS), 'SSL'));
    } else {
      //登録済み
    
    tep_session_unregister('mag_mail');
    tep_session_unregister('mag_name');
    tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_ALREADY), 'SSL'));
    }
    break;
    
    case 'stop':
    //登録削除（退会）
    $mag_mail = tep_db_prepare_input(tep_an_zen_to_han($_POST['email']));
    if (!tep_validate_email($mag_mail)) {
      tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_EMAIL_ERROR), 'SSL'));
    }

    //ccdd
    $regist_query = tep_db_query("select count(*) as cnt from " .  TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
    $regist_result = tep_db_fetch_array($regist_query);
    if($regist_result['cnt'] == '0') {
      //登録情報なし：エラー
      tep_session_unregister('mag_mail');
      tep_session_unregister('mag_name');
      tep_redirect(tep_href_link(FILENAME_MAGAZINE, 'error_message='.urlencode(TEXT_MAGAZINE_STOP_ERROR), 'SSL'));
    } else {
      //削除（退会）処理
      TEP_DB_QUery("delete from " . TABLE_MAIL_MAGAZINE . " where mag_email = '".$mag_mail."' and site_id = '".SITE_ID."'");
      
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
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
  <table class="box_des" width="100%" border="0" align="center" cellpadding="0" cellspacing="0"> 
          <tr> 
            <td> <br> 
              <strong><?php echo HEADING_TITLE ; ?> </strong> </td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          <tr> 
            <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2" align="center"> 
                <tr> 
                  <td><table class="box_des" width="100%" border="0" cellspacing="0" cellpadding="2"> 
                      <tr> 
                        <td colspan="2" valign="top" class="main"><?php echo HEADING_MAGAZINE_TITLE_ACCOUNT; ?></td> 
                      </tr> 
                      <tr> 
                        <td valign="top"><table width="100%" height="100%" border="0" cellpadding="1" cellspacing="0" class="box_des"> 
                            <tr> 
                              <td><table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0" class="box_des"> 
                                  <tr> 
                                    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                  </tr> 
                                  <tr> 
                                    <td class="main"><?php echo TEXT_TITLE_ACCOUNT ; ?></td> 
                                  </tr> 
                                  <tr> 
                                    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                  </tr> 
                                </table></td> 
                            </tr> 
                          </table></td> 
                        <td width="150" align="center" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . tep_image_button('button_edit_account.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                      </tr> 
                      <tr> 
                        <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                      </tr> 
                      <tr> 
                        <td colspan="2" valign="top" class="main"><?php echo HEADING_MAGAZINE_TITLE_REG; ?></td> 
                      </tr> 
                      <form method="post" action="<?php echo tep_href_link(FILENAME_MAGAZINE, 'action=regist', 'NONSSL'); ?>"> 
                        <tr> 
                          <td height="100%" valign="top"><table width="100%" height="100%" border="0" cellpadding="1" cellspacing="0" class="box_des"> 
                              <tr> 
                                <td><table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0" class="box_des"> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2" class="main"><?php echo TEXT_TITLE_REG; ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2" class="main"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td class="main"><?php echo TEXT_MAGAZINE_TITLE_NAME_REG; ?></td> 
                                      <td class="main"><input type="text" name="name" value="<?php echo $mag_name; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td class="main"><?php echo TEXT_MAGAZINE_TITLE_MAIL_REG; ?></td> 
                                      <td class="main"><input type="text" name="email" value="<?php echo $mag_mail; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                  </table></td> 
                              </tr> 
                            </table></td> 
                          <td width="150" align="center" valign="top"><?php echo tep_image_submit('button_continue.gif', TEXT_MAGAZINE_BUTTON_REG); ?></td> 
                        </tr> 
                      </form> 
                      <tr> 
                        <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                      </tr> 
                      <tr> 
                        <td colspan="2" class="main"><?php echo HEADING_MAGAZINE_TITLE_STP; ?></td> 
                      </tr> 
                      <form method="post" action="<?php echo tep_href_link(FILENAME_MAGAZINE, 'action=stop', 'NONSSL'); ?>"> 
                        <tr> 
                          <td align="right"><table width="100%" height="100%" border="0" cellpadding="1" cellspacing="0" class="box_des"> 
                              <tr> 
                                <td><table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0" class="box_des"> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2" class="main"><?php echo TEXT_TITLE_STP; ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                    <tr> 
                                      <td class="main"><?php echo TEXT_MAGAZINE_TITLE_MAIL_STP; ?></td> 
                                      <td class="main"><input type="text" name="email" value="<?php echo $mag_mail; ?>"></td> 
                                    </tr> 
                                    <tr> 
                                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                                    </tr> 
                                  </table></td> 
                              </tr> 
                            </table></td> 
                          <td width="150" align="center" valign="top"><?php echo tep_image_submit('button_delete.gif', TEXT_MAGAZINE_BUTTON_STP); ?></td> 
                        </tr> 
                      </form> 
                    </table> 
                    <br> 
                    <?php
          //session_unregister
          tep_session_unregister('mag_mail');
          tep_session_unregister('mag_name');
          ?></td> 
                </tr> 
              </table></td> 
          </tr> 
        </table></div>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof //-->
  <!-- body_eof //-->  
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
