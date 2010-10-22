<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/
$_noemailclass = true;
  require('includes/application_top.php');
  $breadcrumb->add('お問い合わせ', tep_href_link(FILENAME_CONTACT_US));
//require('includes/configure.php');
require('includes/ost/client.inc.php');

//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require('tickets.php');

    exit;
}

require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="contact_us_warpper">
<div class="tcol">
<h1></h1>
<p>
</p>
</div>
<div class="lcol">
  <div class="contact_left"><img src="./images/new_ticket_icon.jpg" width="60" height="60"></div>
  <div class="contact_right">
  <h3>新規お問い合わせ</h3>
  
  <br>
  <form method="link" action="open.php">
  <input type="submit" class="button2" value="お問い合わせ">
  </form>
  </div>
</div>
<div class="rcol">
  <div class="contact_left"><img src="./images/ticket_status_icon.jpg" width="60" height="60"></div>
  <div class="contact_right">
  <h3>過去のお問い合わせへの返事を確認</h3>
  
  <br>
    <form class="status_form" action="contact_us_login.php" method="post">
    <div class="status_warpper">
      <label>メールアドレス：</label>
      <input type="text" name="lemail">
      </div>
      <div class="status_warpper">
     <label>お問い合わせ番号:</label>
     <input type="text" name="lticket">
     </div>
     <div class="status_warpper02">
        <label>&nbsp;</label>
         <input type="submit" class="button2" value="送信">
     </div>

  </form>
</div>
</div>
<div class="clear"></div>
<br />
</div>
<br />
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
