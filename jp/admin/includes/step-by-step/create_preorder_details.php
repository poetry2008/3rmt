<?php
/*
   $Id$
*/    
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php 
  $url_action = isset($_GET['oID']) ? '?oID='.$_GET['oID'] : '';
  echo tep_draw_form('create_order', 'create_preorder_process.php'.$url_action, '', 'post', '', '') . tep_draw_hidden_field('customers_id', $account->customers_id); 
  tep_draw_hidden_field($customer_id);
?>
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
      </table><input type="hidden" name="fax" value="<?php echo $fax;?>"></td>
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
  </form>
  <tr><td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td class="pageHeading"><?php echo ADDING_TITLE; ?>:</td>
    </tr>
  </table>
  </td></tr>
<?php
if(isset($_GET['oID']) && $_GET['oID'] != ''){
  $oID = $_GET['oID'];
}else{
  $oID = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
}
$PHP_SELF = 'create_preorder.php';
if(isset($email_address) && $email_address != '' && isset($site_id) && $site_id != ''){
  $param_str = "&Customer_mail=$email_address&site_id=$site_id";
}
//start
?>
<?php
if(isset($_SESSION['create_preorder']['orders_products']) && !empty($_SESSION['create_preorder']['orders_products'])){
?>
<tr><td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
</tr>
<tr>
<td class="formAreaTitle"><?php echo ORDERS_PRODUCTS;?></td>
</tr>
</table>
</td></tr>
<tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><input type="hidden" name="oID" value="<?php echo $oID;?>">
  
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr style="background-color: #e1f9fe;">
            <td class="dataTableContent" colspan="2" width="35%">&nbsp;<?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_CURRENICY;?></td>
            <td class="dataTableContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER;?></td>
            </tr>

          <?php
          $currency_text  = DEFAULT_CURRENCY . ",1";
          $currency_array = explode(",", $currency_text);
          $currency = $currency_array[0];
          $currency_value = $currency_array[1];
          foreach ($_SESSION['create_preorder']['orders_products'] as $new_products_temp_add) {
            $orders_products_id = ''; 
            $RowStyle = "dataTableContent";
            $porducts_qty = isset($_SESSION['preorder_products'][$_GET['oID']]['qty']) ? $_SESSION['preorder_products'][$_GET['oID']]['qty'] : $new_products_temp_add['products_quantity'];
            echo '<tr>' . "\n" .
                 '<td class="' . $RowStyle . '" align="left" valign="top" width="20">&nbsp;'
                 .$porducts_qty."&nbsp;x</td>\n" .  '<td class="' . $RowStyle . '">' . $new_products_temp_add['products_name'] . "\n"; 
            // Has Attributes?
            if (sizeof($_SESSION['create_preorder']['orders_products_attributes']) > 0) { 
              $orders_products_attributes_array = $_SESSION['create_preorder']['orders_products_attributes'][$new_products_temp_add['products_id']];
              for ($j=0; $j<sizeof($orders_products_attributes_array); $j++) {
                echo '<div class="order_option_list"><small>&nbsp;<i><div
                  class="order_option_info"><div class="order_option_title"> - ' .str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($orders_products_attributes_array[$j]['option_info']['title'], array("'"=>"&quot;"))) . ': ' . 
                  '</div><div class="order_option_value">' . 
                  str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($orders_products_attributes_array[$j]['option_info']['value'], array("'"=>"&quot;"))); 
                echo '</div></div>';
                echo '<div class="order_option_price">';
                echo isset($_SESSION['preorder_products'][$_GET['oID']]['attr'][$j]) ? $_SESSION['preorder_products'][$_GET['oID']]['attr'][$j] : (int)$orders_products_attributes_array[$j]['options_values_price'];
                echo TEXT_MONEY_SYMBOL;
                echo '</div>';
                echo '</i></small></div>';
              }
            }

                echo '</td>' . "\n" .
                     '<td class="' . $RowStyle . '">' . $new_products_temp_add['products_model'] . '</td>' . "\n" .
                     '<td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($new_products_temp_add['products_tax']) . '%</td>' . "\n";
            $new_products_temp_add['products_price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['price']) ? $_SESSION['preorder_products'][$_GET['oID']]['price'] : $new_products_temp_add['products_price'];
            if($new_products_temp_add['products_price'] < 0){

              $orders_products_price = '<font color="#FF0000">'.str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['products_price']), 2)))).'</font>';
            }else{

              $orders_products_price = str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['products_price']), 2)))); 
            }
            echo '<td class="'.$RowStyle.'" align="right">'.$orders_products_price.TEXT_MONEY_SYMBOL.'</td>'; 
            $new_products_temp_add['final_price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['final_price']) ? $_SESSION['preorder_products'][$_GET['oID']]['final_price'] : $new_products_temp_add['final_price'];
            if($new_products_temp_add['final_price'] < 0){
              $orders_products_tax_price = '<font color="#FF0000">'.str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['final_price']),2)))).'</font>'; 
            }else{
              $orders_products_tax_price = str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add['final_price']),2)))); 
            }
                echo '<td class="' . $RowStyle . '" align="right">' .$orders_products_tax_price.TEXT_MONEY_SYMBOL ."\n" . '</td>' . "\n" . 
                     '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']), true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']), true, $currency, $currency_value);
            }
            echo '</div></td>' . "\n" . 
              '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($new_products_temp_add['final_price'] * $new_products_temp_add['products_quantity'], true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($new_products_temp_add['final_price'] * $new_products_temp_add['products_quantity'], true, $currency, $currency_value);
            }
            echo '</div></td>' . "\n" . 
                 '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]"><b>';
            if ($new_products_temp_add['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']) * $new_products_temp_add['products_quantity'], true, $currency, $currency_value)).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add['final_price'], $new_products_temp_add['products_tax']) * $new_products_temp_add['products_quantity'], true, $currency, $currency_value);
            }
            echo '</b></div></td>' . "\n" . 
                 '</tr>' . "\n";
          }
          ?>
        </table>
        </td>
        </tr>     
        </table>
        </td>
        </tr>
