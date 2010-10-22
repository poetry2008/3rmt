<?php
/*********************************************************************
    secure.inc.php

    File included on every client's "secure" pages

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id$
**********************************************************************/
if(!strcasecmp(basename($_SERVER['SCRIPT_NAME']),basename(__FILE__))) die('Kwaheri rafiki!');
//if(!file_exists(DIR_OST.'client.inc.php')) die('Fatal Error.');
require_once(DIR_OST.'client.inc.php');
//User must be logged in!


if(!$thisclient || !$thisclient->getId() || !$thisclient->isValid()){
  require_once('./contact_us_login.php');
  exit;
}
$thisclient->refreshSession();
?>
