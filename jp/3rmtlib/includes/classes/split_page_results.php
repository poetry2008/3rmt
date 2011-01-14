<?php
/*
  $Id$
*/

  class splitPageResults {
/* class constructor */

    function splitPageResults(&$current_page_number, $max_rows_per_page, &$sql_query, &$query_num_rows, $sql_count_query = null) {
      if (empty($current_page_number)) $current_page_number = 1;
      $offset = ($max_rows_per_page * ($current_page_number - 1));
      if ($sql_count_query !== null) {
        // must use alias named "count"
        $count_query  = tep_db_query($sql_count_query);
        $count_result = tep_db_fetch_array($count_query);
        $query_num_rows = $count_result['count'];
      } else {
        $query_num_rows = tep_db_num_rows(tep_db_query($sql_query));
      }
      $sql_query .= " limit " . $offset . ", " . $max_rows_per_page;
    }
  

/* class functions */

// display split-page-number-links
    function display_links($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '') {
      global $PHP_SELF;

      $class = 'class="pageResults"';

      if ( tep_not_null($parameters) && (substr($parameters, -1) != '&') ) $parameters .= '&';

// calculate number of pages needing links 
      $num_pages = intval($query_numrows / $max_rows_per_page);

// $num_pages now contains int of pages needed unless there is a remainder from division 
      if ($query_numrows % $max_rows_per_page) $num_pages++; // has remainder so add one page 

// first button - not displayed on first page
//      if ($current_page_number > 1) echo '<a href="' . tep_href_link(basename($PHP_SELF),  $parameters . 'page=1') . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_FIRST_PAGE . ' ">' . PREVNEXT_BUTTON_FIRST . '</a>&nbsp;';

// previous button - not displayed on first page
      if ($current_page_number > 1) {
        if ($current_page_number == 2) {
          echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
        } else {
          echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number - 1)) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
        }
      }

// check if num_pages > $max_page_links
      $cur_window_num = intval($current_page_number / $max_page_links);
      if ($current_page_number % $max_page_links) $cur_window_num++;

      $max_window_num = intval($num_pages / $max_page_links);
      if ($num_pages % $max_page_links) $max_window_num++;

// previous window of pages
      if ($cur_window_num > 1) echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . (($cur_window_num - 1) * $max_page_links)) . '" ' . $class . ' title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>';

// page nn button
      for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $num_pages); $jump_to_page++) {
        if ($jump_to_page == $current_page_number) {
          echo '&nbsp;<b>' . $jump_to_page . '</b>&nbsp;';
        } elseif ($jump_to_page == 1) {
          echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters) . '" ' . $class . ' title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' "><u>' . $jump_to_page . '</u></a>&nbsp;';
        } else {
          echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $jump_to_page) . '" ' . $class . ' title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' "><u>' . $jump_to_page . '</u></a>&nbsp;';
        }
      }

// next window of pages
      if ($cur_window_num < $max_window_num) echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . (($cur_window_num) * $max_page_links + 1)) . '" ' . $class . ' title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">...</a>&nbsp;';

// next button
      if (($current_page_number < $num_pages) && ($num_pages != 1)) echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number + 1)) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;';

// last button
//      if (($current_page_number < $num_pages) && ($num_pages != 1)) echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $num_pages) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_LAST_PAGE . ' ">' . PREVNEXT_BUTTON_LAST . '</a>&nbsp;';
    }

// display number of total products found
    function display_count($query_numrows, $max_rows_per_page, $current_page_number, $text_output) {
      $to_num = ($max_rows_per_page * $current_page_number);
      if ($to_num > $query_numrows) $to_num = $query_numrows;
      $from_num = ($max_rows_per_page * ($current_page_number - 1));
      if ($to_num == 0) {
        $from_num = 0;
      } else {
        $from_num++;
      }

      return sprintf($text_output, $from_num, $to_num, $query_numrows);
    }
  }
  
