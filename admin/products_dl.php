<?php
/*
  $Id: mail.php,v 1.3 2003/03/18 02:51:42 ptosh Exp $


  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com


  Copyright (c) 2002 osCommerce


  Released under the GNU General Public License
*/


  require('includes/application_top.php');
  require(DIR_FS_CATALOG . 'includes/configure.php');
  require("includes/jcode.phps");
  
  $msg = "";
  
  //--------------------------------//
  //  function                      //
  //--------------------------------//
  function chenge_block($value) {
    $res = str_replace(array("\n", "\r", "\t", "\\", ','), array("", "", "", "", '.'), $value);
	return $res;
  }
  function get_categories_name($cid) {
    if($cid == "" || $cid == "0") {
	$string = "";
	} else {
	$query = tep_db_query("select categories_name from categories_description where categories_id = '".$cid."' and language_id = '4'");
	$result = tep_db_fetch_array($query);
	
	$string = $result['categories_name'];
	}
	return $string;
  }
  function tep_get_product_path($products_id) {
    $cPath = '';

    $cat_count_sql = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
    $cat_count_data = tep_db_fetch_array($cat_count_sql);

    if ($cat_count_data['count'] == 1) {
      $categories = array();

      $cat_id_sql = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
      $cat_id_data = tep_db_fetch_array($cat_id_sql);
      tep_get_parent_categories($categories, $cat_id_data['categories_id']);

      $size = sizeof($categories)-1;
      for ($i = $size; $i >= 0; $i--) {
        if ($cPath != '') $cPath .= '_';
        $cPath .= $categories[$i];
      }
      if ($cPath != '') $cPath .= '_';
      $cPath .= $cat_id_data['categories_id'];
    }

    return $cPath;
  }
  function tep_get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }
  
  if (isset($_GET['action']) && $_GET['action'] == 'download'){
		header("Content-Type: application/force-download");
		header('Pragma: public');
		header('Content-Disposition: attachment; filename=products.csv');
		
		set_time_limit(3600);
		$query = tep_db_query("select distinct p.*, pd.* from products p, products_description pd where p.products_id = pd.products_id and pd.language_id = '4' order by p.products_id");
		$CsvFields = array("大カテゴリ", "中カテゴリ",  "メーカー名", "商品名", "商品説明", "型番", "画像パス", "定価", "価格", "特売価格", "数量", "発売日", "在庫ステータス", "関連URL", "重量", "項目１", "項目２", "項目３", "項目４", "税種別", "項目５");
		for($i=0;$i<count($CsvFields);$i++){
			if($i!="0") print ",";
			print jcodeconvert($CsvFields[$i],0,2) . "";
		}
		print "\n";
		
		while($result = tep_db_fetch_array($query)) {
		  //商品ID	
		  $products_id = chenge_block($result['products_id']);
		  //print jcodeconvert($products_id,0,2) . ",";
		  
		  /*カテゴリ取得*/
		  $cachk_query = tep_db_query("select categories_id from products_to_categories where products_id = '".$result['products_id']."'");
		  $cachk = tep_db_fetch_array($cachk_query);
		  if($cachk['categories_id'] != "0") {
		  $cPath = explode('_', tep_get_product_path($result['products_id']));
		  $categories_name0 = str_replace("NULL", "", chenge_block($cPath[0]));
		  $categories_name1 = str_replace("NULL", "", chenge_block($cPath[1]));
		  } else {
		  $categories_name0 = "";
		  $categories_name1 = "";
		  }
		  
		  //大カテゴリ
		  print jcodeconvert(get_categories_name($categories_name0),0,2) . ",";
		  
		  //中カテゴリ
		  print jcodeconvert(get_categories_name($categories_name1),0,2) . ",";
			  
		  //メーカー名
		  $mquery = tep_db_query("select manufacturers_name from manufacturers where manufacturers_id = '".$result['manufacturers_id']."'");
		  $mresult = tep_db_fetch_array($mquery);
		  $mname = chenge_block($mresult['manufacturers_name']);
		  print jcodeconvert($mname,0,2) . ",";
		  
		  $pd_query = tep_db_query("select * from products_description where  products_id = '".$products_id."' and language_id = '4'");
		  $pd = tep_db_fetch_array($pd_query);
		  
		  //商品名
		  $pd_name = chenge_block($pd['products_name']);
		  print jcodeconvert($pd_name,0,2) . ",";
		  
		  //商品説明 - edit 2005.11.29 ds-style
		  // edit 2009.5.14 maker
		  $pd['products_description'] = str_replace(array("\r\n", "\n", "\r", ","), array("", "", "", "."), $pd['products_description_'.ABBR_SITENAME]);
		  //$description_array = explode("|-#-|", $pd['products_description']);
		  print jcodeconvert($pd['products_description_'.ABBR_SITENAME],0,2) . ",";
		  //print jcodeconvert($pd['products_description'],0,2) . ",";
		  
		  //型番
		  $p_model = chenge_block($result['products_model']);
		  print jcodeconvert($p_model,0,2) . ",";
		  
		  //画像パス	
		  $p_image = chenge_block($result['products_image']);
		  print jcodeconvert($p_image,0,2) . ",";
		  
		  //定価 - add 2005.11.29 ds-style
		  //print jcodeconvert($pd['products_attention_1'],0,2) . ",";//maker
		  
		  //価格	
		  $p_price = chenge_block($result['products_price']);
		  print jcodeconvert($p_price,0,2) . ",";
		  
		  //特売価格	
		  $sp_count_query = tep_db_query("select count(*) as cnt from specials where products_id = '".$products_id."'");
		  $sp_count_result = tep_db_fetch_array($sp_count_query);
		  
		  if($sp_count_result['cnt'] != 0) {
		  $sp_query = tep_db_query("select * from specials where products_id = '".$products_id."'");
		  $sp_result = tep_db_fetch_array($sp_query);
		  $sp_price = chenge_block($sp_result['specials_new_products_price']);
		  
		    print jcodeconvert($sp_price,0,2) . ",";
		  } else {
		    print ",";
		  }
		  
		  //数量
		  $p_quantity = chenge_block($result['products_quantity']);
		  print jcodeconvert($p_quantity,0,2) . ",";
		  
		  //発売日	
		  $p_available = chenge_block($result['products_available']);
		  print jcodeconvert($p_available,0,2) . ",";
		  
		  //在庫ステータス
		  $p_status = chenge_block($result['products_status']);
		  print jcodeconvert($p_status,0,2) . ",";
		  
		  //関連URL	
		  $pd_url = chenge_block($pd['products_url']);
		  print jcodeconvert($pd_url,0,2) . ",";

		  //重量	
		  $p_weight = chenge_block($result['products_weight']);
		  print jcodeconvert($p_weight,0,2) . ",";
		  
		  //JANコード - add 2005.11.29 ds-style
		  print jcodeconvert($pd['products_attention_1'],0,2) . ",";

		  //サイズ - add 2005.11.29 ds-style
		  print jcodeconvert($pd['products_attention_2'],0,2) . ",";

		  //内容量 - add 2005.11.29 ds-style
		  // edit 2009.05.14 maker
		  print jcodeconvert($pd['products_attention_3'],0,2) . ",";

		  //材質 - add 2005.11.29 ds-style
		  // edit 2009.05.14 maker
		  print jcodeconvert($pd['products_attention_4'],0,2) . ",";
		  
		  //税種別	
		  $p_tcid = chenge_block($result['products_tax_class_id']);
		  print jcodeconvert($p_tcid,0,2) . ",";
		
		  //備考 - add 2005.11.29 ds-style
		  // edit 2009.05.14 maker
		  //print jcodeconvert($pd['products_attention_5'],0,2) . ",";

		  //終了
		  print "\n";
		  
		}

		$msg = MSG_UPLOAD_IMG;
		
 }else{


?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">商品データダウンロード</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr><FORM method="POST" action="products_dl.php?action=download"  enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><input type=submit name=download value="<?php echo ICON_FILE_DOWNLOAD; ?>"></td>
              </tr>
            </table></td>
          </form></tr>


<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php 
  require(DIR_WS_INCLUDES . 'application_bottom.php');


  }
?>