<?php
include 'db_include.php';

//address_format
cmptable('wm_address_format', 'address_format');
//banners
cmp3table('banners');
//banners_history
//#cache
//calendar
cmp3table('calendar');
//categories
cmptable('categories');
//categories_description
$categories_description_query = rq("select * from categories_description");
while($categories_description = mysql_fetch_array($categories_description_query)) {
  $default_cd_sql ="select * from categories_description 
  where categories_id='".m($categories_description['categories_id'])."'
    and language_id='".m($categories_description['language_id'])."'
    and categories_name='".m($categories_description['categories_name'])."'
    and seo_name='".m($categories_description['seo_name'])."'
    and seo_description=''
    and categories_image3='".m($categories_description['categories_image3'])."'
    and categories_meta_text='".m($categories_description['categories_meta_text'])."'
    and categories_header_text=''
    and categories_footer_text=''
    and text_information=''
    and meta_keywords=''
    and meta_description=''
    and site_id='0'
    ";
  $jp_cd_sql = "select * from categories_description 
  where categories_id='".m($categories_description['categories_id'])."'
    and language_id='".m($categories_description['language_id'])."'
    and categories_name='".m($categories_description['categories_name'])."'
    and seo_name='".m($categories_description['seo_name'])."'
    and seo_description='".m($categories_description['seo_description'])."'
    and categories_image3='".m($categories_description['categories_image3'])."'
    and categories_meta_text='".m($categories_description['categories_meta_text'])."'
    and categories_header_text='".m($categories_description['categories_header_text_jp'])."'
    and categories_footer_text='".m($categories_description['categories_footer_text_jp'])."'
    and text_information='".m($categories_description['text_information'])."'
    and meta_keywords='".m($categories_description['meta_keywords_jp'])."'
    and meta_description='".m($categories_description['meta_description_jp'])."'
    and site_id='1'
  ";
  $gm_cd_sql = "select * from categories_description 
  where categories_id='".m($categories_description['categories_id'])."'
    and language_id='".m($categories_description['language_id'])."'
    and categories_name='".m($categories_description['categories_name'])."'
    and seo_name='".m($categories_description['seo_name'])."'
    and seo_description='".m($categories_description['seo_description_gm'])."'
    and categories_image3='".m($categories_description['categories_image3'])."'
    and categories_meta_text='".m($categories_description['categories_meta_text'])."'
    and categories_header_text='".m($categories_description['categories_header_text_gm'])."'
    and categories_footer_text='".m($categories_description['categories_footer_text_gm'])."'
    and text_information='".m($categories_description['text_information_gm'])."'
    and meta_keywords='".m($categories_description['meta_keywords_gm'])."'
    and meta_description='".m($categories_description['meta_description_gm'])."'
    and site_id='2'
  ";
  $wm_cd_sql = "select * from categories_description 
  where categories_id='".m($categories_description['categories_id'])."'
    and language_id='".m($categories_description['language_id'])."'
    and categories_name='".m($categories_description['categories_name'])."'
    and seo_name='".m($categories_description['seo_name'])."'
    and seo_description='".m($categories_description['seo_description_wm'])."'
    and categories_image3='".m($categories_description['categories_image3'])."'
    and categories_meta_text='".m($categories_description['categories_meta_text'])."'
    and categories_header_text='".m($categories_description['categories_header_text_wm'])."'
    and categories_footer_text='".m($categories_description['categories_footer_text_wm'])."'
    and text_information='".m($categories_description['text_information_wm'])."'
    and meta_keywords='".m($categories_description['meta_keywords_wm'])."'
    and meta_description='".m($categories_description['meta_description_wm'])."'
    and site_id='3'
  ";
  if(mysql_num_rows(r3q($default_cd_sql))!=1){
    echo $default_cd_sql."\n";
  }
  if(mysql_num_rows(r3q($jp_cd_sql))!=1){
    echo $jp_cd_sql."\n";
  }
  if(mysql_num_rows(r3q($gm_cd_sql))!=1){
    echo $gm_cd_sql."\n";
  }
  if(mysql_num_rows(r3q($wm_cd_sql))!=1){
    echo $wm_cd_sql."\n";
  }
}

