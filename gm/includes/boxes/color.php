<?php
$colors_query = tep_db_query("select * from ".TABLE_COLOR." order by sort_id, color_name");
if(tep_db_num_rows($colors_query)) {
?>
<!-- by color// -->
<div class="box_title">繧ｫ繝ｩ繝ｼ讀懃ｴ｢</div>
<ul id="box">

  <?php
  while($colors = tep_db_fetch_array($colors_query)) {
  ?> 
  <li> 
      <?php 
	 echo '<a href="'.tep_href_link(FILENAME_DEFAULT, 'colors=' . $colors['color_id']).'">';
	 if($HTTP_GET_VARS['colors'] && $HTTP_GET_VARS['colors'] == $colors['color_id']) {
	   echo '<b>'.$colors['color_name'].'</b>';
	 } else {
	   echo $colors['color_name'];
	 } 
	 echo '</a>';
	 ?>
	 </li> 
  <?php
  }
  ?> 
</ul> 
<!-- //by color --> 
<?php
}
?> 
