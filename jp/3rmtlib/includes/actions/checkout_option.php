<?php
  if (!tep_session_is_registered('customer_id')) {
  // if the customer is not logged on, redirect them to the login page
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if ($cart->count_contents(true) < 1) {
  // if there is nothing in the customers cart, redirect them to the shopping cart page
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }
  
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
  // Stock Check
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock((int)$products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        break;
      } else {
        $n_products_id = (int)$products[$i]['id'];
        $n_products_sum = 0;
        for($j=0;$j<$n;$j++){
          if($n_products_id == (int)$products[$j]['id']){
            $n_products_sum += intval($products[$j]['quantity']);
          }
        }
        if(tep_check_stock((int)$products[$i]['id'], $n_products_sum)){
          tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        }
      }
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_OPTION);
  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART,'','SSL'));
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CHECKOUT_OPTION,'','SSL'));
  
  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');
  $hm_option = new HM_Option();
  
  if ($_POST['action'] == 'process') {
    if (!$hm_option->check(1)) {
      //验证option信息是否填写正确 
      $option_array = array(); 
      foreach ($_POST as $p_key => $p_value) {
        $op_pos = strpos($p_key, 'op_');
        if ($op_pos !== false) {
          $cart_products_id = substr($p_key, 0, $op_pos-1); 
          $o_key_str = substr($p_key, $op_pos+3); 
          
          $p_tmp_value = str_replace(' ', '', $p_value);
          $p_tmp_value = str_replace('　', '', $p_value);
          if ($p_tmp_value != '') {
            $option_array[$cart_products_id][$o_key_str] = str_replace('<BR>', '<br>', stripslashes($p_value)); 
          } else {
            $option_array[$cart_products_id][$o_key_str] = MSG_TEXT_NULL; 
          }
        }
      }
      if (!empty($option_array)) {
        $cart->add_checkout_option($option_array); 
      }
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }
?>
