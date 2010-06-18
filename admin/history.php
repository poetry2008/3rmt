<?php 
	require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

	$cPath=$_GET['cpath'];
	$cID=$_GET['cid'];
	
	
?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<title>履歴表示</title>
</head>
<?php
 switch ($HTTP_GET_VARS['action']){
	case 'oroshi':
	$res=tep_db_query("select * from set_oroshi_names where parent_id='".$cPath."' ORDER BY oroshi_id ASC");
	$cnt=0;
	while($col=tep_db_fetch_array($res)){
		$o_name[]=$col['oroshi_name'];
		$cnt++;
	}
	$res=tep_db_query("select count(*) as cnt_data from set_oroshi_datas where parent_id='".$cPath."' ORDER BY list_id DESC");
	$col=tep_db_fetch_array($res);
	$cnt_data=$col['cnt_data'];
$a=0;
	for($k=0;$k<20;$k++){//過去20件
?>
		<table border="1">

		<tr>
<?php	
		for($i=0;$i<$cnt;$i++){
			$res=tep_db_query("select set_date from set_oroshi_datas where parent_id='".$cPath."' && oroshi_name='".$o_name[$i]."' ORDER BY list_id DESC  limit ".$a.",1 ");
			$col=tep_db_fetch_array($res);
			echo "<td align='center'>".$col['set_date']."</td>";
		}

?>

		</tr>

		<tr>
<?php
		for($i=0;$i<$cnt;$i++){
			$res=tep_db_query("select oroshi_name from set_oroshi_datas where parent_id='".$cPath."' && oroshi_name='".$o_name[$i]."' ORDER BY list_id DESC");
			$col=tep_db_fetch_array($res);
			echo "<th align='center'>".$col['oroshi_name']."</th>";
		}
?>
		</tr>

<?php
		$cr = array("\r\n", "\r");   // 改行コード置換用配
		for($i=0;$i<=$cnt;$i++){
			$res = tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' && oroshi_name='".$o_name[$i]."' ORDER BY list_id DESC limit ".$a.",1 ");
			$col[$i] = tep_db_fetch_array($res);
			$col[$i]['datas'] = trim($col[$i]['datas']);         // 文頭文末の空白を削除
			$col[$i]['datas'] = str_replace($cr, "\n",$col[$i]['datas']);  // 改行コードを統一
			$lines[$i] = explode("\n", $col[$i]['datas']);
			$count[$i]=count($lines[$i]);
		}
		
		for($n=0;$n<$cnt;$n++){//取得したデータでどれが一番件数が大きいか
			if($count[0]<=$count[$n]){
				$count[0]=$count[$n];
			}
		}
	
		for($i=0;$i < $count[0];$i++){
			echo "<tr id=color>";
			for($j=0;$j<$cnt;$j++){
				echo "<td>".$lines[$j][$i]."</td>";
			}
			echo "</tr>";
		}
	
?>
	</table>
<br>
<br>
<br>
<?php 
$a++;
}
break;
case 'd_submit':
			$dougyousya=$_POST['TARGET_INPUT'];//同業者価格
			$proid = $_POST['proid'];
			$dou_id=$_POST['d_id'];//同業者ID
			$loop_cnt=count($dou_id);
			//同業者データを行ごとに分割する
			$rajio_a=0;
			$num=0;
			for($j=0;$j < $d_cnt;$j++){
				if($rajio_a != $loop_cnt){
					$d_datas[$num][$rajio_a]=$dougyousya[$j];
					$rajio_a++;
				}else{
					$rajio_a=0;
					$num++;
					$d_datas[$num][$rajio_a]=$dougyousya[$j];
					$rajio_a++;
				}
			}

			$res_cnt=tep_db_query("select count(*) as cnt_d from set_dougyousya_history where categories_id = '".tep_db_prepare_input($cID)."' AND products_id	= '".tep_db_prepare_input($proid[0])."'");
			$col_cnt=tep_db_fetch_array($res_cnt);
			$cnt_d=$d_cnt*20;//カテゴリー20件保存(5日分)
			for($i=0;$i < $cnt;$i++){//同業者の価格情報保存
				//echo $d_history[$i];
				$radio_query2 = tep_db_query("select history_id from set_dougyousya_history where categories_id = '".tep_db_prepare_input($cID)."' AND products_id='".$proid[$i]."' ORDER BY history_id ASC");
					while($col_radio2=tep_db_fetch_array($radio_query2)){
						$d_history[$i][]=$col_radio2['history_id'];
					}
				for($j=0;$j < $loop_cnt;$j++){

				$radio_query = tep_db_query("select count(*) as cnt from set_dougyousya_history where categories_id = '".tep_db_prepare_input($cID)."' AND history_id= '".$d_history[$i][$j]."'");
    			$col_radio[$j] = tep_db_fetch_array($radio_query);
			 		 if($col_radio[$j]['cnt'] > 0 && $cnt_d == $col_cnt['cnt_d'] ) {
	  				//20件以上なのでアップデート
					tep_db_query("update set_dougyousya_history set dougyosya_kakaku = '".$d_datas[$i][$j]."' ,dougyousya_id='".$dou_id[$j]."',radio_chk='".$radio_chk_data[$i][$j]."', last_date=now() where  history_id = '".$d_history[$i][$j]."'");
      				}else{
	  	 			//20件未満なのでインサート
					tep_db_query("insert into set_dougyousya_history(categories_id, products_id,dougyosya_kakaku, radio_chk,last_date,dougyousya_id) values ('".tep_db_prepare_input($cID)."', '".tep_db_prepare_input($proid[$i])."','".$d_datas[$i][$j]."','".$radio_chk_data[$i][$j]."',now(),'".$dou_id[$j]."')");
	  				}
				}
			}
header("Location:history.php?action=dougyousya&cPath=".$cPath."&cid=".$cID." ");
break;
case 'dougyousya':
$a=0;
$dou_cnt=0;



	$res=tep_db_query("select * from set_dougyousya_names where parent_id='".$cPath."' ORDER BY dougyousya_id ASC");
	$cnt=0;
	while($col=tep_db_fetch_array($res)){
		$d_name[]=$col['dougyousya_name'];
		$dougyousya_id[]=$col['dougyousya_id'];
		$cnt++;
	}
	
	
	$res=tep_db_query("select count(*) as cnt from set_dougyousya_history where categories_id='".$cID."' ORDER BY history_id DESC ");
	$col=tep_db_fetch_array($res);
	$pro_name_cnt=$col['cnt'];
	$res=tep_db_query("select * from products_to_categories where categories_id='".$cID."'");
		$cnt2=0;
		while($col=tep_db_fetch_array($res)){
			$cid_list[]=$col['products_id'];
			
			$cnt2++;
		}
	$pro_name_cnt=$cnt*$cnt2;
?>
<table border="1">
<form method="POST" action="history.php?acton=d_submit&cpath=<?php echo $cPath; ?>&cid=<?php echo $cID; ?>">
<tr>
<td>カテゴリー / 商品</td>
<?php 
		for($i=0;$i<$cnt;$i++){
			$html .= "<td>".$d_name[$i]."</td>";
		}
		echo $html;

?>
</tr>
<?php 
	$res=tep_db_query("select count(*) as cnt from set_dougyousya_names where parent_id='".$cPath."'");
	$count=tep_db_fetch_array($res);
	$target_cnt=1;//同業者専用
	$products_count=0;
	//登録フォーム作成
	for($i=0;$i<$cnt2;$i++){
		$res=tep_db_query("select * from products_description where products_id='".$cid_list[$i]."'");
		$col=tep_db_fetch_array($res);	
		echo "<input type='hidden' name='proid[]' value='".$cid_list[$i]."' >";//products_id
		echo "<input type='hidden' name='d_id[]' value='".$dougyousya_id[$i]."'>";//同業者ID
		echo "<tr><td>".$col['products_name']."</td>";
		if($count['cnt'] > 0){
		
			for($j=0;$j<$count['cnt'];$j++){
				echo "<td class='dataTableContent' >
				<input type='text' size='7px' name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",".$j.",".$count['cnt'].")></td>";//価格同業者
			}
			}else{
			echo "<td class='dataTableContent' ><input type='text' size='7px'  name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",'0','0')></td>";//価格同業者	
			}
	echo "</tr>";
	}
