<?php
/*
  $Id$

*/

  class tableBlock {
    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';

    function tableBlock($contents) {
      $tableBox_string = '<table border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
      if ($this->table_parameters != '') $tableBox_string .= ' ' . $this->table_parameters;
      $tableBox_string .= '>' . "\n";

      $form_set = false;
      if (isset($contents['form']) && $contents['form']) {
        echo $contents['form'] . "\n";
        $form_set = true;
        tep_array_shift($contents);
      }

      for ($i = 0, $n = sizeof($contents); $i < $n; $i++) {
        $tableBox_string .= '  <tr';
        if ($this->table_row_parameters != '') $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && $contents[$i]['params']) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>' . "\n";

        if (isset($contents[$i][0]) && is_array($contents[$i][0])) {
          for ($x = 0, $y = sizeof($contents[$i]); $x < $y; $x++) {
            if ($contents[$i][$x]['text']) {
              $tableBox_string .= '    <td';
              if ($contents[$i][$x]['align'] != '') $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
              if ($contents[$i][$x]['params']) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif ($this->table_data_parameters != '') {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if ($contents[$i][$x]['form']) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if ($contents[$i][$x]['form']) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td';
          if (isset($contents[$i]['align']) && $contents[$i]['align'] != '') $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
          if (isset($contents[$i]['params']) && $contents[$i]['params']) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif ($this->table_data_parameters != '') {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }

        $tableBox_string .= '  </tr>' . "\n";
        if (isset($contents[$i]['form']) && $contents[$i]['form']) $tableBox_string .= '</form>' . "\n";
      }

      $tableBox_string .= '</table>' . "\n";

      return $tableBox_string;
    }
  }
?>
