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
      <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?> </td> 
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
