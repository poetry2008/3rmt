<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/browser_ie6x.php');
 
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_BROWSER_IE6X));
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
      <h2><?php echo HEADING_TITLE; ?></h2>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:12px;">
              <tr>
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td>
                <p><?php echo IE6_TEXT_ONE;?></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x01.gif" width="420" height="160" alt="<?php echo IE6_IMAGE_ONE;?>">
                  <p>Internet&nbsp;<?php echo IE6_TEXT_TWO;?></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x02.gif" width="420" height="379" alt="<?php echo IE6_IMAGE_TWO;?>">
                  <p><?php echo IE6_TEXT_THREE;?></p>
                  <div class="dot">&nbsp;</div>
                  <p class="redtext"><b><?php echo IE6_TEXT_FOUR;?></b></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x03.gif" width="420" height="379" alt="<?php echo IE6_IMAGE_THREE;?>">
                  <p><?php echo IE6_TEXT_FIVE;?></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x04.gif" width="420" height="268" alt="<?php echo IE6_IMAGE_FOUR;?>">
                  <p><?php echo IE6_TEXT_SIX;?></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x05.gif" width="420" height="380" alt="<?php echo IE6_IMAGE_FIVE;?>">
                  <p><?php echo IE6_TEXT_SEVEN;?></p>
                  <div class="dot">&nbsp;</div>
                  <img src="images/browser/ie6x06.gif" width="410" height="383" alt="<?php echo IE6_IMAGE_SIX;?>">
                  <p><?php echo IE6_TEXT_EIGHT;?></p>
                  <div class="dot">&nbsp;</div>
                  <p><?php echo IE6_TEXT_NINE;?></p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div></div>
     <?php include('includes/float-box.php');?>
      </div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
