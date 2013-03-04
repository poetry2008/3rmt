<?php
/*
  $Id$
 */
/*
  $rate = new _YuuPack('yuupack','ゆうパック');
  $rate->SetOrigin('01', 'JP');  // 从北海道
  $rate->SetDest('13', 'JP');    // 到东京
  $rate->SetWeight(10);      // kg
  $quote = $rate->GetQuote();
  print $quote['type'] . "<br>";
  print $quote['cost'] . "\n";
*/
class _YuuPack {
  var $quote;
  var $OriginZone;
  var $OriginCountryCode = 'JP';
  var $DestZone;
  var $DestCountryCode = 'JP';
  var $Weight = 0;
  var $Length = 0;
  var $Width  = 0;
  var $Height = 0;
/*-----------------------------
  功能：构造函数
  参数：$id(string)  module id
  参数：$titl(string) module name
  参数：$zone(string) 省市县代码 '01'～'47'
  参数：$country(string) country code
  返回值：无
 ----------------------------*/
  function _YuuPack($id, $title, $zone = NULL, $country = NULL) {
    $this->quote = array('id' => $id, 'title' => $title);
    if($zone) {
      $this->SetOrigin($zone, $country);
    }
  }
/*---------------------------
 功能：设置发送地
 参数：$zone(string)  省市县代码'01'～'47'
 参数：$country(string)  country code
 返回值：无
 --------------------------*/
  function SetOrigin($zone, $country = NULL) {
    $this->OriginZone = $zone;
    if($country) {
      $this->OriginCountryCode = $country;
    }
  }
/*--------------------------
 功能：设置目标 
 参数：$zone(string)  省市县代码'01'～'47'
 参数：$country(string)  country code
 返回值：无
 -------------------------*/
  function SetDest($zone, $country = NULL) {
    $this->DestZone = $zone;
    if($country) {
      $this->DestCountryCode = $country;
    }
  }
/*-------------------------
 功能：设置重量 
 参数：$weight(string) 重量
 返回值：无
 ------------------------*/
  function SetWeight($weight) {
    $this->Weight = $weight;
  }
/*-----------------------
 功能：设置大小 
 参数：$length(string) 长度
 参数：$width(string) 宽度
 参数；$height(string) 高度
 返回值：无
 ----------------------*/
  function SetSize($length = NULL, $width = NULL, $height = NULL) {
    if($length) {
      $this->Length = $length;
    }
    if($width) {
      $this->Width = $width;
    }
    if($height) {
      $this->Height = $height;
    }
  }
  // 返回尺寸区分(0～4)
  // 规格以外的时候返回-1
  //
  // 区分    3边合计   重量
  // ---------------------------------
  // 0    150cm     2kg
  // 1    150cm     4kg
  // 2    150cm     6kg
  // 3    150cm     8kg
  // 4    150cm    10kg
  // 5    150cm    12kg
  // 6    150cm    14kg
  // 7    150cm    16kg
  // 8    150cm    18kg
  // 9    150cm    20kg
  // 10   150cm    25kg
  // 11   150cm    30kg
  function GetSizeClass() {
    $a_classes = array(
      array(0, 150,  2),  // 区分,3边合计(cm),重量(kg)
      array(1, 150,  4),
      array(2, 150,  6),
      array(3, 150,  8),
      array(4, 150, 10),
      array(5, 150, 12),
      array(6, 150, 14),
      array(7, 150, 16),
      array(8, 150, 18),
      array(9, 150, 20),
      array(10,150, 25),
      array(11,150, 30),
    );

    $n_totallength = $this->Length + $this->Width + $this->Height;

    while (list($n_index, $a_limit) = each($a_classes)) {
      if ($n_totallength <= $a_limit[1] && $this->Weight <= $a_limit[2]) {
        return $a_limit[0];
      }
    }
    return -1;  // 规格外
  }
  // 从送货地点和收货地点来获取地域rank(1～4)
/*--------------------------
 功能：获取地区排名 
 参数：无
 返回值：返回排名(string)
 -------------------------*/
  function GetDistRank() {
    // 地域-地域间的价格顺

    $a_dist_to_rank = array(
    array(1), // 始发:北海道 - 终点:北海道
    array(1,1), // 始发:青森县 - 终点:北海道,青森县
    array(2,1,1),
    array(3,1,1,1),
    array(2,1,1,1,1),
    array(3,1,1,1,1,1),
    array(3,1,1,1,1,1,1),
    array(4,1,1,1,1,1,1,1),
    array(4,1,1,1,1,1,1,1,1),
    array(4,2,2,1,1,1,1,1,1,1),
    array(4,2,2,1,2,1,1,1,1,1,1),
    array(4,2,2,1,2,1,1,1,1,1,1,1),
    array(4,2,2,1,2,1,1,1,1,1,1,1,1),
    array(4,2,2,1,2,1,1,1,1,1,1,1,1,1),
    array(3,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,2,2,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,3,2,2,2,2,2,1,1,1,2,1,1,1,1,1),
    array(4,3,3,2,3,2,2,2,1,1,1,2,1,1,1,1,1,1),
    array(4,2,2,1,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,2,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,3,2,3,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,2,2,3,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,3,2,3,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,3,3,2,3,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,2,2,2,2,2,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,3,2,2,2,2,2,2,2,1,2,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,3,3,2,2,2,2,2,2,1,2,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,3,3,2,2,2,2,2,2,2,2,1,1,1,2,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,3,3,2,2,2,2,2,2,1,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,3,3,3,3,2,2,2,2,2,2,2,2,1,1,1,2,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,1,1,1,2,2,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,2,2,2,3,3,1,2,2,2,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,1,1,1,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,2,2,2,3,3,1,2,2,2,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,2,2,2,3,3,1,2,2,2,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,1,1,1,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,1,1,1,2,2,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,2,2,2,3,3,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,2,2,2,3,3,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,4,4,3,3,3,3,2,2,2,2,2,2,1,1,1,1,1,2,1,1,2,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,4,4,3,3,3,3,3,3,2,2,3,3,1,1,1,1,1,2,1,1,2,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,4,4,3,3,3,3,3,3,2,2,3,3,2,1,2,1,1,2,2,1,2,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,4,4,3,3,3,3,3,3,2,2,3,3,2,1,2,1,1,2,1,1,2,1,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,4,4,3,3,3,3,3,3,2,2,3,3,2,1,2,1,1,1,1,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,3,3,3,2,2,2,2,1,2,2,1,2,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,3,3,3,2,2,2,2,1,2,2,1,2,1,1,1,1,1,1,1),
    array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,3,3,3,3,3,3,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1,1,1),
    );

