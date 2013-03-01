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
    if (empty($products)) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
    }
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
    //判断所购买的商品是否有登录之后的属性 
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));  
  } else {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));  
  }
