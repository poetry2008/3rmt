<?php
/*
  $Id$
*/
?>
 <div id="content">
   <h2 class="index_h2">はじめてRMTゲームマネーをご利用いただくお客様へ</h2> 
<?php 
  // @TODO 改成设置
  $contents = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '" . SITE_ID . "'");//top
  $result = tep_db_fetch_array($contents) ;

  echo $result['text_information'];
?>
</div>      
<!-- body_text_eof //--> 
<!--column_right -->
<div id="r_menu">
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<?php
