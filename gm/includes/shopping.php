<div class="yui3-g main-columns">
<div id="main-product-img"><img src="images/shop.png" alt="detail"></div>
<?php if($product_info['products_name']!=''){ ?>
<div class="hm-product-title"><?php echo $product_info['products_name'].ABOUT_TEXT_LINK; ?></div>
<?php 
$no_title_class = '';
}else{
$no_title_class = ' no-title-class';
} ?>
<?php $show_description = tep_replace_product_des($description);
if(isset($show_description)&&$show_description){
?>
<div class="hm-product-content<?php echo $no_title_class;?>">
<?php  echo $show_description;?>
</div>
<? } ?>
</div>
<div class="yui3-g main-columns">
<?php
if ( (USE_CACHE == 'true') && !SID ) {
  echo tep_cache_also_purchaseds(3600);
} else {
  include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
}
?>
</div>
<?php include(DIR_WS_BOXES.'reviews.php'); ?>
