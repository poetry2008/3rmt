<?php
/*
 * 网页采集类
 */
class Spider {

  private $encoding = 'utf-8'; //编码
  public $url; //网页URL
  public $url_mode; //网页URL页码规则
  public $mode_array; //采集各内容的正则数组
  private $context; //设置读取页面函数的参数
  public $current_page; //当前页码
  public $page_count; //总页码
  public $curl_flag; //使用curl采集的标识
  public $collect_time; //采集时间
  public $collect_flag = true; //采集错误标识
  //private $email = '287499757@qq.com'; //from email 
  //private $admin_email = '287499757@qq.com'; //admin email

  /*----------------------
  功能：初始化类
  参数：$url(string) URL
  参数：$url_mode(string) 网页URL页码规则
  参数：$mode_array(string) 采集各内容的正则数组 
  参数：$page_num_mode(string) 获取每页显示几条数据的正则
  参数：$sum_mode(string) 获取总数据的正则
  返回值：无
  ---------------------*/
  function __construct($url,$url_mode='',$mode_array,$curl_flag=0,$page_num_mode='',$sum_mode=''){


      $this->page_count = $this->page_count($url,$page_num_mode,$sum_mode);
      $this->url = $url;
      $this->url_mode = $url_mode;
      $this->mode_array = $mode_array;
      $this->page_count = $this->page_count($url,$page_num_mode,$sum_mode);
      $opts=array('http'=> array('user_agent'=>'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.112 Safari/534.30','timeout'=>10));
      $this->context = stream_context_create($opts);
      $this->current_page = 1;
      $this->curl_flag = $curl_flag;
  }

  /*----------------------
  功能：采集内容
  参数：无
  返回值：采集后的分类数组
  ---------------------*/ 
  function fetch(){

    $result_array = array();
    if(!$this->is_connect()){

      $this->collect_flag = false;
      return $result_array;
    }
    while($this->current_page <= $this->page_count){

      /*
      //开始采集时间戳
      $start_time = $this->get_timer();
      */
      if($this->curl_flag == 0){
        $contents = file_get_contents($this->make_url($this->url,$this->url_mode,$this->current_page),false,$this->context);
      }else{
        $contents = $this->curl_get_contents($this->make_url($this->url,$this->url_mode,$this->current_page)); 
      }
      /*
      //结束采集时间戳
      $end_time = $this->get_timer();
      $this->collect_time = $this->get_collect_timer($start_time,$end_time);

      //如果采集的内容为空或者超时,给管理员发送邮件
      //if(!$contents || $this->collect_time > 10){
      if(!$contents){

        $error_subject = '错误标题'
        $error_msg = '错误内容';
        $error_headers = "From: ".$this->email ."<".$this->email.">";

        mail($this->admin_email,$error_subject,$error_msg,$error_headers);
      }
         */

      if(!$contents){
        $this->collect_flag = false;
        continue;
        //echo 'error|||'.$this->make_url($this->url,$this->url_mode,$this->current_page);
       // exit;
      }

      $contents = $this->contents_encode($contents);
      $result_array[] = $this->parse_result($contents,$this->mode_array);
      $this->current_page++;
    }
    return $result_array;
  }

  /*----------------------
  功能：根据相应的规则，生成URL
  参数：$url(string) 网页URL
  参数：$url_mode(string) 网页URL页码规则 
  参数：$page(int) 页码
  返回值：生成后的URL
  ---------------------*/ 
  function make_url($url,$url_mode,$page=1){

    if($page == 1){

      return $url;
    }else{

      return str_replace('${page}',$page,$url_mode);
    }

  }

  /*----------------------
  功能：分析采集的内容，根据正则来获取相应的数据
  参数：$contents(string) 采集的网页内容
  参数：$mode_array(string) 采集各内容的正则数组
  返回值：解析后的内容数组
  ---------------------*/ 
  function parse_result($contents,$mode_array){

    $search_array = array();
    foreach($mode_array as $key=>$value){

      preg_match_all('/'.$value.'/is',$contents,$temp_array);
      $search_array[$key] = $temp_array[1];
    }
    return $search_array;
  }

  /*----------------------
  功能：获取采集网页的总页数
  参数：$url(string) 网页URL
  参数：$page_num_mode(string) 获取每页显示几条数据的正则 
  参数：$sum_mode(string) 获取总数据的正则 
  返回值：总页数
  ---------------------*/ 
  function page_count($url,$page_num_mode,$sum_mode){

    /*
    if(!$this->is_connect()){

      $this->collect_flag = false;
      exit;
    }
     */

    if($page_num_mode == '' || $sum_mode == ''){

      return 1;
    }
    $contents = file_get_contents($url,false,$this->context);

    if(!$contents){

      echo 'error|||'.$url;
      exit;
    }
    preg_match_all('/'.$page_num_mode.'/is',$contents,$page_num_array);
    preg_match_all('/'.$sum_mode.'/is',$contents,$sum_array);

    return ceil($sum_array[1][0]/$page_num_array[1][0]);
  }

  /*----------------------
  功能：判断采集的URL是否可以正常访问
  参数：无
  返回值：true || false
  ---------------------*/ 
  function is_connect(){

    $status = get_headers($this->url);
    if (false != stripos($status[0],'200')) {

      return true;
    }else{

      return false;
    }
  }

  /*----------------------
  功能：检测采集页面内容的编码，并转码
  参数：$contents(string) 页面内容
  返回值：转码后的页面内容
  ---------------------*/
  function contents_encode($contents){

    //日文编码
    $encode_array = array('UTF-8','EUC-JP','Shift_JIS','ISO-2022-JP');
    $encode = mb_detect_encoding($contents,$encode_array);
    if(strtolower($encode) != $this->encoding){

      $contents = mb_convert_encoding($contents,$this->encoding,$encode_array);
    }
    return $contents;
  }

  /*----------------------
  功能：使用CURL采集网页
  参数：$url(string) URL 
  返回值：采集到的页面内容
  ---------------------*/
  function curl_get_contents($url){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址 
    //curl_setopt($ch,CURLOPT_HEADER,1); //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置超时  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //设置连接等待时间  
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_); //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,_REFERER_); //设置 referer 
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;
  }

  /*----------------------
  功能：开始、结束时间
  参数：无 
  返回值：时间戳
  ---------------------*/
  function get_timer(){

    return microtime();
  }

  /*----------------------
  功能：采集时间
  参数：无 
  返回值：时间(秒)
  ---------------------*/
  function get_collect_timer($start_time,$end_time){

    $time_start = explode(' ', $start_time);
    $time_end = explode(' ', $end_time);
    return number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
  }
}
?>
