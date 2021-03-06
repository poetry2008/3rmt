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
           c.categories_image, 
           cd.categories_name, 
           cd.categories_name_list, 
           cd.categories_meta_text, 
           cd.categories_image2 ,
           c.sort_order,
           cd.site_id
    from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
    where c.categories_id = cd.categories_id 
      and c.parent_id = '0' 
      and cd.language_id='" . (int)$languages_id ."' 
    order by cd.site_id DESC
    ) p
    where site_id = '0'
       or site_id = '" . SITE_ID . "' 
    group by categories_id 
    having p.categories_status != '1' and p.categories_status != '3' 
    order by sort_order,categories_name_list
    ");
?>
<!-- categories_banner_text //-->
<h2 class="pageHeading">RMT GAME LIST</h2>
<div class="game_list_content">
  <table width="510" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="center">
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
    echo '<td class="smallText">' . "\n";
    echo '<h3 class="game_list"><a href="' . tep_href_link(FILENAME_DEFAULT,'cPath=' . $cbt['categories_id']) . '">' . "\n";
    echo tep_image(DIR_WS_IMAGES. 'categories/'
        .$cbt['categories_image2'],$cbt['categories_name_list'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT) . '<br>' . "\n";
    echo $cbt['categories_name_list']; 
    echo  '</a></h3>' . "\n" . '</td>' . "\n";
    }
    echo '</tr>' . "\n" . '<tr align="center">' . "\n";
  } 
?>
    <td></td><td></td><td></td></tr>
</table>
</div>
<!-- categories_banner_text_eof //-->
