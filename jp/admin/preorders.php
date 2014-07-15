<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require_once('pre_oa/HM_Form.php'); 
  require_once('pre_oa/HM_Group.php'); 
  require(DIR_WS_FUNCTIONS . 'visites.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  require(DIR_WS_CLASSES . 'payment.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
  //删除超时的未转正式的预约订单
  tep_preorders_to_orders_timeout();
  if (isset($_GET['keywords'])) {
    $_GET['keywords'] = tep_db_prepare_input($_GET['keywords']);
  }
  $currencies          = new currencies(2);
  $orders_statuses     = $all_orders_statuses = $orders_status_array = array();
  $all_preorders_status = array();
  $all_payment_method = payment::getPaymentList(PAYMENT_LIST_TYPE_HAIJI); 
  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_PREORDERS_STATUS . " where language_id = '" . $languages_id . "'");

  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],'text' => $orders_status['orders_status_name']);
    
    $all_orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
    $all_preorders_status[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
    
  }
   
  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'sele_act' 给选择的预约订单更新状态并发送邮件 
   case 'update_order' 更新预约订单相关信息并发送邮件 
   case 'deleteconfirm' 删除预约订单 
------------------------------------------------------*/
  case 'sele_act':
    if($_POST['chk'] == ""){
      $_SESSION['error_preorders_status'] = WARNING_ORDER_NOT_UPDATED; 
      $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action'))));
    }
    $update_user_info = tep_get_user_info($ocertify->auth_user);

      if (empty($_POST['status']) || empty($update_user_info['name'])) {
        $_SESSION['error_preorders_status'] = WARNING_LOSING_INFO_TEXT; 
        $messageStack->add_session(WARNING_LOSING_INFO_TEXT, 'warning');
        tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action'))));
      }
      foreach($_POST['chk'] as $value){
      $oID      = $value;
      $status   = tep_db_prepare_input($_POST['status']);
      $title    = tep_db_prepare_input($_POST['os_title']);
      $comments = stripslashes(tep_db_prepare_input($_POST['comments']));
      $site_id  = tep_get_pre_site_id_by_orders_id($value);
    
      $order_updated = false;
      $check_status_query = tep_db_query("select customers_name, customers_id, customers_email_address, orders_status, date_purchased, payment_method, torihiki_date, ensure_deadline, predate  from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);
      
      //Add Point System
      if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
        $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
        $pcount = tep_db_fetch_array($pcount_query);
        if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
          $query1  = tep_db_query("select customers_id from " . TABLE_PREORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
          $query2  = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
          $result2 = tep_db_fetch_array($query2);
          $query3  = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
          $result3 = tep_db_fetch_array($query3);
          $query4  = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
          $result4 = tep_db_fetch_array($query4);
      
        // 计算各个不同顾客的返点率从这开始============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //规定期间内，计算订单合计金额------------
          $ptoday = date("Y-m-d H:i:s", time());
          $pstday_array = getdate();
          $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
          $total_buyed_date = 0;
          $customer_level_total_query = tep_db_query("select * from preorders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
          if(tep_db_num_rows($customer_level_total_query)) {
            while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
              $cltotal_subtotal_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
              $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
        
              $cltotal_point_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
              $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
         
              $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
            }
          }
          //这次的订单金额除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
          //计算返点率----------------------------------
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
          $point_rate = $back_rate;
        } else {
          $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        }
        }
        }   
    
    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_PREORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      preorders_updated(tep_db_input($oID));
      preorders_wait_flag(tep_db_input($oID));

      $customer_notified = '0';
      
      if ($_POST['notify'] == 'on') {
        //发送邮件 
        $ot_query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . SENDMAIL_EDIT_ORDERS_PRICE_UNIT;
        
        $ot_sub_query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_subtotal'");
        $ot_sub_result = tep_db_fetch_array($ot_sub_query);
        $ot_sub_total = abs((int)$ot_sub_result['value']);

        $os_query = tep_db_query("select orders_status_name from " . TABLE_PREORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        $num_product = 0; 
        $num_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$oID."'"); 
        $num_product_res = tep_db_fetch_array($num_product_raw); 
        if ($num_product_res) {
          $num_product = $num_product_res['products_quantity']; 
        }
        $ensure_date_arr = explode(' ', $check_status['ensure_deadline']);
        $title = str_replace(array(
          '${USER_NAME}',
          '${USER_MAIL}',
          '${PREORDER_DATE}',
          '${PREORDER_NUMBER}',
          '${PAYMENT}',
          '${ORDER_TOTAL}',
          '${ORDER_STATUS}',
          '${SITE_NAME}',
          '${SITE_URL}',
          '${PAY_DATE}',
          '${RESERVE_DATE}',
          '${PRODUCTS_QUANTITY}',
          '${PRODUCTS_NAME}',
          '${PRODUCTS_PRICE}',
          '${SUB_TOTAL}'
        ),array(
          $check_status['customers_name'],
          $check_status['customers_email_address'],
          tep_date_long($check_status['date_purchased']),
          $oID,
          $check_status['payment_method'],
          $otm,
          $os_result['orders_status_name'],
          get_configuration_by_site_id('STORE_NAME', $site_id),
          get_url_by_site_id($site_id),
          date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
          $ensure_date_arr[0],
          $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT,
          $num_product_res['products_name'],
          $currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax']),
          $ot_sub_total
          ),$title
        );
        $comments = str_replace(array(
          '${USER_NAME}',
          '${USER_MAIL}',
          '${PREORDER_DATE}',
          '${PREORDER_NUMBER}',
          '${PAYMENT}',
          '${ORDER_TOTAL}',
          '${ORDER_STATUS}',
          '${SITE_NAME}',
          '${SITE_URL}',
          '${SUPPORT_MAIL}',
          '${PAY_DATE}',
          '${RESERVE_DATE}', 
          '${PRODUCTS_QUANTITY}',
          '${PRODUCTS_NAME}',
          '${PRODUCTS_PRICE}',
          '${SUB_TOTAL}',
          '${ORDER_COMMENT}'
        ),array(
          $check_status['customers_name'],
          $check_status['customers_email_address'],
          tep_date_long($check_status['date_purchased']),
          $oID,
          $check_status['payment_method'],
          $otm,
          $os_result['orders_status_name'],
          get_configuration_by_site_id('STORE_NAME', $site_id),
          get_url_by_site_id($site_id),
          get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
          date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
          $ensure_date_arr[0],
          $num_product,
          $num_product_res['products_name'],
          str_replace(SENDMAIL_EDIT_ORDERS_PRICE_UNIT,'',$currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax'])),
          $ot_sub_total,
          preorders_a($oID)
        ),$comments
        );
        $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_id = '".$check_status['customers_id']."'"); 
        $customer_info_res = tep_db_fetch_array($customer_info_raw); 
        if ($customer_info_res['is_send_mail'] != '1') {
          $site_url_raw = tep_db_query("select * from sites where id = '".$site_id."'"); 
          $site_url_res = tep_db_fetch_array($site_url_raw); 
          $change_preorder_url_param = md5(time().$oID); 
          $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$change_preorder_url_param; 
          $comments = str_replace('${REAL_ORDER_URL}', $change_preorder_url, $comments); 
          
          tep_db_query("update ".TABLE_PREORDERS." set check_preorder_str = '".$change_preorder_url_param."' where orders_id = '".$oID."'"); 
          
          $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$num_product_res['products_id']."' and language_id='".$languages_id."' and (site_id='".$site_id."' or site_id='0') order by site_id DESC");
          $search_products_name_array = tep_db_fetch_array($search_products_name_query);
          tep_db_free_result($search_products_name_query);
          //自定义费用列表 
          $totals_email_str = '';
          $totals_email_query = tep_db_query("select title,value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_custom'");
          $num_update_totals = 0;
	  $num_array_totals_rows = tep_db_num_rows($totals_email_query);
	  while($totals_email_result = tep_db_fetch_array($totals_email_query)){
	    $num_update_totals++;
            if($totals_email_result['title'] != '' && $totals_email_result['value'] != ''){
		$t=0;
                for($i=0;$i<mb_strlen($totals_email_result['title']);$i++){
                        $title_str = mb_substr($totals_email_result['title'],$i,1);
                        if(strlen($title_str)>1){
                                ++$t;
                        }
                }
                if((mb_strlen($totals_email_result['title'])-$t)%2 == 0){
                        if($num_array_totals_rows == $num_update_totals){
                                $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value']);
                        }else{
                                $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
                        }
                }else{
                        if($num_array_totals_rows == $num_update_totals){
                                $totals_email_str .= $totals_email_result['title'].' '.str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value']);
                        }else{
                                $totals_email_str .= $totals_email_result['title'].' '.str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
                        }
                }

             // $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -strlen($totals_email_result['title']))/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
            }
          }
          tep_db_free_result($totals_email_query);
          //自定义费用
          if($totals_email_str != ''){
			if($num_array_totals_rows >=1){
            $comments = str_replace('${CUSTOMIZED_FEE}',str_replace('▼','',$totals_email_str), $comments);
			}else{
            $comments = str_replace('${CUSTOMIZED_FEE}'."\r\n",str_replace('▼','',$totals_email_str), $comments);
			}
          }else{
            $comments = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $comments); 
            $comments = str_replace('${CUSTOMIZED_FEE}','', $comments);
		  }
          $comments = tep_replace_mail_templates($comments,$check_status['customers_email_address'],$check_status['customers_name'],$site_id);
          $comments = html_entity_decode(htmlspecialchars($comments));
          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, str_replace($num_product_res['products_name'],$search_products_name_array['products_name'],$comments), get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
          tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS',$site_id), $title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
        } 
        $customer_notified = '1';
      }
      
        
      if($_POST['notify_comments'] == 'on') {
        $customer_notified = '1';
      } else {
        $customer_notified = '0';
      }
      //获取预约订单最后一次备注信息
      $preorders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
      $preorders_status_history_array = tep_db_fetch_array($preorders_status_history_query);
      tep_db_free_result($preorders_status_history_query);
      $sql_data_array = array('last_modified' => 'now()','user_update' => $_SESSION['user_name']);
      tep_db_perform(TABLE_PREORDERS, $sql_data_array, 'update', "orders_id='".$oID."'");
      tep_db_query("insert into " . TABLE_PREORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, user_added) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '".$preorders_status_history_array['comments']."', '".tep_db_input($update_user_info['name'])."')");

      $order_updated = true;
      
    }

      if ($order_updated) {
        $messageStack->add_session(NOTICE_ORDER_ID_TEXT . $oID . NOTICE_ORDER_ID_LINK_TEXT . SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session(NOTICE_ORDER_ID_TEXT . $oID . NOTICE_ORDER_ID_LINK_TEXT . WARNING_ORDER_NOT_UPDATED, 'warning');
      }
      tep_pre_order_status_change($oID,$status); 
      }

      tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action'))));

    
    break;
  case 'update_order':
      $update_user_info = tep_get_user_info($ocertify->auth_user);
      if (empty($_POST['s_status']) || empty($update_user_info['name'])) {
        $_SESSION['error_preorders_status'] = WARNING_LOSING_INFO_TEXT; 
        $messageStack->add_session(WARNING_LOSING_INFO_TEXT, 'warning');
        tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
      }
      $oID      = tep_db_prepare_input($_GET['oID']);
      $status   = tep_db_prepare_input($_POST['s_status']);
      $title    = tep_db_prepare_input($_POST['title']);
      $comments = tep_db_prepare_input($_POST['comments']);
      $site_id  = tep_get_pre_site_id_by_orders_id($oID);
      $order_updated = false;
      $check_status_query = tep_db_query("
          select orders_id, 
                 customers_name, 
                 customers_id,
                 customers_email_address, 
                 orders_status, 
                 date_purchased, 
                 payment_method, 
                 torihiki_date,
                 ensure_deadline
          from " . TABLE_PREORDERS . " 
          where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);
      //oa start 如果状态发生改变，找到当前的订单的
        tep_pre_order_status_change($oID,$status);
      //OA_END
    //Add Point System
    if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
      $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
      $pcount = tep_db_fetch_array($pcount_query);
    if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
      $query1 = tep_db_query("select customers_id from " . TABLE_PREORDERS . " where orders_id = '".$oID."'");
      $result1 = tep_db_fetch_array($query1);
      $query2 = tep_db_query("select value from ".TABLE_PREORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
      $result2 = tep_db_fetch_array($query2);
      $query3 = tep_db_query("select value from ".TABLE_PREORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
      $result3 = tep_db_fetch_array($query3);
      $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
      $result4 = tep_db_fetch_array($query4);


      
    // 计算各个不同顾客的返点率从这开始============================================================
    if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
      $customer_id = $result1['customers_id'];
      //规定期间内，计算订单合计金额------------
      $ptoday = date("Y-m-d H:i:s", time());
      $pstday_array = getdate();
      $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
      $total_buyed_date = 0;
      $customer_level_total_query = tep_db_query("select * from preorders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
      if(tep_db_num_rows($customer_level_total_query)) {
        while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
          $cltotal_subtotal_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
          $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
        
          $cltotal_point_query = tep_db_query("select value from preorders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
          $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
         
          $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
        }
      }
      //这次的订单金额除外
      $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
      //计算返点率----------------------------------
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
      $point_rate = $back_rate;
    } else {
      $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
    }
    }
    }
    
    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_PREORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      preorders_updated(tep_db_input($oID));
      preorders_wait_flag(tep_db_input($oID));
      $customer_notified = '0';
    
    if ($_POST['notify'] == 'on') {
      //发送邮件
      $ot_query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
      $ot_result = tep_db_fetch_array($ot_query);
      $otm = (int)$ot_result['value'] . SENDMAIL_EDIT_ORDERS_PRICE_UNIT;
      
      $ot_sub_query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_subtotal'");
      $ot_sub_result = tep_db_fetch_array($ot_sub_query);
      $ot_sub_total = abs((int)$ot_sub_result['value']);
      
      $os_query = tep_db_query("select orders_status_name from " . TABLE_PREORDERS_STATUS . " where orders_status_id = '".$status."'");
      $os_result = tep_db_fetch_array($os_query);
      
      $num_product = 0; 
      $num_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$oID."'"); 
      $num_product_res = tep_db_fetch_array($num_product_raw); 
      if ($num_product_res) {
        $num_product = $num_product_res['products_quantity']; 
      }
      $ensure_date_arr = explode(' ', $check_status['ensure_deadline']);
      $title = str_replace(array(
        '${USER_NAME}',
        '${USER_MAIL}',
        '${PREORDER_DATE}',
        '${PREORDER_NUMBER}',
        '${PAYMENT}',
        '${ORDER_TOTAL}',
        '${ORDER_STATUS}',
        '${SITE_NAME}',
        '${SITE_URL}',
        '${SUPPORT_MAIL}',
        '${PAY_DATE}',
        '${RESERVE_DATE}', 
        '${PRODUCTS_QUANTITY}',
        '${PRODUCTS_NAME}',
        '${PRODUCTS_PRICE}',
        '${SUB_TOTAL}'
      ),array(
        $check_status['customers_name'],
        $check_status['customers_email_address'],
        tep_date_long($check_status['date_purchased']),
        $oID,
        $check_status['payment_method'],
        $otm,
        $os_result['orders_status_name'],
        get_configuration_by_site_id('STORE_NAME', $site_id),
        get_url_by_site_id($site_id),
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
        date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
        $ensure_date_arr[0], 
        $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT,
        $num_product_res['products_name'], 
        $currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax']),
        $ot_sub_total
      ),$title);

      $comments = str_replace(array(
        '${USER_NAME}',
        '${USER_MAIL}',
        '${PREORDER_DATE}',
        '${PREORDER_NUMBER}',
        '${PAYMENT}',
        '${ORDER_TOTAL}',
        '${ORDER_STATUS}',
        '${SITE_NAME}',
        '${SITE_URL}',
        '${SUPPORT_MAIL}',
        '${PAY_DATE}',
        '${RESERVE_DATE}', 
        '${PRODUCTS_QUANTITY}',
        '${PRODUCTS_NAME}',
        '${PRODUCTS_PRICE}',
        '${SUB_TOTAL}',
        '${ORDER_COMMENT}'
      ),array(
        $check_status['customers_name'],
        $check_status['customers_email_address'],
        tep_date_long($check_status['date_purchased']),
        $oID,
        $check_status['payment_method'],
        $otm,
        $os_result['orders_status_name'],
        get_configuration_by_site_id('STORE_NAME', $site_id),
        get_url_by_site_id($site_id),
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
        date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
        $ensure_date_arr[0], 
        $num_product,
        $num_product_res['products_name'], 
        str_replace(SENDMAIL_EDIT_ORDERS_PRICE_UNIT,'',$currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax'])),
        $ot_sub_total,
        preorders_a($oID)
      ),$comments);
      $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_id = '".$check_status['customers_id']."'"); 
      $customer_info_res = tep_db_fetch_array($customer_info_raw); 
      if ($customer_info_res['is_send_mail'] != '1') {
        $site_url_raw = tep_db_query("select * from sites where id = '".$site_id."'"); 
        $site_url_res = tep_db_fetch_array($site_url_raw); 
        $change_preorder_url_param = md5(time().$oID); 
        $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$change_preorder_url_param; 
        $comments = str_replace('${REAL_ORDER_URL}', $change_preorder_url, $comments); 
        
        tep_db_query("update ".TABLE_PREORDERS." set check_preorder_str = '".$change_preorder_url_param."' where orders_id = '".$oID."'"); 
        $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$num_product_res['products_id']."' and language_id='".$languages_id."' and (site_id='".$site_id."' or site_id='0') order by site_id DESC");
        $search_products_name_array = tep_db_fetch_array($search_products_name_query);
        tep_db_free_result($search_products_name_query);
        //自定义费用列表 
        $totals_email_str = '';
        $totals_email_query = tep_db_query("select title,value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_custom'");
        $num_update_totals = 0;
        $num_array_totals_rows = tep_db_num_rows($totals_email_query);
          while($totals_email_result = tep_db_fetch_array($totals_email_query)){
            $num_update_totals++;
            if($totals_email_result['title'] != '' && $totals_email_result['value'] != ''){
                $t=0;
                for($i=0;$i<mb_strlen($totals_email_result['title']);$i++){
                        $title_str = mb_substr($totals_email_result['title'],$i,1);
                        if(strlen($title_str)>1){
                                ++$t;
                        }
                }
                if((mb_strlen($totals_email_result['title'])-$t)%2 == 0){
                        if($num_array_totals_rows == $num_update_totals){
                                $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value']);
                        }else{
                                $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
                        }
                }else{
                        if($num_array_totals_rows == $num_update_totals){
                                $totals_email_str .= $totals_email_result['title'].' '.str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value']);
                        }else{
                                $totals_email_str .= $totals_email_result['title'].' '.str_repeat('　', intval((16 -mb_strlen($totals_email_result['title'])-$t)/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
                        }
                }

             // $totals_email_str .= $totals_email_result['title'].str_repeat('　', intval((16 -strlen($totals_email_result['title']))/2)).'：'.$currencies->format($totals_email_result['value'])."\n";
            }
          }
        tep_db_free_result($totals_email_query);
          if($totals_email_str != ''){
			$tep_num = count(explode(' ',$totals_emal_str));
			if($num_array_totals_rows >=1){
            $comments = str_replace('${CUSTOMIZED_FEE}',str_replace('▼','',$totals_email_str), $comments);
			}else{
            $comments = str_replace('${CUSTOMIZED_FEE}'."\r\n",str_replace('▼','',$totals_email_str), $comments);
			}
          }else{
            $comments = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $comments); 
            $comments = str_replace('${CUSTOMIZED_FEE}','', $comments);
          }
        $comments = tep_replace_mail_templates($comments,$check_status['customers_email_address'],$check_status['customers_name'],$site_id);
        $comments = html_entity_decode(htmlspecialchars($comments));
        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, str_replace($num_product_res['products_name'],$search_products_name_array['products_name'],$comments), get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
        tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS',$site_id), $title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
      }
      $customer_notified = '1';
    }
    
    if($_POST['notify_comments'] == 'on') {
      $customer_notified = '1';
    } else {
      $customer_notified = '0';
    }
    //获取预约订单最后一次备注信息
    $preorders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
    $preorders_status_history_array = tep_db_fetch_array($preorders_status_history_query);
    tep_db_free_result($preorders_status_history_query);
    $sql_data_array = array('last_modified' => 'now()','user_update' => $_SESSION['user_name']);
    tep_db_perform(TABLE_PREORDERS, $sql_data_array, 'update', "orders_id='".$oID."'");
    tep_db_query("insert into " . TABLE_PREORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, user_added) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" .  $customer_notified . "', '".$preorders_status_history_array['comments']."', '".tep_db_input($update_user_info['name'])."')");
    // 同步问答
    $order_updated = true;
  }

      if ($order_updated) {
        $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      }
        tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
      break;
    case 'deleteconfirm':
      $oID = tep_db_prepare_input($_GET['oID']);

      tep_preorder_remove_order($oID, $_POST['restock']);
      
      tep_redirect(tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action'))));
      break;
  }

  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($_GET['oID']) ) {
    $oID = tep_db_prepare_input($_GET['oID']);
    $orders_query = tep_db_query("
        select orders_id 
        from " . TABLE_PREORDERS . " 
        where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    //判断预约订单是否存在 
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
  }

  include(DIR_WS_CLASSES . 'preorder.php');
  
  $suu = 0;
  $text_suu = 0;  
  $__orders_status_query = tep_db_query("
      select orders_status_id 
      from " . TABLE_PREORDERS_STATUS . " 
      where language_id = " . $languages_id . " 
      order by orders_status_id");
  $__orders_status_ids   = array();
  while($__orders_status = tep_db_fetch_array($__orders_status_query)){
    $__orders_status_ids[] = $__orders_status['orders_status_id'];
  }
  $select_query = tep_db_query("
                      select 
                      orders_status_id,
                      nomail
      from ".TABLE_PREORDERS_STATUS."
      where language_id = " . $languages_id . " 
        and orders_status_id IN (".join(',', $__orders_status_ids).")");

  while($select_result = tep_db_fetch_array($select_query)){
    if($suu == 0){
      $select_select = $select_result['orders_status_id'];
      $suu = 1;
    }
    
    $osid = $select_result['orders_status_id'];

    //获取对应的邮件模板
    $mail_templates_query = tep_db_query("select site_id,title,contents from ". TABLE_MAIL_TEMPLATES ." where flag='PREORDERS_STATUS_MAIL_TEMPLATES_".$osid."' and site_id='0'"); 
    $mail_templates_array = tep_db_fetch_array($mail_templates_query);
    tep_db_free_result($mail_templates_query);
    if($text_suu == 0){
      $select_text = $mail_templates_array['contents'];
      $select_title = $mail_templates_array['title'];
      $text_suu = 1;
      $select_nomail = $select_result['nomail'];
    }
    
    $mt[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['contents'];
    $mo[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['title'];
    $nomail[$osid] = $select_result['nomail'];
  }

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
/* -----------------------------------------------------
   功能: 判断配送开始时间是否为空 
   参数: $oid(string) 预约订单id 
   返回值: 是否为空(boolean) 
-----------------------------------------------------*/
  function check_torihiki_date_error($oid){
    $query = tep_db_query("select * from " . TABLE_PREORDERS . " where orders_id='" . $oid . "'");
    $order = tep_db_fetch_array($query);
    if ($order['torihiki_date'] == '0000-00-00 00:00:00') {
      return true;
    }
    return false;
  }
  if ($_GET['action']=='edit' && $_GET['oID']) {
    //判断该预约订单是否被激活 
    $active_order_raw = tep_db_query("select is_active from ".TABLE_PREORDERS." where orders_id = '".$_GET['oID']."'"); 
    $active_order_res = tep_db_fetch_array($active_order_raw); 
    if (!$active_order_res['is_active']) {
      tep_redirect(FILENAME_PREORDERS); 
    }
  }

if(isset($_SESSION['error_preorders_status'])&&$_SESSION['error_preorders_status']){
  $messageStack->add($_SESSION['error_preorders_status'], 'error');
  unset($_SESSION['error_preorders_status']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<?php 
  // 订单详细页，TITLE显示交易商品名
  if ($_GET['action']=='edit' && $_GET['oID']) {?>
<title><?php echo tep_get_preorders_products_names($_GET['oID'])." ".HEADING_TITLE;; ?></title>
<?php } else { ?>
<title><?php echo HEADING_TITLE; ?></title>
<?php }?>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
<script language="javascript" src="includes/javascript/jquery.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/javascript/jquery.form.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/javascript/all_preorder.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript">
	var js_preorders_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_preorders_pwd = '<?php echo NOTICE_ORDER_INPUT_PASSWORD;?>';
	var js_preorders_error = "<?php echo NOTICE_ORDER_INPUT_WRONG_PASSWORD;?>";
	var js_preorders_npermission = '<?php echo $ocertify->npermission;?>';
	var js_preorders_flag_checked =  " <?php echo TEXT_FLAG_CHECKED;?> ";
	var js_preorders_flag_uncheck = " <?php echo TEXT_FLAG_UNCHECK;?> ";

  <?php // 用作跳转?>
  var base_url = '<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('questions_type')));?>';
  
  <?php // 非完成状态的订单不显示最终确认?>
  var show_q_8_1_able  = <?php echo tep_orders_finished($_GET['oID']) && !check_torihiki_date_error($_GET['oID']) ?'true':'false';?>;
  
  var cfg_last_customer_action = '<?php echo PREORDER_LAST_CUSTOMER_ACTION;?>';

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
	var js_preorders_payment_time = '<?php echo NOTICE_DEL_CONFIRM_PAYEMENT_TIME;?>';
	var js_preorders_payment_time_href = "<?php echo tep_href_link('pre_handle_payment_time.php')?>";
	var js_preorders_payment_time_success = '<?php echo NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS;?>';
	var js_preorders_title_changed =  "<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>";

<?php
if (!isset($_GET['action'])) {
?>
	var js_preorders_action = true;
<?php
}else{
?>
	var js_preorders_action = false;
<?php
}
?>
	var popup_num = 1;
</script>
<script language="javascript" src="includes/javascript/admin_preorders.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/oID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url;
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>', '<?php echo JS_TEXT_INPUT_ONETIME_PWD?>', '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>');
  </script>
<?php }?>
<!-- header -->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content"> 
  <tr>
<?php
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation -->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof -->';
    echo '</table>';
    echo '</td>';
?>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
    // edit start
    $order = new preorder($oID);
    $cpayment = payment::getInstance($order->info['site_id']);
    $payment_code = payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE);
?>
<script>
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
                <?php 
                   if(isset($order->info['flag_qaf'])&&$order->info['flag_qaf']&&($ocertify->npermission != 31)){
                     echo tep_html_element_button(IMAGE_EDIT, 'onclick="once_pwd_redircet_new_url(\''.  tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action','status','questions_type')) .'&action=edit') .'\', \''.$ocertify->npermission.'\')"');
                   }else{
                     echo '<a href="' . tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action','status','questions_type')) . '&action=edit') . '">'; echo tep_html_element_button(IMAGE_EDIT);
                     echo '</a>'; 
                   }
                ?>
                <?php } ?>
                <?php echo '<a id="back_link" href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <?php // 三种状态 + A,B,C,D ?>
      <tr>
        <td width="100%">
          <div id="orders_flag">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="left">
                  <div class="td_title_text">
                      <div class='<?php echo $order->info['orders_care_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_flag(this, 'care', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_ORDER_CARE;?></div>
                      <div class='<?php echo $order->info['orders_wait_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_flag(this, 'wait', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_ORDER_WAIT;?></div>
                      <div class='<?php echo $order->info['orders_inputed_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_flag(this, 'inputed', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_ORDER_INPUTED_FLAG;?></div>
                  </div>
                </td>
                <td width="50%" align="right">
                  <div class="td_title_alphabet">
                      <div id="work_a" class='<?php echo $order->info['orders_work'] == 'a' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_work(this, 'a', '<?php echo $order->info['orders_id'];?>')">A</div>
                      <div id="work_b" class='<?php echo $order->info['orders_work'] == 'b' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_work(this, 'b', '<?php echo $order->info['orders_id'];?>')">B</div>
                      <div id="work_c" class='<?php echo $order->info['orders_work'] == 'c' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_work(this, 'c', '<?php echo $order->info['orders_id'];?>')">C</div>
                      <div id="work_d" class='<?php echo $order->info['orders_work'] == 'd' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="preorders_work(this, 'd', '<?php echo $order->info['orders_id'];?>')">D</div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <?php 
        $buttons = tep_get_buttons();
        $o2c       = tep_get_buttons_by_preorders_id($order->info['orders_id']);
        if ($buttons) {
      ?> 
      <tr><td>
      <?php foreach ($buttons as $button) { ?>
          <div id="orders_alert_<?php echo $button['buttons_id'];?>" onclick="preorders_buttons(this, <?php echo $button['buttons_id'];?>, '<?php echo $order->info['orders_id'];?>')" class="<?php echo in_array($button['buttons_id'], $o2c) ? 'orders_buttons_checked' : 'orders_buttons_unchecked' ;?>"><?php echo $button['buttons_name'];?></div>
      <?php 
        } 
      ?>
      </td></tr>
      <?php
        }
      ?>
      <tr>
        <td>
        <?php // 左右结构 ?>
            <!-- left -->
            <div class="pageHeading_box">
            <div id="orders_info">
              <h3>Order Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_SITE_TEXT;?>:</td>
                  <td class="main"><font color="#FF0000"><?php echo tep_get_pre_site_name_by_order_id($oID);?></font></td>
                </tr>
                <tr>
                  <td class="main"><?php echo ENTRY_ENSURE_DATE;?></td> 
                  <td class="main"><font color="#0000FF">
                  <?php 
                  echo $order->info['ensure_deadline'];
                  ?> 
                  </font> 
                  </td> 
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo TEXT_PREORDER_ID_TEXT;?></td>
                  <td class="main"><?php echo $_GET['oID'] ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo TEXT_PREORDER_DAY;?></td>
                  <td class="main"><?php echo tep_date_long($order->customer['date']); ?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo TEXT_ORDER_CUSTOMER_TYPE;?></td>
                  <td class="main"><?php echo get_guest_chk($order->customer['id'])?TEXT_ORDER_GUEST:TEXT_ORDER_CUSTOMER_VIP;?></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo ENTRY_CUSTOMER; ?></td>
                  <td class="main" style="text-decoration: underline; ">
                  <?php
                  if ($order->info['is_gray'] == '1') {
                  ?>
                  <div class="highlight_color"> 
                  <?php
                  } 
                  $customers_name_arr = explode('|',CUSTOMER_NAME_KEYWORDS); 
                  $customers_name_str = str_replace("\n","<br>",tep_replace_to_red($customers_name_arr,htmlspecialchars($order->customer['name'])));
                  ?>
                  <a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, 'type=cid&search='.$order->customer['id']);?>"><?php echo $customers_name_str; ?></a>
                  <?php
                  if ($order->info['is_gray'] == '1') {
                  ?>
                  </div> 
                  <?php
                  } 
                  ?>
                  </td>
                </tr>
                <tr>
                  <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
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
    $customers_email_arr = explode('|',CUSTOMER_EMAIL_KEYWORDS); 
    $customers_email_str = str_replace("\n","<br>",tep_replace_to_red($customers_email_arr,tep_output_string_protected($order->customer['email_address'])));
?>
    <?php echo '<a class="order_link" href="javascript:void(0);" onclick="copyToClipboard(\'' .  tep_output_string_protected($order->customer['email_address']) . '\')">' .  $customers_email_str .  '</a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="'.TEXT_ORDER_CONCAT_OID_CREATE.'" href="'.$remoteurl.'" target="_blank">'.TEXT_ORDER_EMAIL_LINK.'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($order->customer['email_address']).'">'.TEXT_ORDER_CREDIT_LINK.'</a>'; 
?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo ENTRY_PAYMENT_METHOD; ?></td>
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
            <!-- right -->
              <?php // 订单备注 ?>
            <div style="float:left; width:100%;">
            <div id="orders_client">
              <h3>Customer Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%" nowrap><?php echo TEXT_ORDER_IP_ADDRESS;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_ip'] ? $order->info['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_HOSTNAME;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_host_name']?'<font'.($order->info['orders_host_name'] == $order->info['orders_ip'] ? ' color="red"':'').'>'.$order->info['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_USERAGENT;?></td>
                  <td class="main" style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;"><?php echo tep_high_light_by_keywords($order->info['orders_user_agent'] ? $order->info['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS);?></td>
                </tr>
                <?php if ($order->info['orders_user_agent']) {?>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_OS;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords(getOS($order->info['orders_user_agent']),OS_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_BROWSER_INFO;?></td>
                  <td class="main">
                  <?php $browser_info = getBrowserInfo($order->info['orders_user_agent']);?>
                  <?php echo tep_high_light_by_keywords($browser_info['longName'] . ' ' . $browser_info['version'],BROWSER_LIGHT_KEYWORDS); ?>
                  </td>
                </tr>
                <?php }?>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_HTTP_LAN;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_http_accept_language'] ? $order->info['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_SYS_LAN;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_system_language'] ? $order->info['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_USER_LAN;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_user_language'] ? $order->info['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_SCREEN_RES;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_screen_resolution'] ? $order->info['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_COLOR_DEPTH;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_color_depth'] ? $order->info['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS);?></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Flash:</td>
                  <td class="main">
                    <?php echo tep_high_light_by_keywords($order->info['orders_flash_enable'] === '1' ? 'YES' : ($order->info['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <?php 
                  if ($order->info['orders_flash_enable']) {
                ?>
                <tr>
                  <td class="main" valign="top" width="30%"><?php echo TEXT_ORDER_FLASH_VERS;?></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS);?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td class="main" valign="top" width="30%">Director:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_director_enable'] === '1' ? 'YES' : ($order->info['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Quick time:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_quicktime_enable'] === '1' ? 'YES' : ($order->info['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Real player:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_realplayer_enable'] === '1' ? 'YES' : ($order->info['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Windows media:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_windows_media_enable'] === '1' ? 'YES' : ($order->info['orders_windows_media_enable'] === '0' ? 'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Pdf:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_pdf_enable'] === '1' ? 'YES' : ($order->info['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="30%">Java:</td>
                  <td class="main">
                  <?php echo tep_high_light_by_keywords($order->info['orders_java_enable'] === '1' ? 'YES' : ($order->info['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS);?>
                  </td>
                </tr>
              </table>
            </div>
            <?php //访问解析 ?>
            <div id="orders_referer">
              <h3>Referer Info</h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" valign="top" width="30%" nowrap="nowrap">Referer:</td>
                  <td class="main"><p
                  style="word-break:break-all;width:250px;word-wrap:break-word;overflow:hidden;display:block;"><?php echo mb_convert_encoding(urldecode($order->info['orders_ref']),"utf-8");?></p></td>
                </tr>
                <?php if ($order->info['orders_ref_keywords']) { ?>
                <tr>
                  <td class="main" valign="top" width="30%">Keywords:</td>
                  <td class="main"><?php echo $order->info['orders_ref_keywords'];?></td>
                </tr>
                <?php } ?>
              </table>
            </div>
            <?php if ($show_payment_info == 1) { ?>
            <?php // 信用卡信息 ?>

            <div id="orders_telecom">
              <h3><?php echo TEXT_ORDER_CREDITCARD_TITLE;?></h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                <tr>
                  <td class="main" valign="top" width="20%"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>&search_type=username"><?php echo TEXT_ORDER_CREDITCARD_NAME;?></a></td>
                  <td class="main" width="30%"><?php echo $order->info['telecom_name'];?></td>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>&search_type=telno"><?php echo TEXT_ORDER_CREDITCARD_TEL;?></a></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>&search_type=email"><?php echo TEXT_ORDER_CREDITCARD_EMAIL;?></a></td>
                  <td class="main"><?php echo $order->info['telecom_email'];?></a></td>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>&search_type=money"><?php echo TEXT_ORDER_CREDITCARD_MONEY;?></a></td>
                  <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                </tr>
              </table>
            </div>

            <?php }else if ($show_payment_info == 2) {?>
            <?php // PAYPAL信息 ?>

            <div id="orders_paypal">
              <h3><?php echo TEXT_ORDER_CREDITCARD_TITLE;?></h3>
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                <tr>
                  <td class="main" valign="top" width="20%"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>"><?php echo TEXT_ORDER_CREDITCARD_NAME;?></a></td>
                  <td class="main" width="30%"><?php echo $order->info['telecom_name'];?></td>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>"><?php echo TEXT_ORDER_CREDITCARD_TEL;?></a></td>
                  <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>"><?php echo TEXT_ORDER_CREDITCARD_EMAIL;?></a></td>
                  <td class="main"><?php echo $order->info['telecom_email'];?></a></td>
                  <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>"><?php echo TEXT_ORDER_CREDITCARD_MONEY;?></a></td>
                  <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top" width="20%"><?php echo TEXT_ORDER_CREDITCARD_COUNTRY;?></td>
                  <td class="main" width="30%"><?php echo $order->info['paypal_countrycode'];?></td>
                  <td class="main" valign="top"><?php echo TEXT_ORDER_CREDITCARD_STATUS;?></td>
                  <td class="main"><?php echo $order->info['paypal_payerstatus'];?></a></td>
                </tr>
                <tr>
                  <td class="main" valign="top"><?php echo TEXT_ORDER_CREDITCARD_PAYMENTSTATUS;?></td>
                  <td class="main"><?php echo $order->info['paypal_paymentstatus'];?></a></td>
                  <td class="main" valign="top"><?php echo TEXT_ORDER_CREDITCARD_PAYMENTTYPE;?></td>
                  <td class="main"><?php echo $order->info['paypal_paymenttype'];?></a></td>
                </tr>
              </table>
            </div>
            <?php } ?>
            <?php // 注文履历 ?>
            <?php // 订单历史5条 ?>
            <div id="orders_history">
              <h3><a href="<?php echo tep_href_link('customers_products.php', 'cID='.$order->customer['id'].'&cpage=1')?>">Order History</a></h3>
              <?php 
              $customer_email_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$order->info['orders_id']."'"); 
              $customer_email_res = tep_db_fetch_array($customer_email_raw); 
              $history_list_array = array(); 
              $preorder_history_query = tep_db_query("
                  select orders_id, date_purchased 
                  from ".TABLE_PREORDERS." 
                  where   customers_email_address = '".$customer_email_res['customers_email_address']."'
                  order by date_purchased desc
                  limit 5
                ");
              while ($preorder_history_res = tep_db_fetch_array($preorder_history_query)) {
                $history_list_array['p_'.$preorder_history_res['orders_id']] = strtotime($preorder_history_res['date_purchased']); 
              }
              
              $order_history_query = tep_db_query("
                  select orders_id, date_purchased 
                  from ".TABLE_ORDERS." 
                  where   customers_email_address = '".$customer_email_res['customers_email_address']."'
                  order by date_purchased desc
                  limit 5
                ");
              
              while ($order_history_res = tep_db_fetch_array($order_history_query)) {
                $history_list_array['o_'.$order_history_res['orders_id']] = strtotime($order_history_res['date_purchased']); 
              }
              if (!empty($history_list_array)) {
                  arsort($history_list_array); 
                   ?>
                  <table width="100%" border="0" cellspacing="0" cellpadding="2">
                  <?php
                  $history_list_num = 0; 
                  foreach ($history_list_array as $h_key => $h_value) { 
                    if ($history_list_num > 4) {
                      break; 
                    }
                    $from_site_single = 0;
                    $history_table = TABLE_ORDERS; 
                    $from_site_char = substr($h_key, 0, 1);
                    $h_order_id = substr($h_key, 2);
                    if ($from_site_char == 'p') {
                      $from_site_single = 1;
                      $history_table = TABLE_PREORDERS; 
                    }
                    $order_history_info_raw = tep_db_query("select orders_id, date_purchased, orders_status_name, site_id from ".$history_table." where orders_id = '".$h_order_id."'"); 
                    $order_history_info = tep_db_fetch_array($order_history_info_raw); 
                  ?>
                    <tr>
                      <td class="main">
                      <?php
                        $store_name_raw = tep_db_query("select * from ".TABLE_SITES." where id = '".$order_history_info['site_id']."'");  
                        $store_name_res = tep_db_fetch_array($store_name_raw); 
                        echo $store_name_res['romaji']; 
                      ?>
                      </td> 
                      <td class="main">
                      <?php
                      if (!$from_site_single) {
                        echo TEXT_ORDER_HISTORY_FROM_ORDER; 
                      } else {
                        echo TEXT_ORDER_HISTORY_FROM_PREORDER; 
                      }
                      ?>
                      </td>
                      <td class="main"><?php echo $order_history_info['date_purchased'];?></td>
                      <td class="main">
                      <?php 
                      if (!$from_site_single) {
                        echo strip_tags(tep_get_ot_total_by_orders_id($order_history_info['orders_id'],true));
                      } else {
                        echo strip_tags(tep_get_pre_ot_total_by_orders_id($order_history_info['orders_id'],true));
                      }
                      ?>
                      </td>
                      <td class="main"><?php echo $order_history_info['orders_status_name'];?></td>
                    </tr>
                  <?php
                    $history_list_num++; 
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
            </div> 
            <div class="pageHeading_box_right">
            <div id="orders_comment">
              <h3>Order Comment</h3>
                <form action="ajax_preorders.php" id='form_orders_comment' method="post">

                <textarea name="orders_comment" cols="100" rows="10" class="pageHeading_box_textarea"><?php echo stripslashes($order->info['orders_comment']);?></textarea><br>
                <input type="hidden" name="orders_id" value="<?php echo $order->info['orders_id'];?>">
                <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
                <div align="right" style="clear:both;"><input type="Submit" value="<?php echo TEXT_ORDER_SAVE;?>"></div>
                </form>
              </div>
            
            <div id="orders_answer">
<?php
  // 取得问答的答案
  $total_order_query = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$order->info['orders_id']."' and class = 'ot_total'"); 
  $total_order_res = tep_db_fetch_array($total_order_query); 
  $total_order_sum = 0; 
  if ($total_order_res) {
    $total_order_sum = $total_order_res['value']; 
  }
  // 自动或者手动判断问答种类
  // 0=>贩卖, 1=>买取, 2=>信用卡, 3=>返点/来店 , 4=>不需要支付
?>
                <h3><?php echo TEXT_ORDER_ANSWER;?></h3>
 <!--new order answer{{-->
                          <?php
  $order_id = $order->info['orders_id'];
  $formtype = 4;
  $payment_romaji = tep_get_pre_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  if(!$form){

    $oa_form_temp_sql = "select * from ".TABLE_OA_FORM." limit 0,1";  
    $form = tep_db_fetch_object(tep_db_query($oa_form_temp_sql), "HM_Form");
    $form->id = '';
    $form->groups = array();
    $form->payment_romaji = $payment_romaji;
    $form->formtype = $formtype;
    $form->option = '';
  }
                       if($form){
  $form->loadOrderValue($order_id);
  $form->setAction('pre_oa_answer_process.php?oID='.$order_id);
  $form->render();
                       }
        ?>
    </td>
      </tr>
                </table>
              </div>
            </div>
        </td>
      </tr>
      <?php // 信用调查 ?>
      <tr>
        <td>
          <div id="orders_credit">
            <h3><?php echo TEXT_CREDIT_FIND;?></h3>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
            <form action="ajax_preorders.php?orders_id=<?php echo $order->info['orders_id'];?>" id='form_orders_credit' method="post">
            <td class="main">
            <div >
            <div id="customer_fax_textarea" style="display:none">
            <textarea name="orders_credit" style="width:98%;height:42px;*height:40px;"><?php echo tep_get_customers_fax_by_id($order->customer['id']);?></textarea>
            <input type="hidden" name="orders_id" value="<?php echo $order->info['orders_id'];?>">
            <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
            </div>
            <div id="customer_fax_text" style="width:98%;height:42px;*height:40px;overflow-y:auto">
            <?php
            $fax_arr = explode('|',CUSTOMER_FAX_KEYWORDS); echo str_replace("\n","<br>",tep_replace_to_red($fax_arr,tep_get_customers_fax_by_id($order->customer['id'])));
            ?>
            </div>
            </td>
            <td class="main" width="30">
            <div id="customer_fax_textarea_input" style="display:none">
            <input type="submit" value="<?php echo TEXT_ORDER_SAVE;?>">
            </div>
            <div id="customer_fax_text_input">
            <input type="button" onclick="show_edit_fax()" value="<?php echo IMAGE_EDIT;?>">
            </div>
            </td>
            </form>
            </tr>
            </table>
          </div>
        </td>
      </tr>
      <?php // 订单商品 ?>
      <tr>
        <td>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
  <?php
      for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        echo '    <tr class="dataTableRow">' . "\n" . 
       '      <td class="dataTableContent" valign="top" align="right" nowrap>' . $order->products[$i]['qty']. tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id'], $order->products[$i]['rate']) . '&nbsp;x</td>' . "\n" .
       '      <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];
        if ($order->products[$i]['price'] != '0') {
          if ($order->products[$i]['price'] < 0) {
            echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($order->products[$i]['price'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL.')';
          } else {
            echo ' ('.$currencies->format($order->products[$i]['price'], true, $order->info['currency'], $order->info['currency_value']).')';
          }
        } else if($order->products[$i]['final_price'] != '0'){
          if ($order->products[$i]['final_price'] < 0) {
            echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL.')';
          } else {
            echo ' ('.$currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']).')';
          }
        }

        if (isset($order->products[$i]['attributes']) && $order->products[$i]['attributes'] && ($k = sizeof($order->products[$i]['attributes'])) > 0) {
          $all_show_option_id = array();
          $all_show_option = array();
          $option_item_order_sql = "select it.id from ".TABLE_PRODUCTS."
          p,".TABLE_OPTION_ITEM." it 
          where p.products_id = '".(int)$order->products[$i]['id']."' 
          and p.belong_to_option = it.group_id 
          and it.status = 1
          order by it.sort_num,it.title";
          $option_item_order_query = tep_db_query($option_item_order_sql);
          while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
            $all_show_option_id[] = $show_option_row_item['id'];
          }
          $op_include_array = array(); 
          for ($j = 0; $j < $k; $j++) {
            if (is_array($order->products[$i]['attributes'][$j]['option_info'])) {
              $all_show_option[$order->products[$i]['attributes'][$j]['option_item_id']] = $order->products[$i]['attributes'][$j];
            }
          }
          // new option list 
          foreach($all_show_option_id as $t_item_id){
            $op_include_array[] = $all_show_option[$t_item_id]['id']; 
            if (is_array($all_show_option[$t_item_id]['option_info'])) {
              echo '<table><tr><td valign="top">-&nbsp; </td><td>' .  $all_show_option[$t_item_id]['option_info']['title'] . ': ' .  str_replace(array("<br>", "<BR>"), "",htmlspecialchars($all_show_option[$t_item_id]['option_info']['value']));
              if ($all_show_option[$t_item_id]['price'] != '0'){
                if ($all_show_option[$t_item_id]['price'] < 0) {
                  echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($all_show_option[$t_item_id]['price'], true, $order->info['currency'], $order->info['currency_value'])) . '</font>'.TEXT_MONEY_SYMBOL.')';
                } else {
                  echo ' (' .$currencies->format($all_show_option[$t_item_id]['price'], true, $order->info['currency'], $order->info['currency_value']) . ')';
                }
              }
              echo '</td></tr></table>';
            }
          }
          foreach ($order->products[$i]['attributes'] as $ex_key => $ex_value) {
            if (!in_array($ex_value['id'], $op_include_array)) {
              echo '<table><tr><td valign="top">-&nbsp; </td><td>' .  $ex_value['option_info']['title'] . ': ' .  str_replace(array("<br>", "<BR>"), "",$ex_value['option_info']['value']);
              if ($ex_value['price'] != '0'){
                if ($ex_value['price'] < 0) {
                  echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($ex_value['price'], true, $order->info['currency'], $order->info['currency_value'])) . '</font>'.TEXT_MONEY_SYMBOL.')';
                } else {
                  echo ' (' .$currencies->format($ex_value['price'], true, $order->info['currency'], $order->info['currency_value']) . ')';
                }
              }
              echo '</td></tr></table>';
            }
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
       '      <td class="dataTableContent" valign="top" nowrap>' . $order->products[$i]['model'] . '</td>' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top" nowrap>';
        if ($price_with_tax != '---') {
          if ($order->products[$i]['final_price'] < 0) {
            echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL,'', $price_with_tax).'</font>'.TEXT_MONEY_SYMBOL;
          } else {
            echo $price_with_tax;
          }
        } else {
          echo $price_with_tax;
        }
        echo '</td>' . "\n" .
       '      <td class="dataTableContent" align="right" valign="top" nowrap>';
        if ($price_with_tax != '---') {
          if ($order->products[$i]['final_price'] < 0) {
            echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL,'', $tprice_with_tax).'</font>'.TEXT_MONEY_SYMBOL;
          } else {
            echo $tprice_with_tax; 
          }
        } else {
          echo $tprice_with_tax; 
        }
          echo '</td>' . "\n";
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
       '      <td align="right" class="smallText">' . (mb_substr($order->totals[$i]['title'],-1,1,'utf-8') != ':' ? $order->totals[$i]['title'].':' : $order->totals[$i]['title']) . '</td>' . "\n" .
       '      <td align="right" class="smallText">'; 
        // add font color for '-' value
        if($order->totals[$i]['value']>=0){
          echo $currencies->format($order->totals[$i]['value']);
        }else{
          if($order->totals[$i]['class'] == 'ot_total'){
          echo "<b><font color='#ff0000'>";
          echo str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->totals[$i]['value']));
          echo "</font></b>".TEXT_MONEY_SYMBOL;
          }else{
          echo "<font color='#ff0000'>";
          echo str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($order->totals[$i]['value']));
          echo "</font>".TEXT_MONEY_SYMBOL;
          }
        }
        echo '</td>' . "\n" .
       '    </tr>' . "\n";
       if ($i == count($order->totals)-2) {
          echo 
         '    <tr>' . "\n" .
         '      <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
         '      <td align="right" class="smallText">' . $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
         '    </tr>' . "\n";
       }
      }
  ?>
          <tr>
            <td align="right" class="smallText"><?php echo TEXT_ORDER_TEST_TEXT;?></td>
  <?php
    $warning_sell = '';
    $warning_sell =
    tep_get_ot_total_num_by_text(abs($order->totals[sizeof($order->totals)-1]['value']));
  ?>
            <td align="right" class="smallText"><?php echo $warning_sell; ?></td>
          </tr>
          </table>
        </td>
      </tr>
    </table>
        </td>
      </tr>
    <?php  //订单状态历史记录 ?>
    <!-- orders status history -->
      <tr>
        <td class="main" align="left">
    <table border="0" cellspacing="2" cellpadding="5" bgcolor="#cccccc">
      <tr bgcolor="#ffffff">
        <td class="smallText" align="center"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
        <td class="smallText" align="center" nowrap="true"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
        <td class="smallText" align="center" nowrap="true"><?php echo TABLE_HEADING_STATUS; ?></td>
        <td class="smallText" align="center"><?php echo TABLE_HEADING_COMMENTS; ?></td>
        <td class="smallText" align="center"><?php echo TEXT_OPERATE_USER; ?></td>
        <?php if ($ocertify->npermission >= 15) { ?>
        <td class="smallText" align="center"></td>
        <?php }?> 
      </tr>
  <?php
      $comment_warning_arr = explode('|',COMMENT_SHOW_KEYWORDS);
      $orders_history_query = tep_db_query("select orders_status_history_id, orders_status_id, date_added, customer_notified, comments, user_added from " . TABLE_PREORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
      if (tep_db_num_rows($orders_history_query)) {
        $orders_status_history_str = '';
        while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          $select_select = $orders_history['orders_status_id'];
          echo 
             '    <tr bgcolor="#ffffff">' . "\n" .
             '      <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
             '      <td class="smallText" align="center">';
          if ($orders_history['customer_notified'] == '1') {
            echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
          } else {
            echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
          }
          echo '      <td class="smallText">' .  $orders_status_array[$orders_history['orders_status_id']];
          echo '</td>' . "\n";
          //不显示重复备注信息
          $orders_explode_array = array();
          $orders_explode_all_array = explode("\n",$orders_history['comments']);
          $orders_explode_array = explode(':',$orders_explode_all_array[0]);
          if(count($orders_explode_all_array) > 1){

            if(strlen(trim($orders_explode_array[1])) == 0){ 
              if(count($orders_explode_array) > 1){
                unset($orders_explode_all_array[0]);
              }
              $orders_history_comment = implode("\n",$orders_explode_all_array); 
            }else{ 
              $orders_temp_str = end($orders_explode_all_array);
              array_pop($orders_explode_all_array);
              $orders_comments_old_str = implode("\n",$orders_explode_all_array);
              if(trim($orders_comments_old_str) == trim($orders_status_history_str) && $orders_status_history_str != ''){

                $orders_history_comment = $orders_temp_str;
              }else{
                $orders_history_comment = $orders_history['comments']; 
              }
           }
          }else{
           $orders_history_comment = $orders_history['comments'];
          }
          if($orders_history['comments'] != $orders_status_history_str){
            echo '      <td class="smallText"><p style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;width:170px;">' .  tep_replace_to_red($comment_warning_arr,nl2br(tep_db_output($cpayment->admin_get_comment(payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE),stripslashes($orders_history_comment))))) . '&nbsp;</p></td>' . "\n";
          }else{
            echo '      <td class="smallText"><p style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;width:170px;">&nbsp;</p></td>' . "\n";  
          }
          echo '<td class="smallText">'.htmlspecialchars($orders_history['user_added']).'</td>'; 
          if ($ocertify->npermission >= 15) {
            echo '<td>';
            $order_confirm_payment_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".tep_db_input($oID)."'"); 
            $order_confirm_payment_res = tep_db_fetch_array($order_confirm_payment_raw); 
            echo '<input type="button" class="element_button" onclick="del_confirm_payment_time(\''.$oID.'\', \''.$orders_history['orders_status_history_id'].'\');" value="'.DEL_CONFIRM_PAYMENT_TIME.'">'; 
            echo '</td>';
          } 
          echo '</tr>' . "\n";
          $orders_status_history_str = $orders_history['comments'];
          }
      } else {
        echo
           '    <tr bgcolor="#ffffff">' . "\n" .
           '      <td class="smallText" colspan="6">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
           '    </tr>' . "\n";
      }
  ?>
    </table>
</td>
      </tr>
      </table>
      <!-- orders status history -->
      <!-- mail -->
<table border="0" width="100%">
  <tr>
    <td width="50%">
      <?php echo tep_draw_form('sele_act', FILENAME_PREORDERS, tep_get_all_get_params(array('action')) . 'action=update_order', 'post'); ?>
      <table width="100%" border="0">
      <tr>
        <td class="main"><?php echo ENTRY_STATUS; ?>
        
          <?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_select, 'onChange="new_mail_text(this, \'s_status\',\'comments\',\'title\')" id="s_status"'); ?>
        <input type="hidden" name="tmp_orders_id" id="tmp_orders_id" value="<?php echo $order->info['orders_id'];?>">
        <div style="display:none" id='edit_order_send_mail'></div>
        </td>
      </tr>
      <?php
        
        $ma_se = "select * from ".TABLE_MAIL_TEMPLATES." where ";
        if(!isset($_GET['status']) || $_GET['status'] == ""){
          $ma_se .= " flag = 'PREORDERS_STATUS_MAIL_TEMPLATES_".$order->info['orders_status']."' ";
          
          // 用来判断是否选中 送信&通知，如果nomail==1则不选中
          $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'"));
        }else{
          $ma_se .= " flag = 'PREORDERS_STATUS_MAIL_TEMPLATES_".$_GET['status']."' ";
          
          // 用来判断是否选中 送信&通知，如果nomail==1则不选中
          $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
        }
        $ma_se .= "and site_id='0'";
        $mail_sele = tep_db_query($ma_se);
        $mail_sql  = tep_db_fetch_array($mail_sele);
        $sta       = isset($_GET['status'])?$_GET['status']:'';
      ?>
      <tr>
        <td class="main"><?php echo ENTRY_EMAIL_TITLE; ?><?php echo tep_draw_input_field('title', $mail_sql['title'],'style=" width:315px;" id="mail_title"'); ?></td>
      </tr>
      <tr>
        <td class="main">
        <?php echo TABLE_HEADING_COMMENTS; ?>:
        <?php echo TEXT_MAIL_CONTENT_INFO;?>
        <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;
        <?php echo TEXT_ORDER_COPY;?></td><td>
          <?php echo TEXT_ORDER_LOGIN;?>
          </td></tr></table>
        </td>
      </tr>
      <tr>
        <td class="main">
          <textarea id="c_comments" style="font-family:monospace;font-size:12px; width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_COMMENT}',preorders_a($order->info['orders_id']),$mail_sql['contents']); ?></textarea>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><?php echo tep_draw_checkbox_field('notify', '', true && $ma_s['nomail'] != '1', '', 'id="notify"'); ?>
              <?php echo TEXT_ORDER_SEND_MAIL;?></td>
              <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', true && $ma_s['nomail'] != '1', '', 'id="notify_comments"'); ?>
              <?php echo TEXT_ORDER_STATUS;?></td>
            </tr>
            <tr>
              <td class="main" colspan="2">
              <?php echo tep_draw_hidden_field('qu_type', $orders_questions_type);?> 
              <br><font color="#FF0000;">
              <?php 
                  foreach($orders_statuses as $o_status){
                    echo '<input type="hidden" id="confrim_mail_title_'.$o_status['id'].
                      '" value="'.$mo[$o_status['id']][0].'">';
                  }
              echo TEXT_ORDER_HAS_ERROR;?></font><br><br><a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_UPDATE, 'onclick="check_mail_product_status(\''.$_GET['oID'].'\');"'); ?></a></td>
            </tr>
          </table>
        </td>
      </tr>
      </form>
      </table>
    </td>
    <td width="50%" align="left" valign="top">
<table width="100%">
  <tr><td width="30%">&nbsp; 
  </td>
  </tr>
</table>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
  </tr>
</table>
      
    </table>
    </div>
  </td>
</tr>


<?php
  } else {
  // 预约订单列表
?>
    <tr>
      <td width="100%" height="40">
  <div class="compatible">
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading" nowrap><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="smallText" valign='top' align="right">
			<div class="right_space">
              <?php echo tep_draw_form('orders1', FILENAME_PREORDERS, '',
                  'get','id="orders1" onsubmit="return false"'); ?><?php echo
              TEXT_ORDER_FIND;?> 
              <input name="keywords" style="width:310px;" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <select name="search_type" onChange='search_type_changed(this)' style="text-align:center;">
                <option value="none"><?php echo TEXT_ORDER_FIND_SELECT;?></option>
                <option value="orders_id"<?php echo ($_GET['search_type'] == 'orders_id')?' selected="selected"':'';?>><?php echo TEXT_ORDER_FIND_OID;?></option> 
                <option value="customers_name"<?php echo ($_GET['search_type'] == 'customers_name')?' selected="selected"':'';?>><?php echo TEXT_ORDER_FIND_NAME;?></option>
                <option value="email"<?php echo ($_GET['search_type'] == 'email')?' selected="selected"':'';?>><?php echo TEXT_ORDER_FIND_MAIL_ADD;?></option>
                <option value="products_name"<?php echo ($_GET['search_type'] ==
                    'products_name')?' selected="selected"':'';?>><?php echo
                TEXT_ORDER_FIND_PRODUCT_NAME ;?></option>

                <option value="value"<?php echo ($_GET['search_type'] == 'value')?'
                selected="selected"':'';?>><?php echo TEXT_PREORDER_AMOUNT_SEARCH ;?></option>


                <?php
                foreach ($all_preorders_status as $ap_key => $ap_value) {
                ?>
                
                  <option value="<?php echo 'os_'.$ap_key;?>"<?php echo ($_GET['search_type'] == 'os_'.$ap_key)?' selected="selected"':'';?>><?php echo PREORDERS_STATUS_SELECT_PRE.$ap_value.PREORDERS_STATUS_SELECT_LAST;?></option> 
                <?php
                }
                ?>
                <?php
                foreach ($all_payment_method as $p_method) {
                ?>
                <option value="<?php echo 'payment_method|'.$p_method;?>"<?php echo ($_GET['search_type'] == 'payment_method|'.$p_method)?' selected="selected"':'';?>><?php echo PREORDERS_PAYMENT_METHOD_PRE.$p_method.PREORDERS_PAYMENT_METHOD_LAST;?></option> 
                <?php
                }
                ?>
              </select>
<?php
  $sort_setting_flag = false;
  if(PERSONAL_SETTING_PREORDERS_SORT != ''){
    $sort_list_array = array("0"=>"site_romaji",
                             "1"=>"customers_name",
                             "2"=>"ot_total",
                             "3"=>"date_purchased",
                             "4"=>"orders_status_name"
                           );
    $sort_type_array = array("0"=>"asc",
                             "1"=>"desc"
                           );
    $sort_array = array();
    $sort_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SORT);
    if(array_key_exists($user_info['name'],$sort_setting_array)){
      $sort_setting_str = $sort_setting_array[$user_info['name']]; 
      $sort_array = explode('|',$sort_setting_str);
      $orders_sort = $sort_list_array[$sort_array[0]];
      $orders_type = $sort_type_array[$sort_array[1]];
    }else{
      $sort_setting_flag = true; 
    } 
  }
  if(!isset($_GET['site_id'])){ 
    $site_array = array();
    $orders_site_array = array();
    $orders_site_query = tep_db_query("select id from ". TABLE_SITES);
    while($orders_site_rows = tep_db_fetch_array($orders_site_query)){
      $orders_site_array[] = $orders_site_rows['id'];
    }
    tep_db_free_result($orders_site_query);
    $site_default = implode('|',$orders_site_array);
    if(PERSONAL_SETTING_PREORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
      if(array_key_exists($user_info['name'],$site_setting_array)){

        $site_setting_str = $site_setting_array[$user_info['name']];
      }else{
        $site_setting_str = $site_default; 
      } 
    }else{

      $site_setting_str = $site_default;
    }
    $site_array = explode('|',$site_setting_str);
    $site_list_str = implode(',',$site_array);
  }else{
    $site_array = array();
    $site_array = explode('-',$_GET['site_id']);
    $site_list_str = implode(',',$site_array);
  }
  $site_list_string = implode('-',$site_array);
  echo tep_draw_hidden_field('site_id', $site_list_string); 
  if (isset($_GET['mark'])) {
    echo tep_draw_hidden_field('mark', $_GET['mark']); 
  }
  if(isset($_GET['order_sort'])){
    echo tep_draw_hidden_field('order_sort', $_GET['order_sort']); 
  }else{
    echo tep_draw_hidden_field('order_sort', $orders_sort); 
  }
  if(isset($_GET['order_type'])){
    echo tep_draw_hidden_field('order_type', $_GET['order_type']); 
  }else{
    echo tep_draw_hidden_field('order_type', $orders_type); 
  }
?>
              </form>
			  </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
      </td>
    </tr>
    <tr>
      <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">
    <?php // 订单信息预览，配合javascript，永远浮动在屏幕右下角 ?>
    <div id="orders_info_box" style=" display:none; position:absolute; background:#FFFF00; width:70%;z-index:2; /*bottom:0;margin-top:40px;right:0;width:200px;*/">&nbsp;</div>
<?php
  if ($ocertify->npermission >= 15) {
    if(!tep_session_is_registered('reload')) $reload = 'yes';
  }
?>
    <?php echo tep_draw_form('sele_act', FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'action=sele_act'); ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td valign="bottom">
        <?php tep_site_filter(FILENAME_PREORDERS);?>
        </td>
        <td align="right">
          <?php
          if(isset($_GET['mark']) && $_GET['mark'] != ''){
            
            $get_mark_info = explode('-', $_GET['mark']);
          }else{
            $work_default = '0|1|2|3|4';
            if(PERSONAL_SETTING_PREORDERS_WORK != ''){
              $work_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_WORK);
              if(array_key_exists($user_info['name'],$work_setting_array)){

                $work_setting_str = $work_setting_array[$user_info['name']];
              }else{
                $work_setting_str = $work_default; 
              }
            }else{
              $work_setting_str = $work_default; 
            }  
            $work_array = array();
            $work_array = explode('|',$work_setting_str); 
            $work_str = implode('-',$work_array);
          }
          if(!is_array($get_mark_info)){
            $get_mark_info = array();
          }
          ?>
          <table border="0" width="100%" cellpadding="0" cellspacing="1" class="table_wrapper">
            <tr>
              <td id="mark_o" class="<?php echo (in_array('0', $get_mark_info) || (!isset($_GET['mark']) && in_array('0',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'0','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">&nbsp;</td> 
              <td id="mark_a" class="<?php echo (in_array('1', $get_mark_info) || (!isset($_GET['mark']) && in_array('1',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'1','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">A</td> 
              <td id="mark_b" class="<?php echo (in_array('2', $get_mark_info) || (!isset($_GET['mark']) && in_array('2',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'2','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">B</td> 
              <td id="mark_c" class="<?php echo (in_array('3', $get_mark_info) || (!isset($_GET['mark']) && in_array('3',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'3','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">C</td> 
              <td id="mark_d" class="<?php echo (in_array('4', $get_mark_info) || (!isset($_GET['mark']) && in_array('4',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'4','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">D</td> 
            </tr>
          </table>
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
  
  if ($_GET['order_type'] == 'asc') {
    $type_str = 'desc'; 
  } else {
    $type_str = 'asc'; 
  }
?>
      <td class="dataTableHeadingContent_order">
<?php   
      if ($HTTP_GET_VARS['order_sort'] == 'site_romaji'){
        echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=site_romaji&order_type='.$type_str)."'>";
        echo TABLE_HEADING_SITE;
        if($type_str == 'asc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
      }else{
        if($orders_sort == 'site_romaji' && !isset($_GET['order_sort'])){
          $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
          echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=site_romaji&order_type='.$orders_type_str)."'>";
          echo TABLE_HEADING_SITE;
        if($orders_type == 'desc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
        }else{
          echo "<a class='head_sort_order' href='".tep_href_link(FILENAME_PREORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).
                'order_sort=site_romaji&order_type=desc')."'>";
          echo TABLE_HEADING_SITE;
        }
      }
      echo "</a>";
      ?>
      </td>
      <td class="dataTableHeadingContent_order" width="35%">
      <?php 
      if ($HTTP_GET_VARS['order_sort'] == 'customers_name'){
        echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=customers_name&order_type='.$type_str)."'>";
        echo TABLE_HEADING_CUSTOMERS; 
        if($type_str == 'asc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
      }else{
        if($orders_sort == 'customers_name' && !isset($_GET['order_sort'])){
          $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
          echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=customers_name&order_type='.$orders_type_str)."'>";
          echo TABLE_HEADING_CUSTOMERS; 
        if($orders_type == 'desc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
        }else{
          echo "<a class='head_sort_order' href='".tep_href_link(FILENAME_PREORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).
                'order_sort=customers_name&order_type=desc')."'>";
          echo TABLE_HEADING_CUSTOMERS; 
        }
      }
      echo "</a>";
      ?>
      </td>
      <td class="dataTableHeadingContent_order" align="right">
      <?php 
      if ($HTTP_GET_VARS['order_sort'] == 'ot_total'){
        echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=ot_total&order_type='.$type_str)."'>";
        echo TABLE_HEADING_ORDER_TOTAL; 
        if($type_str == 'asc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
      }else{
        if($orders_sort == 'ot_total' && !isset($_GET['order_sort'])){
          $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
          echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=ot_total&order_type='.$orders_type_str)."'>";
          echo TABLE_HEADING_ORDER_TOTAL; 
        if($orders_type == 'desc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
        }else{
          echo "<a class='head_sort_order' href='".tep_href_link(FILENAME_PREORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).
                'order_sort=ot_total&order_type=desc')."'>";
          echo TABLE_HEADING_ORDER_TOTAL; 
        }
      }
      echo "</a>";
      ?>
      </td>
      <td class="dataTableHeadingContent">&nbsp;</td>
      <td class="dataTableHeadingContent">&nbsp;</td>
      <td class="dataTableHeadingContent">&nbsp;</td>
      <td class="dataTableHeadingContent_order" align="center">
      <?php 
      if ($HTTP_GET_VARS['order_sort'] == 'date_purchased'){
        echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=date_purchased&order_type='.$type_str)."'>";
        echo TABLE_HEADING_DATE_PURCHASED; 
        if($type_str == 'asc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
      }else{
        if($orders_sort == 'date_purchased' && !isset($_GET['order_sort'])){
          $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
          echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=date_purchased&order_type='.$orders_type_str)."'>";
          echo TABLE_HEADING_DATE_PURCHASED; 
        if($orders_type == 'desc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
        }else{
          echo "<a class='head_sort_order' href='".tep_href_link(FILENAME_PREORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).
                'order_sort=date_purchased&order_type=desc')."'>";
          echo TABLE_HEADING_DATE_PURCHASED; 
        }
      }
      echo "</a>";
      ?>
      </td>
      <td class="dataTableHeadingContent" align="right"></td>
      <td class="dataTableHeadingContent_order" align="right">
      <?php  
      if ($HTTP_GET_VARS['order_sort'] == 'orders_status_name'){
        echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=orders_status_name&order_type='.$type_str)."'>";
        echo TABLE_HEADING_STATUS; 
        if($type_str == 'asc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
      }else{
        if($orders_sort == 'orders_status_name' && !isset($_GET['order_sort'])){
          $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
          echo "<a class='head_sort_order_select' href='".tep_href_link(FILENAME_PREORDERS,
            tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).'order_sort=orders_status_name&order_type='.$orders_type_str)."'>";
          echo TABLE_HEADING_STATUS; 
        if($orders_type == 'desc'){
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }else{
          echo "<font color='#facb9c'>";
          echo TEXT_SORT_ASC;
          echo "</font>";
          echo "<font color='#c0c0c0'>";
          echo TEXT_SORT_DESC;
          echo "</font>";
        }
        }else{
          echo "<a class='head_sort_order' href='".tep_href_link(FILENAME_PREORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                'order_sort')).
                'order_sort=orders_status_name&order_type=desc')."'>";
          echo TABLE_HEADING_STATUS; 
        }
      }
      echo "</a>";
      ?>
      </td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
<?php
    
  $where_type = '';
  $where_payment = '';
  $sort_table = '';
  $sort_where = ''; 
  //预约终止的订单是否显示到预约订单列表中
  $is_show_transaction = false; 
  if (PERSONAL_SETTING_PREORDERS_TRANSACTION_FINISH != '') {
    $show_transaction_array = @unserialize(PERSONAL_SETTING_PREORDERS_TRANSACTION_FINISH);  
    if (isset($show_transaction_array[$ocertify->auth_user])) {
      if ($show_transaction_array[$ocertify->auth_user] == '1') {
        $is_show_transaction = true; 
      }
    }
  }
  if (!isset($_GET['order_sort']) || $_GET['order_sort'] == '') {
    if(PERSONAL_SETTING_PREORDERS_SORT == '' || $sort_setting_flag == true){
      $order_str = 'o.date_purchased DESC'; 
    }else{
      if($orders_sort == 'site_romaji'){
        $sort_table = " ,".TABLE_SITES." s ";
        $sort_where = " o.site_id = s.id and ";
        $order_str = " s.romaji ".$orders_type;
      }else if($orders_sort == 'customers_name'){
        $order_str = " o.customers_name ".$orders_type;
      }else if($orders_sort == 'ot_total'){
        $sort_table = " ,". TABLE_PREORDERS_TOTAL." ot ";
        $sort_where = " o.orders_id = ot.orders_id and ot.class  ='ot_total' and ";
        $order_str = " ot.value ".$orders_type;
      }else if($orders_sort == 'predate'){
        $order_str = " o.predate ".$orders_type;
      }else if($orders_sort == 'date_purchased'){
        $order_str = " o.date_purchased ".$orders_type;
      }else if($orders_sort == 'orders_status_name'){
        $order_str = " o.orders_status_name ".$orders_type;
      } 
   }
  } else {
    if($_GET['order_sort'] == 'site_romaji'){
      $sort_table = " ,".TABLE_SITES." s ";
      $sort_where = " o.site_id = s.id and ";
      $order_str = " s.romaji ".$_GET['order_type'];
    }else if($_GET['order_sort'] == 'customers_name'){
      $order_str = " o.customers_name ".$_GET['order_type'];
    }else if($_GET['order_sort'] == 'ot_total'){
      $sort_table = " ,". TABLE_PREORDERS_TOTAL." ot ";
      $sort_where = " o.orders_id = ot.orders_id and ot.class  ='ot_total' and ";
      $order_str = " ot.value ".$_GET['order_type'];
    }else if($_GET['order_sort'] == 'predate'){
      $order_str = " o.predate ".$_GET['order_type'];
    }else if($_GET['order_sort'] == 'date_purchased'){
      $order_str = " o.date_purchased ".$_GET['order_type'];
    }else if($_GET['order_sort'] == 'orders_status_name'){
      $order_str = " o.orders_status_name ".$_GET['order_type'];
    }
  }
  
  $mark_sql_str = ''; 
  if (isset($_GET['mark'])) { 
    $mark_info = explode('-', $_GET['mark']); 
    if (in_array('0', $mark_info)) {
      if (count($mark_info) == 1) {
        $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))"; 
      } else {
        $mark_str = ''; 
        foreach ($mark_info as $m_key => $m_value) {
          if ($m_value == '1') {
            $mark_str .= '\'a\','; 
          } else if ($m_value == '2') {
            $mark_str .= '\'b\','; 
          } else if ($m_value == '3') {
            $mark_str .= '\'c\','; 
          } else if ($m_value == '4') {
            $mark_str .= '\'d\','; 
          } 
        }
        $mark_str = substr($mark_str, 0, -1);
        $mark_sql_str = "((o.orders_work is null) or (o.orders_work = '') or (o.orders_work in (".$mark_str.")))"; 
      }
    } else {
      $mark_str = ''; 
      foreach ($mark_info as $m_key => $m_value) {
        if ($m_value == '1') {
          $mark_str .= '\'a\','; 
        } else if ($m_value == '2') {
          $mark_str .= '\'b\','; 
        } else if ($m_value == '3') {
          $mark_str .= '\'c\','; 
        } else if ($m_value == '4') {
          $mark_str .= '\'d\','; 
        }
      }
      $mark_str = substr($mark_str, 0, -1);
      $mark_sql_str = "o.orders_work in (".$mark_str.")"; 
    }
  }else{
    $mark_info = explode('-', $work_str); 
    if (in_array('0', $mark_info)) {
      if (count($mark_info) == 1) {
        $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))"; 
      } else {
        $mark_str = ''; 
        foreach ($mark_info as $m_key => $m_value) {
          if ($m_value == '1') {
            $mark_str .= '\'a\','; 
          } else if ($m_value == '2') {
            $mark_str .= '\'b\','; 
          } else if ($m_value == '3') {
            $mark_str .= '\'c\','; 
          } else if ($m_value == '4') {
            $mark_str .= '\'d\','; 
          } 
        }
        $mark_str = substr($mark_str, 0, -1);
        $mark_sql_str = "((o.orders_work is null) or (o.orders_work = '') or (o.orders_work in (".$mark_str.")))"; 
      }
    } else {
      $mark_str = ''; 
      foreach ($mark_info as $m_key => $m_value) {
        if ($m_value == '1') {
          $mark_str .= '\'a\','; 
        } else if ($m_value == '2') {
          $mark_str .= '\'b\','; 
        } else if ($m_value == '3') {
          $mark_str .= '\'c\','; 
        } else if ($m_value == '4') {
          $mark_str .= '\'d\','; 
        }
      }
      $mark_str = substr($mark_str, 0, -1);
      $mark_sql_str = "o.orders_work in (".$mark_str.")"; 
    }
  } 
  if (isset($_GET['cEmail']) && $_GET['cEmail']) {
      //邮件查询 
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
               o.predate, 
               o.customers_email_address,
               o.orders_comment,
               o.torihiki_houhou,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table. "
        where ".$sort_where." o.customers_email_address = '" . tep_db_input($cEmail) . "' 
          " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . $where_payment . $where_type . "
        order by ".$order_str;
    } else if (isset($_GET['cID']) && $_GET['cID']) {
      //顾客id查询 
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
               o.predate, 
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table. "
        where ".$sort_where." o.customers_id = '" . tep_db_input($cID) . "' 
          " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . $where_payment . $where_type . "
        order by ".$order_str;
    } elseif (isset($_GET['status']) && $_GET['status']) {
      //状态查询 
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
               o.predate, 
               o.torihiki_houhou,
               o.customers_email_address,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table. "
        where ".$sort_where." o.orders_status = '" . tep_db_input($status) . "' 
          " . " and o.site_id in (". $site_list_str .")"  . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . $where_payment . $where_type . "
        order by ".$order_str;
    }  elseif (isset($_GET['keywords']) && isset($_GET['search_type']) && $_GET['search_type'] == 'products_name' && !$_GET['type'] && !$payment) {
      //商品名查询 
      $orders_query_raw = " select distinct op.orders_id from " .  TABLE_PREORDERS_PRODUCTS . " op, ".TABLE_PREORDERS." o ".$sort_table." where ".$sort_where." op.orders_id = o.orders_id and op.products_name like '%".$_GET['keywords']."%' " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " order by ".$order_str;
    }  elseif (isset($_GET['keywords']) && isset($_GET['search_type']) &&
        $_GET['search_type'] == 'sproducts_id' && !$_GET['type'] && !$payment) {
      //未完成订单查询 
        $query_str = ''; 
        $query_num = '';
        if(!empty($site_id)){

           if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_id) != ''){
               $query_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_id);
           }else{

               if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                    $query_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
               }
           }
        }else{
               if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                    $query_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
               }
        }
        if(!empty($site_id) && $site_id != 0){
          if($query_num != ''){

            $query_str = "and date_format(o.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$query_num.' minutes'))."' ";
          }
        }else{

          $site_id_query = tep_db_query("select id from ".TABLE_SITES);
          $query_str = 'and (';
          while($site_id_array = tep_db_fetch_array($site_id_query)){

           $site_temp_id = $site_id_array['id'];
           $query_temp_num = '';
           if(!empty($site_temp_id)){

             if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id) != ''){
               $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id);
             }else{

               if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                 $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
               }
             }
          }else{
               if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                 $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
               }
          } 
          $query_str .= "(o.site_id = ".$site_temp_id;
          if($query_temp_num != ''){
            $query_str .= " and date_format(o.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$query_temp_num.' minutes'))."') or ";
          }else{
            $query_str .= ') or ';
          }
        }
        tep_db_free_result($site_id_query);
        $query_str = substr($query_str,0,-4);
        $query_str .= ') ';
      }   
      $orders_query_raw = " select distinct op.orders_id from " .  
        TABLE_PREORDERS_PRODUCTS . " op, ".TABLE_PREORDERS." o ".
        $sort_table." where ".$sort_where." op.orders_id = o.orders_id 
        and op.products_id = '".$_GET['keywords']."' " . " 
        and o.finished != '1' and o.flag_qaf != '1' ".$query_str
        ."and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " order by ".$order_str;
    }elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && preg_match('/^os_\d+$/', $_GET['search_type'])))) {
    //状态查询 
    if (!empty($_GET['keywords'])) {
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
                 o.predate, 
                 o.orders_inputed_flag,
                 o.orders_work,
                 o.customers_email_address,
                 o.torihiki_houhou,
                 o.orders_comment,
                 o.confirm_payment_time, 
                 o.is_active, 
                 o.site_id,
                 o.is_gray, 
                 o.read_flag
          from " . TABLE_PREORDERS . " o " . $from_payment . " , ".TABLE_PREORDERS_PRODUCTS." op ".$sort_table." where ".$sort_where." 1=1 " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " and o.orders_status = '".substr($_GET['search_type'], 3)."' and o.orders_id = op.orders_id and (o.orders_id like '%".$_GET['keywords']."%' or o.customers_name like '%".$_GET['keywords']."%' or o.customers_email_address like '%".$_GET['keywords']."%' or op.products_name like '%".$_GET['keywords']."%') " .  $where_payment . $where_type.' order by '.$order_str;
    } else {
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
                 o.predate, 
                 o.orders_inputed_flag,
                 o.orders_work,
                 o.customers_email_address,
                 o.torihiki_houhou,
                 o.orders_comment,
                 o.confirm_payment_time, 
                 o.is_active, 
                 o.site_id,
                 o.is_gray, 
                 o.read_flag
          from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table ." where ".$sort_where." 1=1 " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " and o.orders_status = '".substr($_GET['search_type'], 3)."'" .  $where_payment . $where_type.' order by '.$order_str;
    }
    }elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'orders_id'))) {
    //订单号查询 
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
               o.predate, 
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment . $sort_table."
        where ".$sort_where." 1=1 
          " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " and o.orders_id like '%".$_GET['keywords']."%'" . $where_payment . $where_type .' order by '.$order_str;
    }elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name') || (isset($_GET['search_type']) && $_GET['search_type'] == 'email'))
  ) {
    //顾客名/邮箱查询 
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
               o.predate, 
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table."
        where ".$sort_where." 1=1 
          " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:''). $where_payment . $where_type ;

    $keywords = str_replace('　', ' ', $_GET['keywords']);
    tep_parse_search_string($keywords, $search_keywords);
    
    $sk_raw = '';
    if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
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
        if (isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name') {
          $sk_raw .= "(o.customers_name like '%" . tep_db_input($keyword) . "%' or "; 
          $sk_raw .= "o.customers_name_f like '%" . tep_db_input($keyword) . "%')";
          if($i<$n-1){
            $sk_raw .= ' or ';
          }
        } else if (isset($_GET['search_type']) && $_GET['search_type'] == 'email') {
          $orders_query_raw .= " and o.customers_email_address like '%" . tep_db_input($keyword) . "%'";
        }
    break;
    }
      } 
    }
    if ($sk_raw != '') {
      $and_single = substr($orders_query_raw, -4);
      if ($and_single == 'and ') {
        $orders_query_raw .= '('.$sk_raw.')'; 
      } else {
        $orders_query_raw .= ' and ('.$sk_raw.')'; 
      }
    }
    $orders_query_raw .= " order by ".$order_str;
  } else if (isset($_GET['keywords']) && ((isset($_GET['search_type']) && preg_match('/^payment_method/', $_GET['search_type'])))) {
    //支付方法查询 
    $payment_m = explode('|', $_GET['search_type']); 
    if (!empty($_GET['keywords'])) {
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
                 o.predate, 
                 o.orders_inputed_flag,
                 o.orders_work,
                 o.customers_email_address,
                 o.torihiki_houhou,
                 o.orders_comment,
                 o.confirm_payment_time, 
                 o.is_active, 
                 o.site_id,
                 o.is_gray, 
                 o.read_flag
          from " . TABLE_PREORDERS . " o " . $from_payment . " , ".TABLE_PREORDERS_PRODUCTS." op ".$sort_table." where ".$sort_where." 1=1 " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " and o.payment_method = '".$payment_m[1]."' and o.orders_id = op.orders_id and (o.orders_id like '%".$_GET['keywords']."%' or o.customers_name like '%".$_GET['keywords']."%' or o.customers_email_address like '%".$_GET['keywords']."%' or op.products_name like '%".$_GET['keywords']."%') " .  $where_payment . $where_type.' order by '.$order_str;
    } else {
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
                 o.predate, 
                 o.orders_inputed_flag,
                 o.orders_work,
                 o.customers_email_address,
                 o.torihiki_houhou,
                 o.orders_comment,
                 o.confirm_payment_time, 
                 o.is_active, 
                 o.site_id,
                 o.is_gray, 
                 o.read_flag
          from " . TABLE_PREORDERS . " o " . $from_payment . $sort_table ."
          where ".$sort_where." 1=1 " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . " and o.payment_method = '".$payment_m[1]."'" .  $where_payment .  $where_type.' order by '.$order_str;
    }
  } 
  
elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'value'))) {
   //金额查询 
   $keywords = $_GET['keywords'];
   $orders_total_query = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where
       class='ot_total' and value='".$keywords.".0000'");
      $orders_like_str = '';
      $orders_total_array = array();
      while($orders_total_array = tep_db_fetch_array($orders_total_query)){
       $orders_like_array[] = $orders_total_array['orders_id'];
      }
     $orders_like_str = implode("','",$orders_like_array);
     if(count($orders_like_array) == 1){
       $orders_str = "='".$orders_like_str."'";
           }else{
       $orders_str = " in ('".$orders_like_str."')";
           }
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
               o.predate, 
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
           from " . TABLE_PREORDERS . " o " . $from_payment .$sort_table ."
	       where " . $sort_where.
	       " o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " o.orders_id" .$orders_str.
	       $where_payment . $where_type.' order by '.$order_str; 
              }

  
  elseif (isset($_GET['keywords']) && $_GET['keywords']) {
    //关键字查询 
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
               o.predate, 
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
        from " . TABLE_PREORDERS . " o " . $from_payment . ", " .  TABLE_PREORDERS_PRODUCTS . " op ".$sort_table." where ".$sort_where." o.orders_id = op.orders_id " . " and o.site_id in (". $site_list_str .")" . (($mark_sql_str != '')?' and '.$mark_sql_str:'') . $where_payment . $where_type ;
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
    
    $orders_query_raw .= "order by ".$order_str;
  } else {
      // orders_list 隐藏 「取消」和「取消订单」
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
               o.predate, 
               o.customers_email_address,
               o.orders_comment,
               o.torihiki_houhou,
               o.confirm_payment_time, 
               o.is_active, 
               o.site_id,
               o.is_gray, 
               o.read_flag
         from " . TABLE_PREORDERS . " o " . $from_payment . $sort_table ."
         where ".$sort_where. 
          (($mark_sql_str != '')?' '.$mark_sql_str:'')." 
          -- and o.orders_status != '6'
          -- and o.orders_status != '8'
          " . " and o.site_id in (". $site_list_str .")" .((!$is_show_transaction)?" and o.flag_qaf = 0":''). $where_payment . $where_type . "
         order by ".$order_str;
  }
  // old sort is  order by torihiki_date_error DESC,o.torihiki_date DESC
    // new sort is  order by o.torihiki_date DESC
