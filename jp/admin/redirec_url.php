<?php
  require('includes/application_top.php');
  if(isset($_GET['url'])&&$_GET['url']){
    echo "<a target='_blank' href='".urldecode($_GET['url'])."'>";
    echo urldecode($_GET['url']);
    echo "</a>";
  }
?>
