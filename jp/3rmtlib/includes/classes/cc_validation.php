<?php
/*
  $Id$

*/

  class cc_validation {
    var $cc_type, $cc_number, $cc_expiry_month, $cc_expiry_year;
/*--------------------------------
 功能：验证 
 参数：$number(string) 数字
 参数：$expiry_m(string) 到期的月份
 参数：$expiry_y(string) 到期的年份
 返回值：判断返回验证是否成功(string)
 -------------------------------*/
    function validate($number, $expiry_m, $expiry_y) {
      $this->cc_number = ereg_replace('[^0-9]', '', $number);

      if (ereg('^4[0-9]{12}([0-9]{3})?$', $this->cc_number)) {
        $this->cc_type = 'Visa';
      } elseif (ereg('^5[1-5][0-9]{14}$', $this->cc_number)) {
        $this->cc_type = 'Master Card';
      } elseif (ereg('^3[47][0-9]{13}$', $this->cc_number)) {
        $this->cc_type = 'American Express';
      } elseif (ereg('^3(0[0-5]|[68][0-9])[0-9]{11}$', $this->cc_number)) {
        $this->cc_type = 'Diners Club';
      } elseif (ereg('^6011[0-9]{12}$', $this->cc_number)) {
        $this->cc_type = 'Discover';
      } elseif (ereg('^(3[0-9]{4}|2131|1800)[0-9]{11}$', $this->cc_number)) {
        $this->cc_type = 'JCB';
      } elseif (ereg('^5610[0-9]{12}$', $this->cc_number)) { 
        $this->cc_type = 'Australian BankCard';
      } else {
        return -1;
      }

      if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
        $this->cc_expiry_month = $expiry_m;
      } else {
        return -2;
      }

      $current_year = date('Y');
      $expiry_y = substr($current_year, 0, 2) . $expiry_y;
      if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
        $this->cc_expiry_year = $expiry_y;
      } else {
        return -3;
      }

      if ($expiry_y == $current_year) {
        if ($expiry_m < date('n')) {
          return -4;
        }
      }

      return $this->is_valid();
    }
/*----------------------------
 功能：验证是否是有效的 
 参数：无 
 返回值：如果总额没有余数 验证则是有效的(string)
 ---------------------------*/
    function is_valid() {
      $cardNumber = strrev($this->cc_number);
      $numSum = 0;

      for ($i=0; $i<strlen($cardNumber); $i++) {
        $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
        if ($i % 2 == 1) {
          $currentNum *= 2;
        }

// Add digits of 2-digit numbers together
        if ($currentNum > 9) {
          $firstNum = $currentNum % 10;
          $secondNum = ($currentNum - $firstNum) / 10;
          $currentNum = $firstNum + $secondNum;
        }

        $numSum += $currentNum;
      }

// If the total has no remainder it's OK
      return ($numSum % 10 == 0);
    }
  }
?>
