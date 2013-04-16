<?php
/*
  $Id$

*/

  class newsletter {
    var $show_choose_audience, $title, $content;

/* -------------------------------------
    功能: 构造函数 
    参数: $title(string) 标题   
    参数: $content(string) 内容   
    返回值: 无 
------------------------------------ */
    function newsletter($title, $content) {
      $this->show_choose_audience = false;
      $this->title = $title;
      $this->content = $content;
    }

/* -------------------------------------
    功能: 选择观众 
    参数: 无 
    返回值: 是否选择(boolean) 
------------------------------------ */
    function choose_audience() {
      return false;
    }

/* -------------------------------------
    功能: 确认信息 
    参数: $site_id(int) 网站id 
    返回值: 确认信息的html(string) 
------------------------------------ */
    function confirm($site_id='') {
      global $_GET;

      if($site_id){
        $mail_query = tep_db_query("select count(*) as count from " .
            TABLE_CUSTOMERS . " where customers_newsletter = '1' and site_id
            ='".$site_id."'");
    $mag_query = tep_db_query("select count(*) as count from mail_magazine where
        site_id = '".$site_id."'");
      }else{
      $mail_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
    $mag_query = tep_db_query("select count(*) as count from mail_magazine");
      }
      $mail = tep_db_fetch_array($mail_query);

    
    //add
    $mag = tep_db_fetch_array($mag_query);
    
    $mag_count = $mail['count'] + $mag['count'];
    
    $confirm_string = '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                        '  <tr>' . "\n";
    if (defined('DIR_WS_ADMIN')) {
      $confirm_string .=  '    <td class="main"><font color="#ff0000">' .  sprintf(TEXT_COUNT_CUSTOMERS, $mag_count) . '</font></td>' . "\n";
    } else {
      $confirm_string .=  '    <td class="main"><font color="#ff0000"><b>' .  sprintf(TEXT_COUNT_CUSTOMERS, $mag_count) . '</b></font></td>' . "\n";
    }
    
    $confirm_string .= '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n";
    if (defined('DIR_WS_ADMIN')) {
      $confirm_string .= '    <td class="main">' . $this->title . '</td>' .  "\n";
    } else {
      $confirm_string .= '    <td class="main"><b>' . $this->title . '</b></td>' .  "\n";
    }
    $confirm_string .=  '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><tt>' . nl2br($this->content) . '</tt></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td align="right">';
                        if ($mag_count) {
                          $confirm_string .= '<a href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] .  '&action=confirm_send' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'') .(isset($_GET['send_site_id'])?('&send_site_id='.$_GET['send_site_id']):'')) . '">' . tep_html_element_button(IMAGE_SEND) .  '</a>';
                        } else {
                          $confirm_string .= '<a href="javascript:void(0);">' . tep_html_element_button(IMAGE_SEND, 'onclick="check_send_mail();"') .  '</a>';
                        }
                               $confirm_string .= '<a class="new_product_reset" href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '</table>';

      return $confirm_string;
    }

/* -------------------------------------
    功能: 发送邮件 
    参数: $newsletter_id(int) 杂志id 
    参数: $site_id(int) 网站id 
    返回值: 无 
------------------------------------ */
    function send($newsletter_id,$site_id='') {
      if($site_id){
      $mail_query = tep_db_query("select customers_firstname, customers_lastname,
          customers_email_address from " . TABLE_CUSTOMERS . " where
          customers_newsletter = '1' and site_id='".$site_id."'");
      }else{
      $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
      }

      $mimemessage = new email(array('X-Mailer: iimy Mailer'));
      $mimemessage->add_text($this->content);
      $mimemessage->build_message();
      while ($mail = tep_db_fetch_array($mail_query)) {
        $mimemessage->send(tep_get_fullname($mail['customers_firstname'], $mail['customers_lastname']), $mail['customers_email_address'], '', EMAIL_FROM, $this->title);
      }
    
    //add
    if($site_id){
    $mag_query = tep_db_query("select * from mail_magazine where site_id =
        '".$site_id."'");
    }else{
    $mag_query = tep_db_query("select * from mail_magazine");
    }
    while($mag = tep_db_fetch_array($mag_query)) {
        $mimemessage->send($mag['mag_name'], $mag['mag_email'], '', EMAIL_FROM, $this->title);
    }

      $newsletter_id = tep_db_prepare_input($newsletter_id);
      tep_db_query("update " . TABLE_NEWSLETTERS . " set date_sent = now(), status = '1' where newsletters_id = '" . tep_db_input($newsletter_id) . "'");
    }
  }
?>
