<?php
/*
  $Id$
*/

  require("includes/application_top.php");

  check_uri('/page=\d+/');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHOPPING_CART);

  //检查商品的OPTION是否改动 
  $check_products_option = tep_check_less_product_option();
  if(!empty($check_products_option)){
    if(!isset($_SESSION['change_option_num'])){

      $_SESSION['change_option_num'] = 1;
    }else{
      $_SESSION['change_option_num']++;
    }
  }else{
    unset($_SESSION['change_option_num']); 
  }

  if(isset($_SESSION['change_option_num']) && $_SESSION['change_option_num'] > 1){

    foreach($check_products_option as $check_products_value){
      $cart->remove($check_products_value); 
    }
    unset($_SESSION['change_option_num']);
  }

  $products_cart_array = $cart->get_products();
  if($_GET['action'] != 'update_product'){
    unset($_SESSION['change_option_id']);
    unset($_SESSION['change_option_flag']);
    //记录OPTION有变化的商品
    for ($i=0, $n=sizeof($products_cart_array); $i<$n; $i++) { 
      if(in_array($products_cart_array[$i]['id'],$check_products_option)){
        $_SESSION['change_option_id'][] = $products_cart_array[$i]['id']; 
      }else{
        $_SESSION['change_option_flag'][] = $products_cart_array[$i]['id']; 
      }
    }
  }

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
    } else if ($_GET['action'] == 'check_products_op') {
      $check_products_info = tep_check_less_product_option(); 
      if (!empty($check_products_info)) {
        $notice_msg_array = array(); 
        foreach ($check_products_info as $cpo_key => $cpo_value) {
          $tmp_cpo_info = explode('_', $cpo_value); 
          $notice_msg_array[] = tep_get_products_name($tmp_cpo_info[0]);
        }
        $return_check_array[] = sprintf(NOTICE_LESS_PRODUCT_OPTION_TEXT, implode('、', $notice_msg_array)); 
        $return_check_array[] = implode('>>>', $check_products_info); 
      } else {
        $return_check_array[] = 0; 
      }
      if(isset($_SESSION['change_option_flag']) && count($_SESSION['change_option_flag']) > 0){

        $return_check_array[] = 1;
      }else{
        $return_check_array[] = 0; 
      }
      echo implode('|||', $return_check_array); 
      exit; 
    } else if ($_GET['action'] == 'delete_products_op') {
      $delete_check_op = explode('>>>', $_POST['d_op_list']); 
      if (!empty($delete_check_op)) {
        foreach ($delete_check_op as $dc_key => $dc_value) {
          $cart->remove($dc_value); 
        }
      }
      exit; 
    }
  }
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
?>
<?php page_head();?>
<script type="text/javascript">
<?php //检查不足的option?>
function check_op_products() {
  $.ajax({
    url: '<?php echo FILENAME_SHOPPING_CART.'?action=check_products_op';?>',     
    type: 'POST', 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||');  
      if (msg_arr[0] != '0') {
       if(msg_arr[2] == '0'){
          $.ajax({
            url: '<?php echo FILENAME_SHOPPING_CART.'?action=delete_products_op';?>',     
            data:'d_op_list='+msg_arr[1], 
            type: 'POST', 
            async: false,
            success: function(msg) {
              window.location.href = '<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>'; 
            }
          });
       }else{
         document.forms.cart_quantity.submit();   
       }
      } else {
        document.forms.cart_quantity.submit(); 
      }
    }
  }); 
}
function dbc2sbc(str){   
  var result = '';   
  for (i=0 ; i<str.length; i++)   {   
   code = str.charCodeAt(i);
<?php //获取当前字符的unicode编码   ?>
   if (code >= 65281 && code <= 65373){
<?php //在这个unicode编码范围中的是所有的英文字母已及各种字符   ?>
    result += String.fromCharCode(str.charCodeAt(i) - 65248);
<?php //把全角字符的unicode编码转换为对应半角字符的unicode码   ?>
   }else if (code == 12288){
<?php //空格   ?>
    result += String.fromCharCode(str.charCodeAt(i) - 12288 + 32);   
   }else  {   
    result += str.charAt(i);   
   }   
  }   
  return result;   
}
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
      if (new_option_price < 0) {
        $(this).html('<i><font color="#ff0000">' + (0 - new_option_price) + '</font>' + old_option_pri + '</i>');
      } else {
        $(this).html('<i>' + new_option_price + old_option_pri + '</i>');
      }
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
     
  obj.value = dbc2sbc(obj.value);
  if(obj.value==''){
    obj.value = 0;
  }
  if(isNaN(parseInt(obj.value))){
    obj.value = o_num;
  }
    
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
      if (new_option_price < 0) {
        $(this).html('<i><font color="#ff0000">' + (0 - new_option_price) + '</font>' + old_option_pri + '</i>');
      } else {
        $(this).html('<i>' + new_option_price + old_option_pri + '</i>');
      }
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
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
        <div class="comment">
  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')); ?> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="rmt">
            <?php
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
  if ($cart->count_contents(true) > 0) {
?>
          <tr>
            <td>
                <?php
    $any_out_of_stock = 0;
    $products_array = $cart->get_products();
    //对相同商品OPTION改动的覆盖
    $products_id_array = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      $products_id_str = explode('_',$products_array[$i]['id']);
      $products_id_array[] = $products_id_str[0];
    }
    $products_id_count = array_count_values($products_id_array);
    $products_temp_array = array();
    foreach($products_id_count as $key=>$value){

      if($value >= 2){

        $products_temp_array[] = $key;
      }
    }
    $check_products_option_array = array();
    foreach($check_products_option as $value){

      $check_products_option_str = explode('_',$value);
      $check_products_option_array[] = $check_products_option_str[0];
    } 
    foreach($products_temp_array as $value){

      if(in_array($value,$check_products_option_array)){

        $cart->remove($check_products_option[array_search($value,$check_products_option_array)]); 
      }
    }

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
            } else if ($option_item_res['type'] == 'textarea') {
              $c_option = @unserialize($option_item_res['option']);
              $products[$i]['add_op_attributes'][$op_key]['price'] = $option_item_res['price'];
              if ($c_option['require'] == '0') {
                if ($op_value == MSG_TEXT_NULL) {
                  $products[$i]['add_op_attributes'][$op_key]['price'] = 0;
                }
              } 
            } else {
              $products[$i]['add_op_attributes'][$op_key]['price'] = $option_item_res['price'];
            }
          }
        }
      }
    }?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="rmt">
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
              	<tr class="infoBoxNoticeContents_01">
                	<td colspan="2" class="text">
                        <?php echo TEXT_SHOPPING_CART_READ_INFO;?>
