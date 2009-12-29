<?php
$colors_query = tep_db_query("select * from ".TABLE_COLOR." order by sort_id, color_name");
if(tep_db_num_rows($colors_query)) {
?>
<!-- by color// -->
 <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
  <tr> 
     <td height="25" align="center" background="images/design/box/box_title_bg.jpg"><?php echo tep_image(DIR_WS_IMAGES.'design/box/color.gif','カラーから選択');?></td> 
   </tr> 
</table> 
<table width="100%"  border="0" cellspacing="1" cellpadding="0"> 
  <?php
  while($colors = tep_db_fetch_array($colors_query)) {
  ?> 
  <tr> 
    <td height="23" class="menu"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle" > 
      <?php 
	 echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'colors=' . $colors['color_id']).'">';
	 if($HTTP_GET_VARS['colors'] && $HTTP_GET_VARS['colors'] == $colors['color_id']) {
	   echo '<b>'.$colors['color_name'].'</b>';
	 } else {
	   echo $colors['color_name'];
	 } 
	 echo '</a>';
	 ?> </td> 
  </tr> 
  <?php
  }
  ?> 
</table> 
<!-- //by color --> 
<?php
}
?> 
