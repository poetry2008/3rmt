<?php
require_once ('class/mission.php');

$y = new mission();
$y->init('bobhero');
$y->setDbDriver('resultSaver');
$y->start();
