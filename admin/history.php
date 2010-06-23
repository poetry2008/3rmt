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
	$res=tep_db_query("select * from set_oroshi_names son,set_oroshi_categories soc  where son.oroshi_id = soc.oroshi_id and soc.categories_id = '".$cPath."' ORDER BY oroshi_id ASC");
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
    $cPath = $_GET['cPath'];
    $dougyousya=$_POST['TARGET_INPUT'];//同業者価格
    $proid = $_POST['proid'];
    $dou_id=$_POST['d_id'];//同業者ID
    $d_cnt = count($dou_id);
    $loop_cnt=count($dou_id);
    
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

                  var_dump($sql);
                  tep_db_query($sql);
                  $sql = 'select history_id from  set_dougyousya_history where categories_id='.$cid.' and products_id = '.$proid[$j]. ' and dougyousya_id = '.$dou_id[$i].' order by last_date asc  limit 20,100';
                  var_dump($sql);
                  $res = tep_db_query($sql);
                  while($colx = tep_db_fetch_array($res))
                    {
                      tep_db_query('delete from set_dougyousya_history where history = '.$colx['history_id']);
                    }
                }
              }
          }
    
    //        header("Location:history.php?action=dougyousya_categories&cPath=".$cPath."&cid=".$cID." ");
        break;
 case 'dougyousya':
    //要先把游戏找出来再进行操作

    $did = $_GET['dougyousya_id'];
    $sql = 'select sdc.categories_id,cd.categories_name  from categories_description cd,set_dougyousya_categories sdc where cd.site_id = 0 and sdc.categories_id = cd.categories_id and  sdc.dougyousya_id =' .$did;
    $res = tep_db_query($sql);
    while($testcol  = tep_db_fetch_array($res))
      {
        $cate_id= $testcol['categories_id'];
        $cate_name= $testcol['categories_name'];
        $colmunLimit = 2;//分几行
        $colmunLimit_add_1 = $colmunLimit+1;
        echo "<table border=1>";
        echo "<th>";
        //        echo "<td colspan = ".$colmunLimit_add_1 .">";
        echo "<td>";
        echo $cate_name;
        echo "</td>";
        echo "</th>";
        echo "<tbody>";
          $getSubCategories = 'select cd.categories_name,cd.categories_id from categories_description cd, categories c where c.categories_id=cd.categories_id and cd.site_id = 0 and c.parent_id ='.$cate_id;
        $subRes = tep_db_query($getSubCategories);

        $rowCount = $colmunLimit;
        while($subCol = tep_db_fetch_array($subRes)){
          $sub_cate_id = $subCol['categories_id'];
          $sub_cate_name = $subCol['categories_name'];
            if($rowCount == $colmunLimit){

              echo "</tr>";
            }

          echo "<td><a href= 'history.php?action=dougyousya_categories&cid=".$sub_cate_id."&cPath=".$cate_id."' >".$sub_cate_name.'</a></td>';
          if($rowCount>0)
            {
            $rowCount--;
            }else {
            echo "</tr>";
            $rowCount =$colmunLimit;
          }
        }
        echo "</tbody>";
      }
    break;
 case 'deletePoint':
    $cPath = $_GET['cPath'];
    $cid = $_GET['cid'];
    tep_db_query('delete from set_dougyousya_history where history_id = '.$_GET['pointid']);
    tep_redirect("history.php?action=dougyousya_categories&cid=".$cid."&cPath=".$cPath);
    break;
 case 'dougyousya_categories':
    $cPath = cpathPart($_GET['cPath']);
    $cid = $_GET['cid'];
    $a=0;
    $dou_cnt=0;
	$res=tep_db_query("select sdn.*,sdc.categories_id from set_dougyousya_names sdn,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id ='".$cPath."' ORDER BY sdc.dougyousya_id ASC");
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
    <?php 
    echo   ' <form method="post" action="history.php?action=d_submit&cPath='.$cPath.'&cid='.$cID.'" >';
    ?>

        <table border="1">
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
	$res=tep_db_query("select count(*) as cnt from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id=".$cPath);
	$count=tep_db_fetch_array($res);
	$target_cnt=1;//同業者専用
	$products_count=0;
	//登録フォーム作成
	for($i=0;$i<$cnt2;$i++){
          $res=tep_db_query("select * from products_description where site_id=0 and  products_id='".$cid_list[$i]."'");
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
        $res=tep_db_query("select sdh.* ,sdn.dougyousya_name from set_dougyousya_history sdh ,set_dougyousya_names sdn where sdh.dougyousya_id = sdn.dougyousya_id and categories_id='".$cID."' order by last_date" );
    
        $products_arr = array();
        //        $dgs_array = array();
        while($col_datas=tep_db_fetch_array($res)){
              $products_arr[$col_datas['products_id']][] = $col_datas;
        }
        $color_arr = array('FF0000','000000','0000FF','FF00ff','ffff00');
        //每个产品一个图
    ksort($products_arr);
        foreach ($products_arr as $key=>$value)
          {

            $imgstr = '';
            $product_id = $key;
            $res_for_productname = tep_db_query('select products_name from products_description where site_id = 0 and  products_id = '.$key);
           
            $productname = tep_db_fetch_array($res_for_productname);
            $productname = $productname['products_name'];
            $dys_arr = array();
            $time_arr = array();

            foreach ($value as $record)
              {
                $dys_arr[$record['dougyousya_id']][] = $record;
                $tuli_arr[]  = $record['dougyousya_name'];
              }
            $imgstr = "<img style='float:left' width='300' height='200' alt='".$productname."' src = 'http://chart.apis.google.com/chart?cht=lxy&chs=300x200&";
            $imgstr.= "chd=t:";
            $style = array();
            $key2count = 0;
            $chco = array();                
            $tuli = array();
            
            $dys_arr_count = 0;//dys_arr的计数 为选择颜色所用
              foreach($dys_arr as $key2=>$value2)
              {
                $tuli[] = $tuli_arr[$dys_arr_count];
                $chco[] = $color_arr[$dys_arr_count];
                $dys_arr_count ++;
                //为了计算平均值
                $time_arr = array();
                $kakaku_arr = array();
                foreach ($value2 as $key4=>$value4){
                $time_arr[] = strtotime($value4['last_date']);
                $min = min($time_arr);
                $max = max($time_arr);
                $len = $max - $min;
                $len = $len?$len:1;
                $kakaku_arr[] =$value4['dougyosya_kakaku'];
                $minkaku = min($kakaku_arr);
                $maxkaku = max($kakaku_arr);
                $lenkaku = $maxkaku-$minkaku;
                $lenkaku = $lenkaku?$lenkaku:1;
                }
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
                if($dys_arr_count!=count($dys_arr))
                  {
                    $imgstr .='|';
                  }
                $key2count++;

              }

            $imgstr.='&chm='.join('|',$style);
            $imgstr.='&chco='.join(',',$chco);
            $imgstr.='&chdl='.join('|',$tuli);
            $imgstr.='&chtt='.$productname."|".date('m-d H:i',$min).'+---+'.date('m-d H:i',$max);
            $imgstr.='&chxt='.'x,y';
            $imgstr.='&chxr=1,'.$minkaku.','.$maxkaku;          
            $imgstr.='&chxl=0:|'.date('m-d H:i',$min).'|'.date('m-d H:i',$max);
            $imgstr.="' />";
            echo $imgstr;

            foreach ($dys_arr as $did=>$rowRecord)
              {
                echo "<table style='float:left;' border=1>";
                echo "<tr><td colspan=3>".$rowRecord[0]['dougyousya_name']."____".$productname."</td></tr>";
                  
                foreach($rowRecord as $key8=>$value8){

                echo "<tr>";
                echo "<td>"."<a href='history.php?action=deletePoint&cPath=".$cPath."&cid=".$cid."&pointid=".$value8['history_id']."'>" .$value8['dougyosya_kakaku']."</a></td>";
                echo "<td>".$value8['last_date']."</td>";
                echo "<td>".$value8['dougyousya_id']."</td>";
                echo "</tr>";

              }
                  echo '</table>';                  
              }

            echo '<hr style="clear:both">';
          }
        
        break;
 }
?>

</body>
</html>
