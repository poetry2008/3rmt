<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGOFF);

  $breadcrumb->add(NAVBAR_TITLE);


  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('customer_emailaddress');

  // products session destroy
  tep_session_unregister('shipping');
  tep_session_unregister('payment');
  tep_session_unregister('comments');
  tep_session_unregister('point');
  tep_session_unregister('get_point');
  tep_session_unregister('real_point');
  tep_session_unregister('torihikihouhou');
  tep_session_unregister('date');
  tep_session_unregister('hour');
  tep_session_unregister('min');
  tep_session_unregister('insert_torihiki_date');
  unset($_SESSION['character']);
  unset($_SESSION['option']);
  unset($_SESSION['referer_adurl']);
  unset($_SESSION['campaign_fee']);
  unset($_SESSION['camp_id']);
  tep_session_unregister('h_code_fee');
  tep_session_unregister('h_point');
  // shipping session
  tep_session_unregister('start_hour');
  tep_session_unregister('start_min');
  tep_session_unregister('end_hour');
  tep_session_unregister('end_min');
  tep_session_unregister('ele');
  tep_session_unregister('address_option');
  tep_session_unregister('insert_torihiki_date_end');
  tep_session_unregister('address_show_list');
  unset($_SESSION['options']);
  unset($_SESSION['options_type_array']);
  unset($_SESSION['weight_fee']);
  unset($_SESSION['free_value']);
  tep_session_unregister('hc_point');
  tep_session_unregister('hc_camp_point');
  unset($_SESSION['shipping_page_str']);
  unset($_SESSION['shipping_session_flag']);

  $cart->reset();

?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
<h2><?php echo HEADING_TITLE ; ?></h2> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td align="right"> <br> 
                <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' .
                tep_image_button('button_continue.gif',
                    IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"   onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"') . '</a>'; ?> </td> 
            </tr> 
          </table> 
        </div>
		</div>
					<?php include('includes/float-box.php');?>
	</div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
