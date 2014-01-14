<?php
require('includes/application_top.php');
//one time pwd 
$http_referer = $_SERVER['HTTP_REFERER'];
$http_referer_arr = explode('?',$_SERVER['HTTP_REFERER']);
$http_referer_arr = explode('admin',$http_referer_arr[0]);
$request_page_name = '/admin'.$http_referer_arr[1];
$request_one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$request_page_name."'";
$request_one_time_query = tep_db_query($request_one_time_sql);
$request_one_time_arr = array();
$request_one_time_flag = false; 
while($request_one_time_row = tep_db_fetch_array($request_one_time_query)){
  $request_one_time_arr[] = $request_one_time_row['check_value'];
  $request_one_time_flag = true; 
}

if ($ocertify->npermission == 31) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    if (!isset($_POST['split_param'])) {
      if (!((isset($_POST['orders_id']) && (isset($_POST['orders_comment'])||$_POST['orders_comment_flag']=='true')) || (isset($_GET['orders_id']) && isset($_POST['orders_credit'])))) {
        forward401();
      } 
    }
  }
} else {
  if (count($request_one_time_arr) == 1 && $request_one_time_arr[0] == 'admin' && $ocertify->npermission != 15){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      if (!isset($_POST['split_param'])) {
        forward401();
      }
    }
  }
  
  if (!$request_one_time_flag && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      if (!isset($_POST['split_param'])) {
        forward401();
      }
    }
  }

  if (!in_array('onetime', $request_one_time_arr) && $ocertify->npermission != 15) {
    if (!(in_array('chief', $request_one_time_arr) && in_array('staff', $request_one_time_arr))) {
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)){
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          if (!isset($_POST['split_param'])) {
            forward401();
          }
        }
      }
      if ($ocertify->npermission == 10 && in_array('staff', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          if (!isset($_POST['split_param'])) {
            forward401();
          }
        }
      }
    }
  }
}
//end one time pwd

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
if (isset($_POST['orders_id']) && (isset($_POST['orders_comment'])||$_POST['orders_comment_flag']=='true')) {
/*------------------------------------------------
 功能: 更新订单评论
 参数: $_POST['orders_comment'] 获取orders_comment值
 -----------------------------------------------*/
  // update orders_comment
  tep_db_perform('orders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
} else if ($_GET['action'] == 'paydate') {
/*-----------------------------------------------
 功能: 支付日期 
 参数: 无 
 ----------------------------------------------*/
  echo date('Y'.YEAR_TEXT.'n'.MONTH_TEXT.'j'.DAY_TEXT,strtotime(tep_get_pay_day()));
} else if ($_GET['action'] == 'set_quantity' && $_GET['products_id'] && $_GET['count']) {
/*---------------------------------------------
 功能: 设置数量
 参数: $_GET['products_id'] 产品ID
 参数: $_GET['count']  数量
 --------------------------------------------*/
  $p  = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$_GET['products_id']."'"));
  $rp = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$p['relate_products_id']."'"));

  if ($rp) {
    $q  = $rp['products_real_quantity'] + (int)$_GET['count'];
    tep_db_query("update ".TABLE_PRODUCTS." set products_real_quantity='".$q."' where products_id='".$p['relate_products_id']."'");
  }
} else if (isset($_GET['orders_id']) && isset($_POST['orders_credit'])) {
/*--------------------------------------------
 功能: 订单信用
 参数: $_GET['orders_id'] 订单ID
 -------------------------------------------*/
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$_GET['orders_id']."'"));
  tep_db_perform('customers', array('customers_fax' => $_POST['orders_credit']), 'update', "customers_id='".$order['customers_id']."'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
} else if ($_GET['orders_id'] && isset($_GET['orders_important_flag'])) {
/*-------------------------------------------
 功能: 更新订单重要标志  
 参数: $_GET['orders_id'] 订单ID
 ------------------------------------------*/
  // 重要
  tep_db_perform('orders', array('orders_important_flag' => $_GET['orders_important_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_care_flag'])) {
/*------------------------------------------
 功能: 更新订单服务标志
 参数: $_GET['orders_id'] 订单ID
 -----------------------------------------*/
  // 注意处理方式
  tep_db_perform('orders', array('orders_care_flag' => $_GET['orders_care_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_wait_flag'])) {
/*------------------------------------------
 功能: 更新订单等待标志
 参数: $_GET['orders_id'] 订单ID
 参数: $_GET['orders_wait_flag'] 订单标志值
 -----------------------------------------*/
  // 交易等待
  tep_db_perform('orders', array('orders_wait_flag' => $_GET['orders_wait_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
}  else if ($_GET['orders_id'] && isset($_GET['orders_inputed_flag'])) {
/*------------------------------------------
 功能: 更新订单填写会员标志
 参数: $_GET['orders_id'] 订单ID
 参数: $_GET['orders_inputed_flag'] 订单会员标志值
 -----------------------------------------*/
  // 输入完成
  tep_db_perform('orders', array('orders_inputed_flag' => $_GET['orders_inputed_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['action'] == 'delete' && $_GET['orders_id'] && $_GET['buttons_id']) {
/*-----------------------------------------
 功能: 删除订单和按钮的关联,及关闭警告提示 
 参数: $_GET['orders_id']  订单ID
 参数: $_GET['buttons_id'] 按钮ID值
 ----------------------------------------*/
  tep_db_query("delete from ".TABLE_ORDERS_TO_BUTTONS." where orders_id='".$_GET['orders_id']."' and buttons_id='".(int)$_GET['buttons_id']."'");

  //删除警告提示
  $alarm_name_query = tep_db_query("select buttons_name from ". TABLE_BUTTONS ." where buttons_id='".(int)$_GET['buttons_id']."'");
  $alarm_name_array = tep_db_fetch_array($alarm_name_query);
  tep_db_free_result($alarm_name_query);

  $alarm_id_query = tep_db_query("select alarm_id from ". TABLE_ALARM ." where orders_id='".$_GET['orders_id']."' and title='".$alarm_name_array['buttons_name']."' and alarm_flag='1' and orders_flag='1'");
  $alarm_id_array = tep_db_fetch_array($alarm_id_query);
  tep_db_free_result($alarm_id_query);
 
  //获取警告过期的天数
  $user_info = tep_get_user_info($ocertify->auth_user);
  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $alarm_date = date('Y-m-d H:i:00',strtotime("+".$alarm_day." days")); 
  $sql_data_array = array(
        'title' => $alarm_name_array['buttons_name'],
        'orders_id' => $_GET['orders_id'], 
        'alarm_date' => $alarm_date,
        'adminuser' => $user_info['name'],
        'created_at' => 'now()',
        'alarm_flag' => '1',
        'alarm_show'=> '0'
        );  
  tep_db_perform(TABLE_ALARM, $sql_data_array);         
        
  $alarm_id = tep_db_insert_id();
           
  $sql_data_array = array(
        'type' => 0,
        'title' => $alarm_name_array['buttons_name'],
        'set_time' => $alarm_date,
        'from_notice' => $alarm_id,
        'user' => $user_info['name'],
        'created_at' => 'now()'
        ); 
  tep_db_perform(TABLE_NOTICE, $sql_data_array); 
} else if ($_GET['action'] == 'insert' && $_GET['orders_id'] && $_GET['buttons_id']) {
/*----------------------------------------
 功能: 添加订单和按钮关联,及添加警告提示 
 参数: $_GET['orders_id']  订单ID
 参数: $_GET['buttons_id'] 按钮ID值
 ---------------------------------------*/
  tep_db_query("insert into ".TABLE_ORDERS_TO_BUTTONS." (`orders_id`,`buttons_id`) VALUES('".$_GET['orders_id']."','".(int)$_GET['buttons_id']."')");
 
  //添加警告提示
  $alarm_name_query = tep_db_query("select buttons_name from ". TABLE_BUTTONS ." where buttons_id='".(int)$_GET['buttons_id']."'");
  $alarm_name_array = tep_db_fetch_array($alarm_name_query);
  tep_db_free_result($alarm_name_query);

  $alarm_id_query = tep_db_query("select alarm_id from ". TABLE_ALARM ." where orders_id='".$_GET['orders_id']."' and title='".$alarm_name_array['buttons_name']."' and alarm_flag='1' and orders_flag='1'");
  $alarm_id_num = tep_db_num_rows($alarm_id_query);
  tep_db_free_result($alarm_id_query);
  $user_info = tep_get_user_info($ocertify->auth_user);

  //获取警告过期的天数
  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $alarm_date = date('Y-m-d H:i:00',strtotime("+".$alarm_day." days")); 
  $sql_data_array = array(
      'title' => $alarm_name_array['buttons_name'],
      'orders_id' => $_GET['orders_id'], 
      'alarm_date' => $alarm_date,
      'adminuser' => $user_info['name'],
      'created_at' => 'now()',
      'alarm_flag' => '1',
      'alarm_show'=> '1'
      );  
  tep_db_perform(TABLE_ALARM, $sql_data_array);         
        
  $alarm_id = tep_db_insert_id();
           
  $sql_data_array = array(
      'type' => 0,
      'title' => $alarm_name_array['buttons_name'],
      'set_time' => $alarm_date,
      'from_notice' => $alarm_id,
      'user' => $user_info['name'],
      'created_at' => 'now()'
      ); 
  tep_db_perform(TABLE_NOTICE, $sql_data_array);  
} else if ($_GET['action'] == 'last_customer_action') {
/*---------------------------------------
 功能: 最后顾客操作
 参数: 无
 --------------------------------------*/
  echo LAST_CUSTOMER_ACTION;
} else if (isset($_GET['orders_id']) && isset($_GET['work'])) {
/*--------------------------------------
 功能: 更新订单表里的orders_work字段
 参数: $_GET['orders_id'] 订单ID
 -------------------------------------*/
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
  if (isset($_GET['type'])) {
    if (tep_db_num_rows($orders_query)) {
      echo '1'; 
    } else {
      echo '0'; 
    }
    exit; 
  }
  //获取订单状态标记的过期警告数组
  $orders_expired_array = array();
  $orders_expired_array = check_orders_transaction_expired();
  while ($orders = tep_db_fetch_array($orders_query)) {
    if (!isset($orders['site_id'])) {
      $orders = tep_db_fetch_array(tep_db_query("
						select *
						from ".TABLE_ORDERS." o
						where orders_id='".$orders['orders_id']."'
						"));
    }
    $allorders[] = $orders;

    //过期订单的警告提示
    $expired_orders = '';
    $orders_transaction_time = date('YmdHi',strtotime($orders['torihiki_date_end'])); 
    $orders_today_time = date('YmdHi');
    if($orders_today_time > $orders_transaction_time && $orders_expired_array[$orders['orders_status']] == 1){

      $expired_orders = tep_image(DIR_WS_ICONS . 'blink_exclamation.gif', TEXT_TRANSACTION_EXPIRED);
    }

    //如果是今天的交易的话，显示红色
    $trade_array = getdate(strtotime(tep_datetime_short($orders['torihiki_date_end'])));
    $today_array = getdate();
    if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
      $today_color = 'red';
      $trade_minutes = $trade_array["minutes"] < 10 ? '0'.$trade_array["minutes"] : $trade_array["minutes"];
      $today_minutes = $today_array["minutes"] < 10 ? '0'.$today_array["minutes"] : $today_array["minutes"];
      if ($trade_array["hours"].$trade_minutes >= $today_array["hours"].$today_minutes) {
        $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif', NEXT_ORDER_TEXT); //标记下个订单
      } else {
        $next_mark = '';
      }
    } else {
        $today_color = 'black';
        $next_mark = '';
    }


    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow"
			onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
			onmouseout="this.className=\'dataTableRow\'">' .      "\n";
    if ($ocertify->npermission) {
      ?>
      <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent">
        <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this)">
        </td>
        <?php 
		}
    ?>
    <td style="border-bottom:1px solid #000000;background-color: darkred;"
       class="dataTableContent" onClick="chg_td_color(<?php echo
			$orders['orders_id']; ?>); window.location.href='<?php echo
			tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';"><?php echo tep_get_site_romaji_by_id($orders['site_id']);?></td>
                                                                                                                        <td style="border-bottom:1px solid #000000;background-color: darkred;"
                                                                                                                        class="dataTableContent" onClick="chg_td_color(<?php echo
			$orders['orders_id']; ?>); window.location.href='<?php echo
			tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';">
                                                                                                                        <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
    <a href="<?php echo tep_href_link('orders.php', 'cEmail=' .  tep_output_string_protected($orders['customers_email_address']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', BEFORE_ORDER_TEXT);?></a>
                                                                                                                                                                                                           <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
                                                                                                                                                                                                           <font color="#999">
      <?php }?>
      <a style="text-decoration:underline;" href="<?php echo tep_href_link('customers.php', 'search='.tep_output_string_protected($orders['customers_id']));?>"><?php echo tep_output_string_protected($orders['customers_name']);?></a>
                    <?php 
                    $customers_info_raw = tep_db_query("select pic_icon from ".TABLE_CUSTOMERS." where customers_id = '".$orders['customers_id']."'"); 
                    $customers_info_res = tep_db_fetch_array($customers_info_raw);
                    if ($customers_info_res) {
                      if (!empty($customers_info_res['pic_icon'])) {
                        if (file_exists(DIR_FS_DOCUMENT_ROOT.DIR_WS_IMAGES.'icon_list/'.$customers_info_res['pic_icon'])) {
                          $pic_icon_title_str = ''; 
                          $pic_icon_title_raw = tep_db_query("select pic_alt from ".TABLE_CUSTOMERS_PIC_LIST." where pic_name = '".$customers_info_res['pic_icon']."'"); 
                          $pic_icon_title_res = tep_db_fetch_array($pic_icon_title_raw); 
                          if ($pic_icon_title_res) {
                            $pic_icon_title_str = $pic_icon_title_res['pic_alt']; 
                          }
                          echo tep_image(DIR_WS_IMAGES.'icon_list/'.$customers_info_res['pic_icon'], $pic_icon_title_str); 
                        }
                      }
                    }
                    ?>
                                                                                 <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
                                                                                 </font>
      <?php }?>
      </td>
          <td style="border-bottom:1px solid #000000;background-color:
									darkred;" class="dataTableContent" align="right"
          onClick="chg_td_color(<?php echo $orders['orders_id']; ?>);
				window.location.href='<?php echo tep_href_link(FILENAME_ORDERS,
						tep_get_all_get_params(array('oID', 'action')) .
						'oID='.$orders['orders_id']);?>';">
          <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
          <font color="#999"><?php echo
            strip_tags(tep_get_ot_total_by_orders_id_no_abs($orders['orders_id'], true));?></font>
                                                                                                                    <?php } else { ?>
                                                                                                                    <?php
                                                                                                                      echo
                                                                                                                      strip_tags(tep_get_ot_total_by_orders_id_no_abs($orders['orders_id'], true));?>
                                                                                                                    <?php }?>
                                                                                                                    </td>
                                                                                                                        <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="right"
                                                                                                                        onClick="chg_td_color(<?php echo $orders['orders_id'];?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
                                                        'oID='.$orders['orders_id']);?>';"><?php echo $expired_orders.$next_mark; ?><font color="<?php echo !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>">
<?php echo tep_datetime_short_torihiki($orders['torihiki_date']);
$tmp_date_end = explode(' ',$orders['torihiki_date_end']); 
echo TEXT_TIME_LINK.$tmp_date_end[1]; 
?></font></td>
<td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="left">
<?php
  $read_flag_str_array = explode('|||',$orders['read_flag']);
  $user_info = tep_get_user_info($ocertify->auth_user);
  if($orders['read_flag'] == ''){
    echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$user_info['name'].'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>'; 
  }else{

    if(in_array($user_info['name'],$read_flag_str_array)){

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$user_info['name'].'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif"></a>';
    }else{

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$user_info['name'].'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>';
    }
  }
?>
</td>
                                                                                                                                                                                                                                                                                             <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="left"
                                                                                                                                                                                                                                                                                             onClick="chg_td_color(<?php echo $orders['orders_id'];?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><?php if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES . 'icon_hand.gif', RIGHT_ORDER_INFO_TRANS_WAIT); } else { echo '&nbsp;'; } ?></td>
    <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="left"
                                                                                                                                                                                                     onClick="chg_td_color(<?php echo $orders['orders_id']; ?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
                                                                                                                                                   <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="center"
                                                                                                                                                   onClick="chg_td_color(<?php echo $orders['orders_id']; ?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>
                                                                                                                                                            <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="center"
                                                                                                                                                            onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)
					window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';">　</td>
                                                                                                                                                            <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="right"
                                                                                                                                                            onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)
					window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><font color="<?php echo $today_color; ?>"><?php echo $orders['orders_status_name']; ?></font></td>
                                                                                                                                                         <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="right"> <?php 
                                                                                                                                                         echo '<a href="javascript:void(0);" onclick="showOrdersInfo(\''.$orders['orders_id'].'\', this, 1, \''.urlencode(tep_get_all_get_params(array('oID', 'action'))).'\')">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;</td>
    </tr>
        <?php 
        }
}  else if(isset($_GET['action'])&&$_GET['action'] == 'getallpwd'){
/*--------------------------------------------
 功能: 获得所有密码 
 参数: $_POST['current_page_name'] 当前页面
 -------------------------------------------*/
  $msg_array = array();
  $msg_array['is_popup'] = 1; 
  $msg_array['pwd_list'] = ''; 
  $one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$_POST['current_page_name']."'";
  $one_time_query = tep_db_query($one_time_sql);
  $one_time_arr = array();
  while($one_time_row = tep_db_fetch_array($one_time_query)){
    $one_time_arr[] = $one_time_row['check_value'];
  }
  if ($ocertify->npermission == 31) {
    $msg_array['is_popup'] = 0;
    $sql = "select u.userid,u.rule,l.letter from ".  TABLE_USERS." u , ".TABLE_LETTERS." l where u.userid = l.userid and (l.letter != '' or l.letter != null)";
    $result = tep_db_query($sql);
    $arr =array();
    while($row = tep_db_fetch_array($result)){
      $pwd = $row['letter'].make_rand_pwd($row['rule']);
      $arr[] = $pwd;
    }
    $msg_array['pwd_list'] = implode(',', $arr); 
  } else {
    if ($ocertify->npermission == 15 && (in_array('admin', $one_time_arr))) {
      $msg_array['is_popup'] = 0;
    }
    if ($ocertify->npermission == 10 && (in_array('chief', $one_time_arr))) {
      $msg_array['is_popup'] = 0;
    }
    if ($ocertify->npermission == 7 && (in_array('staff', $one_time_arr))) {
      $msg_array['is_popup'] = 0;
    }
    
    if (!empty($one_time_arr)) {
      $p_list_array = array(); 
      $p_list_array[] = 31; 
      foreach ($one_time_arr as $o_key => $o_value) {
        if ($o_value != 'onetime') {
          switch($o_value) {
            case 'admin':
              $p_list_array[] = 15; 
              break;
            case 'chief':
              $p_list_array[] = 10; 
              break;
            case 'staff':
              $p_list_array[] = 7; 
              break;
          }
        }
      }
      $sql = "select u.userid,u.rule,l.letter from ".  TABLE_USERS." u , ".TABLE_LETTERS." l,".TABLE_PERMISSIONS." p where u.userid = l.userid and (l.letter != '' or l.letter != null) and u.userid=p.userid and u.status = '1' ".(!empty($p_list_array)?" and p.permission in (".implode(',', $p_list_array).")":"and p.permission in (0)");
      
      $result = tep_db_query($sql);
      $arr =array();
      while($row = tep_db_fetch_array($result)){
        $pwd = $row['letter'].make_rand_pwd($row['rule']);
        $arr[] = $pwd;
      }
      $msg_array['pwd_list'] = implode(',',$arr); 
    }
  }
  echo implode('|||', $msg_array); 
}else if(isset($_GET['action'])&&$_GET['action'] == 'getpercent'){
/*-------------------------------------------
 功能: 得到百分比   
 参数: $_POST['cid'] 获取$_POST['cid']值 
 ------------------------------------------*/
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
}else if(isset($_GET['action'])&&$_GET['action'] == 'faq_c_is_set_romaji'){
/*-----------------------------------------
 功能: 常见问题解答罗马字 
 参数: $_POST['romaji']  罗马字值
 ----------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('11111111','&',$romaji);
  $romaji = str_replace('22222222','+',$romaji);
  $romaji = str_replace('33333333','/',$romaji);
  $romaji = str_replace('44444444','%',$romaji);
  $romaji = str_replace('55555555','#',$romaji);
  $romaji = str_replace('66666666','?',$romaji);
  $romaji = str_replace('77777777',' ',$romaji);
  $romaji = str_replace('88888888',",",$romaji);
  $romaji = str_replace('aaaaaaaa',"<",$romaji);
  $romaji = str_replace('bbbbbbbb',">",$romaji);
  $romaji = str_replace('cccccccc',"{",$romaji);
  $romaji = str_replace('dddddddd',"}",$romaji);
  $romaji = str_replace('eeeeeeee',"(",$romaji);
  $romaji = str_replace('ffffffff',")",$romaji);
  $romaji = str_replace('gggggggg',"|",$romaji);
  $romaji = str_replace('hhhhhhhh',"^",$romaji);
  $romaji = str_replace('iiiiiiii',"[",$romaji);
  $romaji = str_replace('jjjjjjjj',"]",$romaji);
  $romaji = str_replace('kkkkkkkk',"`",$romaji);
  $romaji = str_replace('llllllll',"~",$romaji);
  $romaji = str_replace('mmmmmmmm',"\\",$romaji);
  $romaji = str_replace('nnnnnnnn',"*",$romaji);
  $romaji = str_replace('oooooooo',"\"",$romaji);
  $romaji = str_replace('pppppppp',"=",$romaji);
  $romaji = str_replace('qqqqqqqq',"\'",$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql =  "select fcd.faq_category_id as row_id from ".TABLE_FAQ_CATEGORIES." fc,
		".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
			fcd where fc.id=fcd.faq_category_id and
			fc.parent_id='".$_POST['pid']."'      and
			fcd.romaji='".$romaji."' and
			(fcd.site_id='".$site_id."' or fcd.site_id = '0' )"; 
  if(isset($_POST['cid'])&&$_POST['cid']!=''){
    $sql .= " and fc.id != '".$_POST['cid']."'";
  }
  $sql .= " order by fcd.site_id DESC";
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($query)){
    if(isset($_POST['row_id'])&&$_POST['row_id']==$row['row_id']){
      echo 'false';
    }else{
      echo 'true';
    }
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'faq_q_is_set_romaji'){
/*------------------------------------------
 功能: 常见问题罗马字 
 参数: $_POST['romaji']  罗马字值
 -----------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('11111111','&',$romaji);
  $romaji = str_replace('22222222','+',$romaji);
  $romaji = str_replace('33333333','/',$romaji);
  $romaji = str_replace('44444444','%',$romaji);
  $romaji = str_replace('55555555','#',$romaji);
  $romaji = str_replace('66666666','?',$romaji);
  $romaji = str_replace('77777777',' ',$romaji);
  $romaji = str_replace('88888888',",",$romaji);
  $romaji = str_replace('aaaaaaaa',"<",$romaji);
  $romaji = str_replace('bbbbbbbb',">",$romaji);
  $romaji = str_replace('cccccccc',"{",$romaji);
  $romaji = str_replace('dddddddd',"}",$romaji);
  $romaji = str_replace('eeeeeeee',"(",$romaji);
  $romaji = str_replace('ffffffff',")",$romaji);
  $romaji = str_replace('gggggggg',"|",$romaji);
  $romaji = str_replace('hhhhhhhh',"^",$romaji);
  $romaji = str_replace('iiiiiiii',"[",$romaji);
  $romaji = str_replace('jjjjjjjj',"]",$romaji);
  $romaji = str_replace('kkkkkkkk',"`",$romaji);
  $romaji = str_replace('llllllll',"~",$romaji);
  $romaji = str_replace('mmmmmmmm',"\\",$romaji);
  $romaji = str_replace('nnnnnnnn',"*",$romaji);
  $romaji = str_replace('oooooooo',"\"",$romaji);
  $romaji = str_replace('pppppppp',"=",$romaji);
  $romaji = str_replace('qqqqqqqq',"\'",$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql = "select fqd.faq_question_id as row_id from  
		".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
		".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
			where fqd.faq_question_id=fq2c.faq_question_id and
			fq2c.faq_category_id='".$_POST['cid']."'      and
			fqd.romaji='".$romaji."' and
			(fqd.site_id='".$site_id."' or fqd.site_id = '0' )";
  if(isset($_POST['qid'])&&$_POST['qid']!=''){
    $sql .= " and fqd.faq_question_id != '".$_POST['qid']."'"; 
  }
  $sql .= " order by fqd.site_id DESC";
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($sql)){
    if(isset($_POST['row_id'])&&$_POST['row_id']==$row['row_id']){
      echo 'false';
    }else{
      echo 'true';
    }
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'check_romaji'){
/*-------------------------------------------
 功能: 检查罗马字 
 参数: $_POST['romaji']  罗马字值
 ------------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('11111111','&',$romaji);
  $romaji = str_replace('22222222','+',$romaji);
  $romaji = str_replace('33333333','/',$romaji);
  $romaji = str_replace('44444444','%',$romaji);
  $romaji = str_replace('55555555','#',$romaji);
  $romaji = str_replace('66666666','?',$romaji);
  $romaji = str_replace('77777777',' ',$romaji);
  $romaji = str_replace('88888888',",",$romaji);
  $romaji = str_replace('aaaaaaaa',"<",$romaji);
  $romaji = str_replace('bbbbbbbb',">",$romaji);
  $romaji = str_replace('cccccccc',"{",$romaji);
  $romaji = str_replace('dddddddd',"}",$romaji);
  $romaji = str_replace('eeeeeeee',"(",$romaji);
  $romaji = str_replace('ffffffff',")",$romaji);
  $romaji = str_replace('gggggggg',"|",$romaji);
  $romaji = str_replace('hhhhhhhh',"^",$romaji);
  $romaji = str_replace('iiiiiiii',"[",$romaji);
  $romaji = str_replace('jjjjjjjj',"]",$romaji);
  $romaji = str_replace('kkkkkkkk',"`",$romaji);
  $romaji = str_replace('llllllll',"~",$romaji);
  $romaji = str_replace('mmmmmmmm',"\\",$romaji);
  $romaji = str_replace('nnnnnnnn',"*",$romaji);
  $romaji = str_replace('oooooooo',"\"",$romaji);
  $romaji = str_replace('pppppppp',"=",$romaji);
  $romaji = str_replace('qqqqqqqq',"\'",$romaji);
  $replace_str = '\s|　';
  if(preg_match('/[\'\&\+\/\%\#\?\.\(\)\{\}\[\]\<\>\^\~\`\|\\\"\=\*\,\s]/u',$romaji)){
    $new_romaji = preg_replace(
        '/[\'\&\+\/\%\#\?\.\(\)\{\}\[\]\<\>\^\~\`\|\\\"\*\=\,\s]/u','-', $romaji);
    if(preg_match('/'.$replace_str.'/',$new_romaji)){
      $new_romaji = preg_replace('/'.$replace_str.'/','-',$new_romaji);
    }
    echo $new_romaji;
  }else{
    if(preg_match('/'.$replace_str.'/',$romaji)){
      $new_romaji = preg_replace('/'.$replace_str.'/','-',$romaji);
      echo $new_romaji; 
    }else{
      echo '';
    }
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'c_is_set_romaji'){
/*--------------------------------------------
 功能: 设置罗马字 
 参数: $_POST['romaji']  罗马字值
 -------------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('11111111','&',$romaji);
  $romaji = str_replace('22222222','+',$romaji);
  $romaji = str_replace('33333333','/',$romaji);
  $romaji = str_replace('44444444','%',$romaji);
  $romaji = str_replace('55555555','#',$romaji);
  $romaji = str_replace('66666666','?',$romaji);
  $romaji = str_replace('77777777',' ',$romaji);
  $romaji = str_replace('88888888',",",$romaji);
  $romaji = str_replace('aaaaaaaa',"<",$romaji);
  $romaji = str_replace('bbbbbbbb',">",$romaji);
  $romaji = str_replace('cccccccc',"{",$romaji);
  $romaji = str_replace('dddddddd',"}",$romaji);
  $romaji = str_replace('eeeeeeee',"(",$romaji);
  $romaji = str_replace('ffffffff',")",$romaji);
  $romaji = str_replace('gggggggg',"|",$romaji);
  $romaji = str_replace('hhhhhhhh',"^",$romaji);
  $romaji = str_replace('iiiiiiii',"[",$romaji);
  $romaji = str_replace('jjjjjjjj',"]",$romaji);
  $romaji = str_replace('kkkkkkkk',"`",$romaji);
  $romaji = str_replace('llllllll',"~",$romaji);
  $romaji = str_replace('mmmmmmmm',"\\",$romaji);
  $romaji = str_replace('nnnnnnnn',"*",$romaji);
  $romaji = str_replace('oooooooo',"\"",$romaji);
  $romaji = str_replace('pppppppp',"=",$romaji);
  $romaji = str_replace('qqqqqqqq',"\'",$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql =  "select * from ".TABLE_CATEGORIES." c,
		".TABLE_CATEGORIES_DESCRIPTION." 
			cd where c.categories_id=cd.categories_id and
			c.parent_id='".$_POST['pid']."'      and
			cd.romaji='".$romaji."' and
			(cd.site_id='".$site_id."' or cd.site_id = '0' )"; 
  if(isset($_POST['cid'])&&$_POST['cid']!=''){
    $sql .= " and c.categories_id != '".$_POST['cid']."'";
  }
  $sql .= " order by cd.site_id DESC";
  if(tep_db_num_rows(tep_db_query($sql))){
    echo 'true';
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'p_is_set_romaji'){
/*--------------------------------------------
 功能: 罗马字集 
 参数: $_POST['romaji']  罗马字值
 -------------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('11111111','&',$romaji);
  $romaji = str_replace('22222222','+',$romaji);
  $romaji = str_replace('33333333','/',$romaji);
  $romaji = str_replace('44444444','%',$romaji);
  $romaji = str_replace('55555555','#',$romaji);
  $romaji = str_replace('66666666','?',$romaji);
  $romaji = str_replace('77777777',' ',$romaji);
  $romaji = str_replace('88888888',",",$romaji);
  $romaji = str_replace('aaaaaaaa',"<",$romaji);
  $romaji = str_replace('bbbbbbbb',">",$romaji);
  $romaji = str_replace('cccccccc',"{",$romaji);
  $romaji = str_replace('dddddddd',"}",$romaji);
  $romaji = str_replace('eeeeeeee',"(",$romaji);
  $romaji = str_replace('ffffffff',")",$romaji);
  $romaji = str_replace('gggggggg',"|",$romaji);
  $romaji = str_replace('hhhhhhhh',"^",$romaji);
  $romaji = str_replace('iiiiiiii',"[",$romaji);
  $romaji = str_replace('jjjjjjjj',"]",$romaji);
  $romaji = str_replace('kkkkkkkk',"`",$romaji);
  $romaji = str_replace('llllllll',"~",$romaji);
  $romaji = str_replace('mmmmmmmm',"\\",$romaji);
  $romaji = str_replace('nnnnnnnn',"*",$romaji);
  $romaji = str_replace('oooooooo',"\"",$romaji);
  $romaji = str_replace('pppppppp',"=",$romaji);
  $romaji = str_replace('qqqqqqqq',"\'",$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql = "select * from  
		".TABLE_PRODUCTS_DESCRIPTION." pd,
		".TABLE_PRODUCTS_TO_CATEGORIES." p2c 
			where pd.products_id=p2c.products_id and
			p2c.categories_id='".$_POST['cid']."'      and
			pd.romaji='".$romaji."' and
			(pd.site_id='".$site_id."' or pd.site_id = '0' )";
  if(isset($_POST['qid'])&&$_POST['qid']!=''){
    $sql .= " and pd.products_id != '".$_POST['qid']."'"; 
  }
  $sql .= " order by pd.site_id DESC";
  if(tep_db_num_rows(tep_db_query($sql))){
    echo 'true';
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'pwd_check_save'){
/*---------------------------------------------
 功能: 密码检查保存  
 参数: $_POST['check_str'] 校验值 
 参数: $_POST['page_name'] 页面的名称
 --------------------------------------------*/
  if (isset($_POST['check_str']) && $_POST['check_str']) {
    $check_arr = explode(',', $_POST['check_str']);
    tep_db_query("delete from  ".TABLE_PWD_CHECK." where page_name = '".$_POST['page_name']."'");
    foreach($check_arr as $value){
      $sql = "insert into ".TABLE_PWD_CHECK." (`id`,`check_value`,`page_name`) VALUES (null,'".$value."','".$_POST['page_name']."')";
      tep_db_query($sql);
    }
    $show_str = "true|||1"; 
    echo $show_str; 
    exit;
  }
  $exists_page_raw = tep_db_query("select * from ".TABLE_PWD_CHECK." where page_name = '".$_POST['page_name']."'"); 
  if (tep_db_num_rows($exists_page_raw) > 0) {
    tep_db_query("delete from  ".TABLE_PWD_CHECK." where page_name = '".$_POST['page_name']."'");
    $show_str = "true";
    if ($ocertify->npermission != 31) {
      $show_str .= "|||0"; 
    } else {
      $show_str .= "|||1"; 
    }
    echo $show_str; 
  } else {
    echo 'success|||1';
  }
}else if(isset($_GET['action'])&&$_GET['action']=='getpwdcheckbox'){
/*-------------------------------------------
 功能: 得到密码复选框 
 参数: $_POST['page_name'] 页面的名称
 ------------------------------------------*/
  $page_name = $_POST['page_name'];
  if($_SESSION['last_page']!= $page_name){
    unset($_SESSION[$_SESSION['last_page']]);
    $_SESSION['last_page'] = $page_name;
  }

  $one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$page_name."'";
  $one_time_query = tep_db_query($one_time_sql);
  $one_time_arr = array();
  while($one_time_row = tep_db_fetch_array($one_time_query)){
    $one_time_arr[] = $one_time_row['check_value'];
  }
  if ($ocertify->npermission == 31) {
    echo 'false';
    exit;
  } else if($ocertify->npermission == 15
     &&in_array('admin',$one_time_arr)&&in_array('onetime',$one_time_arr)){
    echo 'false';
    exit;
  }else if($ocertify->npermission == 10
           &&in_array('chief',$one_time_arr)&&in_array('onetime',$one_time_arr)){
    echo 'false';
    exit;
  }else if($ocertify->npermission == 7
           &&in_array('staff',$one_time_arr)&&in_array('onetime',$one_time_arr)){
    echo 'false';
    exit;
  }
  if(!(in_array('admin',$one_time_arr)&&in_array('chief',$one_time_arr)&& in_array('staff',$one_time_arr))&&in_array('onetime',$one_time_arr)){
    $p_list_array = array(); 
    $p_list_array[] = 31; 
    foreach ($one_time_arr as $o_key => $o_value) {
      if ($o_value != 'onetime') {
        switch($o_value) {
          case 'admin':
            $p_list_array[] = 15; 
            break;
          case 'chief':
            $p_list_array[] = 10; 
            break;
          case 'staff':
            $p_list_array[] = 7; 
            break;
        }
      }
    }
    $sql = "select u.userid,u.rule,l.letter from ".  TABLE_USERS." u , ".TABLE_LETTERS." l,".TABLE_PERMISSIONS." p where u.userid = l.userid and (l.letter != '' or l.letter != null) and u.userid=p.userid and u.status = '1' ".(!empty($p_list_array)?" and p.permission in (".implode(',', $p_list_array).")":"and p.permission in (0)");
    
    $result = tep_db_query($sql);
    $arr =array();
    while($row = tep_db_fetch_array($result)){
      $pwd = $row['letter'].make_rand_pwd($row['rule']);
      $arr[] = $pwd;
    }
    $str = implode(',',$arr); 
    echo $str;
  }else{
    echo "false";
  }
}else if(isset($_GET['action'])&&$_GET['action']=='save_pwd_log'){
/*-------------------------------------------
 功能: 保存密码记录 
 参数: $_POST['one_time_pwd'] 记录第一次密码 
 参数: $_POST['page_name'] 页面的名称
 ------------------------------------------*/
  tep_insert_pwd_log($_POST['one_time_pwd'],$ocertify->auth_user,true,$_POST['page_name']);
} else if (isset($_GET['action'])&&$_GET['action']=='show_right_order_info') {
/*-------------------------------------------
 功能: 显示右侧的订单信息
 参数: $_POST['oid'] 订单编号值 
 ------------------------------------------*/
  $orders_info_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$_POST['oid']."'"); 
  $orders_info = tep_db_fetch_array($orders_info_raw); 
  require(DIR_WS_FUNCTIONS . 'visites.php');
  $param_str = ''; 
  foreach ($_POST as $key => $value) {
    if (($key != 'oid') && ($key != 'popup')) {
      $param_str .= $key.'='.$value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  if ($_POST['popup'] == '1') {
    tep_get_orders_products_string($orders_info, true, true, $param_str);
  } else {
    tep_get_orders_products_string($orders_info, true, false, $param_str);
  }
} else if (isset($_GET['action'])&&$_GET['action']=='get_oa_type') {
/*-----------------------------------------
 功能: 得到OA类型 
 参数: $_POST['oid'] 订单编号值 
 ----------------------------------------*/
  $onsuit = false;
  $tnsuit = false;
  $notfinish = false;
  $ids = $_POST['oid'];
  $ids_array = explode('_',$ids);
  $orders_status_finish = true;
  $orders_finish = false;
  foreach($ids_array as $oid){
    if($oid==''){continue;}
    unset($orders_info_raw);
	$orders_info_raw = tep_db_query("select payment_method,orders_status  from ".TABLE_ORDERS." where orders_id = '".$oid."'"); 
    $finish          = tep_get_order_canbe_finish($oid)?1:0;
    $type            = tep_check_order_type($oid);
	$orders_info = tep_db_fetch_array($orders_info_raw); 
    if(!isset($orders_info_first)){
      $orders_info_first = $orders_info;
    }else {
      if ($orders_info_first != $orders_info){
        $onsuit = true;
        break;
      }
    }
    if(!isset($type_first)){
      $type_first = $type;
    }else {
      if($type_first !=$type){
      $tnsuit = true;
      break;
      }
    }
    if(!isset($finish_first)){
      $finish_first = $finish;
    }else{
      if($finish_first !=$finish or $notfinish){
      $notfinish = true;
      }
    }
    if(!check_order_transaction_button($orders_info['orders_status']) && $orders_status_finish == true){

      $orders_status_finish = false;
    }
    if(tep_orders_finishqa($oid) && $orders_finish == false){

      $orders_finish = true;
    }
  }
  if(!$onsuit && !$tnsuit){
    echo urlencode($orders_info['payment_method']).'_'.$type.'_';
    $notfinish = $notfinish?0:$finish_first;
    echo $notfinish == 0 && $orders_status_finish == true && $orders_finish == false ? 1 : $notfinish;
  }
  //取得 支付类型 及 支付方法
} else if  (isset($_GET['action'])&&$_GET['action']=='get_oa_groups') {
/*--------------------------------------
 功能: 得到OA组 
 参数: $_GET['payment'] 支付值 
 参数: $_GET['buytype'] 购买类型值
 -------------------------------------*/
  $sql = 'select 
		g.name gname,g.id gid,f.id form_id
		from oa_group g,oa_form f,oa_form_group fg  
		where 
		f.id = fg.form_id 
		and g.id = fg.group_id 
		and f.payment_romaji = "'.urldecode($_GET['payment']).'" and f.formtype = '.$_GET['buytype'].' order by fg.ordernumber';

  $group_res = tep_db_query($sql);
  $bigText = '';
  while($group = tep_db_fetch_array($group_res)){
    $bigText.= $group['gname'];
    $bigText.="|";
    $bigText.=$group['gid'];
    $bigText.="|";
    $bigText.= $group['form_id'];
    $bigText.="_";
  }
  //  $bigText['result'] = $bigText;
  echo json_encode($bigText);

} else if  (isset($_GET['action'])&&$_GET['action']=='get_group_renderstring') {
/*-------------------------------------
 功能: 获取提供的字符串 
 参数: $_GET['ids'] 订单编号
 参数: $_GET['group_id'] 组ID值
 ------------------------------------*/
  $ids = $_GET['ids'];
  $ids_array = explode('_',$ids);
  $sql  = 'select * from oa_item where group_id = "'.$_GET['group_id'].'" and type!="autocalculate" order by  ordernumber';
  require_once 'oa/DbRecord.php';
  require_once 'oa/HM_Form.php'; 
  require_once "oa/HM_Item.php";
  require_once 'oa/HM_Group.php';
  $res = tep_db_query($sql);
  $bigRender ='';
  $orders_status_finish_flag = false;
  $orders_status_finish_js = '';
  foreach($ids_array as $key=>$oid){
    if ($oid ==''){
      unset($ids_array[$key]);
    }else{

      $orders_status_finish_query = tep_db_query("select orders_status from ". TABLE_ORDERS ." where orders_id='".$oid."'");
      $orders_status_finish_array = tep_db_fetch_array($orders_status_finish_query);
      tep_db_free_result($orders_status_finish_query);

      if((tep_orders_finishqa($oid) || check_order_transaction_button($orders_status_finish_array['orders_status'])) && $orders_status_finish_flag == false){

        $orders_status_finish_flag = true;
      }
    }
  }
  if($orders_status_finish_flag == true){
    $orders_status_finish_js = '<script type="text/javascript">';
    $orders_status_finish_js .= 'orders_disable();';
    $orders_status_finish_js .= '</script>';
  }
  while ($item = tep_db_fetch_object($res,'HM_Item')){
    unset($exampleOrder);
    unset($exampleOrderInstead);
    if(count($ids_array)>1){
      foreach($ids_array as $oid){
        $sqlEx = 'select of.orders_id ,of.value from oa_item i,oa_formvalue of where orders_id = "'.$oid.'" and item_id = '.$item->id .' and of.group_id = "'.$_GET['group_id'].'"' ;
        $resEx = tep_db_query($sqlEx);
        $exampleOrderInstead = tep_db_fetch_array($resEx);
        if(!$exampleOrderInstead){
          $exampleOrder = false;
          break;
        }
        if(!isset($exampleOrder)){

          $exampleOrder = $exampleOrderInstead;
        }else {
          if($exampleOrder['value'] != $exampleOrderInstead['value']){
            echo '------------';
            $exampleOrder = false;
            break;
          }else {
          }
        }
      }
    }else {
      $exampleOrder['orders_id']=$ids_array[0];
    }
    if($exampleOrder!=false){
      $orders_info_raw = tep_db_fetch_array(tep_db_query("select oa_form.id from ".TABLE_ORDERS." o, oa_form  where oa_form.payment_romaji = o.payment_method and o.orders_id = '".$exampleOrder['orders_id']."' and oa_form.formtype=".tep_check_order_type($exampleOrder['orders_id']))); 
      $item->init()->loadDefaultValue($exampleOrder['orders_id'],$orders_info_raw['id'],$_GET['group_id']);
    }
    echo "<tr>";
    $bigRender.= $item->init()->render(true).'';
    echo "</tr>";
  }
  echo $orders_status_finish_js;

} else if (isset($_GET['action'])&&$_GET['action']=='show_right_preorder_info') {
/*---------------------------------------------
 功能: 显示右侧预约信息
 参数: $_POST['oid'] 订单编号
 --------------------------------------------*/
  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['oid']."'"); 
  $orders_info = tep_db_fetch_array($orders_info_raw); 
  require(DIR_WS_FUNCTIONS . 'visites.php');
  tep_get_pre_orders_products_string($orders_info, true);

}else if (isset($_GET['action'])&&$_GET['action']=='change_mail_list'){
/*--------------------------------------------
 功能: 更改邮件列表 
 参数: $_POST['mail_list_action'] 邮件列表的作用 
 -------------------------------------------*/
  if(isset($_SESSION['mail_sub_customer'])&&$_SESSION['mail_sub_customer']){
    $tmp_arr = explode(',',$_SESSION['mail_sub_customer']);
  }else{
    $tmp_arr = array();
  }
  if($_POST['mail_list_action']=='sub'){
    if(!in_array($_POST['mail_list_value'],$tmp_arr)){
      array_push($tmp_arr,$_POST['mail_list_value']);
    }
  }else if ($_POST['mail_list_action']=='add'){
    if(in_array($_POST['mail_list_value'],$tmp_arr)){
      $tmp_arr = array_diff($tmp_arr,array($_POST['mail_list_value']));
    }
  }
  $_SESSION['mail_sub_customer'] = implode(',',$tmp_arr);
}else if (isset($_GET['action'])&&$_GET['action']=='save_mail_info'){
/*-------------------------------------------
 功能: 保存邮件信息
 参数: $_POST['mail_info_from'] 邮件信息
 参数: $_POST['mail_info_subject'] 邮件信息主题
 参数: $_POST['mail_info_message'] 邮件信息消息
 ------------------------------------------*/
  $_SESSION['mail_post_value']['from'] = $_POST['mail_info_from'];
  $_SESSION['mail_post_value']['subject'] = $_POST['mail_info_subject'];
  $_SESSION['mail_post_value']['message'] = $_POST['mail_info_message'];
}else if (isset($_GET['action'])&&$_GET['action']=='mail_checkbox_validate'){
/*------------------------------------------
 功能: 邮件复选框验证 
 参数: $_SESSION['mail_list'] 邮件列表
 参数: $_SESSION['mail_sub_customer'] 邮件客户
 -----------------------------------------*/
  if(isset($_SESSION['mail_list'])&&$_SESSION['mail_list']&&
      isset($_SESSION['mail_sub_customer'])&&$_SESSION['mail_sub_customer']){
    $mail_list_query = tep_db_query($_SESSION['mail_list']);
    $sub_mail_arr = explode(',',$_SESSION['mail_sub_customer']);
    if(tep_db_num_rows($mail_list_query)==count($sub_mail_arr)){
      echo "true";
    }else{
      echo "false";
    }
  }else{
    echo "false";
  }
} else if (isset($_GET['action'])&&$_GET['action']=='edit_campaign') {
/*-----------------------------------------
 功能: 编辑优惠劵
 参数: $_POST['cid'] ID值
 ----------------------------------------*/
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_CAMPAIGN); 
  $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_POST['cid']."'"); 
  $campaign_res = tep_db_fetch_array($campaign_query);
  $html_str = '';
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>'; 
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= $campaign_res['title']; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= get_campaign_link_page($campaign_res['id'], $campaign_res['site_id'], $_POST['st_id']); 
  $html_str .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="close_campaign_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>'; 
  $html_str .= tep_draw_form('campaign', FILENAME_CAMPAIGN, 'action=update&site_id='.$campaign_res['site_id'].'&st_id='.$_POST['st_id']); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220">';
  $html_str .= TEXT_INFO_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('title', $campaign_res['title'], 'id="title" class="campaign_input" '); 
  $html_str .= '<div id="title_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('name', $campaign_res['name'], 'id="name" class="campaign_input"'); 
  $html_str .= '<div id="name_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_KEYWORD; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('keyword', $campaign_res['keyword'], 'id="keyword" class="campaign_input" ');
  $html_str .= '<br>'.'<font size="1">'.TEXT_CAMPAIGN_KEYWORD_READ.'</font>';
  $html_str .= '<div id="keyword_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>'; 
  $html_str .= '<td>'; 
  $html_str .= TEXT_CAMPAIGN_TYPE; 
  $html_str .= '</td>'; 
  $html_str .= '<td>'; 
  if ($campaign_res['type'] == 1) {
    $html_str .= tep_draw_radio_field('type',1,true,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_SELL.'&nbsp;'.tep_draw_radio_field('type',2,false,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_BUY; 
    $limit_value_text = TEXT_CAMPAIGN_LIMIT_VALUE_READ_UP;
  } else {
    $html_str .= tep_draw_radio_field('type',1,false,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_SELL.'&nbsp;'.tep_draw_radio_field('type',2,true,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_BUY; 
    $limit_value_text = TEXT_CAMPAIGN_LIMIT_VALUE_READ_DOWN;
  }
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  if ($campaign_res['type'] == 1) {
    $html_str .= '<span id="type_symbol">+</span>';
  } else {
    $html_str .= '<span id="type_symbol">-</span>';
  }
  $html_str .= '&nbsp;&nbsp;&nbsp;&nbsp;'.tep_draw_input_field('limit_value', abs($campaign_res['limit_value']), 'id="limit_value" class="campaign_input_num"').'&nbsp;'.TEXT_MONEY_SYMBOL; 
  $html_str .= '<span id="limit_value_text">';
  $html_str .= $limit_value_text;
  $html_str .= '</span>';
  $html_str .= '<div id="limit_value_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_POINT_VALUE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= '-&nbsp;&nbsp;&nbsp;&nbsp;'.tep_draw_input_field('point_value',
      substr($campaign_res['point_value'],1), 
      'id="point_value" class="campaign_input_num" ').'&nbsp;'.TEXT_MONEY_SYMBOL; 
  $html_str .= '<div id="point_value_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>'; 
  $html_str .= '<td>&nbsp;'; 
  $html_str .= '</td>'; 
  $html_str .= '<td>'; 
  $html_str .= '<font size="1">'.TEXT_CAMPAIGN_VALUE_READ.'</font>'; 
  $html_str .= '</td>'; 
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_PREORDER; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  if ($campaign_res['is_preorder'] == 0) {
    $html_str .= tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder"').TEXT_TRUE.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').TEXT_FALSE; 
  } else {
    $html_str .= tep_draw_radio_field('is_preorder',1,true,'','id="is_preorder"').TEXT_TRUE.'&nbsp;'.tep_draw_radio_field('is_preorder',0,false,'','id="is_preorder"').TEXT_FALSE; 
  }
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_START_DATE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $start_date_info = explode('-', $campaign_res['start_date']); 
  $html_str .= '<select name="syear" id="syear">'; 
  for ($i=2012; $i<=2030; $i++) {
    $select_str = ($start_date_info[0]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.YEAR_TEXT.'&nbsp;'; 
  $html_str .= '<select name="smonth" id="smonth">'; 
  for ($i=1; $i<=12; $i++) {
    $select_str = ($start_date_info[1]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.MONTH_TEXT.'&nbsp;'; 
  $html_str .= '<select name="sday" id="sday">'; 
  for ($i=1; $i<=31; $i++) {
    $select_str = ($start_date_info[2]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.DAY_TEXT.'&nbsp;'; 
  $html_str .= '<div id="date_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_END_DATE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $end_date_info = explode('-', $campaign_res['end_date']); 
  $html_str .= '<select name="eyear" id="eyear">'; 
  for ($i=2012; $i<=2030; $i++) {
    $select_str = ($end_date_info[0]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.YEAR_TEXT.'&nbsp;'; 
  $html_str .= '<select name="emonth" id="emonth">'; 
  for ($i=1; $i<=12; $i++) {
    $select_str = ($end_date_info[1]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.MONTH_TEXT.'&nbsp;'; 
  $html_str .= '<select name="eday" id="eday">'; 
  for ($i=1; $i<=31; $i++) {
    $select_str = ($end_date_info[2]==$i)?' selected':''; 
    $html_str .= '<option  value="'.sprintf('%02d', $i).'"'.$select_str.'>'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.DAY_TEXT.'&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';  
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_MAX_USE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('max_use', $campaign_res['max_use'],
      'id="max_use" class="campaign_input_num" ').TEXT_CAMPAIGN_NUM_UNIT; 
  $html_str .= '<div id="max_use_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_USE_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $camp_num_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where campaign_id = '".$campaign_res['id']."'"); 
  $camp_num = tep_db_fetch_array($camp_num_query); 
  $html_str .= tep_draw_input_field('use_num', strval((int)$camp_num['total']),
      'disabled="true" class="campaign_input_num" style="background:#ccc;" ').TEXT_CAMPAIGN_NUM_UNIT; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  if(tep_not_null($campaign_res['user_added'])){ 
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_USER_ADDED;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= $campaign_res['user_added'];
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }else{
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_USER_ADDED;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_UNSET_DATA;
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }if(tep_not_null(tep_datetime_short($campaign_res['created_at']))){
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_DATE_ADDED;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= $campaign_res['created_at'];
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }else{
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_DATE_ADDED;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_UNSET_DATA;
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }if(tep_not_null($campaign_res['user_update'])){
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_USER_UPDATE;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= $campaign_res['user_update'];
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }else{
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_USER_UPDATE;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_UNSET_DATA;
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }if(tep_not_null(tep_datetime_short($campaign_res['date_update']))){ 
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_DATE_UPDATE;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= $campaign_res['date_update'];
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }else{
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_DATE_UPDATE;
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_UNSET_DATA;
  $html_str .= '</td>';
  $html_str .= '</tr>';
  }
  $html_str .= '<tr>';
  $html_str .= '<td colspan="2" align="center">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="show_new_campaign(\''.$_POST['st_id'].'\')"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_campaign_info('.$campaign_res['id'].', 1, '.$campaign_res['site_id'].');"').'</a>&nbsp;'; 
  if ($ocertify->npermission >= 15) {
    $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_CAMPAIGN.'\')) toggle_campaign_action(\''.$ocertify->npermission.'\', \''.tep_href_link(FILENAME_CAMPAIGN, 'action=deleteconfirm&campaign_id='.$campaign_res['id']).'&site_id='.$_POST['st_id'].'\');"').'</a>'; 
  }
  $html_str .= tep_draw_hidden_field('campaign_id', $campaign_res['id']); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='new_campaign') {
/*------------------------------------------------
 功能: 新建优惠劵
 参数: $_POST['site_id'] site id值 
 -----------------------------------------------*/
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_CAMPAIGN); 
  $html_str = '';
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>';
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= HEADING_TITLE; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= '<a href="javascript:void(0);" onclick="close_campaign_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>'; 
  $html_str .= tep_draw_form('campaign', FILENAME_CAMPAIGN, 'action=insert&site_id='.$_POST['site_id']); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220">';
  $html_str .= TEXT_INFO_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('title', '', 'id="title" class="campaign_input" '); 
  $html_str .= '<div id="title_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('name', '', 'id="name" class="campaign_input" '); 
  $html_str .= '<div id="name_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_KEYWORD; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('keyword', '', 'id="keyword" class="campaign_input" '); 
  $html_str .= '<br>'.'<font size="1">'.TEXT_CAMPAIGN_KEYWORD_READ.'</font>';
  $html_str .= '<div id="keyword_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>'; 
  $html_str .= '<td>'; 
  $html_str .= TEXT_CAMPAIGN_TYPE; 
  $html_str .= '</td>'; 
  $html_str .= '<td>'; 
  $html_str .= tep_draw_radio_field('type',1,true,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_SELL.'&nbsp;'.tep_draw_radio_field('type',2,false,'','id="type" onclick="toggle_type_info(this);"').TEXT_CAMPAIGN_TYPE_BUY; 
  $limit_value_text = TEXT_CAMPAIGN_LIMIT_VALUE_READ_UP;
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= '<span
    id="type_symbol">+</span>&nbsp;&nbsp;&nbsp;&nbsp;'.tep_draw_input_field('limit_value',
        '', 'id="limit_value" class="campaign_input_num" ').'&nbsp;'.TEXT_MONEY_SYMBOL; 
  $html_str .= '<span id="limit_value_text">';
  $html_str .= $limit_value_text;
  $html_str .= '</span>';
  $html_str .= '<div id="limit_value_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_POINT_VALUE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= '-&nbsp;&nbsp;&nbsp;&nbsp;'.tep_draw_input_field('point_value', '',
      'id="point_value" class="campaign_input_num" ').'&nbsp;'.TEXT_MONEY_SYMBOL; 
  $html_str .= '<div id="point_value_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>'; 
  $html_str .= '<td>&nbsp;'; 
  $html_str .= '</td>'; 
  $html_str .= '<td>'; 
  $html_str .= '<font size="1">'.TEXT_CAMPAIGN_VALUE_READ."</font>"; 
  $html_str .= '</td>'; 
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_PREORDER; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder"').TEXT_TRUE.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').TEXT_FALSE; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_START_DATE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= '<select name="syear" id="syear">'; 
  for ($i=2012; $i<=2030; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.YEAR_TEXT.'&nbsp;'; 
  $html_str .= '<select name="smonth" id="smonth">'; 
  for ($i=1; $i<=12; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.MONTH_TEXT.'&nbsp;'; 
  $html_str .= '<select name="sday" id="sday">'; 
  for ($i=1; $i<=31; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.DAY_TEXT.'&nbsp;'; 
  $html_str .= '<div id="date_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_END_DATE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= '<select name="eyear" id="eyear">'; 
  for ($i=2012; $i<=2030; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.YEAR_TEXT.'&nbsp;'; 
  $html_str .= '<select name="emonth" id="emonth">'; 
  for ($i=1; $i<=12; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.MONTH_TEXT.'&nbsp;'; 
  $html_str .= '<select name="eday" id="eday">'; 
  for ($i=1; $i<=31; $i++) {
    $html_str .= '<option  value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).'</option>'; 
  }
  $html_str .= '</select>'.DAY_TEXT.'&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';  
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_MAX_USE; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('max_use', '', 
      'id="max_use" class="campaign_input_num" ').TEXT_CAMPAIGN_NUM_UNIT; 
  $html_str .= '<div id="max_use_error"></div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
   
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_USE_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_input_field('use_num', '0', 'disabled="true"
      class="campaign_input_num" style="background:#ccc;"').TEXT_CAMPAIGN_NUM_UNIT; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td colspan="2" align="center">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_campaign_info(0, 0, '.$_POST['site_id'].');"').'</a>&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='check_campaign') {
/*---------------------------------------------------
 功能: 检查优惠劵
 参数: $_POST['title'] 标题值
 参数: $_POST['campaign_id'] 活动ID值
 参数: $_POST['site_id'] SITE_ID值
 --------------------------------------------------*/
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_CAMPAIGN); 
  $error_array = array();
  $error_array['title'] = '';
  $error_array['name'] = '';
  $error_array['keyword'] = '';
  $error_array['date'] = '';
  $error_array['max_use'] = '';
  $error_array['point_value'] = '';
  $error_array['limit_value'] = '';
  
  if (empty($_POST['title'])) {
    $error_array['title'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_TITLE_IS_NULL.'</font>'; 
  } else {
    if ($_POST['check'] == '1') {
      $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where title = '".$_POST['title']."' and site_id = '".(int)$_POST['site_id']."' and id != '".$_POST['campaign_id']."'"); 
    } else {
      $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where title = '".$_POST['title']."' and site_id = '".(int)$_POST['site_id']."'"); 
    }
    if (tep_db_num_rows($exists_cam_raw)) {
      $error_array['title'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_TITLE_EXISTS.'</font>'; 
    } 
  }
  
  if (empty($_POST['name'])) {
    $error_array['name'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_NAME_IS_NULL.'</font>'; 
  }
  
  if (empty($_POST['keyword'])) {
    $error_array['keyword'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_KEYWORD_IS_NULL.'</font>';
  } else {
    if (!preg_match('/^[0-9a-zA-Z]+$/', $_POST['keyword'])) {
      $error_array['keyword'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_KEYWORD_IS_NULL.'</font>';
    } else {
      if (preg_match('/^[0-9]+$/', $_POST['keyword'])) {
        $error_array['keyword'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_KEYWORD_IS_NULL.'</font>';
      } else {
        if ($_POST['check'] == '1') {
          $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."' and id != '".$_POST['campaign_id']."'"); 
        } else {
          $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."'"); 
        }
        if (tep_db_num_rows($exists_cam_raw)) {
          $error_array['keyword'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_KEYWORD_EXISTS.'</font>';
        }
      }
    }
    }
    $start_date = $_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday'];  
    $end_date = $_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday']; 

    if (!preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $start_date) || !preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $end_date)) {
      $error_array['date'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_DATE_WRONG.'</font>';
    } else {
      $start_time = @strtotime($start_date.' 00:00:00'); 
      $end_time = @strtotime($end_date.' 00:00:00'); 
      if ($start_time > $end_time) {
        $error_array['date'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_DATE_WRONG.'</font>';
      }
    }
    
    if (empty($_POST['max_use'])) {
      $error_array['max_use'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_MAX_USE_WRONG.'</font>';
    } else {
      if (!is_numeric($_POST['max_use'])) {
        $error_array['max_use'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_MAX_USE_WRONG.'</font>';
      } else {
        if ($_POST['max_use'] < 0) {
          $error_array['max_use'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_MAX_USE_WRONG.'</font>';
        }
      }
    }
    
    $p_error = false;
    $l_error = false;
    if (!preg_match('/^[0-9]+(%)?$/', $_POST['point_value'])) {
      $error_array['point_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_POINT_VALUE_WRONG.'</font>';
      $p_error = true; 
    } else {
      if (preg_match('/^[0-9]+%$/', $_POST['point_value'])) {
        $percent_str = substr($_POST['point_value'], 0, -1); 
        if ($_POST['type'] == 1) {
          if ($percent_str > 100) {
            $error_array['point_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_POINT_VALUE_WRONG.'</font>';
            $p_error = true; 
          }
        }
      }  
    }
    
    if (!is_numeric($_POST['limit_value'])) {
      $error_array['limit_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_LIMIT_VALUE_WRONG.'</font>';
      $l_error = true;
    } else {
      if (!preg_match('/^[0-9]+$/', $_POST['limit_value'])) {
        $error_array['limit_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_LIMIT_VALUE_WRONG.'</font>';
        $l_error = true;
      }
      if ($_POST['limit_value'] == 0) {
        $error_array['limit_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_LIMIT_VALUE_WRONG.'</font>';
        $l_error = true;
      }
    }
   
    if (!$p_error && !$l_error) {
      if ($_POST['type'] == 1) {
        if (preg_match('/^[0-9]+$/', $_POST['point_value'])) {
          if ($_POST['limit_value'] < $_POST['point_value']) {
            $error_array['point_value'] = '<font color="#ff0000;">'.TEXT_CAMPAIGN_POINT_VALUE_WRONG.'</font>';
          }
        }
      }
    }
    echo implode('|||', $error_array);
} else if (isset($_GET['action'])&&$_GET['action']=='show_del_info') {
/*-----------------------------------------------
 功能: 显示删除信息 
 参数: $_POST['oID'] 订单ID值
 ----------------------------------------------*/
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_ORDERS);
  $param_str = ''; 
  foreach ($_POST as $key => $value) {
    if (($key != 'oID') && ($key != 'popup')) {
      $param_str .= $key.'='.$value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  $html_str .= '<table class="del_order_notice">'; 
  $html_str .= '<tr><td>'; 
  $html_str .= TEXT_INFO_DELETE_INTRO.'&nbsp;&nbsp;';
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= tep_draw_checkbox_field('restock', 'on', true);
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_RESTOCK_PRODUCT_QUANTITY; 
  $html_str .= '</td>';
  $html_str .= '</tr>'; 
  $html_str .= '<tr>'; 
  $html_str .= '<td colspan="3" align="center"><a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="confirm_del_order_info();"').'</a>'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="cancel_del_order_info(\''.$_POST['oID'].'\', \''.urlencode($param_str).'\')"').'</a>';
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='cancel_del_info') {
/*---------------------------------------------
 功能: 取消删除信息
 参数: $_POST['oID'] 订单ID值
 --------------------------------------------*/
  $param_str = ''; 
  foreach ($_POST as $key => $value) {
    if (($key != 'oID') && ($key != 'popup')) {
      $param_str .= $key.'='.$value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  $html_str = '<a href="'.tep_href_link(FILENAME_ORDERS, $param_str.'&oID='.$_POST['oID'].'&action=edit').'">'.tep_html_element_button(IMAGE_DETAILS).'</a>';
  $html_str .= '&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_order_info(\''.$_POST['oID'].'\', \''.urlencode($param_str).'\')"').'</a>';
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='search_manual') {
/*-------------------------------------------
 功能: 检索 
 参数: $_GET['q'] 要搜索的名字
 ------------------------------------------*/
$json_array=array();
$search_cat_manual_query=tep_db_query("select categories_name,categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where categories_name like '%".$_GET['q']."%' and site_id='0'");
while($search_cat_manual_array=tep_db_fetch_array($search_cat_manual_query)){
	$check_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$search_cat_manual_array['categories_id']."'");
	$check_array=tep_db_fetch_array($check_query);
	if($check_array['parent_id']==0){
$json_array[]=array('name'=>$search_cat_manual_array['categories_name']);
	}
}
$search_pro_manual_query=tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_name like '%".$_GET['q']."%' and site_id='0'");
while($search_pro_manual_array=tep_db_fetch_array($search_pro_manual_query)){
$json_array[]=array('name'=>$search_pro_manual_array['products_name']);
}
echo json_encode($json_array);
} else if (isset($_GET['action'])&&$_GET['action']=='search_group') {
/*-----------------------------------------
 功能: 搜索组 
 参数: $_GET['q'] 要搜索的名字
 ----------------------------------------*/
  $json_array = array(); 
  $search_group_query = tep_db_query("select name from ".TABLE_OPTION_GROUP." where name like '%".tep_replace_full_character($_GET['q'])."%' order by created_at desc");
    while ($search_group = tep_db_fetch_array($search_group_query)) {
      $json_array[] = array('name' => $search_group['name']); 
    }
  echo json_encode($json_array); 
} else if (isset($_GET['action'])&&$_GET['action']=='check_group') {
/*----------------------------------------
 功能: 检查组 
 参数: $_POST['gname'] 组名
 参数: $_POST['gid']   组ID
 ---------------------------------------*/
  //检查组的信息是否填写正确 
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION); 
  $error_array = array();
  $error_array['name'] = '';
  $error_array['title'] = '';
  
  $gname = str_replace(' ', '', $_POST['gname']);
  $gname = str_replace('　', '', $gname);
  $gtitle = str_replace(' ', '', $_POST['gtitle']);
  $gtitle = str_replace('　', '', $gtitle);
  
  if ($gname == '') {
    $error_array['name'] = ERROR_OPTION_IS_NULL;
  }
  if ($_POST['type'] == 0) {
    $group_exists_raw = tep_db_query("select * from ".TABLE_OPTION_GROUP." where name = '".$_POST['gname']."'"); 
    if (tep_db_num_rows($group_exists_raw)) {
      if (empty($error_array['name'])) {
        $error_array['name'] = ERROR_OPTION_GROUP_NAME_EXISTS;  
      }
    }
  } else {
    $group_exists_raw = tep_db_query("select * from ".TABLE_OPTION_GROUP." where name = '".$_POST['gname']."' and id != '".$_POST['gid']."'"); 
    if (tep_db_num_rows($group_exists_raw)) {
      if (empty($error_array['name'])) {
        $error_array['name'] = ERROR_OPTION_GROUP_NAME_EXISTS;  
      } 
    }
  }
  if ($gtitle == '') {
    $error_array['title'] = ERROR_OPTION_IS_NULL;
  }
  echo implode('||', $error_array);
} else if (isset($_GET['action'])&&$_GET['action']=='change_item') {
/*--------------------------------------------------
 功能: 更改项目 
 参数: $_POST['stype'] 类型
 参数: $_POST['item_id'] 项目编号
 -------------------------------------------------*/
  //改变item类型的显示 
  $classname = 'HM_Option_Item_'.ucfirst($_POST['stype']);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  echo $item_instance->prepareFormWithParent($_POST['item_id']);
} else if (isset($_GET['action'])&&$_GET['action']=='handle_option') {
/*-------------------------------------------------
 功能: 处理选项 
 参数: $_POST['keyword'] 关键词
 ------------------------------------------------*/
  $option_group_exists_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where name = '".$_POST['keyword']."'");
  if (!tep_db_num_rows($option_group_exists_raw)) {
    $insert_sql = "insert into `".TABLE_OPTION_GROUP."` values(NULL, '".$_POST['keyword']."', '".$_POST['keyword']."', '', '', '1', '1000', '".date('Y-m-d H:i:s', time())."')";
    tep_db_query($insert_sql);
  }
} else if (isset($_GET['action'])&&$_GET['action']=='check_item') {
/*-----------------------------------------------
 功能: 检查item信息填写是否正确
 参数: $_POST['ititle'] 标题 
 参数: $_POST['ifront_title'] 检查第一个标题
 ----------------------------------------------*/
  //检查item信息填写是否正确 
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION); 
  $error_array = array();
  $error_array['title'] = '';
  $error_array['ftitle'] = '';
  $error_array['rname'] = '';
  $ititle = str_replace(' ', '', $_POST['ititle']);
  $ititle = str_replace('　', '', $ititle);
  $ifront_title = str_replace(' ', '', $_POST['ifront_title']);
  $ifront_title = str_replace('　', '', $ifront_title);
  $option_flag = $_POST['option_flag'];
  
  if ($ititle == '') {
    $error_array['title'] = ERROR_OPTION_IS_NULL;
  }
  
  if ($ifront_title == '') {
    $error_array['ftitle'] = ERROR_OPTION_IS_NULL;
  }
 
  if ($_POST['r_str'] != '') {
    $r_str = substr($_POST['r_str'], 0, -6);
    $r_str_array = explode('<<<|||', $r_str);
    $tmp_r_str_array = array(); 
    if (!empty($r_str_array)) {
      foreach ($r_str_array as $r_key => $rvalue) {
        $tmp_rvalue = str_replace("\r\n", "", $rvalue); 
        $tmp_rvalue = str_replace("\n", "", $tmp_rvalue); 
        $tmp_r_str_array[] = $tmp_rvalue; 
      }
    }
    $unique_array = array_unique($tmp_r_str_array);
    $r_count = count($r_str_array);
    $ru_count = count($unique_array); 
    if ($r_count != $ru_count) {
      $error_array['rname'] = ERROR_OPTION_ITEM_RADIO_IS_SAME; 
    }
  }
  if($option_flag == 'false'){

    $error_array['option_flag'] = TEXT_OPTION_MUST_ENTER;
  }
  echo implode('||', $error_array);
} else if (isset($_GET['action'])&&$_GET['action']=='search_item_title') {
/*--------------------------------------------
 功能: 搜索项目的标题 
 参数: $_POST['sea_title'] 搜索标题
 参数: $_POST['s_item_id'] 搜索物品ID
 -------------------------------------------*/
  //搜索标题相近的item 
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION); 
  $html_str = '';
  $html_str_add = '0'; 
  if (!empty($_POST['s_item_id'])) {
    $other_item_raw = tep_db_query("select id, title from ".TABLE_OPTION_ITEM." where title like '%".$_POST['sea_title']."%' and id != '".(int)$_POST['s_item_id']."'");
  } else {
    $other_item_raw = tep_db_query("select id, title from ".TABLE_OPTION_ITEM." where title like '%".$_POST['sea_title']."%'");
  }
  if (!tep_db_num_rows($other_item_raw)) {
    $html_str .= '<tr><td colspan="2">';
    $html_str .= '<font color="ff0000">'.OPTION_SEARCH_NO_RESULT.'</font>'; 
    $html_str .= '</td></tr>';
    echo $html_str.'|||'.$html_str_add;
    exit;
  }
  while ($other_item = tep_db_fetch_array($other_item_raw)) {
    $html_str .= '<tr>';
    $html_str .= '<td width="25%" align="left">';
    $html_str .= TABLE_HEADING_OPTION_NAME; 
    $html_str .= '</td>';
    $html_str .= '<td align="left">';
    $html_str .= tep_draw_input_field('stitle_'.$other_item['id'], $other_item['title'], 'class="option_text"'); 
    $html_str .= '<a href="javascript:void(0);" onclick="preview_item(\''.$other_item['id'].'\', \''.$_POST['t_type'].'\');">'.tep_html_element_button(OPTION_ITEM_PREVIEW_TEXT, 'onclick=""', 'option_preview').'</a>'; 
    $html_str .= '</td>';
    $html_str .= '</tr>';
  }
  if ($html_str != '') {
    $html_str_add = '1'; 
  }
  
  echo $html_str.'|||'.$html_str_add;
} else if (isset($_GET['action'])&&$_GET['action']=='preview_title') {
/*-------------------------------------------
 功能: 预览标题 
 参数: $_POST['preview_id'] 预览ID
 ------------------------------------------*/
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION); 
  require_once('enabledoptionitem.php'); 
  $html_str = ''; 
  $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$_POST['preview_id']."'");
  $item = tep_db_fetch_array($item_raw);
  if ($item) {
    $show_item_array = array();
    $show_item_array['title'] = $item['title'];     
    $show_item_array['front_title'] = $item['front_title'];     
    $show_item_array['price'] = (int)$item['price'];     
    $show_item_array['sort_num'] = $item['sort_num'];     
    $select_str .= '<select id="type" name="type" onchange="change_option_item_type(0);">'; 
    
    foreach ($enabled_item_array as $ekey => $evalue) {
      if (strtolower($evalue) == $item['type']) {
        $check_str = ' selected'; 
      } else {
        $check_str = ''; 
      }
      $select_str .= '<option value="'.$ekey.'"'.$check_str.'>'.strtolower($evalue).'</option>'; 
    }
    
    $select_str .= '</select>';
    $show_item_array['type'] = $select_str;     
    
    $place_str = ''; 
    if ($item['place_type'] == '0') {
      $place_str .= tep_draw_radio_field('place_type', '0', true).OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1').OPTION_ITEM_TYPE_LAST; 
    } else {
      $place_str .= tep_draw_radio_field('place_type', '0').OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1', true).OPTION_ITEM_TYPE_LAST; 
    }
    $show_item_array['place_type'] = $place_str;     
    
    
    $classname = 'HM_Option_Item_'.ucfirst($item['type']);
    require_once('option/'.$classname.'.php');
    $item_instance = new $classname();
    $element_str = $item_instance->prepareFormWithParent($item['id']);
    
    $show_item_array['item_element'] = $element_str;
    
    $show_item_array['del_title'] = IMAGE_DELETE;
    $show_item_array['new_title'] = IMAGE_SAVE;
    $show_item_array['new_title_info'] = sprintf(OPTION_SAVE_NOTICE_TEXT, $item['title']);
    $show_item_array['item_id'] = $item['id']; 
    $html_str = json_encode($show_item_array); 
  
   
  }
  $place_str = ''; 
  
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='search_title') {
/*-------------------------------------------
 功能: 标题搜索  
 参数: $_GET['q'] 标题  
 ------------------------------------------*/
  $json_array = array(); 
  $search_item_query = tep_db_query("select title from ".TABLE_OPTION_ITEM." where title like '%".tep_replace_full_character($_GET['q'])."%' order by created_at desc");
  while ($search_item = tep_db_fetch_array($search_item_query)) {
    $json_array[] = array('name' => $search_item['title']); 
  }
  echo json_encode($json_array); 
} else if (isset($_GET['action'])&&$_GET['action']=='recalc_price') {
/*-----------------------------------------
 功能: 重新计算价格 
 参数: $_POST['op_string'] 字符串
 参数: $_POST['op_string_title'] 字符串标题
 参数: $_POST['op_string_val'] 字符串值
 参数: $_POST['oid'] 订单ID值
 参数: $_POST['p_price'] 价格
 参数: $_POST['opd'] 订单产品编号
 ----------------------------------------*/
  $op_str = $_POST['op_str'];
  $op_string = $_POST['op_string'];
  $op_string_title = $_POST['op_string_title'];
  $op_string_val = $_POST['op_string_val'];
  $op_array = explode('|||',$op_str);
  $op_string_array = explode('|||',$op_string);
  $op_string_title_array = explode('|||',$op_string_title);
  $op_string_val_array = explode('|||',$op_string_val);
  $session_orders_id = $_POST['orders_id'];
  foreach($op_array as $op_key=>$op_value){

    $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['attributes'][$op_value]['price'] = $op_string_array[$op_key];
    $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['attributes'][$op_value]['option_info']['title'] = $op_string_title_array[$op_key];
    $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['attributes'][$op_value]['option_info']['value'] = $op_string_val_array[$op_key];
  }
  $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['qty'] = $_POST['p_num'];
  $orders_info_raw = tep_db_query("select currency, currency_value from ".TABLE_ORDERS." where orders_id = '".$_POST['oid']."'");
  $orders_info_num_rows = tep_db_num_rows($orders_info_raw);
  $orders_info = tep_db_fetch_array($orders_info_raw);

  $products_session_list_array = explode('_',$_POST['opd']);
  if(count($products_session_list_array) <= 1){ 
    $orders_p_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_products_id = '".$_POST['opd']."'");
    $orders_p = tep_db_fetch_array($orders_p_raw);
  }else{
    $orders_p['products_tax'] = $_SESSION['new_products_list'][$session_orders_id]['orders_products'][$products_session_list_array[1]]['products_tax']; 
  }

  if(count($products_session_list_array) <= 1){ 
    if (tep_check_product_type($_POST['opd'])) {
      $p_price = 0 - tep_replace_full_character($_POST['p_price']); 
    } else {
      $p_price = tep_replace_full_character($_POST['p_price']); 
    }
  }else{
    if($_SESSION['new_products_list'][$session_orders_id]['orders_products'][$products_session_list_array[1]]['products_price'] < 0){
      $p_price = 0 - tep_replace_full_character($_POST['p_price']); 
    }else{
      $p_price = tep_replace_full_character($_POST['p_price']); 
    }
  }
  $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['p_price'] = $p_price; 
  $final_price = $p_price + tep_replace_full_character($_POST['op_price']);
  $_SESSION['orders_update_products'][$session_orders_id][$_POST['opd']]['final_price'] = $final_price;  
  $price_array[] = tep_display_currency(number_format(abs($final_price), 2));
   
  if ($final_price < 0) {
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
    
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
    
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
  
    $price_array[] = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 
    
    $price_array[] = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 
    $price_array[] = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 
    
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($_POST['p_final_price']*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    $price_array[] = TEXT_MONEY_SYMBOL;
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price, true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
  } else {
    $price_array[] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value']); 
    
    $price_array[] = $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value']); 
    
    $price_array[] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value']); 
    $price_array[] = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 
    
    $price_array[] = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 
    $price_array[] = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])); 

    $price_array[] = str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).TEXT_MONEY_SYMBOL;
    $price_array[] = str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($_POST['p_final_price']*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).TEXT_MONEY_SYMBOL;
    $price_array[] = str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).TEXT_MONEY_SYMBOL;
    $price_array[] = TEXT_MONEY_SYMBOL;
    $price_array[] = str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price, true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['currency_value'])).TEXT_MONEY_SYMBOL;
  }
  
  echo implode('|||', $price_array);
} else if (isset($_GET['action'])&&$_GET['action']=='recalc_all_price') {
/*--------------------------------------------------
 功能: 重新计算所有价格
 参数: $_POST['oid'] 订单编号
 参数: $_POST['op_i'] 订单产品编号
 -------------------------------------------------*/
  
  $op_array = explode('|||', $_POST['op_i']);
 
  $price_array = array(); 

  $orders_info_raw = tep_db_query("select currency, currency_value from ".TABLE_ORDERS." where orders_id = '".$_POST['oid']."'");
  $orders_info = tep_db_fetch_array($orders_info_raw);
  
  foreach ($op_array as $key => $value) {
    $op_value = 0; 
    $p_price = 0; 
    if (isset($_POST['update_products'][$value]['attributes'])) {
      foreach ($_POST['update_products'][$value]['attributes'] as $o_key => $o_value) {
        $op_value += tep_replace_full_character($o_value['price']); 
      }
    }
     
    if (isset($_POST['update_products'][$value]['p_price'])) {
      $p_price = tep_replace_full_character($_POST['update_products'][$value]['p_price']); 
    }
    
  
    $orders_p_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_products_id = '".$value."'");
    $orders_p = tep_db_fetch_array($orders_p_raw);
  
    if (tep_get_bflag_by_product_id($orders_p['products_id'])) {
      $p_price = 0 - $p_price; 
    } else {
      $p_price = $p_price; 
    }
    
    $final_price = $p_price + $op_value;

    $price_array[$value][] = tep_display_currency(number_format(abs($final_price), 2));
    
    if ($final_price < 0) {
      $price_array[$value][] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info['currency'], $orders_info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
      
      $price_array[$value][] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price*tep_replace_full_character($_POST['update_products'][$value]['qty']), true, $orders_info['currency'], $orders_info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
      
      $price_array[$value][] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['update_products'][$value]['qty']), true, $orders_info['currency'], $orders_info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
    
    } else {
      $price_array[$value][] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info['currency'], $orders_info['currency_value']); 
      
      $price_array[$value][] = $currencies->format($final_price*tep_replace_full_character($_POST['update_products'][$value]['qty']), true, $orders_info['currency'], $orders_info['currency_value']); 
      
      $price_array[$value][] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['update_products'][$value]['qty']), true, $orders_info['currency'], $orders_info['currency_value']); 
    }
  }
  
  $price_tmp_array = array();
  foreach ($price_array as $p_key => $p_value) {
    $price_tmp_array[] = $p_key.':::'.implode('<<<', $p_value); 
  }
  echo implode('|||', $price_tmp_array);
}else if($_GET['action'] == 'validate_email'){
/*--------------------------------------------
 功能: 验证电子邮件
 参数: $_POST['email'] 电子邮件值
 -------------------------------------------*/
  require('includes/step-by-step/new_application_top.php');
  $email = $_POST['email'];
  if(tep_validate_email($email)){
    echo "true";
  }
}else if($_GET['action'] == 'delete_products'){
/*-------------------------------------------
 功能: 删除产品 
 参数: $_POST['orders_id'] 订单编号
 参数: $_POST['orders_products_id'] 订单产品编号
 ------------------------------------------*/
  $session_orders_id = $_POST['orders_id'];
  $products_id_list_array = explode('_',$_POST['orders_products_id']);
  $products_delete_flag = false;
  if(count($products_id_list_array) <= 1){
    $orders_products_query = tep_db_query("select products_id,final_price,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='".$_POST['orders_products_id']."'");
    $orders_products_array = tep_db_fetch_array($orders_products_query);
    tep_db_free_result($orders_products_query);
    $orders_total = $orders_products_array['final_price']*$orders_products_array['products_quantity'];
    $delete_products_query =tep_db_query("delete from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='".$_POST['orders_products_id']."'");
    $delete_products_attributes_query = tep_db_query("delete from ". TABLE_ORDERS_PRODUCTS_ATTRIBUTES ." where orders_products_id='".$_POST['orders_products_id']."'");
    if($_POST['delete_flag'] != '1') {
      tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = products_real_quantity + ".$orders_products_array['products_quantity'].(tep_orders_finished($session_orders_id) == '1' && tep_orders_finishqa($session_orders_id) == '1' ? ", products_ordered = products_ordered - " . $orders_products_array['products_quantity'] : ''). " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
    } 
  }else{
    $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] -= $_SESSION['new_products_list'][$session_orders_id]['orders_products'][$products_id_list_array[1]]['final_price'];
    $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] -= $_SESSION['new_products_list'][$session_orders_id]['orders_products'][$products_id_list_array[1]]['final_price'];
    unset($_SESSION['new_products_list'][$session_orders_id]['orders_products'][$products_id_list_array[1]]);  
    $products_delete_flag = true;
  }
  if($delete_products_query && $delete_products_attributes_query){
    $total_products_query = tep_db_query("update ". TABLE_ORDERS_TOTAL ." set value=value-".(int)$orders_total." where orders_id='".$session_orders_id."' and class='ot_total'"); 
    $subtotal_products_query = tep_db_query("update ". TABLE_ORDERS_TOTAL ." set value=value-".(int)$orders_total." where orders_id='".$session_orders_id."' and class='ot_subtotal'");
  }
  if($products_delete_flag == true || ($delete_products_query && $delete_products_attributes_query && $total_products_query && $subtotal_products_query)){
    echo 'true';
  }
}else if($_GET['action'] == 'price_total'){
/*-------------------------------------------
 功能: 总价格 
 参数: $_POST['total_value'] 总价值
 参数: $_POST['point_value'] 点值
 参数: $_POST['total_title'] 总标题
 参数: $_POST['total_key'] 总的关键词
 参数: $_POST['orders_id'] 订单编号
 参数: $_POST['point_value_temp'] 点值
 参数: $_POST['handle_fee'] 手续费
 参数: $_POST['fee_total'] 自定义费用总额 
 参数: $_POST['session_site_id'] SITE_ID
 ------------------------------------------*/
  require(DIR_WS_CLASSES . 'payment.php');
  $total_value = $_POST['total_value'];
  $point_value = $_POST['point_value'];
  $total_title = $_POST['total_title'];
  $total_key_array = explode('|||',$_POST['total_key']);
  $total_value_array = explode('|||',$total_value);
  $total_title_array = explode('|||',$total_title);
  $total_orders_id = $_POST['orders_id'];
  $point_value_temp = $_POST['point_value_temp'];
  $handle_fee_value = $_POST['handle_fee'];
  $campaign_flag = false;
  $campaign_fee = 0;
  $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$total_orders_id."' and site_id = '". $_POST['session_site_id'] ."'");
  if(tep_db_num_rows($camp_exists_query)) {
    $campaign_flag = true;
    $campaign_fee = get_campaion_fee($_POST['ot_subtotal'],$total_orders_id,$_POST['session_site_id']);  
  } 
  tep_db_free_result($camp_exists_query);
  if($campaign_flag == true){
    
    $_POST['ot_total'] -= abs($campaign_fee); 
    $point_value = $campaign_fee;
  }
  $_SESSION['orders_update_products'][$total_orders_id]['point'] = $point_value;
  foreach($total_value_array as $total_key=>$total_val){

    $_SESSION['orders_update_products'][$total_orders_id][$total_key_array[$total_key]]['value'] = $total_val;
    $_SESSION['orders_update_products'][$total_orders_id][$total_key_array[$total_key]]['title'] = $total_title_array[$total_key];
  }
  $_SESSION['orders_update_products'][$total_orders_id]['ot_subtotal'] = $_POST['ot_subtotal'];
  $_SESSION['orders_update_products'][$total_orders_id]['ot_total'] = $_POST['ot_total'];
  $_SESSION['orders_update_products'][$total_orders_id]['payment_method'] = $_POST['payment_value'];
  //配送费用
  $shipping_fee = tep_products_shipping_fee($total_orders_id,$_POST['ot_subtotal']+$_POST['fee_total']-$point_value);
  $shipping_fee = $shipping_fee == '' ? 0 : $shipping_fee;
  $cpayment = payment::getInstance($_POST['session_site_id']);
  $handle_fee = $cpayment->handle_calc_fee($_POST['payment_value'], $_POST['ot_subtotal']+$_POST['fee_total']-$point_value+$shipping_fee);
  $handle_fee = $handle_fee == '' ? 0 : $handle_fee;
  $_SESSION['orders_update_products'][$total_orders_id]['code_fee'] = $handle_fee;
  $_SESSION['orders_update_products'][$total_orders_id]['ot_total'] -= $handle_fee_value;
  $_SESSION['orders_update_products'][$total_orders_id]['ot_total'] += $handle_fee;
  $_SESSION['orders_update_products'][$total_orders_id]['ot_total'] -= $_POST['shipping_fee_id'];
  $_SESSION['orders_update_products'][$total_orders_id]['ot_total'] += $shipping_fee;
  $_SESSION['orders_update_products'][$total_orders_id]['fee_total'] = $_POST['fee_total'];
  $_SESSION['orders_update_products'][$total_orders_id]['shipping_fee'] = $shipping_fee;
  echo $handle_fee.'|||'.$campaign_fee.'|||'.$campaign_flag.'|||'.$shipping_fee;
}else if($_GET['action'] == 'orders_session'){

  $session_type = $_POST['orders_session_type'];
  $session_value = $_POST['orders_session_value'];
  $session_orders_id = $_POST['orders_id'];
  $_SESSION['orders_update_products'][$session_orders_id][$session_type] = $session_value;
}else if($_GET['action'] == 'products_num'){

  $products_list_id = $_POST['products_list_id'];
  $products_list_str = $_POST['products_list_str'];
  $products_name_str = $_POST['products_name'];
  if(isset($_POST['orders_products_list_id'])){

    $orders_products_list_id = $_POST['orders_products_list_id'];
    $orders_products_list_id_array = explode('|||',$orders_products_list_id);
  }
  $products_id_array = explode('|||',$products_list_id);
  $products_num_array = explode('|||',$products_list_str);
  $products_name_array = explode('|||',$products_name_str);
  $products_num_error_array = array();

  $products_temp_array = array();
  foreach($orders_products_list_id_array as $orders_products_key=>$orders_products_value){
    if(isset($_POST['products_diff'])){
      $orders_session_list_array = explode('_',$orders_products_value);
      if(count($orders_session_list_array) <= 1){
        $products_orders_query = tep_db_query("select products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id = '" . $orders_products_value  . "'");
        $products_orders_array = tep_db_fetch_array($products_orders_query);
        tep_db_free_result($products_orders_query);
        $products_temp_array[$products_id_array[$orders_products_key]] += $products_orders_array['products_quantity'];
      }
    }else{
      $products_temp_array[$products_id_array[$orders_products_key]] = 0; 
    } 
  }
  foreach($products_id_array as $products_key=>$products_value){
    $products_query = tep_db_query("select products_real_quantity from ". TABLE_PRODUCTS ." where products_id='".$products_value."'");
    $products_array = tep_db_fetch_array($products_query);
    tep_db_free_result($products_query);
    $products_sum = 0;
    foreach($products_id_array as $p_key=>$p_value){

      if($products_value == $p_value){

        $products_sum += $products_num_array[$p_key];
      } 
    }
    $products_sum = $products_sum - $products_temp_array[$products_value];
    if($products_sum > $products_array['products_real_quantity']){

      $products_num_error_array[] = $products_name_array[$products_key];
    }
  }
  $products_num_error_array = array_unique($products_num_error_array);
  if(!empty($products_num_error_array)){

    $products_num_error_str = implode('、',$products_num_error_array);
    echo $products_num_error_str;
  }
} else if ($_GET['action'] == 'handle_mark') {
/*-------------------------------------------
 功能: 处理标记  
 参数: $_GET['select_mark'] 选择标记
 参数: $_GET['mark_symbol'] 标记符号
 参数: $_GET['c_site'] SITE_ID
 参数: $_POST['param_other'] 参数
 ------------------------------------------*/
  $return_array = array();
  $select_mark = $_GET['select_mark'];
  $mark_symbol = $_GET['mark_symbol'];
  if ($select_mark == '') {
    $select_mark = '0-1-2-3-4'; 
  }
  if ($select_mark != '') {
    $select_mark_array = explode('-', $select_mark);    
    $return_array[] = 'success';
    if (in_array($mark_symbol, $select_mark_array)) {
      $mark_array = array(); 
      foreach ($select_mark_array as $m_key => $m_value) {
        if ($m_value != $mark_symbol) {
          $mark_array[] = $m_value;  
        }
      }
      if (!empty($mark_array)) {
        $return_array[] = tep_href_link(FILENAME_ORDERS, $_POST['param_other'].'mark='.implode('-', $mark_array).((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
      } else {
        if (!empty($_GET['c_site'])) {
          $return_array[] = tep_href_link(FILENAME_ORDERS, $_POST['param_other'].'site_id='.$_GET['c_site']);
        } else {
          $return_array[] = tep_href_link(FILENAME_ORDERS, $_POST['param_other']);
        }
      }
    } else {
      $mark_array = $select_mark_array; 
      $mark_array[] = $mark_symbol;
      sort($mark_array);
      $return_array[] = tep_href_link(FILENAME_ORDERS, $_POST['param_other'].'mark='.implode('-', $mark_array).((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
    }
  } else {
    $return_array[] = 'success';
    $return_array[] = tep_href_link(FILENAME_ORDERS, $_POST['param_other'].'mark='.$_GET['mark_symbol'].((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
  }
  echo implode('|||', $return_array);
} else if ($_GET['action'] == 'read_flag') {
/*------------------------------------------
 功能: 读取标志 
 参数: $_POST['user'] 用户
 参数: $_POST['flag'] 标志
 参数: $_POST['oid'] 订单编号
 -----------------------------------------*/
  $users_name = $_POST['user'];
  $read_flag = $_POST['flag'];
  $orders_id = $_POST['oid'];
  $read_flag_query = tep_db_query("select read_flag from ". TABLE_ORDERS ." where orders_id='".$orders_id."'");
  $read_flag_array = tep_db_fetch_array($read_flag_query);
  tep_db_free_result($read_flag_query);
  if($read_flag_array['read_flag'] == ''){

    if($read_flag == 0){
      tep_db_query("update ". TABLE_ORDERS ." set read_flag='".$users_name."' where orders_id='".$orders_id."'"); 
    }
  }else{

    $read_flag_str_array = explode('|||',$read_flag_array['read_flag']);
    if(!in_array($users_name,$read_flag_str_array) && $read_flag == 0){
      $read_flag_add = $read_flag_array['read_flag'].'|||'.$users_name;
      tep_db_query("update ". TABLE_ORDERS ." set read_flag='".$read_flag_add."' where orders_id='".$orders_id."'");
    }else{

      unset($read_flag_str_array[array_search($users_name,$read_flag_str_array)]);
      $read_flag_string = implode('|||',$read_flag_str_array);
      tep_db_query("update ". TABLE_ORDERS ." set read_flag='".$read_flag_string."' where orders_id='".$orders_id."'");
    }
  }
} else if ($_GET['action'] == 'select_site') {
/*-----------------------------------------
 功能: 选择站点 
 参数: $_POST['site_list'] 站点列表中
 参数: $_POST['site_id'] SITE_ID
 ----------------------------------------*/
  if($_POST['site_list'] == ''){
    $orders_site_array = array();
    $orders_site_query = tep_db_query("select id from ". TABLE_SITES);
    while($orders_site_rows = tep_db_fetch_array($orders_site_query)){
      $orders_site_array[] = $orders_site_rows['id'];
    }
    tep_db_free_result($orders_site_query);
    $user_info = tep_get_user_info($ocertify->auth_user); 
    if(PERSONAL_SETTING_ORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
      if(array_key_exists($user_info['name'],$site_setting_array)){

        $site_setting_str = $site_setting_array[$user_info['name']];
      }else{
        $site_setting_str = implode('|',$orders_site_array); 
      }
    }else{
      $site_setting_str = implode('|',$orders_site_array); 
    }
    $site_array = array();
    $site_array = explode('|',$site_setting_str);

    if($_POST['flag'] == 0){

      unset($site_array[array_search($_POST['site_id'],$site_array)]);
    }else{
      $site_array[] = $_POST['site_id']; 
    }
  }else{

    $site_array = explode('-',$_POST['site_list']);
    if($_POST['flag'] == 0){

      unset($site_array[array_search($_POST['site_id'],$site_array)]);
    }else{
      $site_array[] = $_POST['site_id']; 
    }
  }
  sort($site_array);
  if(!empty($site_array)){
    echo tep_href_link(FILENAME_ORDERS, $_POST['param_url'].'site_id='.implode('-',$site_array));
  }else{
    echo tep_href_link(FILENAME_ORDERS, $_POST['param_url']); 
  }
} else if ($_GET['action'] == 'handle_split') {
/*---------------------------------------------
 功能: 处理分组 
 参数: $_POST['split_total_page'] 分总页面
 参数: $_POST['j_page'] 页面 
 --------------------------------------------*/
  if ($_POST['j_page'] > $_POST['split_total_page']) {
    $_POST['j_page'] = $_POST['split_total_page']; 
  } else if ($_POST['j_page'] == 0) {
    $_POST['j_page'] = 1; 
  }
  tep_redirect(tep_href_link($_POST['current_file_info'], $_POST['split_param'].(($_POST['j_page'] != '1')?'page='.$_POST['j_page']:'')));
} else if ($_GET['action'] == 'set_new_price') {
/*--------------------------------------------
 功能: 设置新产品的价格
 参数: $_POST['products_id'] 产品编号
 参数: $_POST['new_price']   新价格
 -------------------------------------------*/
  //设置新的商品价格
  $bflag_single = false;
  if (tep_get_bflag_by_product_id($_POST['products_id'])) {
    $bflag_single = true;
  }
  $price_info_array = array(); 
  $price_info_str = ''; 
  $update_sql_data = array(
      'products_price' => $bflag_single?(0 - $_POST['new_price']):$_POST['new_price']
      );
  tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \''.$_POST['products_id'].'\'');
  $user_info = tep_get_user_info($ocertify->auth_user);
  tep_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$user_info['name']."' where products_id = '".$_POST['products_id']."'"); 
  $products_new_price = tep_get_products_price($_POST['products_id']);
  $html_str = '<u id="edit_p_'.$_POST['products_id'].'">';
  if ($products_new_price['sprice']) {
    $html_str .= '<span class="specialPrice">'.$currencies->format($products_new_price['sprice']).'</span>'; 
  } else {
    $html_str .= $currencies->format($products_new_price['price']); 
  }
  $html_str .= '</u>';
  $html_str .= '<span style="display:none;" id="h_edit_p_'.$_POST['products_id'].'">'.abs($_POST['new_price']).'</span>';
  $html_str .= '|||'; 
  $html_str .= abs($_POST['new_price']); 
  $html_str .= '|||'; 
  $html_str .= $bflag_single?1:2;
  $html_str .= '|||'; 
  $products_info_raw = tep_db_query("select products_last_modified from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['products_id']."' and site_id = '0'"); 
  $products_info = tep_db_fetch_array($products_info_raw); 
  
  $html_str .= tep_get_signal_pic_info($products_info['products_last_modified']); 
  echo $html_str;
} else if ($_GET['action'] == 'get_top_layer') {
/*-----------------------------------------
 功能: 获得页面最大的z-index值
 参数: $_POST['current_belong'] 目前属于的z-index值
 ----------------------------------------*/
  //获得页面最大的z-index值 
  $z_index = '1';
 
  $note_list_raw = mysql_query("select xyz from notes where belong = '".$_POST['current_belong']."'");
  $note_list_array = array();
  
  while ($note_list_res = mysql_fetch_array($note_list_raw)) {
    $note_list_tmp_array = explode('|', $note_list_res['xyz']); 
    $note_list_array[] = $note_list_tmp_array[2]; 
  }
  
  if (!empty($note_list_array)) {
    $z_index = max($note_list_array) + 1; 
  }
  echo $z_index;
} else if ($_GET['action'] == 'hide_note') {
/*-----------------------------------------
 功能: 不显示窗口
 参数: $_POST['note_id'] note的id值 
 ----------------------------------------*/
  tep_db_query("update `notes` set `is_show` = '0' where `id` = '".$_POST['note_id']."'");
  $hide_note_query = tep_db_query("select * from notes where id = '".$_POST['note_id']."'");
  $hide_note = tep_db_fetch_array($hide_note_query);
  echo $hide_note['color'].'|||'.$hide_note['title'];
} else if ($_GET['action'] == 'show_note'){
/*-----------------------------------------
 功能: 显示窗口
 参数: $_POST['note_id'] note的id值 
 ----------------------------------------*/
  tep_db_query("update `notes` set `is_show` = '1' where `id` = '".$_POST['note_id']."'");
} else if ($_GET['action'] == 'select_all_site') {
  $exists_page_raw = tep_db_query("select * from show_site where page = '".$_POST['current_file']."' and user = '".$ocertify->auth_user."'");
  $exists_page_info = tep_db_fetch_array($exists_page_raw); 
  if(isset($_POST['last_site_list'])&&$_POST['last_site_list']!=''){
  $site_list_array = explode('-',$_POST['last_site_list']);
  }else{
  $site_list_raw = tep_db_query("select * from sites"); 
  $site_list_array = array(); 
  $site_list_array[] = 0; 
  while ($site_list_info = tep_db_fetch_array($site_list_raw)) {
    $site_list_array[] = $site_list_info['id']; 
  }
  }
  if ($exists_page_info) {
    $site_info_array = explode('-', $exists_page_info['site']); 
    if (empty($site_info_array)) {
      $site_info_array = $site_list_array; 
    } else {
      if ($_POST['flag'] == 0) {
        unset($site_info_array[array_search($_POST['site_id'], $site_info_array)]);
        if (empty($site_info_array)) {
          $site_info_array = $site_list_array; 
        }
      } else {
        $site_info_array[] = $_POST['site_id']; 
        array_unique($site_info_array); 
      }
    }
    sort($site_info_array);
    tep_db_query("update `show_site` set `site` = '".implode('-',array_unique($site_info_array))."' where `user` = '".$ocertify->auth_user."' and `page` ='".$_POST['current_file']."'");
  } else {
    $site_info_array = $site_list_array; 
    if ($_POST['flag'] == 0&&count($site_info_array)>1) {
      unset($site_info_array[array_search($_POST['site_id'], $site_info_array)]);
    } 
    sort($site_info_array);
    tep_db_query("insert into `show_site` values (null, '".$ocertify->auth_user."', '".$_POST['current_file']."', '".implode('-', array_unique($site_info_array))."')");  
  }
  if(isset($_POST['unshow_list'])&&$_POST['unshow_list']!=''){
    $unshow_list_array = explode('-',$_POST['unshow_list']);
    $site_info_array = array_diff($site_info_array,$unshow_list_array);
  }
  if(!empty($site_info_array)){
    echo tep_href_link($_POST['current_file'], $_POST['param_url'].'site_id='.implode('-', $site_info_array));
  }else{
    echo tep_href_link($_POST['current_file'], $_POST['param_url']); 
  }
} else if ($_GET['action'] == 'generate_onetime_pwd') {
/*-----------------------------------------
 功能: 生成一次性密码
 参数: $letter_info 字符的值 
 参数: $rule_info 规则的值 
 ----------------------------------------*/
  $user_rand_pwd = make_rand_pwd($_POST['rule_info']);
  if ($user_rand_pwd !== false) {
    echo $_POST['letter_info'].$user_rand_pwd;
  } else {
    echo '';
  }
} else if ($_GET['action'] == 'check_user_info') {
/*-----------------------------------------
 功能: 检查用户信息是否正确
 参数: $user_info_id 用户id 
 参数: $stype 类型 
 参数: $userid_info_str 用户填写的id 
 ----------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_USERS);
  $user_error_info['user_error_id'] = ''; 
  $user_error_info['user_error_name'] = ''; 
  $user_error_info['user_error_pwd'] = ''; 
  $user_error_info['user_error_email'] = ''; 
  $user_error_info['user_error_rule'] = ''; 
  
  if (empty($_POST['userid_info_str'])) {
    $user_error_info['user_error_id'] = TEXT_USER_INFO_IS_NULL; 
  } else {
    $userid_len = strlen($_POST['userid_info_str']); 
    if ($userid_len < 2) {
      $user_error_info['user_error_id'] = sprintf(TEXT_USER_INFO_IS_SHORT, 2); 
    } else {
      if (ereg('[[:print:]]', $_POST['userid_info_str']) == false) {
        $user_error_info['user_error_id'] = TEXT_USER_INFO_FORMAT_WRONG; 
      }
    }
    if (empty($user_error_info['user_error_id'])) {
      if ($_POST['stype'] == '0') {
        $exists_user_query = tep_db_query("select * from ".TABLE_USERS." where userid = '".$_POST['userid_info_str']."'"); 
        if (tep_db_num_rows($exists_user_query) > 0) {
          $user_error_info['user_error_id'] = TEXT_USER_ID_EXISTS; 
        }
      } 
    }
  }
 
  if (!empty($_POST['user_info_email'])) {
    if (!tep_validate_new_email($_POST['user_info_email'])) {
      $user_error_info['user_error_email'] = TEXT_USER_EMAIL_FORMAT_WRONG; 
    }
  }
  
  if (empty($_POST['user_info_name'])) {
    $user_error_info['user_error_name'] = TEXT_USER_INFO_IS_NULL; 
  }
   
  if (isset($_POST['user_info_pwd'])) {
    if ($_POST['stype'] == '1') {
      if (!empty($_POST['user_info_pwd'])) {
        $user_pwd_len = strlen($_POST['user_info_pwd']); 
        if ($user_pwd_len < 2) {
          $user_error_info['user_error_pwd'] = sprintf(TEXT_USER_INFO_IS_SHORT, 2); 
        } else {
          if (ereg('[[:print:]]', $_POST['user_info_pwd']) == false) {
            $user_error_info['user_error_pwd'] = TEXT_USER_INFO_FORMAT_WRONG; 
          }
        }
      }
    } else {
      if (empty($_POST['user_info_pwd'])) {
        $user_error_info['user_error_pwd'] = TEXT_USER_INFO_IS_NULL; 
      } else {
        $user_pwd_len = strlen($_POST['user_info_pwd']); 
        if ($user_pwd_len < 2) {
          $user_error_info['user_error_pwd'] = sprintf(TEXT_USER_INFO_IS_SHORT, 2); 
        } else {
          if (ereg('[[:print:]]', $_POST['user_info_pwd']) == false) {
            $user_error_info['user_error_pwd'] = TEXT_USER_INFO_FORMAT_WRONG; 
          }
        }
      }
    }
  }
 
  if (trim($_POST['user_rule']) != '') {
    if (!make_rand_pwd($_POST['user_rule'])) {
      $user_error_info['user_error_rule'] = TEXT_USER_RULE_WRONG; 
    }
  }
  echo implode('|||',$user_error_info);
} else if ($_GET['action'] == 'record_pwd_log') {
/*-----------------------------------------
 功能: 记录日志
 参数: $_POST['current_pwd'] 密码 
 参数: $_POST['url_redirect_str'] url 
 ----------------------------------------*/
  tep_insert_pwd_log($_POST['current_pwd'],$ocertify->auth_user,false,'',$_POST['url_redirect_str']);
} else if ($_GET['action'] == 'get_select_payment_status') {
/*-----------------------------------------
 功能: 获取该支付方法的状态
 参数: $_POST['select_payment'] 支付方法的代码 
 参数: $_POST['s_site_id'] 网站id 
 参数: $_POST['h_type'] 标识 
 ----------------------------------------*/
  if (isset($_POST['h_type'])) {
    $default_select = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($_POST['select_payment']).'_PREORDER_STATUS_ID', $_POST['s_site_id']); 
    echo ($default_select != 0 ? $default_select: get_configuration_by_site_id('DEFAULT_PREORDERS_STATUS_ID'));
  } else {
    $default_select = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($_POST['select_payment']).'_ORDER_STATUS_ID', $_POST['s_site_id']); 
    echo ($default_select != 0 ? $default_select: get_configuration_by_site_id('DEFAULT_ORDERS_STATUS_ID'));
  }
} else if ($_GET['action'] == 'check_order_variable_data') {
/*-----------------------------------------
 功能: 检查变量是否为空
 参数: $_POST['c_comments'] 内容 
 参数: $_POST['o_id'] 订单id 
 参数: $_POST['c_title'] 标题 
 参数: $_POST['c_status_id'] 状题id 
 ----------------------------------------*/
  $o_array = array();
  $o_array[] = $_POST['o_id'];
  echo tep_check_order_variable_data($o_array, $_POST['c_comments'], $_POST['c_title'], false, $_POST['c_status_id']); 
} else if ($_GET['action'] == 'check_order_list_variable_data') {
/*-----------------------------------------
 功能: 检查变量是否为空
 参数: $_POST['c_comments'] 内容 
 参数: $_POST['o_id_list'] 订单id列表 
 参数: $_POST['c_title'] 标题 
 参数: $_POST['c_status_id'] 状题id 
 ----------------------------------------*/
  $o_array = array();
  if (!empty($_POST['o_id_list'])) {
    $o_id_list = substr($_POST['o_id_list'], 0, -1);
    $o_array = explode(',', $o_id_list);
  } 
  echo tep_check_order_variable_data($o_array, $_POST['c_comments'], $_POST['c_title'], true, $_POST['c_status_id']); 
} else if ($_GET['action'] == 'check_new_order_variable_data') {
/*-----------------------------------------
 功能: 检查变量是否为空
 参数: $_POST['o_id_info'] 订单id 
 参数: $_POST['c_comments'] 内容 
 参数: $_POST['fetch_date'] 时间 
 参数: $_POST['c_title'] 标题 
 参数: $_POST['c_status_id'] 状态id 
 参数: $_POST['c_payment'] 方法信息 
 参数: $_POST['c_name_info'] 名字 
 参数: $_POST['c_mail_info'] 邮箱 
 参数: $_POST['site_id_info'] 网站id 
 参数: $_POST['c_comment_info'] 信息 
 参数: $_POST['is_customized_fee'] 信息 
 ----------------------------------------*/
  echo tep_check_new_order_variable_data($_POST['o_id_info'], $_POST['fetch_date'], $_POST['c_comments'], $_POST['c_title'], $_POST['c_status_id'], $_POST['c_payment'], $_POST['c_mail_info'], $_POST['c_name_info'], $_POST['site_id_info'], $_POST['c_comment_info'], $_POST['is_customized_fee']); 
} else if ($_GET['action'] == 'check_edit_order_variable_data') {
/*-----------------------------------------
 功能: 检查变量是否为空
 参数: $_POST['c_comments'] 内容 
 参数: $_POST['o_id'] 订单id 
 参数: $_POST['c_title'] 标题 
 参数: $_POST['c_status_id'] 状题id 
 参数: $_POST['c_payment'] 方法信息 
 参数: $_POST['c_name_info'] 名字信息 
 参数: $_POST['c_mail_info'] 邮箱信息 
 参数: $_POST['c_comment_info'] 信息 
 参数: $_POST['is_customized_fee'] 信息 
 ----------------------------------------*/
  echo tep_check_edit_order_variable_data($_POST['o_id'], $_POST['c_comments'], $_POST['c_title'], $_POST['c_status_id'], $_POST['c_payment'], $_POST['c_name_info'], $_POST['c_mail_info'], $_POST['c_comment_info'], $_POST['is_customized_fee']); 
} else if ($_GET['action'] == 'check_is_numeric') {
/*-----------------------------------------
 功能: 检查变量是否大于等于0
 参数: $_POST['o_params'] 内容 
 ----------------------------------------*/
  $show_error_single = false; 
  if (is_numeric($_POST['o_param'])) {
    if ($_POST['o_param'] < 0) {
      $show_error_single = true; 
    } 
  } else {
    $show_error_single = true; 
  }
  if ($show_error_single) {
    echo TEXT_INPUT_IS_NO_NUMERIC; 
  } else {
    echo ''; 
  }
} else if ($_GET['action'] == 'check_products_profit') {
/*-----------------------------------------
 功能: 检查商品价格是否低于指定利润率
 参数: $_POST['products_id'] 商品id 
 参数: $_POST['new_price'] 价格 
 ----------------------------------------*/
  echo check_products_price_info($_POST['products_id'], $_POST['new_price']);
} else if ($_GET['action'] == 'check_list_products_profit') {
/*-----------------------------------------
 功能: 检查商品列表里的商品价格是否低于指定利润率
 参数: $_POST['products_id_list'] 商品id列表 
 参数: $_POST['product_price_list'] 商品价格列表 
 ----------------------------------------*/
  $show_error_str = ''; 
  if (!empty($_POST['product_id_list'])) {
    $product_list_array = explode('|||', $_POST['product_id_list']); 
    $product_price_array = explode('|||', $_POST['product_price_list']); 
    foreach ($product_list_array as $p_key => $p_value) {
      $tmp_show_error_str = check_products_price_info($p_value, $product_price_array[$p_key]);
      if (!empty($tmp_show_error_str)) {
        $show_error_str .= $tmp_show_error_str."\n"; 
      }
    }
  }
  echo $show_error_str;
} else if ($_GET['action'] == 'check_order_products_profit') {
/*-----------------------------------------
 功能: 检查商品列表里的商品价格是否低于指定利润率
 参数: $_POST['products_list_str'] 商品列表 
 参数: $_POST['price_list_str'] 商品价格列表 
 参数: $_POST['num_list_str'] 商品数量列表 
 ----------------------------------------*/
  $show_error_str = ''; 
  if ($_POST['price_list_str'] != '') {
    $price_info_array = explode('|||', $_POST['price_list_str']); 
    $product_info_array = explode('|||', $_POST['products_list_str']); 
    $num_info_array = explode('|||', $_POST['num_list_str']); 
    foreach ($product_info_array as $pi_key => $pi_value) {
      $tmp_check_str = substr($pi_value, 0, 2); 
      if ($tmp_check_str != 'o_') {
        $order_products_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_products_id = '".$pi_value."'"); 
        $order_products_res = tep_db_fetch_array($order_products_raw); 
        $tmp_products_id = $order_products_res['products_id'];  
      } else {
        $tmp_products_id = substr($pi_value, 2); 
      }
      if (isset($num_info_array[$pi_key])) {
        if (!empty($num_info_array[$pi_key])) {
          $tmp_show_error_str = check_products_price_info((int)$tmp_products_id, $price_info_array[$pi_key]);
          if ($tmp_show_error_str) {
            $show_error_str .= $tmp_show_error_str."\n"; 
          }
        }
      }
    }
  }
  if ($show_error_str != '') {
    echo $show_error_str."\n".ERROR_WARNING_TEXT;
  }
} else if ($_GET['action'] == 'check_category_to_products_profit') {
/*-----------------------------------------
 功能: 检查商品价格是否低于指定利润率
 参数: $_POST['product_flag'] 标识 
 参数: $_POST['new_price'] 价格 
 参数: $_POST['p_relate_id'] 关联id 
 参数: $_POST['num_value'] 数值 
 ----------------------------------------*/
  echo check_new_products_price_info($_POST['product_flag'], $_POST['new_price'], $_POST['p_relate_id'], $_POST['num_value']);
} else if ($_GET['action'] == 'check_single_products_profit') {
/*-----------------------------------------
 功能: 检查商品价格是否低于指定利润率
 参数: $_POST['products_id'] 商品id 
 参数: $_POST['new_price'] 价格 
 参数: $_POST['relate_new_price'] 关联价格 
 ----------------------------------------*/
  $error_str = '';
  $origin_product_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$_POST['products_id']."'"); 
  $origin_product = tep_db_fetch_array($origin_product_raw); 
  if ($origin_product) {
    $relate_product_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$origin_product['relate_products_id']."'"); 
    $relate_product = tep_db_fetch_array($relate_product_raw); 
    if ($relate_product) {
      $origin_product_error = check_single_products_price_info($_POST['products_id'], $_POST['new_price'], $_POST['relate_new_price']);
    }
    if (!empty($origin_product_error)) {
      $error_str = $origin_product_error; 
    }
  }
  echo $error_str;
}else if ($_GET['action'] == 'has_pimage'){
/*-----------------------------------------
 功能: 检查商品图片是否存在多个同名
 参数: $_POST['image_name'] 全部搜索图片名
 ----------------------------------------*/
  if (isset($_SESSION['site_permission'])) {
    $site_arr = $_SESSION['site_permission'];  
  } else {
    $site_arr = '';  
  }
  $tmp_single = editPermission($site_arr, $_POST['site_id']); 
  if ($tmp_single) {
    $tmp_info = 1; 
  } else {
    $tmp_info = 0; 
  }
  if(isset($_POST['image_name'])&&$_POST['image_name']!=''){
    $image_name = $_POST['image_name'];
    $sql = "select products_id from ".TABLE_PRODUCTS_DESCRIPTION." 
      where (products_image ='".$image_name."' 
      OR products_image2='".$image_name."'
      OR products_image3='".$image_name."')
      and site_id = '".$_POST['site_id']."' limit 1";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      echo 'true|||'.$tmp_info;
    }else{
      echo 'false|||'.$tmp_info;
    }
  }else{
    $image_name = $_POST['image_value'];
    $sql = "select count(*) as con from ".TABLE_PRODUCTS_DESCRIPTION.
      " where products_image ='".$image_name."' 
      and site_id = '".$_POST['site_id']."'"; 
    $sql2 = "select count(*) as con from ".TABLE_PRODUCTS_DESCRIPTION.
    " where products_image2='".$image_name."'
    and site_id = '".$_POST['site_id']."'";
    $sql3 = "select count(*) as con from ".TABLE_PRODUCTS_DESCRIPTION.
      " where products_image3='".$image_name."'
      and site_id = '".$_POST['site_id']."'";
    $res = tep_db_fetch_array(tep_db_query($sql));
    $res2 = tep_db_fetch_array(tep_db_query($sql2));
    $res3 = tep_db_fetch_array(tep_db_query($sql3));
    $con = $res['con']+$res2['con']+$res3['con'];
    $sql_self = "select products_id from ".TABLE_PRODUCTS_DESCRIPTION.
      " where ".$_POST['col_name']."='".$image_name."'
      and products_id='".$_POST['pid']."' 
      and site_id = '".$_POST['site_id']."' limit 1";
    $query_self = tep_db_query($sql_self);
    if($res_self=tep_db_fetch_array($query_self)){
      $con--;
    }
    if($con > 0){
      echo 'true|||'.$tmp_info;
    }else{
      echo 'false|||'.$tmp_info;
    }
  }
}else if ($_GET['action'] == 'change_pimage'){
  $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set ".$_POST['col_name']."='' where products_id='".$_POST['pid']."' and site_id='".$_POST['site_id']."'";
  tep_db_query($update_sql);
}else if ($_GET['action'] == 'check_play_sound'){
/*-----------------------------------------
 功能: 检查是否播放声音
 参数: 无
 ----------------------------------------*/
  if (PERSONAL_SETTING_NOTIFICATION_SOUND == '') {
    echo '1'; 
  } else {
    $sound_array = @unserialize(PERSONAL_SETTING_NOTIFICATION_SOUND);
    if (isset($sound_array[$ocertify->auth_user])) {
      echo $sound_array[$ocertify->auth_user]; 
    } else {
      echo '1'; 
    }
  }
}else if ($_GET['action'] == 'has_cimage'){
/*-----------------------------------------
 功能: 检查分类图片是否存在多个同名
 参数: $_POST['image_name'] 全部搜索图片名
 ----------------------------------------*/
  if (isset($_SESSION['site_permission'])) {
    $site_arr = $_SESSION['site_permission'];  
  } else {
    $site_arr = '';  
  }
  $tmp_single = editPermission($site_arr, $_POST['site_id']); 
  if ($tmp_single) {
    $tmp_info = 1; 
  } else {
    $tmp_info = 0; 
  }
  if(isset($_POST['image_name'])&&$_POST['image_name']!=''){
    $image_name = $_POST['image_name'];
    $sql = "select categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where (categories_image ='".$image_name."' OR categories_image2='".$image_name."') and site_id = '".$_POST['site_id']."' limit 1";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      echo 'true|||'.$tmp_info;
    }else{
      echo 'false|||'.$tmp_info;
    }
  }else{
    $image_name = $_POST['image_value'];
    $sql = "select count(*) as con from ".TABLE_CATEGORIES_DESCRIPTION.  " where categories_image='".$image_name."' and site_id = '".$_POST['site_id']."'";
    $sql2 = "select count(*) as con from ".TABLE_CATEGORIES_DESCRIPTION.  " where categories_image2='".$image_name."' and site_id = '".$_POST['site_id']."'";
    $res = tep_db_fetch_array(tep_db_query($sql));
    $res2 = tep_db_fetch_array(tep_db_query($sql2));
    $con = $res['con']+$res2['con'];
    $sql_self = "select categories_id from ".TABLE_CATEGORIES_DESCRIPTION.  " where ".$_POST['col_name']."='".$image_name."' and categories_id='".$_POST['e_cid']."' and site_id = '".$_POST['site_id']."' limit 1";
    $query_self = tep_db_query($sql_self);
    if($res_self=tep_db_fetch_array($query_self)){
      $con--;
    }
    if($con > 0){
      echo 'true|||'.$tmp_info;
    }else{
      echo 'false|||'.$tmp_info;
    }
  }
}else if ($_GET['action'] == 'change_cimage'){
  $update_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set ".$_POST['col_name']."='' where categories_id='".$_POST['e_cid']."' and site_id='".$_POST['site_id']."'";
  tep_db_query($update_sql);
}
