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
<div class="tcol">
<h1></h1>
<p></p>
</div>
<div class="lcol">
  <div class="contact_left"><img src="./images/new_ticket_icon.gif" width="48" height="48"></div>
  <div class="contact_right">
  <h3>&#26032;&#35215;&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;</h3>
  <form method="link" action="open.php">
  <input type="image" class="button2" value="&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;" src="includes/languages/japanese/images/buttons/lemail.gif">
  </form>
</div>
</div>
<div class="rcol">
  <div class="contact_left"><img src="./images/ticket_status_icon.gif" width="48" height="48"></div>
  <div class="contact_right">
  <h3>&#36942;&#21435;&#12398;&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;&#12408;&#12398;&#36820;&#20107;&#12434;&#30906;&#35469;</h3>
  </div>
  <form class="status_form" action="contact_us_login.php" method="post">
    <div class="status_warpper">
      <label>&#12513;&#12540;&#12523;&#12450;&#12489;&#12524;&#12473;&#65306;</label>
      <input type="text" name="lemail">
    </div>
    <div class="status_warpper">
     <label>&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;&#30058;&#21495;:</label>
     <input type="text" name="lticket">
    </div>
         <input type="image" class="button2" value="&#36865;&#20449;" src="includes/languages/japanese/images/buttons/button_send_mail.gif">
  </form>
</div>
<div class="clear"></div>
<br />
</div>
<br />
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
