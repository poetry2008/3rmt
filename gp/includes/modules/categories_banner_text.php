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
    order by sort_order,categories_name
    ");
?>
<!-- categories_banner_text //-->
<div class="pageHeading_long01"><h2><?php echo INDEX_CATEGORY_BANNER_TEXT;?></h2></div>
<div class="comment02">
  <table width="710" class="game_list_content" border="0"cellpadding="0" cellspacing="0">
    <tr>
<?php 
  $number_of_categories = 0 ;
  $col = 0 ;
  while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
    echo '<td class="smallText">' . "\n";
    echo '<h3 class="game_list"><a href="' . tep_href_link(FILENAME_DEFAULT,'cPath=' . $cbt['categories_id']) . '">' . "\n";
    echo tep_image(DIR_WS_IMAGES. 'categories/' .$cbt['categories_image2'],$cbt['categories_name'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT) . '<br>' . "\n";
    $cbt_dec = explode(',',$cbt['categories_name']);
    for($i=0; $i < sizeof($cbt_dec); $i++) {
      if($cbt_dec[$i] != ''){
        //echo strip_tags(mb_substr($cbt_dec[$i],0,36,"UTF-8")) . "\n";
      }
    }
    echo  '</a></h3>' . "\n" . '</td>' . "\n";
  
    if (($number_of_categories/4) == floor($number_of_categories/4)) {
      echo '</tr>' . "\n" . '<tr align="center">' . "\n";
    } else {
      echo '';
      //echo '<td>'.tep_draw_separator('pixel_trans.gif', '18', '1').'</td>'."\n";
    }
  } 
?>
    <td></td><td></td><td></td></tr>
</table>
</div>
<!-- categories_banner_text_eof //-->
