<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

/**
echo "<pre>";
print_r($_GET);
print_r($_POST);
echo "</pre>";
/**/
$cID=$_POST['cID_list'];
$cpath = $_POST['cpath'];
//$cID=$_POST['cpath_yobi'];
switch ($HTTP_GET_VARS['action']){
      case 'set_bai':
    $bai     = $_POST['bai'];
    $keisan  = $_POST['kei'];
    $shisoku = $_POST['shisoku'];
    $percent = $_POST['percent'];
    $res=tep_db_query("select count(*) as cnt from set_auto_calc where parent_id='".$cID."'");
    $count=tep_db_fetch_array($res);
    if($count['cnt'] > 0){
      tep_db_query("update  set_auto_calc set bairitu='".$bai."',keisan='".$keisan."',shisoku='".$shisoku."',percent='".$percent."' where  parent_id='".$cID."'");
    }else{
      tep_db_query("insert into set_auto_calc (parent_id,bairitu,keisan,shisoku,percent) values ('".$cID."','".$bai."','".$keisan."','".$shisoku."','".$percent."')");
    }
    tep_redirect('categories_admin.php?cPath='.$cpath);
    break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<title>無題ドキュメント</title>
</head>
<?php 
  $res=tep_db_query("select * from set_auto_calc where parent_id='".$cID."'");
  $col=tep_db_fetch_array($res);
?>
<body>
<form method="post" action="set_bairitu.php?action=set_bai"  onsubmit="alert('更新されました。')">
<p>倍率設定：<input type="text" value="<?php echo isset($col['bairitu'])?$col['bairitu']:1.1?>" name="bai" ></p>
<p><b>単価の差額</b></p>
<p>パーセント：<input type="text" value="<?php echo $col['percent']?>" name="percent" size="10">%</p>
<p><b>特別価格設定の計算</b></p>
<p>計算：<select  name="shisoku">
<?php 
  if($col['shisoku'] == "+"){
    echo "<option value='+' selected>＋</option>";
    echo "<option value='-'>−</option>";
  }else{
    echo "<option value='+'>＋</option>";
    echo "<option value='-' selected >−</option>";
  }
 ?>
 </select>
<input type="text" value="<?php echo $col['keisan']?>" name="kei" ></p>
<input type="hidden" value="<?php echo $cID ?>" name="cID_list">
<input type="hidden" value="<?php echo $cpath ?>" name="cpath">
<input type="submit" value="計算設定">
</form>
</body>
</html>
