<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

?>
<?php page_head();?>
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
          <?php 
          $info_page = tep_db_fetch_array(tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where show_status='1' and romaji = 'present_success.php' and site_id = '".SITE_ID."'")); 
          echo str_replace('${PRODUCTS_INFO}','',str_replace('${PROCEDURE}',TEXT_HEADER_INFO,str_replace('${NEXT}','<a href="' .  tep_href_link(FILENAME_DEFAULT) . '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) .  '</a>',$info_page['text_information'])));
          ?>
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
<?php 
//session开放
tep_session_unregister('pc_id');
tep_session_unregister('firstname');
tep_session_unregister('lastname');
tep_session_unregister('email_address');
tep_session_unregister('telephone');
tep_session_unregister('street_address');
tep_session_unregister('suburb');
tep_session_unregister('postcode');
tep_session_unregister('city');
tep_session_unregister('zone_id');


require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
