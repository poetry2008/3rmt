<?php
/*
  $Id$

*/

  class tableBox {
    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';

// class constructor
/*-----------------------
 功能：把内容信息放在表格中输出
 参数：$contents(string) 内容信息
 参数：$direct_output(string) 是否直接输出
 返回值：表格的字符串输出(string)
 ----------------------*/
    function tableBox($contents, $direct_output = false) {
      $tableBox_string = '<table summary="table" border="' . $this->table_border . '" width="' . $this->table_width . '" cellspacing="' . $this->table_cellspacing . '" cellpadding="' . $this->table_cellpadding . '"';
      if (tep_not_null($this->table_parameters)) $tableBox_string .= ' ' . $this->table_parameters;
      $tableBox_string .= '>' . "\n";

      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        if (isset($contents[$i]['form']) && tep_not_null($contents[$i]['form'])) $tableBox_string .= $contents[$i]['form'] . "\n";
        $tableBox_string .= '  <tr';
        if (tep_not_null($this->table_row_parameters)) $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>' . "\n";

        if (!isset($contents[$i][0])) $contents[$i][0] = NULL;
        if (is_array($contents[$i][0])) {
          for ($x=0, $n2=sizeof($contents[$i]); $x<$n2; $x++) {
            if (isset($contents[$i][$x]['text']) && tep_not_null($contents[$i][$x]['text'])) {
              $tableBox_string .= '    <td';
              if (isset($contents[$i][$x]['align']) && tep_not_null($contents[$i][$x]['align'])) $tableBox_string .= ' align="' . $contents[$i][$x]['align'] . '"';
              if (isset($contents[$i][$x]['params']) && tep_not_null($contents[$i][$x]['params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif (tep_not_null($this->table_data_parameters)) {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td';
          if (isset($contents[$i]['align']) && tep_not_null($contents[$i]['align'])) $tableBox_string .= ' align="' . $contents[$i]['align'] . '"';
          if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif (tep_not_null($this->table_data_parameters)) {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }

        $tableBox_string .= '  </tr>' . "\n";
        if (isset($contents[$i]['form']) && tep_not_null($contents[$i]['form'])) $tableBox_string .= '</form>' . "\n";
      }

      $tableBox_string .= '</table>' . "\n";

      if ($direct_output == true) echo $tableBox_string;

      return $tableBox_string;
    }
  }

  class infoBox extends tableBox {
/*-----------------------------
 功能：信息框 
 参数：$contents(string) 内容
 返回值：无
 ----------------------------*/
    function infoBox($contents) {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => $this->infoBoxContents($contents));
      $this->table_cellpadding = '1';
      $this->table_parameters = 'class="infoBox"';
      $this->tableBox($info_box_contents, true);
    }
/*---------------------------
 功能：信息框内容 
 参数：$contents(string) 内容
 返回值：信息框内容(string)
 --------------------------*/
    function infoBoxContents($contents) {
      $this->table_cellpadding = '3';
      $this->table_parameters = 'class="infoBoxContents"';
      $info_box_contents = array();
      if(NEW_STYLE_WEB===true){
        $info_box_contents[] = array(array('text' => ''));
      }
      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
  if (!isset($contents[$i]['align'])) $contents[$i]['align']=NULL;
  if (!isset($contents[$i]['form'])) $contents[$i]['form']=NULL;
        $info_box_contents[] = array(array('align' => $contents[$i]['align'],
                                           'form' => $contents[$i]['form'],
                                           'params' => 'class="boxText"',
                                           'text' => $contents[$i]['text']));
      }

      if(NEW_STYLE_WEB===true){
           $info_box_contents[] = array(array('text' => ''));

      }
      return $this->tableBox($info_box_contents);
    }
  }

  class infoBoxHeading extends tableBox {
/*-----------------------------
 功能：信息框的标题 
 参数：$contents(string) 内容
 参数：$left_corner(string) 左上
 参数：$right_corner(string) 右上
 参数：$right_arrow(string) 右箭头
 返回值：无
 ----------------------------*/
    function infoBoxHeading($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
      $this->table_cellpadding = '0';

      if ($left_corner == true) {
        $left_corner = tep_image(DIR_WS_IMAGES . 'pixel_trans.gif');
      } else {
        $left_corner = tep_image(DIR_WS_IMAGES . 'pixel_trans.gif');
      }
      if ($right_arrow == true) {
        $right_arrow = '<a href="' . $right_arrow . '">' . tep_image(DIR_WS_IMAGES . 'infobox/arrow_right.gif', ICON_ARROW_RIGHT) . '</a>';
      } else {
        $right_arrow = '';
      }
      if ($right_corner == true) {
        $right_corner = $right_arrow . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif');
      } else {
        $right_corner = $right_arrow . tep_draw_separator('pixel_trans.gif', '11', '20');
      }

      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'height="20" class="infoBoxHeading"',
                                         'text' => $left_corner),
                                   array('params' => 'width="100%" height="20" class="infoBoxHeading"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="20" class="infoBoxHeading" nowrap',
                                         'text' => $right_corner));

      $this->tableBox($info_box_contents, true);
    }
  }

  class contentBox extends tableBox {
/*--------------------------------
 功能：内容框 
 参数：$contents(string) 内容
 返回值：无
 -------------------------------*/
    function contentBox($contents) {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => $this->contentBoxContents($contents));
      $this->table_cellpadding = '1';
      $this->tableBox($info_box_contents, true);
    }
/*-------------------------------
 功能：放在内容框里面的信息 
 参数：$contents(string) 内容
 返回值：内容框里面的信息(string)
 ------------------------------*/
    function contentBoxContents($contents) {
      $this->table_cellpadding = '4';
      return $this->tableBox($contents);
    }
  }

  class contentBoxHeading extends tableBox {
/*--------------------------------
 功能：内容框标题 
 参数：$contents(string) 内容
 返回值：无
 -------------------------------*/
    function contentBoxHeading($contents) {
      $this->table_width = '100%';
      $this->table_cellpadding = '0';

      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'height="20" class="infoBoxHeading"',
                                         'text' => tep_image(DIR_WS_IMAGES . 'pixel_trans.gif')),
                                   array('params' => 'height="20" class="infoBoxHeading" width="100%"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="20" class="infoBoxHeading"',
                                         'text' => tep_image(DIR_WS_IMAGES . 'pixel_trans.gif')));

      $this->tableBox($info_box_contents, true);
    }
  }

  class errorBox extends tableBox {
/*------------------------------
 功能：错误信息 
 参数：$contents(string) 信息
 返回值：无
 -----------------------------*/
    function errorBox($contents) {
      $this->table_data_parameters = 'class="errorBox"';
      $this->tableBox($contents, true);
    }
  }
?>
