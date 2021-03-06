<?php
/*
  $Id$
*/

  class email {
    var $html;
    var $text;
    var $output;
    var $html_text;
    var $html_images;
    var $image_types;
    var $build_params;
    var $attachments;
    var $headers;
/*------------------------------
 功能：电子邮件 
 参数：$headers(string) 头部
 参数：$site_id(string) SITE_ID值
 返回值：无
 -----------------------------*/
    function email($headers = '', $site_id = null) {
      if ($headers == '') $headers = array();
      if ($site_id) {
        $this->site_id = $site_id;
      }

      $this->html_images = array();
      $this->headers = array();

      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }

/**
 * If you want the auto load functionality
 * to find other mime-image/file types, add the
 * extension and content type here.
 */

      $this->image_types = array('gif' => 'image/gif',
                                 'jpg' => 'image/jpeg',
                                 'jpeg' => 'image/jpeg',
                                 'jpe' => 'image/jpeg',
                                 'bmp' => 'image/bmp',
                                 'png' => 'image/png',
                                 'tif' => 'image/tiff',
                                 'tiff' => 'image/tiff',
                                 'swf' => 'application/x-shockwave-flash');

      $this->build_params['html_encoding'] = 'quoted-printable';
      $this->build_params['text_encoding'] = '7bit';
      $this->build_params['html_charset']  = 'utf-8';
      $this->build_params['text_charset']  = 'utf-8';
      $this->build_params['text_wrap'] = 998;

/**
 * Make sure the MIME version header is first.
 */

      $this->headers[] = 'MIME-Version: 1.0';

      reset($headers);
      while (list(,$value) = each($headers)) {
        if (tep_not_null($value)) {
          $this->headers[] = $value;
        }
      }
    }

/**
 * This function will read a file in
 * from a supplied filename and return
 * it. This can then be given as the first
 * argument of the the functions
 * add_html_image() or add_attachment().
 */
/*---------------------------------
 功能：获取文件 
 参数：$filename(string) 文件名字
 返回值：判断是否获取成功(boolean/string)
 --------------------------------*/
    function get_file($filename) {
      $return = '';

      if ($fp = fopen($filename, 'rb')) {
        while (!feof($fp)) {
          $return .= fread($fp, 1024);
        }
        fclose($fp);

        return $return;
      } else {
        return false;
      }
    }

/**
 * Function for extracting images from
 * html source. This function will look
 * through the html code supplied by add_html()
 * and find any file that ends in one of the
 * extensions defined in $obj->image_types.
 * If the file exists it will read it in and
 * embed it, (not an attachment).
 *
 * Function contributed by Dan Allen
 */
/*-------------------------------
 功能：查找HTML的图像目录
 参数：$images_dir(string) 图像目录
 返回值：无
 -------------------------------*/
    function find_html_images($images_dir) {
// Build the list of image extensions
      while (list($key, ) = each($this->image_types)) {
        $extensions[] = $key;
      }

      preg_match_all('/"([^"]+\.(' . implode('|', $extensions).'))"/Ui', $this->html, $images);

      for ($i=0; $i<count($images[1]); $i++) {
        if (file_exists($images_dir . $images[1][$i])) {
          $html_images[] = $images[1][$i];
          $this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
        }
      }

      if (tep_not_null($html_images)) {
// If duplicate images are embedded, they may show up as attachments, so remove them.
        $html_images = array_unique($html_images);
        sort($html_images);

        for ($i=0; $i<count($html_images); $i++) {
          if ($image = $this->get_file($images_dir . $html_images[$i])) {
            $content_type = $this->image_types[substr($html_images[$i], strrpos($html_images[$i], '.') + 1)];
            $this->add_html_image($image, basename($html_images[$i]), $content_type);
          }
        }
      }
    }

/**
 * Adds plain text. Use this function
 * when NOT sending html email
 */
