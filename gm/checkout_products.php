<?php
/*
  $Id$
  ファイルコードを確認
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
    echo 'alert("'.TEXT_ALERT.'");'."\n";
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
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php


?>
<div id="main-content">
<form action="<?php echo tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'); ?>" method="post" onSubmit="return chara_mess();">

<input type="hidden" name="dummy" value="<?php echo TEXT_DUMMY;?>">
<input type="hidden" name="act" value="chk">

<h2><?php echo HEADING_TITLE ; ?></h2> 

<?php if($error == 'T'){ ?><tr><td align="center" style="color:#FF0000"><?php echo TEXT_ERROR_ONE;?></td></tr><?php } ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link">
      <tr>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES .
                  'checkout_bullet.gif',''); ?></td> 
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
            </tr> 
          </table>
        </td> 
        <td width="20%">
               <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%">  
        <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%">  
            
        <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>

       </td> 
        <td width="20%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%">  
        <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
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
	<div id="hm-checkout-warp">
        <div class="checkout-title"><?php echo TEXT_ORDERS_SUBMIT_THREE;?></div>
  <div class="checkout-bottom"> <?php echo
  tep_image_submit('button_continue_02.gif',IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"'); ?></div>  
  </div>
      <div class="checkout-conent">
           <?php
          foreach($cart as $key => $val){
            if($key == 'contents'){
            foreach($val as $key2 => $val2){
            //ccdd
            $cp_result = tep_get_product_by_id($key2, SITE_ID, $languages_id);
        ?>
          <div class="checkout-list"><?php echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO,
          'products_id=' .  $cp_result['products_id']) . '">' .
            tep_image(DIR_WS_IMAGES . 'products/' . $cp_result['products_image'],
                $cp_result['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT)
            . '</a>'; ?>
		   <div class="checkout-title" style="width:80%;">
			 <?php
				echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $cp_result['products_id']) . '"><b><u>' . $cp_result['products_name'] . '</u></b></a><br>';
				if($cp_result['products_cflag'] == 1){
				  echo TEXT_CHARACTER . tep_draw_input_field('cname_' . $key2,'','id="cname_' . $key2 . '" style="width:40%;"');
				}
			  ?>
           </div>
			</div>
           <?php         
            }
          }
          }
        ?>  
          <p><?php echo TEXT_ORDERS_PROMPT_ONE;?></p>
          <h3 style="margin:15px 0;"><b><?php echo TEXT_ORDERS_PROMPT_TWO;?></b></h3>
        <ul>
          <li>
          <?php echo TEXT_ORDERS_PROMPT_THREE;?> 
          </li>
          <li>
          <?php echo TEXT_ORDERS_PROMPT_FOUR;?> 
          </li>
          <li>
          <?php echo TEXT_ORDERS_PROMPT_FIVE;?> 
          </li>
        </ul>
        <p>
        <span class="redtext">※</span>&nbsp;<?php echo TEXT_ORDERS_PROMPT_SIX;?>
  </p>
</div>
<div id="hm-checkout-warp">
  <div class="checkout-title">
  <?php echo TEXT_ORDERS_SUBMIT_THREE;?> 
  </div>
  <div class="checkout-bottom"> <?php echo
  tep_image_submit('button_continue_02.gif',IMAGE_BUTTON_CONTINUE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"'); ?></div>  
  </div>
</form>
  </div>


</div>
<?php include('includes/float-box.php');?>
</div>
<!-- body_text_eof //--> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //--> 
  <!-- footer //--> 
   <!-- footer_eof //--> 
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

