<?php
// iimy,gm,wm to 3rmt
// todo: mysql_escape

define('RMT_DB_HOST', 'localhost');
define('RMT_DB_USER', 'root');
define('RMT_DB_PASS', '123456');
define('RMT_DB_NAME', 'test_rmt');
//define('R3MT_DB_HOST', 'localhost');
//define('R3MT_DB_USER', 'root');
//define('R3MT_DB_PASS', '123456');
define('R3MT_DB_NAME', 'test_3rmt');

define('R3MT_JP_ID', '1');
define('R3MT_GM_ID', '2');
define('R3MT_WM_ID', '3');

echo "connect database\n";

$_link  = mysql_connect(RMT_DB_HOST, RMT_DB_USER, RMT_DB_PASS) or die('3');
mysql_query('set names utf8');

// * => site_id
// % => 原样copy
// ? => 清空表
// zones% & geo_zones% & zones_to_geo_zones%

r3q("truncate zones");
$sql   = "select * from wm_zones";
$query = rq($sql);
while($zone = mysql_fetch_array($query)) {
  $sql = "insert into `zones` (`zone_id`, `zone_country_id`, `zone_code`, `zone_name`) values ('" . $zone['zone_id'] . "', '" . $zone['zone_country_id'] . "', '" . $zone['zone_code'] . "', '" . $zone['zone_name'] . "')";
  r3q($sql);
}

r3q("truncate geo_zones");
$sql   = "select * from wm_geo_zones";
$query = rq($sql);
while($geo_zone = mysql_fetch_array($query)) {
  $sql = "insert into `geo_zones` (`geo_zone_id`, `geo_zone_name`, `geo_zone_description`, `last_modified`, `date_added`) values ('" . $geo_zone['geo_zone_id'] . "', '" . $geo_zone['geo_zone_name'] . "', '" . $geo_zone['geo_zone_description'] . "', '" . $geo_zone['last_modified'] . "', '" . $geo_zone['date_added'] . "')";
  r3q($sql);
}

r3q("truncate zones_to_geo_zones");
$sql   = "select * from wm_zones_to_geo_zones";
$query = rq($sql);

while($ztgz = mysql_fetch_array($query)) {
  $sql = "insert into `zones_to_geo_zones` (`association_id`, `zone_country_id`, `zone_id`, `geo_zone_id`, `last_modified`, `date_added`) values ('" . $ztgz['association_id'] . "', '" . $ztgz['zone_country_id'] . "', '" . $ztgz['zone_id'] . "', '" . $ztgz['geo_zone_id'] . "','" . $ztgz['last_modified'] . "','" . $ztgz['date_added'] . "')";
  r3q($sql);
}
p('geo_zones');
p('zones');
p('zones_to_geo_zones');
// configuration* & configuration_group

r3q("truncate configuration_group");
$sql   = "select * from wm_configuration_group";
$query = rq($sql);
while($group = mysql_fetch_array($query)) {
  $sql = "insert into `configuration_group` (
    `configuration_group_id`, 
    `configuration_group_title`, 
    `configuration_group_description`, 
    `sort_order`, 
    `visible`) values (
      '" . $group['configuration_group_id'] . "', 
      '" . $group['configuration_group_title'] . "', 
      '" . $group['configuration_group_description'] . "', 
      '" . $group['sort_order'] . "', 
      '" . $group['visible'] . "')";
  r3q($sql);
}
$sql   = "select * from wm_configuration_ds_group";
$query = rq($sql);
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
p('configuration_group');

r3q("truncate configuration");

$sql   = "select * from iimy_configuration";
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
    '" . $config['configuration_group_id'] . "',
    '" . $config['sort_order'] . "',
    '" . $config['last_modified'] . "',
    '" . $config['date_added'] . "',
    '" . $config['use_function'] . "',
    '" . mysql_real_escape_string($config['set_function']) . "',
    '" . R3MT_JP_ID . "')";
  r3q($sql);
}

$sql   = "select * from iimy_configuration_ds";
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
    '" . R3MT_JP_ID . "'
    )";
  r3q($sql);
}

$sql   = "select * from gm_configuration";
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
    '" . $config['configuration_group_id'] . "',
    '" . $config['sort_order'] . "',
    '" . $config['last_modified'] . "',
    '" . $config['date_added'] . "',
    '" . $config['use_function'] . "',
    '" . mysql_real_escape_string($config['set_function']) . "',
    '" . R3MT_GM_ID . "'
    )";
  r3q($sql);
}

