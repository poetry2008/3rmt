<?php
/*
  $Id$
*/
?>
<!-- order_details -->
<?php
  echo '  <tr>' . "\n";

  $colspan = 4;

  if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan++;
  }

  echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_QUANTITY . '</td>' . "\n";
  echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_IMAGE . '</td>' . "\n";

  if ((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    //$colspan++;
    //echo '    <td class="tableHeading">' . TABLE_HEADING_MODEL . '</td>' . "\n";
  }

  echo '    <td class="tableHeading">' . TABLE_HEADING_PRODUCTS . '</td>' . "\n";

  if (!strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan++;
    echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_TAX . '</td>' . "\n";
  }

  echo '    <td align="right" class="tableHeading">' . TABLE_HEADING_TOTAL . '</td>' . "\n" .
       '    <td align="center" class="tableHeading">' . TABLE_HEADING_OPERATE . '</td>' . "\n" .
       /*
       '  </tr>' . "\n" .
       '  <tr>' . "\n" .
       '    <td colspan="' . $colspan . '" style=" background: #ddd; line-height: 0px; font-size: 0px;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
       */
       '  </tr>' . "\n";

  //检查商品的OPTION是否改动
  $check_products_option = tep_check_less_product_option();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    echo '  <tr>' . "\n";

// Delete box only for shopping cart
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td align="center" height="25"  style=" background:#dbfdff">' . tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']) . '</td>' . "\n";
    }

    $product_info = tep_get_product_by_id((int)$products[$i]['id'], SITE_ID, $languages_id,true,'shopping_cart');

