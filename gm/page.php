<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (!isset($_GET['pID'])) {
    $page_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and site_id = '".SITE_ID."'order by sort_id"); 
    define('PAGE_NAVBAR_TITLE', PAGE_NEW_TITLE); 
  } else {
    $error = false;
    $pID = $_GET['pID'];
  
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
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div id="main">
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>

<?php include('includes/search_include.php');?>


<div id="main-content">

        <?php
    if (isset($error)) { 
            if($error == true) {//No page result
    ?>
    <table width="100%" border="0" cellpadding="0">
      <tr>
      <td><p><?php echo PAGE_TEXT_NOT_FOUND; ?></p></td>
    </tr>
    <tr>
      <td><div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div></td>
    </tr>
    </table>
        <?php
      } else {
    ?>
<h2><?php echo PAGE_HEADING_TITLE ; ?></h2> 
        
        <div id="detail-div"> 
          <?php  echo PAGE_TEXT_INFORMATION; ?>
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

        <div align="left" class="botton-continue">
          <?php echo '<a href="javascript:history.back()">' .
          tep_image_button('button_back.gif',
              IMAGE_BUTTON_BACK,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?>
        </div>

      </div>
      <!-- body_text_eof //--> 

</div>
<?php include('includes/float-box.php');?>
   </div>
</div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

