<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
<?php
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){

    case '1':
      echo 'var country_fee_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_fee_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_fee_id = 'op_'.$address_fixed_array['name_flag'];
      break;
    case '2':
      echo 'var country_area_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_area_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_area_id = 'op_'.$address_fixed_array['name_flag'];
      break;
    case '3':
      echo 'var country_city_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'op_'.$address_fixed_array['name_flag'];
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

function check_form(){
  var lastname = document.getElementsByName("lastname");
  var first_name = document.getElementById("first_name");
  first_name.value = lastname[0].value;
  var firstname = document.getElementsByName("firstname");
  var end_name = document.getElementById("end_name");
  end_name.value = firstname[0].value;
  var email_address = document.getElementsByName("email_address");
  var email = document.getElementById("email");
  email.value = email_address[0].value;
  var newsletter = document.getElementsByName("newsletter");
  var options = document.getElementById("options");
  options.value = newsletter[1].value;
  var password = document.getElementsByName("password");
  var pwd = document.getElementById("pwd");
  pwd.value = password[1].value;
  var confirmation = document.getElementsByName("confirmation");
  var pwd_1 = document.getElementById("pwd_1");
  pwd_1.value = confirmation[1].value;
  var old_email_1 = document.getElementsByName("old_email_1");
  var old_email = document.getElementById("old_email");
  old_email.value = old_email_1[0].value;
  var action_flag = document.getElementById("action_flag");
  action_flag.value = 1;
  document.account_edit_address.submit();
}

function check_form_address(){ 
  var lastname = document.getElementsByName("lastname");
  var first_name = document.getElementById("first_name");
  first_name.value = lastname[0].value;
  var firstname = document.getElementsByName("firstname");
  var end_name = document.getElementById("end_name");
  end_name.value = firstname[0].value;
}

function address_clear(){
  var arr_new = Array();
  var arr_comment = Array();
<?php
  $address_new_i = 0;
  $address_new_query = tep_db_query("select name_flag,comment from ". TABLE_ADDRESS ." where type='textarea' and status='0'");
  while($address_new_array = tep_db_fetch_array($address_new_query)){

    echo 'arr_new['. $address_new_i .'] = "'. $address_new_array['name_flag'] .'";';
    echo 'arr_comment['. $address_new_i .'] = "'. $address_new_array['comment'] .'";';
    $address_new_i++;
  } 
  tep_db_free_result($address_new_query);
?>
  check();
  country_check($("#"+country_fee_id).val());
  country_area_check($("#"+country_area_id).val());
  for(x in arr_new){

    $("#op_"+arr_new[x]).val(arr_comment[x]);
    var op_comment = document.getElementById("op_"+arr_new[x]);
    op_comment.style.color = '#999';
    if(document.getElementById("l_"+arr_new[x])){
      if($("#l_"+arr_new[x]).val() == 'true'){
        $("#r_"+arr_new[x]).html("&nbsp;*必須");
      }
    }
    $("#error_"+arr_new[x]).html("");
  }
  $("#address_flag_id").val("");
}
//判断数组中是否含有某值
function in_array(value,arr){
  
  for(vx in arr){
    if(value == arr[vx]){
      return true;
    }
  }
  return false;
}

function address_list(){
  var arr_old = new Array();
  var arr_name = new Array();
<?php
//根据后台的设置来显示相应的地址列表
  $address_i = 0;
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    echo 'arr_name['. $address_i .'] = "'. $address_list_array['name_flag'] .'";';
    $address_i++;
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;

  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' and customers_id=". $_SESSION['customer_id'] ." order by id asc");

   
  echo 'arr_old['. $address_num .'] = new Array();';
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    $address_orders_array['value'] = str_replace("\n","",$address_orders_array['value']); 
    $address_orders_array['value'] = str_replace("\r","",$address_orders_array['value']); 
    echo 'arr_old['. $address_num .']["'. $address_orders_array['name'] .'"] = "'. $address_orders_array['value'] .'";';
  }

  $address_num++; 
  tep_db_free_result($address_orders_query); 
  } 

  
  tep_db_free_result($address_orders_group_query); 
