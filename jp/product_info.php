<?php
/*
  $Id: product_info.php,v 1.2 2003/04/22 04:37:37 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);
  
//begin dynamic meta tags query -->

$the_product_info_query = tep_db_query("select pd.language_id, p.products_id, pd.products_name, pd.products_attention_1, pd.products_attention_2, pd.products_attention_3, pd.products_attention_4, pd.products_attention_5, pd.products_description, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, 
p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'" . " and pd.language_id ='" .  (int)$languages_id . "'"); 
$the_product_info = tep_db_fetch_array($the_product_info_query);
$the_product_name = strip_tags ($the_product_info['products_name'], "");
$the_product_description = mb_substr (strip_tags ($the_product_info['products_description'], ""),0,65);
$the_product_model = strip_tags ($the_product_info['products_model'], "");

$the_manufacturer_query = tep_db_query("select m.manufacturers_id, m.manufacturers_name from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "'), " . TABLE_PRODUCTS . " p  where p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.manufacturers_id = m.manufacturers_id"); 
$the_manufacturers = tep_db_fetch_array($the_manufacturer_query);
// end dynamic meta tags query -->
?>
<?php page_head();?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript">
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
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
function showimage($1) {
	document.images.lrgproduct.src = $1;
}

//--></script>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="left_colum_border"><!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" class="contents">
	  	<?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')) . "\n"; ?>
<?php
  $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_attention_1,pd.products_attention_2,pd.products_attention_3,pd.products_attention_4,pd.products_attention_5,pd.products_description, p.products_model, p.products_quantity, p.products_image,p.products_image2,p.products_image3, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.products_bflag, p.products_cflag, p.products_small_sum from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "'");
  if (!tep_db_num_rows($product_info_query)) { // product not found in database
?>
        <P><?php echo TEXT_PRODUCT_NOT_FOUND; ?></P>
        <div align="right"><a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></div>
        <?php
  } else {
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and language_id = '" . $languages_id . "'");
    $product_info = tep_db_fetch_array($product_info_query);

    if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
      $pricedef = $new_price;
	  $products_price = '<s>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
    } else {
      $pricedef = $product_info['products_price'];
	  $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
    }
	
	//Add ds-style - 2005.11.29
	// edit 2009.5.14 maker
	//$description = explode("|-#-|", $product_info['products_description']);
	$description = $product_info['products_description'];
	//data1
	$data1 = explode("//", $product_info['products_attention_1']);
	//data1
	$data2 = explode("//", $product_info['products_attention_2']);
	//data1
	$data3 = explode("//", $product_info['products_attention_3']);
	//data1
	$data4 = explode("//", $product_info['products_attention_4']);
	//data1
	//$data5 = explode("//", $product_info['products_attention_5']);
?>
<?php 
			//if(tep_not_null(ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1)) { 
			//    echo tep_image(DIR_WS_IMAGES.tep_not_null(ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1) ;
            //  }else{
			//    echo '' ;
            //  }
?>
        <h1 class="pageHeading_long"><?php echo $product_info['products_name']; ?></h1>
        <h2 class="line"><?php echo ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1); ?> <?php echo ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],2); ?></h2>
        <table width="689"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="250" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">


                <?php
    if (tep_not_null($product_info['products_image'])) {
?>
                <tr>
                  <td align="center" class="smallText"><script type="text/javascript"><!--
document.write('<?php echo '<a href="images/'.$product_info['products_image'].'" rel="lightbox[products]">' . tep_image3(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_name']), PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'name="lrgproduct" id="lrgproduct"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '<\'+\'/a>'; ?>');
--></script>
                    <noscript>
                    <?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . urlencode($product_info['products_image'])) . '">' . tep_image3(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
                    </noscript>
                  </td>
                </tr>
                <tr>
                  <td align="center"><table border="0" cellspacing="6" cellpadding="0">
                      <tr>
                        <?php if (tep_not_null($product_info['products_image'])) { ?>
                        <td width="60" height="60" align="center" class="image_border"><a href="images/<?php echo $product_info['products_image'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
                        <?php if (tep_not_null($product_info['products_image2'])) { ?>
                        <td width="60" align="center" class="image_border"><a href="images/<?php echo $product_info['products_image2'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES . $product_info['products_image2'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
                        <?php if (tep_not_null($product_info['products_image3'])) { ?>
                        <td width="60" align="center" class="image_border"><a href="images/<?php echo $product_info['products_image3'] ; ?>" rel="lightbox[products]"><?php echo tep_image2(DIR_WS_IMAGES . $product_info['products_image3'], $product_info['products_name'], 50, 50, 'name="prod_thum_1"') ;?></a></td>
                        <?php } ?>
                      </tr>
                    </table>
                    <?php
    }
?>
                  </td>
                </tr>
              </table></td>
            <td><img src="images/design/spacer.gif" width="15" height="1" alt="img"></td>
            <td valign="top"><table width="100%"  border="0" cellpadding="0" cellspacing="0" class="infoBox">
                <tr>
                  <td><table width="100%" border="0" cellpadding="3" cellspacing="1">
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF">商品コード</font></td>
                        <td class="main"><?php if (PRODUCT_LIST_MODEL > 0){ echo $product_info['products_model'] ; }else{ echo '-' ; } ?></td>
                      </tr>
                      <?php 
				  if(!empty($data1[0])){
				  ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF"><?php echo $data1[0] ; ?></font></td>
                        <td class="main"><?php echo $data1[1] ; ?></td>
                      </tr>
                      <?php } ?>
                      <?php 
				  if(!empty($data2[0])){
				  ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF"><?php echo $data2[0] ; ?></font></td>
                        <td class="main"><?php echo $data2[1] ; ?></td>
                      </tr>
                      <?php } ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF">メーカー名</font></td>
                        <td class="main"><?php include(DIR_WS_BOXES.'manufacturer_info.php') ; ?></td>
                      </tr>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF">価格</font></td>
                        <td class="main">
							<?php
							  # 追加スタート ---------------------------------------
							  # -- 注文数量と単価のリスト --------------------------
							  if(tep_not_null($product_info['products_small_sum'])) {
								$wari_array = array();
								echo '<span class="smallText">単位は1個あたりの価格となります</span><table border="0" cellpadding="0" cellspacing="0" class="infoBox">';
								$parray = explode(",", $product_info['products_small_sum']);
								for($i=0; $i<sizeof($parray); $i++) {
								  $tt = explode(':', $parray[$i]);
								  $wari_array[$tt[0]] = $tt[1];
								}
								
								@ksort($wari_array);
							  
								foreach($wari_array as $key => $val) {
								  echo '<tr class="infoBoxContents">';
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
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF">注文可能数</font></td>
                        <td class="main">残り<strong>&nbsp;<?php echo $product_info['products_quantity']; ?></strong>&nbsp;個</td>
                      </tr>
<?php 
$tag_query = tep_db_query("select t.tags_id, t.tags_images, t.tags_name from " . TABLE_PRODUCTS_TO_TAGS . " pt, " . TABLE_TAGS . " t where t.tags_id = pt.tags_id and pt.products_id='" . $product_info['products_id'] . "'");
if(tep_db_num_rows($tag_query)){
  ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF"><?php echo TEXT_PRODUCT_TAGS;?></font></td>
                      <td>
                      <?php
while($tag = tep_db_fetch_array($tag_query)) {
  ?>
 <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'tags_id=' .  $tag['tags_id']);?>">
<?php if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $tag['tags_images']) && $tag['tags_images'])
 {
   echo tep_image(DIR_WS_IMAGES . $tag['tags_images'], $tag['tags_name'] , 20, 15);
 }
  else
  { 
    $tag['tags_name'];
  }
  ?>
</a>
 &nbsp;&nbsp;
 <?php
}
?>

                      </td>
                      </tr>
                      <?php 
}
				  if(!empty($data3[0])){
				  ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF"><?php echo $data3[0] ; ?></font></td>
                        <td class="main"><?php echo $data3[1] ; ?></td>
                      </tr>
                      <?php } ?>
                      <?php 
				  if(!empty($data4[0])){
				  ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF"><?php echo $data4[0] ; ?></font></td>
                        <td class="main"><?php echo $data4[1] ; ?></td>
                      </tr>
                      <?php } ?>
                      <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') { ?>
                      <tr class="infoBoxContents">
                        <td class="main"><font color="#0070AF">ポイント</font></td>
                        <td class="main"><?php echo ds_tep_get_point_value($HTTP_GET_VARS['products_id']) ; ?>&nbsp;ポイント</td>
                      </tr>
                      <?php } ?>
                    </table></td>
                </tr>
                <tr class="header2">
                  <td height="30" class="main" style="padding-bottom:4px; " align="right"><?php
  if($product_info['products_quantity'] < 1) {
    if($product_info['products_bflag'] == '1') {
	  # 買い取り商品
	  echo '<span class="markProductOutOfStock">一時停止</span>';
	} elseif ($product_info['products_cflag'] == '0') {
	  echo '<span class="markProductOutOfStock">売り切れ</span>';
	} else {
	  # 通常商品
	  echo '<br><span class="markProductOutOfStock">在庫切れ<br><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle" alt=""><a href=' . tep_href_link(FILENAME_PREORDER, 'products_id=' . $HTTP_GET_VARS['products_id']) . '>' . $product_info['products_name'] . 'を予約する</a></span>';
	}
  }else{	


    $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
    $products_attributes = tep_db_fetch_array($products_attributes_query);
    if ($products_attributes['total'] > 0) {
      echo '<!-- 商品オプション -->' ;
	  echo '<br>'."\n".'<b>' . TEXT_PRODUCT_OPTIONS . '</b><br>' .
           '<table border="0" cellpadding="2" cellspacing="0">';
      $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $languages_id . "'");
      while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
        $selected = 0;
        $products_options_array = array();
        echo '<tr><td class="main">' . $products_options_name['products_options_name'] . ':</td><td>' . "\n";
        $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_at_quantity from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $languages_id . "' order by pa.products_attributes_id");
        while ($products_options = tep_db_fetch_array($products_options_query)) {
          //add products_at_quantity - ds-style
		  if($products_options['products_at_quantity'] > 0) {
		    $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
            if ($products_options['options_values_price'] != '0') {
              $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
            }
			//options stock
//			$products_options_array[sizeof($products_options_array)-1]['text'] .= ' (在庫:' . $products_options['products_at_quantity'] .') ';
			
		  }
        }
        //if (!isset($cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']])) $cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']] = NULL; 
        echo tep_draw_pull_down_menu('id[' .  $products_options_name['products_options_id'] . ']' ,
            $products_options_array, isset($cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']])?$cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']]:NULL);
        echo '</td></tr>';
      }
      echo '</table>';
    }
?>
                    <table align="right">
                      <tr>
                        <td class="main">数量:</td>
                        <td class="main">
                        <table>
                        <tr>
                        <td>
                        <input name="quantity" type="text" id="quantity" value="1" size="4" maxlength="4">
</td>
  <td>
  <div class="top_and_bottom">
  <?php
      echo '<a onclick="change_num(\'quantity\',\'up\',1,'.
        $product_info['products_quantity'].')" style="display:block"><img src="images/nup.gif" style="vertical-align:bottom;"></a>';
      echo '<a onclick="change_num(\'quantity\',\'down\',1,'.
        $product_info['products_quantity'].')" style="display:block"><img src="images/ndown.gif" style="vertical-align:top;"></a>';
      ?>
   </div>
        </td>
        <td>
  &nbsp;個&nbsp;
      </td>
  </tr>
  </table>
                        <td><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_image_submit('button_in_cart.jpg', IMAGE_BUTTON_IN_CART); ?></td>
                      </tr>
                    </table>
                    <?php
 }
?>
                  </td>
                </tr>
                <tr class="header2">
                  <td height="40" align="right" valign="bottom" class="smallText"><div class="dot">&nbsp;</div>
                    <a href="<?php echo tep_href_link(FILENAME_TELL_A_FRIEND,'products_id='.(int)$HTTP_GET_VARS['products_id']) ;  ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_tellafriend.jpg',BOX_HEADING_TELL_A_FRIEND);?></a>&nbsp; <a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,'products_id='.(int)$HTTP_GET_VARS['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_review.jpg',BOX_REVIEWS_WRITE_REVIEW);?></a>&nbsp; <a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'products_id='.(int)$HTTP_GET_VARS['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/botton_question.jpg',IMAGE_BUTTON_QUT);?></a> </td>
                </tr>
              </table></td>
          </tr>
        </table>
        <?php
			    //サブ画像
				$sub_colors_query = tep_db_query("select color_image, color_id, color_to_products_name from ".TABLE_COLOR_TO_PRODUCTS." where products_id = '".(int)$HTTP_GET_VARS['products_id']."'");
				$cnt=0;
               if(tep_db_num_rows($sub_colors_query) >= 1) {
?>
        <!-- //color image -->
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <?php
				while($sub_colors = tep_db_fetch_array($sub_colors_query)) {
				  //色名を取得
				  $colors_name_query = tep_db_query("select color_name from ".TABLE_COLOR." where color_id = '".$sub_colors['color_id']."'");
				  $colors_name_result = tep_db_fetch_array($colors_name_query);
				  
				  $mcnt++;
				  if($mcnt == 1) {
				  ?>
            <script type="text/javascript"><!--
document.write('<?php //echo '<td class="smallText" align="center"><a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\\\')">' . tep_image2(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="2" vspace="2"  class="image_border"') . '</a><br>-</td>'; ?>');
//--></script>
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
        <div class="underline">&nbsp;</div>
     	<?php if($description){?>
		<br>
		<h3 class="pageHeading_long"><?php echo $product_info['products_name']; ?>について</h3>
        <!-- 説明文　-->
        <p>
              <?php 
            //Edit ds-style 2005.11.29
            //echo stripslashes($product_info['products_description']);
            echo $description;
            ?>
        </p>
         <?php }?>
        <?php
//    $reviews = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "'");
//    $reviews_values = tep_db_fetch_array($reviews);
//    if ($reviews_values['count'] > 0) {
    include(DIR_WS_BOXES.'reviews.php') ;
?>
        <!-- 		<p><a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS,'product_id='.(int)$HTTP_GET_VARS['products_id']) ; ?>"><?php echo TEXT_CURRENT_REVIEWS . ' ' . $reviews_values['count']; ?></a></p>
 -->
        <?php
//    }

    if (tep_not_null($product_info['products_url'])) {
      //var_dump(urlencode($product_info['products_url']));
?>
        <p><?php echo sprintf(TEXT_MORE_INFORMATION,
            tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' .  urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></p>
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
        <br>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <!--             <td class="main"><a href="<?php echo tep_href_link(FILENAME_PRODUCT_REVIEWS, substr(tep_get_all_get_params(), 0, -1)); ?>"><?php echo tep_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS); ?></a></td>
 -->
            <td align="right" class="main"><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) ; ?></td>
          </tr>
        </table>
        <?php
    if ( (USE_CACHE == 'true') && !SID ) {
      echo tep_cache_also_purchased(3600);
    } else {
      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
    }
  }
?>
        </form>
        <?php
	  if (tep_session_is_registered('affiliate_id')) {
?>
        <h1 class="pageHeading_long"><?php echo 'アフィリエイト広告用タグ' ; ?> </h1>
        <p><b>この商品の広告を登録することができます！！</b><br>
          あなたのホームページにこの商品を表示させるには以下のソースコードをコピーしてホームページにペースとしてください。この商品の画像が表示されます。</p>
        <textarea class="boxText" style="width:95%; height:90px; "><a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$HTTP_GET_VARS['products_id'].'&ref='.$affiliate_id ; ?>" target="_blank"><?php echo tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"');?><br><?php echo $product_info['products_name'] ; ?> </a></textarea>
        <p align="center">実際に表示されるイメージ<br>
          <a href="<?php echo HTTP_SERVER.DIR_WS_CATALOG.FILENAME_PRODUCT_INFO.'?products_id='.(int)$HTTP_GET_VARS['products_id'].'&ref='.$affiliate_id ; ?>" target="_blank"><?php echo tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?><br>
          <?php echo $product_info['products_name'] ; ?> </a></p>
        <?php
   }
 ?>
      </td>
      <!-- body_text_eof //-->
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
