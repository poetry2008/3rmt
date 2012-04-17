<?php
/*
  $Id$
*/
?>
<!-- order_details -->
<?php
  echo '  <tr>' . "\n";

  $colspan = 3;

  if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan +=2;
  }

  echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_QUANTITY . '</td>' . "\n";

  if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)){
    echo '    <td align="center" class="tableHeading">' . TABLE_HEADING_IMAGE. '</td>' . "\n";
  }

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
       '  </tr>' . "\n" .
       '  <tr>' . "\n" .
       '    <td colspan="' . $colspan . '" bgcolor="#bdced5" height="1">' . '</td>' . "\n" .
       '  </tr>' . "\n";

  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    echo '  <tr>' . "\n";

// Delete box only for shopping cart
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td align="center" height="25">' . tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']) . '</td>' . "\n";
    }
    // ccdd
    $product_info = tep_get_product_by_id($products[$i]['id'], SITE_ID, $languages_id,true,'shopping_cart');

// Quantity box or information as an input box or text
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      $product_price_after_tax = tep_add_tax($products[$i]['price'],tep_get_tax_rate($products[$i]['tax_class_id']));
      echo '<td align="center" style="padding-left:10px;padding-right:20px;">';
      echo '<table><tr><td colspan="3"><table><tr><td>';
      echo tep_draw_hidden_field('unit_price_' . $products[$i]['id'],
          $product_info['products_price'], 'id="unit_price_'.$products[$i]['id'].'"');
      echo tep_draw_hidden_field('small_sum_' . $products[$i]['id'], $product_info['products_small_sum'], ' id="small_sum_'.$products[$i]['id'].'"');
      echo tep_draw_hidden_field('final_price', tep_add_tax($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id'])), 'id="id_'.$products[$i]['id'].'"');
      echo tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'],
          'size="4" maxlength="4" class="input_text_short"
          id="quantity_'.$products[$i]['id'] . '"') .
        tep_draw_hidden_field('products_id[]', $products[$i]['id']);
      echo '</td>';
      echo '<td><div class="top_and_bottom">';
      echo '<a onclick="change_num(\'quantity_'.$products[$i]['id'].'\',\'up\',1,'.
        $product_info['products_quantity'].')" style="display:block"><img src="images/nup.gif" style="vertical-align:bottom;"></a>';
      echo '<a onclick="change_num(\'quantity_'.$products[$i]['id'].'\',\'down\',1,'.
        $product_info['products_quantity'].')" style="display:block"><img src="images/ndown.gif" style="vertical-align:top;"></a>';
      echo '</div></td><td>';
      echo ' <font style="font-size:10px">個</font>';
      echo '</td></tr></table></td></tr><tr><td colspan="3" width="90">';
      echo (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) ? '<span style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>': '') . '</td>' . "\n";
      echo '</td></tr></table>';
      echo  '</td>' . "\n";
    } else {
      echo '    <td align="center" class ="main" style=" background:#FFFFFF;padding-left:10px;padding-right:20px;">' . $products[$i]['quantity'] . '個' ;
      echo (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) ? '<span style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>' : '');
      echo '</td>' . "\n";
    }

    //image
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '<td align="center" valign="middle">';
      if (!empty($product_info['products_image']))
      {
        echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'],
            $product_info['products_name'],60, 60);
      }
      else if (!empty($product_info['products_image2']))
      {
        echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image2'],
            $product_info['products_name'],60, 60);
      }
      else if (!empty($product_info['products_image3']))
      {
        echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image3'],
            $product_info['products_name'],60, 60);
      }
      else
      {}
      echo '</td>';
    }
