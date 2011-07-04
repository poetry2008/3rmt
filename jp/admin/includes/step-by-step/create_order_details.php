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
  
  if(CI == '銀行振込(買い取り)'){
    document.getElementById('trpass1').style.display = "";
  }else{
    document.getElementById('trpass1').style.display = "none";
  }
  if (CI == 'コンビニ決済') {
    document.getElementById('copass1').style.display = "";
  } else {
    document.getElementById('copass1').style.display = "none";
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
  // 支払方法のリスト作成
  $payment_text = "銀行振込\nクレジットカード決済\n銀行振込(買い取り)\nペイパル決済\nポイント(買い取り)\n来店支払い\nコンビニ決済\nゆうちょ銀行（郵便局）\n支払いなし";
  $payment_array = explode("\n", $payment_text);
  $payment_list[] = array('id' => '', 'text' => '支払方法を選択してください');
  for($i=0; $i<sizeof($payment_array); $i++) {
    $payment_list[] = array('id' => $payment_array[$i],
                'text' => $payment_array[$i]);
  }

  // 口座科目の記憶
  switch(isset($bank_kamoku)?$bank_kamoku:null) {
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
    echo '<tr>';
  } else {
    echo '<tr id="copass1" style="display: none;">';
  ?>
  <td colspan="2"><br><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="main"><?php echo CREATE_ORDER_PC_TEXT;?></td> 
      <td class="main">&nbsp;<?php echo tep_draw_input_field('con_email', $email_address); ?></td>
    </tr>
  </table>
  </td> 
  <?php
  }
  echo '</tr>';
  if (isset($payment_method) && $payment_method == '銀行振込(買い取り)') {
    echo '<tr>';
  } else {
    echo '<tr id="trpass1" style="display: none;">';
  }  
?>
          <td colspan="2"><br><table border="0" cellspacing="0" cellpadding="0">
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
        </table></td>
        </tr>
            </table></td>
        </tr>
      </table>
  </td>
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
                //diff order and order2
                /*
                if(isset($from_page)&&$from_page == 'create_order_process2'){
                  if(!isset($hour)||$hour==''){
                    $hour = date('H',time());  
                  }
                  echo tep_draw_pull_down_menu('hour', $hour_list, isset($hour)?$hour:''); 
                }else{
                echo tep_draw_pull_down_menu('hour', $hour_list, isset($hour)?$hour:''); 
                }
                */
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
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">

<!--

              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('telephone', $telephone) . '&nbsp;' . ENTRY_TELEPHONE_NUMBER_TEXT;?></td>
              </tr>

-->

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

<!--

  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_ORDER_DETAILS; ?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_CURRENCY; ?></td>
                <td class="main"><?php echo $SelectCurrencyBox ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>

-->

</table>
