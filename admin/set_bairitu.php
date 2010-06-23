<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

//$cID=$_POST['cID_list'];
$cID=$_POST['cpath_yobi'];
switch ($HTTP_GET_VARS['action']){
  		case 'set_bai':
		$bai=$_POST['bai'];
		$keisan=$_POST['kei'];
		$shisoku=$_POST['shisoku'];
		$res=tep_db_query("select count(*) as cnt from set_auto_calc where parent_id='".$cID."'");
		$count=tep_db_fetch_array($res);
			if($count['cnt'] > 0){
				tep_db_query("update  set_auto_calc set bairitu='".$bai."',keisan='".$keisan."',shisoku='".$shisoku."' where  parent_id='".$cID."'");
			}else{
				tep_db_query("insert into set_auto_calc (parent_id,bairitu,keisan,shisoku) values ('".$cID."','".$bai."','".$keisan."','".$shisoku."')");
			}
		break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<p>倍率設定：<input type="txte" value="<?php echo $col['bairitu']?>" name="bai" ></p>
<p>特別価格設定の計算</p>
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
<input type="txte" value="<?php echo $col['keisan']?>" name="kei" ></p>
<input type="hidden" value="<?php echo $cID ?>" name="cpath_yobi">
<input type="submit" value="計算設定">
</form>
</body>
</html>
