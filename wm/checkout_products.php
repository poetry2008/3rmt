<?php
/*
  $Id: checkout_products.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  // if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  // if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }
  
// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
        break;
      }
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PRODUCTS);
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CHECKOUT_PRODUCTS));
  
  $error = 'F';
  
  if (!isset($HTTP_POST_VARS['act'])) $HTTP_POST_VARS['act'] = NULL;//del notcie
  if($HTTP_POST_VARS['act'] == 'chk'){
	foreach($HTTP_POST_VARS as $value){
	  if($value == ""){
	    $error = 'T';
	  }
	}
	
	if($error == 'F'){
	  unset($_SESSION['character']);
	  
	  foreach($cart as $key => $val){
	    if($key == 'contents'){
	      foreach($val as $key2 => $val2){
		    $_SESSION['character'][$key2] = $HTTP_POST_VARS['cname_' . $key2]; 
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
	  $cp_query = tep_db_query("select p.products_id,p.products_image,p.products_date_added,p.products_price,p.products_cflag,pd.products_name from " . TABLE_PRODUCTS . " p," . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '".$key2."' and p.products_id = pd.products_id");
	  $cp_result = tep_db_fetch_array($cp_query);
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
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="table"> 
            <?php
			  if($error == 'T'){
			?>
			<tr>
			  <td class="main" align="center" style="color:#FF0000">入力漏れがあります。キャラクター名は、全て入力して下さい。</td>
			</tr>
			<?php
			  }
			?>
			<tr>
              <td><form action="<?php echo tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'); ?>" method="post" onSubmit="return chara_mess();">
			    <input type="hidden" name="dummy" value="あいうえお眉幅">
				<table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
				<?php
				  foreach($cart as $key => $val){
				    if($key == 'contents'){
					  foreach($val as $key2 => $val2){
						$cp_query = tep_db_query("select p.products_id,p.products_image,p.products_date_added,p.products_price,p.products_cflag,pd.products_name from " . TABLE_PRODUCTS . " p," . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '".$key2."' and p.products_id = pd.products_id");
						$cp_result = tep_db_fetch_array($cp_query);
				?>
				  <tr>
				    <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $key2) . '">' . tep_image(DIR_WS_IMAGES . $cp_result['products_image'], $cp_result['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
				    <td class="main">
					<?php
					  echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $key2) . '"><b><u>' . $cp_result['products_name'] . '</u></b></a><br>';
					  if($cp_result['products_cflag'] == 1){
					    echo TEXT_CHARACTER . tep_draw_input_field('cname_' . $key2,'','id="cname_' . $key2 . '" class="input_text"');
					  }
					  echo "\n";
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
			    
				<div class="contents">
					<p class="red">ご入力されましたキャラクター名にお間違えはございませんか？</p>
					<span>よくある間違い</span>
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
						<span class="red">※</span>&nbsp;キャラクター名の入力不要な商品が一部ございます。「入力フォーム」が表示されない場合は「次へ進む」をクリックしてください。
					</p>
				</div>
				
				<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox" summary="table"> 
                  <tr class="infoBoxContents"> 
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">  
                      <tr> 
                        <td class="main">&nbsp;&nbsp;間違いがなければ「次へ進む」をクリックしてください。</td> 
                        <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  </tr> 
                </table>
				
				<input type="hidden" name="act" value="chk">
 			  </form></td>
			</tr>
		  </table>
		</div>
        <p class="pageBottom"></p>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

