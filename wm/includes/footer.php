<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<table id="footer" cellpadding="0" cellspacing="0" summary="footer">
	<tr>
    	<td class="footer_1"></td>
        <td class="footer_2"></td>
        <td class="footer_3"></td>
    </tr>
    <tr>
    	<td colspan="3">
        	<div class="footer_games_box">
        	<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div class="footer_games" align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>	
            </div>
        </td>
    </tr>
    <tr>
    	<td class="footer_4"></td>
        <td class="footer_5">
        	<address class="footer_contacts">
			  <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;2009&nbsp;&nbsp;<a class="bold" href="http://rmt.worldmoney.jp/"><strong>RMT</strong>ワールドマネー</a>
            </address>
        </td>
        <td class="footer_6"></td>
    </tr>
</table>
<?php /*
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&&p=g&&md=nj" alt="" height="1" width="1" ></noscript>
*/?>
<?php 
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>