//where
          //(o.q_8_1 IS NULL or o.q_8_1 = '')
    $from_pos = strpos($orders_query_raw, 'from orders');
    $order_pos = strpos($orders_query_raw, 'order by');
    $op_pos = strpos($orders_query_raw, 'distinct op.orders_id'); 
    if (($from_pos !== false) && ($order_pos !== false)) {
      if ($op_pos !== false) {
        $sql_count_query = "select count(op.orders_id) as count ".substr($orders_query_raw, $from_pos, $order_pos - $from_pos);
      } else {
        $sql_count_query = "select count(o.orders_id) as count ".substr($orders_query_raw, $from_pos, $order_pos - $from_pos);
      }
    }
    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows, $sql_count_query);
    $orders_query = tep_db_query($orders_query_raw);
    $allorders    = $allorders_ids = array();
    $orders_i = 0;
    while ($orders = tep_db_fetch_array($orders_query)) {
      $orders_i++;
      if (!isset($orders['site_id'])) {
        $orders = tep_db_fetch_array(tep_db_query("
          select *
          from ".TABLE_PREORDERS." o
          where orders_id='".$orders['orders_id']."'
        "));
      }
      $allorders[] = $orders;
      if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
        $oInfo = new objectInfo($orders);
      }

  //如果是今天的交易的话红色显示
  $trade_array = getdate(strtotime(tep_datetime_short($orders['torihiki_date'])));
  $today_array = getdate();
  if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
    $today_color = 'red';
    if ($trade_array["hours"] >= $today_array["hours"]) {
      $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif',
          TEXT_ORDER_NEXT_ORDER); //标记下个订单
    } else {
      $next_mark = '';
    }
  } else {
      $today_color = 'black';
    $next_mark = '';
  }
  $even = 'dataTableSecondRow';
  $odd  = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even; 
  } else {
    $nowColor = $odd; 
  }
  $is_gray_style = ''; 
  if ($orders['is_gray'] == '1') {
    $is_gray_style = 'style="background-color:#AAAAAA;"'; 
  }
  if ( (isset($oInfo) && is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) {
    if($orders_i == 1 && !isset($_GET['oID'])){ 
      echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" '.$is_gray_style.'>' . "\n";
    }else{
      echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
    }
  } else {
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" '.$is_gray_style.'>' . "\n";
  }
?>
  <?php 
  if ($ocertify->npermission) {
    ?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent"> 
        <?php
        if ($orders['is_active'] == '0') {
        ?>
        <input type="checkbox" name="unchk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this);show_questions(this);" disabled>
        <?php
        } else {
        ?>
        <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this);show_questions(this, '<?php echo JS_TEXT_ALL_ORDER_COMPLETION_TRANSACTION;?>', '<?php echo JS_TEXT_ALL_ORDER_SAVE;?>');">
        <?php
        }
        ?>
        </td>
<?php 
  }
