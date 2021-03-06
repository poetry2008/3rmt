<?php
/*
  $Id$


*/

  class box extends tableBlock {
/*------------------------
 功能：构造函数 
 参数：无
 返回值：无
 ------------------------*/
    function box() {
      $this->heading = array();
      $this->contents = array();
    }
/*-----------------------
 功能：信息框
 参数：$heading(string) 头部标题
 参数：$contents(string) 内容
 返回值：返回信息(string)
 -----------------------*/
    function infoBox($heading, $contents) {
      $this->table_row_parameters = 'class="infoBoxHeading"';
      $this->table_data_parameters = 'class="infoBoxHeading"';
      $this->heading = $this->tableBlock($heading);

      $this->table_row_parameters = '';
      $this->table_data_parameters = 'class="infoBoxContent"';
      $this->contents = $this->tableBlock($contents);

      return $this->heading . $this->contents;
    }
/*-------------------------
 功能：菜单框 
 参数：$heading(string) 头部标题
 参数：$contents(string) 内容
 返回值：返回菜单(string)
 ------------------------*/
    function menuBox($heading, $contents) {
      $this->table_data_parameters = 'class="menuBoxHeading"';
      if (isset($heading[0]['link']) && $heading[0]['link']) {
        $this->table_data_parameters .= ' onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . $heading[0]['link'] . '\'"';
        $heading[0]['text'] = '&nbsp;<a href="' . $heading[0]['link'] . '" class="menuBoxHeadingLink">' . $heading[0]['text'] . '</a>&nbsp;';
      } else {
        $heading[0]['text'] = '&nbsp;' . $heading[0]['text'] . '&nbsp;';
      }
      $this->heading = $this->tableBlock($heading);

      $this->table_data_parameters = 'class="menuBoxContent"';
      $this->contents = $this->tableBlock($contents);

      return $this->heading . $this->contents;
    }
  }
?>
