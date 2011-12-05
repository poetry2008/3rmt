<?php
  require('includes/application_top.php');
?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']&&false){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<?php
  if(isset($_GET['url'])&&$_GET['url']){
    echo "<a href='".urldecode($_GET['url'])."'>";
    echo urldecode($_GET['url']);
    echo "</a>";
  }
 require(DIR_WS_INCLUDES . 'footer.php');
?>
