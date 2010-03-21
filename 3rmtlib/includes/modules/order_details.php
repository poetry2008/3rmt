<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- order_details -->
<?php
  echo '  <tr>' . "\n";

  $colspan = 3;

  if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
    $colspan +=2;
    //echo '    <td align="center" class="smallText" height="25"><b>' . TABLE_HEADING_REMOVE . '</b></td>' . "\n";
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
       '    <td colspan="' . $colspan . '" bgcolor="#bdced5">' . tep_draw_separator('pixel_trans.gif', '1', '1') . '</td>' . "\n" .
       '  </tr>' . "\n";

  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    echo '  <tr>' . "\n";

// Delete box only for shopping cart
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      //echo '    <td align="center" height="25">' . tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']) . '</td>' . "\n";
    }
    // ccdd
    /*
    $_product_info_query = tep_db_query("
        select p.products_image, 
               p.products_image2, 
               p.products_image3, 
               p.products_id, 
               pd.products_name, 
               pd.products_attention_1,
               pd.products_attention_2,
               pd.products_attention_3,
               pd.products_attention_4,
               pd.products_attention_5,
               pd.products_description, 
               p.products_model, 
               p.products_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               pd.products_url, 
               p.products_price, 
               p.products_tax_class_id, 
               p.products_date_added, 
               p.products_date_available, 
               p.manufacturers_id, 
               p.products_bflag, 
               p.products_cflag, 
               p.products_small_sum 
        from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd 
        where p.products_status = '1' 
          and p.products_id = '" . $products[$i]['id'] . "' 
          and pd.products_id = p.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and pd.site_id = ".SITE_ID
    );
    */
    // ccdd
    /*
    tep_db_query("
        update " . TABLE_PRODUCTS_DESCRIPTION . " 
        set products_viewed = products_viewed+1 
        where products_id = '" .  (int)$HTTP_GET_VARS['products_id'] . "' 
        and language_id = '" . $languages_id . "' 
        and site_id = '".SITE_ID."'
    ");
    */
    //$product_info = tep_db_fetch_array($_product_info_query);
    $product_info = tep_get_product_by_id($products[$i]['id'], SITE_ID, $languages_id);
    $data1 = explode("//", $product_info['products_attention_1']);

// Quantity box or information as an input box or text
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      $product_price_after_tax = tep_add_tax($products[$i]['price'],tep_get_tax_rate($products[$i]['tax_class_id']));
      echo '<td align="center" style="padding-left:10px;padding-right:20px;">';
      echo '<table><tr><td colspan="3"><table><tr><td>';
      echo tep_draw_hidden_field('unit_price_' . $products[$i]['id'],
          $product_price_after_tax, 'id="unit_price_'.$products[$i]['id'].'"');
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
      echo  (!empty($data1[0]) && strlen($data1[1])<=50 && tep_get_full_count_in_order($products[$i]['quantity'], $data1[1]) ?  '<span style="font-size:10px">'.  tep_get_full_count_in_order($products[$i]['quantity'], $data1[1]) .'</span>': '');
      echo '</td></tr></table>';
      echo  '</td>' . "\n";
    } else {
      //echo '    <td align="center" class ="main" style="">' . $products[$i]['quantity'] . (!empty($data1[0]) && strlen($data1[1])<=30 ? '<span style="font-size:10px">x'. $data1[1] .'</span>' : '') . '</td>' . "\n";
      echo '    <td align="center" class ="main" style="padding-left:10px;padding-right:20px;">' . $products[$i]['quantity'] . '個' . (!empty($data1[0]) && strlen($data1[1])<=50 && tep_get_full_count_in_order($products[$i]['quantity'], $data1[1]) ? '<span style="font-size:10px">'. $data1[1] .'</span>' : '') . '</td>' . "\n";
    }

    //image
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '<td align="center" valign="middle">';
      if (!empty($product_info['products_image']))
      {
        echo tep_image(DIR_WS_IMAGES . $product_info['products_image'],
            $product_info['products_name'],60, 60);
      }
      else if (!empty($product_info['products_image2']))
      {
        echo tep_image(DIR_WS_IMAGES . $product_info['products_image2'],
            $product_info['products_name'],60, 60);
      }
      else if (!empty($product_info['products_image3']))
      {
        echo tep_image(DIR_WS_IMAGES . $product_info['products_image3'],
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
    if (strstr($PHP_SELF, FILENAME_SHOPPING_CART)) {
      echo '    <td class="main" style="">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['model'] . '</a><br>':'').'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '"><b>' . $products[$i]['name'] . '</b></a>';
    } else {
      echo '    <td class="main" style="">'.(((PRODUCT_LIST_MODEL > 0) && strstr($PHP_SELF, FILENAME_SHOPPING_CART))?'<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . $products[$i]['model'] . '</a><br>':'').'<b>' . $products[$i]['name'] . '</b>';
    }

// Display marker if stock quantity insufficient
    if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
      if (STOCK_CHECK == 'true') {
        echo $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if ($stock_check) $any_out_of_stock = 1;
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
      echo '    <td align="right" class="main" style=""><b id="pri_'.
        $products[$i]['id'] .'" >' . $currencies->display_price($products[$i]['price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</b>';
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
      echo '<td align="center"><a class="button_delete02" href="'.tep_href_link(FILENAME_SHOPPING_CART, 'products_id='.$products[$i]['id'].'&action=delete').'">'.TEXT_DEL_LINK.'</a></td>'; 
    }
    echo '</tr>';
    echo '<tr><td colspan="' . $colspan . '" bgcolor="#bdced5">' .
      tep_draw_separator('pixel_trans.gif', '1', '1') . '</td></tr>';
  }
 echo '</table>' . "\n";
?>
<!-- order_details_eof -->
