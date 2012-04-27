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
  // update orders_comment
  tep_db_perform('orders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
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
} else if ($_GET['orders_id'] && isset($_POST['orders_credit'])) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$_GET['orders_id']."'"));
  tep_db_perform('customers', array('customers_fax' => $_POST['orders_credit']), 'update', "customers_id='".$order['customers_id']."'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
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
			tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';"><?php echo tep_get_site_romaji_by_id($orders['site_id']);?></td>
                                                                                                                        <td style="border-bottom:1px solid #000000;background-color: darkred;"
                                                                                                                        class="dataTableContent" onClick="chg_td_color(<?php echo
			$orders['orders_id']; ?>); window.location.href='<?php echo
			tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))
					. 'oID='.$orders['orders_id']);?>';">
                                                                                                                        <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
    <a href="<?php echo tep_href_link('orders.php', 'cEmail=' . tep_output_string_protected($orders['customers_email_address']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', '過去の注文');?></a>
                                                                                                                                                                                                           <?php
                                                                                                                                                                                                           if (false) {?>
                                                                                                                                                                                                           &nbsp;<a href="<?php echo tep_href_link('customers.php', 'page=1&cID=' . tep_output_string_protected($orders['customers_id']) . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'arrow_r_red.gif', '顧客情報');?></a>&nbsp;&nbsp;
                                                                                                                                                                                                           <?php }?>
                                                                                                                                                                                                           <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
                                                                                                                                                                                                           <font color="#999">
      <?php }?>
      <a style="text-decoration:underline;" href="<?php echo tep_href_link('customers.php', 'page=1&cID='.tep_output_string_protected($orders['customers_id']).'&action=edit');?>"><b><?php echo tep_output_string_protected($orders['customers_name']);?></b></a>
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
							'oID='.$orders['orders_id']);?>';"><?php echo $next_mark; ?><font color="<?php echo !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>"><?php echo tep_datetime_short($orders['torihiki_date']); ?></font></td>
                                                                                                                                                                                                                                                                                             <td style="border-bottom:1px solid
