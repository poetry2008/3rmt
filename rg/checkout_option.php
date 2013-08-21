<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'checkout_option.php'); 
  $page_url_array = explode('/',$_SERVER['REQUEST_URI']);
  $_SESSION['shipping_page_str'] = end($page_url_array);
  if ($_GET['action'] == 'check_products_op') {
      $check_products_info = tep_check_less_product_option(); 
      if (!empty($check_products_info)) {
        $notice_msg_array = array(); 
        foreach ($check_products_info as $cpo_key => $cpo_value) {
          $tmp_cpo_info = explode('_', $cpo_value); 
          $notice_msg_array[] = tep_get_products_name($tmp_cpo_info[0]);
        }
        $return_check_array[] = sprintf(NOTICE_LESS_PRODUCT_OPTION_TEXT, implode('、', $notice_msg_array)); 
        $return_check_array[] = implode('>>>', $check_products_info); 
      } else {
        $return_check_array[] = 0; 
      } 
      echo implode('|||', $return_check_array); 
      exit; 
  }
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/option.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  var alert_info_flag = $("#alert_info").val();
  var alert_info_str = $("#alert_info_str").val();
  if(alert_info_flag == '1'){

    alert(alert_info_str); 
    window.location.href = '<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>';
  }
});
<?php //检查不足的option?>
function check_option_change(){ 
  $.ajax({
    url: '<?php echo FILENAME_CHECKOUT_OPTION.'?action=check_products_op';?>',     
    type: 'POST', 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||');  
      if (msg_arr[0] != '0') {
        alert(msg_arr[0]);
        window.location.href = '<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL');?>'; 
      }else{
        document.forms.option_form.submit(); 
      }
    }
  }); 
} 
</script>
<?php
if(isset($_SESSION['shipping_session_flag']) && $_SESSION['shipping_session_flag'] == true){
?>
<script type="text/javascript">
$(document).ready(function(){
  alert("<?php echo TEXT_SESSION_ERROR_ALERT;?>");
});  
</script>
<?php
unset($_SESSION['shipping_session_flag']);
}
?>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <div class="pageHeading"><img align="top" src="images/menu_ico.gif" alt=""><h1><?php echo HEADING_TITLE ; ?></h1></div>
        <div class="comment"> 
        <form name="option_form" action="<?php echo tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'); ?>" method="post" >
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                <tr> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_OPTION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
                </tr> 
  </table>
            </td> 
          </tr>
          <tr> 
            <td><img width="100%" height="10" alt="" src="images/pixel_trans.gif"></td> 
          </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
                <tr> 
                  <td class="main"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></td> 
                  <td class="main" align="right"><a href="javascript:void(0);" onClick="check_option_change();"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></td> 
                </tr> 
              </table></td> 
            </tr> 
        <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td>
          <?php
          //检查商品的OPTION是否改动
          $check_products_option = tep_check_less_product_option();
          $products_array = $cart->get_products();
          //对相同商品OPTION改动的覆盖
          $products_id_array = array();
          for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
            $products_id_str = explode('_',$products_array[$i]['id']);
            $products_id_array[] = $products_id_str[0];
          }
          $products_id_count = array_count_values($products_id_array);
          $products_temp_array = array();
          foreach($products_id_count as $key=>$value){

            if($value >= 2){

              $products_temp_array[] = $key;
            }
          }
          $check_products_option_array = array();
          foreach($check_products_option as $value){

            $check_products_option_str = explode('_',$value);
            $check_products_option_array[] = $check_products_option_str[0];
          } 
          foreach($products_temp_array as $value){

            if(in_array($value,$check_products_option_array)){

              $cart->remove($check_products_option[array_search($value,$check_products_option_array)]); 
            }
          }
          $list_products = $cart->get_products(); 
          for ($j=0, $k=sizeof($list_products); $j<$k; $j++) {
            $belong_option_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".(int)$list_products[$j]['id']."'");  
            $belong_option = tep_db_fetch_array($belong_option_raw);
            
            echo '<div class="option_product_title"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.(int)$list_products[$j]['id']).'">'.$list_products[$j]['name'].'</a></div>';
            
            if ($belong_option) {
              $is_checkout_item_raw = tep_db_query("select id from ".TABLE_OPTION_ITEM." where place_type = '1' and group_id = '".$belong_option['belong_to_option']."' and status = '1'"); 
              if (tep_db_num_rows($is_checkout_item_raw)) {
                $p_cflag = tep_get_cflag_by_product_id((int)$list_products[$j]['id']); 
                echo '<div class="option_render">'; 
                $hm_option->render($belong_option['belong_to_option'], false, 1, $list_products[$j]['id'].'_', $cart, (int)$p_cflag); 
                echo '</div>'; 
              }
            }
          }
          //触发弹出层的条件
          $check_products_info = tep_check_less_product_option(); 
          if (!empty($check_products_info)) {
            $notice_msg_array = array(); 
            foreach ($check_products_info as $cpo_key => $cpo_value) {
              $tmp_cpo_info = explode('_', $cpo_value); 
              $notice_msg_array[] = tep_get_products_name($tmp_cpo_info[0]);
            }
            $alert_info = sprintf(NOTICE_LESS_PRODUCT_OPTION_TEXT, implode('、', $notice_msg_array)); 
            echo '<input type="hidden" id="alert_info" value="1">';
            echo '<input type="hidden" id="alert_info_str" value="'.$alert_info.'">';
          }else{
            echo '<input type="hidden" id="alert_info" value="0">'; 
            echo '<input type="hidden" id="alert_info_str" value="">';
          }
          ?>
          </td>
        </tr>
        </table>
        <table border="0" width="100%" cellspacing="1" cellpadding="2"> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
                <tr> 
                  <td class="main"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></td> 
                  <td class="main" align="right"><a href="javascript:void(0);" onClick="check_option_change();"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a></td> 
                </tr> 
              </table></td> 
            </tr> 
          </table>
        </td>
      </tr>
      </table>
    <input type="hidden" name="action" value="process"> 
    </form>
    </div>
    </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> 
      </td> 
    </tr>
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

