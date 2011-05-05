<?php
/*
  $Id$
*/

//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log

// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS == 'true') {?>
<div id="debug_info">
  <pre>
<?php if(isset($logger)){
    foreach ($logger->queries as $qk => $qv) {
      echo '[' . $logger->times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
    }
  }
   print_r($_SESSION);
  ?>
  <?php //print_r($logger->times);?>
  </pre>
</div>
<?php }?>
