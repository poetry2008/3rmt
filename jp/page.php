<?php
// 3rmt over
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $error = false;
  $romaji= $_GET['pID'];
  
  if(!$pID || $pID == '0' || $pID == '') {
    $error = true;
  } else {
//ccdd
    $page_query = tep_db_query("
        select * 
        from ".TABLE_INFORMATION_PAGE." 
        where romaji = '".$romaji."' 
          and status = '1' 
          and site_id = '".SITE_ID."'
    ");
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

  $breadcrumb->add(PAGE_NAVBAR_TITLE, '');
?>
<?php page_head();?>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <?php
	  if($error == true) {//No page result
	  ?>

            <p><?php echo PAGE_TEXT_NOT_FOUND; ?></p>
            <div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div>
        <?php
	  } else {
	  ?>
<h1 class="pageHeading"><?php echo PAGE_HEADING_TITLE ; ?></h1> 
        
        <div id="contents"> 
          <?php echo PAGE_TEXT_INFORMATION; ?>
        <?php
	  }
	  ?>
      </div></td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
