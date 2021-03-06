<?php
$categories = array();
$ca_num = 0;
$categories_query = tep_db_query("
select * from (
select c.categories_id, 
  cd.categories_name, 
  c.parent_id, 
  c.sort_order,
  cd.categories_image, 
  cd.categories_status, 
  cd.categories_image2,
  cd.site_id
from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
  where c.parent_id = '0' 
  and c.categories_id = cd.categories_id 
  and cd.language_id='" . $languages_id ."' 
  order by cd.site_id desc
) c
where site_id='".SITE_ID."' or site_id='0'
group by categories_id
having c.categories_status != '1' and c.categories_status != '3'
order by sort_order, categories_name
");
while ($category = tep_db_fetch_array($categories_query))  {
  $pic_not_exists = false; 
  if ($ca_num % 2 == 0) {
    echo '<li class="head_category01">';
  } else {
    echo '<li class="header_category02">';
  }
  echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']).'">'; 
  echo '<span class="img">';
  if (!empty($category['categories_image2'])) {
    if (file_exists(DIR_FS_CATALOG."/".DIR_WS_IMAGES.'categories/'.substr($category['categories_image2'], 0, -4).".ico")) {  
      echo '<img src="./images/categories/'.substr($category['categories_image2'], 0, -4).'.ico'.'" width="27" height="27" alt="'.$category['categories_name'].'">';
    } else {
      $pic_not_exists = true; 
    }
  } else {
    $pic_not_exists = true; 
  }
  if ($pic_not_exists) {
    $zero_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where site_id = '0' and categories_id = '".$category['categories_id']."'"); 
    $zero_ca_res = tep_db_fetch_array($zero_ca_query); 
    if ($zero_ca_res) {
      if (!empty($zero_ca_res['categories_image2'])) {
        if (file_exists(DIR_FS_CATALOG."/".DIR_WS_IMAGES.'categories/'.substr($zero_ca_res['categories_image2'], 0, -4).".ico")) {  
          echo '<img src="./images/categories/'.substr($zero_ca_res['categories_image2'], 0, -4).'.ico'.'" width="27" height="27" alt="'.$category['categories_name'].'">';
        } else {
          echo '<img src="./images/design/tt2.ico" alt="'.$category['categories_name'].'">';
        }
      } else {
        echo '<img src="./images/design/tt2.ico" alt="'.$category['categories_name'].'">';
      }
    } else {
      echo '<img src="./images/design/tt2.ico" alt="'.$category['categories_name'].'">';
    }
  }
  echo '</span>'; 
  echo '<div class="text_warp"><div class="text_info_warp" ><span class="text">'.str_replace('RMT', '', $category['categories_name']).'</span></div></div>'; 
  echo '</a>'; 
  echo '</li>';
  $ca_num++;
} 

