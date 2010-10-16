<?php
require_once('./config/config.inc.php');
require_once('./class/mission.php');
$id = $_GET['mission_id'];
$m = mission::getObj($id);
$m->stop();
header("Location: ".$_SERVER['SCRIPT_NAME']);
