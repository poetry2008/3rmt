<?php
/*
  $Id$
 */
/*
    
    var $quote;
    var $OriginZone;
    var $OriginCountryCode = 'JP';
    var $DestZone;
    var $DestCountryCode = 'JP';
    var $Weight = 0;
    var $Length = 0;
    var $Width  = 0;
    var $Height = 0;

    // 构造函数
    // $id:   module id
    // $titl: module name
    // $zone: 省市县代码 '01'～'47'
    // $country: country code
    function _SagawaEx($id, $title, $zone = NULL, $country = NULL) {
        $this->quote = array('id' => $id, 'title' => $title);
        if($zone) {
            $this->SetOrigin($zone, $country);
        }
    }
    // 设置发送地
    // $zone: 省市县代码 '01'～'47'
    // $country: country code
    function SetOrigin($zone, $country = NULL) {
        $this->OriginZone = $zone;
        if($country) {
            $this->OriginCountryCode = $country;
        }
    }
    function SetDest($zone, $country = NULL) {
        $this->DestZone = $zone;
        if($country) {
            $this->DestCountryCode = $country;
        }
    }
    function SetWeight($weight) {
        $this->Weight = $weight;
    }
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
    // 0     60size  60cm      2kg
    // 1     80size  80cm      5kg
    // 2    100size 100cm      10kg
    // 3    140size 140cm      20kg
    // 4    160size 160cm      30kg
    // 9    规格外    
    function GetSizeClass() {
        $a_classes = array(
            array(0,  60,  2),  // 区分，3边合计，重量
            array(1,  80,  5),
            array(2, 100, 10),
            array(3, 140, 20),
            array(4, 160, 30)
        );

        $n_totallength = $this->Length + $this->Width + $this->Height;

        while (list($n_index, $a_limit) = each($a_classes)) {
            if ($n_totallength <= $a_limit[1] && $this->Weight <= $a_limit[2]) {
                return $a_limit[0];
            }
        }
        return -1;  // 规格外
    }
    //用送货地点和收货地点来做成key
    //
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
    // 通过省市县代码来获取地域代码
    // $zone: 省市县代码
        function GetLZone($zone) {
        // 把省市县代码替换成地域代码('A'～'M')
        //  北海道:'A' = 北海道
        //  北東北:'B' = 青森県,岩手県,秋田県
        //  南東北:'C' = 宮城県,山形県,福島県
        //  関東  :'D' = 茨城県,栃木県,群馬県,埼玉県,千葉県,東京都,神奈川県,山梨県
        //  信越  :'E' = 新潟県,長野県
        //  東海  :'F' = 岐阜県,静岡県,愛知県,三重県
        //  北陸  :'G' = 富山県,石川県,福井県
        //  関西  :'H' = 滋賀県,京都府,大阪府,兵庫県,奈良県,和歌山県
        //  中国  :'I' = 鳥取県,島根県,岡山県,広島県,山口県
        //  四国  :'J' = 徳島県,香川県,愛媛県,高知県
        //  北九州:'K' = 福岡県,佐賀県,長崎県,大分県
        //  南九州:'L' = 熊本県,宮崎県,鹿児島県
        //  沖縄  :'M' = 沖縄県 (通常便は配達区域外)
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
        '43'=>'L',  // 熊本県
        '44'=>'K',  // 大分県
        '45'=>'L',  // 宮崎県
        '46'=>'L',  // 鹿児島県
        '47'=>'M'   // 沖縄県
        );
        return $a_zonemap[$zone];
    }

    function GetQuote() {
        // 按照距离定的价格顺: rankcode => 价格(60,80,100,140,160)
        $a_pricerank = array(
        'N01'=>array(630, 630,630,630,630), // 通常邮寄(01) 近距离
        'N02'=>array(630, 630,630,630,630), // 通常邮寄(02)   ↑
        'N03'=>array(630, 630,630,630,630), // 通常邮寄(03)
        'N04'=>array(630, 630,630,630,630), // 通常邮寄(04)
        'N05'=>array(630, 630,630,630,630), // 通常邮寄(05)
        'N06'=>array(630, 630,630,630,630), // 通常邮寄(06)
        'N07'=>array(630, 630,630,630,630), // 通常邮寄(07)
        'N08'=>array(630, 630,630,630,630), // 通常邮寄(08)
        'N09'=>array(630, 630,630,630,630), // 通常邮寄(09)
        'N10'=>array(630, 630,630,630,630), // 通常邮寄(10)   ↓
        'N11'=>array(630, 630,630,630,630)  // 通常邮寄(11) 远距离
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
        'AL'=>'N11','BL'=>'N07','CL'=>'N07','DL'=>'N05','EL'=>'N05','FL'=>'N03','GL'=>'N03','HL'=>'N02','IL'=>'N01','JL'=>'N02','KL'=>'N01','LL'=>'N01',
        'AM'=>'',   'BM'=>'',   'CM'=>'',   'DM'=>'',   'EM'=>'',   'FM'=>'',   'GM'=>'',   'HM'=>'',   'IM'=>'',   'JM'=>'',   'KM'=>'',   'LM'=>'',   'MM'=>''
        );

        $s_key = $this->GetDistKey();
        if ( $s_key ) {
            $s_rank = $a_dist_to_rank[$s_key];
            if ( $s_rank ) {
                $n_sizeclass = $this->GetSizeClass();
                if ($n_sizeclass < 0) {
                    $this->quote['error'] = MODULE_SHIPPING_SAGAWAEX_TEXT_OVERSIZE;
                } else {
                    $this->quote['cost'] = $a_pricerank[$s_rank][$n_sizeclass];
                }
            //  $this->quote['DEBUG'] = ' zone=' . $this->OriginZone . '=>' . $this->DestZone   //DEBUG
            //                  . ' cost=' . $a_pricerank[$s_rank][$n_sizeclass];           //DEBUG
            } else {
                $this->quote['error'] = MODULE_SHIPPING_SAGAWAEX_TEXT_OUT_OF_AREA . '(' . $s_key .')';
            }
        } else {
            $this->quote['error'] = MODULE_SHIPPING_SAGAWAEX_TEXT_ILLEGAL_ZONE . '(' . $this->OriginZone . '=>' . $this->DestZone . ')';
        }

        return $this->quote;
    }
}
?>