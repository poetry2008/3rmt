<?php
/*
  $Id$
*/

  require("includes/application_top.php");
  
  check_uri('/page=\d+/');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHOPPING_CART);

  if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete') {
      $cart->remove($_GET['products_id']); 
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL')); 
    } else if ($_GET['action'] == 'save_quantity'){
      $p_info = explode('<<', $_POST['sp_info']);
      foreach ($p_info as $p_key => $p_value) {
        if (empty($p_value)) {
          continue; 
        }
        $tp_info = explode('||', $p_value);
        $cart->update_quantity($tp_info[0], $tp_info[1]);
      }
      exit; 
    }
  }
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
?>
<?php page_head();?>
<?php //页面产品数量输入框 验证JS?>
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
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>

<script type="text/javascript">
<!--
function history_back(back_url){
  
  var final_prices = document.getElementsByName('final_price');
  var url_param_str = ''; 
  for (var i=0; i<final_prices.length; i++)
  {
    var t_pid = final_prices[i].id.substr(3);
    var t_pquantity = document.getElementById('quantity_'+ final_prices[i].id.substr(3)).value;
    url_param_str += t_pid + "||" + t_pquantity + "<<"; 
  }
  
  if (url_param_str != '') {
    $.ajax({
        url: '<?php echo FILENAME_SHOPPING_CART;?>?action=save_quantity',
        type: 'POST',
        async: false,
        data: 'sp_info='+url_param_str,
        success: function(msg){
          window.location.href=back_url;
        }
    });
  } else {
    window.location.href=back_url;
  }
}

function fmoney(s)
{
 s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(0) + "";
 var l = s.split(".")[0].split("").reverse();
 var t = '';
 for(i = 0; i < l.length; i ++ ){
    t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
  }
 return t.split("").reverse().join("");
}

function money_update(objid, targ, origin_qty, origin_small)
{
  var obj = document.getElementById(objid);
  var product_id = obj.id.substr(9);
  var unit_price = document.getElementById("unit_price_" + product_id);
  var attr_prices = document.getElementsByName("attr_" + product_id);
  var attr_prices_option = document.getElementsByName("attr_option_" + product_id);
  var sub_total = document.getElementById('sub_total_hidden');
  var hop_price_str = document.getElementById("h_op_" + product_id).value;
  
  var old_price_total  = document.getElementById("pri_" + product_id);
     
  var small_sum = document.getElementById("small_sum_" + product_id);
  var right_price = 0;
  var isum = small_sum.value.split(',');
  for (var i=isum.length ;i>0;i--){
    var tmplevel = isum[i-1].split(':');
    if (parseInt(obj.value)>=parseInt(tmplevel[0])){
      var right_price = tmplevel[1]
      break;
    }else{
      continue;
    }
  }
  
  var final_price = parseInt(unit_price.value) + parseInt(right_price);
  
  var new_unit_price_total = final_price * Number(obj.value);
  new_unit_price_total = Math.round(new_unit_price_total);

  if (unit_price.value < 0) {
    old_price_total.innerHTML = '<font color="#ff0000">'+Math.abs(new_unit_price_total).toString() +'</font>' + '<?php echo JPMONEY_UNIT_TEXT;?>';
  } else {
    old_price_total.innerHTML = Math.abs(new_unit_price_total).toString() + '<?php echo JPMONEY_UNIT_TEXT;?>';
  }
  
  if (hop_price_str != '') {
  hop_array = hop_price_str.split(",");
  var hop_num = 0;
  $('#pri_'+product_id).parent().find('small').each(function() {
    old_option_price = $(this).find('i').html();
    if (old_option_price != '&nbsp;') {
      old_option_pri = old_option_price.slice(-1);
      old_option_price_info =  old_option_price.slice(0, -1).replace(/,/g, '');
      new_option_price = Number(hop_array[hop_num])*Number(obj.value); 
      $(this).html('<i>' + new_option_price + old_option_pri + '</i>');
      hop_num++; 
    }
  });
  } 
   
  if (document.getElementById('one_price_show_'+product_id)) {
    var one_price = Number(origin_small.replace(/,/g, '')) / origin_qty  * Number(obj.value);
    document.getElementById('one_price_show_'+product_id).innerHTML = fmoney(one_price);
  }  
  set_sub_total();
}

function money_blur_update(objid, o_num, old_small)
{
  var obj = document.getElementById(objid);
  var product_id = obj.id.substr(9);
  var unit_price = document.getElementById("unit_price_" + product_id);
  var attr_prices = document.getElementsByName("attr_" + product_id);
  var attr_prices_option = document.getElementsByName("attr_option_" + product_id);
  var sub_total = document.getElementById('sub_total_hidden');
  var hop_price_str = document.getElementById("h_op_" + product_id).value;
  
  var old_price_total  = document.getElementById("pri_" + product_id);
     
    
  var small_sum = document.getElementById("small_sum_" + product_id);
  var right_price = 0;
  var isum = small_sum.value.split(',');
  for (var i=isum.length ;i>0;i--){
    var tmplevel = isum[i-1].split(':');
    if (parseInt(obj.value)>=parseInt(tmplevel[0])){
      var right_price = tmplevel[1]
      break;
    }else{
      continue;
    }
  }
  
  var final_price = parseInt(unit_price.value) + parseInt(right_price);
  
  var new_unit_price_total = final_price * Number(obj.value);
  new_unit_price_total = Math.round(new_unit_price_total);
  
  if (unit_price.value < 0) {
    old_price_total.innerHTML = '<font color="#ff0000">'+Math.abs(new_unit_price_total).toString() +'</font>' + '<?php echo JPMONEY_UNIT_TEXT;?>';
  } else {
    old_price_total.innerHTML = Math.abs(new_unit_price_total).toString() + '<?php echo JPMONEY_UNIT_TEXT;?>';
  }
  if (hop_price_str != '') {
  hop_array = hop_price_str.split(",");
  var hop_num = 0;
  $('#pri_'+product_id).parent().find('small').each(function() {
    old_option_price = $(this).find('i').html();
    if (old_option_price != '&nbsp;') {
      old_option_pri = old_option_price.slice(-1);
      old_option_price_info =  old_option_price.slice(0, -1).replace(/,/g, '');
      new_option_price = Number(hop_array[hop_num])*Number(obj.value); 
      $(this).html('<i>' + new_option_price + old_option_pri + '</i>');
      hop_num++; 
    }
  });
  }
 
  if (document.getElementById('one_price_show_'+product_id)) {
    var one_price = Number(old_small.replace(/,/g, '')) / o_num  * Number(obj.value);
    document.getElementById('one_price_show_'+product_id).innerHTML = fmoney(one_price);
  }
  set_sub_total();
}

