<?php 
require('includes/application_top.php');

$cID   = (int)$_GET['cID'];
$cPath = $_GET['cPath'];

switch ($HTTP_GET_VARS['action']){
      case 'save':
    $res=tep_db_query("select count(*) as cnt from set_comments where categories_id='".$cID."'");
    $count=tep_db_fetch_array($res);
    if($count['cnt'] > 0){
      tep_db_query("update  set_comments set author='".mysql_real_escape_string($_POST['author'])."',rule='".mysql_real_escape_string($_POST['rule'])."',comment='".mysql_real_escape_string($_POST['comment'])."',last_modified=now() where categories_id='".$cID."'");
    }else{
      tep_db_query("insert into set_comments (categories_id,author,rule,comment,date_added,last_modified) values ('".$cID."','".mysql_real_escape_string($_POST['author'])."','".mysql_real_escape_string($_POST['rule'])."','".mysql_real_escape_string($_POST['comment'])."',now(),now())");
    }
    tep_redirect('categories_admin.php?cPath='.$cPath);
    break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo CHARSET; ?>">
<title>担当者登録</title>
</head>
<?php 
  $res=tep_db_query("select * from set_comments where categories_id='".$cID."'");
  $col=tep_db_fetch_array($res);
?>
<body>
<form method="post" action="set_comment.php?action=save&cID=<?php echo $cID;?>&cPath=<?php echo $cPath;?>"  onsubmit="alert('更新されました。')">
<p>担当者:</p>
<p><input type='text' name='author' value="<?php echo $col['author'];?>" /></p>
<p>単価ルール:</p>
<p><textarea cols='60' rows='12' name='rule'><?php echo $col['rule'];?></textarea></p>
<p>コメント:</p>
<p><textarea cols='60' rows='12' name='comment'><?php echo $col['comment'];?></textarea></p>
<p><input type="submit" value="担当者登録"><p>
</form>
</body>
</html>
