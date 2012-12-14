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
  //products shipping fee  
  $fixed_option_list_array = array();
  $fixed_option_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where status='0' and fixed_option!='0'");
  while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){

    $fixed_option_list_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
  }
  tep_db_free_result($fixed_option_query);

  if(isset($_POST['ad_'.$fixed_option_list_array[3]])){

    $country_city_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where name='".$_POST['ad_'.$fixed_option_list_array[3]]."' and status='0'");
    $country_city_search_array = tep_db_fetch_array($country_city_search_query);
    tep_db_free_result($country_city_search_query);
    $weight_limit = $country_city_search_array['weight_limit'];

    if($weight_count > $weight_limit){
       echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[3]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
    }else{
       echo ''; 
    }
  }elseif(isset($_POST['ad_'.$fixed_option_list_array[2]])){
    $country_area_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where name='".$_POST['ad_'.$fixed_option_list_array[2]]."' and status='0'");
    $country_area_search_array = tep_db_fetch_array($country_area_search_query);
    tep_db_free_result($country_area_search_query);
    $weight_limit = $country_area_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[2]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
      
    }else{

      echo '';
    }
  }elseif(isset($_POST['ad_'.$fixed_option_list_array[1]])){

    $country_fee_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where name='".$_POST['ad_'.$fixed_option_list_array[1]]."' and status='0'");
    $country_fee_search_array = tep_db_fetch_array($country_fee_search_query);
    tep_db_free_result($country_fee_search_query);
    $weight_limit = $country_fee_search_array['weight_limit'];

    if($weight_count > $weight_limit){

       
      echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[1]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
      
    }else{

       echo '';
    }
  } 
      } 
      break;
case 'edit_orders':
    // products weight
    $shipping_array = array();
    foreach($update_products as $products_key=>$products_value){

      $products_session_list_array = explode('_',$products_key);
      if(count($products_session_list_array) <= 1){
        $shipping_products_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $products_key."'");
        $shipping_products_array = tep_db_fetch_array($shipping_products_query);
        tep_db_free_result($shipping_products_query);
      }else{
        $shipping_products_array['products_id'] = $_SESSION['new_products_list'][$_GET['oID']]['orders_products'][$products_session_list_array[1]]['products_id']; 
      }
      $shipping_array[] = array('id'=>$shipping_products_array['products_id'],'qty'=>$products_value['qty'],'final_price'=>$products_value['final_price']);
    }
    $shipping_weight_total = 0;
    foreach($shipping_array as $shipping_value){

      $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $shipping_value['id']);
      $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
      $shipping_weight_total += $shipping_value['qty'] * $shipping_fee_array['products_weight'];
      tep_db_free_result($shipping_fee_query);
    }

    $weight_count = $shipping_weight_total;      
     if($weight_count > $weight_count_limit){
        echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
     }else{

  //products shipping fee  
  $fixed_option_list_array = array();
  $fixed_option_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where status='0' and fixed_option!='0'");
  while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){

    $fixed_option_list_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
  }
  tep_db_free_result($fixed_option_query);

  if(isset($_POST['ad_'.$fixed_option_list_array[3]])){

    $country_city_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where name='".$_POST['ad_'.$fixed_option_list_array[3]]."' and status='0'");
    $country_city_search_array = tep_db_fetch_array($country_city_search_query);
    tep_db_free_result($country_city_search_query);
    $weight_limit = $country_city_search_array['weight_limit'];

    if($weight_count > $weight_limit){
       echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[3]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
    }else{
       echo ''; 
    }
  }elseif(isset($_POST['ad_'.$fixed_option_list_array[2]])){
    $country_area_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where name='".$_POST['ad_'.$fixed_option_list_array[2]]."' and status='0'");
    $country_area_search_array = tep_db_fetch_array($country_area_search_query);
    tep_db_free_result($country_area_search_query);
    $weight_limit = $country_area_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[2]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
      
    }else{

      echo '';
    }
  }elseif(isset($_POST['ad_'.$fixed_option_list_array[1]])){

    $country_fee_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where name='".$_POST['ad_'.$fixed_option_list_array[1]]."' and status='0'");
    $country_fee_search_array = tep_db_fetch_array($country_fee_search_query);
    tep_db_free_result($country_fee_search_query);
    $weight_limit = $country_fee_search_array['weight_limit'];

    if($weight_count > $weight_limit){

       
      echo PRODUCTS_WEIGHT_ERROR_ONE.$_POST['ad_'.$fixed_option_list_array[1]].PRODUCTS_WEIGHT_ERROR_TWO."\n".PRODUCTS_WEIGHT_ERROR_THREE.$weight_limit.PRODUCTS_WEIGHT_ERROR_FOUR;
      
    }else{

       echo '';
    }
  }
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
case 'edit_new_preorder':

      // products weight   
      $pro_weight_total = 0; //商品总重量
      
      $products_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $_POST['products_id'] ."'");
      $products_array = tep_db_fetch_array($products_query);
      tep_db_free_result($products_query);

      $pro_weight_total = $products_array['products_weight'] * $_POST['qty'];

      $weight_count = $pro_weight_total;

      if($weight_count > $weight_count_limit){
          echo CREATE_ORDER_PRODUCTS_WEIGHT.$weight_count_limit.CREATE_ORDER_PRODUCTS_WEIGHT_ONE;
      }else{
          echo '';
      }
      break;
}
?>
