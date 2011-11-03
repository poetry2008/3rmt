<?php
  require('includes/application_top.php');
?>
<div class="popwin" style="width:420px">
<h3 class="popup_title"><?php echo TEXT_ADD_NOTE;?></h3>
<div id="note_form">
<form id="add_form" action="#" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="posttable">
<tr>
<td ><?php echo TEXT_COMMENT_NOTE;?><span id="msg"></span></td>
</tr>
<tr>
<td ><textarea name="note_txt" id="note_txt" class="input" style="width:98%; height:80px"></textarea></td>
</tr>
<tr>
<td><?php echo TEXT_COLOR;?></td>
</tr>
<tr>
<td><ul id="color">
<li class="yellow"></li>
<li class="blue"></li>
<li class="green"></li>
</ul><input type="hidden" id="mycolor" value="yellow" /></td>
</tr>
<tr>
<td height="36" colspan="2" align="center"><input type="submit" id="addbtn"
class="btn" value="<?php echo IMAGE_SAVE?>" /> <input type="button" class="btn"
value="<?php echo IMAGE_CANCEL?>" id="cancel" onclick="$.fancybox.close()" /></td>
</tr>
</table>
</form>
</div>
</div>
