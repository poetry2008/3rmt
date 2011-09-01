<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License

  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('Error: Error 1', 'error');
  $messageStack->add('Error: Error 2', 'warning');
  if ($messageStack->size > 0) echo $messageStack->output();
*/

  class messageStack extends tableBlock {
    var $size = 0;

    function messageStack() {
      global $messageToStack;

      $this->errors = array();

      if (tep_session_is_registered('messageToStack')) {
        for ($i = 0, $n = sizeof($messageToStack); $i < $n; $i++) {
          $this->add($messageToStack[$i]['text'], $messageToStack[$i]['type']);
        }
        tep_session_unregister('messageToStack');
      }
    }

    function add($message, $type = 'error') {
      if ($type == 'error') {
        $this->errors[] = array('params' => 'class="messageStackError"', 'text' => tep_image(DIR_WS_ICONS . 'error.gif', ICON_ERROR) . '&nbsp;' . $message);
      } elseif ($type == 'warning') {
        $this->errors[] = array('params' => 'class="messageStackWarning"', 'text' => tep_image(DIR_WS_ICONS . 'warning.gif', ICON_WARNING) . '&nbsp;' . $message);
      } elseif ($type == 'success') {
        $this->errors[] = array('params' => 'class="messageStackSuccess"', 'text' => tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . $message);
      } else if($type == 'alert'){
        $this->errors[] = array('params' => 'class="messageStackError"', 'text' =>
            tep_image(DIR_WS_ICONS . 'error.gif', ICON_ERROR) . '&nbsp;' . $message.
            "<script language='javascript'>alert('".$message."')</script>");
      }else {
        $this->errors[] = array('params' => 'class="messageStackError"', 'text' => $message);
      }

      $this->size++;
    }

    function add_session($message, $type = 'error') {
      global $messageToStack;

      if (!tep_session_is_registered('messageToStack')) {
        tep_session_register('messageToStack');
        $messageToStack = array();
      }



      /*
      if(!isset($_SESSION['messageToStack'])||(!is_array($_SESSION['messageToStack'])&&count($_SESSION['messageToStack']>0))){
        $_SESSION['messageToStack'] = array();

      }
      $messageToStack = array();
      */
      $exists_single = false; 
      if (!empty($messageToStack)) {
        foreach ($messageToStack as $key => $value) {
          if (($value['text'] == $message) && ($value['type'] == $type)) {
            $exists_single = true; 
          }
        }
      }
      if (!$exists_single) {
        $messageToStack[] = array('text' => $message, 'type' => $type);
      }
      /*
      $_SESSION['messageToStack'] = $messageToStack;
      */
    }

    function reset() {
      $this->errors = array();
      $this->size = 0;
    }

    function output() {
      $this->table_data_parameters = 'class="messageBox"';
      return $this->tableBlock($this->errors);
    }
  }
?>
