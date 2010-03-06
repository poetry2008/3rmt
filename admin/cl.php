<?php
/*
  $Id$
 */
include("includes/application_top.php");

if(isset($HTTP_POST_VARS['updata']) && $HTTP_POST_VARS['updata'] == 'on') {
	$mm_1 = "";
	for($i=1; $i<32; $i++) {
	  $mm_1 .= $_POST[$i];
	}
	tep_db_query("update ".TABLE_CALENDER." set cl_value='".$mm_1."' where cl_ym = '".$_POST['ymd']."' and site_id = '".(int)$HTTP_POST_VARS['site_id']."'");
	
	$mm_2 = "";
	for($j=101; $j<132; $j++) {
	  $mm_2 .= $_POST[$j];
	}
	tep_db_query("update ".TABLE_CALENDER." set cl_value='".$mm_2."' where cl_ym = '".$_POST['ymd2']."' and site_id = '".(int)$HTTP_POST_VARS['site_id']."'");
	
	tep_redirect(tep_href_link(FILENAME_CL, 'site_id='.(int)$HTTP_POST_VARS['site_id'].'&action=success'));
}

// 处理多站点
$sites    = tep_get_sites();
$sites_id = tep_get_sites_id();
if (isset($HTTP_GET_VARS['site_id']) && in_array((int)$HTTP_GET_VARS['site_id'], $sites_id)) {
  $site_id = (int)$HTTP_GET_VARS['site_id'];
} else {
  $site_id = $sites_id[0];
}
// 取得当前站信息
foreach($sites as $v){
  if ($sites['id'] = $site_id){
    $site = $v;
    continue;
  }
}

