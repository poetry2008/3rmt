<?php
/*
  $Id$
*/

  require("includes/application_top.php");
  
  check_uri('/page=\d+/');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHOPPING_CART);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
?>
<?php page_head();?>
<?php
  if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete') {
      $cart->remove($_GET['products_id']); 
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL')); 
    }
  }
?>
<script type="text/javascript">
function key(e)
{
  if(window.event) {
    if(e.keyCode<48   ||   e.keyCode>57||e.keyCode==8) {
      return false;
    } else {
      return true;
    }
  } else if(e.which) {
    if((e.which>47)   &&   (e.which<58)||(e.which==8)) {
      return true;
    } else {
      return false;
    }
  }
}
</script>
<script type="text/javascript">
<!--
function money_update(objid)
{
  var obj = document.getElementById(objid);
  var product_id = obj.id.substr(9);
  var unit_price = document.getElementById("unit_price_" + product_id);
  var attr_prices = document.getElementsByName("attr_" + product_id);
  var attr_prices_option = document.getElementsByName("attr_option_" + product_id);
  var sub_total = document.getElementById('sub_total_hidden');


  var new_unit_price_total = Number(unit_price.value) * Number(obj.value);
  new_unit_price_total = Math.round(new_unit_price_total);
  
  
  var old_price_total  = document.getElementById("pri_" + product_id);
  var monetary_unit_pri = old_price_total.innerHTML.slice(-1);
  old_price_total.innerHTML = Math.abs(new_unit_price_total).toString() + monetary_unit_pri;

  for (var i = 0; i < attr_prices.length; i++)
  {
    var new_attr_price = Number(attr_prices[i].value) * Number(obj.value);
    var old_price_attr = document.getElementById("attr_" + product_id + "_attr_" + attr_prices_option[i].value);
    var prefix_attr = old_price_attr.innerHTML.slice(0,1);
    var monetary_unit_attr = old_price_attr.innerHTML.slice(-1);
    old_price_attr.innerHTML = prefix_attr + new_attr_price.toString() + monetary_unit_attr;

  }


  set_sub_total();
  //alert(old_sub_total);
  //alert(new_sub_total);
}

function set_sub_total()
{
  var final_prices = document.getElementsByName('final_price');
  var sub_total = 0;
  for (var i=0; i<final_prices.length; i++)
  {
    sub_total = sub_total + Number(final_prices[i].value)*Number(document.getElementById('quantity_' +
            final_prices[i].id.substr(3)).value);
  }

  sub_total = Math.round(sub_total);
  var sub_total_text = document.getElementById("sub_total");
  var monetary_sub_total = sub_total_text.innerHTML.slice(-1);
  sub_total_text.innerHTML = Math.abs(sub_total).toString() + monetary_sub_total;

}
  
function update_cart(objid)
{
    money_update(objid);
}

