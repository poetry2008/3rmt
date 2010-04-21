#!/usr/bin/php
<?php
/*
   设置上传目录的权限
 */
exit;
ch_permission($_SERVER['PWD']);
function ch_permission($dir){
  if (is_dir($dir)) {
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))) {
      if ($file != '.' && $file != '..') {
        if (is_dir($file)) {
          //echo $dir . '/' . $file . "\n";
          if ($file != '.git') {
            chmod(755, $dir . '/' . $file);
            ch_permission($dir . $file);
          }
        } else {
          //echo $dir . '/' . $file . "\n";
          switch (get_file_type($file)) {
            case 'htaccess':
              chmod(604, $dir . '/' . $file);
              break;
            default:
              chmod(644, $dir . '/' . $file);
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
