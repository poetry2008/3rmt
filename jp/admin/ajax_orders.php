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
} else if ($_GET['orders_id'] && $_POST['orders_credit']) {
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
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="showOrdersInfo(\''.$orders['orders_id'].'\', this);this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="hideOrdersInfo();this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'page')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
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
  tep_get_orders_products_string($orders_info, true);
}
