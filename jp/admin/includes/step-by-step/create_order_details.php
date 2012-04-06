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

var first_num = 0;
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
      if($address_new_arr['set_value'] != ''){
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['set_value'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
      }else{
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_array['comment'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#999";';
      }
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
      $("#error_"+x).html('');
    }
    break;
  case 'old' :
    $("#address_show_id").show(); 
    var arr_old  = new Array();
<?php
if(isset($customerId) && $customerId != ''){
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $customerId ." group by orders_id order by orders_id desc");
  
   
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
  //address_show_list.options[address_show_list.options.length]=new Option('--',''); 
  j_num = 0;
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
      ++j_num;
      if(j_num == 1){first_num = i;}
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i);
    }

  }
    //address_option_list(first_num);  
    break;
  }
}

function address_option_list(value){
  var arr_list = new Array();
<?php
if(isset($customerId) && $customerId != ''){

  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id=". $customerId ." group by orders_id order by orders_id desc");
  
   
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
    $("#error_"+x).html(''); 
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
    $("#error_"+x).html('');
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
<?php 
$t_today = getdate();
$t_mon = $t_today['mon'];
$t_day = $t_today['mday'];
$t_year = $t_today['year'];
$t_hour = $t_today['hours'];
$t_min = $t_today['minutes'];
?>

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

function check_min(value){
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
   
    min_1.options.length = 0;
    for(mi_i = value;mi_i <= 59;mi_i++){
      min_1.options[min_1.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
}

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

<?php
if (!isset($_POST['address_option'])) {
?>
  $(document).ready(function(){
    
    address_option_show('old'); 
    address_option_list(first_num);
  });
<?php
}elseif(isset($_POST['address_option']) && $_POST['address_option'] == 'old'){
?>
  $(document).ready(function(){
    
    address_option_show('old'); 
  });
<?php
}
?>
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
$hour = $today['hours'];
$min = $today['minutes'];

$selected = '';
$year_str = '<select name="year" id="year" onchange="check_year(this.value);">';
for($y = $year;$y <= $year+19;$y++){
  $selected = $y == $year_num ? 'selected' : '';
  $year_str .= '<option value="'. $y .'" '. $selected .'>'. $y .'</option>';
}
$year_str .= '</select>';

$selected = '';
$m_num = isset($year_num) && $year_num != $year ? 1 : $m_num;
$mon_str = '<select name="mon" id="mon" onchange="check_mon(this.value);">';
for($m = $m_num;$m <= 12;$m++){
  $selected = $m == $mon_num ? 'selected' : '';
  $mon_str .= '<option value="'. $m .'" '. $selected .'>'. $m .'</option>';
}
$mon_str .= '</select>';

$selected = '';
$d_num = isset($year_num) && $year_num != $year ? 1 : $d_num;
$day_str = '<select name="day" id="day" onchange="check_day(this.value);">';
for($d = $d_num;$d <= 31;$d++){
  $selected = $d == $day_num ? 'selected' : '';
  $day_str .= '<option value="'. $d .'" '. $selected .'>'. $d .'</option>';
}
$day_str .= '</select>';

$selected = '';
$hour = isset($year_num) && $year_num != $year ? 1 : $hour;
$selected_1 = '';
$hour_str = '<select name="hour" id="hour" onchange="check_hour(this.value);">';
$hour_str_1 = '<select name="hour_1" id="hour_1" onchange="check_hour_1(this.value);">';
for($h = $hour;$h <= 24;$h++){
  $h_str = $h == 24 ? '00' : $h;
  $selected = $h == $hour_num ? 'selected' : '';
  $selected_1 = $h == $hour_1 ? 'selected' : '';
  $hour_str .= '<option value="'. $h_str .'" '. $selected .'>'. $h_str .'</option>';
  $hour_str_1 .= '<option value="'. $h_str .'" '. $selected_1 .'>'. $h_str .'</option>';
}
$hour_str .= '</select>';
$hour_str_1 .= '</select>';

$selected = '';
$selected_1 = '';
$min = isset($year_num) && $year_num != $year ? 1 : $min;
$min_str = '<select name="min" id="min" onchange="check_min(this.value);">';
$min_str_1 = '<select name="min_1" id="min_1">';
for($mi = $min;$mi <= 59;$mi++){
  
  $selected = $mi == $min_num ? 'selected' : '';
  $selected_1 = $mi == $min_1 ? 'selected' : ''; 
  $min_str .= '<option value="'. $mi .'" '. $selected .'>'. $mi .'</option>';
  $min_str_1 .= '<option value="'. $mi .'" '. $selected_1.'>'. $mi .'</option>';
}
$min_str .= '</select>';
$min_str_1 .= '</select>';


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
