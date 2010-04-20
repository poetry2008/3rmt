<?php
// iimy,gm,wm to 3rmt

// @todo configuration default value

define('RMT_DB_HOST', 'localhost');
define('RMT_DB_USER', 'root');
define('RMT_DB_PASS', '123456');
define('RMT_DB_NAME', 'maker_rmt');
define('R3MT_DB_NAME', 'test_3rmt');

$sites =  array('jp', 'gm', 'wm');
$faq = array('168', '169', '170', '171', '177', '178', '179', '190', '195');
$delete_configuration = array(
    'AFFILIATE_PAYMENT_ORDER_MIN_STATUS',
    'AFFILIATE_VALUE');

define('R3MT_JP_ID', '1');
define('R3MT_GM_ID', '2');
define('R3MT_WM_ID', '3');

//echo "connect database\n";
$_link  = mysql_connect(RMT_DB_HOST, RMT_DB_USER, RMT_DB_PASS) or die('3');

mysql_query('set names utf8');
// init finish

// ! => special
// % => origin
// * => site_id
// @ => customer_id
// + => truncate

//address_format
cptable('wm_address_format', 'address_format');
// countries
cptable('iimy_countries', 'countries');
//exit;
//banners*
cp3table('banners');
//calendar*
cp3table('calendar');
/*
$sql = 'select * from wm_calendar';
$query = rq($sql);
while($cl = mysql_fetch_array($query)){
  $sql = "insert into calendar (
      cl_id,
      cl_ym,
      cl_value,
      site_id
    ) values (
      NULL,
      '".$cl['cl_ym']."',
      '".$cl['cl_value']."',
      '".R3MT_JP_ID."',
    )";
  r3q($sql);
}
*/
//categories%
cptable('categories');
//categories_description!
r3q("truncate categories_description");
$sql = 'select * from categories_description';
$query = rq($sql);
while($desc = mysql_fetch_array($query)){
  $sql = "insert into `categories_description` (
    `categories_id`,
    `site_id`,
    `language_id`,
    `categories_name`,
    `seo_name`,
    `categories_image2`,
    `categories_image3`,
    `categories_meta_text`,
    `seo_description`,
    `categories_header_text`,
    `categories_footer_text`,
    `text_information`,
    `meta_keywords`,
    `meta_description`
     ) values (
      '" . $desc['categories_id'] . "',
      '0',
      '" . $desc['language_id'] . "',
      '" . mysql_real_escape_string($desc['categories_name']) . "',
      '" . mysql_real_escape_string($desc['seo_name']) . "',
      '" . $desc['categories_image2'] . "',
      '" . $desc['categories_image3'] . "',
      '" . mysql_real_escape_string($desc['categories_meta_text']) . "',
      '" . mysql_real_escape_string($desc['seo_description']) . "',
      '" . mysql_real_escape_string($desc['categories_header_text_jp']) . "',
      '" . mysql_real_escape_string($desc['categories_footer_text_jp']) . "',
      '" . mysql_real_escape_string($desc['text_information']) . "',
      '" . mysql_real_escape_string($desc['meta_keywords_jp']) . "',
      '" . mysql_real_escape_string($desc['meta_description_jp']) . "'
      ),(
      '" . $desc['categories_id'] . "',
      '" . R3MT_JP_ID . "',
      '" . $desc['language_id'] . "',
      '" . mysql_real_escape_string($desc['categories_name']) . "',
      '" . mysql_real_escape_string($desc['seo_name']) . "',
      '" . $desc['categories_image2'] . "',
      '" . $desc['categories_image3'] . "',
      '" . mysql_real_escape_string($desc['categories_meta_text']) . "',
      '" . mysql_real_escape_string($desc['seo_description']) . "',
      '" . mysql_real_escape_string($desc['categories_header_text_jp']) . "',
      '" . mysql_real_escape_string($desc['categories_footer_text_jp']) . "',
      '" . mysql_real_escape_string($desc['text_information']) . "',
      '" . mysql_real_escape_string($desc['meta_keywords_jp']) . "',
      '" . mysql_real_escape_string($desc['meta_description_jp']) . "'
      ),(
      '" . $desc['categories_id'] . "',
      '" . R3MT_GM_ID . "',
      '" . $desc['language_id'] . "',
      '" . mysql_real_escape_string($desc['categories_name']) . "',
      '" . mysql_real_escape_string($desc['seo_name']) . "',
      '" . $desc['categories_image2'] . "',
      '" . $desc['categories_image3'] . "',
      '" . mysql_real_escape_string($desc['categories_meta_text']) . "',
      '" . mysql_real_escape_string($desc['seo_description_gm']) . "',
      '" . mysql_real_escape_string($desc['categories_header_text_gm']) . "',
      '" . mysql_real_escape_string($desc['categories_footer_text_gm']) . "',
      '" . mysql_real_escape_string($desc['text_information_gm']) . "',
      '" . mysql_real_escape_string($desc['meta_keywords_gm']) . "',
      '" . mysql_real_escape_string($desc['meta_description_gm']) . "'
      ), (
      '" . $desc['categories_id'] . "',
      '" . R3MT_WM_ID . "',
      '" . $desc['language_id'] . "',
      '" . mysql_real_escape_string($desc['categories_name']) . "',
      '" . mysql_real_escape_string($desc['seo_name']) . "',
      '" . $desc['categories_image2'] . "',
      '" . $desc['categories_image3'] . "',
      '" . mysql_real_escape_string($desc['categories_meta_text']) . "',
      '" . mysql_real_escape_string($desc['seo_description_wm']) . "',
      '" . mysql_real_escape_string($desc['categories_header_text_wm']) . "',
      '" . mysql_real_escape_string($desc['categories_footer_text_wm']) . "',
      '" . mysql_real_escape_string($desc['text_information_wm']) . "',
      '" . mysql_real_escape_string($desc['meta_keywords_wm']) . "',
      '" . mysql_real_escape_string($desc['meta_description_wm']) . "'
      )";
  r3q($sql);
}
print("categories_description\n");

