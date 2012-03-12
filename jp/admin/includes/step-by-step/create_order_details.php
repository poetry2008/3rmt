<?php
/*
  $Id$
*/
?>
<script type="text/javascript">
  //todo:修改通性用
  function hidden_payment(){
  var idx = document.create_order.elements["payment_method"].selectedIndex;
  var CI = document.create_order.elements["payment_method"].options[idx].value;
  $(".rowHide").hide();
  $(".rowHide").find("input").attr("disabled","true");
  $(".rowHide_"+CI).show();
  $(".rowHide_"+CI).find("input").removeAttr("disabled");
 }
   $(document).ready(function(){hidden_payment()});

//住所

function check(value){
  var arr  = new Array();
  var arr_set = new Array();
<?php
  $options_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='option' and status='0' order by sort");
  $json_array = array();
  $json_set_value = array();
  while($options_array = tep_db_fetch_array($options_query)){
    if(!isset($otpions_array_temp['select_value']) && $otpions_array_temp['select_value'] == ''){
        $show_array[] = unserialize($options_array['type_comment']);
    }
  }

  foreach($show_array as $show_value){
    foreach($show_value as $show_key=>$show_val){

      $json_array[$show_key] = $show_val;
      $json_set_value[$show_key] = $show_val['select_value'];
    } 
  }

  tep_db_free_result($options_query);
  foreach($json_array as $key=>$value_temp){
    echo 'arr["'. $key .'"] = new Array();';
    echo 'arr_set["'. $key .'"] = new Array();';
    $value_temp['option_list'] = array_values($value_temp['option_list']);
    foreach($value_temp['option_list'] as $k=>$val){

      echo 'arr["'. $key .'"]['. $k .'] = "'. $val .'";';
    } 
    echo 'arr_set["'. $key .'"] = "'. $json_set_value[$key] .'";';

  }  
?>
  
  var option_id = document.getElementById("list_option5");
  option_id.options.length = 0;
  len = arr[value].length;
  option_id.options[option_id.options.length]=new Option('--',''); 
  for(i = 0;i < len;i++){
    if(arr_set[value] == arr[value][i]){

      option_id.options[option_id.options.length]=new Option(arr[value][i], arr[value][i]);
    }     
  } 
  for(i = 0;i < len;i++){
    if(arr_set[value] == arr[value][i]){
      continue; 
    }
    option_id.options[option_id.options.length]=new Option(arr[value][i], arr[value][i]);    
  } 
}

function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array();
    arr_color = new Array();
    $("#address_show_id").hide();
    
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
if(isset($customerId) && $customerId != ''){
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $customerId ." group by orders_id");
  
   
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
if(isset($customerId) && $customerId != ''){

  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $customerId ." group by orders_id");
  
   
  $address_num = 0;
  $json_str_list = '';
  $json_str_array = array();
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $address_orders_group_array['orders_id'] ."'");
  
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
    $json_str_list = '';
 
  tep_db_free_result($address_orders_query); 
  }
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

function check_clear(){
  var arr_clear = new Array();
<?php

  $address_clear_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
  while($address_clear_array = tep_db_fetch_array($address_clear_query)){

    echo 'arr_clear["'. $address_clear_array['name_flag'] .'"] = "'. $address_clear_array['comment'] .'";';
  }
  tep_db_free_result($address_clear_query);
?>
  for(x in arr_clear){
    document.getElementById("op_"+x).value = arr_clear[x];
    document.getElementById("op_"+x).style.color = '#999';
  }
}

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
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
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
  <td class="main">&nbsp;<?php echo tep_draw_hidden_field('customers_id', $customer_id) . $customer_id;?></td>
  </tr>
  <tr>
  <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME;?></td>
  <td class="main">
     &nbsp;
     <?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . 
     ENTRY_LAST_NAME_TEXT;?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE;
     if (isset($entry_firstname_error) && $entry_firstname_error == true) { 
       echo '&nbsp;&nbsp;<font color="red">Error</font>'; 
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
echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
?>&nbsp;&nbsp;<?php
echo CREATE_ORDER_NOTICE_ONE;?>
  
<?php
if (isset($entry_lastname_error) && $entry_lastname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; };
?></td>
</tr>
<tr>
<td class="main">&nbsp;<?php
echo ENTRY_EMAIL_ADDRESS;
?></td>
<td class="main">&nbsp;
<?php
echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>';
?>
  
<?php
if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; };
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
  <td class="main">&nbsp;
