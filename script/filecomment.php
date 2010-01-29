<?php

$dir = "/home/maker/project/3rmt/script/test";

$find = "Oscommerce";

filecomment($dir);

function filecomment($dir){
  echo $dir ."\n";
  if (is_dir($dir)){
    if ($dh = opendir($dir)){
      while(($file = readdir($dh)) !== false) {
        if($file != '.' && $file != '..'){
          if(is_dir($dir .'/'. $file)){
            filecomment($dir.'/'.$file);
          }else{
            processfile($dir.'/'.$file);
          }
        }
      }
    }
  }
}

function processfile($file){
  echo $file . "\n";
  $fh = fopen($file, 'r');
  $contents = array();
  $i = 0;
  while($line = fgets($fh)){
    if ($i == 1 && trim($line) == "/*" && trim($contents[0]) == "<?php"){
      $contents[] = rtrim($line, "\r");
      $contents[] = '  $Id$'."\n";
    } else {
      $contents[] = $line;
    }
    $i++;
  }
  fclose($fh);
  $fh = fopen($file, 'w');
  foreach($contents as $key => $line){
    fwrite($fh, str_replace("\r\n", "\n", $line));
  }
  fclose($fh);
}
