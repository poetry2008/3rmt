<?php
//采集脚本
ini_set("display_errors", "Off");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
set_time_limit(0);

//file patch
require('includes/configure.php');
require_once('class/spider.php');

//link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_query('set names utf8');
mysql_select_db(DB_DATABASE);

//kakaran网站采集
$kakaran_array = array('http://rmt.kakaran.jp/ragnarok/','http://rmt.kakaran.jp/redstone/','http://rmt.kakaran.jp/lineage2/');

//获取游戏分类
$game_type = $_POST['game'];
$flag_check = $_POST['flag'];
if($flag_check!= ''){
  //在show里面点击更新
   $game_type = $game_type == '' ? 'FF11' : $game_type;
   $category = array('buy','sell');
   get_contents_main($game_type,$category,'');

}



function get_contents_main($game_type,$category,$site){
  /*
   * jp 游戏各网站采集
   */
  $site_str = array();
  $url_str_array = array();
  $category_id_str_array = array();
  $url_kaka_array = array();
  /*以下是区分是手动更新的还是后台自动执行更新的判断
   * 买卖是数组是手动更新的,相反就是后台自动更新的
   * */
  //site
  $site_query = mysql_query("select site_id from site order by site_id asc");
  $i = 0;
  $j = 0;
  while($site_array = mysql_fetch_array($site_query)){

    $category_query = mysql_query("select * from category where site_id='".$site_array['site_id']."' and category_name='".$game_type."' and game_server='jp'");
    while($category_array = mysql_fetch_array($category_query)){

      if($category_array['category_type'] == 1){

        $url_str_array['buy'][$i] = $category_array['category_url'];
        $category_id_str_array['buy'][$i] = $category_array['category_id'];
        $site_str['buy'][] = $i;
        $i++;
      }else{
       
        $url_str_array['sell'][$j] = $category_array['category_url'];
        $category_id_str_array['sell'][$j] = $category_array['category_id'];
        $site_str['sell'][] = $j;
        $j++;
      }
    } 
  }
if(!is_array($category)){
	if($category==1){
	    $category_tep = 'buy';
	}else{
	    $category_tep ='sell';
	}
    $category_type = array($category_tep);
	$x=0;
    $category_query = mysql_query("select * from category where site_id='".$site."' and category_type=".$category." and category_name='".$game_type."' and game_server='jp'");
    $category_array =  mysql_fetch_array($category_query);
    foreach($url_str_array[$category_tep] as $k => $v){
      if($v == $category_array['category_url']){
        $x= $k;
        break;
      }
    }
    $url_str_array[$category_tep][$x] = $category_array['category_url'];
    $category_id_str_array[$category_tep][$x] = $category_array['category_id'];
    $site_str[$category_tep][] = $x;
}else{
  $category_type = $category;
}
/*以下是正式采集*/
$game_type=$game_type;

  foreach($category_type as $category_value){
/*
    if($category_value == 'buy'){ 
      $url_array = $url_str_array['buy'];
      $category_id_array = $category_id_str_array['buy'];
      $site = $site_str['buy'];
      switch($game_type){ 
        case 'FF14':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+).*?\-rmt<\/td>',
                        '6-20'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '21-500'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-29'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-59'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '60-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?L?E?G?A?C?Y?\)?<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のギル販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'RO':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+?)<\/td>',
                        '10-99'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '100-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?.*?\)?<\/span><\/td>',
                      '1-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),

                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゼニー販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+).*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;
        case 'RS':
          $search_array = array(
            array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9]*?\s*?([\sa-zA-Z]+?)\s*?<\/td>',
                        '1-29'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '30-10000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-19'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '5-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-199'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '200-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),

                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td class="center">.*?([\sa-zA-Z]+?).*?<\/td> ',
                        'price'=>'<td class="center" nowrap="nowrap" colspan="3">.*?<span style="font-weight: bold;">(.*?)<\/span>.*?<\/td>',
                        'inventory'=>'<td class="center" nowrap="nowrap" colspan="3">.*?<span style="font-weight: bold;">.*?<\/span>.*?<\/td><td class="center" nowrap="nowrap">(.*?)<\/td>'
                        ),
                    array('products_name'=>'<td align="left" bgcolor="#D6ECFC">([\sa-zA-Z]+?)<\/td>',
                          '10-49'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '50-99'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '100-199'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td>',
                          '200-'=>'<td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">[0-9,]+&nbsp;円<\/td><td align="center">([0-9,]+)&nbsp;円<\/td>',
                          'inventory'=>'<td align="left" bgcolor="#D6ECFC">[\sa-zA-Z]+?<\/td><td align="center">(.*?)<\/td>'
                        
                        ),
                    
                    array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                          'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">(.*?)円<\/td>',
                          'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">(.*?) 口<\/td>'
                       ),
                    array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>',
                          '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '30-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>',
                          'inventory'=>'<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>'
                       ),
                   array('products_name'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">(.*?)<\/a><\/td>',
                           '1-99'=>'<td class=.*?>([0-9,.]+)円<\/td><td class=.*?>[0-9,.]+円<\/td><td class=.*?>.*?円<\/td><td class=.*?>.*?WM<\/td>', 
                           '100'=>'<td class=.*?>[0-9,.]+円<\/td><td class=.*?>([0-9,.]+)円<\/td><td class=.*?>.*?円<\/td><td class=.*?>.*?WM<\/td>',
                           'inventory'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">.*?<\/a><\/td>.*?<td class=.*? style="cursor:pointer;"><a href=".*?" onClick=".*?">(.*?)<\/a><\/td>'
                        ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  );
       break;
       case 'FF11':
         $search_array = array(  
                   array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>\s*?([\sa-zA-Z]+?) rmt*?<\/td>',
                        '1-99'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '100-10000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)口<\/td>'
                      ), 
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>.*?<td class="center">.*?円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+).*?<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-99'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '100-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のギル販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                   array(),
                   array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">([a-zA-Z]+)<\/a>.*?<\/td>',
                      '10-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '100-199'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '200-299'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                     '300-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="center">.*?<a href=".*?">.*?<\/a>.*?<\/td><td align="center"><a href=".*?"><b style="text-decoration:none;">(.*?)&nbsp;口<\/b><\/a><\/td>'
                          ),
                                             
                 array('products_name'=>'<td class="left" .*?><a href=".*?">.*?([a-zA-Z]+).*?<\/a><\/td>',
                      'price'=>'<td class="left" .*?><a href=".*?">.*?<\/a>.*?<\/td>.*?<td .*?>([0-9.,]+).*?<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">.*?([0-9,]+) 口.*?<\/td>' 
                    ),
                    array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '1-2'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '3-4'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '5-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                    'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class=.*? rowspan="2" width=200>(.*?)<\/td>',
                           'price'=>'<td class=.*? rowspan="2" width=200>.*?<\/td>.*?<td class=.*? rowspan="2" width=100>.*?<\/td><td class=.*?>.*?([0-9.,]+)円.*?<\/td>',
                           'inventory'=>'<td class=.*? rowspan="2" width=200>.*?<\/td>.*?<td class=.*? rowspan="2" width=100>.*?([0-9.,]+)&nbsp;口.*?<\/td>'
                        ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  );
          break;
       case 'DQ10':
          $search_array = array(
                  array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '51-100'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '101-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                  array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                  array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                   array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">.*?([0-9,]+口).*?<br\/>.*?<a .*?>.*?<img.*?>.*?<\/td>' 
                    ),
                  array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ), 
                  array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z0-9]+のゴールド販売)<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  array('products_name'=>'<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
                  array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
          	      'inventory'=>'<td align="left" bgcolor="#D6ECFC">.*?<\/td>.*?<td align="center">(.*?)<\/td>'
                    ),
                  array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">([0-9,]+)円<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9,]+) 口<\/td>' 
                    ),
                  array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '50-149'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '150-299'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '300-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td .*?>.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+).*?<\/td>' 
                    ),
                  array('products_name'=>'<tr><td class=buygmnr2 style="cursor:pointer;"><a .*?>(.*?)<\/a><\/td>',
                      '1-99'=>'<td class=buygmnr2>([0-9,]+)円<\/td><td class=buygmnr2>.*?円<\/td><td class=buygmnr2>.*?円<\/td>',
                      '100-'=>'<td class=buygmnr2>.*?円<\/td><td class=buygmnr2>([0-9,]+)円<\/td><td class=buygmnr2>.*?円<\/td>',
	     	      'inventory'=>'<td class=buygmnr2 style="cursor:pointer;"><a .*?>([0-9.,]+&nbsp;口)<\/a><\/td>.*?<td class=buygmnr2>.*?円<\/td>' 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  );
          break;
      case 'L2':
          $search_array = array(
                     array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9１２３４５鯖]+(.*?)rmt<\/td>',
                          '5-30'=>'<td class=\'border03 border04\'>([0-9,.]+)円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td><td class=\'border03 border04\'>[0-9,.]+円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td>',
                          '31-500'=>'<td class=\'border03 border04\'>[0-9,.]+円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td><td class=\'border03 border04\'>([0-9,.]+)円<span style=\'margin-right:5px\'>.*?<\/span>.*?WM<\/td>',
                          'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'                       
                       ),
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-19'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<tr class=".*?">.*?<td rowspan="3"><span>(.*?)<\/span><\/td>',
                          '1-19'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9.,]+)円<\/td><td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td>',
                          '20-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9.,]+円<\/td><td>([0-9.,]+)円<\/td><td>[0-9.,]+円<\/td>',
                          '50-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td><td>([0-9.,]+)円<\/td>',
                           'inventory'=>'<td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td><td>[0-9.,]+円<\/td>.*?<td rowspan="3">([0-9,.]+)口<br\/>'
                      ),
                     array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                           'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9]+) 円.*?<\/td>',
                           'inventory'=>'<td class="col2">.*?WM.*?<\/td>.*?<td class="col2">([0-9]+) 口.*?<\/td>'
                         ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            '1'=>'<td class="price">[0-9,.]+円<\/td><td class="price">([0-9,.]+)PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            '2'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?([0-9,.]+)<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            '3'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">([0-9,.]+)円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                             ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のアデナ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),

                    array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style="color: #CC3333; font-weight: bold;">.*?<\/span>.*?<\/td>',                
                          '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '10-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>',
                          '20-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*?<\/td>',
                          'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style="color: #CC3333; font-weight: bold;">(.*?)<\/span>.*?<\/td>'
                        ),
                        
                    array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-19'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '20-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
					  'inventory'=>'<a href=".*?">.*?<b .*?>(.*?)<\/b>.*?<\/a>' 
                    ),
            
                    array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                        'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td .*?>([0-9]+)円<\/td>',
                        'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9]+) 口<\/td>'
                        ),
                    array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                    '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '30-99'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '100-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" colspan="2" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+(.*?)<\/a><\/td> <td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td>',
                        '1-5'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td><td class=.*?>([0-9]+)円<\/td><td class=.*?>[0-9]+円<\/td>',
                        '6'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+&nbsp;口<\/a><\/td><td class=.*?>[0-9]+円<\/td><td class=.*?>([0-9]+)円<\/td>',
                        'inventory'=>'<td class=.*? style="cursor:pointer;"><a onClick=".*?">[0-9]+.*?<\/a><\/td> <td class=.*? style="cursor:pointer;"><a onClick=".*?">([0-9]+)&nbsp;口<\/a><\/td>'
                        ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                    ),
                  );
          break;
      case 'ARAD':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-24'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '25-49'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金貨販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
          break;
      case 'nobunaga':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-49'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の貫販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
          break;
        case 'PSO2':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のメセタ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
          break;
        case 'L1':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">.*?<br>(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ), 
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のアデナ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<tr .*?>.*?<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '10-29'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '30-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<tr .*?>.*?<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
          break;
        case 'TERA':
          $search_array = array(

		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
		    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),

                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;

        case 'AION':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-9'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '10-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のギーナ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のギーナ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;

        case 'CABAL':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のAzl販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'WZ':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '1-50'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '51-5000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;

        case 'latale':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のエリー販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td class="left" style=".*?"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="left" style=".*?"><a href=".*?">.*?<\/a><\/td>.*?<td style=".*?">([0-9,]+)円<\/td>',
                      'inventory'=>'<td style=".*?">.*?WM<\/td>.*?<td style=".*?">([0-9,]+) 口<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;
        case 'blade':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金販売<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">(.*?)<\/a>.*?<\/td>',
                      '1-4'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '5-9'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '10-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<a href=".*?"><b style=".*?">([0-9,]+)&nbsp;口<\/b><\/a>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;

        case 'megaten':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9.,] (.*?)-rmt<\/td>',
                        '1-100'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '101-9999'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のマッカ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;
        case 'EWD':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>.*?<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のED販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'LH':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">[0-9.,]+円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>.*?<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のスター販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'HR':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>(.*?)<\/td>',
                        '1-10'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-2000'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">[0-9,]+円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'AA':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                      array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>.*?<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'ThreeSeven':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">[0-9]+ WM.*?<\/td>.*?<td class="col2">([0-9]+) 口.*? <\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)販売<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'ECO':
          $search_array = array(
                      array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>.*?<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td align="left" bgcolor="#D6ECFC">(.*?)<\/td>',
                      '10-49'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '100-299'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      '300-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="left" bgcolor="#D6ECFC">.*?<\/td>.*?<td align="center">(.*?)<\/td>' 
                    ),
                  );
        break;
        case 'FNO':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>.*?_(.*?)<\/td>',
                        '1-10'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-100'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                    array('products_name'=>'<td class="col0"><a href=".*?">.*?_(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">([0-9,.]*?) 円 .*?<\/td>',
                      'inventory'=>'<td class="col2">[0-9,.]*? WM .*?<\/td>.*?<td class="col2">([0-9,.]*?) 口 .*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'SUN':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のハイム販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'talesweave':
          $search_array = array(
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のSeed販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'MU':
          $search_array = array(
                      array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<img class="middle" .*?>.*?<a class="bold" href=".*?">(.*?)の宝石販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'C9':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド販売<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'MS':
          $search_array = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+(.*?)<\/td>',
                        '5-10'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '11-500'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td height=\'24\' class=\'border03 border04\'>[0-9,.]+.*?<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                    array('products_name'=>'<td rowspan="3"><span>(.*?)<\/span><\/td>',
                      '1-19'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '20-49'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '50-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),

                   array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,]+)円<\/td>.*?<td>[0-9,]+PT<\/span><\/td>',
                      'inventory'=>'<td>.*?円<\/span><\/td>.*?<td>([0-9,]+)<\/span>口<\/td>.*?<td class="price">.*?<a href=".*?">.*?<\/a>.*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のメル販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  array('products_name'=>'<td align="left" bgcolor="#D6ECFC"><a href=".*?">[0-9,.]+(.*?)<\/a>.*?<\/td>',
                      '20-49'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+)&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td>',
                      '50-99'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+)&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td>',
                      '100-'=>'<td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">[0-9]+&nbsp;円<\/td><td align="center">([0-9]+&nbsp;)円<\/td>',
                      'inventory'=>'<td align="center"><a href=".*?"><b style=".*?">([0-9,.]+)&nbsp;口<\/b><\/a><\/td>'
                      ),
                  );
        break;
        case 'cronous':
          $search_array = array(
                      array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<img class="middle" .*?>.*?<a class="bold" href=".*?">(.*?)のクロ販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'tenjouhi':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-49'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '50-99'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '100-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の銀銭販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'rose':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のジュリー販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'hzr':
          $search_array = array(
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'dekaron':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のDIL販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'fez':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+)<br>.*?<\/A><\/td>',
                      '1-19'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '20-29'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '30-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?)<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'lakatonia':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      '1-2'=>'<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '3-4'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>',
                      '5-'=>'<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">.*?円<\/td>.*?<td width="70" class="txt_12" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="85" class="txt_12" align="center">.*?WM<\/td>.*?<td width="60".*?>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'moe':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td align="left" bgcolor="#D6ECFC">.*?<a href=".*?">([a-zA-Z]+)<\/a>.*?<\/td>',
                      '5-9'=>'<td align="center">.*?円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '10-49'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td>',
                      '50-'=>'<td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">[0-9.,]+&nbsp;円<\/td><td align="center">([0-9.,]+)&nbsp;円<\/td>',
                      'inventory'=>'<td align="left" bgcolor="#D6ECFC"><a href=".*?">.*?<\/td>.*?<td align="center"><a href=".*?"><b .*?>(.*?)&nbsp;口<\/b><\/a><\/td>' 
                    ),
                  );
        break;
        case 'mabinogi':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のGold販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'WF':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-24'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '25-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+)&nbsp;.*?<\/td>' 
                    ),
                  );
        break;
        case 'rohan':
          $search_array = array(
                    array('products_name'=>'<td class="col0"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="col0"><a href=".*?">.*?<\/a><\/td>.*?<td class="col2">(.*?)<\/td>',
                      'inventory'=>'<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">.*?<\/td>.*?<td class="col2">(.*?) 口.*?<\/td>.*?<td class="col99"><a href=".*?">.*?<\/a><\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のクロン販売<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
        case 'genshin':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">([0-9,]+).*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金販売<\/a>',
                      'price'=>'<p>1個([0-9,.]+)円から<\/p>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<td class="center" rowspan="4">(.*?)<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" rowspan="4">.*?<\/td>.*?<td class="center" rowspan="4" nowrap="nowrap">.*?([0-9,.]+).*?<\/td>' 
                    ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                   array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>', 
                            'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                            '0'=>'<td class="price">([0-9,.]+)円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                            'inventory'=>'<td class="price">[0-9,.]+円<\/td><td class="price">[0-9,.]+PT<\/td><td class="price">.*?[0-9,.]+<\/td><td class="price">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        ),
                  );
        break;


      }
  }else{
 
    $url_array = $url_str_array['sell'];
    $category_id_array = $category_id_str_array['sell'];
    $site = $site_str['sell'];
    switch($game_type){
      case 'FF14':
        $search_array = array(array(),
                    array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">([0-9,.]+)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th"><?a? ?h?r?e?f?=?"?.*?"?>?([a-zA-Z]+)\(?L?E?G?A?C?Y?\)?<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のギル買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'RO':
        $search_array = array(array(),
                      array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th">([a-zA-Z]+).*?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゼニー買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'RS':
        $search_array = array(array(),
                    array('products_name'=>'<th class="rowheader">.*?([^>]*?)<br>.*?<\/th>',
                      'price'=>'<td class="center">([0-9,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                    array(),
                    array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>',
                            'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>.*?<td>.*?<\/span>口<\/td>',
                            'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>(.*?)<\/span>口<\/td>'
                       ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z\s]+)のゴールド買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td class="center">.*?([\sa-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                            'price'=>'<td class="center" colspan="3">.*?<span style="font-weight: bold;">(.*?)<\/span>.*?<\/td>',
                            'inventory'=>'<td class="center">.*?([\sa-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>'
                       ),
                    array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                          '5-'=>'<span class="sell_serverPrice" title=".*?" style="width:33%"  onmouseover=".*?" onmouseout=".*?">(.*?)&nbsp;円<\/span>',
                          'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>'                        
                        ),

                    array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                           'price'=>'<td class="left"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]*?)円<\/td>',
                           'inventory'=>'<td>[0-9,.]*?円.*?<\/td><!-- <td>0 WM<\/td> --><td>([0-9,.]*?) 口<\/td>'                        
                        ),
                    array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>',
                           '1-9'=>'<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>', 
                           '10-29'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                           '30-'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                           'inventory'=>'<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,.]*?)&nbsp;口.*?<\/td>'
                        ),
                    array(),
                  );
    break;
    case 'FF11':
          $search_array = array(
                    array(),
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+).*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),

                    array('products_name'=>'<td class="th"><?.*?>?([^>]*?)<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                    array(),
                    array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のギル買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array(),
                    array('products_name'=>'<span class="sell_serverName" style="width:33%">&nbsp;(.*?)<\/span>.*?<span class="sell_serverBlank">&nbsp;<\/span>',
                    '1-4'=>'<span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">[0-9,.]+&nbsp;円<\/span>',   
                    '5-'=>'<span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">[0-9,.]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:20%"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span>',
                    'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
                    
                    array('products_name'=>'<td class="left"><a href=".*?">([a-zA-Z]+)<br \/>.*?<\/a>.*?<\/td>',
                            'price'=>'<td class="left"><a href=".*?">.*?<\/a>.*?<\/td>.*?<td>([0-9,]+)円<\/td>',
                            'inventory'=>'<td>.*?円<\/td>.*?<td>([0-9,]+) 口<\/td>'
                         ),

                    array('products_name'=>'<tr .*?>.*?<td class="center">.*?([a-zA-Z]+).*?<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込<br \/>',
                      '1-2'=>'<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>',
                      '3-4'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>',
                      '5-'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)円<br \/>.*?<\/td>',
	      'inventory'=>'<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?[0-9,]+円<br \/>.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
                    array(),
                  );
          break; 
    case 'DQ10':
          $search_array = array(
		    array(),
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">(.*?)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th"><?.*?>?([^>]*?)<?\/?a?>?<\/td>',
                      'price'=>'<td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>([0-9,.]+ 口).*?<\/td>' 
                    ),
	       	    array(),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z\s0-9]+のゴールド買取)<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<td class="center">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                      '1-19'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>',
                      '20-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>',
                      'inventory'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9,]+)&nbsp;口.*?<\/td>' 
                    ),
                   array('products_name'=>'<span class="sell_serverName" style="width:21%">&nbsp;(.*?)<\/span>',
                      '1-9'=>'<span class="sell_serverPrice" .*?>(.*?)円<\/span><span .*?>.*?円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      '10-49'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>(.*?)円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      '50-99'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>.*?円<\/span><span.*?>(.*?)円<\/span><span .*?>.*?円<\/span>',
                      '100-'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span .*?>.*?円<\/span><span.*?>.*?円<\/span><span .*?>.*?円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span><\/li>' 
                    ),
                   array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>([0-9,]+) 口<\/td>' 
                    ),
                   array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                      '1-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '50-149'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '150-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                      'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+&nbsp;口).*?<\/td>' 
                    ),
                  );
          break; 
     case 'L2':
         $search_array = array(
                      array(),
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<tr class=".*?">.*?<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                          'price'=>'<tr class=".*?">.*?<td class="th"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)円<\/td>',
                          'inventory'=>'<td>[0-9,.]+Pt<\/td>.*?<td>([0-9,.]+) 口.*?<\/a><\/td>'
                      ),
                    array(),
                    array('products_name'=>'<tr>.*?<td><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?[0-9]+<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>'
                      ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のアデナ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<td class="center" rowspan="3">(.*?)<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style=".*?">.*?<\/span>.*?<\/td>',
                         '1-9'=>'<td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td>',
                         '10-19'=>'<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td>',
                         '20-'=>'<td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?[0-9]+円<br \/>.*? <\/td><td class="center" nowrap="nowrap">.*?([0-9]+)円<br \/>.*? <\/td>',
                         'inventory'=>'<td class="center" rowspan="3">.*?<\/td>.*?<td class="center" rowspan="3" nowrap="nowrap">.*?<span style=".*?">(.*?)<\/span>.*?<\/td>'                        
                        ),
                    array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" title=".*?" style=".*?"  onmouseover=".*?" onmouseout=".*?">([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
                    array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a>.*?<\/td>',
                          'price'=>'<td>([0-9]+)円<\/td><!-- <td>.*? WM<\/td> --><td>[0-9]+ 口<\/td>',
                          'inventory'=>'<td>[0-9]+円<\/td><!-- <td>.*? WM<\/td> --><td>([0-9]+) 口<\/td>'
                        ),
                    array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>',
                    '1-9'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      'inventory'=>'<td class="center" colspan="2" nowrap="nowrap">.*?<\/td>.*?<td class="center" nowrap="nowrap">(.*?)<\/td>' 
                    ),
                    
                    array(),
                  );
         break;
         case 'ARAD':
           $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金貨買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),  
                  ); 
           break;
        case 'nobunaga':
           $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の貫買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),  
                  ); 
         break;
        case 'PSO2':
           $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のメセタ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),  
                  );
           break;
        case 'L1':
           $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">.*?<br>(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ), 
                    array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のアデナ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),  
                    array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>.*?銀行振込.*?<\/td>',
                     'price'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)&nbsp;口.*?<\/td>' 
                    ),
                  );
        break;	
        case 'TERA':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">(.*?)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),

                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'AION':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のギーナ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のギーナ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;

        case 'CABAL':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のAzl買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'WZ':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;

      case 'latale':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のエリー買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<td class="left"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,]+)円<\/td>',
                      'inventory'=>'<td>([0-9,]+) 口<\/td>' 
                    ),
                  );
      break;
      case 'blade':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金買取<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<span class="sell_serverName" style="width:33%">&nbsp;([^"]*?)<\/span>',
                      '1-9'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span><span class="sell_serverPrice" .*?>.*?円<\/span>',
                      '10-'=>'<span class="sell_serverPrice" .*?>.*?円<\/span><span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>' 
                    ),
                  );
      break;
      case 'megaten':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のマッカ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'EWD':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">.*?([0-9,.]+).*?M募集.*?<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のED買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'LH':
          $search_array = array(
		    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<td class="center">([0-9.,]+)円<\/td>.*?<td class="center">.*?<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のスター買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
        case 'HR':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                        'price'=>'<td class="center">([0-9,]+)円<\/td>',
                        'inventory'=>'<td class="center">[0-9,]+円<\/td>.*?<td class="center">.*?([0-9,]+).*?<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド買取<\/a>',
                       'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                       'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
        break;
      case 'AA':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">([a-zA-Z]+).*?<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のG買取<\/a>',
                      'price'=>'<p>.*?個([0-9,.]+)円から<\/p>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'ThreeSeven':
          $search_array = array(
                     array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)買取<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                    
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'ECO':
          $search_array = array(
                    array( 'products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                    'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                    'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/li>' 
                    ),
                  );
      break;
        case 'FNO':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
        break;
      case 'SUN':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のハイム買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'talesweave':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のSeed買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'MU':
          $search_array = array(
                  array('products_name'=>'<th class="rowheader">(.*?)<\/th>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                   array('products_name'=>'<a class="bold" href=".*?">(.*?)の宝石買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'C9':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド買取<\/a>',
                      'price'=>'<td class="main" align="right">.*?<p>1個([0-9,.]+)円から<\/p>.*?<\/td>',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'MS':
        $search_array = array(
                    array('products_name'=>'<td class="th"><a href=".*?">(.*?)<\/a><\/td>',
                      'price'=>'<td>([0-9,.]+)円<\/td>',
                      'inventory'=>'<td>[0-9,.]+?円<\/td>.*?<td>[0-9,.]+?Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),

                    array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                          'price'=>'<td class="price"><a href=".*?">.*?([0-9]+)<\/a><\/td>',
                         'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のメル買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<span class="sell_serverName" style="width:21%">&nbsp;[1,2,3,4,50-9.]+(.*?)<\/span>',
                      '1-19'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '20-49'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '50-99'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span>',
                      '100-'=>'<span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">[0-9]+&nbsp;円<\/span><span class="sell_serverPrice" title=".*?" style="width:13%"  onmouseover=".*?" onmouseout=".*?">([0-9]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/span>'
                     ),
                      );
      break;
      case 'cronous':
          $search_array = array(
                  array('products_name'=>'<th class="rowheader">(.*?)<\/span>.*?<\/th>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                   array('products_name'=>'<a class="bold" href=".*?">(.*?)のクロ買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'tenjouhi':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の銀銭買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
      break;
      case 'rose':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のジュリー買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'hzr':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'dekaron':
          $search_array = array(
                      array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のDIL買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'fez':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">([a-zA-Z]+)<br>.*?<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
                  );
      break;
      case 'lakatonia':
          $search_array = array(
                    array('products_name'=>'<td width="110" class="txt_11" align="center"><a href=".*?">.*?\((.*?)\)<\/A><\/td>',
                      'price'=>'<td width="50" class="txt_11" align="center">(.*?)円<\/td>',
                      'inventory'=>'<td width="60" class="txt_11" align="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のG買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ), 
           );
      break;
      case 'moe':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">([a-zA-Z]+)のゴールド買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<span class="sell_serverName" style="width:40%">&nbsp;(.*?)<\/span>',
                      'price'=>'<span class="sell_serverPrice" .*?>([0-9,.]+)&nbsp;円<\/span>',
                      'inventory'=>'<span class="sell_serverTotal" style="width:20%">(.*?)<\/li>' 
                    ),
           );
      break;
      case 'mabinogi':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                     'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                     'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のGold買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
            );
      break;
      case 'WF':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のゴールド買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                      '1-24'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td .*?>.*?<\/td>.*?<td .*?>.*?<\/td>',
                      '25-49'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>',
                      '50-'=>'<td class="center" nowrap="nowrap">.*?銀行振込.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?円.*?<\/td>.*?<td class="center" nowrap="nowrap">.*?([0-9.,]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)&nbsp;口.*?<\/td>' 
                    ),
                  );
      break;
      case 'rohan':
          $search_array = array(
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)のクロン買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                  );
      break;
      case 'genshin':
          $search_array = array(
                    array('products_name'=>'<a href=".*?"><font style="color:black;"><u><font style="color:black;">(.*?)<\/font><\/u><\/font><\/a>',
                      'price'=>'<th class="rowheader">.*?<\/th>.*?<td class="center">(.*?)円<\/td>',
                      'inventory'=>'<td class="center">.*?円<\/td>.*?<td class="center">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td><a href=".*?">(.*?)<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                      'price'=>'<td class="price"><a href=".*?">.*?([0-9,.]+)<\/a>.*?<\/td>',
                      'inventory'=>'<td class="price"><a href=".*?">.*?<\/a><\/td>.*?<td>([0-9,.]+)<\/span>口<\/td>' 
                    ),
                    array('products_name'=>'<a class="bold" href=".*?">(.*?)の金買取<\/a>',
                      'price'=>'<span class="productSpecialPrice">([0-9,.]+)円<\/span>&nbsp;から',
                      'inventory'=>'<p>残り&nbsp;<b>([0-9,]+)<\/b>&nbsp;個<\/p>' 
                    ),
                    array('products_name'=>'<tr .*?>.*?<td class="center">(.*?)<\/td>.*?<td .*?>',
                     'price'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+)円.*?<\/td>',
                     'inventory'=>'<td class="center" nowrap="nowrap">.*?([0-9,.]+&nbsp;口).*?<\/td>' 
                    ),
                  );
      break;



    }
  }

*/
include_once('collect_match.php');
  //开始采集数据
  $curl_flag = 0;
  foreach($site as $site_value){
    if($url_array[$site_value] == ''){continue;}
 //   if(strpos($url_array[$site_value],'www.iimy.co.jp')){continue;}
//将网站转换成主站地址,方便gamelife 测试使用
  if(strpos($url_array[$site_value],'www.iimy.co.jp')){
     $url_array[$site_value]= str_replace('www.iimy.co.jp','192.168.160.200',$url_array[$site_value]);
  }
   if(strpos($url_array[$site_value],'pastel-rmt.jp')||strpos($url_array[$site_value],'www.rmt-king.com')||strpos($url_array[$site_value],'192.168.100.200')){$curl_flag=0;}else{$curl_flag=1;}
    if($url_array[$site_value]=='//http://rmtrank.com/777town+index.htm'){
      $url_array[$site_value] = str_replace('//http://rmtrank.com/777town+index.htm','http://rmtrank.com/777town+index.htm',$url_array[$site_value]);
      $result = new Spider($url_array[$site_value],'',$search_array[$site_value],$curl_flag);
      $result_array = $result->fetch();
    }else{
      $result = new Spider($url_array[$site_value],'',$search_array[$site_value],$curl_flag);
      $result_array = $result->fetch();
   }
    //处理kakaran
    if($result_array[0]['url']){
      $url_kaka_array[] = 'rmt.kakaran.jp'.$site_value;
      //取出单价
      $kaka_array = array();
      foreach($result_array[0]['url'] as $key=>$url){
          if($url==''){continue;}
          $result_kaka = new Spider("rmt.kakaran.jp".$url,'',$search_array[5],$curl_flag);
          $result_array_kaka = $result_kaka->fetch();
          //选三个最小的数据
          $inventorys_array = $result_array_kaka[0]['inventory'];
          $result_array_kaka = array($result_array_kaka[0][0]);
         $result_array_kakas = array();
         foreach($result_array_kaka as $k=>$kaka){
             foreach($kaka as $keyk=>$kk){
                $kk =str_replace(',','',$kk);
                $result_array_kakas[$k][$keyk]['price'] = $kk;
                 $kkk =str_replace(',','',$inventorys_array[$keyk]);
                $result_array_kakas[$k][$keyk]['inventory'] = $kkk;
             }
          }
          $prices_array = array();
        $kaka_array = array();
          foreach($result_array_kakas as $val){
             foreach($val as $v){
               if($v['inventory'] !=0){
                     $prices_array[] = $v['price'];
                     $kaka_array[] = $v;
               }
            } 
          }
          array_multisort($prices_array, SORT_ASC,$kaka_array);
          $kaka_key = count($url_kaka_array)-1;
          $result_array[0][prices][] =  $kaka_array[$kaka_key]['price'];
          $result_array[0][inventory][] = $kaka_array[$kaka_key]['inventory'];
/* if(in_array($site_value,array(5,2,1))){
          $result_array[0][prices][] =  $kaka_array[0]['price'];
          $result_array[0][inventory][] = $kaka_array[0]['inventory'];
         
          } 
 if(in_array($site_value,array(7,12,4))){
           $result_array[0][prices][] =  $kaka_array[1]['price'];
           $result_array[0][inventory][] = $kaka_array[1]['inventory'];
         }
if(in_array($site_value,array(8,13,5))){

           $result_array[0][prices][] =  $kaka_array[2]['price'];
           $result_array[0][inventory][] = $kaka_array[2]['inventory'];
   }
*/
     }

   }
echo $url_array[$site_value];
echo $site_value;
var_dump($result_array);
//将ip地址重新转换成域名形式
  if(strpos($url_array[$site_value],'192.168.160.200')){
     $url_array[$site_value]= str_replace('192.168.160.200','www.iimy.co.jp',$url_array[$site_value]);
  }


    $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$category_id_array[$site_value]."'");

    //kakaran L2处理
    if(in_array($url_array[$site_value],$kakaran_array)){

      $url_search_array = array('site_name'=>'<td class="trader">(.*?)<\/td>',
                                'price'=>'<td class="price sort">(.*?)円<\/td>',
                                'inventory'=>'<td class="stock">(.*?)<\/td>' 
                              );
      //排除10RMT
      $rmt_name = array('ジャックポット','ゲームマネー','ワールドマネー','itemdepot','カメズ','学園','FF14-RMT','レッドストーン','ゲームプラネット','GM-Exchange');
      $rmt_url = array('http://www.iimy.co.jp/','http://www.gamemoney.cc/','http://rmt.worldmoney.jp/','http://www.itemdepot.jp/','http://www.rmt-kames.jp/','http://www.rmtgakuen.jp/','http://www.redstone-rmt.com/','http://www.ff14-rmt.com/','http://www.gameplanet.jp/','http://www.gm-exchange.jp/');
      foreach($result_array[0]['url'] as $url_key=>$url_value){

        $result_url = new Spider('http://rmt.kakaran.jp'.$url_value.'?s=bank_transfer','',$url_search_array,1);
        $result_url_array = $result_url->fetch();
        unset($result_array[0]['url']);
        foreach($result_url_array[0]['site_name'] as $site_name_key=>$site_name_value){
          preg_match_all('/<a.*?>(.*?)<\/a>/is',$site_name_value,$result_site_array);
          preg_match_all('/<a href=".*?\?t=(.*?)">.*?<\/a>/is',$site_name_value,$result_site_temp_array);
          if($result_site_array[1][0] != ''){

            $site_flag = false;
            foreach($rmt_url as $key=>$value){

              if(strpos($value,$result_site_temp_array[1][0])){

                $site_flag = true; 
                break;
              }
            }
            if(!in_array($result_site_array[0],$rmt_name) && $site_flag == false){

              $result_array[0]['price'][$url_key] = $result_url_array[0]['price'][$site_name_key];
              $result_array[0]['inventory'][$url_key] = $result_url_array[0]['inventory'][$site_name_key];
              break;
            }
          }else{
           
            if(!in_array($site_name_value,$rmt_name)){

              $result_array[0]['price'][$url_key] = $result_url_array[0]['price'][$site_name_key];
              $result_array[0]['inventory'][$url_key] = $result_url_array[0]['inventory'][$site_name_key];
              break;
            }
          } 
        }
      }
    }

    $result_array[0]['products_name'] = array_unique($result_array[0]['products_name']);
//当获取的数据商品名称为空(或这个页面没有数据)
if(empty($result_array[0]['products_name'])){
  mysql_query("delete from product where category_id='".$category_id_array[$site_value]."'");
}
    foreach($result_array[0]['products_name'] as $product_key=>$value){
        if($site_value == 0){//夢幻
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          switch($game_type){
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                    $price = $result_array[0]['1-99'][$product_key]*10; 
                  }else{
                    $price = $result_array[0]['100-10000'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-99'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }
              $result_str = $price;
            break;
            case 'DQ10':
             if(!strpos($value,'通取')){
               $value = '';
	     }else{
               $value = 'DQ10';
                 if($inventory_array[0] != ''){
                    if($inventory_array[0] >= 101){
                        $price = $result_array[0]['101-9999'][$product_key]; 
                    }else{
                        $price = $result_array[0]['51-100'][$product_key]; 
                    } 
                    $result_inventory = $inventory_array[0];
                 }else{
                    $price = $result_array[0]['51-100'][$product_key]; 
                    $result_inventory = 0;
                 }
                 $result_str = $price;
	    }

              break;
            case 'RS':
               if(strpos($result_array[0]['inventory'][$product_key],'a')){
                  $inventory_array[0]=0;
               }
               $value = str_replace('Ecplise','Eclipse',$value);

              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 30 && $inventory_array[0] <=10000){

                  $price = $result_array[0]['30-10000'][$product_key]; 
                }else{
                  $price = $result_array[0]['1-29'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-29'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
             case 'L2':
              if(strpos($result_array[0]['inventory'][$product_key],'a')){
  		  $inventory_array[0]=0;
              }
              if($inventory_array[0] != ''){
                if($inventory_array[0] >=5 && $inventory_array[0] <=30){
                  $price = $result_array[0]['5-30'][$product_key];
                }else{
                  $price = $result_array[0]['31-500'][$product_key];
                }
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['5-30'][$product_key];
                $result_inventory = 0; 
              }
              $result_str = $price;
              break;
            case 'FF14':
if(strpos($result_array[0]['inventory'][$product_key],'a')){
    $inventory_array[0]=0;
 }
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 21 && $inventory_array[0] <=500){

                  $price = $result_array[0]['21-500'][$product_key]; 
                }else{
                  $price = $result_array[0]['6-20'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['6-20'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
              break;
            case 'RO':
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 100 && $inventory_array[0] <=9999){

                  $price = $result_array[0]['100-9999'][$product_key]; 
                }else{
                  $price = $result_array[0]['10-99'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0]/100;
              }else{
                $price = $result_array[0]['10-99'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*100;
              break;
             case 'ARAD':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
              }
              break;
             case 'nobunaga':
              $price = $result_array[0]['price'][$product_key];
              if($category_value == 'sell'){
                $result_str = $price;
                if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]/10;
                }else{
                    $result_inventory = 0; 
                } 
              }else{
                $result_str = $price*10;
                if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0; 
                } 
              }
              break;
             case 'PSO2':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
            case 'L1':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
              break;
            case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10000;
              }else{
                $result_inventory = 0; 
              } 
              break;

           case 'AION':
             $value = str_replace('）','',$value);
             $tep_arr=explode('（',$value);
             $str_explode = ' ';
                   $value=$tep_arr[1].$str_explode.$tep_arr[0];
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;

            case 'CABAL':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'WZ':
             if($category_value == 'buy'){
              $value = str_replace('　','',$value);
              if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=50){
                   $price = $result_array[0]['1-50'][$product_key]; 
                 }else{
                   $price = $result_array[0]['51-5000'][$product_key]; 
                 } 
                 $result_inventory = $inventory_array[0]/10;

               }else{
                 $price = $result_array[0]['1-50'][$product_key]; 
                 $result_inventory = 0;
               }
                 $result_str = $price*10;

             }else{
               $price = $result_array[0]['price'][$product_key];
               $result_str = $price;
               $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'latale':
             $value = str_replace('ダイア','ダイヤ',$value);
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price*10;
                  if($result_array[0]['inventory'][$product_key] != ''){
                    $result_inventory = $result_array[0]['inventory'][$product_key]/1000;
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'blade':
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price*10;
                  if($result_array[0]['inventory'][$product_key] != ''){
                    $result_inventory = $result_array[0]['inventory'][$product_key]/10;
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'megaten':
             if($category_value == 'buy'){

               $value = str_replace('　','',$value);
               if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=100){
                  $price = $result_array[0]['1-100'][$product_key]; 
                }else{
                  $price = $result_array[0]['101-9999'][$product_key]; 
                } 
                 $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-100'][$product_key]; 
                  $result_inventory = 0;
                }
                $result_str = $price*10;

             }else{
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'EWD':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price*10;
              if($result_array[0]['inventory'][$product_key]!= ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'LH':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($result_array[0]['inventory'][$product_key]!= ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'HR':
             if($category_value == 'buy'){

               if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=10){
                   $price = $result_array[0]['1-10'][$product_key]; 
                 }else{
                   $price = $result_array[0]['11-2000'][$product_key]; 
                 } 
                   $result_inventory = $inventory_array[0];
               }else{
                    $price = $result_array[0]['1-10'][$product_key]; 
                    $result_inventory = 0;
               }
                   $result_str = $price;

             }else{
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'AA':
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]/100;
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'ThreeSeven':
                if($category_value == 'buy'){
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
                 }else{
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price/10;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]*10;
                  }else{
                    $result_inventory = 0; 
                  }

                }
              break;
             case 'ECO':
                  preg_match('/[0-9,]+(口|M)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'FNO':
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=10){
                   $price = $result_array[0]['1-10'][$product_key]; 
                 }else{
                   $price = $result_array[0]['11-100'][$product_key]; 
                 } 
                   $result_inventory = $inventory_array[0]*10;
                  $result_str = $price/10;
              break;
             case 'SUN':
                   $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/100;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'talesweave':
                   $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'MU':
                preg_match_all("|<a.*?><font.*?><u.*?><font .*?>(.*?)<\/font><\/u><\/font><\/a>|",$value,$temp_array);
                if(!empty($temp_array[1][0])){
                  $value = $temp_array[1][0].'の'.$temp_array[1][1];
                }else{
                  $value = str_replace('祝福','の祝福',$value); 
                }
                   $price = $result_array[0]['price'][$product_key]; 
          preg_match('/[0-9,]+(口|M)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'C9':
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;
              break;
             case 'MS':
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 5 && $inventory_array[0] <=10){
                        $price = $result_array[0]['5-10'][$product_key]; 
                     }else{
                        $price = $result_array[0]['11-500'][$product_key]; 
                     } 
                        $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['5-10'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'cronous':
                preg_match_all("|<a.*?><font.*?><u.*?><font .*?>(.*?)<\/font><\/u><\/font><\/a>|",$value,$temp_array);
                if($temp_array[0][0]!=''){
                    $value=  'アルテミス';
                }
                if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/10000;
                }else{
                     $result_inventory = 0;
                } 
                   $result_str = $result_array[0]['price'][$product_key]*10;
              break;
             case 'tenjouhi':
    
	       if(strpos($result_array[0]['inventory'][$product_key],'img')){
   	  	  $inventory_array[0]=0;
		 }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                        $price = $result_array[0]['1-49'][$product_key]; 
                     }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                        $price = $result_array[0]['50-99'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['100-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['1-49'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'rose':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'hzr':
               if($category_value == 'buy'){
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/10;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price*10;
               }
              break;
             case 'dekaron':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price*10;
              break;
             case 'fez':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                        $price = $result_array[0]['1-19'][$product_key]; 
                     }else if($inventory_array[0] >= 20 && $inventory_array[0] <=29){
                        $price = $result_array[0]['20-29'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['30-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0];
                 }else{
                     $price = $result_array[0]['1-19'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;

               }
              break;
             case 'lakatonia':
		if(strpos($result_array[0]['inventory'][$product_key],'img')){
   		   $inventory_array[0]=0;
		 }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=2){
                        $price = $result_array[0]['1-2'][$product_key]; 
                     }else if($inventory_array[0] >= 3 && $inventory_array[0] <=4){
                        $price = $result_array[0]['3-4'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['5-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['1-2'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'mabinogi':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'rohan':
                    $value = str_replace('連合サーバー','統合',$value);
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0];
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'genshin':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;

         case 'moe':
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
		        $price = $result_array[0]['5-9'][$product_key];
		  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
 		      $price = $result_array[0]['10-49'][$product_key];
                  }else{
		      $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0]/10;
 
              }else{
 		$price = $result_array[0]['5-9'][$product_key];
                $result_inventory = 0;
              }
             $result_str = $price*10;
            }
         break;
     
         case 'rohan':
         $value = str_replace('サーバー','',$value);
        // $value = str_replace('連合サーバー','統合',$value);
         break;
          }
        }else if($site_value == 1){//マツブシ
          $result_str = $result_array[0]['price'][$product_key];
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $result_array[0]['inventory'][$product_key];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
          switch($game_type){ 
            case 'FF11': 
               if($category_value == 'buy'){
                  $result_inventory =$inventory_array[0]/10;
                  $price = $result_array[0]['price'][$product_key]*10;
               }else{
                  $result_inventory =$inventory_array[0]/100;
                  $price = $result_array[0]['price'][$product_key];
               }
               $result_str = $price;
            break;
           case 'DQ10':
               $value = 'DQ10';
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
              }
          break;
          case 'RS':
             $result_inventory = $inventory_array[0]/100;
          break;
          case 'L2':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              }
          break;
            case 'FF14':
		if(strpos($result_array[0]['inventory'][$product_key],'img')){
   		   $inventory_array[0]=0;
		 }
              $result_inventory = $inventory_array[0]/10;
            break;
          case 'ARAD':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=24){

                    $price = $result_array[0]['1-24'][$product_key]; 
                  }else if($inventory_array[0] >= 25 && $inventory_array[0] <=49){
                    $price = $result_array[0]['25-49'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-24'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
           break;
           case 'nobunaga':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0]/10;
              }
              $result_str = $price*10;
          break;
          case 'PSO2':
              $result_str = $result_array[0]['price'][$product_key];
              $result_inventory = $result_array[0]['inventory'][$product_key];
          break;
          case 'RO':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*100;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              }
         break;
         case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
			
              }
         break;
         case 'AION':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
           $tep_arr=explode('_',$value);
           $str_explode = ' ';
           $value=$tep_arr[1].$str_explode.$tep_arr[0];
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $result_str = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $result_str = $result_array[0]['10-29'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_str = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }

         break;
	 case 'CABAL':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['100-'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
              }
         break; 
         case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
			
	      }
         break; 
	 case 'WZ':
              if($category_value == 'buy'){
                 $result_inventory = str_replace(',','',$inventory_array[0]); 
                 $price = $result_array[0]['price'][$product_key]; 
                 $result_str = $price;
                 if($inventory_array[0] != ''){
                   $result_inventory = $result_inventory/10;
                 }else{
                    $result_inventory = 0; 
	         }
               }else{
                  if($inventory_array[0] != ''){
                    $price = $result_array[0]['price'][$product_key]; 
                    $result_inventory = $inventory_array[0];
                  }else{
                    $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = 0;
                  }
                   $result_str = $price;

               }
         break;
         case 'latale':
             $value = str_replace('ダイア','ダイヤ',$value);
             $value = str_replace('ァイヤ','ァイア',$value);
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
         break; 
	 case 'blade':
         if($category_value == 'buy'){

              $price = $result_array[0]['prices'][$product_key]; 
              $result_str = $price;
            if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
         }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
            if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
          }
         break; 
	 case 'megaten':
            if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
            }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
            }
        break; 
	case 'EWD':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'LH':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){

                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['100-'][$product_key]*10; 
                  } 
                   $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
            //如果没有库存就会是一张图片
                if(strpos($result_array[0]['inventory'][$product_key],'img')){
                    $result_str = $result_array[0]['1-49'][$product_key]*10;
                    $result_inventory = 0;
                 }
              }else{
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
              }
         break; 
	 case 'HR':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
        case 'AA':
            $result_inventory = str_replace(',','',$inventory_array[0]); 
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
			
		}
        break;
        case 'ThreeSeven':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'FNO':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($result_array[0]['inventory'][$product_key] != ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'SUN':
              preg_match_all("|.*?([0-9,]+).*?口.*?|",$result_array[0]['inventory'][$product_key],$temp_array);
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $temp_array[1][0]/10;
              }else{
                $result_inventory = 0; 
	      }
         break;
	 case 'MU':
           if($category_value == 'buy'){
             $value = $value.'の祝福';
           }
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
	  case 'MS':
           if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 

                  $price = str_replace(',','',$price); 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-19'][$product_key]*10; 
                  $result_inventory = 0;
                }
           }else{
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
            }
 	  break;
	 case 'cronous':
              $price = $result_array[0]['price'][$product_key]; 
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
	      }
              $result_str = $price*100;
        break; 
	case 'rose':
           if($category_value == 'buy'){
             $value = str_replace('デネブ','Deneb',$value);
             $value = str_replace('ベガ','Vega',$value);
           }
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'dekaron':
           if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
	case 'fez':
             $value = str_replace('イシュルド','Ishuld',$value);
             $value = str_replace('エレミア','Jeremiah',$value);
             $value = str_replace('ケテル','Kether',$value);
             $value = str_replace('ザイン','Zayin',$value);
           if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
        case 'moe':
           
	if(strpos($result_array[0]['inventory'][$product_key],'img')){
   	   $inventory_array[0]=0;
         }
           $inventory_array[0]=$result_array[0]['inventory'][$product_key];
           if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
                    $price = $result_array[0]['5-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                    $price = $result_array[0]['10-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 

                  $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['5-9'][$product_key]*10; 
                  $result_inventory = 0;
                }
           }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
	case 'WF':
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
           if($inventory_array[0] != ''){
              if($inventory_array[0] >= 1 && $inventory_array[0] <=24){
                  $price = $result_array[0]['1-24'][$product_key]; 
              }else if($inventory_array[0] >= 25 && $inventory_array[0] <=49){
                  $price = $result_array[0]['25-49'][$product_key]; 
              }else{
                  $price = $result_array[0]['50-'][$product_key]; 
              } 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
           }else{
              $result_str = $result_array[0]['1-24'][$product_key]*10; 
              $result_inventory = 0;
           }
        break; 
	case 'genshin':
           $inventory_array[0] = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
             if($category_value == 'buy'){
                $price = $result_array[0]['prices'][$product_key]; 
             }else{
                $price = $result_array[0]['price'][$product_key]; 
             }
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 


      } 
      }else if($site_value == 2){//FTB
         if($game_type != 'L2'){
           preg_match('/([0-9,]+)(口|M|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         }else{
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         }
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $result_array[0]['inventory'][$product_key];
         }else{
           $result_inventory = 0; 
         } 
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
         $result_str = $price; 
         switch($game_type){
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                    $price = $result_array[0]['1-9'][$product_key]; 
                  }else{
                    $price = $result_array[0]['10-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
           break;

            case 'DQ10':
               $value = 'DQ10';
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $price = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $price = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['100-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-49'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;

            break;
          case 'RS':

             if(strpos($result_array[0]['inventory'][$product_key],'img')){
               $inventory_array[0]=0;
             }
	 if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                  $price = $result_array[0]['20-29'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['20-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
			 }else{
		        $result_inventory = $inventory_array[0];
                $price = $result_array[0]['price'][$product_key];
			 }
                $result_str = $price;
            break;  
            case 'L2':
             if(strpos($result_array[0]['inventory'][$product_key],'img')){
               $inventory_array[0]=0;
             }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $price = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                    $price = $result_array[0]['10-19'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['20-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
            break;
          case 'FF14':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=29){
                     $price = $result_array[0]['1-29'][$product_key]; 
                  }else if($inventory_array[0] >= 30 && $inventory_array[0] <=59){
                     $price = $result_array[0]['30-59'][$product_key]; 
                  }else{
                     $price = $result_array[0]['60-'][$product_key]; 
                  } 
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                    $price = $result_array[0]['1-29'][$product_key]; 
                    $result_inventory = 0;
                 }
                  $result_str = $price*10;
            }else{
                $price = $result_array[0]['price'][$product_key]; 
               if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
                  $result_str = $price*10;
               }else{
                  $result_inventory = $inventory_array[0]; 
                  $result_str = $price*10;
               }
            }
          break;
          case 'RO':
             if($category_value == 'buy'){
               if(strpos($result_array[0]['inventory'][$product_key],'img')){
                  $inventory_array[0]=0;
               }
               if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                     $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=29){
                     $price = $result_array[0]['20-29'][$product_key]; 
                  }else{
                     $price = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/100;
               }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
               }
               $result_str = $price*100;
            }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_str = $price*100; 
                $result_inventory = $result_inventory[0]/100;
            }
          break;
            case 'ARAD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'nobunaga':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'PSO2':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'L1':
              if($category_value =='buy'){
      
              if($inventory_array[0] != ''){
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = 0;
              }
              }else{
           
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
             }
              $result_str = $price;
            break;
            case 'WZ':
            if($category_value == 'buy'){

              if($inventory_array[0] != ''){
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = 0;
              }
            }else{
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
            }
              $result_str = $price;
            break;

            case 'latale':
             $value = str_replace('ダイア','ダイヤ',$value);
             if($category_value == 'buy'){
             
             if($inventory_array[0] != ''){
                $price = $result_array[0]['prices'][$product_key]*10; 
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['prices'][$product_key]*10; 
                $result_inventory = 0;
              }
              $result_str = $price;
             }else{ 
             if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = 0;
              }
              $result_str = $price;
             }
            break;
            case 'megaten':
            if($category_value == 'buy'){

              if($inventory_array[0] != ''){
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['prices'][$product_key]; 
                $result_inventory = 0;
              }
            }else{
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
            }
              $result_str = $price*10;
            break;
            case 'EWD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
            break;
            case 'LH':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case  'ECO':
            if($category_value == 'buy'){
              //商品名称
                preg_match_all("|<a .*?>([a-zA-Z]+).*?<\/a>|",$value,$temp_array);
                if($temp_array[1][0]==''){
                }else{
                   $value=$temp_array[1][0];
                }
               //商品库存
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 

                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 10 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['10-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]*10; 
                  }else if($inventory_array[0] >= 100 && $inventory_array[0] <=299){
                    $result_str = $result_array[0]['100-299'][$product_key]*10; 
                  }else{
                    $result_str = $result_array[0]['300-'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
             }else{
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
             }
             break;
             case 'lineage':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory  =$inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
             break;

         case 'AION':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
           $tep_arr=explode('_',$value);
           $str_explode = ' ';
           $value=$tep_arr[1].$str_explode.$tep_arr[0];
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $result_str = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $result_str = $result_array[0]['10-29'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_str = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }

         break;
	  case 'MS':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
 	  break;
            }

        }else if($site_value == 3){//WM
          preg_match('/([0-9,]+)\s*?(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 
            switch($game_type){    
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                    $price = $result_array[0]['1-49'][$product_key]*10; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $price = $result_array[0]['50-99'][$product_key]*10;
                  }else{
                    $price = $result_array[0]['100-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0]/10;
              }
              $result_str = $price*10;
           break;
              case 'DQ10':
                  if($product_key != 2){
                      $value = '';
	          }else{
               $value = 'DQ10';
		      if($category_value == 'buy'){
                          if($inventory_array[0] != ''){
                             if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                                $price = $result_array[0]['1-9'][$product_key]; 
                             }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                                 $price = $result_array[0]['10-29'][$product_key]; 
                             }else{
                                 $price = $result_array[0]['30-'][$product_key]; 
                             } 
                             $result_inventory = $inventory_array[0];
                          }else{
                              $price = $result_array[0]['1-9'][$product_key]; 
                              $result_inventory = 0;
                          }
		      }else{
                         if($inventory_array[0] != ''){
                            $result_inventory = $inventory_array[0];
                         }else{
                            $result_inventory = 0; 
                         } 
                         $price = $result_array[0]['price'][$product_key];
		      } 
                        $result_str = $price;
	         }

              break;
            case 'RS':
             if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 5 && $inventory_array[0] <=99){
                  $price = $result_array[0]['5-99'][$product_key]; 
                }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                  $price = $result_array[0]['100-199'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['200-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['5-99'][$product_key]; 
                $result_inventory = 0;
              }
            }else{
              if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }                   
              $price = $result_array[0]['price'][$product_key];
            }
              $result_str = $price;
              break; 
              case 'L2':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key];
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                   $price = $result_array[0]['20-49'][$product_key];
                  }else{
                   $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
                  $result_str = $price;
                }else{
                if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0; 
                } 
                $price = $result_array[0]['price'][$product_key];
                $result_str = $price;
                }
              break;
            case 'FF14':
             if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                  $price = $result_array[0]['10-29'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['30-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
             }else{
              if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;  
              }
              $price = $result_array[0]['price'][$product_key];

            }
              $result_str = $price;
              break;

            case 'RO':
              if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                  $price = $result_array[0]['1-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else{
                  $price = $result_array[0]['100-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-49'][$product_key]; 
                  $result_inventory = 0;
                }
                $price= str_replace(',','',$price); 
                $result_str = $price;
			  }else{

                 $price = $result_array[0]['price'][$product_key];
                 $result_str = $price*100;
                 $result_inventory = $inventory_array[0]/100;
			  }
              break;
              case 'ARAD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
              break;
              case 'blade':
             $value = str_replace('こはく','琥珀',$value);

			 if($category_value == 'buy'){
              $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=4){
                    $price = $result_array[0]['1-4'][$product_key]; 
                  }else if($inventory_array[0] >= 5 && $inventory_array[0] <=10){
                    $price = $result_array[0]['5-10'][$product_key]; 
                  }else{
                    $price = $result_array[0]['10-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-4'][$product_key]; 
                  $result_inventory = 0;
                }

              $result_str = $price*10;
			 }else{
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                  $price = $result_array[0]['1-9'][$product_key]; 
                }else{
                  $price = $result_array[0]['10-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
			 }
              break;
              case 'genshin':
           $inventory_array[0] = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
            }else{
                $result_inventory = $inventory_array[0];
                $price = $result_array[0]['price'][$product_key]; 
             }
              $result_str = $price;
              break;
			  case 'latale':
              if($category_value == 'sell'){
               $value = str_replace('ダイア','ダイヤ',$value);
               if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
               }else{
                 $result_inventory = 0; 
               } 
               $price = $result_array[0]['price'][$product_key];
               $result_str = $price*10;
			  }	  
             			  break;
        case 'MS':

            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                  $price = $result_array[0]['1-19'][$product_key]; 
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['1-19'][$product_key]; 
                $result_inventory = 0;
              }
