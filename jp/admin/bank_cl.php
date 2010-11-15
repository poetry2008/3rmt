<?php
include("includes/application_top.php");

if($_POST['updata'] == 'on') {
  /*
  echo "<pre>";
  print_r($_POST);
  exit;
  /**/
  /*
  $mm_1 = "";
  for($i=1; $i<32; $i++) {
    $mm_1 .= $_POST[$i];
  }
  tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_1."' where cl_ym = '".$_POST['ymd1']."'");
  print("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_1."' where cl_ym = '".$_POST['ymd1']."'");
  */

  $mm_2 = "";
  for($j=201; $j<232; $j++) {
    $mm_2 .= $_POST[$j];
  }
  
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd2']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_2."' where cl_ym = '".$_POST['ymd2']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd2']."','".$mm_2."')");
  }
  
  $mm_3 = "";
  for($j=301; $j<332; $j++) {
    $mm_3 .= $_POST[$j];
  }
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd3']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_3."' where cl_ym = '".$_POST['ymd3']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd3']."','".$mm_3."')");
  }
    
  $mm_4 = "";
  for($j=401; $j<432; $j++) {
    $mm_4 .= $_POST[$j];
  }
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd4']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_4."' where cl_ym = '".$_POST['ymd4']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd4']."','".$mm_4."')");
  }
  
  $mm_5 = "";
  for($j=501; $j<532; $j++) {
    $mm_5 .= $_POST[$j];
  }
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd5']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_5."' where cl_ym = '".$_POST['ymd5']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd5']."','".$mm_5."')");
  }
  
  $mm_6 = "";
  for($j=601; $j<632; $j++) {
    $mm_6 .= $_POST[$j];
  }
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd6']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_6."' where cl_ym = '".$_POST['ymd6']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd6']."','".$mm_6."')");
  }
  
  $mm_7 = "";
  for($j=701; $j<732; $j++) {
    $mm_7 .= $_POST[$j];
  }
  if (tep_db_num_rows("select * from ".TABLE_BANK_CALENDAR." where cl_ym = '".$_POST['ymd7']."'")) {
    tep_db_query("update ".TABLE_BANK_CALENDAR." set cl_value='".$mm_7."' where cl_ym = '".$_POST['ymd7']."'");
  } else {
    tep_db_query("insert into  ".TABLE_BANK_CALENDAR." (cl_ym,cl_value) values ('".$_POST['ymd7']."','".$mm_7."')");
  }
  
  tep_redirect(tep_href_link(FILENAME_BANK_CL, 'action=success&date='.$_POST['date']));
}
?>
<?php

