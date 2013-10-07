<?php
/*
  $Id$
*/

  class splitPageResults {
/* class constructor */
/*------------------------------
 功能：缩减订单字符串 
 参数：$sql(string) SQL语句
 返回值：缩减完之后的订单字符串(string)
 -----------------------------*/
    function cutOrderString($sql)
    {
      return preg_replace('/order by [a-z0-9A-Z\.\_]+ \w*$/','',trim($sql));
    }
/*----------------------------
 功能：分页的结果 
 参数：$current_page_number(string) 当前页码
 参数：$max_rows_per_page(string) 每页最大的行数
 参数：$sql_query(string) SQL查询
 参数：$query_num_rows(string) 查询行数
 参数：$sql_count_query(string) SQL计数查询
 返回值：无
 ---------------------------*/    
    function splitPageResults(&$current_page_number, $max_rows_per_page, &$sql_query, &$query_num_rows, $sql_count_query = null) {
      if (empty($current_page_number)) $current_page_number = 1;
      $offset = ($max_rows_per_page * ($current_page_number - 1));
      if ($sql_count_query !== null) {
        // must use alias named "count"
        $count_query  = tep_db_query($sql_count_query);
        $count_result = tep_db_fetch_array($count_query);
        $query_num_rows = $count_result['count'];
      } else {

        $query_num_rows = tep_db_num_rows(tep_db_query($this->cutOrderString($sql_query)));
      }
      
      $total_page = intval($query_num_rows / $max_rows_per_page);
      if ($query_num_rows % $max_rows_per_page) $total_page++; 
      
      if (($current_page_number > $total_page) && ($total_page > 0)) {
        $offset = ($max_rows_per_page * ($total_page - 1));
      }
      $sql_query .= " limit " . $offset . ", " . $max_rows_per_page;
    }
  

/* class functions */

// display split-page-number-links
/*---------------------------------
 功能：显示连接 
 参数：$query_numrows(string) 查询行数
 参数: $max_rows_per_page(string) 每页最大的行数
 参数：$max_page_links(string) 最大的页面连接
 参数：$current_page_number(string) 当前页码
 参数：$parameters(string) 自定义参数
 返回值：无
 --------------------------------*/
    function display_links($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '') {
      global $PHP_SELF;

      $class = 'class="pageResults"';

      if ( tep_not_null($parameters) && (substr($parameters, -1) != '&') ) $parameters .= '&';

      $num_pages = intval($query_numrows / $max_rows_per_page);
      
      if ($query_numrows % $max_rows_per_page) $num_pages++; 
   
      if ($current_page_number > $num_pages) {
        $current_page_number = $num_pages; 
      }
      
      echo '<div class="float_right">'; 
      if ($current_page_number > 1) {
        if ($current_page_number == 2) {
          $prev_url_str = str_replace('\'', '||||', tep_href_link(basename($PHP_SELF), $parameters)); 
          $prev_url_str = str_replace('"', '>>>>', $prev_url_str); 
          echo '<input type="button" value="'.(defined('DIR_WS_ADMIN')?BUTTON_PREV:PREVNEXT_BUTTON_PREV).'" onclick="page_change(\''.$prev_url_str.'\')">&nbsp;&nbsp;'; 
        } else {
          $prev_url_str = str_replace('\'', '||||', tep_href_link(basename($PHP_SELF), $parameters.'page='.($current_page_number - 1))); 
          $prev_url_str = str_replace('"', '>>>>', $prev_url_str); 
          echo '<input type="button" value="'.(defined('DIR_WS_ADMIN')?BUTTON_PREV:PREVNEXT_BUTTON_PREV).'" onclick="page_change(\''.$prev_url_str.'\')">&nbsp;&nbsp;'; 
        }
      }
      
      if (!defined('DIR_WS_ADMIN')) {
        echo '<span class="box_text">'; 
      } 
      if ($num_pages <= 11) {
        for ($i = 1; $i <= $num_pages; $i++) {
          if ($i == $current_page_number) {
            if ($num_pages > 1) {
              if (!defined('DIR_WS_ADMIN')) {
                echo '&nbsp;<b>'.$i.'</b>&nbsp;'; 
              } else {
                echo '&nbsp;'.$i.'&nbsp;'; 
              }
            }
          } else {
            echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($i != 1)?'page='.$i:'')).'">'.$i.'</a>&nbsp;'; 
          }
        }
      } else if (($current_page_number + 5) >= $num_pages) {
        $diff_num = $num_pages - $current_page_number;  
        
        if (($current_page_number - (10 - $diff_num)) > 1) {
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters).'">1...</a>&nbsp;&nbsp;'; 
        }
        
        for ($i = 10 - $diff_num; $i > 0; $i--) {
          $front_start = $current_page_number - $i; 
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($front_start != 1)?'page='.$front_start:'')).'">'.$front_start.'</a>&nbsp;'; 
        }
        
        if (!defined('DIR_WS_ADMIN')) {
          echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;';
        } else {
          echo '&nbsp;'.$current_page_number.'&nbsp;';
        }
        
        for ($j = 1; $j <= $diff_num; $j++) {
          $end_start = $current_page_number + $j;
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($end_start != 1)?'page='.$end_start:'')).'">'.$end_start.'</a>&nbsp;'; 
        }
      } else if (($current_page_number - 5) <= 1) {
        $diff_num = $current_page_number - 1;
        
        for ($i = $diff_num; $i > 0; $i--) {
          $front_start = $current_page_number - $i;
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($front_start != 1)?'page='.$front_start:'')).'">'.$front_start.'</a>&nbsp;'; 
        }
        
        if (!defined('DIR_WS_ADMIN')) {
          echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;';
        } else {
          echo '&nbsp;'.$current_page_number.'&nbsp;';
        }
        
        for ($j = 1; $j <= (10 - $diff_num); $j++) {
          $end_start = $current_page_number + $j;
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($end_start != 1)?'page='.$end_start:'')).'">'.$end_start.'</a>&nbsp;'; 
        }
        
        if ($end_start < $num_pages) {
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($num_pages != 1)?'page='.$num_pages:'')).'">...'.$num_pages.'</a>&nbsp;'; 
        }
      } else {
        $front_start = 1;
        
        if ($current_page_number > 5) {
          $front_start = $current_page_number - 5; 
        }
        
        if ($front_start > 1) {
          echo '<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters).'">1...</a>&nbsp;&nbsp;'; 
        }
        
        for ($i = $front_start; $i < $current_page_number; $i++) {
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($i != 1)?'page='.$i:'')).'">'.$i.'</a>&nbsp;'; 
        }

        if (!defined('DIR_WS_ADMIN')) {
          echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;';
        } else {
          echo '&nbsp;'.$current_page_number.'&nbsp;';
        }
      
        $end_start = 5;
        if ($num_pages > $end_start && ($current_page_number + $end_start) < $num_pages) {
          $end_start = $current_page_number + $end_start; 
        } else {
          $end_start = $num_pages; 
        }
      
        for ($j = $current_page_number + 1; $j <= $end_start; $j++) {
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($j != 1)?'page='.$j:'')).'">'.$j.'</a>&nbsp;'; 
        }
        
        if ($end_start < $num_pages) {
          echo '&nbsp;<a '.$class.' href="'.tep_href_link(basename($PHP_SELF), $parameters.(($num_pages != 1)?'page='.$num_pages:'')).'">...'.$num_pages.'</a>&nbsp;'; 
        }
      }
    
      if (!defined('DIR_WS_ADMIN')) {
        echo '</span>'; 
      } 
      if ($current_page_number < $num_pages) {
        $next_url_str = str_replace('\'', '||||', tep_href_link(basename($PHP_SELF), $parameters.'page='.($current_page_number + 1))); 
        $next_url_str = str_replace('"', '>>>>', $next_url_str); 
        echo '&nbsp;&nbsp;<input type="button" value="'.(defined('DIR_WS_ADMIN')?BUTTON_NEXT:PREVNEXT_BUTTON_NEXT).'" onclick="page_change(\''.$next_url_str.'\');">'.(defined('DIR_WS_ADMIN')?'':'&nbsp;'); 
      }
      echo '</div>'; 
    
      if ($num_pages > 1) {
        if (defined('DIR_WS_ADMIN')) {
          echo '<div class="float_right">'; 
          echo '<form method="post" action="'.tep_href_link('ajax_orders.php', 'action=handle_split').'">'; 
        } else {
          echo '<div class="float_right_box">'; 
          echo '<form method="post" action="'.tep_href_link('handle_split.php').'">'; 
        }
        if ($current_page_number) {
          echo '<input type="text" class="input_box" name="j_page" value="'.$current_page_number.'" size="2">'; 
        } else {
          echo '<input type="text" class="input_box" name="j_page" value="1" size="2">'; 
        }
        echo '<input type="hidden" name="split_param" value="'.$parameters.'">'; 
        echo '<input type="hidden" name="current_file_info" value="'.basename($PHP_SELF).'">'; 
        echo '<input type="hidden" name="split_total_page" value="'.$num_pages.'">'; 
        if (!defined('DIR_WS_ADMIN')) {
          echo '<span class="box_text">&nbsp;'.JUMP_PAGE_TEXT.'&nbsp;</span>'; 
        } else {
          echo '&nbsp;'.JUMP_PAGE_TEXT.'&nbsp;'; 
        }
        echo '<input type="button" value="'.JUMP_PAGE_BUTTON_TEXT.'" onclick="jump_page(this, \''.$num_pages.'\',\''.(isset($current_page_number)?$current_page_number:'1').'\')">&nbsp;&nbsp;';
        echo '</form>'; 
        echo '</div>'; 
      }
    }