$sql   = "select * from gm_configuration_ds";
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
    '" . R3MT_GM_ID . "'
    )";
  r3q($sql);
}

$sql   = "select * from wm_configuration";
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
    '" . $config['configuration_group_id'] . "',
    '" . $config['sort_order'] . "',
    '" . $config['last_modified'] . "',
    '" . $config['date_added'] . "',
    '" . $config['use_function'] . "',
    '" . mysql_real_escape_string($config['set_function']) . "',
    '" . R3MT_WM_ID . "'
    )";
  r3q($sql);
}


$sql   = "select * from wm_configuration_ds";
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
    '" . R3MT_WM_ID . "'
    )";
  r3q($sql);
}
p('configuration');

// address_book*
r3q('truncate address_book');
$sql   = "select * from iimy_address_book";
$query = rq($sql);
while($abook = mysql_fetch_array($query)) {
  $sql = "insert into address_book (
    `customers_id`,
    `address_book_id`,
    `entry_gender`,
    `entry_company`,
    `entry_firstname`,
    `entry_lastname`,
    `entry_firstname_f`,
    `entry_lastname_f`,
    `entry_street_address`,
    `entry_suburb`,
    `entry_postcode`,
    `entry_city`,
    `entry_state`,
    `entry_country_id`,
    `entry_telephone`,
    `entry_zone_id`,
    `site_id`
    ) values (
    '" . $abook['customers_id'] . "',
    '" . $abook['address_book_id'] . "',
    '" . $abook['entry_gender'] . "',
    '" . $abook['entry_company'] . "',
    '" . $abook['entry_firstname'] . "',
    '" . $abook['entry_lastname'] . "',
    '" . $abook['entry_firstname_f'] . "',
    '" . $abook['entry_lastname_f'] . "',
    '" . $abook['entry_street_address'] . "',
    '" . $abook['entry_suburb'] . "',
    '" . $abook['entry_postcode'] . "',
    '" . $abook['entry_city'] . "',
    '" . $abook['entry_state'] . "',
    '" . $abook['entry_country_id'] . "',
    '" . $abook['entry_telephone'] . "',
    '" . $abook['entry_zone_id'] . "',
    '" . R3MT_JP_ID . "'
    )";
  r3q($sql);
}
$sql   = "select * from gm_address_book";
$query = rq($sql);
while($abook = mysql_fetch_array($query)) {
  $sql = "insert into address_book (
    `customers_id`,
    `address_book_id`,
    `entry_gender`,
    `entry_company`,
    `entry_firstname`,
    `entry_lastname`,
    `entry_firstname_f`,
    `entry_lastname_f`,
    `entry_street_address`,
    `entry_suburb`,
    `entry_postcode`,
    `entry_city`,
    `entry_state`,
    `entry_country_id`,
    `entry_telephone`,
    `entry_zone_id`,
    `site_id`
    ) values (
    '" . $abook['customers_id'] . "',
    '" . $abook['address_book_id'] . "',
    '" . $abook['entry_gender'] . "',
    '" . $abook['entry_company'] . "',
    '" . $abook['entry_firstname'] . "',
    '" . $abook['entry_lastname'] . "',
    '" . $abook['entry_firstname_f'] . "',
    '" . $abook['entry_lastname_f'] . "',
    '" . $abook['entry_street_address'] . "',
    '" . $abook['entry_suburb'] . "',
    '" . $abook['entry_postcode'] . "',
    '" . $abook['entry_city'] . "',
    '" . $abook['entry_state'] . "',
    '" . $abook['entry_country_id'] . "',
    '" . $abook['entry_telephone'] . "',
    '" . $abook['entry_zone_id'] . "',
    '" . R3MT_GM_ID . "'
    )";
  r3q($sql);
}
$sql   = "select * from wm_address_book";
$query = rq($sql);
while($abook = mysql_fetch_array($query)) {
  $sql = "insert into address_book (
    `customers_id`,
    `address_book_id`,
    `entry_gender`,
    `entry_company`,
    `entry_firstname`,
    `entry_lastname`,
    `entry_firstname_f`,
    `entry_lastname_f`,
    `entry_street_address`,
    `entry_suburb`,
    `entry_postcode`,
    `entry_city`,
    `entry_state`,
    `entry_country_id`,
    `entry_telephone`,
    `entry_zone_id`,
    `site_id`
    ) values (
    '" . $abook['customers_id'] . "',
    '" . $abook['address_book_id'] . "',
    '" . $abook['entry_gender'] . "',
    '" . $abook['entry_company'] . "',
    '" . $abook['entry_firstname'] . "',
    '" . $abook['entry_lastname'] . "',
    '" . $abook['entry_firstname_f'] . "',
    '" . $abook['entry_lastname_f'] . "',
    '" . $abook['entry_street_address'] . "',
    '" . $abook['entry_suburb'] . "',
    '" . $abook['entry_postcode'] . "',
    '" . $abook['entry_city'] . "',
    '" . $abook['entry_state'] . "',
    '" . $abook['entry_country_id'] . "',
    '" . $abook['entry_telephone'] . "',
    '" . $abook['entry_zone_id'] . "',
    '" . R3MT_WM_ID . "'
    )";
  r3q($sql);
}

