<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  check_uri('/(.*)\{(.*)\}(.*)/'); 
  
  $all_ca_arr = tep_other_get_categories_id_by_parent_id(FF_CID);
  if (empty($all_ca_arr)) {
    $all_ca_arr = array(FF_CID); 
  } else {
    array_push($all_ca_arr, FF_CID); 
  }
  $whether_expro_raw = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id in (".implode(',', $all_ca_arr).") and products_id = '".(int)$_GET['products_id']."'");
  if (!tep_db_num_rows($whether_expro_raw)) {
    forward404(); 
  }
  
  if (tep_whether_show_products((int)$_GET['products_id'])) {
    forward404(); 
  }
  require('ajax_process.php'); 
  require(DIR_WS_ACTIONS . 'product_info.php');
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
var jq = jQuery.noConflict();
</script>
<script type="text/javascript" src="js/light_box.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript">
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
jq(document).ready(function () {
   var change_flag = jq("#change_flag").val();
   if(change_flag == 'true'){
     calc_product_final_price("<?php echo (int)$_GET['products_id'];?>");
     jq("#show_price").show();
     jq(".calc_show_price").show();  
   } 
   var actiontime =new Date().getTime();  
   jq(".option_product_radio_list").each(function(){

     var radio_option_value = jq(this).next("span").children("input[type='hidden']").val();
     radio_option_value = radio_option_value.replace(/<br>/i,"<br>");
     radio_option_value = radio_option_value.replace(/<\/br>/i,"</br>");
     var tmp_t_obj = jq(this); 
     jq(this).children(".option_product_radio_img_list").children(".option_product_single_radio").each(function(){

       var radio_list_option_value = jq(this).children().children(".option_conent").children("a").children("span:first").html();
       radio_list_option_value = radio_list_option_value.replace(/<br>/i,"<br>");
       radio_list_option_value = radio_list_option_value.replace(/<\/br>/i,"</br>");
       if(radio_list_option_value == radio_option_value ){

         jq(this).children().attr("class","option_show_border");
         if (tmp_t_obj.children(".option_product_default_radio")) {
           tmp_t_obj.children(".option_product_default_radio").find("div:first").attr("class","option_hide_border");
         }
       }else{
         jq(this).children().attr("class","option_hide_border"); 
       }
     });
   });
});
function calc_product_final_price(pid)
{
   var attr_price = 0; 
   jq('.option_table').find('input').each(function() {
       if (jq(this).attr('type') == 'hidden') {
         var reg_str = /^tp1_(.*)$/g;
         if (reg_str.exec(jq(this).attr('name'))) {
           attr_price += Number(jq(this).val());  
         }
         var reg_rstr = /^tp0_(.*)$/g;
         if (reg_rstr.exec(jq(this).attr('name'))) {
           var o_name = jq(this).attr('name').substr(4);
           i_data = document.getElementsByName('op_'+o_name)[0].value;
           i_data = i_data.replace(/\s/g, '');
           if (i_data != '') {
             attr_price += Number(jq(this).val());  
           }
         }
       }
   }); 
   
   jq.getJSON("<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.(int)$_GET['products_id']);?>"+"?action=calc_price&p_id="+pid+"&oprice="+attr_price+"&qty="+jq('#quantity').val(), function(msg) { 
     document.getElementById("show_price").innerHTML = msg.price; 
     jq("#change_flag").val('true');
     jq("#show_price").show();
     jq(".calc_show_price").show();
  });
}

function recalc_product_price(t_obj)
{
  calc_product_final_price("<?php echo (int)$_GET['products_id'];?>");
}