/*-----------------------------
 功能：添加文本
 参数：$text(string) 文本
 返回值：无
 ----------------------------*/
    function add_text($text = '') {
      $this->text = mb_convert_encoding(mb_convert_kana($text, "KV"),
          'UTF-8' );
    }

/**
 * Adds a html part to the mail.
 * Also replaces image names with
 * content-id's.
 */
/*--------------------------
 功能：添加HTML文本 
 参数：$html(string) html 文本
 参数：$text(string) 文本
 参数：$images_dir(string) 图片目录
 返回值：无
 -------------------------*/
    function add_html($html, $text = NULL, $images_dir = NULL) {
      $this->html = $html;
      $this->html_text = $text;

      if (isset($images_dir)) $this->find_html_images($images_dir);
    }

/**
 * Adds an image to the list of embedded
 * images.
 */
/*-------------------------
 功能：添加HTML图片 
 参数：$file(string) 文件
 参数：$name(string) 名字
 参数：$c_type(string) 目录路径
 返回值：无
 ------------------------*/
    function add_html_image($file, $name = '', $c_type='application/octet-stream') {
      $this->html_images[] = array('body' => $file,
                                   'name' => $name,
                                   'c_type' => $c_type,
                                   'cid' => md5(uniqid(time())));
    }

/**
 * Adds a file to the list of attachments.
 */
/*------------------------
 功能：添加到列表的附件 
 参数：$file(string) 文件
 参数：$name(string) 名字
 参数：$c_type(string) 路径
 参数：$encoding(string) 编码
 返回值：无
 -----------------------*/
    function add_attachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64') {
      $this->attachments[] = array('body' => $file,
                                   'name' => $name,
                                   'c_type' => $c_type,
                                   'encoding' => $encoding);
    }

/**
 * Adds a text subpart to a mime_part object
 */

/* HPDL PHP3 */
/*-----------------------
 功能：添加文本到mime_part对象
 参数：$obj(string) 对象 
 参数：$text(string) 文本
 返回值：返回mime_part对象(obj)
 ----------------------*/
    function add_text_part(&$obj, $text) {
      $params['content_type'] = 'text/plain';
      $params['encoding'] = $this->build_params['text_encoding'];
      $params['charset'] = $this->build_params['text_charset'];

      if (is_object($obj)) {
        return $obj->addSubpart($text, $params);
      } else {
        return new mime($text, $params);
      }
    }

/**
 * Adds a html subpart to a mime_part object
 */

/* HPDL PHP3 */
/*-----------------------------
 功能：添加HTML到mime_part对象
 参数：$obj(string) 对象
 返回值：返回mime_part对象 (obj)
 -----------------------------*/
    function add_html_part(&$obj) {
      $params['content_type'] = 'text/html';
      $params['encoding'] = $this->build_params['html_encoding'];
      $params['charset'] = $this->build_params['html_charset'];

      if (is_object($obj)) {
        return $obj->addSubpart($this->html, $params);
      } else {
        return new mime($this->html, $params);
      }
    }

/**
 * Starts a message with a mixed part
 */

/* HPDL PHP3 */
/*-------------------------------
 功能：添加混合部分信息 
 参数：无
 返回值：信息(string)
 ------------------------------*/
    function add_mixed_part() {
      $params['content_type'] = 'multipart/mixed';

      return new mime('', $params);
    }

/**
 * Adds an alternative part to a mime_part object
 */

/* HPDL PHP3 */
/*-------------------------------
 功能：添加一个替代部分到mime_part对象 
 参数：$obj(string) 对象
 返回值：mime_part对象(obj)
 ------------------------------*/
    function add_alternative_part(&$obj) {
      $params['content_type'] = 'multipart/alternative';

      if (is_object($obj)) {
        return $obj->addSubpart('', $params);
      } else {
        return new mime('', $params);
      }
    }

/**
 * Adds a html subpart to a mime_part object
 */

