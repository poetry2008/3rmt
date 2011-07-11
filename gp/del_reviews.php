<?php
/*
  $Id$
*/

 $host_url = "localhost";

 $database_name = "maker_3rmt";
 
 $user_name = "maker";
 
 $user_pass = "123456";
  
 $con = mysql_connect($host_url, $user_name, $user_pass);

 if (!$con) {
   echo 'wrong'; 
   exit; 
 }
mysql_select_db($database_name);
mysql_query("set names utf8");
  
  $reviews_query = mysql_query("select * from reviews r where site_id = 9");
  while ($reviews_res = mysql_fetch_array($reviews_query)) {
    mysql_query("delete from reviews_description where reviews_id = '".$reviews_res['reviews_id']."'"); 
  }
mysql_query("delete from reviews where site_id = 9"); 
echo 'del success'; 
