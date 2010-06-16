<?php
/*
  $Id$
*/
// ccdd
$categories_tab_query1 = tep_db_query("
  select * 
  from (
    select c.categories_id, 
           c.parent_id, 
           c.categories_status, 
           c.categories_image, 
           cd.categories_name, 
           cd.categories_meta_text, 
           cd.categories_image2 ,
           c.sort_order,
           cd.site_id
    from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
    where c.categories_id = cd.categories_id 
      and c.parent_id = '0' 
      and c.categories_status!='1' 
      and cd.language_id='" . (int)$languages_id ."' 
    order by cd.site_id DESC
    ) p
    where site_id = '0'
       or site_id = '" . SITE_ID . "' 
    group by categories_id 
    order by sort_order, categories_name
    ");
?>
<!-- categories_banner_text //-->
<h2 class="pageHeading">RMT GAME LIST</h2>
<div class="game_list_content">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0">
    <tr align="center">
<?php 
  $number_of_categories = 0 ;
  $col = 0 ;
  while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
    echo '<td class="smallText">' . "\n";
    echo '<h3 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT,'cPath=' . $cbt['categories_id']) . '">' . "\n";
    echo tep_image(DIR_WS_IMAGES.'categories/'.$cbt['categories_image2'],$cbt['categories_name'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT) . '<br>' . "\n";
    $cbt_dec = explode(',',$cbt['categories_meta_text']);
    for($i=0; $i < sizeof($cbt_dec); $i++) {
      if($cbt_dec[$i] != ''){
        echo strip_tags(mb_substr($cbt_dec[$i],0,36, "UTF-8")) . "\n";
      }
    } 
    echo  '</a></h3>' . "\n" . '</td>' . "\n";
  
    if (($number_of_categories/3) == floor($number_of_categories/3)) {
      echo '</tr>' . "\n" . '<tr align="center">' . "\n";
    } else {
      echo '';
    }  
  } 
?>
    <td></td><td></td><td></td>
        </tr>
  </table>
</div>
<!-- categories_banner_text_eof //-->
