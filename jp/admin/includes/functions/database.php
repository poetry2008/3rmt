<?php
/*
  $Id$
*/
  function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
      $$link = mysql_pconnect($server, $username, $password);
    } else {
      $$link = mysql_connect($server, $username, $password);
    }
    if(!$$link){
      $handle = fopen(DIR_FS_ADMIN.'/log/db_error.txt','a+');
      $time_string = '['.date("D M j G:i:s T Y").']';
      fwrite($handle,$time_string." [Unable to connect to database server!]\n");
      fclose($handle);
      header("Location:/admin/sql_error.php?string=Unable to connect to database server!");
      exit;
    }
    
    if ($$link) mysql_select_db($database);

    if (intval(substr(mysql_get_server_info(), 0, 1) >= 4)){
      mysql_query('set names utf8');
    }
    /*
    $sql = "set interactive_timeout=1";
    mysql_query($sql);
    $sql = "set wait_timeout=1";
    mysql_query($sql);
    */

    return $$link;
  }

  function tep_db_close($link = 'db_link') {
    global $$link;

    return mysql_close($$link);
  }

  function tep_db_error($query, $errno, $error) { 
    $handle = fopen(DIR_FS_ADMIN.'/log/db_error.txt','a+');
    $time_string = '['.date("D M j G:i:s T Y").']';
    fwrite($handle,$time_string." [".$errno."] [".$error."] [".$query."]\n");
    fclose($handle);
    header("Location:/admin/sql_error.php?string=" . $errno . ' - ' . $error .'<br><br>[SQL-ERROR]<br><br>');
  }

  function tep_db_query($query, $link = 'db_link') {
    global $$link, $logger;

    if (STORE_DB_TRANSACTIONS == 'true') {
      if (!is_object($logger)) $logger = new logger;
      $logger->write($query, 'QUERY');
    }
    $runtime= new runtime;
    $runtime->start();
    $result = mysql_query($query, $$link);
    if (mysql_error()) {
      tep_db_error($query, mysql_errno(), mysql_error());
    }
    if (STORE_DB_TRANSACTIONS == 'true') {
      if (mysql_error()) $logger->write(mysql_error(), 'ERROR');
    }
    $runtime->stop();
    $logger->times[] = $runtime->spent();
    //sql log
    if (defined(SQL_LOG) && SQL_LOG) {
      global $logNumber;
      global $testArray;
      $project = 'WM';
      $time    = date('Y-m-d H:i:s');
      $usec    = gettimeofday();
      $usec    = $usec['usec'];
      $sql     = $query;
      $n       = "\r\n";
      $content = $time."<".$sql.">".$n;
      if($logNumber == 1){
        $log['time']    = $time.' '.$usec;
        $log['project'] = $project;
        $log['sql']     = $sql;
        $log['param']['get']  = $_GET;
        $log['param']['post'] = $_POST; 
        $paramArray = serialize($log['param']);
        $log['paramarray'] = $paramArray;
        $testArray[]    = $log;
      }else{
        $res = mysql_query('SELECT MAX(gruup) as maxnumber FROM sql_log');
        $ordernum = mysql_fetch_assoc($res);
        $order = $ordernum['maxnumber'];
        if($ordernum){
          $order = $order+1;
        }else{
          $order = 1;
        }
        if (!empty($testArray)) {
          foreach($testArray as $test){
            mysql_query('INSERT INTO sql_log(time,gruup,project,content,file,param) VALUES("'.$test['time'].'", "'.$order.'", "'.$test['project'].'", "'.$test['sql'].'", "'.$_SERVER['SCRIPT_NAME'].'", "'.addslashes($test['paramarray']).'")');
          }
        }
      }
    }
    //end sql log
    return $result;
  }

  function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    reset($data);
    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return tep_db_query($query, $link);
  }

  function tep_db_fetch_array($db_query) {
    return mysql_fetch_array($db_query, MYSQL_ASSOC);
  }

  function tep_db_result($result, $row, $field = '') {
    return mysql_result($result, $row, $field);
  }

  function tep_db_num_rows($db_query) {
    return mysql_num_rows($db_query);
  }

  function tep_db_data_seek($db_query, $row_number) {
    return mysql_data_seek($db_query, $row_number);
  }

  function tep_db_insert_id() {
    return mysql_insert_id();
  }

  function tep_db_free_result($db_query) {
    return mysql_free_result($db_query);
  }

  function tep_db_fetch_fields($db_query) {
    return mysql_fetch_field($db_query);
  }

  function tep_db_output($string) {
    return htmlspecialchars($string);
  }

  function tep_db_input($string) {
    return addslashes($string);
  }

  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(stripslashes($string));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = tep_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }
  
  function tep_db_fetch_object($result, $classname = '')
  {
    if (empty($classname)) {
      return mysql_fetch_object($result); 
    }
    return mysql_fetch_object($result, $classname); 
  }

class runtime
{ 
    var $StartTime = 0; 
    var $StopTime = 0; 
 
    function get_microtime() 
    { 
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    } 
 
    function start() 
    { 
        $this->StartTime = $this->get_microtime(); 
    } 
 
    function stop() 
    { 
        $this->StopTime = $this->get_microtime(); 
    } 
 
    function spent() 
    { 
        return round(($this->StopTime - $this->StartTime) * 1000, 1); 
    } 
}
