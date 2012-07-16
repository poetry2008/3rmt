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
  //计算商品的总价格及总重量
  $shi_preorders_query = tep_db_query("select * from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."'");
  $shi_preorders_array = tep_db_fetch_array($shi_preorders_query);
  $shi_pid = $shi_preorders_array['orders_id'];
  tep_db_free_result($shi_preorders_query);

  $customers_guest_query = tep_db_query("select customers_guest_chk from ". TABLE_CUSTOMERS ." where customers_id='".$shi_preorders_array['customers_id']."'");
  $customers_guest_array = tep_db_fetch_array($customers_guest_query);
  tep_db_free_result($customers_guest_query);
  $customers_guest_flag = $customers_guest_array['customers_guest_chk'];

  $weight_count = 0; 
  $shi_products_query = tep_db_query("select * from ". TABLE_PREORDERS_PRODUCTS ." where orders_id='". $shi_pid ."'");
  while($shi_products_array = tep_db_fetch_array($shi_products_query)){

    $shi_products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $shi_products_array['products_id'] ."'");
    $shi_products_weight_array = tep_db_fetch_array($shi_products_weight_query);
    tep_db_free_result($shi_products_weight_query);
    $weight_count += $shi_products_weight_array['products_weight']*$shi_products_array['products_quantity'];
  }
  tep_db_free_result($shi_products_query);
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/option.js"></script>
<script type="text/javascript">

<?php
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){

    case '1':
      echo 'var country_fee_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_fee_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_fee_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
    case '2':
      echo 'var country_area_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_area_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_area_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
      break;
    case '3':
      echo 'var country_city_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
      break;
    }
  }
?>

function check(select_value){

  var arr = new Array();
<?php 
    $country_fee_query = tep_db_query("select id,name from ". TABLE_COUNTRY_FEE ." where status='0' order by id");
    while($country_fee_array = tep_db_fetch_array($country_fee_query)){

      echo 'arr["'.$country_fee_array['name'].'"] = "'. $country_fee_array['name'] .'";'."\n";
    }
    tep_db_free_result($country_fee_query);
  ?>
    var country_fee = document.getElementById(country_fee_id);
    country_fee.options.length = 0;
    var i = 0;
    for(x in arr){

      country_fee.options[country_fee.options.length]=new Option(arr[x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

      $("#td_"+country_fee_id_one).hide();
    }else{
       
      $("#td_"+country_fee_id_one).show();
    }
}
function country_check(value,select_value){
   
   var arr = new Array();
<?php 
    $country_array = array();
    $country_area_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_AREA ." where status='0' order by sort");
    while($country_area_array = tep_db_fetch_array($country_area_query)){
      
      $country_fee_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_FEE ." where id='".$country_area_array['fid']."'"); 
      $country_fee_fid_array = tep_db_fetch_array($country_fee_fid_query);
      tep_db_free_result($country_fee_fid_query);
      $country_array[$country_fee_fid_array['name']][$country_area_array['name']] = $country_area_array['name'];
      
    }
    tep_db_free_result($country_area_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>
    var country_area = document.getElementById(country_area_id);
    country_area.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_area.options[country_area.options.length]=new Option(arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

      $("#td_"+country_area_id_one).hide();
    }else{
       
      $("#td_"+country_area_id_one).show();
    }

}

function country_area_check(value,select_value){
   
   var arr = new Array();
  <?php
    $country_array = array();
    $country_city_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_CITY ." where status='0' order by sort");
    while($country_city_array = tep_db_fetch_array($country_city_query)){
      
      $country_area_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_AREA ." where id='".$country_city_array['fid']."'"); 
      $country_area_fid_array = tep_db_fetch_array($country_area_fid_query);
      tep_db_free_result($country_area_fid_query); 
      $country_array[$country_area_fid_array['name']][$country_city_array['name']] = $country_city_array['name'];
      
    }
    tep_db_free_result($country_city_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>
    var country_city = document.getElementById(country_city_id);
    country_city.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_city.options[country_city.options.length]=new Option(arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

      $("#td_"+country_city_id_one).hide();
    }else{
       
      $("#td_"+country_city_id_one).show();
    }

}

