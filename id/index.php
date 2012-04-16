<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'index_top.php');
  if ($_SERVER['REQUEST_URI'] == '/present.php' || $_SERVER['REQUEST_URI'] == '/present_order.php' || $_SERVER['REQUEST_URI'] == '/specials.php') {
    forward404(); 
  }
  if (isset($_GET['tags_id'])) {
    forward404(); 
  }
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/notice.js"></script>
<script type="text/javascript" >
  function replace_game_name()
{
  $(".text").each(function(index) {
    tmp_str = $(this).html();
    tmp_str = tmp_str.replace(/(\（)([^(\)|\）)]*)(\）){0,1}/,"<br />"+"$2");
    $(this).html(tmp_str);
  });
}
</script>
<?php 
$index_i_pos = strpos($_SERVER['PHP_SELF'], '/index.php'); 
if ($index_i_pos !== false) {
  if ($category_depth == 'nested') {
  } elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {
  } elseif ($_GET['colors'] && !empty($_GET['colors'])) {
  } elseif ($_GET['action'] && $_GET['action'] == 'select') {
  } else {
?>
<script type="text/javascript">
 if(window.addEventListener) window.addEventListener('beforeunload', function(){}, false);
</script>
  <?php
  }
}
?>
</head>
<?php
  if (isset($body_option)) {
?>
<body onLoad="replace_game_name()" <?php echo $body_option;?>>
<?php
  } else {
?>
<body onload="replace_game_name()" >
<?php
  }
