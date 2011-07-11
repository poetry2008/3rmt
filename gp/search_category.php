<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $romaji_symbol = explode(',', $_POST['ra']);
  
  $categories_query = tep_db_query("
      select * 
      from (
        select c.categories_id, 
               cd.categories_name, 
               cd.categories_status, 
               c.parent_id,
               cd.site_id,
               cd.romaji, 
               cd.categories_image2,
               cd.character_romaji,
               cd.alpha_romaji,
               c.sort_order
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '0' 
          and c.categories_id = cd.categories_id 
          and cd.language_id='" . $languages_id ."' 
        order by site_id DESC
      ) c 
      where site_id = ".SITE_ID."
         or site_id = 0
      group by categories_id
      having c.categories_status != '1' and c.categories_status != '3'  
      order by sort_order, categories_name
  ");
  $ca_str = '<div class="close_search_category"><a href="javascript:void(0);" onclick="close_top_category(\'showca\');"><img src="images/design/box/action_stop.gif" alt=""></a></div>'; 
  $ca_list_str = ''; 
  $row = 0;  
  while ($category = tep_db_fetch_array($categories_query))  {
    $ca_romaji_arr = explode(',', $category['character_romaji']);  
    $al_romaji_arr = explode(',', $category['alpha_romaji']); 

    $ins_arr = array_intersect($romaji_symbol, $ca_romaji_arr);
    $alp_arr = array_intersect($romaji_symbol, $al_romaji_arr); 
    if (!empty($ins_arr) || !empty($alp_arr)) {
      if ($row % 3 == 0) {
        $ca_list_str .= '<div class="search_list_category">'; 
      }
      
      $ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']).'">'.$category['categories_name'].'</a>'; 
      
      $row++; 
      
      if ($row % 3 == 0) {
        $ca_list_str .= '</div>'; 
      }
    }
  }
  
  if ($row % 3 != 0) {
    $ca_list_str .= '</div>'; 
  }
  
  if (empty($ca_list_str)) {
    $ca_list_str = '<font color="#ffffff">'.SEARCH_NO_TOP_CATEGORY.'</font>'; 
  }
  
  echo $ca_str.$ca_list_str;
