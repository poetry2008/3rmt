<?php
require_once('./class/mission.php');
require_once('./class/session.php');
require_once('./class/record.php');
if(isset($_GET['mission_id'])&&$_GET['mission_id']!=''){
  $mid = $_GET['mission_id'];
  $mission = mission::getConn();
  $session = new session();
  $record = new record();
  $mission->delete($mid);
  $session->deleteByMissionId($mid);
  $record->deleteByMissionId($mid);
}
header("Location: ".$_SERVER['SCRIPT_NAME']);
exit;
