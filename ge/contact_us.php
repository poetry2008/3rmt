<?php
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
require_once(CLIENTINC_DIR.'header.inc.php');
?>
<div id="contact_us_warpper">
<div class="lcol">
  <div class="contact_left"><img src="./images/new_ticket_icon.gif" width="48" height="48"></div>
  <div class="contact_right">
  <h3>新規お問い合わせ</h3>
  
</div>
<form method="link" action="<?php echo tep_href_link('open.php','','SSL')?>">
  <input type="image" class="button2" value="お問い合わせ" src="includes/languages/japanese/images/buttons/lemail.gif">
  </form>
</div>
<div class="rcol">
  <div class="contact_left"><img src="./images/ticket_status_icon.gif" width="48" height="48"></div>
  <div class="contact_right">
  <h3>お問い合わせへの返事を確認</h3>
  </div>
  <form class="status_form" action="<?php echo tep_href_link('contact_us_login.php','','SSL')?>" method="post">
    <div class="status_warpper">
      <label>メールアドレス</label>
      <input class="input_text" type="text" name="lemail">
    </div>
    <div class="status_warpper">
     <label>お問い合わせ番号</label>
     <input class="input_text" type="text" name="lticket">
    </div>
         <input type="image" class="button2" value="送信" src="includes/languages/japanese/images/buttons/button_send_mail.gif">
  </form>
</div>
<div class="clear"></div>
<br />
</div>
<br />
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
