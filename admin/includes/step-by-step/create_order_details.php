<?php
/*
	JP��GM���̥ե�����
*/

    tep_draw_hidden_field($customer_id);    
?>
<script type="text/javascript">
function hidden_payment(){
  var idx = document.create_order.elements["payment_method"].selectedIndex;
  var CI = document.create_order.elements["payment_method"].options[idx].value;
  
  if(CI == '��Կ���(�㤤���)'){
    document.getElementById('trpass1').style.display = "";
	document.getElementById('trpass2').style.display = "";
    document.getElementById('trpass3').style.display = "";
	document.getElementById('trpass4').style.display = "";
  }else{
    document.getElementById('trpass1').style.display = "none";
	document.getElementById('trpass2').style.display = "none";
    document.getElementById('trpass3').style.display = "none";
	document.getElementById('trpass4').style.display = "none";
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
                <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?>&nbsp;&nbsp;�ѹ�������н������Ƥ�������<?php if ($entry_firstname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?>&nbsp;&nbsp;�ѹ�������н������Ƥ�������<?php if ($entry_lastname_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>'; ?><?php if ($entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <?php
  if (ACCOUNT_COMPANY == 'true') {
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

<!--

  <tr>
    <td class="formAreaTitle"><br>
      <?php echo CATEGORY_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main">
	<table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_POST_CODE; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('postcode', $postcode) . '&nbsp;' . ENTRY_POST_CODE_TEXT; ?></td>
              </tr>		  
              <?php
  if (ACCOUNT_STATE == 'true') {
              ?>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_STATE; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('state', $state) . '&nbsp;' . ENTRY_STATE_TEXT; ?></td>
              </tr>
              <?php
   }
   
   tep_draw_hidden_field('step', '3')
              ?>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_CITY; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('city', $city) . '&nbsp;' . ENTRY_CITY_TEXT; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_STREET_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('street_address', $street_address) . '&nbsp;' . ENTRY_STREET_ADDRESS_TEXT; ?></td>
              </tr>
              <?php
  if (ACCOUNT_SUBURB == 'true') {
              ?>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_SUBURB; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('suburb', $suburb) . '&nbsp;' . ENTRY_SUBURB_TEXT; ?></td>
              </tr>
              <?php
  }
              ?>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_COUNTRY; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('country', tep_get_country_name($country)) . '&nbsp;' . ENTRY_COUNTRY_TEXT; ?></td>
              </tr>
            </table></td>
        </tr>
      </table> 
	  </td>
  </tr>
  
-->

<?php
	// ���ץ����Υꥹ�Ⱥ���
	$torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
	$torihiki_list[] = array('id' => '', 'text' => '���򤷤Ƥ�������');
	for($i=0; $i<sizeof($torihiki_array); $i++) {
		$torihiki_list[] = array('id' => $torihiki_array[$i],
								'text' => $torihiki_array[$i]);
	}
	// ������Υꥹ�Ⱥ���
	$today = getdate();
	$m_num = $today[mon];
	$d_num = $today[mday];
	$year = $today[year];
	$date_list[] = array('id' => '', 'text' => '����������򤷤Ƥ�������');
	for($i=0; $i<14; $i++) {
		$date_list[] = array('id' => date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)),
							'text' => strftime("%Yǯ%m��%d����%a��", mktime(0,0,0,$m_num,$d_num+$i,$year)));
	}
	// ������֤Υꥹ�Ⱥ���
	$hour_list[] = array('id' => '', 'text' => '--');
	for($i=1; $i<24; $i++) {
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
	// ��ʧ��ˡ�Υꥹ�Ⱥ���
	$payment_text = "��Կ���\n���쥸�åȥ����ɷ��\n��Կ���(�㤤���)\n����ӥ˷��\n�椦�����ԡ�͹�ضɡ�\n����¾";
	$payment_array = explode("\n", $payment_text);
	$payment_list[] = array('id' => '', 'text' => '��ʧ��ˡ�����򤷤Ƥ�������');
	for($i=0; $i<sizeof($payment_array); $i++) {
		$payment_list[] = array('id' => $payment_array[$i],
								'text' => $payment_array[$i]);
	}

	// ���²��ܤε���
	switch($bank_kamoku) {
		case '����':
			default:
			$bank_sele_f = true;
			$bank_sele_t = false;
			break;
		case '����':
			$bank_sele_f = false;
			$bank_sele_t = true;
			break;
	}

?>
  <tr>
    <td class="formAreaTitle"><br>��ʧ��ˡ</td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
				<td class="main">&nbsp;��ʧ��ˡ:</td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('payment_method', $payment_list, $payment_method, 'onchange="hidden_payment()"'); ?><?php if ($entry_payment_method_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
<?php
	if ($payment_method == '��Կ���(�㤤���)') {
		echo '<tr>';
	} else {
		echo '<tr id="trpass1" style="display: none;">';
	}  
?>
			  	<td colspan="2"><br><table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td class="main">&nbsp;��ͻ����̾:</td>
						<td class="main">&nbsp;<?php echo tep_draw_input_field('bank_name', ''); ?><?php if ($entry_bank_name_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
					</tr>
					<tr>
						<td class="main">&nbsp;��Ź̾:</td>
						<td class="main">&nbsp;<?php echo tep_draw_input_field('bank_shiten', ''); ?><?php if ($entry_bank_shiten_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
					</tr>
					<tr>
						<td class="main">&nbsp;���¼���:</td>
						<td class="main">&nbsp;	<?php echo tep_draw_radio_field('bank_kamoku', '����', $bank_sele_f); ?>&nbsp;����&nbsp;&nbsp;<?php echo tep_draw_radio_field('bank_kamoku', '����', $bank_sele_t); ?>&nbsp;����</td>
					</tr>
					<tr>
						<td class="main">&nbsp;�����ֹ�:</td>
						<td class="main">&nbsp;<?php echo tep_draw_input_field('bank_kouza_num', ''); ?><?php if ($entry_bank_kouza_num_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
					</tr>
					<tr>
						<td class="main">&nbsp;����̾��:</td>
						<td class="main">&nbsp;<?php echo tep_draw_input_field('bank_kouza_name', ''); ?><?php if ($entry_bank_kouza_name_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
					</tr>
				</table></td>
			  </tr>
            </table></td>
        </tr>
      </table>
	</td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br>�������</td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
				<td class="main">&nbsp;�����:</td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('date', $date_list, $date); ?><?php if ($entry_date_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;�������:</td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('hour', $hour_list, $hour); ?>&nbsp;��&nbsp;<?php echo tep_draw_pull_down_menu('min', $min_list, $min); ?>&nbsp;ʬ&nbsp;<b>��24����ɽ����</b><?php if ($entry_tardetime_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;���ץ����:</td>
                <td class="main">&nbsp;<?php echo tep_draw_pull_down_menu('torihikihouhou', tep_get_all_torihiki(), $torihikihouhou);//tep_draw_pull_down_menu('torihikihouhou', $torihiki_list, $torihikihouhou); ?><?php if ($entry_torihikihouhou_error == true) { echo '&nbsp;&nbsp;<font color="red">Error</font>'; }; ?></td>
              </tr>
            </table></td>
        </tr>
      </table>
	</td>
  </tr>
  <tr>
    <td class="formAreaTitle"><br>���һ�����</td>
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
                <td class="main">&nbsp;����Ĵ��:</td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('fax', $fax, 'size="60" maxlength="255"'); ?>&nbsp;&nbsp;��Ϣ�ҡ�HQ��&nbsp;&nbsp;��ա�WA��&nbsp;&nbsp;ȯ���ػߡ�BK��</td>
              </tr>
			  <tr>
			  	<td class="main" colspan="2">&nbsp;���쥫���������C2007/01/01&nbsp;&nbsp;&nbsp;&nbsp;���ꥢ���ס�Aok&nbsp;&nbsp;&nbsp;&nbsp;�ܿͳ�ǧ�ѡ�Hok&nbsp;&nbsp;&nbsp;&nbsp;YahooID��������Y2007/01/01&nbsp;&nbsp;&nbsp;&nbsp;��ե��顼��R</td>
			  </tr>
			  <tr>
			  	<td class="main" colspan="2">&nbsp;<b>�����㡧WA-Aok-C2007/01/01-Hok-R��������FF11 RMT</b></td>
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
