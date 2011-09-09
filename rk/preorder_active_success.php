<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_active_success.php');
  
  $pid = $_GET['pid'];
  
  $preorder_pro_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'"); 
  $preorder_pro_res = tep_db_fetch_array($preorder_pro_query);
  
  $url_str = tep_href_link(FILENAME_DEFAULT);
  if ($preorder_pro_res) {
    $product_info_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$preorder_pro_res['products_id']."' and (site_id = '0' or site_id = '".SITE_ID."') order by site_id DESC limit 1"); 
    $product_info_res = tep_db_fetch_array($product_info_query); 
    if ($product_info_res) {
      $url_str = tep_preorder_href_link($product_info_res['romaji']); 
    }
  }
  $breadcrumb->add(PREORDER_ACTIVE_SUCCESS_TITLE, '');
?>
<?php page_head();?>
</head>
<body><div align="center"> 
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
      <h1 class="pageHeading"><?php echo PREORDER_ACTIVE_SUCCESS_TITLE;?></h1> 
      <div class="comment">
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:11px;">
        <tr>
          <td>
            <?php echo PREORDER_ACTIVE_SUCCESS_TEXT;?> 
          </td>
        </tr>
        <tr>
          <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main" align="right"><?php echo '<a href="' .$url_str. '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                    <td align="right" class="main">
                    </td> 
                  </tr> 
                </table></td> 
        </tr>
      </table>
      </div>
      <p class="pageBottom"></p> 
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
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
