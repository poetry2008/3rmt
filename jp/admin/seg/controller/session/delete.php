<?php
require_once('./class/record.php');
require_once('./class/session.php');
$session_id = $_GET['session_id'];
$session = new session();
$record = new record();
$session->delete($session_id);
$record->deleteBySessionId($session_id);
header("Location: /index.php?action=index&controller=session&mission_id=".$mission_id);
exit;