?>
<td><input type="submit" name="b1" value="登録"></td>
</form>
</table>
<br>
<br>
<?php 


	
	for($k=0;$k<20;$k++){//過去20件
		?>
		<table border="1">
	<tr>
	<td>カテゴリー / 商品</td>
	<?php 
		$html="";
		$html2="";
		$html3="";
		for($i=0;$i<$cnt;$i++){
			$html .= "<td>".$d_name[$i]."</td>";
		}
		echo $html;
	?>
	<td>更新時刻</td>
	</tr>
	<?php 
					

			$res=tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' ORDER BY last_date desc limit ".$a.",".$pro_name_cnt."");
			$cnt4=0;
			
			while($col_datas=tep_db_fetch_array($res)){
				$d_proid[$k][]=$col_datas['products_id'];
				//$d_datas[$k][]=$col_datas['dougyosya_kakaku'];
				$d_date[$k][]=$col_datas['last_date'];
				$d_id[$k][]=$col_datas['dougyousya_id'];
				$cnt4++;
			}
			 $d_data_con=array();//同業者価格配列初期化
			for($i=0;$i<$cnt;$i++){
			 	$num_dou=0;
				
				/*$res=tep_db_query("select count(*) as dou_cnt from set_dougyousya_history where categories_id='".$cID."' AND dougyousya_id='".$dougyousya_id[$i]."' ORDER BY  history_id   limit ".$dou_cnt.",".$cnt2."");
				$col=tep_db_fetch_array($res);
				if($col['dou_cnt'] > 0){*/
					$res=tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' AND dougyousya_id='".$dougyousya_id[$i]."' ORDER BY  history_id,last_date desc   limit ".$dou_cnt.",".$cnt2."");
					while($col=tep_db_fetch_array($res)){
						$d_data_con[$i][$num_dou]=$col['dougyosya_kakaku'];
						$num_dou++;	
					}
				/*}else{
					$d_data_con=array();
				}*/

			}
			$dou_cnt += $cnt2; 
			/*$res=tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' && products_id= '".$cid_list[$num2]."' ORDER BY last_date DESC , history_id  limit ".$a.",".$pro_name_cnt."");*/
			$hako="";
			for($num=0;$num < $pro_name_cnt;$num++){
				if($hako != $d_proid[$k][$num]){
					for($num2=0;$num2 < $cnt2;$num2++){
						if($d_proid[$k][$num]==$cid_list[$num2]){
							$p_id[]=$cid_list[$num2];
							$hako=$cid_list[$num2];
						}else{
							continue;
						}
					}
				}			
				
			}
		
		for($j=0;$j<$cnt2;$j++){
			$res=tep_db_query("select * from products_description where products_id='".$p_id[$j]."'");
			$col=tep_db_fetch_array($res);		
			echo "<tr>"	;
			echo  "<td>".$col['products_name']."</td>";

	
			$html2="";
			for($i=0;$i<$cnt;$i++){
				$html2 .= "<td>".$d_data_con[$i][$j]."</td>";
			}
			echo $html2;
			echo "<td align='center'>".$d_date[$k][$j]."</td></tr>";	
		}
	?>
	</table>
<br>
<br>
<br>
<?php  
$a=$a+$pro_name_cnt;
	}
	break;
}
?>

</body>
</html>
