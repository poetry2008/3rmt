<?php
/*
  $Id$
*/

  class mime {
    var $_encoding;
    var $_subparts;
    var $_encoded;
    var $_headers;
    var $_body;

/**
 * Constructor.
 * 
 * Sets up the object.
 *
 * @param $body   - The body of the mime part if any.
 * @param $params - An associative array of parameters:
 *                  content_type - The content type for this part eg multipart/mixed
 *                  encoding     - The encoding to use, 7bit, base64, or quoted-printable
 *                  cid          - Content ID to apply
 *                  disposition  - Content disposition, inline or attachment
 *                  dfilename    - Optional filename parameter for content disposition
 *                  description  - Content description
 * @access public
 */
/*--------------------------------
 功能： 设置的对象
 参数：$body(string) BODY部分
 参数：$params(string) 参数的关联数组
 返回值：无
 -------------------------------*/
    function mime($body, $params = '') {
      if ($params == '') $params = array();

// Make sure we use the correct linfeed sequence
      if (EMAIL_LINEFEED == 'CRLF') {
        $this->lf = "\r\n";
      } else {
        $this->lf = "\n";
      }

      reset($params);
      while (list($key, $value) = each($params)) {
        switch ($key) {
          case 'content_type':
            $headers['Content-Type'] = $value . (isset($charset) ? '; charset="' . $charset . '"' : '');
            break;
          case 'encoding':
            $this->_encoding = $value;
            $headers['Content-Transfer-Encoding'] = $value;
            break;
          case 'cid':
            $headers['Content-ID'] = '<' . $value . '>';
            break;
          case 'disposition':
            $headers['Content-Disposition'] = $value . (isset($dfilename) ? '; filename="' . $dfilename . '"' : '');
            break;
          case 'dfilename':
            if (isset($headers['Content-Disposition'])) {
              $headers['Content-Disposition'] .= '; filename="' . $value . '"';
            } else {
              $dfilename = $value;
            }
            break;
          case 'description':
            $headers['Content-Description'] = $value;
            break;
          case 'charset':
            if (isset($headers['Content-Type'])) {
              $headers['Content-Type'] .= '; charset="' . $value . '"';
            } else {
              $charset = $value;
            }
            break;
        }
      }

// Default content-type
      if (!isset($_headers['Content-Type'])) {
        $_headers['Content-Type'] = 'text/plain';
      }

// Assign stuff to member variables
      $this->_encoded = array();
/* HPDL PHP3 */
//      $this->_headers  =& $headers;
      $this->_headers = $headers;
      $this->_body = $body;
    }

/**
 * encode()
 * 
 * Encodes and returns the email. Also stores
 * it in the encoded member variable
 *
 * @return An associative array containing two elements,
 *         body and headers. The headers element is itself
 *         an indexed array.
 * @access public
 */
/*------------------------
 功能：编码和返回的电子邮件
 参数：无
 返回值：返回一个关联数组 BODY和headers
 -----------------------*/
    function encode() {
/* HPDL PHP3 */
//      $encoded =& $this->_encoded;
      $encoded = $this->_encoded;

      if (tep_not_null($this->_subparts)) {
        $boundary = '=_' . md5(uniqid(tep_rand()) . microtime());
        $this->_headers['Content-Type'] .= ';' . $this->lf . chr(9) . 'boundary="' . $boundary . '"';

// Add body parts to $subparts
        for ($i=0; $i<count($this->_subparts); $i++) {
          $headers = array();
/* HPDL PHP3 */
//          $tmp = $this->_subparts[$i]->encode();
          $_subparts = $this->_subparts[$i];
          $tmp = $_subparts->encode();

          reset($tmp['headers']);
          while (list($key, $value) = each($tmp['headers'])) {
            $headers[] = $key . ': ' . $value;
          }

          $subparts[] = implode($this->lf, $headers) . $this->lf . $this->lf . $tmp['body'];
        }

        $encoded['body'] = '--' . $boundary . $this->lf . implode('--' . $boundary . $this->lf, $subparts) . '--' . $boundary.'--' . $this->lf;
      } else {
        $encoded['body'] = $this->_getEncodedData($this->_body, $this->_encoding) . $this->lf;
      }

// Add headers to $encoded
/* HPDL PHP3 */
//      $encoded['headers'] =& $this->_headers;
      $encoded['headers'] = $this->_headers;

      return $encoded;
    }

/**
 * &addSubPart()
 * 
 * Adds a subpart to current mime part and returns
 * a reference to it
 *
 * @param $body   The body of the subpart, if any.
 * @param $params The parameters for the subpart, same
 *                as the $params argument for constructor.
 * @return A reference to the part you just added. It is
 *         crucial if using multipart/* in your subparts that
 *         you use =& in your script when calling this function,
 *         otherwise you will not be able to add further subparts.
 * @access public
 */

/* HPDL PHP3 */
//    function &addSubPart($body, $params) {
/*-------------------------------------
 功能：添加一个部分
 参数：$body(string) BODY部分
 参数：$params(string) 参数的部分
 返回值：返回新添加的部分
 ------------------------------------*/
    function addSubPart($body, $params) {
      $this->_subparts[] = new mime($body, $params);

      return $this->_subparts[count($this->_subparts) - 1];
    }

/**
 * _getEncodedData()
 * 
 * Returns encoded data based upon encoding passed to it
 *
 * @param $data     The data to encode.
 * @param $encoding The encoding type to use, 7bit, base64,
 *                  or quoted-printable.
 * @access private
 */
/*---------------------------------
 功能：获取编码的数据 
 参数：$data(string) 数据编码
 参数：$encoding(string) 编码累心
 返回值：无
 --------------------------------*/
    function _getEncodedData($data, $encoding) {
      switch ($encoding) {
       case '7bit':
         return $data;
         break;
       case 'quoted-printable':
         return $this->_quotedPrintableEncode($data);
         break;
       case 'base64':
         return rtrim(chunk_split(base64_encode($data), 76, $this->lf));
         break;
      }
    }

/**
 * quoteadPrintableEncode()
 * 
 * Encodes data to quoted-printable standard.
 *
 * @param $input    The data to encode
 * @param $line_max Optional max line length. Should 
 *                  not be more than 76 chars
 *
 * @access private
 */
/*-----------------------------
 功能：使用quoted-printable标准对数据进行编码 
 参数：$input(string) 数据进行编码
 参数：$line_max(string) 最大的长度  不能超过76个字符
 返回值：返回编码过的数据
 ----------------------------*/
    function _quotedPrintableEncode($input , $line_max = 76) {
      $lines = preg_split("/\r\n|\r|\n/", $input);
      $eol = $this->lf;
      $escape = '=';
      $output = '';

      while (list(, $line) = each($lines)) {
        $linlen = strlen($line);
        $newline = '';

        for ($i = 0; $i < $linlen; $i++) {
          $char = substr($line, $i, 1);
          $dec = ord($char);

// convert space at eol only
          if ( ($dec == 32) && ($i == ($linlen - 1)) ) {
            $char = '=20';
          } elseif ($dec == 9) {
// Do nothing if a tab.
          } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
            $char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
          }

// $this->lf is not counted
          if ((strlen($newline) + strlen($char)) >= $line_max) {
// soft line break; " =\r\n" is okay
            $output .= $newline . $escape . $eol;
            $newline = '';
          }
          $newline .= $char;
        }
        $output .= $newline . $eol;
      }
// Don't want last crlf
      $output = substr($output, 0, -1 * strlen($eol));

      return $output;
    }
  }
?>
