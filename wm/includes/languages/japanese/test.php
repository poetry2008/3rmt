<?php require('includes/application_top.php');

    $customer_level_array = explode("||",MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
	if(!empty($customer_level_array)) {
	   $customer_lebel_string = '<ul>'."\n";
	    for($i=0,$n=sizeof($customer_level_array); $i < $n; $i++){
      	   $customer_lebel_detail = explode(",",$customer_level_array[$i]);
		   $customer_lebel_string .= '<li>今までの当店での購入金額が'.$customer_lebel_detail['2'][$i].'円以下のお客様:'.$customer_lebel_detail[$i][0].'&nbsp;&nbsp;<b>'.(int)($customer_lebel_detail[1][$i]*100).'</b>ポイント'."\n" ;
	    }
	   $customer_lebel_string .= '</ul>'."\n";
	   define('TEXT_POINT','<p class="main"><i><strong>ポイントシステム</strong></i><br>ポイントサービスは、当店でお買い物をされた場合、過去'.MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN.'日間における購入金額に応じて還元されるポイントレベルが異なります。ポイント還元率は以下の通りです。</p>
              '.$customer_lebel_string.'<p class="main">次回のお買い物に1ポイント＝1円で使えます。ポイントの有効期限は'.MODULE_ORDER_TOTAL_POINT_LIMIT.'日です。</p>');
	 }
	 echo TEXT_POINT ;
?>