// Quantity box or information as an input box or text
    $disabled = in_array($products[$i]['id'],$check_products_option) ? ' disabled="disabled"' : '';    
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      // add up and down 
      $p_a_quan = tep_get_quantity($products[$i]['id'],true);
      $p_id = 'quantity_'.$products[$i]['id'];
      $product_price_after_tax = tep_add_tax($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id'])); 
      echo tep_draw_hidden_field('unit_price_'.$products[$i]['id'], $product_info['products_price'], 'id="unit_price_'.$products[$i]['id'].'"');
      echo tep_draw_hidden_field('small_sum_' . $products[$i]['id'], $product_info['products_small_sum'], ' id="small_sum_'.$products[$i]['id'].'"');
      echo tep_draw_hidden_field('final_price', tep_add_tax($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id'])), 'id="id_'.$products[$i]['id'].'"');
      echo '<td align="center" style=" background:#f4f4f4;padding-left:10px;padding-right:20px;">';
      echo '<table>'; 
      echo '<tr>'; 
      echo '<td>'; 
      $origin_small = ''; 
      if (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id'])) {
        $origin_small = tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']); 
      }
      if(in_array($products[$i]['id'],$check_products_option)){
        echo tep_draw_hidden_field('cart_quantity[]',$products[$i]['quantity']); 
        echo tep_draw_hidden_field('cart_products_id_list[]',$products[$i]['id']);
      }
      echo tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4" maxlength="4" class="input_text_short" id="quantity_'.$products[$i]['id'].'" onkeypress="return key(event);" onblur="money_blur_update(\'quantity_'.$products[$i]['id'].'\', \''.$products[$i]['quantity'].'\', \''.$origin_small.'\')"'.$disabled);
      echo   tep_draw_hidden_field('products_id[]', $products[$i]['id']);
      echo tep_draw_hidden_field('option_info[]', serialize($products[$i]['op_attributes'])); 
      
      $sh_option_info = array();
      foreach ($products[$i]['add_op_attributes'] as $hp_key => $hp_value) {
        if ($hp_value['price'] != '0.0000') {
          $sh_option_info[] = number_format($hp_value['price']); 
        }
      } 
      $sh_option_str = ''; 
      if (!empty($sh_option_info)) {
        $sh_option_str = implode(',', $sh_option_info); 
      }
      echo tep_draw_hidden_field('h_op_'.$products[$i]['id'], $sh_option_str, 'id=h_op_'.$products[$i]['id']);
      
      echo '</td>'; 
      echo '<td>'; 
      if(!in_array($products[$i]['id'],$check_products_option)){
      ?>
      <a style="display:block;" href="javascript:void(0)" onclick="change_num('<?php echo $p_id;?>', 'up',1,<?php echo $p_a_quan;?>, '<?php echo $products[$i]['quantity'];?>', '<?php echo $origin_small;?>'); return false;"> 
      <img src="images/ico/nup.gif" alt="up"> 
      </a> 
      <a style="display:block;" href="javascript:void(0)" onclick="change_num('<?php echo $p_id;?>', 'down',1,<?php echo $p_a_quan;?>, '<?php echo $products[$i]['quantity'];?>', '<?php echo $origin_small;?>'); return false;"> 
      <img src="images/ico/ndown.gif" alt="down"> 
      </a> 
      <?php
      }else{

        echo '<div style="display:block"><img src="images/nup.gif" style="vertical-align:bottom;"></div>';
        echo '<div style="display:block"><img src="images/ndown.gif" style="vertical-align:top;"></div>';
      }
      echo '</td>';
      echo '<td><font style="font-size:10px">'.NUM_UNIT_TEXT.'</font></td>';
      echo '</tr>'; 
      echo '</table>'; 
      echo (!empty($product_info['products_attention_1_3']) &&
          tep_get_full_count_in_order2($products[$i]['quantity'],
            $products[$i]['id']) ? '<span id="one_price_show_'.$products[$i]['id'].'" style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>': '') .  "\n";
      echo (!empty($product_info['products_attention_1_3']) &&
          tep_get_full_count_in_order2($products[$i]['quantity'],
            $products[$i]['id']) ? '<span id="one_price_'.$products[$i]['id'].
          '" style="display:none">'.
          tep_get_full_count_in_order2($products[$i]['quantity'],
            $products[$i]['id'],true) .'</span>': '') . '</td>' . "\n";
    } else {
      echo '    <td align="center" class ="main" style=" background:#FFFFFF;padding-left:10px;padding-right:20px;">' . $products[$i]['quantity'] . NUM_UNIT_TEXT;
      echo (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) ? '<span style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>' : '');
      echo '</td>' . "\n";
    }
    //add products image 
    echo '<td align="center" class="main" style=" background:#f4f4f4;padding-left:10px;padding-right:20px;">';
    $pimage_query = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".intval($products[$i]['id'])."'"); 
    $pimage_res = tep_db_fetch_array($pimage_query); 

    if ($pimage_res) {
      if (!empty($pimage_res['products_image'])) {
        echo tep_image(DIR_WS_IMAGES . 'products/' . $pimage_res['products_image'], $products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); 
      }
    }
    echo '</td>';
// Model
    if ((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td class="main" style=" background:#dbfdff">1<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['model'] . '</a></td>' . "\n";
    }
  
// Product name, with or without link
    $stock_link_single = false; 
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      $cart_pro_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".(int)$products[$i]['id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
      $cart_pro_status = tep_db_fetch_array($cart_pro_status_raw);
      if ($cart_pro_status['products_status'] == 0 || $cart_pro_status['products_status'] == 3) {
        $stock_link_single = true; 
        echo '    <td class="main" style=" background:#f4f4f4; width:150px;">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?$products[$i]['model'] . '<br>':'').'<b>' . $products[$i]['name'] . '</b>';
      } else {
        echo '    <td class="main" style=" background:#f4f4f4; width:150px;">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$products[$i]['id']) . '">' . $products[$i]['model'] .  '</a><br>':'').'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$products[$i]['id']) . '"><b>' . $products[$i]['name'] . '</b></a>';
      }
    } else {
      echo '    <td class="main" style=" background:#FFFFFF">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$products[$i]['id']) . '">' . $products[$i]['model'] . '</a><br>':'').'<b>' . $products[$i]['name'] . '</b>';
    }
        if(in_array($products[$i]['id'],$check_products_option)){

      echo '<br>'.TEXT_PRODUCTS_OPTION_CHANGE_ERROR;
    }

// Display marker if stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (STOCK_CHECK == 'true') {
        echo $stock_check = tep_check_stock((int)$products[$i]['id'], $products[$i]['quantity'], $stock_link_single);
        if ($stock_check) $any_out_of_stock = 1;
        if(!isset($stock_check)||$stock_check == ''){
          $n_products_id = (int)$products[$i]['id'];
          $n_products_sum = 0;
          for($j=0;$j<$n;$j++){
            if($n_products_id == (int)$products[$j]['id']){
              $n_products_sum += intval($products[$j]['quantity']);
            }
          }
        echo $stock_check = tep_check_stock((int)$products[$i]['id'], $n_products_sum, $stock_link_single);
        if ($stock_check) $any_out_of_stock = 1;
        }
      }
    }

    if($products_error == true && $stock_check == ''){

      echo '<span class="markProductOutOfStock"><a style="color:#CC0033" href="'.tep_href_link('open.php', 'products_name='.urlencode($products[$i]['name']), 'SSL').'">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</a></span>';
    }
