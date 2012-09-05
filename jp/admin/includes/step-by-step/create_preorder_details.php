<?php
/*
   $Id$
  
   3rmt over
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
              <?php
                $predate_array = explode('-', date('Y-m-d', time())); 
              ?>
              <div style="float:left;"> 
                <select name="predate_year" id="predate_year" onchange="change_predate_date();">
                <?php
                  $default_predate_year = (isset($_POST['predate_year']))?$_POST['predate_year']:$predate_array[0]; 
                  for ($f_num = 2006; $f_num <= 2050; $f_num++) {
                    echo '<option value="'.$f_num.'"'.(($default_predate_year == $f_num)?' selected':'').'>'.$f_num.'</option>'; 
                  }
                ?>
                </select>
                <select name="predate_month" id="predate_month" onchange="change_predate_date();">
                <?php
                  for ($f_num = 1; $f_num <= 12; $f_num++) {
                    $default_predate_month = (isset($_POST['predate_month']))?$_POST['predate_month']:$predate_array[1]; 
                    $tmp_predate_month = sprintf('%02d', $f_num); 
                    echo '<option value="'.$tmp_predate_month.'"'.(($default_predate_month == $tmp_predate_month)?' selected':'').'>'.$tmp_predate_month.'</option>'; 
                  }
                ?>
                </select>
                <select name="predate_day" id="predate_day" onchange="change_predate_date();">
                <?php
                  for ($f_num = 1; $f_num <= 31; $f_num++) {
                    $default_predate_day = (isset($_POST['predate_day']))?$_POST['predate_day']:$predate_array[2]; 
                    $tmp_predate_day = sprintf('%02d', $f_num); 
                    echo '<option value="'.$tmp_predate_day.'"'.(($default_predate_day == $tmp_predate_day)?' selected':'').'>'.$tmp_predate_day.'</option>'; 
                  }
                ?>
                </select>
              </div>
              <div class="yui3-skin-sam yui3-g">
              <input type="hidden" name="predate" id="predate" value="<?php echo isset($_POST['predate'])?$_POST['predate']:$predate;?>"> 
              <a href="javascript:void(0);" onclick="open_calendar();" class="dpicker"></a>
              <input type="hidden" name="toggle_open" value="0" id="toggle_open">
              <div class="yui3-u" id="new_yui3">
              <div id="mycalendar"></div> 
              </div>
              </div>
              </td>
              <?php
              echo '<td>'; 
              if (empty($predate)) {
                echo '&nbsp;<font color="red">'.CREATE_PREORDER_MUST_INPUT.'</font>'; 
              }
              echo '</td>'; 
              ?>
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
                <td class="main">&nbsp;<textarea name='fax' style='width:400px;height:45px;'><?php echo $fax;?></textarea>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
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
