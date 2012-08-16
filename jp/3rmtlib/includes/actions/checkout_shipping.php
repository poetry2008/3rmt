<?php
require('address/AD_Option.php');
require('address/AD_Option_Group.php');

$hm_option = new AD_Option();
$check_before_pos = strpos($_SERVER['HTTP_REFERER'], 'login.php');
if ($check_before_pos !== false || !isset($_SERVER['HTTP_REFERER'])) {
  if ($cart->count_contents() > 0) {
    $c_products_list = $cart->get_products();  
    $check_op_single = false; 
    require('option/HM_Option.php'); 
    require('option/HM_Option_Group.php');
    $op_option = new HM_Option(); 
    foreach ($c_products_list as $ch_key => $ch_value) {
       $op_pro_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".(int)$ch_value['id']."'"); 
       $op_pro_res = tep_db_fetch_array($op_pro_raw);
       if ($op_pro_res) {
         if (!empty($op_pro_res['belong_to_option'])) {
           if ($op_option->check_old_symbol_show($op_pro_res['belong_to_option'], true)) {
             $check_op_single = true;
             break;
           }
         }
       }
    }
    if ($check_op_single) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));
    } 
  }
}
?>
