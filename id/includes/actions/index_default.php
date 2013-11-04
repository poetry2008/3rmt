<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
<?php 
  echo DEFAULT_PAGE_TOP_CONTENTS;
  
  echo '<div class="news_reviews05">';
  include(DIR_WS_MODULES . FILENAME_NEWS);
  include(DIR_WS_BOXES . 'index_reviews.php'); 
  echo '</div>';
  echo '<div class="info_index05">';
  echo DEFAULT_PAGE_BOTTOM_CONTENTS;
  echo '</div>';
?>
      </td>
    <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
