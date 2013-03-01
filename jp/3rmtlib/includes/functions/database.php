<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: mysql连接 
    参数: $server(string) mysql服务器地址  
    参数: $username(string) 用户名 
    参数: $password(string) 密码 
    参数: $database(string) 数据库的名字 
    参数: $link(string) 连接符 
    返回值: 连接标识(resource/boolean) 
------------------------------------ */
  function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
      $$link = mysql_pconnect($server, $username, $password);
    } else {
      $$link = mysql_connect($server, $username, $password);
    }

    if ($$link) mysql_select_db($database);

    if (intval(substr(mysql_get_server_info(), 0, 1) >= 4)){
      mysql_query('set names utf8');
    }

    return $$link;
  }

/* -------------------------------------
    功能: 关闭mysql连接 
    参数: $link(string) 连接符 
    返回值: 是否关闭连接(boolean) 
------------------------------------ */
  function tep_db_close($link = 'db_link') {
    global $$link;

    return mysql_close($$link);
  }

/* -------------------------------------
    功能: mysql的sql发生错误的显示页面 
    参数: $query(string) sql语句 
    参数: $errno(string) 错误代码编号 
    参数: $error(string) 错误信息 
    返回值: 无 
------------------------------------ */
  function tep_db_error($query, $errno, $error) { 
    $sql_error_pos = strpos($error, 'MySQL server has gone away'); 
    if ($sql_error_pos !== false) {
      die(header('Location:error.html'));
    } else {
      die('<font color="#000000"><b>' . $errno . ' - ' . $error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></b></font>');
    }
  }

/* -------------------------------------
    功能: mysql的sql查询 
    参数: $query(string) sql语句 
    参数: $link(string) 连接资源标识 
    返回值: 查询资源符(resource)  
------------------------------------ */
  function tep_db_query($query, $link = 'db_link') {
    global $$link, $log_queries, $log_times;

    if (STORE_DB_TRANSACTIONS == 'true') {
      @error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
      $runtime= new runtime;
      $runtime->start();
    }

    $result = mysql_query($query, $$link) or tep_db_error($query, mysql_errno(), mysql_error());

    if (STORE_DB_TRANSACTIONS == 'true') {
      $log_queries[] = $query;
      $runtime->stop();
      $log_times[] = $runtime->spent();
      $result_error = mysql_error();
      @error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    //sql log
    if (defined(SQL_LOG) && SQL_LOG) {
      global $logNumber;
      global $testArray;
      $project = 'JP';
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
        if(empty($testArray)) $testArray = array();
        foreach($testArray as $test){
          mysql_query('INSERT INTO sql_log(time,gruup,project,content,file,param) VALUES("'.$test['time'].'", "'.$order.'", "'.$test['project'].'", "'.$test['sql'].'", "'.$_SERVER['SCRIPT_NAME'].'", "'.addslashes($test['paramarray']).'")');
        }
      }
    }
    //end sql log
    return $result;
  }

/* -------------------------------------
    功能: 插入或者更新数据 
    参数: $table(string) 表名 
    参数: $data(array) 数据 
    参数: $action(string) 执行的动作(插入/更新) 
    参数: $parameters(string) 更新的条件 
    参数: $link(string) 连接标识符 
    返回值: 资源符(resource)  
------------------------------------ */
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
            $query .= '\'' . mysql_real_escape_string($value) . '\', ';
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
            $query .= $columns . ' = \'' . mysql_real_escape_string($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return tep_db_query($query, $link);
  }

/* -------------------------------------
    功能: 获取资源结果集 
    参数: $db_query(resource) 查询资源符 
    返回值: 资源结果集合(array)  
------------------------------------ */
  function tep_db_fetch_array($db_query) {
    return mysql_fetch_array($db_query, MYSQL_ASSOC);
  }

/* -------------------------------------
    功能: 返回结果集的行数 
    参数: $db_query(resource) 查询资源符 
    返回值: 行数(int/boolean)  
------------------------------------ */
  function tep_db_num_rows($db_query) {
    return mysql_num_rows($db_query);
  }

/* -------------------------------------
    功能: 移动内部结果的指针 
    参数: $db_query(resource) 查询资源符 
    参数: $row_number(int) 行数 
    返回值: 移动的结果集(source/boolean)  
------------------------------------ */
  function tep_db_data_seek($db_query, $row_number) {
    return mysql_data_seek($db_query, $row_number);
  }

/* -------------------------------------
    功能: 插入数据获得的id值 
    参数: 无 
    返回值: id值(int)  
------------------------------------ */
  function tep_db_insert_id() {
    return mysql_insert_id();
  }

/* -------------------------------------
    功能: 释放结果内存 
    参数: $db_query(resource) 查询资源符 
    返回值: 是否释放(boolean)  
------------------------------------ */
  function tep_db_free_result($db_query) {
    return mysql_free_result($db_query);
  }

/* -------------------------------------
    功能: 从结果集中取得列信息并作为对象返回 
    参数: $db_query(resource) 查询资源符 
    返回值: 相关信息(object)  
------------------------------------ */
  function tep_db_fetch_fields($db_query) {
    return mysql_fetch_field($db_query);
  }

/* -------------------------------------
    功能: 把一些预定义的字符转换为HTML实体 
    参数: $string(string) 字符串 
    返回值: 转换后的字符串(string)  
------------------------------------ */
  function tep_db_output($string) {
    return stripslashes($string);
  }

/* -------------------------------------
    功能: 在指定的预定义字符前添加反斜杠 
    参数: $string(string) 字符串 
    返回值: 处理后的字符串(string)  
------------------------------------ */
  function tep_db_input($string) {
    return addslashes($string);
  }

/* -------------------------------------
    功能: 删除由 addslashes() 函数添加的反斜杠 
    参数: $string(string) 字符串 
    返回值: 处理后的字符串(string)  
------------------------------------ */
  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(tep_sanitize_string(stripslashes($string)));
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


class runtime
{ 
    var $StartTime = 0; 
    var $StopTime = 0; 
 
/* -------------------------------------
    功能: 获得毫秒数 
    参数: 无 
    返回值: 毫秒数(float)  
------------------------------------ */
    function get_microtime() 
    { 
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    } 
 
/* -------------------------------------
    功能: 开始时间 
    参数: 无 
    返回值: 无 
------------------------------------ */
    function start() 
    { 
        $this->StartTime = $this->get_microtime(); 
    } 
 
/* -------------------------------------
    功能: 结束时间 
    参数: 无 
    返回值: 无 
------------------------------------ */
    function stop() 
    { 
        $this->StopTime = $this->get_microtime(); 
    } 
 
/* -------------------------------------
    功能: 花费时间 
    参数: 无 
    返回值: 花费时间(float) 
------------------------------------ */
    function spent() 
    { 
        return round(($this->StopTime - $this->StartTime) * 1000, 1); 
    } 
}

