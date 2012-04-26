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
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<?php
  if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete') {
      $cart->remove($_GET['products_id']); 
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL')); 
    }
  }
?>
<script type="text/javascript">
<!--
function money_update(objid, targ)
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

  if (new_unit_price_total < 0) {
    old_price_total.innerHTML = '<font color="#ff0000">'+Math.abs(new_unit_price_total).toString() +'</font>' +monetary_unit_pri;
  } else {
    old_price_total.innerHTML = Math.abs(new_unit_price_total).toString() + monetary_unit_pri;
  }
  
  $('#pri_'+product_id).parent().find('small').each(function() {
    old_option_price = $(this).find('i').html();
    old_option_pri = old_option_price.slice(-1);
    old_option_price_info =  old_option_price.slice(0, -1);
    if (targ == 'up') {
      old_num = Number(obj.value) - 1; 
    } else {
      old_num = Number(obj.value) + 1; 
    }
    old_single_option_price = old_option_price_info / old_num; 
    new_option_price = Number(old_single_option_price)*Number(obj.value); 
    $(this).html('<i>' + new_option_price + old_option_pri + '</i>');
  });
  
  set_sub_total();
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
  if (sub_total >= 0) {
    sub_total_text.innerHTML = Math.abs(sub_total).toString() + monetary_sub_total;
  } else {
    sub_total_text.innerHTML = '<font color="#ff0000">' + Math.abs(sub_total).toString() + '</font>' + monetary_sub_total;
  }

}
  
function update_cart(objid, targ)
{
    money_update(objid, targ);
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
    update_cart(product_quantity.id, targ);
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
  
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')); ?> 
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
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
      if (isset($products[$i]['op_attributes'])) {
        foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
          $op_key_array = explode('_', $op_key); 
          $option_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name='".$op_key_array[1]."' and id= '".$op_key_array[3]."'");
          $option_item_res = tep_db_fetch_array($option_item_query);
          if ($option_item_res) {
            $products[$i]['add_op_attributes'][$op_key]['option_name'] = $option_item_res['front_title'];
            $products[$i]['add_op_attributes'][$op_key]['option_value'] = $op_value;
            if ($option_item_res['type'] == 'radio') {
              $c_option = @unserialize($option_item_res['option']);
              if (!empty($c_option)) {
                foreach ($c_option['radio_image'] as $cr_key => $cr_value) {
                  if (trim($cr_value['title']) == trim($op_value)) {
                    $products[$i]['add_op_attributes'][$op_key]['price'] = $cr_value['money'];
                    break;
                  }
                }
              }
            } else {
              $products[$i]['add_op_attributes'][$op_key]['price'] = $option_item_res['price'];
            }
          }
        }
      }
    }
  ?><table border="0" width="100%" cellspacing="0" cellpadding="0"><?php
    require(DIR_WS_MODULES. 'order_details.php');
?>
              
            </td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td align="right" class="main"><b><?php echo SUB_TITLE_SUB_TOTAL; ?>
             <span id="sub_total"> 
             <?php echo $currencies->format_total($cart->show_total()); ?></span></b></td>
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
              	<tr class="infoBoxNoticeContents_01">
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
                <tr class="infoBoxNoticeContents_01">
                  <td width="33" height="35"><img src="images/icons/hinto.jpg" align="absmiddle" /></td>
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
              <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice">
                <tr class="infoBoxNoticeContents">
                  <?php echo sprintf(DS_LIMIT_PRICE_OVER_ERROR,$currencies->format(DS_LIMIT_PRICE),$currencies->format(DS_LIMIT_PRICE)); ?></td>
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
                  echo sprintf("%s未満の注文はできません。合計金額を%s以上にしてから再度お申し込みください。",$limit_error_str,$limit_error_str); 
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
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr style=" text-align:center">
                  <td width="17%" align="center" class="main">
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
    $cart_products = tep_get_cart_products(tep_get_products_by_shopiing_cart($products));
    if ($cart_products) {
      $h2_show_flag = true;
      foreach($cart_products as $cp){
        $cp = tep_get_product_by_id($cp, SITE_ID, 4, true, 'shopping_cart', true);
        $cp_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$cp['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
        $cp_status_res = tep_db_fetch_array($cp_status_raw);
        if ($cp_status_res['products_status'] == 0) {
          $h2_show_flag = false;
        }
      }
  if($h2_show_flag){
?>
  <h2 class="pageHeading">こちらの商品もオススメ！！</h2>
<?php } ?>
  <div style="text-align:center;padding:10px 0;">
<?php
      foreach($cart_products as $cp){
        $cp = tep_get_product_by_id($cp, SITE_ID, 4, true, 'shopping_cart', true);
        $cp_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$cp['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
        $cp_status_res = tep_db_fetch_array($cp_status_raw);
        if ($cp_status_res['products_status'] == 0) {
          /*
          echo "<img src='".DIR_WS_IMAGES . 'carttags/'. $cp['products_cart_image']."' alt='".$cp['products_name']."' title='".$cp['products_name']."'>";
          */
        } else {
          echo "<a href='".tep_href_link(FILENAME_PRODUCT_INFO, "products_id=".$cp['products_id'])."'>";
          echo "<img src='".DIR_WS_IMAGES . 'carttags/'. $cp['products_cart_image']."' alt='".$cp['products_name']."' title='".$cp['products_name']."'>";
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
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
  </table>
  
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
