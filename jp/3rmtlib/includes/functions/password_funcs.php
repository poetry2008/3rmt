<?php
/*
  $Id$

*/

/* -------------------------------------
    功能: 验证文件里的密码 
    参数: $plain(string) 文本内容   
    参数: $encrypted(string) 加密信息   
    返回值: 是否正确(boolean) 
------------------------------------ */
  function tep_validate_password($plain, $encrypted) {
    if (tep_not_null($plain) && tep_not_null($encrypted)) {
// split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) return false;

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

/* -------------------------------------
    功能: 加密密码 
    参数: $plain(string) 文本内容   
    返回值: 加密后的密码(string) 
------------------------------------ */
  function tep_encrypt_password($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= tep_rand();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }
?>
