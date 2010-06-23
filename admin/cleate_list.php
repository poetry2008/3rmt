<?php
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
//  $cPath=cpathPart($_POST['cpath']);
$cPath=cpathPart($_GET['cpath']);
$oid = $_GET['o_id'];
$action = $HTTP_GET_VARS['action'];

switch ($HTTP_GET_VARS['action']){
case 'data_cleate':
  $cPath=cpathPart($_POST['cpath']);
  $setdata=$_POST['set_list'];
  $date=date("Y-m-d H:i:s");
  $cid = $_POST['cid'];
  foreach ($setdata as $key=>$value){
    tep_db_query('delete from set_oroshi_datas where oroshi_id = '.$key.' and parent_id ='.$cid);
    $oroid = $key;
    $sql = 'insert into set_oroshi_datas (oroshi_id ,parent_id,datas,set_date) values(';
    $sql.= $key.',';
    $sql.= $cid.',';
    $sql.= '"'.$value.'",';
    $sql.= 'now()';
    $sql.= ')';
    tep_db_query($sql);
  }
    tep_redirect('cleate_list.php?action=prelist&cid=' . $cid );
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
  //根据 oid 取出当前 oid 关系了哪些个分类
  if ($action == 'oroshi'){
  $getMyCate = 'select cd.categories_name,soc.categories_id  from set_oroshi_categories soc ,categories_description cd where cd.site_id =0 and soc.categories_id = cd.categories_id and soc.oroshi_id = '.$oid;
$res = tep_db_query($getMyCate);
while ($col = tep_db_fetch_array($res)){
  $cate_id = $col['categories_id'];
  $cate_name = $col['categories_name'];
  
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

    //    echo "<td><a href= 'cleate_list.php?action=prelist&cid=".$sub_cate_id."&cPath=".$cate_id."' >".$sub_cate_name.'</a></td>';
    echo "<td><a href= 'cleate_list.php?action=prelist&cid=".$sub_cate_id."' >".$sub_cate_name.'</a></td>';
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

  }
if ($action =='prelist'){
  $cid = $_GET['cid'];
  //  $res = tep_db_query("select * from set_oroshi_categories soc,set_oroshi_names son where son.oroshi_id = soc.oroshi_id and soc.categories
  $res =tep_db_query('select * from set_oroshi_names son, categories c ,set_oroshi_categories soc where c.categories_id = '.$cid.' and c.parent_id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by soc.oroshi_id desc');
      $html2 = '';
    while($col = tep_db_fetch_array($res)){
      $oroname = $col['oroshi_name'];
      $oroid = $col['oroshi_id'];
      //$cnt=0;

      //while($col=tep_db_fetch_array($res)){
      $html.= "<th align='center'>".$col['oroshi_name']."</th>";
      $html2.= '';
      $html2.="<td><textarea rows='5' cols='30' id='textarea_".$col['oroshi_id']."' name='set_list[".$oroid."]' ></textarea></td>";

}
echo $html;
?>
</tr>
<form method="post" action="cleate_list.php?action=data_cleate" >
  <tr>
  <?php

  echo $html2;
				
?>
</tr>
<td><input type="hidden" value="<?php echo $cid;?>" name='cid' /></td>
<td><input type="submit" value="リスト登録"></td>

  </form>
  </table>

  <table border="1">

<?php
    $lines_arr = array();
$oroname = array();
$cr = array("\r\n", "\r");   // 改行コード置換用配
  $res = tep_db_query("select * from set_oroshi_names son, set_oroshi_datas sod where sod.oroshi_id = son.oroshi_id and  parent_id='".$cid."' ORDER BY list_id ");
  //  var_dump("select * from set_oroshi_datas where parent_id='".$cid."' ORDER BY list_id DESC");
  while($col = tep_db_fetch_array($res)){
    $oroname[] = $col['oroshi_name'];
    $col['datas'] = trim($col['datas']);         // 文頭文末の空白を削除
    $col['datas'] = str_replace($cr, "\n",$col['datas']);  // 改行コードを統一
    $lines= explode("\n", $col['datas']);
    $count[]=count($lines);
    $lines_arr[]=$lines;
}	
                                
  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
echo "<tr>";	
foreach ($oroname as $value){
  echo "<td>$value</td>";
}
echo "</tr>";
for($i=0;$i < $count[0];$i++){
  echo "<tr id=color>";
  for($j=0;$j<$cnt;$j++){
    echo "<td>".$lines_arr[$j][$i]."</td>";
  }
  echo "</tr>";
}
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
