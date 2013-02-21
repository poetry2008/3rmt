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

if(count($request_one_time_arr)==1&&$request_one_time_arr[0]=='admin'&&$_SESSION['user_permission']!=15){
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
    forward401();
  }
}
if (!$request_one_time_flag && $_SESSION['user_permission']!=15) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    forward401();
  }
}
if(!in_array('onetime',$request_one_time_arr)&&$_SESSION['user_permission']!=15){
  if(!(in_array('chief',$request_one_time_arr)&&in_array('staff',$request_one_time_arr))){
  if($_SESSION['user_permission']==7&&in_array('chief',$request_one_time_arr)){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if($_SESSION['user_permission']==10&&in_array('staff',$request_one_time_arr)){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
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
if ($_POST['orders_id'] &&
    ($_POST['orders_comment']||$_POST['orders_comment_flag']=='true')) {
/*------------------------------------------------
 功能: 订单注释标志
 参数: $_POST['orders_comment'] 获取orders_comment值
 -----------------------------------------------*/
  // update orders_comment
  tep_db_perform('preorders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  tep_redirect(tep_href_link(FILENAME_PREORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
} else if ($_GET['action'] == 'paydate') {
/*-----------------------------------------------
 功能: 支付日期 
 参数: 无 
 ----------------------------------------------*/
  echo date('Y'.YEAR_TEXT.'n'.MONTH_TEXT.'j'.DAY_TEXT,strtotime(tep_get_pay_day()));
} else if ($_GET['action'] == 'set_quantity' && $_GET['products_id'] && $_GET['count']) {
/*---------------------------------------------
 功能: 设置数量
 参数: $_GET['products_id'] 产品编号值为真
 参数: $_GET['count']  数值为真
 --------------------------------------------*/
  $p  = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$_GET['products_id']."'"));
  $rp = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$p['relate_products_id']."'"));

  if ($rp) {
    //print_r($rp);
    $q  = $rp['products_real_quantity'] + (int)$_GET['count'];
    tep_db_query("update ".TABLE_PRODUCTS." set products_real_quantity='".$q."' where products_id='".$p['relate_products_id']."'");
    //print_r("update ".TABLE_PRODUCTS." set products_real_quantity='".$q."' where products_id='".$p['relate_products_id']."'"); 
  }
} else if ($_GET['orders_id'] && $_POST['orders_credit']) {
/*--------------------------------------------
 功能: 订单信用
 参数: $_GET['orders_id'] 订单编号值为真
 参数: $_POST['orders_credit'] 设置$_POST['orders_credit']
 -------------------------------------------*/
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS." where orders_id='".$_GET['orders_id']."'"));
  tep_db_perform('customers', array('customers_fax' => $_POST['orders_credit']), 'update', "customers_id='".$order['customers_id']."'");
  tep_redirect(tep_href_link(FILENAME_PREORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
} else if ($_GET['orders_id'] && isset($_GET['orders_important_flag'])) {
/*-------------------------------------------
 功能: 订单的重要标志  
 参数: $_GET['orders_id'] 订单编号值为真
 参数: $_GET['orders_important_flag'] 设置$_GET['orders_important_flag']订单标志
 ------------------------------------------*/
  // 重要
  tep_db_perform('preorders', array('orders_important_flag' => $_GET['orders_important_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_care_flag'])) {
/*------------------------------------------
 功能: 订单服务标志
 参数: $_GET['orders_id'] 订单编号值为真
 参数: $_GET['orders_care_flag'] 设置$_GET['orders_care_flag']
 -----------------------------------------*/
  // 注意处理方式
  tep_db_perform('preorders', array('orders_care_flag' => $_GET['orders_care_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['orders_id'] && isset($_GET['orders_wait_flag'])) {
/*------------------------------------------
 功能: 订单等待标志
 参数: $_GET['orders_id'] 订单编号值为真
 参数: $_GET['orders_wait_flag'] 订单标志值
 -----------------------------------------*/
  // 交易等待
  tep_db_perform('preorders', array('orders_wait_flag' => $_GET['orders_wait_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
}  else if ($_GET['orders_id'] && isset($_GET['orders_inputed_flag'])) {
/*------------------------------------------
 功能: 订单填写会员标志
 参数: $_GET['orders_id'] 订单编号值为真
 参数: $_GET['orders_inputed_flag'] 订单会员标志值
 -----------------------------------------*/
  // 输入完成
  tep_db_perform('preorders', array('orders_inputed_flag' => $_GET['orders_inputed_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
} else if ($_GET['action'] == 'delete' && $_GET['orders_id'] && $_GET['computers_id']) {
/*-----------------------------------------
 功能: 删除订单 
 参数: $_GET['orders_id']  订单编号值
 参数: $_GET['computers_id'] 计算机ID值
 ----------------------------------------*/
  tep_db_query("delete from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id='".$_GET['orders_id']."' and computers_id='".(int)$_GET['computers_id']."'");
} else if ($_GET['action'] == 'insert' && $_GET['orders_id'] && $_GET['computers_id']) {
/*----------------------------------------
 功能: 添加订单 
 参数: $_GET['orders_id']  订单编号值
 参数: $_GET['computers_id'] 计算机ID值
 ---------------------------------------*/
  tep_db_query("insert into ".TABLE_PREORDERS_TO_COMPUTERS." (`orders_id`,`computers_id`) VALUES('".$_GET['orders_id']."','".(int)$_GET['computers_id']."')");
} else if ($_GET['action'] == 'last_customer_action') {
/*---------------------------------------
 功能: 最后顾客操作
 参数: 无
 --------------------------------------*/
  echo PREORDER_LAST_CUSTOMER_ACTION;
} else if (isset($_GET['orders_id']) && isset($_GET['work'])) {
  // A, B, C
  $exists_order_work_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_GET['orders_id']."'"); 
  $exists_order_work_res = tep_db_fetch_array($exists_order_work_raw);
  if ($exists_order_work_res) {
    if ($exists_order_work_res['orders_work'] == $_GET['work']) {
      tep_db_query("update `".TABLE_PREORDERS."` set `orders_work` = NULL where `orders_id` = '".$_GET['orders_id']."'"); 
      print('failed');
    } else {
      tep_db_perform('preorders', array('orders_work' => $_GET['work']), 'update', "orders_id='".$_GET['orders_id']."'") && print('success');
    }
  } else {
    tep_db_perform('preorders', array('orders_work' => $_GET['work']), 'update', "orders_id='".$_GET['orders_id']."'") && print('success');
  }
} else if ($_GET['action'] == 'get_new_orders' && $_GET['prev_customer_action']) {
  // ajax在订单列表的顶部插入新订单，如果订单结构发生改变此处需要和orders.php同步修改
  $orders_query = tep_db_query("
			select * from ".TABLE_PREORDERS."
			where date_purchased > '".$_GET['prev_customer_action']."' and is_active = '1'");

  if (isset($_GET['type'])) {
    if (tep_db_num_rows($orders_query)) {
      echo '1'; 
    } else {
      echo '0'; 
    }
    exit; 
  }
  while ($orders = tep_db_fetch_array($orders_query)) {
    if (!isset($orders['site_id'])) {
      $orders = tep_db_fetch_array(tep_db_query("
						select *
						from ".TABLE_PREORDERS." o
						where orders_id='".$orders['orders_id']."'
						"));
    }
    $allorders[] = $orders;
    //if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
    //  $oInfo = new objectInfo($orders);
    //}

    //如果是今天的交易的话，显示红色
    $trade_array = getdate(strtotime(tep_datetime_short($orders['predate'])));
    $today_array = getdate();
    if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
      $today_color = 'red';
      if ($trade_array["hours"] >= $today_array["hours"]) {
        $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif', NEXT_ORDER_TEXT); //标记下个订单
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


    //echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="showOrdersInfo(\''.tep_get_orders_products_string($orders).'\');this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo();this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'page')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
    /*
      echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="showOrdersInfo(\''.$orders['orders_id'].'\', this);this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo();this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'page')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
    */
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
			tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';"><?php echo tep_get_site_romaji_by_id($orders['site_id']);?></td>
                                                                                                                        <td style="border-bottom:1px solid #000000;background-color: darkred;"
                                                                                                                        class="dataTableContent" onClick="chg_td_color(<?php echo
			$orders['orders_id']; ?>); window.location.href='<?php echo
			tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';">
                                                                                                                        <a href="<?php echo tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
    <a href="<?php echo tep_href_link('preorders.php', 'cEmail=' .  tep_output_string_protected($orders['customers_email_address']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', BEFORE_ORDER_TEXT);?></a>
                                                                                                                                                                                                           
<?php if (false) {?>
                                                                                                                                                                                                           &nbsp;<a href="<?php echo tep_href_link('customers.php', 'page=1&cID=' .  tep_output_string_protected($orders['customers_id']) .  '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS .  'arrow_r_red.gif', CUSTOMER_INFO_TEXT);?></a>&nbsp;&nbsp;
                                                                                                                                                                                                           <?php }?>
                                                                                                                                                                                                           <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
                                                                                                                                                                                                           <font color="#999">
      <?php }?>
      <a style="text-decoration:underline;" href="<?php echo tep_href_link('customers.php', 'page=1&cID='.tep_output_string_protected($orders['customers_id']).'&action=edit');?>"><b><?php echo tep_output_string_protected($orders['customers_name']);?></b></a>
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
				window.location.href='<?php echo tep_href_link(FILENAME_PREORDERS,
						tep_get_all_get_params(array('oID', 'action')) .
						'oID='.$orders['orders_id']);?>';">
          <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
          <font color="#999"><?php echo strip_tags(tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'], true));?></font>
                                                                                                                    <?php } else { ?>
                                                                                                                    <?php
                                                                                                                      echo
                                                                                                                      strip_tags(tep_get_pre_ot_total_by_orders_id_no_abs($orders['orders_id'], true));?>
                                                                                                                    <?php }?>
                                                                                                                    </td>
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
                                                                                                                                                                                                                                                                                                                                                                                                                     <td style="border-bottom:1px solid #000000;background-color: darkred;" class="dataTableContent" align="left"
                                                                                                                                                                                                                                                                                             onClick="chg_td_color(<?php echo $orders['orders_id'];?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_PREORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><?php if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES . 'icon_hand.gif', RIGHT_ORDER_INFO_TRANS_WAIT); } else { echo '&nbsp;'; } ?></td>
    <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="left"
                                                                                                                                                                                                     onClick="chg_td_color(<?php echo $orders['orders_id']; ?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_PREORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
                                                                                                                                                   <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="center"
                                                                                                                                                   onClick="chg_td_color(<?php echo $orders['orders_id']; ?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_PREORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>
                                                                                                                                                            <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="center"
                                                                                                                                                            onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)
					window.location.href='<?php echo
					tep_href_link(FILENAME_PREORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';">　</td>
                                                                                                                                                            <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="right"
                                                                                                                                                            onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)
					window.location.href='<?php echo
					tep_href_link(FILENAME_PREORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><font color="<?php echo $today_color; ?>"><?php echo $orders['orders_status_name']; ?></font></td>
                                                                                                                                                         <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="right" onmouseover="if(popup_num == 1) showPreOrdersInfo('<?php echo $orders['orders_id']?>', this, 0 , '<?php echo urlencode(tep_get_all_get_params(array('oID', 'action')));?>');" onmouseout="if(popup_num == 1) hideOrdersInfo(0);">
                                                                                                                                                         <?php
                                                                                                                                                         echo '<a href="javascript:void(0);" onclick="showPreOrdersInfo(\''.$orders['orders_id'].'\', this, 1, \''.urlencode(tep_get_all_get_params(array('oID', 'action'))).'\');">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;</td>
    </tr>
        <?php 
        }
}  else if(isset($_GET['action'])&&$_GET['action'] == 'getallpwd'){
/*--------------------------------------------
 功能: 获得所有密码 
 参数: 无
 -------------------------------------------*/
  $sql = "select u.userid,u.rule,l.letter from ".
    TABLE_USERS." u , ".TABLE_LETTERS." l 
		where u.userid = l.userid and (l.letter != '' or l.letter != null)";
  $result = tep_db_query($sql);
  $arr =array();
  while($row = tep_db_fetch_array($result)){
    $pwd = $row['letter'].make_rand_pwd($row['rule']);
    $arr[] = $pwd;
  }
  $str = implode(',',$arr); 
  echo $str;
}else if(isset($_GET['action'])&&$_GET['action'] == 'getpercent'){
/*-------------------------------------------
 功能: 得到百分比   
 参数: $_POST['cid'] 获取$_POST['cid']值 
 ------------------------------------------*/
  if(isset($_POST['cid'])&&$_POST['cid']){
    $sql = "select sac.percent as percent from ".TABLE_PRODUCTS_TO_CATEGORIES." 
			p2c,set_auto_calc sac,".TABLE_PREORDERS_PRODUCTS." op 
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
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql =  "select * from ".TABLE_FAQ_CATEGORIES." fc,
		".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
			fcd where fc.id=fcd.faq_category_id and
			fc.parent_id='".$_POST['pid']."'      and
			fcd.romaji='".$romaji."' and
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
}else if(isset($_GET['action'])&&$_GET['action'] == 'faq_q_is_set_romaji'){
/*------------------------------------------
 功能: 常见问题罗马字 
 参数: $_POST['romaji']  罗马字值
 -----------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
  $sql = "select * from  
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
  if(tep_db_num_rows(tep_db_query($sql))){
    echo 'true';
  }else{
    echo 'false';
  }
}else if(isset($_GET['action'])&&$_GET['action'] == 'check_romaji'){
/*-------------------------------------------
 功能: 检查罗马字 
 参数: $_POST['romaji']  罗马字值
 ------------------------------------------*/
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $replace_str = '\s|　';
  if(preg_match('/[^\x{4e00}-\x{9fa5}\x{3130}-\x{318F}\x{0800}-\x{4e00}a-zA-Z0-9-？]/u',$romaji)){
    $new_romaji =
      preg_replace('/[^\x{4e00}-\x{9fa5}\x{3130}-\x{318F}\x{0800}-\x{4e00}a-zA-Z0-9-？]/u','-',$romaji);
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
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
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
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
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
  if(isset($_POST['check_str'])&&$_POST['check_str']){
    $check_arr = explode(',',$_POST['check_str']);
    if(in_array('admin',$check_arr)){
      tep_db_query("delete from  ".TABLE_PWD_CHECK." where
					page_name='".$_POST['page_name']."'");
      foreach($check_arr as $value){
        $sql = "insert into ".TABLE_PWD_CHECK."
					(`id`,`check_value`,`page_name`)VALUES
					(null,'".$value."','".$_POST['page_name']."')";
        tep_db_query($sql);
      }
      echo "true";
    }else if(count($check_arr)==1&&$check_arr[0]=='onetime'){
      tep_db_query("delete from  ".TABLE_PWD_CHECK." where
					page_name='".$_POST['page_name']."'");
      foreach($check_arr as $value){
        $sql = "insert into ".TABLE_PWD_CHECK."
					(`id`,`check_value`,`page_name`)VALUES
					(null,'".$value."','".$_POST['page_name']."')";
        tep_db_query($sql);
      }
      echo "true";
    }else{
      echo "noadmin";
    }
  }else{
    echo "noall";
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
  if($ocertify->npermission == 15
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
  if(!(in_array('admin',$one_time_arr)&&in_array('chief',$one_time_arr)&&
       in_array('staff',$one_time_arr))&&in_array('onetime',$one_time_arr)){
    $sql = "select u.userid,u.rule,l.letter from ".
      TABLE_USERS." u , ".TABLE_LETTERS." l,".TABLE_PERMISSIONS." p 
			where u.userid = l.userid 
			and (l.letter != '' or l.letter != null)
			and u.userid=p.userid ";
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
 参数: $_POST['one_time_pwd'] 一次密码 
 参数: $_POST['page_name'] 页面的名称
 ------------------------------------------*/
  tep_insert_pwd_log($_POST['one_time_pwd'],$ocertify->auth_user,true,$_POST['page_name']);
} else if (isset($_GET['action'])&&$_GET['action']=='show_right_order_info') {
/*-------------------------------------------
 功能: 正确的顺序信息 
 参数: $_POST['oid'] 订单编号值 
 ------------------------------------------*/
  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['oid']."'"); 
  $orders_info = tep_db_fetch_array($orders_info_raw); 
  require(DIR_WS_FUNCTIONS . 'visites.php');
  tep_get_pre_orders_products_string($orders_info, true);
} else if (isset($_GET['action'])&&$_GET['action']=='get_oa_type') {
  $onsuit = false;
  $tnsuit = false;
  $notfinish = false;
  $ids = $_POST['oid'];
  $ids_array = explode('_',$ids);
  foreach($ids_array as $oid){
    if($oid==''){continue;}
    unset($orders_info_raw);
	$orders_info_raw = tep_db_query("select payment_method  from ".TABLE_PREORDERS." where orders_id = '".$oid."'"); 
    $finish          = tep_get_preorder_canbe_finish($oid)?1:0;
    //$type            = tep_check_pre_order_type($oid);
    $type            = 4;
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
  }
  if(!$onsuit && !$tnsuit){
    echo urlencode($orders_info['payment_method']).'_'.$type.'_';
    echo $notfinish?0:$finish_first;
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
  //  $ids = $_POST['ids'];
  $ids_array = explode('_',$ids);
  $sql  = 'select * from oa_item where group_id = "'.$_GET['group_id'].'" and type!="autocalculate" order by  ordernumber';
  require_once 'pre_oa/DbRecord.php';
  require_once 'pre_oa/HM_Form.php'; 
  require_once "pre_oa/HM_Item.php";
  require_once 'pre_oa/HM_Group.php';
  $res = tep_db_query($sql);
  $bigRender ='';
  foreach($ids_array as $key=>$oid){
    if ($oid ==''){
      unset($ids_array[$key]);
    }
  }
  while ($item = tep_db_fetch_object($res,'HM_Item')){
    unset($exampleOrder);
    unset($exampleOrderInstead);
    if(count($ids_array)>1){
      foreach($ids_array as $oid){
        $sqlEx = 'select of.orders_id ,of.value from oa_item i,preorders_oa_formvalue of where orders_id = "'.$oid.'" and item_id = '.$item->id .' and of.group_id = "'.$_GET['group_id'].'"' ;
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
      //$orders_info_raw = tep_db_fetch_array(tep_db_query("select oa_form.id from ".TABLE_PREORDERS." o, oa_form  where oa_form.payment_romaji = o.payment_method and o.orders_id = '".$exampleOrder['orders_id']."' and oa_form.formtype=".tep_check_pre_order_type($exampleOrder['orders_id']))); 
      $orders_info_raw = tep_db_fetch_array(tep_db_query("select oa_form.id from ".TABLE_PREORDERS." o, oa_form  where oa_form.payment_romaji = o.payment_method and o.orders_id = '".$exampleOrder['orders_id']."' and oa_form.formtype=4")); 
      $item->init()->loadDefaultValue($exampleOrder['orders_id'],$orders_info_raw['id'],$_GET['group_id']);
    }
    echo "<tr>";
    $bigRender.= $item->init()->render(true).'';
    echo "</tr>";
  }
  //  echo $bigRender;

} else if (isset($_GET['action'])&&$_GET['action']=='show_right_preorder_info') {
/*---------------------------------------------
 功能: 右序信息
 参数: $_POST['oid'] 订单编号
 --------------------------------------------*/
  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['oid']."'"); 
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
    tep_get_pre_orders_products_string($orders_info, true, true, $param_str);
  } else {
    tep_get_pre_orders_products_string($orders_info, true, false, $param_str);
  }

} else if (isset($_GET['action'])&&$_GET['action']=='check_preorder_deadline') {
  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."'"); 
  $orders_info = tep_db_fetch_array($orders_info_raw);
  if ($orders_info['ensure_deadline'] == '0000-00-00 00:00:00') {
    echo 'true'; 
  } else {
    echo 'false';
  }
} else if (isset($_GET['action']) && $_GET['action'] == 'get_nyuuka') {
  $preorder_status_raw = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$_POST['sid']."'");
  $preorder_status = tep_db_fetch_array($preorder_status_raw);
  if ($preorder_status) {
    echo $preorder_status['is_nyuuka']; 
  } else {
    echo 0; 
  }
} else if (isset($_GET['action']) && $_GET['action'] == 'get_mail') {
  $preorder_status_raw = tep_db_query("select * from ".TABLE_PREORDERS_MAIL." where orders_status_id = '".$_POST['sid']."'");
  $preorder_status = tep_db_fetch_array($preorder_status_raw);
  if ($preorder_status) {
    if ($_POST['type']) {
      echo $preorder_status['orders_status_title']; 
    } else {
      $replace_str = date('Y年n月j日',strtotime(tep_get_pay_day()));
      echo str_replace('${PAY_DATE}', $replace_str, $preorder_status['orders_status_mail']); 
    }
  } else {
    echo ''; 
  }
} else if (isset($_GET['action']) && $_GET['action'] == 'show_del_preorder_info') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_PREORDERS); 
  $param_str = ''; 
  foreach ($_POST as $key => $value) {
    if (($key != 'oID') && ($key != 'popup')) {
      $param_str .= $key.'='.$value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  $html_str = TEXT_INFO_DELETE_INTRO.'<br>';
  $html_str .= tep_html_element_submit(IMAGE_DELETE);
  $html_str .= '&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="cancel_del_preorder_info(\''.$_POST['oID'].'\', \''.urlencode($param_str).'\')"').'</a>'; 
  echo $html_str;
} else if (isset($_GET['action']) && $_GET['action'] == 'cancel_del_preorder_info') {
/*---------------------------------------------
 功能: 取消删除序信息 
 参数: $_POST['oID'] 订单编号 
 -------------------------------------------*/
  $param_str = ''; 
  foreach ($_POST as $key => $value) {
    if (($key != 'oID') && ($key != 'popup')) {
      $param_str .= $key.'='.$value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  $preorder_raw = tep_db_query("select is_active from ".TABLE_PREORDERS." where orders_id = '".$_POST['oID']."'"); 
  $preorder = tep_db_fetch_array($preorder_raw);
  if ($preorder['is_active'] == '1') {
    $html_str = '<a href="'.tep_href_link(FILENAME_PREORDERS, $param_str.'&oID='.$_POST['oID'].'&action=edit').'">'.tep_html_element_button(IMAGE_DETAILS).'</a>'; 
  }
  $html_str .= '&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_preorder_info(\''.$_POST['oID'].'\', \''.urlencode($param_str).'\')"').'</a>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='recalc_price') {
/*-------------------------------------------
 功能: 重新计算价格
 参数: $_POST['oid'] 订单编号
 参数: $_POST['opd'] 订单产品编号
 ------------------------------------------*/
  require(DIR_WS_CLASSES . 'payment.php');
  $orders_info_raw = tep_db_query("select currency, currency_value from ".TABLE_PREORDERS." where orders_id = '".$_POST['oid']."'");
  $orders_info = tep_db_fetch_array($orders_info_raw);
  $orders_info_num_rows = tep_db_num_rows($orders_info_raw);
  
  $orders_p_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_products_id = '".$_POST['opd']."'");
  $orders_p = tep_db_fetch_array($orders_p_raw);
  
  if (tep_check_pre_product_type($_POST['opd'])) {
    $p_price = 0 - tep_replace_full_character($_POST['p_price']); 
  } else {
    $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . $_POST['opd'] . "'");
    $product = tep_db_fetch_array($product_query);
    tep_db_free_result($product_query);
    if($product['products_bflag']){
      $p_price = 0 - tep_replace_full_character($_POST['p_price']);
    }else{
      $p_price = tep_replace_full_character($_POST['p_price']); 
    }
  }
  
  $final_price = $p_price + tep_replace_full_character($_POST['op_price']);
   
  $price_array[] = tep_display_currency(number_format(abs($final_price), 2));
   
  if ($final_price < 0) {
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
    
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 
    
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL; 

    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;

    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($_POST['p_final_price']*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;

    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true,$orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    $price_array[] = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price, true,$orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
  } else {
    $price_array[] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']); 
    
    $price_array[] = $currencies->format($final_price*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']); 
    
    $price_array[] = $currencies->format(tep_add_tax($final_price, $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']); 

    $price_array[] = $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']); 
    
    $price_array[] = $currencies->format($_POST['p_final_price']*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']); 
    
    $price_array[] = $currencies->format(tep_add_tax($_POST['p_final_price'], $orders_p['products_tax'])*tep_replace_full_character($_POST['p_num']), true, $orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value']);
    $price_array[] = str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($final_price, true,$orders_info_num_rows > 0 ? $orders_info['currency'] : $_SESSION['create_preorder']['orders']['currency'], $orders_info_num_rows > 0 ? $orders_info['currency_value'] : $_SESSION['create_preorder']['orders']['currency_value'])).TEXT_MONEY_SYMBOL;
  }
  $update_total_num = $_POST['update_total_num'];
  $customers_total_array = explode('|||',$_POST['total_str']);
  array_pop($customers_total_array);
  $customers_total_price_array = explode('|||',$_POST['total_price_str']);
  array_pop($customers_total_price_array);
  foreach($customers_total_array as $customers_key=>$customers_value){

    $_SESSION['preorder_products'][$_POST['oid']]['customer'][$customers_value] = trim($customers_total_price_array[$customers_key]) == '' ? '' : $customers_total_price_array[$customers_key];
  }
  //把option价格存储为SESSION
  $op_str_array = explode('|||',$_POST['op_str']); 
  $op_price_str_array = explode('|||',$_POST['op_price_str']);
  array_pop($op_price_str_array);
  foreach($op_str_array as $key=>$value){

    $_SESSION['preorder_products'][$_POST['oid']]['attr'][$value] = $op_price_str_array[$key];   
  }
  $_SESSION['preorder_products'][$_POST['oid']]['qty'] = tep_replace_full_character($_POST['p_num']);
  $_SESSION['preorder_products'][$_POST['oid']]['price'] = tep_replace_full_character($p_price);
  $_SESSION['preorder_products'][$_POST['oid']]['final_price'] = $final_price;
  $_SESSION['preorder_products'][$_POST['oid']]['ot_subtotal'] = $final_price*tep_replace_full_character($_POST['p_num']);
  $cpayment = payment::getInstance((isset($_POST['session_site_id']) ? $_POST['session_site_id'] : (int)$_SESSION['create_preorder']['orders']['site_id']));
  if(isset($_POST['payment_method'])){
    if(tep_db_num_rows($orders_p_raw) > 0){
      $_SESSION['preorder_products'][$_POST['oid']]['payment_method'] = $_POST['payment_method'];
    }else{
      $_SESSION['create_preorder']['orders']['payment_method'] = $_POST['payment_method'];
    }
    $handle_fee = $cpayment->handle_calc_fee($_POST['payment_method'], $_SESSION['preorder_products'][$_POST['oid']]['ot_subtotal']);
  }
  $handle_fee = $handle_fee == '' ? 0 : $handle_fee;
  $_SESSION['preorder_products'][$_POST['oid']]['ot_total'] = $_SESSION['preorder_products'][$_POST['oid']]['ot_subtotal']+$update_total_num+$handle_fee;
  $price_array[] = $handle_fee;
  echo implode('|||', $price_array);
} else if ($_GET['action'] == 'handle_mark') {
/*----------------------------------------
 功能: 处理标记 
 参数: $_GET['select_mark'] 选择标记 
 参数: $_GET['mark_symbol'] 标记符号
 参数: $_POST['param_other'] 参数
 参数: $_GET['c_site'] SITE_ID
 ---------------------------------------*/
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
        $return_array[] = tep_href_link(FILENAME_PREORDERS, $_POST['param_other'].'mark='.implode('-', $mark_array).((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
      } else {
        if (!empty($_GET['c_site'])) {
          $return_array[] = tep_href_link(FILENAME_PREORDERS, $_POST['param_other'].'site_id='.$_GET['c_site']);
        } else {
          $return_array[] = tep_href_link(FILENAME_PREORDERS, $_POST['param_other']);
        }
      }
    } else {
      $mark_array = $select_mark_array; 
      $mark_array[] = $mark_symbol;
      sort($mark_array);
      $return_array[] = tep_href_link(FILENAME_PREORDERS, $_POST['param_other'].'mark='.implode('-', $mark_array).((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
    }
  } else {
    $return_array[] = 'success';
    $return_array[] = tep_href_link(FILENAME_PREORDERS, $_POST['param_other'].'mark='.$_GET['mark_symbol'].((!empty($_GET['c_site']))?'&site_id='.$_GET['c_site']:''));
  }
  echo implode('|||', $return_array);
} else if ($_GET['action'] == 'read_flag') {
/*-------------------------------------------
 功能: 读取标志 
 参数: $_POST['user'] 用户
 参数: $_POST['flag'] 标志
 参数: $_POST['oid'] 订单编号
 ------------------------------------------*/
  $users_name = $_POST['user'];
  $read_flag = $_POST['flag'];
  $orders_id = $_POST['oid'];
  $read_flag_query = tep_db_query("select read_flag from ". TABLE_PREORDERS ." where orders_id='".$orders_id."'");
  $read_flag_array = tep_db_fetch_array($read_flag_query);
  tep_db_free_result($read_flag_query);
  if($read_flag_array['read_flag'] == ''){

    if($read_flag == 0){
      tep_db_query("update ". TABLE_PREORDERS ." set read_flag='".$users_name."' where orders_id='".$orders_id."'"); 
    }
  }else{

    $read_flag_str_array = explode('|||',$read_flag_array['read_flag']);
    if(!in_array($users_name,$read_flag_str_array) && $read_flag == 0){
      $read_flag_add = $read_flag_array['read_flag'].'|||'.$users_name;
      tep_db_query("update ". TABLE_PREORDERS ." set read_flag='".$read_flag_add."' where orders_id='".$orders_id."'");
    }else{

      unset($read_flag_str_array[array_search($users_name,$read_flag_str_array)]);
      $read_flag_string = implode('|||',$read_flag_str_array);
      tep_db_query("update ". TABLE_PREORDERS ." set read_flag='".$read_flag_string."' where orders_id='".$orders_id."'");
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
    if(PERSONAL_SETTING_PREORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
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
    echo tep_href_link(FILENAME_PREORDERS, $_POST['param_url'].'site_id='.implode('-',$site_array));
  }else{
    echo tep_href_link(FILENAME_PREORDERS, $_POST['param_url']); 
  }
}else if($_GET['action'] == 'orders_session'){
/*------------------------------------------
 功能: 订单会话 
 参数: $_POST['orders_session_type'] 订单会话类型
 参数: $_POST['orders_session_value'] 订单会话值
 -----------------------------------------*/
  $session_type = $_POST['orders_session_type'];
  $session_value = $_POST['orders_session_value'];
  $session_orders_id = $_POST['orders_id'];
  $_SESSION['orders_update_products'][$session_orders_id][$session_type] = $session_value;
}