function set_sub_total()
{
  var final_prices = document.getElementsByName('final_price');
  var sub_total = 0;
  for (var i=0; i<final_prices.length; i++)
  {

    var p_l_html = document.getElementById('pri_' + final_prices[i].id.substr(3)).innerHTML.split('<?php echo JPMONEY_UNIT_TEXT;?>')[0].replace(/,/g,'');
    var one_p_quantity = document.getElementById('quantity_'+ final_prices[i].id.substr(3)).value;
    if(document.getElementById('one_price_show_'+ final_prices[i].id.substr(3))){
    var one_price_money = document.getElementById('one_price_'+ final_prices[i].id.substr(3)).innerHTML.replace(/,/g,'');
    var res_one_price = Number(one_price_money) * Number(one_p_quantity);
    var res_one_price_str = fmoney(res_one_price);
    document.getElementById('one_price_show_'+ final_prices[i].id.substr(3)).innerHTML=res_one_price_str;
    }
    if(!isNaN(p_l_html)){
      var p_l_price = p_l_html;
    }else{
      p_l_html = p_l_html.replace(/(<.*?[^>]>)/gi,"");
      var p_l_price = 0 - Number(p_l_html);
    }
    op_price_str = document.getElementById('h_op_'+final_prices[i].id.substr(3)).value; 
    var op_price_total = 0; 
    if (op_price_str != '') {
      op_price_arr = op_price_str.split(',');
      for (var j=0; j<op_price_arr.length; j++) {
        op_price_total = op_price_total + Number(op_price_arr[j])*Number(one_p_quantity); 
      }
    }
    sub_total = sub_total + Number(p_l_price)+op_price_total;
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
  
function update_cart(objid, targ, origin_qty, origin_small)
{
    money_update(objid, targ, origin_qty, origin_small);
}

function change_num(ob,targ, quan,a_quan, origin_qty, origin_small)
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
    update_cart(product_quantity.id, targ, origin_qty, origin_small);
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
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <div class="pageHeading"><h1><?php echo HEADING_TITLE ; ?></h1></div>
        <div class="comment">
  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')); ?> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="rmt" class="product_info_box">
            <?php
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
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
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cr_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
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
    }?>
                <table border="0" class="order_details" width="100%" cellspacing="0" cellpadding="0" summary="rmt">
                  <?php
    require(DIR_WS_MODULES. 'order_details.php');
?>
                </table>
              
              </td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
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
                        <?php echo TEXT_SHOPPING_CART_READ_INFO;?>
</td>
                </tr>
                <tr class="infoBoxNoticeContents">
                  <td width="33" height="35"><img src="images/icons/hinto.jpg" alt="img"></td>
                  <td align="left"
                  valign="middle"><?php echo TEXT_SHOPPING_CART_READ_NOTICE_MONEY;?></td>
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
                    <td><?php echo sprintf(DS_LIMIT_PRICE_OVER_ERROR,$currencies->format(DS_LIMIT_PRICE),$currencies->format(DS_LIMIT_PRICE)); ?></td>
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
                  echo sprintf(TEXT_SHOPPING_CART_NOTICE_TEXT,$limit_error_str,$limit_error_str); 
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
    if($products_error == true){
?>
          <tr>
            <td class="stockWarning" align="center"><br>
              <?php echo PRODUCTS_WEIGHT_ERROR; ?></td>
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
                  <tr style="text-align:center;">
                  	<td width="22%"></td>
                    <td width="17%" align="center" class="main">
<?php
    $back = sizeof($navigation->path)-2;
    if (isset($navigation->path[$back]) and 0) {
?> 
                    <input type="hidden" name="goto" value="<?php echo tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']);?>">
                    <input type="submit" name="continue" value="" class="shopping_cart_continue">
<?php } else { ?>
<?php
if (!empty($_SESSION['history_url'])) {
  $back_url = $_SESSION['history_url'];
} else {
  $back_url = HTTP_SERVER;
}
?>
                    <button  class="shopping_cart_continue" onClick="history_back('<?php echo $back_url;?>'); return false;"></button>
<?php } ?>
                    </td>
                    <td width="6%"></td>
                    <td align="left" class="main">
                      <input type="submit" name="checkout" value="" class="shopping_cart_checkout">
                    </td>
                  </tr>
                  <tr>
                    <td class="main" colspan="4"><?php echo TEXT_UPDATE_CART_INFO; // 2003.02.27 nagata Add Japanese osCommerce ?></td>
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
  <h2><?php echo TEXT_SHOPPING_CART_PICKUP_PRODUCTS;?></h2>
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
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE); ?></a>
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
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
