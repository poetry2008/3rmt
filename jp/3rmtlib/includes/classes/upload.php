<?php
  class upload {
    var $file, $filename, $destination, $prefix, $permissions, $extensions, $tmp_filename, $message_location;
/*---------------------------
 功能：上传文件
 参数：$file(string) 文件
 参数：$destination(string) 目标
 参数：$prefix(string) 设置前缀
 参数：$permissions(string) 设置权限
 参数：$extensions(string) 设置扩展
 返回值：判断上传文件是否成功(boolean)
 --------------------------*/
    function upload($file = '', $destination = '', $prefix = '', $permissions = '777', $extensions = '') {
      $this->set_file($file);
      $this->set_destination($destination);
      $this->set_prefix($prefix);
      $this->set_permissions($permissions);
      $this->set_extensions($extensions);

      $this->set_output_messages('direct');

      if (tep_not_null($this->file) && tep_not_null($this->destination)) {
        $this->set_output_messages('session');

        if ( ($this->parse() == true) && ($this->save() == true) ) {
          return true;
        } else {
          return false;
        }
      }
    }
/*-------------------------------
 功能：解析上传的文件
 参数: 无
 返回值：判断是否解析成功(boolean)
 ------------------------------*/
    function parse() {
      global $HTTP_POST_FILES, $messageStack;

      $file = array();

      if (isset($_FILES[$this->file])) {
        $file = array('name' => str_replace(array('#','%'), array('no','percent'), $_FILES[$this->file]['name']),
                      'type' => $_FILES[$this->file]['type'],
                      'size' => $_FILES[$this->file]['size'],
                      'tmp_name' => $_FILES[$this->file]['tmp_name']);
      } elseif (isset($HTTP_POST_FILES[$this->file])) {
        $file = array('name' => str_replace(array('#','%'), array('no','percent'), $HTTP_POST_FILES[$this->file]['name']),
                      'type' => $HTTP_POST_FILES[$this->file]['type'],
                      'size' => $HTTP_POST_FILES[$this->file]['size'],
                      'tmp_name' => $HTTP_POST_FILES[$this->file]['tmp_name']);
      }

      if ( tep_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
        if (sizeof($this->extensions) > 0) {
          if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
            if ($this->message_location == 'direct') {
              $messageStack->add(ERROR_FILETYPE_NOT_ALLOWED, 'error');
  // BOF: Additional Images
            } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
              $messageStack->add_session(ERROR_FILETYPE_NOT_ALLOWED, 'error');
            }

            return false;
          }
        }

        $this->set_file($file);
        $this->set_filename($file['name']);
        $this->set_tmp_filename($file['tmp_name']);

        return $this->check_destination();
      } else {
        if ($this->message_location == 'direct') {
          $messageStack->add(WARNING_NO_FILE_UPLOADED, 'warning');
  // BOF: Additional Images
        } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
          $messageStack->add_session(WARNING_NO_FILE_UPLOADED, 'warning');
        }

        return false;
      }
    }
/*-----------------------------
 功能：提交文件 
 参数：无
 返回值：判断是否提交成功(boolean)
 ----------------------------*/
    function save() {
      global $messageStack;

      if (substr($this->destination, -1) != '/') $this->destination .= '/';

      //rename the filename
      $this->filename = $this->prefix . $this->filename;
      if (move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) {
        chmod($this->destination . $this->filename, $this->permissions);

        if ($this->message_location == 'direct') {
          $messageStack->add(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');
  // BOF: Additional Images
        } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
          $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');
        }

        return true;
      } else {
        if ($this->message_location == 'direct') {
          $messageStack->add(ERROR_FILE_NOT_SAVED, 'error');
  // BOF: Additional Images } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
          $messageStack->add_session(ERROR_FILE_NOT_SAVED, 'error');
        }

        return false;
      }
    }
/*-------------------------
 功能：设置文件 
 参数: $file(string) 文件
 返回值：无
 ------------------------*/
    function set_file($file) {
      $this->file = $file;
    }
/*-------------------------
 功能：设置目标 
 参数：$destination(string) 目标 
 返回值：无
 ------------------------*/
    function set_destination($destination) {
      $this->destination = $destination;
    }
/*------------------------
 功能：设置前缀 
 参数: $prefix(string) 前缀
 返回值：无
 ------------------------*/
    function set_prefix($prefix) {
      $this->prefix= $prefix;
    }
/*------------------------
 功能：设置权限 
 参数：$permissions(string) 权限
 返回值：无
 -----------------------*/
    function set_permissions($permissions) {
      $this->permissions = octdec($permissions);
    }
/*------------------------
 功能：设置文件名 
 参数：$filename(string) 文件名
 返回值: 无
 ------------------------*/
    function set_filename($filename) {
      $this->filename = $filename;
    }
/*-----------------------
 功能：设置TMP文件名 
 参数：$filename(string) 文件名
 返回值：无
 ----------------------*/
    function set_tmp_filename($filename) {
      $this->tmp_filename = $filename;
    }
/*----------------------
 功能: 设置扩展 
 参数：$extensions(string) 扩展
 返回值：无
 ---------------------*/
    function set_extensions($extensions) {
      if (tep_not_null($extensions)) {
        if (is_array($extensions)) {
          $this->extensions = $extensions;
        } else {
          $this->extensions = array($extensions);
        }
      } else {
        $this->extensions = array();
      }
    }
/*------------------------
 功能：检查目标文件 
 参数：无
 返回值：判断检查是否成功(boolean)
 -----------------------*/
    function check_destination() {
      global $messageStack;

      if (!is_writeable($this->destination)) {
        if (is_dir($this->destination)) {
          if ($this->message_location == 'direct') {
            $messageStack->add(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
  // BOF: Additional Images
          } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
            $messageStack->add_session(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
          }
        } else {
          if ($this->message_location == 'direct') {
            $messageStack->add(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
  // BOF: Additional Images
          } elseif ($this->message_location == 'session') {
  // EOF: Additional Images
            $messageStack->add_session(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
          }
        }

        return false;
      } else {
        return true;
      }
    }
/*-----------------------
 功能：设置输出信息 
 参数：$location(string) 位置
 返回值：无
 ----------------------*/
    function set_output_messages($location) {
      switch ($location) {
        case 'session':
          $this->message_location = 'session';
          break;
        case 'direct':
        default:
          $this->message_location = 'direct';
          break;
      }
    }
  }
  
// BOF: Additional Images

  class upload_quiet extends upload {
/*---------------------
 功能: 上传信息  
 参数：$file(string) 文件
 参数：$destination(string) 目标
 参数：$prefix(string) 设置前缀
 参数：$permissions(string) 设置权限
 参数：$extensions(string) 设置扩展
 返回值：判断上传是否成功(boolean)
 --------------------*/
    function upload_quiet($file = '', $destination = '', $prefix = '', $permissions = '777', $extensions = '') {
      $this->set_file($file);
      $this->set_destination($destination);
      $this->set_prefix($prefix);
      $this->set_permissions($permissions);
      $this->set_extensions($extensions);

      $this->message_location = 'quiet';

      if (tep_not_null($this->file) && tep_not_null($this->destination)) {

        if ( ($this->parse() == true) && ($this->save() == true) ) {
          return true;
        } else {
          return false;
        }
      }
    }
  }
// EOF: Additional Images
?>
