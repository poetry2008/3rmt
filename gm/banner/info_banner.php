<?php
if($p_image_count>1){
$p_info_image_list ='';
foreach($p_image_list as $key=>$p_image_src){
  if(file_exists3(DIR_WS_IMAGES.'products/'.$p_image_src)&&
    $p_image_src){
    $p_info_image_list .= '<div class="carousel-feature">';
    $p_info_image_list .= '<a class="light" title="'.$product_info['romaji'].'" ';
    $p_info_image_list .= ' href="javascript:void(0);" onclick="fnCreate(\''.tep_href_link(DIR_WS_IMAGES.'products/' .$p_image_src) . '\','.$key.')" >';
    $p_info_image_list .=  tep_image3(DIR_WS_IMAGES.'products/' . $p_image_src, $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="carousel-image"',true) . '</a>';
    $p_info_image_list .= '<div class="carousel-caption">';
    $p_info_image_list .= '</div></div>';
  }
}
$p_info_image_head = '<div class="carousel-container"><div id="carousel">'."\n";
$p_info_image_footer = '</div>';
$p_info_image_footer .= '<div id="carousel-left"><img src="banner/imag/carousel_back_normal.png" alt="picture"></div>';
$p_info_image_footer .= '<div id="carousel-right"><img src="banner/imag/carousel_next_normal.png" alt="picture"></div>';
$p_info_image_footer .= '</div>';
echo $p_info_image_head;
echo $p_info_image_list;
echo $p_info_image_footer;
}else{
  echo "<div >";
  $show_images = false;
  foreach($p_image_list as $key=>$p_list_src){
  if(file_exists3(DIR_WS_IMAGES.'products/'.$p_list_src)&&
      $p_list_src){
      echo  '<a class="light" title="'.$product_info['romaji'].'" href="javascript:void(0);" onclick="fnCreate(\''.tep_href_link(DIR_WS_IMAGES.'products/' .$p_list_src) . '\','.$key.')" >';
      echo tep_image3(DIR_WS_IMAGES.'products/' . $p_list_src,
        $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT,
        'hspace="2" vspace="2" class="product-carousel-image"');
      echo '</a>';
    }
  }
  echo "</div>";
}
?>