    $n_rank = 0;
    if ( $this->OriginZone && $this->DestZone ) {
      $n_z1 = (int)$this->OriginZone - 1; // 转换成零起点
      $n_z2 = (int)$this->DestZone   - 1;

      // 获取rank
      if ( $n_z1 <= $n_z2 ) {
        $n_rank = $a_dist_to_rank[$n_z2][$n_z1];
      } else {
        $n_rank = $a_dist_to_rank[$n_z1][$n_z2];
      }
    }
    return $n_rank;
  }
/*---------------------------------
 功能：获取价格
 参数：无
 返回值：返回价格值(string)
 --------------------------------*/
  function GetQuote() {
    // 按照距离划分的价格顺: rank => 价格([2],[4],[6],[8]...[20],[25],[30])
    $a_pricerank = array(
    array( 510, 630, 750, 810, 870, 930, 990,1050,1110,1170,1320,1470),// 第一地域（市内）近距离
    array( 610, 770, 930,1010,1090,1170,1250,1330,1410,1490,1690,1890),// 第一地域(其他) ↑
    array( 710, 870,1030,1110,1190,1270,1350,1430,1510,1590,1790,1990),// 第二地域
    array( 820, 980,1140,1220,1300,1380,1460,1540,1620,1700,1900,2100),// 第三地域         ↓
    array(1020,1180,1340,1420,1500,1580,1660,1740,1820,1900,2100,2300) // 第四地域       远距离
    );

    if ( $this->OriginCountryCode == 'JP' && $this->DestCountryCode == 'JP' ) {
      $n_rank = $this->GetDistRank();
      if ( $n_rank ) {
        $n_sizeclass = $this->GetSizeClass();
        if ($n_sizeclass < 0) {
          $this->quote['error'] = MODULE_SHIPPING_YUUPACK_TEXT_OVERSIZE;
        } else {
          // 同一省市县内
          // if ( $this->OriginZone == $this->DestZone ) {
          //   $s_pattern = ($this->OriginZone == '13') ? '^(.+区)' : '^(.+市)';
          // }
          $this->quote['cost'] = $a_pricerank[$n_rank][$n_sizeclass];
        }
      // $this->quote['DEBUG'] = ' zone=' . $this->OriginZone . '=>' . $this->DestZone  //DEBUG
      // . ' cost=' . $a_pricerank[$n_rank][$n_sizeclass];      //DEBUG
      } else {
        $this->quote['error'] = MODULE_SHIPPING_YUUPACK_TEXT_ILLEGAL_ZONE
         . ' (' . $this->OriginZone . '=>' . $this->DestZone . ')';
      }
    } else {
      $this->quote['error'] = MODULE_SHIPPING_YUUPACK_TEXT_NOTAVAILABLE
       . ' (' . $this->OriginCountryCode . '=>' . $this->DestCountryCode . ')';
    }

    return $this->quote;
  }
}
?>