/* HPDL PHP3 */
/*------------------------------
 功能：添加HTML部位到mime_part对象 
 参数：$obj($obj) 对象 
 返回值：mime_part对象(obj)
 -----------------------------*/
    function add_related_part(&$obj) {
      $params['content_type'] = 'multipart/related';

      if (is_object($obj)) {
        return $obj->addSubpart('', $params);
      } else {
        return new mime('', $params);
      }
    }

/**
 * Adds an html image subpart to a mime_part object
 */

/* HPDL PHP3 */
/*-----------------------------
 功能：添加一个图片到mime_part对象 
 参数：$obj(string) 对象
 参数：$value(string) 内容类型 
 返回值：无
 ----------------------------*/
    function add_html_image_part(&$obj, $value) {
      $params['content_type'] = $value['c_type'];
      $params['encoding'] = 'base64';
      $params['disposition'] = 'inline';
      $params['dfilename'] = $value['name'];
      $params['cid'] = $value['cid'];

      $obj->addSubpart($value['body'], $params);
    }

/**
 * Adds an attachment subpart to a mime_part object
 */

/* HPDL PHP3 */
/*----------------------------
 功能：添加一个附加部分到mime_part对象 
 参数：$obj(string) 对象
 参数：$value(string) 内容类型
 ---------------------------*/
    function add_attachment_part(&$obj, $value) {
      $params['content_type'] = $value['c_type'];
      $params['encoding'] = $value['encoding'];
      $params['disposition'] = 'attachment';
      $params['dfilename'] = $value['name'];

      $obj->addSubpart($value['body'], $params);
    }

/**
 * Builds the multipart message from the
 * list ($this->_parts). $params is an
 * array of parameters that shape the building
 * of the message. Currently supported are:
 *
 * $params['html_encoding'] - The type of encoding to use on html. Valid options are
 *                            "7bit", "quoted-printable" or "base64" (all without quotes).
 *                            7bit is EXPRESSLY NOT RECOMMENDED. Default is quoted-printable
 * $params['text_encoding'] - The type of encoding to use on plain text Valid options are
 *                            "7bit", "quoted-printable" or "base64" (all without quotes).
 *                            Default is 7bit
 * $params['text_wrap']     - The character count at which to wrap 7bit encoded data.
 *                            Default this is 998.
 * $params['html_charset']  - The character set to use for a html section.
 *                            Default is iso-8859-1
 * $params['text_charset']  - The character set to use for a text section.
 *                          - Default is iso-8859-1
 */

