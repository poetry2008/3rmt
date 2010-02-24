<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
if ($ocertify->npermission >= 10) {
	require(DIR_WS_BOXES . 'configuration.php');
	require(DIR_WS_BOXES . 'catalog.php');
	require(DIR_WS_BOXES . 'modules.php');
	require(DIR_WS_BOXES . 'customers.php');
	// require(DIR_WS_BOXES . 'taxes.php');
	require(DIR_WS_BOXES . 'localization.php');
	require(DIR_WS_BOXES . 'reports.php');
	require(DIR_WS_BOXES . 'tools.php');
	require(DIR_WS_BOXES . 'users.php');
}
?>
