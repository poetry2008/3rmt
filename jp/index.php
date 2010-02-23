<?php
/*
  $Id$
 */
require('includes/application_top.php');
require(DIR_WS_ACTIONS.'index_top.php');

page_head();
?>
</head>
<body <?php echo $body_option;?>>
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
} elseif (isset($HTTP_GET_VARS['tags_id'])) {
  // 根据标签tags_id取得产品列表
  //   ex: http://www.iimy.co.jp/tags/t-10.html
  require(DIR_WS_ACTIONS.'index_tags.php');
} elseif ($category_depth == 'products' || isset($HTTP_GET_VARS['manufacturers_id'])) {
  // 根据当前分类或者生产商manufacturers_id商品列表
  //   ex: http://www.iimy.co.jp/game/m-2.html
  require(DIR_WS_ACTIONS.'index_products.php');
} elseif(isset($HTTP_GET_VARS['colors']) && !empty($HTTP_GET_VARS['colors'])) {
  // 根绝颜色color_id取得商品列表
  //   ex: http://www.iimy.co.jp/item/co-1.html
  require(DIR_WS_ACTIONS.'index_colors.php');

// 选择页面左侧的快捷链接, JP中无此功能
//} elseif(isset($HTTP_GET_VARS['action']) && $HTTP_GET_VARS['action'] == 'select') {
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
