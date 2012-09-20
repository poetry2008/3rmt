<?php
/*

  $Id$
  ファイルコードを確認
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'index_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_INDEX);
  if (isset($_GET['tags_id'])) {
    forward404(); 
  }
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/notice.js"></script>
</head>
<?php
if (isset($body_option)) {
?>
<body <?php echo $body_option;?>>
<?php
} else {
?>
<body>
<?php
}
?>
<!-- header --> 
<?php  require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!--body -->

<div id="main">


<!-- left_navigation -->

<!-- left_navigation_eof -->

      <!-- body_text --> 
<?php
if ($category_depth == 'nested') {
           require(DIR_WS_ACTIONS.'index_nested.php');
          } elseif ($_GET['tags_id']) { 
             require(DIR_WS_ACTIONS.'index_tags.php');
  ?>
        <div id="cgi">
              <div id="current"><?php echo $breadcrumb->trail(' <img  src="images/point.gif" alt="img"> '); ?></div>


              <div id="body_text">
                <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
              </div>
      </div>
      <?php
          } elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {
            require(DIR_WS_ACTIONS.'index_products.php');
          ?> 
          <div id="layout" class="yui3-u">
          <div id="current"><?php echo $breadcrumb->trail('<img  src="images/point.gif" alt="img"> '); ?></div>
          <?php include('includes/search_include.php');?>


                <div id="main-content" >

        <h2>
          <?php
              if($_GET['cPath']) {
               $categories_path = explode('_', $_GET['cPath']);
               $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
               echo $_categories['categories_name'];
             } else {
               echo CATEGORY_SHOW_TEXT;
                }
            ?>      
          </h2>


<?php
  if (tep_show_warning($current_category_id)) {
    echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
  }
?>
<div class="list_spacing"><?php echo str_replace('#STORE_NAME#', STORE_NAME,
    $seo_category['categories_header_text']);?></div>
      <?php
      $has_ca_single = false; 
      ?>
      <?php if (isset($_GET['cPath'])) { ?>
      <div id="product_list">

        <ul> 
          <?php
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   c.categories_image, 
                   c.parent_id,
                   cd.site_id,
                   cd.categories_status, 
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
      if($rows%MAX_DISPLAY_CATEGORIES_PER_ROW ==0&&$rows!=0&&MAX_DISPLAY_CATEGORIES_PER_ROW!=0){
        echo '</ul><ul>';
      }
      $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      if(MAX_DISPLAY_CATEGORIES_PER_ROW!=0){
        $c_li_list_style = 'style="width:'.$width.'"';
      }else{
        $c_li_list_style = 'style="padding-left:11px;padding-right:11px"';
      }
      echo '<li '.$c_li_list_style.' ><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
                           if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                 echo $categories['categories_name'] . '</a></li>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
                    }
  }
?> 
        </ul> 
      </div>
     <?php }?>
<h3>

<?php
  if (isset($cPath_array)) {
    echo $seo_category['categories_name']; 
  } elseif ($_GET['manufacturers_id']) {
    echo $seo_manufacturers['manufacturers_name'];
  } else {
    echo HEADING_TITLE ;
  }
?> </h3>
<?php
   if (isset($_GET['cPath'])) {
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
   } else {
      include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); 
   }
?>
  <!--content-->
<div class="space_top">
  <table width="100%" style="border-top:#444 dashed 1px;" class="table_border">
    <tr>
      <td align="left" width="70">
        <?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_footer_text']);?>
      </td>
    </tr>
  </table>
</div>
<?php
  if (isset($_GET['cPath'])) {
    $new_products_category_id = $current_category_id;
    if (!$has_ca_single) {
      include(DIR_WS_MODULES.'new_products3.php');
    } else {
      include(DIR_WS_MODULES.'new_products4.php');
    }
  }
?>
   </div>
</div>
<?php include('includes/float-box.php');?>

</div>
</div>

<?php include('includes/rmt_shopping.php');?>


<?php
} elseif($_GET['colors'] && !empty($_GET['colors'])) {
  require(DIR_WS_ACTIONS.'index_colors.php');
} else {
  require(DIR_WS_ACTIONS.'index_main.php');
  require(DIR_WS_ACTIONS.'index_default.php');
?>
<?php 
if ($_SESSION['reset_flag'] == true){
unset($_SESSION['reset_flag']);
?>
<script type="text/javascript">
$(document).ready(function() {
var docheight = $(document).height();
var screenwidth, screenheight, mytop, getPosLeft, getPosTop
screenwidth = $(window).width();
screenheight = $(window).height();
mytop = $(document).scrollTop();
getPosLeft = 155;
getPosTop = 50;

$("#popup_notice").css('display', 'block');
$("#popup_notice").css({ "top": getPosTop })

$(window).resize(function() {
           screenwidth = $(window).width();
           screenheight = $(window).height();
           mytop = $(document).scrollTop();
           getPosLeft = 155;
           getPosTop = 50;
           $("#popup_notice").css({ "top": getPosTop + mytop });

});
$("body").append("<div id='greybackground'></div>");
$("#greybackground").css({ "opacity": "0.5", "height": docheight });
});
</script>
<div id="popup_notice" style="display:none;">
<?php
$oc_title_text = '';
$oc_title_raw = tep_db_query("select value from ".TABLE_OTHER_CONFIG." where keyword = 'reset_pwd_title'");
$oc_title = tep_db_fetch_array($oc_title_raw);
if ($oc_title) {
$oc_title_text = $oc_title['value'];
//  echo $oc_title['value'].'<br>';
}
$oc_content_text = '';
$oc_content_raw = tep_db_query("select value from ".TABLE_OTHER_CONFIG." where keyword = 'reset_pwd_content'");
$oc_content = tep_db_fetch_array($oc_content_raw);
if ($oc_content) {
$oc_content_text = $oc_content;
//  echo tep_get_replaced_reset_msg($oc_content['value']).'<br>';
}
?>
<div class="popup_notice_text">
	<?php echo $oc_title['value'];?>
</div>
<div class="popup_notice_middle">
<?php 
echo tep_get_replaced_reset_msg($oc_content['value']).'<br>';
$update_url = tep_get_popup_url();
?>
</div>
<div align="center" class="popup_notice_button">
<a href="javascript:void(0);" onClick="close_popup_notice()"><img alt="<?php echo
TEXT_INDEX_PWD_NOCHANGE;?>" src="images/design/changeless.gif"></a>&nbsp;&nbsp;
<a href="javascript:void(0);" onClick="update_notice('<?php echo
$update_url;?>')"><img alt="<?php echo TEXT_INDEX_PWD_CHANGED;?>" src="images/design/change.gif"></a>
</div>
</div>
<?php
}
?>
<?php
}
?>



<!-- footer --> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof --> 
<!--body_EOF// -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