$result_str = $price*10;
break;

          }
        }else if($site_value == 4){//ランキング
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 

        switch($game_type){
          case 'FF11':
            if($category_value == 'buy'){
             if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key];
                $result_inventory = $inventory_array[0]/10;
              }else{
               $price = $result_array[0]['price'][$product_key];
               $result_inventory = 0;
             }
            }
            $result_str = $price*10;
            break;
         case 'DQ10':
               $value = 'DQ10';
            if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
               $price = $result_array[0]['price'][$product_key]; 
           
              $result_str = $price;

        break;

       case 'RS':
            // $inventory_array[0] = str_replace(',','',$inventory_array[0]);
             $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
             $result_inventory = $result_array[0]['inventory'][$product_key];
       break;
       case 'L2':
             if(strpos($result_array[0]['inventory'][$product_key],'span')){
               $inventory_array[0]=0;
             }
           if($category_value == 'buy'){
            if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
               $price = $result_array[0]['price'][$product_key]; 
           }
              $result_str = $price;
        break; 
      case 'RO':
             $price = $result_array[0]['price'][$product_key];
             $result_inventory = $result_array[0]['inventory'][$product_key]/100;
             $result_str = $price*100;
        break; 
		case 'latale':
             $value = str_replace('ダイア','ダイヤ',$value);
             $price = $result_array[0]['price'][$product_key];
             $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             $result_str = $price*10;
        break; 
		case 'L1':
           if(strpos($result_array[0]['inventory'][$product_key],'span')){
             $inventory_array[0]=0;
            }
            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                  $price = $result_array[0]['10-29'][$product_key]; 
                }else {
                  $price = $result_array[0]['30-'][$product_key];
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
             $result_str = $price;
         break;
         case 'rohan':
         $value = str_replace('サーバー','',$value);
         break;
          case 'MS':
           if($category_value == 'buy'){
            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['20-49'][$product_key]; 
                $result_inventory = 0;
              }
           }else{
            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                  $price = $result_array[0]['1-19'][$product_key]; 
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['1-19'][$product_key]; 
                $result_inventory = 0;
              }
           }
          $result_str = $price*10;
          break;

          case 'genshin':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'megaten':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
           }

        }else if($site_value == 5) {//カカラン
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11': 
              if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
               }
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
              if($category_value == 'buy'){
                $price = $result_array[0]['prices'][$product_key];
              }else{
              
                $price = $result_array[0]['price'][$product_key];
              }
              $result_str = $price;
           break;
	   case 'DQ10':
             if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
              }
             if($product_key != 0){
               $value = '';
	     }else{
               $value = 'DQ10';
               if($category_value=='buy'){
                 if($inventory_array[0] != ''){
                     $price = $result_array[0]['prices'][$product_key]; 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['prices'][$product_key]; 
                     $result_inventory = 0;
                  }
               }else{
           
                 if($inventory_array[0] != ''){
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = 0;
                  }
               }
                $result_str = $price;

             }
	 break;
          case 'RS':
          // if(strpos($result_array[0]['inventory'][$product_key],'-')){
            //  $inventory_array[0]=$result_array[0]['inventory1'][$product_key];
          // }
           $value = str_replace('RedEmrald','RedEmerald',$value); 
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
              if($category_value == 'buy'){
               $price = $result_array[0]['prices'][$product_key]; 
             }else{ 
               $price = $result_array[0]['price'][$product_key]; 
             } 
              $result_str = $price;
          break;
	  case 'L2':
              if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
               }
               if(strpos($result_array[0]['inventory'][$product_key],'span')){
                 $inventory_array[0]=0;
               }
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
                if($category_value == 'buy'){
               $price = $result_array[0]['prices'][$product_key]; 
                }else{
		$price = $result_array[0]['price'][$product_key];  
                }
               $result_str = $price;
         break;
	  case 'RO':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['prices'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*100;
             $result_inventory = $inventory_array[0]/100;
 	  break;

	  case 'MS':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['prices'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
 	  break;

          case 'genshin':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;

          case 'latale':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price*10;
          break;
          
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          
          case 'megaten':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          } 
      }else if($site_value == 6){
		  //这个是主站
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
        
          case 'latale':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price*10;
          break;
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
         }
      }else if($site_value == 7){//ぱすてる
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
	 case 'DQ10':
               $value = 'DQ10';
              if(strpos($result_array[0]['inventory'][$product_key],'span')){
                   $inventory_array[0]=0;
              }
              $inventory_array[0] = str_replace(',','',$inventory_array[0]);
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 0 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
                $result_str = $price*10;
             }else{
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
  
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                 }else{
                    $price = $result_array[0]['1-19'][$product_key]; 
                 }
                 $result_str = $price*10;
                 $result_inventory = $inventory_array[0]/10;
            }
       break;
         case 'L2':
          if($category_value == 'buy'){
           if($inventory_array[0] != ''){
              if($inventory_array[0] >= 0 && $inventory_array[0] <=9){
                $price = $result_array[0]['1-9'][$product_key];
              }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                $price = $result_array[0]['10-19'][$product_key];
              }else{
                $price = $result_array[0]['20-'][$product_key];
              }
              $result_inventory = $inventory_array[0]/10;
           }else {
             $price = $result_array[0]['1-9'][$product_key];
             $result_inventory = 0;
           }
           $result_str = $price*10;
          }
          break;
	
          case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/100;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price*100;
          break;
       }
      }else if($site_value == 8){//ダイアモンドギル
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($category_value == 'buy'){ 
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 10 && $inventory_array[0] <=99){
                    $price = $result_array[0]['10-99'][$product_key]; 
                  }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                    $price = $result_array[0]['100-199'][$product_key];
                  }else if($inventory_array[0] >= 200 && $inventory_array[0] <=299){
                    $price = $result_array[0]['200-299'][$product_key];
                  }else{
                    $price = $result_array[0]['300-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['10-99'][$product_key]; 
                  $result_inventory = 0;
                }
           }else {
               if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=4){
                   $price = $result_array[0]['1-4'][$product_key];
                 }else {
                   $price = $result_array[0]['5-'][$product_key];
                 }
                 $result_inventory = $inventory_array[0]/10;
              }else {
                 $price = $result_array[0]['1-4'][$product_key];
                 $result_inventory = 0;
              }
           }
           $result_str = $price*10;
        break;
	 case 'DQ10':
             if($product_key != 2){
               $value = '';
	     }else{
               $value = 'DQ10';
               if($category_value == 'buy'){
               //商品库存
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                            $price = $result_array[0]['50-99'][$product_key]; 
                        }else{
                            $price = $result_array[0]['100-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-49'][$product_key]; 
                      }
		 }else{
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                            $price = $result_array[0]['1-9'][$product_key]; 
                        }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                            $price = $result_array[0]['10-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                            $price = $result_array[0]['50-99'][$product_key]; 
                        }else{
                            $price = $result_array[0]['100-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-9'][$product_key]; 
                      }
		 }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];
		  }

           break;
         case 'RS':
           $value = str_replace('Twlight','Twilight',$value);
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
               if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                  $price = $result_array[0]['10-49'][$product_key];
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                   $price = $result_array[0]['50-99'][$product_key];
                }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                      $price = $result_array[0]['100-199'][$product_key];
                }else{
                   $price = $result_array[0]['200-'][$product_key];
                }
               $result_inventory = $inventory_array[0];
              }else{
                 $price = $result_array[0]['10-49'][$product_key];
                 $result_inventory = 0;
              
              }
               $result_str = $price;
           }else{
            if($inventory_array[0] != ''){
              $result_inventory = $inventory_array[0];
            }else{
              $result_inventory = 0;
            }
             $price = $result_array[0]['5-'][$product_key];
             $result_str = $price;
           }
           break;
         case 'L2':
            if(strpos($result_array[0]['inventory'][$product_key],'-')){
               $inventory_array[0]=$result_array[0]['inventory'][$product_key];
            }
           if($category_value == 'buy'){
             if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                   $price = $result_array[0]['1-19'][$product_key];
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                   $price = $result_array[0]['20-49'][$product_key];
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                   $price = $result_array[0]['50-99'][$product_key];
                }else{
                   $price = $result_array[0]['100-'][$product_key];
                }
                $result_inventory = $inventory_array[0];
             }else{
               $price = $result_array[0]['1-19'][$product_key];
               $result_inventory = 0;
             }
           }else{
             if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
             }else{
                $result_inventory = 0;
             }
             $price = $result_array[0]['price'][$product_key];
           }
           $result_str = $price;
         break;
	
          case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/100;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price*100;
          break;
         case 'moe':
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
		        $price = $result_array[0]['5-9'][$product_key];
		  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
 		      $price = $result_array[0]['10-49'][$product_key];
                  }else{
		      $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0]/10;
 
              }else{
 		$price = $result_array[0]['5-9'][$product_key];
                $result_inventory = 0;
              }
             $result_str = $price*10;
            }
         break;
	 }
	}
      else if($site_value == 9){//アサヒ
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
			 //DQ10不需要处理
         case 'FF11': 
           if($category_value == 'buy'){ 
                if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
                $price = $result_array[0]['price'][$product_key]; 
           }else {
               if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0]/10;
              }else {
                 $result_inventory = 0;
              }
              $price = $result_array[0]['price'][$product_key];
           }
           $result_str = $price*10;
        break;
        case'DQ10':
               $value = 'DQ10';
        break;
         case 'RS':
         if($category_value == 'buy'){
            if($inventory_array[0] != ''){
              $price = $result_array[0]['price'][$product_key];
              $result_inventory = $inventory_array[0];
            }else{
              $result_inventory = 0;            
            }
            $result_str = $price;
        }else{
            if($inventory_array[0] != ''){
              $price = $result_array[0]['price'][$product_key];
               $result_inventory = $inventory_array[0];
            }else{
               $result_inventory = 0;
            }
             $result_str = $price;
        }
         break;
         case 'L2':
           if($inventory_array[0] != ''){
             $result_inventory = $inventory_array[0];
           }else{
              $result_inventory = 0;
           }
            $price = $result_array[0]['price'][$product_key];
            $result_str = $price;
         break;
		 }
	  }
      else if($site_value == 10){//KING
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($inventory_array[0] != ''){
              if($inventory_array[0] >=1 && $inventory_array[0]<=2){
                $price = $result_array[0]['1-2'][$product_key];
              }else if($inventory_array[0] >=3 && $inventory_array[0]<=4){
                $price = $result_array[0]['3-4'][$product_key];
              }else{
                $price = $result_array[0]['5-'][$product_key];
              }
             $result_inventory = $inventory_array[0]*10;
            }else{
              $price = $result_array[0]['1-2'][$product_key];
              $result_inventory = 0;
            }    
          $price = str_replace(',','',$price); 
          $result_str = $price/10;
        break;
	 case 'DQ10':
             if($product_key!=0){
               $value = '';
	      }else{
               $value = 'DQ10';
	          if($category_value == 'buy'){
                      $inventory_array[0] = str_replace(',','',$inventory_array[0]);
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=149){
                            $price = $result_array[0]['50-149'][$product_key]; 
                        }else if($inventory_array[0] >= 150 && $inventory_array[0] <=299){
                            $price = $result_array[0]['150-299'][$product_key]; 
                        }else{
                            $price = $result_array[0]['300-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['300-'][$product_key]; 
                      }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];
		 }else{
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=149){
                            $price = $result_array[0]['50-149'][$product_key]; 
                        }else{
                            $price = $result_array[0]['150-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-49'][$product_key]; 
                      }
	        }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];
		 }
		break;
                case 'RS':
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                      $inventory_array[0]=0;
                }
                $value = str_replace('RedEmrald','RedEmerald',$value);
                if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                    $price = $result_array[0]['1-9'][$product_key];
                 }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $price = $result_array[0]['10-29'][$product_key];
                 }else{
                    if($category_value != 'buy'){
                         $price = $result_array[0]['10-29'][$product_key];
                    }else{
                         $price = $result_array[0]['30-'][$product_key];
                    }
                 }
                 $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key];
                   $result_inventory = 0;
                }
                 $result_str = $price;
                break;
                case 'L2':
                
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                      $inventory_array[0]=0;
                }
                  if($category_value == 'buy'){
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                        $price = $result_array[0]['1-9'][$product_key];
                      }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                        $price = $result_array[0]['10-29'][$product_key];
                      }else if($inventory_array[0] >= 30 && $inventory_array[0] <=99){
                        $price = $result_array[0]['30-99'][$product_key];
                      }else{
                        $price = $result_array[0]['100-'][$product_key];
                      }
                      $result_inventory = $inventory_array[0];
                    }else{
                       $price = $result_array[0]['1-9'][$product_key];
                       $result_inventory = 0;
                    }
                  }else{
                     if($inventory_array[0] != ''){
                         $result_inventory = $inventory_array[0];
                     }else {
                        $result_inventory = 0;
                    }
                     $price = $result_array[0]['1-9'][$product_key];
                  }
                  $result_str = $price;
                break;
                case 'L1':
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                    $inventory_array[0]=0;
                 }
                if($category_value == 'buy'){               
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                        $price = $result_array[0]['1-9'][$product_key];
                      }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                        $price = $result_array[0]['10-29'][$product_key];
                      }else{
                        $price = $result_array[0]['30-'][$product_key];
                      }
                      $result_inventory = $inventory_array[0];
                    }else{
                       $price = $result_array[0]['1-9'][$product_key];
                       $result_inventory = 0;
                    }
                 }else{
                    if($inventory_array[0] != ''){
                        $price = $result_array[0]['1-9'][$product_key];
                        $result_inventory = $inventory_array[0];  
                    }else{
                        $result_inventory = 0;
                    } 
                 }
                break;
		 }
	  }
      else if($site_value == 11){//SONIC
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($category_value == 'buy'){
             if($inventory_array[0] != ''){
               $result_inventory = $inventory_array[0]/10;
            }else {
               $result_inventory = 0;
           }
           }
           $price = $result_array[0]['price'][$product_key];
           $result_str = $price*10;
        break;
	 case 'DQ10':
             if($product_key != 1){
               $value = '';
	     }else{
               $value = 'DQ10';
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-99'][$product_key]; 
                     }else{
                        $price = $result_array[0]['100-'][$product_key]; 
                     } 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['1-99'][$product_key]; 
                     $result_inventory = 0;
                  }
                $result_str = $price;

             }
		 break;
                 case 'RS':
                  if(strpos($result_array[0]['inventory'][$product_key],'img')){
                      $inventory_array[0]=0;
                  }
                 $value = str_replace('RedEmrald','RedEmerald',$value);
                 $value = str_replace('Twlight','Twilight',$value);
                 if($category_value == 'buy'){
                   if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-99'][$product_key];
                     }else{
                     $price = $result_array[0]['100'][$product_key];
                     }
                     $result_inventory = $inventory_array[0];
                   }else{
                     $price = $result_array[0]['1-99'][$product_key];
                     $result_inventory = 0;
                   }
                   $result_str = $price;
                 }
                 break;
                 case 'L2':
                 if($category_value == 'buy'){
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-5'][$product_key];
                      }else{
                        $price = $result_array[0]['6'][$product_key];
                      }
                    }else{
                       $price = $result_array[0]['1-5'][$product_key];
                     $result_inventory = 0;
                    }
                 }
                  $result_str = $price;
                 break;
           }
       
       }else if($site_value == 12){

          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
               $value = 'DQ10';
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
           $value = str_replace('RedEmrald','RedEmerald',$value); 
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
         case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          }



       }else if($site_value == 13){

          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
               $value = 'DQ10';
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
           $value = str_replace('RedEmrald','RedEmerald',$value); 
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['prices'][$product_key]; 
               $result_str = $price;
          break;
          }
        } else{
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 
        } 
      

      $value = str_replace('<br />','',$value);
      $result_str = str_replace(',','',$result_str);
      $result_inventory = str_replace(',','',$result_inventory);
      $value = preg_replace('/<.*?>/','',$value);
     //echo "insert into product values(NULL,'".$category_id_array[$site_value]."','".trim($value)."','".$result_str."','".$result_inventory."',0)<br>";
      //数据入库