?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
        <?php 
        if ($orders['is_active'] == '0') {
          echo '<span style="color:#999999;">'; 
        }
        echo tep_get_site_romaji_by_id($orders['site_id']);
        if ($orders['is_active'] == '0') {
          echo '</span>'; 
        }
        ?>
        </td>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
          <div class="float_left">
          <?php
            if ($orders['is_active'] == '1') {
          ?>
          <a href="<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <?php
            } else {
          ?>
          <a href="javascript:void(0);" onclick="window.alert('<?php echo NOTICE_LIMIT_SHOW_PREORDER_TEXT;?>');"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <?php
            }
          ?>
          <a href="<?php echo tep_href_link('preorders.php', 'cEmail=' .  tep_output_string_protected(urlencode($orders['customers_email_address'])));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', TEXT_ORDER_HISTORY_ORDER);?></a>
          </div>
          <div>
           
          <?php 
  if ($orders['is_active'] == '0') {
  ?>
          <a style="text-decoration:underline;" href="javascript:void(0);">
  <?php } else { ?>
          <a style="text-decoration:underline;" href="<?php echo tep_href_link('customers.php', 'type=cid&search=' .  tep_output_string_protected($orders['customers_id']));?>">
  <?php }?>
          <?php 
  if ($orders['is_active'] == '0') {
  ?>
  <span style="color:#999999;"> 
  <?php 
  } else {
  if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {
  ?>
  <font color="#999">
  <?php } else { ?> 
  <font color="#000">
  <?php } ?>
  <?php } ?>
          <?php echo tep_output_string_protected($orders['customers_name']);?>
          <?php
  if ($orders['is_active'] == '0') {
  ?>
  </span>  
  <?php
  } else {
  ?>
  </font>
  <?php
  }
  ?>
          </a>
          
          <input type="hidden" id="cid_<?php echo $orders['orders_id'];?>" name="cid[]" value="<?php echo $orders['customers_id'];?>" />
  <?php 
  $customers_info_raw = tep_db_query("select pic_icon from ".TABLE_CUSTOMERS." where customers_id = '".$orders['customers_id']."'"); 
  $customers_info_res = tep_db_fetch_array($customers_info_raw);
  $customers_pic = explode(',',$customers_info_res['pic_icon']); 
  if ($customers_info_res) {
    if (!empty($customers_info_res['pic_icon'])) {
      foreach($customers_pic as $key => $pic_value){
      if (file_exists(DIR_FS_DOCUMENT_ROOT.DIR_WS_IMAGES.'icon_list/'.$pic_value)) {
        $pic_icon_title_str = ''; 
        $pic_icon_title_raw = tep_db_query("select pic_alt from ".TABLE_CUSTOMERS_PIC_LIST." where pic_name = '".$pic_value."'"); 
        $pic_icon_title_res = tep_db_fetch_array($pic_icon_title_raw); 
        if ($pic_icon_title_res) {
          $pic_icon_title_str = $pic_icon_title_res['pic_alt']; 
        }
        echo tep_image(DIR_WS_IMAGES.'icon_list/'.$pic_value, $pic_icon_title_str); 
      }
     }
    }
  }
  ?>
  <?php if ($orders['orders_care_flag']) { ?>
  <?php echo tep_image(DIR_WS_ICONS . 'care.gif', TEXT_ORDER_CARE);?>
  <?php }?>

