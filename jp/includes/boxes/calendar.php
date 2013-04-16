<?php
/*
  $Id$
 */

 //获取显示几个月的日历
 $month_num = get_configuration_by_site_id('CALENDAR_FRONT_DESK_SETTING_MONTH',SITE_ID);
 $month_num = $month_num == '' ? get_configuration_by_site_id('CALENDAR_FRONT_DESK_SETTING_MONTH',0) : $month_num;
 if($month_num > 2){
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
<tr> 
          <td background="images/design/box/box_title_bg.jpg" align="center" height="25"><img width="164" height="17" src="images/design/box/calendar.gif"></td> 
        </tr> 
</table>
<div align="center" style="height:300px;overflow-x:hidden;overflow-y:auto;padding:5px 5px 5px 5px; font-size:12px;">
<?php
 }else{
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%"> 
<tr> 
          <td background="images/design/box/box_title_bg.jpg" align="center" height="25"><img width="164" height="17" src="images/design/box/calendar.gif"></td> 
        </tr> 
</table>
<div align="center" style="padding:5px 5px 5px 5px; font-size:12px;">
<?php
 }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php 
  //生成银行状态说明列表
  $status_str = '<table border="0" cellspacing="0" cellpadding="0">';
  $cl_status_array = array();
  $calendar_status_query = tep_db_query("select id,title,name,color,is_show,front_desk_show from ". TABLE_CALENDAR_STATUS ." where front_desk_comment_show=1 order by sort asc,id asc");
  while($calendar_status_array = tep_db_fetch_array($calendar_status_query)){

    $status_str .= '<tr><td class="main">';
    $status_str .= '<div style="float: left; background-color: '.$calendar_status_array['color'].'; border: 1px solid #CCCCCC; padding: 6px; font-size:12px; margin-top:2px;"></div>
	<div style="float: left; width:86%;margin-left:4px;">'.$calendar_status_array['name'];
    $status_str .= '</div></td></tr>';
    $cl_status_array[$calendar_status_array['id']] = array('is_show'=>$calendar_status_array['is_show'],'color'=>$calendar_status_array['color'],'front_desk_show'=>$calendar_status_array['front_desk_show']);
  }
  tep_db_free_result($calendar_status_query);
  $status_str .= '</table>';

  //重复设置处理
  $repeat_array = array(); 
  $repeat_date_query = tep_db_query("select id,cl_date,type,repeat_type,is_show,date_update from ". TABLE_CALENDAR_DATE ." where repeat_type!=0 order by date_update asc");    
  while($repeat_date_array = tep_db_fetch_array($repeat_date_query)){

    $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$repeat_date_array['type']."'");
    $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
    tep_db_free_result($repeat_sort_query);
    $repeat_array[$repeat_date_array['id']] = array('cl_date'=>$repeat_date_array['cl_date'],'repeat'=>$repeat_date_array['repeat_type'],'type'=>$repeat_date_array['type'],'is_show'=>$repeat_date_array['is_show'],'date_update'=>$repeat_date_array['date_update'],'sort'=>($repeat_date_array['type'] == 0 ? -1 : $repeat_sort_array['sort']));
  }
  tep_db_free_result($repeat_date_query);

  //分类处理特殊重复设置

  foreach($repeat_array as $cl_key=>$cl_value){

    if($cl_value['repeat'] == 1){

      $cl_repeat_array[1][$cl_key] = tep_get_repeat_date(1,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 2){

      $cl_repeat_array[2][$cl_key] = tep_get_repeat_date(2,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 3){

      $cl_repeat_array[3][$cl_key] = tep_get_repeat_date(3,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 4){

      $cl_repeat_array[4][$cl_key] = tep_get_repeat_date(4,$cl_value['cl_date']);
    }
  }

  //重复周
  $cl_week_array = array();
  foreach($cl_repeat_array[1] as $cl_week_key=>$cl_week_value){

    $cl_week_array[$cl_week_value] = $cl_week_key;
  }

  //每月重复的日
  $cl_month_day_array = array();
  foreach($cl_repeat_array[2] as $cl_month_key=>$cl_month_value){

    $cl_month_day_array[$cl_month_value] = $cl_month_key;
  }

  //每月重复固定周
  $cl_month_week_array = array();
  foreach($cl_repeat_array[3] as $cl_month_week_key=>$cl_month_week_value){

    $cl_month_week_array[$cl_month_week_value[0]][$cl_month_week_value[1]] = $cl_month_week_key;
  }

  //每年重复的月日
  $cl_year_month_array = array();
  foreach($cl_repeat_array[4] as $cl_year_month_key=>$cl_year_month_value){

    $cl_year_month_array[$cl_year_month_value] = $cl_year_month_key; 
  }

  $select_year = date("Y");
  $calendar_date_query = tep_db_query("select cl_date,type,is_show,date_update from ". TABLE_CALENDAR_DATE ." where cl_date like '".$select_year."%'");
  $cl_array = array();
  while($calendar_date_array = tep_db_fetch_array($calendar_date_query)){

    $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$calendar_date_array['type']."'");
    $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
    tep_db_free_result($repeat_sort_query);
    $cl_array[$calendar_date_array['cl_date']] = array('type'=>$calendar_date_array['type'],'is_show'=>$calendar_date_array['is_show'],'date_update'=>$calendar_date_array['date_update'],'sort'=>$repeat_sort_array['sort']);
  }
  tep_db_free_result($calendar_date_query);
?>
<tr>
<td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <?php
    $default_bgcolor = '#FFFFFF';
    $search_year = date('Y');
    $now = $search_year.'-1';
    $m_num = date('m',strtotime($now.'-1 00:00:00')) - 1;
    $year = date('Y',strtotime($now.'-1 00:00:00'));

    //获取日历的标题 
    $date_title = get_configuration_by_site_id('CALENDAR_FRONT_DESK_SETTING_TITLE',SITE_ID);
    $date_title = $date_title == '' ? get_configuration_by_site_id('CALENDAR_FRONT_DESK_SETTING_TITLE',0) : $date_title;
 
    for($ii = date('m');$ii<=date('m')+$month_num-1;$ii++){
    $today2 = getdate(mktime(0,0,0,$m_num+$ii,1,$year));
    $m_num2 = $today2['mon'];
    $year2 = $today2['year'];
    // 第一天对应的星期
    $f_today2 = getdate(mktime(0,0,0,$m_num2,1,$year2));
    $wday2 = $f_today2['wday'];
 
    echo '
      <tr><td valign="top"><table border="0" cellspacing="0" cellpadding="2" width="100%" class="date_bgcolor" style="font-size:12px; margin-top:5px;">
      <tr>
      <td colspan="7" align="center"><b>'.str_replace('${MONTH}',$m_num2,$date_title).'</b></td> 
      </tr>
      <tr bgcolor="#EEEEEE">
        <td align="middle" height="15">'.CL_TEXT_DATE_SUNDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_MONDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_TUESDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_WEDNESDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_THURSDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_FRIDAY.'</font></td>
        <td align="middle" height="15">'.CL_TEXT_DATE_STATURDAY.'</font></td>
      </tr>
      <tr bgcolor="#ffffff">
    ';
    
    for ($i=0; $i<$wday2; $i++) { // Blank
      echo "<td align=center bgcolor='#ffffff'>　</td>\n"; 
    }
    
    $day2 = 1;
    while(checkdate($m_num2,$day2,$year2)){
      $cl_date_str = $year2.($m_num2 <= 9 ? '0'.$m_num2 : $m_num2).($day2 <= 9 ? '0'.$day2 : $day2); 
      $cl_bgcolor = array_key_exists($cl_date_str,$cl_array) && $cl_status_array[$cl_array[$cl_date_str]['type']]['is_show'] == 1 && $cl_array[$cl_date_str]['is_show'] == 1 && $cl_status_array[$cl_array[$cl_date_str]['type']]['front_desk_show'] == 1 ? ' bgcolor="'.$cl_status_array[$cl_array[$cl_date_str]['type']]['color'].'"' : ' bgcolor="'.$default_bgcolor.'"';
 
      $cl_day = $day2 <= 9 ? '0'.$day2 : $day2; 

      $temp_num_week = ceil($day2/7);
    
      $temp_year_month_day = ($m_num2 < 10 ? '0'.$m_num2 : $m_num2).($day2 < 10 ? '0'.$day2 : $day2);
 
      //状态重复设置，冲突时，以状态排序最小的一个为准 
      $date_time_array = array();
      $date_time_array = array('month'=>$repeat_array[$cl_month_day_array[$cl_day]]['sort'],
                               'week'=>$repeat_array[$cl_week_array[$wday2]]['sort'],                     
                               'month_week'=>$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'],
                               'year'=>$repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']
                              );
      arsort($date_time_array);
      $date_time_array = array_filter($date_time_array);
      $first_value_array = array_slice($date_time_array,0,1);
      $first_value_type = array_keys($first_value_array);
      if($first_value_array[$first_value_type[0]] != ''){
        
        switch($first_value_type[0]){

          case 'month':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_month_day_array[$cl_day]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_month_day_array[$cl_day]]['sort'] != '') ||($repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1)){
              $cl_bgcolor = array_key_exists($cl_day,$cl_month_day_array) && $cl_status_array[$repeat_array[$cl_month_day_array[$cl_day]]['type']]['is_show'] == 1 && $repeat_array[$cl_month_day_array[$cl_day]]['is_show'] == 1 && $cl_status_array[$repeat_array[$cl_month_day_array[$cl_day]]['type']]['front_desk_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_month_day_array[$cl_day]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"';
              if($repeat_array[$cl_month_day_array[$cl_day]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            } 
            break;
          case 'week':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_week_array[$wday2]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_week_array[$wday2]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_week_array[$wday2]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_week_array[$wday2]]['sort'] != '') || ($repeat_array[$cl_week_array[$wday2]]['sort'] == -1)){
              $cl_bgcolor = array_key_exists($wday2,$cl_week_array) && $cl_status_array[$repeat_array[$cl_week_array[$wday2]]['type']]['is_show'] == 1 && $repeat_array[$cl_week_array[$wday2]]['is_show'] == 1 && $cl_status_array[$repeat_array[$cl_week_array[$wday2]]['type']]['front_desk_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_week_array[$wday2]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"'; 
              if($repeat_array[$cl_week_array[$wday2]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"'; 
              }
            }
            break;
          case 'month_week': 
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1 ? true : false; 
            if(($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort']) || ($cl_array[$cl_date_str]['sort'] == '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] != '') || ($repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1)){ 
              $temp_week_array = array_slice($cl_month_week_array,0,1);
              $cl_bgcolor = is_array($cl_month_week_array[$temp_num_week]) && array_key_exists($wday2,$temp_week_array[0]) && $cl_status_array[$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['type']]['is_show'] == 1 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['is_show'] == 1 && $cl_status_array[$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['type']]['front_desk_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['type']]['color'].'"' : ' bgcolor="#FFFFFF"';

              if($repeat_array[$cl_month_week_array[$temp_num_week][$wday2]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            }
            break;
          case 'year':
            $cl_date_temp = isset($cl_array[$cl_date_str]['type']) && $cl_array[$cl_date_str]['type'] == 0 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1 ? true : false;
            if(!isset($cl_array[$cl_date_str]['sort']) || ($cl_array[$cl_date_str]['sort'] != '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '' && $cl_array[$cl_date_str]['sort'] < $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']) || ($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1)){ 
              $cl_bgcolor = array_key_exists($temp_year_month_day,$cl_year_month_array) && $cl_status_array[$repeat_array[$cl_year_month_array[$temp_year_month_day]]['type']]['is_show'] == 1 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['is_show'] == 1 && $cl_status_array[$repeat_array[$cl_year_month_array[$temp_year_month_day]]['type']]['front_desk_show'] == 1 && $cl_date_temp == false ? ' bgcolor="'.$cl_status_array[$repeat_array[$cl_year_month_array[$temp_year_month_day]]['type']]['color'].'"' : $cl_bgcolor;

              if($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1){

                $cl_bgcolor = ' bgcolor="#FFFFFF"';
              }
            }
            break;
        }
      }
      echo "<td align='center'".$cl_bgcolor.">".(date("Ymd") == $cl_date_str ? '<div class="date_today">' : '')."<font size=2>$day2</font>".(date("Ymd") == $cl_date_str ? '</div>' : '')."</td>\n";
      // 换行
      if($wday2 == 6 && checkdate($m_num2,$day2+1,$year2)) echo "</tr><tr>";
      $day2++;
      $wday2++;
      $wday2 = $wday2 % 7;
    }
    if($wday2 > 0){
      while($wday2 < 7) { // Blank
      echo "<td align=center bgcolor='#ffffff'>　</td>\n";
      $wday2++;
      }
    } 
    echo '</tr></table></td></tr>' . "\n"; 
    }
?>
  </tr>
  </table> 
</td>
</tr> 
<tr>
<td class="main" align="left"><br><?php echo $status_str;?> </td>
</tr>
</table>
</div>