?>
  var address_show_list = document.getElementById("address_show_list");

  address_show_list.options.length = 0;

  len = arr_old.length;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
    }
    if(arr_str != ''){
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i);
    }

  }
}

function address_option_list(value){
  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();
  var arr_list = new Array();
  var arr_flag = new Array();
<?php
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' and customers_id=". $_SESSION['customer_id'] ." order by id asc");
  
   
  echo 'arr_list['. $address_num .'] = new Array();';
  echo 'arr_flag['. $address_num .'] = "'. $address_orders_group_array['orders_id'] .'";'; 
    while($address_orders_array = tep_db_fetch_array($address_orders_query)){
       
      $address_orders_array['value'] = str_replace("\n","",$address_orders_array['value']); 
      $address_orders_array['value'] = str_replace("\r","",$address_orders_array['value']); 
      echo 'arr_list['. $address_num .']["'. $address_orders_array['name'] .'"] = "'. $address_orders_array['value'] .'";';
    }
  $address_num++;
  tep_db_free_result($address_orders_query); 
  }
  tep_db_free_result($address_orders_group_query); 
?>
if(arr_list.length > 0){
  for(x in arr_list[value]){
   if(document.getElementById("op_"+x)){
     var list_option = document.getElementById("op_"+x);
     if('<?php echo $country_fee_id;?>' == 'op_'+x){
      check(arr_list[value][x]);
    }else if('<?php echo $country_area_id;?>' == 'op_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[value][x]);
     
    }else if('<?php echo $country_city_id;?>' == 'op_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = arr_list[value][x]; 
    }
     
    if(document.getElementById("l_"+x)){
      if($("#l_"+x).val() == 'true'){
        $("#r_"+x).html("&nbsp;*必須");
      }
    }
    $("#error_"+x).html("");
   }
  }
  
  $("#address_flag_id").val(arr_flag[value]);
}else{

  address_clear();
}

}

<?php
  if(!isset($_POST['action'])){
?>
$(document).ready(function(){ 
  address_list();
  address_option_list(0);
});
<?php
  }elseif($_POST['action'] == 'address'){
?>
$(document).ready(function(){ 
  address_list();
  $("#address_flag_id").val("<?php echo $_POST['address_flag_id'];?>");
});
<?php
  }else{
?>
$(document).ready(function(){ 
  address_list();
  address_option_list(0);
});
<?php 
  }
  $address_histroy_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $_SESSION['customer_id'] ."'"); 
  $address_histroy_num = tep_db_num_rows($address_histroy_query);
  tep_db_free_result($address_histroy_query);
?>
$(document).ready(function(){
  $("#"+country_fee_id).change(function(){
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
  }); 
  $("#"+country_area_id).change(function(){
    country_area_check($("#"+country_area_id).val());
  });
  <?php
    if(isset($_POST[$country_fee_id])){
  ?>  
    check("<?php echo isset($_POST[$country_fee_id]) ? $_POST[$country_fee_id] : '';?>");
  <?php
   }elseif($address_histroy_num > 0){
  ?>
    check();
    address_option_list(0);
  <?php
   }else{
  ?>
    check();
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_area_id])){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_POST[$country_area_id];?>");
  <?php
   }elseif(isset($_POST[$country_fee_id]) && isset($_POST[$country_area_id])){
  ?>
   country_check($("#"+country_fee_id).val(),"<?php echo $_POST[$country_area_id];?>");
  <?php
   }elseif($address_histroy_num > 0){
  ?>
    country_check($("#"+country_fee_id).val());
    address_option_list(0);
  <?php
   }else{
  ?>
    country_check($("#"+country_fee_id).val());
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_city_id])){
  ?>
     
     country_area_check($("#"+country_area_id).val(),"<?php echo $_POST[$country_city_id];?>");
  <?php
   }elseif(isset($_POST[$country_fee_id]) && isset($_POST[$country_area_id])){
  ?>
     country_area_check($("#"+country_area_id).val(),"<?php echo $_POST[$country_city_id];?>"); 
  <?php
   }elseif($address_histroy_num > 0){
  ?>
    country_area_check($("#"+country_area_id).val());
    address_option_list(0);
  <?php
   }else{
  ?>
    country_area_check($("#"+country_area_id).val());
  <?php
  }
  ?>   
});
</script>
<?php
/*
  $Id$

  顾客详细信息页
*/
//设置是否有新信息的多语言 
  $newsletter_array = array(array('id' => '1',
                                  'text' => ENTRY_NEWSLETTER_YES),
                            array('id' => '0',
                                  'text' => ENTRY_NEWSLETTER_NO));

