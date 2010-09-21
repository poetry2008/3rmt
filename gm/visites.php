<?php
require('includes/application_top.php');
$_SESSION['screenResolution']   = $_GET['res'];
$_SESSION['colorDepth']         = $_GET['col'];
$_SESSION['flashEnable']        = $_GET['flash'];
$_SESSION['flashVersion']       = $_GET['flashversion'];
$_SESSION['directorEnable']     = $_GET['director'];
$_SESSION['quicktimeEnable']    = $_GET['quicktime'];
$_SESSION['realPlayerEnable']   = $_GET['realplayer'];
$_SESSION['windowsMediaEnable'] = $_GET['windowsmedia'];
//$cookieAccept = $_GET['cookie', 0, 'numeric');
$_SESSION['pdfEnable']          = $_GET['pdf'];
$_SESSION['javaEnable']         = $_GET['java'];
//print_r($_GET);

/*
orders_screen_resolution 
orders_color_depth 
orders_flash_enable
orders_flash_version
orders_director_enable
orders_quicktime_enable
orders_realplayer_enable
orders_windows_media_enable
orders_pdf_enable
orders_java_enable
*/