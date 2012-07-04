<?php
/*
  $Id$
*/

  class breadcrumb {
    var $_trail;

    function breadcrumb() {
      $this->reset();
    }

    function reset() {
      $this->_trail = array();
    }

    function add($title, $link = '') {
      $this->_trail[] = array('title' => $title, 'link' => $link);
    }

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
