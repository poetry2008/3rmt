<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  if(isset($_SESSION['preorder_products_list'])){

    foreach($_SESSION['preorder_products_list'] as $key=>$value){

      if(substr($key,0,3) == 'op_' && !empty($_POST)){

        unset($_SESSION['preorder_products_list'][$key]);
      }
    }
    $_POST = array_merge($_SESSION['preorder_products_list'],$_POST);
  }else{
    $_SESSION['preorder_products_list'] = $_POST; 
  }
  if (!isset($_POST['products_id'])) {
    forward404(); 
  }
  if(isset($_GET['action']) && $_GET['action'] == 'process'){
    $customer_error = false;
    if(tep_session_is_registered('customer_id')){
      $flag_customer_info = tep_is_customer_by_id($customer_id);
      if(!$flag_customer_info ||
        $flag_customer_info['customers_email_address'] != $_SESSION['customer_emailaddress']){
        $customer_error = true;
      }
    }
  }
  if($customer_error){
  $site_romaji = tep_get_site_romaji_by_id(SITE_ID);
  $oconfig_raw = tep_db_query("select value from ".TABLE_OTHER_CONFIG." where keyword = 'css_random_string' and site_id = '".SITE_ID."'");
  $oconfig_res = tep_db_fetch_array($oconfig_raw);
  tep_db_free_result($oconfig_raw);
  if($oconfig_res){
     $css_random_str = substr($oconfig_res['value'], 0, 4);
  }else{
     $css_random_str = date('YmdHi', time());
  }
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo 'css/'.$site_romaji.'.css?v='.$css_random_str;?>">
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/notice.js"></script>
<script type="text/javascript">
$(document).ready(function() {
var docheight = $(document).height();
var screenwidth, screenheight, mytop, getPosLeft, getPosTop
screenwidth = $(window).width();
screenheight = $(window).height();
mytop = $(document).scrollTop();
getPosLeft = screenwidth / 2 - 276;
getPosTop = 50;

$("#popup_notice").css('display', 'block');
$("#popup_notice").css({ "left": getPosLeft, "top": getPosTop })

$(window).resize(function() {
           screenwidth = $(window).width();
           screenheight = $(window).height();
           mytop = $(document).scrollTop();
           getPosLeft = screenwidth / 2 - 276;
           getPosTop = 50;
           $("#popup_notice").css({ "left": getPosLeft, "top": getPosTop + mytop });

});


$("body").append("<div id='greybackground'></div>");
$("#greybackground").css({ "opacity": "0.5", "height": docheight });
});
</script>
</head>
<body>
<div id="popup_notice" style="display:none;">
<div class="popup_notice_text">
<?php echo TEXT_ORDERS_ERROR;?>
</div>
<div class="popup_notice_middle">
<?php 
echo TEXT_ORDERS_EMPTY_COMMENT;
?>
</div>
<div align="center" class="popup_notice_button">
<a href="javascript:void(0);" onClick="update_notice('index.php')"><img alt="<?php echo LOCATION_HREF_INDEX;?>" src="images/design/href_home.gif"></a>&nbsp;&nbsp;
<a href="javascript:void(0);" onClick="update_notice('contact_us.php')"><img alt="<?php echo CONTACT_US;?>" src="images/design/contact_us.gif"></a>
</div>
</div>
</body>
</html>

<?php
  exit;
  }

  require(DIR_WS_CLASSES. 'payment.php'); 
  $payment_modules = payment::getInstance(SITE_ID,'','preorder');
  if (tep_session_is_registered('customer_id')) {
    $account = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_email_address,
               is_send_mail
        from " .  TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "' 
          and site_id = '".SITE_ID."'
    ");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }


  $valid_product = false;
  
  $products_id = $_POST['products_id']; 
  
  $product_info_query = tep_db_query("
      select pd.products_id, pd.products_name, pd.products_status, pd.romaji, pd.preorder_status 
      from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
      where pd.products_id = '" .  $products_id . "' 
        and pd.language_id = '" . $languages_id . "' 
        and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
      order by pd.site_id DESC
      limit 1
  ");
  $valid_product = (tep_db_num_rows($product_info_query) > 0);
  
  if (!$valid_product) {
    forward404(); 
  }
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER_PAYMENT);
  $product_info = tep_db_fetch_array($product_info_query);
 
  if ($product_info['preorder_status'] != '1') {
    forward404(); 
  }
  $ca_path = tep_get_product_path($product_info['products_id']);
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
        $categories_info = tep_db_fetch_array($categories_query); $breadcrumb->add($categories_info['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($ca_path_array, 0, ($cnum+1)))));
      } else {
        break;
      }
    }
  }
  
  $breadcrumb->add($product_info['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$product_info['products_id']));
  $breadcrumb->add(sprintf(HEADING_TITLE, $product_info['products_name']));
  $po_game_c = ds_tep_get_categories($product_info['products_id'],1);
?>
<?php page_head();?>
<?php
if (isset($_GET['check']) && $_GET['check'] == '1' && !empty($_POST['pre_payment'])) {
      $payment_error = false;
      $sn_type = $payment_modules->preorder_confirmation_check($_POST['pre_payment']); 
      if ($sn_type) {
        $sn_error_info = $payment_modules->get_preorder_error($_POST['pre_payment'], $sn_type); 
        $error = true; 
        $payment_error = true;
        $payment_error_str = $sn_error_info; 
      } else {
        $payment_error = false;
      }

      if($payment_error == false){

        echo tep_draw_form('payment_form', tep_href_link(FILENAME_PREORDER_CONFIRMATION));
        foreach ($_POST as $p_key => $p_value) {
          echo tep_draw_hidden_field($p_key, stripslashes($p_value)); 
        }
        echo '</form>';
        echo '<script type="text/javascript">';
        echo 'document.forms.payment_form.submit();';
        echo '</script>';
      }
    }
?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function check_pre_products(op_info_str, products_id_str) {
  var pre_payment = document.getElementsByName("pre_payment");  
  var pre_payment_length = pre_payment.length;
  var error = true;
  for(var i=0;i<pre_payment_length;i++){

    if(pre_payment[i].checked == true){

      error = false;
    }
  }
  if(error == true){

    alert('<?php echo JS_ERROR;?>'); 
  }else{
    $.ajax({
      url: '<?php echo tep_href_link('ajax_notice.php', 'action=check_pre_products_op');?>',     
      type: 'POST',
      data: 'op_info_str='+op_info_str+'&products_id_str='+products_id_str,
      async: false,
      success: function(msg) {
        if (msg != 'success') {
          alert(msg); 
          document.forms.form1.submit(0); 
        } else {
          document.forms.preorder_product.submit(0); 
        }
      }
    });
  }
}
function triggerHide(radio)
{
 if ($(radio).attr("checked") == true) {
      $(".rowHide").hide();
      $(".rowHide").find("input").attr("disabled","true");
      $(".rowHide_"+$(radio).val()).show();
      $(".rowHide_"+$(radio).val()).find("input").removeAttr("disabled");
      
      $("input[name=pre_payment]").each(function(index){
	if ($(this).val() != $(radio).val()) {
          $(this).parent().parent().removeClass(); 
          $(this).parent().parent().addClass("box_content_title"); 
        }
      });
      
      $(radio).parent().parent().removeClass(); 
      $(radio).parent().parent().addClass("box_content_title box_content_title_selected"); 
      var headID = document.getElementsByTagName("head")[0];
      var newCss = document.createElement('link');
      newCss.type = 'text/css';
      newCss.rel = "stylesheet";
      newCss.href = "css/gm.css";
      headID.appendChild(newCss);
 }
}
$(document).ready(function(){
    if($("input[name=pre_payment]").length == 1){
      $("input[name=pre_payment]").each(function(index){
	  $(this).attr('checked','true');
	});
    }
    $("input[name=pre_payment]").click(function(index){
	  triggerHide(this);
    });
    $("input[name=pre_payment]").each(function(index){
	if ($(this).attr('checked') == true) {
	  triggerHide(this);
	}
    });
});
</script>
</head>
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<div id="main">
  <div class="yui3-u" id="layout">
  <div id="current" ><?php echo $breadcrumb->trail(' <img  src="images/point.gif"> '); ?></div>
 <?php include('includes/search_include.php');?>
 	<div id="main-content">
      <h2><?php echo $po_game_c . '&nbsp;' . $product_info['products_name'].TEXT_PREORDER_BOOK; ?></h2>
            <div class="frame_content">
<?php
  if ($valid_product == false) {
?>
      <p class="main">
        <?php echo HEADING_TITLE_ERROR; ?><br><?php echo ERROR_INVALID_PRODUCT; ?>
      </p>
<?php
  } else {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link"> 
           <tr> 
               <td width="20%">
                   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                         <tr> 
                            <td width="50%" align="right">
                             &nbsp;  
                              </td> 
                              <td>
                              <?php
                                echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); 
                               ?>
                              </td>
                                <td width="50%">
                                 <?php
                                   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                                            ?>
                                </td> 
                          </tr> 
                    </table>
                  </td>  
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
                 <td width="50%">
                      <?php
                     echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                  </td>  
                  <td width="50%">
                    <?php
                         echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                  </td> 
            </tr> 
        </table>
    </td>  
    <td width="20%">
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
                <td width="50%">
                  <?php
                    echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                </td> 
                  <td width="50%">
                        <?php
                      echo tep_draw_separator('pixel_silver.gif', '1', '5'); 
                      ?>
                  </td> 
              </tr> 
          </table>
    </td> 
</tr> 
<tr class="box_des">  
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarCurrent">
   <?php
   echo CHECKOUT_BAR_PAYMENT; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_CONFIRMATION; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_FINISHED; 
?>
</td> 
</tr> 
</table>
      <?php echo tep_draw_form('preorder_product', tep_href_link(FILENAME_PREORDER_PAYMENT, 'check=1')) .  tep_draw_hidden_field('products_id', $product_info['products_id']).tep_draw_hidden_field('products_name', $product_info['products_name']).tep_draw_hidden_field('action', 'process'); ?>
<div id="hm-checkout-warp">
<?php
$op_info_array = array(); 
              foreach ($_POST as $op_s_key => $op_s_value) {
                $ops_single_str = substr($op_s_key, 0, 3);
                if ($ops_single_str == 'op_') {
                  $op_info_array[] = $op_s_key.'||||||'.stripslashes($op_s_value); 
                }
              }
   $op_info_tmp_str = implode('<<<<<<', $op_info_array);
?>
       <div class="checkout-title"><p><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</p><p>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE;  ?></p></div>
      <div class="checkout-bottom"><a href="javascript:void(0);" onclick="check_pre_products('<?php echo $op_info_tmp_str;?>', '<?php echo $product_info['products_id'];?>');"><?php echo tep_image_button('button_continue_02_hover.gif', IMAGE_BUTTON_CONTINUE);?></a></div>
</div>     
</div>
<div class="checkout-conent">
    <h3><b><?php echo FORM_FIELD_PREORDER_PAYMENT; ?></b></h3> 
    <div class="checkout_payment_info">  
     <div id="hm-payment"><div class="hm-payment-top-left"><?php echo TEXT_SELECT_PAYMENT_METHOD;?></div><div class="hm-payment-right"><b><?php echo TITLE_PLEASE_SELECT;?></b></div></div> 
    <?php
      $selection = $payment_modules->selection(1); 
      if ($payment_error == true) {
          echo '<br>';
          if (sizeof($selection) > 1) { 
            if (isset($payment_error_str)) {
              echo '<div class="box_waring">'; 
              echo TEXT_PAYMENT_ERROR_TOP;
              echo $payment_error_str; 
              echo '</div>'; 
            }
          } else {
              echo '<div class="box_waring">'; 
              echo TEXT_NO_PAYMENT;
              echo '</div>'; 
          }
        }
    if (sizeof($selection) > 0) { 
      ?>
      <?php
        foreach ($selection as $key => $singleSelection) { 
          if (defined('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_PREORDER_SHOW'))) {
            if (constant('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_PREORDER_SHOW')) == 'False') {
              continue; 
            }
            
            if (!tep_whether_show_preorder_payment(constant('MODULE_PAYMENT_'.strtoupper($singleSelection['id'].'_LIMIT_SHOW')))) {
              continue; 
            }
            
            if ($payment_modules->moneyInRange($singleSelection['id'], $_POST['preorder_subtotal'])) {
              continue; 
            }
          } else {
            continue; 
          }
        ?>
        <div>
          <div class="box_content_title <?php if ($_POST['pre_payment'] == $singleSelection['id']) {echo 'box_content_title_selected';};?>"> 
            <div class="hm-payment-left"><b><?php echo $singleSelection['module'];?></b></div> 
            <div class="hm-payment-right">
            <?php echo tep_draw_radio_field('pre_payment', $singleSelection['id'], $_POST['pre_payment'] == $singleSelection['id']);?> 
            </div>
          </div>
          <div class="box_content_text">
            <p class="cp_description"><?php echo $singleSelection['description'];?></p>
            <div class="cp_content">
              <div style="display:none;" class="rowHide rowHide_<?php echo $singleSelection['id'];?>">
              <?php 
                echo $singleSelection['fields_description']; 
                foreach ($singleSelection['fields'] as $key2 => $field) {
              ?>
                <div class="txt_input_box">
                  <?php if ($field['title']) {?>
                  <div class="frame_title"><?php echo $field['title'];?></div> 
                  <?php }?>
                  <div class="input_title"><?php echo $field['field']?><small><font color="#AEOE30"><?php echo $field['message'];?></font></small></div> 
                </div>
              <?php
                }
              ?> 
               <?php echo $singleSelection['footer'];?> 
              </div>
            </div>
          </div>
        </div>
        <?php 
        }
        ?>
      <?php }?> 
      </div>
      </div>
      <div class="checkout-conent">
      <h3><b><?php echo PREORDER_EXPECT_CTITLE; ?></b></h3>
      <table width="100%" cellpadding="2" cellspacing="0" border="0">
        <tr><td><?php echo tep_draw_textarea_field('yourmessage', 'soft', 50, 5, $_POST['yourmessage'], 'style="width:100%;"');?></td></tr>
      </table>
      </div>
