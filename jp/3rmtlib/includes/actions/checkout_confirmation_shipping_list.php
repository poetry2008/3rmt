<?php
$shipping_method_count = count($_SESSION['shipping_method_info_arr']);
$shipping_method_info_arr = $_SESSION['shipping_method_info_arr'];
$show_some_shipping = false;
if($shipping_method_count > 1){
  $show_some_shipping = true;
}
if($show_some_shipping){
//多个产品多个配送
  echo "<table width='100%'>";
foreach($shipping_method_info_arr as $pid => $value){
  $shipping_method_info = $value;
  echo "<tr><td>";
?>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents">
<td width="100%" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
<?php
if (sizeof($order->info['tax_groups']) > 1) {
  ?> 
    <tr> 
    <td class="main" colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td> 
    </tr> 
    <?php
} else {
  ?> 
    <tr> 
    <td class="main" colspan="3"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr> 
    <?php
}

for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
  if($order->products[$i]['id'] == $pid){
    $product_info = tep_get_product_by_id($pid, SITE_ID, $languages_id);
    break;
  }
}

  echo '          <tr>' . "\n" .
    '            <td class="main" align="center" valign="top" width="150">' . $order->products[$i]['qty'] . '&nbsp;個' . (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) ? '<br><span style="font-size:10px">'. tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) .'</span>': '') . '</td>' . "\n" .
    '            <td class="main" valign="top">' . $order->products[$i]['name'];

  if (STOCK_CHECK == 'true') {
    echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
  }

  if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
    for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
      echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
    }
  }

  echo '</td>' . "\n";

  if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

  echo '            <td class="main" align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</td>' . "\n" .
    '          </tr>' . "\n";
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<tr> 
<td>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents"> 
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="main" colspan="3"><b><?php echo TEXT_TORIHIKI_TITLE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main"><?php echo TEXT_TORIHIKIHOUHOU; ?></td>
<td class="main"><?php echo $shipping_method_info['torihikihouhou']; ?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main" width="30%"><?php echo TEXT_TORIHIKIKIBOUBI; ?></td>
<td class="main" width="70%"><?php
$torihiki_confirm_datetime_arr = explode(' ',
    $shipping_method_info['insert_torihiki_date']);
echo $torihiki_confirm_datetime_arr[0];
?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main"><?php echo TEXT_TORIHIKIKIBOUJIKAN; ?></td>
<td class="main"><?php echo tep_get_torihiki_format('',$shipping_method_info['torihiki_time']);?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main" valign="top"><?php echo TEXT_TORIHIKIKI_ADDRESS; ?></td>
<td class="main"><?php echo 
tep_address_label($_SESSION['customer_id'],$shipping_method_info['shipping_address']);
?></td>
</tr>

</table>

</td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<?php
echo "</td></tr>";
}
echo "</table>";

}else{
//多个产品一个配送
foreach($shipping_method_info_arr as $value){
  $shipping_method_info = $value;
}
?>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents">
<td width="100%" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
<?php
if (sizeof($order->info['tax_groups']) > 1) {
  ?> 
    <tr> 
    <td class="main" colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td> 
    <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td> 
    </tr> 
    <?php
} else {
  ?> 
    <tr> 
    <td class="main" colspan="3"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr> 
    <?php
}

for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
  $product_info = tep_get_product_by_id($order->products[$i]['id'], SITE_ID, $languages_id);

  echo '          <tr>' . "\n" .
    '            <td class="main" align="center" valign="top" width="150">' . $order->products[$i]['qty'] . '&nbsp;個' . (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) ? '<br><span style="font-size:10px">'. tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) .'</span>': '') . '</td>' . "\n" .
    '            <td class="main" valign="top">' . $order->products[$i]['name'];

  if (STOCK_CHECK == 'true') {
    echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
  }

  if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
    for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
      echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
    }
  }

  echo '</td>' . "\n";

  if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

  echo '            <td class="main" align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</td>' . "\n" .
    '          </tr>' . "\n";
}
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<tr> 
<td>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents"> 
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="main" colspan="3"><b><?php echo TEXT_TORIHIKI_TITLE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main"><?php echo TEXT_TORIHIKIHOUHOU; ?></td>
<td class="main"><?php echo $shipping_method_info['torihikihouhou']; ?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main" width="30%"><?php echo TEXT_TORIHIKIKIBOUBI; ?></td>
<td class="main" width="70%"><?php
$torihiki_confirm_datetime_arr = explode(' ',
    $shipping_method_info['insert_torihiki_date']);
echo $torihiki_confirm_datetime_arr[0];
?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main"><?php echo TEXT_TORIHIKIKIBOUJIKAN; ?></td>
<td class="main"><?php echo tep_get_torihiki_format('',$shipping_method_info['torihiki_time']);?></td>
</tr>
<tr>
<td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
<td class="main" valign="top"><?php echo TEXT_TORIHIKIKI_ADDRESS; ?></td>
<td class="main"><?php echo 
tep_address_label($_SESSION['customer_id'],$shipping_method_info['shipping_address']);
?></td>
</tr>

</table>

</td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<?php
}
?>
