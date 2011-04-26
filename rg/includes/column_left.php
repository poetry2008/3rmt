<?php
/*
  $Id$
*/

  //require(DIR_WS_BOXES . 'quick_link.php');
?>
  <div id="search">
  <div class="menu_top"><img align="top" alt="" src="images/menu_seach.gif"><span><?php echo LEFT_SEARCH_TITLE;?></span></div>
  <?php
echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get')."\n";
?>
  <table cellpadding="0" cellspacing="0" width="170" border="0">
                    <tr><td><?php echo tep_draw_input_field('keywords', 'RMT', 'class="header_search_input"'); ?></td>
                      <td><input name="imageField" type="submit" class="header_search_submit" value="" alt="¸¡º÷">
                <?php 
            echo '<input type="hidden" name="search_in_description" value="1">';
            echo '<input type="hidden" name="inc_subcat" value="1">';
            echo tep_hide_session_id(); 
        ?></td></tr>
</table>
</form>
</div>
<?php
  if ( (USE_CACHE == 'true') && !SID ) {
    include(DIR_WS_BOXES . 'categories.php');
   // echo tep_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/

  //require(DIR_WS_BOXES . 'information.php');
  //require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
    
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
