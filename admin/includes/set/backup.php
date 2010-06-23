<?php
//右面边栏
  /*  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
    }*/


//new products
  if (isset($_GET['action']) && $_GET['action'] == 'new_product') {
    require ('includes/set/new_products.php');
    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers['manufacturers_name']);
    }

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    $languages = tep_get_languages();

    if(isset($pInfo->products_status)){
      switch ($pInfo->products_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
      }
    } else{
      $in_status = true; $out_status = false;
    }
    if(isset($pInfo->products_cflag)){
      switch ($pInfo->products_cflag) {
      case '1': $in_cflag = false; $out_cflag = true; break;
      case '0':
      default: $in_cflag = true; $out_cflag = false;
      }
    } else {
      $in_cflag = true; $out_cflag = false;
    }

    if(isset($pInfo->products_bflag)){
      switch ($pInfo->products_bflag) {
      case '1': $in_bflag = false; $out_bflag = true; break;
      case '0':
      default: $in_bflag = true; $out_bflag = false;
      }
    } else {
      $in_bflag = true; $out_bflag = false;
    }
  
    //商品説明を分割
    if(isset($pInfo->products_id)){
      $des_query = tep_db_query("
      select products_attention_1,
             products_attention_2,
             products_attention_3,
             products_attention_4,
             products_attention_5,
             products_description 
      from products_description 
      where language_id = '4' 
        and products_id = '".$pInfo->products_id."' 
        and site_id ='".(tep_products_description_exist($pInfo->products_id,$site_id,4)?$site_id:0)."'"); 
      $des_result = tep_db_fetch_array($des_query);
    }
    ?>
    <link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
    <script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
    <script language="javascript">
    var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo isset($pInfo->products_date_available)?$pInfo->products_date_available:''; ?>",scBTNMODE_CUSTOMBLUE);
    </script>
    <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr><?php echo tep_draw_form('new_product', FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . (isset($_GET['pID'])?$_GET['pID']:'') . '&action=new_product_preview', 'post', 'enctype="multipart/form-data" onSubmit="return mess();"'); ?>
    <input type="hidden" name="site_id" value="<?php echo $site_id;?>">
    <td><table border="0" cellspacing="0" cellpadding="2">
    <tr>
    <td colspan="2"><fieldset>
    <legend style="color:#FF0000 ">商品の基本情報</legend>
    <table>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_status', '1', $in_status, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '0', $out_status, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
    <td class="main">&nbsp;</td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_BUY_AND_SELL; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_bflag', '0', $in_bflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_USUALLY . '&nbsp;' . tep_draw_radio_field('products_bflag', '1', $out_bflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_PURCHASE; ?></td>
    <td class="main">&nbsp;</td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_CHARACTER; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_cflag', '0', $in_cflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_NOT_INDISPENSABILITY . '&nbsp;' . tep_draw_radio_field('products_cflag', '1', $out_cflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_INDISPENSABILITY; ?></td>
    <td class="main">&nbsp;</td>
    </tr>   
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br>
    <small>(YYYY-MM-DD)</small></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>
    <script language="javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
    <td class="main">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, isset($pInfo->manufacturers_id)?$pInfo->manufacturers_id:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
    <td class="main">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_OPTION; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('option_type', tep_get_option_array(), isset($pInfo->option_type)?$pInfo->option_type:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
    <td class="main">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      ?>
      <tr>
        <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_NAME; ?></td>
                                                                           <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_name($pInfo->products_id, $languages[$i]['id'], $site_id):'')), ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <td class="fieldRequired">検索キー</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }
    ?>

    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <input type="hidden" name="products_price_def" value="">
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr bgcolor="#CCCCCC">
    <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_PRICE . '</b></font>'; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price', isset($pInfo->products_price)?$pInfo->products_price:'','id="pp"' . ($site_id ? 'class="readonly" ' : '')); ?></td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr bgcolor="#CCCCCC">
    <td class="main"><?php echo '<font color="blue"><b>増減の値:</b></font>'; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price_offset', $pInfo->products_price_offset, ($site_id ? 'class="readonly" readonly' : '')); ?></td>
    </tr>
    <tr>
    <td class="main">&nbsp;</td>
    <td colspan="2" class="smallText"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 割り引くパーセンテージを "増減の値" 欄に入力することができます。例: 20%'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 新しい価格を入力する場合には、新しい価格を入力してください。例: 1980'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 登録されている情報を消去する場合は、値を空白にしてください。'; ?></td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main" valign="top"><?php echo TEXT_PRODUCTS_SMALL_SUM; ?></td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_small_sum', 'soft', '70', '5', isset($pInfo->products_small_sum)?$pInfo->products_small_sum:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
    </tr>
    <tr>
    <td class="main">&nbsp;</td>
    <td colspan="2" class="smallText">
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;(例１：割増)商品単価を100円とした場合'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 1:20,50:10,100:0'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 1個から49個までの加算値は20→商品単価は120円'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 50個〜99個までの加算値は10→商品単価は110円'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 割引の場合は、加算値を-20の様なマイナス値にして下さい。'; ?><br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; <b>割引は未検証なので入力しないこと！</b>'; ?></td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, isset($pInfo->products_tax_class_id)?$pInfo->products_tax_class_id:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
    </tr>
        
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr bgcolor="#CCCCCC">
    <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_QUANTITY . '</b></font>'; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_quantity', isset($pInfo->products_quantity)?$pInfo->products_quantity:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td class="smallText" colspan="2">在庫計算する場合は入力してください。在庫を計算する場合は　基本設定→在庫管理　を設定してください。</td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', isset($pInfo->products_model)?$pInfo->products_model:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
    <td class="fieldRequired">検索キー</td>
    </tr>

    <tr>
    <td class="main">項目１</td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_jan', isset($des_result['products_attention_1'])?$des_result['products_attention_1']:''); ?><br>
    <span class="smallText">項目名とデータは「//」スラッシュ2本で区切ってください。例）サイズ//H1000　W560</span></td>
    </tr>
    <tr>
    <td class="main">項目２</td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_size', isset($des_result['products_attention_2'])?$des_result['products_attention_2']:''); ?></td>
    </tr>
    <tr>
    <td class="main">項目３</td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_naiyou', isset($des_result['products_attention_3'])?$des_result['products_attention_3']:''); ?></td>
    </tr>
    <tr>
    <td class="main">項目４</td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_zaishitu', isset($des_result['products_attention_4'])?$des_result['products_attention_4']:''); ?></td>
    </tr>
    <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main" valign="top">キャラクタ名</td>
    <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_attention_5', 'soft', '70', '15', isset($des_result['products_attention_5'])?$des_result['products_attention_5']:''); ?></td>
    </tr>
    </table>
    </fieldset></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>

    <tr>
    <td colspan="2"><fieldset>
    <legend style="color:#FF0000">商品の説明文/オプション登録</legend>
    <table>

    <?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      ?>
      <tr>
      <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
      <td class="main"><table border="0" cellspacing="0" cellpadding="0">
      <tr>
      <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
      <td class="main"><?php echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_description($pInfo->products_id, $languages[$i]['id'], $site_id):''))); ?></td>
      </tr>
      </table>
      HTMLによる入力可<br>
      <span class="fieldRequired">検索キー</span></td>
      </tr>
      <?php
    }
    ?>
    <!-- options// -->
    <?php

    //オプションデータ取得
    if(isset($_GET['pID']) && $_GET['pID']) {
      $options_query = tep_db_query("select * from products_attributes where products_id = '".(int)$_GET['pID']."' order by products_attributes_id");
      if(tep_db_num_rows($options_query)) {
        $options_array = '';
        while($options = tep_db_fetch_array($options_query)) {
          $options_array .= tep_get_add_options_name($options['options_id']) . ',' . tep_get_add_options_value($options['options_values_id']) . ',' . (int)$options['options_values_price'] . ',' . $options['price_prefix'] . ',' . $options['products_at_quantity'] . "\n";
        }
      } else {
        $options_array = '';
      }
    } else {
      $options_array = '';
    }
    ?>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main" valign="top">オプション登録</td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_options', 'soft', '70', '15', $options_array, ($site_id ? 'class="readonly" readonly' : '')); ?></td>
    </tr>
    <tr>
    <td></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>「オプション名,オプション値,オプション価格,接頭辞,在庫数」の順で入力（区切りは「,」・改行で複数同時登録可）<br>
    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>例）<br>
    <table border="0" cellspacing="0" cellpadding="3">
    <tr>
    <td class="main">                  言語,日本語,0,+ <br>
    言語,中国語,400,+ <br>
    言語,韓国語,100,-</td>
    <td width="50" align="center" class="main">→</td>
    <td class="main">言語:
    <select name="select">
    <option selected>日本語</option>
    <option>中国語(+400円)</option>
    <option>韓国語(-100円)</option>
    </select></td>
    </tr>
    </table>
    </td>
    </tr>
    <!-- //options -->
    <tr>
    </table>
    </fieldset></td>
    </tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <?php if (!$site_id) {?>
    <tr>
    <td colspan="2"><fieldset>
    <legend style="color:#009900 ">商品の画像</legend>
    <table>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image)?$pInfo->products_image:'') . tep_draw_hidden_field('products_previous_image', isset($pInfo->products_image)?$pInfo->products_image:''); ?>
    <?php
    if(isset($pInfo->products_image) && tep_not_null($pInfo->products_image)){
      echo '<br>'.tep_info_image('products/'.$pInfo->products_image,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image&action='.$_GET['action'].'&file='.(isset($pInfo->products_image)?$pInfo->products_image:'').'&mode=p_delete&site_id='.$site_id) ; ?>');" style="color:#0000FF;">この画像を削除する</a>
         <?php } ?>
    </td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?>2</td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image2') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image2)?$pInfo->products_image2:'') . tep_draw_hidden_field('products_previous_image2', isset($pInfo->products_image2)?$pInfo->products_image2:''); ?>
    <?php
    if(isset($pInfo->products_image2) && tep_not_null($pInfo->products_image2)){
      echo '<br>'.tep_info_image('products/'.$pInfo->products_image2,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image2&action='.$_GET['action'].'&file='.$pInfo->products_image2.'&mode=p_delete') ; ?>');" style="color:#0000FF;">この画像を削除する</a>
         <?php } ?>
    </td>
    </tr>
    <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?>3</td>
    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image3') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image3)?$pInfo->products_image3:'') . tep_draw_hidden_field('products_previous_image3', isset($pInfo->products_image3)?$pInfo->products_image3:''); ?>
    <?php
    if(isset($pInfo->products_image3) && tep_not_null($pInfo->products_image3)){
      echo '<br>'.tep_info_image('products/'.$pInfo->products_image3,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT , $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image3&action='.$_GET['action'].'&file='.$pInfo->products_image3.'&mode=p_delete') ; ?>');" style="color:#0000FF;">この画像を削除する</a>
         <?php } ?>
    </td>
    </tr>
    </table>
    <?php
    if(COLOR_SEARCH_BOX_TF == "true" ){
      ?>
      <!-- カラー別画像// -->
        <hr size="1">
        <legend style="color:#009900 ">カラー別画像</legend>
        <table border="0" cellpadding="1" cellspacing="5">
        <tr>
            
        <?php
        $color_query = tep_db_query("select * from ".TABLE_COLOR." order by color_name");
      $cnt=0;
      while($color = tep_db_fetch_array($color_query)) {
        $ctp_query = tep_db_query("select color_image, color_to_products_name from ".TABLE_COLOR_TO_PRODUCTS." where color_id = '".$color['color_id']."' and products_id = '".$pInfo->products_id."'");
        $ctp = tep_db_fetch_array($ctp_query);
        echo '<td bgcolor="'.$color['color_tag'].'">';
        echo '<table border="0" cellpadding="0" cellspacing="5" width="100%" bgcolor="#FFFFFF">';
        echo '<tr>';
        echo '<td class="main" width="33%">テキスト：&nbsp;'.tep_draw_input_field('colorname_'.$color['color_id'], $ctp['color_to_products_name']).'<br>'.$color['color_name'].': '.tep_draw_file_field('image_'.$color['color_id']).'<br>&nbsp;&nbsp;&nbsp;' . $ctp['color_image'].tep_draw_hidden_field('image_pre_'.$color['color_id'], $ctp['color_image']).'</td>';
        echo '</tr>';
        echo '</table>';
        echo '</td>';
        $cnt++;
        if($cnt>2) {
          $cnt=0;
          echo '</tr><tr>';
        }
      }
      ?>
              
      </tr>
          </table>
          <!-- //カラー別画像 -->
          <?php
          }
    ?>
    </fieldset></td>
    </tr>
    <?php }?>
<tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td colspan="2">
    <table>
    <tr>
    <td><?php echo TEXT_PRODUCTS_TAGS;?></td> 
    </tr>
    <tr>
    <td<?php if($site_id){echo ' class="readonly"';}?>> 
    <?php
    //show tags 
    $checked_tags = array();
    if (isset($_GET['pID']) && $_GET['pID']) {
      $c_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_TAGS." where products_id = '".$_GET['pID']."'"); 
      while ($ptt = tep_db_fetch_array($c_query)) {
        $checked_tags[$ptt['tags_id']] = $ptt['tags_id']; 
      }
    }
    $t_query = tep_db_query("select * from ".TABLE_TAGS); 
    while ($tag = tep_db_fetch_array($t_query)) {
      ?>
      <input type='checkbox' name='tags[]' value='<?php echo $tag['tags_id'];?>' 
        <?php
        if ($_GET['pID']) {
          if (isset($checked_tags[$tag['tags_id']])) {
            echo 'checked'; 
          }
        } else if ($tag['tags_checked']) {
          echo 'checked'; 
        } else if (isset($_POST['tags']) && in_array($tag['tags_id'], $_POST['tags'])) {
          echo 'checked'; 
        }
      ?><?php if ($site_id) {echo ' onclick="return false;"';}?>
      ><?php echo $tag['tags_name'];?> 
           <?php 
           }
    ?>
    </td> 
    </tr>
    </table>
    </td>
    </tr>
    <?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      ?>
      <tr>
      <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_URL . '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?></td>
      <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : (isset($pInfo->products_id) ?tep_get_products_url(isset($pInfo->products_id)?$pInfo->products_id:'', $languages[$i]['id'], $site_id):''))); ?></td>
      </tr>
      <?php
    }
    ?>
    <input type="hidden" name="products_weight" value="">
    <input type="hidden" name="site_id" value="<?php echo $site_id;?>">
    </table></td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
    <td class="main" align="right"><?php echo tep_draw_hidden_field('products_date_added', (isset($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) . tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . (isset($_GET['pID'])?$_GET['pID']:'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
    </form>
    </tr>
    <?php
  } elseif (isset($_GET['action']) && $_GET['action'] == 'new_product_preview') {
  //print_r($_POST);
  if ($_POST) {
    $pInfo = new objectInfo($_POST);
    $products_name = $_POST['products_name'];
    $products_description = $_POST['products_description'];
    $products_url = $_POST['products_url'];
    $site_id = $_POST['site_id'];

    // copy image only if modified
    $products_image = tep_get_uploaded_file('products_image');
    $image_directory = tep_get_local_path(tep_get_upload_dir($site_id).'products/');

    if (is_uploaded_file($products_image['tmp_name'])) {
      tep_copy_uploaded_file($products_image, $image_directory);
      $products_image_name = $products_image['name'];
      $products_image_name2 = $products_image2['name'];//add
      $products_image_name3 = @$products_image3['name'];//add
    } else {
      $products_image_name = $_POST['products_previous_image'];
      $products_image_name2 = $_POST['products_previous_image2'];
      $products_image_name3 = $_POST['products_previous_image3'];
    }
    // copy image only if modified -- add ds-style
    $products_image2 = tep_get_uploaded_file('products_image2');
    $products_image3 = tep_get_uploaded_file('products_image3');
    $image_directory = tep_get_local_path(tep_get_upload_dir($site_id).'products/');

    if (is_uploaded_file($products_image2['tmp_name'])) {
      tep_copy_uploaded_file($products_image2, $image_directory);
      $products_image_name2 = $products_image2['name'];
    } else {
      $products_image_name2 = $_POST['products_previous_image2'];
    }
    if (is_uploaded_file($products_image3['tmp_name'])) {
      tep_copy_uploaded_file($products_image3, $image_directory);
      $products_image_name3 = $products_image3['name'];
    } else {
      $products_image_name3 = $_POST['products_previous_image3'];
    }
    
    //========================================
    //color image upload    
    //========================================
    $color_query = tep_db_query("select * from ".TABLE_COLOR." order by color_name");
    $cnt=0;
    $color_image_hidden = '';
    while($color = tep_db_fetch_array($color_query)) {
      $ctp_query = tep_db_query("select color_image from ".TABLE_COLOR_TO_PRODUCTS." where color_id = '".$color['color_id']."' and products_id = '".(isset($pInfo->products_id)?$pInfo->products_id:'')."'");
      $ctp = tep_db_fetch_array($ctp_query);
      $color_image = tep_get_uploaded_file('image_'.$color['color_id']);
      $image_directory = tep_get_local_path(tep_get_upload_dir() . 'colors/');
      if (is_uploaded_file($color_image['tmp_name'])) {
        tep_copy_uploaded_file($color_image, $image_directory);
        //$products_image_name2 = $products_image2['name'];
        $color_image_hidden .= tep_draw_hidden_field('image_'.$color['color_id'], $color_image['name']);
      } else {
        //$products_image_name2 = $_POST['products_previous_image2'];
      }
    }
    //========================================
    
  } else {
    $site_id = isset($_GET['site_id']) ? $_GET['site_id'] : '0';
    $product_query = tep_db_query("
          select p.products_id, 
                 pd.language_id, 
                 pd.products_name, 
                 pd.products_description, 
                 pd.products_url, 
                 p.products_quantity, 
                 p.products_model, 
                 p.products_image,
                 p.products_image2,
                 p.products_image3, 
                 p.products_price, 
                 p.products_weight, 
                 p.products_date_added, 
                 p.products_last_modified, 
                 p.products_date_available, 
                 p.products_status, 
                 p.manufacturers_id  
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
          where p.products_id = pd.products_id 
            and p.products_id = '" . $_GET['pID'] . "' 
            and pd.site_id='".(isset($_GET['site_id'])?$_GET['site_id']:'0')."'");
    $product = tep_db_fetch_array($product_query);

    $pInfo = new objectInfo($product);
    $products_image_name = $pInfo->products_image;
    $products_image_name2 = $pInfo->products_image2;
    $products_image_name3 = $pInfo->products_image3;
  }

  if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['ordigin']) || !$_GET['origin'])) {
    $form_action = 'simple_update';
  } elseif ($_GET['pID']) {
    $form_action = 'update_product';
  } else {
    $form_action = 'insert_product';
  }

  echo tep_draw_form($form_action, FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $_GET['pID'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data" onSubmit="return mess();"');
  echo '<input type="hidden" name="site_id" value="'.strval($site_id).'">';

  echo isset($color_image_hedden) ? $color_image_hidden : '';
  $languages = tep_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    if (isset($_GET['read']) && $_GET['read'] == 'only') {
      $pInfo->products_name = tep_get_products_name($pInfo->products_id, $languages[$i]['id']);
      $pInfo->products_description = tep_get_products_description($pInfo->products_id, $languages[$i]['id']);
      $pInfo->products_url = tep_get_products_url($pInfo->products_id, $languages[$i]['id']);
    } else {
      $pInfo->products_name = tep_db_prepare_input($products_name[$languages[$i]['id']]);
      $pInfo->products_description = tep_db_prepare_input($products_description[$languages[$i]['id']]);
      $pInfo->products_url = tep_db_prepare_input($products_url[$languages[$i]['id']]);
    }

    //特価がある場合の処理
    /*
      $special_price_check = tep_get_products_special_price(isset($pInfo->products_id)?$pInfo->products_id:'');
      if (!empty($pInfo->products_special_price)) {
      //％指定の場合は価格を算出
      if (substr($_POST['products_special_price'], -1) == '%') {
      $sprice = ($pInfo->products_price - (($pInfo->products_special_price / 100) * $pInfo->products_price));
      } else {
      $sprice = $pInfo->products_special_price;
      }
      $products_price_preview = '<s>' . $currencies->format($pInfo->products_price) . '</s> <span class="specialPrice">' . $currencies->format($sprice) . '</span>';
      } elseif (!empty($special_price_check)) { //プレビューの表示用
      $products_price_preview = '<s>' . $currencies->format($pInfo->products_price) . '</s> <span class="specialPrice">' . $currencies->format($special_price_check) . '</span>';
      } else {
      $products_price_preview = $currencies->format($pInfo->products_price);
      }*/
    if (tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) {
      $products_price_preview = '<s>' . $currencies->format(tep_get_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) . '</s> <span class="specialPrice">' . $currencies->format(tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) . '</span>';
    } else {
      $products_price_preview = $currencies->format(tep_get_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum));
    }
    ?>
    <tr>
       <td class="pageHeading">
       <?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name . "\n"; ?>
       </td>
       </tr>
       <tr>
       <td><hr size="2" noshade><b><?php //価格数量変更機能
       if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) {
         echo '価格：&nbsp;' . tep_draw_input_field('products_price', (int)$pInfo->products_price,'id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;円' . '&nbsp;&nbsp;←&nbsp;' . (int)$pInfo->products_price . '円<br><hr size="2" noshade>' . "\n";
         echo '増減：&nbsp;' . tep_draw_input_field('products_price_offset', (int)$pInfo->products_price_offset,'id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;円' . '&nbsp;&nbsp;←&nbsp;' . (int)$pInfo->products_price_offset . '円<br><hr size="2" noshade>' . "\n";
         echo '数量：&nbsp;' . tep_draw_input_field('products_quantity', $pInfo->products_quantity,'size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;個' . '&nbsp;&nbsp;←&nbsp;' . $pInfo->products_quantity . '個<br><hr size="2" noshade>' . "\n";
         //商品説明を分割
         /*
           $des_query = tep_db_query("select * from products_description where language_id = '4' and products_id = '" . $pInfo->products_id . "'"); 
           $des_result = tep_db_fetch_array($des_query); 
           echo '当社キャラクター名の入力欄：<br>' . tep_draw_textarea_field('products_attention_5', 'soft', '70', '10', $des_result['products_attention_5']) . '<br>' . "\n";
           echo '<table width="100%" cellspacing="0" cellpadding="5" border="0" class="smalltext"><tr><td><b>販売</b></td><td><b>買取</b></td></tr>' . "\n";
           echo '<tr><td>所持金上限や、弊社キャラクターの在庫の都合上、複数のキャラクターにて<br>分割してお届けする場合がございます。ご注文いただきました数量に達する<br>まで受領操作をお願いいたします。<br>【】または【】よりお届けいたします。</td><td>当社キャラクター【】または【】にトレードをお願いいたします。</td></tr></table><hr size="2" noshade>' . "\n";
         */
         echo tep_image_submit('button_update.gif', 'よく確認してから押しなさい') . '</form>' . "\n";
       } else {
         echo '価格：&nbsp;' . $products_price_preview . '<br>数量：&nbsp;' . $pInfo->products_quantity . '個' . "\n";
       }
    ?>
    </b>
        </td>
        </tr>
        <?php
        if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) { //表示制限
          echo '<tr><td><b>よく確認してから押しなさい</b></td></tr>' . "\n";
        } else {
          ?>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                   </tr>
                                                                                   <tr>
                                                                                   <td class="main"><?php echo $pInfo->products_description.'<hr size="1" noshade><table width=""><tr><td>'.
                                                                                   tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="" hspace="5" vspace="5"')
                                                                                   .'</td><td>'.
                                                                                   tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name2, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"')
                                                                                   .'</td><td align="right">'.
                                                                                   tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name3, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"')
                                                                                   .'</td></tr></table>'; ?></td>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                                if ($pInfo->products_url) {
                                                                                                                  ?>
                                                                                                                  <tr>
                                                                                                                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                                                                                                                           </tr>
                                                                                                                                                                                           <tr>
                                                                                                                                                                                           <td class="main"><?php echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url); ?></td>
                                                                                                                                                                                                                                                                                           </tr>
                                                                                                                                                                                                                                                                                           <?php
                                                                                                                                                                                                                                                                                           }
          ?>
          <tr>
             <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                    if (isset($pInfo->products_date_available) && $pInfo->products_date_available > date('Y-m-d')) {
                                                                                      ?>
                                                                                      <tr>
                                                                                        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)); ?></td>
                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                    <?php
                                                                                                                                                                                                                                    } else {
                                                                                      ?>
                                                                                      <tr>
                                                                                        <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added)); ?></td>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                            <?php
                                                                                                                                                                                                                            }
          ?>
          <tr>
             <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                    }
  } // 表示制限終わり

  if (isset($_GET['read']) && $_GET['read'] == 'only') {
    if (isset($_GET['origin']) && $_GET['origin']) {
      $pos_params = strpos($_GET['origin'], '?', 0);
      if ($pos_params != false) {
        $back_url = substr($_GET['origin'], 0, $pos_params);
        $back_url_params = substr($_GET['origin'], $pos_params + 1);
      } else {
        $back_url = $_GET['origin'];
        $back_url_params = '';
      }
    } else {
      $back_url = FILENAME_CATEGORIES_ADMIN;
      $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
    }
    ?>
    <tr>
       <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
                                                                                                                                                                                </tr>
                                                                                                                                                                                <?php
                                                                                                                                                                                } else {
    ?>
    <tr>
      <td align="right" class="smallText"><?php
      /* Re-Post all POST'ed variables */
      reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    } 
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      echo tep_draw_hidden_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_name[$languages[$i]['id']])));
      echo tep_draw_hidden_field('products_description[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_description[$languages[$i]['id']])));
      echo tep_draw_hidden_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_url[$languages[$i]['id']])));
    }
    //add hidden tags
    if (isset($_POST['tags']) && $_POST['tags']) {
      foreach ($_POST['tags'] as $t) {
        echo tep_draw_hidden_field('tags[]', $t); 
      }
    }
    echo tep_draw_hidden_field('products_image', stripslashes($products_image_name));
    echo tep_draw_hidden_field('products_image2', stripslashes($products_image_name2));
    echo tep_draw_hidden_field('products_image3', stripslashes($products_image_name3));
    echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

    if ($_GET['pID']) {
      echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
    } else {
      echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
    }
    echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
    ?></td>
    </form>
        </tr>
        <?php
        }
} else
