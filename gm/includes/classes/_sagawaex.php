<?php
/*
  $Id: _sagawaex.php,v 1.1 2003/04/12 11:28:46 ptosh Exp $

  SagawaEx Shipping Calculator.
  Calculate shipping costs.

  2002/03/29 written by TAMURA Toshihiko (tamura@bitscope.co.jp)
  2003/04/10 modified for ms1
 */
/*
    $rate = new _SagawaEx('sagawaex','�̾���');
    $rate->SetOrigin('01', 'JP');   // �̳�ƻ����
    $rate->SetDest('13', 'JP');     // ����Ԥޤ�
    $rate->SetWeight(10);           // kg
    $quote = $rate->GetQuote();
    print $quote['type'] . "<br>";
    print $quote['cost'] . "\n";
*/
class _SagawaEx {
    var $quote;
    var $OriginZone;
    var $OriginCountryCode = 'JP';
    var $DestZone;
    var $DestCountryCode = 'JP';
    var $Weight = 0;
    var $Length = 0;
    var $Width  = 0;
    var $Height = 0;

    // ���󥹥ȥ饯��
    // $id:   module id
    // $titl: module name
    // $zone: ��ƻ�ܸ������� '01'��'47'
    // $country: country code
    function _SagawaEx($id, $title, $zone = NULL, $country = NULL) {
        $this->quote = array('id' => $id, 'title' => $title);
        if($zone) {
            $this->SetOrigin($zone, $country);
        }
    }
    // ȯ�����򥻥åȤ���
    // $zone: ��ƻ�ܸ������� '01'��'47'
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
    // ��������ʬ(0��4)���֤�
    // ���ʳ��ξ���9���֤�
    //
    // ��ʬ  ������̾  ���շ�   ����
    // ----------------------------------
    // 0     60������  60cm�ޤ�  2kg�ޤ�
    // 1     80������  80cm�ޤ�  5kg�ޤ�
    // 2    100������ 100cm�ޤ� 10kg�ޤ�
    // 3    140������ 140cm�ޤ� 20kg�ޤ�
    // 4    160������ 160cm�ޤ� 30kg�ޤ�
    // 9    ���ʳ�    
    function GetSizeClass() {
        $a_classes = array(
            array(0,  60,  2),  // ��ʬ,���շ�,����
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
        return -1;  // ���ʳ�
    }
    // ���ո��������褫�饭�����������
    //
    function GetDistKey() {
        $s_key = '';
        $s_z1 = $this->GetLZone($this->OriginZone);
        $s_z2 = $this->GetLZone($this->DestZone);
        if ( $s_z1 && $s_z2 ) {
            // ���ӥ����ɤ򥢥�ե��٥åȽ��Ϣ�뤹��
            if ( ord($s_z1) < ord($s_z2) ) {
                $s_key = $s_z1 . $s_z2;
            } else {
                $s_key = $s_z2 . $s_z1;
            }
        }
        return $s_key;
    }
    // ��ƻ�ܸ������ɤ������ӥ����ɤ��������
    // $zone: ��ƻ�ܸ�������
    function GetLZone($zone) {
        // ��ƻ�ܸ������ɤ����ӥ�����('A'��'M')���Ѵ�����
        //  �̳�ƻ:'A' = �̳�ƻ
        //  ������:'B' = �Ŀ���,��긩,���ĸ�
        //  ������:'C' = �ܾ븩,������,ʡ�縩
        //  ����  :'D' = ��븩,���ڸ�,���ϸ�,��̸�,���ո�,�����,�����,������
        //  ����  :'E' = ���㸩,Ĺ�
        //  �쳤  :'F' = ���츩,�Ų���,���θ�,���Ÿ�
        //  ��Φ  :'G' = �ٻ���,���,ʡ�温
        //  ����  :'H' = ���츩,������,�����,ʼ�˸�,���ɸ�,�²λ���
        //  ���  :'I' = Ļ�踩,�纬��,������,���縩,������
        //  �͹�  :'J' = ���縩,���,��ɲ��,���θ�
        //  �̶彣:'K' = ʡ����,���츩,Ĺ�긩,��ʬ��
        //  ��彣:'L' = ���ܸ�,�ܺ긩,�����縩
        //  ����  :'M' = ���츩 (�̾��ؤ���ã��賰)
        $a_zonemap = array(
        '01'=>'A',  // �̳�ƻ
        '02'=>'B',  // �Ŀ���
        '03'=>'B',  // ��긩
        '04'=>'C',  // �ܾ븩
        '05'=>'B',  // ���ĸ�
        '06'=>'C',  // ������
        '07'=>'C',  // ʡ�縩
        '08'=>'D',  // ��븩
        '09'=>'D',  // ���ڸ�
        '10'=>'D',  // ���ϸ�
        '11'=>'D',  // ��̸�
        '12'=>'D',  // ���ո�
        '13'=>'D',  // �����
        '14'=>'D',  // �����
        '15'=>'E',  // ���㸩
        '16'=>'G',  // �ٻ���
        '17'=>'G',  // ���
        '18'=>'G',  // ʡ�温
        '19'=>'D',  // ������
        '20'=>'E',  // Ĺ�
        '21'=>'F',  // ���츩
        '22'=>'F',  // �Ų���
        '23'=>'F',  // ���θ�
        '24'=>'F',  // ���Ÿ�
        '25'=>'H',  // ���츩
        '26'=>'H',  // ������
        '27'=>'H',  // �����
        '28'=>'H',  // ʼ�˸�
        '29'=>'H',  // ���ɸ�
        '30'=>'H',  // �²λ���
        '31'=>'I',  // Ļ�踩
        '32'=>'I',  // �纬��
        '33'=>'I',  // ������
        '34'=>'I',  // ���縩
        '35'=>'I',  // ������
        '36'=>'J',  // ���縩
        '37'=>'J',  // ���
        '38'=>'J',  // ��ɲ��
        '39'=>'J',  // ���θ�
        '40'=>'K',  // ʡ����
        '41'=>'K',  // ���츩
        '42'=>'K',  // Ĺ�긩
        '43'=>'L',  // ���ܸ�
        '44'=>'K',  // ��ʬ��
        '45'=>'L',  // �ܺ긩
        '46'=>'L',  // �����縩
        '47'=>'M'   // ���츩
        );
        return $a_zonemap[$zone];
    }

    function GetQuote() {
        // ��Υ�̤β��ʥ��: ��󥯥����� => ����(60,80,100,140,160)
        $a_pricerank = array(
        'N01'=>array(630, 630,630,630,630), // �̾���(01) ���Υ
        'N02'=>array(630, 630,630,630,630), // �̾���(02)   ��
        'N03'=>array(630, 630,630,630,630), // �̾���(03)
        'N04'=>array(630, 630,630,630,630), // �̾���(04)
        'N05'=>array(630, 630,630,630,630), // �̾���(05)
        'N06'=>array(630, 630,630,630,630), // �̾���(06)
        'N07'=>array(630, 630,630,630,630), // �̾���(07)
        'N08'=>array(630, 630,630,630,630), // �̾���(08)
        'N09'=>array(630, 630,630,630,630), // �̾���(09)
        'N10'=>array(630, 630,630,630,630), // �̾���(10)   ��
        'N11'=>array(630, 630,630,630,630)  // �̾���(11) ���Υ
        );
        // ���� - ���Ӵ֤β��ʥ��
        // (����) http://www.sagawa-exp.co.jp/business/budjetserch-j.html
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