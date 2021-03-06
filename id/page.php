<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (!isset($_GET['pID'])) { 
    $page_info_query = tep_db_query("select * from ". TABLE_INFORMATION_PAGE." where status = 1 and site_id = '".SITE_ID."' order by sort_id"); 
    define('PAGE_NAVBAR_TITLE', PAGE_NEW_TITLE); 
  } else {  
    $error = false;
    $pID = $_GET['pID'];
    
    if(!$pID || $pID == '') {
      $error = true;
    } else {
      $page_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = '".$pID."' and status = '1' and site_id = '".SITE_ID."'");
      if (!tep_db_num_rows($page_query)) {
        $error = true;
        //forward 404
        forward404();
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
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <?php
    if (isset($error)) { 
            if($error == true) {//No page result
    ?>
            <p><?php echo PAGE_TEXT_NOT_FOUND; ?></p>
            <div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div>
        <?php
    } else {
    ?>
    <h1 class="pageHeading">
    <span class="game_t"> 
    <?php echo PAGE_HEADING_TITLE ; ?>
    </span> 
    </h1> 
        <div class="comment">
       <?php echo PAGE_TEXT_INFORMATION; ?>
       </div>
     <?php
      }
          } else {
          ?>
          <h1 class="pageHeading">
          <span class="game_im">
            <img width="26" height="26" src="images/design/title_img20.gif" alt=""> 
          </span>
          <span class="game_t"> 
          <?php echo PAGE_NAVBAR_TITLE;?>
          </span>
          </h1> 
          <div class="comment">
          <ul class="comment_page01">
          <?php
            while ($page_info_res = tep_db_fetch_array($page_info_query)) {
              echo '<li><a href="'.info_tep_href_link($page_info_res['romaji']).'">'.$page_info_res['heading_title'].'</a></li>'; 
            }
          ?>
          </ul>
          </div>
          <?php
          }
    ?>
      </td> 
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
