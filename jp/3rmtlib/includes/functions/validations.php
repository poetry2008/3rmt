<?php
/*
   $Id$

 */

////////////////////////////////////////////////////////////////////////////////////////////////
//
// Function    : tep_validate_email
//
// Arguments   : email   email address to be checked
//
// Return      : true  - valid email address
//               false - invalid email address
//
// Description : function for validating email address that conforms to RFC 822 specs
//
//               This function is converted from a JavaScript written by
//               Sandeep V. Tamhankar (stamhankar@hotmail.com). The original JavaScript
//               is available at http://javascript.internet.com
//
// Sample Valid Addresses:
//
//    first.last@host.com
//    firstlast@host.to
//    "first last"@host.com
//    "first@last"@host.com
//    first-last@host.com
//    first.last@[123.123.123.123]
//
// Invalid Addresses:
//
//    first last@host.com
//
//
////////////////////////////////////////////////////////////////////////////////////////////////
function tep_validate_email($email) {
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex) {
    $isValid = false;
  } else {
    $domain = substr($email, $atIndex+1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64) {
      // front @ length 
      $isValid = false;
    } else if ($domainLen < 1 || $domainLen > 255) {
      // back @  length 
      $isValid = false;
    } else if ($local[0] == '.') {
      // dot at start or end
      $isValid = false;
    } else if (!preg_match('/^[\]\\:[A-Za-z0-9\\-\\.]+$/', $domain)) {
      // character not valid in domain part
      $isValid = false;
    } else if (preg_match('/\\.\\./', $domain)||preg_match('/^\./',$domain)) {
      // domain part has two consecutive dots
      $isValid = false;
    } else if(!preg_match('/^(\\\\."|[\(\)\<\>\[\]\:\;\,A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
          str_replace("\\\\","",$local))) {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/',
            str_replace("\\\\","",$local))) {
        $isValid = false;
      }
    }
    if ($isValid && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
      if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
        $isValid = false;
      }
    }
  }
  return $isValid;
}

?>