function session_value(){

  var session_array = new Array();
  <?php
    foreach($_SESSION['preorder_information'] as $p_key=>$p_value){

      if(substr($p_key,0,3) == 'ad_'){

        echo 'session_array["'. $p_key .'"] = "'. $p_value .'";'."\n";
      }
    }
  ?>
  
    for(x in session_array){
      if(document.getElementById(x)){
        var pre_id = document.getElementById(x); 
        pre_id.style.color = '#000';
        $("#"+x).val(session_array[x]);
      }
    }

}
  function in_array(value,arr){

    for(vx in arr){
      if(value == arr[vx]){

        return true;
      }
    }
    return false;
  }
var first_num = 0;
function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array(); arr_color = new Array(); $("#address_show_id").hide();
    
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
    if(document.getElementById("ad_"+x)){ 
      var list_options = document.getElementById("ad_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
      $("#error_"+x).html('');
      if(document.getElementById("l_"+x)){
        if($("#l_"+x).val() == 'true'){
          $("#r_"+x).html('&nbsp;*<?php echo TEXT_REQUIRED;?>');
        }
      }
    }
  }
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
    break;
  case 'old' :
    $("#address_show_id").show();
    var arr_old  = new Array();
    var arr_name = new Array();
<?php
if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != ''){
  
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_i = 0;
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
    echo 'arr_name['. $address_i .'] = "'. $address_list_array['name_flag'] .'";';
    $address_i++;
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();

  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id asc");

   
  $json_str_list = '';
  unset($json_old_array);
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[$address_num] = $json_str_list; 
      echo 'arr_old['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        $value = str_replace("\n","",$value);
        $value = str_replace("\r","",$value);
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
  j_num = 0;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
        <?php 
        if(!isset($_POST['address_option']) || $_POST['address_option'] == 'new'){
        ?>
        if(document.getElementById("l_"+x)){
        if($("#l_"+x).val() == 'true'){
          $("#r_"+x).html('&nbsp;*<?php echo TEXT_REQUIRED;?>');
        }
        }
        <?php
        }
        ?>
        //$("#error_"+x).html('');
    }
    if(arr_str != ''){
      ++j_num;
      if(j_num == 1){first_num = i;}
        <?php
  if(isset($_POST['address_show_list']) && $_POST['address_show_list'] != ''){

    echo 'var address_show_list_one = "'. $_POST['address_show_list'] .'";'."\n"; 
  }elseif(isset($_SESSION['preorder_information']['address_show_list']) && $_SESSION['preorder_information']['address_show_list'] != ''){
    echo 'var address_show_list_one = "'. $_SESSION['preorder_information']['address_show_list'] .'";'."\n";
  }else{
    echo 'var address_show_list_one = first_num;'."\n"; 
  }
        ?>
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,i==address_show_list_one,i==address_show_list_one);
    }

  } 
    //address_option_list(first_num); 
    break;
  }
}

function address_option_list(value){
  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();
  var arr_list = new Array();
<?php
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
  }
  tep_db_free_result($address_list_query);  
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $shi_preorders_array['customers_id']." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id");

  $json_str_list = '';
  unset($json_old_array); 
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[] = $json_str_list; 
      echo 'arr_list['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        $value = str_replace("\n","",$value);
        $value = str_replace("\r","",$value);
        echo 'arr_list['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
    }
 
  tep_db_free_result($address_orders_query); 
  }
?>
  ii = 0;
  for(x in arr_list[value]){
   if(document.getElementById("ad_"+x)){
     var list_option = document.getElementById("ad_"+x);
     if('<?php echo $country_fee_id;?>' == 'ad_'+x){
      check(arr_list[value][x]);
    }else if('<?php echo $country_area_id;?>' == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[value][x]);
     
    }else if('<?php echo $country_city_id;?>' == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = arr_list[value][x];   
    }
     
    $("#error_"+x).html('');
    ii++; 
   }
  }

    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');

}