// address_format%
r3q("truncate address_format");
$sql   = "select * from wm_address_format";
$query = rq($sql);
while($aformat = mysql_fetch_array($query)) {
  $sql = "insert into `address_format` (
    `address_format_id`,
    `address_format`,
    `address_summary`
    ) values (
      '" . $aformat['address_format_id'] . "',
      '" . $aformat['address_format'] . "',
      '" . $aformat['address_summary'] . "'
    )";
  r3q($sql);
}
p("address_format");

// banners*
/*
r3q("truncate banners");
$sql = 'select * from iimy_banners';
$query = rq($sql);
while($banner = mysql_fetch_array($query)){
  $sql = "insert into `banners` (
      `banners_id`,
      `banners_title`,
      `banners_url`,
      `banners_image`,
      `banners_group`,
      `banners_html_text`,
      `expires_impressions`,
      `expires_date`,
      `date_scheduled`,
      `date_added`,
      `date_status_change`,
      `status`,
      `site_id`
    ) values (
      '" . $banner['banners_id'] . "',
      '" . $banner['banners_title'] . "',
      '" . $banner['banners_url'] . "',
      '" . $banner['banners_image'] . "',
      '" . $banner['banners_group'] . "',
      '" . $banner['banners_html_text'] . "',
      '" . $banner['expires_impressions'] . "',
      '" . $banner['expires_date'] . "',
      '" . $banner['date_scheduled'] . "',
      '" . $banner['date_added'] . "',
      '" . $banner['date_status_change'] . "',
      '" . $banner['status'] . "',
      '" . R3MT_JP_ID . "'
    )";
  r3q($sql);
}
$sql = 'select * from gm_banners';
$query = rq($sql);
while($banner = mysql_fetch_array($query)){
  $sql = "insert into `banners` (
      `banners_id`,
      `banners_title`,
      `banners_url`,
      `banners_image`,
      `banners_group`,
      `banners_html_text`,
      `expires_impressions`,
      `expires_date`,
      `date_scheduled`,
      `date_added`,
      `date_status_change`,
      `status`,
      `site_id`
    ) values (
      '" . $banner['banners_id'] . "',
      '" . $banner['banners_title'] . "',
      '" . $banner['banners_url'] . "',
      '" . $banner['banners_image'] . "',
      '" . $banner['banners_group'] . "',
      '" . $banner['banners_html_text'] . "',
      '" . $banner['expires_impressions'] . "',
      '" . $banner['expires_date'] . "',
      '" . $banner['date_scheduled'] . "',
      '" . $banner['date_added'] . "',
      '" . $banner['date_status_change'] . "',
      '" . $banner['status'] . "',
      '" . R3MT_GM_ID . "'
    )";
  r3q($sql);
}
$sql = 'select * from wm_banners';
$query = rq($sql);
while($banner = mysql_fetch_array($query)){
  $sql = "insert into `banners` (
      `banners_id`,
      `banners_title`,
      `banners_url`,
      `banners_image`,
      `banners_group`,
      `banners_html_text`,
      `expires_impressions`,
      `expires_date`,
      `date_scheduled`,
      `date_added`,
      `date_status_change`,
      `status`,
      `site_id`
    ) values (
      '" . $banner['banners_id'] . "',
      '" . $banner['banners_title'] . "',
      '" . $banner['banners_url'] . "',
      '" . $banner['banners_image'] . "',
      '" . $banner['banners_group'] . "',
      '" . $banner['banners_html_text'] . "',
      '" . $banner['expires_impressions'] . "',
      '" . $banner['expires_date'] . "',
      '" . $banner['date_scheduled'] . "',
      '" . $banner['date_added'] . "',
      '" . $banner['date_status_change'] . "',
      '" . $banner['status'] . "',
      '" . R3MT_WM_ID . "'
    )";
  r3q($sql);
}
p("banners");
*/