/* HPDL PHP3 */
/*-------------------------
 功能：构建分支信息
 参数：$params(string) 建立参数
 返回值：判断构建分支信息是否成功(string)
 ------------------------*/
    function build_message($params = '') {
      if ($params == '') $params = array();

      if (count($params) > 0) {
        reset($params);
        while(list($key, $value) = each($params)) {
          $this->build_params[$key] = $value;
        }
      }

      if (tep_not_null($this->html_images)) {
        reset($this->html_images);
        while (list(,$value) = each($this->html_images)) {
          $this->html = str_replace($value['name'], 'cid:' . $value['cid'], $this->html);
        }
      }

      $null = NULL;
      $attachments = ((tep_not_null($this->attachments)) ? true : false);
      $html_images = ((tep_not_null($this->html_images)) ? true : false);
      $html = ((tep_not_null($this->html)) ? true : false);
      $text = ((tep_not_null($this->text)) ? true : false);

      switch (true) {
        case (($text == true) && ($attachments == false)):
          $message = $this->add_text_part($null, $this->text);
          break;
        case (($text == false) && ($attachments == true) && ($html == false)):
          $message = $this->add_mixed_part();

          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($text == true) && ($attachments == true)):
          $message = $this->add_mixed_part();
          $this->add_text_part($message, $this->text);

          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($html == true) && ($attachments == false) && ($html_images == false)):
          if (tep_not_null($this->html_text)) {
            $message = $this->add_alternative_part($null);
            $this->add_text_part($message, $this->html_text);
            $this->add_html_part($message);
          } else {
            $message = $this->add_html_part($null);
          }
          break;
        case (($html == true) && ($attachments == false) && ($html_images == true)):
          if (tep_not_null($this->html_text)) {
            $message = $this->add_alternative_part($null);
            $this->add_text_part($message, $this->html_text);
            $related = $this->add_related_part($message);
          } else {
            $message = $this->add_related_part($null);
            $related = $message;
          }
          $this->add_html_part($related);

          for ($i=0; $i<count($this->html_images); $i++) {
            $this->add_html_image_part($related, $this->html_images[$i]);
          }
          break;
        case (($html == true) && ($attachments == true) && ($html_images == false)):
          $message = $this->add_mixed_part();
          if (tep_not_null($this->html_text)) {
            $alt = $this->add_alternative_part($message);
            $this->add_text_part($alt, $this->html_text);
            $this->add_html_part($alt);
          } else {
            $this->add_html_part($message);
          }

          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
        case (($html == true) && ($attachments == true) && ($html_images == true)):
          $message = $this->add_mixed_part();

          if (tep_not_null($this->html_text)) {
            $alt = $this->add_alternative_part($message);
            $this->add_text_part($alt, $this->html_text);
            $rel = $this->add_related_part($alt);
          } else {
            $rel = $this->add_related_part($message);
          }
          $this->add_html_part($rel);

          for ($i=0; $i<count($this->html_images); $i++) {
            $this->add_html_image_part($rel, $this->html_images[$i]);
          }

          for ($i=0; $i<count($this->attachments); $i++) {
            $this->add_attachment_part($message, $this->attachments[$i]);
          }
          break;
      }

      if ( (isset($message)) && (is_object($message)) ) {
        $output = $message->encode();
        $this->output = $output['body'];

        reset($output['headers']);
        while (list($key, $value) = each($output['headers'])) {
          $headers[] = $key . ': ' . $value;
        }

        $this->headers = array_merge($this->headers, $headers);

        return true;
      } else {
        return false;
      }
    }

/**
 * Sends the mail.
 */
/*-------------------------
 功能：发送邮件 
 参数: $to_name(string)命名
 参数：$to_addr(string) 地址
 参数：$from_name(string) 名称
 参数：$from_addr(string) mail 地址
 参数：$subject(string) 主题
 参数：$headers(string) 头部 
 参数：$from_page(string) 页面
 返回值：判断发送邮件是否成功(string)
 ------------------------*/
    function send($to_name, $to_addr, $from_name, $from_addr, $subject = '',
        $headers = '',$from_page="",$real_from_email="") {
      // $from_name 是 "" ， $from_addr 是 "Name <someone@abc.com>" 格式的情况下
      // $from_addr 分解后，转换成纯粹的 E-mail 地址

      if (ereg("[ ]*(.+[^ ]+)[ ]*<([^@]+@[^@]+)>[ ]*", $from_addr, $a_regs)) {
        $from_name .= $a_regs[1];
        $from_addr = $a_regs[2];
      }
    //echo $subject;
      if ($subject != '') {
      if($from_page=='mail'){
      $subject = mb_convert_encoding($subject, 'UTF-8');
      }else{
      $subject = mb_convert_encoding($subject, 'ISO-2022-JP'); // 添加
      }
      if($from_page=='mail'){
      $subject = '=?UTF-8?B?'.base64_encode($subject)."?=";
      }else{
      $subject = '=?ISO-2022-JP?B?'.base64_encode(mb_convert_kana($subject, "KV"))."?=";
      }
      }
    
    if($to_name != '') {
      $to_name = mb_convert_encoding($to_name, 'UTF-8'); // 添加
        $to_addr = mb_convert_encoding($to_addr, 'UTF-8'); // 添加
    }
    
    if($from_name != '') {
      $from_name = mb_convert_encoding($from_name, 'UTF-8'); // 添加
        $from_addr = mb_convert_encoding($from_addr, 'UTF-8'); // 添加
    }
      $to    = ($to_name != '')
          ? ('"' . mb_encode_mimeheader(mb_convert_kana($to_name, "KV"), 'UTF-8') . '" <' . $to_addr . '>')
          : $to_addr;
      $from  = ($from_name != '')
          ? ('"' . mb_encode_mimeheader(mb_convert_kana($from_name, "KV"), 'UTF-8')  . '" <' . $from_addr. '>')
          : $from_addr;

      if (is_string($headers)) {
        $headers = explode($this->lf, trim($headers));
      }

      for ($i=0; $i<count($headers); $i++) {
        if (is_array($headers[$i])) {
          for ($j=0; $j<count($headers[$i]); $j++) {
            if ($headers[$i][$j] != '') {
              $xtra_headers[] = $headers[$i][$j];
            }
          }
        }

        if ($headers[$i] != '') {
          $xtra_headers[] = $headers[$i];
        }
      }

      if (!isset($xtra_headers)) {
        $xtra_headers = array();
      }

      if($real_from_email==''){
      $bounce_mail_option = '-f' . (defined('BOUNCE_EMAIL_ADDRESS') ? BOUNCE_EMAIL_ADDRESS : ($this->site_id?get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $this->site_id):STORE_OWNER_EMAIL_ADDRESS));
      }else{
      $bounce_mail_option = '-f' . $real_from_email;
      }
      if (EMAIL_TRANSPORT == 'smtp') {
        return mail($to_addr, $subject, $this->output,
          ('From: ' . $from . $this->lf . 'To: ' . $to . $this->lf . implode($this->lf, $this->headers) . $this->lf . implode($this->lf, $xtra_headers)),
          $bounce_mail_option
        );
      } else {

        return mail($to, $subject, $this->output,
          ('From: ' . $from . $this->lf . implode($this->lf, $this->headers) . $this->lf . implode($this->lf, $xtra_headers)),
          $bounce_mail_option
        );
      }
    }

