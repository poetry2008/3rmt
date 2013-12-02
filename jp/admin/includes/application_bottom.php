<?php
/*
  $Id$
*/

// close session (store variables)
  tep_session_close();

  if (STORE_PAGE_PARSE_TIME == 'true') {
    if (!is_object($logger)) {
    $logger = new logger;
    echo $logger->timer_stop(DISPLAY_PAGE_PARSE_TIME);
    }
  }
//输出压缩页面
ob_end_flush();
?>
