<?php
/*
  $Id: stats_sales_report2.php,v 1.00 2003/03/08 19:02:22 Exp $

  Charly Wilhelm  charly@yoshi.ch
  
  Released under the GNU General Public License

  Copyright (c) 2003 osCommerce
  
  possible views (srView):
  1 yearly
  2 monthly
  3 weekly
  4 daily
  
  possible options (srDetail):
  0 no detail
  1 show details (products)
  2 show details only (products)
  
  export
  0 normal view
  1 csv
  
  sort
  0 no sorting
  1 product description asc
  2 product description desc
  3 #product asc, product descr asc
  4 #product desc, product descr desc
  5 revenue asc, product descr asc
  6 revenue desc, product descr desc

  compare
  0 no compare
  1 compare with the values a month ago
  2 compare with the values a week ago
  3 compare with the values a month ago
  4 compare with the values a year ago
  
*/

  // set the default values

  // default detail no detail
  $srDefaultDetail = 0;
  // default view (daily)
  $srDefaultView = 2;
  // default export
  $srDefaultExp = 0;
  // default sort
  $srDefaultSort = 4;
  // default max
  $srDefaultMax = 0;
  // default status
  $srDefaultStatus = 0;
  // default compare
  $srDefaultCompare = 0;

  define('TEMPLATE_DEFAULT', 'includes/sales_report/template_default.php');
  define('TEMPLATE_CSV', 'includes/sales_report/template_csv.php');

