<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  forward404();
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRESENT));
  
  if(isset($_GET['goods_id']) && $_GET['goods_id']) {
    $present_query = tep_db_query("select * from ".TABLE_PRESENT_GOODS." where goods_id = '".(int)$_GET['goods_id']."' and site_id = '" . SITE_ID . "'") ;
    $present = tep_db_fetch_array($present_query) ;
    $breadcrumb->add($present['title'], tep_href_link(FILENAME_PRESENT, 'goods_id='.$present['goods_id']));
    //forward 404
    forward404Unless($present);
  } 
  
  
?>
<?php page_head();?>
<script language="javascript" type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
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
<h1 class="pageHeading"><?php echo ($_GET['goods_id'] && $_GET['goods_id'] != '' ) ? $present['title'] : HEADING_TITLE ; ?></h1>
      <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
        <tr>
          <td><?php
      ######################
      ##    详细页面    ##
      ######################
      if($_GET['goods_id'] && !empty($_GET['goods_id'])) {
      $present_query = tep_db_query("
          select * 
          from ".TABLE_PRESENT_GOODS." 
          where goods_id = '".(int)$_GET['goods_id']."' 
            and site_id  = '" . SITE_ID . "'
      ") ;
      $present = tep_db_fetch_array($present_query) ;
      forward404Unless($present);
      ?>
          <p align="right" class="box_des"> <?php echo TEXT_PRESENT_ORDER_DATE.tep_date_long($present['start_date']) . '&nbsp;&nbsp;&nbsp;～&nbsp;&nbsp;&nbsp;' . tep_date_long($present['limit_date']) ; ?></p>
          <table class="box_des" border="0" cellspacing="0" cellpadding="2" align="right">
            <tr>
              <td align="center" class="smallText">
              <?php 
              if((file_exists(DIR_WS_IMAGES.'present/'.$present['image']) != '') && ($present['image'] != '')){
                 require('js/light_box.js');
              ?>
              <a href="javascript:void(0);" onclick="fnCreate('<?php echo DIR_WS_IMAGES .'present/'.  $present['image'] ;?>')">
              <?php echo tep_image_new(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5" id="lrgproduct" name="lrgproduct"') .  '<br>'.TEXT_PRESENT_ENLARGE;?>
              </a>
              <?php 
              }
              ?>
              <noscript>
              <?php echo tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT, 'align="right"'); ?>
              </noscript>
              </td>
            </tr>
          </table>
          <p class="box_des">
            <?php 
          if($present['html_check'] == '1') {
            echo stripslashes($present['text']); 
                  }else{
            echo nl2br($present['text']); 
            }
         ?>
          </p>
          <br>
          <table class="box_des" width="100%" border="0" cellpadding="2" cellspacing="0">
            <tr>
              <td><a href="javascript:history.back()"><?php echo tep_image_button('button_back.gif', IMAGE_BUTTON_BACK); ?></a> </td>
              <td align="right"><a href="<?php echo tep_href_link(FILENAME_PRESENT_ORDER,'goods_id='.$_GET['goods_id'],'SSL'); ?>"><?php echo tep_image_button('button_present.gif', IMAGE_BUTTON_PRESENT); ?></a></td>
            </tr>
          </table>
          <?php
      ######################
      ##    一览页面    ##
      ######################
      } else {
      ?>
          <?php
        $today = date("Y-m-d", time());
        $present_query_raw = "
              select * from ".TABLE_PRESENT_GOODS."  
              where site_id = '" . SITE_ID . "'
              order by start_date DESC
        ";
        $present_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $present_query_raw, $present_numrows);
        $present_query = tep_db_query($present_query_raw);
  ?>
          <br>
          <?php 
          if (!$_GET['goods_id'] && ($present_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
          ?> 
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $present_split->display_count($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRESENT); ?></td>
            </tr>
          </table>
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText"><?php echo $present_split->display_links($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
            </tr>
          </table>
          <?php
          }
          ?>
          <div class="underline">&nbsp;</div>
          <table class="box_des" border="0" width="100%" cellspacing="1" cellpadding="2">
            <?php 
   
            $row = 0;
        while($present = tep_db_fetch_array($present_query)){
          $row ++ ;
        ?>
            <tr>
              <td class="main" width="<?php echo SMALL_IMAGE_WIDTH ; ?>"><?php echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],'NONSSL').'">' . tep_image(DIR_WS_IMAGES.'present/'.$present['image'],$present['title'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"') . '</a>'; ?></td>
              <td class="main"><b><?php echo '<a href="'.tep_href_link(FILENAME_PRESENT , 'goods_id='.$present['goods_id'],'NONSSL').'">'. $present['title'].'</a>' ; ?></b> <br>
              <?php echo TEXT_PRESENT_ORDER_DATE;?>:<?php echo tep_date_long($present['start_date']) .'～'. tep_date_long($present['limit_date']); ?>
              <p class="smallText"><?php echo mb_substr(strip_tags($present['text']),0,100) ; ?>..</p></td>
            </tr>
            <?php
        }
        ?>
          </table>
          <?php
      }
      ?>
          </td>
        </tr>
        <?php
    if (!$_GET['goods_id'] && ($present_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
    ?>
        <tr>
          <td><div class="underline">&nbsp;</div>
            <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo $present_split->display_count($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRESENT); ?></td>
              </tr>
            </table>
            <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo $present_split->display_links($present_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <?php
    }
    ?>
      </table></div>
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
