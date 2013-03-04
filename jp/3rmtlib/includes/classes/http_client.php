<?php
/*
  $Id$

  Minimal Example:

  $http = new httpClient();
  $http->Connect("somehost", 80) or die("Connect problem");
  $status = $http->Get("/index.html");
  if ($status != 200) {
    die("Problem : " . $http->getStatusMessage());
  } else {
    echo $http->getBody();
  }
  $http->Disconnect();

  Persistent Example:

  $http = new httpClient("dir.yahoo.com", 80);
  $http->addHeader("Host", "dir.yahoo.com");
  $http->addHeader("Connection", "keep-alive");

  if ($http->Get("/Reference/Libraries/") == 200) $page1 = $http->getBody();
  if ($http->Get("/News_and_Media/") == 200 ) $page2 = $http->getBody();
  $http->disconnect();
*/

  class httpClient {
    var $url; // array containg server URL, similar to parseurl() returned array
    var $reply; // response code
    var $replyString; // full response
    var $protocolVersion = '1.1';
    var $requestHeaders, $requestBody;
    var $socket = false;
// proxy stuff
    var $useProxy = false;
    var $proxyHost, $proxyPort;

/**
 * httpClient constructor
 * Note: when host and port are defined, the connection is immediate
 * @seeAlso connect
 **/
/*--------------------------------
 功能: HTTP 客户端 
 参数：$host(string) 主机
 参数：$port(string) 端口
 返回值：无
 -------------------------------*/
    function httpClient($host = '', $port = '') {
      if (tep_not_null($host)) {
        $this->connect($host, $port);
      }
    }

/**
 * turn on proxy support
 * @param proxyHost proxy host address eg "proxy.mycorp.com"
 * @param proxyPort proxy port usually 80 or 8080
 **/
/*-----------------------------
 功能：设置代理
 参数：$proxyHost(string) 代理主机
 参数：$proxyPort(string) 代理服务器端口
 返回值：无
 ----------------------------*/
    function setProxy($proxyHost, $proxyPort) {
      $this->useProxy = true;
      $this->proxyHost = $proxyHost;
      $this->proxyPort = $proxyPort;
    }

/**
 * setProtocolVersion
 * define the HTTP protocol version to use
 * @param version string the version number with one decimal: "0.9", "1.0", "1.1"
 * when using 1.1, you MUST set the mandatory headers "Host"
 * @return boolean false if the version number is bad, true if ok
 **/
/*-----------------------------
 功能：设置协议版本 
 参数：$version(string) 版本
 返回值：设置协议版本成功
 ----------------------------*/
    function setProtocolVersion($version) {
      if ( ($version > 0) && ($version <= 1.1) ) {
        $this->protocolVersion = $version;
        return true;
      } else {
        return false;
      }
    }

/**
 * set a username and password to access a protected resource
 * Only "Basic" authentication scheme is supported yet
 * @param username string - identifier
 * @param password string - clear password
 **/
/*------------------------------
 功能：设置验证 
 参数：$username(string) 用户名
 参数：$password(string) 密码
 返回值：无
 -----------------------------*/
    function setCredentials($username, $password) {
      $this->addHeader('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
     }

/**
 * define a set of HTTP headers to be sent to the server
 * header names are lowercased to avoid duplicated headers
 * @param headers hash array containing the headers as headerName => headerValue pairs
 **/
/*-----------------------------
 功能：设置头部标题 
 参数：$headers(string) 头部标题
 返回值：无
 ----------------------------*/
    function setHeaders($headers) {
      if (is_array($headers)) {
        reset($headers);
        while (list($name, $value) = each($headers)) {
          $this->requestHeaders[$name] = $value;
        }
      }
    }

/**
 * addHeader
 * set a unique request header
 * @param headerName the header name
 * @param headerValue the header value, ( unencoded)
 **/
/*-----------------------------
 功能：添加一个头部标题
 参数：$headerName(string) 标题名称
 参数：$headerValue(string) 标题值
 返回值：无
 ----------------------------*/
    function addHeader($headerName, $headerValue) {
      $this->requestHeaders[$headerName] = $headerValue;
    }

/**
 * removeHeader
 * unset a request header
 * @param headerName the header name
 **/
/*----------------------------
 功能：删除头部标题 
 参数：$headName(string) 头部名称
 返回值：无
 ---------------------------*/
    function removeHeader($headerName) {
      unset($this->requestHeaders[$headerName]);
    }

/**
 * Connect
 * open the connection to the server
 * @param host string server address (or IP)
 * @param port string server listening port - defaults to 80
 * @return boolean false is connection failed, true otherwise
 **/
/*-----------------------------
 功能：链接服务器 
 参数：$host(string) 主机
 参数：$port(string) 端口
 返回值：链接服务器成功
 ----------------------------*/
    function Connect($host, $port = '') {
      $this->url['scheme'] = 'http';
      $this->url['host'] = $host;
      if (tep_not_null($port)) $this->url['port'] = $port;

      return true;
    }

/**
 * Disconnect
 * close the connection to the  server
 **/
/*---------------------------
 功能：关闭链接的服务器 
 参数：无
 返回值：无
 --------------------------*/
    function Disconnect() {
      if ($this->socket) fclose($this->socket);
    }

/**
 * head
 * issue a HEAD request
 * @param uri string URI of the document
 * @return string response status code (200 if ok)
 * @seeAlso getHeaders()
 **/
/*---------------------------
 功能：头部标题
 参数：$uri(string) URL
 参数：判断是否返回头部标题
 --------------------------*/
    function Head($uri) {
      $this->responseHeaders = $this->responseBody = '';

      $uri = $this->makeUri($uri);

      if ($this->sendCommand('HEAD ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

/**
 * get
 * issue a GET http request
 * @param uri URI (path on server) or full URL of the document
 * @return string response status code (200 if ok)
 * @seeAlso getHeaders(), getBody()
 **/
/*----------------------------
 功能：获取HTTP服务请求 
 参数：$url(string) URL
 返回值：返回字符串响应状态
 ---------------------------*/
    function Get($url) {
      $this->responseHeaders = $this->responseBody = '';

      $uri = $this->makeUri($url);

      if ($this->sendCommand('GET ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

/**
 * Post
 * issue a POST http request
 * @param uri string URI of the document
 * @param query_params array parameters to send in the form "parameter name" => value
 * @return string response status code (200 if ok)
 * @example 
 * $params = array( "login" => "tiger", "password" => "secret" );
 * $http->post( "/login.php", $params );
 **/
/*------------------------------
 功能：发出POST HTTP请求
 参数：$uri(string) URL
 参数：$query_params(string) 查询参数
 返回值: 返回响应状态
 -----------------------------*/
    function Post($uri, $query_params = '') {
      $uri = $this->makeUri($uri);

      if (is_array($query_params)) {
        $postArray = array();
        reset($query_params);
        while (list($k, $v) = each($query_params)) {
          $postArray[] = urlencode($k) . '=' . urlencode($v);
        }

        $this->requestBody = implode('&', $postArray);
      }

// set the content type for post parameters
      $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');

      if ($this->sendCommand('POST ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      $this->removeHeader('Content-Type');
      $this->removeHeader('Content-Length');
      $this->requestBody = '';

      return $this->reply;
    }

/**
 * Put
 * Send a PUT request
 * PUT is the method to sending a file on the server. it is *not* widely supported
 * @param uri the location of the file on the server. dont forget the heading "/"
 * @param filecontent the content of the file. binary content accepted
 * @return string response status code 201 (Created) if ok
 * @see RFC2518 "HTTP Extensions for Distributed Authoring WEBDAV"
 **/
/*---------------------------
 功能：发出一个PUT请求  
 参数：$uri(string) URL
 参数：$filecontent(string) 文件内容
 返回值：返回响应状态
 --------------------------*/
    function Put($uri, $filecontent) {
      $uri = $this->makeUri($uri);
      $this->requestBody = $filecontent;

      if ($this->sendCommand('PUT ' . $uri . ' HTTP/' . $this->protocolVersion)) {
        $this->processReply();
      }

      return $this->reply;
    }

/**
 * getHeaders
 * return the response headers
 * to be called after a Get() or Head() call
 * @return array headers received from server in the form headername => value
 * @seeAlso get, head
 **/
/*-------------------------------
 功能：获取头部标题 
 参数：无
 返回值：返回响应的头部标题
 ------------------------------*/
    function getHeaders() {
      return $this->responseHeaders;
    }

/**
 * getHeader
 * return the response header "headername"
 * @param headername the name of the header
 * @return header value or NULL if no such header is defined
 **/
/*-------------------------------
 功能：获取头部名称
 参数：$headername(string) 头部名称
 返回值：返回响应的头部名称
 ------------------------------*/
    function getHeader($headername) {
      return $this->responseHeaders[$headername];
    }

/**
 * getBody
 * return the response body
 * invoke it after a Get() call for instance, to retrieve the response
 * @return string body content
 * @seeAlso get, head
 **/
/*--------------------------------
 功能：获取BODY 
 参数：无
 返回值：返回响应的BODY
 -------------------------------*/
    function getBody() {
      return $this->responseBody;
    }

/**
 * getStatus return the server response's status code
 * @return string a status code
 * code are divided in classes (where x is a digit)
 *  - 20x : request processed OK
 *  - 30x : document moved
 *  - 40x : client error ( bad url, document not found, etc...)
 *  - 50x : server error 
 * @see RFC2616 "Hypertext Transfer Protocol -- HTTP/1.1"
 **/
/*----------------------------
 功能：获取状态返回服务器的响应状态代码 
 参数：无
 返回值：返回字符串的状态
 ---------------------------*/
    function getStatus() {
      return $this->reply;
    }

/** 
 * getStatusMessage return the full response status, of the form "CODE Message"
 * eg. "404 Document not found"
 * @return string the message 
 **/
/*----------------------------
 功能：获取状态信息 
 参数：无
 返回值：返回状态信息
 ---------------------------*/
    function getStatusMessage() {
      return $this->replyString;
    }

/**
 * @scope only protected or private methods below
 **/

/** 
 * send a request
 * data sent are in order
 * a) the command
 * b) the request headers if they are defined
 * c) the request body if defined
 * @return string the server repsonse status code
 **/
/*-------------------------
 功能：发送请求 
 参数：$command(string) 请求
 返回值: 返回服务器的回应状态
 ------------------------*/
    function sendCommand($command) {
      $this->responseHeaders = array();
      $this->responseBody = '';

// connect if necessary
      if ( ($this->socket == false) || (feof($this->socket)) ) {
        if ($this->useProxy) {
          $host = $this->proxyHost;
          $port = $this->proxyPort;
        } else {
          $host = $this->url['host'];
          $port = $this->url['port'];
        }

        if (!tep_not_null($port)) $port = 80;

        if (!$this->socket = fsockopen($host, $port, $this->reply, $this->replyString)) {
          return false;
        }

        if (tep_not_null($this->requestBody)) {
          $this->addHeader('Content-Length', strlen($this->requestBody));
        }

        $this->request = $command;
        $cmd = $command . "\r\n";
        if (is_array($this->requestHeaders)) {
          reset($this->requestHeaders);
          while (list($k, $v) = each($this->requestHeaders)) {
            $cmd .= $k . ': ' . $v . "\r\n";
          }
        }

        if (tep_not_null($this->requestBody)) {
          $cmd .= "\r\n" . $this->requestBody;
        }

// unset body (in case of successive requests)
        $this->requestBody = '';

        fputs($this->socket, $cmd . "\r\n");

        return true;
      }
    }
/*--------------------------------
 功能：回复过程 
 参数：无
 返回值：回复的内容
 -------------------------------*/
    function processReply() {
      $this->replyString = trim(fgets($this->socket, 1024));

      if (preg_match('|^HTTP/\S+ (\d+) |i', $this->replyString, $a )) {
        $this->reply = $a[1];
      } else {
        $this->reply = 'Bad Response';
      }

//get response headers and body
      $this->responseHeaders = $this->processHeader();
      $this->responseBody = $this->processBody();

      return $this->reply;
    }

/**
 * processHeader() reads header lines from socket until the line equals $lastLine
 * @scope protected
 * @return array of headers with header names as keys and header content as values
 **/
/*----------------------------
 功能：头部过程 
 参数：$lastLine(string) 最后一行换行
 返回值：返回数组的头文件，头文件名作为值作为键名和标题内容
 ---------------------------*/
    function processHeader($lastLine = "\r\n") {
      $headers = array();
      $finished = false;

      while ( (!$finished) && (!feof($this->socket)) ) {
        $str = fgets($this->socket, 1024);
        $finished = ($str == $lastLine);
        if (!$finished) {
          list($hdr, $value) = split(': ', $str, 2);
// nasty workaround broken multiple same headers (eg. Set-Cookie headers) @FIXME 
          if (isset($headers[$hdr])) {
            $headers[$hdr] .= '; ' . trim($value);
          } else {
            $headers[$hdr] = trim($value);
          }
        }
      }

      return $headers;
    }

/**
 * processBody() reads the body from the socket
 * the body is the "real" content of the reply
 * @return string body content 
 * @scope private
 **/
/*--------------------------
 功能：读取BODY的过程 
 参数：无
 返回值：返回BODY的内容
 -------------------------*/
    function processBody() {
      $data = '';
      $counter = 0;

      do {
        $status = socket_get_status($this->socket);
        if ($status['eof'] == 1) {
          break;
        }

        if ($status['unread_bytes'] > 0) {
          $buffer = fread($this->socket, $status['unread_bytes']);
          $counter = 0;
        } else {
          $buffer = fread($this->socket, 128);
          $counter++;
          usleep(2);
        }

        $data .= $buffer;
      } while ( ($status['unread_bytes'] > 0) || ($counter++ < 10) );

      return $data;
    }

/**
 * Calculate and return the URI to be sent ( proxy purpose )
 * @param the local URI
 * @return URI to be used in the HTTP request
 * @scope private
 **/
/*----------------------------
 功能：计算并返回发送
 参数：$uri(string) URL
 返回值：返回URL用于HTTP请求中
 ---------------------------*/
    function makeUri($uri) {
      $a = parse_url($uri);

      if ( (isset($a['scheme'])) && (isset($a['host'])) ) {
        $this->url = $a;
      } else {
        unset($this->url['query']);
        unset($this->url['fragment']);
        $this->url = array_merge($this->url, $a);
      }

      if ($this->useProxy) {
        $requesturi = 'http://' . $this->url['host'] . (empty($this->url['port']) ? '' : ':' . $this->url['port']) . $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      } else {
        $requesturi = $this->url['path'] . (empty($this->url['query']) ? '' : '?' . $this->url['query']);
      }

      return $requesturi;
    }
  }
?>
