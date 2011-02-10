<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'update':
        // tamura 2002/12/30 「全角」英数字を「半角」に変換
        $an_cols = array('customers_email_address','customers_telephone','customers_fax','customers_dob','entry_postcode');
        foreach ($an_cols as $col) {
          $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
        }

        $customers_id            = tep_db_prepare_input($_GET['cID']);
        $customers_firstname     = tep_db_prepare_input($_POST['customers_firstname']);
        $customers_lastname      = tep_db_prepare_input($_POST['customers_lastname']);
        $customers_firstname_f   = tep_db_prepare_input($_POST['customers_firstname_f']);
        $customers_lastname_f    = tep_db_prepare_input($_POST['customers_lastname_f']);
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);
        $customers_telephone     = tep_db_prepare_input($_POST['customers_telephone']);
        $customers_fax           = tep_db_prepare_input($_POST['customers_fax']);
        $customers_newsletter    = tep_db_prepare_input($_POST['customers_newsletter']);
        $customers_gender        = tep_db_prepare_input($_POST['customers_gender']);
        $customers_dob           = tep_db_prepare_input($_POST['customers_dob']);

        $sql_data_array = array('customers_firstname'     => $customers_firstname,
                                'customers_lastname'      => $customers_lastname,
                                'customers_firstname_f'   => $customers_firstname_f,
                                'customers_lastname_f'    => $customers_lastname_f,
                                'customers_email_address' => $customers_email_address,
                                'customers_telephone'     => $customers_telephone,
                                'customers_fax'           => $customers_fax,
                                'customers_newsletter'    => $customers_newsletter);

        if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $customers_gender;
        if (ACCOUNT_DOB    == 'true') $sql_data_array['customers_dob']    = tep_date_raw($customers_dob);
        
        $customers = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".tep_db_input($customers_id)."'"));
        $check_email = tep_db_query("
            select customers_email_address 
            from " . TABLE_CUSTOMERS . " 
            where customers_email_address = '" . tep_db_input($customers_email_address) . "' 
              and customers_id <> '" . tep_db_input($customers['customers_id']) . "'
              and site_id = '".$customers['site_id']."'
              
        ");
        if (tep_db_num_rows($check_email)) {
          $messageStack->add_session(ERROR_EMAIL_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'action=edit&cID=' . $customers_id));
        }
        //Add Point System
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
          $point = tep_db_prepare_input($_POST['point']);
          $sql_data_array['point'] = $point;
        }

        tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "'");

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customers_id) . "'");

        $default_address_id   = tep_db_prepare_input($_POST['default_address_id']);
        $entry_street_address = tep_db_prepare_input($_POST['entry_street_address']);
        $entry_suburb         = tep_db_prepare_input($_POST['entry_suburb']);
        $entry_postcode       = tep_db_prepare_input($_POST['entry_postcode']);
        $entry_city           = tep_db_prepare_input($_POST['entry_city']);
        $entry_country_id     = tep_db_prepare_input($_POST['entry_country_id']);
        $entry_company        = tep_db_prepare_input($_POST['entry_company']);
        $entry_state          = tep_db_prepare_input($_POST['entry_state']);
        $entry_zone_id        = tep_db_prepare_input($_POST['entry_zone_id']);
        $entry_telephone      = tep_db_prepare_input($_POST['customers_telephone']);

        if ($entry_zone_id > 0) $entry_state = '';

        $sql_data_array = array('entry_firstname' => $customers_firstname,
                                'entry_lastname' => $customers_lastname,
                                'entry_street_address' => $entry_street_address,
                                'entry_postcode' => $entry_postcode,
                                'entry_city' => $entry_city,
                                'entry_country_id' => $entry_country_id,
                                'entry_telephone' => $entry_telephone);

        if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $entry_company;
        if (ACCOUNT_SUBURB  == 'true') $sql_data_array['entry_suburb']  = $entry_suburb;
        if (ACCOUNT_STATE == 'true') {
          $sql_data_array['entry_state']   = $entry_state;
          $sql_data_array['entry_zone_id'] = $entry_zone_id;
        }

        tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "' and address_book_id = '" . tep_db_input($default_address_id) . "'");

    tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $customers_id));
        break;
      case 'deleteconfirm':
        $customers_id = tep_db_prepare_input($_GET['cID']);

        if ($_POST['delete_reviews'] == 'on') {
          $reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where customers_id = '" . tep_db_input($customers_id) . "'");
          while ($reviews = tep_db_fetch_array($reviews_query)) {
            tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $reviews['reviews_id'] . "'");
          }
          tep_db_query("delete from " . TABLE_REVIEWS . " where customers_id = '" . tep_db_input($customers_id) . "'");
        } else {
          tep_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" . tep_db_input($customers_id) . "'");
        }

        tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . tep_db_input($customers_id) . "'");
        tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . tep_db_input($customers_id) . "'");

        tep_redirect(tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')))); 
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<?php
  // 编辑页面
  if (isset($_GET['action']) && $_GET['action'] == 'edit') {
?>
<script language="javascript"><!--
function resetStateText(theForm) {
  theForm.entry_state.value = '';
  if (theForm.entry_zone_id.options.length > 1) {
    theForm.entry_state.value = '<?php echo JS_STATE_SELECT; ?>';
  }
}

function resetZoneSelected(theForm) {
  if (theForm.entry_state.value != '') {
    theForm.entry_zone_id.selectedIndex = '0';
    if (theForm.entry_zone_id.options.length > 1) {
      theForm.entry_state.value = '<?php echo JS_STATE_SELECT; ?>';
    }
  }
}

function update_zone(theForm) {
  var NumState = theForm.entry_zone_id.options.length;
  var SelectedCountry = '';

  while(NumState > 0) {
    NumState--;
    theForm.entry_zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.entry_country_id.options[theForm.entry_country_id.selectedIndex].value;

<?php echo tep_js_zone_list('SelectedCountry', 'theForm', 'entry_zone_id'); ?>

  resetStateText(theForm);
}

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var customers_firstname = document.customers.customers_firstname.value;
  var customers_lastname = document.customers.customers_lastname.value;
  
  var customers_firstname_f = document.customers.customers_firstname_f.value;
  var customers_lastname_f = document.customers.customers_lastname_f.value;
  
<?php if (ACCOUNT_COMPANY == 'true') echo 'var entry_company = document.customers.entry_company.value;' . "\n"; ?>
<?php if (ACCOUNT_DOB == 'true') echo 'var customers_dob = document.customers.customers_dob.value;' . "\n"; ?>
  var customers_email_address = document.customers.customers_email_address.value;  
  //var entry_street_address = document.customers.entry_street_address.value;
  //var entry_postcode = document.customers.entry_postcode.value;
  //var entry_city = document.customers.entry_city.value;
  //var customers_telephone = document.customers.customers_telephone.value;

<?php if (ACCOUNT_GENDER == 'true') { ?>
  if (document.customers.customers_gender[0].checked || document.customers.customers_gender[1].checked) {
  } else {
    error_message = error_message + "<?php echo JS_GENDER; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_firstname == "" || customers_firstname.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
    error = 1;
  }

  if (customers_lastname == "" || customers_lastname.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
    error = 1;
  }
<?php if (ACCOUNT_DOB == 'true') { ?>
  if (customers_dob == "" || customers_dob.length < <?php echo ENTRY_DOB_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_DOB; ?>";
    error = 1;
  }
<?php } ?>

  if (customers_email_address == "" || customers_email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
    error = 1;
  }
<?php if (ACCOUNT_STATE == 'true') { ?>
  if (document.customers.entry_zone_id.options.length <= 1) {
    if (document.customers.entry_state.value == "" || document.customers.entry_state.length < 4 ) {
       error_message = error_message + "<?php echo JS_STATE; ?>";
       error = 1;
    }
  } else {
    document.customers.entry_state.value = '';
    if (document.customers.entry_zone_id.selectedIndex == 0) {
       error_message = error_message + "<?php echo JS_ZONE; ?>";
       error = 1;
    }
  }
<?php } ?>

  //if (document.customers.entry_country_id.value == 0) {
    //error_message = error_message + "<?php echo JS_COUNTRY; ?>";
    //error = 1;
  //}
  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
<?php
  }
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $customers_query = tep_db_query("
        select c.customers_gender, 
               c.customers_firstname, 
               c.customers_lastname, 
               c.customers_firstname_f, 
               c.customers_lastname_f, 
               c.customers_dob, 
               c.customers_email_address, 
               a.entry_company, 
               a.entry_street_address, 
               a.entry_suburb, 
               a.entry_postcode, 
               a.entry_city, 
               a.entry_state, 
               a.entry_zone_id, 
               a.entry_country_id, 
               c.customers_telephone, 
               c.customers_fax, 
               c.customers_newsletter, 
               c.customers_default_address_id,
               s.romaji,
               s.name as site_name
        from " . TABLE_CUSTOMERS . " c 
          left join " . TABLE_ADDRESS_BOOK . " a on c.customers_default_address_id = a.address_book_id ,".TABLE_SITES." s
        where a.customers_id = c.customers_id 
          and s.id = c.site_id
          and c.customers_id = '" . (int)$_GET['cID'] . "'
    ");
    $customers = tep_db_fetch_array($customers_query);
    $cInfo = new objectInfo($customers);

    $newsletter_array = array(array('id' => '1', 'text' => ENTRY_NEWSLETTER_YES),
                              array('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));

    include_once(DIR_WS_CLASSES . 'address_form.php');
    $address_form = new addressForm;

    // gender
    $a_value = tep_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;'
     . tep_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE;
    $address_form->setFormLine('gender',ENTRY_GENDER,$a_value);

    // firstname
    $a_value = tep_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"', false);
    $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$a_value);

    // lastname
    $a_value = tep_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"', false);
    $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$a_value);
  
  // firstname_f
    $a_value = tep_draw_input_field('customers_firstname_f', $cInfo->customers_firstname_f, 'maxlength="32"', false);
    $address_form->setFormLine('firstname_f',ENTRY_FIRST_NAME_F,$a_value);

    // lastname_f
    $a_value = tep_draw_input_field('customers_lastname_f', $cInfo->customers_lastname_f, 'maxlength="32"', false);
    $address_form->setFormLine('lastname_f',ENTRY_LAST_NAME_F,$a_value);

    // dob
    $a_value = tep_draw_input_field('customers_dob', tep_date_short($cInfo->customers_dob), 'maxlength="10"', false);
    $address_form->setFormLine('dob',ENTRY_DATE_OF_BIRTH,$a_value);

    // email_address
    $a_value = tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"', false);
    $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$a_value);

    // company
    $a_value = tep_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"');
    $address_form->setFormLine('company',ENTRY_COMPANY,$a_value);

    // street_address
    $a_value = tep_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"', true);
    $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$a_value);

    // suburb
    $a_value = tep_draw_input_field('entry_suburb', $cInfo->entry_suburb, 'maxlength="32"');
    $address_form->setFormLine('suburb',ENTRY_SUBURB,$a_value);

    // postcode
    $a_value = tep_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"', true);
    $address_form->setFormLine('postcode',ENTRY_POST_CODE,$a_value);

    // city
    $a_value = tep_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"', true);
    $address_form->setFormLine('city',ENTRY_CITY,$a_value);
    $address_form->setCountry($cInfo->entry_country_id);
    $a_value = tep_draw_pull_down_menu('entry_country_id', tep_get_countries(), $cInfo->entry_country_id, 'onChange="update_zone(this.form);"');
    $address_form->setFormLine('country',ENTRY_COUNTRY,$a_value);
    $a_hidden = tep_draw_hidden_field('entry_country_id',$cInfo->entry_country_id);
    $address_form->setFormHidden('country',$a_hidden); // in case without country

    // state
    $a_value = tep_draw_pull_down_menu('entry_zone_id', tep_prepare_country_zones_pull_down($cInfo->entry_country_id), $cInfo->entry_zone_id, 'onChange="resetStateText(this.form);"');
    $address_form->setFormLine('zone_id',ENTRY_STATE,$a_value);
    $a_value = tep_draw_input_field('entry_state', $cInfo->entry_state, 'maxlength="32" onChange="resetZoneSelected(this.form);"');
    $address_form->setFormLine('state','&nbsp;',$a_value);

?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
      <?php echo tep_draw_form('customers', FILENAME_CUSTOMERS, tep_get_all_get_params(array('action')) . 'action=update', 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id) . "\n"; ?>
      <input type="hidden" name="dummy" value="あいうえお眉幅">
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_SITE; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_SITE;?>:</td>
            <td class="main">&nbsp;<?php echo $customers['site_name'];?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php
    $address_form->printCategoryPersonal();
?>
        </table></td>
      </tr>
<?php
    // 应日本要求不显示
    if (ACCOUNT_COMPANY == 'true' && false) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_COMPANY; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php
    $address_form->printCategoryCompany();
?>
        </table></td>
      </tr>
<?php
    }
?>
      <!-- <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
<?php
    $address_form->printCategoryAddress();
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_CONTACT; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
            <td class="main"><?php echo tep_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"', true); ?></td>
          </tr>
        </table></td>
      </tr> -->
    <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle">当社使用欄</td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main">信用調査:</td>
            <td class="main"><?php echo tep_draw_input_field('customers_fax', $cInfo->customers_fax, 'size="60" maxlength="255"'); ?>&nbsp;&nbsp;常連客【HQ】&nbsp;&nbsp;注意【WA】&nbsp;&nbsp;発送禁止【BK】</td>
          </tr>
      <tr>
            <td class="main" colspan="2">クレカ初回決済日：C2007/01/01&nbsp;&nbsp;&nbsp;&nbsp;エリア一致：Aok&nbsp;&nbsp;&nbsp;&nbsp;本人確認済：Hok&nbsp;&nbsp;&nbsp;&nbsp;YahooID更新日：Y2007/01/01&nbsp;&nbsp;&nbsp;&nbsp;リファラー：R</td>
          </tr>
      <tr>
            <td class="main" colspan="2"><b>記入例：WA-Aok-C2007/01/01-Hok-RグーグルFF11 RMT</b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_OPTIONS; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_NEWSLETTER; ?></td>
            <td class="main"><?php echo tep_draw_pull_down_menu('customers_newsletter', $newsletter_array, $cInfo->customers_newsletter); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
    <?php if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {//Add Point System 
    $cpoint_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$_GET['cID']."'");
    $cpoint = tep_db_fetch_array($cpoint_query);
    ?>
      <tr>
        <td class="formAreaTitle"><?php echo CATEGORY_POINT; ?></td>
      </tr>
      <tr>
        <td class="formArea"><table border="0" cellspacing="2" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_POINT; ?></td>
            <td class="main"><?php echo tep_draw_input_field('point', $cpoint['point'], 'maxlength="32" size="4" style="text-align:right"'); ?> P</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>   
    <?php } ?>
      <tr>
        <td align="right" class="main"><?php echo
        tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a class="new_product_reset" class =  "new_product_reset" href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('action'))) .'">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr></form>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><?php echo tep_draw_form('search', FILENAME_CUSTOMERS, tep_get_all_get_params(), 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right"><?php //echo tep_draw_hidden_field('d', '龠');?><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search'); ?><br>※検索対象：「顧客名（姓/名/）」「ふりがな（姓/名）」「メールアドレス」
      
      </td>
          </form></tr>
        </table></td>
      </tr>
      <tr><td>
        <?php tep_site_filter(FILENAME_CUSTOMERS);?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="60"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent" width="80"><?php echo TABLE_HEADING_MEMBER_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LASTNAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FIRSTNAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $search = '';
    if ( isset($_GET['search']) && ($_GET['search']) && (tep_not_null($_GET['search'])) ) {
      $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
      $search = "and (c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%' or c.customers_firstname_f like '%" . $keywords . "%'  or c.customers_lastname_f like '%" . $keywords . "%')";
    }
    $customers_query_raw = "
      select c.customers_id, 
             c.site_id,
             c.customers_lastname, 
             c.customers_firstname, 
             c.customers_email_address, 
             a.entry_country_id, 
             c.customers_guest_chk,
             ci.customers_info_date_account_created as date_account_created, 
             ci.customers_info_date_account_last_modified as date_account_last_modified, 
             ci.customers_info_date_of_last_logon as date_last_logon, 
             ci.customers_info_number_of_logons as number_of_logons 
      from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci
        where c.customers_id = ci.customers_info_id
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and c.site_id = '" . intval($_GET['site_id']) . "' " : '') . "
        " . $search . " 
      order by c.customers_lastname, c.customers_firstname
    ";
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    while ($customers = tep_db_fetch_array($customers_query)) {
      //$info_query = tep_db_query("
          //select customers_info_date_account_created as date_account_created, 
                 //customers_info_date_account_last_modified as date_account_last_modified, 
                 //customers_info_date_of_last_logon as date_last_logon, 
                 //customers_info_number_of_logons as number_of_logons 
          //from " . TABLE_CUSTOMERS_INFO . " 
          //where customers_info_id = '" . $customers['customers_id'] . "'
      //");
      //$info = tep_db_fetch_array($info_query);

      if (
          ((!isset($_GET['cID']) || !$_GET['cID']) || (@$_GET['cID'] == $customers['customers_id'])) 
          && (!isset($cInfo) || !$cInfo)
        ) {
        $country_query = tep_db_query("
            select countries_name 
            from " . TABLE_COUNTRIES . " 
            where countries_id = '" . $customers['entry_country_id'] . "'
        ");
        $country = tep_db_fetch_array($country_query);

        $reviews_query = tep_db_query("
            select count(*) as number_of_reviews 
            from " . TABLE_REVIEWS . " 
            where customers_id = '" . $customers['customers_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);

        $customer_info = tep_array_merge($country, $customers, $reviews);

        $cInfo_array = tep_array_merge($customers, $customer_info);
        $cInfo = new objectInfo($cInfo_array);
      }

    if($customers['customers_guest_chk'] == 1) {
      $type = TABLE_HEADING_MEMBER_TYPE_GUEST;
    } else {
      $type = TABLE_HEADING_MEMBER_TYPE_MEMBER;
    }

      if ( (isset($cInfo) && is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo tep_get_site_romaji_by_id($customers['site_id']); ?></td>
                <td class="dataTableContent"><?php echo $type; ?></td>
                <td class="dataTableContent"><?php echo htmlspecialchars($customers['customers_lastname']); ?></td>
                <td class="dataTableContent"><?php echo htmlspecialchars($customers['customers_firstname']); ?></td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($customers['date_account_created']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (isset($cInfo) && is_object($cInfo)) && ($customers['customers_id'] == $cInfo->customers_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                  </tr>
<?php
                                                                      if (isset($_GET['search']) and tep_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a class = "new_product_reset"  href="' . tep_href_link(FILENAME_CUSTOMERS) . '">' . tep_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'confirm':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</b>');

      $contents = array('form' => tep_draw_form('customers', FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO . '<br><br><b>' . tep_get_fullname($cInfo->customers_firstname, $cInfo->customers_lastname) . '</b>');
      if ($cInfo->number_of_reviews > 0) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_reviews', 'on', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a class = "new_product_reset" href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . tep_get_fullname($cInfo->customers_firstname, $cInfo->customers_lastname) . '</b>');

        $contents[] = array('align' => 'center', 'text' => 
          '<a class = "new_product_reset" href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' .  ($ocertify->npermission == 15 ? (' <a class = "new_product_reset" href="' . tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=confirm') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'') . ' <a href="' . tep_href_link(FILENAME_ORDERS, 'cID=' .  $cInfo->customers_id) . '">' . tep_image_button('button_orders.gif', IMAGE_ORDERS) . '</a> <a href="'.tep_href_link('customers_products.php', str_replace('page', 'cpage', tep_get_all_get_params(array('cID', 'action')).'cID='.$cInfo->customers_id)).'">'.tep_image_button('button_products.gif', BUTTON_CUSTOMERS_PRODUCTS_TEXT).'</a> <a href="' . tep_href_link(FILENAME_MAIL, 'selected_box=tools&customer=' . $cInfo->customers_email_address) . '">' . tep_image_button('button_email.gif', IMAGE_EMAIL) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_DATE_ACCOUNT_CREATED . ' ' . tep_date_short($cInfo->date_account_created));
        $contents[] = array('text' => '<br>' . TEXT_DATE_ACCOUNT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->date_account_last_modified));
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_LAST_LOGON . ' '  . tep_date_short($cInfo->date_last_logon));
        $contents[] = array('text' => '<br>' . TEXT_INFO_NUMBER_OF_LOGONS . ' ' . $cInfo->number_of_logons);
        $contents[] = array('text' => '<br>' . TEXT_INFO_NUMBER_OF_REVIEWS . ' ' . $cInfo->number_of_reviews);
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
