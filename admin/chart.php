<?php
header("content-type:image/png");

$url = "http://chart.apis.google.com/chart?".$_SERVER['QUERY_STRING'];
echo file_get_contents($url);