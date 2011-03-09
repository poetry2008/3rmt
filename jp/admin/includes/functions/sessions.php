<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      $SESS_LIFE = 1440;
    }

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $qid = tep_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "'");

      $value = tep_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

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

    function _sess_destroy($key) {
      return tep_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
    }

    function _sess_gc($maxlifetime) {
      tep_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function tep_session_start() {
    //if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")) {
    /*  session_cache_limiter('public');
    //}
    return session_start();
    */
    $success = session_start();
    if($success)
    {
      $session_keys = array_keys($_SESSION);
      foreach($session_keys as $variable)
      {
        link_session_variable($variable, true);
      }
    }
    return $success;
  }

  function tep_session_register($variable) {
    //return session_register($variable);
    link_session_variable($variable, true);

    return true;
  }

  function tep_session_is_registered($variable) {
    //return session_is_registered($variable);
    return isset($_SESSION[$variable]);
  }

  function tep_session_unregister($variable) {
    //return session_unregister($variable);
    link_session_variable($variable, false);
    return true;
  }

  function tep_session_id($sessid = '') {
    if ($sessid != '') {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function tep_session_name($name = '') {
    if ($name != '') {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function tep_session_close() {
    $session_keys = array_keys($_SESSION);
    foreach($session_keys as $variable)
    {
      link_session_variable($variable, false); 
    }

    //上面是新加代码
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function tep_session_destroy() {
    $session_keys = array_keys($_SESSION);
    foreach($session_keys as $variable)
    {
      link_session_variable($variable, false); 
    }

    //上面是新加代码



    return session_destroy();
  }

  function tep_session_save_path($path = '') {
    if ($path != '') {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }
  function link_session_variable($var_name, $map)
  {
    if($map)
    {
      if(array_key_exists($var_name,$GLOBALS))
      {
        $_SESSION[$var_name] =& $GLOBALS[$var_name];
      }else{
        $GLOBALS[$var_name] =& $_SESSION[$var_name];
      }
    }else{
      $nothing = 0;
      $GLOBALS[$var_name] =& $nothing;
      unset($GLOBALS[$var_name]);
      $GLOBALS[$var_name] = $_SESSION[$var_name];
    }
  }
?>
