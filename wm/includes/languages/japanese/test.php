<?php require('includes/application_top.php');

    $customer_level_array = explode("||",MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
	if(!empty($customer_level_array)) {
	   $customer_lebel_string = '<ul>'."\n";
	    for($i=0,$n=sizeof($customer_level_array); $i < $n; $i++){
      	   $customer_lebel_detail = explode(",",$customer_level_array[$i]);
		   $customer_lebel_string .= '<li>���ޤǤ���Ź�Ǥι�����ۤ�'.$customer_lebel_detail['2'][$i].'�߰ʲ��Τ�����:'.$customer_lebel_detail[$i][0].'&nbsp;&nbsp;<b>'.(int)($customer_lebel_detail[1][$i]*100).'</b>�ݥ����'."\n" ;
	    }
	   $customer_lebel_string .= '</ul>'."\n";
	   define('TEXT_POINT','<p class="main"><i><strong>�ݥ���ȥ����ƥ�</strong></i><br>�ݥ���ȥ����ӥ��ϡ���Ź�Ǥ��㤤ʪ�򤵤줿��硢���'.MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN.'���֤ˤ����������ۤ˱����ƴԸ������ݥ���ȥ�٥뤬�ۤʤ�ޤ����ݥ���ȴԸ�Ψ�ϰʲ����̤�Ǥ���</p>
              '.$customer_lebel_string.'<p class="main">����Τ��㤤ʪ��1�ݥ���ȡ�1�ߤǻȤ��ޤ����ݥ���Ȥ�ͭ�����¤�'.MODULE_ORDER_TOTAL_POINT_LIMIT.'���Ǥ���</p>');
	 }
	 echo TEXT_POINT ;
?>