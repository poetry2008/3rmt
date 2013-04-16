<?php
// ajax_create_order

require('includes/application_top.php');

if(isset($_GET['action']) && $_GET['action'] == 'search_email'){
  $json_array = array();
  $customers_query = tep_db_query("select customers_email_address from ". TABLE_CUSTOMERS ." where  customers_email_address like '%".tep_replace_full_character($_GET['q'])."%' order by customers_email_address asc");
  while($customers_array = tep_db_fetch_array($customers_query)){

    $json_array[] = array('name'=>$customers_array['customers_email_address']);
  }
  tep_db_free_result($customers_query);

  echo json_encode($json_array);
}else if(isset($_GET['action']) && $_GET['action'] == 'search_product_name'){
  $json_array = array();
  $reviews_query_raw = " select r.reviews_id, r.products_id, r.date_added, r.last_modified, r.user_added, r.user_update, r.reviews_rating, r.reviews_status , s.romaji, s.name as site_name, pd.products_name from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s, ".TABLE_PRODUCTS_DESCRIPTION." pd where r.site_id = s.id and r.products_id = pd.products_id and pd.language_id = '".$languages_id."' and pd.site_id = 0 " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" .  intval($_GET['site_id']) . "' " : '') . " and pd.products_name like '%".tep_replace_full_character($_GET['q'])."%' order by date_added DESC";
 $reviews_query = tep_db_query($reviews_query_raw);
while($review_array = tep_db_fetch_array($reviews_query)){
    $json_array[] = array('name'=>$review_array['products_name']);
  }
  tep_db_free_result($reviews_query);
  echo json_encode($json_array);
}
?>