//给主站的商品进行排序
 if(strpos($url_array[$site_value],'www.iimy.co.jp')){
      $sort_order =10000-$product_key;
 }else{
//如果价格是空或是0
    if($result_str==0){
        $products_query = mysql_query("delete from product where category_id='".$category_id_array[$site_value]."' and product_name='".trim($value)."'");
    }
       $sort_order = 0;
   }

//判断数据库是否存在相同名称相同category_id 的商品
      $search_query = mysql_query("select product_id from product where category_id='".$category_id_array[$site_value]."' and product_name='".trim($value)."'");

//最新采集的商品名称
$product_new[] = trim($value);
//有,则更新 没有,则添加
      if(mysql_num_rows($search_query) == 1){
        $products_query = mysql_query("update product set product_price='".$result_str."',product_inventory='".$result_inventory."',sort_order='".$sort_order."' where category_id='".$category_id_array[$site_value]."' and product_name='".trim($value)."'");
      }else{
        if($value!='' && $result_str!=''){
          $products_query = mysql_query("insert into product values(NULL,'".$category_id_array[$site_value]."','".trim($value)."','".$result_str."','".$result_inventory."','".$sort_order."')");
        }
      }
      }    
//数据库原有的商品名称
$search_query = mysql_query("select product_name from product where category_id='".$category_id_array[$site_value]."'");
$product_old_list[] = array();
while($row_tep = mysql_fetch_array($search_query)){
   $product_old_list[] = $row_tep['product_name'];
}
//新获取的数据已经不包含数据库的数据,删除
foreach($product_old_list as $product_old_name){
    if(!in_array($product_old_name,$product_new)){
        $products_query = mysql_query("delete from product where category_id='".$category_id_array[$site_value]."' and product_name='".$product_old_name."'");
    }
}

  }
  //exit;
  }

