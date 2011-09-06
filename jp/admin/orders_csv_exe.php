<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
 require('includes/application_top.php');

// CSVファイル名の作成

  $filename = ((isset($_POST['site_id'])&&$_POST['site_id']) ? (tep_get_site_romaji_by_id(intval($_POST['site_id'])).'_') :'')."orders_".date("Ymd_His", time()).".csv";

//ダウンロード範囲の取得
  $s_y = $_POST['s_y'] ; //開始日　年
  $s_m = $_POST['s_m'] ; //開始日　月
  $s_d = $_POST['s_d'] ; //開始日　日
  $start = $s_y.$s_m.$s_d ;
  
  $e_y = $_POST['e_y'] ; //終了日　年
  $e_m = $_POST['e_m'] ; //終了日　月
  $e_d = $_POST['e_d'] ; //終了日　日
  $end = $e_y.$e_m.$e_d ;

// ダウンロード範囲の指定
    //if($_POST['order_status'] && $_POST['order_status'] !=""){
      //$csv_query = tep_db_query("
          //select o.*, op.* 
          //from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op 
          //where o.orders_id = op.orders_id 
            //and o.date_purchased >= '" . $start . "' 
            //and o.date_purchased <= '" . $end . "' 
            //and o.orders_status = '".(int)$_POST['order_status']."' 
          //order by o.orders_id, op.orders_products_id
      //");
	//}else{
      $csv_query = tep_db_query("
          select o.*, op.*, s.romaji
          from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_SITES." s
          where o.orders_id = op.orders_id 
            and o.site_id = s.id
            and o.date_purchased >= '" . $start . "' 
            and o.date_purchased <= '" . $end . "' 
            ".(isset($_POST['order_status']) && $_POST['order_status'] ? ("and o.orders_status = '".(int)$_POST['order_status'] . "'") : '')."
            ".(isset($_POST['site_id']) && $_POST['site_id'] ? ("and o.site_id = '".(int)$_POST['site_id'] . "'") : '')."
          order by o.orders_id, op.orders_products_id
      ");
    //}

  header("Content-Type: application/force-download");
  header('Pragma: public');
  header('Content-Disposition: attachment; filename='.$filename);

  $csv_header = (isset($_POST['site_id']) && $_POST['site_id']?'"'.ENTRY_SITE.'",':'').'"受注番号","注文日時","商品名","商品ID","商品番号","個数","単価","項目・選択肢","顧客ID","注文者名","注文者名フリガナ","メールアドレス","注文者郵便番号","注文者住所国名","注文者住所都道府県","注文者住所都市区","注文者住所１","注文者住所２","注文者会社名","注文者電話番号","請求先名","請求先名フリガナ","請求先郵便番号","請求先住所国名","請求先住所都道府県","請求先住所都市区","請求先住所１","請求先住所２","請求先会社名","請求先電話番号","送付先名","送付先名フリガナ","送付先郵便番号","送付先住所国名","送付先住所都道府県","送付先住所都市区","送付先住所１","送付先住所２","送付先会社名","送付先電話番号","決済方法","クレジットカード種類","クレジットカード番号","クレジットカード名義人","クレジットカード有効期限","配送方法","コメント","合計","送料","代引料","取扱手数料","消費税","請求金額","ポイント割引","ポイント利用条件","ポイント利用額","合計金額"';

  //$csv_header = mb_convert_encoding($csv_header,'SJIS','EUC-JP');

  print chr(0xEF).chr(0xBB).chr(0xBF);
  print $csv_header."\r\n";

  while ($csv_orders = tep_db_fetch_array($csv_query)) {

    $csv_ot_query = tep_db_query("
        select * 
        from ".TABLE_ORDERS_TOTAL." 
        where orders_id = '".$csv_orders['orders_id']."'
    ");

    $ot_shipping       = "";
    $ot_shipping_title = "";
    $ot_awardpoints    = "";
    $ot_codt           = "";
    $ot_loworderfee    = "";
    $ot_tax            = "";
    $ot_total          = "";
    $ot_redemptions      = "";
    $ot_redemptions_flag = 0;
    $ot_redemptions_alt  = "";

    while ($csv_ot_orders = tep_db_fetch_array($csv_ot_query)) {

      if($csv_ot_orders['class'] == "ot_shipping") { $ot_shipping = $csv_ot_orders['value']; $ot_shipping_title = mb_split(" [\(（]",$csv_ot_orders['title']); }
      if($csv_ot_orders['class'] == "ot_awardpoints") { $ot_awardpoints = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_codt") { $ot_codt = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_loworderfee") { $ot_loworderfee = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_tax") { $ot_tax = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_total") { $ot_total = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_point") { $ot_redemptions = $csv_ot_orders['value']; $ot_redemptions_flag = 1; $ot_redemptions_alt = 1; }

    }

    $csv_sh_query = tep_db_query("
        select comments 
        from ".TABLE_ORDERS_STATUS_HISTORY." 
        where orders_id = '".$csv_orders['orders_id']."' 
        order by orders_status_history_id 
        limit 0,1");
    $csv_sh_orders = tep_db_fetch_array($csv_sh_query);

    if(!isset($start_id) || !$start_id){ $start_id = $csv_orders['orders_id']; }
    $end_id = $csv_orders['orders_id'];

    $csv  = isset($_POST['site_id']) && $_POST['site_id'] ? ('"'.$csv_orders['romaji'].'",' ):'';
    $csv .=  '"'.precsv($csv_orders['orders_id']).'"';
    $csv .= ',"'.precsv($csv_orders['date_purchased']).'"';
    $csv .= ',"'.precsv($csv_orders['products_name']).'"';
    $csv .= ',"'.precsv($csv_orders['products_id']).'"';
    $csv .= ',"'.precsv($csv_orders['products_model']).'"';
    $csv .= ',"'.precsv($csv_orders['products_quantity']).'"';
    $csv .= ',"'.precsv($csv_orders['final_price']*1).'"';

    $csv_op  = "";

    $csv_op_query = tep_db_query("
        select * 
        from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." 
        where orders_products_id = ".$csv_orders['orders_products_id']." 
        order by orders_products_attributes_id");

    while ($csv_op_orders = tep_db_fetch_array($csv_op_query)) {

      $csv_op .= precsv($csv_op_orders['products_options'])."\t".precsv($csv_op_orders['products_options_values'])."\t";

      if($csv_op_orders['price_prefix'] == "-") {
        $csv_op .= $csv_op_orders['price_prefix'].precsv($csv_op_orders['options_values_price']*1)."\t";
      } else {
        $csv_op .= precsv($csv_op_orders['options_values_price']*1)."\t";
      }
    }

    $csv .= ',"'.$csv_op.'"';

    $csv .= ',"'.precsv($csv_orders['customers_id']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['customers_name_kana']) ? $csv_orders['customers_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['customers_email_address']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_country']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_state']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_city']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_company']).'"';
    $csv .= ',"'.precsv($csv_orders['customers_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['billing_name_kana']) ? $csv_orders['billing_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['billing_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_country']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_state']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_city']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_company']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_name']).'"';
    $csv .= ',"'.precsv(isset($csv_orders['delivery_name_kana']) ? $csv_orders['delivery_name_kana'] : '').'"';
    $csv .= ',"'.precsv($csv_orders['delivery_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_country']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_state']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_city']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_company']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['payment_method']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_type']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_number']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_owner']).'"';
    $csv .= ',"'.precsv($csv_orders['cc_expires']).'"';
    $csv .= ',"'.precsv(isset($ot_shipping_title[0]) ? $ot_shipping_title[0]: '').'"';
    $csv .= ',"'.precsv(str_replace(array("\r","\n","\t"),array("","",""),$csv_sh_orders['comments'])).'"';
if(defined('JPTAX') && JPTAX == "on"){
    $csv .= ',"'.precsv($ot_total+$ot_redemptions-$ot_shipping-$ot_codt-$ot_loworderfee).'"';
} else {
    $csv .= ',"'.precsv($ot_total+$ot_redemptions-$ot_shipping-$ot_codt-$ot_loworderfee-$ot_tax).'"';
}
    $csv .= ',"'.precsv($ot_shipping*1).'"';
    $csv .= ',"'.precsv($ot_codt*1).'"';
    $csv .= ',"'.precsv($ot_loworderfee*1).'"';
    $csv .= ',"'.precsv($ot_tax*1).'"';
    $csv .= ',"'.precsv($ot_total*1).'"';
    $csv .= ',"'.precsv($ot_redemptions_flag).'"';
    $csv .= ',"'.precsv($ot_redemptions_alt).'"';
    $csv .= ',"'.precsv($ot_redemptions*1).'"';
    $csv .= ',"'.precsv($ot_total).'"';

    print $csv."\r\n";

  }
/*
  if($start_id && $end_id) {
    $sql_data_array = array('start' => $start_id, 
                            'end' => $end_id, 
                            'download_date' => 'now()');
    tep_db_perform(TABLE_ORDERCSV_LOG, $sql_data_array);
  }
*/
  function precsv($query) {
    global $result;
    $result = $query;
    $result = str_replace('"', '""', $result);
    //$result = mb_convert_encoding($result,'SJIS','EUC-JP');
    return $result;    
  }

?>