?>
<div  align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" summary="container" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <!-- body_text //-->
<?php
if ($category_depth == 'nested') {
  require(DIR_WS_ACTIONS.'index_nested.php');
} elseif ($_GET['tags_id']) {
  require(DIR_WS_ACTIONS.'index_tags.php');
?>
  <td valign="top" id="contents_long">
        <h1 class="pageHeading_long">
        <?php
        $sel_tags_query = tep_db_query("select * from ".TABLE_TAGS." where tags_id =
            '".(int)$_GET['tags_id']."'"); 
        $sel_tags_res = tep_db_fetch_array($sel_tags_query); 
        if ($sel_tags_res) {
          echo $sel_tags_res['tags_name']; 
        }
        ?>
        </h1> 
        <div class="comment_long">
        <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?></div>
        </td>
<?php
} elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {

  require(DIR_WS_ACTIONS.'index_products.php');
?> 
  <td valign="top" id="contents_long">
<?php
  #$current_category    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$current_category_id."'"));
  if (tep_show_warning($current_category_id)) {
    echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
  }
?>
    <h1 class="pageHeading_long">
    <span class="game_t"> 
    <?php
  if (isset($cPath_array)) {
    if (count($cPath_array) > 1) {
      $top_c = tep_get_top_category_by_cpath($cPath_array); 
      if ($top_c) {
        echo $top_c['categories_name'].'&nbsp;'; 
      }
    }
    echo $seo_category['categories_name'];
  } elseif ($_GET['manufacturers_id']) {
    echo $seo_manufacturers['manufacturers_name'];
  } else {
    echo HEADING_TITLE;
  }
?></span></h1>
    <div class="comment"><?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_header_text']); //seoフレーズ ?>
        <?php
        $has_ca_single = false; 
        ?>
        <?php if (isset($_GET['cPath'])) { 
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
    if (tep_db_num_rows($categories_query)) {
?> 
        <table border="0" width="100%" cellspacing="3" cellpadding="3" summary=""> 
          <tr align="center">
<?php
    while ($categories = tep_db_fetch_array($categories_query)) {
      $has_ca_single = true; 
      $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '<td class="smallText"><h2 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
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
    } 
    ?>
    <h2 class="line"><?php
  if($_GET['cPath']) {
    $categories_path = explode('_', $_GET['cPath']);
    $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
    echo $_categories['categories_name'];
  } else {
    echo 'RMT：ゲーム通貨・アイテム・アカウント';
  }
 
?></h2>
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
  <p><?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_footer_text']);?></p> 
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
  <?php
   if (isset($_GET['cPath'])) {
      if ($seo_category['seo_description']) {
        echo '<h2 class="pageHeading_long">';
        echo '<span class="game_t">'.str_replace('#STORE_NAME#', STORE_NAME, $seo_category['seo_name']).'について'.'</span>';
        echo '</h2>';
        echo '<div class="comment">'.str_replace('#STORE_NAME#', STORE_NAME, $seo_category['seo_description']).'</div>'; 
      }
      if (!empty($seo_category['text_information'])) {
        echo '<div>'.str_replace('#STORE_NAME#', STORE_NAME, $seo_category['text_information']).'</div>'; 
        //echo '<div class="comment">'.$seo_category['text_information'].'</div>'; 
      }
    }
  ?>
     <?php
        if (isset($cPath) && !ereg('_', $cPath)) {
          //$cgame_news_query = tep_db_query("select ns.headline, ns.date_added, ns.url from ". TABLE_CATEGORIES_ALIAS." a, ".TABLE_CATEGORIES_NEWS." ns where a.categories_id = '".$current_category_id."' and a.alias = ns.categories_name"); 
          //if (tep_db_num_rows($cgame_news_query)) { 
          $all_game_news = tep_get_categories_rss($current_category_id);
          if ($all_game_news) {
            ?>
          <h2 class="pageHeading">
          <span class="game_t"><?php echo $_categories['categories_name'];?> NEWS for</span> 
          </h2>
          <div class="comment"> 
            <div id="game_news">
              <ul>
            <?php 
            //while ($cgame_news_res = tep_db_fetch_array($cgame_news_query)) {
            foreach($all_game_news as $key => $cgame_news_res){
              if($key == CATEGORIES_GAME_NEWS_MAX_DISPLAY)break;
              echo '<li><a href="'.$cgame_news_res['url'].'" rel="nofollow" target="_blank">'.mb_strimwidth($cgame_news_res['headline'],0,95,'...').'</a>'; 
              echo '<span>'.tep_date_short($cgame_news_res['date_added'])."</span></li>";
            } 
          ?>
              </ul> 
            </div> 
          </div> 
          <?php
          }
        } 
      ?>
      </td> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
<?php
} elseif($_GET['colors'] && !empty($_GET['colors'])) {
  require(DIR_WS_ACTIONS.'index_colors.php');
} elseif($_GET['action'] && $_GET['action'] == 'select') { 
  require(DIR_WS_ACTIONS.'index_select.php');
} else { 
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
getPosLeft = screenwidth / 2 - 276;
getPosTop = screenheight / 2 - 73;

$("#popup_notice").css('display', 'block');
$("#popup_notice").css({ "left": getPosLeft, "top": getPosTop })

$(window).resize(function() {
            screenwidth = $(window).width();
           screenheight = $(window).height();
           mytop = $(document).scrollTop();
           getPosLeft = screenwidth / 2 - 276;
           getPosTop = screenheight / 2 - 73;
           $("#popup_notice").css({ "left": getPosLeft, "top": getPosTop + mytop });

});


$("body").append("<div id='greybackground'></div>");
$("#greybackground").css({ "opacity": "0.5", "height": docheight });
});
</script>
<div id="popup_notice" style="display:none;">
<?php
$oc_title_text = '';
$oc_title_raw = tep_db_query("select value from oconfig where keyword = 'reset_pwd_title'");
$oc_title = tep_db_fetch_array($oc_title_raw);
if ($oc_title) {
$oc_title_text = $oc_title['value'];
//  echo $oc_title['value'].'<br>';
}
$oc_content_text = '';
$oc_content_raw = tep_db_query("select value from oconfig where keyword = 'reset_pwd_content'");
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
<a href="javascript:void(0);" onClick="close_popup_notice()"><img alt="次へ進む" src="images/design/popup_henkou.gif"></a>&nbsp;&nbsp;
<a href="javascript:void(0);" onClick="update_notice('<?php echo $update_url;?>')"><img alt="次へ進む" src="images/design/popup_send.gif"></a>
</div>
</div>
<?php
}
?>
<?php
}
?> 
    </tr> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