//categories_rss
cmptable('categories_rss');
//#color
//#color_to_products
//configuration
cmp3table('configuration');
//configuration_group
cmptable('wm_configuration_group', 'configuration_group');
//contents
cmp3table('contents');
//countries
cmptable('iimy_countries', 'countries');
//currencies
cmptable('wm_currencies', 'currencies');
//faq_categories
foreach($faq as $f) {
  $faq_categories_query = rq("select * from gm_faq".$f."_categories");
  //echo("select * from gm_faq".$f."_categories\n");
  //echo(mysql_num_rows($faq_categories_query)."\n");
  while ($faq_categories = mysql_fetch_array($faq_categories_query)) {
    //echo 'fuk';
    $f3c_sql = "select * from faq_categories 
      where g_id='".$f."'
        and c_order='".m($faq_categories['c_order'])."'
        and category='".m($faq_categories['category'])."'
    ";
    //echo $f3c_sql . "\n";
    $f3c_query = r3q($f3c_sql);
    if (mysql_num_rows($f3c_query) != 1) {
      echo $f3c_sql."\n";
      continue;
    }
    $f3c = mysql_fetch_array($f3c_query);
    //faq_questions
    //var_dump($faq_categories);
    $faq_questions_query = rq("select * from gm_faq".$f."_questions where c_id='".$faq_categories['c_id']."'");
   //echo("select * from gm_faq".$f."_questions where c_id='".$faq_category['c_id']."'");
    while($fq = mysql_fetch_array($faq_questions_query)) {
      //echo 'ufo';
      $f3q_sql = "select * from faq_questions 
        where c_id='".$f3c['c_id']."'
        and q_order='".$fq['q_order']."'
        and question='".m($fq['question'])."'
        and answer='".m($fq['answer'])."'
        ";
      if(mysql_num_rows(r3q($f3q_sql)) != 1) {
        echo $f3q_sql."\n";
      }
    }
  }
}

//geo_zones
cmptable('wm_geo_zones', 'geo_zones');
//image_document_types
cmptable('wm_image_document_types', 'image_document_types');
//#image_documents
//information_page
cmp3table('information_page');
//languages
cmptable('wm_languages', 'languages');
//latest_news
cmp3table('latest_news');
//login
cmptable('login');
//mail_magazine
cmp3table('mail_magazine');
//manufacturers
cmptable('manufacturers');
//manufacturers_info
cmptable('manufacturers_info');
//newsletters
cmp3table('newsletters');
//orders_mail




