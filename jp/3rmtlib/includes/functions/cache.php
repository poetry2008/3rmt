<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: 写入缓存 
    参数: $var(string) 值  
    参数: $filename(string) 文件名   
    返回值: 是否写入成功(boolean) 
------------------------------------ */
  function write_cache(&$var, $filename) {
    $filename = DIR_FS_CACHE . $filename;
    $success = false;

// try to open the file
    if ($fp = @fopen($filename, 'w')) {
// obtain a file lock to stop corruptions occuring
      flock($fp, 2); // LOCK_EX
// write serialized data
      fputs($fp, serialize($var));
// release the file lock
      flock($fp, 3); // LOCK_UN
      fclose($fp);
      $success = true;
    }

    return $success;
  }

/* -------------------------------------
    功能: 读出缓存 
    参数: $var(string) 值  
    参数: $filename(string) 文件名   
    参数: $auto_expire(boolean) 是否过期   
    返回值: 是否读出成功(boolean) 
------------------------------------ */
  function read_cache(&$var, $filename, $auto_expire = false){
    $filename = DIR_FS_CACHE . $filename;
    $success = false;

    if (($auto_expire == true) && file_exists($filename)) {
      $now = time();
      $filetime = filemtime($filename);
      $difference = $now - $filetime;

      if ($difference >= $auto_expire) {
        return false;
      }
    }

// try to open file
    if ($fp = @fopen($filename, 'r')) {
// read in serialized data
      $szdata = fread($fp, filesize($filename));
      fclose($fp);
// unserialze the data
      $var = unserialize($szdata);

      $success = true;
    }

    return $success;
  }

/* -------------------------------------
    功能: 从缓存或者数据库中取得数据 
    参数: $sql(string) sql语句  
    参数: $var(string) 值  
    参数: $filename(string) 文件名   
    参数: $refresh(boolean) 是否刷新   
    返回值: 无 
------------------------------------ */
  function get_db_cache($sql, &$var, $filename, $refresh = false){
    $var = array();

// check for the refresh flag and try to the data
    if (($refresh == true)|| !read_cache($var, $filename)) {
// Didn' get cache so go to the database.
      $res = tep_db_query($sql);
// loop through the results and add them to an array
      while ($rec = tep_db_fetch_array($res)) {
        $var[] = $rec;
      }
// write the data to the file
      write_cache($var, $filename);
    }
  }

/* -------------------------------------
    功能: 缓存分类栏 
    参数: $auto_expire(boolean) 是否过期   
    参数: $refresh(boolean) 是否刷新   
    参数: $tmp_id_array(array) 分类数组  
    返回值: 缓存信息(string) 
------------------------------------ */
  function tep_cache_categories_box($auto_expire = false, $refresh = false, $tmp_id_array = array()) {
    global $cPath, $foo, $language, $languages_id, $id, $categories_string;
    if (!empty($tmp_id_array)) {
      $tmp_cPath = implode('_', $tmp_id_array); 
    } else {
      $tmp_cPath = $cPath; 
    }
    if (($refresh == true) || !read_cache($cache_output, 'categories_box-' .  $language . '.cache' . $tmp_cPath, $auto_expire)) {
      ob_start();
      include(DIR_WS_BOXES . 'categories.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'categories_box-' . $language . '.cache' . $tmp_cPath);
    }

    return $cache_output;
  }

/* -------------------------------------
    功能: 缓存制造商栏 
    参数: $auto_expire(boolean) 是否过期   
    参数: $refresh(boolean) 是否刷新   
    返回值: 缓存信息(string) 
------------------------------------ */
  function tep_cache_manufacturers_box($auto_expire = false, $refresh = false) {
    global $_GET, $language;

    if (($refresh == true) || !read_cache($cache_output, 'manufacturers_box-' . $language . '.cache' . $_GET['manufacturers_id'], $auto_expire)) {
      ob_start();
      include(DIR_WS_BOXES . 'manufacturers.php');
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'manufacturers_box-' . $language . '.cache' . $_GET['manufacturers_id']);
    }

    return $cache_output;
  }

/* -------------------------------------
    功能: 缓存已购买商品栏 
    参数: $auto_expire(boolean) 是否过期   
    参数: $refresh(boolean) 是否刷新   
    返回值: 缓存信息(string) 
------------------------------------ */
  function tep_cache_also_purchased($auto_expire = false, $refresh = false) {
    global $_GET, $language, $languages_id;

    if (($refresh == true) || !read_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['products_id'], $auto_expire)) {
      ob_start();
      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['products_id']);
    }

    return $cache_output;
  }
?>