// cache+

// calendar*
r3q("truncate calendar");
$sql = 'select * from iimy_calendar';
$query = rq($sql);
while($cal = mysql_fetch_array($query)){
  $sql = "insert into `calendar` (
    `cl_id`,
    `cl_ym`,
    `cl_value`,
    `site_id`
    ) values (
      NULL,
      '" . $cal['cl_ym'] . "',
      '" . $cal['cl_value'] . "',
      '" . R3MT_JP_ID . "'
    )";
  r3q($sql);
}
$sql = 'select * from gm_calendar';
$query = rq($sql);
while($cal = mysql_fetch_array($query)){
  $sql = "insert into `calendar` (
    `cl_id`,
    `cl_ym`,
    `cl_value`,
    `site_id`
    ) values (
      NULL,
      '" . $cal['cl_ym'] . "',
      '" . $cal['cl_value'] . "',
      '" . R3MT_GM_ID . "'
    )";
  r3q($sql);
}
$sql = 'select * from wm_calendar';
$query = rq($sql);
while($cal = mysql_fetch_array($query)){
  $sql = "insert into `calendar` (
    `cl_id`,
    `cl_ym`,
    `cl_value`,
    `site_id`
    ) values (
      NULL,
      '" . $cal['cl_ym'] . "',
      '" . $cal['cl_value'] . "',
      '" . R3MT_WM_ID . "'
    )";
  r3q($sql);
}

// categories% & categories_description* & products% & products_description* 
// products_to_categories & products_attributes & products_attributes_download & products_notifications 
r3q("truncate products_notifications");
$sql = 'select * from products_notifications';
$query = rq($sql);
while($pn = mysql_fetch_array($query)){
  $sql = "insert into `products_notifications` (
      `products_id`,
      `customers_id`,
      `date_added`
    ) values (
      '" . $pn['products_id'] . "',
      '" . $pn['customers_id'] . "',
      '" . $pn['date_added'] . "'
    )";
  r3q($sql);
}
p('products_notifications?');

r3q("truncate products_to_categories");
$sql = 'select * from products_to_categories';
$query = rq($sql);
while($ptc = mysql_fetch_array($query)){
  $sql = "insert into `products_to_categories` (
      `products_id`,
      `categories_id`
    ) values (
      '" . $ptc['products_id'] . "',
      '" . $ptc['categories_id'] . "'
    )";
  r3q($sql);
}
p('products_to_categories');

r3q("truncate products_attributes");
$sql = 'select * from products_attributes';
$query = rq($sql);
while($pa = mysql_fetch_array($query)){
  $sql = "insert into `products_attributes` (
      `products_attributes_id`,
      `products_id`,
      `options_id`,
      `products_values_id`,
      `products_values_price`,
      `price_prefix`,
      `products_at_quantity`
    ) values (
      '" . $pa['products_attributes_id'] . "',
      '" . $pa['products_id'] . "',
      '" . $pa['options_id'] . "',
      '" . $pa['proeucts_values_id'] . "',
      '" . $pa['products_values_price'] . "',
      '" . $pa['price_prefix'] . "',
      '" . $pa['products_at_quantity'] . "'
    )";
  r3q($sql);
}
p('products_attributes');

r3q("truncate products_attributes_download");
$sql = 'select * from products_attributes_download';
$query = rq($sql);
while($pad = mysql_fetch_array($query)){
  $sql = "insert into `products_attributes_download` (
      `products_attributes_id`,
      `products_attributes_filename`,
      `products_attributes_maxdays`,
      `products_attributes_maxcount`
    ) values (
      '" . $pad ['products_attributes_id'] . "',
      '" . $pad ['products_attributes_filename'] . "',
      '" . $pad ['products_attributes_maxdays'] . "',
      '" . $pad ['categories_attributes_maxcount'] . "'
    )";
  r3q($sql);
}
p('products_attributes_download');

