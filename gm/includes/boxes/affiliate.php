<?php
/*
  $Id: affiliate.php,v 2.00 2003/10/12

  OSC-Affiliate

  Contribution based on:

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!--affiliate--><div class="box_title">AFFILIATE</div>
<ul id="box">
  <?php
  if (!tep_session_is_registered('affiliate_id')) {
?>  
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_INFO, '', 'NONSSL'). '">' . BOX_AFFILIATE_INFO . '</a>';?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_FAQ, '', 'NONSSL') . '">' . BOX_AFFILIATE_FAQ . '</a>';?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE, '', 'SSL') . '">' . BOX_AFFILIATE_LOGIN . '</a>' ; ?></li> 
<?php 
  }else{ 
?> 
    <li><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_SUMMARY, '', 'SSL') . '">' . BOX_AFFILIATE_SUMMARY . '</a></b>';?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_ACCOUNT, '', 'SSL') . '">' . BOX_AFFILIATE_ACCOUNT . '</a>';?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_PASSWORD, '', 'SSL') . '">' . BOX_AFFILIATE_PASSWORD . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_NEWSLETTER, '', 'SSL') . '">' . BOX_AFFILIATE_NEWSLETTER . '</a>' ; ?></li> 
    <li><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS . '</a></b>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BANNERS, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_BANNERS . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BUILD, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_BUILD . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_PRODUCT, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_PRODUCT . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_TEXT, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_TEXT . '</a>' ; ?></li> 
    <li><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_REPORTS, '', 'SSL') . '">' . BOX_AFFILIATE_REPORTS . '</a></b>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_CLICKS, '', 'SSL'). '">' . BOX_AFFILIATE_CLICKRATE . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_SALES, '', 'SSL'). '">' . BOX_AFFILIATE_SALES . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_PAYMENT, '', 'SSL'). '">' . BOX_AFFILIATE_PAYMENT . '</a>' ; ?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_CONTACT, '', 'SSL') . '">' . BOX_AFFILIATE_CONTACT . '</a>';?></li> 
    <li><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_LOGOUT). '">' . BOX_AFFILIATE_LOGOUT . '</a>' ; ?></li> 
<?php } ?> 
</ul>
<!--affiliate_eof//--> 
