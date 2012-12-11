<?php
/*
    $Id$
*/

$_noemailclass = true;
require_once('includes/application_top.php');
require_once('includes/ost/client.inc.php');
//We are checking to make sure the user is logged in before a logout to avoid session reset tricks on excess logins
$_SESSION['_client']=array();
session_unset();
session_destroy();
header('Location: contact_us.php');
require('contact_us.php');