r3q("truncate categories");
$sql = 'select * from categories';
$query = rq($sql);
while($categories = mysql_fetch_array($query)){
  $sql = "insert into `categories` (
    `categories_id`, 
    `categories_status`, 
    `categories_image`, 
    `parent_id`, 
    `sort_order`, 
    `date_added`, 
    `last_modified`) values (
    '" . $categories['categories_id'] . "',
    '" . $categories['categories_status'] . "',
    '" . $categories['categories_image'] . "',
    '" . $categories['parent_id'] . "',
    '" . $categories['sort_order'] . "',
    '" . $categories['date_added'] . "',
    '" . $categories['last_modified'] . "')";
  r3q($sql);
}
p('categories');

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
p('categories_description');

r3q("truncate products");
$sql = 'select * from products';
$query = rq($sql);
while($product = mysql_fetch_array($query)){
  $sql = "insert into `products` (
    `products_id`, 
    `products_quantity`, 
    `products_model`, 
    `products_image`, 
    `products_image2`, 
    `products_image3`, 
    `products_price`, 
    `products_date_added`, 
    `products_last_modified`, 
    `products_date_available`, 
    `products_weight`, 
    `products_status`, 
    `products_tax_class_id`, 
    `manufacturers_id`, 
    `products_ordered`, 
    `products_bflag`, 
    `products_cflag`, 
    `products_small_sum`, 
    `option_type`) values (
    '" . $product['products_id'] . "',
    '" . $product['products_quantity'] . "',
    '" . $product['products_model'] . "',
    '" . $product['products_image'] . "',
    '" . $product['products_image2'] . "',
    '" . $product['products_image3'] . "',
    '" . $product['products_price'] . "',
    '" . $product['products_date_added'] . "',
    '" . $product['products_last_modified'] . "',
    '" . $product['products_date_available'] . "',
    '" . $product['products_weight'] . "',
    '" . $product['products_status'] . "',
    '" . $product['products_tax_class_id'] . "',
    '" . $product['manufacturers_id'] . "',
    '" . $product['products_ordered'] . "',
    '" . $product['products_bflag'] . "',
    '" . $product['products_cflag'] . "',
    '" . $product['products_small_sum'] . "',
    '" . $product['option_type'] . "')";
  r3q($sql);
}
r3q("truncate products_description");
$sql = 'select * from products_description';
$query = rq($sql);
while($pd = mysql_fetch_array($query)){
  $sql = "insert into `products_description` (
    `products_id`,
    `languages_id`,
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
        '" . $pd['languages_id'] . "',
        '" . $pd['products_name'] . "',
        '" . $pd['products_description'] . "',
        '" . R3MT_JP_ID . "',
        '" . $pd['products_attention_1'] . "',
        '" . $pd['products_attention_2'] . "',
        '" . $pd['products_attention_3'] . "',
        '" . $pd['products_attention_4'] . "',
        '" . $pd['products_attention_5'] . "',
        '" . $pd['products_url'] . "',
        '" . $pd['products_viewed'] . "'
      ), (
        '" . $pd['products_id'] . "',
        '" . $pd['languages_id'] . "',
        '" . $pd['products_name'] . "',
        '" . $pd['products_description_gm'] . "',
        '" . R3MT_GM_ID . "',
        '" . $pd['products_attention_1'] . "',
        '" . $pd['products_attention_2'] . "',
        '" . $pd['products_attention_3'] . "',
        '" . $pd['products_attention_4'] . "',
        '" . $pd['products_attention_5'] . "',
        '" . $pd['products_url'] . "',
        '" . $pd['products_viewed'] . "'
      ), (
        '" . $pd['products_id'] . "',
        '" . $pd['languages_id'] . "',
        '" . $pd['products_name'] . "',
        '" . $pd['products_description_wm'] . "',
        '" . R3MT_WM_ID . "',
        '" . $pd['products_attention_1'] . "',
        '" . $pd['products_attention_2'] . "',
        '" . $pd['products_attention_3'] . "',
        '" . $pd['products_attention_4'] . "',
        '" . $pd['products_attention_5'] . "',
        '" . $pd['products_url'] . "',
        '" . $pd['products_viewed'] . "'
      )";
}
p('products_description');
// products_options & products_values & products_options_values_to_products

