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
function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array(); arr_color = new Array(); $("#address_show_id").hide();
    
<?php 
  $address_new_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type!='text' and status='0' order by sort");
  while($address_new_array = tep_db_fetch_array($address_new_query)){
    $address_new_arr = unserialize($address_new_array['type_comment']);
    if($address_new_array['type'] == 'textarea'){
      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_array['comment'] .'";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#999";';
    }elseif($address_new_array['type'] == 'option' && $address_new_arr['select_value'] !=''){
      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['select_value'] .'";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
    }else{

      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';


    }
  }
  tep_db_free_result($address_new_query);
?>
  for(x in arr_new){
     
      var list_options = document.getElementById("op_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
    }
    break;
  case 'old' :
    $("#address_show_id").show();
    var arr_old  = new Array();
<?php
if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != ''){
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();

  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id asc");

   
  $json_str_list = '';
  unset($json_old_array);
  $address_i = 0;
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if($address_i == 7 || $address_i == 8 || $address_i == 9){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
    $address_i++;   
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[$address_num] = $json_str_list; 
      echo 'arr_old['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        echo 'arr_old['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
  }
 
  tep_db_free_result($address_orders_query); 
  }
}
?>
  var address_show_list = document.getElementById("address_show_list");

  address_show_list.options.length = 0;

  len = arr_old.length;
  address_show_list.options[address_show_list.options.length]=new Option('--',''); 
  for(i = 0;i < len;i++){
    j = 0;
    arr_str = '';
    for(x in arr_old[i]){
        if(j == 7 || j == 8 || j == 9){
          arr_str += arr_old[i][x];
        }
        j++;
    }
    if(arr_str != ''){
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i);
    }

  }   
    break;
  }
}

function address_option_list(value){
  var arr_list = new Array();
<?php
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $address_orders_group_array['orders_id'] ."'");

  $json_str_list = '';
  unset($json_old_array); 
  $address_i = 0;
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if($address_i == 7 || $address_i == 8 || $address_i == 9){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
    $address_i++;   
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[] = $json_str_list; 
      echo 'arr_list['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        echo 'arr_list['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
    }
 
  tep_db_free_result($address_orders_query); 
  }
?>
  ii = 0;
  for(x in arr_list[value]){
    var list_option = document.getElementById("op_"+x);
    list_option.style.color = '#000';
    list_option.value = arr_list[value][x];
    //if(ii == 7){

      //fee(arr_list[value][x]);
    //}
    ii++; 
  }

}
</script>
<script type="text/javascript" src="js/data.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
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
<div align="center"> 
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
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
            <tr>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="30%" align="right" valign="top"><?php echo tep_image(DIR_WS_IMAGES.'checkout_bullet.gif');?></td> 
                    <td width="70%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="60%">
              <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
              </td>
              <td width="20%">
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
              <td align="center" width="60%" class="preorderBarTo"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="right" width="20%" class="preorderBarTo"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
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
          <div class="formAreaTitle"><?php echo CHANGE_ORDER_CUSTOMER_DETAILS?></div> 
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
          <div class="formAreaTitle"><?php echo CHANGE_ORDER_PRODUCT_DETAILS;?></div> 
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
              </td>
            </tr>
        </table> 
        <br>
        <!-- 住所 -->
        <?php
        //计算商品的总价格及总重量
        $shipping_preorders_query = tep_db_query("select * from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."'");
        $shipping_preorders_array = tep_db_fetch_array($shipping_preorders_query);
        $shipping_pid = $shipping_preorders_array['orders_id'];
        tep_db_free_result($shipping_preorders_query);
        $weight_total = 0;
        $shipping_products_query = tep_db_query("select * from ". TABLE_PREORDERS_PRODUCTS ." where orders_id='". $shipping_pid ."'");
        while($shipping_products_array = tep_db_fetch_array($shipping_products_query)){

          $shipping_products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $shipping_products_array['products_id'] ."'");
          $shipping_products_weight_array = tep_db_fetch_array($shipping_products_weight_query);
          tep_db_free_result($shipping_products_weight_query);
          $weight_total += $shipping_products_weight_array['products_weight']*$shipping_products_array['products_quantity'];
        }
        tep_db_free_result($shipping_products_query);

        if($weight_total > 0){ 
        ?>
        <div class="formAreaTitle"><?php echo TEXT_ADDRESS;?></div>
        <table border="0" width="100%" cellspacing="2" cellpadding="2" class="formArea"> 
            <tr>
            <td colspan="2" class="main" height="30">
              <input type="radio" name="address_option" value="new" onclick="address_option_show('new');" checked><?php echo TABLE_OPTION_NEW; ?>
              <input type="radio" name="address_option" value="old" onclick="address_option_show('old');"><?php echo TABLE_OPTION_OLD; ?>
            </td>
            </tr>
            <tr id="address_show_id" style="display:none">
            <td class="main" width="150"><?php echo TABLE_ADDRESS_SHOW; ?></td>
            <td class="main" height="30">
            <select name="address_show_list" id="address_show_list" onchange="address_option_list(this.value);">
            <option value="">--</option>
            </select>
            </td></tr>

        <?php
        
          $ad_option->render('');  
        ?>
        </table>
        <br>
        <?php 
        }
        ?>
        
        <!-- 住所结束 -->
        <div class="formAreaTitle"><?php echo CHANGE_ORDER_FETCH_TIME_TITLE;?></div> 
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
          if ($hm_option->whether_show($product_info_res['belong_to_option'])) { 
          ?>
          <br>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
          <tr>
            <td>
            <?php echo $hm_option->render($product_info_res['belong_to_option'], true);?> 
            </td>
          </tr>
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
