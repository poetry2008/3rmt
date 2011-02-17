<?php
include './db_include.php';
// ! => special
// % => origin
// * => site_id
// @ => customer_id
// + => truncate
phpinfo();

// ADD INDEX
foreach($sites as $s){
  rq("ALTER TABLE  `".table_prefix($s)."address_book` ADD INDEX (  `customers_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."customers_basket` ADD INDEX ( `customers_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."customers_basket_attributes` ADD INDEX ( `customers_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."orders` ADD INDEX (  `customers_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."orders_products` ADD INDEX (  `orders_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."orders_products_attributes` ADD INDEX ( `orders_id` ,  `orders_products_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."orders_status_history` ADD INDEX (  `orders_id` ) ;");
  rq("ALTER TABLE  `".table_prefix($s)."orders_total` ADD INDEX (  `orders_id` ) ;");
}

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
        '" . mysql_real_escape_string($pg['title']) . "',
        '" . $pg['image'] . "',
        '" . mysql_real_escape_string($pg['text']) . "',
        '" . site_id($s) . "',
        '" . $pg['start_date'] . "',
        '" . $pg['limit_date'] . "'
      )";
    //echo $sql."\n";
    r3q($sql);
    $pg2pg[$s][$pg['goods_id']] = mysql_insert_id();
  }
}
print("present_goods \n");
//address_format
cptable('wm_address_format', 'address_format');
// countries
cptable('iimy_countries', 'countries');
//exit;
//banners*
//cp3table('banners');

foreach($sites as $s) {
  $bquery = rq("select * from ".table_prefix($s)."banners");
  while($banner =  mysql_fetch_array($bquery)) {
    r3q("
        insert into banners (
          banners_id,
          banners_title,
          banners_url,
          banners_image,
          banners_group,
          banners_html_text,
          expires_impressions,
          expires_date,
          date_scheduled,
          date_added,
          date_status_change,
          status,
          site_id
          ) values (
            NULL,
            '".mysql_real_escape_string($banner['banners_title'])."',
            '".mysql_real_escape_string($banner['banners_url'])."',
            '".mysql_real_escape_string($banner['banners_image'])."',
            '".mysql_real_escape_string($banner['banners_group'])."',
            '".mysql_real_escape_string($banner['banners_html_text'])."',
            '".mysql_real_escape_string($banner['expires_impressions'])."',
            '".mysql_real_escape_string($banner['expires_date'])."',
            '".mysql_real_escape_string($banner['date_scheduled'])."',
            '".mysql_real_escape_string($banner['date_added'])."',
            '".mysql_real_escape_string($banner['date_status_change'])."',
            '".mysql_real_escape_string($banner['status'])."',
            '" . site_id($s) . "'
          )
        ");
    $bid = mysql_insert_id();
    $bhquery = rq("select * from ".table_prefix($s)."banners_history where banners_id='".$banner['banners_id']."'");
    while($bh = mysql_fetch_array($bhquery)){
      $sql = "
        insert into banners_history (
            banners_history_id,
            banners_id,
            banners_shown,
            banners_clicked,
            banners_history_date
            ) values (
              null,
              '".$bid."',
              '".$bh['banners_shown']."',
              '".$bh['banners_clicked']."',
              '".$bh['banners_history_date']."'
            )
        ";
      r3q($sql);
    }
  }
}

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
      '',
      '',
      '',
      '',
      '',
      ''
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
      '".mysql_real_escape_string($c['configuration_value'])."',
      '".mysql_real_escape_string($c['configuration_description'])."',
      '".$c['configuration_group_id']."',
      '".$c['sort_order']."',
      '".$c['last_modified']."',
      '".$c['date_added']."',
      '".$c['use_function']."',
      '".mysql_real_escape_string($c['set_function'])."',
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
//
//present_applicant
// 处理customers_id = 0的申请
r3q("truncate present_applicant");
foreach($sites as $s) {
  $paquery = rq("select * from " . table_prefix($s) . "present_applicant where customer_id='0'");
  while($pa = mysql_fetch_array($paquery)){
    if (isset($pg2pg[$s][$pa['goods_id']])) 
    $sql = "insert into present_applicant (
      id,
      goods_id,
      customer_id,
      family_name,
      first_name,
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
        '0',
        '" . mysql_real_escape_string($pa['family_name']) . "',
        '" . mysql_real_escape_string($pa['first_name']) . "',
        '" . mysql_real_escape_string($pa['mail']) . "',
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
        '".mysql_real_escape_string($faqc['category'])."'
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
          '".mysql_real_escape_string($faqq['question'])."',
          '".mysql_real_escape_string($faqq['answer'])."'
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
cptable('wm_languages', 'languages');
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
$osquery = rq("select * from iimy_orders_mail");
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
/*
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
*/
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
        '',
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
  $sql   = "select * from ".table_prefix($s)."reviews where customers_id='0'";
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
          '" . mysql_real_escape_string($rd['reviews_text']) . "'
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
// categories_rss
cptable('categories_rss');

cptable('set_auto_calc');
cptable('set_comments');
cptable('set_dougyousya_categories');
cptable('set_dougyousya_history');
cptable('set_dougyousya_names');
cptable('set_menu_list');
cptable('set_oroshi_categories');
cptable('set_oroshi_datas');
cptable('set_oroshi_names');
cptable('set_products_dougyousya');

// clear configuration
foreach($delete_configuration as $c){
  r3q("delete from configuration where configuration_key='".$c."'");
}

file_put_contents('pg2pg', serialize($pg2pg));
