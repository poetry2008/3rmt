<?php
/*********************************************************************
    logout.php

    Destroy clients session.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/

$_noemailclass = true;
require_once('includes/application_top.php');
require_once('includes/ost/client.inc.php');
//We are checking to make sure the user is logged in before a logout to avoid session reset tricks on excess logins
$_SESSION['_client']=array();
session_unset();
session_destroy();
header('Location: contact_us.php');
require('contact_us.php');
?>
