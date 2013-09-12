<?php
/*
   $Id$
   确认文件代码
 */
require('includes/application_top.php');
check_uri('/(.*)\{(.*)\}(.*)/'); 
if (tep_whether_show_products((int)$_GET['products_id'])) {
  forward404(); 
}
require('ajax_process.php'); 
require(DIR_WS_ACTIONS . 'product_info.php');
$product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID, $languages_id,true,'product_info');
$product_info['products_quantity'] = tep_get_quantity($product_info['products_id'],true);
$p_image_list = array();
if($product_info['products_image']){
  $p_image_list[] = $product_info['products_image'];
}
if($product_info['products_image2']){
  $p_image_list[] = $product_info['products_image2'];
}
if($product_info['products_image3']){
  $p_image_list[] = $product_info['products_image3'];
}
$p_image_count = 0;
foreach($p_image_list as $p_list_row){
  if(file_exists3(DIR_WS_IMAGES.'products/'.$p_list_row)&&
      $p_list_row){
    $p_image_count++;
  }
}
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.7.min.js"></script>
<script type="text/javascript" src="js/product_info.js"></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.js"></script>
<link rel="stylesheet" href="css/jquery.lightbox-0.5.css" type="text/css">
<?php if($p_image_count>1){ ?>
<script type="text/javascript" src="js/jquery.featureCarousel.js" ></script>
<link rel="stylesheet" href="css/feature-carousel.css" type="text/css">
<script type="text/javascript">
$(document).ready(function() {
  var carousel = $("#carousel").featureCarousel({
      largeFeatureWidth:   <?php echo  PRODUCT_INFO_IMAGE_WIDTH;?>,
      largeFeatureHeight:  <?php echo  PRODUCT_INFO_IMAGE_HEIGHT;?>,
      smallFeatureWidth:   <?php echo  PRODUCT_INFO_IMAGE_WIDTH/2;?>,
      smallFeatureHwight:  <?php echo  PRODUCT_INFO_IMAGE_HEIGHT/2;?>,
    });
  $("#carousel a").lightBox();
});
</script>
<?php
}else{
?>
<script type="text/javascript">
$(document).ready(function() {
  $(".light").lightBox();
});
</script>
<?php
}
?>
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
$(document).ready(function () {
   var change_flag = $("#change_flag").val();
   if(change_flag == 'true'){
     calc_product_final_price("<?php echo (int)$_GET['products_id'];?>");
     $("#show_price").show();
     $(".calc_show_price").show();  
   } 
   var actiontime =new Date().getTime();  
   $(".option_product_radio_list").each(function(){

     var radio_option_value = $(this).next("span").children("input[type='hidden']").val();
     radio_option_value = radio_option_value.replace(/<br>/i,"<br>");
     radio_option_value = radio_option_value.replace(/<\/br>/i,"</br>");
     var tmp_t_obj = $(this); 
     $(this).children(".option_product_radio_img_list").children(".option_product_single_radio").each(function(){

       var radio_list_option_value = $(this).children().children(".option_conent").children("a").children("span:first").html();
       radio_list_option_value = radio_list_option_value.replace(/<br>/i,"<br>");
       radio_list_option_value = radio_list_option_value.replace(/<\/br>/i,"</br>");
       if(radio_list_option_value == radio_option_value ){

         $(this).children().attr("class","option_show_border");
         if (tmp_t_obj.children(".option_product_default_radio")) {
           tmp_t_obj.children(".option_product_default_radio").find("div:first").attr("class","option_hide_border");
         }
       }else{
         $(this).children().attr("class","option_hide_border"); 
       }
     });
   });
});
function calc_product_final_price(pid)
{
   var attr_price = 0; 
   $('.option_table').find('input').each(function() {
       if ($(this).attr('type') == 'hidden') {
         var reg_str = /^tp1_(.*)$/g;
         if (reg_str.exec($(this).attr('name'))) {
           attr_price += Number($(this).val());  
         }
         var reg_rstr = /^tp0_(.*)$/g;
         if (reg_rstr.exec($(this).attr('name'))) {
           var o_name = $(this).attr('name').substr(4);
           i_data = document.getElementsByName('op_'+o_name)[0].value;
           i_data = i_data.replace(/\s/g, '');
           if (i_data != '') {
             attr_price += Number($(this).val());  
           }
         }
       }
   }); 
   
   $.getJSON("<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.(int)$_GET['products_id']);?>"+"?action=calc_price&p_id="+pid+"&oprice="+attr_price+"&qty="+$('#quantity').val(), function(msg) { 
     document.getElementById("show_price").innerHTML = msg.price; 
     $("#change_flag").val('true');
     $("#show_price").show();
     $(".calc_show_price").show();
  });
}

