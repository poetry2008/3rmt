<?php
/*
  $Id$
*/
?>
<script type="text/javascript">
   $(document).ready(function(){hidden_payment()});

$(document).ready(function(){
<?php
  if(isset($_SESSION['error_array']) && !empty($_SESSION['error_array'])){


    foreach($_SESSION['error_array'] as $value){

      echo '$("#error_'.$value.'").html("<font color=red>&nbsp;&nbsp;Error</font>");';
    } 
  }
  unset($_SESSION['error_array']);
?>
});
<?php 
$t_today = getdate();
$t_mon = $t_today['mon'];
$t_day = $t_today['mday'];
$t_year = $t_today['year'];
$t_hour = $t_today['hours'];
$t_min = $t_today['minutes'];
?>
<?php //检查年份 ?>
function check_year(value){
  var mon = document.getElementById('mon');
  var mon_value = mon.value;
  var day = document.getElementById('day');
  var day_value = day.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value; 
  var min = document.getElementById('min');
  var min_value = min.value; 


  var run_num;
  if((value % 4 == 0 && value % 100 != 0) || value % 400 == 0){

    run_num = 29;
  }else{

    run_num = 28;
  } 
  var day_num;
  switch(mon_value){

  case '1':
  case '3':
  case '5':
  case '7':
  case '8':
  case '10':
  case '12':
    day_num = 31;
    break;
  case '4':
  case '6':
  case '9':
  case '11':
    day_num = 30;
    break;
  case '2':
    day_num = run_num;
    break;
  } 
  if(value != <?php echo $t_year;?>){
    mon.options.length = 0;
    for(m_i = 1;m_i <= 12;m_i++){
      mon.options[mon.options.length]=new Option(m_i,m_i,m_i==mon_value); 
    }
    day.options.length = 0;
    for(d_i = 1;d_i <= day_num;d_i++){
      day.options[day.options.length]=new Option(d_i,d_i,d_i==day_value); 
    }
    hour.options.length = 0;
    for(h_i = 1;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = 0;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    }
  }else{
    mon.options.length = 0;
    for(m_i = <?php echo $t_mon;?>;m_i <= 12;m_i++){
      mon.options[mon.options.length]=new Option(m_i,m_i,m_i==mon_value); 
    }
    day.options.length = 0;
    for(d_i = <?php echo $t_day;?>;d_i <= day_num;d_i++){
      day.options[day.options.length]=new Option(d_i,d_i,d_i==day_value); 
    }
    hour.options.length = 0;
    for(h_i = <?php echo $t_hour;?>;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = <?php echo $t_min;?>;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    } 
  } 
}
<?php //检查是否周一 ?>
function check_mon(value){
  var year = document.getElementById('year');
  var year_value = year.value;
  var mon = document.getElementById('mon');
  var mon_value = mon.value;
  var day = document.getElementById('day');
  var day_value = day.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value; 
  var min = document.getElementById('min');
  var min_value = min.value;
  
  var run_num;
  if((year_value % 4 == 0 && year_value % 100 != 0) || year_value % 400 == 0){

    run_num = 29;
  }else{

    run_num = 28;
  } 
  var day_num;
  switch(value){

  case '1':
  case '3':
  case '5':
  case '7':
  case '8':
  case '10':
  case '12':
    day_num = 31;
    break;
  case '4':
  case '6':
  case '9':
  case '11':
    day_num = 30;
    break;
  case '2':
    day_num = run_num;
    break;
  } 
  if(year_value == <?php echo $t_year;?> && value == <?php echo $t_mon;?>){
    day.options.length = 0;
    for(d_i = <?php echo $t_day;?>;d_i <= day_num;d_i++){
      day.options[day.options.length]=new Option(d_i,d_i,d_i==day_value); 
    }
    hour.options.length = 0;
    for(h_i = <?php echo $t_hour;?>;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = <?php echo $t_min;?>;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    } 
  }else{
    day.options.length = 0;
    for(d_i = 1;d_i <= day_num;d_i++){
      day.options[day.options.length]=new Option(d_i,d_i,d_i==day_value); 
    } 
    hour.options.length = 0;
    for(h_i = 1;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = 0;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    }
  }
}
<?php //检查日期 ?>
function check_day(value){
  var year = document.getElementById('year');
  var year_value = year.value;
  var mon = document.getElementById('mon');
  var mon_value = mon.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
  var min = document.getElementById('min');
  var min_value = min.value;
   
  if(year_value == <?php echo $t_year;?> && mon_value == <?php echo $t_mon;?> && value == <?php echo $t_day;?>){
    hour.options.length = 0;
    for(h_i = <?php echo $t_hour;?>;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = <?php echo $t_min;?>;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    }
  }else{
    hour.options.length = 0;
    for(h_i = 1;h_i <= 24;h_i++){
      h_i_str = h_i == 24 ? '00' : h_i;
      hour.options[hour.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_value); 
    }
    min.options.length = 0;
    for(mi_i = 0;mi_i <= 59;mi_i++){
      min.options[min.options.length]=new Option(mi_i,mi_i,mi_i==min_value); 
    } 
  }
}
<?php //检查小时 ?>
function check_hour(value){
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
   
  hour_1.options.length = 0;
  value = value == '00' ? 0 : value;
  for(h_i = value;h_i <= 24;h_i++){
      if(h_i == 0){continue;}
      h_i_str = h_i == 24 ? '00' : h_i;
      hour_1.options[hour_1.options.length]=new Option(h_i_str,h_i_str,h_i_str==value); 
    }
}
<?php //检查分钟 ?>
function check_min(value){
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
   
    min_1.options.length = 0;
    for(mi_i = value;mi_i <= 59;mi_i++){
      min_1.options[min_1.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
}
<?php //最后配送的小时和分钟 ?>
function check_hour_1(value){
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;

  
  if(hour_value == value){ 
    min_1.options.length = 0;
    for(mi_i = min_1_value;mi_i <= 59;mi_i++){
      min_1.options[min_1.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
  }else{

    min_1.options.length = 0;
    for(mi_i = 0;mi_i <= 59;mi_i++){
      min_1.options[min_1.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
  }
}
</script>
<?php
$orders_get = isset($_GET['oID']) ? '?oID='.$_GET['oID'] : '';

if(isset($_GET['Customer_mail']) && isset($_GET['oID'])){
  $Customer_mail_get = '&Customer_mail='.$_GET['Customer_mail']; 
}elseif(isset($_GET['Customer_mail'])){
  $Customer_mail_get = '?Customer_mail='.$_GET['Customer_mail']; 
}else{
  $Customer_mail_get = '';
}

if(isset($_GET['oID']) && isset($_GET['site_id'])){
  $site_get = '&site_id='.$_GET['site_id'];
}elseif(isset($_GET['site_id']) && isset($_GET['Customer_mail'])){

  $site_get = '&site_id='.$_GET['site_id'];
}elseif(isset($_GET['site_id'])){
  $site_get = '?site_id='.$_GET['site_id'];
}else{
  $site_get = '';
}

if(isset($_GET['action']) && $_GET['action'] == 'add_product'){

  $add_product_get = '&action=add_product';
}else{
  $add_product_get = '';
}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<form id="create_order_form_1" name="create_order_form_1" method="post" action="create_order_process.php<?php echo $orders_get.$add_product_get.$Customer_mail_get.$site_get;?>">
<input type="hidden" id="fax_value" name="fax" value="">
  <tr>
  <td class="formAreaTitle">
  <?php
  echo CATEGORY_CORRECT; ?></td>
  </tr>
  <tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><table border="0" cellspacing="0" cellpadding="2">
  <tr>
  <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID;?></td>
  <td class="main">&nbsp;
  <?php 
    echo tep_draw_hidden_field('customers_id', $customer_id) . $customer_id;
    echo tep_draw_hidden_field('customers_guest_chk', $customers_guest_chk)
  ?>
  </td>
  </tr>
  <tr>
  <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME;?></td>
  <td class="main">
     &nbsp;
     <?php 
      $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : $lastname;
      echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . 
       ENTRY_LAST_NAME_TEXT;?>&nbsp;&nbsp;<?php 
     if (isset($customer_error) && $customer_error == true) { 
       echo '<font color="red">'. CREATE_ORDER_CUSTOMERS_ERROR .'</font>'; 
     }else{
       if (isset($entry_lastname_error) && $entry_lastname_error == true) { 
         echo '<font color="red">'. CREATE_ORDER_CUSTOMERS_ERROR .'</font>'; 
       }else{
         echo CREATE_ORDER_NOTICE_ONE;
       }
     } 
     ?>
   </td>
</tr>
<tr>
<td class="main">&nbsp;<?php
echo ENTRY_FIRST_NAME;
?></td>
<td class="main">&nbsp;
<?php
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : $firstname;
echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
?>&nbsp;&nbsp;<?php
if (isset($customer_error) && $customer_error == true) { 
  echo '<font color="red">'. CREATE_ORDER_CUSTOMERS_ERROR .'</font>'; 
}else{
  if (isset($entry_firstname_error) && $entry_firstname_error == true && !isset($customer_error)){
    echo '<font color="red">'. CREATE_ORDER_CUSTOMERS_ERROR .'</font>'; 
  }else{
    echo CREATE_ORDER_NOTICE_ONE;
  }
}
?>

</td>
</tr>
<tr>
<td class="main">&nbsp;<?php
echo ENTRY_EMAIL_ADDRESS;
?></td>
<td class="main">&nbsp;
<?php
echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red">' . $email_address . '</font>';
?>
  
<?php
if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">'.TEXT_REQUIRED.'</font>'; };
?></td>
</tr>
</table></td>
</tr>
</table></td>
</tr>
<tr>
<td class="formAreaTitle">
  <br>
  <?php   echo CATEGORY_SITE; ?>
</td>
</tr>
<tr>
<td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><table border="0" cellspacing="0" cellpadding="2">
  <tr>
  <td class="main">&nbsp;<?php
echo ENTRY_SITE;
?>:</td>
<td class="main">&nbsp;
<?php
echo isset($account) && $account?( '<font color="#FF0000">'.tep_get_site_romaji_by_id($account['site_id']).'</font>'.tep_draw_hidden_field('site_id', $account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT);
?></td>
</tr>
</table></td>
</tr>
</table></td>
</tr>
  
<?php

if (ACCOUNT_COMPANY == 'true' && false) {
  ?>
  <tr>
    <td class="formAreaTitle"><br>
                                                                                                       
    <?php
    echo CATEGORY_COMPANY;
  ?></td>
  </tr>
      <tr>
      <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
      <td class="main"><table border="0" cellspacing="0" cellpadding="2">
      <tr>
      <td class="main">&nbsp;
  <?php
  echo ENTRY_COMPANY;
  ?></td>
  <td class="main">&nbsp;
  <?php
  echo tep_draw_input_field('company', $company) . '&nbsp;' . ENTRY_COMPANY_TEXT;
  ?></td>
  </tr>
      </table></td>
      </tr>
      </table></td>
      </tr>
      <?php
      }
?>


<?php

if(isset($_GET['oID']) && $_GET['oID'] != ''){
  $oID = $_GET['oID'];
}else{
  $oID = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
}
$orders_oid_query = tep_db_query("select orders_id from ". TABLE_ORDERS ." where orders_id='".$oID."'");
$ordres_oid_num_rows = tep_db_num_rows($orders_oid_query);
tep_db_free_result($orders_oid_query);
$orders_exit_flag = false;
if($ordres_oid_num_rows > 0){

  $orders_exit_flag = true;
}
?>

<!-- Begin Products Listing Block --> 
          <?php
            $order = new order($oID);
            // Override order.php Class's Field Limitations
            $index = 0;
          $order->products = array();
          $orders_products_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($oID) . "'");
          while ($orders_products = tep_db_fetch_array($orders_products_query)) {
            $order->products[$index] = array('qty' => $orders_products['products_quantity'],
                'name' => str_replace("'", "&#39;", $orders_products['products_name']),
                'model' => $orders_products['products_model'],
                'tax' => $orders_products['products_tax'],
                'price' => $orders_products['products_price'],
                'final_price' => $orders_products['final_price'],
                'orders_products_id' => $orders_products['orders_products_id']);

            $subindex = 0;
            $attributes_query_string = "select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($oID) . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
            $attributes_query = tep_db_query($attributes_query_string);

            if (tep_db_num_rows($attributes_query)) {
              while ($attributes = tep_db_fetch_array($attributes_query)) {
                $order->products[$index]['attributes'][$subindex] = array('id' => $attributes['orders_products_attributes_id'],
                    'option_info' => @unserialize(stripslashes($attributes['option_info'])),                    
                    'option_group_id' => $attributes['option_group_id'],                    
                    'option_item_id' => $attributes['option_item_id'],                    
                    'price' => $attributes['options_values_price']);
                $subindex++;
              }
            }
            $index++;
          }
          ?>
            <?php // Version without editable names & prices ?>
<tr><td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td class="pageHeading"><br>
  <?php
  echo CREATE_ORDER_PRODUCTS_ADD_TITLE;?>:</td>
  </tr>
<?php
if($index > 0){
?>
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
</tr>
<tr>
<td class="formAreaTitle"><?php echo EDIT_ORDERS_PRO_LIST_TITLE;?></td>
</tr>
<?php
}
?>
</table>
</td></tr>

<?php
if($index > 0){
?>
<tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><input type="hidden" name="oID" value="<?php echo $oID;?>">
  
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr style="background-color: #e1f9fe;">
            <td class="dataTableContent" colspan="2" width="35%">&nbsp;<?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_CURRENICY;?></td>
            <td class="dataTableContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER;?></td>
            </tr>

          <?php
          $currency_text  = DEFAULT_CURRENCY . ",1"; 
          $currency_array = explode(",", $currency_text);
          $currency = $currency_array[0];
          $currency_value = $currency_array[1];
          $only_buy= true;
          for ($i=0; $i<sizeof($order->products); $i++) {
            $orders_products_id = $order->products[$i]['orders_products_id'];
            if(!tep_get_bflag_by_product_id($orders_products_id)){
              $only_buy= false;
            }
            $RowStyle = "dataTableContent";
            $porducts_qty = isset($products_error) && $products_error ? 0 : $order->products[$i]['qty'];
            $porducts_qty = isset($_POST['update_products'][$orders_products_id]['qty']) ? $_POST['update_products'][$orders_products_id]['qty'] : $order->products[$i]['qty'];
            $porducts_qty = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'] : $porducts_qty;
            $order->products[$i]['qty'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'] : $order->products[$i]['qty'];
            echo '    <tr>' . "\n" .
              '      <td class="' . $RowStyle . '" align="left" valign="top" width="35">&nbsp;'
              . "<input type='hidden' id='update_products_qty_$orders_products_id' value='" . $order->products[$i]['qty'] . "'><input type='hidden' class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" . $porducts_qty . "' onkeyup=\"clearLibNum(this);\">".$porducts_qty."&nbsp;x</td>\n" .  '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input id='update_products_name_$orders_products_id' name='update_products[$orders_products_id][name]' size='64' type='hidden' value='" . $order->products[$i]['name'] . "'>\n"; 
            // 判断是否存在Attributes?
            if (sizeof($order->products[$i]['attributes']) > 0) {
              $op_info_array = array();
                 for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
                 $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id'];
              }
              $op_info_str = implode('|||', $op_info_array);
              for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
                $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
                $order->products[$i]['attributes'][$j]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'] : $order->products[$i]['attributes'][$j]['price'];

                $order->products[$i]['attributes'][$j]['option_info']['title'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title'] : $order->products[$i]['attributes'][$j]['option_info']['title'];
                $order->products[$i]['attributes'][$j]['option_info']['value'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value'] : $order->products[$i]['attributes'][$j]['option_info']['value'];
                echo '<div class="order_option_list"><small>&nbsp;<i><div
                  class="order_option_info"><div class="order_option_title"> - ' .str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;"))) . ': <input type="hidden" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][option]" size="10" value="' .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;")) . '">' . 
                  '</div><div class="order_option_value">' . 
                  str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&quot;"))).'<input type="hidden" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][value]" size="35" value="' .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&quot;"));
                echo '"></div></div>';
                echo '<div class="order_option_price">';
                echo "<input type='hidden' size='9' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$order->products[$i]['attributes'][$j]['price'])."' onkeyup=\"recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."');\">";                  echo (int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$order->products[$i]['attributes'][$j]['price']);
                echo TEXT_MONEY_SYMBOL;
                echo '</div>';
                echo '</i></small></div>';
              }
            }

            echo '      </td>' . "\n" .
              '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
              '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n";
            $order->products[$i]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price'] : $order->products[$i]['price'];
            if($order->products[$i]['price'] < 0){
              $products_price_value = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2))).'</font>'; 
            }else{
              $products_price_value = $currencies->format(tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2))); 
            }
              echo '<td class="'.$RowStyle.'" align="right"><input type="hidden" style="text-align:right;" class="once_pwd" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'" onkeyup="if(!clearNoNum(this)){recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\', \''.$op_info_str.'\');}">'.str_replace(TEXT_MONEY_SYMBOL,'',$products_price_value).TEXT_MONEY_SYMBOL.'</td>'; 
            $order->products[$i]['final_price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price'] : $order->products[$i]['final_price'];
            if($order->products[$i]['final_price'] < 0){
              $product_tax_price_value = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs($order->products[$i]['final_price']),2))).'</font>';  
            }else{
              $product_tax_price_value = $currencies->format(tep_display_currency(number_format(abs($order->products[$i]['final_price']),2))); 
            }
              echo '      <td class="' . $RowStyle . '" align="right">' . "<input
              type='hidden' class='once_pwd' style='text-align:right;' name='update_products[$orders_products_id][final_price]' size='9' value='" . tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) 
              . "' onkeyup='clearNoNum(this)' >" .
              '<input type="hidden" name="op_id_'.$orders_products_id.'" 
              value="'.tep_get_product_by_op_id($orders_products_id).'">' .str_replace(TEXT_MONEY_SYMBOL,'',$product_tax_price_value).TEXT_MONEY_SYMBOL ."\n" . '</td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value);
            }
            echo '</div></td>' . "\n" . 
              '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value);
            }
            echo '</div></td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $currency, $orders_exit_flag == true ? $order->info['currency_value'] : $currency_value);
            }
            echo '</div></td>' . "\n" . 
              '    </tr>' . "\n";
          }
          ?>
            </table>

            </td>
            </tr>     
