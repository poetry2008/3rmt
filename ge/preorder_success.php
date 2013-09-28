<?php
/*
  $Id$

*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_success.php');
  $pe_email = '';
  $preorder_id = 0;  
  
  if (isset($_SESSION['send_preorder_id'])) {
    $preorder_id = $_SESSION['send_preorder_id'];
  }
  
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$preorder_id."' and site_id = '".SITE_ID."'"); 
  $preorder = tep_db_fetch_array($preorder_raw);
 
  if (!$preorder) {
    forward404(); 
  }
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'");
  $preorder_product = tep_db_fetch_array($preorder_product_raw);
  $categories_name = '';

  $ca_path = tep_get_product_path($preorder_product['products_id']);
  
  if (tep_not_null($ca_path)) {
    $ca_path_array = tep_parse_category_path($ca_path); 
  }
  if (isset($ca_path_array)) {
    for ($cnum = 0, $ctnum=sizeof($ca_path_array); $cnum<$ctnum; $cnum++) {
      $categories_query = tep_db_query("
          select categories_name 
          from " .  TABLE_CATEGORIES_DESCRIPTION . " 
          where categories_id = '" .  $ca_path_array[$cnum] . "' 
            and language_id='" . $languages_id . "' 
            and (site_id = ".SITE_ID." or site_id = 0)
          order by site_id DESC
          limit 1" 
      );
      if (tep_db_num_rows($categories_query) > 0) {
        $categories_info = tep_db_fetch_array($categories_query); 

        if ($cnum == 0) {
          $categories_name = $categories_info['categories_name']; 
        }
        
        $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
      } else {
        break;
      }
    }
  }

  //商品信息订阅处理
  if (isset($_GET['action']) && ($_GET['action'] == 'update')) {
    $notify_string = 'action=notify&';
    $notify = $_POST['notify'];
    if (!is_array($notify)) $notify = array($notify);
    for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
      $notify_string .= 'notify[]=' . $notify[$i] . '&';
    }
    if (strlen($notify_string) > 0) $notify_string = substr($notify_string, 0, -1);

    tep_redirect(tep_href_link(FILENAME_DEFAULT, $notify_string));
  }
  
  $breadcrumb->add($preorder_product['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$preorder_product['products_id'])); 
  
  $breadcrumb->add(PREORDER_SUCCESS_ACTIVE_HEAD_TITLE, '');

  //获取商品信息订阅的商品
  if(tep_session_is_registered('customer_id')){

    $customer_id = $_SESSION['customer_id'];
  }else{
    $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".$_GET['from']."' and site_id = '".SITE_ID."'");    
    if (tep_db_num_rows($exists_customer_raw)) {
      $exists_customer_res = tep_db_fetch_array($exists_customer_raw);
      $customer_id = $exists_customer_res['customers_id'];
    }
  }
  $global_query = tep_db_query("
      SELECT global_product_notifications 
      FROM " . TABLE_CUSTOMERS_INFO . " 
      WHERE customers_info_id = '" . $customer_id . "'
      ");
  $global = tep_db_fetch_array($global_query);

  if ($global['global_product_notifications'] != '1') {
    $orders_query = tep_db_query("
        SELECT orders_id 
        FROM " . TABLE_PREORDERS . " 
        WHERE customers_id = '" . $customer_id . "' 
         AND site_id = '".SITE_ID."' 
        ORDER BY date_purchased DESC 
        LIMIT 1
      ");
    $orders = tep_db_fetch_array($orders_query);

    $products_array = array();
    $products_query = tep_db_query("
        SELECT products_id, products_name 
        FROM " . TABLE_PREORDERS_PRODUCTS . " 
        WHERE orders_id = '" . $orders['orders_id'] . "' 
        ORDER BY products_name
      ");
    while ($products = tep_db_fetch_array($products_query)) {
      $products_array[] = array('id' => $products['products_id'],
                                'text' => $products['products_name']);
    }
  }
?>
<?php page_head();?>
</head>
<body>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <div id="main">
      <div id="l_menu">
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      </div>
      <div id="content"><?php echo tep_draw_form('order', tep_href_link(FILENAME_PREORDER_SUCCESS, 'action=update', 'SSL')); ?>
      <div class="headerNavigation">
      <?php echo $breadcrumb->trail(' &raquo; ');?>
      </div>
        <?php 
         $preorder_info = '
            <table class="preorder_active_info" border="0" cellpadding="0" cellspacing="1" width="100%"> 
            <tr> 
            <td colspan="2">'.PREORDER_SUCCESS_APPOINT_CONTENT.'
            <br>
            </td> 
            </tr> 
            <tr> 
            <td width="150"> '.PREORDER_SUCCESS_APPOINT_PRODUCT_NAME.' </td>
            <td>';
                  $show_products_name = tep_get_products_name($preorder_product['products_id']); 
            $preorder_info .= tep_not_null($show_products_name) ? $show_products_name : $preorder_product['products_name'];
            $preorder_info .= '
            </td>
            </tr>';
            $preorder_attributes_raw = tep_db_query("select prea.* from ".
                TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." prea left join ".
                TABLE_OPTION_ITEM." it 
                on prea.option_item_id = it.id
                where prea.orders_id = '".$preorder_id."' 
                order by it.sort_num,it.title"); 
            while ($preorder_attributes = tep_db_fetch_array($preorder_attributes_raw)) {
              $option_info_array = @unserialize(stripslashes($preorder_attributes['option_info'])); 
            $preorder_info .= '
            <tr>
              <td>
               '.$option_info_array['title'].'：'.' 
              </td>
              <td>
              '. str_replace(array("<br>", "<BR>"), '', $option_info_array['value']).' 
              </td>
            </tr>';
            }
            $preorder_info .= '
            <tr> 
            <td>';
            $preorder_info .= PREORDER_SUCCESS_APPOINT_PRODUCT_NUM.'
            </td>
            <td>';
            $preorder_info .= $preorder_product['products_quantity'].PREORDER_SUCCESS_UNIT_TEXT;
            if(isset($preorder_product['products_rate']) &&$preorder_product['products_rate']!=0 &&$preorder_product['products_rate']!=1 &&$preorder_product['products_rate']!=''){
             $preorder_info .=  ' ('.number_format($preorder_product['products_rate']*$preorder_product['products_quantity']).')';
            }
            $preorder_info .= '
            </td>
            </tr>
            <tr>
              <td>
              '. PREORDER_SUCCESS_APPOINT_PAYMENT_NAME.'
              </td>
              <td>
               '.$preorder['payment_method'].'
              </td>
            </tr>
            <tr>
              <td>
              '.PREORDER_SUCCESS_APPOINT_COMMENT.'
              </td>
              <td>
              '.nl2br($preorder['comment_msg']).'
              </td>
            </tr>
            </table>';
            $info_page = tep_db_fetch_array(tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where show_status='1' and romaji = 'preorder_success.php' and site_id = '".SITE_ID."'"));
  if ($global['global_product_notifications'] != '1') {
    $info_notify = TEXT_NOTIFY_PRODUCTS . '<br><p class="productsNotifications">';

    $products_displayed = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      if (!in_array($products_array[$i]['id'], $products_displayed)) {
        $info_notify .=  tep_draw_checkbox_field('notify[]', $products_array[$i]['id']) . ' ' . $products_array[$i]['text'] . '<br>';
        $products_displayed[] = $products_array[$i]['id'];
      }
    }

    $info_notify .= '</p>';
  } else {
    $info_notify = TEXT_SEE_ORDERS . '<br><br>' . TEXT_CONTACT_STORE_OWNER;
  }
          echo str_replace('${PRODUCTS_INFO}',$preorder_info,str_replace('${PRODUCTS_SUBSCRIPTION}','<br><br>'.$info_notify,str_replace('${PROCEDURE}',TEXT_HEADER_INFO,str_replace('${NEXT}',tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE),$info_page['text_information']))));
            ?>
      </form>
      <div id="r_menu">
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      </div>

  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
