<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  //处理商品订阅
  if (!tep_session_is_registered('customer_id')) {
    tep_redirect(tep_href_link(FILENAME_DEFAULT));
    exit;
  }
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
  require(DIR_WS_LANGUAGES . $language . '/change_preorder_success.php');
   
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_FETCH, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_CONFIRM, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE, '');
  //商品订阅的商品列表
  $global_query = tep_db_query("
      SELECT global_product_notifications 
      FROM " . TABLE_CUSTOMERS_INFO . " 
      WHERE customers_info_id = '" . $customer_id . "'
      ");
  $global = tep_db_fetch_array($global_query);

  if ($global['global_product_notifications'] != '1') {
    $orders_query = tep_db_query("
        SELECT orders_id 
        FROM " . TABLE_ORDERS . " 
        WHERE customers_id = '" . $customer_id . "' 
         AND site_id = '".SITE_ID."' 
        ORDER BY date_purchased DESC 
        LIMIT 1
      ");
    $orders = tep_db_fetch_array($orders_query);

    $products_array = array();
    $products_query = tep_db_query("
        SELECT products_id, products_name 
        FROM " . TABLE_ORDERS_PRODUCTS . " 
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
<body><div align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><?php echo tep_draw_form('order', tep_href_link('change_preorder_success.php', 'action=update', 'SSL')); ?> 
        <?php 
        $info_page = tep_db_fetch_array(tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where show_status='1' and romaji = 'change_preorder_success.php' and site_id = '".SITE_ID."'")); 
if ($global['global_product_notifications'] != '1') {
    $info_notify = TEXT_NOTIFY_PRODUCTS . '<br><p class="productsNotifications">';

    $products_displayed = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      if (!in_array($products_array[$i]['id'], $products_displayed)) {
        $info_notify .= tep_draw_checkbox_field('notify[]', $products_array[$i]['id']) . ' ' . $products_array[$i]['text'] . '<br>';
        $products_displayed[] = $products_array[$i]['id'];
      }
    }

    $info_notify .= '</p>';
  } else {
    $info_notify = TEXT_SEE_ORDERS . '<br><br>' . TEXT_CONTACT_STORE_OWNER;
  }
        echo str_replace('${PRODUCTS_INFO}','',str_replace('${PRODUCTS_SUBSCRIPTION}',$info_notify,str_replace('${PROCEDURE}',TEXT_HEADER_INFO,str_replace('${NEXT}',tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE),$info_page['text_information']))));
         ?> 
      </form>
      </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div>
</div>
</body>
</html>
<?php 
if($guestchk == '1') {
  tep_session_unregister('customer_id');
}
?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