<?php
if (!isset($_POST['address_option']) && $customers_guest_flag == 0) {
?>
  $(document).ready(function(){
    
    address_option_show('old'); 
    address_option_list(first_num);
  });
<?php
}elseif((isset($_POST['address_option']) && ($_POST['address_option'] == 'old'))){
?>
  $(document).ready(function(){
    
    address_option_show('old'); 
  });
<?php
}
?>
$(document).ready(function(){
  $("#"+country_fee_id).change(function(){
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
  }); 
  $("#"+country_area_id).change(function(){
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
  });
<?php
    $address_histroy_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $shi_preorders_array['customers_id'] ."'"); 
    $address_histroy_num = tep_db_num_rows($address_histroy_query);
    tep_db_free_result($address_histroy_query);
    if(isset($_POST[$country_fee_id])){
  ?>  
    check("<?php echo isset($_POST[$country_fee_id]) ? $_POST[$country_fee_id] : '';?>");
  <?php
   }elseif(!isset($_SESSION['preorder_information'])){
?>
  <?php
    if($address_histroy_num > 0 && $customers_guest_flag == 0){
  ?>
    check();
    address_option_list(first_num);
  <?php
    }else{
  ?>
   check();
  <?php
   }
  ?>
  <?php
  }else{
  ?>
    check("<?php echo $_SESSION['preorder_information'][$country_fee_id];?>");
    session_value();
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_area_id])){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_POST[$country_area_id];?>");
  <?php
   }elseif(!isset($_SESSION['preorder_information'])){
?>
  <?php
    if($address_histroy_num > 0 && $customers_guest_flag == 0){
  ?>
    country_check($("#"+country_fee_id).val());
    address_option_list(first_num);
  <?php
    }else{
  ?>
    country_check($("#"+country_fee_id).val());
  <?php
   }
  ?>
  <?php
  }else{
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_SESSION['preorder_information'][$country_area_id];?>");
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_city_id])){
  ?>
     
     country_area_check($("#"+country_area_id).val(),"<?php echo $_POST[$country_city_id];?>");
  <?php
   }elseif(!isset($_SESSION['preorder_information'])){
?>
  <?php
    if($address_histroy_num > 0 && $customers_guest_flag == 0){
  ?>
    country_area_check($("#"+country_area_id).val());
    address_option_list(first_num);
  <?php
    }else{
  ?>
    country_area_check($("#"+country_area_id).val());
  <?php
   }
  ?>
  <?php
  }else{
  ?>
    country_area_check($("#"+country_area_id).val(),"<?php echo $_SESSION['preorder_information'][$country_city_id]?>");
  <?php
  }
  ?>
});
</script>
<script type="text/javascript" src="js/predate_time.js"></script>
</head>
<body>
<?php 
if ($error == false && $_POST['action'] == 'process') { 
//echo tep_draw_form('order1', tep_href_link('change_preorder_confirm.php'));
$preorder_information = array();
foreach ($_POST as $post_key => $post_value) {
  if (is_array($post_value)) {
    foreach ($post_value as $ps_key => $ps_value) {
      //echo '<input type="hidden" name="'.$post_key.'['.$ps_key.']" value="'.$ps_value.'">'; 
      //$preorder_info_attr[] = $ps_value;
      $preorder_information[$post_key][$ps_key] = $ps_value; 
    }
  } else {
    //echo '<input type="hidden" name="'.$post_key.'" value="'.stripslashes($post_value).'">'; 
      $preorder_information[$post_key] = stripslashes($post_value); 
  }
}

$preorder_information['pid'] = $preorder_id; 
if (!tep_session_is_registered('preorder_information')) {
   tep_session_register('preorder_information');
}
tep_redirect(tep_href_link('change_preorder_confirm.php'));
//echo '<input type="hidden" name="pid" value="'.$preorder_id.'">'; 
//echo '</form>';
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
          <p class="formAreaTitle" style="font-size:12px;"><?php echo CHANGE_ORDER_CUSTOMER_DETAILS?></p> 
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
          <p class="formAreaTitle" style="font-size:12px;"><?php echo CHANGE_ORDER_PRODUCT_DETAILS;?></p> 
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
            $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$preorder_id."'"); 
            while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
              $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info'])); 
            ?>
            <tr>
              <td class="main"><?php echo $old_attr_info['title'];?>:</td> 
              <td class="main">
              <?php 
              echo str_replace(array("<br>", "<BR>"), '', $old_attr_info['value']);
              //if ($old_attr_res['options_values_price'] != '0') {
                //echo ' ('.$currencies->format($old_attr_res['options_values_price'] * $preorder_product_res['products_quantity']).')'; 
              //}
              ?>
              </td> 
            </tr>
            <?php
            }
            ?>
            <?php
              $product_info_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$preorder_product_res['products_id']."'"); 
              $product_info_res = tep_db_fetch_array($product_info_raw); 
            ?>
            <tr>
              <td class="main">
              <?php echo CHANGE_ORDER_PRODUCT_NUM;?> 
              </td>
              <td class="main">
              <?php echo $preorder_product_res['products_quantity'].PRODUCT_UNIT_TEXT;?> 
              <?php echo tep_get_full_count2($preorder_product_res['products_quantity'], $preorder_product_res['products_id']);?> 
              </td>
            </tr>
        </table> 
        <br>
        <?php
        //计算商品的总价格及总重量
        $shipping_preorders_query = tep_db_query("select * from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."'");
        $shipping_preorders_array = tep_db_fetch_array($shipping_preorders_query);
        $shipping_pid = $shipping_preorders_array['orders_id'];
        tep_db_free_result($shipping_preorders_query);
        $weight_total = 0; 
        $cart_products_id = array();
        $shipping_products_query = tep_db_query("select * from ". TABLE_PREORDERS_PRODUCTS ." where orders_id='". $shipping_pid ."'");
        while($shipping_products_array = tep_db_fetch_array($shipping_products_query)){

          $shipping_products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $shipping_products_array['products_id'] ."'");
          $shipping_products_weight_array = tep_db_fetch_array($shipping_products_weight_query);
          tep_db_free_result($shipping_products_weight_query);
          $weight_total += $shipping_products_weight_array['products_weight']*$shipping_products_array['products_quantity'];
          $cart_products_id[] = $shipping_products_array['products_id'];
        }
        tep_db_free_result($shipping_products_query);


  //根据预约中的商品来生成取引时间
  //根据$cart_products_id数组中的商品ID来获取每个商品的取引时间
  $cart_shipping_time = array();
  foreach($cart_products_id as $cart_products_value){
    
    $shipping_time_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id=".(int)$cart_products_value);
    $shipping_time_array = tep_db_fetch_array($shipping_time_query);
    tep_db_free_result($shipping_time_query);
    $cart_shipping_time[] = $shipping_time_array['products_shipping_time'];
  }
   
  $cart_shipping_time = array_unique($cart_shipping_time); 
  
  $products_num = count($cart_shipping_time); 
  $shipping_time_array = array();
  foreach($cart_shipping_time as $cart_shipping_value){

    $shipping_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=".$cart_shipping_value);
    $shipping_array = tep_db_fetch_array($shipping_query);
    $shipping_time_array['work'][] = unserialize($shipping_array['work']);
    $shipping_time_array['db_set_day'][] = $shipping_array['db_set_day'];
    $shipping_time_array['shipping_time'][] = $shipping_array['shipping_time'];

  }
  
  //work
  $shipping_time_start = array();
  $shipping_time_end = array();
  foreach($shipping_time_array['work'] as $shipping_time_key=>$shipping_time_value){

    foreach($shipping_time_value as $k=>$val){

      $shipping_time_start[$shipping_time_key][] = $val[0]; 
      $shipping_time_end[$shipping_time_key][] = $val[1];
    } 
  }
   
  
  $ship_array = array();
  $ship_time_array = array();
  $j = 0;
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$i]) && $ship_time_array[$i] != ''){
          if($ship_temp_array[$i] != $j){$ship_array[$i]++;}
          $ship_time_array[$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
          $ship_temp_array[$i] = $j;
        }
      } 
    }
    
    $j++;  
  }

  $s_array = array();
  foreach($ship_array as $ship_k=>$ship_v){
    if($ship_v >= $products_num-1){
      $s_array[$ship_k] = $ship_v;
    } 
  } 
  $ship_array = $s_array;
  $shipp_array = array_keys($ship_array);
  sort($shipp_array);
  $ship_new_array = array();
  foreach($shipp_array as $shipp_key=>$shipp_value){
  
    $ship_1_array = explode('|',$ship_time_array[$shipp_value]);
    foreach($ship_1_array as $ship_1_value){

      $ship_2_array = explode(',',$ship_1_value);
      $ship_3_array[$shipp_key][] = $ship_2_array[0];
      $ship_4_array[$shipp_key][] = $ship_2_array[1];
    } 
  }

  foreach($ship_3_array as $ship_3_key=>$ship_3_value){

    natsort($ship_3_array[$ship_3_key]); 
    natsort($ship_4_array[$ship_3_key]);
    $ship_new_array[] = end($ship_3_array[$ship_3_key]).','.current($ship_4_array[$ship_3_key]);
  }

  foreach($ship_new_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);
      sort($s_temp_array);
      $ship_new_array[$_s_key] = implode('|',$s_temp_array);
  } 
  $max_time_str = implode('||',$shipp_array);
  $min_time_str = implode('||',$ship_new_array);
  //----------
  if(count($shipping_time_array['work']) == 1){
    
    $shi_time_array = array();
    foreach($shipping_time_start[0] as $shi_key=>$shi_value){

      $shi_start_array = explode(':',$shi_value);
      $shi_end_array = explode(':',$shipping_time_end[0][$shi_key]);

      for($shi_i = (int)$shi_start_array[0];$shi_i <= (int)$shi_end_array[0];$shi_i++){

        if(isset($shi_time_array[$shi_i]) && $shi_time_array[$shi_i] != ''){

          
          $shi_time_array[$shi_i] .= '|'.$shi_value.','.$shipping_time_end[0][$shi_key]; 
        }else{

          $shi_time_array[$shi_i] = $shi_value.','.$shipping_time_end[0][$shi_key]; 
        }
      }
    }

    foreach($shi_time_array as $_s_key=>$_s_value){
        $s_temp_array = explode('|',$_s_value);
        sort($s_temp_array);
        $shi_time_array[$_s_key] = implode('|',$s_temp_array);
    }
    $max_time_str = implode('||',array_keys($shi_time_array));
    $min_time_str = implode('||',$shi_time_array);
  }
     
  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;
  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

        if($weight_total > 0){
          $checked_str_old = '';
          $checked_str_new = '';
          $show_flag = '';
          if ((isset($_POST['address_option']) && ($_POST['address_option'] == 'old')) || (!isset($_POST['address_option']) && !isset($_SESSION['preorder_information']['address_option']))){

            $checked_str_old = 'checked';
            $show_flag = 'block';
          }elseif(isset($_SESSION['preorder_information']['address_option']) && $_SESSION['preorder_information']['address_option'] == 'old'){
            $checked_str_old = 'checked';
            $show_flag = 'block';           
          }elseif(isset($_SESSION['preorder_information']['address_option']) && $_SESSION['preorder_information']['address_option'] == 'new'){
            $checked_str_new = 'checked';
            $show_flag = 'none';
          }else{

            $checked_str_new = 'checked';
            $show_flag = 'none';
          }

          //判断用户是否是会员
          $quest_query = tep_db_query("select customers_guest_chk from ". TABLE_CUSTOMERS ." where customers_id={$shi_preorders_array['customers_id']}");
          $quest_array = tep_db_fetch_array($quest_query);
          tep_db_free_result($quest_query);
        ?>
        <p class="formAreaTitle" style="font-size:12px;"><?php echo TEXT_ADDRESS;?></p>
        <table border="0" width="100%" cellspacing="2" cellpadding="2" class="formArea"> 
        <?php
          if($quest_array['customers_guest_chk'] == 0){
            $address_history_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $shi_preorders_array['customers_id'] ."'");
            $address_history_num = tep_db_num_rows($address_history_query);
            tep_db_free_result($address_history_query);
            if($address_history_num == 0 && !isset($_POST['address_option']) && !isset($_SESSION['preorder_information'])){
              $checked_str_old = '';
              $checked_str_new = 'checked';
        ?>
         <script type="text/javascript">
         $(document).ready(function(){
  
           address_option_show('new'); 
           
         }); 
        </script>

        <?php
            }
        ?>
        <script type="text/javascript">
         $(document).ready(function(){
  
           <?php
              if($show_flag == 'none'){
          ?>   
               $("#address_show_id").hide();
          <?php
              }
           ?>
         }); 
        </script>
            <tr>
            <td colspan="2" class="main">
              <input type="radio" name="address_option" value="old" onClick="address_option_show('old');address_option_list(first_num);" <?php echo $checked_str_old;?>><?php echo TABLE_OPTION_OLD; ?> 
              <input type="radio" name="address_option" value="new" onClick="address_option_show('new');" <?php echo $checked_str_new;?>><?php echo TABLE_OPTION_NEW; ?>
            </td>
            </tr>
            <tr id="address_show_id">
            <td class="main"><?php echo TABLE_ADDRESS_SHOW; ?></td>
            <td class="main">
            <select name="address_show_list" id="address_show_list" onChange="address_option_list(this.value);">
            <option value="">--</option>
            </select>
            </td></tr>

        <?php
         } 
          $ad_option->render('');  
        ?>
        </table>
        <br>
        <?php 
        }
        ?>
        
        <p class="formAreaTitle" style="font-size:12px;"><?php echo CHANGE_ORDER_FETCH_TIME_TITLE;?></p> 
        <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
        <tr>
          <td class="main" width="150">
          <?php echo CHANGE_ORDER_FETCH_DAY;?> 
          </td>
          <td class="main">
            <?php
    $today = getdate();
      $m_num = $today['mon'];
      $d_num = $today['mday']+$db_set_day;;
      $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="date" onChange="selectDate('<?php echo $work_start; ?>', '<?php echo $work_end; ?>',this.value);$('#date_error').remove();">
    <option value=""><?php echo PREORDER_SELECT_EMPTY_OPTION;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(TEXT_DATE_MONDAY, TEXT_DATE_TUESDAY, TEXT_DATE_WEDNESDAY, TEXT_DATE_THURSDAY, TEXT_DATE_FRIDAY, TEXT_DATE_STATURDAY, TEXT_DATE_SUNDAY); 
    for($j = 0;$j < $shipping_time;$j++){

      $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_POST['date'] ? 'selected' : ''; 
      if(!isset($_POST['date'])){
        $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_SESSION['preorder_information']['date'] ? 'selected' : ''; 
      }
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'" '. $selected_str .'>'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";

    }
    ?>
  </select>
              <?php
              if (isset($date_error)) {
                echo '<br><font id="date_error" color="#ff0000">'.$date_error.'</font>'; 
              }
              ?> 
              </td>
            </tr>
            <tr id="shipping_list" style="display:none;">
              <td class="main"><?php echo CHANGE_ORDER_FETCH_DATE;?></td> 
              <td class="main" id="shipping_list_show">
