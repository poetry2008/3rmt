<?php
//采集脚本
ini_set("display_errors", "On");
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
   //采集内容为空或者超时的数据数组
   $collect_error_array = array();
   get_contents_main($game_type,$category,'',$collect_error_array,$flag=false);

   if(!empty($collect_error_array)){
     //获取所有的网站
     $site_list_array = array();
     $site_url_array = array();
     $site_query = mysql_query("select site_id,site_name,site_url from site");
     while($site_array = mysql_fetch_array($site_query)){

       $site_list_array[$site_array['site_id']] = $site_array['site_name'];
       $site_url_array[$site_array['site_id']] = $site_array['site_url'];
     }
     //发送错误邮件
     $mail_str = '手動更新失敗詳細'."\n";
     foreach($collect_error_array as $collect_error_value){

       if($collect_error_value['type'] == 'buy'){

         $category_type = 1;
       }else{
         $category_type = 0;
       }
       $category_query = mysql_query("select category_id,site_id from category where category_name='".$collect_error_value['game']."' and category_url='".$collect_error_value['url']."' and category_type='".$category_type."'");
       if(mysql_num_rows($category_query) > 0){
         $category_array = mysql_fetch_array($category_query);
       }else{
         $url_array = parse_url($collect_error_value['url']);
         $url_str = $url_array['scheme'].'://'.$url_array['host'].'/';
         $category_array['site_id'] = array_search($url_str,$site_url_array); 
       }
       //mysql_query("update product set is_error=1 where category_id='".$category_array['category_id']."'");
       $mail_str .= date('H:i:s',$collect_error_value['time']).'　　';
       $mail_str .= $collect_error_value['game'].'--';
       $mail_str .= $collect_error_value['type'].'--';
       $mail_str .= $site_list_array[$category_array['site_id']].'　　';
       $mail_str .= $collect_error_value['url']."\n";
     }
     $email = '287499757@qq.com';
     $admin_email = '287499757@qq.com';
     $error_subject = '取得失敗エラー';
     $error_msg = $mail_str;
     $error_headers = "From: ".$email ."<".$email.">";
     //mail($admin_email,$error_subject,$error_msg,$error_headers);
   }

}



function get_contents_main($game_type,$category,$site,&$collect_error_array,$flag){
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
}else{
  $category_type = $category;
}
/*以下是正式采集*/
$game_type=$game_type;
require('collect_match.php');
  foreach($category_type as $category_value){

      $url_array = $url_str_array[$category_value];
      $category_id_array = $category_id_str_array[$category_value];
      $site = $site_str[$category_value];

	  //正则
	      $search_array = $search_array_match[$category_value][$game_type];

  //开始采集数据
  $curl_flag = 0;
  foreach($site as $site_value){
    if($url_array[$site_value] == ''){
      
      $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$url_array[$site_value]);
      continue;
    }
 //   if(strpos($url_array[$site_value],'www.iimy.co.jp')){continue;}
//将网站转换成主站地址,方便gamelife 测试使用
  if(strpos($url_array[$site_value],'www.iimy.co.jp')){
    $iimy_url_array= parse_url($url_array[$site_value]);
   preg_match_all("|[0-9]+_([0-9]+)|",$iimy_url_array['path'],$temp_category_id);
 $url_array[$site_value]= 'www.iimy.co.jp/api.php?key=testkey1_98ufgo48d&action=clt&cpath='.$temp_category_id[1][0];
//   $url_array[$site_value]= str_replace('www.iimy.co.jp','192.168.160.200',$url_array[$site_value]);
  }
   if(strpos($url_array[$site_value],'pastel-rmt.jp')||strpos($url_array[$site_value],'www.rmt-king.com')||strpos($url_array[$site_value],'192.168.100.200')){$curl_flag=0;}else{$curl_flag=1;}
    if($url_array[$site_value]=='//http://rmtrank.com/777town+index.htm'){
      $url_array[$site_value] = str_replace('//http://rmtrank.com/777town+index.htm','http://rmtrank.com/777town+index.htm',$url_array[$site_value]);
      $result = new Spider($url_array[$site_value],'',$search_array[$site_value],$curl_flag);
      $result_array = $result->fetch();
      if(!$result->collect_flag){

        $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$url_array[$site_value]);
      }
    }else{
      $result = new Spider($url_array[$site_value],'',$search_array[$site_value],$curl_flag);
      $result_array = $result->fetch();
      if(!$result->collect_flag){

        $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$url_array[$site_value]);
      }
    }
    //处理kakaran
    if($result_array[0]['url']){
      $url_kaka_array[] = 'rmt.kakaran.jp'.$site_value;
      //取出单价i
      $kaka_array = array();
      foreach($result_array[0]['url'] as $key=>$url){
          if($url==''){continue;}
			  if($flag==true){
			  sleep(3);
			  }
          $result_kaka = new Spider("rmt.kakaran.jp".$url,'',$search_array[$site_value],$curl_flag);
          $result_array_kaka = $result_kaka->fetch();
          if(!$result_kaka->collect_flag){

            $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>"http://rmt.kakaran.jp".$url);
          }
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
          $result_array[0][price][] =  $kaka_array[$kaka_key]['price'];
          $result_array[0][inventory][] = $kaka_array[$kaka_key]['inventory'];
     }

   }