/**
 * Use this method to return the email
 * in message/rfc822 format. Useful for
 * adding an email to another email as
 * an attachment. there's a commented
 * out example in example.php.
 *
 * string get_rfc822(string To name,
 *       string To email,
 *       string From name,
 *       string From email,
 *       [string Subject,
 *        string Extra headers])
 */
/*---------------------------------
 功能：返回的电子邮件 
 参数: $to_name(string)命名
 参数：$to_addr(string) 地址
 参数：$from_name(string) 名称
 参数：$from_addr(string) mail 地址
 参数：$subject(string) 主题
 参数：$headers(string) 头部 
 参数：$from_page(string) 页面
 返回值：返回电子邮件 (string)
 --------------------------------*/
    function get_rfc822($to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = '') {
// Make up the date header as according to RFC822
      $date = 'Date: ' . date('D, d M y H:i:s');
      $to = (($to_name != '') ? 'To: "' . $to_name . '" <' . $to_addr . '>' : 'To: ' . $to_addr);
      $from = (($from_name != '') ? 'From: "' . $from_name . '" <' . $from_addr . '>' : 'From: ' . $from_addr);

      if (is_string($subject)) {
        $subject = 'Subject: ' . $subject;
      }

      if (is_string($headers)) {
        $headers = explode($this->lf, trim($headers));
      }

      for ($i=0; $i<count($headers); $i++) {
        if (is_array($headers[$i])) {
          for ($j=0; $j<count($headers[$i]); $j++) {
            if ($headers[$i][$j] != '') {
              $xtra_headers[] = $headers[$i][$j];
            }
          }
        }

        if ($headers[$i] != '') {
          $xtra_headers[] = $headers[$i];
        }
      }

      if (!isset($xtra_headers)) {
        $xtra_headers = array();
      }

      $headers = array_merge($this->headers, $xtra_headers);

      return $date . $this->lf . $from . $this->lf . $to . $this->lf . $subject . $this->lf . implode($this->lf, $headers) . $this->lf . $this->lf . $this->output;
    }
  }
?>
