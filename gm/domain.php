<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  $breadcrumb->add('相互リンク', tep_href_link('domain.php'));
?>
<?php page_head();?> 
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo '相互リンク'; ?></h1>
<div class="box">
<div class="content_email01">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <ul>
        <li><a href="http://rmt.worldmoney.jp">RMTワールドマネー</a></li>
        <li><a href="http://www.iimy.co.jp">RMTジャックポット</a></li>
      </ul>
    </div>
    <!-- end of wrapper_mail_trouble -->
  </div>
  <!-- end of bgn_content -->
</div>
      </div></div>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>

