<?php
/*
  $Id$
*/

  //require(DIR_WS_BOXES . 'quick_link.php');
?>
<?php
if ($_SERVER['PHP_SELF'] == '/product_info.php' || $_SERVER['PHP_SELF'] == '/product_reviews_info.php') {
  include(DIR_WS_BOXES . 'list_new_categories.php');
}
if ($_SERVER['PHP_SELF'] == '/product_info.php') {
?>
<div id="search">
<div class="menu_top"><span><?php echo LEFT_BOX_TITLE;?></span></div> 
  <div class="column_left_bg">
  	<div class="column_left_comment">
<?php 
  echo '<div class="left_total_money">'; 
  echo LEFT_TOTAL_TEXT.':&nbsp;&nbsp;'; 
  echo '<font color="#ff0000">'.$currencies->format($cart->show_total()).'</font>';
  echo '</div>';
  echo '<div class="left_total_money_link"><a href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'"><img src="images/design/s_img_a.gif" alt="'.HEADER_TITLE_CART_CONTENTS.'"></a></div>';
  echo '<div class="left_total_money_link"><a href="'.tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL').'"><img src="images/design/s_img_b.gif" alt="'.HEADER_TITLE_CHECKOUT.'"></a></div>';
?>
</div>
</div>
</div>
<?php } ?>
<?php
if($_SERVER['REQUEST_URI']=='/'||$_SERVER['REQUEST_URI']=='/index.php'||$_SERVER['REQUEST_URI']=='/reviews/'||$_SERVER['PHP_SELF'] == '/page.php'||$_SERVER['PHP_SELF'] == '/contact_us.php'||$_SERVER['PHP_SELF'] == '/reorder.php'||$_SERVER['PHP_SELF'] == '/sitemap.php'||$_SERVER['PHP_SELF'] == '/tags.php'||$_SERVER['PHP_SELF'] == '/products_new.php'||$_SERVER['PHP_SELF'] == '/specials.php'||(strpos($_SERVER['REQUEST_URI'],'index.php?cmd='))||(strpos($_SERVER['REQUEST_URI'],'?cmd='))){
  }else{
  $left_search_category_single = true; 
  if ( (USE_CACHE == 'true') && !SID ) {
    if ($cPath) {
      include(DIR_WS_BOXES . 'list_categories.php');
    } else {
      include(DIR_WS_BOXES . 'categories.php');
    }
   // echo tep_cache_categories_box();
  } else {
    if ($cPath) {
      include(DIR_WS_BOXES . 'list_categories.php');
    } else {
      include(DIR_WS_BOXES . 'categories.php');
    }
  }
  }
  if (isset($left_search_category_single) || $_SERVER['PHP_SELF'] == '/page.php') {
?>
	<div class="seach_before">
    	<div class="menu_top"><span><?php echo LEFT_SEARCH_CATEGORY_TITLE;?></span></div>
        <script type="text/javascript" src="js/left_search_category.js"></script> 
        <table width="170" cellpadding="1" cellspacing="2" border="0" class="seach_before_info">
        	<tr>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_ONE_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_ONE_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_TWO_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_TWO_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_THREE_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_THREE_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_FOUR_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_FOUR_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_FIVE_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_FIVE_TEXT;?></a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_SIX_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_SIX_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_SEVEN_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_SEVEN_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_EIGHT_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_EIGHT_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_NINE_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_NINE_TEXT;?></a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=<?php echo LEFT_SEARCH_CATEGORY_TEN_TEXT;?>');"><?php echo LEFT_SEARCH_CATEGORY_TEN_TEXT;?></a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=a,b,c');">A<?php echo CONNECT_SYMBOL_TEXT;?>C</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=d,e,f');">D<?php echo CONNECT_SYMBOL_TEXT;?>F</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=g,h,i');">G<?php echo CONNECT_SYMBOL_TEXT;?>I</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=j,k,l');">J<?php echo CONNECT_SYMBOL_TEXT;?>L</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=m,n,o');">M<?php echo CONNECT_SYMBOL_TEXT;?>O</a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=p,q,r');">P<?php echo CONNECT_SYMBOL_TEXT;?>R</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=s,t,u');">S<?php echo CONNECT_SYMBOL_TEXT;?>U</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=v,w');">V<?php echo CONNECT_SYMBOL_TEXT;?>W</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=x,y,z');">X<?php echo CONNECT_SYMBOL_TEXT;?>Z</a></td>
                <td><a href="javascript:void(0);" onclick="left_search_category('left_search_category.php?ra=1,2,3,4,5,6,7,8,9');">1<?php echo CONNECT_SYMBOL_TEXT;?>9</a></td>
            </tr>
        </table>
    </div>
    <div id="leftca" style="display:none;"></div> 
  <?php }?> 
  <div id="search">
  <div class="menu_top"><span><?php echo LEFT_SEARCH_TITLE;?></span><div class="seach_more"><a href="<?php echo tep_href_link(FILENAME_ADVANCED_SEARCH);?>"><?php echo LEFT_SEARCH_TOTAL_TITLE;?></a></div></div> 
  <?php
echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', $request_type, false), 'get')."\n";
?>
  <table cellpadding="0" cellspacing="0" width="170" border="0">
  <tr>
    <td colspan="2">
                      <?php
// --- get categoris list ( parent_id = 0 ) --- //
  $cat1 = '';
  if ($_GET['cPath']) {
    $cat0 = explode('_', $_GET['cPath']);
  } elseif ($_GET['products_id']) {
    $cat_products = tep_get_product_path($_GET['products_id']);
    $cat0 = explode('_', $cat_products);
  }
  if (!isset($cat0[0])) $cat0[0] = NULL; //del notice
  $cat1 = $cat0[0];
  // ccdd
  $categories_parent0_query = tep_db_query("
      select * 
      from (
        select c.categories_id, 
               cd.categories_status, 
               cd.categories_name,
               c.sort_order,
               cd.site_id
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '0' 
        and c.categories_id = cd.categories_id 
        and cd.language_id = '" . (int)$languages_id . "' 
        order by sort_order, cd.categories_name, cd.site_id DESC
      ) c
      where site_id = '0'
         or site_id = '".SITE_ID."'
      group by categories_id
      having c.categories_status != '1' and c.categories_status != '3' 
      order by sort_order, categories_name
      ");
  $categories_array = '<select name="categories_id" class="header_search_select">'."\n";
  $categories_array .= '<option value=""';
  if($cat1 == '') {
    $categories_array .= ' selected';
  }
  $categories_array .= '>'.LEFT_SELECT_ALL_CATEGORY.'</option>'."\n";
  while($categories_parent0 = tep_db_fetch_array($categories_parent0_query)) {
    $categories_array .= '<option value="'.$categories_parent0['categories_id'].'"';
    if($cat1 == $categories_parent0['categories_id']) {
      $categories_array .= ' selected';
    }
    $categories_array .= '>'.$categories_parent0['categories_name'].'</option>'."\n";
  }
  $categories_array .= '</select>'."\n";
  echo $categories_array ;
// --- end add--- //
?>
    </td>
  </tr>
</table>
<div class="column_left_bg">
  <table cellpadding="0" cellspacing="0" width="130" border="0">
  <tr><td><?php echo tep_draw_input_field('keywords', COLUMNLEFT_SEARCH_DEFAULT_VALUE, 'class="header_search_input" id="skeywords" onclick="document.getElementById(\'skeywords\').value = \'\';"'); ?></td>
                      <td><input name="imageField" type="submit" class="header_search_submit" value="" alt="search">
                <?php 
            echo '<input type="hidden" name="search_in_description" value="1">';
            echo '<input type="hidden" name="inc_subcat" value="1">';
            echo tep_hide_session_id(); 
        ?></td></tr>
</table>
</form>
<ul>
  <li><a href="<?php echo tep_tags_link();?>"><?php echo TEXT_TAGS;?></a></li> 
  <li><a href="<?php echo tep_href_link(FILENAME_PRODUCTS_NEW);?>"><?php echo LEFT_PRODUCTS_NEW_PREVIEW;?></a></li> 
  <li><a href="<?php echo tep_href_link(FILENAME_SPECIALS);?>"><?php echo LEFT_SPECIALS_PREVIEW;?></a></li>
</ul>
</div>
</div>
<?php
if ($_SERVER['PHP_SELF'] != '/product_info.php') {
?>
<div id="search">
<div class="menu_top"><span><?php echo LEFT_BOX_TITLE;?></span></div> 
  <div class="column_left_bg">
  	<div class="column_left_comment">
<?php 
  echo '<div class="left_total_money">'; 
  echo LEFT_TOTAL_TEXT.':&nbsp;&nbsp;'; 
  echo '<font color="#ff0000">'.$currencies->format($cart->show_total()).'</font>';
  echo '</div>';
  echo '<div class="left_total_money_link"><a href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'"><img src="images/design/s_img_a.gif" alt="'.HEADER_TITLE_CART_CONTENTS.'"></a></div>';
  echo '<div class="left_total_money_link"><a href="'.tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL').'"><img src="images/design/s_img_b.gif" alt="'.HEADER_TITLE_CHECKOUT.'"></a></div>';
?>
</div>
</div>
</div>
<?php } ?>
