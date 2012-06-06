<?php
/*
 * ajax orders weight
 */
      require('includes/application_top.php');
      require('includes/languages/japanese/step-by-step/create_order.php');
      $action = $_GET['action'];

      $country_max_fee = 0; 
      $country_fee_max_array = array();
      $country_fee_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where status='0'");
      while($country_fee_array = tep_db_fetch_array($country_fee_query)){
        $country_fee_max_array[] = $country_fee_array['weight_limit'];
      }
      tep_db_free_result($country_fee_query);
      $country_max_fee = max($country_fee_max_array);

      $country_max_area = 0; 
      $country_area_max_array = array();
      $country_area_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where status='0'");
      while($country_area_array = tep_db_fetch_array($country_area_query)){

        $country_area_max_array[] = $country_area_array['weight_limit'];
      }
      tep_db_free_result($country_area_query);
      $country_max_area = max($country_area_max_array);

      $country_max_city = 0; 
      $country_city_max_array = array();
      $country_city_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where status='0'");
      while($country_city_array = tep_db_fetch_array($country_city_query)){

        $country_city_max_array[] = $country_city_array['weight_limit'];
      }
      tep_db_free_result($country_city_query);
      $country_max_city = max($country_city_max_array);

      $weight_count_limit = max($country_max_fee,$country_max_area,$country_max_city);      



switch($action){

case 'create_orders':

    $weight_count = 0;
    foreach($_POST['update_products'] as $update_key=>$update_value){
      $orders_products_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $update_key ."'");
      $orders_products_array = tep_db_fetch_array($orders_products_query);
      tep_db_free_result($orders_products_query);
      $products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $orders_products_array['products_id'] ."'");
      $products_weight_array = tep_db_fetch_array($products_weight_query);
      $weight_count += $products_weight_array['products_weight'] * $update_value['qty'];
      tep_db_free_result($products_weight_query);
    } 

      if($weight_count > $weight_count_limit){
        echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
      }else{
        echo '';
      }
      break;
case 'create_new_orders':
      // products weight
      $pro_weight_total = 0; //商品总重量
      $products_qty_array = array();
      foreach($update_products as $up_key=>$up_value){
        
        $up_weight_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $up_key ."'");
        $up_weight_array = tep_db_fetch_array($up_weight_query);
        tep_db_free_result($up_weight_query);
        $pro_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $up_weight_array['products_id'] ."'");
        $pro_weight_array = tep_db_fetch_array($pro_weight_query);
        $products_qty_array[$up_weight_array['products_id']] = $up_value['qty'];
        $pro_weight_total += $pro_weight_array['products_weight']*$up_value['qty'];
        tep_db_free_result($pro_weight_query);
      } 
      $weight_count = $pro_weight_total;
      if($weight_count > $weight_count_limit){
        echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
      }else{
        echo '';
      } 
      break;
case 'edit_orders':
    // products weight
    $shipping_array = array();
    foreach($update_products as $products_key=>$products_value){

      $shipping_products_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $products_key."'");
      $shipping_products_array = tep_db_fetch_array($shipping_products_query);
      tep_db_free_result($shipping_products_query);
      $shipping_array[] = array('id'=>$shipping_products_array['products_id'],'qty'=>$products_value['qty'],'final_price'=>$products_value['final_price']);
    }
    $shipping_weight_total = 0;
    foreach($shipping_array as $shipping_value){

      $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $shipping_value['id']);
      $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
      $shipping_weight_total += $shipping_value['qty'] * $shipping_fee_array['products_weight'];
      tep_db_free_result($shipping_fee_query);
    }

    // shipping fee
    $fixed_option_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where fixed_option!='0'");
    while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){

      $ship_fee_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
    }
    tep_db_free_result();

    $weight_count = $shipping_weight_total;      
     if($weight_count > $weight_count_limit){
        echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
      }else{
        echo '';
      } 
    break;
case 'edit_preorder':
        // products weight
        $pro_weight_total = 0; //商品总重量
       
        $pro_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $_POST['products_id'] ."'");
        $pro_weight_array = tep_db_fetch_array($pro_weight_query);
        $pro_weight_total = $pro_weight_array['products_weight']*$_POST['qty'];
        tep_db_free_result($pro_weight_query);

        $weight_count = $pro_weight_total;
        if($weight_count > $weight_count_limit){
          echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
        }else{
          echo '';
        } 
  break;
}
?>
