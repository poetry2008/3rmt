<?php
/*
  $Id$
*/

  class sales_report {
    var $mode, $globalStartDate, $startDate, $endDate, $actDate, $showDate, $showDateEnd, $sortString, $status, $outlet, $method;

    function sales_report($mode, $startDate = 0, $endDate = 0, $sort = 0, $statusFilter = 0, $filter = 0, $srMethod = 0) {
      // startDate and endDate have to be a unix timestamp. Use mktime !
      // if set then both have to be valid startDate and endDate
      $this->method = $srMethod == 1 ? 'date_purchased' : 'torihiki_date';
      
      $this->mode = $mode;
      $this->tax_include = DISPLAY_PRICE_WITH_TAX;

      $this->statusFilter = $statusFilter;
            
      // get date of first sale
      $firstQuery = tep_db_query("select UNIX_TIMESTAMP(min(".$this->method.")) as first FROM " . TABLE_ORDERS);
      $first = tep_db_fetch_array($firstQuery);
      $this->globalStartDate = mktime(0, 0, 0, date("m", $first['first']), date("d", $first['first']), date("Y", $first['first']));
            
      $statusQuery = tep_db_query("select * from " . TABLE_ORDERS_STATUS);
      $i = 0;
      while ($outResp = tep_db_fetch_array($statusQuery)) {
        $status[$i] = $outResp;
        $i++;
      }
      $this->status = $status;

      
      if ($startDate == 0  or $startDate < $this->globalStartDate) {
        // set startDate to globalStartDate
        $this->startDate = $this->globalStartDate;
      } else {
        $this->startDate = $startDate;
      }
      if ($this->startDate > mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
        $this->startDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      }

      if ($endDate > mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))) {
        // set endDate to tomorrow
        $this->endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
      } else {
        $this->endDate = $endDate;
      }
      if ($this->endDate < $this->startDate + 24 * 60 * 60) {
        $this->endDate = $this->startDate + 24 * 60 * 60;
      }

      $this->actDate = $this->startDate;
      
      $siteStr = isset($_GET['site_id']) && strlen($_GET['site_id'])?" AND o.site_id='".$_GET['site_id']."' ":'';
      if($_GET['bflag'] == '2') {
        $bflag = '1';
        $likeStr = ' like \'%買%\' ';
      } else if ($_GET['bflag'] == '1') {
        $bflag = '0';
        $likeStr = ' not like \'%買%\' ';
      }

      // query for order count
      $buyOrSellWhere = isset($_GET['bflag']) && $_GET['bflag'] ? (" AND o.payment_method " . $likeStr) : '';
      $this->queryOrderCnt = "SELECT count(o.orders_id) as order_cnt FROM " . TABLE_ORDERS . " o WHERE 1=1".$siteStr.$buyOrSellWhere;


      // queries for item details count
      $buyOrSellWhere = isset($_GET['bflag']) && $_GET['bflag'] ? (" AND op.products_id=p.products_id AND p.products_bflag=" . $bflag) : '';
      $this->queryItemCnt = "SELECT op.products_id as pid, op.orders_products_id,
        op.products_name as pname, sum(op.products_quantity) as pquant, ".
        /*
        if(p.products_bflag = '0' , sum(op.final_price * op.products_quantity), 0-sum(op.final_price * op.products_quantity))
        */
        " sum(op.final_price * op.products_quantity) as psum, op.products_tax as ptax FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p" ." WHERE o.orders_id = op.orders_id AND op.products_id = p.products_id " . $siteStr . $buyOrSellWhere ;



      switch ($sort) {
        case '0':
          $this->sortString = "";
          break;
        case '1':
          $this->sortString = " order by pname asc ";
          break;
        case '2':
          $this->sortString = " order by pname desc";
          break;
        case '3':
          $this->sortString = " order by pquant asc, pname asc";
          break;
        case '4':
          $this->sortString = " order by pquant desc, pname asc";
          break;
        case '5':
          $this->sortString = " order by psum asc, pname asc";
          break;
        case '6':
          $this->sortString = " order by psum desc, pname asc";
          break;
      }

    }

    function hasNext() {
        return ($this->actDate < $this->endDate);
    }

    function next() {
  $sd = time();
  $ed = time();
      switch ($this->mode) {
        // yearly
        case '1':
          $sd = $this->actDate;
          $ed = mktime(0, 0, 0, date("m", $sd), date("d", $sd), date("Y", $sd) + 1);
          break;
        // monthly
        case '2':
          $sd = $this->actDate;
          $ed = mktime(0, 0, 0, date("m", $sd) + 1, 1, date("Y", $sd));
          break;
        // weekly
        case '3':
          $sd = $this->actDate;
          $ed = mktime(0, 0, 0, date("m", $sd), date("d", $sd) + 7, date("Y", $sd));
          break;
        // daily
        case '4':
          $sd = $this->actDate;
          $ed = mktime(0, 0, 0, date("m", $sd), date("d", $sd) + 1, date("Y", $sd));
          break;
      }
      if ($ed > $this->endDate) {
        $ed = $this->endDate;
      }

      $filterString = "";
      if (strpos($this->statusFilter, ',')) {
        $filterString .= " AND o.orders_status in (" . $this->statusFilter . ") ";
      } else if ($this->statusFilter > 0) {
        $filterString .= " AND o.orders_status = " . $this->statusFilter . " ";
      }
      $rqOrders = tep_db_query($this->queryOrderCnt . " AND o.".$this->method." >= '" . tep_db_input(date("Y-m-d\TH:i:s", $sd)) . "' AND o.".$this->method." < '" . tep_db_input(date("Y-m-d\TH:i:s", $ed)) . "'" . $filterString);
      $order = tep_db_fetch_array($rqOrders);

      $rqItems = tep_db_query($this->queryItemCnt . " AND o.".$this->method." >= '" . tep_db_input(date("Y-m-d\TH:i:s", $sd)) . "' AND o.".$this->method." < '" . tep_db_input(date("Y-m-d\TH:i:s", $ed)) . "'" . $filterString . " group by pid " . $this->sortString);

      // set the return values
      $this->actDate = $ed;
      $this->showDate = $sd;
      $this->showDateEnd = $ed - 60 * 60 * 24;

      // execute the query
      $cnt = 0;
      $itemTot = 0;
      $sumTot = 0;
      $sumBuyTot = 0;
      $sumSellTot = 0;
      while ($resp[$cnt] = tep_db_fetch_array($rqItems)) {
        // to avoid rounding differences round for every quantum
        // multiply with the number of items afterwords.
        $price = $resp[$cnt]['psum'] / $resp[$cnt]['pquant'];

        $resp[$cnt]['price'] = tep_add_tax($price, $resp[$cnt]['ptax']);
        $resp[$cnt]['psum'] = $resp[$cnt]['pquant'] * tep_add_tax($price, $resp[$cnt]['ptax']);
        $resp[$cnt]['order'] = $order['order_cnt'];
        //$resp[$cnt]['shipping'] = $shipping['shipping'];

        if ($resp[$cnt]['psum'] < 0) {
          $sumBuyTot += $resp[$cnt]['psum'];
        } else {
          $sumSellTot += $resp[$cnt]['psum'];
        }
        $resp[$cnt]['totsumBuy'] = $sumBuyTot;
        $resp[$cnt]['totsumSell'] = $sumSellTot;

        // values per date and item
        $sumTot += $resp[$cnt]['psum'];
        $itemTot += $resp[$cnt]['pquant'];
        // add totsum and totitem until current row
        $resp[$cnt]['totsum'] = $sumTot;
        
        $resp[$cnt]['totitem'] = $itemTot;
        $cnt++;
      }
      return $resp;
      
    }
}
