<?php
ob_start();
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
  $o_id = $_POST['oid'];
  foreach ($setdata as $key=>$value){
    //    tep_db_query('delete from set_oroshi_datas where oroshi_id = '.$key.' and parent_id ='.$cid);
    if(trim($value)){
    $oroid = $key;
    $sql = 'insert into set_oroshi_datas (oroshi_id ,parent_id,datas,set_date) values(';
    $sql.= '"'.$key.'",';
    $sql.= '"'.$cid.'",';
    $sql.= '"'.$value.'",';
    $sql.= 'now()';
    $sql.= ')';
    tep_db_query($sql);
    }
  }
  var_dump($_GET['src_id']);
  if(isset($_GET['src_id'])&&$_GET['src_id']!=null){
    $jump_url = 'cleate_list.php?action=prelist&cid='. $cid  .'&oid='.$o_id.'&src_id=his';
  }else{
    $jump_url = 'cleate_list.php?action=prelist&cid=' . $cid .'&oid='.$o_id;
  }
  tep_redirect($jump_url);
    break;

}
/*
  危険　24時間　価格更新なし
  警告　4時間未満　7時間未満　24時間　価格更新なし
*/    //DBに保存するのは最大20
    
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
<script language="javascript" >
function goto(){
  var link = document.getElementById('back_link').href;
  location.href=link;
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<div id="spiffycalendar" class="text"></div>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
   <tr>
      <td width="<?php echo BOX_WIDTH; ?>" valign="top">
         <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <tr>
               <td><?php require(DIR_WS_INCLUDES . 'column_left.php'); ?></td>
            </tr>   
         </table>
      </td>
      <td width="100%" valign="top">
         <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
               <td class = "pageHeading">卸業者のデータ登録
               <input type="button" onClick = "goto()" value='戻る'>
               </td>
            </tr>
            <tr>
               <td>
                  <table width="100%" class="list" border="0" cellspacing="0" cellpadding="4" bgcolor="#F0F1F1">
                     <tr bordercolor="#F0F1F1">
                        <td></td>
                     </tr>   
            <?php 
  //根据 oid 取出当前 oid 关系了哪些个分类
  if ($action == 'oroshi'){
    $back_url = 'cleate_oroshi.php';
  $getMyCate = 'select cd.categories_name,soc.categories_id  from
    set_oroshi_categories soc ,categories_description cd where cd.site_id =0 and
    soc.categories_id = cd.categories_id and soc.oroshi_id = "'.$oid.'"';
$res = tep_db_query($getMyCate);
while ($col = tep_db_fetch_array($res)){
  $cate_id = $col['categories_id'];
  $cate_name = $col['categories_name'];
  
  $colmunLimit = 2;//分几行
  $colmunLimit_add_1 = $colmunLimit+1;

  echo "<tr bgcolor='#F0F1F1' onmouseover='this.style.backgroundColor=\"#FFD700\"' onmouseout='this.style.backgroundColor=\"#F0F1F1\"'>";
  echo "<td><a href=
    'cleate_list.php?action=prelist&cid=".$cate_id."&oid=".$_GET['o_id']."' >".$cate_name.'</a></td>';
  echo "</tr>";
//  echo "</table>";
}

  }
if ($action =='prelist'){
  $cid = $_GET['cid'];
  $oid = $_GET['oid'];
  $back_url = 'cleate_list.php';
  $back_url_params = 'action=oroshi&o_id='.$oid;
  $form_action = 'cleate_list.php?action=data_cleate';
  if (isset($_GET['src_id'])&&$_GET['src_id']!=null){
    $src_id=$_GET['src_id'];
    $back_url_params = 'action=oroshi&cid='.$cid.'&o_id='.$oid.'&src_id='.$src_id;
    $back_url = 'history.php';
    $form_action = 'cleate_list.php?action=data_cleate&src_id='.$src_id;
  }
  //  $res = tep_db_query("select * from set_oroshi_categories soc,set_oroshi_names son where son.oroshi_id = soc.oroshi_id and soc.categories
  $res =tep_db_query('select * from set_oroshi_names son, categories c
      ,set_oroshi_categories soc where c.categories_id = "'.$cid.'" and c.categories_id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by soc.oroshi_id ');
  //var_dump('select * from set_oroshi_names son, categories c ,set_oroshi_categories soc where c.categories_id = '.$cid.' and c.categories__id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by soc.oroshi_id desc');
      $html2 = '';
      $c=0;
    while($col = tep_db_fetch_array($res)){
      $c++;
      $oroname = $col['oroshi_name'];
      $oroid = $col['oroshi_id'];
      //$cnt=0;

      //while($col=tep_db_fetch_array($res)){
      $html.= "<td><a href='history.php?action=oroshi_c&cPath=".$_GET['cid']."&oid=".$col['oroshi_id']."' title='履歴を見る'>".$col['oroshi_name']."</a>&nbsp;&nbsp;&nbsp;<a href='history.php?action=oroshi_c&cPath=".$_GET['cid']."&oid=".$col['oroshi_id']."' title='履歴を見る'>履歴を見る</a></td>";
      $html2.= '';
      $html2.="<td><textarea rows='5' cols='30' id='textarea_".$col['oroshi_id']."' name='set_list[".$oroid."]' ></textarea></td>";

}
echo $html;
?>
          <form method="post" action="<?php echo $form_action;?>">
                     <tr bgcolor='#F0F1F1'>
            <?php
  echo $html2;
?>
                        <td>
                        <input type="hidden" value="<?php echo $cid;?>" name='cid' />
                        <input type="hidden" value="<?php echo $oid;?>" name='oid' />
                        </td>
                        <td></td>
                     </tr>
        <tr>
          <td colspan="<?php echo count($c)+2;?>"><input type="submit" value="リスト登録"></td>
        </tr>
          </form>
          <?php
    $lines_arr = array();
$oroname = array();
$cr = array("\r\n", "\r");   // 改行コード置換用配
$orocnt = tep_db_query('select distinct(oroshi_id) from set_oroshi_datas where parent_id = "'.$cid.'" order by oroshi_id');
while($testcol = tep_db_fetch_array($orocnt)){
  $oroids[] = $testcol['oroshi_id'];
}
if($oroids){
foreach($oroids as $key=>$value){
  $res = tep_db_query("select * from set_oroshi_names son, set_oroshi_datas sod where sod.oroshi_id ='". $value."' and sod.oroshi_id = son.oroshi_id and  parent_id='".$cid."' ORDER BY sod.list_id desc limit 1");
  $col = tep_db_fetch_array($res);
  $cols[]=$col;
}

foreach($cols as $col){
    $oroname[] = $col['oroshi_name'];
    $orotime[] = $col['set_date'];
    /**
    $col['datas'] = trim($col['datas']);         // 文頭文末の空白を削除
    $col['datas'] = str_replace($cr, "\n",$col['datas']);  // 改行コードを統一
    $lines= explode("\n", $col['datas']);
    $count[]=count($lines);
    $lines_arr[]=$lines;
    /**/
    $lines = spliteOroData($col['datas']);
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
  foreach ($orotime as $value){
    echo "<td>$value</td>";
  }
echo "</tr>";
echo "<tr>";  
  foreach ($oroname as $value){
    echo "<th align='left'>$value</th>";
  }

echo "</tr>";

}
for($i=0;$i < $count[0];$i++){
  echo "<tr id=color>";
  for($j=0;$j<$cnt;$j++){
    echo "<td>".$lines_arr[$j][$i]."</td>";
  }
  echo "</tr>";
}
} 
?>
        </table>
               </td>
            </tr>
            <tr>
               <td><a id="back_link" style="display:none" href="<?php echo tep_href_link($back_url, $back_url_params); ?>">go back</a></td>
            </tr>
         </table>
      </td>
  </tr>
  
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
