<?php
/*
  $Id$

*/
?>
<br>
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