// Product options names
    $attributes_exist = ((isset($products[$i]['add_op_attributes'])) ? 1 : 0);

    if ($attributes_exist == 1) {
      foreach ($products[$i]['add_op_attributes'] as $ap_key => $ap_value) {
        echo '<br><small><i> - ' . $ap_value['option_name'] . ': ' .  str_replace(array("<br>", "<BR>"), '', $ap_value['option_value']) . '</i></small>';
      }
    }

    echo '</td>' . "\n";

// Tax (not in shopping cart, tax rate may be unknown)
    if (!strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td align="center" class="main" style=" background:#fff">' . number_format($products[$i]['tax'], TAX_DECIMAL_PLACES) . '%</td>' . "\n";
    }

// Product price  
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      //echo '    <td align="right" class="main" style=" background:#dbfdff"><span id="pri_'.$products[$i]['id'] .'"><b>' .  $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</b></span>';
      // edit total 
      echo '    <td align="right" class="main" style="background:#f4f4f4"><br><span id="pri_'.$products[$i]['id'] .'">';
      if ($products[$i]['price'] < 0) {
        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity'])).'</font>'.JPMONEY_UNIT_TEXT;
      } else {
        echo $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']);
      }
      echo '</span>';
    } else {
      echo '    <td align="right" class="main" style=" background:#FFFFFF"><b>' . $currencies->display_price($products[$i]['price'], $products[$i]['tax'], $products[$i]['quantity']) . '</b>';
    }

// Product options prices
    if ($attributes_exist == 1) {
      foreach ($products[$i]['add_op_attributes'] as $opa_key => $opa_value) {
        // Check Options stock - add ds-style
        if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
          if (STOCK_CHECK == 'true') {
          //echo $stock_check = tep_check_opstock($products[$i][$option]['products_at_quantity'], $products[$i]['quantity']);
      if ($stock_check) $any_out_of_stock = 1;
        }
        }
    
    if ($opa_value['price'] != 0) {
          if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
            if ($opa_value['price'] < 0) {
              echo '<br><small><i><font color="#ff0000">' .  str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($opa_value['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity'])) . '</font>'.JPMONEY_UNIT_TEXT.'</i></small>';
            } else {
              echo '<br><small><i>' . $currencies->display_price($opa_value['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</i></small>';
            }
          } else {
            echo '<br><small><i>' . $currencies->display_price($opa_value['price'], $products[$i]['tax'], $products[$i]['quantity']) . '</i></small>';
          }
        } else {
// Keep price aligned with corresponding option
          echo '<br><small><i>&nbsp;</i></small>';
        }
      }
    }

  
    echo '</td>' . "\n"; 
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART) && !in_array($products[$i]['id'],$check_products_option)) {
      echo '<td align="center" style=" background:#f4f4f4;"><a class="button_delete02" href="'.tep_href_link(FILENAME_SHOPPING_CART, 'products_id='.$products[$i]['id'].'&amp;action=delete', 'SSL').'">'.TEXT_DEL_LINK.'</a></td>'; 
    }else{
      echo '<td align="center" style=" background:#f4f4f4;">&nbsp;</td>'; 
    }
    echo '  </tr>' . "\n";
     
    /*
 echo
       '  <tr>' . "\n" .
       '    <td colspan="' . $colspan . '" style=" background:#ddd; line-height: 0px; font-size: 0px;">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
       '  </tr>' . "\n";
       */
  
  }
?>
<!-- order_details_eof -->
