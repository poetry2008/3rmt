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
    if(!$from){
      $from = $_SESSION['mail_post_value']['from'];
    }
    if(!$subject){
      $subject = $_SESSION['mail_post_value']['subject'];
    }
    if(!$message){
      $message = $_SESSION['mail_post_value']['message'];
    }


    if($_POST['back_mail']==''){
    //Let's build a message object using the email class
    // add the message to the object
    //while ($mail = tep_db_fetch_array($mail_query)) {
      //$mimemessage->send(tep_get_fullname($mail['customers_firstname'], $mail['customers_lastname']), $mail['customers_email_address'], '', $from, $subject);
    //}
    $mail_sum=0;
    $mail_sql = $_SESSION['mail_list'];
    if(isset($_SESSION['mail_sub_customer'])&&$_SESSION['mail_sub_customer']){
      $tmp_arr = explode(',',$_SESSION['mail_sub_customer']);
      if(isset($tmp_arr[0])&&$tmp_arr[0]){
        $mail_sql .= ' and c.customers_id not in ('.$_SESSION['mail_sub_customer'].') ';
      }
    }
    $mail_query = tep_db_query($mail_sql);
    while ($mail = tep_db_fetch_array($mail_query)) {
      $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
      $mimemessage->add_text($message);
      $mimemessage->build_message();
      $mimemessage->send(tep_get_fullname($mail['customers_firstname'],
            $mail['customers_lastname']), $mail['customers_email_address'], '',
          $from, $subject,'','mail');
      unset($mimemessage);
      $mail_sum++;
    }

      unset($_SESSION['mail_post_value']);
      unset($_SESSION['mail_list']);
      unset($_SESSION['mail_sub_customer']);
      tep_redirect(tep_href_link(FILENAME_MAIL, 'mail_sent_to=' .
            urlencode($mail_sent_to).'&mail_sum='.$mail_sum));
    }
  }

  if ( isset($_GET['action']) && ($_GET['action'] == 'preview')) {
    if (empty($_POST['se_pname']) && empty($_POST['se_mail']) && empty($_POST['se_cname']) && empty($_POST['se_site'])) {
      if(isset($_SESSION['mail_post_value'])&&empty($_SESSION['mail_post_value'])){
        $messageStack->add(ERROR_NO_SEARCH_TEXT, 'error');
      }
    }
   
    if(!isset($_SESSION['mail_post_value'])){
      $_SESSION['mail_post_value'] = array(); 
    }
    if(empty($_SESSION['mail_post_value'])){
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        $_SESSION['mail_post_value'][$key] = htmlspecialchars(stripslashes($value));
      }
    }
    }else{
      foreach($_SESSION['mail_post_value'] as $key => $value){
        $_POST[$key]=$value;
      }
    }
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
    $mail_select_sql = 'select c.customers_id,c.customers_firstname, c.customers_lastname, c.customers_email_address ';
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
      $mail_or_sql .= ' and s.name like \'%'.$_POST['se_site'].'%\''; 
    }
    
    if (!empty($_POST['se_mail'])) {
      $mail_or_sql .= ' and c.customers_email_address like \'%'.$_POST['se_mail'].'%\''; 
    }
    
    if (!empty($_POST['se_cname'])) {
      //$mail_or_sql .= ' or c.customers_firstname like \'%'.$_POST['se_cname'].'%\' or c.customers_lastname like \'%'.$_POST['se_cname'].'%\''; 
      $mail_or_sql .= ' and concat(c.customers_lastname, \' \', c.customers_firstname) like \'%'.$_POST['se_cname'].'%\''; 
    }
    
    if ($mail_where_sql != '') {
      $mail_where_sql = ' where '.substr($mail_where_sql, 5).' and ('.substr($mail_or_sql, 4).')'; 
    } else {
      $mail_where_sql = ' where '.substr($mail_or_sql, 4); 
    }
    
    $mail_sql .= $mail_select_sql.$mail_from_sql.$mail_where_sql;
    
    
    if(!isset($_SESSION['mail_list'])||$_SESSION['mail_list']){
      $_SESSION['mail_list'] = $mail_sql; 
    }

    //$messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if (isset($_GET['mail_sent_to']) && $_GET['mail_sent_to']) {
      $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO,
            isset($_GET['mail_sum'])?$_GET['mail_sum']:0), 'success');
  }
  if ($_GET['selected_box']=='tools') {
    $mail_sql = '';
    $mail_select_sql = 'select c.customers_id,c.customers_firstname, c.customers_lastname, c.customers_email_address ';
    $mail_from_sql = ' from '.TABLE_CUSTOMERS.' c'; 
    $mail_where_sql =
      ' where c.customers_email_address="'.tep_db_prepare_input($_GET['customer']).'"';
    if(isset($_GET['site_id'])&&$_GET['site_id']){
      $mail_where_sql .=
      ' and  c.site_id="'.tep_db_prepare_input($_GET['site_id']).'"';
    }
    $mail_sql .= $mail_select_sql.$mail_from_sql.$mail_where_sql;
    if(!isset($_SESSION['mail_list'])||$_SESSION['mail_list']){
      $_SESSION['mail_list'] = $mail_sql; 
    }
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
<script language="javascript" >
function valadate_search(){
  if(
  !$("input[name=se_pname]").val()&&
  !$("input[name=se_mail]").val()&&
  !$("input[name=se_cname]").val()&&
  !$("input[name=se_site]").val()
  ){
    alert("<?php echo TEXT_LESS_THAN_ONE_SEARCH;?>");
    return false;
  }else{
    return true;
  }

}
function save_mail_info(){
  mail_info_from = $("#mail_info_from").val();
  mail_info_subject = $("#mail_info_subject").val();
  mail_info_message = $("#mail_info_message").val();
  $.ajax({
    url: 'ajax_orders.php?action=save_mail_info',
    data: 'mail_info_from='+mail_info_from+'&mail_info_subject='+mail_info_subject+'&mail_info_message='+mail_info_message,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
    }
  });
}
function change_select_mail(_this){
  if($(_this).attr('checked')){
    mail_list_action = 'add';
  }else{
    mail_list_action = 'sub';
  }
  $.ajax({
    url: 'ajax_orders.php?action=change_mail_list',
    data: 'mail_list_value='+_this.value+'&mail_list_action='+mail_list_action,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
    }
  });
}
function back_to_mail(){
  document.mail.back_mail.value = 'back';
  document.mail.submit();
}
function send_mail_validate(){
  save_mail_info();
  var flag_checkbox = true;
  $.ajax({
    url: 'ajax_orders.php?action=mail_checkbox_validate',
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(data){
      if(data == 'true'){
        flag_checkbox=false;
      }
    }
  });
  if(flag_checkbox){
    return true;
  }else{
    alert("<?php echo TEXT_NO_SELECTED_CHECKBOX;?>");
    return false; 
  }
}
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
          <tr>
          <td colspan='2'>

          <?php 
          if($_GET['action']=='preview'||(isset($_GET['customer'])&&$_GET['customer'])){
            echo TEXT_MAIL_RESULT_INFO;
          }else{
            echo TEXT_MAIL_SEARCH_INFO;
          }
          ?>
          </td>
          </tr>
          <tr>
            <td colspan='2'></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (( isset($_GET['action']) && ($_GET['action'] ==
          'preview')&&($_POST['se_pname'] || $_POST['se_mail'] || $_POST['se_cname']
            || $_POST['se_site']) && !isset($error_email_single))||(
        isset($_GET['selected_box'])&&$_GET['selected_box']=='tools' )){
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_MAIL,
              'action=send_email_to_user','post','onsubmit="return send_mail_validate()"'); ?>
            <td id="mail_left_td"><table border="0" cellpadding="0" cellspacing="2">
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
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td><?php echo tep_draw_input_field('from',
                    $_POST['from']?$_POST['from']:EMAIL_FROM,'id="mail_info_from"'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo
                tep_draw_input_field('subject',$_POST['subject']?$_POST['subject']:'',
                    'id="mail_info_subject"'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60',
                    '15',$_POST['message']?$_POST['message']:'','id="mail_info_message"'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
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
                    <td><?php 
                    if($_GET['selected_box']!='tools'){
                    echo tep_html_element_submit(IMAGE_BACK,'onclick="back_to_mail()"');
                    }else{
                    echo '<a class="new_product_reset" href="' .
                      tep_href_link(FILENAME_CUSTOMERS,'page='.$_GET['customer_page'].'&site_id='.
                          $_GET['site_id'].'&cID='.$_GET['cID']). '">' .
                      tep_html_element_button(IMAGE_BACK) . '</a> ';
                    }
                    ?></td>
                    <td align="right"><?php echo '<a class="new_product_reset" href="' . tep_href_link(FILENAME_MAIL) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a> ' .  tep_html_element_submit(BUTTON_SENDMAIL_TEXT); ?></td>
                  </tr>
            </table></td>
          </form></tr>
              </table></td>
                <td valign="top" width="50%">
                <table>
                <tr>
                <td id = "search_mail_list" valign="top">
                <table>
                <tr>
                  <td></td>
                  <td><?php echo TEXT_CUSTOMER_FULL_NAME;?></td>
                  <td><?php echo TEXT_CUSTOMER_EMAIL;?></td>
                </tr>
                <?php //checkbox
                $mail_split = new splitPageResults($_GET['page'],
                    MAX_DISPLAY_CUSTOMER_MAIL_RESULTS,$mail_sql,$mail_query_numrows);
                $mail_query = tep_db_query($mail_sql);
                $mail_sub_customer_arr =
                  explode(',',$_SESSION['mail_sub_customer']);
                while($mail_row = tep_db_fetch_array($mail_query)){
                  echo "<tr>";
                  echo "<td>";
                  echo "<input type='checkbox' name='mail_list_checkbox[]'
                    onclick='change_select_mail(this)' value='".
                    $mail_row['customers_id']."' ";
                  if(!in_array($mail_row['customers_id'],$mail_sub_customer_arr)){
                    echo " checked='checked' "; 
                  }
                  echo ">";
                  echo "</td>";
                  echo
                    "<td>".tep_get_fullname($mail_row['customers_firstname'],
                        $mail_row['customers_lastname'])."</td>";
                  echo "<td>".$mail_row['customers_email_address']."</td>";
                  echo "</tr>";
                }
                ?> 
                </table>
                </td>
                </tr>
                <tr>
                <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText" height="35">
                <?php
                // page split 
                echo 
                $mail_split->display_count($mail_query_numrows,
                    MAX_DISPLAY_CUSTOMER_MAIL_RESULTS,$_GET['page'],
                    TEXT_DISPLAY_NUMBER_OF_MAIL);
                ?>
                  </td>
                  <td class="smallText" align="right">
                <?php
                echo 
                $mail_split->display_links($mail_query_numrows,
                    MAX_DISPLAY_CUSTOMER_MAIL_RESULTS,
                    MAX_DISPLAY_PAGE_LINKS,$_GET['page'],
                    tep_get_all_get_params(array('page')),
                    ' onclick="save_mail_info()" ');
                ?>
                  </td>
                <tr>
                </table>
                </td>
                </tr>
                </table>
                </td>
              </tr>

<?php
  } else {
    unset($_SESSION['mail_post_value']);
    unset($_SESSION['mail_list']);
    unset($_SESSION['mail_sub_customer']);
?>
          <tr><?php echo tep_draw_form('mail', FILENAME_MAIL,
              'action=preview','post','onsubmit="return valadate_search()"'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
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