function select_item_radio(i_obj, t_str, o_str, p_str, r_price)
{
      jq(i_obj).parent().parent().parent().parent().parent().find('a').each(function() {
        if (jq(this).parent().parent()[0].className == 'option_show_border') {
          jq(this).parent().parent()[0].className = 'option_hide_border';
        } 
      });   
      if (t_str == '') {
        t_str = jq(i_obj).children("span:first").html(); 
      } else {
        t_str = ''; 
      }
      
      jq(i_obj).parent().parent()[0].className = 'option_show_border'; 
      origin_default_value = jq('#'+o_str).val(); 
      jq('#'+o_str).val(t_str);
      
      item_info = p_str.split('_');
      item_id = item_info[3];
      var r_tmp_name = p_str.substr(3);
      if (t_str == '') {
        jq('#tp1_'+r_tmp_name).val(0);
      } else {
        jq('#tp1_'+r_tmp_name).val(r_price);
      }
      actiontime =new Date().getTime();  
      setTimeout(function (){ timeline_action("<?php echo (int)$_GET['products_id'];?>")},1000); 
}

function change_num(ob,targ, quan, a_quan)
{
  jq("#quantity_error").html('');
  var product_quantity = document.getElementById(ob);
  if(isNaN(product_quantity.value)||product_quantity.value==''){
    jq("#quantity_error").html('<?php echo TEXT_PRODUCT_QUANTITY_ERROR;?>');
  }else{
    var product_quantity_reg = new RegExp(/\.|\-/);
    if(product_quantity_reg.test(product_quantity.value)){
      jq("#quantity_error").html('<?php echo TEXT_PRODUCT_QUANTITY_ERROR;?>');
    }else{
      if(product_quantity.value.substr(0,1) == 0 || product_quantity.value.substr(0,1) == '+'){
        var length = product_quantity.value.length;
        var code = '';
        var code_flag = false;
        var add_code = product_quantity.value.charAt(0) == '+' ? true : false;
        for(var i=0;i<length;i++){
          if(product_quantity.value.charAt(i) > 0 && code_flag == false){
            code_flag = true; 
          }
          if(code_flag == true){
            code += product_quantity.value.charAt(i); 
          }
        }
        code = code == '' || code == '+' ? 0 : code;
        code = add_code == true && code != 0? '+'+code : code;
        product_quantity.value = code;
      }
    } 
  }
  var product_quantity_num = parseInt(product_quantity.value);
  if (targ == 'up') { 
  if(isNaN(product_quantity.value)||product_quantity.value==''){
      jq("#quantity_error").html('<?php echo TEXT_PRODUCT_QUANTITY_ERROR;?>');
    }else{
      if (product_quantity_num >= a_quan) {
        num_value = product_quantity_num;
      } else {
        num_value = product_quantity_num + quan; 
      }
    }
  } else if(targ == 'down') {
    if(isNaN(product_quantity.value)||product_quantity.value==''){
      jq("#quantity_error").html('<?php echo TEXT_PRODUCT_QUANTITY_ERROR;?>');
    }else{
      if (product_quantity_num <= 1) {
        num_value = product_quantity_num;
      } else { 
        num_value = product_quantity_num - quan;
      }
    }
  }else {
    num_value = product_quantity.value;
  }

  product_quantity.value = num_value;
  actiontime =new Date().getTime();  
   setTimeout( function() {
      timeline_action("<?php echo (int)$_GET['products_id'];?>");  
   }, 1000);    
}
function get_current_ts(){

   return new Date().getTime();  

}

function timeline_action(p){

  if (get_current_ts()-actiontime>=980){
  calc_product_final_price(p);
  };
}
</script>
<script language="javascript" type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
function showimage($1) {
    document.images.lrgproduct.src = $1;
}

--></script>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof -->
  <!-- body -->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box">
    <tr>
      <td valign="top" class="left_colum_border"><!-- left_navigation -->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof -->
      </td>
      <!-- body_text -->
      <td valign="top" id="contents">
