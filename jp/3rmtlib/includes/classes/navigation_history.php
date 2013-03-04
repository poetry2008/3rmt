<?php
/*
  $Id$
*/

  class navigationHistory {
    var $path, $snapshot;
/*------------------------
 功能：导航历史记录 
 参数：无
 返回值：无
 -----------------------*/
    function navigationHistory() {
      $this->reset();
    }
/*----------------------
 功能：重置 
 参数：无
 返回值：无
 ---------------------*/
    function reset() {
      $this->path = array();
      $this->snapshot = array();
    }
/*---------------------
 功能：添加当前页面
 参数：无
 返回值：无
 --------------------*/
    function add_current_page() {
      global $PHP_SELF, $_GET, $_POST, $_SERVER, $cPath;

      $set = 'true';
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        if ( ($this->path[$i]['page'] == basename($PHP_SELF)) ) {
          if (isset($cPath)) {
            if (!isset($this->path[$i]['get']['cPath'])) {
              continue;
            } else {
              if ($this->path[$i]['get']['cPath'] == $cPath) {
                array_splice($this->path, ($i+1));
                $set = 'false';
                break;
              } else {
                $old_cPath = explode('_', $this->path[$i]['get']['cPath']);
                $new_cPath = explode('_', $cPath);

                for ($j=0, $n2=sizeof($old_cPath); $j<$n2; $j++) {
                  if ($old_cPath[$j] != $new_cPath[$j]) {
                    array_splice($this->path, ($i));
                    $set = 'true';
                    break 2;
                  }
                }
              }
            }
          } else {
            array_splice($this->path, ($i));
            $set = 'true';
            break;
          }
        }
      }

      if ($set == 'true') {
        $this->path[] = array('page' => basename($PHP_SELF),
                              'mode' => (isset($_SERVER['HTTPS'])?(($_SERVER['HTTPS'] == 'on') ? 'SSL' : 'NONSSL'):'NONSSL'),
                              'get' => $_GET,
                              'post' => $_POST);
      }
    }
/*-------------------------
 功能：删除当前页面
 参数：无
 返回值：无
 ------------------------*/
    function remove_current_page() {
      global $PHP_SELF;

      $last_entry_position = sizeof($this->path) - 1;
      if ($this->path[$last_entry_position]['page'] == basename($PHP_SELF)) {
        unset($this->path[$last_entry_position]);
      }
    }
/*-----------------------
 功能：设置快照 
 参数：$page(string) 分页
 返回值：无
 ----------------------*/
    function set_snapshot($page = '') {
      global $PHP_SELF, $_GET, $_POST, $_SERVER;

      if (is_array($page)) {
        $this->snapshot = array('page' => $page['page'],
                                'mode' => $page['mode'],
                                'get'  => $page['get'],
                                'post' => $page['post']);
      } else {
        $this->snapshot = array('page' => basename($PHP_SELF),
                                'mode' => (($_SERVER['HTTPS'] == 'on') ? 'SSL' : 'NONSSL'),
                                'get'  => $_GET,
                                'post' => $_POST);
      }
    }
/*---------------------------
 功能: 清除快照
 参数：无
 返回值：无
 --------------------------*/
    function clear_snapshot() {
      $this->snapshot = array();
    }
/*--------------------------
 功能：设置路径快照 
 参数：$history(string) 历史路径
 返回值：无
 -------------------------*/
    function set_path_as_snapshot($history = 0) {
      $pos = (sizeof($this->path)-1-$history);
      $this->snapshot = array('page' => $this->path[$pos]['page'],
                              'mode' => $this->path[$pos]['mode'],
                              'get' => $this->path[$pos]['get'],
                              'post' => $this->path[$pos]['post']);
    }
/*------------------------
 功能：调试导航
 参数：无
 返回值：无
 -----------------------*/
    function debug() {
      for ($i=0, $n=sizeof($this->path); $i<$n; $i++) {
        echo $this->path[$i]['page'] . '?';
        while (list($key, $value) = each($this->path[$i]['get'])) {
          echo $key . '=' . $value . '&';
        }
        if (sizeof($this->path[$i]['post']) > 0) {
          echo '<br>';
          while (list($key, $value) = each($this->path[$i]['post'])) {
            echo '&nbsp;&nbsp;<b>' . $key . '=' . $value . '</b><br>';
          }
        }
        echo '<br>';
      }

      if (sizeof($this->snapshot) > 0) {
        echo '<br><br>';

        echo $this->snapshot['mode'] . ' ' . $this->snapshot['page'] . '?' . tep_array_to_string($this->snapshot['get'], array(tep_session_name())) . '<br>';
      }
    }
/*------------------------------
 功能：反，序列化
 参数：$broken(string) 打破常规序列 
 返回值：无
 -----------------------------*/
    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }
  }
?>
