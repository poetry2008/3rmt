<?php
/*
  $Id$
*/

  class sales_report {
    var $mode, $globalStartDate, $startDate, $endDate, $actDate, $showDate, $showDateEnd, $sortString, $status, $outlet, $method, $products_id, $categories_id, $orders_flag, $order_sort, $order_type;
/*----------------------------------------------------
 功能: 销售报告
 参数: $mode(string) 模式
 参数: $startDate(string) 开始日期
 参数: $endDate(string) 结束日期 
 参数: $sort(string) 分类
 参数: $statusFilter(string) 状态过滤器
 参数: $filter(string) 过滤器
 返回值: 无
 ---------------------------------------------------*/
    function sales_report($mode, $startDate = 0, $endDate = 0, $sort = 0, $statusFilter = 0, $filter = 0, $srMethod = 0, $products_id, $categories_id, $order_sort, $order_type) {
      // startDate and endDate have to be a unix timestamp. Use mktime !
      // if set then both have to be valid startDate and endDate
      $this->method = $srMethod == 1 ? 'date_purchased' : 'torihiki_date';
      $this->products_id = $products_id;
      $this->categories_id = $categories_id;
      $this->orders_flag = false;
      $this->order_sort = $order_sort;
      $this->order_type = $order_type;
      
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
      
      $siteStr = isset($_GET['site_id']) && strlen($_GET['site_id'])?" AND o.site_id in (".str_replace('-',',',$_GET['site_id']).") ":'';
      if($_GET['bflag'] == '2') {
        $bflag = '1';
      } else if ($_GET['bflag'] == '1') {
        $bflag = '0';
      }

      //获取分类下的所有商品
      $products_id_str = '';
      if($this->products_id == 0 && $this->categories_id != 0){

        $categories_id_array = array(); 
        $categories_query = tep_db_query("select categories_id from ". TABLE_CATEGORIES ." where parent_id='".$this->categories_id."'");
        $categories_num_rows = tep_db_num_rows($categories_query);
        if($categories_num_rows == 0){

          $categories_id_array[] = $this->categories_id;
          tep_db_free_result($categories_query); 
        }else{
          $categories_id_array[] = $this->categories_id;
          while($categories_array = tep_db_fetch_array($categories_query)){

            $categories_child_query = tep_db_query("select categories_id from ". TABLE_CATEGORIES ." where parent_id='".$categories_array['categories_id']."'");
            $categories_child_num_rows = tep_db_num_rows($categories_child_query);
            if($categories_child_num_rows == 0){

              $categories_id_array[] = $categories_array['categories_id'];
              tep_db_free_result($categories_child_query);
            }else{
              $categories_id_array[] = $categories_array['categories_id'];
              while($categories_child_array = tep_db_fetch_array($categories_child_query)){

                $categories_id_array[] = $categories_child_array['categories_id']; 
              }
              tep_db_free_result($categories_child_query);
            }
          }
          tep_db_free_result($categories_query);
        }
        $products_id_array = array();
        foreach($categories_id_array as $categories_value){
          $products_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_value."'");
          while($products_array = tep_db_fetch_array($products_query)){

            $products_id_array[] = $products_array['products_id'];
          }
          tep_db_free_result($products_query);
        }
        if(!empty($products_id_array)){
          $products_id_str = ' op.products_id in ('.implode(',',$products_id_array).') and';
        }else{
          $products_id_str = ' op.products_id=0 and'; 
        }
      }
      // query for order count
      $buyOrSellWhere = isset($_GET['bflag']) && $_GET['bflag'] ? (" AND p.products_bflag=" . $bflag) : '';
      $this->queryOrderCnt = "SELECT count(o.orders_id) as order_cnt FROM " . TABLE_ORDERS . " o left join ".TABLE_ORDERS_PRODUCTS." op on o.orders_id=op.orders_id left join ". TABLE_PRODUCTS ." p on op.products_id=p.products_id left join ". TABLE_ORDERS_STATUS ." os on o.orders_status=os.orders_status_id WHERE".($this->products_id != 0 ? ' op.products_id='.$this->products_id.' and' : $products_id_str)." 1=1".$siteStr.$buyOrSellWhere;


      // queries for item details count
      $buyOrSellWhere = isset($_GET['bflag']) && $_GET['bflag'] ? (" AND op.products_id=p.products_id AND p.products_bflag=" . $bflag) : '';
      $this->queryItemCnt = "SELECT o.orders_id as orders_id, o.date_purchased as date_purchased, op.products_id as pid, op.orders_products_id,
        op.products_name as pname, sum(op.products_quantity) as pquant, ".
        /*
        if(p.products_bflag = '0' , sum(op.final_price * op.products_quantity), 0-sum(op.final_price * op.products_quantity))
        */
        " sum(op.final_price * op.products_quantity) as psum, op.products_tax as ptax FROM " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p, ". TABLE_ORDERS_STATUS ." os WHERE".($this->products_id != 0 ? ' p.products_id='.$this->products_id.' and' : $products_id_str)." o.orders_id = op.orders_id AND op.products_id = p.products_id and o.orders_status=os.orders_status_id " . $siteStr . $buyOrSellWhere ;



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
/*---------------------------------------------
 功能：下一个日期
 参数: 无
 返回值: 时间(boolean) 
 --------------------------------------------*/
    function hasNext() {
        return ($this->actDate < $this->endDate);
    }
/*--------------------------------------------
 功能: 时间戳 
 参数: 无
 返回值：返回的时间戳值(string)
 -------------------------------------------*/
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
        // orders
        case '5':
          $sd = $this->actDate;
          $ed = $this->endDate;
          $this->orders_flag = true;
          if($this->order_sort == 'date'){

            $this->order_sort = 'o.date_purchased';
            if($this->order_sort != '' && $this->order_type != ''){

              $this->sortString = " order by ".$this->order_sort." ".$this->order_type;
            }
          }
          if($this->order_sort == 'orders'){

            $this->order_sort = 'o.orders_id';
            if($this->order_sort != '' && $this->order_type != ''){

              $this->sortString = " order by ".$this->order_sort." ".$this->order_type;
            }
          }
          if($this->order_sort == 'num'){

            $this->order_sort = 'pquant';
            if($this->order_sort != '' && $this->order_type != ''){

              $this->sortString = " order by ".$this->order_sort." ".$this->order_type;
            }
          }
          if($this->order_sort == 'price'){

            $this->order_sort = 'psum';
            if($this->order_sort != '' && $this->order_type != ''){

              $this->sortString = " order by ".$this->order_sort." ".$this->order_type;
            }
          }
          if($this->order_sort == 'pname'){

            $this->order_sort = 'op.products_name';
            if($this->order_sort != '' && $this->order_type != ''){

              $this->sortString = " order by ".$this->order_sort." ".$this->order_type;
            }
          } 
          break;
      }
      if ($ed > $this->endDate) {
        $ed = $this->endDate;
      }

      $filterString = "";
      if ($this->statusFilter == 'success') {
        $filterString .= " AND os.finished = '1' AND o.flag_qaf = '1' ";
      } else if ($this->statusFilter > 0) {
        $filterString .= " AND o.orders_status = " . $this->statusFilter . " ";
      }
      $rqOrders = tep_db_query($this->queryOrderCnt . " AND o.".$this->method." >= '" . tep_db_input(date("Y-m-d\TH:i:s", $sd)) . "' AND o.".$this->method." < '" . tep_db_input(date("Y-m-d\TH:i:s", $ed)) . "'" . $filterString);
      $order = tep_db_fetch_array($rqOrders);

      $rqItems = tep_db_query($this->queryItemCnt . " AND o.".$this->method." >= '" . tep_db_input(date("Y-m-d\TH:i:s", $sd)) . "' AND o.".$this->method." < '" . tep_db_input(date("Y-m-d\TH:i:s", $ed)) . "'" . $filterString . " group by ".($this->orders_flag == false ? 'pid' : 'orders_id,pid')." " . $this->sortString);

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
      if($this->orders_flag == false){
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
      }

      if($this->orders_flag == true){
        $orders_list_array = array();
        $orders_i = 0;
        while($orders_array = tep_db_fetch_array($rqItems)){

          $orders_list_array[$orders_i]['date_purchased'] = $orders_array['date_purchased'];
          $orders_list_array[$orders_i]['orders_id'] = $orders_array['orders_id'];
          $orders_list_array[$orders_i]['pid'] = $orders_array['pid'];
          $orders_list_array[$orders_i]['pname'] = $orders_array['pname'];
          $orders_list_array[$orders_i]['pquant'] = $orders_array['pquant'];
          $orders_list_array[$orders_i]['psum'] = $orders_array['psum'];
          $orders_i++;
        }  
        return $orders_list_array;
      }else{
        return $resp;
      }
      
    }
}
