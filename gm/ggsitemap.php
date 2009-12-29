<?php
  require('includes/application_top.php');
  define('HTTP_HOST', 'http://'.$_SERVER['HTTP_HOST'].'/');
  function gg_url($loc, $lastmod = null, $changefreq = 'daily', $priority = 0.3)
  {
?>
  <url>
    <loc><?php echo $loc;?></loc>
    <lastmod><?php echo $lastmod?$lastmod:date('c');?></lastmod>
    <changefreq><?php echo $changefreq?$changefreq:'daily';?></changefreq>
    <priority><?php echo $priority;?></priority>
  </url>
<?php
  }
  function get_cPath($id, $categories)
  {
  	  if($categories[$id]['parent_id'] == '0'){
  	  	return $categories[$id]['categories_id'];
  	  } else {
  	  	return ($categories[$categories[$id]['parent_id']]['parent_id'] == 0 ? $categories[$categories[$id]['parent_id']]['categories_id'] : $categories[$categories[$id]['parent_id']]['parent_id'].'_'.$categories[$categories[$id]['parent_id']]['categories_id']) . '_' . $categories[$id]['categories_id'];
  	  }
  }
  header('Content-Type:   text/xml');
  $categories = $pages = $products = array();
  $categories_query = tep_db_query("select * from " . TABLE_CATEGORIES);
  while ($category = tep_db_fetch_array($categories_query))  {
    $categories[$category['categories_id']] = $category;
  }
  $contents_page = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 order by sort_id ");
  while ($result = tep_db_fetch_array($contents_page)){
    //$pages[] = tep_href_link(FILENAME_PAGE,'pID='.$result['pID'],NONSSL);
    //add info romaji 
    $pages[] = info_tep_href_link($result['romaji']);
  } 
  $products_query = tep_db_query("select * from ".TABLE_PRODUCTS);
  while ($product = tep_db_fetch_array($products_query)){
    $products[] = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product['products_id']);
    //$products[] = tep_href_link(FILENAME_PRODUCT_REVIEWS, 'products_id=' . $product['products_id']);
  }
?>
<?php echo "<?";?>xml version="1.0" encoding="UTF-8"<?php echo "?>";?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <?php echo gg_url(HTTP_HOST, null, null, 0.8);?>
  <?php echo gg_url(HTTP_HOST.'login.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'shopping_cart.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'advanced_search.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'latest_news.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'specials.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'manufacturers.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'present.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'sitemap.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'reorder.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'reorder2.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'reviews.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'products_new.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'contact_us.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'password_forgotten.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'send_mail.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'email_trouble.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_HOST.'browser_ie6x.php', null, null, 0.3);?>
  <?php foreach($categories as $category){?>
    <?php echo gg_url(tep_href_link(FILENAME_DEFAULT, 'cPath='.get_cPath($category['categories_id'],$categories)), null, null, 0.3);?>
  <?php }?>
  <?php if($pages)foreach($pages as $page){?>
    <?php echo gg_url($page, null, null, 0.5);?>
  <?php }?>
  <?php if($products)foreach($products as $product){?>
    <?php echo gg_url($product, null, null, 0.5);?>
  <?php }?>
</urlset>

