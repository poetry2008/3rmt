<?php 
/*
  $Id$
*/

function mb_substr($str, $start = 0, $length = 0, $encode = '')
{
   return jsubstr($str, $start, $length);
}

//
// mb_strlen() function
//
function mb_strlen($str, $encoding)
{
    // jstrlen() - strlen() function for japanese(euc-jp)
    // for using shift_jis encoding, remove comment string.
    return jstrlen($str);
}

function mb_convert_kana($str, $option, $encode = 'auto')
{

   if ($encode == 'auto') { $encode_val = AutoDetect($str); }
   else { $encode_val = _check_encoding($str); }

   for ($i = 0; $i < strlen($option); $i++) {

      switch ( substr($option, $i, 1) ) {
         case 'a':
              $str = ZENtoHAN($str, $encode_val, 0, 1);
              break;
         case 'K':
              $str = HANtoZEN($str, $encode_val);
              break;
         case 'k':
              $str = ZENtoHAN($str, $encode_val, 1, 0);
              break;
      }
      return $str;
   }
}

function mb_convert_encoding($str, $to, $from = 'auto')
{
    $jc_to   = _check_encoding($to);
    if ($from == 'auto') { $jc_from = AutoDetect($str); }
    else { $jc_from = _check_encoding($from); }
    return JcodeConvert($str, $jc_from, $jc_to);
}

function mb_encode_mimeheader($str, $encode = 'ISO-2022-JP', $trn = 'B', $lf ='\r\n')
{
    return "=?".$encode."?".$trn."?".base64_encode(mb_convert_encoding($str, 'JIS', 'EUC'))."?=";
}

function _check_encoding($str_encoding)
{
    switch (strtolower($str_encoding)) {
        case 'e':
        case 'euc':
        case 'euc-jp':
            $jc_encoding = 1;
            break;
        case 's':
        case 'sjis':
        case 'shift_jis':
            $jc_encoding = 2;
            break;
        case 'j':
        case 'jis':
        case 'iso-2022-jp':
            $jc_encoding = 3;
            break;
        case 'u':
        case 'utf8':
        case 'utf-8':
            $jc_encoding = 4;
            break;
        default:
            $jc_encoding = 0;
            break;
    }
    return $jc_encoding;
}

?>
