<?php
      /*
      echo "<pre>";
      print_r($_POST);
      echo "</pre>";
      exit;*/
      
      //产品id 列表
      $products_id = tep_db_prepare_input($HTTP_GET_VARS['pID']);
      $cID=$_POST['cID_list'];

      if($_POST[flg_up]==1){
        $psrice_datas = $_POST['price'];//特价
        $proid = $_POST['proid'];//pid
        $products_prise=$_POST['pprice'];//正常价格
        $products_quantity=$_POST['quantity'];//在库数
      
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
        //radioボタンチェックフラグ
        /*
        $rajio_a=0;
        $num=0;
        for($j=0;$j < $d_cnt;$j++){
          if($rajio_a != $loop_cnt){
            $radio_chk_data[$num][$rajio_a]=$radio_chk[$j];
            $rajio_a++;
          }else{
            $rajio_a=0;
            $num++;
            $radio_chk_data[$num][$rajio_a]=$radio_chk[$j];
            $rajio_a++;
          }
        }*/
        $res_cnt=tep_db_query("select count(*) as cnt_d from set_dougyousya_history where categories_id !=0 and categories_id = '".tep_db_prepare_input($cID)."' AND products_id  = '".tep_db_prepare_input($proid[0])."'");
        $col_cnt=tep_db_fetch_array($res_cnt);
        $cnt_d=$d_cnt*20;//カテゴリー20件保存(5日分)

        for($n=0;$n < $cnt;$n++ ){
          update_products_dougyousya(tep_db_prepare_input($proid[$n]), tep_db_prepare_input($radio_chk[$n]));
          
          if (tep_db_num_rows(tep_db_query("select * from set_menu_list where categories_id = '".tep_db_prepare_input($cID)."' and products_id='".tep_db_prepare_input($proid[$n])."'"))) {
            tep_db_perform('set_menu_list', array('kakuukosuu' => tep_db_prepare_input($_POST['imaginary'][$n])), 'update', 'categories_id=\'' . tep_db_prepare_input($cID) . '\' and products_id = \'' . tep_db_prepare_input($proid[$n]) . '\'');
          } else {
            tep_db_perform('set_menu_list', array(
              'kakuukosuu' => tep_db_prepare_input($_POST['imaginary'][$n]),
              'categories_id' => tep_db_prepare_input($cID),
              'products_id' => tep_db_prepare_input($proid[$n])
            ));
          }
          
          $update_sql_data = array('products_last_modified' => 'now()',
                                   'products_quantity' => tep_db_prepare_input($products_quantity[$n]),
                                   'products_price' => tep_db_prepare_input($products_prise[$n]));
          tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_prepare_input($proid[$n]) . '\'');

          // 特価商品インサート
          if(!empty($psrice_datas[$n])) {
            //％指定の場合は価格を算出
            if (substr($psrice_datas[$n], -1) == '%') {
              $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_prepare_input($proid[$n]) . "'");
              $new_special_insert = tep_db_fetch_array($new_special_insert_query);
              $products_prise[$n] = $new_special_insert[$products_prise[$n]];
              $psrice_datas[$n] = ($products_prise[$n] - (($psrice_datas[$n] / 100) * $products_prise[$n]));
            } 
      
            $spcnt_query = tep_db_query("select count(*) as cnt from " . TABLE_SPECIALS . " where products_id = '".tep_db_prepare_input($proid[$n])."'");
            $spcnt = tep_db_fetch_array($spcnt_query);
      
            if($spcnt['cnt'] > 0) {
              //登録済みなのでアップデート
              tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '".$psrice_datas[$n]."', specials_last_modified = now(), status = '1' where  products_id = '".tep_db_prepare_input($proid[$n])."'");
            }else{
              //未登録なのでインサート
              tep_db_query("insert into " . TABLE_SPECIALS . "(specials_id, products_id, specials_new_products_price, specials_date_added, status) values ('', '".tep_db_prepare_input($proid[$n])."', '".tep_db_prepare_input($psrice_datas[$n])."', now(), '1')");
            }
          }else{
            $spcnt_query = tep_db_query("select count(*) as cnt from " . TABLE_SPECIALS . " where products_id = '".tep_db_prepare_input($proid[$n])."'");
            $spcnt = tep_db_fetch_array($spcnt_query);
            if($spcnt['cnt'] > 0) {
              //データを削除
              tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . tep_db_prepare_input($proid[$n]) . "'");
            }
            //tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '".$psrice_datas[$n]."', specials_last_modified = now(), status = '1' where  products_id = '".tep_db_prepare_input($proid[$n])."'");
          }
        }
      }
      
      

