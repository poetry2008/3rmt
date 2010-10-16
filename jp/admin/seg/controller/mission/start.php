<?php
require_once('./config/config.inc.php');
require_once('./class/mission.php');
$id = $_GET['id'];
$m = mission::getObj($id);

$m->start();
