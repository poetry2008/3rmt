<?php
include 'db_include.php';
$pg2pg = unserialize(file_get_contents('pg2pg'));
foreach(array('wm') as $s){
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


    // reviews && reviews_description
  $reviewssql   = "select * from ".table_prefix($s)."reviews where customers_id='".$c['customers_id']."'";
  $reviewsquery = rq($reviewssql);
  while($re = mysql_fetch_array($reviewsquery)) {
    $reviewsinsertsql = "insert into `reviews` (
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
        '" . $customer_id . "',
        '" . $re['customers_name'] . "',
        '" . $re['reviews_rating'] . "',
        '" . $re['date_added'] . "',
        '" . $re['last_modified'] . "',
        '" . $re['reviews_read'] . "',
        '" . constant(strtoupper('r3mt_'.$s.'_id')) . "',
        '" . $re['reviews_status'] . "'
      )";
    r3q($reviewsinsertsql);
    
    $rid = mysql_insert_id();
    $reviewsdescsql = "select * from ".table_prefix($s)."reviews_description where reviews_id=".$re['reviews_id'];
    $rdquery = rq($reviewsdescsql);
    while($rd = mysql_fetch_array($rdquery)){
      $irdsql = "insert into `reviews_description` (
        `reviews_id`,
        `languages_id`,
        `reviews_text`
        ) values (
          '" . $rid . "',
          '" . $rd['languages_id'] . "',
          '" . mysql_real_escape_string($rd['reviews_text']) . "'
        )";
      r3q($irdsql);
    }
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
             '".mysql_real_escape_string($op['products_model'])."',
             '".mysql_real_escape_string($op['products_name'])."',
             '".$op['products_price']."',
             '".$op['final_price']."',
             '".$op['products_tax']."',
             '".$op['products_quantity']."',
             '".mysql_real_escape_string($op['products_character'])."'
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
              '".mysql_real_escape_string($opa['products_options'])."',
              '".mysql_real_escape_string($opa['products_options_values'])."',
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
            '".mysql_real_escape_string($osh['comments'])."'
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
            '".mysql_real_escape_string($ot['title'])."',
            '".mysql_real_escape_string($ot['text'])."',
            '".mysql_real_escape_string($ot['value'])."',
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
          '" . mysql_real_escape_string($pa['family_name']) . "',
          '" . $pa['mail'] . "',
          '" . $pa['postcode'] . "',
          '" . $pa['prefectures'] . "',
          '" . mysql_real_escape_string($pa['cities']) . "',
          '" . mysql_real_escape_string($pa['address1']) . "',
          '" . mysql_real_escape_string($pa['address2']) . "',
          '" . $pa['phone'] . "',
          '" . $pa['tourokubi'] . "'
        )";
        r3q($sql);
      }
    }
  }
}
print("wm customers\n");
