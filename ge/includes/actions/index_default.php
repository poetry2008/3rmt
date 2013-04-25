<?php
/*
  $Id$
*/
?>
 <div id="content">
<?php 
  include(DIR_WS_MODULES . FILENAME_NEWS);
?>
  <div class="index_h2">&nbsp;</div> 
<?php
  echo DEFAULT_PAGE_TOP_CONTENTS;
?>
</div>   
<!-- body_text_eof --> 
<!--column_right -->
<div id="r_menu">
<?php 
  $index_default = true;
  require(DIR_WS_INCLUDES . 'column_right.php'); 
?> 
</div>
<?php
