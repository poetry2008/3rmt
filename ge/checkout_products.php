<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  // if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  // if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }
  
// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        break;
      }else{
        $n_products_id = tep_get_prid($products[$i]['id']);
        $n_products_sum = 0;
        for($j=0;$j<$n;$j++){
          if($n_products_id == tep_get_prid($products[$j]['id'])){
            $n_products_sum += intval($products[$j]['quantity']);
          }
        }
        if(tep_check_stock($products[$i]['id'], $n_products_sum)){
          tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        }
      }
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PRODUCTS);
  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CHECKOUT_PRODUCTS));
  
  $error = 'F';

  if (!isset($_POST['act'])) $_POST['act'] = NULL;
  if($_POST['act'] == 'chk'){
  foreach($_POST as $value){
    if($value == ""){
      $error = 'T';
    }
  }
    
  if($error == 'F'){
    unset($_SESSION['character']);
    
    foreach($cart as $key => $val){
      if($key == 'contents'){
        foreach($val as $key2 => $val2){
        $_SESSION['character'][$key2] = $_POST['cname_' . $key2]; 
      }
      }
    }
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL', true, false));    
  }
  }
?>
<?php page_head();?>
<script type="text/javascript">
function chara_mess(){
<?php
foreach($cart as $key => $val){
  if($key == 'contents'){
    foreach($val as $key2 => $val2){
    //ccdd
    $cp_result = tep_get_product_by_id($key2, SITE_ID, $languages_id);
    if($cp_result['products_cflag'] == 1){
      $cid = 'cname_' . $key2;
    
    echo 'if(document.getElementById(\'' . $cid . '\').value == ""){'."\n";
    echo 'alert("キャラクター名を入力して下さい。");'."\n";
    echo "document.getElementById('$cid').focus();"."\n";
    echo 'return false;'."\n";
    echo '}'."\n";
    
    }
  }
  }
}
?>
}
</script>
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<!-- left_navigation -->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content">
<form action="<?php echo tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'); ?>" method="post" onSubmit="return chara_mess();">
<input type="hidden" name="dummy" value="あいうえお眉幅">
<input type="hidden" name="act" value="chk">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
<?php if($error == 'T'){ ?><tr><td class="main" align="center" style="color:#FF0000">入力漏れがあります。キャラクター名は、全て入力して下さい。</td></tr><?php } ?>
<tr>
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link">
      <tr>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
            </tr> 
          </table>
        </td> 
        <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr class="box_des">
        <td align="center" nowrap="nowrap" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_PRODUCTS; ?></td>
        <td align="center" nowrap="nowrap" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_DELIVERY; ?></td>
        <td align="center" nowrap="nowrap" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
        <td align="center" nowrap="nowrap" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
        <td align="center" nowrap="nowrap" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
      <tr> 
        <td class="main"><b>ご注文の手続きを進めてください。</b><div style="margin-top:5px;">「次へ進む」をクリックして取引日時の選択へ。</div></td> 
        <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
      </tr> 
    </table>
  </td>
</tr>
      <tr>
        <td>
        <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
        <?php
          foreach($cart as $key => $val){
            if($key == 'contents'){
            foreach($val as $key2 => $val2){
            //ccdd
            $cp_result = tep_get_product_by_id($key2, SITE_ID, $languages_id);
        ?>
          <tr>
            <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main"><?php echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $cp_result['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $cp_result['products_image'], $cp_result['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
          <td class="main">
          <?php
            echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $cp_result['products_id']) . '"><b><u>' . $cp_result['products_name'] . '</u></b></a><br>';
            if($cp_result['products_cflag'] == 1){
              echo TEXT_CHARACTER . tep_draw_input_field('cname_' . $key2,'','id="cname_' . $key2 . '"');
            }
          ?>
          </td>
          </tr>
          <tr>
            <td colspan="2" class="main">&nbsp;</td>
          </tr>
        <?php         
            }
          }
          }
        ?>  
        </table>
        <p class="redtext">ご入力されましたキャラクター名にお間違えはございませんか？</p>
        <h3>よくある間違い</h3>
        <ul>
          <li>
            スペル間違い。記号や数字の有無。
          </li>
          <li>
            -　（ハイフン）と　_　（アンダーバー）の入力間違い。
          </li>
          <li>
            ・　（中点）と　.　（ドット）の入力間違い。
          </li>
        </ul>
        <p>
    <span class="redtext">※</span>&nbsp;キャラクター名の入力不要な商品が一部ございます。「入力フォーム」が表示されない場合は「次へ進む」をクリックしてください。
  </p>
  </td>
</tr>
<tr>
  <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
      <tr> 
        <td class="main"><b>ご注文の手続きを進めてください。</b><div style="margin-top:5px;">「次へ進む」をクリックして取引日時の選択へ。</div></td> 
        <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
      </tr> 
    </table>
  </td>
</tr>
<tr> 
  <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
</table>
</form>
</div>
<!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