// color* & color_to_products

// contents*

// currencies%
r3q("truncate currencies");
$sql   = "select * from wm_currencies";
$query = rq($sql);
while($curr = mysql_fetch_array($query)) {
  $sql = "insert into `currencies` (
    `currencies_id`, 
    `title`, 
    `code`, 
    `symbol_left`, 
    `symbol_right`, 
    `decimal_point`, 
    `thousands_point`, 
    `decimal_places`, 
    `value`, 
    `last_updated` 
      ) values (
    '" . $curr['currencies_id'] . "', 
    '" . $curr['title'] . "', 
    '" . $curr['code'] . "', 
    '" . $curr['symbol_left'] . "', 
    '" . $curr['symbol_right'] . "', 
    '" . $curr['decimal_point'] . "', 
    '" . $curr['thousands_point'] . "', 
    '" . $curr['decimal_places'] . "', 
    '" . $curr['value'] . "', 
    '" . $curr['last_updated'] . "' 
    )";
  r3q($sql);
}
p('currencies');

// customers* & customers_basket+ & customers_basket_attributes+ & customers_info

// faq_categories & faq_questions

// image_documents? & image_document_types? & products_to_images_documents?

// information_page
r3q("truncate information_page");
$sql   = "select * from iimy_information_page";
$query = rq($sql);
while($info = mysql_fetch_array($query)) {
  $sql = "insert into `information_page` (
    `pID`, 
    `navbar_title`, 
    `heading_title`, 
    `text_information`, 
    `status`, 
    `sort_id`, 
    `romaji`, 
    `site_id` 
      ) values (
    NULL, 
    '" . mysql_real_escape_string($info['navbar_title']) . "', 
    '" . mysql_real_escape_string($info['heading_title']) . "', 
    '" . mysql_real_escape_string($info['text_information']) . "', 
    '" . $info['status'] . "', 
    '" . $info['sort_id'] . "', 
    '" . $info['romaji'] . "', 
    '" . R3MT_JP_ID . "' 
    )";
  r3q($sql);
}
$sql   = "select * from gm_information_page";
$query = rq($sql);
while($info = mysql_fetch_array($query)) {
  $sql = "insert into `information_page` (
    `pID`, 
    `navbar_title`, 
    `heading_title`, 
    `text_information`, 
    `status`, 
    `sort_id`, 
    `romaji`, 
    `site_id` 
      ) values (
    NULL, 
    '" . mysql_real_escape_string($info['navbar_title']) . "', 
    '" . mysql_real_escape_string($info['heading_title']) . "', 
    '" . mysql_real_escape_string($info['text_information']) . "', 
    '" . $info['status'] . "', 
    '" . $info['sort_id'] . "', 
    '" . $info['romaji'] . "', 
    '" . R3MT_GM_ID . "' 
    )";
  r3q($sql);
}
$sql   = "select * from wm_information_page";
$query = rq($sql);
while($info = mysql_fetch_array($query)) {
  $sql = "insert into `information_page` (
    `pID`, 
    `navbar_title`, 
    `heading_title`, 
    `text_information`, 
    `status`, 
    `sort_id`, 
    `romaji`, 
    `site_id` 
      ) values (
    NULL, 
    '" . mysql_real_escape_string($info['navbar_title']) . "', 
    '" . mysql_real_escape_string($info['heading_title']) . "', 
    '" . mysql_real_escape_string($info['text_information']) . "', 
    '" . $info['status'] . "', 
    '" . $info['sort_id'] . "', 
    '" . $info['romaji'] . "', 
    '" . R3MT_WM_ID . "' 
    )";
  r3q($sql);
}
p('information_page');

// languages%
r3q("truncate languages");
$sql   = "select * from wm_languages";
$query = rq($sql);
while($l = mysql_fetch_array($query)) {
  $sql = "insert into `languages` (
    `languages_id`, 
    `name`, 
    `code`, 
    `image`, 
    `directory`, 
    `sort_order`
      ) values (
    '" . $l['languages_id'] . "', 
    '" . $l['name'] . "', 
    '" . $l['code'] . "', 
    '" . $l['image'] . "', 
    '" . $l['directory'] . "', 
    '" . $l['sort_order'] . "'
    )";
  r3q($sql);
}
p('languages');

