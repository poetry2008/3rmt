<?php
/*
  $Id: footer.php,v 1.1 2003/09/08 19:26:06 jhtalk Exp jhtalk $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<div id="footer">
	<?php echo FOOTER_TEXT_BODY . "\n"; ?>
	<address>
		Copyright&nbsp;&copy;&nbsp;2004-2009&nbsp;Jackpot&nbsp;<a class="bold" href="http://www.iimy.co.jp/"><strong>RMT</strong>ジャックポット</a>
	</address>
	<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<br><div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>
</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
<?php
/*
Some functions Supported by <a href="http://www.ds-style.com">DigitalStudio</a> INC.  Powered by <a href="http://osc.ds-style.com">osCommerce</a>
*/
?>
<?php 
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>
