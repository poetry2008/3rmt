<?php
require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies          = new currencies(2);
  
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
if ($_POST['orders_id'] && $_POST['orders_comment']) {
  // update orders_comment
  tep_db_perform('orders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  echo $_POST['orders_comment'];
} else if ($_GET['action'] == 'paydate') {
  echo date('Y年n月j日',strtotime(tep_get_pay_day()));
} else if ($_GET['action'] == 'set_quantity' && $_GET['products_id'] && $_GET['count']) {
  $p  = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$_GET['products_id']."'"));
  $rp = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$p['relate_products_id']."'"));
  
  if ($rp) {
    //print_r($rp);
    $q  = $rp['products_real_quantity'] + (int)$_GET['count'];
    tep_db_query("update ".TABLE_PRODUCTS." set products_real_quantity='".$q."' where products_id='".$p['relate_products_id']."'");
    //print_r("update ".TABLE_PRODUCTS." set products_real_quantity='".$q."' where products_id='".$p['relate_products_id']."'"); 
  }
} else if ($_GET['orders_id'] && $_POST['orders_credit']) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$_GET['orders_id']."'"));
  tep_db_perform('customers', array('customers_fax' => $_POST['orders_credit']), 'update', "customers_id='".$order['customers_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_important_flag'])) {
  // 重要
  tep_db_perform('orders', array('orders_important_flag' => $_GET['orders_important_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_care_flag'])) {
  // 取り扱い注意
  tep_db_perform('orders', array('orders_care_flag' => $_GET['orders_care_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_wait_flag'])) {
  // 取引待ち
  tep_db_perform('orders', array('orders_wait_flag' => $_GET['orders_wait_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
}  else if ($_GET['orders_id'] && isset($_GET['orders_inputed_flag'])) {
  // 入力済み
  tep_db_perform('orders', array('orders_inputed_flag' => $_GET['orders_inputed_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['action'] == 'delete' && $_GET['orders_id'] && $_GET['computers_id']) {
  tep_db_query("delete from ".TABLE_ORDERS_TO_COMPUTERS." where orders_id='".$_GET['orders_id']."' and computers_id='".(int)$_GET['computers_id']."'");
} else if ($_GET['action'] == 'insert' && $_GET['orders_id'] && $_GET['computers_id']) {
  tep_db_query("insert into ".TABLE_ORDERS_TO_COMPUTERS." (`orders_id`,`computers_id`) VALUES('".$_GET['orders_id']."','".(int)$_GET['computers_id']."')");
} else if ($_GET['action'] == 'last_customer_action') {
  echo LAST_CUSTOMER_ACTION;
} else if (isset($_GET['orders_id']) && isset($_GET['work'])) {
  // A, B, C
  $exists_order_work_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$_GET['orders_id']."'"); 
  $exists_order_work_res = tep_db_fetch_array($exists_order_work_raw);
  if ($exists_order_work_res) {
    if ($exists_order_work_res['orders_work'] == $_GET['work']) {
      tep_db_query("update `".TABLE_ORDERS."` set `orders_work` = NULL where `orders_id` = '".$_GET['orders_id']."'"); 
      print('failed');
    } else {
      tep_db_perform('orders', array('orders_work' => $_GET['work']), 'update', "orders_id='".$_GET['orders_id']."'") && print('success');
    }
  } else {
    tep_db_perform('orders', array('orders_work' => $_GET['work']), 'update', "orders_id='".$_GET['orders_id']."'") && print('success');
  }
} else if ($_GET['action'] == 'get_new_orders' && $_GET['prev_customer_action']) {
  // ajax在订单列表的顶部插入新订单，如果订单结构发生改变此处需要和orders.php同步修改
  $orders_query = tep_db_query("
    select * from ".TABLE_ORDERS."
    where date_purchased > '".$_GET['prev_customer_action']."'
  ");

  while ($orders = tep_db_fetch_array($orders_query)) {
      if (!isset($orders['site_id'])) {
        $orders = tep_db_fetch_array(tep_db_query("
          select *
          from ".TABLE_ORDERS." o
          where orders_id='".$orders['orders_id']."'
        "));
      }
      $allorders[] = $orders;
      //if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
      //  $oInfo = new objectInfo($orders);
      //}

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
  

  echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="showOrdersInfo(\''.tep_get_orders_products_string($orders).'\');this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo();this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'page')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
  if ($ocertify->npermission) {
    ?>
        <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent">
          <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this)">
        </td>
<?php 
  }
?>
        <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo tep_get_site_romaji_by_id($orders['site_id']);?></td>
        <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
          <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <a href="<?php echo tep_href_link('orders.php', 'cEmail=' . tep_output_string_protected($orders['customers_email_address']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', '過去の注文');?></a>
<?php if ($ocertify->npermission) {?>
          &nbsp;<a href="<?php echo tep_href_link('customers.php', 'page=1&cID=' . tep_output_string_protected($orders['customers_id']) . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'arrow_r_red.gif', '顧客情報');?></a>&nbsp;&nbsp;
<?php }?>
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  <font color="#999">
  <?php }?>
          <b><?php echo tep_output_string_protected($orders['customers_name']);?></b>
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  </font>
  <?php }?>
    </td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
      <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
      <font color="#999"><?php echo strip_tags(tep_get_ot_total_by_orders_id($orders['orders_id'], true));?></font>
      <?php } else { ?>
      <?php echo strip_tags(tep_get_ot_total_by_orders_id($orders['orders_id'], true));?>
      <?php }?>
    </td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $next_mark; ?><font color="<?php echo !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>"><?php echo tep_datetime_short($orders['torihiki_date']); ?></font></td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES . 'icon_hand.gif', '取引待ち'); } else { echo '&nbsp;'; } ?></td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">　</td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['orders_status_name']; ?></font></td>
    <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="right"><?php 
      echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID','page')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;</td>
    </tr>
<?php 
  }
} else if ($_GET['action'] == 'save_questions' && $_GET['orders_id']) {
  // 保存订单问答
  
  //print_r($_POST);
  isset($_POST['q_1_1']) && $questions_arr['q_1_1'] = intval($_POST['q_1_1']);
  isset($_POST['q_1_2']) && $questions_arr['q_1_2'] = intval($_POST['q_1_2']);
  isset($_POST['q_2_1']) && $questions_arr['q_2_1'] = intval($_POST['q_2_1']);
  isset($_POST['q_2_2']) && $questions_arr['q_2_2'] = intval($_POST['q_2_2']);
  isset($_POST['q_3_1']) && $questions_arr['q_3_1'] = intval($_POST['q_3_1']);
  ($_POST['q_3_2_m'] && $_POST['q_3_2_d']) && $questions_arr['q_3_2'] = date('y') . '-' . intval($_POST['q_3_2_m']) . '-' . intval($_POST['q_3_2_d']);
  isset($_POST['q_3_3']) && $questions_arr['q_3_3'] = intval($_POST['q_3_3']);
  isset($_POST['q_3_4']) && $questions_arr['q_3_4'] = intval($_POST['q_3_4']);
  isset($_POST['q_4_1']) && $questions_arr['q_4_1'] = $_POST['q_4_1'];
  isset($_POST['q_4_2']) && $questions_arr['q_4_2'] = intval($_POST['q_4_2']);
  ($_POST['q_4_3_m'] && $_POST['q_4_3_d']) && $questions_arr['q_4_3'] = date('y') . '-' . intval($_POST['q_4_3_m']) . '-' . intval($_POST['q_4_3_d']);
  isset($_POST['q_5_1']) && $questions_arr['q_5_1'] = intval($_POST['q_5_1']);
  ($_POST['q_5_2_m'] && $_POST['q_5_2_d']) && $questions_arr['q_5_2'] = date('y') . '-' . intval($_POST['q_5_2_m']) . '-' . intval($_POST['q_5_2_d']);
  isset($_POST['q_6_1']) && $questions_arr['q_6_1'] = intval($_POST['q_6_1']);
  isset($_POST['q_6_2']) && $questions_arr['q_6_2'] = intval($_POST['q_6_2']);
  isset($_POST['q_7_1']) && $questions_arr['q_7_1'] = $_POST['q_7_1'];
  isset($_POST['q_7_2']) && $questions_arr['q_7_2'] = intval($_POST['q_7_2']);
  isset($_POST['q_8_1']) && $questions_arr['q_8_1'] = $_POST['q_8_1'];
  isset($_POST['q_9_1']) && $questions_arr['q_9_1'] = intval($_POST['q_9_1']);
  ($_POST['q_9_2_m'] && $_POST['q_9_2_d']) && $questions_arr['q_9_2'] = date('y') . '-' . intval($_POST['q_9_2_m']) . '-' . intval($_POST['q_9_2_d']);
  isset($_POST['q_10_1']) && $questions_arr['q_10_1'] = intval($_POST['q_10_1']);
  isset($_POST['q_10_2']) && $questions_arr['q_10_2'] = intval($_POST['q_10_2']);
  //isset($_POST['q_11_1']) && $questions_arr['q_11_1'] = intval($_POST['q_11_1']);
  //isset($_POST['q_11_2']) && $questions_arr['q_11_2'] = $_POST['q_11_2'];
  isset($_POST['q_11_3']) && $questions_arr['q_11_3'] = intval($_POST['q_11_3']);
  isset($_POST['q_11_4']) && $questions_arr['q_11_4'] = intval($_POST['q_11_4']);
  isset($_POST['q_11_5']) && $questions_arr['q_11_5'] = intval($_POST['q_11_5']);
  isset($_POST['q_11_6']) && $questions_arr['q_11_6'] = intval($_POST['q_11_6']);
  isset($_POST['q_11_7']) && $questions_arr['q_11_7'] = intval($_POST['q_11_7']);
  isset($_POST['q_11_8']) && $questions_arr['q_11_8'] = intval($_POST['q_11_8']);
  //isset($_POST['q_11_9']) && $questions_arr['q_11_9']   = $_POST['q_11_9'];
  //isset($_POST['q_11_10']) && $questions_arr['q_11_10'] = $_POST['q_11_10'];
  isset($_POST['q_11_11']) && $questions_arr['q_11_11'] = intval($_POST['q_11_11']);
  isset($_POST['q_11_12']) && $questions_arr['q_11_12'] = intval($_POST['q_11_12']);
  ($_POST['q_11_13_m'] && $_POST['q_11_13_d']) && $questions_arr['q_11_13'] = date('y') . '-' . intval($_POST['q_11_13_m']) . '-' . intval($_POST['q_11_13_d']);
  isset($_POST['q_11_14']) && $questions_arr['q_11_14'] = intval($_POST['q_11_14']);
  isset($_POST['q_11_15']) && $questions_arr['q_11_15'] = intval($_POST['q_11_15']);
  isset($_POST['q_11_16']) && $questions_arr['q_11_16'] = intval($_POST['q_11_16']);
  isset($_POST['q_12_1']) && $questions_arr['q_12_1'] = intval($_POST['q_12_1']);
  isset($_POST['q_12_2']) && $questions_arr['q_12_2'] = intval($_POST['q_12_2']);
  isset($_POST['q_13_1']) && $questions_arr['q_13_1'] = intval($_POST['q_13_1']);
  ($_POST['q_13_2_m'] && $_POST['q_13_2_d']) && $questions_arr['q_13_2'] = date('y') . '-' . intval($_POST['q_13_2_m']) . '-' . intval($_POST['q_13_2_d']);
  isset($_POST['q_14_1']) && $questions_arr['q_14_1'] = intval($_POST['q_14_1']);
  isset($_POST['q_14_2']) && $questions_arr['q_14_2'] = $_POST['q_14_2'];
  isset($_POST['q_15_1']) && $questions_arr['q_15_1'] = intval($_POST['q_15_1']);
  ($_POST['q_15_2_m'] && $_POST['q_15_2_d']) && $questions_arr['q_15_2'] = date('y') . '-' . intval($_POST['q_15_2_m']) . '-' . intval($_POST['q_15_2_d']);
  isset($_POST['q_15_3']) && $questions_arr['q_15_3'] = intval($_POST['q_15_3']);
  isset($_POST['q_15_4']) && $questions_arr['q_15_4'] = intval($_POST['q_15_4']);
  isset($_POST['q_15_5']) && $questions_arr['q_15_5'] = intval($_POST['q_15_5']);
  //($_POST['q_15_6_m'] && $_POST['q_15_6_d']) && $questions_arr['q_15_6'] = date('y') . '-' . intval($_POST['q_15_6_m']) . '-' . intval($_POST['q_15_6_d']);
  isset($_POST['q_15_7']) && $questions_arr['q_15_7'] = $_POST['q_15_7'];
  isset($_POST['q_15_8']) && $questions_arr['q_15_8'] = intval($_POST['q_15_8']);
  isset($_POST['q_16_1']) && $questions_arr['q_16_1'] = $_POST['q_16_1'];
  isset($_POST['q_16_2']) && $questions_arr['q_16_2'] = intval($_POST['q_16_2']);
  isset($_POST['q_17_1']) && $questions_arr['q_17_1'] = $_POST['q_17_1'];
  isset($_POST['q_17_2']) && $questions_arr['q_17_2'] = intval($_POST['q_17_2']);
  isset($_POST['questions_type']) && $questions_arr['orders_questions_type'] = intval($_POST['questions_type']);
  
  //print_r($questions_arr);
  $q = tep_db_fetch_array(tep_db_query("select * from orders_questions where orders_id='".$_GET['orders_id']."'"));
  if (tep_db_num_rows(tep_db_query("select orders_id from orders_questions where orders_id='".$_GET['orders_id']."'"))) {
    // q_8_1只能输入一次
    if ($q['q_8_1']) {
      unset($questions_arr['q_8_1']);
    }
    tep_db_perform('orders_questions', $questions_arr, 'update', "orders_id='".$_GET['orders_id']."'");
  } else {
    $questions_arr['orders_id'] = $_GET['orders_id'];
    tep_db_perform('orders_questions', $questions_arr);
  }
  echo "<pre>";
  print_r($questions_arr);
  //print_r(tep_db_fetch_array(tep_db_query("select * from orders_questions where orders_id='".$_GET['orders_id']."'")));
  //relate_product[<?php echo $op['products_id'];
  if ($_POST['relate_product'] || $_POST['offset']) {
    if($_POST['relate_product']) {
    foreach($_POST['relate_product'] as $pid => $rp){
      $of = intval($_POST['offset'][$pid]);
      if (tep_db_num_rows(tep_db_query("select * from orders_questions_products where orders_id='".$_GET['orders_id']."' and products_id='".$pid."'"))) {
        tep_db_perform('orders_questions_products',array('checked'=>$_POST['relate_product'][$pid], 'offset' => $_POST['offset'][$pid]), 'update', "orders_id='".$_GET['orders_id']."' and products_id='".$pid."'");
      } else {
        tep_db_perform('orders_questions_products',array('orders_id'=>$_GET['orders_id'], 'products_id'=>$pid, 'checked'=>$_POST['relate_product'][$pid], 'offset' => $_POST['offset'][$pid]));
      }
    }
    } else {
      tep_db_query("update orders_questions_products set checked='0' where orders_id='".$_GET['orders_id']."'");
    }
  }
  if (isset($questions_arr['q_8_1']) && $questions_arr['q_8_1']) {
      orders_updated($_GET['orders_id']);
      orders_wait_flag($_GET['orders_id']);
  }
  
} else if ($_GET['action'] == 'clean_option' && $_GET['questions_no'] && $_GET['orders_id']) {
  // 清空选项
  // 清空只能为null,否则orders显示会错误
  switch ($_GET['questions_no']) {
    case 4:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => 'null',
        'q_'.$_GET['questions_no'].'_2' => '',
        'q_'.$_GET['questions_no'].'_3' => '0000-00-00',
      );
    break;
    case 5:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => '',
        'q_'.$_GET['questions_no'].'_2' => 'null'
      );
    break;
    case 7:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => 'null',
        'q_'.$_GET['questions_no'].'_2' => ''
      );
    break;
    case 13:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => '',
        'q_'.$_GET['questions_no'].'_2' => 'null'
      );
      break;
    case 14:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => '',
        'q_'.$_GET['questions_no'].'_2' => 'null'
      );
      break;
    case 16:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => 'null',
        'q_'.$_GET['questions_no'].'_2' => ''
      );
      break;
    case 1:
    case 2:
    case 6:
    case 9:
    case 10:
    case 12:
    case 17:
      $arr = array(
        'q_'.$_GET['questions_no'].'_1' => 'null',
        'q_'.$_GET['questions_no'].'_2' => 'null'
      );
      break;
    case 3:
      $arr = array(
        'q_3_1' => '',
        'q_3_2' => 'null',
        'q_3_3' => 'null',
        'q_3_4' => '',
      );
      break;
    case 8:
      $arr = array(
        'q_8_1' => 'null'
      );
      break;
    case 11:
      $arr = array(
        //'q_11_1' => '',
        //'q_11_2' => '',
        'q_11_3' => 'null',
        'q_11_4' => 'null',
        'q_11_5' => 'null',
        'q_11_6' => 'null',
        'q_11_7' => 'null',
        'q_11_8' => 'null',
        //'q_11_9' => '',
        //'q_11_10' => '',
        'q_11_11' => 'null',
        'q_11_12' => 'null',
        'q_11_13' => 'null',
        'q_11_14' => 'null',
        'q_11_15' => 'null',
        'q_11_16' => 'null'
      );
      break;
    case 15:
      $arr = array(
        'q_15_1' => '',
        'q_15_2' => 'null',
        'q_15_3' => 'null',
        'q_15_4' => 'null',
        'q_15_5' => 'null',
        //'q_15_6' => '',
        'q_15_7' => 'null',
        'q_15_8' => 'null'
      );
      break;
    case 'relate':
      tep_db_query("update orders_questions_products set checked='0' where orders_id='".$_GET['orders_id']."'");
      exit;
      break;
  }
  tep_db_perform('orders_questions', $arr, 'update', "orders_id='".$_GET['orders_id']."'");
}else if(isset($_POST['action'])&&$_POST['action'] == 'valedate'){

}else if(isset($_GET['action'])&&$_GET['action'] == 'getallpwd'){
  $sql = "select u.userid,u.rule,l.letter from ".
    TABLE_USERS." u , ".TABLE_LETTERS." l 
    where u.userid = l.userid and (l.letter != '' or l.letter != null)";
  if($ocertify->npermission == 15){
    $sql .= "";
  }else{
    $sql .= " and u.userid != '".$ocertify->auth_user."'";
  }
  $result = tep_db_query($sql);
  $arr =array();
  while($row = tep_db_fetch_array($result)){
      $pwd = $row['letter'].make_rand_pwd($row['rule']);
      $arr[] = $pwd;
  }
  $str = implode(',',$arr); 
  echo $str;
}else if(isset($_GET['action'])&&$_GET['action'] == 'getpercent'){
  if(isset($_POST['cid'])&&$_POST['cid']){
  $sql = "select sac.percent as percent from ".TABLE_PRODUCTS_TO_CATEGORIES." 
        p2c,set_auto_calc sac,".TABLE_ORDERS_PRODUCTS." op 
        where op.orders_products_id = '".$_POST['cid']."' 
        and p2c.products_id = op.products_id   
        and p2c.categories_id = sac.parent_id limit 1";
  }else if(isset($_POST['pid'])&&$_POST['pid']){
  $sql = "select sac.percent as percent from ".TABLE_PRODUCTS_TO_CATEGORIES." 
        p2c,set_auto_calc sac  
        where p2c.products_id = '".$_POST['pid']."'    
        and p2c.categories_id = sac.parent_id limit 1";
  }
  $result = tep_db_query($sql);
  if($row = tep_db_fetch_array($result)){
    if($row['percent']!=''){
      echo $row['percent'];
    }else{
      echo 0;
    }
  }else{
    echo 0;
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'c_is_set_romaji'){
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql =  "select * from ".TABLE_FAQ_CATEGORIES." fc,
              ".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
              fcd where fc.id=fcd.faq_category_id and
              fc.parent_id='".$_POST['pid']."'      and
              fcd.romaji='".$_POST['romaji']."' and
              (fcd.site_id='".$site_id."' or fcd.site_id = '0' )"; 
  if(isset($_POST['cid'])&&$_POST['cid']!=''){
     $sql .= " and fc.id != '".$_POST['cid']."'";
  }
  $sql .= " order by fcd.site_id DESC";
  if(tep_db_num_rows(tep_db_query($sql))){
    echo 'true';
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'q_is_set_romaji'){
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql = "select * from  
              ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
              ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
              where fqd.faq_question_id=fq2c.faq_question_id and
              fq2c.faq_category_id='".$_POST['cid']."'      and
              fqd.romaji='".$_POST['romaji']."' and
              (fqd.site_id='".$site_id."' or fqd.site_id = '0' )";
  if(isset($_POST['qid'])&&$_POST['qid']!=''){
    $sql .= " and fqd.faq_question_id != '".$_POST['qid']."'"; 
  }
  $sql .= " order by fqd.site_id DESC";
  if(tep_db_num_rows(tep_db_query($sql))){
    echo 'true';
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'check_romaji'){
  $romaji = tep_db_prepare_input($_POST['romaji']);
  if(preg_match('/[^\x{4e00}-\x{9fa5}\x{3130}-\x{318F}\x{0800}-\x{4e00}a-zA-Z0-9-]/u',$romaji)){
  $new_romaji =
   preg_replace('/[^\x{4e00}-\x{9fa5}\x{3130}-\x{318F}\x{0800}-\x{4e00}a-zA-Z0-9-]/u','-',$romaji);
    if(preg_match('/\s|　|「|【|「|】|」/',$new_romaji)){
      $new_romaji = preg_replace('/\s|　|「|【|「|】|」/','-',$new_romaji);
    }
    echo $new_romaji;
  }else{
    if(preg_match('/\s|　|「|【|「|】|」/',$romaji)){
      $new_romaji = preg_replace('/\s|　|「|【|「|】|」/','-',$romaji);
      echo $new_romaji; 
    }else{
      echo '';
    }
  }
}
