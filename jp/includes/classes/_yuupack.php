<?php
/*
  $Id: _yuupack.php,v 1.2 2004/01/07 06:57:13 ptosh Exp $

  YuuPack Shipping Calculator.
  Calculate shipping costs.

  2002/04/23 written by TAMURA Toshihiko (tamura@bitscope.co.jp)
  2003/04/12 modified for ms1
 */
/*
  $rate = new _YuuPack('yuupack','�椦�ѥå�');
  $rate->SetOrigin('01', 'JP');  // �̳�ƻ����
  $rate->SetDest('13', 'JP');    // ����Ԥޤ�
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

  // ���󥹥ȥ饯��
  // $id:   module id
  // $titl: module name
  // $zone: ��ƻ�ܸ������� '01'��'47'
  // $country: country code
  function _YuuPack($id, $title, $zone = NULL, $country = NULL) {
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
  // ���ʳ��ξ���-1���֤�
  //
  // ��ʬ ���շ�    ����
  // ---------------------------------
  // 0    150cm�ޤ�  2kg�ޤ�
  // 1    150cm�ޤ�  4kg�ޤ�
  // 2    150cm�ޤ�  6kg�ޤ�
  // 3    150cm�ޤ�  8kg�ޤ�
  // 4    150cm�ޤ� 10kg�ޤ�
  // 5    150cm�ޤ� 12kg�ޤ�
  // 6    150cm�ޤ� 14kg�ޤ�
  // 7    150cm�ޤ� 16kg�ޤ�
  // 8    150cm�ޤ� 18kg�ޤ�
  // 9    150cm�ޤ� 20kg�ޤ�
  // 10   150cm�ޤ� 25kg�ޤ�
  // 11   150cm�ޤ� 30kg�ޤ�
  function GetSizeClass() {
    $a_classes = array(
      array(0, 150,  2),  // ��ʬ,���շ�(cm�ޤ�),����(kg�ޤ�)
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
    return -1;  // ���ʳ�
  }
  // ���ո��������褫�����ӥ��(1��4)���������
  //
  function GetDistRank() {
    // ���� - ���Ӵ֤β��ʥ��
    // (����) http://www.post.yusei.go.jp/service/parcel/you_pack/
    $a_dist_to_rank = array(
    array(1), // ����:�̳�ƻ - ����:�̳�ƻ
    array(1,1), // ����:�Ŀ��� - ����:�̳�ƻ,�Ŀ���
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
      $n_z1 = (int)$this->OriginZone - 1; // �������ꥸ����Ѵ�
      $n_z2 = (int)$this->DestZone   - 1;

      // ��󥯤����
      if ( $n_z1 <= $n_z2 ) {
        $n_rank = $a_dist_to_rank[$n_z2][$n_z1];
      } else {
        $n_rank = $a_dist_to_rank[$n_z1][$n_z2];
      }
    }
    return $n_rank;
  }

  function GetQuote() {
    // ��Υ�̤β��ʥ��: ��� => ����([2],[4],[6],[8]...[20],[25],[30])
    $a_pricerank = array(
    array( 510, 630, 750, 810, 870, 930, 990,1050,1110,1170,1320,1470),// �裱����(����) ���Υ
    array( 610, 770, 930,1010,1090,1170,1250,1330,1410,1490,1690,1890),// �裱����(����¾) ��
    array( 710, 870,1030,1110,1190,1270,1350,1430,1510,1590,1790,1990),// �裲����
    array( 820, 980,1140,1220,1300,1380,1460,1540,1620,1700,1900,2100),// �裳����         ��
    array(1020,1180,1340,1420,1500,1580,1660,1740,1820,1900,2100,2300) // �裴����       ���Υ
    );

    if ( $this->OriginCountryCode == 'JP' && $this->DestCountryCode == 'JP' ) {
      $n_rank = $this->GetDistRank();
      if ( $n_rank ) {
        $n_sizeclass = $this->GetSizeClass();
        if ($n_sizeclass < 0) {
          $this->quote['error'] = MODULE_SHIPPING_YUUPACK_TEXT_OVERSIZE;
        } else {
          // Ʊ����ƻ�ܸ���
          // if ( $this->OriginZone == $this->DestZone ) {
          //   $s_pattern = ($this->OriginZone == '13') ? '^(.+��)' : '^(.+��)';
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