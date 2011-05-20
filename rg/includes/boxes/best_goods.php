<?php
/*
  $Id$
*/

$level_one_ca_query = tep_db_query("
    select * 
    from (
      select c.categories_id, 
             cd.categories_name, 
             cd.categories_status, 
             c.parent_id,
             cd.site_id,
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
$sort_ca_arr = array();
$list_ca_arr = array();
while ($level_one_ca_res = tep_db_fetch_array($level_one_ca_query)) {
   $all_ca_arr = tep_get_all_subcategories($level_one_ca_res['categories_id']);
   
   $count_onum_query = tep_db_query("select count(o.products_id) as total_sort from ".TABLE_ORDERS_PRODUCTS." o, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c  where o.products_id = p.products_id and p.products_id = p2c.products_id and p2c.categories_id in (".implode(',', $all_ca_arr).") and o.site_id = '".SITE_ID."'");
   $count_onum_res = tep_db_fetch_array($count_onum_query);
   
   $list_ca_arr['t'.$level_one_ca_res['categories_id']] = array($level_one_ca_res['categories_id'], $level_one_ca_res['categories_name'], $level_one_ca_res['categories_image2']); 
   
   if ($count_onum_res) {
     $sort_ca_arr['t'.$level_one_ca_res['categories_id']] = $count_onum_res['total_sort'];        
   } else {
     $sort_ca_arr['t'.$level_one_ca_res['categories_id']] = 0;        
   }
}
if (!empty($sort_ca_arr)) {
   arsort($sort_ca_arr);
   $sort_ca_num = 0;
?>
<div class="best_goods">
<div class="menu_top">
<img align="top" alt="" src="images/menu_ico11.gif">
<span>
<?php echo COLUMNRIGHT_BEST_GOODS_TITLE;?>
</span>
</div>
<div class="best_goods_info">
<?php
   foreach ($sort_ca_arr as $ca_key => $ca_value) {
     if ($sort_ca_num > 1) {
       break; 
     }
?>
<?php 
     echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$list_ca_arr[$ca_key][0]).'">'.tep_image(DIR_WS_IMAGES.'categories/'.$list_ca_arr[$ca_key][2], $list_ca_arr[$ca_key][1], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT).'</a><br>'; 
?>

<?php
     echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$list_ca_arr[$ca_key][0]).'">'.$list_ca_arr[$ca_key][1].'</a><br>'; 
?>
<?php
     $sort_ca_num++; 
   }
}
?>
</div>
</div>
