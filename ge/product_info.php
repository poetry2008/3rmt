<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  check_uri('/(.*)\{(.*)\}(.*)/'); 
  if (tep_whether_show_products((int)$_GET['products_id'])) {
    forward404(); 
  }
  require(DIR_WS_ACTIONS . 'product_info.php');
?>


<?php page_head();?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
var jq = jQuery.noConflict();
</script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript">
jq(document).ready(function () {
   calc_product_final_price("<?php echo (int)$_GET['products_id'];?>"); 
   var actiontime =new Date().getTime();  
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
   
   jq.getJSON("<?php echo HTTP_SERVER;?>"+"/ajax_process.php?action=calc_price&p_id="+pid+"&oprice="+attr_price+"&qty="+jq('#quantity').val(), function(msg) { 
       document.getElementById("show_price").innerHTML = msg.price; 
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
      jq('#'+o_str).parent().html("<input type='hidden' id='"+o_str+"' name='"+p_str+"' value=\""+t_str+"\">"); 
      
      item_info = p_str.split('_');
      item_id = item_info[3];
      var r_tmp_name = p_str.substr(3);
      if (t_str == '') {
        jq('#tp1_'+r_tmp_name).val(0);
      } else {
        jq('#tp1_'+r_tmp_name).val(r_price);
      }
      calc_product_final_price("<?php echo (int)$_GET['products_id'];?>");
}

function change_num(ob,targ, quan, a_quan)
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

//--></script>
</head><body>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<div id="main">
  <!-- left_navigation -->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- left_navigation_eof -->
  <!-- body_text -->
  <div id="cgi">
    <div id="body_text">
      <?php
  $product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID, $languages_id,true,'product_info');
  if (!$product_info) { // product not found in database
?>
      <P><?php echo TEXT_PRODUCT_NOT_FOUND; ?></P>
      <div align="right">
        <a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a>
      </div>
      <?php
  } else {
    // ccdd
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
      <div class="headerNavigation">
        <?php echo $breadcrumb->trail(' &raquo; '); ?>
      </div>
    <?php if (tep_show_warning(tep_get_products_categories_id($product_info['products_id'])) or $product_info['products_status'] != '1') {
      echo '<div class="waring_product">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
    } ?>
      <h1 class="pageHeading"><?php echo ds_tep_get_categories((int)$_GET['products_id'],1) ; ?></h1>
      <div class="line">
        <?php echo ds_tep_get_categories((int)$_GET['products_id'],2) ; ?>
      </div>
      <h2><?php echo $product_info['products_name']; ?></h2>
      <br>
      <table class="box_des" width="98%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="box_des_size">
                    <tr>
                      <td class="main" width="85"><b><font color="#0070AF">商品コード</font></b></td>
                      <td class="main">
                        <?php if (PRODUCT_LIST_MODEL > 0){ echo $product_info['products_model'] ; }else{ echo '-' ; } ?>
                      </td>
                    </tr>
                    <?php 
                      if(!empty($product_info['products_attention_1_1']) && !empty($product_info['products_attention_1_3'])){
                      ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF"><?php echo $product_info['products_attention_1_1'] ; ?></font></b></td>
                      <td class="main"><?php echo $product_info['products_attention_1_2'] .'&nbsp;&nbsp;'.tep_display_attention_1_3($product_info['products_attention_1_3']) . $product_info['products_attention_1_4'] ; ?></td>
                    </tr>
                    <?php } ?>

                      <?php
                      if(!empty($data1[0])){
                      ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF"><?php echo $data1[0] ; ?></font></b></td>
                      <td class="main"><?php echo $data1[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php 
                      if(!empty($data2[0])){
                      ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF"><?php echo $data2[0] ; ?></font></b></td>
                      <td class="main"><?php echo $data2[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF">メーカー名</font></b></td>
                      <td class="main">
                        <?php include(DIR_WS_BOXES.'manufacturer_info.php') ; ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><b><font color="#0070AF">価格</font></b></td>
                      <td class="main">
                        <?php
                                  # 追加スタート ---------------------------------------
                                  # -- 注文数量と単価のリスト --------------------------
                                  if(tep_not_null($product_info['products_small_sum'])) {
                                    $wari_array = array();
                                    echo '<span class="smallText">単位は1個あたりの価格となります</span><table border="0" cellpadding="0" cellspacing="0" class="box_des_size">';
                                    $parray = explode(",", $product_info['products_small_sum']);
                                    for($i=0; $i<sizeof($parray); $i++) {
                                      $tt = explode(':', $parray[$i]);
                                      $wari_array[$tt[0]] = $tt[1];
                                    }

                                    @ksort($wari_array);
                                  
                                    foreach($wari_array as $key => $val) {
                                      echo '<tr>';
                                      echo '<td class="main" align="right">'.$key.'個以上&nbsp;注文すると&nbsp;</td>';
                                      echo '<td class="main"><b>'.$currencies->display_price(round($pricedef + $val),0).'</b></td>';
                                      echo '</tr>'."\n";
                                    }
                                    echo '</table>'."\n";
                                  } else {
                                    echo '<strong>'.$products_price.'</strong>';
                                  }
                                  
                                  # -- 注文数量と単価のリスト --------------------------
                                  # 追加エンド -------------------------------------------
                                
                                ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><b><font color="#0070AF">注文可能数</font></b></td>
                      <td class="main">残り<b>&nbsp;<?php echo tep_show_quantity($product_info['products_quantity']); ?></b>&nbsp;個</td>
                    </tr>
                    <?php 
                      if(!empty($data3[0])){
                      ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF"><?php echo $data3[0] ; ?></font></b></td>
                      <td class="main"><?php echo $data3[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php 
                      if(!empty($data4[0])){
                      ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF"><?php echo $data4[0] ; ?></font><b></td>
                      <td class="main"><?php echo $data4[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && !$product_info['products_bflag']) { ?>
                    <tr>
                      <td class="main"><b><font color="#0070AF">ポイント</font></b></td>
                      <td class="main"><?php echo ds_tep_get_point_value($_GET['products_id']) ; ?>&nbsp;ポイント</td>
                    </tr>
                    <?php } ?>
                    <?php
// ccdd
if (false) {
$tag_query = tep_db_query("
    SELECT t.tags_id, 
           t.tags_images, 
           t.tags_name 
    FROM " . TABLE_PRODUCTS_TO_TAGS . " pt, " . TABLE_TAGS . " t 
    WHERE t.tags_id = pt.tags_id 
      AND pt.products_id='" . $product_info['products_id'] . "'
");
if(tep_db_num_rows($tag_query)){
?>
                      <tr> 
                      <td class="main"><b><font color="#0070AF">タグ</font></b></td> 
                      <td class="main">
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
          </td>
          <td valign="top" width="200">
            <table class="box_des" width="100%" border="0" cellpadding="0" cellspacing="0">
              <?php
        if (tep_not_null($product_info['products_image'])) {
    ?>
              <tr>
                <td align="center" class="smallText">
                  <script type="text/javascript">
                      <!--
document.write('<?php echo '<a href="'.DIR_WS_IMAGES . 'products/' . $product_info['products_image'].'" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . 'products/'. $product_info['products_image'], addslashes($product_info['products_name']), PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'name="lrgproduct" id="lrgproduct"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '<\'+\'/a>'; ?>');
                        -->
                        </script>
                  <noscript>
                  <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES .  urlencode($product_info['products_image'])) . '">' . tep_image3(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
                  </noscript>
                </td>
              </tr>
              <tr>
                <td align="center">
                  <table class="box_des" border="0" cellspacing="6" cellpadding="0">
                      <tr>
                        <?php if (tep_not_null($product_info['products_image'])) { ?>
                        <td width="60" height="60" align="center" class="image_border"><a href="<?php echo DIR_WS_IMAGES .'products/'. $product_info['products_image'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES .'products/'. $product_info['products_image'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
                        <?php if (tep_not_null($product_info['products_image2'])) { ?>
                        <td width="60" align="center" class="image_border"><a href="<?php echo DIR_WS_IMAGES . 'products/'.$product_info['products_image2'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES .'products/'. $product_info['products_image2'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
                        <?php if (tep_not_null($product_info['products_image3'])) { ?>
                        <td width="60" align="center" class="image_border"><a href="<?php echo DIR_WS_IMAGES.'products/'.$product_info['products_image3'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES .'products/'. $product_info['products_image3'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
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
      <table width="99%" border="0" cellpadding="0" cellspacing="0">
         <tr class="header">
           <td height="30" class="main">
           <div class="option_dot">
<?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=process')); ?>
                  <?php
      if($product_info['products_quantity'] < 1) {
        if($product_info['products_bflag'] == '1') {
          # 買い取り商品
          echo '<br><span class="markProductOutOfStock">一時停止';
        } elseif ($product_info['products_cflag'] == '0') {
          echo '<br><span class="markProductOutOfStock">売り切れ';
        } else {
          # 通常商品
          echo '<br><span class="markProductOutOfStock">在庫切れ';
        }
        if ($product_info['preorder_status'] == '1') {
          echo '<br><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle" alt=""><a href=' .  tep_preorder_href_link($product_info['products_id'], $product_info['romaji']) . '>' . $product_info['products_name'] . 'を予約する</a>';
        }
        echo '</span>'; 
      }else{    
      ?>
      <?php
      $p_cflag = tep_get_cflag_by_product_id($product_info['products_id']); 
      $hm_option->render($product_info['belong_to_option'], false, 0, '', '', $p_cflag);
      ?>
         <table class="box_des_size" width="100%" border="0" cellpadding="3" cellspacing="1">
            <tr>
              <td width="85">数量:</td>
              <td colspan="2"><table border="0" cellpadding="0" cellspacing="0">
              <tr>
              <td>
                <input name="quantity" type="text" id="quantity" value="1" size="10" maxlength="4" style="text-align:right;">
              </td>
              <td width="15">
                <div>
<?php $p_a_quan = $product_info['products_quantity'];?>
                  <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','up',1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/nup.gif" alt="+"></a>
                  <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','down', 1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/ndown.gif" alt="-"></a>
                </div>
              </td>
              <td align="left">個</td>
              </tr></table>
            </td>
            
          </tr>
          <tr>
            <td width="85">
            <div class="calc_show_price">価格:</div> 
            </td> 
            <td colspan="4" align="left">
            <div id="show_price"></div> 
            </td> 
            <td align="right" style="padding:0;"><?php echo tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></td>
          </tr>
        </table>
        <?php
     }
    ?>
<?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></form>
</div>
          </td>
        </tr>
        <tr class="header">
          <td height="40" align="right" valign="bottom" class="smallText">
                  <div class="option_dot">
                  <table class="table_a_spacing"><tr><td>
                  <a  href="<?php echo tep_href_link(FILENAME_TELL_A_FRIEND,'products_id='.(int)$_GET['products_id']) ;  ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/tellafriend.jpg',BOX_HEADING_TELL_A_FRIEND);?></a>
                  &nbsp;</td><td>
                  <a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,'products_id='.(int)$_GET['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/review.jpg',BOX_REVIEWS_WRITE_REVIEW);?></a>
                  &nbsp;</td><td valign="top">
                  <?php echo tep_draw_form('open',tep_href_link('open.php','','SSL'),'get');?><input type="image" style="padding-bottom:3px;" src="<?php echo DIR_WS_IMAGES;?>design/button/contact_us.jpg"><?php echo tep_draw_hidden_field('products', $product_info['products_name']) ; ?></form>
                  </td></tr></table></div>
          </td>
        </tr>

      </table>
      <?php
                    //サブ画像
        // ccdd
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
      <!-- //color image -->
      <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <?php
                    while($sub_colors = tep_db_fetch_array($sub_colors_query)) {
                      //色名を取得
          // ccdd
          $colors_name_query = tep_db_query("
              SELECT color_name 
              FROM ".TABLE_COLOR." 
              WHERE color_id = '".$sub_colors['color_id']."'
          ");
                      $colors_name_result = tep_db_fetch_array($colors_name_query);
                      
                      $mcnt++;
                      if($mcnt == 1) {
                      ?>
          <script type="text/javascript"><!--
    document.write('<?php //echo '<td class="smallText" align="center"><a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\\\')">' . tep_image2(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2"  class="image_border"') . '</a><br>-</td>'; ?>');
    --></script>
          <noscript>
          <?php echo '<td class="smallText" align="center" width="20%"><a href="' . tep_href_link(DIR_WS_IMAGES . $product_info['products_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>-</td>'; ?>
          </noscript>
          <?php
                      }
                       // $cnt++;
                      ?>
          <?php echo '<td class="smallText" align="center" width="20%"><a href="' . tep_href_link(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image'], $product_info['products_name'],SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'.$sub_colors['color_to_products_name'].'</td>'; ?>
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
      <!-- //color image -->
      <?php
        }
 ?>
      <div class="sep">
        &nbsp;
      </div>
      <h3><?php echo $product_info['products_name']; ?>について</h3>
      <br>
      <!-- 説明文　-->
      <div class="box_des">
        <?php 
            echo $description;
            ?>
      </div>
      <?php
    include(DIR_WS_BOXES.'reviews.php') ;
?>
<?php

    if (tep_not_null($product_info['products_url'])) {
?>
      <p class="box_des"><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></p>
      <?php
    }

    if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
?>
      <p class="box_des"><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($product_info['products_date_available'])); ?></p>
      <?php
    } else {
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
      
      <?php
      if (tep_session_is_registered('affiliate_id')) {
?>
      <h1 class="pageHeading_long"><?php echo 'アフィリエイト広告用タグ' ; ?>
      </h1>
      <p class="box_des"><b>この商品の広告を登録することができます！！</b><br>
        あなたのホームページにこの商品を表示させるには以下のソースコードをコピーしてホームページにペースとしてください。この商品の画像が表示されます。</p>
      <textarea class="boxText" style="width:95%; height:90px; "><a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$_GET['products_id'].'&ref='.$affiliate_id ; ?>" class="blank"><?php echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"');?><br><?php echo $product_info['products_name'] ; ?> </a>
</textarea>
      <p align="center" class="box_des">実際に表示されるイメージ<br>
        <a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$_GET['products_id'].'&ref='.$affiliate_id ; ?>" class="blank"><?php echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?><br>
        <?php echo $product_info['products_name'] ; ?>
        </a>
      </p>
      <?php
   }
 ?>
      <!-- body_text_eof -->
    </div>
  </div>
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