/*
 * na FF14 游戏采集
 */
if($game_type == 'FF14'){
  $na_url_array = array();
  $na_category_id_array = array();

  $na_category_query = mysql_query("select * from category where category_name='FF14' and game_server='na'");
  while($na_category_array = mysql_fetch_array($na_category_query)){

    $na_url_array[] = $na_category_array['category_url'];
    $na_category_id_array[] = $na_category_array['category_id'];
  }

  $na_search_array  = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+)\(.*?\)\-rmt<\/td>',
                        '1-80'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '81-500'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?L?E?G?A?C?Y?.*?\)?<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th"><a href=".*?">([a-zA-Z]+)\(?L?E?G?A?C?Y?.*?\)?<\/a><\/td>',
                      'price'=>'<td>([0-9.,]+)円<\/td>.*?<td>[0-9.,]+Pt<\/td>.*?<td>.*?<\/td>',
                      'inventory'=>'<td>[0-9.,]+円<\/td>.*?<td>[0-9.,]+Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                    );

  //开始采集数据
  foreach($na_url_array as $key=>$value){

    if($value == ''){continue;}
    $result = new Spider($value,'',$na_search_array[$key]);
    $result_array = $result->fetch();
    $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$na_category_id_array[$key]."'");
    //print_r($result_array);

    foreach($result_array[0]['products_name'] as $products_key=>$products_value){
      preg_match('/([0-9,]+).*?口/is',$result_array[0]['inventory'][$products_key],$inventory_array);
      if($key == 0){

        if($inventory_array[0] != ''){

          if($inventory_array[0] >= 1 && $inventory_array[0] <= 80){

            $price = $result_array[0]['1-80'][$products_key];
          }else if($inventory_array[0] >= 81 && $inventory_array[0] <= 500){

            $price = $result_array[0]['81-500'][$products_key];
          }
          $result_inventory = $inventory_array[0];
        }else{
          $price = $result_array[0]['1-80'][$products_key]; 
          $result_inventory = 0;
        }
      }else if($key == 1){

        if($inventory_array[0] != ''){

          if($inventory_array[0] >= 1 && $inventory_array[0] <= 9){

            $price = $result_array[0]['1-9'][$products_key];
          }else if($inventory_array[0] >= 10 && $inventory_array[0] <= 29){

            $price = $result_array[0]['10-29'][$products_key];
          }else{
             $price = $result_array[0]['30-'][$products_key]; 
          }
          $result_inventory = $inventory_array[0];
        }else{
          $price = $result_array[0]['1-9'][$products_key]; 
          $result_inventory = 0;
        }
      }else if($key == 2){

          $price = $result_array[0]['price'][$products_key]; 
          if($inventory_array[0] != ''){
       
            $result_inventory = $inventory_array[0];
          }else{
        
            $result_inventory = 0;
          }
      }

      //数据入库
      $search_query = mysql_query("select product_id from product where category_id='".$na_category_id_array[$key]."' and product_name='".trim($products_value)."'");

      if(mysql_num_rows($search_query) == 1){

        $products_query = mysql_query("update product set product_price='".$price."',product_inventory='".$result_inventory."'where category_id='".$na_category_id_array[$key]."' and product_name='".trim($products_value)."'");
      }else{

        $products_query = mysql_query("insert into product values(NULL,'".$na_category_id_array[$key]."','".trim($products_value)."','".$price."','".$result_inventory."',0)");
      } 
    }
  }
}
/*get_contents_main end*/
}
?>
