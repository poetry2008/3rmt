<?php
if(extension_loaded('zlib')){//检查服务器是否开启了zlib拓展
    ob_start('ob_gzhandler');
}
//特殊文件包含的JS
header('content-type:application/javascript; charset: utf-8');//注意修改到你的编码
header('cache-control: must-revalidate');
$offset = 60 * 60 * 24;//css文件的距离现在的过期时间，这里设置为一天
$expire = 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($expire);
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time() + $offset)." GMT");
//包含你的全部css文档
include('javascript/jquery.js');
if(extension_loaded('zlib')){
    ob_end_flush();//输出buffer中的内容，即压缩后的css文件
}
?>
