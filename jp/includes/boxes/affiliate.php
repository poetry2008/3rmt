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
<!--affiliate-->
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="affiliate"> 
        <tr> 
          <td height="25" align="center" background="images/design/box/box_title_bg.jpg"><?php echo tep_image(DIR_WS_IMAGES.'design/box/affiliate.gif',BOX_HEADING_AFFILIATE);?></td> 
        </tr> 
      </table> 
      <table width="100%"  border="0" cellspacing="1" cellpadding="0" summary="affiliate">
  <?php
  if (!tep_session_is_registered('affiliate_id')) {
?> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_INFO, '', 'NONSSL'). '">' . BOX_AFFILIATE_INFO . '</a>';?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_FAQ, '', 'NONSSL') . '">' . BOX_AFFILIATE_FAQ . '</a>';?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE, '', 'SSL') . '">' . BOX_AFFILIATE_LOGIN . '</a>' ; ?></td> 
   </tr> 
  <?php }else{ ?> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_SUMMARY, '', 'SSL') . '">' . BOX_AFFILIATE_SUMMARY . '</a></b>';?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_ACCOUNT, '', 'SSL') . '">' . BOX_AFFILIATE_ACCOUNT . '</a>';?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_PASSWORD, '', 'SSL') . '">' . BOX_AFFILIATE_PASSWORD . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_NEWSLETTER, '', 'SSL') . '">' . BOX_AFFILIATE_NEWSLETTER . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS . '</a></b>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BANNERS, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_BANNERS . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_BUILD, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_BUILD . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_PRODUCT, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_PRODUCT . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_TEXT, '', 'SSL') . '">' . BOX_AFFILIATE_BANNERS_TEXT . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<b><a href="' . tep_href_link(FILENAME_AFFILIATE_REPORTS, '', 'SSL') . '">' . BOX_AFFILIATE_REPORTS . '</a></b>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_CLICKS, '', 'SSL'). '">' . BOX_AFFILIATE_CLICKRATE . '</a>' ; ?></td> 
   </tr> 
  <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_SALES, '', 'SSL'). '">' . BOX_AFFILIATE_SALES . '</a>' ; ?></td> 
   </tr> <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_PAYMENT, '', 'SSL'). '">' . BOX_AFFILIATE_PAYMENT . '</a>' ; ?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_CONTACT, '', 'SSL') . '">' . BOX_AFFILIATE_CONTACT . '</a>';?></td> 
   </tr> 
  <tr> 
     <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" ><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE_LOGOUT). '">' . BOX_AFFILIATE_LOGOUT . '</a>' ; ?></td> 
   </tr> 
  <?php } ?> 
</table> 
<!--affiliate_eof//--> 
