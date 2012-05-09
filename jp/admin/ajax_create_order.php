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
}
?>