//orders_status
cmptable('wm_orders_status', 'orders_status');
//permissions
cmptable('permissions');
//present_goods
cmp3table('present_goods');
//products
cmptable('products');
//products_attributes
cmptable('products_attributes');
//products_attributes_download
cmptable('products_attributes_download');
//products_description
$products_description_query = rq("select * from products_description");
while($products_description = mysql_fetch_array($products_description_query)) {
  $default_pd_sql ="select * from products_description 
  where products_id='".m($products_description['products_id'])."'
    and language_id='".m($products_description['language_id'])."'
    and products_name='".m($products_description['products_name'])."'
    and products_description=''
    and products_attention_1='".m($products_description['products_attention_1'])."'
    and products_attention_2='".m($products_description['products_attention_2'])."'
    and products_attention_3='".m($products_description['products_attention_3'])."'
    and products_attention_4='".m($products_description['products_attention_4'])."'
    and products_attention_5='".m($products_description['products_attention_5'])."'
    and products_url='".m($products_description['products_url'])."'
    and products_viewed='".m($products_description['products_viewed'])."'
    and site_id='0'
    ";
  $jp_pd_sql = "select * from products_description 
  where products_id='".m($products_description['products_id'])."'
    and language_id='".m($products_description['language_id'])."'
    and products_name='".m($products_description['products_name'])."'
    and products_description='".m($products_description['products_description'])."'
    and products_attention_1='".m($products_description['products_attention_1'])."'
    and products_attention_2='".m($products_description['products_attention_2'])."'
    and products_attention_3='".m($products_description['products_attention_3'])."'
    and products_attention_4='".m($products_description['products_attention_4'])."'
    and products_attention_5='".m($products_description['products_attention_5'])."'
    and products_url='".m($products_description['products_url'])."'
    and products_viewed='".m($products_description['products_viewed'])."'
    and site_id='1'
  ";
  $gm_pd_sql = "select * from products_description 
  where products_id='".m($products_description['products_id'])."'
    and language_id='".m($products_description['language_id'])."'
    and products_name='".m($products_description['products_name'])."'
    and products_description='".m($products_description['products_description_gm'])."'
    and products_attention_1='".m($products_description['products_attention_1'])."'
    and products_attention_2='".m($products_description['products_attention_2'])."'
    and products_attention_3='".m($products_description['products_attention_3'])."'
    and products_attention_4='".m($products_description['products_attention_4'])."'
    and products_attention_5='".m($products_description['products_attention_5'])."'
    and products_url='".m($products_description['products_url'])."'
    and products_viewed='".m($products_description['products_viewed'])."'
    and site_id='2'
  ";
  $wm_pd_sql = "select * from products_description 
  where products_id='".m($products_description['products_id'])."'
    and language_id='".m($products_description['language_id'])."'
    and products_name='".m($products_description['products_name'])."'
    and products_description='".m($products_description['products_description_wm'])."'
    and products_attention_1='".m($products_description['products_attention_1'])."'
    and products_attention_2='".m($products_description['products_attention_2'])."'
    and products_attention_3='".m($products_description['products_attention_3'])."'
    and products_attention_4='".m($products_description['products_attention_4'])."'
    and products_attention_5='".m($products_description['products_attention_5'])."'
    and products_url='".m($products_description['products_url'])."'
    and products_viewed='".m($products_description['products_viewed'])."'
    and site_id='3'
  ";
  if(mysql_num_rows(r3q($default_pd_sql))!=1){
    echo $default_pd_sql."\n";
  }
  if(mysql_num_rows(r3q($jp_pd_sql))!=1){
    echo $jp_pd_sql."\n";
  }
  if(mysql_num_rows(r3q($gm_pd_sql))!=1){
    echo $gm_pd_sql."\n";
  }
  if(mysql_num_rows(r3q($wm_pd_sql))!=1){
    echo $wm_pd_sql."\n";
  }
}
//products_notifications
//products_options
cmptable('products_options');
//products_options_values
cmptable('products_options_values');
//products_options_values_to_products_options
cmptable('products_options_values_to_products_options');
//products_to_categories
cmptable('products_to_categories');
//#products_to_image_documents
//products_to_tags
cmptable('products_to_tags');
//reviews
//reviews_description
foreach($sites as $s) {
  $reviews_query = rq("select * from ".table_prefix($s)."reviews r,".table_prefix($s)."reviews_description rd where rd.reviews_id=r.reviews_id and r.customers_id='0'");
  while($reviews = mysql_fetch_array($reviews_query)) {
    $r3_sql = "
      select * from reviews r,reviews_description rd
      where r.reviews_id=rd.reviews_id
        and r.products_id='".$reviews['products_id']."'
        and r.customers_id='0'
        and r.customers_name='".m($reviews['customers_name'])."'
        and r.reviews_rating='".m($reviews['reviews_rating'])."'
        and r.date_added='".m($reviews['date_added'])."'
        and r.reviews_read='".m($reviews['reviews_read'])."'
        and r.reviews_status='".m($reviews['reviews_status'])."'
        and rd.languages_id='".m($reviews['languages_id'])."'
        and rd.reviews_text='".m($reviews['reviews_text'])."'
        and r.site_id='".site_id($s)."'
      ";
    if(mysql_num_rows(r3q($r3_sql))!=1) {
      echo $r3_sql."\n";
    }
  }
}
//#sessions
//#sites
//specials
cmptable('specials');
//#sql_log
//tags
cmptable('tags');
//tax_class
cmptable('wm_tax_class', 'tax_class');
//tax_rates
cmptable('wm_tax_rates', 'tax_rates');
//users
cmptable('users');
//whos_online
//zones
cmptable('wm_zones', 'zones');
//zones_to_geo_zones
cmptable('wm_zones_to_geo_zones', 'zones_to_geo_zones');


