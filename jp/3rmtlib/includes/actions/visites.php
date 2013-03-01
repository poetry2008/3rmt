<?php
/*
  $Id$
*/
//分辨率
$_SESSION['screenResolution']   = $_GET['res'];
//颜色
$_SESSION['colorDepth']         = $_GET['col'];
//是否支持flash
$_SESSION['flashEnable']        = $_GET['flash'];
//flash版本
$_SESSION['flashVersion']       = $_GET['flashversion'];
//是否支持director
$_SESSION['directorEnable']     = $_GET['director'];
//是否支持quicktime
$_SESSION['quicktimeEnable']    = $_GET['quicktime'];
//是否支持realPlayer
$_SESSION['realPlayerEnable']   = $_GET['realplayer'];
//是否支持windowsMedia
$_SESSION['windowsMediaEnable'] = $_GET['windowsmedia'];
//是否支持pdf
$_SESSION['pdfEnable']          = $_GET['pdf'];
//是否支持java
$_SESSION['javaEnable']         = $_GET['java'];
//系统语言
$_SESSION['systemLanguage']     = $_GET['systemlanguage'];
//用户语言
$_SESSION['userLanguage']       = $_GET['userlanguage'];