//configuration!
cp3table('configuration');


foreach($sites as $s){
  $sql   = "select * from ".table_prefix($s)."configuration_ds";
  $query = rq($sql);
  while($config = mysql_fetch_array($query)) {
    $sql = "insert into configuration (
      configuration_id,
      configuration_title,
      configuration_key,
      configuration_value,
      configuration_description,
      configuration_group_id,
      sort_order,
      last_modified,
      date_added,
      use_function,
      set_function,
      site_id
        ) values (
      NULL,
      '" . $config['configuration_title'] . "',
      '" . $config['configuration_key'] . "',
      '" . $config['configuration_value'] . "',
      '" . mysql_real_escape_string($config['configuration_description']) . "',
      '" . (intval($config['configuration_group_id'])+15) . "',
      '" . $config['sort_order'] . "',
      '" . $config['last_modified'] . "',
      '" . $config['date_added'] . "',
      '" . $config['use_function'] . "',
      '" . mysql_real_escape_string($config['set_function']) . "',
      '" . constant(strtoupper('r3mt_'.$s.'_id')) . "'
      )";
    r3q($sql);
  }
}

//configuration_group!
cptable('wm_configuration_group', 'configuration_group');
$query = rq("select * from wm_configuration_ds_group");
while($group = mysql_fetch_array($query)) {
  $sql = "insert into `configuration_group` (
    `configuration_group_id`, 
    `configuration_group_title`, 
    `configuration_group_description`, 
    `sort_order`, 
    `visible`) values (
      '" . (intval($group['configuration_group_id'])+15) . "', 
      '" . $group['configuration_group_title'] . "', 
      '" . $group['configuration_group_description'] . "', 
      '" . $group['sort_order'] . "', 
      '" . $group['visible'] . "')";
  r3q($sql);
}

$query = r3q("select * from configuration group by configuration_key order by site_id asc");
while ($c = mysql_fetch_array($query)) {
  $sql = "insert into `configuration` (
    configuration_id, 
    configuration_title, 
    configuration_key, 
    configuration_value, 
    configuration_description, 
    configuration_group_id, 
    sort_order, 
    last_modified, 
    date_added, 
    use_function, 
    set_function, 
    site_id
    ) values (
      NULL,
      '".$c['configuration_title']."',
      '".$c['configuration_key']."',
      '".$c['configuration_value']."',
      '".$c['configuration_description']."',
      '".$c['configuration_group_id']."',
      '".$c['sort_order']."',
      '".$c['last_modified']."',
      '".$c['date_added']."',
      '".$c['use_function']."',
      '".$c['set_function']."',
      '0'
      )
    ";
  r3q($sql);
}


