<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_success.php');
  $pe_email = '';
  $preorder_id = 0;  
  
  if (isset($_SESSION['send_preorder_id'])) {
    $preorder_id = $_SESSION['send_preorder_id'];
  }
  
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$preorder_id."' and site_id = '".SITE_ID."'"); 
  $preorder = tep_db_fetch_array($preorder_raw);
 
  if (!$preorder) {
    forward404(); 
  }
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'");
  $preorder_product = tep_db_fetch_array($preorder_product_raw);
  $categories_name = '';

  $ca_path = tep_get_product_path($preorder_product['products_id']);
  
  if (tep_not_null($ca_path)) {
    $ca_path_array = tep_parse_category_path($ca_path); 
  }
  if (isset($ca_path_array)) {
    for ($cnum = 0, $ctnum=sizeof($ca_path_array); $cnum<$ctnum; $cnum++) {
      $categories_query = tep_db_query("
          select categories_name 
          from " .  TABLE_CATEGORIES_DESCRIPTION . " 
          where categories_id = '" .  $ca_path_array[$cnum] . "' 
            and language_id='" . $languages_id . "' 
            and (site_id = ".SITE_ID." or site_id = 0)
          order by site_id DESC
          limit 1" 
      );
      if (tep_db_num_rows($categories_query) > 0) {
        $categories_info = tep_db_fetch_array($categories_query); 
        
        if ($cnum == 0) {
          $categories_name = $categories_info['categories_name']; 
        }
        
        $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
      } else {
        break;
      }
    }
  }
  
  $breadcrumb->add($preorder_product['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product['products_id'])); 
  
  $breadcrumb->add(PREORDER_SUCCESS_ACTIVE_HEAD_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  
  
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        
      </td>
      
      <td valign="top" id="contents">
        <div class="pageHeading"><img align="top" alt="img" src="images/menu_ico.gif"><h1><?php echo PREORDER_SUCCESS_ACTIVE_HEAD_TITLE; ?></h1></div>
                <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="preorder_success_box">
          <tr>
          <td style="font-size:15px; color:#ff0000;">
            <?php echo PREORDER_ACTIVE_SUCCESS_READ_HEAD.'<br><br>';?> 
          </td>
          </tr>
          <tr>
            <td>
            <table class="preorder_active_info" border="0" cellpadding="0" cellspacing="1" width="100%"> 
            <tr> 
            <td colspan="2"> 
            <?php echo PREORDER_SUCCESS_APPOINT_CONTENT;?>
            <br>
            </td> 
            </tr> 
            <tr> 
            <td width="100"> 
            <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_NAME;?>
            </td>
            <td>
            <?php echo $preorder_product['products_name'];?> 
            </td>
            </tr> 
            <tr> 
            <td> 
            <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_NUM;?>
            </td>
            <td>
            <?php echo $preorder_product['products_quantity'].PREORDER_SUCCESS_UNIT_TEXT;?> 
            </td>
            </tr>
            <tr>
            <td>
            <?php echo PREORDER_SUCCESS_APPOINT_PRODUCT_DATE;?>
            </td>
            <td>
            <?php echo date('Y'.PREORDER_SUCCESS_YEAR_TEXT.'m'.PREORDER_SUCCESS_MONTH_TEXT.'d'.PREORDER_SUCCESS_DAY_TEXT, strtotime($preorder['predate']));?>
            </td>
            </tr>
            </table><br>
            </td>
          </tr>
          <tr>
            <td>
              <?php 
              echo PREORDER_ACTIVE_SUCCESS_READ_INFO.'<br>';
              ?>
            </td>
          </tr>
          <tr>
            <td>
            <div class="preorder_active_line">&nbsp;</div> 
            </td>
          </tr>
          <tr>
            <td>
              <?php
              $preorder_product_status_query = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$preorder_product['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
              $preorder_product_status = tep_db_fetch_array($preorder_product_status_query);
              if ($preorder_product_status['products_status'] == 0 || $preorder_product_status['products_status'] == 3) {
                echo sprintf(PREORDER_ACTIVE_SUCCESS_READ_BOTTOM, $categories_name, $preorder_product['products_name']);
              } else {
                echo sprintf(PREORDER_ACTIVE_SUCCESS_READ_BOTTOM, $categories_name, '<a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product['products_id']).'">'.$preorder_product['products_name'].'</a>');
              }
              
              ?> 
            </td>
          </tr>
          <tr>
            <td><br>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                      <td align="right" class="main">
                      </td> 
                    </tr> 
                  </table></td> 
          </tr>
        </table>
                </div>
                <p class="pageBottom"></p>
      </td>
      
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        
      </td>
    </tr>
  </table>
  
  
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>