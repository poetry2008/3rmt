<?php
header("Content-Type: application/octat-stream");
header("Content-Disposition: attachment; filename=sales_report2.csv");

require("includes/jcode.phps");

$sum = 0;
while ($sr->hasNext()) {
  $info = $sr->next();
  $last = sizeof($info) - 1;
  
  // csv export
  echo date(DATE_FORMAT, $sr->showDate) . SR_SEPARATOR1 . date(DATE_FORMAT, $sr->showDateEnd) . SR_SEPARATOR1;
  echo $info[0]['order'] . SR_SEPARATOR1;
  echo $info[$last - 1]['totitem'] . SR_SEPARATOR1;
  echo (int)$info[$last - 1]['totsum'] . SR_SEPARATOR1;
  echo (int)$info[0]['shipping'] . SR_NEWLINE;
  //echo $currencies->format($info[$last - 1]['totsum']) . SR_SEPARATOR1;
  //echo $currencies->format($info[0]['shipping']) . SR_NEWLINE;

  if ($srDetail) {
    for ($i = 0; $i < $last; $i++) {
      if ($srMax == 0 or $i < $srMax) {
        // csv export
        if (is_array($info[$i]['attr'])) {
          $attr_info = $info[$i]['attr'];
          foreach ($attr_info as $attr) {
            echo jcodeconvert($info[$i]['pname'], 0, 2) . "(";
            $flag = 0;
            foreach ($attr['options_values'] as $value) {
              if ($flag > 0) {
                echo ", " . jcodeconvert($value, 0, 2);
              } else {
                echo jcodeconvert($value, 0, 2);
                $flag = 1;
              }
            }
            $price = 0;
            foreach ($attr['price'] as $value) {
              $price += $value;
            }
            if ($price != 0) {
              echo ' (';
              if ($price > 0) {
                echo "+";
              } else {
                echo " ";
              }
              echo (int)$price. ')';
			  //echo $currencies->format($price). ')';
            }
            echo ")" . SR_SEPARATOR2;
            if ($srDetail == 2) {
              echo $attr['quant'] . SR_SEPARATOR2;
              echo (int)$attr['quant'] * ($info[$i]['price'] + $price) . SR_NEWLINE;
              //echo $currencies->format( $attr['quant'] * ($info[$i]['price'] + $price)) . SR_NEWLINE;
            } else {
              echo $attr['quant'] . SR_NEWLINE;
            }
            $info[$i]['pquant'] = $info[$i]['pquant'] - $attr['quant'];
          }
        }
        if ($info[$i]['pquant'] > 0) {
          echo jcodeconvert($info[$i]['pname'],0,2) . SR_SEPARATOR2;
          if ($srDetail == 2) {
            echo $info[$i]['pquant'] . SR_SEPARATOR2;
            echo (int)$info[$i]['pquant'] * $info[$i]['price'] . SR_NEWLINE;
            //echo $currencies->format($info[$i]['pquant'] * $info[$i]['price']) . SR_NEWLINE;
          } else {
            echo $info[$i]['pquant'] . SR_NEWLINE;
          }
        }
      }
    }
  }
}

if ($srCompare > SR_COMPARE_NO) {
  $sum = 0;
  while ($sr2->hasNext()) {
    $info = $sr2->next();
    $last = sizeof($info) - 1;

    // csv export
    echo date(DATE_FORMAT, $sr2->showDate) . SR_SEPARATOR1 . date(DATE_FORMAT, $sr2->showDateEnd) . SR_SEPARATOR1;
    echo $info[0]['order'] . SR_SEPARATOR1;
    echo $info[$last - 1]['totitem'] . SR_SEPARATOR1;
    echo (int)$info[$last - 1]['totsum'] . SR_SEPARATOR1;
    echo (int)$info[0]['shipping'] . SR_NEWLINE;
    //echo $currencies->format($info[$last - 1]['totsum']) . SR_SEPARATOR1;
    //echo $currencies->format($info[0]['shipping']) . SR_NEWLINE;

    if ($srDetail) {
      for ($i = 0; $i < $last; $i++) {
        if ($srMax == 0 or $i < $srMax) {
          // csv export
          if (is_array($info[$i]['attr'])) {
            $attr_info = $info[$i]['attr'];
            foreach ($attr_info as $attr) {
              echo jcodeconvert($info[$i]['pname'],0,2) . "(";
              $flag = 0;
              foreach ($attr['options_values'] as $value) {
                if ($flag > 0) {
                  echo ", " . jcodeconvert($value,0,2);
                } else {
                  echo jcodeconvert($value,0,2);
                  $flag = 1;
                }
              }
              $price = 0;
              foreach ($attr['price'] as $value) {
                $price += $value;
              }
              if ($price != 0) {
                echo ' (';
                if ($price > 0) {
                  echo "+";
                } else {
                  echo " ";
                }
                echo $price. ')';
                //echo $currencies->format($price). ')';
              }
              echo ")" . SR_SEPARATOR2;
              if ($srDetail == 2) {
                echo $attr['quant'] . SR_SEPARATOR2;
                echo (int)$attr['quant'] * ($info[$i]['price'] + $price) . SR_NEWLINE;
                //echo $currencies->format( $attr['quant'] * ($info[$i]['price'] + $price)) . SR_NEWLINE;
              } else {
                echo $attr['quant'] . SR_NEWLINE;
              }
              $info[$i]['pquant'] = $info[$i]['pquant'] - $attr['quant'];
            }
          }
          if ($info[$i]['pquant'] > 0) {
            echo jcodeconvert($info[$i]['pname'],0,2) . SR_SEPARATOR2;
            if ($srDetail == 2) {
              echo $info[$i]['pquant'] . SR_SEPARATOR2;
              echo (int)$info[$i]['pquant'] * $info[$i]['price'] . SR_NEWLINE;
              //echo $currencies->format($info[$i]['pquant'] * $info[$i]['price']) . SR_NEWLINE;
            } else {
              echo $info[$i]['pquant'] . SR_NEWLINE;
            }
          }
        }
      }
    }
  }
}
?>
