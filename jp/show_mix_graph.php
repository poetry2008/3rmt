<?php
/*
  $Id$

*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES.'graphbar.php'); 
  $graph_axis_info = tep_get_graph_axis_info(); 
  $graph_axis_array = array();
  foreach ($graph_axis_info as $key => $value) {
    $graph_axis_array[] = $value[1]; 
  }
  
  $graph_value_info = tep_get_graph_value_info(); 
  $bar = new Bar($_GET['width'], $_GET['height'], $graph_value_info, $graph_axis_array);
  $bar->stroke();
?>
