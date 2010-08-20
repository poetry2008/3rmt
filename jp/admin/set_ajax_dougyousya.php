<?php
require('includes/application_top.php');

if ($_GET['dougyousya_id'] && $_GET['products_id']) {
  update_products_dougyousya(intval($_GET['products_id']), intval($_GET['dougyousya_id']));
  //tep_db_perform('set_products_dougyousya', array('dougyousya_id' => intval($_GET['dougyousya_id'])), 'update', "product_id='".intval($_GET['products_id'])."'");
  echo 'success';
} else {
  echo 'failed';
}