//contents*  unused
cp3table('contents');
//currencies%
cptable('wm_currencies', 'currencies');
//color* //color_to_products! 
r3q("truncate color");
r3q("truncate color_to_products");
foreach($sites as $s){
  $query = rq("select * from ".table_prefix($s)."color");
  while($color = mysql_fetch_array($query)){
    $sql = "insert into color (
      color_id,
      color_tag,
      color_name,
      sort_id) values (
        NULL,
        '" . $color['color_tag'] . "',
        '" . $color['color_tag'] . "',
        '" . $color['color_tag'] . "'
      )";
    r3q($sql);
    $color_id = mysql_insert_id();
    $sql = "select * from ".table_prefix($s)."color_to_products where color_id=".$color['color_id'];
    $cquery = rq($sql);
    while($ctp = mysql_fetch_array($cquery)){
      $sql = "insert into color_to_products (
        color_id, 
        categories_id,
        products_id,
        color_image,
        color_to_products_name,
        manufacturers_id,
        cid
        ) values (
          '" . $color_id . "',
          '" . $ctp['categories_id'] . "',
          '" . $ctp['products_id'] . "',
          '" . $ctp['color_image'] . "',
          '" . $ctp['color_to_products_name'] . "',
          '" . $ctp['manufacturers_id'] . "',
          NULL
        )";
      r3q($sql);
    }
  }
}
print("color\n");
print("color_to_products\n");
//present_goods*
r3q("truncate present_goods");
foreach($sites as $s){
  $pgquery = rq("select * from " . table_prefix($s) . "present_goods");
  while($pg = mysql_fetch_array($pgquery)){
    $sql = "insert into present_goods (
      goods_id,
      html_check,
      title,
      image,
      text,
      site_id,
      start_date,
      limit_date
      ) values (
        NULL,
        '" . $pg['html_check'] . "',
        '" . $pg['title'] . "',
        '" . $pg['image'] . "',
        '" . $pg['text'] . "',
        '" . site_id($s) . "',
        '" . $pg['start_date'] . "',
        '" . $pg['limit_date'] . "'
      )";
    r3q($sql);
    $pg2pg[$s][$pg['goods_id']] = mysql_insert_id();
  }
}
print("present_goods \n");
//print_r($pg2pg);
//customers*
r3q("truncate customers");
r3q("truncate customers_info");
r3q("truncate address_book");
r3q("truncate customers_basket");
r3q("truncate customers_basket_attributes");
r3q("truncate orders");
r3q("truncate orders_products");
r3q("truncate orders_products_attributes");
r3q("truncate orders_products_download");
r3q("truncate orders_status_history");
r3q("truncate orders_total");
r3q("truncate present_applicant"); 
foreach($sites as $s){
  $query = rq('select * from '.table_prefix($s).'customers');
  while($c = mysql_fetch_array($query)){
    $sql = "insert into customers (
      customers_id,
      customers_gender,
      customers_firstname,
      customers_lastname,
      customers_firstname_f,
      customers_lastname_f,
      customers_dob,
      customers_email_address,
      customers_default_address_id,
      customers_telephone,
      customers_fax,
      customers_password,
      customers_newsletter,
      point,
      site_id,
      customers_guest_chk
      ) values(
        NULL,
        '" . $c['customers_gender'] . "',
        '" . $c['customers_firstname'] . "',
        '" . $c['customers_lastname'] . "',
        '" . $c['customers_firstname_f'] . "',
        '" . $c['customers_lastname_f'] . "',
        '" . $c['customers_dob'] . "',
        '" . $c['customers_email_address'] . "',
        '" . $c['customers_default_address_id'] . "',
        '" . $c['customers_telephone'] . "',
        '" . $c['customers_fax'] . "',
        '" . $c['customers_password'] . "',
        '" . $c['customers_newsletter'] . "',
        '" . $c['point'] . "',
        '" . site_id($s) . "',
        '" . $c['customers_guest_chk'] . "'
      )";
    r3q($sql);
    $customer_id = mysql_insert_id();
    //customers_info!
    $ciquery = rq("select * from ".table_prefix($s)."customers_info where customers_info_id=".$c['customers_id']);
    while($ci = mysql_fetch_array($ciquery)){
      $sql = "insert into customers_info(
        customers_info_id,
        customers_info_date_of_last_logon,
        customers_info_number_of_logons,
        customers_info_date_account_created,
        customers_info_date_account_last_modified,
        global_product_notifications
        )values(
          '".$customer_id."',
          '".$ci['customers_info_date_of_last_logon']."',
          '".$ci['customers_info_number_of_logons']."',
          '".$ci['customers_info_date_account_created']."',
          '".$ci['customers_info_date_account_last_modified']."',
          '".$ci['global_product_notifications']."'
        )";
      r3q($sql);
    }
    //address_book*@!
    $abquery = rq("select * from ".table_prefix($s)."address_book where customers_id=".$c['customers_id']);
    while($ab = mysql_fetch_array($abquery)){
      $sql = "insert into address_book (
        customers_id,
        address_book_id,
        entry_gender,
        entry_company,
        entry_firstname,
        entry_lastname,
        entry_firstname_f,
        entry_lastname_f,
        entry_street_address,
        entry_suburb,
        entry_postcode,
        entry_city,
        entry_state,
        entry_country_id,
        entry_telephone,
        entry_zone_id
        )values(
          '".$customer_id."',
          '".$ab['address_book_id']."',
          '".$ab['entry_gender']."',
          '".$ab['entry_company']."',
          '".$ab['entry_firstname']."',
          '".$ab['entry_lastname']."',
          '".$ab['entry_firstname_f']."',
          '".$ab['entry_lastname_f']."',
          '".$ab['entry_street_address']."',
          '".$ab['entry_suburb']."',
          '".$ab['entry_postcode']."',
          '".$ab['entry_city']."',
          '".$ab['entry_state']."',
          '".$ab['entry_country_id']."',
          '".$ab['entry_telephone']."',
          '".$ab['entry_zone_id']."'
        )";
      r3q($sql);
    }
    //customers_basket!
    $cbquery = rq("select * from ".table_prefix($s)."customers_basket where customers_id=".$c['customers_id']);
    while($cb = mysql_fetch_array($cbquery)){
      $sql = "insert into customers_basket(
        customers_basket_id,
        customers_id,
        products_id,
        customers_basket_quantity,
        final_price,
        customers_basket_date_added
        )values(
          NULL,
          '".$customer_id."',
          '".$cb['products_id']."',
          '".$cb['customers_basket_quantity']."',
          '".$cb['final_price']."',
          '".$cb['customers_basket_date_added']."'
        )";
      r3q($sql);
    }
    //customers_basket_attributes!
    $cbaquery = rq("select * from ".table_prefix($s)."customers_basket_attributes where customers_id=".$c['customers_id']);
    while($cba = mysql_fetch_array($cbaquery)){
      $sql = "insert into customers_basket_attributes(
        customers_basket_attributes_id,
        customers_id,
        products_id,
        products_options_id,
        products_options_value_id
        )values(
          NULL,
          '".$customer_id."',
          '".$cba['products_id']."',
          '".$cba['products_options_id']."',
          '".$cba['products_options_value_id']."'
        )";
      r3q($sql);
    }
    //orders@
    $oquery = rq("select * from ".table_prefix($s)."orders where customers_id=".$c['customers_id']);
    while($o = mysql_fetch_array($oquery)){
      $sql = "insert into orders(
        orders_id,
        site_id,
        customers_id,
        customers_name,
        customers_name_f,
        customers_company,
        customers_street_address,
        customers_suburb,
        customers_city,
        customers_postcode,
        customers_state,
        customers_country,
        customers_telephone,
        customers_email_address,
        customers_address_format_id,
        delivery_name,
        delivery_name_f,
        delivery_company,
        delivery_street_address,
        delivery_suburb,
        delivery_city,
        delivery_postcode,
        delivery_state,
        delivery_country,
        delivery_telephone,
        delivery_address_format_id,
        billing_name,
        billing_name_f,
        billing_company,
        billing_street_address,
        billing_suburb,
        billing_city,
        billing_postcode,
        billing_state,
        billing_country,
        billing_telephone,
        billing_address_format_id,
        payment_method,
        cc_type,
        cc_owner,
        cc_number,
        cc_expires,
        last_modified,
        date_purchased,
        orders_status,
        orders_date_finished,
        currency,
        currency_value,
        torihiki_Bahamut,
        torihiki_houhou,
        torihiki_date
        )values(
        '".$o['orders_id']."',
        '".site_id($s)."',
        '".$customer_id."',
        '".$o['customers_name']."',
        '".$o['customers_name_f']."',
        '".$o['customers_company']."',
        '".$o['customers_street_address']."',
        '".$o['customers_suburb']."',
        '".$o['customers_city']."',
        '".$o['customers_postcode']."',
        '".$o['customers_state']."',
        '".$o['customers_country']."',
        '".$o['customers_telephone']."',
        '".$o['customers_email_address']."',
        '".$o['customers_address_format_id']."',
        '".$o['delivery_name']."',
        '".$o['delivery_name_f']."',
        '".$o['delivery_company']."',
        '".$o['delivery_street_address']."',
        '".$o['delivery_suburb']."',
        '".$o['delivery_city']."',
        '".$o['delivery_postcode']."',
        '".$o['delivery_state']."',
        '".$o['delivery_country']."',
        '".$o['delivery_telephone']."',
        '".$o['delivery_address_format_id']."',
        '".$o['billing_name']."',
        '".$o['billing_name_f']."',
        '".$o['billing_company']."',
        '".$o['billing_street_address']."',
        '".$o['billing_suburb']."',
        '".$o['billing_city']."',
        '".$o['billing_postcode']."',
        '".$o['billing_state']."',
        '".$o['billing_country']."',
        '".$o['billing_telephone']."',
        '".$o['billing_address_format_id']."',
        '".$o['payment_method']."',
        '".$o['cc_type']."',
        '".$o['cc_owner']."',
        '".$o['cc_number']."',
        '".$o['cc_expires']."',
        '".$o['last_modified']."',
        '".$o['date_purchased']."',
        '".$o['orders_status']."',
        '".$o['orders_date_finished']."',
        '".$o['currency']."',
        '".$o['currency_value']."',
        '".$o['torihiki_Bahamut']."',
        '".$o['torihiki_houhou']."',
        '".$o['torihiki_date']."'
        )";
      r3q($sql);
      $orders_id = mysql_insert_id();
      //orders_products
      $opquery = rq("select * from ".table_prefix($s) . "orders_products where orders_id='" . $o['orders_id'] . "'");
      while($op = mysql_fetch_array($opquery)){
          $sql = "insert into orders_products(
             orders_products_id,
             orders_id,
             products_id,
             products_model,
             products_name,
             products_price,
             final_price,
             products_tax,
             products_quantity,
             products_character
        )values(
             NULL,
             '".$o['orders_id']."',
             '".$op['products_id']."',
             '".$op['products_model']."',
             '".$op['products_name']."',
             '".$op['products_price']."',
             '".$op['final_price']."',
             '".$op['products_tax']."',
             '".$op['products_quantity']."',
             '".$op['products_character']."'
        )";
        r3q($sql);
        //orders_products_attributes
        $orders_products_id = mysql_insert_id();
        $opaquery = rq("select * from ".table_prefix($s)."orders_products_attributes where orders_id='".$o['orders_id']."' and orders_products_id=".$op['orders_products_id']);
        while($opa = mysql_fetch_array($opaquery)){
          $sql = "insert into orders_products_attributes (
              orders_products_attributes_id,
              orders_id,
              orders_products_id,
              products_options,
              products_options_values,
              options_values_price,
              price_prefix,
              attributes_id
            ) values (
              NULL,
              '".$o['orders_id']."',
              '".$orders_products_id."',
              '".$opa['products_options']."',
              '".$opa['products_options_values']."',
              '".$opa['options_values_price']."',
              '".$opa['price_prefix']."',
              '".$opa['attributes_id']."'
            )";
          r3q($sql);
        }


        //orders_products_download
        $opdquery = rq("select * from ".table_prefix($s)."orders_products_download where orders_id='".$o['orders_id']."' and orders_products_id=".$op['orders_products_id']);
        while($opd = mysql_fetch_array($opdquery)){
          $sql = "insert into orders_products_download (
              orders_products_download_id,
              orders_id,
              orders_products_id,
              orders_products_filename,
              download_maxdays,
              download_count
            ) values (
              NULL,
              '".$o['orders_id']."',
              '".$orders_products_id."',
              '".$opd['orders_products_filename']."',
              '".$opd['download_maxdays']."',
              '".$opd['download_count']."'
            )";
          r3q($sql);
        }

      }

      //orders_status_history
      $oshquery = rq("select * from ".table_prefix($s)."orders_status_history where orders_id='".$o['orders_id']."'");
      while($osh = mysql_fetch_array($oshquery)){
        $sql = "insert into orders_status_history (
          orders_status_history_id,
          orders_id,
          orders_status_id,
          date_added,
          customer_notified,
          comments
          ) values (
            NULL,
            '".$o['orders_id']."',
            '".$osh['orders_status_id']."',
            '".$osh['date_added']."',
            '".$osh['customer_notified']."',
            '".$osh['comments']."'
          )";
        r3q($sql);
      }
      //orders_total
      $otquery = rq("select * from ".table_prefix($s)."orders_total where orders_id='".$o['orders_id']."'");
      while($ot = mysql_fetch_array($otquery)){
        $sql = "insert into orders_total (
          orders_total_id,
          orders_id,
          title,
          text,
          value,
          class,
          sort_order
          ) values (
            NULL,
            '".$o['orders_id']."',
            '".$ot['title']."',
            '".$ot['text']."',
            '".$ot['value']."',
            '".$ot['class']."',
            '".$ot['sort_order']."'
          )";
        r3q($sql);
      }
    }
    //present_applicant!@
    $paquery = rq("select * from ".table_prefix($s)."present_applicant where customer_id=".$c['customers_id']);
    while($pa = mysql_fetch_array($paquery)){
      //echo $s . ' ' . $pa['goods_id']."\n";
      // givp up applicant when present was deleted
      if(isset($pg2pg[$s][$pa['goods_id']])){
        $sql = "insert into present_applicant (
        id,
        goods_id,
        customer_id,
        family_name,
        mail,
        postcode,
        prefectures,
        cities,
        address1,
        address2,
        phone,
        tourokubi
        ) values (
          NULL,
          '" . $pg2pg[$s][$pa['goods_id']] . "',
          '" . $customer_id . "',
          '" . $pa['family_name'] . "',
          '" . $pa['mail'] . "',
          '" . $pa['postcode'] . "',
          '" . $pa['prefectures'] . "',
          '" . $pa['cities'] . "',
          '" . $pa['address1'] . "',
          '" . $pa['address2'] . "',
          '" . $pa['phone'] . "',
          '" . $pa['tourokubi'] . "'
        )";
        r3q($sql);
      }
    }
  }
}
print("customers\n");
//faq_categories!  //faq_questions!
r3q("truncate faq_categories");
r3q("truncate faq_questions");
foreach($faq as $f){
  $faqcquery = rq("select * from gm_faq".$f."_categories");
  while($faqc = mysql_fetch_array($faqcquery)){
    $sql = "insert into faq_categories (
      c_id,
      g_id,
      c_order,
      category
      ) values (
        NULL,
        '".$f."',
        '".$faqc['c_order']."',
        '".$faqc['category']."'
      )";
    r3q($sql);
    $fid = mysql_insert_id();
    $faqqquery = rq("select * from gm_faq".$f."_questions where c_id ='".$faqc['c_id']."'");
    while($faqq = mysql_fetch_array($faqqquery)){
      $sql = "insert into faq_questions (
        q_id,
        c_id,
        q_order,
        question,
        answer) values (
          NULL,
          '".$fid."',
          '".$faqq['q_order']."',
          '".$faqq['question']."',
          '".$faqq['answer']."'
        )";
      r3q($sql);
    }
  }
}
//image_documents! was wrong, so truncate
//image_document_types
cptable('wm_image_document_types', 'image_document_types');
//information_page*
cp3table('information_page');
//languages%
//cptable('wm_languages', 'languages');
//latest_news*
cp3table('latest_news');
//login+
cptable('login');
//mail_magazine*
cp3table('mail_magazine');
//manufacturers%
cptable('manufacturers');
//manufacturers_info%
cptable('manufacturers_info');
//newsletters
cp3table('newsletters');
//orders_mail
r3q("truncate orders_mail");
$osquery = rq("select * from jp_orders_mail");
while($os = mysql_fetch_array($osquery)){
  $sql = "insert into orders_mail (
    orders_status_id,
    language_id,
    site_id,
    orders_status_title,
    orders_status_mail
    ) values (
      '".$os['orders_status_id']."',
      '".$os['language_id']."',
      '0',
      '".mysql_real_escape_string($os['orders_status_title'])."',
      '".mysql_real_escape_string($os['orders_status_mail'])."'
    )";
  r3q($sql);
}
foreach($sites as $s){
  $osquery = rq("select * from ".table_prefix($s)."orders_mail");
  while($os = mysql_fetch_array($osquery)){
    $sql = "insert into orders_mail (
      orders_status_id,
      language_id,
      site_id,
      orders_status_title,
      orders_status_mail
      ) values (
        '".$os['orders_status_id']."',
        '".$os['language_id']."',
        '".site_id($s)."',
        '".mysql_real_escape_string($os['orders_status_title'])."',
        '".mysql_real_escape_string($os['orders_status_mail'])."'
      )";
    r3q($sql);
  }
}
print("orders_mail\n");
//orders_status
cptable('wm_orders_status', 'orders_status');
//permissions%
cptable('permissions');
//products%
cptable('products');
//products_description!
r3q("truncate products_description");
$sql = 'select * from products_description';
$query = rq($sql);
while($pd = mysql_fetch_array($query)){
  $sql = "insert into `products_description` (
    `products_id`,
    `language_id`,
    `products_name`,
    `products_description`,
    `site_id`,
    `products_attention_1`,
    `products_attention_2`,
    `products_attention_3`,
    `products_attention_4`,
    `products_attention_5`,
    `products_url`,
    `products_viewed`
      ) values (
        '" . $pd['products_id'] . "',
        '" . $pd['language_id'] . "',
        '" . mysql_real_escape_string($pd['products_name']) . "',
        '" . mysql_real_escape_string($pd['products_description']) . "',
        '0',
        '" . mysql_real_escape_string($pd['products_attention_1']) . "',
        '" . mysql_real_escape_string($pd['products_attention_2']) . "',
        '" . mysql_real_escape_string($pd['products_attention_3']) . "',
        '" . mysql_real_escape_string($pd['products_attention_4']) . "',
        '" . mysql_real_escape_string($pd['products_attention_5']) . "',
        '" . mysql_real_escape_string($pd['products_url']) . "',
        '" . $pd['products_viewed'] . "'
           ),(
        '" . $pd['products_id'] . "',
        '" . $pd['language_id'] . "',
        '" . mysql_real_escape_string($pd['products_name']) . "',
        '" . mysql_real_escape_string($pd['products_description']) . "',
        '" . R3MT_JP_ID . "',
        '" . mysql_real_escape_string($pd['products_attention_1']) . "',
        '" . mysql_real_escape_string($pd['products_attention_2']) . "',
        '" . mysql_real_escape_string($pd['products_attention_3']) . "',
        '" . mysql_real_escape_string($pd['products_attention_4']) . "',
        '" . mysql_real_escape_string($pd['products_attention_5']) . "',
        '" . mysql_real_escape_string($pd['products_url']) . "',
        '" . $pd['products_viewed'] . "'
      ), (
        '" . $pd['products_id'] . "',
        '" . $pd['language_id'] . "',
        '" . mysql_real_escape_string($pd['products_name']) . "',
        '" . mysql_real_escape_string($pd['products_description_gm']) . "',
        '" . R3MT_GM_ID . "',
        '" . mysql_real_escape_string($pd['products_attention_1']) . "',
        '" . mysql_real_escape_string($pd['products_attention_2']) . "',
        '" . mysql_real_escape_string($pd['products_attention_3']) . "',
        '" . mysql_real_escape_string($pd['products_attention_4']) . "',
        '" . mysql_real_escape_string($pd['products_attention_5']) . "',
        '" . mysql_real_escape_string($pd['products_url']) . "',
        '" . $pd['products_viewed'] . "'
      ), (
        '" . $pd['products_id'] . "',
        '" . $pd['language_id'] . "',
        '" . mysql_real_escape_string($pd['products_name']) . "',
        '" . mysql_real_escape_string($pd['products_description_wm']) . "',
        '" . R3MT_WM_ID . "',
        '" . mysql_real_escape_string($pd['products_attention_1']) . "',
        '" . mysql_real_escape_string($pd['products_attention_2']) . "',
        '" . mysql_real_escape_string($pd['products_attention_3']) . "',
        '" . mysql_real_escape_string($pd['products_attention_4']) . "',
        '" . mysql_real_escape_string($pd['products_attention_5']) . "',
        '" . mysql_real_escape_string($pd['products_url']) . "',
        '" . $pd['products_viewed'] . "'
      )";
  r3q($sql);
}
print("products_description\n");
//products_attributes%
cptable('products_attributes');
//products_attributes_download%
cptable('products_attributes_download');
//products_notifications!@ was wrong, so truncate
//products_options%
cptable('products_options');
//products_options_values%
cptable('products_options_values');
//products_options_values_to_products_options%
cptable('products_options_values_to_products_options');
//products_to_categories%
cptable('products_to_categories');
//products_to_image_documents?
//cptable('products_to_image_documents');
//products_to_tags //tags%
cptable('tags');
cptable('products_to_tags');
//reviews //reviews_description
r3q("truncate reviews");
r3q("truncate reviews_description");
foreach($sites as $s){
  $sql   = "select * from ".table_prefix($s)."reviews";
  $query = rq($sql);
  while($re = mysql_fetch_array($query)) {
    $sql = "insert into `reviews` (
      `reviews_id`,
      `products_id`,
      `customers_id`,
      `customers_name`,
      `reviews_rating`,
      `date_added`,
      `last_modified`,
      `reviews_read`,
      `site_id`,
      `reviews_status`
      ) values (
        NULL,
        '" . $re['products_id'] . "',
        '" . $re['customers_id'] . "',
        '" . $re['customers_name'] . "',
        '" . $re['reviews_rating'] . "',
        '" . $re['date_added'] . "',
        '" . $re['last_modified'] . "',
        '" . $re['reviews_read'] . "',
        '" . constant(strtoupper('r3mt_'.$s.'_id')) . "',
        '" . $re['reviews_status'] . "'
      )";
    r3q($sql);
    $rid = mysql_insert_id();
    $sql = "select * from ".table_prefix($s)."reviews_description where reviews_id=".$re['reviews_id'];
    $rdquery = rq($sql);
    while($rd = mysql_fetch_array($rdquery)){
      $sql = "insert into `reviews_description` (
        `reviews_id`,
        `languages_id`,
        `reviews_text`
        ) values (
          '" . $rid . "',
          '" . $rd['languages_id'] . "',
          '" . $rd['reviews_text'] . "'
        )";
      r3q($sql);
    }
  }
}
//sites
r3q("truncate sites");
$sites_values = array();
$sql = "insert into `sites` (id, romaji) values ";
foreach($sites as $s){
 $sites_values[] = "('".constant(strtoupper('r3mt_'.$s.'_id'))."', '".$s."')";
}
$sql .= join(',', $sites_values);
r3q($sql);
//specials%
cptable('specials');
//tax_class% //tax_rates%
cptable('wm_tax_class', 'tax_class');
cptable('wm_tax_rates', 'tax_rates');
//users%
cptable('users');
//zones% //geo_zones //zones_to_geo_zones%
cptable('wm_geo_zones', 'geo_zones');
cptable('wm_zones', 'zones');
cptable('wm_zones_to_geo_zones', 'zones_to_geo_zones');

