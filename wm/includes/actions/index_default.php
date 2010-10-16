<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
<?php 
  // @TODO 改成设置
  #$contents1 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '10' and site_id = '" . SITE_ID . "'");  //top1
  #$result1   = tep_db_fetch_array($contents1) ;
  #$contents2 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '" . SITE_ID . "'");  //top2
  #$result2   = tep_db_fetch_array($contents2) ;
  
  include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);
  include(DIR_WS_MODULES . 'categories_banner_text.php');
?>
<?php
  echo DEFAULT_PAGE_TOP_CONTENTS;
?>
<?php
  echo DEFAULT_PAGE_BOTTOM_CONTENTS;
?>
      </td>
    <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
