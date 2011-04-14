<?php
require('includes/application_top.php');
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<table border='1'>
  <tr>
    <th>商品名</th>
    <th>テキスト１</th>
    <th>数値</th>
    <th>テキスト2</th>
  </tr>
<?php
$query = tep_db_query("
  select p.products_id,
         p.products_attention_1,
         pd.products_name 
  from products p,products_description pd 
  where p.products_id=pd.products_id 
    and pd.site_id=0
    and products_attention_1 != ''
");
while ($p = tep_db_fetch_array($query)) {
  $data = explode('//',$p['products_attention_1']);
  $arr =  to4($data[1]);
  if ($arr['a'] && $arr['b'] && $arr['c']) {
    tep_db_perform('products', array(
      'products_attention_1_1' => '内容',
      'products_attention_1_2' => $arr['a'],
      'products_attention_1_3' => $arr['b'],
      'products_attention_1_4' => $arr['c'],
    ), 'update', "products_id='".$p['products_id']."'");
  } else {
    tep_db_perform('products', array(
      'products_attention_1_1' => '',
      'products_attention_1_2' => '',
      'products_attention_1_3' => '',
      'products_attention_1_4' => '',
    ), 'update', "products_id='".$p['products_id']."'");
  }
  ?>
  <tr>
    <td><?php echo $p['products_name'];?></td>
    <td><?php echo $arr['a'];?></td>
    <td><?php echo $arr['b'];?></td>
    <td><?php echo $arr['c'];?></td>
  </tr>
  <?php
}
?>
</table>
<?php

  function to4($rate) {
    $rate = trim($rate);
    
    if (trim($rate) == '天空の羽毛5個・インクリスクロール5個のセット'){
      return array('a' => '1個あたり　天空の羽毛', 'b' => '5', 'c' => '枚のお取引となります');
    }
    
    if (trim($rate) == 'ネットカフェ1DAYチケット5枚セット'){
      return array('a' => '1個あたり　ネットカフェ1DAYチケット', 'b' => '5', 'c' => '枚のお取引となります');
    } 
    if (trim($rate) == '天空の羽毛10個・インクリスクロール10個のセット'){
      return array('a' => '1個あたり　天空の羽毛', 'b' => '10', 'c' => '枚のお取引となります');
    }
    
    if (trim($rate) == 'ネットカフェ1DAYチケット10枚セット'){
      return array('a' => '1個あたり　ネットカフェ1DAYチケット', 'b' => '10', 'c' => '枚のお取引となります');
    } 
    
    $rate = str_replace(array(','), array(''), $rate);

    if (preg_match('/^(.*)億(.*)万(.*)$/', $rate, $out)) {
      $rate = (($out[1] * 100000000) + ($out[2] * 10000)) . $out[3];
    } else {
      $rate = str_replace(array('万','億'), array('0000','00000000'), $rate);
    }
    
    if (preg_match('/^(\d+)(.*)（\d+.*）$/', $rate, $out)) {
      return array('a' => '1個あたり', 'b' => $out[1], 'c' => $out[2].'のお取引となります');
    }
    
    if (preg_match('/^(\d+)(.*)\(\d+.*\)$/', $rate, $out)) {
      return array('a' => '1個あたり', 'b' => $out[1], 'c' => $out[2].'のお取引となります');
    }
    
    if (preg_match('/^(\d+)(.*)$/', $rate, $out)) {
      return array('a' => '1個あたり', 'b' => $out[1], 'c' => $out[2] . 'のお取引となります');
    }
    
    if (preg_match('/^([^\d]*)(\d+)([^\d]*)$/', $rate, $out)) {
      return array('a' => '1個あたり　' . $out[1], 'b' => $out[2], 'c' => $out[3].'のお取引となります');
    }
    
    return array('a' => '', 'b' => '', 'c' => '');
  }
  
  /*
  
  
  10000 => 1万
  
  100000000 => 1億