function recalc_product_price(t_obj)
{
  calc_product_final_price("<?php echo (int)$_GET['products_id'];?>");
}

function select_item_radio(i_obj, t_str, o_str, p_str, r_price)
{
      $(i_obj).parent().parent().parent().parent().parent().find('a').each(function() {
        if ($(this).parent().parent()[0].className == 'option_show_border') {
          $(this).parent().parent()[0].className = 'option_hide_border';
        } 
      });   
      if (t_str == '') {
        t_str = $(i_obj).children("span:first").html(); 
      } else {
        t_str = ''; 
      }
      
      $(i_obj).parent().parent()[0].className = 'option_show_border'; 
      origin_default_value = $('#'+o_str).val(); 
      $('#'+o_str).val(t_str);
      
      item_info = p_str.split('_');
      item_id = item_info[3];
      var r_tmp_name = p_str.substr(3);
      if (t_str == '') {
        $('#tp1_'+r_tmp_name).val(0);
      } else {
        $('#tp1_'+r_tmp_name).val(r_price);
      }
      actiontime =new Date().getTime();  
      setTimeout(function (){ timeline_action("<?php echo (int)$_GET['products_id'];?>")},1000); 
}

function change_num(ob,targ, quan, a_quan)
{
  var product_quantity = document.getElementById(ob);
  product_quantity.value = dbc2sbc(product_quantity.value);
  if(isNaN(product_quantity.value)||product_quantity.value==''){
    product_quantity.value = 0;
  }else{
    var product_quantity_reg = new RegExp(/\.|\-/);
    if(product_quantity_reg.test(product_quantity.value)){
      product_quantity.value = 0; 
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
    if (product_quantity_num >= a_quan) {
      num_value = product_quantity_num;
    } else {
      num_value = product_quantity_num + quan; 
    }
  } else if(targ == 'down') {
    if (product_quantity_num <= 1) {
      num_value = product_quantity_num;
    } else { 
      num_value = product_quantity_num - quan;
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
</head><body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div id="main">
<div class="yui3-u" id="layout">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="product-switching">
<?php
if (!$product_info) { // product not found in database
  ?>
    <P><?php echo TEXT_PRODUCT_NOT_FOUND; ?></P>
    <div align="right">
    <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a>
    </div>
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
      $currencies->display_price(tep_get_price($product_info['products_price'], $product_info['products_price_offset'], $product_info['products_small_sum'], $product_info['products_bflag']), tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> 

      <span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($product_info['products_price'], $product_info['products_price_offset'], $product_info['products_small_sum']), tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $pricedef = $product_info['products_price'];
    $products_price = $currencies->display_price(tep_get_price($product_info['products_price'], $product_info['products_price_offset'], tep_get_price($product_info['products_price'],$product_info['products_small_sum'], '', $product_info['products_bflag']), $product_info['products_bflag']), tep_get_tax_rate($product_info['products_tax_class_id']));
  }

  $description = replace_store_name($product_info['products_description']);

  $data1 = explode("//", $product_info['products_attention_1']);

  $data2 = explode("//", $product_info['products_attention_2']);
  $data3 = explode("//", $product_info['products_attention_3']);
  $data4 = explode("//", $product_info['products_attention_4']);
  ?>
    <?php if (tep_show_warning(tep_get_products_categories_id($product_info['products_id'])) or $product_info['products_status'] != '1') {
      echo '<div class="waring_product">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
    } ?>
  <br>     
    <h1 ><?php echo ds_tep_get_categories((int)$_GET['products_id'],1) ; ?></h1>
    <div style="background:#466886; margin:5px 0; padding:5px;">
    <?php  echo ds_tep_get_categories((int)$_GET['products_id'],2) ; ?>
    </div>
    <h2><?php echo $product_info['products_name']; ?></h2>
    </div>
    <?php include(DIR_FS_DOCUMENT_ROOT.'banner/info_banner.php');?> 
    <div id="main-content">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" > 
    <tr>
    <td width="20%"><b><?php 
    echo TEXT_PRODUCT_MODEL;?></b></td>
    <td>
    <?php if (PRODUCT_LIST_MODEL > 0){ echo $product_info['products_model'] ; }else{ echo '-' ; } ?>
    </td>
    </tr>
    <?php 
    if(!empty($product_info['products_attention_1_1']) && !empty($product_info['products_attention_1_3'])){
      ?>
        <tr>
        <td><b><?php echo $product_info['products_attention_1_1'] ; ?></b></td>
        <td><?php echo $product_info['products_attention_1_2'] .'&nbsp;&nbsp;'.tep_display_attention_1_3($product_info['products_attention_1_3']) . $product_info['products_attention_1_4'] ; ?></td>
        </tr>
        <?php } ?>
        <?php
        if(!empty($data1[0]) && !empty($data1[1])){
          ?>
            <tr>
            <td><b><?php echo $data1[0] ; ?></b></td>
            <td><?php echo $data1[1] ; ?></td>
            </tr>
            <?php } ?>
            <?php 
            if(!empty($data2[0]) && !empty($data2[1])){
              ?>
                <tr>
                <td><b><?php echo $data2[0] ; ?></b></td>
                <td><?php echo $data2[1] ; ?></td>
                </tr>
                <?php } ?>
                <tr>
                <td><b><?php 
                echo TEXT_PRODUCT_MANUFACTURER_NAME;?></b></td>
                <td>
                <?php include(DIR_WS_BOXES.'manufacturer_info.php') ; ?>
                </td>
                </tr>
                <tr>
                <td valign="top"><b><?php echo
                TEXT_PRODUCT_PRICE;?></b></td>
                <td>
                <?php
//# 添加开始 ---------------------------------------
//# -- 订单数量和单价列表 --------------------------
                if(tep_not_null($product_info['products_small_sum'])) {
                  $wari_array = array();
                  echo '<span class="smallText">'.TEXT_PRODUCT_INFO_PRICE_INFO.'</span>';
                  $parray = explode(",", $product_info['products_small_sum']);
                  for($i=0; $i<sizeof($parray); $i++) {
                    $tt = explode(':', $parray[$i]);
                    $wari_array[$tt[0]] = $tt[1];
                  }

                  @ksort($wari_array);

                  foreach($wari_array as $key => $val) {
                    echo '<p>'.sprintf(TEXT_PRODUCT_INFO_QTY_ORDER,$key);
                    echo '<b>'.$currencies->display_price(round($pricedef + $val),0).'</b></p>';
                  }
                } else {
                  echo '<strong>'.$products_price.'</strong>';
                }
//# -- 订单数量和单价列表 --------------------------
//# 添加结束 -------------------------------------------
  ?>
    </td>
    </tr>
    <tr>
    <td><b><?php echo
    TEXT_ORDERS_NUM;?></b></td>
    <td><?php echo
    sprintf(TEXT_PRODUCT_INFO_QTY_TEXT,tep_show_quantity($product_info['products_quantity'])); ?></td>
    </tr>
    <?php 
    if(!empty($data3[0]) && !empty($data3[1])){
      ?>
        <tr>
        <td><b><?php echo $data3[0] ; ?></b></td>
        <td><?php echo $data3[1] ; ?></td>
        </tr>
        <?php } ?>
        <?php 
        if(!empty($data4[0]) && !empty($data4[1])){
          ?>
            <tr>
            <td valign="top"><b><?php echo $data4[0] ; ?></b></td>
            <td><?php echo $data4[1] ; ?></td>
            </tr>
            <?php } ?>
            <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && !$product_info['products_bflag']) { ?>
              <tr>
                <td><b><?php echo 
                TEXT_POINT;?></b></td>
                <td><?php echo
                sprintf(TEXT_PRODUCT_INFO_POINT_INFO,ds_tep_get_point_value($_GET['products_id'])) ; ?></td>
                </tr>
                <?php } ?>
                <?php
                if (false) {
                  $tag_query = tep_db_query("
                      SELECT t.tags_id, 
                      t.tags_images, 
                      t.tags_name 
                      FROM " . TABLE_PRODUCTS_TO_TAGS . " pt, " . TABLE_TAGS . " t 
                      WHERE t.tags_id = pt.tags_id 
                      AND pt.products_id='" . $product_info['products_id'] . "'
                      ");
                  if (tep_db_num_rows($tag_query)) { 
                    ?>
                      <tr> 
                      <td><b><?php echo TEXT_PRODUCT_INFO_TAG;?></b></td> 
                      <td>
                      <ul class="show_tags01"> 
                      <?php
                      while($tag = tep_db_fetch_array($tag_query)) {
                        ?>
                          <li><a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'tags_id=' .  $tag['tags_id']);?>">
                          <?php if (
                              (
                               (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $tag['tags_images']) && !is_dir(DIR_FS_CATALOG . DIR_WS_IMAGES . $tag['tags_images'])) 
                               || 
                               (file_exists(DIR_FS_CATALOG . 'default_images/' . $tag['tags_images']) && !is_dir(DIR_FS_CATALOG . 'default_images/' . $tag['tags_images']))
                              )
                              && $tag['tags_images']
                              )
                          {
                            echo tep_image(DIR_WS_IMAGES . $tag['tags_images'], $tag['tags_name'] , 20, 15);
                          } else { 
                            echo $tag['tags_name'];
                          }
                        ?>
                          </a></li>
                          &nbsp;&nbsp;
                        <?php
                      }
                    ?>
                      </ul> 
                      </td> 
                      </tr> 
                      <?php
                  }
                }
  ?>
    </table> 
    <hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none; margin:20px 0 25px 0;">
    <div id="option-detail">  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=process')); ?>
    <div id="detail-div-number"> 
    <?php
    if($product_info['products_quantity'] < 1) {
      if($product_info['products_bflag'] == '1') {
# 买取商品
        echo '<span class="markProductOutOfStock">'.TEXT_PAUSE;
      } elseif ($product_info['products_cflag'] == '0') {
        echo '<span class="markProductOutOfStock">'.TEXT_SOLD_OUT;
      } else {
# 通常商品
        echo '<span class="markProductOutOfStock">'.TEXT_OUT_OF_STOCK;
      }
      if ($product_info['preorder_status'] == '1') {
        echo '<img src="images/design/box/arrow_2.gif" width="5" height="5"
          hspace="5" border="0" align="absmiddle" alt=""><a href=' .
          tep_preorder_href_link($product_info['products_id'],
              $product_info['romaji']) . '>' . $product_info['products_name'] . 
          TEXT_PREORDER_BOOK.'</a>';
      }
      echo '</span>'; 
    }else{    
    $p_cflag = tep_get_cflag_by_product_id($product_info['products_id']); 
    $hm_option->render($product_info['belong_to_option'], false, 0, '', '', $p_cflag);
    ?>
        <table width="100%" border="0" id="calculation" cellpadding="6" cellspacing="0">
        <tr>
        <td width="20%"><?php echo TEXT_PRODUCTS_QTY;?></td>
        <td colspan="2">
        <?php $p_a_quan = $product_info['products_quantity'];?>
        <input name="quantity" type="text" id="quantity" style="text-align:right;" value="<?php echo (isset($_POST['quantity'])?$_POST['quantity']:1);?>" size="4" maxlength="4" onchange="change_num('quantity','','',<?php echo $p_a_quan;?>)">                  
        <div id="calculation-add">
        <a style="display:block;" <?php echo $void_href;?> onClick="change_num('quantity','up',1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/nup.gif" alt="+"></a>
        <a style="display:block;" <?php echo $void_href;?> onClick="change_num('quantity','down', 1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/ndown.gif" alt="-"></a>
        </div>
        <span> <?php echo TEXT_UNIT;?></span>
        </td>
        </tr>
        <tr>
        	<td width="20%">
       		 <div class="calc_show_price"><input type="hidden" id="change_flag" name="change_num_flag" value="<?php echo isset($_POST['change_num_flag']) ? $_POST['change_num_flag'] : 'false';?>"><?php echo TEXT_PRODUCT_SUBTOTAL;?></div></td>
            <td>
             <div id="show_price"></div>
            </td>
            <td>
              <div id="calculation-bottom"><?php echo tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></div> 
            </td>
        </tr>
        </table>
       
        <?php
    }
  ?>
    </div> 
    <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></form>
    <ul id="detail-ul">
    <hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none;">
    <li onClick="document.open_ost.submit();">      
    <?php echo   tep_draw_form('open_ost',tep_href_link('open.php', '', 'SSL'),'get');?>
    <?php echo tep_draw_hidden_field('products', $product_info['products_name']) ;?>
    <a <?php echo $void_href;?> ><img src="images/ask01.gif"><?php echo TEXT_CONTACT_US;?></a>
    </li>
    <li> <a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,'products_id='.(int)$_GET['products_id'])
    ; ?>"><img src="images/Record.gif"><?php echo BOX_REVIEWS_WRITE_REVIEW;?></a>
    </li>
    <li>                  <a href="<?php echo
    tep_href_link(FILENAME_TELL_A_FRIEND,'products_id='.(int)$_GET['products_id']);
  ?>"><img src="images/Collection.gif"><?php echo BOX_HEADING_TELL_A_FRIEND;?></a></li>                   
    </ul>
    </form>
    </div>
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
      <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"
      border="1">
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
            <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . 'products/' .  $product_info['products_image']) . '" rel="lightbox[products]">' .  tep_image2(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], PRODUCT_INFO_SMALL_IMAGE_WIDTH, PRODUCT_INFO_SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'; ?>
            </noscript>
            <?php
        }
        ?>
          <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image'], $product_info['products_name'],PRODUCT_INFO_SMALL_IMAGE_WIDTH, PRODUCT_INFO_SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'.$sub_colors['color_to_products_name']; ?>
          <?php
          $cnt++;
        if($cnt > 6) {
          $cnt=0;
        }
      }
  }
  if (tep_not_null($product_info['products_url'])) {
    ?>
      <p class="box_des"><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></p>
      <?php
  }
  if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
    ?>
      <p class="box_des"><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])); ?></p>
      <?php
  }
}
?>
</div> 
</div>
<?php include('includes/float-box.php');?>
</div>  
<?php include('includes/shopping.php');?>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
