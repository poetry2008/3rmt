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

  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    echo '  <tr>' . "\n";

// Delete box only for shopping cart
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td align="center" height="25"  style=" background:#dbfdff">' . tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']) . '</td>' . "\n";
    }

    $product_info = tep_get_product_by_id($products[$i]['id'], SITE_ID, $languages_id,true,'shopping_cart');

// Quantity box or information as an input box or text
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      // add up and down 
      $a_quantity_query = tep_db_query("select products_real_quantity + products_virtual_quantity as products_quantity from ".TABLE_PRODUCTS." where products_id = '".$products[$i]['id']."'"); 
      $a_quantity = tep_db_fetch_array($a_quantity_query); 
      $p_a_quan = $a_quantity['products_quantity'];
      $p_id = 'quantity_'.$products[$i]['id'];
      $product_price_after_tax = tep_add_tax($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id'])); 
      echo tep_draw_hidden_field($products[$i]['id'].'_unit_price', $product_price_after_tax, 'id="unit_price_'.$products[$i]['id'].'"');
      echo tep_draw_hidden_field('final_price', tep_add_tax($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id'])), 'id="id_'.$products[$i]['id'].'"');
      echo '<td align="center" style=" background:#FFFFFF;padding-left:10px;padding-right:20px;">';
      echo '<table>'; 
      echo '<tr>'; 
      echo '<td>'; 
      echo tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4" maxlength="4" class="input_text_short" id="quantity_'.$products[$i]['id'].'" onblur="update_cart(this);" onkeypress="return key(event);"');
      echo tep_draw_hidden_field('products_id[]', $products[$i]['id']);
      echo '</td>'; 
      echo '<td>'; 
      ?>
      <a style="display:block;" href="javascript:void(0)" onclick="change_num('<?php echo $p_id;?>', 'up',1,<?php echo $p_a_quan;?>); return false;"> 
      <img src="images/ico/nup.gif" alt="up"> 
      </a> 
      <a style="display:block;" href="javascript:void(0)" onclick="change_num('<?php echo $p_id;?>', 'down',1,<?php echo $p_a_quan;?>); return false;"> 
      <img src="images/ico/ndown.gif" alt="down"> 
      </a> 
      <?php
      echo '</td>';
      echo '<td><font style="font-size:10px">個</font></td>';
      echo '</tr>'; 
      echo '</table>'; 
      echo (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) ? '<span style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>': '') . '</td>' . "\n";
    } else {
      echo '    <td align="center" class ="main" style=" background:#FFFFFF;padding-left:10px;padding-right:20px;">' . $products[$i]['quantity'] . '個' ;
      echo (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) ? '<span style="font-size:10px">'. tep_get_full_count_in_order2($products[$i]['quantity'], $products[$i]['id']) .'</span>' : '');
      echo '</td>' . "\n";
    }
    //add products image 
    echo '<td align="center" class="main" style=" background:#FFFFFF;padding-left:10px;padding-right:20px;">';
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
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td class="main" style=" background:#FFFFFF; width:150px;">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '">' . $products[$i]['model'] . '</a><br>':'').'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '"><b>' . $products[$i]['name'] . '</b></a>';
    } else {
      echo '    <td class="main" style=" background:#FFFFFF">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '">' . $products[$i]['model'] . '</a><br>':'').'<b>' . $products[$i]['name'] . '</b>';
    }

// Display marker if stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (STOCK_CHECK == 'true') {
        echo $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if ($stock_check) $any_out_of_stock = 1;
        if(!isset($stock_check)||$stock_check == ''){
          $n_products_id = tep_get_prid($products[$i]['id']);
          $n_products_sum = 0;
          for($j=0;$j<$n;$j++){
            if($n_products_id == tep_get_prid($products[$j]['id'])){
              $n_products_sum += intval($products[$j]['quantity']);
            }
          }
        echo $stock_check = tep_check_stock($products[$i]['id'], $n_products_sum);
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
      echo '    <td align="center" class="main" style=" background:#FFFFFF">' . number_format($products[$i]['tax'], TAX_DECIMAL_PLACES) . '%</td>' . "\n";
    }

// Product price  
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      //echo '    <td align="right" class="main" style=" background:#dbfdff"><span id="pri_'.$products[$i]['id'] .'"><b>' .  $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</b></span>';
      // edit total 
      echo '    <td align="right" class="main" style="background:#FFFFFF"><span id="pri_'.$products[$i]['id'] .'">'.  $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</span>';
    } else {
      echo '    <td align="right" class="main" style=" background:#FFFFFF"><b>' . $currencies->display_price($products[$i]['price'], $products[$i]['tax'], $products[$i]['quantity']) . '</b>';
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

  
    echo '</td>' . "\n"; 
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '<td align="center" style=" background:#FFFFFF;"><a class="button_delete02" href="'.tep_href_link(FILENAME_SHOPPING_CART, 'products_id='.$products[$i]['id'].'&amp;action=delete', 'SSL').'">'.TEXT_DEL_LINK.'</a></td>'; 
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
