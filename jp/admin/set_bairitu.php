<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$cID=$_POST['cID_list'];
$cpath = $_POST['cpath'];
switch ($HTTP_GET_VARS['action']){
/* -----------------------------------------------------
   case 'set_bai'设置分类的计算设定    
   case 'set_time' 设置分类的限制时间     
------------------------------------------------------*/
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
    tep_redirect('categories.php?cPath='.$cpath);
    break;
  case 'set_time':
    $path_array = explode('_', $_POST['cepath']);
    $limit_ca_cid = $path_array[count($path_array)-1];
  
    $exists_ltime_query = tep_db_query("select * from ".TABLE_BESTSELLERS_TIME_TO_CATEGORY." where categories_id = '".(int)$limit_ca_cid."'");
    if (tep_db_num_rows($exists_ltime_query)) {
      tep_db_query("update ".TABLE_BESTSELLERS_TIME_TO_CATEGORY." set limit_time = '".(int)$_POST['btime']."' where categories_id = '".(int)$limit_ca_cid."'");  
    } else {
      tep_db_query("insert into `".TABLE_BESTSELLERS_TIME_TO_CATEGORY."` values('".(int)$limit_ca_cid."', '".(int)$_POST['btime']."')"); 
    }

    tep_redirect('categories.php?cPath='.$_POST['cepath']);
    break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<title><?php echo SET_BAIRITU_TITLE;?></title>
</head>
<?php 
  $res=tep_db_query("select * from set_auto_calc where parent_id='".$cID."'");
  $col=tep_db_fetch_array($res);
?>
<body>
<div class="show_left_menu">
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
    <script language='javascript'>
          one_time_pwd('<?php echo $page_name;?>');
    </script>
<?php }?>
<form method="post" action="set_bairitu.php?action=set_bai"  onsubmit="alert('<?php echo SET_BAIRITU_UPDATE_NOTICE;?>')">
<p><?php echo SET_BAIRITU_CURSET;?><input type="text" value="<?php echo isset($col['bairitu'])?$col['bairitu']:1.1?>" name="bai" ></p>
<p><?php echo SET_BAIRITU_SINGLE_PRICE;?></p>
<p><?php echo SET_BAIRITU_PERCENT;?><input type="text" value="<?php echo $col['percent']?>" name="percent" size="10">%</p>
<p><?php echo SET_BAIRITU_SPRICE;?></p>
<p><?php echo SET_BAIRITU_CAL;?><select  name="shisoku">
<?php 
  if($col['shisoku'] == "+"){
    echo "<option value='+' selected>+</option>";
    echo "<option value='-'>-</option>";
  }else{
    echo "<option value='+'>+</option>";
    echo "<option value='-' selected >-</option>";
  }
 ?>
 </select>
<input type="text" value="<?php echo $col['keisan']?>" name="kei" ></p>
<input type="hidden" value="<?php echo $cID ?>" name="cID_list">
<input type="hidden" value="<?php echo $cpath ?>" name="cpath">
<span><input type="submit" value="<?php echo SET_BAIRITU_CAL_SET;?>"></span>
</form>
<?php if (!empty($_POST['cpath'])) {?>
<?php
$path_array = explode('_', $cpath);
$limit_ca_cid = $path_array[count($path_array)-1];
$best_limit_query = tep_db_query("select * from ".TABLE_BESTSELLERS_TIME_TO_CATEGORY." where categories_id = '".(int)$limit_ca_cid."'");

$current_limit_time = 0;
$best_limit_res= tep_db_fetch_array($best_limit_query);
if ($best_limit_res) {
  $current_limit_time = $best_limit_res['limit_time'];
}
?>
<form method="post" action="set_bairitu.php?action=set_time">
<p><?php echo SET_BAIRITU_BESTSELLER;?></p>
<p><input type="text" value="<?php echo $current_limit_time;?>" name="btime"><?php echo SET_BAIRITU_BESTSELLER_READ;?></p>
<input type="hidden" value="<?php echo $cpath ?>" name="cepath">
<p><input type="submit" value="<?php echo IMAGE_CONFIRM;?>"></p>
</form>
<?php }?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