#000000;background-color: darkred;" class="dataTableContent" align="left"
                                                                                                                                                                                                                                                                                             onClick="chg_td_color(<?php echo $orders['orders_id'];?>);
				window.location.href='<?php echo
					tep_href_link(FILENAME_ORDERS,
							tep_get_all_get_params(array('oID', 'action')) .
							'oID='.$orders['orders_id']);?>';"><?php if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES . 'icon_hand.gif', '取引待ち'); } else { echo '&nbsp;'; } ?></td>
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
#000000;background-color: darkred;" class="dataTableContent" align="right" onmouseover="if(popup_num == 1) showOrdersInfo('<?php echo $orders['orders_id']?>', this, 0, '<?php echo urlencode(tep_get_all_get_params(array('oID', 'action')));?>');" onmouseout="if(popup_num == 1) hideOrdersInfo(0);"> <?php 
                                                                                                                                                         echo '<a href="javascript:void(0);" onclick="showOrdersInfo(\''.$orders['orders_id'].'\', this, 1, \''.urlencode(tep_get_all_get_params(array('oID', 'action'))).'\')">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;</td>
    </tr>
        <?php 
        }
}  else if(isset($_GET['action'])&&$_GET['action'] == 'getallpwd'){
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
}else if(isset($_GET['action'])&&$_GET['action'] == 'faq_c_is_set_romaji'){
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $romaji = str_replace('<33333333>','/',$romaji);
  $romaji = str_replace('<44444444>','%',$romaji);
  $romaji = str_replace('<55555555>','#',$romaji);
  $romaji = str_replace('<66666666>','?',$romaji);
  $romaji = str_replace('<77777777>',' ',$romaji);
  $romaji = str_replace('<88888888>',"'",$romaji);
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
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $romaji = str_replace('<33333333>','/',$romaji);
  $romaji = str_replace('<44444444>','%',$romaji);
  $romaji = str_replace('<55555555>','#',$romaji);
  $romaji = str_replace('<66666666>','?',$romaji);
  $romaji = str_replace('<77777777>',' ',$romaji);
  $romaji = str_replace('<88888888>',"'",$romaji);
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
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $romaji = str_replace('<33333333>','/',$romaji);
  $romaji = str_replace('<44444444>','%',$romaji);
  $romaji = str_replace('<55555555>','#',$romaji);
  $romaji = str_replace('<66666666>','?',$romaji);
  $romaji = str_replace('<77777777>',' ',$romaji);
  $romaji = str_replace('<88888888>',"'",$romaji);
  $replace_str = '\s|　';
  if(preg_match('/[\'\&\+\/\%\#\?\.\s]/u',$romaji)){
    $new_romaji =
      preg_replace('/[\'\&\+\/\%\#\?\.\s]/u','-', $romaji);
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
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $romaji = str_replace('<33333333>','/',$romaji);
  $romaji = str_replace('<44444444>','%',$romaji);
  $romaji = str_replace('<55555555>','#',$romaji);
  $romaji = str_replace('<66666666>','?',$romaji);
  $romaji = str_replace('<77777777>',' ',$romaji);
  $romaji = str_replace('<88888888>',"'",$romaji);
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
  $romaji = $_POST['romaji'];
  $romaji = str_replace('<11111111>','&',$romaji);
  $romaji = str_replace('<22222222>','+',$romaji);
  $romaji = str_replace('<33333333>','/',$romaji);
  $romaji = str_replace('<44444444>','%',$romaji);
  $romaji = str_replace('<55555555>','#',$romaji);
  $romaji = str_replace('<66666666>','?',$romaji);
  $romaji = str_replace('<77777777>',' ',$romaji);
  $romaji = str_replace('<88888888>',"'",$romaji);
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
    if(!in_array('admin',$one_time_arr)){
      $sql .= " and p.permission = '15' ";
    }else if(in_array('chief',$one_time_arr)){
      $sql .= " and (p.permission = '15' or p.permission='10')";
    }else {
      $sql .= " and p.permission = '15' ";
    }
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
  tep_insert_pwd_log($_POST['one_time_pwd'],$ocertify->auth_user,true,$_POST['page_name']);
} else if (isset($_GET['action'])&&$_GET['action']=='show_right_order_info') {
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
  $onsuit = false;
  $tnsuit = false;
  $notfinish = false;
  $ids = $_POST['oid'];
  $ids_array = explode('_',$ids);
  foreach($ids_array as $oid){
    if($oid==''){continue;}
    unset($orders_info_raw);
	$orders_info_raw = tep_db_query("select payment_method  from ".TABLE_ORDERS." where orders_id = '".$oid."'"); 
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
  }
  if(!$onsuit && !$tnsuit){
    echo urlencode($orders_info['payment_method']).'_'.$type.'_';
    echo $notfinish?0:$finish_first;
  }
  //取得 支付类型 及 支付方法
} else if  (isset($_GET['action'])&&$_GET['action']=='get_oa_groups') {
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
  $ids = $_GET['ids'];
  //  $ids = $_POST['ids'];
  $ids_array = explode('_',$ids);
  $sql  = 'select * from oa_item where group_id = "'.$_GET['group_id'].'" and type!="autocalculate" order by  ordernumber';
  require_once 'oa/DbRecord.php';
  require_once 'oa/HM_Form.php'; 
  require_once "oa/HM_Item.php";
  require_once 'oa/HM_Group.php';
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
  //  echo $bigRender;

} else if (isset($_GET['action'])&&$_GET['action']=='show_right_preorder_info') {
  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['oid']."'"); 
  $orders_info = tep_db_fetch_array($orders_info_raw); 
  require(DIR_WS_FUNCTIONS . 'visites.php');
  tep_get_pre_orders_products_string($orders_info, true);

}else if (isset($_GET['action'])&&$_GET['action']=='change_mail_list'){
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
  $_SESSION['mail_post_value']['from'] = $_POST['mail_info_from'];
  $_SESSION['mail_post_value']['subject'] = $_POST['mail_info_subject'];
  $_SESSION['mail_post_value']['message'] = $_POST['mail_info_message'];
}else if (isset($_GET['action'])&&$_GET['action']=='mail_checkbox_validate'){
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
  $html_str .= '<b>'.$campaign_res['title'].'</b>'; 
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
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td  style="padding-left:20%;">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="show_new_campaign(\''.$_POST['st_id'].'\')"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_campaign_info('.$campaign_res['id'].', 1, '.$campaign_res['site_id'].');"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_CAMPAIGN.'\')) window.location.href = \''.tep_href_link(FILENAME_CAMPAIGN, 'action=deleteconfirm&campaign_id='.$campaign_res['id']).'&site_id='.$_POST['st_id'].'\';"').'</a>'; 
  $html_str .= tep_draw_hidden_field('campaign_id', $campaign_res['id']); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='new_campaign') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_CAMPAIGN); 
  $html_str = '';
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>';
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= '<b>'.HEADING_TITLE.'</b>'; 
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
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td style="padding-left:20%;">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_campaign_info(0, 0, '.$_POST['site_id'].');"').'</a>&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='check_campaign') {
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
  $html_str .= tep_draw_checkbox_field('restock', '', true);
  $html_str .= '</td>';
  $html_str .= '<td>';
  $html_str .= TEXT_INFO_RESTOCK_PRODUCT_QUANTITY; 
  $html_str .= '</td>';
  $html_str .= '</tr>'; 
  $html_str .= '<tr>'; 
  $html_str .= '<td colspan="3">'.tep_html_element_submit(IMAGE_DELETE); 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="cancel_del_order_info(\''.$_POST['oID'].'\', \''.urlencode($param_str).'\')"').'</a>';
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='cancel_del_info') {
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
} else if (isset($_GET['action'])&&$_GET['action']=='new_group') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_GROUP); 
  $html_str = '';
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>';
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= '<b>'.HEADING_TITLE.'</b>'; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= '<a href="javascript:void(0);" onclick="close_group_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>';
  $html_str .= tep_draw_form('option_group', FILENAME_OPTION_GROUP, 'action=insert'); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('name', '', 'id="name"'); 
  $html_str .= '<span id="name_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('title', '', 'id="title"'); 
  $html_str .= '<span id="title_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_IS_PREORDER; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_DESC; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_textarea_field('comment', 'hard', '30', '10'); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_SORT_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('sort_num', '1000', 'size="31" id="sort_num" style="text-align:right;"'); 
  $html_str .= '</td>';
  $html_str .= '</tr>';

  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td style="padding-left:20%;" align="left">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_group_info(0, 0);"').'</a>&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>';
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='edit_group') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_GROUP); 
  $group_raw = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id = '".$_POST['group_id']."'"); 
  $group = tep_db_fetch_array($group_raw);
  
  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'group_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.$p_value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  
  $html_str = '';
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>'; 
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= '<b>'.$group['name'].'</b>'; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= get_option_group_link($group['id'], $_POST['keyword']); 
  $html_str .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="close_group_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>';
  $html_str .= tep_draw_form('option_group', FILENAME_OPTION_GROUP, 'action=update&'.$param_str); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('name', $group['name'], 'id="name"'); 
  $html_str .= '<span id="name_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('title', $group['title'], 'id="title"'); 
  $html_str .= '<span id="title_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_IS_PREORDER; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  if ($group['is_preorder'] == '1') {
    $html_str .= tep_draw_radio_field('is_preorder',1,true,'','id="is_preorder"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,false,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER; 
  } else {
    $html_str .= tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER; 
  }
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_DESC; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_textarea_field('comment', 'hard', '30', '10', $group['comment']); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_GROUP_SORT_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('sort_num', $group['sort_num'], 'size="31" id="sort_num" style="text-align:right;"'); 
  $html_str .= '</td>';
  $html_str .= '</tr>';

  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td style="padding-left:20%;" align="left">';
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="create_option_group();"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_group_info('.$group['id'].', 1);"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION_GROUP.'\')) window.location.href = \''.tep_href_link(FILENAME_OPTION_GROUP, 'action=deleteconfirm&group_id='.$group['id'].'&'.$param_str).'\';"').'</a>'; 
  $html_str .= tep_draw_hidden_field('group_id', $group['id']); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= '</form>';
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='search_group') {
  $json_array = array(); 
  $search_group_query = tep_db_query("select name from ".TABLE_OPTION_GROUP." where name like '%".tep_replace_full_character($_GET['q'])."%' order by created_at desc");
    while ($search_group = tep_db_fetch_array($search_group_query)) {
      $json_array[] = array('name' => $search_group['name']); 
    }
  echo json_encode($json_array); 
} else if (isset($_GET['action'])&&$_GET['action']=='check_group') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_GROUP); 
  $error_array = array();
  $error_array['name'] = '';
  $error_array['title'] = '';
  
  if ($_POST['gname'] == '') {
    $error_array['name'] = ERROR_OPTION_GROUP_IS_NULL;
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
  if ($_POST['gtitle'] == '') {
    $error_array['title'] = ERROR_OPTION_GROUP_IS_NULL;
  }
  echo implode('||', $error_array);
  
} else if (isset($_GET['action'])&&$_GET['action']=='new_item') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_ITEM); 
  require_once('enabledoptionitem.php'); 
  $html_str = '';
  
  $html_str = '<script>$(function() {
      function format_item(item) {
          return item.name;
      }
      $("#title").autocomplete(\'ajax_orders.php?action=search_title\', {
        multipleSeparator: \'\',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format_item(item);
        }
      }).result(function(e, item) {
      });
});</script>'; 
  
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>';
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= '<b>'.HEADING_TITLE.'</b>'; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= '<a href="javascript:void(0);" onclick="close_item_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>'; 
  $html_str .= '</table>';
  
  $html_str .= tep_draw_form('option_item', FILENAME_OPTION_ITEM, 'action=insert&group_id='.$_POST['group_id'], 'post', 'enctype="multipart/form-data"'); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('title', '', 'id="title" class="option_text" autocomplete="off"'); 
  $html_str .= '&nbsp;<a href="javascript:void(0);" onclick="search_item_title(0, 0);">'.tep_html_element_button(IMAGE_SEARCH, 'onclick=""').'</a>'; 
  $html_str .= '<span id="title_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>'; 
  
  $html_str .= '<table id="search_title" cellspacing="0" cellpadding="2" border="0" width="100%" style="padding:0 50px 0; margin-top:-10px;">';
  $html_str .= '</table>'; 
  
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('front_title', '', 'id="front_title" class="option_text"'); 
  $html_str .= '<span id="front_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_TYPE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= '<div id="se_item">'; 
  $html_str .= '<select id="type" name="type" onchange="change_option_item_type(0);">'; 
  $i=0; 
  foreach ($enabled_item_array as $ekey => $evalue) {
    if ($i == 0) {
      $first_item = $ekey; 
    }
    $html_str .= '<option value="'.$ekey.'">'.strtolower($evalue).'</option>'; 
    $i++; 
  }
  $html_str .= '</select>'; 
  $html_str .= '</div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>'; 

  $html_str .= '<table id="show_select" width="100%" cellspacing="0" cellpadding="2" border="0" class="campaign_body">';
  $classname = 'HM_Option_Item_'.ucfirst($first_item);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  $html_str .= $item_instance->prepareFormWithParent($item['id']);
  $html_str .= '</table>'; 
  
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';

  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_PRICE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('price', '', 'id="price" class="option_item_input"').TEXT_MONEY_SYMBOL; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_SORT_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('sort_num', '1000', 'id="sort_num" class="option_item_input"'); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_PLACE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= '<div id="p_type">'; 
  $html_str .= tep_draw_radio_field('place_type', '0', true).OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1').OPTION_ITEM_TYPE_LAST; 
  $html_str .= '</div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td style="padding-left:20%;" align="left">';
  $html_str .= '<input type="hidden" name="is_copy" value="0" id="is_copy">'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_item_info()"').'</a>&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='edit_item') {
  require_once('enabledoptionitem.php'); 
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_ITEM); 
  $item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$_POST['item_id']."'"); 
  $item = tep_db_fetch_array($item_query); 
  $html_str = '';
  
  $html_str = '<script>$(function() {
      function format_item(item) {
          return item.name;
      }
      $("#title").autocomplete(\'ajax_orders.php?action=search_title\', {
        multipleSeparator: \'\',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format_item(item);
        }
      }).result(function(e, item) {
      });
});</script>';
  
  
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_top">';
  $html_str .= '<tr>'; 
  $html_str .= '<td width="20">'; 
  $html_str .= tep_image(DIR_WS_IMAGES . 'icon_info.gif',IMAGE_ICON_INFO,16,16)."&nbsp;";
  $html_str .= '</td>'; 
  $html_str .= '<td align="left">'; 
  $html_str .= '<b>'.$item['title'].'</b>'; 
  $html_str .= '</td>'; 
  $html_str .= '<td align="right">'; 
  $html_str .= get_option_item_link($_POST['group_id'], $item['id']); 
  $html_str .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="close_item_info()">X</a>';  
  $html_str .= '</td>'; 
  $html_str .= '</tr>';
  $html_str .= '</table>';
  $html_str .= tep_draw_form('option_item', FILENAME_OPTION_ITEM, 'action=update&group_id='.$_POST['group_id'], 'post', 'enctype="multipart/form-data"'); 
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= '<input type="hidden" name="item_id" value="'.$item_id.'" id="item_uid">'; 
  $html_str .= TABLE_HEADING_OPTION_ITEM_NAME; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('title', $item['title'], 'id="title" class="option_text" autocomplete="off"'); 
  $html_str .= '&nbsp;<a href="javascript:void(0);" onclick="search_item_title(1, '.$item['id'].');">'.tep_html_element_button(IMAGE_SEARCH, 'onclick=""').'</a>'; 
  $html_str .= '<span id="title_error" style="color:#ff0000;"></span>'; 
  $html_str .= '<input type="hidden" name="is_more" id="is_more" value="0">'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '</table>'; 
  
  $html_str .= '<table id="search_title" cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '</table>'; 
  
  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_TITLE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('front_title', $item['front_title'], 'id="front_title" class="option_text"'); 
  $html_str .= '<span id="front_error" style="color:#ff0000;"></span>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_TYPE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= '<div id="se_item">'; 
  $html_str .= '<select id="type" name="type" onchange="change_option_item_type(0);">'; 
  foreach ($enabled_item_array as $ekey => $evalue) {
    if (strtolower($evalue) == $item['type']) {
      $html_str .= '<option value="'.$ekey.'" selected>'.strtolower($evalue).'</option>'; 
    } else {
      $html_str .= '<option value="'.$ekey.'">'.strtolower($evalue).'</option>'; 
    }
  }
  $html_str .= '</select>'; 
  $html_str .= '</div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  $html_str .= '</table>'; 
  
  $html_str .= '<table id="show_select" width="100%" cellspacing="0" cellpadding="2" border="0" class="campaign_body">';
  $classname = 'HM_Option_Item_'.ucfirst($item['type']);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  $html_str .= $item_instance->prepareFormWithParent($item['id']);
  $html_str .= '</table>'; 

  $html_str .= '<table cellspacing="0" cellpadding="2" border="0" width="100%" class="campaign_body">';
  
  if ($item['type'] != 'radio') {
    $html_str .= '<tr>';
    $html_str .= '<td width="220" align="left">';
    $html_str .= TABLE_HEADING_OPTION_ITEM_PRICE; 
    $html_str .= '</td>';
    $html_str .= '<td align="left">';
    $html_str .= tep_draw_input_field('price', number_format($item['price']), 'id="price" class="option_item_input"').TEXT_MONEY_SYMBOL; 
    $html_str .= '</td>';
    $html_str .= '</tr>';
  } 
  $html_str .= '<tr>';
  $html_str .= '<td width="220" align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_SORT_NUM; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= tep_draw_input_field('sort_num', $item['sort_num'], 'id="sort_num" class="option_item_input"'); 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td align="left">';
  $html_str .= TABLE_HEADING_OPTION_ITEM_PLACE; 
  $html_str .= '</td>';
  $html_str .= '<td align="left">';
  $html_str .= '<div id="p_type">'; 
  if ($item['place_type'] == 0) {
    $html_str .= tep_draw_radio_field('place_type', '0', true).OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1').OPTION_ITEM_TYPE_LAST; 
  } else {
    $html_str .= tep_draw_radio_field('place_type', '0', false).OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1', true).OPTION_ITEM_TYPE_LAST; 
  }
  $html_str .= '</div>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '<tr>';
  $html_str .= '<td>';
  $html_str .= '&nbsp;'; 
  $html_str .= '</td>';
  $html_str .= '<td style="padding-left:20%;" align="left">';
  $html_str .= '<input type="hidden" name="is_copy" value="0" id="is_copy">'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="create_option_item(\''.$_POST['group_id'].'\');"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_item_info();"').'</a>&nbsp;'; 
  $html_str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION_ITEM.'\')) window.location.href = \''.tep_href_link(FILENAME_OPTION_ITEM, 'action=deleteconfirm&item_id='.$item['id'].'&group_id='.$_POST['group_id']).'\';"').'</a>'; 
  $html_str .= '</td>';
  $html_str .= '</tr>';
  
  $html_str .= '</table>';
  $html_str .= '</form>'; 
  echo $html_str;
} else if (isset($_GET['action'])&&$_GET['action']=='change_item') {
  $classname = 'HM_Option_Item_'.ucfirst($_POST['stype']);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  echo $item_instance->prepareFormWithParent($_POST['item_id']);
} else if (isset($_GET['action'])&&$_GET['action']=='handle_option') {
  $option_group_exists_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where name = '".$_POST['keyword']."'");
  if (!tep_db_num_rows($option_group_exists_raw)) {
    $insert_sql = "insert into `".TABLE_OPTION_GROUP."` values(NULL, '".$_POST['keyword']."', '".$_POST['keyword']."', '', '', '1', '1000', '".date('Y-m-d H:i:s', time())."')";
    tep_db_query($insert_sql);
  }
} else if (isset($_GET['action'])&&$_GET['action']=='check_item') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_ITEM); 
  $error_array = array();
  $error_array['title'] = '';
  $error_array['ftitle'] = '';
  $error_array['rname'] = '';
  if ($_POST['ititle'] == '') {
    $error_array['title'] = ERROR_OPTION_ITEM_IS_NULL;
  }
  
  if ($_POST['ifront_title'] == '') {
    $error_array['ftitle'] = ERROR_OPTION_ITEM_IS_NULL;
  }
 
  if ($_POST['r_str'] != '') {
    $r_str = substr($_POST['r_str'], 0, -6);
    $r_str_array = explode('<<<|||', $r_str);
    $unique_array = array_unique($r_str_array);
    $r_count = count($r_str_array);
    $ru_count = count($unique_array); 
    if ($r_count != $ru_count) {
      $error_array['rname'] = ERROR_OPTION_ITEM_RADIO_IS_SAME; 
    }
  }
  echo implode('||', $error_array);
} else if (isset($_GET['action'])&&$_GET['action']=='search_item_title') {
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_ITEM); 
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
    $html_str .= '<td width="220" align="left">';
    $html_str .= TABLE_HEADING_OPTION_ITEM_NAME; 
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
  require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION_ITEM); 
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
  $json_array = array(); 
  $search_item_query = tep_db_query("select title from ".TABLE_OPTION_ITEM." where title like '%".tep_replace_full_character($_GET['q'])."%' order by created_at desc");
  while ($search_item = tep_db_fetch_array($search_item_query)) {
    $json_array[] = array('name' => $search_item['title']); 
  }
  echo json_encode($json_array); 
} 