</td>
                </tr>
                <tr class="infoBoxNoticeContents_01">
                  <td width="33" height="35"><img src="images/icons/hinto.jpg" align="absmiddle" /></td>
                  <td align="left" valign="middle"><?php echo TEXT_SHOPPING_CART_READ_NOTICE_MONEY;?></td>
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
                  <tr>
                    <td width="17%" align="left" class="main">
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
                    <td align="left" class="main">
                      <input type="button" name="checkout_op" value="" class="shopping_cart_checkout" onclick="check_op_products();">
                      <input type="hidden" name="checkout" value=""> 
                    </td>
                  </tr>
                  <tr>
                    <td class="main" colspan="3"><?php echo TEXT_UPDATE_CART_INFO;?></td>
                  </tr>
                </table>
<?php 
    $cart_products = tep_get_cart_products(tep_get_products_by_shopiing_cart($products));
    if ($cart_products) {
      $h3_show_flag = true;
      foreach($cart_products as $cp){
        $cp = tep_get_product_by_id($cp, SITE_ID, 4, true, 'shopping_cart', true);
        $cp_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$cp['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
        $cp_status_res = tep_db_fetch_array($cp_status_raw);
        if ($cp_status_res['products_status'] == 0) {
          $h3_show_flag = false;
        }
      }
?>
</td></tr></table></div>

<?php if($h3_show_flag){ ?>
<p class="pageBottom"></p>
<h3 class="pageHeading"><?php echo TEXT_SHOPPING_CART_PICKUP_PRODUCTS;?></h3>
<?php } ?>
  <div class="comment">
<table><tr><td>
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
          </table>
        </form>
        </div>
        <p class="pageBottom"></p>
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
          </table>
        </form>
        </div>
        <p class="pageBottom"></p>
            <?php
  }
?>
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
