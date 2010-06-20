<?php
require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
//  $cPath=cpathPart($_POST['cpath']);
  $cPath=cpathPart($_GET['cpath']);
$oid = $_GET['oid'];
switch ($HTTP_GET_VARS['action']){
case 'list_cleate':
  $cPath=cpathPart($_POST['cpath']);
  $oroshi_name=$_POST['oroshi_name'];
  $oroshi_id = $_POST['oroshi_id'];
  $setdata=$_POST['set_list'];
  $date=date("Y-m-d H:i:s");
		
  $list_cnt=count($oroshi_name);
		
  /*for($i=0;$i < $list_cnt;$i++){
    $res=tep_db_query("select parent_id,oroshi_name from set_oroshi_names where parent_id='".$cPath."' && oroshi_name='".$oroshi_name[$i]."' ORDER BY oroshi_id ASC");
    echo mysql_num_rows($res);
			
    DB各卸業者のデータが何件あるか調べる20件なら一番古いデータを削除後追加
    }*/
  for($i=0;$i < $list_cnt;$i++){
    $res_cnt=tep_db_query("select count(*) as data_cnt from set_oroshi_datas where parent_id='".$cPath."' AND oroshi_id='".$oroshi_id[$i]."' ORDER BY list_id ASC " );
    $col_cnt[]=tep_db_fetch_array($res_cnt);
			
    $res_id=tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' AND oroshi_id='".$oroshi_id[$i]."' ORDER BY list_id ASC " );
    $col_id[]=tep_db_fetch_array($res_id);
  }
  for($i=0;$i < $list_cnt;$i++){
    if($setdata[$i] != "" && $col_cnt[$i]['data_cnt'] != 20){
      tep_db_query("insert into set_oroshi_datas (oroshi_id,oroshi_name,parent_id,datas,set_date) values ('".$oroshi_id[$i]."','".$oroshi_name[$i]."','".$cPath."','".$setdata[$i]."','".$date."')");
    }else if($setdata[$i] != ""){
      tep_db_query("update set_oroshi_datas set datas='".$setdata[$i]."',set_date='".$date."' where list_id='".$col_id[$i]['list_id']."' ");
    }
  }
  break;
}
/*
  危険　24時間　価格更新なし
  警告　4時間未満　7時間未満　24時間　価格更新なし
*/		//DBに保存するのは最大20
		
//DBの記録に必要なもの
/*
  卸業者名、$cPath、時間、データ
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
  <title>リスト作成</title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<?php 
  if($oid){
?>
<script language="javascript" >
  $(document).ready(function(){
      $("#textarea_<?php echo $oid;?>").focus()
        })
</script>
    <?php } ?>

  </head>

  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr><td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </table></td>
  <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
  <table><tr><td class = "pageHeading">卸業者のデータ登録</td></tr><tr><td>
  <table border="0"class="list">
  <tr>
  <?php 
$res=tep_db_query("select * from set_oroshi_names where parent_id='".$cPath."' ORDER BY oroshi_id ASC");
$cnt=0;
$html2 = '';
while($col=tep_db_fetch_array($res)){
  $html.= "<th align='center'>".$col['oroshi_name']."</th>";
  $o_name[]=$col['oroshi_name'];
  $o_id[]=$col['oroshi_id'];
  $html2.= '';
  $html2.="<td><textarea rows='5' cols='30' id='textarea_".$col['oroshi_id']."' name='set_list[]' ></textarea></td>";
  $html2.="<input type='hidden' value='".$o_name[$cnt]."' name='oroshi_name[]'>";					
  $html2.="<input type='hidden' value='".$o_id[$cnt]."' name='oroshi_id[]'>";					
  $cnt++;


}
echo $html;
?>
</tr>
<form method="post" action="cleate_list.php?action=list_cleate" >
  <input type="hidden" value="<?php echo $cPath ?>" name="cpath">
  <tr>
  <?php
  echo $html2;
				
?>
</tr>
<td><input type="submit" value="リスト登録"></td>
  </form>
  </table>

  <table border="1">

  <tr>
  <?php
  /*$today[]=date("Y-m-d H:i:s",strtotime("-1 day"));
    $today[]=date("Y-m-d H:i:s",strtotime("-24 hours"));
    $today[]=date("Y-m-d H:i:s",strtotime("-7 hours"));
    $today[]=date("Y-m-d H:i:s",strtotime("-4 hours"));
    for($i=0;$i<$cnt;$i++){
    $res=tep_db_query("select set_date from set_oroshi_datas where parent_id='".$cPath."' && oroshi_name='".$o_name[$i]."' ORDER BY list_id DESC");
    $col=tep_db_fetch_array($res);


    if(strtotime($col['set_date']) < strtotime($today[0])){
    echo "24時間以上更新がありません";
    }else if(strtotime($col['set_date']) <= strtotime($today[1])){
    echo "24時間未満更新がありません";
    }else if(strtotime($col['set_date']) <= strtotime($today[2])){
    echo "7時間未満更新がありません";
    }else if(strtotime($col['set_date']) <= strtotime($today[3])){
    echo "4時間未満更新がありません";
    }else{
    echo "異常なし";
    }
	
    echo "<td align='center'>".$col['set_date']."</td>";
    }
    テストのために作成*/
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
  $res = tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' && oroshi_name='".$o_name[$i]."' ORDER BY list_id DESC");
  $col[] = tep_db_fetch_array($res);


	
  $col[$i]['datas'] = trim($col[$i]['datas']);         // 文頭文末の空白を削除
  $col[$i]['datas'] = str_replace($cr, "\n",$col[$i]['datas']);  // 改行コードを統一
  $lines[] = explode("\n", $col[$i]['datas']);
  $count[]=count($lines[$i]);
}	
for($n=0;$n<$cnt;$n++){
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
</table></td></tr></table></td>
</tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