</table>
</td>
</tr>
<?php
}
?>
</form>
  <tr>
  <td class="formAreaTitle"><?php echo $index > 0 ? '<br>' : tep_draw_separator('pixel_trans.gif', '100%', '10');echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
  </tr>
  <tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">  
  <tr>
  <td class="main"><input type="hidden" name="oID" value="<?php echo $oID;?>">  

          <?php
          //   获得全部商品列表

          $result = tep_db_query("
              SELECT products_name, 
              p.products_id, 
              cd.categories_name, 
              ptc.categories_id 
              FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id 
              where pd.language_id = '" . (int)$languages_id . "' 
              and cd.site_id = '0'
              and pd.site_id = '0'
              ORDER BY categories_name");
        while($row = tep_db_fetch_array($result))
        {
          extract($row,EXTR_PREFIX_ALL,"db");
          $ProductList[$db_categories_id][$db_products_id] = $db_products_name;
          $CategoryList[$db_categories_id] = $db_categories_name;
          $LastCategory = $db_categories_name;
        }


        $LastOptionTag = "";
        $ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
        $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
        foreach($ProductList as $Category => $Products)
        {
          $ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
          $ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
          asort($Products);
          foreach($Products as $Product_ID => $Product_Name)
          {
            $ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
          }

          if($Category != $LastCategory)
          {
            $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
            $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
          }
        }


        //  添加商品步骤 
        if(isset($Customer_mail) && $Customer_mail != '' && isset($site_id) && $site_id != ''){
          $param_str = "&Customer_mail=$Customer_mail&site_id=$site_id";
        }
        print "<table border='0' cellspacing='0' cellpadding='2' width='100%'>\n";
        
        $Customer_mail = tep_db_prepare_input($_GET['Customer_mail']);
        $site_id = tep_db_prepare_input($_GET['site_id']);
        // 设置默认值 
        if(!isset($add_product_categories_id))
          $add_product_categories_id = 0;

        if(!isset($add_product_products_id))
          $add_product_products_id = 0;

        // 步骤 1: 选择分类
        $product_error = isset($products_error) && $products_error == true ? PRODUCT_ERROR : '';
        $PHP_SELF = 'create_order.php';
        print "<tr>\n";
        print "<td class='dataTableContent' width='70'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 1:</td>\n";
        print "<td class='dataTableContent'>";
        print "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST'>\n";
        print '<table>'; 
        print '<tr>';
        print '<td width="150">'; 
        print ADDPRODUCT_TEXT_STEP1_TITLE; 
        print '</td>'; 
        print '<td>'; 
        print tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
        print "<input type='hidden' name='step' value='2'>";
        print '<td></tr>'; 
        print '</table>'; 
        print "</form>";
        print "</td>\n";
        print "<td class='dataTableContent'>&nbsp;<span><font color='red'>".$product_error."</font></span></td>\n";
        print "</tr>\n";

        // 步骤 2: 选择商品
        if(($step > 1) && ($add_product_categories_id > 0))
        {
          print "<tr>\n";
          print "<td class='dataTableContent'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 2: </td>\n";
          print "<td class='dataTableContent'>";
          print "<form action='$PHP_SELF?oID=$oID&action=$action$param_str' method='POST'>\n";
          print "<table>"; 
          print "<tr><td width='150'>"; 
          print ADDPRODUCT_TEXT_STEP2_TITLE."</td>";
          print "<td>";
          print "<select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
          $ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
          asort($ProductList[$add_product_categories_id]);
          foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
          {
            $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
          }
          $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
          print $ProductOptions;
          print "</select>";
          print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
          print "<input type='hidden' name='step' value='3'>\n";
          print "<input type='hidden' name='cstep' value='1'>\n";
          print "</td>";
          print "</tr>";
          print "</table>";
          print "</form>";
          print "</td>\n";
          print "<td class='dataTableContent'>&nbsp;</td>\n";
          print "</tr>\n";
        }
        $hm_option = new HM_Option();
        if(($step == 3) && ($add_product_products_id > 0) && isset($_POST['action_process'])) {
          if (!$hm_option->check()) {
            $step = 4; 
          }
        }
        // 步骤 3: 选择 Options
        if(($step > 2) && ($add_product_products_id > 0))
        {
          $option_product_raw = tep_db_query("select products_cflag, belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'"); 
          $option_product = tep_db_fetch_array($option_product_raw); 
          if(!$hm_option->admin_whether_show($option_product['belong_to_option'], 0, $option_product['products_cflag']))
          {
            print "<tr>\n";
            print "<td class='dataTableContent' valign='top'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 3: </td>\n";
            print "<td class='dataTableContent' valign='top' colspan='2'><i>" . ADDPRODUCT_TEXT_OPTIONS_NOTEXIST . "</i></td>\n";
            print "</tr>\n";
            $step = 4;
          }
          else
          {
            $p_cflag = tep_get_cflag_by_product_id($add_product_products_id);
            print "<tr>\n";
            print "<td class='option_title_space' valign='top'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 3: </td><td class='dataTableContent' valign='top'>";
            print "<form name='coform' action='$PHP_SELF?oID=$oID&action=$action$param_str' method='POST'>";
            print $hm_option->render($option_product['belong_to_option'], false, 2, '', '', $p_cflag); 
            print "</td>";
            print "<td class='dataTableContent' align='center' valign='top'>";
            print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
            print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
            print "<input type='hidden' name='step' value='3'>";
            print "<input type='hidden' name='action_process' value='1'>";
            print "</form></td>\n";
            print "</tr>\n";
            print "<tr>"; 
            print "<td colspan='4' class='dataTableContent' align='right'>"; 
            print "<div><input type='button' value='" .  ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "' onclick='document.forms.coform.submit();'></div>";
            print "</td>"; 
            print "</tr>"; 
          }

        }

        // 步骤 4: 最终确认
        if($step > 3)
        {
          $products_query = tep_db_query("select products_price from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'");
          $products_array = tep_db_fetch_array($products_query);
          tep_db_free_result($products_query);  
          echo "<tr><form action='$PHP_SELF?oID=$oID&action=$action$param_str' method='POST'>\n";
          echo "<td class='dataTableContent'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 4: </td>";
          echo '<td class="dataTableContent"><table><tr><td width="150">' .
            ADDPRODUCT_TEXT_CONFIRM_QUANTITY . ':</td><td><input name="add_product_quantity" size="9" value="1" onkeyup="clearLibNum(this);" style="text-align:right;">&nbsp;'.EDIT_ORDERS_NUM_UNIT.'&nbsp;&nbsp;&nbsp;&nbsp;';
          echo '<input type="hidden" style="text-align:right;" class="once_pwd" onkeyup="clearNoNum_1(this);" value="'.  (int)$products_array['products_price'] .'" size="9" name="add_product_price"></td></tr></table></td>';
          echo "<td class='dataTableContent' align='right' colspan='2'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

          foreach ($_POST as $op_key => $op_value) {
            $op_pos = substr($op_key, 0, 3);
            if ($op_pos == 'op_') {
              echo "<input type='hidden' name='".$op_key."' value='".tep_parse_input_field_data(stripslashes($op_value), array("'" => "&quot;"))."'>"; 
            }
          }
          echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
          echo "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
          echo "<input type='hidden' name='step' value='5'>";
          echo "</td>\n";
          echo "</form></tr>\n";
        }

        echo "</table></td></tr>\n";
?> 
</table>
</td>
</tr>
<tr>
<td class="formAreaTitle"><br>
  
  <?php
  echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><table border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="main" valign="top" nowrap="nowrap" style="padding-left:6px;"><?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
<td class="main">&nbsp;
<textarea id='fax' name='fax_1' style='width:400px;height:42px;*height:40px;'><?php echo $fax;?></textarea>
</td>
</tr>
</table></td>
</tr>
</table>
</td>
</tr>
</table>
