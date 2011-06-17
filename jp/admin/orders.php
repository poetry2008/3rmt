<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  
  require(DIR_WS_FUNCTIONS . 'visites.php');

  require(DIR_WS_CLASSES . 'currencies.php');

  $currencies          = new currencies(2);
  $orders_statuses     = $all_orders_statuses = $orders_status_array = array();

  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");

  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    if (
      $orders_status['orders_status_id'] != 17 
      //&& $orders_status['orders_status_id'] != 31
      )
      $orders_statuses[] = array('id' => $orders_status['orders_status_id'],'text' => $orders_status['orders_status_name']);
    
    $all_orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }
   
  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
    //一括変更----------------------------------
  case 'sele_act':
    if($_POST['chk'] == ""){
      $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
    }

      //批量问答
      if(
        ($_POST['q_15_3'] || $_POST['q_15_4'] || $_POST['q_15_5'])
        && $_POST['q_15_7']
        && $_POST['q_15_8']
        && $_POST['q_8_1']
        && $_POST['status'] == 5
      ){
        foreach($_POST['chk'] as $value) {
          $oID = $value;
          $arr = array(
            'q_15_3' => $_POST['q_15_3'],
            'q_15_4' => $_POST['q_15_4'],
            'q_15_5' => $_POST['q_15_5'],
            'q_15_7' => $_POST['q_15_7'],
            'q_15_8' => $_POST['q_15_8'],
            'q_8_1'  => $_POST['q_8_1']
          );
          tep_db_perform('orders_questions',$arr,'update',"orders_id='".$oID."'");
          orders_updated($oID);
          orders_wait_flag($oID);
        }
      }
      //tep_redirect(tep_href_link(FILENAME_ORDERS));

      foreach($_POST['chk'] as $value){
      $oID      = $value;
      $status   = tep_db_prepare_input($_POST['status']);
      $title    = tep_db_prepare_input($_POST['os_title']);
      $comments = tep_db_prepare_input($_POST['comments']);
      $site_id  = tep_get_site_id_by_orders_id($value);
    
      $order_updated = false;
      $check_status_query = tep_db_query("select customers_name, customers_id, customers_email_address, orders_status, date_purchased, payment_method, torihiki_date from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);
      
      //Add Point System
      if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
        $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
        $pcount = tep_db_fetch_array($pcount_query);
        if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
          $query1  = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
          $query2  = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
          $result2 = tep_db_fetch_array($query2);
          $query3  = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
          $result3 = tep_db_fetch_array($query3);
          $query4  = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
          $result4 = tep_db_fetch_array($query4);
      
        // ここからカスタマーレベルに応じたポイント還元率算出============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //設定した期間内の注文合計金額を算出------------
          $ptoday = date("Y-m-d H:i:s", time());
          $pstday_array = getdate();
          $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
          $total_buyed_date = 0;
          $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
          if(tep_db_num_rows($customer_level_total_query)) {
            while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
              $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
              $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
        
              $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
              $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
         
              $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
            }
          }
          //----------------------------------------------
          //今回の注文額は除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
          //還元率を計算----------------------------------
          if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
            $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
            $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
            for($j=0; $j<sizeof($back_rate_array); $j++) {
              $back_rate_array2 = explode(",", $back_rate_array[$j]);
              if($back_rate_array2[2] <= $total_buyed_date) {
              $back_rate = $back_rate_array2[1];
              $back_rate_name = $back_rate_array2[0];
              }
            }
          } else {
          $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
          if($back_rate_array[2] <= $total_buyed_date) {
            $back_rate = $back_rate_array[1];
            $back_rate_name = $back_rate_array[0];
          }
          }
          //----------------------------------------------
          $point_rate = $back_rate;
        } else {
          $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        }
        // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
        
          if ($result3['value'] >= 0) {
            $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
            tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $get_point . " where customers_id = " . $result1['customers_id'] );
          } else {
            if ($check_status['payment_method'] == 'ポイント(買い取り)') {
              $get_point = abs($result3['value']);
            } else {
              $get_point = 0;
            }
          }
        
        }
        }   
    
    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      orders_updated(tep_db_input($oID));
      orders_wait_flag(tep_db_input($oID));

      $customer_notified = '0';
      
      if ($_POST['notify'] == 'on') {
  
        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . '円';
        
        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        $title = str_replace(array(
          '${NAME}',
          '${MAIL}',
          '${ORDER_D}',
          '${ORDER_N}',
          '${PAY}',
          '${ORDER_M}',
          '${TRADING}',
          '${ORDER_S}',
          '${SITE_NAME}',
          '${SITE_URL}',
          '${PAY_DATE}'
          
        ),array(
          $check_status['customers_name'],
          $check_status['customers_email_address'],
          tep_date_long($check_status['date_purchased']),
          $oID,
          $check_status['payment_method'],
          $otm,
          tep_torihiki($check_status['torihiki_date']),
          $os_result['orders_status_name'],
          get_configuration_by_site_id('STORE_NAME', $site_id),
          get_url_by_site_id($site_id),
          date('Y年n月j日',strtotime(tep_get_pay_day()))
        ),$title
        );
        $comments = str_replace(array(
          '${NAME}',
          '${MAIL}',
          '${ORDER_D}',
          '${ORDER_N}',
          '${PAY}',
          '${ORDER_M}',
          '${TRADING}',
          '${ORDER_S}',
          '${SITE_NAME}',
          '${SITE_URL}',
          '${SUPPORT_EMAIL}',
          '${PAY_DATE}'
        ),array(
          $check_status['customers_name'],
          $check_status['customers_email_address'],
          tep_date_long($check_status['date_purchased']),
          $oID,
          $check_status['payment_method'],
          $otm,
          tep_torihiki($check_status['torihiki_date']),
          $os_result['orders_status_name'],
          get_configuration_by_site_id('STORE_NAME', $site_id),
          get_url_by_site_id($site_id),
          get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
          date('Y年n月j日',strtotime(tep_get_pay_day()))
        ),$comments
        );
        
        if (!tep_is_oroshi($check_status['customers_id'])) {
          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
        } 
        tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), '送信済：'.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
        $customer_notified = '1';
      }
      
        
      if($_POST['notify_comments'] == 'on') {
        $customer_notified = '1';
      } else {
        $customer_notified = '0';
      }
      tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '')");

      $order_updated = true;
      
      // 同步问答
      orders_status_updated_for_question($oID,tep_db_input($status),$_POST['notify_comments'] == 'on');
    }

      if ($order_updated) {
        $messageStack->add_session('注文ID' . $oID . 'の' . SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session('注文ID' . $oID . 'の' . WARNING_ORDER_NOT_UPDATED, 'warning');
      }
    }

      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));

    
    break;
    //------------------------------------------
  case 'update_order':
      $oID      = tep_db_prepare_input($_GET['oID']);
      $status   = tep_db_prepare_input($_POST['s_status']);
      $title    = tep_db_prepare_input($_POST['title']);
      $comments = tep_db_prepare_input($_POST['comments']);
      $site_id  = tep_get_site_id_by_orders_id($oID);

      $order_updated = false;
      $check_status_query = tep_db_query("
          select orders_id, 
                 customers_name, 
                 customers_id,
                 customers_email_address, 
                 orders_status, 
                 date_purchased, 
                 payment_method, 
                 torihiki_date 
          from " . TABLE_ORDERS . " 
          where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);
    
    //Add Point System
    if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
      $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
      $pcount = tep_db_fetch_array($pcount_query);
    if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
      $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
      $result1 = tep_db_fetch_array($query1);
      $query2 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
      $result2 = tep_db_fetch_array($query2);
      $query3 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
      $result3 = tep_db_fetch_array($query3);
      $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
      $result4 = tep_db_fetch_array($query4);


      
    // ここからカスタマーレベルに応じたポイント還元率算出============================================================
    if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
      $customer_id = $result1['customers_id'];
      //設定した期間内の注文合計金額を算出------------
      $ptoday = date("Y-m-d H:i:s", time());
      $pstday_array = getdate();
      $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
      $total_buyed_date = 0;
      $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
      if(tep_db_num_rows($customer_level_total_query)) {
        while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
          $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
          $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
        
          $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
          $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
         
          $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
        }
      }
      //----------------------------------------------
      //今回の注文額は除外
      $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
      //還元率を計算----------------------------------
      if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
        $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
        $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        for($j=0; $j<sizeof($back_rate_array); $j++) {
          $back_rate_array2 = explode(",", $back_rate_array[$j]);
          if($back_rate_array2[2] <= $total_buyed_date) {
          $back_rate = $back_rate_array2[1];
          $back_rate_name = $back_rate_array2[0];
          }
        }
      } else {
        $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
        if($back_rate_array[2] <= $total_buyed_date) {
          $back_rate = $back_rate_array[1];
          $back_rate_name = $back_rate_array[0];
        }
      }
      //----------------------------------------------
      $point_rate = $back_rate;
    } else {
      $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
    }
    // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
      if ($result3['value'] >= 0) {
        $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
      } else {
        if ($result3['value'] > -200) {
          if ($check_status['payment_method'] == '来店支払い') {
            $get_point = 0;
          } else {
            $get_point = abs($result3['value']);
          }
        } else {
          $get_point = 0;
        }
      }
      //$plus = $result4['point'] + $get_point;
      
      if($check_status['payment_method'] != 'ポイント(買い取り)'){
      tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $get_point . " where customers_id = " . $result1['customers_id'] );
      }
    }else{
      $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
      $os_result = tep_db_fetch_array($os_query);
      if($os_result['orders_status_name']=='支払通知*'){
       $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
       $result1 = tep_db_fetch_array($query1);
       if ($check_status['payment_method'] == 'ポイント(買い取り)') {
         $query_t = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".tep_db_input($oID)."'");
         $result_t = tep_db_fetch_array($query_t);
         $get_point = abs(intval($result_t['value']));
       } else {
         $get_point = 0;
       }
       $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from
         ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and 
         orders_id = '".tep_db_input($oID)."'");
       $point_done_row  =  tep_db_fetch_array($point_done_query);
       if($point_done_row['cnt'] <1 ){
      tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " .
          $get_point . " where customers_id = " . $result1['customers_id'] );
       }
      }
    }
    }
    
    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      orders_updated(tep_db_input($oID));
      orders_wait_flag(tep_db_input($oID));
      $customer_notified = '0';
    
    if ($_POST['notify'] == 'on') {

      $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
      $ot_result = tep_db_fetch_array($ot_query);
      $otm = (int)$ot_result['value'] . '円';
      
      $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
      $os_result = tep_db_fetch_array($os_query);

      $title = str_replace(array(
        '${NAME}',
        '${MAIL}',
        '${ORDER_D}',
        '${ORDER_N}',
        '${PAY}',
        '${ORDER_M}',
        '${TRADING}',
        '${ORDER_S}',
        '${SITE_NAME}',
        '${SITE_URL}',
        '${SUPPORT_EMAIL}',
        '${PAY_DATE}'
      ),array(
        $check_status['customers_name'],
        $check_status['customers_email_address'],
        tep_date_long($check_status['date_purchased']),
        $oID,
        $check_status['payment_method'],
        $otm,
        tep_torihiki($check_status['torihiki_date']),
        $os_result['orders_status_name'],
        get_configuration_by_site_id('STORE_NAME', $site_id),
        get_url_by_site_id($site_id),
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
        date('Y年n月j日',strtotime(tep_get_pay_day()))
      ),$title);

      $comments = str_replace(array(
        '${NAME}',
        '${MAIL}',
        '${ORDER_D}',
        '${ORDER_N}',
        '${PAY}',
        '${ORDER_M}',
        '${TRADING}',
        '${ORDER_S}',
        '${SITE_NAME}',
        '${SITE_URL}',
        '${SUPPORT_EMAIL}',
        '${PAY_DATE}'
      ),array(
        $check_status['customers_name'],
        $check_status['customers_email_address'],
        tep_date_long($check_status['date_purchased']),
        $oID,
        $check_status['payment_method'],
        $otm,
        tep_torihiki($check_status['torihiki_date']),
        $os_result['orders_status_name'],
        get_configuration_by_site_id('STORE_NAME', $site_id),
        get_url_by_site_id($site_id),
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
        date('Y年n月j日',strtotime(tep_get_pay_day()))
      ),$comments);
      if (!tep_is_oroshi($check_status['customers_id'])) {
        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
      }
      tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), '送信済：'.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
      $customer_notified = '1';
    }
    
      
    if($_POST['notify_comments'] == 'on') {
      $customer_notified = '1';
    } else {
      $customer_notified = '0';
    }
    tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '')");
    // 同步问答
    orders_status_updated_for_question($oID,tep_db_input($status),$_POST['notify_comments'] == 'on', $_POST['qu_type']);
    $order_updated = true;
  }

      if ($order_updated) {
        $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      }
      //if ($status == 6 or $status == 8) {
      //  tep_redirect(tep_href_link(FILENAME_ORDERS));
      //} else {
        tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
      //}
      break;
    case 'deleteconfirm':
      $oID = tep_db_prepare_input($_GET['oID']);

      tep_remove_attributes($oID, $_POST['restock']);

      tep_remove_order($oID, $_POST['restock']);

      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))));
      break;
  }

  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($_GET['oID']) ) {
    $oID = tep_db_prepare_input($_GET['oID']);

    $orders_query = tep_db_query("
        select orders_id 
        from " . TABLE_ORDERS . " 
        where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
  }

  include(DIR_WS_CLASSES . 'order.php');
  
  //------------------------------------------------
  $suu = 0;
  $text_suu = 0;  
  $__orders_status_query = tep_db_query("
      select orders_status_id 
      from " . TABLE_ORDERS_STATUS . " 
      where language_id = " . $languages_id . " 
      order by orders_status_id");
  $__orders_status_ids   = array();
  while($__orders_status = tep_db_fetch_array($__orders_status_query)){
    $__orders_status_ids[] = $__orders_status['orders_status_id'];
  }
  $select_query = tep_db_query("
      select om.orders_status_mail,
                      om.orders_status_title,
                      os.orders_status_id,
                      os.nomail,
                      om.site_id
      from ".TABLE_ORDERS_STATUS." os left join ".TABLE_ORDERS_MAIL." om on os.orders_status_id = om.orders_status_id
      where os.language_id = " . $languages_id . " 
        and os.orders_status_id IN (".join(',', $__orders_status_ids).")");

  while($select_result = tep_db_fetch_array($select_query)){
    if($suu == 0){
      $select_select = $select_result['orders_status_id'];
      $suu = 1;
    }
    
    $osid = $select_result['orders_status_id'];
    
    if($text_suu == 0){
      $select_text = $select_result['orders_status_mail'];
      $select_title = $select_result['orders_status_title'];
      $text_suu = 1;
      $select_nomail = $select_result['nomail'];
    }
    
    $mt[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_mail'];
    $mo[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_title'];
    $nomail[$osid] = $select_result['nomail'];
  }

  //------------------------------------------------
  if(isset($_GET['reload'])) {
    switch($_GET['reload']) {
    case 'Yes':
      $reload = 'yes';
    tep_session_register('reload');
    break;
    case 'No':
      $reload = 'no';
    tep_session_register('reload');
    break;
  } 
  } else {
    if(tep_session_is_registered('reload')) {
    //Read Session
  } else {
    $reload = 'yes';
  }
  }
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
  function check_torihiki_date_error($oid){
    $query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id='" . $oid . "'");
    $order = tep_db_fetch_array($query);
    if ($order['torihiki_date'] == '0000-00-00 00:00:00') {
      return true;
    }
    return false;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<?php 
  // 订单详细页，TITLE显示交易商品名
  if ($_GET['action']=='edit' && $_GET['oID']) {?>
<title><?php echo tep_get_orders_products_names($_GET['oID']); ?></title>
<?php } else { ?>
<title><?php echo TITLE; ?></title>
<?php }?>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/all_order.js"></script>
<script language="javascript">
  // 用作跳转
  var base_url = '<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('questions_type')));?>';
  
  // 非完成状态的订单不显示最终确认
  var show_q_8_1_able  = <?php echo tep_orders_finished($_GET['oID']) && !check_torihiki_date_error($_GET['oID']) ?'true':'false';?>;
  
  var cfg_last_customer_action = '<?php echo LAST_CUSTOMER_ACTION;?>';

<?php 
  // 输出订单邮件
  // title
  foreach ($mo as $oskey => $value){
    echo 'window.status_title['.$oskey.'] = new Array();'."\n";
    foreach ($value as $sitekey => $svalue) {
      echo 'window.status_title['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
    }
  }
  
  //content
  foreach ($mt as $oskey => $value){
    echo 'window.status_text['.$oskey.'] = new Array();'."\n";
    foreach ($value as $sitekey => $svalue) {
      echo 'window.status_text['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
    }
  }

  //no mail
  echo 'var nomail = new Array();'."\n";
  foreach ($nomail as $oskey => $value){
    echo 'nomail['.$oskey.'] = "' . $value . '";' . "\n";
  }
?>
<?php
/*
if($reload == 'yes') {
  if((int)DS_ADMIN_ORDER_RELOAD < 1) {
    $reloadcnt = '60';
  } else {
    $reloadcnt = DS_ADMIN_ORDER_RELOAD;
  }
}
*/
?>
// 以当前时间为如今日
function q_3_2(){
  if ($('#q_3_1').attr('checked') == true){
    if ($('#q_3_2_m').val() == '' || $('#q_3_2_m').val() == '') {
      $('#q_3_2_m').val(new Date().getMonth()+1);
      $('#q_3_2_d').val(new Date().getDate());
    }
  }
}

// 以当前时间为如今日
function q_4_3(){
  if ($('#q_4_2').attr('checked') == true){
    if ($('#q_4_3_m').val() == '' || $('#q_4_3_m').val() == '') {
      $('#q_4_3_m').val(new Date().getMonth()+1);
      $('#q_4_3_d').val(new Date().getDate());
    }
  }
}
</script>
</head>
<body>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission >= 10) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
    // edit start
    $order = new order($oID);
?>
<script>
  // 游戏人物名，订单详细页用来替换邮件内容
  var orders_status_id = <?php echo $order->info['orders_status'];?>;
  window.orderStr = '<?php echo  str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'), orders_a($order->info['orders_id'], array(array('orders_id' => $order->info['orders_id']))));?>';
</script>
<tr>
  <td width="100%">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
              <td class="pageHeading" align="right">
                <?php if ($ocertify->npermission) { ?>
                <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action','status','questions_type')) . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'; ?>
                <?php } ?>
                <?php echo '<a id="back_link" href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- 三种状态 + A,B,C -->
      <tr>
        <td width="100%">
          <div id="orders_flag">
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="50%" align="left">
                  <table width="100%" border="0" cellspacing="2" cellpadding="2">
                    <tr>
                      <?php 

/* <!--<td width="100" align="center" class='<?php echo $order->info['orders_important_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'important')">重要</td>--> */ ?>
                      <td width="100" align="center" class='<?php echo $order->info['orders_care_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'care', '<?php echo $order->info['orders_id'];?>')">取り扱い注意</td>
                      <td width="100" align="center" class='<?php echo $order->info['orders_wait_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'wait', '<?php echo $order->info['orders_id'];?>')">取引待ち</td>
                      <td width="100" align="center" class='<?php echo $order->info['orders_inputed_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'inputed', '<?php echo $order->info['orders_id'];?>')">入力済み</td>
                      <td>&nbsp;</td>
                    <tr>
                  </table>
                </td>
                <td width="50%" align="right">
                  <table width="100%" border="0" cellspacing="2" cellpadding="2">
                    <tr>
                      <td>&nbsp;</td>
                      <td width="100" align="center" id="work_a" class='<?php echo $order->info['orders_work'] == 'a' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'a', '<?php echo $order->info['orders_id'];?>')">A</td>
                      <td width="100" align="center" id="work_b" class='<?php echo $order->info['orders_work'] == 'b' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'b', '<?php echo $order->info['orders_id'];?>')">B</td>
                      <td width="100" align="center" id="work_c" class='<?php echo $order->info['orders_work'] == 'c' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'c', '<?php echo $order->info['orders_id'];?>')">C</td>
                    <tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td>
        <!-- 左右结构 -->
            <!-- left -->
            <div class="pageHeading_box">
            <div id="orders_info">
              <h3>Order Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%"><b>注文書サイト:</b></td>
                  <td class="main"><b style=" color:#FF0000"><?php echo tep_get_site_name_by_order_id($oID);?></b></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>取引日時:</b></td>
                  <td class="main"><b style=" color:#0000FF"><?php echo $order->tori['date'];?></b></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b>オプション:</b></td>
                  <td class="main"><b style=" color:#0000FF"><?php echo $order->tori['houhou'];?></b></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b>ご注文番号:</b></td>
                  <td class="main"><?php echo $_GET['oID'] ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b>注文日:</b></td>
                  <td class="main"><?php echo tep_date_long($order->customer['date']); ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b>顧客種別:</b></td>
                  <td class="main"><?php echo get_guest_chk($order->customer['id'])?'ゲスト':'会員';?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
                  <td class="main" style="text-decoration: underline; "><a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, 'action=edit&cID='.$order->customer['id']);?>"><?php echo $order->customer['name']; ?></a></td>
                </tr>
                <tr>
                  <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                  <td class="main">
