<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  $error = false;
  $romaji= $_GET['pID'];
  
  if(!$pID || $pID == '0' || $pID == '') {
    $error = true;
  } else {
    $page_query = tep_db_query("
        select * 
        from ".TABLE_INFORMATION_PAGE." 
        where romaji = '".$romaji."' 
          and status = '1' 
          and site_id = '".SITE_ID."'
    ");
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

  $breadcrumb->add(PAGE_NAVBAR_TITLE, '');
?>
<?php page_head();?>
<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script>
<?php //搜索商品 ?>
function search_top_category(ra_str)
{
  $.ajax({
    type:"POST", 
    url:"search_category.php", 
    data:"ra="+ra_str, 
    success:function(msg){
      $("#show_popup_info").html(msg);   
      $("#show_popup_info").css('display', 'block');   
      if( document.getElementById('kanasearch').style.display != 'none'){
      $("#show_popup_info").css('top',$('#kanasearch').position().top);   
      }else if(document.getElementById('englishsearch').style.display != 'none'){
      $("#show_popup_info").css('top',$('#englishsearch').position().top);   
      }
    }
  });
}
<?php //关闭搜索商品弹出框 ?>
function close_top_category(close_name)
{
  $("#"+close_name).css('display', 'none');
}
<?php //切换菜单 ?>
function toggle_index_menu(toggle_num)
{
  if (toggle_num == 0) {
    $('#kanatitle').css('display', 'block'); 
    $('#englishtitle').css('display', 'none'); 
    
    $('#kanasearch').css('display', 'block'); 
    $('#englishsearch').css('display', 'none'); 
  } else {
    $('#kanatitle').css('display', 'none'); 
    $('#englishtitle').css('display', 'block'); 
    
    $('#kanasearch').css('display', 'none'); 
    $('#englishsearch').css('display', 'block'); 
  }
  $('#show_popup_info').css('display', 'none');
}
</script>
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
          <?php 
          echo PAGE_TEXT_INFORMATION; ?>
        <?php
    } 
    ?>
      </div>
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
