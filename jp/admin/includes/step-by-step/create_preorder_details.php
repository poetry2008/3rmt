<?php
/*
   $Id$
*/

    tep_draw_hidden_field($customer_id);    
?>
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
                <td class="main">&nbsp;<?php echo tep_draw_input_field('lastname', $lastname) . '&nbsp;' . ENTRY_LAST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE;?><?php if (isset($entry_firstname_error) && $entry_firstname_error == true) { echo '&nbsp;&nbsp;<font color="red">'.CREATE_PREORDER_MUST_INPUT.'</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_input_field('firstname', $firstname) . '&nbsp;' . ENTRY_FIRST_NAME_TEXT; ?>&nbsp;&nbsp;<?php echo CREATE_ORDER_NOTICE_ONE;?><?php if (isset($entry_lastname_error) && $entry_lastname_error == true) { echo
  '&nbsp;&nbsp;<font color="red">'.CREATE_PREORDER_MUST_INPUT.'</font>'; }; ?></td>
              </tr>
              <tr>
                <td class="main">&nbsp;<?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main">&nbsp;<?php echo tep_draw_hidden_field('email_address', $email_address) . '<font color="red"><b>' . $email_address . '</b></font>'; ?><?php if (isset($entry_email_address_error) && $entry_email_address_error == true) { echo '&nbsp;&nbsp;<font color="red">'.CREATE_PREORDER_MUST_INPUT.'</font>'; }; ?></td>
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
                <td class="main">&nbsp;
                <?php 
                $s_account = tep_get_customer_by_id((int)$customer_id); 
                echo isset($s_account) && $s_account?( '<font color="#FF0000"><b>'.tep_get_site_romaji_by_id($s_account['site_id']).'</b></font>'.tep_draw_hidden_field('site_id', $s_account['site_id'])):(tep_site_pull_down_menu($site_id) . '&nbsp;' . ENTRY_SITE_TEXT); ?></td>
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
  $payment_array = payment::getPaymentList(); 
  $payment_list[] = array('id' => '', 'text' => CREATE_PREORDER_PAYMENT_LIST_DEFAULT);
  for($i=0; $i<sizeof($payment_array[0]); $i++) {
    if (!empty($payment_array[0][$i])) {
      $payment_list[] = array('id' => $payment_array[0][$i], 'text' => $payment_array[1][$i]);
    }
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
                echo tep_draw_pull_down_menu('payment_method', $payment_list, isset($payment_method)?$payment_method:'', 'onchange="hidden_payment()"'); 
                if (isset($entry_payment_method_error ) && $entry_payment_method_error == true) { 
                  echo '&nbsp;&nbsp;<font color="red">'.CREATE_PREORDER_MUST_INPUT.'</font>'; 
                } ?>
        </td>
              </tr>

              <?php
              foreach ($selection as $skey => $singleton) { 
              foreach ($singleton['fields'] as $fkey => $field) { 
              ?>
              <tr class="rowHide rowHide_<?php echo $singleton['id'];?>">
                <td class="main">
                &nbsp;<?php echo $field['title'];?> 
                </td>
                <td class="main">
                &nbsp;&nbsp;<?php echo $field['field'];?> 
                <font color="#red"><?php echo str_replace('Error', CREATE_PREORDER_MUST_INPUT,$field['message']);?></font> 
                </td>
              </tr>
              <?php }?> 
              <?php }?>
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

              <tr>
                <td class="main">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<textarea name='fax' style='width:400px;height:42px;*height:40px;'><?php echo $fax;?></textarea>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
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
