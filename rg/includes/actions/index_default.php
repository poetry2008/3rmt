<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
    <div class="top_index_image">
    <script type='text/javascript' src="js/flash_rmt.js"></script>
    <script type="text/javascript" src="js/images.js"></script>
    <noscript>
    <a href="index.php"><img src="images/design/banner02.gif" width="530" height="210" alt="RMT" ></a>
    </noscript>    </div>
<?php
  echo DEFAULT_PAGE_TOP_CONTENTS;
?>
<?php 
 
  include(DIR_WS_MODULES . 'categories_banner_text.php');
  include(DIR_WS_MODULES . FILENAME_NEWS);
?>
<?php
  echo DEFAULT_PAGE_BOTTOM_CONTENTS;
?>
      </td>
    <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
