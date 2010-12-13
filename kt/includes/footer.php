<?php
/*
  $Id$
*/
?>
<div id="footer">
          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;2009-2010&nbsp;&nbsp;<a class="bold" href="http://www.rmt-kames.jp/">亀'sTime</a>
            </address>

</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
<?php 
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>