<?php 
    //osticket
    $ostGetPara = array(
                        "name"=>$order->customer['name'],
                        "topicid"=>constant("SITE_TOPIC_".$order->info['site_id']),
                        "source"=>'Email',
                        "email"=>$order->customer['email_address']);
    function makeValueUrlencode(&$value,$key){
      $value = urlencode($value);
    }
    array_walk($ostGetPara,'makeValueUrlencode');
    $parmStr = '';
    foreach($ostGetPara as $key=>$value){
      $parmStr.= '&'.$key.'='.$value;
    }
    $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";
?>
    <?php echo '<a class="order_link" href="javascript:void(0);" onclick="copyToClipboard(\'' . tep_output_string_protected($order->customer['email_address']) . '\')">' . tep_output_string_protected($order->customer['email_address']) . '</a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="問合番号を新規作成します" href="'.$remoteurl.'" target="_blank">メール</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($order->customer['email_address']).'">クレカ</a>'; 
?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
                  <td class="main"><?php echo $order->info['payment_method']; ?></td>
                </tr>
            <?php
                if ( (($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number'])) ) {
            ?>
                <tr>
                  <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
                  <td class="main"><?php echo $order->info['cc_type']; ?></td>
                </tr>
                <tr>
                  <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
                  <td class="main"><?php echo $order->info['cc_owner']; ?></td>
                </tr>
                <tr>
                  <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
                  <td class="main"><?php echo $order->info['cc_number']; ?></td>
                </tr>
                <tr>
                  <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
                  <td class="main"><?php echo $order->info['cc_expires']; ?></td>
                </tr>
            <?php
                }
            ?>
              </table>
            </div>
            <div id="orders_client">
              <h3>Customer Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%"><b>IPアドレス:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_ip'] ? $order->info['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>ホスト名:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_host_name']?'<font'.($order->info['orders_host_name'] == $order->info['orders_ip'] ? ' color="red"':'').'>'.$order->info['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>ユーザーエージェント:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_user_agent'] ? $order->info['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS);?></td>
                </tr>
                <?php if ($order->info['orders_user_agent']) {?>
                <tr>
                  <td class="main" valign="top" width="30%"><b>OS:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords(getOS($order->info['orders_user_agent']),OS_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>ブラウザの種類:</b></td>
                  <td class="main">
                  <?php $browser_info = getBrowserInfo($order->info['orders_user_agent']);?>
                  <?php echo tep_high_light_by_keywords($browser_info['longName'] . ' ' . $browser_info['version'],BROWSER_LIGHT_KEYWORDS); ?>
                  </td>
                </tr>
                <?php }?>
                <tr>
                  <td class="main" valign="top" width="30%"><b>ブラウザの言語:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_http_accept_language'] ? $order->info['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>パソコンの言語環境:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_system_language'] ? $order->info['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>ユーザーの言語環境:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_user_language'] ? $order->info['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>画面の解像度:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_screen_resolution'] ? $order->info['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>画面の色:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_color_depth'] ? $order->info['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Flash:</b></td>
                  <td class="main">
                    <?php echo tep_high_light_by_keywords($order->info['orders_flash_enable'] === '1' ? 'YES' : ($order->info['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <?php 
                  if ($order->info['orders_flash_enable']) {
                ?>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Flashのバージョン:</b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS);?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Director:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_director_enable'] === '1' ? 'YES' : ($order->info['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Quick time:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_quicktime_enable'] === '1' ? 'YES' : ($order->info['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Real player:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_realplayer_enable'] === '1' ? 'YES' : ($order->info['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Windows media:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_windows_media_enable'] === '1' ? 'YES' : ($order->info['orders_windows_media_enable'] === '0' ? 'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Pdf:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_pdf_enable'] === '1' ? 'YES' : ($order->info['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Java:</b></td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_java_enable'] === '1' ? 'YES' : ($order->info['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
              </table>
            </div>
            <!-- 访问解析 -->
            <div id="orders_referer">
              <h3>Referer Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%"><b>Referer:</b></td>
                  <td class="main"><p
                  style="word-break:break-all;width:650px;word-wrap:break-word;overflow:hidden;display:block;"><?php echo urldecode($order->info['orders_ref']);?></p></td>
                </tr>
                <?php if ($order->info['orders_ref_keywords']) { ?>
                <tr>
                  <td class="main" valign="top" width="30%"><b>Keywords:</b></td>
                  <td class="main"><?php echo $order->info['orders_ref_keywords'];?></td>
                </tr>
                <?php } ?>
              </table>
            </div>
                
            <?php if ($order->info['payment_method'] == 'クレジットカード決済') { ?>
            <!-- 信用卡信息 -->

            <div id="orders_telecom">
              <h3>クレジットカード情報</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                <tr>
                  <td class="main" valign="top" width="20%"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>&search_type=username">カード名義:</a></b></td>
                  <td class="main" width="30%"><?php echo $order->info['telecom_name'];?></td>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>&search_type=telno">電話番号:</a></b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>&search_type=email">メールアドレス:</a></b></td>
                  <td class="main"><?php echo $order->info['telecom_email'];?></a></td>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>&search_type=money">金額:</a></b></td>
                  <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                </tr>
              </table>
            </div>

            <?php }else if ($order->info['payment_method'] == 'ペイパル決済') {?>
            <!-- PAYPAL信息 -->

            <div id="orders_paypal">
              <h3>クレジットカード情報</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                <tr>
                  <td class="main" valign="top" width="20%"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>">カード名義:</a></b></td>
                  <td class="main" width="30%"><?php echo $order->info['telecom_name'];?></td>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>">電話番号:</a></b></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>">メールアドレス:</a></b></td>
                  <td class="main"><?php echo $order->info['telecom_email'];?></a></td>
                  <td class="main" valign="top"><b><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>">金額:</a></b></td>
                  <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="20%"><b>居住国:</b></td>
                  <td class="main" width="30%"><?php echo $order->info['paypal_countrycode'];?></td>
                  <td class="main" valign="top"><b>認証:</b></td>
                  <td class="main"><?php echo $order->info['paypal_payerstatus'];?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><b>支払ステータス:</b></td>
                  <td class="main"><?php echo $order->info['paypal_paymentstatus'];?></a></td>
                  <td class="main" valign="top"><b>支払タイプ:</b></td>
                  <td class="main"><?php echo $order->info['paypal_paymenttype'];?></a></td>
                </tr>
              </table>
            </div>
            <?php } ?>
            <!-- 注文履历 -->
            <?php // 订单历史5条 ?>
            <div id="orders_history">
              <h3><a href="<?php echo tep_href_link('customers_products.php', 'cID='.$order->customer['id'].'&cpage=1')?>">Order History</a></h3>
              <?php 
                $order_history_query = tep_db_query("
                  select * 
                  from ".TABLE_ORDERS." 
                  where customers_id='".$order->customer['id']."'
                  order by date_purchased desc
                  limit 5
                ");
                if (tep_db_num_rows($order_history_query)) {
                  ?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="2">
                  <?php
                  while($order_history = tep_db_fetch_array($order_history_query)){
                  ?>
                    <tr>
                      <td class="main"><?php echo $order_history['date_purchased'];?></td>
                      <td class="main"><?php echo
                      strip_tags(tep_get_ot_total_by_orders_id($order_history['orders_id'],true));?></td>
                      <td class="main"><?php echo $order_history['orders_status_name'];?></td>
                    </tr>
                  <?php
                  }
                  ?>
                  </table>
                  <?php
                } else {
                  echo "no orders";
                }
              ?>
            </div>
            </div>
            <!-- /left -->
            <!-- right -->
            <div class="pageHeading_box02">
              <?php // 订单备注 ?>
              <div id="orders_comment">
              <h3>Order Comment</h3>
                <form action="ajax_orders.php" id='form_orders_comment' method="post">

                <textarea name="orders_comment" cols="100" rows="10" style="width:100%"><?php echo $order->info['orders_comment'];?></textarea><br>
                <input type="hidden" name="orders_id" value="<?php echo $order->info['orders_id'];?>">
                <div align="right"><input type="Submit" value="保存"></div>
                </form>
              </div>
              <div id="orders_answer">
<?php
  // 取得问答的答案
  $orders_questions_query = tep_db_query("select * from orders_questions where orders_id = '".$order->info['orders_id']."'");
  
  $oq = tep_db_fetch_array($orders_questions_query);

  $total_order_query = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where orders_id = '".$order->info['orders_id']."' and class = 'ot_total'"); 
  $total_order_res = tep_db_fetch_array($total_order_query); 
  $total_order_sum = 0; 
  if ($total_order_res) {
    $total_order_sum = $total_order_res['value']; 
  }
  // 自动或者手动判断问答种类
  // 0=>贩卖, 1=>买取, 2=>信用卡, 3=>返点/来店 , 4=>不需要支付
  if (isset($_GET['questions_type'])) {
    $orders_questions_type = intval($_GET['questions_type']);
  } else if ($oq['orders_questions_type']) {
    $orders_questions_type = $oq['orders_questions_type'];
  } else {
    $has_oquestion_raw = tep_db_query("select * from orders_questions where orders_id = '".$order->info['orders_id']."'"); 
    if (tep_db_num_rows($has_oquestion_raw)) { 
      $has_oquestion_res = tep_db_fetch_array($has_oquestion_raw);
      $orders_questions_type = $has_oquestion_res['orders_questions_type']; 
    } else { 
      $o_orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$order->info['orders_id']."'"); 
      $o_orders_res = tep_db_fetch_array($o_orders_query);
      $o_cus_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$o_orders_res['customers_id']."' and site_id = '".$o_orders_res['site_id']."'"); 
      $o_cus_res = tep_db_fetch_array($o_cus_query);
      $guch_num = 0; 
      if ($o_cus_res) {
        $guch_num = $o_cus_res['customers_guest_chk']; 
      }
      if ($guch_num == 9) {
        $orders_questions_type = 1;
      } else {
      if ($total_order_sum < 0) {
      if ($order->info['payment_method'] == '銀行振込(買い取り)' || $order->info['payment_method'] == '支払いなし' || $order->info['payment_method'] == '来店支払い' || $order->info['payment_method'] == 'ポイント(買い取り)') {
        $orders_questions_type = 1;
      } else {
        if ($order->info['payment_method'] === 'クレジットカード決済') {
          $orders_questions_type = 1;
        } else if ($order->info['payment_method'] === 'クレジットカード決済' || $order->info['payment_method'] === 'ペイパル決済') {
          $orders_questions_type = 2;
        } else if ($order->info['payment_method'] === '来店支払い' || $order->info['payment_method'] === 'ポイント(買い取り)') {
          $orders_questions_type = 3;
        } else if ($order->info['payment_method'] === '支払いなし') {
          $orders_questions_type = 4;
        } else {
          $orders_questions_type = 0;
        }
      }
    } else {
      if ($order->info['payment_method'] == 'ペイパル決済' || $order->info['payment_method'] == 'クレジットカード決済') {
        $orders_questions_type = 2;
      } else {
        $orders_questions_type = 0;
      }
    }
      }
    }
  }
?>
                <h3>Order Answer</h3>
                <!-- ajax submit -->
                <form name="form_orders_questions" id='form_orders_questions' action="ajax_orders.php?action=save_questions&orders_id=<?php echo $order->info['orders_id'];?>" method='post'>
<table width="100%">
  <tr>
    <td class="main" width="30%">支払方法：</td>
    <td class="main">
<?php //if (tep_is_oroshi($order->customer['id']) && $orders_questions_type==1) { ?>
<?php      
if (false) {
?>
買取：銀行支払<input type="hidden" name="questions_type" id="questions_type" value="1">
<?php
}
?>
<?php //} else { ?>
      <select name="questions_type" id="questions_type" onchange="window.location.href=base_url+'&questions_type='+this.value">
<?php if (isset($_GET['questions_type'])) { ?>
        <option value="0">販売：銀行振込</option>
        <option value="2"<?php if ($_GET['questions_type']==2) {?> selected="selected"<?php } ?>>販売：クレカ</option>
        <option value="1"<?php if ($_GET['questions_type']==1) {?> selected="selected"<?php } ?>>買取：銀行支払</option>
<?php } else { ?>
        <?php
          $ex_oquestion_raw = tep_db_query("select * from orders_questions where orders_id = '".$order->info['orders_id']."'"); 
          if (tep_db_num_rows($ex_oquestion_raw)) {
        ?>
              <option value="0">販売：銀行振込</option>
              <option value="2"<?php if ($orders_questions_type==2) {?> selected="selected"<?php } ?>>販売：クレカ</option>
              <option value="1"<?php if ($orders_questions_type==1) {?> selected="selected"<?php } ?>>買取：銀行支払</option>
        <?php
          } else {
          $o_orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$order->info['orders_id']."'"); 
          $o_orders_res = tep_db_fetch_array($o_orders_query);
          $o_cus_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$o_orders_res['customers_id']."' and site_id = '".$o_orders_res['site_id']."'"); 
          $o_cus_res = tep_db_fetch_array($o_cus_query);
          $guch_num = 0; 
          if ($o_cus_res) {
            $guch_num = $o_cus_res['customers_guest_chk']; 
          }
          if ($guch_num == 9) {
          ?>
              <option value="0">販売：銀行振込</option>
              <option value="2">販売：クレカ</option>
              <option value="1" selected="selected">買取：銀行支払</option>
          <?php
          } else {
          if ($total_order_sum < 0) {
            if ($order->info['payment_method'] == '銀行振込(買い取り)' || $order->info['payment_method'] == '支払いなし' || $order->info['payment_method'] == '来店支払い' || $order->info['payment_method'] == 'ポイント(買い取り)') {
            ?>
              <option value="0">販売：銀行振込</option>
              <option value="2">販売：クレカ</option>
              <option value="1" selected="selected">買取：銀行支払</option>
            <?php
            } else {
            ?>
              <option value="0">販売：銀行振込</option>
              <option value="2"<?php if ($orders_questions_type==2) {?> selected="selected"<?php } ?>>販売：クレカ</option>
              <option value="1"<?php if ($orders_questions_type==1) {?> selected="selected"<?php } ?>>買取：銀行支払</option>
            <?php
            }
          } else {
            if ($order->info['payment_method'] == 'ペイパル決済' || $order->info['payment_method'] == 'クレジットカード決済') {
          ?>
              <option value="0">販売：銀行振込</option>
              <option value="2" selected="selected">販売：クレカ</option>
              <option value="1">買取：銀行支払</option>
          <?php
            } else {
          ?>
              <option value="0" selected="selected">販売：銀行振込</option>
              <option value="2">販売：クレカ</option>
              <option value="1">買取：銀行支払</option>
          <?php
            }
          }
          }
          }
        ?>
<?php } ?>
      </select>
<?php //} ?>
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right">&nbsp;</td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">備考の有無：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_1_1', '0', $oq['q_1_1'] === '0', '', 'id="q_1_1_0" onclick="exclude(this,\'q_1_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無｜<?php echo tep_draw_radio_field('q_1_1', '1', $oq['q_1_1'] === '1', '', 'id="q_1_1_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>有 → <?php echo tep_draw_checkbox_field('q_1_2', '1', $oq['q_1_2'] === '1', '', 'id="q_1_2" onclick="auto_radio(this,\'q_1_1_1\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>返答済
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_1_1_0').attr('checked', '');$('#q_1_1_1').attr('checked', '');$('#q_1_2').attr('checked', '');clean_option(1,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
<?php
  if ($orders_questions_type == 1) {
    //買取
    //受领通知
    $pay_time = tep_get_orders_status_history_time($order->info['orders_id'], 13);
    $pay_email = tep_get_orders_status_history_notified($order->info['orders_id'], 13);
    //支付通知
    $end_time = tep_get_orders_status_history_time($order->info['orders_id'], 5);
    $end_email = tep_get_orders_status_history_notified($order->info['orders_id'], 5);
?>
  <tr>
    <td class="main">キャラクターの有無：</td>
    <td class="main"><?php echo tep_draw_radio_field('q_12_1', '1', $oq['q_12_1'] === '1', '', 'id="q_12_1_1" onclick="exclude(this,\'q_12_2\')" onchange="change_option(this)"');?>有｜<?php echo tep_draw_radio_field('q_12_1', '0', $oq['q_12_1'] === '0', '', 'id="q_12_1_0" onchange="change_option(this)"');?>無 → <?php echo tep_draw_checkbox_field('q_12_2', '1', $oq['q_12_2'] === '1', '', 'id="q_12_2" onclick="auto_radio(this,\'q_12_1_0\')" onchange="change_option(this)"');?>新規作成してお客様へ連絡</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_12_1_0').attr('checked','');$('#q_12_1_1').attr('checked','');$('#q_12_2').attr('checked','');clean_option(12,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">受領 <font color="red">※注意※</font>：</td>
    <td class="main"><?php echo tep_draw_checkbox_field('q_13_1', '1', isset($oq['q_13_1']) ? $oq['q_13_1'] === '1' : $pay_time,'', 'id="q_13_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?><?php echo tep_draw_input_field('q_13_2_m', $oq['q_13_2'] && $oq['q_13_2'] != '0000-00-00' ? date('m', strtotime($oq['q_13_2'])) : ($pay_time?date('m', strtotime($pay_time)):''), 'size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_13_2_d', $oq['q_13_2'] && $oq['q_13_2'] != '0000-00-00' ? date('d', strtotime($oq['q_13_2'])) : ($pay_time?date('d', strtotime($pay_time)):''), 'size="2" class="questions_date" readonly');?>日</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_13_1').attr('checked','');clean_option(13,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>

  <!-- 关联商品库存的处理 -->
  <tr>
    <!-- <td class="main">&nbsp;</td>-->
    <td class="main" id="relate_products_box" colspan="2">
    
<table border="0">
<?php
  $orders_products_query = tep_db_query("select p.products_id,op.products_quantity,op.products_name,p.relate_products_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where op.products_id=p.products_id and op.orders_id='".$order->info['orders_id']."' order by op.products_name asc");

  while ($opp = tep_db_fetch_array($orders_products_query)) {
    $op = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id=pd.products_id and pd.site_id='0' and p.products_id='".$opp['relate_products_id']."'"));
    if ($op) {
    
    $oqp = tep_db_fetch_array(tep_db_query("select * from orders_questions_products where orders_id='".$order->info['orders_id']."' and products_id='".$op['products_id']."'"));
?>
<tr>
<td><input type="checkbox" class="relate_chk" id="checkbox_<?php echo $opp['products_id'];?>" name="relate_product[<?php echo $op['products_id'];?>]" value="1" onclick="click_relate(<?php echo $opp['products_id'];?>,this)" onchange="change_option(this)" onpropertychange="propertychange_option(this)"<?php if($oqp['checked']) { ?> checked<?php } ?>> <?php echo $op['products_name'];?></td>
<td><span id="quantity_<?php echo $opp['products_id'];?>"><?php echo $opp['products_quantity'];?></span>-<input type="text" id="offset_<?php echo $opp['products_id'];?>" name="offset[<?php echo $op['products_id'];?>]]" value="<?php echo $oqp['offset'] ? $oqp['offset'] : 0;?>" onchange="if(document.getElementById('quantity_<?php echo $opp['products_id'];?>').innerHTML-this.value<0){this.value=0;}change_option(this);print_quantity(<?php echo $opp['products_id'];?>);" onpropertychange="propertychange_option(this)" onmouseout="if(document.getElementById('quantity_<?php echo $opp['products_id'];?>').innerHTML-this.value<0){this.value=0;}change_option(this);print_quantity(<?php echo $opp['products_id'];?>);"<?php if($oqp['checked']){ ?> readonly<?php } ?>>=<span id="relate_product_<?php echo $opp['products_id'];?>"><?php echo $opp['products_quantity']-$oqp['offset'];?></span></td>
</tr>
<?php
    } else {
    $oqp = tep_db_fetch_array(tep_db_query("select * from orders_questions_products where orders_id='".$order->info['orders_id']."' and products_id='".$op['products_id']."'"));
?>
<tr><td><input type="checkbox" class="relate_chk" id="checkbox_<?php echo $opp['products_id'];?>" name="relate_product[<?php echo $op['products_id'];?>]" value="1" onclick="click_relate(<?php echo $opp['products_id'];?>)" onchange="change_option(this)" onpropertychange="propertychange_option(this)"<?php if($oqp['checked']) { ?> checked<?php } ?>> <?php echo $opp['products_name'];?></td><td> 関連付け商品がないので手動入力してください</td></tr>
<?php
    }
  }
?>
</table>
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="clear_quantity();$('#relate_products_box input[type=text]').val('0');$('#relate_products_box input[type=text]').attr('readonly',false);clean_option('','<?php echo $order->info['orders_id'];?>');print_quantity(<?php echo $op['products_id'];?>);clean_option('relate','<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">残量入力→誤差有無：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_6_1', '0', $oq['q_6_1'] === '0', '', 'id="q_6_1_0" onclick="exclude(this,\'q_6_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無｜<?php echo tep_draw_radio_field('q_6_1', '1', $oq['q_6_1'] === '1', '', 'id="q_6_1_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>有 → <?php echo tep_draw_checkbox_field('q_6_2', '1', $oq['q_6_2'] === '1', '', 'id="q_6_2" onclick="auto_radio(this,\'q_6_1_1\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>報告
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_6_1_0').attr('checked','');$('#q_6_1_1').attr('checked','');$('#q_6_2').attr('checked','');clean_option(6,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">受領メール送信：</td>
    <td class="main"><?php echo tep_draw_checkbox_field('q_14_1', '1', isset($oq['q_14_1']) ? $oq['q_14_1'] === '1' : $pay_email,'','id="q_14_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?><?php echo tep_draw_hidden_field('q_14_2', $oq['q_14_2'] ? $oq['q_14_2'] : $order->customer['email_address'], 'size="20" class="readonly" readonly');?>済</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_14_1').attr('checked','');$('#q_14_3').attr('checked','');clean_option(14,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main" valign="top">支払：</td>
    <td class="main" id='td_q_15'>
<table>
  <tr>
    <td class="main"><?php echo tep_draw_checkbox_field('q_15_1', '1', isset($oq['q_15_1']) ? $oq['q_15_1'] === '1' : $end_time,'','id="q_15_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?><?php echo tep_draw_input_field('q_15_2_m', $oq['q_15_2'] && $oq['q_15_2'] != '0000-00-00' ? date('m', strtotime($oq['q_15_2'])) : ($end_time?date('m', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_15_2_d', $oq['q_15_2'] && $oq['q_15_2'] != '0000-00-00' ? date('d', strtotime($oq['q_15_2'])) : ($end_time?date('d', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>日 ＊総額5.000円未満は168円引く＊</td>
  </tr>
  <tr>
    <td class="main"><?php echo tep_draw_checkbox_field('q_15_3', '1', isset($oq['q_15_3']) ? $oq['q_15_3'] === '1' : '','','id="q_15_3" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>JNB <?php echo tep_draw_checkbox_field('q_15_4', '1', isset($oq['q_15_4']) ? $oq['q_15_4'] === '1' : '','','id="q_15_4" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>eBank <?php echo tep_draw_checkbox_field('q_15_5', '1', isset($oq['q_15_5']) ? $oq['q_15_5'] === '1' : '','','id="q_15_5" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>ゆうちょ</td>
  </tr>
  <tr>
    <td class="main"><?php echo tep_draw_checkbox_field('q_15_8', '1', isset($oq['q_15_8']) ? $oq['q_15_8'] === '1' : '','','id="q_15_8" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>済 <!-- 入金予定日 <?php echo tep_draw_input_field('q_15_6_m', $oq['q_15_6'] && $oq['q_15_6'] != '0000-00-00' ? date('m', strtotime($oq['q_15_6'])) : ($end_time?date('m', strtotime($end_time)):''), 'size="2" id="q_15_6_m"');?>月<?php echo tep_draw_input_field('q_15_6_d', $oq['q_15_6'] && $oq['q_15_6'] != '0000-00-00' ? date('d', strtotime($oq['q_15_6'])) : ($end_time?date('d', strtotime($end_time)):''), 'size="2" id="q_15_6_d"');?>日 -->受付番号<?php echo tep_draw_input_field('q_15_7', $oq['q_15_7'], 'size="20" id="q_15_7" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?> </td>
  </tr>
</table>
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#td_q_15 input[type=checkbox]').attr('checked','');$('#q_15_7').val('');clean_option(15,'<?php echo $order->info['orders_id'];?>');"></td>
<?php } ?>
  </tr>
    
  <tr>
    <td class="main">支払完了メール送信：</td>
    <td class="main"><?php echo tep_draw_hidden_field('q_16_1', $oq['q_16_1'] ? $oq['q_16_1'] : $order->customer['email_address'], 'size="20" class="readonly" readonly');?><?php echo tep_draw_checkbox_field('q_16_2', '1', isset($oq['q_16_2']) ? $oq['q_16_2'] === '1' : $end_email,'','id="q_16_2" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>済</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_16_2').attr('checked', '');clean_option(16,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
<?php
  } else if ($orders_questions_type == 2) {
    //信用卡
    $pay_time = tep_get_orders_status_history_time($order->info['orders_id'], 9);
    $pay_email = tep_get_orders_status_history_notified($order->info['orders_id'], 9);
    
    $end_time = tep_get_orders_status_history_time($order->info['orders_id'], 2);
    $end_email = tep_get_orders_status_history_notified($order->info['orders_id'], 2);
?>
  <tr>
    <td class="main">決算確認：</td>
    <td class="main"><?php echo tep_draw_checkbox_field('q_9_1', '1', $oq['q_9_1'], '', 'id="q_9_1" onclick="if(this.checked){$(\'#q_9_2_m\').val(new Date().getMonth()+1);$(\'#q_9_2_d\').val(new Date().getDate());change_option(this);}else{$(\'#q_9_2_m\').val(\'\');$(\'#q_9_2_d\').val(\'\');change_option(this);}"');?><?php echo tep_draw_input_field('q_9_2_m', $oq['q_9_2'] && $oq['q_9_2'] != '0000-00-00' ? date('m', strtotime($oq['q_9_2'])) : '', 'size="2" id="q_9_2_m" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_9_2_d', $oq['q_9_2'] && $oq['q_9_2'] != '0000-00-00' ? date('d', strtotime($oq['q_9_2'])) : '', 'size="2" id="q_9_2_d" class="questions_date" readonly');?>日</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_9_1').attr('checked','');$('#q_9_2_m').val('');$('#q_9_2_d').val('');clean_option(9,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">在庫確認：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_10_1', '1', $oq['q_10_1'] === '1', '', 'id="q_10_1_1" onclick="exclude(this,\'q_10_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>有｜<?php echo tep_draw_radio_field('q_10_1', '0', $oq['q_10_1'] === '0', '', 'id="q_10_1_0" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無 → <?php echo tep_draw_checkbox_field('q_10_2', '1', $oq['q_10_2'] === '1', '', 'id="q_10_2" onclick="auto_radio(this,\'q_10_1_0\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>入手困難ならお客様へ電話
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_10_1_0').attr('checked','');$('#q_10_1_1').attr('checked','');$('#q_10_2').attr('checked','');clean_option(10,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main" valign="top">信用調査：</td>
    <td class="main" id="td_q_11">
      <table>
        <tr>
          <td class="main">
            金額は一致していますか？<?php echo tep_draw_radio_field('q_11_15', '1', $oq['q_11_15'] === '1', '', 'id="q_11_15_1" onclick="exclude(this,\'q_11_16\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>Y｜<?php echo tep_draw_radio_field('q_11_15', '0', $oq['q_11_15'] === '0', '', 'id="q_11_15_0" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>N → <?php echo tep_draw_checkbox_field('q_11_16', '1', $oq['q_11_16'] === '1', '', 'id="q_11_16" onclick="auto_radio(this,\'q_11_15_0\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>お客様へ電話
          </td>
        </tr>
        <tr>
          <td class="main"><?php echo tep_draw_radio_field('q_11_3', '0', $oq['q_11_3'] === '0', '', 'id="q_11_3_0" onclick="$(\'#td_q_11_first\').hide();$(\'#td_q_11_second\').show();" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>２回目以降</td>
        </tr>
        <tr>
          <td class="main" id='td_q_11_second'<?php if ($oq['q_11_3'] === '1' or !isset($oq['q_11_3'])) echo ' style="display:none"';?>>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_4', '1', $oq['q_11_4'] === '1','','onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>常連（以下のチェック必要無）<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_5', '1', $oq['q_11_5'] === '1','','onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?> 1.過去に本人確認をしている<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_6', '1', $oq['q_11_6'] === '1','','onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?> 2.決算内容に変更がない<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_7', '1', $oq['q_11_7'] === '1','','onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?> 3.短期間に高額決算がない
          </td>
        </tr>
        <tr>
          <td class="main"><?php echo tep_draw_radio_field('q_11_3', '1', $oq['q_11_3'] === '1', '', 'id="q_11_3_1" onclick="$(\'#td_q_11_second\').hide();$(\'#td_q_11_first\').show();" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>初回<td>
        </tr>
        <tr>
          <td class="main" id='td_q_11_first' <?php if ($oq['q_11_3'] === '0'or !isset($oq['q_11_3'])) echo ' style="display:none"';?>>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_8', '1', $oq['q_11_8'] === '1', '', 'onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>IP・ホストのチェック<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_11', '1', $oq['q_11_11'] === '1', '', 'onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>カード名義・商品名・キャラ名一致<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_12', '1', $oq['q_11_12'] === '1', '', 'onclick="if(this.checked){$(\'#q_11_13_m\').val(new Date().getMonth()+1);$(\'#q_11_13_d\').val(new Date().getDate());}else{$(\'#q_11_13_m\').val(\'\');$(\'#q_11_13_d\').val(\'\');}" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>本人確認日：<?php echo tep_draw_input_field('q_11_13_m', $oq['q_11_13'] && $oq['q_11_13'] != '0000-00-00' ? date('m', strtotime($oq['q_11_13'])) : '', 'size="2" id="q_11_13_m"');?>月<?php echo tep_draw_input_field('q_11_13_d', $oq['q_11_13'] && $oq['q_11_13'] != '0000-00-00' ? date('d', strtotime($oq['q_11_13'])) : '', 'size="2" id="q_11_13_d"');?>日<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_checkbox_field('q_11_14', '1', $oq['q_11_14'] === '1', '', 'onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>信用調査入力
          </td>
        </tr>
        <tr>
          <td class="main"> </td>
        </tr>
      </table>
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_11_15').val('');$('#td_q_11 input').attr('checked','');$('#td_q_11_first').hide();$('#td_q_11_second').hide();clean_option(11,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">信用判定：</td>
    <td class="main"><?php echo tep_draw_radio_field('q_17_2', '0', $oq['q_17_2'] === '0', '', 'id="q_17_2_0" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>正常 ｜ <?php echo tep_draw_radio_field('q_17_2', '1', $oq['q_17_2'] === '1', '', 'id="q_17_2_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>異常 → 担当者<?php echo tep_draw_input_field('q_17_1', $oq['q_17_1'], 'size="10" id="q_17_1"');?>の承諾を得た</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_17_2_0').attr('checked','');$('#q_17_2_1').attr('checked','');$('#q_17_1').val('');clean_option(17,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">入金確認メール送信：</td>
    <td class="main"><?php echo tep_draw_hidden_field('q_4_1', $oq['q_4_1'] ? $oq['q_4_1'] : $order->customer['email_address'], 'size="20" class="questions_date" readonly');?><?php echo tep_draw_checkbox_field('q_4_2', '1', isset($oq['q_4_2'])?$oq['q_4_2'] === '1':$pay_email,'','id="q_4_2" onclick="q_4_3();change_option(this);"');?>済
    
    <?php echo tep_draw_input_field('q_4_3_m', $oq['q_4_3'] == '0000-00-00' ? '' : ($oq['q_4_3'] ? date('m', strtotime($oq['q_4_3'])) : ($pay_time?date('m', strtotime($pay_time)):'')), 'id="q_4_3_m" size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_4_3_d', $oq['q_4_3'] == '0000-00-00' ? '' : ($oq['q_4_3'] ? date('d', strtotime($oq['q_4_3'])) : ($pay_time?date('d', strtotime($pay_time)):'')), 'id="q_4_3_d" size="2" class="questions_date" readonly');?>日
    
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_4_2').attr('checked','');$('#q_4_3_m').val('');$('#q_4_3_d').val('');clean_option(4,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">発送：</td>
    <td class="main"><?php echo tep_draw_checkbox_field('q_5_1', '1', isset($oq['q_5_1']) ? $oq['q_5_1'] === '1' : $end_time, '', 'id="q_5_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?><?php echo tep_draw_input_field('q_5_2_m', $oq['q_5_2'] && $oq['q_5_2'] != '0000-00-00' ? date('m', strtotime($oq['q_5_2'])) : ($end_time?date('m', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_5_2_d', $oq['q_5_2'] && $oq['q_5_2'] != '0000-00-00' ? date('d', strtotime($oq['q_5_2'])) : ($end_time?date('d', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>日</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_5_1').attr('checked','');clean_option(5,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">発送完了メール送信：</td>
    <td class="main"><?php echo tep_draw_hidden_field('q_7_1', $oq['q_7_1'] ? $oq['q_7_1'] : $order->customer['email_address'], 'size="20" class="readonly" class="questions_date" readonly');?><?php echo tep_draw_checkbox_field('q_7_2', '1', isset($oq['q_7_2']) ? $oq['q_7_2'] === '1' : $end_time, '', 'id="q_7_2" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>済</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_7_2').attr('checked','');clean_option(7,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">残量入力→誤差有無：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_6_1', '0', $oq['q_6_1'] === '0', '', 'id="q_6_1_0" onclick="exclude(this,\'q_6_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無｜<?php echo tep_draw_radio_field('q_6_1', '1', $oq['q_6_1'] === '1', '', 'id="q_6_1_1"');?>有 → <?php echo tep_draw_checkbox_field('q_6_2', '1', $oq['q_6_2'] === '1', '', 'id="q_6_2" onclick="auto_radio(this,\'q_6_1_1\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>報告
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_6_1_0').attr('checked','');$('#q_6_1_1').attr('checked','');$('#q_6_2').attr('checked','');clean_option(6,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
<?php
  } else {
    //販売
    $pay_time = tep_get_orders_status_history_time($order->info['orders_id'], 9);
    $pay_email = tep_get_orders_status_history_notified($order->info['orders_id'], 9);
    
    $end_time = tep_get_orders_status_history_time($order->info['orders_id'], 2);
    $end_email = tep_get_orders_status_history_notified($order->info['orders_id'], 2);
    
    $new_price =
      tep_get_ot_total_num_by_text(tep_get_ot_total_by_orders_id($order->info['orders_id'],true));
?>
  <tr>
    <td class="main">在庫確認：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_2_1', '1', $oq['q_2_1'] === '1', '', 'id="q_2_1_1" onclick="exclude(this,\'q_2_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>有｜<?php echo tep_draw_radio_field('q_2_1', '0', $oq['q_2_1'] === '0', '', 'id="q_2_1_0" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無 → <?php echo tep_draw_checkbox_field('q_2_2', '1', $oq['q_2_2'] === '1', '', 'id="q_2_2" onclick="auto_radio(this,\'q_2_1_0\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>入金確認後仕入
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_2_1_0').attr('checked','');$('#q_2_1_1').attr('checked','');$('#q_2_2').attr('checked','');clean_option(2,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">入金確認：</td>
    <td class="main">
    <?php echo tep_draw_checkbox_field('q_3_1', '1', $oq['q_3_1'], '', 'id="q_3_1" onclick="q_3_2();change_option(this)"');?><?php echo tep_draw_input_field('q_3_2_m', $oq['q_3_2'] == '0000-00-00' ? '' : ($oq['q_3_2'] ? date('m', strtotime($oq['q_3_2'])) : ''), 'id="q_3_2_m" size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_3_2_d', $oq['q_3_2'] == '0000-00-00' ? '' :( $oq['q_3_2'] ? date('d', strtotime($oq['q_3_2'])) : ''), 'id="q_3_2_d" size="2" class="questions_date" readonly');?>日 → 金額は<b>
    <?php echo strip_tags($new_price);?>
    <?php 
    echo tep_draw_hidden_field('q_3_3', strip_tags($oq['q_3_3']?($oq['q_3_3'] != $new_price ? $new_price : $oq['q_3_3']):$new_price), 'size="10" class="questions_date" readonly style="text-align:right;font-weight:bold;font-size:12px"');
?>
  </b>円ですか？→<?php echo tep_draw_checkbox_field('q_3_4', '1', $oq['q_3_4'], '','id="q_3_4" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>はい</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_3_1').attr('checked','');$('#q_3_2_m').val('');$('#q_3_2_d').val('');$('#q_3_4').attr('checked','');clean_option(3,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">入金確認メール送信：</td>
    <td class="main"><?php echo tep_draw_hidden_field('q_4_1', $oq['q_4_1'] ? $oq['q_4_1'] : $order->customer['email_address'], 'size="20" class="questions_date" readonly');?><?php echo tep_draw_checkbox_field('q_4_2', '1', isset($oq['q_4_2'])?$oq['q_4_2'] === '1':$pay_email, '', 'id="q_4_2" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>済</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_4_2').attr('checked','');clean_option(4,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">発送：</td>
    <td class="main"><?php echo tep_draw_checkbox_field('q_5_1', '1', isset($oq['q_5_1']) ? $oq['q_5_1'] === '1' : $end_time, '', 'id="q_5_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?><?php echo tep_draw_input_field('q_5_2_m', $oq['q_5_2'] && $oq['q_5_2'] != '0000-00-00' ? date('m', strtotime($oq['q_5_2'])) : ($end_time?date('m', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>月<?php echo tep_draw_input_field('q_5_2_d', $oq['q_5_2'] && $oq['q_5_2'] != '0000-00-00' ? date('d', strtotime($oq['q_5_2'])) : ($end_time?date('d', strtotime($end_time)):''), 'size="2" class="questions_date" readonly');?>日</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_5_1').attr('checked','');clean_option(5,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">発送完了メール送信：</td>
    <td class="main"><?php echo tep_draw_hidden_field('q_7_1', $oq['q_7_1'] ? $oq['q_7_1'] : $order->customer['email_address'], 'size="20" class="questions_date" readonly');?><?php echo tep_draw_checkbox_field('q_7_2', '1', isset($oq['q_7_2']) ? $oq['q_7_2'] === '1' : $end_email, '', 'id="q_7_2" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>済</td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_7_2').attr('checked','');clean_option(7,'<?php echo $order->info['orders_id'];?>')"></td>
<?php } ?>
  </tr>
  <tr>
    <td class="main">残量入力→誤差有無：</td>
    <td class="main">
      <?php echo tep_draw_radio_field('q_6_1', '0', $oq['q_6_1'] === '0', '', 'id="q_6_1_0" onclick="exclude(this,\'q_6_2\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>無｜<?php echo tep_draw_radio_field('q_6_1', '1', $oq['q_6_1'] === '1', '', 'id="q_6_1_1" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>有 → <?php echo tep_draw_checkbox_field('q_6_2', '1', $oq['q_6_2'] === '1', '', 'id="q_6_2" onclick="auto_radio(this,\'q_6_1_1\')" onchange="change_option(this)" onpropertychange="propertychange_option(this)"');?>報告
    </td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right">
    <!--<button type="button" onclick="$('#q_6_1_0').attr('checked','');$('#q_6_1_1').attr('checked','');$('#q_6_2').attr('checked','');">X</button>-->
    <img onclick="$('#q_6_1_0').attr('checked','');$('#q_6_1_1').attr('checked','');$('#q_6_2').attr('checked','');clean_option(6,'<?php echo $order->info['orders_id'];?>')" src="images/icons/icon_cancel.gif"> 
    </td>
<?php } ?>
  </tr>
<?php
  }
?>
  <tr id="tr_q_8_1"<?php if (!tep_orders_finished($order->info['orders_id']) || $order->tori['date'] === '0000-00-00 00:00:00') {?> style="display:none;"<?php } ?>>
    <td class="main">最終確認：</td>
    <td class="main">確認者名<?php echo tep_draw_input_field('q_8_1', $oq['q_8_1'], 'size="10" id="q_8_1"');?></td>
<?php if (!$oq['q_8_1']) { ?>
    <td class="main" align="right"><img src="images/icons/icon_cancel.gif" onclick="$('#q_8_1').val('');clean_option(8,'<?php echo $order->info['orders_id'];?>')"></td>
<?php }?>
  </tr>
</table>
<div align="right" id="orders_questions_submit_div" style="display:none;">
  <?php if (tep_orders_finished($order->info['orders_id'])) { ?>
  <input  type="submit" id="orders_questions_submit" value="取引完了"<?php if ($oq['q_8_1']) {?> disabled="true"<?php } ?>>
  <?php } else { ?>
  <input  type="submit" id="orders_questions_submit" value="保存"<?php if ($oq['q_8_1']) {?> disabled="true"<?php } ?>>
  <?php }?>
</div>
                </form>     
                </div>      
 <!--new order answer{{-->
                          <?php
  require_once('oa/HM_Form.php'); 
  require_once('oa/HM_Group.php'); 
  $order_id = $order->info['orders_id'];
  $formtype = tep_check_order_type($order_id);
  $formtype = '2';
  $payment_romaji = 'buying'; 
//  $payment_romaji = tep_get_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  
  $form->loadOrderValue($order_id);
  $form->setAction('oa_answer_process.php?oID='.$order_id);
  $form->render();
        ?>
    </td>
      </tr>
                </table>
              </div>
            </div>
            <!-- /right -->
        </td>
      </tr>
      <!-- 信用调查 -->
      <tr>
        <td>
          <div id="orders_credit">
            <h3>信用調査</h3>
            <form action="ajax_orders.php?orders_id=<?php echo $order->info['orders_id'];?>" id='form_orders_credit' method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <!--<td class="main" valign="top" width="30%"><b>信用調査:</b></td>-->
                <td class="main"><input type="text" name="orders_credit" style="width:100%" value="<?php echo tep_get_customers_fax_by_id($order->customer['id']);?>" ></td>
                <td class="main" width="30"><input type="submit" value="保存"></td>
              </tr>
            </table>
            </form>
          </div>
        </td>
      </tr>
      <!-- 订单商品 -->
      <tr>
        <td>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CHARACTER; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
        <!--<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>-->
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <!--<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>-->
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
  <?php
      for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        echo '    <tr class="dataTableRow">' . "\n" . 
       '      <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty']. tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id'], $order->products[$i]['rate']) . '&nbsp;x</td>' . "\n" .
       '      <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

        if (isset($order->products[$i]['attributes']) && $order->products[$i]['attributes'] && ($k = sizeof($order->products[$i]['attributes'])) > 0) {
          for ($j = 0; $j < $k; $j++) {
            echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
            if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')<br>';
              echo '</i></small></nobr>';
          }
        }

      if ( DISPLAY_PRICE_WITH_TAX == 'true' ) {
        $price_with_tax = $currencies->format(
        tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']),
        true,
        $order->info['currency'], $order->info['currency_value']);
        $tprice_with_tax = $currencies->format(
        tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'],
        true,
        $order->info['currency'],
        $order->info['currency_value']);
      } else {
        $price_with_tax = $tprice_with_tax = '---';
      }

        echo '      </td>' . "\n" .
       '      <td class="dataTableContent" valign="top" style="font-size:20px">' . htmlspecialchars($order->products[$i]['character']) . '</td>' . "\n" .
       '      <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
       '      <!--<td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>-->' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top"><b>' . $price_with_tax . '</b></td>' . "\n" .
       '      <!--<td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'],true,$order->info['currency'],$order->info['currency_value']) . '</b></td>-->' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top"><b>' . $tprice_with_tax . '</b></td>' . "\n";
        echo '    </tr>' . "\n";
      }
  ?>
      <tr>
        <td align="right" colspan="9">
          <table border="0" cellspacing="0" cellpadding="2">
  <?php
      for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
        echo 
       '    <tr>' . "\n" .
       '      <td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
       '      <td align="right" class="smallText">';
        // add font color for '-' value
        /*
        echo
          $currencies->format_total($order->totals[$i]['value'],true,$order->info['currency'],$order->info['currency_value']);
        */
        if($order->totals[$i]['value']>=0){
          echo $currencies->format($order->totals[$i]['value']);
        }else{
          if($order->totals[$i]['class'] == 'ot_total'){
          echo "<b><font color='red'>";
          echo $currencies->format($order->totals[$i]['value']);
          echo "</font></b>";
          }else{
          echo "<font color='red'>";
          echo $currencies->format($order->totals[$i]['value']);
          echo "</font>";
          }
        }
        echo '</td>' . "\n" .
       '    </tr>' . "\n";
       if ($i == 0) {
          echo 
         '    <tr>' . "\n" .
         '      <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
         '      <td align="right" class="smallText">' . $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
         '    </tr>' . "\n";
       }
      }
  ?>
          <tr>
            <td align="right" class="smallText">試験運用中<font color="red">（上記の数値と一致しているか確認するように）</font>買取コピペ用:</td>
  <?php
    $warning_sell = '';
    $warning_sell =
    tep_get_ot_total_num_by_text(abs($order->totals[sizeof($order->totals)-1]['value']));
  ?>
            <td align="right" class="smallText"><?php echo $warning_sell; ?></td>
          </tr>
  <?php
    if ( $warning_sell < 5000 ) {
      echo '<tr><td align="right" colspan="2" class="smallText"><font color="blue">この注文は5,000円未満です。買取なら手数料168円引く</font></td></tr>';
    }
  ?>
          </table>
        </td>
      </tr>
    </table>
        </td>
      </tr>
    <!-- /订单商品 -->
    <!-- orders status history -->
      <tr>
        <td class="main">
<table width="100%"><tr><td width="50%">
    <table border="1" cellspacing="0" cellpadding="5" width="100%">
      <tr>
        <td class="smallText" align="center" width="10%"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>
        <td class="smallText" align="center" width="10%"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>
        <td class="smallText" align="center" width="10%"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>
        <td class="smallText" align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
  <?php
      $orders_history_query = tep_db_query("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
      if (tep_db_num_rows($orders_history_query)) {
        while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          $select_select = $orders_history['orders_status_id'];
          echo 
             '    <tr>' . "\n" .
             '      <td class="smallText" align="center" width="10%">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
             '      <td class="smallText" align="center" width="10%">';
          if ($orders_history['customer_notified'] == '1') {
            echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
          } else {
            echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
          }
          echo 
           '      <td class="smallText" width="10%">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n" .
           '      <td class="smallText">' . nl2br(tep_db_output($orders_history['comments'])) . '<p style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;width:380px;">&nbsp;</p></td>' . "\n" .
           
           '    </tr>' . "\n";
          }
      } else {
        echo
           '    <tr>' . "\n" .
           '      <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
           '    </tr>' . "\n";
      }
  ?>
    </table>
</td><td width="50%"></td>
    </tr></table>
        </td>
      </tr>
      <!-- /orders status history -->
      <!-- mail -->
  
<table border="0" width="100%">
  <tr>
    <td width="50%">
      <?php echo tep_draw_form('sele_act', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'); ?>
      <table width="100%" border="0">
      <tr>
        <td class="main"><b><?php echo ENTRY_STATUS; ?></b>
        
          <?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_select,  'onChange="mail_text(\'s_status\',\'comments\',\'title\')"'); ?>
        </td>
      </tr>
      <?php
        
        $ma_se = "select * from ".TABLE_ORDERS_MAIL." where ";
        if(!isset($_GET['status']) || $_GET['status'] == ""){
          $ma_se .= " orders_status_id = '".$order->info['orders_status']."' ";
          //echo '<input type="hidden" name="status" value="' .$order->info['orders_status'].'">';
          
          // 用来判断是否选中 送信&通知，如果nomail==1则不选中
          $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'"));
        }else{
          $ma_se .= " orders_status_id = '".$_GET['status']."' ";
          //echo '<input type="hidden" name="status" value="' .$_GET['status'].'">';
          
          // 用来判断是否选中 送信&通知，如果nomail==1则不选中
          $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
        }
        $ma_se .= "and site_id='0'";
        $mail_sele = tep_db_query($ma_se);
        $mail_sql  = tep_db_fetch_array($mail_sele);
        $sta       = isset($_GET['status'])?$_GET['status']:'';
      ?>
      <tr>
        <td class="main"><b><?php echo ENTRY_EMAIL_TITLE; ?></b><?php echo tep_draw_input_field('title', $mail_sql['orders_status_title']); ?></td>
      </tr>
      <tr>
        <td class="main">
        <b><?php echo TABLE_HEADING_COMMENTS; ?>:</b>自動的に改行して表示し、送信されるメールにも改行が入ります。
        <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>ただ今よりログインいたします。</td></tr></table>
        </td>
      </tr>
      <tr>
        <td class="main">
          <textarea style="font-family:monospace;font-size:12px;width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']); ?></textarea>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><?php echo tep_draw_checkbox_field('notify', '', true && $ma_s['nomail'] != '1', '', 'id="notify"'); ?><b>メール送信</b></td>
              <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', true && $ma_s['nomail'] != '1', '', 'id="notify_comments"'); ?><b>ステータス通知</b></td>
            </tr>
            <tr>
              <td class="main" colspan="2">
              <?php echo tep_draw_hidden_field('qu_type', $orders_questions_type);?> 
              <br><b style="color:#FF0000;">間違い探しはしましたか？</b><br><br><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      </form>
      </table>
      <!-- /mail -->
    </td>
    <td width="50%" align="left" valign="top">
<table width="100%">
  <tr><td width="30%">
    <?php $computers = tep_get_computers();
          $o2c       = tep_get_computers_by_orders_id($order->info['orders_id']);
          if ($computers) {?>
      <table>
        <tr>
          <td class="main"><b>PC</b></td>
        </tr>
        <?php foreach ($computers as $computer) { ?>
        <tr>
          <td onclick="orders_computers(this, <?php echo $computer['computers_id'];?>, '<?php echo $order->info['orders_id'];?>')" class="<?php echo in_array($computer['computers_id'], $o2c) ? 'orders_computer_checked' : 'orders_computer_unchecked' ;?>" style="font-size:20px;padding:5px 10px;"><?php echo $computer['computers_name'];?></td>
        </tr>
        <?php } ?>
      </table>
    <?php } ?>
  </td>
  </tr>
</table>
    </td>
  </tr>
</table>

      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
    </table>
  </td>
</tr>


<?php
  // edit over
  } else {
  // list start
?>
    <tr>
      <td width="100%">
  
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="smallText" valign='top'>
              <?php echo tep_draw_form('orders1', FILENAME_ORDERS, '', 'get','id="orders1" onsubmit="return false"'); ?>検索 : 
              <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <select name="search_type" onChange='search_type_changed(this)'>
                <option value="none">--選択してください--</option>
                <option value="customers_name">名前</option>
                <option value="email">メールアドレス</option>
                <option value="products_name">商品名</option>
              </select>
              </form>
            </td>
            <td valign="top"></td>
          </tr>
        </table>
      </td>
      <td align="right">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="smallText" align="right">
              <?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
              <?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit'); ?>
              </form>
            </td>
          </tr>
          <tr>
            <td class="smallText" align="right">
              <?php echo tep_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
              <?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $all_orders_statuses), '', 'onChange="this.form.submit();"'); ?>
              </form>
            </td>
          </tr>      
        </table>
      </td>
    </tr>
  </table>

      </td>
    </tr>
<?php
  if ($ocertify->npermission == 15) {
?>
    <tr>
      <td>
    <!--ORDER EXPORT SCRIPT //-->
    <form action="<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL') ; ?>" method="post">
    <fieldset><legend class="smallText"><b>注文データダウンロード</b></legend>
    <span class="smallText">ダウンロード中はサーバに対して高負荷となります。アクセスの少ない時間に実行してください。</span>
    <table  border="0" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <td class="smallText" width='150'>
      注文書サイト<?php //echo ENTRY_SITE;?>:
      <?php echo tep_site_pull_down_menu_with_all(isset($_GET['site_id']) ? $_GET['site_id'] :'', false);?>
      </td>
      <td class="smallText">
      開始日:
      <select name="s_y">
      <?php for($i=2002; $i<=date('Y'); $i++) { if($i == date('Y')){ echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; }else{ echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;} } ?>
      </select>
      年
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { if($i == date('m')-1){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      月
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
      <td width="80" align="center">～</td>
      <td class="smallText">終了日
      <select name="e_y">
      <?php
      for($i=2002; $i<=date('Y'); $i++) {
        if($i == date('Y')){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if($i == date('m')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
       <td class="smallText"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $all_orders_statuses), '', ''); ?></td>
      <td>&nbsp;</td>
    <td><input type="image" src="includes/languages/japanese/images/buttons/button_csv_exe.gif" alt="CSVエクスポート" width="105" height="22" border="0"></td>
      </tr>
    </table></fieldset>
    </form>
    <!--ORDER EXPORT SCRIPT EOF //-->
    </td>
      </tr>
<?php
  }
?>
    <tr>
      <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">
<?php
  if ($ocertify->npermission == 15) {
    if(!tep_session_is_registered('reload')) $reload = 'yes';
    if (false) { 
    if($reload == 'yes') {
?>
      <table border="0" width="100%" cellspacing="1" cellpadding="2" style="background: #FF8E90;" height="30"> 
        <tr style="background: #FFE6E6; font-size: 10px; "> 
          <td><strong><font color="#FF0000"> 【注意】 </font></strong>現在自動リロード機能が有効になっています　→ 【<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'reload')) . 'reload=No'); ?>"><b>無効にする</b></a>】&nbsp;&nbsp;|&nbsp;&nbsp;一覧に表示する<a href="orders_status.php"><b>注文ステータス設定</b></a></td>
        </tr>
      </table>
<?php
    } else {
?>
      <table border="0" width="100%" cellspacing="1" cellpadding="2" style="background: #FF8E90;" height="30"> 
        <tr style="background: #FFE6E6; font-size: 10px; "> 
          <td><strong><font color="#FF0000"> 【注意】 </font></strong>現在自動リロード機能が無効になっています　→ 【<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'reload')) . 'reload=Yes'); ?>"><b>有効にする</b></a>】&nbsp;&nbsp;|&nbsp;&nbsp;一覧に表示する<a href="orders_status.php"><b>注文ステータス設定</b></a></td>
        </tr>
      </table>
<?php
    }
    }
  }
?>
    <?php echo tep_draw_form('sele_act', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'action=sele_act'); ?>
    <table width="100%">
      <tr>
        <td>
    <?php tep_site_filter(FILENAME_ORDERS);?>
        </td>
        <td align="right">
          <div id="order_icons">
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'sell') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=sell', 'SSL');?>" title="売"><img src="images/icons/mai4.gif" alt="売" title="売"> </a>
          </span>
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'buy') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=buy','SSL');?>" title="買"><img src="images/icons/mai3.gif" alt="買" title="買"> </a>
          </span>
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'mix') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=mix','SSL');?>" title="混"><img src="images/icons/kon.gif" alt="混" title="混"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'moneyorder') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=moneyorder','SSL');?>" title="銀行振込"><img src="images/icons/gi.gif" alt="銀行振込" title="銀行振込"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'postalmoneyorder') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=postalmoneyorder','SSL');?>" title="ゆうちょ銀行（郵便局）"><img src="images/icons/yu.gif" alt="ゆうちょ銀行（郵便局）" title="ゆうちょ銀行（郵便局）"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'telecom') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=telecom','SSL');?>" title="クレジットカード決済"><img src="images/icons/ku.gif" alt="クレジットカード決済" title="クレジットカード決済"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'convenience_store') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=convenience_store','SSL');?>" title="コンビニ決済"><img src="images/icons/ko.gif" alt="コンビニ決済" title="コンビニ決済"> </a>
          </span>
          </div>
        </td>
      </tr>
    </table>
  
    <table border="0" width="100%" cellspacing="0" cellpadding="2" id='orders_list_table'>
    <tr class="dataTableHeadingRow">
<?php 
  if ($ocertify->npermission) {
?>
      <td class="dataTableHeadingContent"><input type="checkbox" name="all_chk" onClick="all_check()"></td>
<?php 
  }
?>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
      <td class="dataTableHeadingContent" align="center">取引日</td>
      <td class="dataTableHeadingContent">&nbsp;</td>
      <td class="dataTableHeadingContent">&nbsp;</td>
      <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
      <td class="dataTableHeadingContent" align="right"></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
<?php
    
  $where_type = '';
  if(isset($_GET['type'])){
  switch ($_GET['type']) { 
    case 'sell':
      $where_type = " and (!(o.payment_method like '%買い取り%') and h.orders_id not in (select orders_id from ".TABLE_ORDERS_STATUS_HISTORY." where comments like '金融機関名%支店名%'))"; 
      break;
    case 'buy':
      $where_type = " and (o.payment_method like '%買い取り%')"; 
      break;
    case 'mix':
      $where_type = " and (!(o.payment_method like '%買い取り') and h.comments like '金融機関名%支店名%')"; 
      break;
  }
  }
    
  $where_payment = '';
  if(isset($_GET['payment'])){
  switch ($_GET['payment']) { 
    case 'convenience_store':
      $where_payment = " and o.payment_method = 'コンビニ決済'";
      break;
    case 'telecom':
      $where_payment = " and (o.payment_method = 'クレジットカード決済' or o.payment_method = 'ペイパル決済')";
      break;
    case 'postalmoneyorder':
      $where_payment = " and o.payment_method = 'ゆうちょ銀行（郵便局）'";
      break;
    case 'moneyorder':
    case 'buying':
      //$where_payment .= " and (o.payment_method = '銀行振込' or o.payment_method = '銀行振込(買い取り)' or o.payment_method = '銀行振込（買い取り）')"; 
      $where_payment .= " and (o.payment_method = '銀行振込' or o.payment_method like '%買い取り%')"; 
      break;
  }
  }
  $from_payment = (isset($_GET['payment']) or isset($_GET['type']))?("left join " . TABLE_ORDERS_STATUS_HISTORY . " h on (o.orders_id = h.orders_id)"):'';

  if (isset($_GET['cEmail']) && $_GET['cEmail']) {
      $cEmail = tep_db_prepare_input($_GET['cEmail']);
      $orders_query_raw = "
        select distinct o.orders_id, 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_name, 
               o.customers_id, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name, 
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.orders_comment,
               o.torihiki_houhou,
               o.site_id
        from " . TABLE_ORDERS . " o " . $from_payment . "
        where o.customers_email_address = '" . tep_db_input($cEmail) . "' 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type . "
        order by torihiki_date_error DESC,o.torihiki_date DESC";
    } else if (isset($_GET['cID']) && $_GET['cID']) {
      $cID = tep_db_prepare_input($_GET['cID']);
      $orders_query_raw = "
        select distinct o.orders_id, 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_name, 
               o.customers_id, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name, 
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.site_id
        from " . TABLE_ORDERS . " o " . $from_payment . "
        where o.customers_id = '" . tep_db_input($cID) . "' 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type . "
        order by torihiki_date_error DESC,o.torihiki_date DESC";
    } elseif (isset($_GET['status']) && $_GET['status']) {
      $status = tep_db_prepare_input($_GET['status']);
      $orders_query_raw = "
        select distinct o.orders_id, 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name, 
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.torihiki_houhou,
               o.customers_email_address,
               o.orders_comment,
               o.site_id
        from " . TABLE_ORDERS . " o " . $from_payment . "
        where 
          o.orders_status = '" . tep_db_input($status) . "' 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type . "
        order by torihiki_date_error DESC,o.torihiki_date DESC";
    }  elseif (isset($_GET['keywords']) && $_GET['keywords'] && isset($_GET['search_type']) && $_GET['search_type'] == 'products_name' && !$_GET['type'] && !$payment) {
      $orders_query_raw = " select distinct op.orders_id from " . TABLE_ORDERS_PRODUCTS . " op where op.products_name like '%".$_GET['keywords']."%' " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and op.site_id = '" . intval($_GET['site_id']) . "' " : '') . " order by op.torihiki_date desc";
  } elseif (
    isset($_GET['keywords']) && $_GET['keywords']
    and ((isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name')
    or (isset($_GET['search_type']) && $_GET['search_type'] == 'email'))
  ) {
      $orders_query_raw = "
        select o.orders_id, 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name,
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.site_id
        from " . TABLE_ORDERS . " o " . $from_payment . "
        where 1=1 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type ;

    $keywords = str_replace('　', ' ', $_GET['keywords']);
    
    tep_parse_search_string($keywords, $search_keywords);
    
    if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
      $orders_query_raw .= " and ";
      for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
      switch ($search_keywords[$i]) {
      case '(':
      case ')':
      case 'and':
      case 'or':
          $orders_query_raw .= " " . tep_db_prepare_input($search_keywords[$i]) . " ";
    break;
      default:
        $keyword = tep_db_prepare_input($search_keywords[$i]);
        //$orders_query_raw .= "(";
        if (isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name') {
          $orders_query_raw .= "o.customers_name like '%" . tep_db_input($keyword) . "%' or ";
          $orders_query_raw .= "o.customers_name_f like '%" . tep_db_input($keyword) . "%'";
        } else if (isset($_GET['search_type']) && $_GET['search_type'] == 'email') {
          $orders_query_raw .= "o.customers_email_address like '%" . tep_db_input($keyword) . "%'";
        }
        //$orders_query_raw .= ")";
    break;
    }
      } 
    //$orders_query_raw .= ")";  
    }
    
    $orders_query_raw .= " order by torihiki_date_error DESC,o.torihiki_date DESC";
  } elseif (isset($_GET['keywords']) && $_GET['keywords']) {
    $orders_query_raw = "
        select distinct(o.orders_id), 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name,
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.site_id
        from " . TABLE_ORDERS . " o " . $from_payment . ", " . TABLE_ORDERS_PRODUCTS . " op 
        where o.orders_id = op.orders_id
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type ;
    $keywords = str_replace('　', ' ', $_GET['keywords']);
    tep_parse_search_string($keywords, $search_keywords);
    if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
      $orders_query_raw .= " and (";
      for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
      switch ($search_keywords[$i]) {
      case '(':
      case ')':
      case 'and':
      case 'or':
          $orders_query_raw .= " " . tep_db_prepare_input($search_keywords[$i]) . " ";
    break;
      default:
          $keyword = tep_db_prepare_input($search_keywords[$i]);
          $orders_query_raw .= "(";
          $orders_query_raw .= "o.customers_name like '%" . tep_db_input($keyword) . "%' or ";
          $orders_query_raw .= "o.customers_name_f like '%" . tep_db_input($keyword) . "%' or ";
          $orders_query_raw .= "o.customers_email_address like '%" . tep_db_input($keyword) . "%' or ";
          $orders_query_raw .= "o.customers_telephone like '%" . tep_db_input($keyword) . "%' or ";
          $orders_query_raw .= "op.products_name like '%" . tep_db_input($keyword) . "%'";
          $orders_query_raw .= ")";
    break;
    }
      } 
    $orders_query_raw .= ")";  
    }
    
    $orders_query_raw .= "order by torihiki_date_error DESC,o.torihiki_date DESC";
  } else {
      // 隐藏 「キャンセル」と「注文取消」
      $orders_query_raw = "
        select distinct o.orders_status as orders_status_id, 
               o.orders_id, 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00',1,0) as torihiki_date_error,
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name, 
               o.orders_status_image,
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.orders_comment,
               o.torihiki_houhou,
               o.site_id
         from " . TABLE_ORDERS . " o " . $from_payment . "
         where 
          (o.q_8_1 IS NULL or o.q_8_1 = '')
          -- and o.orders_status != '6'
          -- and o.orders_status != '8'
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and o.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
          " . $where_payment . $where_type . "
         order by torihiki_date_error DESC,o.torihiki_date DESC
      ";
    }

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows);
    //echo $orders_query_raw;
    $orders_query = tep_db_query($orders_query_raw);
    $allorders    = $allorders_ids = array();
    while ($orders = tep_db_fetch_array($orders_query)) {
      if (!isset($orders['site_id'])) {
        $orders = tep_db_fetch_array(tep_db_query("
          select *
          from ".TABLE_ORDERS." o
          where orders_id='".$orders['orders_id']."'
        "));
      }
      $allorders[] = $orders;
      if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
        $oInfo = new objectInfo($orders);
      }

  //今日の取引なら赤色
  $trade_array = getdate(strtotime(tep_datetime_short($orders['torihiki_date'])));
  $today_array = getdate();
  if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
    $today_color = 'red';
    if ($trade_array["hours"] >= $today_array["hours"]) {
      $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif', '次の注文'); //次の注文に目印をつける
    } else {
      $next_mark = '';
    }
  } else {
    #if ($ocertify->npermission) {
      $today_color = 'black';
    #} else {
      #$today_color = '#999';
    #}
    $next_mark = '';
  }
  
  if ( (isset($oInfo) && is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) {
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRowSelected" onmouseover="showOrdersInfo(\''.tep_get_orders_products_string($orders).'\',this);this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo()" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
  } else {
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="showOrdersInfo(\''.tep_get_orders_products_string($orders).'\',this);this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo();this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
  }
?>
  <?php 
  if ($ocertify->npermission) {
    ?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent">
          <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this);show_questions(this);">
        </td>
<?php 
  }
?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo tep_get_site_romaji_by_id($orders['site_id']);?></td>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
          <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <a href="<?php echo tep_href_link('orders.php', 'cEmail=' . tep_output_string_protected($orders['customers_email_address']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', '過去の注文');?></a>
<?php if ($ocertify->npermission) {?>
          &nbsp;<a href="<?php echo tep_href_link('customers.php', 'page=1&cID=' . tep_output_string_protected($orders['customers_id']) . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'arrow_r_red.gif', '顧客情報');?></a>&nbsp;&nbsp;
<?php }?> 
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  <font color="#999">
  <?php } else { ?>
  <font color="#000">
  <?php } ?>
          <b><?php echo tep_output_string_protected($orders['customers_name']);?></b>
          <input type="hidden" id="cid_<?php echo $orders['orders_id'];?>" name="cid[]" value="<?php echo $orders['customers_id'];?>" />
  </font>
  <?php if (tep_is_oroshi($orders['customers_id'])) { ?>
  <?php echo tep_image(DIR_WS_ICONS . 'oroshi.gif', '卸業者');?>
  <?php }?>
  <?php if ($orders['orders_care_flag']) { ?>
  <?php echo tep_image(DIR_WS_ICONS . 'care.gif', '取り扱い注意');?>
  <?php }?>


    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
      <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
      <font color="#999"><?php echo
        strip_tags(tep_get_ot_total_by_orders_id($orders['orders_id'],true));?></font>
      <?php } else { ?>
      <?php echo strip_tags(tep_get_ot_total_by_orders_id($orders['orders_id'], true));?>
      <?php }?>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $next_mark; ?><font color="<?php echo !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>" id="tori_<?php echo $orders['orders_id']; ?>"><?php echo tep_datetime_short($orders['torihiki_date']); ?></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES . 'icon_hand.gif', '取引待ち'); } else { echo '&nbsp;'; } ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
    <?php 
    // ===============================================================
    // 订单历史记录的图标
      $___orders_status_query = tep_db_query("select orders_status_id from `".TABLE_ORDERS_STATUS_HISTORY."` WHERE `orders_id`='".$orders['orders_id']."' order by `date_added` asc");
      $___orders_status_ids   = array();
      while($___orders_status = tep_db_fetch_array($___orders_status_query)){
        $___orders_status_ids[] = $___orders_status['orders_status_id'];
      }
      if ($___orders_status_ids) {
      $_orders_status_history_query_raw = "select * from `".TABLE_ORDERS_STATUS."` WHERE `orders_status_id` IN (".join(',',$___orders_status_ids).")";
      $_orders_status_history_query     = tep_db_query($_orders_status_history_query_raw);     $_osh = array();
      $_osi = false;
      while ($_orders_status_history = tep_db_fetch_array($_orders_status_history_query)){
        if(!in_array($_orders_status_history['orders_status_id'], $_osh)
        && !is_dir(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image']) 
        && file_exists(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image'])){
          echo tep_image(tep_get_web_upload_dir(). 'orders_status/' . $_orders_status_history['orders_status_image'], $_orders_status_history['orders_status_image'], 15, 15, ($_orders_status_history['orders_status_id'] == @$orders['orders_status_id'])?'style="vertical-align: middle;"':'style="vertical-align: middle;"');
          $_osi = $_osi or true;
        }
          $_osh[] = $_orders_status_history['orders_status_id'];
      }
      }
      if(!$_osi){
        echo '　';
      }
    // ===============================================================
    ?>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['orders_status_name']; ?><input type="hidden" name="os[]" id="orders_status_<?php echo $orders['orders_id']; ?>" value="<?php echo $orders['orders_status']; ?>"></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php 
    if ( isset($oInfo) && (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) { 
      echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
    } else { 
      echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    } ?>&nbsp;</td>
    </tr>
<?php }?>
  </table>
<script language="javascript">
  // 游戏人物名字符串，订单列表页用来替换邮件内容
  window.orderStr = new Array();
  // 订单所属网站
  window.orderSite = new Array();
  // 0 空 1 卖 2 买 3 混
  var orderType = new Array();
  
  var questionShow = new Array();
<?php foreach($allorders as $key=>$orders){?>
  window.orderStr['<?php echo $orders['orders_id'];?>']  = "<?php echo str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'), orders_a($orders['orders_id'], $allorders));?>";
  window.orderSite['<?php echo $orders['orders_id'];?>'] = "<?php echo $orders['site_id'];?>";
  orderType['<?php echo $orders['orders_id'];?>']        = "<?php echo tep_get_order_type($orders['orders_id']);?>";
  questionShow['<?php echo $orders['orders_id'];?>']     = "<?php echo (tep_questions_can_show($orders['orders_id'])?'1':'0');?>";
<?php }?>
  
function submit_confirm()
{
  var idx = document.sele_act.elements['status'].selectedIndex;
  var CI  = document.sele_act.elements['status'].options[idx].value;
  chk = getCheckboxValue('chk[]')
  if((chk.length > 1 || chk.length < 1) && window.status_text[CI].indexOf('${ORDER_A}') != -1){
    if(chk.length > 1){
      alert('複数の選択はできません。');
    } else {
      alert('注文書はまだ選択していません。');
    }
    return false;
  }
  return true;
}
</script>
<table width="100%"><tr><td>
      <table width="100%" id="select_send" style="display:none">
        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
        <td class="main"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $select_select,  'onChange="mail_text(\'status\',\'comments\',\'os_title\')"'); ?> <?php if($ocertify->npermission > 7 ) { ?>&nbsp;<a href="<?php echo tep_href_link(FILENAME_ORDERS_STATUS,'',SSL);?>">メール本文編集</a><?php } ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_EMAIL_TITLE; ?></b></td>
        <td class="main"><?php echo tep_draw_input_field('os_title', $select_title); ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main" valign="top"><b><?php echo TABLE_HEADING_COMMENTS . ':'; ?></b></td>
        <td class="main">
          自動的に改行して表示し、送信されるメールにも改行が入ります。
          <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>ただ今よりログインいたします。</td></tr></table>
          <br>
          <?php echo tep_draw_textarea_field('comments', 'hard', '74', '30', $select_text, 'style="font-family:monospace;font-size:x-small"'); ?>
        </td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><?php echo tep_draw_checkbox_field('notify', '', !$select_nomail, '', 'id="notify"'); ?><b>メール送信</b></td>
                <td class="main" align="right"><?php echo tep_draw_checkbox_field('notify_comments', '', !$select_nomail, '', 'id="notify_comments"'); ?><b>ステータス通知</b></td>
              </tr>
              <tr>
                <td class="main" colspan="2"><br><b style="color:#FF0000;">間違い探しはしましたか？</b><br><br><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE, 'onclick="return submit_confirm()&&check_question_form();"'); ?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
</td><td valign="top" align="right">
      <table id="select_question" style="display:none">
        <tr>
          <td>銀行:</td>
          <td>
            <input type="checkbox" name="q_15_3" id="q_15_3" value="1">JNB 
            <input type="checkbox" name="q_15_4" id="q_15_4" value="1">eBank 
            <input type="checkbox" name="q_15_5" id="q_15_5" value="1">ゆうちょ 
          </td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td><input type="checkbox" name="q_15_8" id="q_15_8" value="1">済 受付番号:</td>
          <td><input type="text" name="q_15_7" id="q_15_7"></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td>確認者名:</td>
          <td><input type="text" name="q_8_1" id="q_8_1"></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <!--
        <tr>
          <td colspan="2" align="right"><input type="submit" name="submit" value="取引完了" onclick="return check_question_form()"></td>
        </tr>
        -->
      </table>
</td></tr></table>
      </form>
      <!-- display add end-->

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="5">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
            <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');

      $contents = array('form' => tep_draw_form('orders', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . tep_get_fullname(isset($cInfo->customers_firstname)?$cInfo->customers_firstname:'', isset($cInfo->customers_lastname)?$cInfo->customers_lastname:'') . '</b>');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('restock', '', true) . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']<br>' . tep_datetime_short($oInfo->date_purchased) . '</b>');

        if ($ocertify->npermission == 15) {
          $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_details.gif', IMAGE_DETAILS) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        } else {
          $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_details.gif', IMAGE_DETAILS) . '</a>');
        }
        $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
        if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
          $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
      }
      break;
  }


    echo '      <td width="20%" valign="top">' . "\n";
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    $box = new box;
    echo $box->infoBox($heading, $contents);
  }
  ?>
    <!-- 订单信息预览，配合javascript，永远浮动在屏幕右下角 -->
    <div id="orders_info_box" style="display:none;position:absolute;background:#FFF;padding-top:5px;/*bottom:0;margin-top:40px;right:0;width:200px;*/">&nbsp;</div>
  <?php
    echo '      </td>' . "\n";

?>
    </tr>
  </table>
      </td>
    </tr>
<?php } ?>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<embed id="warn_sound" src="images/warn.mp3" width="0" height="0" loop="false" autostart="false"></embed>
<!-- footer_eof //-->
<br>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
