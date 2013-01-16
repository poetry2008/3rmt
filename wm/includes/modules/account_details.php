<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function in_array(value,arr){
  
  for(vx in arr){
    if(value == arr[vx]){
      return true;
    }
  }
  return false;
}
<?php
if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] !=''){
?>
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
    
     
    $address_orders_array['value'] = str_replace("\r\n","<br>",$address_orders_array['value']); 
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
  var arr_address = new Array();
  var arr_list = new Array();
  var arr_flag = new Array();
<?php
  $address_i = 0;
  $address_option_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0'");
  while($address_option_array = tep_db_fetch_array($address_option_query)){

    echo 'arr_address['.$address_i.'] = "'.$address_option_array['name_flag'].'";'."\n";
    $address_i++;
  }
  tep_db_free_result($address_option_query);
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
       
      $address_orders_array['value'] = str_replace("\r\n","<br>",$address_orders_array['value']); 
      echo 'arr_list['. $address_num .']["'. $address_orders_array['name'] .'"] = "'. $address_orders_array['value'] .'";';
    }
  $address_num++;
  tep_db_free_result($address_orders_query); 
  }
  tep_db_free_result($address_orders_group_query); 
?>
  for(y in arr_address){

    $("#tr_"+arr_address[y]).hide();
  }
  for(x in arr_list[value]){
    $("#op_"+x).html(arr_list[value][x]);
    $("#tr_"+x).show();
  }
  
  $("#address_flag_id").val(arr_flag[value]);

}

$(document).ready(function(){ 
  address_list();
  address_option_list(0);
});
<?php
}
?>
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
          $a_value = tep_draw_input_field('firstname', '', "class='input_text'") .'&nbsp;'. ENTRY_FIRST_NAME_ERROR;
      } else {
          $a_value = $firstname .  tep_draw_hidden_field('firstname','','class="input_text"');
      }
  } else {
      $a_value = tep_draw_input_field('firstname', $account['customers_firstname'], "class='input_text'") .'&nbsp;'. ENTRY_FIRST_NAME_TEXT;
  }
  $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

  // lastname
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_lastname'],false,true);
  } elseif ($error == true) {
      if ($entry_lastname_error == true) {
          $a_value = tep_draw_input_field('lastname', '', "class='input_text'") .'&nbsp;'. ENTRY_LAST_NAME_ERROR;
      } else {
          $a_value = $lastname . tep_draw_hidden_field('lastname');
      }
  } else {
      $a_value = tep_draw_input_field('lastname', $account['customers_lastname'], "class='input_text'") .'&nbsp;'. ENTRY_LAST_NAME_TEXT;
  }
  $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value); 
  
  // email_address
  if ($is_read_only == true) {
      $a_value = tep_output_string($account['customers_email_address'],false,true);
  } elseif ($error == true) {
      if ($entry_email_address_error == true) {
          $a_value = tep_draw_input_field('email_address', '', "class='input_text'") .'&nbsp;'. ENTRY_EMAIL_ADDRESS_ERROR;
      } elseif ($entry_email_address_check_error == true) {
          $a_value = tep_draw_input_field('email_address', '', "class='input_text'") .'&nbsp;'. ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      } elseif ($entry_email_address_exists == true) {
          $a_value = tep_draw_input_field('email_address', '', "class='input_text'") .'&nbsp;'. ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
      } elseif ($entry_guest_not_active == true) {
          $a_value = tep_draw_input_field('email_address', '', "class='input_text'") .'&nbsp;'. ENTRY_GUEST_EMAIL_NOT_ACTIVE;
      } else {
          $a_value = $email_address . tep_draw_hidden_field('email_address');
      }
  } else {
      $a_value = tep_draw_input_field('email_address', $account['customers_email_address'], "class='input_text'") .'&nbsp;'. ENTRY_EMAIL_ADDRESS_TEXT;
  }
  $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);