//将ip地址重新转换成域名形式
  if(strpos($url_array[$site_value],'192.168.160.200')){
     $url_array[$site_value]= str_replace('192.168.160.200','www.iimy.co.jp',$url_array[$site_value]);
  }

    $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$category_id_array[$site_value]."'");

    $result_array[0]['products_name'] = array_unique($result_array[0]['products_name']);
//当获取的数据商品名称为空(或这个页面没有数据)
if(empty($result_array[0]['products_name'])){
  mysql_query("delete from product where category_id='".$category_id_array[$site_value]."'");
}

foreach($result_array[0]['products_name'] as $product_key=>$value){
  $price_info = tep_get_price_info($result_array,$category_value,$game_type,$site_value,$product_key,$value);
  $value = $price_info['value'];
  $result_str = $price_info['result_str'];
  $result_inventory = $price_info['result_inventory'];
  

//给主站的商品进行排序
 if(strpos($url_array[$site_value],'www.iimy.co.jp')){
      $sort_order =10000-$product_key;
 }else{
//如果价格是空或是0
  //  if($result_str==0){
   //     $products_query = mysql_query("delete from product where category_id='".$category_id_array[$site_value]."' and product_name='".trim($value)."'");
    //}
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
        if($value!=''){
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
  tep_get_toher_collect($game_type);
}
/*get_contents_main end*/
}
function tep_get_toher_collect($game_type){
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
function tep_get_price_info($result_array,$category_value,$game_type,$site_value,$product_key,$value){
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
              $value = str_replace('キャスディエン','キャスティエン',$value);
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
		$value = str_replace('帝愛','',$value);
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
             /*case 'FNO':
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=10){
                   $price = $result_array[0]['1-10'][$product_key]; 
                 }else{
                   $price = $result_array[0]['11-100'][$product_key]; 
                 } 
                   $result_inventory = $inventory_array[0]*10;
                  $result_str = $price/10;
              break;*/
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

              $price = $result_array[0]['price'][$product_key]; 
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
             $value = str_replace('共通サーバー','マビノギ英雄伝',$value); 
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
/*	case 'SUN':
              preg_match_all("|.*?([0-9,]+).*?口.*?|",$result_array[0]['inventory'][$product_key],$temp_array);
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $temp_array[1][0]/10;
              }else{
                $result_inventory = 0; 
	      }
         break;*/
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
                $price = $result_array[0]['price'][$product_key]; 
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
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
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
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
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
             $value = str_replace('ダイアモンド','ダイヤモンド',$value);
             if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'megaten':
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
        /* case 'AION':
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

         break;*/
         case 'HR':
             $value = str_replace('共通サーバー','マビノギ英雄伝',$value); 
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
         break;
	  case 'MS':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 

             $price = $result_array[0]['price'][$product_key];
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
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
             case 'ThreeSeven':
		$value = str_replace('帝愛','',$value);
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
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
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
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
         case 'HR':
             $value = str_replace('共通サーバー','マビノギ英雄伝',$value); 
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
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
	case 'FF14':
           if($category_value == 'buy'){
             $value = str_replace('(LEGASY)','',$value);
           }
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
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
             case 'ThreeSeven':
		$value = str_replace('帝愛','',$value);
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
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
                $price = $result_array[0]['price'][$product_key];
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
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['price'][$product_key]; 
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
               $price = $result_array[0]['price'][$product_key]; 
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
               $price = $result_array[0]['price'][$product_key]; 
                }else{
		$price = $result_array[0]['price'][$product_key];  
                }
               $result_str = $price;
         break;
	  case 'RO':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*100;
             $result_inventory = $inventory_array[0]/100;
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

          case 'genshin':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;

          case 'latale':
             $value = str_replace('ダイアモンド','ダイヤモンド',$value);
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*10;
          break;
          
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
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
         case 'HR':
             $value = str_replace('共通サーバー','マビノギ英雄伝',$value); 
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
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
             case 'ThreeSeven':
		$value = str_replace('帝愛','',$value);
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
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
             $value = str_replace('ダイアモンド','ダイヤモンド',$value);
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*10;
          break;
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
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
	case 'FF14':
           if($category_value == 'buy'){
             $value = str_replace('(LEGASY)','',$value);
           }
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
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
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*100;
          break;
	case 'FF14':
           if($category_value == 'buy'){
             $value = str_replace('(LEGASY)','',$value);
           }
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
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
               $price = $result_array[0]['price'][$product_key]; 
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
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
               $value = 'DQ10';
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
           $value = str_replace('RedEmrald','RedEmerald',$value); 
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
         case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
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
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
               $value = 'DQ10';
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
           $value = str_replace('RedEmrald','RedEmerald',$value); 
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
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
      $res = array('value'=>$value,'result_str'=>$result_str,'result_inventory'=>$result_inventory);
      return $res;

}
?>
