<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/change_preorder_success.php');
   
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_FETCH, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_CONFIRM, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <div id="main"> 
      <div id="l_menu"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </div> 
      <!-- body_text --> 
      <div id="content"> 
        <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
        <?php 
        $info_page = tep_db_fetch_array(tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where show_status='1' and romaji = 'change_preorder_success.php' and site_id = '".SITE_ID."'")); 
        echo str_replace('${PRODUCTS_INFO}','',str_replace('${PRODUCTS_SUBSCRIPTION}','',str_replace('${PROCEDURE}',TEXT_HEADER_INFO,str_replace('${NEXT}','<a href="' .tep_href_link(FILENAME_DEFAULT).  '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) .  '</a>',$info_page['text_information']))));
        ?> 
      </div>
      <!-- body_text_eof --> 
      <div id="r_menu"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </div> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
