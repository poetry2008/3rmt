<?php
/*
  $Id$
*/

  class logger {
    var $timer_start, $timer_stop, $timer_total, $queries;

// class constructor
/*-----------------------
 功能：记录器
 参数：无
 返回值：无
 ----------------------*/
    function logger() {
      $this->timer_start();
    }
/*----------------------
 功能：记录定时启动
 参数：无
 返回值：无
 ---------------------*/
    function timer_start() {
      if (defined("PAGE_PARSE_START_TIME")) {
        $this->timer_start = PAGE_PARSE_START_TIME;
      } else {
        $this->timer_start = microtime();
      }
    }
/*---------------------
 功能：记录定时停止
 参数：$display(boolean) 是否显示
 返回值：判断是否显示定时器
 --------------------*/
    function timer_stop($display = 'false') {
      $this->timer_stop = microtime();

      $time_start = explode(' ', $this->timer_start);
      $time_end = explode(' ', $this->timer_stop);

      $this->timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      $this->write(getenv('REQUEST_URI'), $this->timer_total . 's');

      if ($display == 'true') {
        return $this->timer_display();
      }
    }
/*---------------------
 功能：定时器显示
 参数：无
 返回值：显示一共的时间
 --------------------*/
    function timer_display() {
      return '<span class="smallText">Parse Time: ' . $this->timer_total . 's</span>';
    }
/*--------------------
 功能：记录错误日志
 参数：$message(string) 信息
 参数：$type(string) 类型
 返回值：无
 -------------------*/
    function write($message, $type) {
      $this->queries[] = $message;
      @error_log(strftime(STORE_PARSE_DATE_TIME_FORMAT) . ' [' . $type . '] ' . $message . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }
  }
?>
