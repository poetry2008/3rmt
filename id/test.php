<?php
  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'index_top.php');
  function not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  } # end function


  echo 'SESSION_FORCE_COOKIE_USE == false <br>';
  echo 'SESSION_FORCE_COOKIE_USE = '. SESSION_FORCE_COOKIE_USE."<br>";
  echo 'not_null($SID) do <br>';
  echo 'not_null($SIE) = '.not_null($SID)."<br>"; 
  echo '$request_type == "NONSSL" && ENABLE_SSL ==true <br >';
  echo '$request_type = '.$request_type."<br>";
  echo 'ENABLE_SSL = '.ENABLE_SSL."<br>";

  echo 'HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN . <br>';
  echo 'HTTP_COOKIE_DOMAIN = '.HTTP_COOKIE_DOMAIN."<br>";
  echo 'HTTPS_COOKIE_DOMAIN = '.HTTPS_COOKIE_DOMAIN."<br>";


  echo 'SESSION_RECREATE == "True" <br>';
  echo 'SESSION_RECREATE ='.SESSION_RECREATE."<br>";

      $_sid = '';
      if (not_null($SID)) {

        $_sid = 'not_null:';
        if (SESSION_RECREATE == 'True') {
          $_sid .= tep_session_name().'='.tep_session_id(); 
          $_sid .= '__SESSION_RECREATE == true ';
        } else {
          $_sid .= $SID;
          $_sid .= '__$SID';
        }
      } elseif (  ($request_type == 'NONSSL') && (ENABLE_SSL == true)  ) {
        $_sid = '$request_type == "NONSSL":';
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          if (SESSION_RECREATE == 'True') {
            $_sid .= tep_session_name().'='.tep_session_id(); 
            $_sid .= '__SESSION_RECREATE == true ';
          } else {
            $_sid .= '__SESSION_RECREATE == false ';
          }
        }
      }

  echo '$_sid <br>';
  echo '$_sid = '.$_sid."<br>";

  echo 'ENABLE_SSL  &&  $_SERVER["HTTP_HOST"] == substr(HTTPS_SERVER,8)  is true not
  add cmd<br><br>';
  echo "ENABLE_SSL : ".ENABLE_SSL."<br>";
  echo '$_SERVER["HTTP_HOST"] : '.$_SERVER['HTTP_HOST']."<br>";
  echo "substr(HTTPS_SERVER,8) : ".substr(HTTPS_SERVER,8)."<br>";
  

  ?>