//!customers
  // reviews&reviews_description
  //!customers_info
  //!address_book
  //customers_basket
    //customers_basket_attributes
  //!orders
    //!orders_products
      //!orders_products_attributes
  //#orders_products_download
    //!orders_status_history
    //!orders_total
  //present_applicant


foreach ($sites as $s) {
  $customers_query = rq("select * from ".table_prefix($s)."customers order by customers_id asc");
  while ($customers = mysql_fetch_array($customers_query)) {
    $c3ustomers_sql = "
            select * from customers 
            where customers_gender='".m($customers['customers_gender'])."' 
            and customers_firstname='".m($customers['customers_firstname'])."' 
            and customers_lastname='".m($customers['customers_lastname'])."' 
            and customers_firstname_f='".m($customers['customers_firstname_f'])."' 
            and customers_lastname_f='".m($customers['customers_lastname_f'])."' 
            and customers_dob='".m($customers['customers_dob'])."' 
            and customers_email_address='".m($customers['customers_email_address'])."' 
            and customers_default_address_id='".m($customers['customers_default_address_id'])."' 
            and customers_telephone='".m($customers['customers_telephone'])."' 
            and customers_fax='".m($customers['customers_fax'])."' 
            and customers_password='".m($customers['customers_password'])."' 
            and customers_newsletter='".m($customers['customers_newsletter'])."' 
            and point='".m($customers['point'])."' 
            and customers_guest_chk='".m($customers['customers_guest_chk'])."'";
    $c3ustomers_query = r3q($c3ustomers_sql);
    if (mysql_num_rows($c3ustomers_query) != 1) {
      echo $s.':customers id:'.$customers['customers_id']." not found in 3rmt\n";
      echo $c3ustomers_sql."\n";
      continue;
    }
    // customers_info
    $_3customers = mysql_fetch_array($c3ustomers_query);
    $customers_info = mysql_fetch_array(rq("select * from ".table_prefix($s)."customers_info where customers_info_id='".$customers['customers_id']."'"));
    $ci_sql = "select * from customers_info 
            where customers_info_id='".$_3customers['customers_id']."'
            and customers_info_date_of_last_logon='".$customers_info['customers_info_date_of_last_logon']."'
            and customers_info_number_of_logons='".$customers_info['customers_info_number_of_logons']."'
            and customers_info_date_account_created='".$customers_info['customers_info_date_account_created']."'
            and customers_info_date_account_last_modified='".$customers_info['customers_info_date_account_last_modified']."'
            and global_product_notifications='".$customers_info['global_product_notifications']."'";
    if (mysql_num_rows(r3q($ci_sql)) != 1) {
      echo $s.':customers_info id:'.$customers['customers_id']." not found in 3rmt\n";
      echo $ci_sql."\n";
    }
    // address_book
    $address_book_query = rq("select * from ".table_prefix($s)."address_book where customers_id='".$customers['customers_id']."'");
    while($address_book = mysql_fetch_array($address_book_query)){
      $_3ab_sql = "select * from address_book 
              where customers_id='".m($_3customers['customers_id'])."'
              and address_book_id='".m($address_book['address_book_id'])."'
              and entry_gender='".m($address_book['entry_gender'])."'
              and entry_company='".m($address_book['entry_company'])."'
              and entry_firstname='".m($address_book['entry_firstname'])."'
              and entry_lastname='".m($address_book['entry_lastname'])."'
              and entry_firstname_f='".m($address_book['entry_firstname_f'])."'
              and entry_lastname_f='".m($address_book['entry_lastname_f'])."'
              and entry_street_address='".m($address_book['entry_street_address'])."'
              and entry_suburb='".m($address_book['entry_suburb'])."'
              and entry_postcode='".m($address_book['entry_postcode'])."'
              and entry_city='".m($address_book['entry_city'])."'
              and entry_state='".m($address_book['entry_state'])."'
              and entry_country_id='".m($address_book['entry_country_id'])."'
              and entry_telephone='".m($address_book['entry_telephone'])."'
              and entry_zone_id='".m($address_book['entry_zone_id'])."'";
      if (mysql_num_rows(r3q($_3ab_sql))!=1) {
        echo $s.':address_book customers_id:'.$customers['customers_id']." address_book_id:".$address_book['address_book_id']." not found in 3rmt\n";
        echo $_3ab_sql."\n";
      }
    }
    // orders
    $orders_query = rq("select * from ".table_prefix($s)."orders where customers_id='".$customers['customers_id']."'");
    //echo "select * from ".table_prefix($s)."orders where customers_id='".$customers['customers_id']."'\n";
    while($orders = mysql_fetch_array($orders_query)) {
      $_3o_sql = "select * from orders 
        where orders_id='".$orders['orders_id']."'
        and site_id='".site_id($s)."'
        and customers_id='".$_3customers['customers_id']."'
        and customers_name='".m($orders['customers_name'])."'
        and customers_name_f='".m($orders['customers_name_f'])."'
        and customers_company='".m($orders['customers_company'])."'
        and customers_street_address='".m($orders['customers_street_address'])."'
        and customers_suburb='".m($orders['customers_suburb'])."'
        and customers_city='".m($orders['customers_city'])."'
        and customers_postcode='".m($orders['customers_postcode'])."'
        and customers_state='".m($orders['customers_state'])."'
        and customers_country='".m($orders['customers_country'])."'
        and customers_telephone='".m($orders['customers_telephone'])."'
        and customers_email_address='".m($orders['customers_email_address'])."'
        and customers_address_format_id='".m($orders['customers_address_format_id'])."'
        and delivery_name='".m($orders['delivery_name'])."'
        and delivery_name_f='".m($orders['delivery_name_f'])."'
        and delivery_company='".m($orders['delivery_company'])."'
        and delivery_street_address='".m($orders['delivery_street_address'])."'
        and delivery_suburb='".m($orders['delivery_suburb'])."'
        and delivery_city='".m($orders['delivery_city'])."'
        and delivery_postcode='".m($orders['delivery_postcode'])."'
        and delivery_state='".m($orders['delivery_state'])."'
        and delivery_country='".m($orders['delivery_country'])."'
        and delivery_telephone='".m($orders['delivery_telephone'])."'
        and delivery_address_format_id='".m($orders['delivery_address_format_id'])."'
        and billing_name='".m($orders['billing_name'])."'
        and billing_name_f='".m($orders['billing_name_f'])."'
        and billing_company='".m($orders['billing_company'])."'
        and billing_street_address='".m($orders['billing_street_address'])."'
        and billing_suburb='".m($orders['billing_suburb'])."'
        and billing_city='".m($orders['billing_city'])."'
        and billing_postcode='".m($orders['billing_postcode'])."'
        and billing_state='".m($orders['billing_state'])."'
        and billing_country='".m($orders['billing_country'])."'
        and billing_telephone='".m($orders['billing_telephone'])."'
        and billing_address_format_id='".m($orders['billing_address_format_id'])."'
        and payment_method='".m($orders['payment_method'])."'
        and cc_type='".m($orders['cc_type'])."'
        and cc_owner='".m($orders['cc_owner'])."'
        and cc_number='".m($orders['cc_number'])."'
        and cc_expires='".m($orders['cc_expires'])."'
        and last_modified='".m($orders['last_modified'])."'
        and date_purchased='".m($orders['date_purchased'])."'
        and orders_status='".m($orders['orders_status'])."'
        and orders_date_finished='".m($orders['orders_date_finished'])."'
        and currency='".m($orders['currency'])."'
        and currency_value='".m($orders['currency_value'])."'
        and torihiki_Bahamut='".m($orders['torihiki_Bahamut'])."'
        and torihiki_houhou='".m($orders['torihiki_houhou'])."'
        and torihiki_date='".m($orders['torihiki_date'])."'
        and code_fee='".m($orders['code_fee'])."'
        ";
      $_3o_query = r3q($_3o_sql);
        if (mysql_num_rows($_3o_query)!=1) {
          echo $s.':order id:'.$orders['orders_id']." not found in 3rmt\n";
          echo $_3o_sql."\n";
          continue;
        }
        $_3o = mysql_fetch_array($_3o_query);
        // orders_products
        $op_query = rq("select * from ".table_prefix($s)."orders_products where orders_id='".$orders['orders_id']."'");
        //echo "select * from ".table_prefix($s)."orders_products where orders_id='".$orders['orders_id']."'";
        while($orders_product = mysql_fetch_array($op_query)) {

          $o3p_sql = "select * from orders_products
          where orders_id='".$orders['orders_id']."'
            and products_id='".m($orders_product['products_id'])."'
            and products_model='".m($orders_product['products_model'])."'
            and products_name='".m($orders_product['products_name'])."'
            and products_price='".m($orders_product['products_price'])."'
            and final_price='".m($orders_product['final_price'])."'
            and products_tax='".m($orders_product['products_tax'])."'
            and products_quantity='".m($orders_product['products_quantity'])."'
            and products_character='".m($orders_product['products_character'])."'
          ";
          $o3p_query = r3q($o3p_sql);
          if (mysql_num_rows($o3p_query)<1) {
            echo $s.':order_products_id:'.$orders_product['orders_products_id']." not found in 3rmt\n";
            echo $o3p_sql."\n";
            continue;
          }
          $o3p = mysql_fetch_array($o3p_query);
          
            // orders_products_attributes
            $opa_query = rq("select * from ".table_prefix($s)."orders_products_attributes where orders_products_id='".$orders_product['orders_products_id']."'");
            //echo "select * from ".table_prefix($s)."orders_products_attributes where orders_products_id='".$orders_product['orders_products_id']."'\n";
            while($orders_products_attribute = mysql_fetch_array($opa_query)) {
              //echo "a\n";
              $o3pa_sql = "select * from orders_products_attributes
              where orders_id='".$orders['orders_id']."'
                and orders_products_id='".$o3p['orders_products_id']."'
                and products_options='".$orders_products_attribute['products_options']."'
                and products_options_values='".$orders_products_attribute['products_options_values']."'
                and options_values_price='".$orders_products_attribute['options_values_price']."'
                and price_prefix='".$orders_products_attribute['price_prefix']."'
                and attributes_id='".$orders_products_attribute['attributes_id']."'
              ";
              if (mysql_num_rows(r3q($o3pa_sql))!=1) {
                echo $s.':order_products_attributes_id:'.$orders_products_attribute['orders_products_attributes_id']." not found in 3rmt\n";
                echo $o3pa_sql."\n";
                continue;
              }
            }
          
        }// end orders_products
      // orders_status_history
      $osh_query = rq("select * from ".table_prefix($s)."orders_status_history where orders_id='".$orders['orders_id']."'");
      while ($osh = mysql_fetch_array($osh_query)) {
        $o3sh_sql = "
          select * from orders_status_history
          where orders_id='".$orders['orders_id']."'
            and orders_status_id='".m($osh['orders_status_id'])."'
            and date_added='".m($osh['date_added'])."'
            and customer_notified='".m($osh['customer_notified'])."'
            and comments='".m($osh['comments'])."'
        ";
        //echo $o3sh_sql."\n";
        if(mysql_num_rows(r3q($o3sh_sql))<1){
          echo $s.':orders_id:'.$orders['orders_id']." status:".$osh['orders_status_id']." not found in 3rmt\n";
          echo $o3sh_sql."\n";
          continue;
        }
      }
      
      
      // orders_total
      $ot_query = rq("select * from ".table_prefix($s)."orders_total where orders_id='".$orders['orders_id']."'");
      while ($ot = mysql_fetch_array($ot_query)) {
        $o3t_sql = "
          select * from orders_total
          where orders_id='".$orders['orders_id']."'
            and title='".m($ot['title'])."'
            and text='".m($ot['text'])."'
            and value='".m($ot['value'])."'
            and class='".m($ot['class'])."'
            and sort_order='".m($ot['sort_order'])."'
        ";
        //echo $o3t_sql."\n";
        if(mysql_num_rows(r3q($o3t_sql))!=1){
          echo $s.':orders_id:'.$orders['orders_id']." total:".$ot['title']." not found in 3rmt\n";
          echo $o3t_sql."\n";
          continue;
        }
      }
    }// end orders
    
    // present_applicant
    //echo "b\n";
    $present_applicant_query = rq("select * from ".table_prefix($s)."present_applicant where customer_id='".$customers['customers_id']."'");
    //echo "c\n";
    while ($present_applicant = mysql_fetch_array($present_applicant_query)) {
      $p3resent_applicant_sql = "select * from present_applicant
        where customer_id='".$_3customers['customers_id']."'
          and family_name='".m($present_applicant['family_name'])."'
          and first_name='".m($present_applicant['first_name'])."'
          and mail='".m($present_applicant['mail'])."'
          and postcode='".m($present_applicant['postcode'])."'
          and prefectures='".m($present_applicant['prefectures'])."'
          and cities='".m($present_applicant['cities'])."'
          and address1='".m($present_applicant['address1'])."'
          and address2='".m($present_applicant['address2'])."'
          and phone='".m($present_applicant['phone'])."'
          and tourokubi='".m($present_applicant['tourokubi'])."'
      ";
      if(mysql_num_rows(r3q($p3resent_applicant_sql))!=1){
        echo $s.':customers:'.$customers['customers_id']." present_applicant id:".$present_applicant['id']." not found in 3rmt\n";
        echo $p3resent_applicant_sql."\n";
        continue;
      }
    }
    // reviews&review_description
    $reviews_query = rq("select * from ".table_prefix($s)."reviews r,".table_prefix($s)."reviews_description rd where rd.reviews_id=r.reviews_id and r.customers_id='".$customers['customers_id']."'");
    while($reviews = mysql_fetch_array($reviews_query)) {
      $r3_sql = "
        select * from reviews r,reviews_description rd
        where r.reviews_id=rd.reviews_id
          and r.products_id='".$reviews['products_id']."'
          and r.customers_id='".$_3customers['customers_id']."'
          and r.customers_name='".m($reviews['customers_name'])."'
          and r.reviews_rating='".m($reviews['reviews_rating'])."'
          and r.date_added='".m($reviews['date_added'])."'
          and r.reviews_read='".m($reviews['reviews_read'])."'
          and r.reviews_status='".m($reviews['reviews_status'])."'
          and rd.languages_id='".m($reviews['languages_id'])."'
          and rd.reviews_text='".m($reviews['reviews_text'])."'
          and r.site_id='".site_id($s)."'
        ";
      if(mysql_num_rows(r3q($r3_sql))!=1) {
        echo $r3_sql."\n";
      }
    }
  } // end customers
} // end sites
