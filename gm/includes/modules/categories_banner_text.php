<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
$categories_tab_query1 = tep_db_query("select c.categories_id, c.parent_id, c.categories_status, c.categories_image, cd.categories_name, cd.categories_meta_text, cd.categories_image2 from categories c, categories_description cd where c.categories_id = cd.categories_id and cd.site_id = '" . SITE_ID . "' and c.parent_id = '0' and c.categories_status='0' and  cd.language_id='" . (int)$languages_id ."' order by sort_order");
?>
<!-- categories_banner_text //-->
        <h2> 
          <table width="100%" border="0" align="center" cellpadding="0"
          cellspacing="0" summary="<?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B'));?>"> 
            <tr> 
              <td width="63"><img src="images/design/contents/title_lineup.jpg"
              width="118" height="23" title="<?php echo BOX_HEADING_CATEGORIES;?>"></td> 
              <td background="images/design/contents/title_bg.jpg">&nbsp;</td> 
              <td width="47"><img
              src="images/design/contents/title_lignup_right.jpg" width="54"
              height="23" title="<?php echo BOX_HEADING_CATEGORIES;?>"></td> 
            </tr> 
          </table> 
        </h2>
<table width="" border="0" align="center" cellpadding="0" cellspacing="0">
<tr> 
  <?php 
  $number_of_categories = 0 ;
  $col = 0 ;
  while($cbt = tep_db_fetch_array($categories_tab_query1)){
    $number_of_categories ++;
    echo '<td valign="top" style="padding-bottom:10px;"><a href="'.tep_href_link(FILENAME_DEFAULT,'cPath=' .$cbt['categories_id']).'">'.tep_image(DIR_WS_IMAGES.$cbt['categories_image2'],$cbt['categories_name'], CATEGORY_IMAGE_WIDTH, CATEGORY_IMAGE_HEIGHT).'</a>'."\n";
    $cbt_dec = explode(',',$cbt['categories_meta_text']);
    for($i=0; $i < sizeof($cbt_dec); $i++) {
    if($cbt_dec[$i] != ''){
    echo '<h3 class="Tlist">'.strip_tags(substr($cbt_dec[$i],0,36)).'..</h3>'."\n";
      }
    }
    echo  ' </td>'."\n";
  
    if (($number_of_categories/2) == floor($number_of_categories/2)) {
      echo '</tr>'."\n".'<tr>' ;
    } else {
      echo '<td>'.tep_draw_separator('pixel_trans.gif', '18', '1').'</td>'."\n";
    }
  } 
?> 
</tr> 
</table> 
<!-- categories_banner_text_eof //-->
