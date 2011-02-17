<?php
include 'db_include.php';

// products
foreach ($sites as $s) {
  $product_query = rq("select * from products");
  while($product = mysql_fetch_array($product_query)){
    // products_image
    if($product['products_image']) 
    cp(fs($s).'images/'.$product['products_image'], fs('3rmt').'admin/upload_images/0/products/'.$product['products_image']);
    // products_image2
    if ($product['products_image2']) 
    cp(fs($s).'images/'.$product['products_image2'], fs('3rmt').'admin/upload_images/0/products/'.$product['products_image2']);
    // products_image3
    if ($product['products_image3']) 
    cp(fs($s).'images/'.$product['products_image3'], fs('3rmt').'admin/upload_images/0/products/'.$product['products_image3']);
  }
  cp(fs($s).'images/stock.gif', fs('3rmt').'admin/upload_images/'.site_id($s).'/products/stock.gif');
}

// categories
foreach ($sites as $s) {
  $categories_query = rq("select c.categories_image,cd.categories_image2,cd.categories_image3 from categories c, categories_description cd where c.categories_id=cd.categories_id");
  while($categories= mysql_fetch_array($categories_query )){
    // categories_image
    if ($categories['categories_image']) 
    cp(fs($s).'images/'.$categories['categories_image'], fs('3rmt').'admin/upload_images/'.site_id($s).'/categories/'.$categories['categories_image']);
    // categories_image2 in description
    if ($categories['categories_image2']) 
    cp(fs($s).'images/'.$categories['categories_image2'], fs('3rmt').'admin/upload_images/'.site_id($s).'/categories/'.$categories['categories_image2']);
    // categories_image3 in description
    if ($categories['categories_image3']) 
    cp(fs($s).'images/'.$categories['categories_image3'], fs('3rmt').'admin/upload_images/'.site_id($s).'/categories/'.$categories['categories_image3']);
  }
}

// latest_news
foreach ($sites as $s) {
  $latest_news_query = rq("select * from " . table_prefix($s) . "latest_news");
  while($latest_news = mysql_fetch_array($latest_news_query )){
    // news_image
    if($latest_news['news_image'])
    cp(fs($s).'images/'.$latest_news['news_image'], fs('3rmt').'admin/upload_images/'.site_id($s).'/'.$latest_news['news_image']);
  }
}

// tags
foreach ($sites as $s) {
  $tags_query = rq("select * from tags");
  while($tags = mysql_fetch_array($tags_query )){
    // tags_image
    if($tags['tags_images'])
    cp(fs($s).'images/'.$tags['tags_images'], fs('3rmt').'admin/upload_images/'.site_id($s).'/'.$tags['tags_images']);
  }
}

// present
foreach ($sites as $s) {
  $present_query = rq("select * from " . table_prefix($s) . "present_goods");
  while($present = mysql_fetch_array($present_query )){
    // image
    if($present['image'])
    cp(fs($s).'images/'.$present['image'], fs('3rmt').'admin/upload_images/'.site_id($s).'/present/'.$present['image']);
  }
}

// orders_status
// banners
// colors

