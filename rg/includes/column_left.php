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
                    <tr><td><?php echo tep_draw_input_field('keywords', COLUMNLEFT_SEARCH_DEFAULT_VALUE, 'class="header_search_input" id="skeywords" onclick="document.getElementById(\'skeywords\').value = \'\';"'); ?></td>
                      <td><input name="imageField" type="submit" class="header_search_submit" value="" alt="search">
                <?php 
            echo '<input type="hidden" name="search_in_description" value="1">';
            echo '<input type="hidden" name="inc_subcat" value="1">';
            echo tep_hide_session_id(); 
        ?></td></tr>
</table>
</form>
</div>
<?php
  if($_SERVER['REQUEST_URI']=='/'||$_SERVER['REQUEST_URI']=='/index.php'||
      (strpos($_SERVER['REQUEST_URI'],'index.php?cmd='))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_PREORDER_CONFIRMATION))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_MEMBER_AUTH))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_NON_MEMBER_AUTH))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_NON_PREORDER_AUTH))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_SEND_SUCCESS))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_CHECKOUT_OPTION))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_CHECKOUT_SHIPPING))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_CHECKOUT_PAYMENT))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_CHECKOUT_SUCCESS))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_ACCOUNT))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_ACCOUNT_EDIT))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_ACCOUNT_HISTORY))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_PRODUCT_NOTIFICATIONS))||
      (strpos($_SERVER['REQUEST_URI'],FILENAME_BROWSER_IE6X))||
      (strpos($_SERVER['REQUEST_URI'],'?cmd='))){
  }else{
  if ( (USE_CACHE == 'true') && !SID ) {
    include(DIR_WS_BOXES . 'categories.php');
   // echo tep_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }
  }
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/

  require(DIR_WS_BOXES . 'information.php');
  require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
    
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