<div id="hm-checkout-warp">
<?php
              if (!tep_session_is_registered('customer_id')) {
                echo tep_draw_hidden_field('lastname', $_POST['lastname']); 
                echo tep_draw_hidden_field('firstname', $_POST['firstname']); 
                echo tep_draw_hidden_field('from', $_POST['from']); 
              }
              echo tep_draw_hidden_field('quantity', $_POST['quantity']); 
              echo tep_draw_hidden_field('preorder_subtotal', $_POST['preorder_subtotal']); 
              $op_info_array = array(); 
              foreach ($_POST as $op_s_key => $op_s_value) {
                $ops_single_str = substr($op_s_key, 0, 3);
                if ($ops_single_str == 'op_') {
                  echo tep_draw_hidden_field($op_s_key, stripslashes($op_s_value)); 
                  $op_info_array[] = $op_s_key.'||||||'.stripslashes($op_s_value); 
                }
              }
              $op_info_tmp_str = implode('<<<<<<', $op_info_array); 
            ?>
       <div class="checkout-title"><p><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</p><p>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE;  ?></p></div>
      <div class="checkout-bottom"><a href="javascript:void(0);" onclick="check_pre_products('<?php echo $op_info_tmp_str;?>', '<?php echo $product_info['products_id'];?>');"><?php echo tep_image_button('button_continue_02_hover.gif', IMAGE_BUTTON_CONTINUE);?></a></div>
</div>
    </form>
    <?php
       echo tep_draw_form('form1', tep_preorder_href_link($product_info['products_id'], $product_info['romaji'])); 
       if (!tep_session_is_registered('customer_id')) {
         echo tep_draw_hidden_field('lastname', $_POST['lastname']); 
         echo tep_draw_hidden_field('firstname', $_POST['firstname']); 
         echo tep_draw_hidden_field('from', $_POST['from']); 
       }
       echo tep_draw_hidden_field('quantity', $_POST['quantity']); 
       foreach ($_POST as $op_key => $op_value) {
         $op_single_str = substr($op_key, 0, 3);
         if ($op_single_str == 'op_') {
           echo tep_draw_hidden_field($op_key, stripslashes($op_value)); 
         }
       }
    ?>
    </form>
<?php
    }
?>    
    <!-- body_text_eof -->
  </div>
  </div>
  </div>
  <?php include('includes/float-box.php');?>
</div>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
