<?php
require_once('./class/session.php');
$id= $_GET['id'];
$session = new session();
$session->stop($id);
header("Location: /index.php?action=index&controller=session");
