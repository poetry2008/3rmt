<?php
/*
   $Id$
   
   批量更新价格
*/
//产品id 列表
$products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
$cID=$_POST['cID_list'];

if($_POST[flg_up]==1){
  $psrice_datas = $_POST['price'];//特价
  $proid = $_POST['proid'];//pid

  $cnt = count($psrice_datas);
  $dougyousya=$_POST['TARGET_INPUT'];//同業者価格

  $dou_id=$_POST['d_id'];//同業者ID
  $d_cnt=count($dougyousya);//同業者フォームの数

  $radio_chk=$_POST['chk'];

  $loop_cnt=count($dou_id);
  //同業者データを行ごとに分割する
  $rajio_a=0;
  $num=0;
  for($j=0;$j < $d_cnt;$j++){
    if($rajio_a != $loop_cnt){
      $d_datas[$num][$rajio_a]=$dougyousya[$j];
      $rajio_a++;
    }else{
      $rajio_a=0;
      $num++;
      $d_datas[$num][$rajio_a]=$dougyousya[$j];
      $rajio_a++;
    }
  }

  $res_cnt=tep_db_query("select count(*) as cnt_d from set_dougyousya_history where categories_id !=0 and categories_id = '".tep_db_prepare_input($cID)."' AND products_id  = '".tep_db_prepare_input($proid[0])."'");
  $col_cnt=tep_db_fetch_array($res_cnt);
  $cnt_d=$d_cnt*20;//カテゴリー20件保存(5日分)

  for($n=0;$n < $cnt;$n++ ){
    update_products_dougyousya(tep_db_prepare_input($proid[$n]), tep_db_prepare_input($radio_chk[$n]));
    $update_sql_data = array('products_last_modified' => 'now()',
                             'products_price'         => tep_get_bflag_by_product_id(tep_db_prepare_input($proid[$n])) ? 0 - tep_db_prepare_input($psrice_datas[$n]) : tep_db_prepare_input($psrice_datas[$n]),
    );
    tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_prepare_input($proid[$n]) . '\'');
  }
}



