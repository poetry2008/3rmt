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
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
function showimage($1) {
    document.images.lrgproduct.src = $1;
}

//--></script>
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
function change_num(ob, targ, quan,a_quan)
{
  var product_quantity = document.getElementById(ob);
  var product_quantity_num = parseInt(product_quantity.value);
  if (targ == 'up')
  { 
    if (product_quantity_num >= a_quan)
    {
      num_value = product_quantity_num;
    } else {
      num_value = product_quantity_num + quan; 
    }
  } else {
    if (product_quantity_num <= 1)
    {
      num_value = product_quantity_num;
    } else { 
      num_value = product_quantity_num - quan;
    }
  }

  product_quantity.value = num_value;
}
-->
</script>
</head><body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
      <!-- body_text //-->
  <div class="yui3-u" id="layout">
        <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
     <?php include('banner/info_banner.php');?> 

   
     <!--开始产品-->
<div id="product-switching">

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



     
<h1 ><?php echo ds_tep_get_categories((int)$_GET['products_id'],1) ; ?></h1>
      <div>
        <?php  echo ds_tep_get_categories((int)$_GET['products_id'],2) ; ?>
      </div>
      <h2><?php echo $product_info['products_name']; ?></h2>

   


             

 
</div>
<!--结束-->

    <div id="main-content">

     <table width="100%" cellspacing="0" cellpadding="0" border="0" > 
               <tr>
                     <td width="20%"><font color="#00A2E8" size="2"><b>商品コード</b></font></td>
                      <td>
                        <?php if (PRODUCT_LIST_MODEL > 0){ echo $product_info['products_model'] ; }else{ echo '-' ; } ?>

                      </td>
               </tr>
                    <?php 
                      if(!empty($product_info['products_attention_1_1']) && !empty($product_info['products_attention_1_3'])){
                      ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b><?php echo $product_info['products_attention_1_1'] ; ?></b></font></td>
                      <td><?php echo $product_info['products_attention_1_2'] .'&nbsp;&nbsp;'.tep_display_attention_1_3($product_info['products_attention_1_3']) . $product_info['products_attention_1_4'] ; ?></td>
                    </tr>
                    <?php } ?>
                      <?php
                      if(!empty($data1[0])){
                      ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b><?php echo $data1[0] ; ?></b></font></td>
                      <td><?php echo $data1[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php 
                      if(!empty($data2[0])){
                      ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b><?php echo $data2[0] ; ?></b></font></td>
                      <td><?php echo $data2[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b>メーカー名</b></font></td>
                      <td>
                        <?php include(DIR_WS_BOXES.'manufacturer_info.php') ; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b>価格</b></font></td>
                      <td>
                        <?php
                                  # 添加开始 ---------------------------------------
                                  # -- 订单数量和单价列表 --------------------------
                                  if(tep_not_null($product_info['products_small_sum'])) {
                                    $wari_array = array();
                                    echo '<span class="smallText">単位は1個あたりの価格となります</span>';
                                    $parray = explode(",", $product_info['products_small_sum']);
                                    for($i=0; $i<sizeof($parray); $i++) {
                                      $tt = explode(':', $parray[$i]);
                                      $wari_array[$tt[0]] = $tt[1];
                                    }

                                    @ksort($wari_array);
                                  
                                    foreach($wari_array as $key => $val) {
                                         echo '<p>'.$key.'個以上&nbsp;注文すると&nbsp;';
                                      echo '<b>'.$currencies->display_price(round($pricedef + $val),0).'</b></p>';
                                                                       }
                                                                      } else {
                                    echo '<strong>'.$products_price.'</strong>';
                                  }
                                  
                                  # -- 订单数量和单价列表 --------------------------
                                  # 添加结束 -------------------------------------------
                                
                                ?>
                      </td>
                    </tr>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b>注文可能数</b></font></td>
                      <td>残り<b>&nbsp;<?php echo tep_show_quantity($product_info['products_quantity']); ?></b>&nbsp;個</td>
                    </tr>
                    <?php 
                      if(!empty($data3[0])){
                      ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b><?php echo $data3[0] ; ?></b></font></td>
                      <td><?php echo $data3[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php 
                      if(!empty($data4[0])){
                      ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b><?php echo $data4[0] ; ?></b></font></td>
                      <td><?php echo $data4[1] ; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && !$product_info['products_bflag']) { ?>
                    <tr>
                      <td><font color="#00A2E8" size="2"><b>ポイント</b></font></td>
                      <td><?php echo ds_tep_get_point_value($_GET['products_id']) ; ?>&nbsp;ポイント</td>
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
                      <td><font color="#00A2E8"><b>タグ</b></font></td> 
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
<!--datail-div start-->
    <div id="detail-div">  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')); ?>
     
  <div id="detail-div-number"> 
             
    <?php
      if($product_info['products_quantity'] < 1) {
        if($product_info['products_bflag'] == '1') {
          # 买取商品
          echo '<span class="markProductOutOfStock">一時停止';
        } elseif ($product_info['products_cflag'] == '0') {
          echo '<span class="markProductOutOfStock">売り切れ';
        } else {
          # 通常商品
          echo '<span class="markProductOutOfStock">在庫切れ';
        }
        if ($product_info['preorder_status'] == '1') {
          echo '<img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle" alt=""><a href=' .  tep_preorder_href_link($product_info['products_id'], $product_info['romaji']) . '>' . $product_info['products_name'] . 'を予約する</a>';
        }
        echo '</span>'; 
      }else{    
        
        $products_attributes_query = tep_db_query("
            SELECT count(*) as total 
            FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
            WHERE patrib.products_id = '" . (int)$_GET['products_id'] . "' 
              AND patrib.options_id  = popt.products_options_id 
              AND popt.language_id   = '" . $languages_id . "'
        ");
        $products_attributes = tep_db_fetch_array($products_attributes_query);
        if ($products_attributes['total'] > 0) {
          echo '<!-- 商品オプション -->' ;
       
          
         echo "<div style='overflow: hidden;'>"; 
      
      echo ''."\n".'<p>' . TEXT_PRODUCT_OPTIONS . '</p>' ;
    
      $products_options_name_query = tep_db_query("
          SELECT distinct popt.products_options_id, 
                 popt.products_options_name 
          FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
          WHERE patrib.products_id = '" . (int)$_GET['products_id'] . "' 
            AND patrib.options_id  = popt.products_options_id 
            AND popt.language_id   = '" . $languages_id . "'
      ");


 if ($product_info['option_image_type'] == 'select') {
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            echo '<span>' . $products_options_name['products_options_name'] .
              ':</span>' . "\n";
                  
        $products_options_query = tep_db_query("
            SELECT pov.products_options_values_id, 
                   pov.products_options_values_name, 
                   pa.options_values_price, 
                   pa.price_prefix, 
                   pa.products_at_quantity, 
                   pa.products_at_quantity 
            FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
            WHERE pa.products_id = '" . (int)$_GET['products_id'] . "' 
              AND pa.options_id = '" . $products_options_name['products_options_id'] . "' 
              AND pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "' 
            ORDER BY pa.products_attributes_id");

            while ($products_options = tep_db_fetch_array($products_options_query)) {
              //add products_at_quantity - ds-style
              if($products_options['products_at_quantity'] > 0) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                if ($products_options['options_values_price'] != '0') {
                  $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                }
                //options stock
    //            $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (在庫:' . $products_options['products_at_quantity'] .') ';
                
              }
            }
//            if (!isset($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']])) $cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']]=NULL;
 //           echo tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']]);

            //bobhero
        echo tep_draw_pull_down_menu('id[' .
            $products_options_name['products_options_id'] . ']' ,
            $products_options_array,
            isset($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']])?$cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']]:NULL);          echo "</div>"; 
     }
   }
 else {
        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
          $selected = 0;
          $products_options_array = array();
          
          $products_options_query = tep_db_query("
              SELECT pov.products_options_values_id, 
                     pov.products_options_values_name, 
                     pa.options_values_price, 
                     pa.price_prefix, 
                     pa.products_at_quantity, 
                     pa.products_at_quantity 
            FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
            WHERE pa.products_id = '" . (int)$_GET['products_id'] . "' 
              AND pa.options_id = '" . $products_options_name['products_options_id'] . "' 
              AND pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "' 
            ORDER BY pa.products_attributes_id");
        $n_row = 0; 
        $start_row = 0; 
                 while ($products_options = tep_db_fetch_array($products_options_query)) {
          if($products_options['products_at_quantity'] > 0) {
            $option_image_raw = tep_db_query("select * from products_options_image where products_options_values_id = '".$products_options['products_options_values_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id desc limit 1"); 
            $option_image_res = tep_db_fetch_array($option_image_raw); 
      echo '<td class="product_list02" align="center"><div style=" height:50px;">'; 
              echo tep_image(DIR_WS_IMAGES.'op_image/'.$option_image_res['option_image'], $products_options['products_options_values_name'], 50, 50); 
              echo '</div><br> <span>';
              echo $products_options_name['products_options_name'].':'.$products_options['products_options_values_name']; 
        echo '</span>';
              if ($products_options['options_values_price'] != '0') {
                echo '<br>(' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
              }
              echo '<br>';
              if (isset($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']])) {
                if ($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']] == $products_options['products_options_values_id']) {
                  echo '<input type="radio" name="id['.$products_options_name['products_options_id'].']" value="'.$products_options['products_options_values_id'].'" checked>';  
                } else {
                  echo '<input type="radio" name="id['.$products_options_name['products_options_id'].']" value="'.$products_options['products_options_values_id'].'">';  
                }
              } else {
                if ($start_row == 0) {
                  echo '<input type="radio" name="id['.$products_options_name['products_options_id'].']" value="'.$products_options['products_options_values_id'].'" checked>';  
                } else {
                  echo '<input type="radio" name="id['.$products_options_name['products_options_id'].']" value="'.$products_options['products_options_values_id'].'">';  
                }
              }
                           $n_row++; 
              $start_row++;
              if ($n_row > 2) {
                              $n_row = 0; 
              }
            }
          }



             }
      }
     }
    ?>


  <div id="calculation">
			<span>数量:</span>
			<input name="quantity" type="text" id="quantity"
                         value="1" size="4" maxlength="4">                  <?php $p_a_quan = $product_info['products_quantity'];?>
				  <div id="calculation-add">
                  <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','up',1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/nup.gif" alt="+"></a>
                  <a style="display:block;" href="javascript:void(0)" onClick="change_num('quantity','down', 1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/ndown.gif" alt="-"></a>
				  </div>
				  <span> 個</span>
				   <div id="calculation-bottom"><?php echo tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></div>
              </div>

                                          <?php
     }
    ?>
	 
     </div> 
 
              <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></form>
                     <ul id="detail-ul">
					   <hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none;">
                <li>                  <a href="<?php echo
                  tep_href_link(FILENAME_TELL_A_FRIEND,'products_id='.(int)$_GET['products_id']);
                  ?>"><img src="image/Collection.png"><?php echo TEXT_YOU;?></a></li>
                 <li> <a href="<?php echo
                  tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,'products_id='.(int)$_GET['products_id'])
                  ; ?>"><img src="image/Record.png"><?php echo TEXT_SHU;?></a>
                  </li>                    <li onClick="document.open.submit();">      
<?php echo   tep_draw_form('open',tep_href_link('open.php'),'get');?>

                 <a href="javascript:void(0)" ><img src="image/ask01.png"><?php echo TEXT_HE;?></a>
                             </li>
                 </ul>
                                   </form>
</div>
  <!--datail-div  end-->
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
      <!-- //color image -->
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
          <script type="text/javascript"><!--
    document.write('<?php //echo '<td class="smallText" align="center"><a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\\\')">' . tep_image2(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2"  class="image_border"') . '</a><br>-</td>'; ?>');
    //--></script>
          <noscript>
          <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $product_info['products_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br'; ?>
          </noscript>
          <?php
                      }
                       // $cnt++;
                      ?>
          <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image'], $product_info['products_name'],SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'.$sub_colors['color_to_products_name']; ?>
          <?php
                      
                      $cnt++;
                      if($cnt > 6) {
                         $cnt=0;
                      }
                    }
    ?>
                <!-- //color image -->
      <?php
        }
 ?>
         <!--  <h3><?php// echo $product_info['products_name']; ?>について</h3>-->
      
      <!-- 说明　-->
      <div class="box_des">
        <?php 
           // echo $description;
            ?>
      </div>
         <!--       
      <p><a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS,'product_id='.(int)$_GET['products_id']) ; ?>"><?php echo TEXT_CURRENT_REVIEWS . ' ' . $reviews_values['count']; ?></a></p>
 -->
      <?php
//    }

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
?>
      <?php
    }
?>
     
     
      <?php
    if ( (USE_CACHE == 'true') && !SID ) {
     // echo tep_cache_also_purchased(3600);
    } else {
      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
    }
  }
?>
       <?php
      if (tep_session_is_registered('affiliate_id')) {
?>
      <h1><?php echo 'アフィリエイト広告用タグ' ; ?></h1>
      <p class="box_des"><b>この商品の広告を登録することができます！！</b><br>
        あなたのホームページにこの商品を表示させるには以下のソースコードをコピーしてホームページにペースとしてください。この商品の画像が表示されます。</p>
      <textarea class="boxText" style="width:95%; height:90px; "><a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$_GET['products_id'].'&ref='.$affiliate_id ; ?>" class="blank"><?php echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"');?><br><?php echo $product_info['products_name'] ; ?> </a>
</textarea>
      <p>実際に表示されるイメージ<br>
        <a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$_GET['products_id'].'&ref='.$affiliate_id ; ?>" class="blank"><?php echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?><br>
        <?php echo $product_info['products_name'] ; ?>
        </a>
      </p>
</div>
            <?php
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
