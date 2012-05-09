<?php
  // if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  // if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }
  
// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
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

  
  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');
  $hm_option = new HM_Option();
  if (tep_check_also_products_attr()) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));  
  } else {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));  
  }