// clear configuration
foreach($delete_configuration as $c){
  r3q("delete form configuration where configuration_key='".$c."'");
}


// functions
//function i($str) {
  //return iconv('euc-jp', 'utf-8', $str);
//}

function rq($sql){
  //echo RMT_DB_NAME;
  mysql_select_db(RMT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $sql . "#\n";
    echo $e . "#\n";
  }
  return $q;
}

function r3q($sql){
  mysql_select_db(R3MT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $sql . "\n";
    echo $e . "\n";
  }
  return $q;
}

/**
 * 
 */
function cptable($rtable, $r3table = null){
  if (!$r3table) $r3table = $rtable;
  $r3fields = $rfields = array();
  r3q("truncate $r3table");
  $q = r3q('describe '.$r3table);
  while ($f = mysql_fetch_array($q)) {
    $r3fields[] = $f['Field'];
  }
  $q = rq('describe '.$rtable);
  while ($f = mysql_fetch_array($q)) {
    $rfields[] = $f['Field'];
  }
  if(array_diff($rfields, $r3fields))
    echo "$rtable -> $r3table diff fields : " . join(',', array_diff($rfields, $r3fields)) . "\n";
  $q = rq("select * from " . $rtable);
  while($r = mysql_fetch_array($q)){
    $values = array();
    $sql = "insert into $r3table (`" . join('`,`', $r3fields) . "`) values ('";
    foreach($r3fields as $f){
      $values[] = mysql_real_escape_string($r[$f]);
    }
    $sql .= join("','", $values);
    $sql .= "')";
    r3q($sql);
  }
  print("$r3table \n");
}

function cp3table($table){
  global $sites;
  r3q("truncate $table");
  $fields = array();
  $q = r3q('describe '.$table);
  while ($f = mysql_fetch_array($q)) {
    $fields[] = $f['Field'];
  }
  foreach($sites as $s){
    $q = rq("select * from " . table_prefix($s) . $table);
    while($r = mysql_fetch_array($q)){
      $values = array();
      $sql = "insert into $table (`" . join('`,`', $fields) ."`) values ('";
      foreach($fields as $k => $f){
        if($k === 0){
          $values[] = null;
        }elseif($f == 'site_id'){
          $values[] = constant(strtoupper('r3mt_'.$s.'_id'));
        }else{
          $values[] = mysql_real_escape_string($r[$f]);
        }
      }
      $sql .= join("','", $values);
      $sql .= "')";
      r3q($sql);
    }
  }
  print("$table \n");
}

function table_prefix($s){
  if(strtoupper($s) == 'JP'){
    return 'iimy_';
  } else 
    return $s.'_';
}
 
function site_id($s){
  return constant(strtoupper('r3mt_'.$s.'_id'));
}
