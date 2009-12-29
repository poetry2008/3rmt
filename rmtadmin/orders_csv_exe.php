<?php
/*
  $Id: orders_csv_exe.php,v 1.4 2005/02/15 08:30:57 hiroyuki Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
 require('includes/application_top.php');

// CSV�ե�����̾�κ���

  $filename = "orders_".date("Ymd_His", time()).".csv";

//����������ϰϤμ���
  $s_y = $HTTP_POST_VARS['s_y'] ; //��������ǯ
  $s_m = $HTTP_POST_VARS['s_m'] ; //����������
  $s_d = $HTTP_POST_VARS['s_d'] ; //����������
  $start = $s_y.$s_m.$s_d ;
  
  $e_y = $HTTP_POST_VARS['e_y'] ; //��λ����ǯ
  $e_m = $HTTP_POST_VARS['e_m'] ; //��λ������
  $e_d = $HTTP_POST_VARS['e_d'] ; //��λ������
  $end = $e_y.$e_m.$e_d ;

// ����������ϰϤλ���
    if($HTTP_POST_VARS['status'] && $HTTP_POST_VARS['status'] !=""){
      $csv_query = tep_db_query("select o.*, op.* from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and o.date_purchased >= '" . $start . "' and o.date_purchased <= '" . $end . "' and o.orders_status = '".(int)$HTTP_POST_VARS['status']."' order by o.orders_id, op.orders_products_id");
	}else{
      $csv_query = tep_db_query("select o.*, op.* from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and o.date_purchased >= '" . $start . "' and o.date_purchased <= '" . $end . "' order by o.orders_id, op.orders_products_id");
    }

  header("Content-Type: application/force-download");
  header('Pragma: public');
  header('Content-Disposition: attachment; filename='.$filename);

  $csv_header = '"�����ֹ�","��ʸ����","����̾","����ID","�����ֹ�","�Ŀ�","ñ��","���ܡ������","�ܵ�ID","��ʸ��̾","��ʸ��̾�եꥬ��","�᡼�륢�ɥ쥹","��ʸ��͹���ֹ�","��ʸ�Խ����̾","��ʸ�Խ�����ƻ�ܸ�","��ʸ�Խ����ԻԶ�","��ʸ�Խ��꣱","��ʸ�Խ��ꣲ","��ʸ�Բ��̾","��ʸ�������ֹ�","������̾","������̾�եꥬ��","������͹���ֹ�","�����轻���̾","�����轻����ƻ�ܸ�","�����轻���ԻԶ�","�����轻�꣱","�����轻�ꣲ","��������̾","�����������ֹ�","������̾","������̾�եꥬ��","������͹���ֹ�","�����轻���̾","�����轻����ƻ�ܸ�","�����轻���ԻԶ�","�����轻�꣱","�����轻�ꣲ","��������̾","�����������ֹ�","�����ˡ","���쥸�åȥ����ɼ���","���쥸�åȥ������ֹ�","���쥸�åȥ�����̾����","���쥸�åȥ�����ͭ������","������ˡ","������","���","����","�����","�谷�����","������","������","�ݥ���ȳ��","�ݥ�������Ѿ��","�ݥ�������ѳ�","��׶��"';

  $csv_header = mb_convert_encoding($csv_header,'SJIS','EUC-JP');

  print $csv_header."\r\n";

  while ($csv_orders = tep_db_fetch_array($csv_query)) {

    $csv_ot_query = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where orders_id = '".$csv_orders['orders_id']."'");

    $ot_shipping = "";
    $ot_shipping_title = "";
    $ot_awardpoints = "";
    $ot_codt = "";
    $ot_loworderfee = "";
    $ot_tax = "";
    $ot_total = "";
    $ot_redemptions = "";
    $ot_redemptions_flag = 0;
    $ot_redemptions_alt = "";

    while ($csv_ot_orders = tep_db_fetch_array($csv_ot_query)) {

      if($csv_ot_orders['class'] == "ot_shipping") { $ot_shipping = $csv_ot_orders['value']; $ot_shipping_title = mb_split(" [\(��]",$csv_ot_orders['title']); }
      if($csv_ot_orders['class'] == "ot_awardpoints") { $ot_awardpoints = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_codt") { $ot_codt = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_loworderfee") { $ot_loworderfee = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_tax") { $ot_tax = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_total") { $ot_total = $csv_ot_orders['value']; }
      if($csv_ot_orders['class'] == "ot_point") { $ot_redemptions = $csv_ot_orders['value']; $ot_redemptions_flag = 1; $ot_redemptions_alt = 1; }

    }

    $csv_sh_query = tep_db_query("select comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".$csv_orders['orders_id']."' order by orders_status_history_id limit 0,1");
    $csv_sh_orders = tep_db_fetch_array($csv_sh_query);

    if(!$start_id){ $start_id = $csv_orders['orders_id']; }
    $end_id = $csv_orders['orders_id'];

    $csv  =  '"'.precsv($csv_orders['orders_id']).'"';
    $csv .= ',"'.precsv($csv_orders['date_purchased']).'"';
    $csv .= ',"'.precsv($csv_orders['products_name']).'"';
    $csv .= ',"'.precsv($csv_orders['products_id']).'"';
    $csv .= ',"'.precsv($csv_orders['products_model']).'"';
    $csv .= ',"'.precsv($csv_orders['products_quantity']).'"';
    $csv .= ',"'.precsv($csv_orders['final_price']*1).'"';

    $csv_op  = "";

    $csv_op_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = ".$csv_orders['orders_products_id']." order by orders_products_attributes_id");

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
    $csv .= ',"'.precsv($csv_orders['customers_name_kana']).'"';
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
    $csv .= ',"'.precsv($csv_orders['billing_name_kana']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_postcode']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_country']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_state']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_city']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_street_address']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_suburb']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_company']).'"';
    $csv .= ',"'.precsv($csv_orders['billing_telephone']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_name']).'"';
    $csv .= ',"'.precsv($csv_orders['delivery_name_kana']).'"';
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
    $csv .= ',"'.precsv($ot_shipping_title[0]).'"';
    $csv .= ',"'.precsv(str_replace(array("\r","\n","\t"),array("","",""),$csv_sh_orders['comments'])).'"';
if(JPTAX == "on"){
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
    $result = mb_convert_encoding($result,'SJIS','EUC-JP');
    return $result;    
  }

?>
