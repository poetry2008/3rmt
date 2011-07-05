<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $romaji_symbol = explode(',', $_GET['ra']);
  
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
  $ca_str = '<div class="close_search_category"><a href="javascript:void(0);" onclick="close_left_category();"><img src="images/design/box/action_stop.gif" alt=""></a></div>'; 
  $ca_list_str = ''; 
  $row = 0;  
  while ($category = tep_db_fetch_array($categories_query))  {
    $ro_str = mb_substr($category['romaji'], 0, 1, 'UTF-8'); 
    if (in_array(strtolower($ro_str), $romaji_symbol)) {
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
