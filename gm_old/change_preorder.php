<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'change_preorder.php');
  
  $breadcrumb->add(NAVBAR_CHANGE_PREORDER_TITLE, '');
?>
<?php page_head();?>
<script type="text/javascript">
</script>
<script type="text/javascript" src="js/data.js"></script>
</head>
<body>
<?php 
if ($error == false && $_POST['action'] == 'process') { 
echo tep_draw_form('order1', tep_href_link('change_preorder_confirm.php'));
foreach ($_POST as $post_key => $post_value) {
  if (is_array($post_value)) {
    foreach ($post_value as $ps_key => $ps_value) {
      echo '<input type="hidden" name="'.$post_key.'['.$ps_key.']" value="'.$ps_value.'">';  
      $preorder_info_attr[] = $ps_value;
    }
  } else {
    echo '<input type="hidden" name="'.$post_key.'" value="'.$post_value.'">'; 
  }
}
echo '<input type="hidden" name="pid" value="'.$preorder_id.'">'; 
echo '</form>';
?>
<script type="text/javascript">
  document.forms.order1.submit(); 
</script>
<?php
} 
?>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <div id="main"> 
      <div id="l_menu"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </div> 
      <!-- body_text //--> 
      <div id="content">
          <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div> 
          <h1 class="pageHeading"><?php echo NAVBAR_CHANGE_PREORDER_TITLE;?></h1> 
          <div class="comment_change_preorder">
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
            <tr>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="30%" align="right"><?php echo tep_image(DIR_WS_IMAGES.'checkout_bullet.gif');?></td> 
                    <td width="70%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="58%">
              <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
              </td>
              <td width="22%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="70%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                    <td width="30%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?>
                    </td>
                  </tr>
                </table>  
              </td>
            </tr>
            <tr>
              <td align="left" width="20%" class="preorderBarcurrent"><?php echo PREORDER_TRADER_LINE_TITLE;?></td> 
              <td align="center" width="58%" class="preorderBarTo"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="right" width="22%" class="preorderBarTo"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
            </tr>
          </table>
          <?php
          echo tep_draw_form('order', tep_href_link('change_preorder.php', 'pid='.$_GET['pid'])).tep_draw_hidden_field('action', 'process'); 
          ?>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="c_pay_info">
            <tr>
              <td class="main">
              <?php echo TEXT_PREORDER_FETCH_BUTTON_INFO;?> 
              </td>
              <td class="main" align="right">
                <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE);?> 
              </td>
            </tr>
          </table> 
          <h3 class="formAreaTitle"><?php echo CHANGE_ORDER_CUSTOMER_DETAILS?></h3> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="150">
              <?php echo CHANGE_ORDER_CUSTOMER_NAME;?> 
              </td>
              <td class="main">
              <?php echo $preorder_res['customers_name'];?> 
              </td>
            </tr>
            <tr>
              <td class="main">
              <?php echo CHANGE_ORDER_CUSTOMER_EMAIL;?> 
              </td>
              <td class="main">
              <?php echo $preorder_res['customers_email_address'];?> 
              </td>
            </tr>
          </table>
          <br> 
          <?php
            $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'"); 
            $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
          ?> 
          <h3 class="formAreaTitle"><?php echo CHANGE_ORDER_PRODUCT_DETAILS;?></h3> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="150">
              <?php echo CHANGE_ORDER_PRODUCT_NAME;?> 
              </td>
              <td class="main">
              <?php
                $product_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$preorder_product_res['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
                $product_status_res = tep_db_fetch_array($product_status_raw); 
                if ($product_status_res['products_status'] == 0 || $product_status_res['products_status'] == 3) {
                  echo $preorder_product_res['products_name']; 
                } else {
                ?>
                <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product_res['products_id']);?>" target="_blank"><?php echo $preorder_product_res['products_name'];?></a> 
                <?php
                }
              ?>
              </td>
            </tr>
            <?php
              $product_info_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$preorder_product_res['products_id']."'"); 
              $product_info_res = tep_db_fetch_array($product_info_raw); 
              
              if ($product_info_res['products_cflag'] == 1) {
            ?>
            <tr>
              <td class="main"><?php echo CHANGE_ORDER_PRODUCT_CHARACTER;?></td> 
              <td class="main">
              <?php 
              $p_character_name = $preorder_product_res['products_character']; 
              echo tep_draw_input_field('p_character', isset($_POST['p_character'])?$_POST['p_character']:$p_character_name);
              if (isset($character_error)) {
                echo '<br><font color="#ff0000">'.$character_error.'</font>'; 
              }
              ?> 
              </td>
            </tr>
            <?php
            }  
            ?>
            <tr>
              <td class="main">
              <?php echo CHANGE_ORDER_PRODUCT_NUM;?> 
              </td>
              <td class="main">
              <?php echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;?> 
              <?php echo
              tep_get_full_count2($preorder_product_res['products_quantity'],
                  $preorder_product_res['products_id']);?>
              </td>
            </tr>
        </table> 
        <br> 
        <h3 class="formAreaTitle"><?php echo CHANGE_ORDER_FETCH_TIME_TITLE;?></h3> 
        <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>
              <td class="main" width="150">
              <?php echo CHANGE_ORDER_FETCH_TIME_READ;?> 
              </td>
              <td class="main">
              <?php 
              $ids[] = $preorder_product_res['products_id']; 
              echo tep_get_torihiki_select_by_products($ids);
              if (isset($torihikihouhou_error)) {
                echo '<font color="#ff0000">'.$torihikihouhou_error.'</font>'; 
              }
              ?> 
               
              </td>
        </tr>
        <tr>
          <td class="main">
          <?php echo CHANGE_ORDER_FETCH_DAY;?> 
          </td>
          <td class="main">
            <?php
    $today = getdate();
      $m_num = $today['mon'];
      $d_num = $today['mday'];
      $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="date" onChange="selectDate('<?php echo $hours; ?>', '<?php echo $mimutes; ?>')">
    <option value=""><?php echo PREORDER_SELECT_EMPTY_OPTION;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(PREORDER_MONDAY_TEXT, PREORDER_TUESDAY_TEXT, PREORDER_WENSDAY_TEXT, PREORDER_THIRSDAY_TEXT, PREORDER_FRIDAY_TEXT, PREORDER_STATURDAY_TEXT, PREORDER_SUNDAY_TEXT);
    for($i=0; $i<7; $i++) {
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.str_replace($oarr, $newarr,date("Y".PREORDER_YEAR_TEXT."m".PREORDER_MONTH_TEXT."d".PREORDER_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
    }
    ?>
  </select>
              <?php
              if (isset($date_error)) {
                echo '<font color="#ff0000">'.$date_error.'</font>'; 
              }
              ?> 
              </td>
            </tr>
            <tr>
              <td class="main"><?php echo CHANGE_ORDER_FETCH_DATE;?></td> 
              <td class="main">
  <select name="hour" onChange="selectHour('<?php echo $hours; ?>', '<?php echo $mimutes; ?>')">
    <option value="">--</option>
  </select>
  &nbsp;<?php echo PREORDER_HOUR_TEXT;?>&nbsp;
  <select name="min">
    <option value="">--</option>
  </select>
  &nbsp;<?php echo PREORDER_MIN_TEXT;?>&nbsp;
             <?php  
             if (isset($jikan_error)) {
                echo '<font color="#ff0000">'.$jikan_error.'</font>'; 
              }
 ?> 
  <?php echo TEXT_CHECK_24JI; ?>
              </td> 
            </tr>
          </table> 
          <?php
          $products_options_name_query = tep_db_query("
              SELECT distinct popt.products_options_id, 
                     popt.products_options_name 
              FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
              WHERE patrib.products_id = '" . $preorder_product_res['products_id'] . "' 
                AND patrib.options_id  = popt.products_options_id 
                AND popt.language_id   = '" . $languages_id . "'
          ");
          if (tep_db_num_rows($products_options_name_query)) { 
          ?>
          <br> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <?php
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            echo '<tr><td class="main" width="150">' . $products_options_name['products_options_name'] . ':</td><td>' . "\n";
        $products_options_query = tep_db_query("
            SELECT pov.products_options_values_id, 
                   pov.products_options_values_name, 
                   pa.options_values_price, 
                   pa.price_prefix, 
                   pa.products_at_quantity, 
                   pa.products_at_quantity 
            FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
            WHERE pa.products_id = '" . $preorder_product_res['products_id'] . "' 
              AND pa.options_id = '" . $products_options_name['products_options_id'] . "' 
              AND pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "' 
            ORDER BY pa.products_attributes_id");

            while ($products_options = tep_db_fetch_array($products_options_query)) {
              //add products_at_quantity - ds-style
              if($products_options['products_at_quantity'] > 0) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
              }
            }
            echo tep_draw_pull_down_menu('op_id[' .  $products_options_name['products_options_id'] . ']' , $products_options_array, isset($_POST['op_id'][$products_options_name['products_options_id']])?$_POST['op_id'][$products_options_name['products_options_id']]:'');
            echo '</td></tr>';
          }
            ?>
          </table> 
          <?php }?> 
          <br>
          <?php
          $preorder_total = 0;
          $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_res['orders_id']."' and class = 'ot_subtotal'");
          $preorder_total_res = tep_db_fetch_array($preorder_total_raw);
          if ($preorder_total_res) {
            $preorder_total = number_format($preorder_total_res['value'], 0, '.', ''); 
          }
          if ($is_member_single && MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && ($preorder_total > 0)) { 
            ?>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="150"><?php echo TEXT_PREORDER_POINT_TEXT;?></td> 
              <td class="main">
              <input type="text" name="preorder_point" size="24" value="<?php echo isset($_POST['preorder_campaign_info'])?$_POST['preorder_campaign_info']:(isset($_POST['preorder_point'])?$_POST['preorder_point']:'0');?>" style="text-align:right;">&nbsp;&nbsp;<?php echo $preorder_point;?> 
              <?php 
              echo TEXT_PREORDER_POINT_READ; 
              if (isset($point_error)) {
                echo '<br><font color="#ff0000">'.$point_error.'</font>'; 
              }
              ?>
              </td> 
            </tr>
          </table>
          <br>
          <?php } else if ($is_member_single && MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && ($preorder_total < 0)) { 
          ?>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="150"><?php echo TEXT_PREORDER_POINT_TEXT;?></td> 
              <td class="main">
              <input type="text" name="camp_preorder_point" size="24" value="<?php echo isset($_POST['preorder_campaign_info'])?$_POST['preorder_campaign_info']:(isset($_POST['camp_preorder_point'])?$_POST['camp_preorder_point']:'0');?>" style="text-align:right;">
              <?php 
              if (isset($point_error)) {
                echo '<br><font color="#ff0000">'.$point_error.'</font>'; 
              }
              ?>
              </td> 
            </tr>
          </table>
          <br>
          <?php
          }?> 
          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="c_pay_info">
            <tr>
              <td class="main">
              <?php echo TEXT_PREORDER_FETCH_BUTTON_INFO;?> 
              </td>
              <td class="main" align="right">
                <?php
                 if (!$is_member_single && MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && ($preorder_total > 0)) { 
                   echo '<input type="hidden" name="preorder_point" value="0">'; 
                 }
                ?>
                <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE);?> 
              </td>
            </tr>
          </table> 
          </form> 
          </div>
          <p class="pageBottom"></p>
      </div> 
      <!-- body_text_eof //--> 
      <div id="r_menu"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </div> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div>
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>