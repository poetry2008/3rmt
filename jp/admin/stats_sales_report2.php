<?php
/*
  $Id$

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
  $srDefaultView = 4;
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
  if ( isset($_GET['report']) && ($_GET['report']) && (tep_not_null($_GET['report'])) ) 
{    $srView = $_GET['report'];
  } else {
    $srView = $srDefaultView;
  }
  if ($srView < SR_VIEW_YEARLY || $srView > SR_VIEW_DAILY) {
    $srView = $srDefaultView;
  }

  // detail
  if ( isset($_GET['detail']) && ($_GET['detail']) && (tep_not_null($_GET['detail'])) ) 
{    $srDetail = $_GET['detail'];
  } else {
    $srDetail = $srDefaultDetail;
  }
  if ($srDetail < SR_DETAIL_NO || $srDetail > SR_DETAIL_EXT) {
    $srDetail = $srDefaultDetail;
  }
  
  // export
  if ( isset($_GET['export']) && ($_GET['export']) && (tep_not_null($_GET['export'])) ) 
{    $srExp = $_GET['export'];
  } else {
    $srExp = $srDefaultExp;
  }
  if ($srExp < SR_EXPORT_NO || $srExp > SR_EXPORT_CSV) {
    $srExp = $srDefaultExp;
  }
  
  // item_level
  if ( isset($_GET['max']) && ($_GET['max']) && (tep_not_null($_GET['max'])) ) {
    $srMax = $_GET['max'];
  } else {
    $srMax = $srDefaultMax;
  }
  if (!is_numeric($srMax)) {
    $srMax = $srDefaultMax;
  }
      
  // order status
  if ( isset($_GET['status']) && ($_GET['status']) && (tep_not_null($_GET['status'])) ) {
    $srStatus = $_GET['status'];
  } else {
    $srStatus = $srDefaultStatus;
  }
  if (!is_numeric($srStatus)) {
    $srStatus = $srDefaultStatus;
  }
  
  // sort
  if ( isset($_GET['sort']) && ($_GET['sort']) && (tep_not_null($_GET['sort'])) ) {
    $srSort = $_GET['sort'];
  } else {
    $srSort = $srDefaultSort;
  }
  if ($srSort < SR_SORT_NO || $srSort > SR_SORT_REVENUE_DESC) {
    $srSort = $srDefaultSort;
  }
    
  // compare
  if ( isset($_GET['compare']) && ($_GET['compare']) && (tep_not_null($_GET['compare'])) ) {
    $srCompare = $_GET['compare'];
  } else {
    $srCompare = $srDefaultCompare;
  }
  if ($srCompare < SR_COMPARE_NO || $srCompare > SR_COMPARE_YEAR) {
    $srCompare = $srDefaultCompare;
  }

  // check start and end Date
  $startDate = "";
  $startDateG = 0;
  if ( isset($_GET['startD']) && ($_GET['startD']) && (tep_not_null($_GET['startD'])) ) 
{    $sDay = $_GET['startD'];
    $startDateG = 1;
  } else {
    $sDay = 1;
  }
  if ( isset($_GET['startM']) && ($_GET['startM']) && (tep_not_null($_GET['startM'])) ) 
{    $sMon = $_GET['startM'];
    $startDateG = 1;
  } else {
    $sMon = 1;
  }
  if ( isset($_GET['startY']) && ($_GET['startY']) && (tep_not_null($_GET['startY'])) ) 
{    $sYear = $_GET['startY'];
    $startDateG = 1;
  } else {
    $sYear = date("Y");
  }
  if ($startDateG) {
    $startDate = mktime(0, 0, 0, $sMon, $sDay, $sYear);
  } else {
    $startDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
  }
    
  $endDate = "";
  $endDateG = 0;
  if ( isset($_GET['endD']) && ($_GET['endD']) && (tep_not_null($_GET['endD'])) ) {
    $eDay = $_GET['endD'];
    $endDateG = 1;
  } else {
    $eDay = 1;
  }
  if ( isset($_GET['endM']) && ($_GET['endM']) && (tep_not_null($_GET['endM'])) ) {
    $eMon = $_GET['endM'];
    $endDateG = 1;
  } else {
    $eMon = 1;
  }
  if ( isset($_GET['endY']) && ($_GET['endY']) && (tep_not_null($_GET['endY'])) ) {
    $eYear = $_GET['endY'];
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
  
  $sr = new sales_report($srView, $startDate, $endDate, $srSort, $srStatus, isset($srFilter)?$srFilter:'');
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
      $sr2 = new sales_report($srView, $compStartDate, $compEndDate, $srSort, $srStatus, isset($srFilter) ? $srFilter : '');
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
