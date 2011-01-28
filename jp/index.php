<?php
/*
  $Id$
 */
require('includes/application_top.php');
require(DIR_WS_ACTIONS.'index_top.php');
page_head();
?>
</head>
<body>
<div align="center">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="left_colum_border">
      <!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
    </td>
<?php
if ($category_depth == 'nested') {
  // 一级分类页面, 分类下只有子分类无商品
  //   ex: http://www.iimy.co.jp/rmt/c-169.html
  require(DIR_WS_ACTIONS.'index_nested.php');
} elseif (isset($_GET['tags_id'] )) {
  // 根据标签tags_id取得产品列表
  //   ex: http://www.iimy.co.jp/tags/t-10.html
  require(DIR_WS_ACTIONS.'index_tags.php');
?>
      <td valign="top" id="contents">
       <h1 class="pageHeading_long"><?php echo $seo_tags['tags_name'];?></h1>
       <h2 class="line">RMT：ゲーム通貨・アイテム・アカウント </h2>
<?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
</td>
<td></td>
<?php
} elseif ($category_depth == 'products' || isset($_GET['manufacturers_id'])) {
  // 根据当前分类或者生产商manufacturers_id商品列表
  //   ex: http://www.iimy.co.jp/game/m-2.html
  require(DIR_WS_ACTIONS.'index_products.php');
?>
  <td valign="top" id="contents">
<?php
  #$current_category    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$current_category_id."'"));
  if (tep_show_warning($current_category_id)) {
    echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
  }
?>
    <h1 class="pageHeading_long"><?php
  if (isset($cPath_array)) {
    echo $seo_category['categories_name'];
  } elseif ($_GET['manufacturers_id']) {
    echo $seo_manufacturers['manufacturers_name'];
  } else {
    echo HEADING_TITLE;
  }
?></h1>
    <p class="comment"><?php echo $seo_category['categories_header_text']; //seoフレーズ ?></p>
<?php
  $has_ca_single = false;
?>
<?php if (isset($_GET['cPath'])) { ?> 
    <table border="0" width="100%" cellspacing="3" cellpadding=l3"">
      <tr align="center">
<?php
       $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   cd.categories_status, 
                   c.categories_image, 
                   c.parent_id,
                   cd.site_id,
                   c.sort_order
            from " .  TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
            where c.parent_id = '" . $current_category_id . "' 
              and c.categories_id = cd.categories_id 
              and cd.language_id = '" . $languages_id . "'  
            order by cd.site_id DESC
          ) c
          where site_id = 0 
             or site_id = ".SITE_ID."
          group by categories_id
          having c.categories_status != '1' and c.categories_status != '3' 
          order by sort_order, categories_name
        ");

    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
      $has_ca_single = true;
      $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '<td class="smallText"><h2 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES .'categories/'. $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
                             if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                   echo $categories['categories_name'] . '</a></h2></td>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
        echo '        </tr>' . "\n";
        echo '        <tr align="center">' . "\n";
      }
  }
?> 
      </tr>
    </table>
    <?php
    } 
    ?>
    <h2 class="line"><?php
  if(isset($_GET['cPath']) && $_GET['cPath']) {
    $categories_path = explode('_', $_GET['cPath']);
    //大カテゴリの画像を返す
    // ccdd
    $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
    echo $_categories['categories_name'];
  } else {
    echo 'RMT：ゲーム通貨・アイテム・アカウント';
  }
?></h2>
    <?php 
      if (isset($_GET['cPath'])) {
    ?>
    <?php
        $listing_tmp_sql = $listing_sql;
        $list_tmp_query = tep_db_query($listing_tmp_sql);
        if (tep_db_num_rows($list_tmp_query)) {
          $isone_ca_query = tep_db_query("select * from categories where categories_id = '".$current_category_id."' and parent_id = '0'"); 
          if (tep_db_num_rows($isone_ca_query)) {
            $has_ca_single = true; 
          } else {
            $has_ca_single = false; 
          }
          include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); 
        }
        $c_tmp_path = explode('', $_GET['cPath']);
        $new_c_name = '';
    ?>
    <?php
      } else {
        include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); 
      } 
    ?> 
    <p class="comment"><?php echo $seo_category['categories_footer_text']; //seoフレーズ ?></p>
    <?php
      if (isset($_GET['cPath'])) {
    ?>
    <?php
        $new_products_category_id = $current_category_id; 
        if (!$has_ca_single) {
          include(DIR_WS_MODULES . 'new_products3.php'); 
        } else {
          include(DIR_WS_MODULES . 'new_products4.php'); 
        }
      }
    ?>
  <?php
    if (isset($cPath_array)) {
      if ($seo_category['seo_description']) {
        echo '<h3 class="pageHeading_long">'.$seo_category['seo_name'].'について</h3>'; 
        echo '<p class="comment">'.$seo_category['seo_description'].'</p>'; 
        echo '<p class="pageBottom"></p>'; 
      }
      if (!empty($seo_category['text_information'])) {
        echo str_replace('class="pageHeading"', 'class="pageHeading_long"', $seo_category['text_information']); 
        echo '<p class="pageBottom"></p>'; 
      }
    }
  ?>
</td> 
<?php
} elseif(isset($_GET['colors']) && !empty($_GET['colors'])) {
  // 根绝颜色color_id取得商品列表
  //   ex: http://www.iimy.co.jp/item/co-1.html
  require(DIR_WS_ACTIONS.'index_colors.php');

// 选择页面左侧的快捷链接, JP中无此功能
//} elseif(isset($_GET['action']) && $_GET['action'] == 'select') {
  //   require(DIR_WS_ACTIONS.'index_select.php');
} else {
  // 默认显示首页
  //   ex: http://www.iimy.co.jp 
  require(DIR_WS_ACTIONS.'index_default.php');
}

?> 
  </tr> 
</table> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
