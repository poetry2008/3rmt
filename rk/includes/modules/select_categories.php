<?php
/*
   $Id$
*/
// ccdd
$categories_tab_query1 = tep_db_query("
  select * 
  from (
    select c.categories_id, 
           cd.categories_status,
           c.parent_id, 
           c.categories_image, 
           c.sort_order,
           cd.categories_name, 
           cd.categories_meta_text, 
           cd.categories_image2,
           cd.site_id
    from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd 
    where c.categories_id = cd.categories_id 
      and c.parent_id = '0' 
      and  cd.language_id='" .  (int)$languages_id ."' 
    order by cd.site_id DESC
  ) c
  where site_id = 0
     or site_id = '".SITE_ID."' 
  group by categories_id
  having c.categories_status != '1' and c.categories_status != '3' 
  order by sort_order"
);
?>
<!-- select_categories //-->
<h1 class="pageHeading">SELECT A GAME</h1>
<div class="game_list_content">
<form action='/select.php' method='post'>
  <table width="510" border="0" align="center" onkeypress="" cellpadding="0" cellspacing="0" summary="table">
    <tr align="center">
<?php 
  $number_of_categories = 0 ;
  $col = 0 ;
  while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
    echo '<td class="smallText">' . "\n";
    echo "<h3 class=\"game_list\">" . "\n";
    echo "<a href='javascript:void(0);' onkeypress='SomeJavaScriptCode' onclick=\"document.getElementById('categories_id_" . $cbt['categories_id'] . "').checked = true;\">" . "\n";
    echo tep_image(DIR_WS_IMAGES. 'categories/' .$cbt['categories_image2'],$cbt['categories_name'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT) . '<br>' . "\n";
    $cbt_dec = explode(',',$cbt['categories_meta_text']);
    for($i=0; $i < sizeof($cbt_dec); $i++) {
      if($cbt_dec[$i] != ''){
        echo strip_tags(substr($cbt_dec[$i],0,36)) . "\n";
      }
    } 
    echo '<br><input type="radio" id="categories_id_' . $cbt['categories_id'] . '" name="categories_id" value="' . $cbt['categories_id'] . '" >';
    echo  '</a></h3>' . "\n" . '</td>' . "\n";
  
    if (($number_of_categories/3) == floor($number_of_categories/3)) {
      echo '</tr>' . "\n" . '<tr align="center">' . "\n";
    } else {
      echo '';
      // echo '<td>'.tep_draw_separator('pixel_trans.gif', '18', '1').'</td>'."\n";
    }  
  } 
?>
    </tr>
  </table>
   <div style="text-align:center;"><input type='image' value='決定' src="includes/languages/japanese/images/buttons/button_ok.gif" ></div>
</form>
</div>
<p class="pageBottom"></p>
<!-- select_categories_eof //-->
