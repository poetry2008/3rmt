<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EDIT);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">        <div id="current"><?php echo
$breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
		<?php include('includes/search_include.php');?>
<?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 

	<div id="main-content">
    <h2><?php echo HEADING_TITLE ; ?></h2> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <?php
//ccdd
  $account_query = tep_db_query("select c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_firstname_f, c.customers_lastname_f, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_zone_id, a.entry_state, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter from " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " a where c.customers_id = '" . $customer_id . "' and a.customers_id = c.customers_id and a.address_book_id = '" .  $customer_default_address_id . "' and c.site_id = '".SITE_ID."'");
  $account = tep_db_fetch_array($account_query);
  $email_address = $account['customers_email_address'];

  require(DIR_WS_MODULES . 'account_details.php');
?>
<input type="hidden" name="old_email" value="<?php echo $account['customers_email_address'];?>">
</td> 
            </tr> 
          </table>
		  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="botton-continue"> 
                  <tr> 
                    <td width="20%"><?php echo '<a href="' .
                    tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' .
                    tep_image_button('button_back.gif',
                        IMAGE_BUTTON_BACK,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?></td> 
                    <td align="right"><?php echo
                    tep_image_submit('button_continue.gif',
                        IMAGE_BUTTON_CONTINUE,'
                        onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"
                        onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"'); ?></td> 
                  </tr> 
                </table>
          </div>
          </form> 
        </div>
      <?php include('includes/float-box.php');?>
        </div>
        <?php include('includes/new.php');?>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
