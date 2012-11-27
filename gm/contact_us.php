<?php
$_noemailclass = true;
require_once('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/contact_us.php');
$breadcrumb->add(HEADING_TITLE, tep_href_link(FILENAME_CONTACT_US));
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
 <table border="0" width="100%" cellspacing="0" cellpadding="0"
 class="contact_us_spacing">
 <tr>
  <td width="5%"><img src="./images/new_ticket_icon.gif" width="48" height="48"></td>
    <td width="85%">  
  <h3>&#26032;&#35215;&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;</h3>
  </td>
  <td align="left" valign="bottom">
  <form method="link" action="<?php echo tep_href_link('open.php','','SSL')?>">
  <input type="image" class="button2" value="&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;" src="includes/languages/japanese/images/buttons/lemail.gif"
   onmouseover="this.src='includes/languages/japanese/images/buttons/lemail_hover.gif'"
   onmouseout="this.src='includes/languages/japanese/images/buttons/lemail.gif'"
  
  >
  </form>
 </td>
  </tr>
<tr>
  <td height="100" width="5%" valign="top"><img src="./images/ticket_status_icon.gif" width="48" height="48"></td>
  <td>
  <form class="status_form" action="<?php echo tep_href_link('contact_us_login.php','','SSL')?>" method="post"> 
   <table border="0" width="100%" cellspacing="0" cellpadding="0" >
   <tr><td colspan="2" style="font-size:18px"><?php echo TEXT_PROMPT_CONFIRM;?></td></tr>
   <tr><td width="30%">&#12513;&#12540;&#12523;&#12450;&#12489;&#12524;&#12473;&#65306;</td>
   <td width="70%"><input id="input_text" type="text" name="lemail" style="width:300px;"></td></tr>
   <tr><td width="30%">&#12362;&#21839;&#12356;&#21512;&#12431;&#12379;&#30058;&#21495;&#65306;</td>
   <td width="70%"><input id="input_text" type="text" name="lticket" style="width:300px;"></td></tr>
    </table>
  </td>
<td align="left" valign="bottom">
    <input type="image" class="button2" value="&#36865;&#20449;"
    src="includes/languages/japanese/images/buttons/button_send_mail.gif"
    onmouseover="this.src='includes/languages/japanese/images/buttons/button_send_mail_hover.gif'"
    onmouseout="this.src='includes/languages/japanese/images/buttons/button_send_mail.gif'">
    </td> 
     </tr>
     </table>
     </form>
<?php  require(CLIENTINC_DIR.'footer.inc.php'); ?>
