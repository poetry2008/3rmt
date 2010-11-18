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
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading">
        <span class="game_im">
          <img width="26" height-"26" src="images/design/title_img20.gif" alt=""> 
        </span>
        <span class="game_t">
        <?php if ($_GET['news_id']) { echo replace_store_name($latest_news['headline']); } else { echo HEADING_TITLE; } ?>
        </span>
        </h1>
        <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td>
          <?php
          $all_game_news = tep_get_rss(ALL_GAME_RSS); 
          ?>
          <ul>
          <?php
          foreach ($all_game_news as $latest_news) {
            if (time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400) {
              $latest_news_new = tep_image(DIR_WS_IMAGES.'design/latest_news_new.gif', $latest_news['headine']); 
            } else {
              $latest_news_new = ''; 
            }
            
            echo '<li class="news_list"><span>'.tep_date_short($latest_news['date_added']).'</span><a class="news_list_link02" href="'.$latest_news['url'].'" rel="nofollow" target="_blank">'.mb_strimwidth($latest_news['headline'],0,95,'...').'&nbsp;&nbsp;'.$lateset_news_image.'</a></li>';
          }
          ?>
          </ul>
          </td>
        </tr>
        </table>
        </div>
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
