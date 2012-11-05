<?php
  require('includes/application_top.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<link href="includes/stylesheet.css" type="text/css" rel="stylesheet">
</head>
<body>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']&&false){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<?php
  if(isset($_GET['url'])&&$_GET['url']){
    echo "<div class='box_link'><a href='".urldecode($_GET['url'])."'>";
    echo urldecode($_GET['url']);
    echo "</a></div>";
  }
 require(DIR_WS_INCLUDES . 'footer.php');
?>
</body>
</html>
