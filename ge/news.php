<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_ACTIONS.'news.php');
?>
<?php page_head();?>
<script type="text/javascript" src="js/light_box.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
--></script>
</head>
<body>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<div id="main">
<!-- left_navigation -->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php if ($_GET['news_id']) { echo replace_store_name($latest_news['headline']); } else { echo HEADING_TITLE; } ?></h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
<?php
  if ($_GET['news_id']) {  
    if($latest_news['news_image']) {
?>
        <table width="100%" border="0" cellpadding="4" cellspacing="1">
          <tr>
            <td class="infoBoxContents">
              <script type="text/javascript">
                <!--
                  document.write('<?php echo '<a href="javascript:void(0);" onclick=fnCreate(\"'.DIR_WS_IMAGES . $latest_news['news_image'].'\",0)>' . tep_image_new(DIR_WS_IMAGES . $latest_news['news_image'], addslashes(replace_store_name($latest_news['headline'])), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>');
                -->
              </script>
              <noscript>
                <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $latest_news['news_image']) . '">' . tep_image_new(DIR_WS_IMAGES . $latest_news['news_image'], replace_store_name($latest_news['headline']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>
              </noscript>
              <br>
              <?php echo replace_store_name($latest_news['news_image_description']); ?>
            </td>
          </tr>
        </table>
<?php
    }
?>
        <p class="main"><?php echo nl2br(replace_store_name($latest_news['content'])); ?></p>
<?php
  } else {
    if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
          </tr>   
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table>
<?php
    }

    echo '<ul class="show_latest_news">'."\n";
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
        $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', strip_tags(replace_store_name($latest_news['headline'])), '15', '15');
      } else {
        $latest_news_image = '';
      }
      
      if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
        $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags(replace_store_name($latest_news['headline'])));
      } else {
        $latest_news_new = '';
      }
    
    echo '<li class="news_list"><span>'.tep_date_short($latest_news['date_added'])
      .'</span><a href="' .tep_href_link(FILENAME_NEWS ,'news_id=' . $latest_news['news_id']).'">' . replace_store_name($latest_news['headline']) . $latest_news_image . $latest_news_new .'</a></li>'."\n";
    
    }
    echo '</ul>';
  
    if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
          </tr>
        </table>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table>
<?php
    }
  }
  if ($_GET['news_id']) { 
?>
                <p align="right" class="smallText">
          [ <?php echo tep_date_long($latest_news['date_added']); ?> ]
        </p>
                <div style="text-align:right; margin:5px 0;">
          <?php echo '<a href="' . tep_href_link(FILENAME_NEWS) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
        </div>
<?php
  }
?>
      </div>
    </td>
  </tr>
</table>
</div>
<!-- body_text_eof -->
<!-- right_navigation -->
<div id="r_menu">
  <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<!-- right_navigation_eof -->
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