<?php
echo ENTRY_SITE;
?>:</td>
<td class="main">&nbsp;
<?php
echo isset($account) && $account?( '<font color="#FF0000"><b>'.tep_get_site_romaji_by_id($account['site_id']).'</b></font>'.tep_draw_hidden_field('site_id', $account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT);
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
// オプションのリスト作成
$torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
$torihiki_list[] = array('id' => '', 'text' => '選択してください');
for($i=0; $i<sizeof($torihiki_array); $i++) {
  $torihiki_list[] = array('id' => $torihiki_array[$i],
                           'text' => $torihiki_array[$i]);
}
// 取引日のリスト作成
$today = getdate();
$m_num = $today['mon'];
$d_num = $today['mday'];
$year = $today['year'];
$date_list[] = array('id' => '', 'text' => '取引日を選択してください');
for($i=0; $i<14; $i++) {
  $date_list[] = array('id' => date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)),
                       'text' => strftime("%Y年%m月%d日（%a）", mktime(0,0,0,$m_num,$d_num+$i,$year)));
}
// 取引時間のリスト作成
$hour_list[] = array('id' => '', 'text' => '--');
for($i=0; $i<24; $i++) {
  $hour_num = str_pad($i, 2, "0", STR_PAD_LEFT);
  $hour_list[] = array('id' => $hour_num,
                       'text' => $hour_num);
}
  
$min_list[] = array('id' => '', 'text' => '--');
for($i=0; $i<6; $i++) {
  $min_num = str_pad($i, 2, "0", STR_PAD_RIGHT);
  $min_list[] = array('id' => $min_num,
                      'text' => $min_num);
}
for($i=0; $i<sizeof($payment_array[0]); $i++) {
  $payment_list[] = array('id' => $payment_array[0][$i],
                          'text' => $payment_array[1][$i]);
}


?>
<tr>
<td class="formAreaTitle"><br>
  <?php
  echo CREATE_ORDER_PAYMENT_TITLE;?></td>
  </tr>
  <tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><table border="0" cellspacing="0" cellpadding="2">
  <tr>
  <td class="main" valign="top">&nbsp;
<?php
echo CREATE_ORDER_PAYMENT_TITLE;?>:</td>
<td class="main">&nbsp;

<?php
 
//diff order and order2
if(isset($from_page)&&$from_page == 'create_order_process2'){
  echo $payment_method;
  echo tep_draw_hidden_field('payment_method',$payment_method);
}else{ 
  echo tep_draw_pull_down_menu('payment_method', $payment_list, isset($payment_method)?$payment_method:'', 'onchange="hidden_payment()"'); 
}

if (isset($entry_payment_method_error ) && $entry_payment_method_error == true) { 
  echo '&nbsp;&nbsp;<font color="red">Error</font>'; 
} 
?>
<?php


if(isset($from_page)&&$from_page == 'create_order_process2'){

foreach ($selections as $se){
if ($se['id'] == payment::changeRomaji($_POST['payment_method'],PAYMENT_RETURN_TYPE_CODE)) {
foreach($se['fields'] as $field ){
    echo '<tr>';
    echo '<td class="main">';
    echo "&nbsp;".$field['title']."</td>";
    echo "<td class='main'>";
    echo "&nbsp;&nbsp;".$field['field'];
    echo "</td>";
    echo "</tr>";
  }
}
}
} else {
foreach ($selections as $se){
?>
<?php
  foreach($se['fields'] as $field ){
    echo '<tr class="rowHide rowHide_'.$se['id'].'">';
    echo '<td class="main">';
    echo "&nbsp;".$field['title']."</td>";
    echo "<td class='main'>";
    echo "&nbsp;&nbsp;".$field['field'];
    echo "<font color='#red'>".$field['message']."</font>";
    echo "</td>";
    echo "</tr>";
  }?>
<?php
}
}
?>

</td>
</tr>


</table></td>
</tr>
</table>
</td>
</tr>

<!-- 作所 -->

