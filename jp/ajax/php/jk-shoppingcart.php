<?php
/*
  $Id: jk-shoppingcart.php,v 0.1 2006/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  //require('includes/application_top.php');
  echo number_format($cart->show_total());
?>