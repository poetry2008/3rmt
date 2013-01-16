<?php
/*
    $Id$
*/
$_noemailclass = true;
  require_once('includes/application_top.php');
$breadcrumb->add('お問い合わせ', tep_href_link(FILENAME_CONTACT_US));
//require('includes/configure.php');
require_once('includes/ost/client.inc.php');
//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require_once('tickets.php');
    exit;
}
mysql_select_db(DB_DATABASE);
require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="contact_us_warpper">
<div class="lcol">
  <div class="contact_left"><img src="./images/new_ticket_icon.jpg" width="60" height="60" alt="img"></div>
  <div class="contact_right">
  <h3>新規お問い合わせ</h3>
    </div>
    <form method="link" action="<?php echo tep_href_link('open.php','','SSL')?>">
  <input type="image" class="button2" value="お問い合わせ" src="includes/languages/japanese/images/buttons/lemail.gif">
  </form>
</div>
<div class="rcol">
  <div class="contact_left"><img src="./images/ticket_status_icon.jpg" width="60" height="60" alt="img"></div>
  <div class="contact_right">
  <h3>お問い合わせへの返事を確認</h3> 
  <form class="status_form" action="<?php echo tep_href_link('contact_us_login.php','','SSL')?>" method="post">
    <div class="status_warpper">
      <label>メールアドレス：</label>
      <input type="text" name="lemail" class="input_text">
      </div>
      <div class="status_warpper">
     <label>お問い合わせ番号:</label>
     <input type="text" name="lticket" class="input_text">
     </div>
     <div class="status_warpper02">
         <input type="image" class="button2" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif">
     </div>

  </form>
</div>
</div>
</div>
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
