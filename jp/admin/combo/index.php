<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<?php
$i = 0;
if (!empty($_GET)) {
  foreach ($_GET as $key => $value) {
    $js_str = substr($key, -3);
    $css_str = substr($key, -4);
    if (($i == 0) && ($css_str == '_css')) {
      header('Content-Type:text/css; charset=utf-8'); 
    }
    if (($i == 0) && ($js_str == '_js')) {
      header('Content-Type:application/javascript; charset=utf-8'); 
    }
    if ($js_str == '_js') {
      echo file_get_contents('../includes/3.4.1'.substr($key, 5, -3).'.js');
    } else if ($css_str == '_css') {
      echo file_get_contents('../includes/3.4.1'.substr($key, 5, -4).'.css');
    }
?>
<?php
    $i++; 
  }
}
?>