<?php
  $product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID,
      $languages_id,true,'product_info');
  $product_info['products_quantity'] = tep_get_quantity($product_info['products_id'],true);
  if (!$product_info) { // product not found in database
?>
        <P><?php echo TEXT_PRODUCT_NOT_FOUND; ?></P>
        <div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div>
        <?php
  } else {
    $product_info['site_id'] == SITE_ID && tep_db_query("
        UPDATE " . TABLE_PRODUCTS_DESCRIPTION . " 
        SET products_viewed = products_viewed+1 
        WHERE products_id = '" .  (int)$_GET['products_id'] . "' 
          AND language_id = '" . $languages_id . "' 
          AND site_id     = '".SITE_ID."'
    ");

    if (tep_get_special_price($product_info['products_price'], $product_info['products_price_offset'], $product_info['products_small_sum'])) {
      $pricedef = $product_info['products_price'];
      $products_price = '<s>' .
        $currencies->display_price(tep_get_price($product_info['products_price'],
              $product_info['products_price_offset'],
              $product_info['products_small_sum'], $product_info['products_bflag'],
              $product_info['price_type']), tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> 
      
      <span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($product_info['products_price'], $product_info['products_price_offset'], $product_info['products_small_sum']), tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
    } else {
      
      $pricedef = $product_info['products_price'];
      $products_price =
        $currencies->display_price(tep_get_price($product_info['products_price'],
              $product_info['products_price_offset'],
              tep_get_price($product_info['products_price'],$product_info['products_small_sum'],
                '', $product_info['products_bflag'],$product_info['price_type']),
              $product_info['products_bflag'], $product_info['price_type']), tep_get_tax_rate($product_info['products_tax_class_id']));
    }
     
    $description = replace_store_name($product_info['products_description']);
    //获取商品上部分内容
    $products_info_top = $product_info['products_info_top'];
    $products_info_under = $product_info['products_info_under'];
?>
    <?php if (tep_show_warning(tep_get_products_categories_id($product_info['products_id'])) or $product_info['products_status'] != '1' ) {
      echo '<div class="waring_product">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
    } ?>
         <h1 class="pageHeading_long"><?php echo $product_info['products_name']; ?></h1>
         <div class="comment_long">
         <h2 class="line"><?php echo ds_tep_get_categories((int)$_GET['products_id'],1); ?> <?php echo ds_tep_get_categories((int)$_GET['products_id'],2); ?></h2>
         <table width="684"  border="0" cellpadding="0" cellspacing="0" summary="rmt" bgcolor="#f2f2f2">
          <tr>
            
                <td valign="top" style="padding-right:10px;">
                    <table border="0" cellpadding="0" cellspacing="0" summary="info_box" class="infoBox">
                    <tr>
                      <td>
                      <div class="product_info_box">
                      <table summary="info_box_contents" cellpadding="3" cellspacing="1" border="0" class="product_info_table">
                          <tr>
                          <td class="main" width="85"><b><font color="#00719D"><?php echo TEXT_PRODUCT_MODEL;?></font></b></td>
                            <td class="main"><?php if (PRODUCT_LIST_MODEL > 0){ echo $product_info['products_model'] ; }else{ echo '-' ; } ?></td>
                          </tr>
                          <?php 
                       $products_info_top_array = explode('------',$products_info_top);
                       foreach($products_info_top_array as $top_value){

                         $top_array = explode('||||||',$top_value); 
                      ?>
                          <tr>
                            <td class="main"><b><font color="#00719D"><?php echo str_replace('${RATE}',tep_display_attention_1_3($product_info['products_exchange_rate']),$top_array[0]); ?></font></b></td>
                            <td class="main"><?php echo str_replace("\r\n",'<br>',str_replace('${RATE}',tep_display_attention_1_3($product_info['products_exchange_rate']),$top_array[1])); ?></td>
                          </tr>
                          <?php } ?>
                          <tr>
                          <td class="main"><b><font color="#00719D"><?php echo TEXT_PRODUCT_MANUFACTURER_NAME;?></font></b></td>
                            <td class="main"><?php include(DIR_WS_BOXES.'manufacturer_info.php') ; ?></td>
                          </tr>
                          <tr>
                          <td class="main"><b><font color="#00719D"><?php echo TEXT_PRODUCT_PRICE;?></font></b></td>
                            <td class="main">
                                <?php
                                  # 添加开始 ---------------------------------------
                                  # -- 订单数量和单价列表 --------------------------
                                  if(tep_not_null($product_info['products_small_sum'])
                                      && $product_info['price_type'] == 1) {
                                    $wari_array = array();
                                    echo '<span class="smallText">'.TEXT_ONE_UNIT_PRICE.'</span><div class="small_table">';
                                    $parray = explode(",", $product_info['products_small_sum']);
                                    for($i=0; $i<sizeof($parray); $i++) {
                                      $tt = explode(':', $parray[$i]);
                                      $wari_array[$tt[0]] = $tt[1];
                                    }

                                    @ksort($wari_array);
                                  
                                    foreach($wari_array as $key => $val) {
                                      echo '<div class="product_small_info">';
                                      echo '<div class="product_small_info01">'.$key.TEXT_MORE_UNIT_PRICE.'</div>';
                                      echo '<div class="product_small_info02"><b>'.$currencies->display_price(round($pricedef + $val),0).'</b></div>';
                                      echo '</div>'."\n";
                                    }
                                    echo '</div>'."\n";
                                  } else {
                                    echo '<strong>'.$products_price.'</strong>';
                                  }
                                  
                                  # -- 订单数量和单价列表 --------------------------
                                  # 添加结束 -------------------------------------------
                                
                                ?>
                            </td>
                          </tr>
                          <tr>
                          <td class="main"><b><font color="#00719D"><?php echo TEXT_ORDERS_NUM;?></font></b></td>
                          <td class="main"><?php echo TEXT_REMAINING;?><strong>&nbsp;<?php echo tep_show_quantity($product_info['products_quantity']); ?></strong>&nbsp;<?php echo TEXT_UNIT;?></td>
                          </tr>
                          <?php 
                       $products_info_under_array = explode('------',$products_info_under);
                       foreach($products_info_under_array as $under_value){

                         $under_array = explode('||||||',$under_value); 
                        ?>
                          <tr>
                            <td class="main red"><b><?php echo str_replace('${RATE}',tep_display_attention_1_3($product_info['products_exchange_rate']),$under_array[0]); ?></b></td>
                            <td class="main"><?php echo str_replace("\r\n",'<br>',str_replace('${RATE}',tep_display_attention_1_3($product_info['products_exchange_rate']),$under_array[1])); ?></td>
                          </tr>
                          <?php } ?>
                          <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && !$product_info['products_bflag']) { ?>
                          <tr>
                          <td class="main"><b><font color="#00719D"><?php echo TEXT_POINT;?></font></b></td>
                          <td class="main"><?php echo ds_tep_get_point_value($_GET['products_id']) ; ?>&nbsp;<?php echo TEXT_POINT;?></td>
                          </tr>
                          <?php } ?> 
                          </table>
                        </div>
                        </td>
                    </tr>
                    
                    
                  </table></td>
              
                <td width="250" valign="top">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="rmt_img">
                    <?php
//获取商品的图片
$products_img_array =
tep_products_images($product_info['products_id'],$product_info['site_id']);
        if (tep_not_null($products_img_array[0])) {
    ?>
                    <tr>
                <td align="center" class="smallText">
                      <script language="javascript" type="text/javascript">
                      <!--
document.write('<?php echo '<a onclick=fnCreate(\"'.DIR_WS_IMAGES . 'products/' .  $products_img_array[0].'\",0) href="javascript:void(0)">' .  tep_image3(DIR_WS_IMAGES . 'products/'. $products_img_array[0], addslashes($product_info['products_name']), PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'name="lrgproduct" id="lrgproduct"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '<\'+\'/a>'; ?>');
                        -->
                        </script>
                        <noscript>
                        <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES .
                        'products/' . urlencode($products_img_array[0])) . '">' .
                        tep_image3(DIR_WS_IMAGES . 'products/' .
                            $products_img_array[0], $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
                        </noscript>
                      </td>
                    </tr>
                    <tr>
                      <td align="center">
                      <table border="0" cellspacing="6" cellpadding="0" summary="rmt_info">
                      <tr>
                        <?php
                        foreach($products_img_array as $img_key=>$img_value){
                          if (tep_not_null($img_value)){
                      ?>
                          <td width="60" height="60" align="center" class="image_border"><a href="javascript:void(0)" onclick="fnCreate('<?php echo DIR_WS_IMAGES .'products/'.  $img_value;?>',<?php echo $img_key;?>)" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES .'products/'.  $img_value, $product_info['products_name'],PRODUCT_INFO_SMALL_IMAGE_WIDTH, PRODUCT_INFO_SMALL_IMAGE_HEIGHT , 'name="prod_thum_1" class="image_alt_list"') ;?></a>
                          <input type="hidden" class="large_image_input" value="<?php echo DIR_WS_IMAGES .'products/'.$img_value;?>">
                          </td>
                      <?php
                          }
                        }
                      ?> 
                      </tr>
                        </table>
                        <?php
        }
    ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <table width="684"  border="0" cellpadding="0" cellspacing="0" summary="rmt" bgcolor="#f2f2f2">
            <tr class="header2">
                      <td height="30" class="main" style="padding-bottom:4px; " align="right">
                      <div class="option_dot">
                       <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=process')) . "\n"; ?>
<?php
      if($product_info['products_quantity'] < 1) {
        if($product_info['products_bflag'] == '1') {
          # 买取商品
          echo '<span class="markProductOutOfStock">'.TEXT_PAUSE;
        } elseif ($product_info['products_cflag'] == '0') {
          echo '<span class="markProductOutOfStock">'.TEXT_SOLD_OUT;
        } else {
          # 通常商品
          echo '<br><span class="markProductOutOfStock">'.TEXT_OUT_OF_STOCK;
        }
        if ($product_info['preorder_status'] == '1') {
          echo '<br><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle" alt=""><a href=' .  tep_preorder_href_link($product_info['products_id'], $product_info['romaji']) . '>' . $product_info['products_name'] . TEXT_PREORDER_BOOK .'</a>';
        }
        echo '</span>'; 
      }else{    
    
    $p_cflag = tep_get_cflag_by_product_id($product_info['products_id']); 
    $hm_option->render($product_info['belong_to_option'], false, 0, '', '', $p_cflag);
    ?>
                        <table width="100%" summary="rmt_text" cellpadding="3" cellspacing="1" border="0" class="product_info_table">
                          <tr>
                          <td class="main" valign="middle" width="85" style="padding-left:8px;"><b><?php echo TEXT_PRODUCTS_QTY;?></b></td>
                            <td colspan="2">
                            <table border="0" cellpadding="0" cellspacing="0"><tr>
                            <?php $p_a_quan = $product_info['products_quantity'];?>
                            <td class="main" valign="middle"><input name="quantity" type="text" id="quantity" value="<?php echo (isset($_POST['quantity'])?$_POST['quantity']:1);?>" class="input_text_short" maxlength="4" onchange="change_num('quantity','','',<?php echo $p_a_quan;?>)"></td>
                            <td valign="middle">
                              <div style="*margin-top:-3px;">
                                <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','up',1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/nup.gif" alt="+"></a>
                                <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','down', 1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/ndown.gif" alt="-"></a>
                              </div>
                            </td>
                            <td class="main">&nbsp;<?php echo TEXT_UNIT;?>&nbsp;<span id="quantity_error"><?php echo $quantity_error == true ? TEXT_PRODUCT_QUANTITY_ERROR : '';?></span></td>
                            </tr></table>
                          </tr> 
                          <tr>   
                          <td class="main" width="85" style="padding-left:8px;">
                          <div class="calc_show_price"><input type="hidden" id="change_flag" name="change_num_flag" value="<?php echo isset($_POST['change_num_flag']) ? $_POST['change_num_flag'] : 'false';?>"><?php echo TEXT_PRODUCT_SUBTOTAL;?>:</div>
                          </td>
                          <td width="325">
                            <div id="show_price"></div>
                          </td>
                          <td align="right" style="padding-right:5px;"><?php echo tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></td>
                          </tr>
                        </table>
                        <?php
     }
    ?>
    <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></form>
                      </div>
                      </td>
                    </tr>
            <tr class="header2">
                      <td align="right" valign="bottom" class="smallText">
                          <div class="option_dot">
                        <a class="table_a_spacing" href="<?php echo tep_href_link(FILENAME_TELL_A_FRIEND,'products_id='.(int)$_GET['products_id']) ;  ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_tellafriend.jpg',BOX_HEADING_TELL_A_FRIEND);?></a>&nbsp; <a class="table_a_spacing" href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,'products_id='.(int)$_GET['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_review.jpg',BOX_REVIEWS_WRITE_REVIEW);?></a>&nbsp; <?php echo tep_draw_form('open',tep_href_link('open.php', '', 'SSL'),'get');?><input class="table_a_spacing" type="image" style="vertical-align:bottom;" src="<?php echo DIR_WS_IMAGES;?>design/button/botton_question.jpg"><?php echo tep_draw_hidden_field('products', $product_info['products_name']) ; ?></form></div></td>
                    </tr>
            </table>
      <?php
                    //sub图像
        $sub_colors_query = tep_db_query("
            SELECT color_image, 
                   color_id, 
                   color_to_products_name 
            FROM ".TABLE_COLOR_TO_PRODUCTS." 
            WHERE products_id = '".(int)$_GET['products_id']."'
        ");
                    $cnt=0;
                   if(tep_db_num_rows($sub_colors_query) >= 1) {
    ?>
            <!-- color image -->
            <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="rmt">
              <tr>
                <?php
                    while($sub_colors = tep_db_fetch_array($sub_colors_query)) {
                      //获取颜色名
          $colors_name_query = tep_db_query("
              SELECT color_name 
              FROM ".TABLE_COLOR." 
              WHERE color_id = '".$sub_colors['color_id']."'
          ");
                      $colors_name_result = tep_db_fetch_array($colors_name_query);
                      
                      $mcnt++;
                      if($mcnt == 1) {
                      ?>
                <noscript>
                <?php echo '<td class="smallText" align="center" width="20%"><a href="' . tep_href_link(DIR_WS_IMAGES .'products/'. $products_img_array[0]) . '" rel="lightbox[products]">' . tep_image2(DIR_WS_IMAGES .'products/'. $products_img_array[0], $product_info['products_name'], PRODUCT_INFO_SMALL_IMAGE_WIDTH, PRODUCT_INFO_SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>-</td>'; ?>
                </noscript>
                <?php
                      }
                      ?>
                <?php echo '<td class="smallText" align="center" width="20%"><a href="' . tep_href_link(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image']) . '" rel="lightbox[products]">' . tep_image2(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image'], $product_info['products_name'],PRODUCT_INFO_SMALL_IMAGE_WIDTH, PRODUCT_INFO_SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'.$sub_colors['color_to_products_name'].'</td>'; ?>
                <?php
                      
                      $cnt++;
                      if($cnt > 6) {
                        echo '</tr><tr>';
                        $cnt=0;
                      }
                    }
    ?>
              </tr>
            </table>

        <!-- color image -->
        <?php
        }
 ?>
 </div>
         <?php if($description){?>
            <h3 class="pageHeading_long"><?php echo $product_info['products_name'].TEXT_ABOUT; ?></h3>
            <div class="comment_long">
              <div  class="reviews_area"><p><?php 
            echo $description;
            ?>
            </p></div></div>
         <?php }?>
        <?php
    include(DIR_WS_BOXES.'reviews.php') ;
?>

<?php

    if (tep_not_null($product_info['products_url'])) {
?>
        <p><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></p>
<?php
    }

    if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
?>
        <p><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])); ?></p>
        <?php
    } else {
?>

        <?php
    }
?>
        <?php
    if ( (USE_CACHE == 'true') && !SID ) {
      echo tep_cache_also_purchased(3600);
    } else {
      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
    }
  }
?>
        
      </td>
     </tr>
      <!-- body_text_eof -->
  </table>
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