<?php
}
if(!isset($_SESSION['create_preorder']['orders_products']) || empty($_SESSION['create_preorder']['orders_products'])){
?> 
        <tr>
        <td width="100%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="formAreaTitle"><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
            </tr>
          </table>
        </td>
      </tr>
<?php
  // ############################################################################
  //   Get List of All Products
  // ############################################################################

    $result = tep_db_query("
        SELECT products_name, 
               p.products_id, 
               cd.categories_name, 
               ptc.categories_id 
        FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id 
        where pd.language_id = '" . (int)$languages_id . "' 
          and cd.site_id = '0'
          and pd.site_id = '0'
        ORDER BY categories_name");
    while($row = tep_db_fetch_array($result))
    {
      extract($row,EXTR_PREFIX_ALL,"db");
      $ProductList[$db_categories_id][$db_products_id] = $db_products_name;
      $CategoryList[$db_categories_id] = $db_categories_name;
      $LastCategory = $db_categories_name;
    }
    
    // ksort($ProductList);
    
    $LastOptionTag = "";
    $ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
    $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
    foreach($ProductList as $Category => $Products)
    {
      $ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
      $ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
      asort($Products);
      foreach($Products as $Product_ID => $Product_Name)
      {
        $ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
      }
      
      if($Category != $LastCategory)
      {
        $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
        $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
      }
    }
  
  
  // ############################################################################
  //   Add Products Steps
  // ############################################################################
  
    print "<tr><td><table border='0' width='100%' class='option_box_space' cellspacing='1' cellpadding='2'>\n";
    
    // Set Defaults
      if(!IsSet($add_product_categories_id))
      $add_product_categories_id = 0;

      if(!IsSet($add_product_products_id))
      $add_product_products_id = 0;
    
    // Step 1: Choose Category
      print "<tr>\n";
      print "<td class='dataTableContent' width='70'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 1:</b></td>\n";
      print "<td class='dataTableContent' valign='top'>";
      echo "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST'>";
      echo "<table>";
      echo "<tr>";
      print '<td width="150">';
      print ADDPRODUCT_TEXT_STEP1;
      print '</td>';
      print '<td>';
      echo ' ' . tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
      print "<input type='hidden' name='step' value='2'>";
      print '<td></tr>';
      print '</table>';
      print "</form>";
      print "</td>\n";
      print "<td class='dataTableContent'>";
      if($orders_products_list_error == true){
        print "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>".ORDERS_PRODUCT_ERROR."</font>";
      }
      echo "</td>\n";
      print "</tr>\n";

    // Step 2: Choose Product
    if(($step > 1) && ($add_product_categories_id > 0))
    {
      print "<tr>\n";
      print "<td class='dataTableContent'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 2: </b></td>\n";
      print "<td class='dataTableContent' valign='top'>";
      echo "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST'>";
      print "<table>";
      print "<tr><td width='150'>";
      print ADDPRODUCT_TEXT_STEP2."</td>";
      print "<td>";
      print "<select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
      $ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
      asort($ProductList[$add_product_categories_id]);
      foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
      {
      $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
      }
      $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
      print $ProductOptions;
      print "</select>\n";
      print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      print "<input type='hidden' name='step' value='3'>\n";
      print "<input type='hidden' name='cstep' value='1'>\n";
      print "</td>";
      print "</tr>";
      print "</table>";
      print "</form>";
      print "</td>\n";
      print "<td class='dataTableContent' align='right'>&nbsp;</td>\n";
      print "</tr>\n";
    }
    require('option/HM_Option.php');
    require('option/HM_Option_Group.php');
    $hm_option = new HM_Option();
    
    if (($step == 3) && ($add_product_products_id > 0) && isset($_POST['action_process'])) {
      if (!$hm_option->check()) {
        $step = 4; 
      }
    }
    // Step 3: Choose Options
    if(($step > 2) && ($add_product_products_id > 0))
    {
      $option_product_raw = tep_db_query("select products_cflag, belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'"); 
      $option_product = tep_db_fetch_array($option_product_raw); 
      if(!$hm_option->admin_whether_show($option_product['belong_to_option'], 1, $option_product['products_cflag']))
      {
        print "<tr>\n"; 
        print "<td class=\"dataTableContent\" valign='top'>&nbsp;<b>".ADDPRODUCT_TEXT_STEP." 3: </b></td>\n"; 
        print "<td class=\"dataTableContent\" valign='top' colspan='2'><i>".ADDPRODUCT_TEXT_OPTIONS_NOTEXIST."</i></td>\n"; 
        print "</tr>\n"; 
        $step = 4; 
      }
      else
      {
        
      
        $p_cflag = tep_get_cflag_by_product_id($add_product_products_id);
        print "<tr>";
        print "<td class='dataTableContent' valign='top'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td><td class='dataTableContent' valign='top'>";
        print "<div class=\"pro_option\">"; 
        print "<form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST' name='aform'>\n";
        
        print $hm_option->render($option_product['belong_to_option'], false, 0, '', '', $p_cflag); 
        
        print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
        print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
        print "<input type='hidden' name='step' value='3'>";
        print "<input type='hidden' name='action_process' value='1'>";
        print "</form>";
        print "</div>"; 
        print "</td>";
        print "</tr>\n";
        print "<tr><td colspan='3' align='right'><input type='button' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "' onclick='document.forms.aform.submit();'>";
        print "</td>\n";
        print "</tr>\n";
      }

    }

    // Step 4: Confirm
    if($step > 3)
    {
      echo "<tr><form action='$PHP_SELF?oID=$oID&action=add_product$param_str' method='POST' id='edit_order_id' name='edit_order_id'>\n";
      echo "<td class='dataTableContent'>&nbsp;<b>" . ADDPRODUCT_TEXT_STEP .  " 4: </b></td>";
      $products_num = isset($_POST['add_product_quantity']) ? $_POST['add_product_quantity'] : 1;
      $products_price = isset($_POST['add_product_price']) ? $_POST['add_product_price'] : 0;
      echo '<td class="dataTableContent" valign="top">&nbsp;' .
        ADDPRODUCT_TEXT_CONFIRM_QUANTITY . '<input id="add_product_quantity" name="add_product_quantity" size="2" value="'.$products_num.'" onkeyup="clearLibNum(this);">&nbsp;'.EDIT_ORDERS_NUM_UNIT.'&nbsp;&nbsp;'.TABLE_HEADING_UNIT_PRICE.'<input name="add_product_price" id="add_product_price" size="4" value="'.$products_price.'" onkeyup="clearNoNum(this);">&nbsp;'.EDIT_ORDERS_PRICE_UNIT.'&nbsp;&nbsp;&nbsp;'; 
      echo '</td>';
      echo '<td class="dataTableContent" align="right"><input type="button" value="' . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . '" onclick="submit_check();">';
       
      foreach ($_POST as $op_key => $op_value) {
        $op_pos = substr($op_key, 0, 3);
        if ($op_pos == 'op_') {
          echo "<input type='hidden' name='".$op_key."' value='".tep_parse_input_field_data(stripslashes($op_value), array("'" => "&quot;"))."'>"; 
        }
      }
      echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      echo "<input type='hidden' id='add_product_products_id' name='add_product_products_id' value='$add_product_products_id'>";
      echo "<input type='hidden' name='step' value='5'>";
      echo "</td>\n";
      echo "</form></tr>\n";
    }
    
    echo "</table></td></tr>\n";
}
//end
?>
  <tr>
    <td class="formAreaTitle"><br><?php echo CREATE_ORDER_COMMUNITY_TITLE_TEXT;?></td>
  </tr>
  <tr>
    <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
        <tr>
          <td class="main"><table border="0" cellspacing="0" cellpadding="2">

              <tr>
                <td class="main" valign="top">&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_TEXT;?></td>
                <td class="main">&nbsp;<textarea name='fax_flag' style='width:400px;height:42px;*height:40px;'><?php echo $fax;?></textarea>&nbsp;&nbsp;<?php echo CREATE_ORDER_COMMUNITY_SEARCH_READ;?></td>
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
