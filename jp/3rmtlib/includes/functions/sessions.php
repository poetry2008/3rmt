<?php
/*
  $Id$
*/

  if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      $SESS_LIFE = 1440;
    }

/* -------------------------------------
    功能: session开启 
    参数: $save_path(string) 保存路径   
    参数: $session_name(string) session名字   
    返回值: 开启标识(boolean) 
------------------------------------ */
    function _sess_open($save_path, $session_name) {
      return true;
    }

/* -------------------------------------
    功能: session关闭 
    参数: 无   
    返回值: 关闭标识(boolean) 
------------------------------------ */
    function _sess_close() {
      return true;
    }

/* -------------------------------------
    功能: session读取 
    参数: $key(string) session键值   
    返回值: 读取到的值(string/boolean) 
------------------------------------ */
    function _sess_read($key) {
      $qid = tep_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "'");

      $value = tep_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

/* -------------------------------------
    功能: session写入 
    参数: $key(string) session键值   
    参数: $val(string) session值   
    返回值: 写入的资源符(resource/boolean) 
------------------------------------ */
    function _sess_write($key, $val) {
      global $SESS_LIFE;

      $expiry = time() + $SESS_LIFE;
      $value = addslashes($val);

      $qid = tep_db_query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
      $total = tep_db_fetch_array($qid);

      if ($total['total'] > 0) {
        return tep_db_query("update " . TABLE_SESSIONS . " set expiry = '" . $expiry . "', value = '" . $value . "' where sesskey = '" . $key . "'");
      } else {
        return tep_db_query("insert into " . TABLE_SESSIONS . " values ('" . $key . "', '" . $expiry . "', '" . $value . "')");
      }
    }

/* -------------------------------------
    功能: 删除指定session 
    参数: $key(string) session键值   
    返回值: 删除的资源符(resource/boolean) 
------------------------------------ */
    function _sess_destroy($key) {
      return tep_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
    }

/* -------------------------------------
    功能: 删除过期的session 
    参数: $maxlifetime(int) 最大生存时间   
    返回值: 删除成功的标识(boolean) 
------------------------------------ */
    function _sess_gc($maxlifetime) {
      tep_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

/* -------------------------------------
    功能: session开启 
    参数: 无 
    返回值: 是否开启(boolean) 
------------------------------------ */
  function tep_session_start() {
    return session_start();
  }

/* -------------------------------------
    功能: session注册 
    参数: $variable(string) 变量名 
    返回值: 是否注册成功(boolean) 
------------------------------------ */
  function tep_session_register($variable) {
    return session_register($variable);
  }

/* -------------------------------------
    功能: 是否注册过该session 
    参数: $variable(string) 变量名 
    返回值: 是否注册(boolean) 
------------------------------------ */
  function tep_session_is_registered($variable) {
    return isset($_SESSION[$variable]);
  }

/* -------------------------------------
    功能: 注销该session 
    参数: $variable(string) 变量名 
    返回值: 是否注销成功(boolean) 
------------------------------------ */
  function tep_session_unregister($variable) {
    return session_unregister($variable);
  }

/* -------------------------------------
    功能: 获得/设置当前的session的id值 
    参数: $sessid(string) 指定id 
    返回值: session的id值(string) 
------------------------------------ */
  function tep_session_id($sessid = '') {
    if (!empty($sessid)) {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

/* -------------------------------------
    功能: 获得/设置当前的session的名字 
    参数: $name(string) 指定名字 
    返回值: session的名字(string) 
------------------------------------ */
  function tep_session_name($name = '') {
    if (!empty($name)) {
      return session_name($name);
    } else {
      return session_name();
    }
  }

/* -------------------------------------
    功能: 关闭session 
    参数: 无 
    返回值: 是否关闭成功(boolean) 
------------------------------------ */
  function tep_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

/* -------------------------------------
    功能: 销毁session 
    参数: 无 
    返回值: 是否销毁成功(boolean) 
------------------------------------ */
  function tep_session_destroy() {
    return session_destroy();
  }

/* -------------------------------------
    功能: 获得/设置当天session的保存路径 
    参数: $path(string) 路径 
    返回值: 保存路径(string) 
------------------------------------ */
  function tep_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }

/* -------------------------------------
    功能: 生成新的session的id 
    参数: 无 
    返回值: 新的session的id(string) 
------------------------------------ */
  function tep_session_newid() {
    mt_srand( (double)microtime() * 1000000 );
    return md5( uniqid( mt_rand() ) );
  }

/* -------------------------------------
    功能: 重新生成session 
    参数: 无 
    返回值: 无 
------------------------------------ */
  function tep_session_recreate() {
    if (PHP_VERSION >= 4.1) {
      $session_backup = $_SESSION;

      unset($_COOKIE[tep_session_name()]);

      tep_session_destroy();

      if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
      }

      $saved_value = ini_set('session.use_trans_sid','0');
      session_id(tep_session_newid());
      define('SID_NEW', tep_session_name().'='.tep_session_id());
      tep_session_start();
      ini_set('session.use_trans_sid', $saved_value);

      $_SESSION = $session_backup;
      unset($session_backup);
    }
  }
?>
