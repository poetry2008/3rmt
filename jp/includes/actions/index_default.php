<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
<?php 
  echo DEFAULT_PAGE_TOP_CONTENTS;
  include(DIR_WS_MODULES . 'categories_banner_text.php');
  include(DIR_WS_MODULES . FILENAME_NEWS);
  echo DEFAULT_PAGE_BOTTOM_CONTENTS;
?>
      </td>
    <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
