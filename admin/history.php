<?php 
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
$cPath=$_GET['cPath'];
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
		$o_id[]=$col['oroshi_id'];
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
                  $res=tep_db_query("select set_date from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC  limit ".$a.",1 ");
                  $col=tep_db_fetch_array($res);
                  echo "<td align='center'>".$col['set_date']."</td>";
		}

?>

		</tr>

		<tr>
                    <?php
                    for($i=0;$i<$cnt;$i++){
                      $res=tep_db_query("select oroshi_name from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC");
                      $col=tep_db_fetch_array($res);
                      echo "<th align='center'>".$col['oroshi_name']."</th>";
                    }
          ?>
          </tr>
              
              <?php
              $cr = array("\r\n", "\r");   // 改行コード置換用配
          for($i=0;$i<=$cnt;$i++){
            $res = tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC limit ".$a.",1 ");
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
        $d_cnt = count($dou_id);
        $loop_cnt=count($dou_id);
        
        echo 'dougyousya';
        var_dump($dougyousya);
        echo 'proid';
        var_dump($proid);
        echo 'dou_id';
        var_dump($dou_id);
        $count_tontye = 0;
        foreach($dou_id as $value)
          {
            if ($value!='')
              $count_tontye++;
          }
        $count_product = count($proid);//一共几行
        $limit = 20;        
        for ($i = 0;$i<$count_tontye;$i++)
          {
            for ($j=0;$j<$count_product;$j++)
              {
                $kankan =  $dougyousya[$j*$count_tontye+$i];
                if ($kankan){
                  $sql = 'insert into set_dougyousya_history ( `categories_id`,`products_id`,`dougyosya_kakaku`,`dougyousya_id`,`last_date`)';
                  $sql.= 'values ('.$cID.','.$proid[$j].','.$kankan.','.$dou_id[$i].',now())';
                  tep_db_query($sql);
                  $sql = 'select history_id from  set_dougyousya_history where categories_id='.$cid.' and products_id = '.$proid[$j]. ' and dougyousya_id = '.$dou_id[$i].' order by last_date asc  limit 20,100';
                  $res = tep_db_query($sql);
                  while($colx = tep_db_fetch_array($res))
                    {
                      tep_db_query('delete from set_dougyousya_history where history = '.$colx['history_id']);
                    }
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
        <form method="POST" action="history.php?action=d_submit&cpath=<?php echo $cPath; ?>&cid=<?php echo $cID; ?>">
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
              //				<input type='text' size='7px' name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",".$j.",".$count['cnt'].")></td>";//価格同業者
              echo "<td class='dataTableContent' >
				<input type='text' size='7px' name='TARGET_INPUT[]' >";//価格同業者
            }
          }else{
            //            echo "<td class='dataTableContent' ><input type='text' size='7px'  name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",'0','0')></td>";//価格同業者	
            echo "<td class='dataTableContent' ><input type='text' size='7px'  name='TARGET_INPUT[]' ></td>";//価格同業者	
          }
          echo "</tr>";
	}
        ?>
        <td><input type="submit" name="b1" value="登録"></td>
        </form>
        </table>


        <!-- data start -->
        <br>
        <br>
          <?php 
        $res=tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' order by last_date" );
        $products_arr = array();
        //        $dgs_array = array();
        while($col_datas=tep_db_fetch_array($res)){
          //          if (in_array($col_datas['products_id'],array_keys($dgs_array)))
          //{
              $products_arr[$col_datas['products_id']][] = $col_datas;
              //}else 
              //            {
              
              //            }
              
        }
        $color_arr = array('FF0000','000000','0000FF','FF00ff','ffff00');
        //每个产品一个图
        foreach ($products_arr as $key=>$value)
          {
            $imgstr = '';
            $product_id = $key;
            $product_name = $key.'name';
            $dys_arr = array();
            $time_arr = array();
            $chco = array();                
            foreach ($value as $record)
              {
                $dys_arr[$record['dougyousya_id']][] = $record;
                $time_arr[] = strtotime($record['last_date']);
                $min = min($time_arr);
                $max = max($time_arr);
                $len = $max - $min;

                $kakaku_arr[] =$record['dougyosya_kakaku'];
                $minkaku = min($kakaku_arr);
                $maxkaku = max($kakaku_arr);
                $lenkaku = $maxkaku-$minkaku;
              }
            $imgstr = "<img width='300' height='200' alt='<?php echo $product_name; ?>' src = 'http://chart.apis.google.com/chart?cht=lxy&chs=300x200&";
            $imgstr.= "chd=t:";
            $style = array();
            $key2count = 0;
            foreach($dys_arr as $key2=>$value2)
              {
                $x = '';
                $y = '';
                foreach ($value2 as $key3=> $value3){
                  $x.= round((strtotime($value3['last_date'])-$min)/$len*100);
                  if (isset($value2[$key3+1])){
                    $x.=',';
                  }
                  $y.= round(($value3['dougyosya_kakaku']-$minkaku)/$lenkaku*100);
                  if (isset($value2[$key3+1])){
                    $y.=',';
                  }
                  $style[]='s,FF0000,'.$key2count.','.$key3.',5';

                }
                $imgstr.=$x.'|'.$y;
                if (!isset($dys_arr[$key2+1])){
                $imgstr.='|';
                }

            $key2count++;

              }

            $imgstr.='&chm='.join('|',$style);
            $imgstr.='&chco='.join(',',$chco);
            $imgstr.="' />";
            echo $imgstr;
            echo '<hr>';
          }
        
        break;
 }
?>

</body>
</html>
