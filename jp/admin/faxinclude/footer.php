<?php
/*
  $Id$

*/
?>
<br>
<!--
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="center" class="smallText">
<?php
/*
  The following copyright announcement is in compliance
  to section 2c of the GNU General Public License, and
  thus can not be removed, or can only be modified
  appropriately.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:


  Please leave this comment intact together with the
  following copyright announcement.
*/
?>
    </td>
  </tr>
  <tr>
    <td><?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5'); ?></td>
  </tr>
  <tr>
    <td align="center" class="smallText">Powered by <a href="http://www.oscommerce.com" target="_blank">osCommerce</a></td>
  </tr>
</table>
-->
<?php if (STORE_DB_TRANSACTIONS) {?>
<div id="debug_info">
<pre>
<?php //print_r($logger->queries);?>
</pre>
</div>
<?php }?>
<?php 
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>
