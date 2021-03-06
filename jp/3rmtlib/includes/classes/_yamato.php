<?php
/*
  $Id$
 */
/*
    $rate = new _Yamato('yamato','宅急便');
    $rate->SetOrigin('01', 'JP');   // 从北海道
    $rate->SetDest('13', 'JP');     // 到东京
    $rate->SetWeight(10);           // kg
    $quote = $rate->GetQuote();
    print $quote['type'] . "<br>";
    print $quote['cost'] . "\n";
*/
class _Yamato {
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
    function _Yamato($id, $title, $zone = NULL, $country = NULL) {
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
    // 规格以外的时候返回9
    //
    // 区分  尺寸名  3边合计   重量
    // ----------------------------------
    // 0     60size  60cm  2kg
    // 1     80size  80cm  5kg
    // 2    100size 100cm 10kg
    // 3    120size 120cm 15kg
    // 4    140size 140cm 20kg
    // 5    160size 160cm 25kg
    // 9    规格外    
/*-------------------------------
 功能：获取尺寸区分 
 参数：无
 返回值：返回尺寸区分
 ------------------------------*/
    function GetSizeClass() {
        $a_classes = array(
            array(0,  60,  2),  // 区分，3边合计，重量
            array(1,  80,  5),
            array(2, 100, 10),
            array(3, 120, 15),
            array(4, 140, 20),
            array(5, 160, 25)
        );

        $n_totallength = $this->Length + $this->Width + $this->Height;

        while (list($n_index, $a_limit) = each($a_classes)) {
            if ($n_totallength <= $a_limit[1] && $this->Weight <= $a_limit[2]) {
                return $a_limit[0];
            }
        }
        return -1;  // 规格外
    }
    // 用发货地址和收货地址来做成key
    //
/*----------------------
 功能：获取地址
 参数：无
 返回值：返回发货地址和收货地址(string)
 ---------------------*/
    function GetDistKey() {
        $s_key = '';
        $s_z1 = $this->GetLZone($this->OriginZone);
        $s_z2 = $this->GetLZone($this->DestZone);
        if ( $s_z1 && $s_z2 ) {
            // 地域代码用阿拉伯数字连接
            if ( ord($s_z1) < ord($s_z2) ) {
                $s_key = $s_z1 . $s_z2;
            } else {
                $s_key = $s_z2 . $s_z1;
            }
        }
        return $s_key;
    }
/*--------------------------------
  功能：通过省市县代码来获取地域代码
  参数： $zone: 省市县代码
  返回值：返回区域代码数组  (string)
-------------------------------*/
    function GetLZone($zone) {
        // 把省市县代码替换成地域代码('A'～'M')
        //  北海道:'A' = 北海道
        //  北東北:'B' = 青森県,岩手県,秋田県
        //  南東北:'C' = 宮城県,山形県,福島県
        //  関東  :'D' = 茨城県,栃木県,群馬県,埼玉県,千葉県,東京都,神奈川県,山梨県
        //  信越  :'E' = 新潟県,長野県
        //  中部  :'F' = 岐阜県,静岡県,愛知県,三重県
        //  北陸  :'G' = 富山県,石川県,福井県
        //  関西  :'H' = 滋賀県,京都府,大阪府,兵庫県,奈良県,和歌山県
        //  中国  :'I' = 鳥取県,島根県,岡山県,広島県,山口県
        //  四国  :'J' = 徳島県,香川県,愛媛県,高知県
        //  九州  :'K' = 福岡県,佐賀県,長崎県,大分県,熊本県,宮崎県,鹿児島県
        //  沖縄  :'L' = 沖縄県
        $a_zonemap = array(
        '01'=>'A',  // 北海道
        '02'=>'B',  // 青森県
        '03'=>'B',  // 岩手県
        '04'=>'C',  // 宮城県
        '05'=>'B',  // 秋田県
        '06'=>'C',  // 山形県
        '07'=>'C',  // 福島県
        '08'=>'D',  // 茨城県
        '09'=>'D',  // 栃木県
        '10'=>'D',  // 群馬県
        '11'=>'D',  // 埼玉県
        '12'=>'D',  // 千葉県
        '13'=>'D',  // 東京都
        '14'=>'D',  // 神奈川県
        '15'=>'E',  // 新潟県
        '16'=>'G',  // 富山県
        '17'=>'G',  // 石川県
        '18'=>'G',  // 福井県
        '19'=>'D',  // 山梨県
        '20'=>'E',  // 長野県
        '21'=>'F',  // 岐阜県
        '22'=>'F',  // 静岡県
        '23'=>'F',  // 愛知県
        '24'=>'F',  // 三重県
        '25'=>'H',  // 滋賀県
        '26'=>'H',  // 京都府
        '27'=>'H',  // 大阪府
        '28'=>'H',  // 兵庫県
        '29'=>'H',  // 奈良県
        '30'=>'H',  // 和歌山県
        '31'=>'I',  // 鳥取県
        '32'=>'I',  // 島根県
        '33'=>'I',  // 岡山県
        '34'=>'I',  // 広島県
        '35'=>'I',  // 山口県
        '36'=>'J',  // 徳島県
        '37'=>'J',  // 香川県
        '38'=>'J',  // 愛媛県
        '39'=>'J',  // 高知県
        '40'=>'K',  // 福岡県
        '41'=>'K',  // 佐賀県
        '42'=>'K',  // 長崎県
        '43'=>'K',  // 熊本県
        '44'=>'K',  // 大分県
        '45'=>'K',  // 宮崎県
        '46'=>'K',  // 鹿児島県
        '47'=>'L'   // 沖縄県
        );
        return $a_zonemap[$zone];
    }
/*-------------------------
 功能：获取价格 
 参数：无
 返回值：返回价格(string)
 ------------------------*/
    function GetQuote() {
        // [通常邮寄] 按照距离定的价格顺
        // rankcode => 价格(60,80,100,120,140,160)
        $a_pricerank = array(
        'N01'=>array( 630, 630,630,630,630,630),// (01) 近距离
        'N02'=>array( 630, 630,630,630,630,630),// (02)   ↑
        'N03'=>array( 630, 630,630,630,630,630),// (03)
        'N04'=>array(630, 630,630,630,630,630),// (04)
        'N05'=>array(630, 630,630,630,630,630),// (05)
        'N06'=>array(630, 630,630,630,630,630),// (06)
        'N07'=>array(630, 630,630,630,630,630),// (07)
        'N08'=>array(630, 630,630,630,630,630),// (08)
        'N09'=>array(630, 630,630,630,630,630),// (09)
        'N10'=>array(630, 630,630,630,630,630),// (10)   ↓
        'N11'=>array(630, 630,630,630,630,630),// (11) 远距离
        'X05'=>array(630, 630,630,630,630,630),// 
        'X06'=>array(630, 630,630,630,630,630),// 
        'X07'=>array(630, 630,630,630,630,630),// 
        'X08'=>array(630, 630,630,630,630,630),// 
        'X09'=>array(630, 630,630,630,630,630),// 
        'X12'=>array(630, 630,630,630,630,630) // 
        );
        // 地域-地域间的价格顺

        $a_dist_to_rank = array(
        'AA'=>'N01',
        'AB'=>'N03','BB'=>'N01',
        'AC'=>'N04','BC'=>'N01','CC'=>'N01',
        'AD'=>'N05','BD'=>'N02','CD'=>'N01','DD'=>'N01',
        'AE'=>'N05','BE'=>'N02','CE'=>'N01','DE'=>'N01','EE'=>'N01',
        'AF'=>'N06','BF'=>'N03','CF'=>'N02','DF'=>'N01','EF'=>'N01','FF'=>'N01',
        'AG'=>'N06','BG'=>'N03','CG'=>'N02','DG'=>'N01','EG'=>'N01','FG'=>'N01','GG'=>'N01',
        'AH'=>'N08','BH'=>'N04','CH'=>'N03','DH'=>'N02','EH'=>'N02','FH'=>'N01','GH'=>'N01','HH'=>'N01',
        'AI'=>'N09','BI'=>'N05','CI'=>'N05','DI'=>'N03','EI'=>'N03','FI'=>'N02','GI'=>'N02','HI'=>'N01','II'=>'N01',
        'AJ'=>'N10','BJ'=>'N06','CJ'=>'N06','DJ'=>'N04','EJ'=>'N04','FJ'=>'N03','GJ'=>'N03','HJ'=>'N02','IJ'=>'N02','JJ'=>'N01',
        'AK'=>'N11','BK'=>'N07','CK'=>'N07','DK'=>'N05','EK'=>'N05','FK'=>'N03','GK'=>'N03','HK'=>'N02','IK'=>'N01','JK'=>'N02','KK'=>'N01',
        'AL'=>'X12','BL'=>'X09','CL'=>'X08','DL'=>'X06','EL'=>'X07','FL'=>'X06','GL'=>'X07','HL'=>'X06','IL'=>'X06','JL'=>'X06','KL'=>'X05','LL'=>'N01'
        );

        $s_key = $this->GetDistKey();
        if ( $s_key ) {
            $s_rank = $a_dist_to_rank[$s_key];
            if ( $s_rank ) {
                $n_sizeclass = $this->GetSizeClass();
                if ($n_sizeclass < 0) {
                    $this->quote['error'] = MODULE_SHIPPING_YAMATO_TEXT_OVERSIZE;
                } else {
                    $this->quote['cost'] = $a_pricerank[$s_rank][$n_sizeclass];
                }
            } else {
                $this->quote['error'] = MODULE_SHIPPING_YAMATO_TEXT_OUT_OF_AREA . '(' . $s_key .')';
            }
        } else {
            $this->quote['error'] = MODULE_SHIPPING_YAMATO_TEXT_ILLEGAL_ZONE . '(' . $this->OriginZone . '=>' . $this->DestZone . ')';
        }

        return $this->quote;
    }
}
?>
