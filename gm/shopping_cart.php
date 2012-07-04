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
    if ($_GET['action'] == 'save_quantity'){
      $cart->update_quantity($_POST['pid'],$_POST['pquantity']);
      exit;
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
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
<!--
function history_back(back_url){
  var final_prices = document.getElementsByName('final_price');
  for (var i=0; i<final_prices.length; i++)
  {
    var t_pid = final_prices[i].id.substr(3);
    var t_pquantity = document.getElementById('quantity_'+ final_prices[i].id.substr(3)).value;
    $.ajax({
      url: '<?php echo FILENAME_SHOPPING_CART;?>?action=save_quantity',
      type: 'POST',
      async: false,
      data: 'pid='+t_pid+'&pquantity='+t_pquantity,
      success: function(){
      }
    });
  }
  window.location.href=back_url;
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
function money_update(objid)
{
  var obj = document.getElementById(objid);
  var product_id = obj.id.substr(9);
  var unit_price = document.getElementById("unit_price_" + product_id);
  var small_sum = document.getElementById("small_sum_" + product_id);
  var attr_prices = document.getElementsByName("attr_" + product_id);
  var attr_prices_option = document.getElementsByName("attr_option_" + product_id);
  var sub_total = document.getElementById('sub_total_hidden');
  var isum = small_sum.value.split(',');
  var right_price = 0
  for (var i=isum.length ;i>0;i--){
    var tmplevel = isum[i-1].split(':');
    if (parseInt(obj.value)>=parseInt(tmplevel[0])){
      var right_price = tmplevel[1]
      break;
    }else{
      continue;
    }
  }

  final_price = parseInt(unit_price.value)+parseInt(right_price); var new_unit_price_total = Number(final_price) * Number(obj.value);
  new_unit_price_total = Math.round(new_unit_price_total);
  var old_price_total  = document.getElementById("pri_" + product_id);
  var monetary_unit_pri = old_price_total.innerHTML.slice(-1);
  if (new_unit_price_total < 0) {
    old_price_total.innerHTML = '<font color="#ff0000">'+Math.abs(new_unit_price_total).toString() +'</font>' +monetary_unit_pri;
  } else {
    old_price_total.innerHTML = Math.abs(new_unit_price_total).toString() + monetary_unit_pri;
  }

  for (var i = 0; i < attr_prices.length; i++)
  {
    var new_attr_price = Number(attr_prices[i].value) * Number(obj.value);
    var old_price_attr = document.getElementById("attr_" + product_id + "_attr_" + attr_prices_option[i].value);
    var prefix_attr = old_price_attr.innerHTML.slice(0,1);
    var monetary_unit_attr = old_price_attr.innerHTML.slice(-1);
    old_price_attr.innerHTML = prefix_attr + new_attr_price.toString() + monetary_unit_attr;

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
    if(document.getElementById('one_price_show_'+ final_prices[i].id.substr(3))){
    var one_price_money = document.getElementById('one_price_'+ final_prices[i].id.substr(3)).innerHTML.replace(/,/g,'');
    var one_p_quantity = document.getElementById('quantity_'+ final_prices[i].id.substr(3)).value;
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
    sub_total = sub_total + Number(p_l_price);
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
   
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div class="yui3-u" id="layout">
             <div id="current" ><?php echo $breadcrumb->trail(' <img  src="images/point.gif"> '); ?></div>
 <?php include('includes/search_include.php');?>
            
                    <div id="main-content">
      <h2><?php echo HEADING_TITLE; ?></h2>
        <?php
  if ($cart->count_contents() > 0) {
?>    
        <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'SSL')); ?> 
         <table id="detail-table" border="0" width="100%" cellspacing="0" cellpadding="0">

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
    }
   
    require(DIR_WS_MODULES. 'order_details.php');
?> 
          </table> 
  


	   <hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none; margin:10px 0;">
       <div class="shopping_cart_total"> 
         <b><?php echo SUB_TITLE_SUB_TOTAL; ?>
          <span id="sub_total"><?php echo $currencies->format_total($cart->show_total()); ?></span></b>        </div> 
<?php   
    if($cart->show_total() < 0 && $cart->show_total() > -200) {
?>
          <div class="shopping_waring">  
               <table border="0" width="100%" cellspacing="0" cellpadding="2" style="margin-bottom:0;">
              <tr>
               <td colspan="2">
               <?php echo TEXT_SHOPPING_CART_READ_INFO;?>
               </td>
                </tr>
                <tr>
                  <td width="33" height="35"><img src="images/design/hinto.jpg" align="absmiddle" /></td>
                  <td align="left" valign="middle">
                  <?php echo TEXT_SHOPPING_CART_READ_NOTICE_MONEY;?>
                  </td>
                </tr>
              </table>
            
          </div>
          <?php 
  }
?>
<?php   
    if(isset($_GET['limit_error']) && $_GET['limit_error'] == 'true') {
?>

        <div class="4" >
         <table border="0" width="100%" cellspacing="1" cellpadding="2"> 
                  <tr> 
                    <td><?php echo sprintf(DS_LIMIT_PRICE_OVER_ERROR,$currencies->format(DS_LIMIT_PRICE),$currencies->format(DS_LIMIT_PRICE)); ?></td> 
                  </tr> 
                </table>
             </div> 
<?php 
  }
?>
<?php   
    if(isset($_GET['limit_min_error']) && $_GET['limit_min_error'] == 'true') {
?>

          <div class="5">     
              <table border="0" width="100%" cellspacing="1" cellpadding="2">
                <tr>
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
            
         </div>
          <?php 
  }
?>
<?php
    if ($any_out_of_stock == 1) {
      if (STOCK_ALLOW_CHECKOUT == 'true') {
?> 
       <div class="6"> 
         
          <?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?> 
        </div> 
        <?php
      } else {
?> 
        <div class="7"> 
           
          <?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?> 
        </div> 
        <?php
      }
    }
?> 
        <div class="8">  
           
          <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
            <tr> 
              <td id="shopping_cart_continue" align="right">
<?php
    $back = sizeof($navigation->path)-2;
    if (isset($navigation->path[$back]) and 0) {
?> 
                    <input type="hidden" name="goto" value="<?php echo tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']);?>"></div>
<?php } else { ?>
<?php
if (!empty($_SESSION['history_url'])) {
  $back_url = $_SESSION['history_url'];
} else {
  $back_url = HTTP_SERVER;
}
?>

                    <button  class="shopping_cart_continue" onClick="history_back('<?php echo $back_url;?>'); return false;"></button>

<?php } ?></td>
<td id="shopping_cart_checkout" align="left">

  <input type="submit" name="checkout" value="" class="shopping_cart_checkout">

  </td> 
            </tr> 
            <tr> 
              <td  colspan="2"><?php echo TEXT_UPDATE_CART_INFO; // 2003.02.27 nagata Add Japanese osCommerce ?></td> 
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
      if($h3_show_flag){
?>
  <h3><?php echo TEXT_SHOPPING_CART_PICKUP_PRODUCTS;?></h3>
<?php } ?>
<?php
      foreach($cart_products as $cp){
        $cp = tep_get_product_by_id($cp, SITE_ID, 4, true, 'shopping_cart', true);
        $cp_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$cp['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
        $cp_status_res = tep_db_fetch_array($cp_status_raw);
        if ($cp_status_res['products_status'] == 0) {
        } else {
         echo "<div style='text-align:center;padding:10px 0;'>";
         echo "<a href='".tep_href_link(FILENAME_PRODUCT_INFO, "products_id=".$cp['products_id'])."'>";
          echo "<img src='".DIR_WS_IMAGES . 'carttags/'. $cp['products_cart_image']."' alt='".$cp['products_name']."' title='".$cp['products_name']."'>";
          echo "</a></div>";
        }
   
      }
?>
<?php
    }
  ?>
       <?php require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_INFO_SHOPPING_CART);?>
<p><b><i><?php echo SUB_HEADING_TITLE_1; ?></i></b><br><?php echo SUB_HEADING_TEXT_1; ?></p>
<p><b><i><?php echo SUB_HEADING_TITLE_2; ?></i></b><br><?php echo SUB_HEADING_TEXT_2; ?></p>
<p><b><i><?php echo SUB_HEADING_TITLE_3; ?></i></b><br><?php echo SUB_HEADING_TEXT_3; ?></p>
          </form>
          
       </div> 
        <?php
  } else {
?> 
      <div style="margin-top:13px;"> 
         <?php echo TEXT_CART_EMPTY; ?>
         </div> 
       <div class="botton-continue" align="right">
               <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>" ><?php echo
          tep_image_button('button_continue.gif',
IMAGE_BUTTON_CONTINUE,'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"
onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"'); ?></a>

          </div>
          <?php
  }
?> 
        </table></div>
                </div>
<?php include('includes/float-box.php');?>

      <!-- body_text_eof //--> 
  <!-- body_eof //--> 
  <!-- footer //--> 
   <!-- footer_eof //-->
  </div>
<?php echo DEFAULT_PAGE_TOP_CONTENTS;?>

</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