</div>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
      <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
      <?php
      if ($orders['is_active'] == '0') {
      ?>
      <span style="color:#999999;"> 
      <?php
      } else {
      ?>
      <?php
      }
      ?>
      <?php 
      if ($orders['is_active'] == '1') {
        echo str_replace(array('<b>', '</b>'), '', tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'],true));
      } else {
        echo strip_tags(tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'],true));
      }
      ?>
      <?php
      if ($orders['is_active'] == '0') {
      ?>
      </span> 
      <?php
      } else {
      ?>
      <?php
      }
      ?>
      <?php } else { ?>
      <?php
      if ($orders['is_active'] == '0') {
      ?>
      <span style="color:#999999;"> 
      <?php }?> 
      <?php 
      if ($orders['is_active'] == '1') {
        echo str_replace(array('<b>', '</b>'), '', tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'], true));
      } else {
        echo strip_tags(tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'], true));
      }
      ?>
      <?php
      if ($orders['is_active'] == '0') {
      ?>
      </span> 
      <?php }?> 
      <?php }?>
    </td>
<td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left">
<?php
  $read_flag_str_array = explode('|||',$orders['read_flag']);
  if($orders['read_flag'] == ''){
    echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>'; 
  }else{

    if(in_array($ocertify->auth_user,$read_flag_str_array)){

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif"></a>';
    }else{

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>';
    }
  }
?>
</td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><?php
    if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES .
        'icon_hand.gif', TEXT_ORDER_WAIT); } else { echo '&nbsp;'; } ?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
    <?php 
    // 订单历史记录的图标
      $___orders_status_query = tep_db_query("select orders_status_id from `".TABLE_PREORDERS_STATUS_HISTORY."` WHERE `orders_id`='".$orders['orders_id']."' order by `date_added` asc");
      $___orders_status_ids   = array();
      while($___orders_status = tep_db_fetch_array($___orders_status_query)){
        $___orders_status_ids[] = $___orders_status['orders_status_id'];
      }
      if ($___orders_status_ids) {
      $_orders_status_history_query_raw = "select * from `".TABLE_PREORDERS_STATUS."` WHERE `orders_status_id` IN (".join(',',$___orders_status_ids).")";
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
    ?>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
    <?php
    if ($orders['is_active'] == '0') {
    ?>
    <span style="color:#999999;"> 
    <?php
    } else {
    ?>
    <font color="<?php echo $today_color; ?>">
    <?php
    }
    ?>
    <?php 
      echo $orders['orders_status_name']; 
    ?>
    <input type="hidden" name="os[]" id="orders_status_<?php echo $orders['orders_id']; ?>" value="<?php echo $orders['orders_status']; ?>">
    <?php
    if ($orders['is_active'] == '0') {
    ?>
    </span> 
    <?php
    } else {
    ?>
    </font>
    <?php
    }
    ?>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"  onmouseout="if(popup_num == 1) hideOrdersInfo(0);">
    <?php
      echo '<a href="javascript:void(0);" onclick="showPreOrdersInfo(\''.$orders['orders_id'].'\', this, 1, \''.urlencode(tep_get_all_get_params(array('oID', 'action'))).'\');">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
      
    ?>&nbsp;</td>
    </tr>
<?php }?>
  </table>
<script language="javascript">
  window.orderStr = new Array();
  <?php // 订单所属网站?>
  window.orderSite = new Array();
  <?php // 0 空 1 卖 2 买 3 混?>
  var orderType = new Array();
  var questionShow = new Array();
<?php foreach($allorders as $key=>$orders){?>
  window.orderStr['<?php echo $orders['orders_id'];?>']  = "<?php echo str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'), orders_a($orders['orders_id'], $allorders));?>";
  window.orderSite['<?php echo $orders['orders_id'];?>'] = "<?php echo $orders['site_id'];?>";
  orderType['<?php echo $orders['orders_id'];?>']        = "<?php echo tep_check_order_type($orders['orders_id']);?>";
<?php }?>
function submit_confirm()
{
  var _end = $("#mail_title_status").val();
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
  if(confirm("<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>")){
  }else{
     return false;
    }
  }
  var idx = document.sele_act.elements['status'].selectedIndex;
  var CI  = document.sele_act.elements['status'].options[idx].value;
  chk = getCheckboxValue('chk[]')
  if((chk.length > 1 || chk.length < 1) && window.status_text[CI].indexOf('${ORDER_COMMENT}') != -1){
    if(chk.length > 1){
      alert('<?php echo TEXT_SELECT_MORE;?>');
    } else {
      alert('<?php echo TEXT_ORDER_SELECT;?>');
    }
    return false;
  }
  return true;
}
</script>
<table width="100%"><tr><td width="70%" id="send_mail_td">
      <table width="100%" id="select_send" style="display:none">
        <tr>
          <td class="main" width="100" nowrap="nowrap"><?php echo ENTRY_STATUS; ?></td>
        <td class="main"><?php echo tep_draw_pull_down_menu('status',
            $orders_statuses, $select_select,
            'onChange="mail_text(\'status\',\'comments\',\'os_title\')" id="mail_title_status"'); ?> <?php
        if($ocertify->npermission > 7 ) { ?>&nbsp;<a href="<?php echo
          tep_href_link(FILENAME_PREORDERS_STATUS,'',SSL);?>"><?php echo
            TEXT_EDIT_MAIL_TEXT;?></a><?php } ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main" nowrap="nowrap"><?php echo ENTRY_EMAIL_TITLE; ?></td>
        <td class="main"><?php echo tep_draw_input_field('os_title', $select_title,' style=" width:400px;" id="mail_title"'); ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main" valign="top" nowrap="nowrap"><?php echo TABLE_HEADING_COMMENTS . ':'; ?></td>
        <td class="main">
          <?php echo TEXT_MAIL_CONTENT_INFO;?>
          <table><tr class="smalltext"><td><font
          color="red">※</font>&nbsp;<?php echo TEXT_ORDER_COPY;?></td><td>
          <?php echo TEXT_ORDER_LOGIN;?></td></tr></table>
          <br>
          <?php echo tep_draw_textarea_field('comments', 'hard', '74', '30', $select_text, 'style="font-family:monospace;font-size:12px; width:400px;" id="c_comments"'); ?>
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
                <td class="main"><?php echo tep_draw_checkbox_field('notify', '',
                    !$select_nomail, '', 'id="notify"'); ?><?php echo
                TEXT_ORDER_SEND_MAIL;?></td>
                <td class="main" align="right"><?php echo
                tep_draw_checkbox_field('notify_comments', '', !$select_nomail, '',
                    'id="notify_comments"'); ?><?php echo TEXT_ORDER_STATUS;?></td>
              </tr>
              <tr>
                <td class="main" colspan="2"><br><font color="#FF0000;"><?php
                  foreach($orders_statuses as $o_status){
                    echo '<input type="hidden" id="confrim_mail_title_'.$o_status['id'].
                      '" value="'.$mo[$o_status['id']][0].'">';
                  }
                echo TEXT_ORDER_HAS_ERROR;?></font><br><br>
                <?php echo tep_html_element_button(IMAGE_UPDATE,'onclick="check_list_preorder_submit();"');?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