// display number of total products found
/*---------------------------
 功能：显示产品的总数 
 参数：$query_numrows(string) 查询行数
 参数: $max_rows_per_page(string) 每页最大的行数
 参数：$current_page_number(string) 当前页码
 参数：$text_output(string) 文本输出 
 返回值：产品总数(string)
 --------------------------*/
    function display_count($query_numrows, $max_rows_per_page, $current_page_number, $text_output) {
      $total_page = intval($query_numrows / $max_rows_per_page);
      if ($query_numrows % $max_rows_per_page) $total_page++; 
        
      if (($current_page_number > $total_page) && ($total_page > 0)) {
         $current_page_number = $total_page; 
      } 
      
      $to_num = ($max_rows_per_page * $current_page_number);
      if ($to_num > $query_numrows) $to_num = $query_numrows;
      $from_num = ($max_rows_per_page * ($current_page_number - 1));
      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      if (!defined('DIR_WS_ADMIN')) {
        return sprintf($text_output, $from_num, $to_num, $query_numrows);
      } else {
        $replace_array = array('<b>', '</b>'); 
        $return_str = sprintf($text_output, $from_num, $to_num, $query_numrows);
        $return_str = str_replace($replace_array, '', $return_str); 
        return $return_str; 
      }
    }
  }
  
