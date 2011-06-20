<?php
function tep_rr_get_categories_id_by_parent_id($categories_id_str, $languages_id = 4) {
  $arr = array();
  $categories_id_arr = explode(',', $categories_id_str);
  foreach ($categories_id_arr as $key => $value) {
    $categories = tep_get_categories_by_parent_id($value, $languages_id);
    foreach ($categories as $c){
      $arr[] = $c['categories_id'];
      $subcategories = tep_get_categories_by_parent_id($c['categories_id'], $languages_id);
      foreach ($subcategories as $sc) {
        $arr[] = $sc['categories_id']; 
      }
    }
  }
  return $arr;
}

function tep_rr_get_categories($categories_array = '', $parent_id = '0', $indent = '', $include_pid = '') {
  global $languages_id;

  $parent_id = tep_db_prepare_input($parent_id);
  $pa_cid_arr = explode(',', $include_pid);
  if (!is_array($categories_array)) $categories_array = array();
  //ccdd
  $categories_query = tep_db_query("
    select *
    from (
      select c.categories_id, cd.categories_name ,c.sort_order, cd.site_id
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
      where parent_id = '" . tep_db_input($parent_id) . "' 
        and c.categories_id = cd.categories_id 
        and cd.language_id = '" . (int)$languages_id . "' 
      order by cd.site_id DESC
    ) c
    where site_id = '0'
       or site_id = ".SITE_ID." 
    group by categories_id
    order by sort_order, categories_name");
  while ($categories = tep_db_fetch_array($categories_query)) {
    if (!in_array($categories['categories_id'], $pa_cid_arr) && $parent_id == 0) {
      continue; 
    }
    $categories_array[] = array('id' => $categories['categories_id'],
                                'text' => $indent . $categories['categories_name']);

    if ($categories['categories_id'] != $parent_id) {
      $categories_array = tep_rr_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;', $include_pid);
    }
  }

  return $categories_array;
}