// latest_news*
r3q("truncate latest_news");
$sql   = "select * from iimy_latest_news";
$query = rq($sql);
while($ln = mysql_fetch_array($query)) {
  $sql = "insert into `latest_news` (
      `news_id`,
      `headline`,
      `content`,
      `date_added`,
      `status`,
      `news_image`,
      `news_image_description`,
      `site_id`,
      `isfirst`
    ) values (
      '" . $ln['news_id'] . "',
      '" . $ln['headline'] . "',
      '" . $ln['content'] . "',
      '" . $ln['date_added'] . "',
      '" . $ln['status'] . "',
      '" . $ln['news_image'] . "',
      '" . $ln['news_image_description'] . "',
      '" . R3MT_JP_ID . "',
      '" . $ln['isfirst'] . "',
    )";
  r3q($sql);
}
p('latest_news');
$sql   = "select * from gm_latest_news";
$query = rq($sql);
while($ln = mysql_fetch_array($query)) {
  $sql = "insert into `latest_news` (
      `news_id`,
      `headline`,
      `content`,
      `date_added`,
      `status`,
      `news_image`,
      `news_image_description`,
      `site_id`,
      `isfirst`
    ) values (
      '" . $ln['news_id'] . "',
      '" . $ln['headline'] . "',
      '" . $ln['content'] . "',
      '" . $ln['date_added'] . "',
      '" . $ln['status'] . "',
      '" . $ln['news_image'] . "',
      '" . $ln['news_image_description'] . "',
      '" . R3MT_GM_ID . "',
      '" . $ln['isfirst'] . "',
    )";
  r3q($sql);
}
p('latest_news');
$sql   = "select * from iimy_latest_news";
$query = rq($sql);
while($ln = mysql_fetch_array($query)) {
  $sql = "insert into `latest_news` (
      `news_id`,
      `headline`,
      `content`,
      `date_added`,
      `status`,
      `news_image`,
      `news_image_description`,
      `site_id`,
      `isfirst`
    ) values (
      '" . $ln['news_id'] . "',
      '" . $ln['headline'] . "',
      '" . $ln['content'] . "',
      '" . $ln['date_added'] . "',
      '" . $ln['status'] . "',
      '" . $ln['news_image'] . "',
      '" . $ln['news_image_description'] . "',
      '" . R3MT_WM_ID . "',
      '" . $ln['isfirst'] . "',
    )";
  r3q($sql);
}
p('latest_news');

// login?

// mail_magazine*

// manufacturers% & manufacturers_info%

// newsletters*

// orders* & orders_products & orders_products_attributes & orders_products_download & orders_status_history

// orders_status_id

// orders_mail*?

// permissions%
r3q("truncate permissions");
$sql   = "select * from permissions";
$query = rq($sql);
while($p = mysql_fetch_array($query)) {
  $sql = "insert into `permissions` (
    `userid`, 
    `permission` 
      ) values (
    '" . $p['userid'] . "', 
    '" . $p['permission'] . "' 
    )";
  r3q($sql);
}
p('permission');

// present_applicant & present_goods

// tags% & products_to_tags%

r3q("truncate tags");
$sql   = "select * from tags";
$query = rq($sql);
while($tag = mysql_fetch_array($query)) {
  $sql = "insert into `tags` (
    `tags_id`,
    `tags_name`,
    `tags_images`,
    `tags_checked`,
    `tags_order`
    ) values (
      '" . $tag['tags_id'] . "',
      '" . $tag['tags_name'] . "',
      '" . $tag['tags_images'] . "',
      '" . $tag['tags_checked'] . "',
      '" . $tag['tags_order'] . "'
    )";
  r3q($sql);
}
p('tags');
r3q("truncate products_to_tags");
$sql   = "select * from products_to_tags";
$query = rq($sql);
while($ptt = mysql_fetch_array($query)) {
  $sql = "insert into `products_to_tags` (
    `products_id`,
    `tags_id`
    ) values (
      '" . $tag['products_id'] . "',
      '" . $tag['tags_id'] . "'
    )";
  r3q($sql);
}
p('product_to_tags');