function change_num(ob,targ, quan,a_quan)
{
  var product_quantity = document.getElementById(ob);
  var product_quantity_num = parseInt(product_quantity.value);
  if (targ == 'up')
  { 
    if (product_quantity_num >= a_quan)
    {
      num_value = product_quantity_num;
    }
    else
    {
      num_value = product_quantity_num + quan; 
    }
  }
  else
  {
    if (product_quantity_num <= 1)
    {
      num_value = product_quantity_num;
    }
    else
    { 
      num_value = product_quantity_num - quan;
    }
  }

  product_quantity.value = num_value;
  if (product_quantity_num != num_value)
  { 
    update_cart(product_quantity.id);
  }
}
-->
</script>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="rmt">
    <tr>
      <td valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><span><?php echo HEADING_TITLE ; ?></span></h1>
        <div class="comment">
  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')); ?> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="rmt" class="product_info_box">
            <?php
  if ($cart->count_contents() > 0) {
?>
            <tr>
              <td>
                <?php
    $any_out_of_stock = 0;
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
// Push all attributes information in an array
      if (isset($products[$i]['attributes'])) {
        while (list($option, $value) = each($products[$i]['attributes'])) {
          echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
          // ccdd
          $attributes = tep_db_query("select popt.products_options_name, 
                                             poval.products_options_values_name, 
                                             pa.options_values_price, 
                                             pa.price_prefix, 
                                             pa.products_at_quantity
                                      from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                      where pa.products_id = '" . $products[$i]['id'] . "'
                                       and pa.options_id = '" . $option . "'
                                       and pa.options_id = popt.products_options_id
                                       and pa.options_values_id = '" . $value . "'
                                       and pa.options_values_id = poval.products_options_values_id
                                       and popt.language_id = '" . $languages_id . "'
                                       and poval.language_id = '" . $languages_id . "'");
          $attributes_values = tep_db_fetch_array($attributes);

          $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
          $products[$i][$option]['options_values_id'] = $value;
          $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
          $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
          $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
      $products[$i][$option]['products_at_quantity'] = $attributes_values['products_at_quantity'];
        }
      }
    }?>
                <table border="0" class="order_details" width="100%" cellspacing="0" cellpadding="2" summary="rmt">
                  <?php
    require(DIR_WS_MODULES. 'order_details.php');
?>
                </table>
              
              </td>
            </tr>
            <tr>
              <td><?php //echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td align="right" class="main"><b><?php echo SUB_TITLE_SUB_TOTAL; ?>
                <span id="sub_total"><?php echo $currencies->format_total($cart->show_total()); ?></span></b></td>
            </tr>
<?php   
    // 买取200以下提示
    if($cart->show_total() < 0 && $cart->show_total() > -200) {
?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td align="right" class="main">
             <table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxNotice_01">
              	<tr class="infoBoxNoticeContents">
                	<td colspan="2" class="text">
【重要】<br>
小計金額が赤色の場合は「買取」となり、表示された金額をお客様へお支払いいたします。<br>
買取金額が200円未満の場合は手数料の関係上、支払方法にて銀行振込を選択することができません。<br>
<br>
選択できる支払方法は以下となります。<br>
A:来店による支払い<br>
B:ポイントの加算（<?php echo STORE_NAME;?>会員でなければ表示されません）<br>
</td>
                </tr>
                <tr class="infoBoxNoticeContents">
                  <td width="33" height="35"><img src="default_images/icons/hinto.jpg" align="absmiddle" /></td>
                  <td align="left"
                  valign="middle">200円未満になる場合は商品名「ウェブマネーの販売」をカートに入れてみてはどうでしょう。</td>
                </tr>
              </table>
            </td>
          </tr>
          <?php 
  }
?>
            <?php   
    if(isset($_GET['limit_error']) && $_GET['limit_error'] == 'true') {
?>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td align="right" class="main">
                <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice" summary="rmt">
                  <tr class="infoBoxNoticeContents">
                    <td><?php echo '<font color="#000000">'.sprintf(DS_LIMIT_PRICE_OVER_ERROR,$currencies->format(DS_LIMIT_PRICE),$currencies->format(DS_LIMIT_PRICE)).'</font>'; ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php 
  }
?>
<?php   
    if(isset($_GET['limit_min_error']) && $_GET['limit_min_error'] == 'true') {
?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td align="right" class="main">
              <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice">
                <tr class="infoBoxNoticeContents">
                  <td>
                  <?php 
                  $limit_min_error_arr = explode(',', LIMIT_MIN_PRICE);
                  $limit_error_str = ''; 
                  if (count($limit_min_error_arr) == 2) {
                    $limit_error_str .= $currencies->format($limit_min_error_arr[0]).'-';
                    $limit_error_str .= $currencies->format($limit_min_error_arr[1]);
                  } else {
                    $limit_error_str .= $currencies->format($limit_min_error_arr[0]);
                  }
                  echo '<font color="#000000">'.sprintf("%s未満の注文はできません。合計金額を%s以上にしてから再度お申し込みください。",$limit_error_str,$limit_error_str).'</font>'; 
                  ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <?php 
  }
?>
            <?php
    if ($any_out_of_stock == 1) {
      if (STOCK_ALLOW_CHECKOUT == 'true') {
?>
            <tr>
              <td class="stockWarning" align="center"><br>
                <?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></td>
            </tr>
            <?php
      } else {
?>
            <tr>
              <td class="stockWarning" align="center"><br>
                <?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></td>
            </tr>
            <?php
      }
    }
?>
            <tr>
              <td><br>
                <table border="0" width="100%" cellspacing="0" cellpadding="2" summary="rmt">
                  <tr>
                    <td width="17%" align="left" class="main">
<?php
    $back = sizeof($navigation->path)-2;
    if (isset($navigation->path[$back]) and 0) {
?> 
                    <input type="hidden" name="goto" value="<?php echo tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']);?>">
                    <input type="submit" name="continue" value="" class="shopping_cart_continue">
<?php } else { ?>
                    <button  class="shopping_cart_continue" onClick="history.back(); return false;"></button>
<?php } ?>
                    </td>
                    <td align="left" class="main">
                      <input type="submit" name="checkout" value="" class="shopping_cart_checkout">
                    </td>
                  </tr>
                  <tr>
                    <td class="main" colspan="3"><?php echo TEXT_UPDATE_CART_INFO; // 2003.02.27 nagata Add Japanese osCommerce ?></td>
                  </tr>
                </table>
<?php 
    $cat_cid_arr = tep_rr_get_categories_id_by_parent_id(FF_CID); 
    $sp_cid_arr = explode(',', FF_CID); 
    if (empty($cat_cid_arr)) {
      $cat_cid_arr[] = $sp_cid_arr[0]; 
      $cat_cid_arr[] = $sp_cid_arr[1]; 
    } else {
      array_push($cat_cid_arr, $sp_cid_arr[0], $sp_cid_arr[1]); 
    }
    $cart_products = tep_get_cart_other_products(tep_get_products_by_shopiing_cart($products), $cat_cid_arr);
    if ($cart_products) {
?>
  <h2>こちらの商品もオススメ！！</h2>
  <div style="text-align:center;padding:10px 0;">
<?php
      foreach($cart_products as $cp){
        $cp = tep_get_product_by_id($cp, SITE_ID, 4, true, 'shopping_cart', true);
        $cp_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$cp['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
        $cp_status_res = tep_db_fetch_array($cp_status_raw);
        if ($cp_status_res['products_status'] == 0) {
          /*
          echo "<img src='default_".DIR_WS_IMAGES . 'carttags/'. $cp['products_cart_image']."' alt='".$cp['products_name']."' title='".$cp['products_name']."'>";
          */
        } else {
          echo "<a href='".tep_href_link(FILENAME_PRODUCT_INFO, "products_id=".$cp['products_id'])."'>";
          echo "<img src='default_".DIR_WS_IMAGES . 'carttags/'. $cp['products_cart_image']."' alt='".$cp['products_name']."' title='".$cp['products_name']."'>";
          echo "</a>";
        }
        echo "<br>";
      }
?>
  </div>
<?php
    }
  ?>
                <?php require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_INFO_SHOPPING_CART);?>
                <p class="main"><b><i><?php echo SUB_HEADING_TITLE_1; ?></i></b><br>
                  <?php echo SUB_HEADING_TEXT_1; ?></p>
                <p class="main"><b><i><?php echo SUB_HEADING_TITLE_2; ?></i></b><br>
                  <?php echo SUB_HEADING_TEXT_2; ?></p>
                <p class="main"><b><i><?php echo SUB_HEADING_TITLE_3; ?></i></b><br>
                  <?php echo SUB_HEADING_TEXT_3; ?></p>
              </td>
            </tr>
            <?php
  } else {
?>
            <tr>
              <td class="main"><?php echo TEXT_CART_EMPTY; ?></td>
            </tr>
            <tr>
              <td align="right" class="main"><br>
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a>
              </td>
            </tr>
            <?php
  }
?>
          </table>
        </form>
        </div>
        <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