<tr>
    <td class="formAreaTitle"><br>
                                                                                                       
    <?php
echo TEXT_ADDRESS;
echo '&nbsp;&nbsp;<input type="button" value="'. TEXT_CLEAR .'" onclick="check_clear();">';
  ?></td>
  </tr>
      <tr>
      <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
      <td class="main"><table border="0" cellspacing="0" cellpadding="2">

<tr>
                            <td>&nbsp;</td>
                            <td colspan="2">
                            <input type="radio" name="address_option" value="new" onclick="address_option_show('new');" checked><?php echo TABLE_OPTION_NEW; ?>
                            <input type="radio" name="address_option" value="old" onclick="address_option_show('old');"><?php echo TABLE_OPTION_OLD; ?>
                            </td>
</tr>
<tr id="address_show_id" style="display:none;"><td>&nbsp;</td>
<td class="main"><?php echo TABLE_ADDRESS_SHOW; ?></td>
<td class="main" height="30">
<select name="address_show_list" id="address_show_list" onchange="address_option_list(this.value);">
<option value="">--</option>
</select>
</td></tr>

<?php
$hm_option->render('');    
?>
      </table></td>
      </tr>
      </table></td>
      </tr>
 
<tr>
<td class="formAreaTitle"><br><?php echo CREATE_ORDER_FETCH_TIME_TITLE_TEXT;?></td>
</tr>
<tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2">
            <tr>
      <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_DATE_TEXT;?></td>
              <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('date', $date_list, isset($date)?$date:''); ?><?php if (isset($entry_date_error) && $entry_date_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
            </tr>
            <tr>
              <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_TIME_TEXT;?></td>
              <td class="main">&nbsp;
              <?php 
              echo tep_draw_pull_down_menu('hour', $hour_list, isset($hour)?$hour:''); 
              ?>&nbsp;時&nbsp;<?php 
              echo tep_draw_pull_down_menu('min', $min_list, isset($min)?$min:''); 
              ?>&nbsp;分&nbsp;<b><?php echo CREATE_ORDER_FETCH_ALLTIME_TEXT;?></b><?php 
              if (isset($entry_tardetime_error ) && $entry_tardetime_error == true) { 
                echo '&nbsp;&nbsp;<font color="red">Error</font>'; 
              } ?></td>
            </tr>
            <tr>
              <td class="main">&nbsp;<?php echo CREATE_ORDER_FETCH_TIME_SELECT_TEXT;?></td>
              <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('torihikihouhou', tep_get_all_torihiki(), isset($torihikihouhou)?$torihikihouhou:''); ?><?php if (isset($entry_torihikihouhou_error) && $entry_torihikihouhou_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
            </tr>
          </table></td>
      </tr>
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

  <!--

  <tr>
  <td class="main">&nbsp;
<?php
echo ENTRY_TELEPHONE_NUMBER;
?></td>
<td class="main">&nbsp;
<?php
echo tep_draw_input_field('telephone', $telephone) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;?></td>
</tr>

-->

<tr>
<td class="main">&nbsp;
<?php
echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
<td class="main">&nbsp;
<?php
echo tep_draw_input_field('fax', $fax, 'size="60" maxlength="255"');
?>&nbsp;&nbsp;
<?php
echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
</tr>
<tr>
<td class="main" colspan="2">&nbsp;
<?php
echo CREATE_ORDER_COMMUNITY_SEARCH_READ_ONE;?></td>
</tr>
<tr>
<td class="main" colspan="2">&nbsp;<b>

<?php
echo CREATE_ORDER_COMMUNITY_SEARCH_READ_TWO;?></b></td>
</tr>
</table></td>
</tr>
</table>
</td>
</tr>

<!--

<tr>
<td class="formAreaTitle"><br>
                                                                                 
  <?php
  echo CATEGORY_ORDER_DETAILS;
?></td>
</tr>
<tr>
<td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><table border="0" cellspacing="0" cellpadding="2">
  <tr>
  <td class="main">&nbsp;
<?php
echo ENTRY_CURRENCY;
?></td>
<td class="main">
                                                                                 
<?php
  echo $SelectCurrencyBox;
?></td>
</tr>
</table></td>
</tr>
</table></td>
</tr>

-->

</table>