if($_GET['action'] == 'success') {
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body bgcolor="#FFFFFF" onLoad="SetFocus();">
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

    <form action="<?php echo $PHP_SELF; ?>" method="post">
    <input type="hidden" name="updata" value="on">
    <input type="hidden" name="date" value="<?php echo $_GET['date'];?>">
    <?php
    //今日
    
    $now = $_GET['date']?$_GET['date']:date('Y-m-d');
    $m_num = date('m',strtotime($now)) - 1;
    $year = date('Y',strtotime($now));

    //----------------------------------------------------
    for($ii = 1;$ii<7;$ii++){
    echo '<br><br>';
    //----------------------------------------------------
    
    $today2 = getdate(mktime(0,0,0,$m_num+$ii,1,$year));
    
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
    $calen_query2 = tep_db_query("select cl_value from ".TABLE_BANK_CALENDAR." where cl_ym = '".$ymd2."'");
    //echo("select cl_value from ".TABLE_BANK_CALENDAR." where cl_ym = '".$ymd2."'");
    $calen2 = tep_db_fetch_array($calen_query2);
    
    $array2 = array(
    ($ii+1)."01"=>substr($calen2['cl_value'], 0, 1), 
    ($ii+1)."02"=>substr($calen2['cl_value'], 1, 1), 
    ($ii+1)."03"=>substr($calen2['cl_value'], 2, 1), 
    ($ii+1)."04"=>substr($calen2['cl_value'], 3, 1), 
    ($ii+1)."05"=>substr($calen2['cl_value'], 4, 1), 
    ($ii+1)."06"=>substr($calen2['cl_value'], 5, 1), 
    ($ii+1)."07"=>substr($calen2['cl_value'], 6, 1), 
    ($ii+1)."08"=>substr($calen2['cl_value'], 7, 1), 
    ($ii+1)."09"=>substr($calen2['cl_value'], 8, 1), 
    ($ii+1)."10"=>substr($calen2['cl_value'], 9, 1), 
    ($ii+1)."11"=>substr($calen2['cl_value'], 10, 1), 
    ($ii+1)."12"=>substr($calen2['cl_value'], 11, 1), 
    ($ii+1)."13"=>substr($calen2['cl_value'], 12, 1), 
    ($ii+1)."14"=>substr($calen2['cl_value'], 13, 1), 
    ($ii+1)."15"=>substr($calen2['cl_value'], 14, 1), 
    ($ii+1)."16"=>substr($calen2['cl_value'], 15, 1), 
    ($ii+1)."17"=>substr($calen2['cl_value'], 16, 1), 
    ($ii+1)."18"=>substr($calen2['cl_value'], 17, 1), 
    ($ii+1)."19"=>substr($calen2['cl_value'], 18, 1), 
    ($ii+1)."20"=>substr($calen2['cl_value'], 19, 1), 
    ($ii+1)."21"=>substr($calen2['cl_value'], 20, 1), 
    ($ii+1)."22"=>substr($calen2['cl_value'], 21, 1), 
    ($ii+1)."23"=>substr($calen2['cl_value'], 22, 1), 
    ($ii+1)."24"=>substr($calen2['cl_value'], 23, 1), 
    ($ii+1)."25"=>substr($calen2['cl_value'], 24, 1), 
    ($ii+1)."26"=>substr($calen2['cl_value'], 25, 1), 
    ($ii+1)."27"=>substr($calen2['cl_value'], 26, 1), 
    ($ii+1)."28"=>substr($calen2['cl_value'], 27, 1), 
    ($ii+1)."29"=>substr($calen2['cl_value'], 28, 1), 
    ($ii+1)."30"=>substr($calen2['cl_value'], 29, 1), 
    ($ii+1)."31"=>substr($calen2['cl_value'], 30, 1));
    //print_r($array2);
    echo '
      <table border=1 cellspacing=0 cellpadding=2 width=100%>
      <tr bgcolor=#000000>
      <td nowrap colspan=3>&nbsp;&nbsp;<font size=2 color=#FFFFFF>'.$year2.'年'.$m_num2.'月</font></td>
      <td nowrap colspan=4 align=right>&nbsp;&nbsp;<font size=2 color=#FFFFFF>&nbsp;&nbsp;■&raquo;通常営業&nbsp;&nbsp;<font color="'.CL_COLOR_01.'">■</font>&raquo;銀行休業&nbsp;&nbsp;<font color="'.CL_COLOR_02.'">■</font>&raquo;メール返信休業&nbsp;&nbsp;</td>
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
      if($array2[(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT)] == '1') {
        $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
      $option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0"></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1" checked></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2"></td>' . "\n";
      $option2 .= '</tr></table>' . "\n";
      } elseif($array2[(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT)] == '2') {
        $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
      $option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0"></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1"></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2" checked></td>' . "\n";
      $option2 .= '</tr></table>' . "\n";
      } else {
        $option2 = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
      $option2 .= '<td bgcolor="#FFFFFF"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="0" checked></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_01.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="1"></td>' . "\n";
      $option2 .= '<td bgcolor="'.CL_COLOR_02.'"><input type="radio" name="'.(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT).'" value="2"></td>' . "\n";
      $option2 .= '</tr></table>' . "\n";
      }
      
      
      if($array2[(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT)] == '1'){
      //お店の休業日
      echo "<td align=center bgcolor=".CL_COLOR_01."><font size=2>$day2<br>".$option2."</font></td>\n";
      }
      elseif($array2[(1+$ii).str_pad($day2, 2, 0, STR_PAD_LEFT)] == '2'){
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
    echo '<input type="hidden" name="ymd'.($ii+1).'" value="'.$ymd2.'">';
    }
    echo '<P>'.tep_image_submit('button_update.gif', IMAGE_UPDATE).'</P>'
    ?>
    </form>
    </td>
      </tr>
    <tr>
      <td align="right">
        <table border="0">
          <tr>
            <td><a href="<?php echo tep_href_link('bank_cl.php', 'date='.date('Y-m',mktime(0,0,0,$m_num-1,1,$year)));?>">前へ</a></td>
            <td><a href="<?php echo tep_href_link('bank_cl.php', 'date='.date('Y-m',mktime(0,0,0,$m_num+7,1,$year)));?>">次へ</a></td>
            <td>
              <select name="year" id="year">
              <?php for($i=date('Y');$i<date('Y')+20;$i++) { ?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
              <?php } ?>
              </select>
              <select name="month" id="month" onchange="if(this.value){window.location.href='bank_cl.php?date='+document.getElementById('year').value+'-'+document.getElementById('month').value}">
                <option value="0"> -- </option>
              <?php for($i=1;$i<13;$i++) { ?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
              <?php } ?>
              </select>
            </td>
          </tr>
        </table>
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
