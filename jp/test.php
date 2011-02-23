<?php

var_dump( tep_get_google_adsense_adurl("http://www.google.com/?from=adwords") );
var_dump( tep_get_google_adsense_adurl("http://www.google.com/?afdfr=adwords") );

function tep_get_google_adsense_adurl($url) {
  /*
  $arr = parse_url($url);
  $q_arr = array();
  if ($arr['query']) {
    $queries = explode("&",$arr['query']);
    foreach($queries as $q) {
      $tmp = explode('=',$q);
      $q_arr[$tmp[0]] = $tmp[1];
    }
    if ($q_arr['sa'] && $q_arr['ai'] && $q_arr['adurl']) {
      return $q_arr['adurl'];
    }
    return false;
  } else {
    return false;
  }
  */
  if (
    preg_match('/(affr)|(from)=adwords/',$url)
  ) {
    return '1';
  } else {
    
    return false;
  }
}