?>
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
<?php
  $address_form->printCategoryPersonal();
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
<?php
  $address_history_query = tep_db_query("select customers_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='".$_SESSION['customer_id']."'");
  $address_history_num = tep_db_num_rows($address_history_query);
  tep_db_free_result($address_history_query);
  if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != '' && isset($account['customers_email_address']) && $account['customers_email_address'] != '' && $address_history_num > 0){
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo TITLE_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main">
    <table border="0" width="100%" summary="table" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main">
        <table border="0" cellspacing="0" cellpadding="2" summary="table">
          <tr>
            <td class="main" width="93"><?php echo TITLE_ADDRESS_OPTION; ?></td>
            <td class="main"><select id="address_show_list" onchange="address_option_list(this.value);"></select>
            </td>
            </tr> 
      <?php
        $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where status='0' and type!='text' order by sort");
        while($address_array = tep_db_fetch_array($address_query)){
      ?>    
      <tr id="tr_<?php echo $address_array['name_flag'];?>">
        <td class="main" valign="top">&nbsp;<?php echo $address_array['name'].':'; ?></td>
        <?php if (isset($check_ac_single)) {?> 
          <td class="main"><div class="address_item_info" id="op_<?php echo $address_array['name_flag'];?>"></div></td>
        <?php } else {?>
          <td class="main">&nbsp;<span id="op_<?php echo $address_array['name_flag'];?>"></span></td>
        <?php }?>
      </tr>
      <?php
        }
        tep_db_free_result($address_query);
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
  <!-- end -->
  <tr>
    <td class="formAreaTitle"><br><?php echo CATEGORY_OPTIONS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" summary="table" cellspacing="0" cellpadding="2" class="formArea">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
          <tr>
            <td class="main" width="93"><?php echo ENTRY_NEWSLETTER; ?></td>
            <?php
            if (isset($check_ac_single)) {
            ?>
            <td class="main" style="padding-left:6px;">
            <?php
            } else {
            ?>
            <td class="main">
            <?php
            }
            ?>
<?php
  if ($is_read_only == true) {
    if ($account['customers_newsletter'] == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
  } elseif ($processed == true) {
    if ($newsletter == '1') {
      echo ENTRY_NEWSLETTER_YES;
    } else {
      echo ENTRY_NEWSLETTER_NO;
    }
    echo tep_draw_hidden_field('newsletter');  
  } else {
    if (!isset($account['customers_newsletter'])) $account['customers_newsletter'] = NULL; //del notice
    echo tep_draw_pull_down_menu('newsletter', $newsletter_array, $account['customers_newsletter']) . '&nbsp;' . ENTRY_NEWSLETTER_TEXT;
  }
?></td>
          </tr>
<?php
  if ($is_read_only != true) {
    if(substr(basename($PHP_SELF),0,7) == 'create_') {
    $guestchk_array = array(array('id' => '0',
                                   'text' => '<span>'.ENTRY_ACCOUNT_MEMBER.'</span>'),
                              array('id' => '1',
                                   'text' => '<span>'.ENTRY_ACCOUNT_GUEST.'</span>'));
  
?>      
      <tr>
        <td class="main" valign="top"><?php echo ENTRY_GUEST; ?></td>
<?php if (!isset($guestchk)) $guestchk = NULL;?>
        <td class="main">
        <?php 
        if(isset($_POST['guestchk']) && $_POST['guestchk'] != ''){
          $guestchk = tep_db_prepare_input($_POST['guestchk']);
        }
        foreach($guestchk_array as $guestchk_info){
          echo '<div class="input_box"><input type="radio" value="'.  $guestchk_info['id'].'" name="guestchk" ';
          if(isset($guestchk)&&$guestchk){
            if($guestchk_info['id']==$guestchk){
              echo ' checked="true" ';
            }
          }else{
            if($guestchk_info['id']=='0'){
              echo ' checked="true" ';
            }
          }
          echo ' onclick="pass_hidd(\''.$guestchk_info['id'].'\')">';
          echo $guestchk_info['text'];
          echo "&nbsp;&nbsp;</div>";
        }
        ?>
        <br>
        <div class="input_both"><span class="red">※</span>&nbsp;<?php echo TEXT_ACCOUNT_GUEST_INFO;?></div></td>
      </tr>
<?php
    } else {
    echo '<input type="hidden" name="guestchk" value="0" >';
  }
  }
?>
        </table></td>
      </tr>
    </table></td>
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
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table">
      <tr>
        <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table" width="100%">
          <tr>
            <td class="main" width="93"><?php echo ENTRY_PASSWORD; ?></td>
            <td class="main">
<?php
  $p_error_show_str = '';   
  if ($error == true) {
      if ($entry_password_confirm_same_error == true) { 
        echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' .  ENTRY_PASSWORD_TEXT;
        $p_error_show_str = ENTRY_NO_USE_OLD_PASSWORD;
      } else if ($entry_password_confirmation_error == true) { 
        echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
        $p_error_show_str = '<font color="red">'.ENTRY_PASSWORD_IS_DIFFERENT.'</font>';
      } else if($entry_password_error == true) {
        echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
        if (isset($entry_password_error_msg)) {
          $p_error_show_str = $entry_password_error_msg; 
        } else {
          $p_error_show_str = ENTRY_PASSWORD_ERROR;
        }
      } else {
      echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
      }
    } else {
      echo tep_draw_password_field('password', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_TEXT;
    }
?></td>
          </tr>
<?php
    if ( ($error == false) || ($entry_password_error == true) || ($error == true) ) {
?>
          <tr>
            <td class="main" width="93"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
            <td class="main">
<?php
      echo tep_draw_password_field('confirmation', '', "class='input_text'") . '&nbsp;' . ENTRY_PASSWORD_CONFIRMATION_TEXT;
?></td>
          </tr>
<?php
    }
?>  
    <?php
    if ($p_error_show_str != '') {
    ?>
    <tr>
      <td class="main" width="93">&nbsp;</td>
      <td class="main" style="font-size:10px;">
      <?php echo $p_error_show_str;?> 
      </td>
    </tr>
    <?php
    }
    ?>
   
    </table>
    <table border="0" cellspacing="0" cellpadding="2" summary="table" width="100%">
     <tr>
      <td class="main" colspan="2"><?php echo ENTRY_PASSWORD_INFORM_READ_TEXT;?></td> 
    </tr>
    </table>
    </td>
      </tr>
    </table></td>
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
