<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- clender //-->
<div class="box_title">カレンダー</div>
			<?php

//今日
$today = getdate();

$m_num = $today[mon];
$d_num = $today[mday];
$year = $today[year];
// 1日目の曜日
$f_today = getdate(mktime(0,0,0,$m_num,1,$year));
$wday = $f_today[wday];
// 月表示
$m_name = "$year ".substr($today[month],0,3);

//年月取得
$ymd = date("Ym", time());

  echo '<strong>'.$year.'年'.$m_num.'月'.BOX_HEADING_CL.'</strong>';

  //new infoBoxHeading($info_box_contents, false, false);



$calen_query = tep_db_query("select * from ".TABLE_CL." where cl_ym = '".$ymd."'");
$calen = tep_db_fetch_array($calen_query);

$array = array("1"=>substr($calen['cl_value'], 0, 1), "2"=>substr($calen['cl_value'], 1, 1), "3"=>substr($calen['cl_value'], 2, 1), "4"=>substr($calen['cl_value'], 3, 1), "5"=>substr($calen['cl_value'], 4, 1), "6"=>substr($calen['cl_value'], 5, 1), "7"=>substr($calen['cl_value'], 6, 1), "8"=>substr($calen['cl_value'], 7, 1), "9"=>substr($calen['cl_value'], 8, 1), "10"=>substr($calen['cl_value'], 9, 1), "11"=>substr($calen['cl_value'], 10, 1), "12"=>substr($calen['cl_value'], 11, 1), "13"=>substr($calen['cl_value'], 12, 1), "14"=>substr($calen['cl_value'], 13, 1), "15"=>substr($calen['cl_value'], 14, 1), "16"=>substr($calen['cl_value'], 15, 1), "17"=>substr($calen['cl_value'], 16, 1), "18"=>substr($calen['cl_value'], 17, 1), "19"=>substr($calen['cl_value'], 18, 1), "20"=>substr($calen['cl_value'], 19, 1), "21"=>substr($calen['cl_value'], 20, 1), "22"=>substr($calen['cl_value'], 21, 1), "23"=>substr($calen['cl_value'], 22, 1), "24"=>substr($calen['cl_value'], 23, 1), "25"=>substr($calen['cl_value'], 24, 1), "26"=>substr($calen['cl_value'], 25, 1), "27"=>substr($calen['cl_value'], 26, 1), "28"=>substr($calen['cl_value'], 27, 1), "29"=>substr($calen['cl_value'], 28, 1), "30"=>substr($calen['cl_value'], 29, 1), "31"=>substr($calen['cl_value'], 30, 1));

$cl_string = '
  <table border="0" cellspacing="1" cellpadding="2" width="100%" class="tableTop" align="center" bgcolor="#CBCBCB">
 
	<tr bgcolor=ffffff>
      <td align=middle height=20 class="smallText" bgcolor="#A43734"><font color="#FFFFFF">日</font></td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">月</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">火</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">水</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">木</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">金</td>
      <td align=middle height=20 class="smallText" bgcolor="#3A70AC"><font color="#FFFFFF">土</font></td>
    </tr>
    <tr bgcolor=#ffffff>
';

for ($i=0; $i<$wday; $i++) { // Blank
  $cl_string .= "<td align=center>&nbsp;</td>\n"; 
}

$day = 1;
while(checkdate($m_num,$day,$year)){
  if(($day == $today[mday]) && ($m_num == $today[mon]) && ($year == $today[year])){ 
    //  Today 
	if($array[$day] == '1'){
    $cl_string .= "<td align=center bgcolor=".CL_COLOR_01." class=\"smallText\"><b>$day</b></td>\n"; 
	}elseif($array[$day] == '2'){
	$cl_string .= "<td align=center bgcolor=".CL_COLOR_02." class=\"smallText\"><b>$day</b></td>\n"; 
	} else {
	$cl_string .= "<td align=center  class=\"smallText\"><b>$day</b></td>\n"; 
	}
  }
  elseif($array[$day] == '1'){
    //お店の休業日
	$cl_string .= "<td align=center bgcolor=".CL_COLOR_01." class=\"smallText\"><font color=\"#FFFFFF\">$day</font></td>\n";
  }
  elseif($array[$day] == '2'){
    //メール返信休業日
	$cl_string .= "<td align=center bgcolor=".CL_COLOR_02." class=\"smallText\">$day</td>\n";  
  }  
  elseif($wday == 0){ 
    //  Sunday
    $cl_string .= "<td align=center  class=\"smallText\"><font color=#cc0000>$day</font></td>\n";
  }
  elseif($wday == 6){ 
    //  Saturday
    $cl_string .= "<td align=center class=\"smallText\"><font color=#0000cc>$day</font></td>\n";
  }
  else{ 
    // Weekday
    $cl_string .= "<td align=center class=\"smallText\">$day</td>\n";
  }
  // 改行
  if($wday == 6) $cl_string .= "</tr><tr bgcolor=#ffffff>";
  $day++;
  $wday++;
  $wday = $wday % 7;
}
if($wday > 0){
  while($wday < 7) { // Blank
    $cl_string .= "<td align=center></td>\n";
    $wday++;
  }
} else {
  $cl_string .= "<td colspan=7></td>\n";
}
$cl_string .= '</tr></table></div>' . "\n";