?>
<?php
if(isset($_GET['action']) && $_GET['action'] == 'success') {
  echo '<table border="0" cellspacing="0" cellpadding="0" width="100%">' . "\n";
  echo '<tr>' . "\n";
  echo '<td class="messageStackSuccess" height="20" align="center"><strong>更新しました。</strong></td>' . "\n";
  echo '</tr>' . "\n";
  echo '</table><br>' . "\n";
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo 'カレンダー編集'; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <?php foreach (tep_get_sites() as $s) {?>
            <?php if ($site_id == $s['id']) {?>
              <?php echo $s['romaji'];?>
            <?php } else {?>
              <a href="<?php echo tep_href_link(FILENAME_CL, tep_get_all_get_params(array('site_id')) . 'site_id=' . $s['id']);?>">
              <?php echo $s['romaji'];?>
              </a>
            <?php }
           }
          ?>
        </td>
      </tr>
      <tr>
        <td>

		<form action="<?php echo $PHP_SELF; ?>" method="post">
		<input type="hidden" name="updata" value="on">
		<?php
		//今日
		$today = getdate();
		
		$m_num = $today['mon'];
		$d_num = $today['mday'];
		$year = $today['year'];
		// 1日目の曜日
		$f_today = getdate(mktime(0,0,0,$m_num,1,$year));
		$wday = $f_today['wday'];
		// 月表示
		$m_name = "$year ".substr($today['month'],0,3);
		
		//前のつきの最終日（月の日数）
		$test = date("d", mktime(0,0,0,$m_num+2,0,$year));
		
		//月のデータ取得
		$ymd = date("Ym", time());
		$calen_query = tep_db_query("select cl_value from ".TABLE_CALENDER." where cl_ym = '".$ymd."' and site_id=".$site_id);
		$calen = tep_db_fetch_array($calen_query);
		
		$array = array("1"=>substr($calen['cl_value'], 0, 1), "2"=>substr($calen['cl_value'], 1, 1), "3"=>substr($calen['cl_value'], 2, 1), "4"=>substr($calen['cl_value'], 3, 1), "5"=>substr($calen['cl_value'], 4, 1), "6"=>substr($calen['cl_value'], 5, 1), "7"=>substr($calen['cl_value'], 6, 1), "8"=>substr($calen['cl_value'], 7, 1), "9"=>substr($calen['cl_value'], 8, 1), "10"=>substr($calen['cl_value'], 9, 1), "11"=>substr($calen['cl_value'], 10, 1), "12"=>substr($calen['cl_value'], 11, 1), "13"=>substr($calen['cl_value'], 12, 1), "14"=>substr($calen['cl_value'], 13, 1), "15"=>substr($calen['cl_value'], 14, 1), "16"=>substr($calen['cl_value'], 15, 1), "17"=>substr($calen['cl_value'], 16, 1), "18"=>substr($calen['cl_value'], 17, 1), "19"=>substr($calen['cl_value'], 18, 1), "20"=>substr($calen['cl_value'], 19, 1), "21"=>substr($calen['cl_value'], 20, 1), "22"=>substr($calen['cl_value'], 21, 1), "23"=>substr($calen['cl_value'], 22, 1), "24"=>substr($calen['cl_value'], 23, 1), "25"=>substr($calen['cl_value'], 24, 1), "26"=>substr($calen['cl_value'], 25, 1), "27"=>substr($calen['cl_value'], 26, 1), "28"=>substr($calen['cl_value'], 27, 1), "29"=>substr($calen['cl_value'], 28, 1), "30"=>substr($calen['cl_value'], 29, 1), "31"=>substr($calen['cl_value'], 30, 1));
		
		echo '
		  <table border=1 cellspacing=0 cellpadding=2 width="100%">
			<tr bgcolor=#000000>
			<td nowrap colspan=3>&nbsp;&nbsp;<font size=2 color=#FFFFFF>'.$year.'年'.$m_num.'月</font></td>
			<td nowrap colspan=4 align=right>&nbsp;&nbsp;<font size=2 color=#FFFFFF>&nbsp;&nbsp;■&raquo;通常営業&nbsp;&nbsp;<font color="'.CL_COLOR_01.'">■</font>&raquo;店舗休業&nbsp;&nbsp;<font color="'.CL_COLOR_02.'">■</font>&raquo;メール返信休業&nbsp;&nbsp;</td>
			</tr>
			<tr bgcolor=ffffff>
			  <td align=middle height=15><font size="2" color="#cc0000">日</font></td>
			  <td align=middle height=15><font size="2">月</font></td>
			  <td align=middle height=15><font size="2">火</font></td>
			  <td align=middle height=15><font size="2">水</font></td>
			  <td align=middle height=15><font size="2">木</font></td>
			  <td align=middle height=15><font size="2">金</font></td>
			  <td align=middle height=15><font size="2" color="#0000cc">土</font></td>
			</tr>
			<tr bgcolor=#ffffff>
		';
		
		for ($i=0; $i<$wday; $i++) { // Blank
		  echo "<td align=center>　</td>\n"; 
		}
		
		$day = 1;
		while(checkdate($m_num,$day,$year)){
		  //select form
		  if($array[$day] == '1') {
		    $option = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.$day.'" value="0"></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.$day.'" value="1" checked></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.$day.'" value="2"></td>' . "\n";
			$option .= '</tr></table>' . "\n";
		  } elseif($array[$day] == '2') {
		    $option = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.$day.'" value="0"></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.$day.'" value="1"></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.$day.'" value="2" checked></td>' . "\n";
			$option .= '</tr></table>' . "\n";
		  } else {
		    $option = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.$day.'" value="0" checked></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.$day.'" value="1"></td>' . "\n";
			$option .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.$day.'" value="2"></td>' . "\n";
			$option .= '</tr></table>' . "\n";
		  }
		  
		  
		  if(($day == $today['mday']) && ($m_num == $today['mon']) && ($year == $today['year'])){ 
			//  Today 
			if($array[$day] == '1'){
			
			echo "<td align=center bgcolor=".CL_COLOR_01."><font size=2><b>$day</b><br>".$option."</font></td>\n"; 
			}elseif($array[$day] == '2'){
			echo "<td align=center bgcolor=".CL_COLOR_02."><font size=2><b>$day</b><br>".$option."</font></td>\n"; 
			} else {
			echo "<td align=center><font size=2><b>$day</b><br>".$option."</font></td>\n"; 
			}
		  }
		  elseif($array[$day] == '1'){
			//お店の休業日
			echo "<td align=center bgcolor=".CL_COLOR_01."><font size=2>$day<br>".$option."</font></td>\n";
		  }
		  elseif($array[$day] == '2'){
			//メール返信休業日
			echo "<td align=center bgcolor=".CL_COLOR_02."><font size=2>$day<br>".$option."</font></td>\n";  
		  }  
		  elseif($wday == 0){ 
			//  Sunday
			echo "<td align=center bgcolor=><font size=2 color=#cc0000>$day<br>".$option."</font></td>\n";
		  }
		  elseif($wday == 6){ 
			//  Saturday
			echo "<td align=center><font size=2 color=#0000cc>$day<br>".$option."</font></td>\n";
		  }
		  else{ 
			// Weekday
			echo "<td align=center><font size=2>$day<br>".$option."</font></td>\n";
		  }
		  // 改行
		  if($wday == 6) echo "</tr><tr bgcolor=#ffffff>";
		  $day++;
		  $wday++;
		  $wday = $wday % 7;
		}
		if($wday > 0){
		  while($wday < 7) { // Blank
			echo "<td align=center>　</td>\n";
			$wday++;
		  }
		} else {
		  echo "<td colspan=7></td>\n";
		}
		echo '</tr></table>' . "\n";
		
		//----------------------------------------------------
		echo '<br><br>';
		//次月
		//----------------------------------------------------
		
		$today2 = getdate(mktime(0,0,0,$m_num+1,1,$year));
		
		$m_num2 = $today2['mon'];
		$d_num2 = $today2['mday'];
		$year2 = $today2['year'];
		// 1日目の曜日
		$f_today2 = getdate(mktime(0,0,0,$m_num2,1,$year2));
		$wday2 = $f_today2['wday'];
		// 月表示
		$m_name2 = "$year ".substr($today2['month'],0,3);
		
		//月のデータ取得
		$ymd2 = date("Ym", mktime(0,0,0,$m_num2,1,$year2));
		$calen_query2 = tep_db_query("select cl_value from ".TABLE_CALENDER." where cl_ym = '".$ymd2."' and site_id=".$site_id);
		$calen2 = tep_db_fetch_array($calen_query2);
		
		$array2 = array("101"=>substr($calen2['cl_value'], 0, 1), "102"=>substr($calen2['cl_value'], 1, 1), "103"=>substr($calen2['cl_value'], 2, 1), "104"=>substr($calen2['cl_value'], 3, 1), "105"=>substr($calen2['cl_value'], 4, 1), "106"=>substr($calen2['cl_value'], 5, 1), "107"=>substr($calen2['cl_value'], 6, 1), "108"=>substr($calen2['cl_value'], 7, 1), "109"=>substr($calen2['cl_value'], 8, 1), "110"=>substr($calen2['cl_value'], 9, 1), "111"=>substr($calen2['cl_value'], 10, 1), "112"=>substr($calen2['cl_value'], 11, 1), "113"=>substr($calen2['cl_value'], 12, 1), "114"=>substr($calen2['cl_value'], 13, 1), "115"=>substr($calen2['cl_value'], 14, 1), "116"=>substr($calen2['cl_value'], 15, 1), "117"=>substr($calen2['cl_value'], 16, 1), "118"=>substr($calen2['cl_value'], 17, 1), "119"=>substr($calen2['cl_value'], 18, 1), "120"=>substr($calen2['cl_value'], 19, 1), "121"=>substr($calen2['cl_value'], 20, 1), "122"=>substr($calen2['cl_value'], 21, 1), "123"=>substr($calen2['cl_value'], 22, 1), "124"=>substr($calen2['cl_value'], 23, 1), "125"=>substr($calen2['cl_value'], 24, 1), "126"=>substr($calen2['cl_value'], 25, 1), "127"=>substr($calen2['cl_value'], 26, 1), "128"=>substr($calen2['cl_value'], 27, 1), "129"=>substr($calen2['cl_value'], 28, 1), "130"=>substr($calen2['cl_value'], 29, 1), "131"=>substr($calen2['cl_value'], 30, 1));
		
		echo '
		  <table border=1 cellspacing=0 cellpadding=2 width=100%>
			<tr bgcolor=#000000>
			<td nowrap colspan=3>&nbsp;&nbsp;<font size=2 color=#FFFFFF>'.$year2.'年'.$m_num2.'月</font></td>
			<td nowrap colspan=4 align=right>&nbsp;&nbsp;<font size=2 color=#FFFFFF>&nbsp;&nbsp;■&raquo;通常営業&nbsp;&nbsp;<font color="'.CL_COLOR_01.'">■</font>&raquo;店舗休業&nbsp;&nbsp;<font color="'.CL_COLOR_02.'">■</font>&raquo;メール返信休業&nbsp;&nbsp;</td>
			</tr>
			<tr bgcolor=ffffff>
			  <td align=middle height=15><font size="2" color="#cc0000">日</font></td>
			  <td align=middle height=15><font size="2">月</font></td>
			  <td align=middle height=15><font size="2">火</font></td>
			  <td align=middle height=15><font size="2">水</font></td>
			  <td align=middle height=15><font size="2">木</font></td>
			  <td align=middle height=15><font size="2">金</font></td>
			  <td align=middle height=15><font size="2" color="#0000cc">土</font></td>
			</tr>
			<tr bgcolor=#ffffff>
		';
		
		for ($i=0; $i<$wday2; $i++) { // Blank
		  echo "<td align=center>　</td>\n"; 
		}
		
		$day2 = 1;
		while(checkdate($m_num2,$day2,$year2)){
		  //select form
		  if($array2['1'.str_pad($day2, 2, 0, STR_PAD_LEFT)] == '1') {
		    $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0"></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1" checked></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2"></td>' . "\n";
			$option2 .= '</tr></table>' . "\n";
		  } elseif($array2['1'.str_pad($day2, 2, 0, STR_PAD_LEFT)] == '2') {
		    $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0"></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1"></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2" checked></td>' . "\n";
			$option2 .= '</tr></table>' . "\n";
		  } else {
		    $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
			$option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0" checked></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1"></td>' . "\n";
			$option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="1'.str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2"></td>' . "\n";
			$option2 .= '</tr></table>' . "\n";
		  }
		  
		  
		  if($array2['1'.str_pad($day2, 2, 0, STR_PAD_LEFT)] == '1'){
			//お店の休業日
			echo "<td align=center bgcolor=".CL_COLOR_01."><font size=2>$day2<br>".$option2."</font></td>\n";
		  }
		  elseif($array2['1'.str_pad($day2, 2, 0, STR_PAD_LEFT)] == '2'){
			//メール返信休業日
			echo "<td align=center bgcolor=".CL_COLOR_02."><font size=2>$day2<br>".$option2."</font></td>\n";  
		  }  
		  elseif($wday == 0){ 
			//  Sunday
			echo "<td align=center bgcolor=><font size=2 color=#cc0000>$day2<br>".$option2."</font></td>\n";
		  }
		  elseif($wday == 6){ 
			//  Saturday
			echo "<td align=center><font size=2 color=#0000cc>$day2<br>".$option2."</font></td>\n";
		  }
		  else{ 
			// Weekday
			echo "<td align=center><font size=2>$day2<br>".$option2."</font></td>\n";
		  }
		  // 改行
		  if($wday2 == 6) echo "</tr><tr bgcolor=#ffffff>";
		  $day2++;
		  $wday2++;
		  $wday2 = $wday2 % 7;
		}
		if($wday2 > 0){
		  while($wday2 < 7) { // Blank
			echo "<td align=center>　</td>\n";
			$wday2++;
		  }
		} else {
		  echo "<td colspan=7></td>\n";
		}
		echo '</tr></table>' . "\n";
		
		echo '<P>'.tep_image_submit('button_update.gif', IMAGE_UPDATE).'</P>'


		?>
		<input type="hidden" name="ymd" value="<?php echo $ymd; ?>">
		<input type="hidden" name="ymd2" value="<?php echo $ymd2; ?>">
		<input type="hidden" name="site_id" value="<?php echo $site_id; ?>">
		</form>
		</td>
      </tr>
  </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
