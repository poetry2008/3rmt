<?php
/*
 * confirm session error
 */
require('includes/application_top.php');
if(!isset($_SESSION['cart']) || !isset($_SESSION['date']) || !isset($_SESSION['hour']) || !isset($_SESSION['min'])){
//if(!isset($_SESSION['character']) || !isset($_SESSION['torihikihouhou']) || !isset($_SESSION['date']) || !isset($_SESSION['hour']) || !isset($_SESSION['min'])){
  echo 'error';
}
?>
