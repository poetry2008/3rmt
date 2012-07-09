<?php
/*
   $Id$
   ファイルコードを確認
 */
require('includes/application_top.php');
check_uri('/(.*)\{(.*)\}(.*)/'); 
if (tep_whether_show_products((int)$_GET['products_id'])) {
  forward404(); 
}
require(DIR_WS_ACTIONS . 'product_info.php');
$product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID, $languages_id,true,'product_info');
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
<?php if($p_image_count>1){ ?>
<script type="text/javascript" src="js/jquery.featureCarousel.js" ></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.js"></script>
<link rel="stylesheet" href="css/jquery.lightbox-0.5.css" type="text/css">
<link rel="stylesheet" href="css/feature-carousel.css" type="text/css">
<script type="text/javascript">
<!--//
$(document).ready(function() {
  var carousel = $("#carousel").featureCarousel({
    });
  $("#carousel a").lightBox();
});
//-->
</script>
<?php
}else{
?>
    <script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<?php } ?>
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
        if(!empty($data1[0])){
          ?>
            <tr>
            <td><b><?php echo $data1[0] ; ?></b></td>
            <td><?php echo $data1[1] ; ?></td>
            </tr>
            <?php } ?>
            <?php 
            if(!empty($data2[0])){
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
//# 追加スタート ---------------------------------------
//# -- 注文数量と単価のリスト --------------------------
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
//# -- 注文数量と単価のリスト --------------------------
//# 追加エンド -------------------------------------------
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
    if(!empty($data3[0])){
      ?>
        <tr>
        <td><b><?php echo $data3[0] ; ?></b></td>
        <td><?php echo $data3[1] ; ?></td>
        </tr>
        <?php } ?>
        <?php 
        if(!empty($data4[0])){
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
    <div id="detail-div">  <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')); ?>
    <div id="detail-div-number"> 
    <?php
    if($product_info['products_quantity'] < 1) {
      if($product_info['products_bflag'] == '1') {
# 買い取り商品
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
      // ccdd
      $products_attributes_query = tep_db_query("
          SELECT count(*) as total 
          FROM " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
          WHERE patrib.products_id = '" . (int)$_GET['products_id'] . "' 
          AND patrib.options_id  = popt.products_options_id 
          AND popt.language_id   = '" . $languages_id . "'
          ");
      $products_attributes = tep_db_fetch_array($products_attributes_query);
      if ($products_attributes['total'] > 0) {
        echo "<div style='overflow: hidden;'>"; 
        echo ''."\n".'<p>' . TEXT_PRODUCT_OPTIONS . '</p>' ;
        // ccdd
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
            // ccdd
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
              }
            }
            echo tep_draw_pull_down_menu('id[' .
                $products_options_name['products_options_id'] . ']' ,
                $products_options_array,
                isset($cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']])?$cart->contents[$_GET['products_id']]['attributes'][$products_options_name['products_options_id']]:NULL);          echo "</div>"; 
          }
        } else {
          while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            // ccdd
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
        <span><?php echo TEXT_PRODUCTS_QTY;?></span>
        <input name="quantity" type="text" id="quantity" style="text-align:right;"
        value="1" size="4" maxlength="4">                  <?php $p_a_quan = $product_info['products_quantity'];?>
        <div id="calculation-add">
        <a style="display:block;" <?php echo $void_href;?> onClick="change_num('quantity','up',1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/nup.gif" alt="+"></a>
        <a style="display:block;" <?php echo $void_href;?> onClick="change_num('quantity','down', 1,<?php echo $p_a_quan;?>);return false;"><img src="images/ico/ndown.gif" alt="-"></a>
        </div>
        <span> <?php echo TEXT_UNIT;?></span>
        <div id="calculation-bottom"><?php echo tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></div>
        </div>
        <?php
    }
  ?>
    </div> 
    <?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></form>
    <ul id="detail-ul">
    <hr width="100%" style="border-bottom:1px dashed #ccc; height:2px; border-top:none; border-left:none; border-right:none;">
    <li onClick="document.open_ost.submit();">      
    <?php echo   tep_draw_form('open_ost',tep_href_link('open.php'),'get');?>
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
      <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"
      border="1">
      <tr>
      <?php
      while($sub_colors = tep_db_fetch_array($sub_colors_query)) {
        //色名を取得
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
            <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES .  $product_info['products_image']) . '" rel="lightbox[products]">' .  tep_image3(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'; ?>
            </noscript>
            <?php
        }
        ?>
          <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image']) . '" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . 'colors/' . $sub_colors['color_image'], $product_info['products_name'],SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2" class="image_border"') . '</a><br>'.$sub_colors['color_to_products_name']; ?>
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
