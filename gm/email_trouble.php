<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/email_trouble.php');
  $breadcrumb->add(TEXT_EMAIL_TITLE, tep_href_link('email_trouble.php'));
?>
<?php page_head();?> 
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
 <?php include('includes/search_include.php');?>
<div id="main-content">
<h2><?php echo TEXT_EMAIL_TITLE; ?></h2>
<div class="box">
<div class="content_email01">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <p>
      <span class="txt_blue"><?php echo TEXT_EMAIL_COMMENT_ONE;?></span>
      <?php echo TEXT_EMAIL_COMMENT_TWO;?> </p>
      <p>
      <?php echo TEXT_EMAIL_COMMENT_THREE;?> 
      </p>
      <p>
        <br>
        <?php echo TEXT_EMAIL_COMMENT_FOUR;?> </p>
      <br>
      <br>
      <h3><span class="txt_bold"><?php echo TEXT_EMAIL_COMMENT_FIVE;?></span></h3>
      <p>
      <ol>
      <li><?php echo TEXT_EMAIL_COMMNET_LI_ONE;?></li>
      <li><?php echo TEXT_EMAIL_COMMNET_LI_TWO;?></li>
      <li><?php echo TEXT_EMAIL_COMMNET_LI_THREE;?><span class="txt_blue"><?php echo STORE_DOMAIN; ?></span><?php echo TEXT_EMAIL_COMMNET_LI_THREE_RIGHT;?></li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold"><?php echo TEXT_EMAIL_COMMENT_H_ONE;?></span></h3>
      <p>
      <ol>
      <li><?php echo TEXT_EMAIL_COMMENT_OL_ONE;?></li>
      <li><?php echo TEXT_EMAIL_COMMENT_OL_TWO;?></li>
      <li><?php echo STORE_NAME;?><?php echo TEXT_EMAIL_COMMENT_OL_THREE;?><span class="txt_blue"><?php echo STORE_DOMAIN;?></span><?php echo TEXT_EMAIL_COMMENT_OL_FOUR;?></li>
      <li><?php echo TEXT_EMAIL_COMMENT_OL_FIVE;?></li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold"><?php echo TEXT_EMAIL_COMMENT_OL_SIX;?></span></h3>
      <p><?php echo TEXT_EMAIL_COMMENT_OL_SEVEN;?> 
      <ol>
        <li><?php echo TEXT_EMAIL_COMMENT_OL_EIGHT;?></li>
        <li><?php echo TEXT_EMAIL_COMMENT_OL_NINE;?><span class="txt_blue"><?php echo STORE_DOMAIN;?></span><?php echo TEXT_EMAIL_COMMENT_OL_TEN;?></li>
      </ol>
      </p>
      <br>
      <h3><span class="txt_bold"><?php echo TEXT_EMAIL_COMMENT_SPAN_ONE;?></span></h3>
      <p> <?php echo TEXT_EMAIL_COMMENT_SPAN_TWO;?><?php echo STORE_NAME;?><?php echo TEXT_EMAIL_COMMENT_SPAN_THREE;?><br>
      <?php echo TEXT_EMAIL_COMMENT_SPAN_FOUR;?><?php echo STORE_NAME.TEXT_EMAIL_COMMENT_SPAN_FIVE;?><span class="txt_blue"><?php echo STORE_DOMAIN;?></span><?php echo TEXT_EMAIL_COMMENT_SPAN_SIX;?><br>
      </p>
      <br>
      <br>
      <br>
    </div>
    <!-- end of wrapper_mail_trouble -->
  </div>
  <!-- end of bgn_content -->
</div>

      </div></div></div>

<?php include('includes/float-box.php');?>
</div>
      <!-- body_text_eof //--> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>

