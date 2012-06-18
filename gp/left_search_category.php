<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $romaji_symbol = explode(',', $_GET['ra']);
  
 /* $categories_query = tep_db_query("
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
  */
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
        where  c.categories_id = cd.categories_id 
          and cd.language_id='" . $languages_id ."' 
        order by site_id DESC
      ) c 
      where site_id = ".SITE_ID."
         or site_id = 0
      group by categories_id
      having c.categories_status != '1' and c.categories_status != '3'  
      order by categories_name
  ");

  $ca_str = '<div class="close_search_category"><a href="javascript:void(0);" onclick="close_left_category();"><img src="images/design/box/action_stop.gif" alt=""></a></div>'; 
  $ca_list_str = ''; 
  $row = 0;  
$tmp_array = array();
$tmp_i = 0;
while ($category = tep_db_fetch_array($categories_query))  {
    $ca_romaji_arr = explode(',', $category['character_romaji']);  
    $al_romaji_arr = explode(',', strtolower($category['alpha_romaji'])); 

    $ins_arr = array_intersect($romaji_symbol, $ca_romaji_arr);
    $alp_arr = array_intersect($romaji_symbol, $al_romaji_arr); 
    if (!empty($ins_arr) || !empty($alp_arr)) {
   $tmp_array[$tmp_i] = $category ;
$tmp_i++;
    }

}
$num_row = count($tmp_array);
if($num_row%3==0){
$per_col = (int)($num_row/3);
}else{
$per_col = (int)($num_row/3 +1);
}
	foreach ($tmp_array as $key => $val){
if ($row % $per_col == 0) {
        $ca_list_str .= '<div class="search_list_category">'; 
      }

$check_category_query = tep_db_query("select parent_id from categories where categories_id='".$val['categories_id']."'");
$check_category_array = tep_db_fetch_array($check_category_query);
if($check_category_array['parent_id'] == 0){
      $ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$val['categories_id']).'">'.$val['categories_name'].'</a>'; 
}else{
$check_category_p_query = tep_db_query("select parent_id from categories where categories_id='".$check_category_array['parent_id']."'");
$check_category_p_array = tep_db_fetch_array($check_category_p_query);
if($check_category_p_array['parent_id'] == 0){
$ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$check_category_array['parent_id']."_".$val['categories_id']).'">'.$val['categories_name'].'</a>'; 
}else{
$ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$check_category_p_array['parent_id']."_".$check_category_array['parent_id']."_".$val['categories_id']).'">'.$val['categories_name'].'</a>'; 
}
} 

      $row++; 
      
      if ($row % $per_col == 0) {
        $ca_list_str .= '</div>'; 
      }
}
 
 
   
  if ($row % $per_num != 0) {
    $ca_list_str .= '</div>'; 
  }
  
  if (empty($ca_list_str)) {
    $ca_list_str = '<font color="#ffffff">'.SEARCH_NO_TOP_CATEGORY.'</font>'; 
  }
  
  echo $ca_str.$ca_list_str.'</div>';
/*  while ($category = tep_db_fetch_array($categories_query))  {
    $ca_romaji_arr = explode(',', $category['character_romaji']);  
    $al_romaji_arr = explode(',', $category['alpha_romaji']); 

    $ins_arr = array_intersect($romaji_symbol, $ca_romaji_arr);
    $alp_arr = array_intersect($romaji_symbol, $al_romaji_arr); 
    if (!empty($ins_arr) || !empty($alp_arr)) {
      if ($row % 3 == 0) {
        $ca_list_str .= '<div class="search_list_category">'; 
      }
      
//      $ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']).'">'.$category['categories_name'].'</a>'; 
  $check_category_query = tep_db_query("select parent_id from categories where categories_id='".$category['categories_id']."'");
$check_category_array = tep_db_fetch_array($check_category_query);
if($check_category_array['parent_id'] == 0){
      $ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']).'">'.$category['categories_name'].'</a>'; 
}else{
$check_category_p_query = tep_db_query("select parent_id from categories where categories_id='".$check_category_array['parent_id']."'");
$check_category_p_array = tep_db_fetch_array($check_category_p_query);
if($check_category_p_array['parent_id'] == 0){
$ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$check_category_array['parent_id']."_".$category['categories_id']).'">'.$category['categories_name'].'</a>'; 
}else{
$ca_list_str .= '<a class="search_category_name" href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$check_category_p_array['parent_id']."_".$check_category_array['parent_id']."_".$category['categories_id']).'">'.$category['categories_name'].'</a>'; 
}
}    
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
  echo $ca_str.$ca_list_str;*/