echo $cl_string ;
  //new infoBox($info_box_contents);



// NEXT CL

		$today2 = getdate(mktime(0,0,0,$m_num+1,1,$year));
		
		$m_num2 = $today2[mon];
		$d_num2 = $today2[mday];
		$year2 = $today2[year];
		// 1日目の曜日
		$f_today2 = getdate(mktime(0,0,0,$m_num2,1,$year2));
		$wday2 = $f_today2[wday];
		// 月表示
		$m_name2 = "$year ".substr($today2[month],0,3);
		
		//月のデータ取得
		$ymd2 = date("Ym", mktime(0,0,0,$m_num2,1,$year2));
		$calen_query2 = tep_db_query("select cl_value from ".TABLE_CL." where cl_ym = '".$ymd2."'");
		$calen2 = tep_db_fetch_array($calen_query2);
		
		$array2 = array("101"=>substr($calen2['cl_value'], 0, 1), "102"=>substr($calen2['cl_value'], 1, 1), "103"=>substr($calen2['cl_value'], 2, 1), "104"=>substr($calen2['cl_value'], 3, 1), "105"=>substr($calen2['cl_value'], 4, 1), "106"=>substr($calen2['cl_value'], 5, 1), "107"=>substr($calen2['cl_value'], 6, 1), "108"=>substr($calen2['cl_value'], 7, 1), "109"=>substr($calen2['cl_value'], 8, 1), "110"=>substr($calen2['cl_value'], 9, 1), "111"=>substr($calen2['cl_value'], 10, 1), "112"=>substr($calen2['cl_value'], 11, 1), "113"=>substr($calen2['cl_value'], 12, 1), "114"=>substr($calen2['cl_value'], 13, 1), "115"=>substr($calen2['cl_value'], 14, 1), "116"=>substr($calen2['cl_value'], 15, 1), "117"=>substr($calen2['cl_value'], 16, 1), "118"=>substr($calen2['cl_value'], 17, 1), "119"=>substr($calen2['cl_value'], 18, 1), "120"=>substr($calen2['cl_value'], 19, 1), "121"=>substr($calen2['cl_value'], 20, 1), "122"=>substr($calen2['cl_value'], 21, 1), "123"=>substr($calen2['cl_value'], 22, 1), "124"=>substr($calen2['cl_value'], 23, 1), "125"=>substr($calen2['cl_value'], 24, 1), "126"=>substr($calen2['cl_value'], 25, 1), "127"=>substr($calen2['cl_value'], 26, 1), "128"=>substr($calen2['cl_value'], 27, 1), "129"=>substr($calen2['cl_value'], 28, 1), "130"=>substr($calen2['cl_value'], 29, 1), "131"=>substr($calen2['cl_value'], 30, 1));
		
		echo '<br><strong>'.$year2.'年'.$m_num2.'月'.BOX_HEADING_CL.'</strong> 
  <table border="0" cellspacing="1" cellpadding="2" width="162" class="tableTop" align="center" bgcolor="#CBCBCB">
 
	<tr bgcolor=ffffff>
      <td align=middle height=20 class="smallText" bgcolor="#A43734"><font color="#FFFFFF">日</font></td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">月</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">火</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">水</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">木</td>
      <td align=middle height=20 class="smallText" bgcolor="#E5E5E5">金</td>
      <td align=middle height=20 class="smallText" bgcolor="#3A70AC"><font color="#FFFFFF">土</font></td>
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
			echo "<td align=center bgcolor=".CL_COLOR_01." class=\"smallText\"><font color=\"#FFFFFF\">$day2</font></td>\n";
		  }
		  elseif($array2['1'.str_pad($day2, 2, 0, STR_PAD_LEFT)] == '2'){
			//メール返信休業日
			echo "<td align=center bgcolor=".CL_COLOR_02." class=\"smallText\">$day2</td>\n";  
		  }  
		  elseif($wday2 == 0){ 
			//  Sunday
			echo "<td align=\"center\" class=\"smallText\"><font color=#cc0000>$day2</font></td>\n";
		  }
		  elseif($wday2 == 6){ 
			//  Saturday
			echo "<td align=\"center\" class=\"smallText\"><font color=#0000cc>$day2</font></td>\n";
		  }
		  else{ 
			// Weekday
			echo "<td align=\"center\" class=\"smallText\">$day2</td>\n";
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

echo '<p class="smallText" align="left" style="margin:10px;"><font color="'.CL_COLOR_01.'">■</font>の部分はお休みです。
<font color="'.CL_COLOR_02.'">■</font>は出荷・メールでのご返信はお休みとさせていただきます。</p>';
?>
<!-- clender_eof //-->
