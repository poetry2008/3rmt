<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES.$language.'/extend_time.php'); 
  $extend_pid = $_GET['pid'];
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$extend_pid."'");
  $preorder = tep_db_fetch_array($preorder_raw);

  if (!$preorder) {
    forward404(); 
  }
  $error = false; 
  if ($_GET['action'] == 'process') {
    if (empty($_POST['predate'])) {
      $error = true;
      $predate_error = true;
    }
    if (!$error) {
      tep_db_query("update `".TABLE_PREORDERS."` set `predate` = '".$_POST['predate'].' 00:00:00'."' where `orders_id` = '".$extend_pid."' and `site_id` = '".SITE_ID."'");      
      tep_redirect(tep_href_link('extend_time_success.php'));  
    }
  }
  $breadcrumb->add(EXTEND_PREORDER_TIME_TITLE);
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo EXTEND_PREORDER_TIME_TITLE;?></h1>
                <div class="comment">
                <?php
                echo tep_draw_form('preorder', tep_href_link('extend_time.php', 'pid='.$_GET['pid'].'&action=process')); 
                ?>
                <table border="0" width="100%" cellspacing="2" cellpadding="2" class="formArea">
                  <tr>
                    <td class="main"><?php echo EXTEND_CUSTOMERS_NAME_TEXT;?></td> 
                    <td class="main">
                    <?php echo $preorder['customers_name'];?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo EXTEND_PRODUCTS_NAME_TEXT;?></td>                
                    <td class="main">
                    <?php
                      $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$extend_pid."'"); 
                      $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
                      echo $preorder_product_res['products_name']; 
                    ?>
                    </td>                
                  </tr>
                  <tr>
                    <td class="main"><?php echo EXTEND_PRODUCTS_NUM_TEXT;?></td> 
                    <td class="main"><?php echo $preorder_product_res['products_quantity']?></td> 
                  </tr>
                  <tr>
                    <td class="main"><?php echo EXTEND_PREORDER_TIME_TEXT;?></td> 
                    <td class="main">
            <?php
                  $today = getdate();
                  $m_num = $today['mon'];
                  $d_num = $today['mday'];
                  $year = $today['year'];
                
                $hours = date('H');
                $mimutes = date('i');
            ?>
              <select name="predate">
                <option value=""><?php echo PREORDER_SELECT_EMPTY_OPTION;?></option>
                <?php
                      $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                      $newarr = array(PREORDER_MONDAY_TEXT, PREORDER_TUESDAY_TEXT, PREORDER_WENSDAY_TEXT, PREORDER_THIRSDAY_TEXT, PREORDER_FRIDAY_TEXT, PREORDER_STATURDAY_TEXT, PREORDER_SUNDAY_TEXT);
                for($i=0; $i<7; $i++) {
                  if ($_POST['predate'] == date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year))) {
                    $check_str = 'selected'; 
                  } else {
                    $check_str = ''; 
                  }
                  echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'" '.$check_str.'>'.str_replace($oarr, $newarr,date("Y".PREORDER_YEAR_TEXT."m".PREORDER_MONTH_TEXT."d".PREORDER_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
                }
                ?>
              </select>
                      <?php
                      if ($predate_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
                      ?>
                    </td>.
                  </tr>
                  </table>  
                  <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td colspan="2">
                          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                            <tr> 
                              <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                              <td align="right" class="main">
                              </td> 
                            </tr> 
                          </table></td> 
                  </tr>
                </table>
                </form> 
                </div>
                <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
    </tr>
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
