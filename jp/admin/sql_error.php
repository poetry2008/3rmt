<?php
if($_SERVER["HTTP_ACCEPT_LANGUAGE"]){
  $lan_arr = explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  if($lan_arr[0]=='ja'){
    include('includes/languages/japanese/sql_error.php');
  }
}
?>
<HTML>
<HEAD>
<!-- locale-sensitive -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE><?php echo ERROR_TEXT;?></TITLE>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</HEAD>
<BODY BGCOLOR="#FFFFFF" >
<div class="error_body">
<table align="center" class="error_p">
  <tr>
    	<td valign="top"><img src="images/error_01.gif"></td>
        <td><div class="sql_text">
        	<?php echo TEXT_ERROR_PAGE_SHOW;?>
        </div>
		<div class="sql_text01"><input type="button" onClick="window.location.href='<?php echo $_SERVER['HTTP_REFERER'];?>'" value="<?php echo TEXT_ERROR_LINK_BACK;?>">
<input type="button" onClick="window.location.href='index.php'" value="<?php echo TEXT_ERROR_LINK_INDEX;?>">
		</div>
        <div class="sql_text02">
			<?php
            //echo "<br>";
            //echo "<a href='".$_SERVER['HTTP_REFERER']."'>".TEXT_ERROR_LINK_BACK."</a>";
            //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            //echo "<a href='index.php'>".TEXT_ERROR_LINK_INDEX."</a>";
            if(isset($_GET['string'])&&$_GET['string']){
              echo '<font color="#ff0000">'.$_GET['string'].'</font>';
            }
            ?>
        </div>
        </td>
    </tr>
</table>
</div>
<div class="error_box_bootom">&nbsp;</div>
</BODY>
</HTML>
