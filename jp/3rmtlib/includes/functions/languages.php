<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: 获取指定的语言目录 
    参数: $code(int) 语言编号   
    返回值: 语言目录(string) 
------------------------------------ */
  function tep_get_languages_directory($code) {
    global $languages_id;
    //set default language to language_id 
    $languages_id = tep_get_default_language_id();
    $language_query = tep_db_query("select languages_id, directory from " . TABLE_LANGUAGES . " where code = '" . $code . "'");
    if (tep_db_num_rows($language_query)) {
      $language = tep_db_fetch_array($language_query);
      return $language['directory'];
    } else {
      return false;
    }
  }
?>
