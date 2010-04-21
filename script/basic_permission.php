#!/usr/bin/php
<?php
/*
   设置除上传目录之外的基本权限设置，上传目录另设，适用于所有的git项目。
 */
//print_r($_SERVER['PWD']);
//print_r($_SERVER['argv']);
//exit;
ch_permission($_SERVER['PWD']);
function ch_permission($dir){
  if (is_dir($dir)) {
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))) {
      if ($file != '.' && $file != '..') {
        if (is_dir($file)) {
          //echo $dir . '/' . $file . "\n";
          if ($file != '.git') {
            chmod( $dir . '/' . $file, 755);
            ch_permission($dir . $file);
          }
        } else {
          //echo $dir . '/' . $file . "\n";
          switch (get_file_type($file)) {
            case 'htaccess':
              chmod($dir . '/' . $file, 604);
              break;
            default:
              chmod($dir . '/' . $file, 644);
              break;
          }
        }
      }
    }
  }
}

function get_file_type($filename){
  $type  = explode(".", $filename);
  $count = count($type) - 1;
  return $type[$count];
}