</td>
</tr>
<tr><td class="main">&nbsp;</td><td class="main">
             <?php  
             if (isset($jikan_error)) {
                echo '<font id="jikan_error" color="#ff0000">'.$jikan_error.'</font>'; 
             } 
             ?> 
</td></tr>
          </table>  
<table border="0" cellpadding="2" cellspacing="2" style=" position:absolute; width:500px;">
            <tr id="shipping_list_min" style="display:none;">
              <td class="main" width="150">&nbsp;<input type="hidden" id="ele_id" name="ele" value=""></td> 
              <td class="main" id="shipping_list_show_min"> 
              </td> 
            </tr>
</table>
          <?php
          if(isset($_POST['date']) && $_POST['date'] != ''){

                echo '<script>selectDate(\''. $work_start .' \', \''. $work_end .'\');$("#shipping_list").show();</script>';
             }else{

                if(isset($_SESSION['preorder_information']['date']) && $_SESSION['preorder_information']['date'] != ''){

                  echo '<script>selectDate(\''. $work_start .' \', \''. $work_end .'\');$("#shipping_list").show();</script>';
                }
             }
             if(isset($_POST['min']) && $_POST['min'] != ''){

                echo '<script>selectHour(\''. $work_start .' \', \''. $work_end .'\',\''. $_POST['hour'] .'\','. $_POST['min'] .','. $_POST['ele'] .');$("#shipping_list_min").show();</script>';
             }else{

                if(isset($_SESSION['preorder_information']['min']) && $_SESSION['preorder_information']['min'] != ''){

                  echo '<script>selectHour(\''. $work_start .' \', \''. $work_end .'\',\''. $_SESSION['preorder_information']['hour'] .'\','. $_SESSION['preorder_information']['min'] .','. $_SESSION['preorder_information']['ele'] .');$("#shipping_list_min").show();</script>';
                }
             }
          if ($hm_option->preorder_whether_show($product_info_res['belong_to_option'])) { 
          ?>
          <br>
          <table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
          <tr>
            <td style="padding:0;">
            <?php 
            $p_cflag = tep_get_cflag_by_product_id($preorder_product_res['products_id']);
            echo $hm_option->render($product_info_res['belong_to_option'], true, 1, '', '', (int)$p_cflag);
            ?> 
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
              <input type="text" name="preorder_point" size="24" value="<?php echo isset($_POST['preorder_campaign_info'])?$_POST['preorder_campaign_info']:(isset($_POST['preorder_point'])?$_POST['preorder_point']:(isset($_SESSION['preorder_information']['preorder_point'])?$_SESSION['preorder_information']['preorder_point']:'0'));?>" style="text-align:right;">&nbsp;&nbsp;<?php echo $preorder_point;?> 
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
              <input type="text" name="camp_preorder_point" size="24" value="<?php echo isset($_POST['preorder_campaign_info'])?$_POST['preorder_campaign_info']:(isset($_POST['camp_preorder_point'])?$_POST['camp_preorder_point']:(isset($_SESSION['preorder_information']['preorder_campaign_point'])?$_SESSION['preorder_information']['preorder_campaign_point']:'0'));?>" style="text-align:right;">
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
