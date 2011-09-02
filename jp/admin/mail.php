<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  set_time_limit(0);

  if ( isset($_GET['action']) && ($_GET['action'] == 'send_email_to_user') &&
      (!$_POST['back_mail']) ) {
    $mail_sent_to = '';
    
    if ($_POST['se_pname']) {
      $se_name = $_POST['se_pname'];
      $mail_sent_to .= $_POST['se_pname'].','; 
    }
    if ($_POST['se_mail']) {
      $mail_sent_to .= $_POST['se_mail'].','; 
    }
    if ($_POST['se_cname']) {
      $mail_sent_to .= $_POST['se_cname'].','; 
    }
    if ($_POST['se_site']) {
      $mail_sent_to .= $_POST['se_site'].','; 
    }

    if ($mail_sent_to != '') {
      $mail_sent_to = substr($mail_sent_to, 0, -1); 
    }
       
    $from = tep_db_prepare_input($_POST['from']);
    $subject = tep_db_prepare_input($_POST['subject']);
    $message = tep_db_prepare_input($_POST['message']);



    if($_POST['back_mail']==''){
    //Let's build a message object using the email class
    $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
    // add the message to the object
    $mimemessage->add_text($message);
    $mimemessage->build_message();
    //while ($mail = tep_db_fetch_array($mail_query)) {
      //$mimemessage->send(tep_get_fullname($mail['customers_firstname'], $mail['customers_lastname']), $mail['customers_email_address'], '', $from, $subject);
    //}
    $mail_sum=0;
    foreach($_POST['mail_list'] as $mail){
      $mail_arr = explode('|_|',$mail);
      $mimemessage->send(tep_get_fullname($mail_arr['0'], $mail_arr['1']), $mail_arr['2'], '', $from, $subject);
      $mail_sum++;
    }

      tep_redirect(tep_href_link(FILENAME_MAIL, 'mail_sent_to=' .
            urlencode($mail_sent_to).'&mail_sum='.$mail_sum));
    }
  }

  if ( isset($_GET['action']) && ($_GET['action'] == 'preview')) {
    if (empty($_POST['se_pname']) && empty($_POST['se_mail']) && empty($_POST['se_cname']) && empty($_POST['se_site'])) {
      $messageStack->add(ERROR_NO_SEARCH_TEXT, 'error');
    }
    /*
    if (!empty($_POST['se_mail'])) {
      if (!preg_match("/^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/", $_POST['se_mail'])) {
        $messageStack->add(ERROR_EMAIL_WRONG_TYPE, 'error');
        $error_email_single = true; 
      }
    }
    */
    $mail_sent_to = '';
    
    if ($_POST['se_pname']) {
      $se_name = $_POST['se_pname'];
      $mail_sent_to .= $_POST['se_pname'].','; 
    }
    if ($_POST['se_mail']) {
      $mail_sent_to .= $_POST['se_mail'].','; 
    }
    if ($_POST['se_cname']) {
      $mail_sent_to .= $_POST['se_cname'].','; 
    }
    if ($_POST['se_site']) {
      $mail_sent_to .= $_POST['se_site'].','; 
    }

    if ($mail_sent_to != '') {
      $mail_sent_to = substr($mail_sent_to, 0, -1); 
    }
    
    $mail_sql = ''; 
    $mail_select_sql = 'select c.customers_firstname, c.customers_lastname, c.customers_email_address ';
    $mail_from_sql = '';
    $mail_where_sql = '';
    $mail_or_sql = '';
   
    if (!empty($_POST['se_pname'])) {
      $mail_from_sql .= ' from '.TABLE_ORDERS.' p, '.TABLE_ORDERS_PRODUCTS.' op, '.TABLE_CUSTOMERS.' c'; 
      $mail_where_sql .= ' and p.customers_id = c.customers_id and p.orders_id = op.orders_id'; 
      $mail_or_sql .= ' or op.products_name like \''.$_POST['se_pname'].'\''; 
    } else {
      $mail_from_sql .= ' from '.TABLE_CUSTOMERS.' c'; 
    } 
    
    if (!empty($_POST['se_site'])) {
      $mail_from_sql .= ', '.TABLE_SITES.' s'; 
      $mail_where_sql .= ' and c.site_id = s.id'; 
      $mail_or_sql .= ' or s.name like \'%'.$_POST['se_site'].'%\''; 
    }
    
    if (!empty($_POST['se_mail'])) {
      $mail_or_sql .= ' or c.customers_email_address like \'%'.$_POST['se_mail'].'%\''; 
    }
    
    if (!empty($_POST['se_cname'])) {
      //$mail_or_sql .= ' or c.customers_firstname like \'%'.$_POST['se_cname'].'%\' or c.customers_lastname like \'%'.$_POST['se_cname'].'%\''; 
      $mail_or_sql .= ' or concat(c.customers_lastname, \' \', c.customers_firstname) like \'%'.$_POST['se_cname'].'%\''; 
    }
    
    if ($mail_where_sql != '') {
      $mail_where_sql = ' where '.substr($mail_where_sql, 4).' and ('.substr($mail_or_sql, 3).')'; 
    } else {
      $mail_where_sql = ' where '.substr($mail_or_sql, 3); 
    }
    
    $mail_sql .= $mail_select_sql.$mail_from_sql.$mail_where_sql;
    
    $mail_query = tep_db_query($mail_sql);

    //$messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if (isset($_GET['mail_sent_to']) && $_GET['mail_sent_to']) {
      $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO,
            isset($_GET['mail_sum'])?$_GET['mail_sum']:0), 'success');
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/jquery_select.js"></script>
<script language="javascript" >
function back_to_mail(){
  document.mail.back_mail.value = 'back';
  document.mail.submit();
}
$(document).ready(function(){
$("#search_mail_list").dgMagnetCombo();
});
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'preview')&&($_POST['se_pname'] || $_POST['se_mail'] || $_POST['se_cname'] || $_POST['se_site']) && !isset($error_email_single)) {
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_MAIL, 'action=send_email_to_user'); ?>
            <td width="50%"><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo SEARCH_MAIL_PRODUCTS_NAME; ?></b><br><?php echo $_POST['se_pname']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo SEARCH_MAIL_TITLE; ?></b><br><?php echo $_POST['se_mail']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo SEARCH_MAIL_CUSTOMERS_NAME; ?></b><br><?php echo $_POST['se_cname']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo SEARCH_MAIL_SITENAME; ?></b><br><?php echo $_POST['se_site']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
                </table></td>
                <td valign="top">
                <select id="search_mail_list" multiple="multiple" name="mail_list[]" id="search_mail_list">
                <?php
                $temp_mail_arr  = array();
                while($mail_row = tep_db_fetch_array($mail_query)){
                  if(in_array($mail_row['customers_email_address'],$temp_mail_arr)){
                    continue;
                  }
                  echo "<option
                    value='".$mail_row['customers_firstname']."|_|"
                    .$mail_row['customers_lastname']."|_|"
                    .$mail_row['customers_email_address']."' >";
                  echo $mail_row['customers_firstname']." "
                    .$mail_row['customers_lastname']." "
                    .$mail_row['customers_email_address'];
                  echo "</option>";
                  $temp_mail_arr[] = $mail_row['customers_email_address'];
                }
                if(empty($temp_mail_arr)){
                  echo "<option>";
                  echo NO_MAIL_RESULT;
                  echo "</option>";
                }
                ?>
                </select>
                </td>
              </tr>
              <tr>
                <td colspan="2">
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
    echo tep_draw_hidden_field('back_mail','');
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td><?php echo
                    tep_html_element_submit(IMAGE_BACK,'onclick="back_to_mail()"');?></td>
                    <td align="right"><?php echo '<a class="new_product_reset" href="' . tep_href_link(FILENAME_MAIL) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a> ' .  tep_html_element_submit(BUTTON_SENDMAIL_TEXT); ?></td>
                  </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    /* 
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    $mail_query = tep_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = tep_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
    */    
?>
              <tr>
                <td class="main"><?php echo SEARCH_MAIL_PRODUCTS_NAME; ?></td>
                <td>
                <?php echo tep_draw_input_field('se_pname');?> 
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo SEARCH_MAIL_TITLE; ?></td>
                <td>
                <?php echo tep_draw_input_field('se_mail');?> 
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo SEARCH_MAIL_CUSTOMERS_NAME; ?></td>
                <td>
                <?php echo tep_draw_input_field('se_cname');?> 
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo SEARCH_MAIL_SITENAME; ?></td>
                <td>
                <?php echo tep_draw_input_field('se_site');?> 
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo tep_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo tep_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo
                tep_html_element_submit(BUTTON_SEARCH_TEXT); ?></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
