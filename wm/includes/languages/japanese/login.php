<?php
/*
  $Id: login.php,v 1.8 2003/05/22 10:55:46 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

if ($HTTP_GET_VARS['origin'] == FILENAME_CHECKOUT_PAYMENT) {
  define('NAVBAR_TITLE', '����ʸ');
  define('HEADING_TITLE', '����ʸ�ϴ�ñ');
  define('TEXT_STEP_BY_STEP', '��ʸ��³���򣱥��ƥåפ��Ȥˤ��������������ޤ�');
} else {
  define('NAVBAR_TITLE', '������');
  define('HEADING_TITLE', '�褦����!');
  define('TEXT_STEP_BY_STEP', ''); // should be empty
}


define('TEXT_MAIL','�᡼�륢�ɥ쥹�����Ϥ��Ƥ���������');
define('TEXT_FIRST_BUY','����������ʪ�򤵤줿���Ȥ�����ޤ���?');
define('HEADING_NEW_CUSTOMER', '��Ͻ��ƤǤ���');
define('TEXT_NEW_CUSTOMER', '<b>�μ��ؿʤ��</b>�򥯥�å����Ƥ���������');
define('TEXT_NEW_CUSTOMER_INTRODUCTION', '<font color="red"><b>�����Ͽ�򤷤ʤ�����ʸ���뤪���ͤ⤳���餫�餪��³������������</b></font><br>�����Ͽ�򤹤�ȡ�&nbsp;�᡼�륢�ɥ쥹�ȥѥ���ɤ����Ϥ�������Ǵ�ñ�˥����󤬤Ǥ��ơ� '.STORE_NAME.' �������ˤ���ʪ���Ǥ��ޤ���');

define('HEADING_RETURNING_CUSTOMER', '���˥�������Ȥ���äƤ��ޤ���');
define('TEXT_RETURNING_CUSTOMER', '�᡼�륢�ɥ쥹�ȥѥ���ɤ����Ϥ��ơ������󤷤Ƥ���������');
define('ENTRY_EMAIL_ADDRESS', '�᡼�륢�ɥ쥹:');
define('ENTRY_PASSWORD', '�ѥ����:');

define('TEXT_PASSWORD_FORGOTTEN', '�ѥ���ɤ�˺��ξ��Ϥ�����򥯥�å�!');

define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>���顼:</b>"�᡼�륢�ɥ쥹" �ޤ��� "�ѥ����" �����פ��ޤ���Ǥ�����</font>');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>�����:</b></font> �����󤹤�ȡ�[����åԥ󥰥�����] �ξ��ʤ� [���С���������åԥ󥰥�����] �ؼ�ưŪ�˰�ư���ޤ��� <a href="javascript:session_win();"> [�ܺپ���]</a>');

if(MODULE_ORDER_TOTAL_POINT_STATUS == "true"){
   //�̾�Υݥ���ȥ����ƥ�
   if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL != "true") {
      $point_text_1 = MODULE_ORDER_TOTAL_POINT_FEE*100 ;
        if(MODULE_ORDER_TOTAL_POINT_LIMIT != "0"){
          $point_text_2 = '�ǽ����������'.MODULE_ORDER_TOTAL_POINT_LIMIT.'����ͭ���Ǥ�' ;
        }else{
          $point_text_2 = '����ޤ���' ;
        }
		define('TEXT_POINT','<p class="main"><i><strong>�ݥ���ȥ����ƥ�</strong></i><br>�ݥ���ȥ����ӥ��ϡ���Ź�Ǥ��㤤ʪ�򤵤줿��硢������ۤ�'.$point_text_1.'%��ݥ���ȤȤ��ƴԸ����Ƥ���ޤ���<br>
              ί�ޤä��ݥ���Ȥϼ���Τ��㤤ʪ��1�ݥ���ȡ�1�ߤǻȤ��ޤ����ݥ���Ȥ�ͭ�����¤�'.$point_text_2.'��</p>');
   }else{
   //�������ޡ���٥�Ϣư���ݥ���ȥ����ƥ�
    $customer_level_array = explode("||",MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
	if(!empty($customer_level_array)) {
	   $customer_lebel_string = '<ul>'."\n";
	    for($i=0,$n=sizeof($customer_level_array); $i < $n; $i++){
      	   $customer_lebel_detail = explode(",",$customer_level_array[$i]);
		   $customer_lebel_string .= '<li>���ޤǤ���Ź�Ǥι�����ۤ�'.$customer_lebel_detail[2].'�߰ʲ��Τ�����:'.$customer_lebel_detail[0].'&nbsp;&nbsp;<b>'.(int)($customer_lebel_detail[1]*100).'</b>�ݥ����</li>'."\n" ;
	    }
	   $customer_lebel_string .= '</ul>'."\n";
	   define('TEXT_POINT','<p class="main"><i><strong>�ݥ���ȥ����ƥ�</strong></i><br>�ݥ���ȥ����ӥ��ϡ���Ź�Ǥ��㤤ʪ�򤵤줿��硢���'.MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN.'���֤ˤ����������ۤ˱����ƴԸ������ݥ���ȥ�٥뤬�ۤʤ�ޤ����ݥ���ȴԸ�Ψ�ϰʲ����̤�Ǥ���</p>
              '.$customer_lebel_string.'<p class="main">����Τ��㤤ʪ��1�ݥ���ȡ�1�ߤǻȤ��ޤ����ݥ���Ȥ�ͭ�����¤�'.MODULE_ORDER_TOTAL_POINT_LIMIT.'���Ǥ���</p>');
	 }
  }
//�ݥ���ȥ����ƥ��Ժ��� 		  
 }else{
  define('TEXT_POINT','');
 }  
  		
?>