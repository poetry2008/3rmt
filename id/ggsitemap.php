<?php
/*
  $Id$
  Google sitemap 网站地图
*/
  require('includes/application_top.php');
  header('Content-Type:   text/xml');
  $categories = $pages = $products = array();

  // 取得全部分类
 
  $categories_query = tep_db_query("select 
      c.categories_id,c.categories_status,c.categories_image,
      c.parent_id,c.sort_order,c.date_added,c.last_modified 
      from "
      . TABLE_CATEGORIES ." c left join " . TABLE_CATEGORIES_DESCRIPTION .
      " cd on c.categories_id = cd.categories_id 
      WHERE cd.categories_status <> '3' 
      and cd.site_id='".SITE_ID."'");
  while ($category = tep_db_fetch_array($categories_query))  {
    $categories[$category['categories_id']] = $category;
  }

  // 取得全部信息页内容
 
  $contents_page = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and site_id = '" . SITE_ID . "' order by sort_id ");
  while ($result = tep_db_fetch_array($contents_page)){
    $pages[] = info_tep_href_link($result['romaji']);
  } 

  // 取得全部商品
 
  $products_query = tep_db_query("select p.* from ".TABLE_PRODUCTS." p
      left join ".TABLE_PRODUCTS_DESCRIPTION." pd on
      p.products_id = pd.products_id where pd.products_status <> '3' 
      and pd.site_id ='".SITE_ID."'");
  while ($product = tep_db_fetch_array($products_query)){
    $products[] = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product['products_id']);
  }
?>
<?php echo "<?";?>xml version="1.0" encoding="UTF-8"<?php echo "?>";?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <?php echo gg_url(HTTP_SERVER, null, null, 0.8);?>
  <?php echo gg_url(HTTP_SERVER.'/login.php', null, null, 0.3);?>
  <?php //echo gg_url(HTTP_SERVER.'/shopping_cart.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/advanced_search.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/news/', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/manufacturers.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/present.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/sitemap.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/reorder.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/reorder2.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/reviews/', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/products_new.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/contact_us.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/password_forgotten.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/send_mail.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/email_trouble.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/browser_ie6x.php', null, null, 0.3);?>
  <?php echo gg_url(HTTP_SERVER.'/link/', null, null, 0.3);?>

  <?php //全部分类页面?>
  <?php foreach($categories as $category){?>
    <?php echo gg_url(HTTP_SERVER.tep_href_link(FILENAME_DEFAULT, 'cPath='.get_cPath($category['categories_id'],$categories)), null, null, 0.3);?>
  <?php }?>

  <?php //全部信息页?>
  <?php if($pages)foreach($pages as $page){?>
    <?php echo gg_url(HTTP_SERVER.$page, null, null, 0.5);?>
  <?php }?>

  <?php //全部商品页?>
  <?php if($products)foreach($products as $product){?>
    <?php echo gg_url(HTTP_SERVER.$product, null, null, 0.5);?>
  <?php }?>
</urlset>
