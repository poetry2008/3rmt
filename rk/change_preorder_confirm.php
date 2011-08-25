<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if (!isset($_POST['pid'])) {
    forward404(); 
  }
  
  require(DIR_WS_LANGUAGES . $language . '/change_preorder_confirm.php');
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."' and site_id = '".SITE_ID."'");
  $preorder_res = tep_db_fetch_array($preorder_raw);
  if (!$preorder_res) {
    forward404(); 
  } 
  $breadcrumb->add(NAVBAR_CHANGE_PREORDER_TITLE, '');
?>
<?php page_head();?>
<script type="text/javascript">
</script>
<script type="text/javascript" src="js/data.js"></script>
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
          <h1 class="pageHeading"><?php echo NAVBAR_CHANGE_PREORDER_TITLE;?></h1> 
          <div class="comment">
          <?php
          echo tep_draw_form('order', 'change_preorder_process.php'); 
          ?>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main">
                <?php echo PRORDER_CONFIRM_PRODUCT_INFO;?> 
              </td>
            </tr>
            <?php
              $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
            ?>
            <tr>
              <td class="main">
                <table width="100%"> 
                  <?php $preorder_product_res = tep_db_fetch_array($preorder_product_raw);?> 
                  <tr>
                    <td class="main"><?php echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;?></td>                  
                    <td class="main">
                    <?php 
                    echo $preorder_product_res['products_name'];
                    if (isset($_POST['op_id'])) {
                      echo '<br>';  
                      foreach ($_POST['op_id'] as $key => $value) {
                        $pro_option_raw = tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS." where products_options_id = '".$key."' and language_id = '".$languages_id."'"); 
                        $pro_option_res = tep_db_fetch_array($pro_option_raw); 
                        
                        $pro_option_value_raw = tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_id = '".$value."' and language_id = '".$languages_id."'");
                        $pro_option_value_res = tep_db_fetch_array($pro_option_value_raw); 
                        echo $pro_option_res['products_options_name'].':'.$pro_option_value_res['products_options_values_name']; 
                        echo '<br>';  
                      }
                    }
                    ?>
                    </td>                  
                    <td class="main">
                    <?php 
                    echo $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']); 
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main">
                <?php echo PRORDER_CONFIRM_FETCH_INFO;?> 
              </td>
            </tr>
            <tr>
              <td class="main">
                <table width="100%"> 
                  <tr>
                    <td class="main"><?php echo PREORDER_CONFIRM_FETCH_TIME_READ;?></td>                  
                    <td class="main">
                    <?php echo $_POST['torihikihouhou'];?> 
                    </td>                  
                  </tr>
                  <tr>
                    <td class="main"><?php echo PREORDER_CONFIRM_FETCH_TIME_DAY;?></td>                  
                    <td class="main">
                    <?php
                      if (!empty($_POST['date'])) {
                        $date_arr = explode('-', $_POST['date']); 
                        echo $date_arr[0].'年'.$date_arr[1].'月'.$date_arr[2].'日'; 
                      }
                    ?>
                    </td>                  
                  </tr>
                  <tr>
                    <td class="main"><?php echo PREORDER_CONFIRM_FETCH_TIME_DATE;?></td>                  
                    <td class="main">
                    <?php
                    echo $_POST['hour'].'時'.$_POST['min'].'分'; 
                    ?>
                    </td>                  
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <?php echo PREORDER_CONFIRM_CHARACTER.$_POST['p_character'];?> 
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
            <tr>
              <td class="main" width="30%">
                <table width="100%" cellpadding="2" cellspacing="2" border="0"> 
                  <tr>
                    <td class="main"><?php echo CHANGE_ORDER_CONFIRM_PAYMENT;?></td>                  
                  </tr>
                  <tr>
                    <td class="main">
                    <?php echo $preorder_res['payment_method'];?> 
                    </td>                  
                  </tr>
                </table> 
              </td>
              <td width="70%" align="right" valign="top">
                <table border="0" cellpadding="2" cellspacing="0"> 
                  <?php
                  if ($preorder_res['code_fee']) {
                  ?>
                  <tr>
                    <td class="main" align="right"><?php echo CHANGE_PREORDER_HANDLE_FEE_TEXT;?></td> 
                    <td class="main" align="right"><?php echo $currencies->format_total($preorder_res['code_fee']);?></td> 
                  </tr>
                  <?php
                  }
                  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."' order by sort_order asc"); 
                  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) { 
                  ?>
                  <tr>
                    <td class="main" align="right"><?php echo $preorder_total_res['title'];?></td>                  
                    <td class="main" align="right"><?php echo $currencies->format_total($preorder_total_res['value'])?></td>                  
                  </tr>
                <?php }?> 
                  <tr>
                    <td class="main" align="right"><?php echo CHANGE_PREORDER_POINT_TEXT;?></td> 
                    <td class="main" align="right">0p</td> 
                  </tr>
                </table> 
              </td>
            </tr>
          </table>
          <br> 
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td class="main" align="left">
              <a href="javascript:void(0);" onclick="document.forms.order1.submit();"><?php echo tep_image_button('button_back.gif', IMAGE_BUTTON_BACK);?></a> 
              </td>
              <td class="main" align="right">
                <?php
                foreach ($_POST as $pe_key => $pe_value) {
                  if ($pe_key == 'action' || $pe_key == 'x' || $pe_key == 'y') {
                    continue; 
                  }
                  if (is_array($pe_value)) {
                    foreach ($pe_value as $pes_key => $pes_value) {
                      echo tep_draw_hidden_field($pe_key.'['.$pes_key.']', $pes_value); 
                    }
                  } else {
                    echo tep_draw_hidden_field($pe_key, $pe_value); 
                  }
                }
                ?>
                <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE);?> 
              </td>
            </tr>
          </table> 
          </form> 
          <?php 
          echo tep_draw_form('order1', tep_href_link('change_preorder.php?pid='.$_POST['pid']));
          foreach ($_POST as $post_key => $post_value) {
            if ($post_key == 'action' || $post_key == 'x' || $post_key == 'y') {
              continue; 
            }
            if (is_array($post_value)) {
              foreach ($post_value as $ps_key => $ps_value) {
                echo tep_draw_hidden_field($post_key.'['.$ps_key.']', $ps_value); 
              }
            } else {
              echo tep_draw_hidden_field($post_key, $post_value); 
            }
          }
          echo tep_draw_hidden_field('pid', $_GET['pid']); 
          echo '</form>';
          ?> 
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
