<?php
/*
  $Id$
*/
$categories_tab_query1 = tep_db_query("
  select * 
  from (
    select c.categories_id, 
           c.parent_id, 
           cd.categories_status, 
           cd.categories_image, 
           cd.categories_name, 
           cd.categories_meta_text, 
           cd.categories_image2 ,
           c.sort_order,
           cd.site_id
    from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
    where c.categories_id = cd.categories_id 
      and c.parent_id = '".FF_CID."'  
      and cd.language_id='" . (int)$languages_id ."' 
    order by cd.site_id DESC
    ) p
    where site_id = '0'
       or site_id = '" . SITE_ID . "' 
    group by categories_id 
    having p.categories_status != '1' and p.categories_status != '3'  
    order by sort_order,categories_name
    ");
?>
<!-- categories_banner_text //-->
<div class="category_banner_list">
<h2 class="pageHeading"><?php echo $categories['0']['categories_name']?></h2>
  <div class="game_list_content">
<?php 
  $number_of_categories = 0 ;
  $col = 0 ;
  $categories_arr = array();
  $cbt_arr = array();
  while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
    $cbt_arr[mb_strlen($cbt['categories_meta_text'],'utf-8')] = $cbt;
    if (($number_of_categories/3) == floor($number_of_categories/3)) {
      krsort($cbt_arr);
      $categories_arr[] = $cbt_arr;
      $cbt_arr = array();
    }
  }
  foreach($categories_arr as $cbt_arr){
    foreach($cbt_arr as $cbt_key => $cbt){
    echo '<div class="game_list"><a href="' . tep_href_link(FILENAME_DEFAULT,'cPath=' . $cbt['parent_id'].'_'.$cbt['categories_id']) . '">' . "\n";
    echo tep_image(DIR_WS_IMAGES. 'categories/' .$cbt['categories_image'], $cbt['categories_name'], 210, 48) . "\n";
    echo '<span>'; 
    echo $cbt['categories_name']; 
    echo  '</span></a></div>' . "\n";
    }
    echo '</tr>' . "\n" . '<tr align="center">' . "\n";
  } 
?>
</div>
</div>
<!-- categories_banner_text_eof //-->