//如果没有设置 只读，则为否
  if (!isset($is_read_only)) $is_read_only = false;
  if (!isset($processed)) $processed = false;

  include_once(DIR_WS_CLASSES . 'address_form.php');
  $address_form = new addressForm;

  // firstname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_firstname'],false,true);
  } elseif ($error == true) {
      if ($entry_firstname_error == true) {
          $a_value = tep_draw_input_field('firstname') . '&nbsp;' . ENTRY_FIRST_NAME_ERROR;
      } else {
          $a_value = $firstname . tep_draw_hidden_field('firstname');
      }
  } else {
      $a_value = tep_draw_input_field('firstname', $account['customers_firstname']) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_lastname'],false,true);
  } elseif ($error == true) {
      if ($entry_lastname_error == true) {
          $a_value = tep_draw_input_field('lastname') . '&nbsp;' . ENTRY_LAST_NAME_ERROR;
      } else {
          $a_value = $lastname . tep_draw_hidden_field('lastname');
      }
  } else {
      $a_value = tep_draw_input_field('lastname', $account['customers_lastname']) . '&nbsp;' . ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);

  // email_address
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_email_address'],false,true);
  } elseif ($error == true) {
      if ($entry_email_address_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR;
      } elseif ($entry_email_address_check_error == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      } elseif ($entry_email_address_exists == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      } elseif ($entry_guest_not_active == true) {
          $a_value = tep_draw_input_field('email_address') . '&nbsp;' .  ENTRY_GUEST_EMAIL_NOT_ACTIVE;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address', $account['customers_email_address']) . '&nbsp;' . ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table" class="formArea">
      <tr>
        <td class="main">
        <?php
          echo tep_draw_form('account_edit_per', tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onSubmit=""') . tep_draw_hidden_field('action', 'per');
        ?>
        <table border="0" cellspacing="0" cellpadding="2" summary="table">
<?php 
  $address_form->printCategoryPersonal();
?>
        </table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td class="main" align="right" colspan="2"><input type="hidden" name="old_email" value="<?php echo $account['customers_email_address'];?>"><input type="image" src="images/design/button/save.gif">&nbsp;&nbsp;</td></tr>
</table>
</form></td>
      </tr>
    </table></td>
  </tr>
<!-- start -->
  <tr>
    <td class="formAreaTitle"><br><?php echo TITLE_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main">
    <table border="0" width="100%" summary="table" cellspacing="0" cellpadding="2" class="preorder_shipping">
      <tr>
        <td class="main">
        <?php
          echo tep_draw_form('account_edit_address', tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onSubmit=""') . tep_draw_hidden_field('action', 'address');
          $address_orders_history_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $_SESSION['customer_id'] ."'");
          $address_orders_history_num = tep_db_num_rows($address_orders_history_query);
          tep_db_free_result($address_orders_history_query);
          $sylte_none = $address_orders_history_num > 0 ? '' : 'style="display:none;"';
        ?>
        <table border="0" cellspacing="0" cellpadding="2" summary="table" class="formArea">
        <tr id="address_histroy_id"<?php echo $sylte_none;?>><td class="main" width="120">&nbsp;<?php echo
        TITLE_ADDRESS_OPTION;?></td><td class="main">&nbsp;<select id="address_show_list" onchange="address_option_list(this.value);"></select>
        <input type="hidden" id="address_flag_id" name="address_flag_id" value="">
        <input type="hidden" id="first_name" name="lastname" value="">
        <input type="hidden" id="end_name" name="firstname" value="">
        <input type="hidden" id="email" name="email_address" value="">
        <input type="hidden" id="old_email" name="old_email" value="">
        <input type="hidden" id="options" name="newsletter" value="">
        <input type="hidden" id="pwd" name="password" value="">
        <input type="hidden" id="pwd_1" name="confirmation" value="">
        <input type="hidden" id="action_flag" name="action_flag" value="0"> 
        </td></tr>
        <?php       
          $hm_option->render('','',true); 
        ?> 
          <tr><td class="main" align="right" colspan="2"><a href="javascript:void(0);" onclick="if(confirm('このレコードを削除してもよろしいですか？')){location.href='<?php echo FILENAME_ACCOUNT_EDIT;?>?act='+document.getElementById('address_flag_id').value;}else{return false;}"><img src="includes/languages/japanese/images/buttons/button_delete02.gif"></a>&nbsp;<a href="javascript:void(0);" onclick="address_clear();"><img src="images/design/button/new_found.gif"></a>&nbsp;<a href="javascript:void(0);" onclick="check_form_address();document.account_edit_address.submit();"><img src="images/design/button/save.gif"></a></form>&nbsp;&nbsp;</td></tr>
        </table></form>
        </td>
      </tr>
    </table>
    </td>
 </tr>
<!-- end -->

  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_OPTIONS; ?></td>
  </tr>
  <tr>
    <td class="main">
    <?php 
      echo tep_draw_form('account_edit_options', tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onSubmit=""') . tep_draw_hidden_field('action', 'options');
    ?>
    <table border="0" width="100%" summary="table" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"> 
        <table border="0" cellspacing="0" cellpadding="2" summary="table">
          <tr>
            <td class="main" width="120">&nbsp;<?php echo ENTRY_NEWSLETTER; ?></td>
            <td class="main">
<?php
  if ($is_read_only == true) {
    if ($account['customers_newsletter'] == '1') {
      echo '&nbsp;'.ENTRY_NEWSLETTER_YES;
    } else {
      echo '&nbsp;'.ENTRY_NEWSLETTER_NO;
    }
  } elseif ($processed == true) {
    if ($newsletter == '1') {
      echo '&nbsp;'.ENTRY_NEWSLETTER_YES;
    } else {
      echo '&nbsp;'.ENTRY_NEWSLETTER_NO;
    }
    echo tep_draw_hidden_field('newsletter');  
  } else {
    echo '&nbsp;'.tep_draw_pull_down_menu('newsletter', $newsletter_array, $account['customers_newsletter']) . '&nbsp;' . ENTRY_NEWSLETTER_TEXT;
  }
?></td>
          </tr>
<?php
  if ($is_read_only != true) {
    if(substr(basename($PHP_SELF),0,7) == 'create_') {
    $guestchk_array = array(array('id' => '0',
                                   'text' => ENTRY_ACCOUNT_MEMBER),
                              array('id' => '1',
                                   'text' => ENTRY_ACCOUNT_GUEST));
  
?>      
      <tr>
        <td class="main">&nbsp;<?php echo ENTRY_GUEST; ?></td>
<?php if (!isset($guestchk)) $guestchk = NULL;?>
        <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('guestchk', $guestchk_array, $guestchk, 'onchange="pass_hidd()"'); ?>&nbsp;&nbsp;<span class="red">※</span>&nbsp;会員登録をしないで購入することもできます。</td>
      </tr>
<?php
    } else {
    echo '<input type="hidden" name="guestchk" value="0" >';
  }
  }
?>
</table>
</td>
</tr>
<tr><td>&nbsp;</td><td class="main" align="right"><input type="image" src="images/design/button/save.gif">&nbsp;&nbsp;</td></tr>
</form>
</table>
</td>
</tr>

<?php
if (!isset($guestchk)) $guestchk = NULL;
  if($guestchk == '1') {
  $newpass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
  $password = $newpass;
  $confirmation = $newpass;
  }
  if ($is_read_only == false) {
?>

  <tr id="trpass1">
    <td class="formAreaTitle"><br><?php echo CATEGORY_PASSWORD; ?></td>
  </tr> 
  <tr id="trpass2">
    <td class="main">
    <?php 
      echo tep_draw_form('account_edit_pwd', tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onSubmit=""') . tep_draw_hidden_field('action', 'pwd');
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table"> 
      <tr>
        <td class="main"> 
          <table border="0" cellspacing="0" cellpadding="2" summary="table"> 
          <tr>
            <td class="main" width="120">&nbsp;<?php echo ENTRY_PASSWORD; ?></td>
            <td class="main" style="*width:355px;">
<?php
    if ($error_pwd == true) {
      if ($entry_password_english_error == true) { 
        echo '&nbsp;'.tep_draw_password_field('password') . ENTRY_PASSWORD_TEXT;
      } else if($entry_password_error == true) {
        echo '&nbsp;'.tep_draw_password_field('password') . ENTRY_PASSWORD_TEXT;
      } else if($entry_password_confirmation_error == true){
        echo '&nbsp;'.tep_draw_password_field('password') . ENTRY_PASSWORD_TEXT;
      } else if($entry_password_old_error == true){
         echo '&nbsp;'.tep_draw_password_field('password') . ENTRY_PASSWORD_TEXT;
      } else {
        echo PASSWORD_HIDDEN . tep_draw_hidden_field('password') . tep_draw_hidden_field('confirmation');
      }
    } else {
      echo '&nbsp;'.tep_draw_password_field('password') . ENTRY_PASSWORD_TEXT;
    }
?></td>
          </tr>
<?php
    if ( ($error_pwd == false) || ($entry_password_error == true) || ($entry_password_english_error == true) || ($entry_password_confirmation_error == true) || ($entry_password_old_error == true)) {
?>
          <tr>
            <td class="main">&nbsp;<?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
            <td class="main">
<?php
      echo '&nbsp;'.tep_draw_password_field('confirmation') . ENTRY_PASSWORD_CONFIRMATION_TEXT;
?></td>
          </tr>
<?php
    }
?> 
          <tr>
           <td class="main">&nbsp;</td>
           <td class="main">&nbsp;
  <?php
    if ($error_pwd == true) {
      if ($entry_password_english_error == true) { 
        echo '<font color="red">' . ENTRY_PASSWORD_ENGLISH .'</font>';
      } else if($entry_password_error == true) {
        echo ENTRY_PASSWORD_ERROR;
      } else if($entry_password_confirmation_error == true){
        echo '<font color="red">ご入力されたパスワードが一致しておりません</font>';
      } else if($entry_password_old_error == true){
         echo '<font color="red">安全のため、古いパスワードと違うパスワードを設定してください。</font>';
      }     
    }  
  ?> 
           </td>
          </tr> 
        <tr>
        <td class="main" colspan="2"><?php echo ENTRY_PASSWORD_INFORM_READ_TEXT;?></td>
        </tr>
    </table>
      </td>
      </tr> 
      <tr><td class="main" align="right"><input type="image" src="images/design/button/save.gif">&nbsp;&nbsp;</td></tr>
    </table></form></td>
  </tr>  
<?php
  }
?>

<?php
  #agreement_for_use
  if(basename($PHP_SELF) == 'create_account.php'){
    if (file_exists('./includes/agreement_for_use.txt') == 'true') {
      $file = file('includes/agreement_for_use.txt');
    $agreement_text = '';
  
    foreach($file as $key => $value) {
      $agreement_text .= $value;
    }
?>

  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_AGREEMENT; ?></td>
  </tr>

  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
      <tr>
        <td class="main">
<?php
  echo tep_draw_textarea_field('agreement', 'soft', '98%', '5', mb_convert_encoding($agreement_text, 'UTF-8', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
  echo '<br>';
  echo tep_draw_checkbox_field('agreement_chk', 'ok') . ENTRY_AGREEMENT_TEXT;
?>
        </td>
      </tr>
    </table></td>
      </tr>
    </table></td>
  </tr>
<?php
    }
  }
?>
</table>
<input type="hidden" name="country" value="107">
