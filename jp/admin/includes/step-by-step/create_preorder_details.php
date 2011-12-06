<?php
/*
   $Id$
  
   3rmt over
*/

    tep_draw_hidden_field($customer_id);    
?>
<script type="text/javascript">
function hidden_payment(){
  var idx = document.create_order.elements["payment_method"].selectedIndex;
  var CI = document.create_order.elements["payment_method"].options[idx].value;
  
  if (CI == '銀行振込(買い取り)') {
    document.getElementById('trpass1').style.display = "";
  } else {
    document.getElementById('trpass1').style.display = "none";
  }

  if (CI == 'コンビニ決済') {
    document.getElementById('copass1').style.display = "";
  } else {
    document.getElementById('copass1').style.display = "none";
  }
  
  if (CI == '楽天銀行') {
    document.getElementById('rakpass1').style.display = "";
  } else {
    document.getElementById('rakpass1').style.display = "none";
  }
}
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="formAreaTitle"><?php echo CATEGORY_CORRECT; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_CUSTOMERS_ID; ?></td>
                <td class="main">&nbsp;<?php  echo tep_draw_hidden_field('customers_id', $customer_id) . $customer_id; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE;?><?php if (isset($entry_firstname_error) && $entry_firstname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE;?><?php if (isset($entry_lastname_error) && $entry_lastname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>'; ?><?php if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_SITE; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_SITE; ?>:</td>
                <td class="main">&nbsp;<?php echo isset($account) && $account?( '<font color="#FF0000"><b>'.tep_get_site_romaji_by_id($account['site_id']).'</b></font>'.tep_draw_hidden_field('site_id', $account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT); ?></td>
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
      <?php echo CATEGORY_COMPANY; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_COMPANY; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('company', $company) . '&nbsp;' . ENTRY_COMPANY_TEXT; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  }
?>

<?php
  $payment_text = tep_get_list_pre_payment(); 
  $payment_array = explode("\n", $payment_text);
  $payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
  for($i=0; $i<sizeof($payment_array); $i++) {
    $payment_list[] = array('id' => $payment_array[$i],
                'text' => $payment_array[$i]);
  }
  switch (isset($bank_kamoku)?$bank_kamoku:null) {
    case '普通':
    default:
      $bank_sele_f = true;
      $bank_sele_t = false;
      break;
    case '当座':
      $bank_sele_f = false;
      $bank_sele_t = true;
      break;
  }
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_PAYMENT_TITLE;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
        <td class="main">&nbsp;<?php echo CREATE_ORDER_PAYMENT_TITLE;?>:</td>
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
                } ?>
        </td>
              </tr>
<?php
  if ($payment_method == 'コンビニ決済') {
    echo '<tr id="copass1" style="">';
  } else {
    echo '<tr id="copass1" style="display: none;">';
  }
  ?>
  <td colspan="2"><br><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="main"><?php echo CREATE_ORDER_PC_TEXT;?></td> 
      <td class="main">&nbsp;<?php echo tep_draw_input_field('con_email', $con_email); ?></td>
    </tr>
  </table>
  </td> 
  <?php
  echo '</tr>';
?>
<?php
  if ($payment_method == '楽天銀行') {
    echo '<tr id="rakpass1" style="">';
  } else {
    echo '<tr id="rakpass1" style="display: none;">';
  }
  ?>
  <td colspan="2"><br><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="main"><?php echo CREATE_ORDER_TEL_TEXT;?></td> 
      <td class="main">&nbsp;<?php echo tep_draw_input_field('rak_tel', $rak_tel); ?></td>
    </tr>
  </table>
  </td> 
  <?php
  echo '</tr>';
  if ($payment_method == '銀行振込(買い取り)') {
    echo '<tr id="trpass1">'; 
  } else {
    echo '<tr id="trpass1" style="display:none;">'; 
  }
  ?>
  <td colspan="2">
  <br>
  <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main">&nbsp;<?php echo CREATE_ORDER_BANK_NAME_TEXT;?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('bank_name', ''); ?><?php if (isset($entry_bank_name_error) && $entry_bank_name_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo CREATE_ORDER_BANK_SHITEN_TEXT;?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('bank_shiten', ''); ?><?php if (isset($entry_bank_shiten_error) && $entry_bank_shiten_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo CREATE_ORDER_BANK_KAMOKU_TEXT;?></td>
            <td class="main">&nbsp; <?php echo tep_draw_radio_field('bank_kamoku', '普通', $bank_sele_f); ?>&nbsp;<?php echo CREATE_ORDER_SELECT_COMMON_TEXT;?>&nbsp;&nbsp;<?php echo tep_draw_radio_field('bank_kamoku', '当座', $bank_sele_t); ?>&nbsp;<?php echo CREATE_ORDER_SELECT_COMMON_ONE_TEXT;?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo CREATE_ORDER_BANK_KOUZA_NUM_TEXT;?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('bank_kouza_num', ''); ?><?php if (isset($entry_bank_kouza_num_error) && $entry_bank_kouza_num_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
          </tr>
          <tr>
            <td class="main">&nbsp;<?php echo CREATE_ORDER_BANK_KOUZA_NAME_TEXT;?></td>
            <td class="main">&nbsp;<?php echo tep_draw_input_field('bank_kouza_name', ''); ?><?php if (isset($entry_bank_kouza_name_error) && $entry_bank_kouza_name_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
          </tr>
        </table>
        </td>
       </tr>
  </table></td>
        </tr>
      </table>
  </td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_PREORDER_PREDATE;?></td>
  </tr>
  <tr>   
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
              <td class="main">
              &nbsp;<?php echo CREATE_PREORDER_PREDATE;?>: 
              </td>
              <td class="main">
              <?php echo tep_draw_input_field('predate', $predate, 'id="predate"');?> 
              </td>
              </tr>
              </table>
          </td>
        </tr>
    </table> 
  </td> 
  </tr> 
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">

              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('fax', $fax, 'size="60" maxlength="255"'); ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
              </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_ONE;?></td>
        </tr>
        <tr>
          <td class="main" colspan="2">&nbsp;<b><?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ_TWO;?></b></td>
        </tr>
            </table></td>
        </tr>
      </table>
  </td>
  </tr>
</table>
