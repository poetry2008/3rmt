<?php
/*
  $Id$
*/

  class breadcrumb {
    var $_trail;
/*--------------------------
 功能：面包屑
 参数：无
 返回值：无
 -------------------------*/
    function breadcrumb() {
      $this->reset();
    }
/*-------------------------
 功能：重置
 参数：无
 返回值：无
 ------------------------*/
    function reset() {
      $this->_trail = array();
    }
/*------------------------
 功能：添加面包屑 
 参数：$title(string) 标题
 参数：$link(string) 链接
 返回值：无
 -----------------------*/
    function add($title, $link = '') {
      $this->_trail[] = array('title' => $title, 'link' => $link);
    }
/*------------------------
 功能：追踪字符串
 参数：$separator(string) 分割字符串
 返回值：字符串(array)
 -----------------------*/
    function trail($separator = ' - ') {
      $trail_string = '';
      if(NEW_STYLE_WEB===true){
      $trail_string = "<img src='images/navbar_img.png' alt='picture' >";
      }
      for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
        
        if (isset($this->_trail[$i]['link']) && tep_not_null($this->_trail[$i]['link']) && $i != sizeof($this->_trail)-1) {
          $trail_string .= '<a href="' . $this->_trail[$i]['link'] . '" class="headerNavigation">' . $this->_trail[$i]['title'] . '</a>';
        } else {
          $trail_string .= $this->_trail[$i]['title'];
        }

        if (($i+1) < $n) $trail_string .= $separator;
      }
      return $trail_string;
    }
/*------------------------
 功能：面包屑标题
 参数：$separator(string) 分割字符串
 返回值：面包屑标题(string) 
 -----------------------*/
    function trail_title($separator = ' - ') {
      $trail_string = '';

      for ($i=0, $n=sizeof($this->_trail); $i<$n; $i++) {
        $trail_string .= $this->_trail[$i]['title'];

        if (($i+1) < $n) $trail_string .= $separator;
      }

      return $trail_string;
    }
  }
?>
