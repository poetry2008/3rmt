<?php 
/*
   $Id$
*/
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo CHARSET; ?>">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<title><?php echo SET_COMMENT_TITLE?></title>
</head>
<?php 
  $res=tep_db_query("select * from set_comments where categories_id='".$cID."'");
  $col=tep_db_fetch_array($res);
?>
<body>
<body>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
    <script language='javascript'>
          one_time_pwd('<?php echo $page_name;?>');
    </script>
<?php }?>
<form method="post" action="set_comment.php?action=save&cID=<?php echo
$cID;?>&cPath=<?php echo $cPath;?>"  onsubmit="alert('<?php echo SET_BAIRITU_UPDATE_NOTICE;?>')">
<p><?php echo SET_COMMENT_USER?></p>
<p><input type='text' name='author' value="<?php echo $col['author'];?>" /></p>
<p><?php echo SET_COMMENT_SINGLE?></p>
<p><textarea cols='60' rows='12' name='rule'><?php echo $col['rule'];?></textarea></p>
<p><?php echo SET_COMMENT_COMMENT_TEXT;?></p>
<p><textarea cols='60' rows='12' name='comment'><?php echo $col['comment'];?></textarea></p>
<p><input type="submit" value="<?php echo SET_COMMENT_TITLE;?>"><p>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</form>
</body>
</html>