</td><td valign="top" align="right">
<div id='select_question' style="display:none" >
      <table width="100%">
       <tr>
           <td align='right'>
               <select id='oa_dynamic_groups'  ></select>
           </td>
       </tr>
       </table>
      <table id='oa_dynamic_group_item'  width="100%">

       </table>
      <table width="100%">
       <tr><td align='right'><button id="oa_dynamic_submit" ><?php echo IMAGE_SAVE;?></button></td></tr>
       </table>
</div>
</td></tr></table>
      </form>
      <!-- display add end-->
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PREORDERS); ?></td>
            <td class="smallText" align="right">
			<div class="td_box"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></div></td>
          </tr>
        </table>
      </td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');

      $contents = array('form' => tep_draw_form('orders', FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . tep_get_fullname(isset($cInfo->customers_firstname)?$cInfo->customers_firstname:'', isset($cInfo->customers_lastname)?$cInfo->customers_lastname:'') . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']<br>' . tep_datetime_short($oInfo->date_purchased) . '</b>');

        if ($ocertify->npermission == 15) {
          if ($oInfo->is_active) {
            $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_html_element_button(IMAGE_DETAILS) . '</a> <a href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') .  '">' . tep_html_element_button(IMAGE_DELETE) . '</a>');
          } else {
            $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') .  '">' . tep_html_element_button(IMAGE_DELETE) . '</a>');
          }
        } else {
          if ($oInfo->is_active) {
            $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_html_element_button(IMAGE_DETAILS) . '</a>');
          } 
        }
        $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
        if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
        $contents[] = array('text' => tep_show_preorders_products_info($oInfo->orders_id)); 
      }
      break;
  }
?>
    </tr>
  </table>
      </td>
    </tr>
<?php } ?>

    </table></div></td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<embed id="warn_sound" src="images/presound.mp3" type="application/x-ms-wmp" width="0" height="0" loop="false" autostart="false"></embed>
<!-- footer_eof -->
<br>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
