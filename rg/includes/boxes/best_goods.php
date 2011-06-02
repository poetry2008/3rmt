<?php
/*
  $Id$
*/

$best_goods_raw = tep_db_query("SELECT count( op.orders_products_id ) cnt, ptc.categories_id, c.parent_id, c2.parent_id cid, if( c2.parent_id =0, if( c.parent_id =0, ptc.categories_id, c.parent_id ) , ptc.categories_id ) ci FROM ".TABLE_ORDERS_PRODUCTS." op LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." ptc ON ptc.products_id = op.products_id LEFT  JOIN ".TABLE_CATEGORIES." c ON c.categories_id = ptc.categories_id LEFT  JOIN ".TABLE_CATEGORIES." c2 ON c.parent_id = c2.categories_id WHERE ptc.categories_id IS  NOT  NULL AND op.site_id = ".SITE_ID." GROUP  BY ci ORDER  BY  `cnt`  DESC LIMIT 0 , 2");
?>
<?php
if (tep_db_num_rows($best_goods_raw)) {
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
while ($best_goods_res = tep_db_fetch_array($best_goods_raw)) {
  $fcategory_query = tep_db_query("
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
        where c.categories_id = '".$best_goods_res['ci']."' 
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
   $fcategory_res = tep_db_fetch_array($fcategory_query); 
   if ($fcategory_res) {
     echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$fcategory_res['categories_id']).'">'.tep_image(DIR_WS_IMAGES.'categories/'.$fcategory_res['categories_image2'], $fcategory_res['categories_name'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT).'</a><br>'; 
   }
}
?>
</div>
</div>
<?php }?>