// reviews* & reviews_description
r3q("truncate reviews");
$sql   = "select * from iimy_reviews";
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
      '" . R3MT_JP_ID . "',
      '" . $re['reviews_status'] . "'
    )";
  r3q($sql);
  $rid = mysql_insert_id();
  $sql = "select * from iimy_reviews_description where reviews_id=".$re['reviews_id'];
  $rdquery = mysql_query($sql);
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
$sql   = "select * from gm_reviews";
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
      '" . R3MT_GM_ID . "',
      '" . $re['reviews_status'] . "'
    )";
  r3q($sql);
  $rid = mysql_insert_id();
  $sql = "select * from iimy_reviews_description where reviews_id=".$re['reviews_id'];
  $rdquery = mysql_query($sql);
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
$sql   = "select * from wm_reviews";
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
      '" . R3MT_WM_ID . "',
      '" . $re['reviews_status'] . "'
    )";
  r3q($sql);
  $rid = mysql_insert_id();
  $sql = "select * from iimy_reviews_description where reviews_id=".$re['reviews_id'];
  $rdquery = mysql_query($sql);
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
p('reviews');

// session+

// sites
r3q("truncate sites");
r3q("insert into `sites` (id, romaji) values ('1','jp'),('2','gm'),('3','wm')");
p("sites");

// specials
r3q("truncate specials");
$sql   = "select * from specials";
$query = rq($sql);
while($special = mysql_fetch_array($query)) {
  $sql = "insert into `specials` (
    `specials_id`, 
    `products_id`, 
    `specials_new_products_price`, 
    `specials_date_added`, 
    `specials_last_modified`, 
    `expires_date`, 
    `date_status_change`, 
    `status` 
      ) values (
    '" . $special['specials_id'] . "', 
    '" . $special['products_id'] . "', 
    '" . $special['specials_new_products_price'] . "', 
    '" . $special['specials_date_added'] . "', 
    '" . $special['specials_last_modified'] . "', 
    '" . $special['expires_date'] . "', 
    '" . $special['date_status_change'] . "', 
    '" . $special['status'] . "' 
    )";
}
p('specials');

// sql_log+

// tax_class & tax_rates
r3q("truncate tax_class");
$sql   = "select * from wm_tax_class";
$query = rq($sql);
while($tclass = mysql_fetch_array($query)) {
  $sql = "insert into `tax_class` (
    `tax_class_id`,
    `tax_class_title`,
    `tax_class_description`,
    `last_modified`,
    `date_added`
    ) values (
      '" . $tclass['tax_class_id'] . "',
      '" . $tclass['tax_class_title'] . "',
      '" . $tclass['tax_class_description'] . "',
      '" . $tclass['last_modified'] . "',
      '" . $tclass['date_added'] . "'
    )";
  r3q($sql);
}
p('tax_class');
r3q("truncate tax_rates");
$sql   = "select * from wm_tax_rates";
$query = rq($sql);
while($trate = mysql_fetch_array($query)) {
  $sql = "insert into `tax_rates` (
    `tax_rates_id`,
    `tax_zone_id`,
    `tax_class_id`,
    `tax_priority`,
    `tax_rate`,
    `tax_description`,
    `last_modified`,
    `date_added`
    ) values (
      '" . $trate['tax_rates_id'] . "',
      '" . $trate['tax_zone_id'] . "',
      '" . $trate['tax_class_id'] . "',
      '" . $trate['tax_priority'] . "',
      '" . $trate['tax_rate'] . "',
      '" . $trate['tax_description'] . "',
      '" . $trate['last_modified'] . "',
      '" . $trate['date_added'] . "'
    )";
  r3q($sql);
}
p('tax_rates');


// users%
r3q("truncate users");
$sql   = "select * from users";
$query = rq($sql);
while($u = mysql_fetch_array($query)) {
  $sql = "insert into `users` (
    `userid`, 
    `password`, 
    `name`, 
    `email` 
      ) values (
    '" . $u['userid'] . "', 
    '" . $u['password'] . "', 
    '" . $u['name'] . "', 
    '" . $u['email'] . "' 
    )";
  r3q($sql);
}
p('users');

// whos_online+

// functions
function i($str) {
  return iconv('euc-jp', 'utf-8', $str);
}

function p($table){
  echo $table . "\t\t\tDone\n";
}

function rq($sql){
  mysql_select_db(RMT_DB_NAME);
  $q = mysql_query($sql);
  $e = mysql_error();
  if($e){
    echo $e . "\n";
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