// Model
    if ((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td class="main" style="">1<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['model'] . '</a></td>' . "\n";
    }
  
// Product name, with or without link
    $stock_link_single = false; 
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      $cart_pro_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$products[$i]['id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
      $cart_pro_status = tep_db_fetch_array($cart_pro_status_raw);
      if ($cart_pro_status['products_status'] == 0  || $cart_pro_status['products_status'] == 3) {
        $stock_link_single = true; 
        echo '    <td class="main" style="">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?$products[$i]['model'] . '<br>':'').'<b>' . $products[$i]['name'] . '</b>';
      } else {
        echo '    <td class="main" style="">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '">' . $products[$i]['model'] . '</a><br>':'').'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '"><b>' . $products[$i]['name'] . '</b></a>';
      }
    } else {
      echo '    <td class="main" style="">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '">' . $products[$i]['model'] . '</a><br>':'').'<b>' . $products[$i]['name'] . '</b>';
    }

// Display marker if stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (STOCK_CHECK == 'true') {
        echo $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity'], $stock_link_single);
        if ($stock_check) $any_out_of_stock = 1;
        if(!isset($stock_check)||$stock_check == ''){
          $n_products_id = tep_get_prid($products[$i]['id']);
          $n_products_sum = 0;
          for($j=0;$j<$n;$j++){
            if($n_products_id == tep_get_prid($products[$j]['id'])){
              $n_products_sum += intval($products[$j]['quantity']);
            }
          }
        echo $stock_check = tep_check_stock($products[$i]['id'], $n_products_sum, $stock_link_single);
        if ($stock_check) $any_out_of_stock = 1;
        }
      }
    }

// Product options names
    $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);

    if ($attributes_exist == 1) {
      reset($products[$i]['attributes']);
      while (list($option, $value) = each($products[$i]['attributes'])) {
        echo '<br><small><i> - ' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';
      }
    }

    echo '</td>' . "\n";

// Tax (not in shopping cart, tax rate may be unknown)
    if (!strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td align="center" class="main" style="">' . number_format($products[$i]['tax'], TAX_DECIMAL_PLACES) . '%</td>' . "\n";
    }

// Product price  
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      echo '    <td align="right" class="main" style=""><b id="pri_'.  $products[$i]['id'] .'" >';
      if ($products[$i]['price'] < 0) {
        echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity'])).'</font>'.JPMONEY_UNIT_TEXT;
      } else {
        echo $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) ;
      }
      echo '</b>';
    } else {
      echo '    <td align="right" class="main" style=""><b>' . $currencies->display_price($products[$i]['price'], $products[$i]['tax'], $products[$i]['quantity']) . '</b>';
    }

// Product options prices
    if ($attributes_exist == 1) {
      reset($products[$i]['attributes']);
      while (list($option, $value) = each($products[$i]['attributes'])) {
        // Check Options stock - add ds-style
        if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
          if (STOCK_CHECK == 'true') {
          echo $stock_check = tep_check_opstock($products[$i][$option]['products_at_quantity'], $products[$i]['quantity']);
      if ($stock_check) $any_out_of_stock = 1;
        }
        }
    
    if ($products[$i][$option]['options_values_price'] != 0) {
          if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
            echo '<br><small><i>' . $products[$i][$option]['price_prefix'] . $currencies->display_price($products[$i][$option]['options_values_price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</i></small>';
          } else {
            echo '<br><small><i>' . $products[$i][$option]['price_prefix'] . $currencies->display_price($products[$i][$option]['options_values_price'], $products[$i]['tax'], $products[$i]['quantity']) . '</i></small>';
          }
        } else {
// Keep price aligned with corresponding option
          //echo '<br><small><i>&nbsp;</i></small>';
        }
      }
    }
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '<td align="center"><a class="button_delete02" href="'.tep_href_link(FILENAME_SHOPPING_CART, 'products_id='.$products[$i]['id'].'&action=delete', 'SSL').'">'.TEXT_DEL_LINK.'</a></td>'; 
    }
    echo '</tr>';
    echo '<tr><td colspan="' . $colspan . '" bgcolor="#bdced5" height="1">' . '</td></tr>';
  }
 echo '</table>' . "\n";
?>
<!-- order_details_eof -->