// it is not necessary to edit below this line  
//--------------------------------------------------------

  // define the constants
  define('SR_VIEW_YEARLY', '1');
  define('SR_VIEW_MONTHLY', '2');
  define('SR_VIEW_WEEKLY', '3');
  define('SR_VIEW_DAILY', '4');

  define('SR_DETAIL_NO', '0');
  define('SR_DETAIL_WITH', '1');
  define('SR_DETAIL_EXT', '2');

  define('SR_EXPORT_NO', '0');
  define('SR_EXPORT_CSV', '1');

  define('SR_SORT_NO', '0');
  define('SR_SORT_PROD_ASC', '1');
  define('SR_SORT_PROD_DESC', '2');
  define('SR_SORT_PROD_AMOUNT_ASC', '3');
  define('SR_SORT_PROD_AMOUNT_DESC', '4');
  define('SR_SORT_REVENUE_ASC', '5');
  define('SR_SORT_REVENUE_DESC', '6');

  define('SR_COMPARE_NO', '0');
  define('SR_COMPARE_DAY', '1');
  define('SR_COMPARE_MONTH', '2');
  define('SR_COMPARE_YEAR', '3');

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  // report views (1: yearly 2: monthly 3: weekly 4: daily)
  if ( ($HTTP_GET_VARS['report']) && (tep_not_null($HTTP_GET_VARS['report'])) ) 
{    $srView = $HTTP_GET_VARS['report'];
  }
  if ($srView < SR_VIEW_YEARLY || $srView > SR_VIEW_DAILY) {
    $srView = $srDefaultView;
  }

  // detail
  if ( ($HTTP_GET_VARS['detail']) && (tep_not_null($HTTP_GET_VARS['detail'])) ) 
{    $srDetail = $HTTP_GET_VARS['detail'];
  }
  if ($srDetail < SR_DETAIL_NO || $srDetail > SR_DETAIL_EXT) {
    $srDetail = $srDefaultDetail;
  }
  
  // export
  if ( ($HTTP_GET_VARS['export']) && (tep_not_null($HTTP_GET_VARS['export'])) ) 
{    $srExp = $HTTP_GET_VARS['export'];
  }
  if ($srExp < SR_EXPORT_NO || $srExp > SR_EXPORT_CSV) {
    $srExp = $srDefaultExp;
  }
  
  // item_level
  if ( ($HTTP_GET_VARS['max']) && (tep_not_null($HTTP_GET_VARS['max'])) ) {
    $srMax = $HTTP_GET_VARS['max'];
  }
  if (!is_numeric($srMax)) {
    $srMax = $srDefaultMax;
  }
      
  // order status
  if ( ($HTTP_GET_VARS['status']) && (tep_not_null($HTTP_GET_VARS['status'])) ) 
{    $srStatus = $HTTP_GET_VARS['status'];
  }
  if (!is_numeric($srStatus)) {
    $srStatus = $srDefaultStatus;
  }
  
  // sort
  if ( ($HTTP_GET_VARS['sort']) && (tep_not_null($HTTP_GET_VARS['sort'])) ) {
    $srSort = $HTTP_GET_VARS['sort'];
  }
  if ($srSort < SR_SORT_NO || $srSort > SR_SORT_REVENUE_DESC) {
    $srSort = $srDefaultSort;
  }
    
  // compare
  if ( ($HTTP_GET_VARS['compare']) && (tep_not_null($HTTP_GET_VARS['compare'])) ) {
    $srCompare = $HTTP_GET_VARS['compare'];
  }
  if ($srCompare < SR_COMPARE_NO || $srCompare > SR_COMPARE_YEAR) {
    $srCompare = $srDefaultCompare;
  }

  // check start and end Date
  $startDate = "";
  $startDateG = 0;
  if ( ($HTTP_GET_VARS['startD']) && (tep_not_null($HTTP_GET_VARS['startD'])) ) 
{    $sDay = $HTTP_GET_VARS['startD'];
    $startDateG = 1;
  } else {
    $sDay = 1;
  }
  if ( ($HTTP_GET_VARS['startM']) && (tep_not_null($HTTP_GET_VARS['startM'])) ) 
{    $sMon = $HTTP_GET_VARS['startM'];
    $startDateG = 1;
  } else {
    $sMon = 1;
  }
  if ( ($HTTP_GET_VARS['startY']) && (tep_not_null($HTTP_GET_VARS['startY'])) ) 
{    $sYear = $HTTP_GET_VARS['startY'];
    $startDateG = 1;
  } else {
    $sYear = date("Y");
  }
  if ($startDateG) {
    $startDate = mktime(0, 0, 0, $sMon, $sDay, $sYear);
  } else {
    $startDate = mktime(0, 0, 0, date("m"), 1, date("Y"));
  }
    
  $endDate = "";
  $endDateG = 0;
  if ( ($HTTP_GET_VARS['endD']) && (tep_not_null($HTTP_GET_VARS['endD'])) ) {
    $eDay = $HTTP_GET_VARS['endD'];
    $endDateG = 1;
  } else {
    $eDay = 1;
  }
  if ( ($HTTP_GET_VARS['endM']) && (tep_not_null($HTTP_GET_VARS['endM'])) ) {
    $eMon = $HTTP_GET_VARS['endM'];
    $endDateG = 1;
  } else {
    $eMon = 1;
  }
  if ( ($HTTP_GET_VARS['endY']) && (tep_not_null($HTTP_GET_VARS['endY'])) ) {
    $eYear = $HTTP_GET_VARS['endY'];
    $endDateG = 1;
  } else {
    $eYear = date("Y");
  }
  if ($endDateG) {
    $endDate = mktime(0, 0, 0, $eMon, $eDay + 1, $eYear);
  } else {
    $endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
  }
  
  require(DIR_WS_CLASSES . 'sales_report2.php');
  $sr = new sales_report($srView, $startDate, $endDate, $srSort, $srStatus, $srFilter);
  if ($srCompare > SR_COMPARE_NO) {
    if ($srCompare == SR_COMPARE_DAY) {
      $compStartDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) - 1, date("Y", $startDate));
      $compEndDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate) - 1, date("Y", $endDate));
    } else if ($srCompare == SR_COMPARE_MONTH) {
      $compStartDate = mktime(0, 0, 0, date("m", $startDate) - 1, date("d", $startDate), date("Y", $startDate));
      $compEndDate = mktime(0, 0, 0, date("m", $endDate) - 1, date("d", $endDate), date("Y", $endDate));
    } else if ($srCompare == SR_COMPARE_YEAR) {
      $compStartDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate), date("Y", $startDate) - 1);
      $compEndDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate), date("Y", $endDate) - 1);
    }
    if ($compStartDate != $startDate) {
      $sr2 = new sales_report($srView, $compStartDate, $compEndDate, $srSort, $srStatus, $srFilter);
      $compStartDate = $sr2->startDate;
      $compEndDate = $sr2->endDate;
    }
  }
  $startDate = $sr->startDate;
  $endDate = $sr->endDate;  
  
  if ($srExp == SR_EXPORT_CSV) {
    require(TEMPLATE_CSV);
  } else {
    require(TEMPLATE_DEFAULT);
  }
?>
