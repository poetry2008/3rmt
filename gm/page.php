<?php
/*
  $Id: page.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  if (!isset($HTTP_GET_VARS['pID'])) {
    $page_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and site_id = '".SITE_ID."'order by sort_id"); 
    define('PAGE_NAVBAR_TITLE', PAGE_NEW_TITLE); 
  } else {
  $error = false;
  $pID = $HTTP_GET_VARS['pID'];
  
  if(!$pID || $pID == '') {
    $error = true;
  } else {
    if (preg_match("/^\d+$/",$pID)) {
      $page_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '".$pID."' and status = '1' and site_id = '".SITE_ID."'");
    } else {
      $page_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = '".$pID."' and status = '1' and site_id = '".SITE_ID."'");
    }
    if (!tep_db_num_rows($page_query)) {
      $error = true;
    }
  }
  
  //check error
  if($error == false) {
    $page = tep_db_fetch_array($page_query);
	define('PAGE_NAVBAR_TITLE', $page['navbar_title']);
	define('PAGE_HEADING_TITLE', $page['heading_title']);
	define('PAGE_TEXT_INFORMATION', $page['text_information']);
  } else {
	define('PAGE_NAVBAR_TITLE', PAGE_ERR_NAVBER_TITLE);
  }
  
  }
  $breadcrumb->add(PAGE_NAVBAR_TITLE, '');
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
        <?php
	  if (isset($error)) { 
            if($error == true) {//No page result
	  ?>
	  <table class="box_des" width="95%" border="0" cellpadding="0">
	    <tr>
		  <td><p class="box_des"><?php echo PAGE_TEXT_NOT_FOUND; ?></p></td>
		</tr>
		<tr>
		  <td><div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div></td>
		</tr>
	  </table>
        <?php
	    } else {
	  ?>
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo PAGE_HEADING_TITLE ; ?></h1> 
        
        <div id="contents"> 
          <?php echo PAGE_TEXT_INFORMATION; ?>
		</div>
        <?php
	    }
          }else {
         ?>
         <h2><?php echo PAGE_NAVBAR_TITLE;?></h2>
         <ul class="comment_page01">
         <?php
           while ($page_info_res = tep_db_fetch_array($page_info_query)) {
             echo '<li><a href="'.info_tep_href_link($page_info_res['romaji']).'">'.$page_info_res['heading_title'].'</a></li>'; 
           }
         ?>
         </ul>
         <?php
          }
	  ?>
      </div>
      <!-- body_text_eof //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
