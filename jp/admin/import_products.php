<?php
//商品数据处理脚本
set_time_limit(0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set("display_errors", "Off");
include("includes/configure.php");

$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '</head>';
echo '<body>';
echo 'start......<br><br>';


//商品图片处理
$products_images_query = mysql_query("select * from products_description");
while($products_images_array = mysql_fetch_array($products_images_query)){

  if(trim($products_images_array['products_image']) != ''){

    mysql_query("insert into products_images values(NULL,".$products_images_array['products_id'].",".$products_images_array['site_id'].",'".$products_images_array['products_image']."',0)");
  }

  if(trim($products_images_array['products_image2']) != ''){

    mysql_query("insert into products_images values(NULL,".$products_images_array['products_id'].",".$products_images_array['site_id'].",'".$products_images_array['products_image2']."',0)");
  }

  if(trim($products_images_array['products_image3']) != ''){

    mysql_query("insert into products_images values(NULL,".$products_images_array['products_id'].",".$products_images_array['site_id'].",'".$products_images_array['products_image3']."',0)");
  }
}
mysql_free_result($products_images_query);
echo '商品图片处理完成<br>';

//推荐商品图片处理
$products_cart_query = mysql_query("select * from products");
while($products_cart_array = mysql_fetch_array($products_cart_query)){

  if(trim($products_cart_array['products_cart_image']) != ''){

    mysql_query("insert into products_images values(NULL,".$products_cart_array['products_id'].",0,'".$products_cart_array['products_cart_image']."',1)");
  }
}
mysql_free_result($products_cart_query);
echo '推荐商品图片处理完成<br>';

//前台商品详细页面上部、下部、汇率处理
$products_query = mysql_query("select * from products");
while($products_array = mysql_fetch_array($products_query)){

  $contents_top = $products_array['products_attention_1_1'].'||||||'.$products_array['products_attention_1_2'].'${RATE}'.$products_array['products_attention_1_4'];
  $products_attention_1 = explode('//',$products_array['products_attention_1']);
  $contents_top .= '------'.$products_attention_1[0].'||||||'.$products_attention_1[1];
  $products_attention_2 = explode('//',$products_array['products_attention_2']);
  $contents_top .= '------'.$products_attention_2[0].'||||||'.$products_attention_2[1];

  $products_attention_3 = explode('//',$products_array['products_attention_3']);
  $contents_under = $products_attention_3[0].'||||||'.$products_attention_3[1];
  $products_attention_4 = explode('//',$products_array['products_attention_4']);
  $contents_under .= '------'.$products_attention_4[0].'||||||'.$products_attention_4[1];

  $products_exchange_rate = $products_array['products_attention_1_3'];

  $price_type = trim($products_array['products_small_sum']) != '' ? 1 : 0;

  mysql_query("update products set products_info_top='".$contents_top."',products_info_under='".$contents_under."',products_exchange_rate=".$products_exchange_rate.",price_type=".$price_type." where products_id='".$products_array['products_id']."'");
}
mysql_free_result($products_query);
echo '商品上部、下部内容、汇率处理完成<br>';

/* -------------------------------------
    功能: 判断输入是否为空
    参数: $value(string) 字符串 
    返回值: 值是否为空(boolean) 
------------------------------------ */
  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

//获取 STORE_NAME、C_TITLE 的值
$site_name = array();
$configuration_name_query = mysql_query("select * from configuration where configuration_key='STORE_NAME'");
while($configuration_name_array = mysql_fetch_array($configuration_name_query)){

  $site_name[$configuration_name_array['site_id']] = $configuration_name_array['configuration_value'];
}
mysql_free_result($configuration_name_query);

$site_title = array();
$configuration_title_query = mysql_query("select * from configuration where configuration_key='C_TITLE'");
while($configuration_title_array = mysql_fetch_array($configuration_title_query)){

  $site_title[$configuration_title_array['site_id']] = $configuration_title_array['configuration_value'];
}
mysql_free_result($configuration_title_query);

//网页头部信息处理
$categories_query = mysql_query("select * from categories_description");
while($categories_array = mysql_fetch_array($categories_query)){

  switch($categories_array['site_id']){

  case 1:
    $title = $categories_array['categories_name'] . (tep_not_null($categories_array['categories_meta_text']) ? '-' . $categories_array['categories_meta_text'] . '専門の' . $site_name[1] : $site_title[1]);
    break;
  case 2:
    $title = $categories_array['categories_name'] . (tep_not_null($categories_array['categories_meta_text']) ? '-' .  $categories_array['categories_meta_text'] . '｜激安の'.$site_name[2] : $site_title[2]);
    break;
  case 3:
    $title = $categories_array['categories_name'] . 'と言えば'.$site_name[3].'｜' . (tep_not_null($categories_array['categories_meta_text']) ? $categories_array['categories_meta_text'] : $site_title[3]);
    break;
  default:
    $title = $site_title[$categories_array['site_id']];
    break;
  }

  mysql_query("update categories_description set meta_title='".$title."' where categories_id='".$categories_array['categories_id']."' and site_id='".$categories_array['site_id']."'");
}
mysql_free_result($categories_query);
echo '网页头部信息处理完成<br>';

//分类搜索关键字处理
$categories_key_query = mysql_query("select * from categories_description");
while($categories_key_array = mysql_fetch_array($categories_key_query)){

  $key_array = array();
  $key_str = '';
  $key_array[] = $categories_key_array['categories_name']; 
  $key_array[] = $categories_key_array['categories_name_list']; 
  $key_array[] = $categories_key_array['romaji']; 
  $key_array[] = $categories_key_array['meta_title']; 
  $key_array[] = $categories_key_array['meta_keywords']; 
  $key_array[] = $categories_key_array['meta_description']; 
  $key_array[] = $categories_key_array['categories_header_text']; 
  $key_array[] = $categories_key_array['categories_footer_text']; 
  $key_array[] = $categories_key_array['text_information']; 
  $key_str = implode('||||||',$key_array);

  mysql_query("update categories_description set search_info='".$key_str."' where categories_id='".$categories_key_array['categories_id']."' and site_id='".$categories_key_array['site_id']."'");
}
mysql_free_result($categories_key_query);
echo '分类搜索关键字处理完成<br>';

//商品搜索关键字处理
$products_key_query = mysql_query("select * from products_description");
while($products_key_array = mysql_fetch_array($products_key_query)){
  $products_keys_query = mysql_query("select * from products where products_id='".$products_key_array['products_id']."'");
  $products_keys_array = mysql_fetch_array($products_keys_query);
  mysql_free_result($products_keys_query);

  //TAG 名称
  $tags_id_array = array();

  $products_tags_query = mysql_query("select * from products_to_tags where products_id='".$products_key_array['products_id']."'"); 
  while($products_tags_array = mysql_fetch_array($products_tags_query)){

    $tags_id_array[] = $products_tags_array['tags_id'];
  }
  mysql_free_result($products_tags_query);

  $products_cart_tags_query = mysql_query("select * from products_to_tags where products_id='".$products_key_array['products_id']."'"); 
  while($products_cart_tags_array = mysql_fetch_array($products_cart_tags_query)){

    $tags_id_array[] = $products_cart_tags_array['tags_id'];
  }
  mysql_free_result($products_cart_tags_query);

  $tag_id_str = implode(',',$tags_id_array);
  $tags_array = array();
  $tags_name_query = mysql_query("select tags_name from tags where tags_id in (".$tag_id_str.")");
  while($tags_name_array = mysql_fetch_array($tags_name_query)){

   $tags_array[] = $tags_name_array['tags_name']; 
  }
  mysql_free_result($tags_name_query);

  $tags_array = array_unique($tags_array);
  $tags_name_str = implode('||||||',$tags_array);
  

  $key_array = array();
  $key_str = '';
  $key_array[] = $products_keys_array['products_info_top'];
  $key_array[] = $products_keys_array['products_info_under'];
  $key_array[] = $products_keys_array['products_model'];
  $key_array[] = $products_keys_array['products_price'];
  $key_array[] = $tags_name_str;
  $key_array[] = $products_key_array['products_name'];
  $key_array[] = $products_key_array['romaji'];
  $key_array[] = $products_key_array['products_description'];

  $key_str = implode('||||||',$key_array);

  mysql_query("update products_description set search_info='".$key_str."' where products_id='".$products_key_array['products_id']."' and site_id='".$products_key_array['site_id']."'");
}
mysql_free_result($products_key_query);
echo '商品搜索关键字处理完成<br>';

//分类各网站数据处理
//获取网站ID
$site_arr = array(0);
$site_query = mysql_query("select * from sites");
while($site_array = mysql_fetch_array($site_query)){

  $site_arr[] = $site_array['id'];
}
mysql_free_result($site_query);

$categories_id_array = array();
$categories_site_query = mysql_query("select * from categories_description");
while($categories_site_array = mysql_fetch_array($categories_site_query)){

  $categories_id_array[$categories_site_array['categories_id']][] = $categories_site_array['site_id'];
}
mysql_free_result($categories_site_query);

foreach($categories_id_array as $key=>$value){

  $diff_array = array();
  $diff_array = array_diff($site_arr,$value); 

  $c_site_query = mysql_query("select * from categories_description where categories_id='".$key."' and site_id=0");
  $c_site_array = mysql_fetch_array($c_site_query);
  mysql_free_result($c_site_query);
  foreach($diff_array as $diff_value){

    mysql_query("insert into categories_description(categories_id,site_id,language_id,categories_name,categories_name_list,seo_name,categories_image2,categories_meta_text,seo_description,categories_header_text,categories_footer_text,text_information,meta_title,meta_keywords,meta_description,romaji,categories_status,character_romaji,alpha_romaji,last_modified,user_last_modified,c_manual,categories_image,search_info) values('".$c_site_array['categories_id']."','".$diff_value."','".$c_site_array['language_id']."','".$c_site_array['categories_name']."','".$c_site_array['categories_name_list']."','".$c_site_array['seo_name']."','".$c_site_array['categories_image2']."','".$c_site_array['categories_meta_text']."','".$c_site_array['seo_description']."','".$c_site_array['categories_header_text']."','".$c_site_array['categories_footer_text']."','".$c_site_array['text_information']."','".$c_site_array['meta_title']."','".$c_site_array['meta_keywords']."','".$c_site_array['meta_description']."','".$c_site_array['romaji']."','".$c_site_array['categories_status']."','".$c_site_array['character_romaji']."','".$c_site_array['alpha_romaji']."','".$c_site_array['last_modified']."','".$c_site_array['user_last_modified']."','".$c_site_array['c_manual']."','".$c_site_array['categories_image']."','".$c_site_array['search_info']."')");
  }
}


$products_id_array = array();
$products_site_query = mysql_query("select * from products_description");
while($products_site_array = mysql_fetch_array($products_site_query)){

  $products_id_array[$products_site_array['products_id']][] = $products_site_array['site_id'];
}
mysql_free_result($products_site_query);

foreach($products_id_array as $key=>$value){

  $diff_array = array();
  $diff_array = array_diff($site_arr,$value); 

  $c_site_query = mysql_query("select * from products_description where products_id='".$key."' and site_id=0");
  $c_site_array = mysql_fetch_array($c_site_query);
  mysql_free_result($c_site_query);
  foreach($diff_array as $diff_value){

    mysql_query("insert into products_description(products_id,language_id,products_name,products_description,products_description_origin,site_id,products_url,products_viewed,romaji,products_status,products_last_modified,option_image_type,preorder_status,products_user_update,p_manual,search_info) values('".$c_site_array['products_id']."','".$c_site_array['language_id']."','".$c_site_array['products_name']."','".$c_site_array['products_description']."','".$c_site_array['products_description_origin,']."','".$diff_value."','".$c_site_array['products_url']."','".$c_site_array['products_viewed']."','".$c_site_array['romaji']."','".$c_site_array['products_status']."','".$c_site_array['products_last_modified']."','".$c_site_array['option_image_type']."','".$c_site_array['preorder_status']."','".$c_site_array['products_user_update,']."','".$c_site_array['p_manual']."','".$c_site_array['search_info']."')");
  }
}
echo '商品各网站数据处理完成<br>';

echo '<br><br>finish';
echo '</body>';
echo '</html>';
