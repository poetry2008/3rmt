<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_ACTIONS.'latest_news.php');
?>
<?php page_head();?>
<script type="text/javascript" src="js/emailProtector.js"></script>
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body>
<div align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif"><span><?php if ($_GET['news_id']) { echo replace_store_name($latest_news['headline']); } else { echo HEADING_TITLE; } ?></span></h1>
                <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td>
<?php
  if ($_GET['news_id']) {  
          echo '<table width="100%">';
    if($latest_news['news_image']) {
          echo '<tr>';
          echo '<td  width="60" align="left">';
?>
                      <script type="text/javascript">
                        <!--
                          document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE_NEWS, 'nID=' . $latest_news['news_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES . $latest_news['news_image'], addslashes(replace_store_name($latest_news['headline'])), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>');
                        //-->
                      </script>
                      <noscript><?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $latest_news['news_image']) . '">' . tep_image(DIR_WS_IMAGES . $latest_news['news_image'], replace_store_name($latest_news['headline']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?></noscript>
                      </td>
                      <td style="font-size:11px;"><?php echo replace_store_name($latest_news['news_image_description']); ?></td>
                  </tr>
<?php
    }
?>
  <tr>
    <td colspan="2"><p class="main" style="font-size:12px;"><?php echo nl2br(replace_store_name($latest_news['content'])); ?></p></td>
  </tr>
  </table>
<?php
  } else {
    if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                  </tr>
                </table>
<?php
    }

    echo '<ul>' . "\n";
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
        $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', strip_tags(replace_store_name($latest_news['headline'])), '50', '15');
      } else {
        $latest_news_image = '';
      }
      
      if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
        $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags(replace_store_name($latest_news['headline'])));
      } else {
        $latest_news_new = '';
      }
    
    echo '<li class="news_list">'.tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' .tep_href_link(FILENAME_LATEST_NEWS ,'news_id=' . $latest_news['news_id']).'">' . replace_store_name($latest_news['headline']) . '&nbsp;&nbsp;' . $latest_news_image . $latest_news_new .'</a></li>'."\n";
    
    }
    echo '</ul>' . "\n";
  
    if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
                <br>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                  </tr>
                </table>
<?php
    }
  }
  if ($_GET['news_id']) { 
?>
                <p align="right" class="smallText">
                  [ <?php echo tep_date_long($latest_news['date_added']) ;?> ]
                </p>
                <div align="right">
                  <?php echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
                </div>
<?php
  }
?>
            </td>
          </tr>
        </table>
                </div>
                <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
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
