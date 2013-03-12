<?php
/*
  $Id$

*/

/* -------------------------------------
    功能: 画图 
    参数: $names(array) 名
    参数: $values(array) 宽度值 
    参数: $bars(array) 柱的颜色 
    参数: $vals(array) 相应的信息 
    参数: $dvalues(int) 宽度值 
    参数: $bars(int) 柱的颜色 
    返回值: 生成画图的html(string) 
------------------------------------ */
  function html_graph($names, $values, $bars, $vals, $dvalues = 0, $dbars = 0) {
// set the error level on entry and exit so as not to interfear with anyone elses error checking.
    $er = error_reporting(1);

// set the values that the user didn't
    $vals = hv_graph_defaults($vals);
    $html_graph_string = start_graph($vals, $names);

    if ($vals['type'] == 0) {
      $html_graph_string .= horizontal_graph($names, $values, $bars, $vals);
    } elseif ($vals['type'] == 1) {
      $html_graph_string .= vertical_graph($names, $values, $bars, $vals);
    } elseif ($vals['type'] == 2) {
      $html_graph_string .= double_horizontal_graph($names, $values, $bars, $vals, $dvalues, $dbars);
    } elseif ($vals['type'] == 3) {
      $html_graph_string .= double_vertical_graph($names, $values, $bars, $vals, $dvalues, $dbars);
    }

    $html_graph_string .= end_graph();

// Set the error level back to where it was.
    error_reporting($er);  

    return $html_graph_string;
  }

/* -------------------------------------
    功能: 初始化画图的相关信息 
    参数: 无 
    返回值: 画图的相关信息(array) 
------------------------------------ */
  function html_graph_init() {
    $vals = array('vlabel'=>'',
                  'hlabel'=>'',
                  'type'=>'',
                  'cellpadding'=>'',
                  'cellspacing'=>'',
                  'border'=>'',
                  'width'=>'',
                  'background'=>'',
                  'vfcolor'=>'',
                  'hfcolor'=>'',
                  'vbgcolor'=>'',
                  'hbgcolor'=>'',
                  'vfstyle'=>'',
                  'hfstyle'=>'',
                  'noshowvals'=>'',
                  'scale'=>'',
                  'namebgcolor'=>'',
                  'valuebgcolor'=>'',
                  'namefcolor'=>'',
                  'valuefcolor'=>'',
                  'namefstyle'=>'',
                  'valuefstyle'=>'',
                  'doublefcolor'=>'');

    return($vals);
  }

/* -------------------------------------
    功能: 生成图表的头部和标签 
    参数: $vals(array) 标签的样式和值以及头部的相关信息 
    参数: $names(array) 名 
    返回值: 图表的头部和标签的html(string) 
------------------------------------ */
  function start_graph($vals, $names) {
    $start_graph_string = '<table cellpadding="' . $vals['cellpadding'] . '" cellspacing="' . $vals['cellspacing'] . '" border="' . $vals['border'] . '"';

    if ($vals['width'] != 0) $start_graph_string .= ' width="' . $vals['width'] . '"';
    if ($vals['background']) $start_graph_string .= ' background="' . $vals['background'] . '"';

    $start_graph_string .= '>' . "\n";

    if ( ($vals['vlabel']) || ($vals['hlabel']) ) {
      if ( ($vals['type'] == 0) || ($vals['type'] == 2) ) {
// horizontal chart
        $rowspan = sizeof($names) + 1; 
        $colspan = 3; 
      } elseif ( ($vals['type'] == 1) || ($vals['type'] == 3) ) {
// vertical chart
        $rowspan = 3;
        $colspan = sizeof($names) + 1; 
      }

      $start_graph_string .= '  <tr>' . "\n" .
                             '    <td align="center" valign="center"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $start_graph_string .= ' bgcolor="' . $vals['hbgcolor'] . '"';

      $start_graph_string .= ' colspan="' . $colspan . '"><font color="' . $vals['hfcolor'] . '" style="' . $vals['hfstyle'] . '"><b>' . $vals['hlabel'] . '</b></font></td>' . "\n" .
                             '  </tr>' . "\n" .
                             '  <tr>' . "\n" .
                             '    <td align="center" valign="center"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $start_graph_string .= ' bgcolor="' . $vals['vbgcolor'] . '"';

      $start_graph_string .=  ' rowspan="' . $rowspan . '"><font color="' . $vals['vfcolor'] . '" style="' . $vals['vfstyle'] . '"><b>' . $vals['vlabel'] . '</b></font></td>' . "\n" .
                              '  </tr>' . "\n";
    }

    return $start_graph_string;
  }

/* -------------------------------------
    功能: 生成图表的尾部 
    参数: 无 
    返回值: 图表的尾部的html(string) 
------------------------------------ */
  function end_graph() {
    return '</table>' . "\n";
  }

/* -------------------------------------
    功能: 设置图表的默认参数 
    参数: $vals(array) 相关信息 
    返回值: 图表的默认参数(array) 
------------------------------------ */
  function hv_graph_defaults($vals) {
    if (!$vals['vfcolor']) $vals['vfcolor'] = '#000000';
    if (!$vals['hfcolor']) $vals['hfcolor'] = '#000000';
    if (!$vals['vbgcolor']) $vals['vbgcolor'] = '#FFFFFF';
    if (!$vals['hbgcolor']) $vals['hbgcolor'] = '#FFFFFF';
    if (!$vals['cellpadding']) $vals['cellpadding'] = '0';
    if (!$vals['cellspacing']) $vals['cellspacing'] = '0';
    if (!$vals['border']) $vals['border'] = '0';
    if (!$vals['scale']) $vals['scale'] = '1';
    if (!$vals['namebgcolor']) $vals['namebgcolor'] = '#FFFFFF';
    if (!$vals['valuebgcolor']) $vals['valuebgcolor'] = '#FFFFFF';
    if (!$vals['namefcolor']) $vals['namefcolor'] = '#000000';
    if (!$vals['valuefcolor']) $vals['valuefcolor'] = '#000000';
    if (!$vals['doublefcolor']) $vals['doublefcolor'] = '#886666';

    return $vals;
  }

/* -------------------------------------
    功能: 画水平图表 
    参数: $names(array) 名 
    参数: $values(array) 宽度值 
    参数: $bars(array) 柱的颜色 
    参数: $vals(array) 相应的信息 
    返回值: 水平图表(string) 
------------------------------------ */
  function horizontal_graph($names, $values, $bars, $vals) {
    $horizontal_graph_string = '';
    for($i = 0, $n = sizeof($values); $i < $n; $i++) { 
      $horizontal_graph_string .= '  <tr>' . "\n" .
                                  '    <td align="right"';
// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $horizontal_graph_string .= ' bgcolor="' . $vals['namebgcolor'] . '"';

      $horizontal_graph_string .= '><font size="-1" color="' . $vals['namefcolor'] . '" style="' . $vals['namefstyle'] . '">' . $names[$i] . '</font></td>' . "\n" .
                                  '    <td'; 

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $horizontal_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $horizontal_graph_string .= '>';

// decide if the value in bar is a color code or image.
      if (ereg('^#', $bars[$i])) { 
        $horizontal_graph_string .= '<table cellpadding="0" cellspacing="0" bgcolor="' . $bars[$i] . '" width="' . ($values[$i] * $vals['scale']) . '">' . "\n" .
                                    '  <tr>' . "\n" .
                                    '    <td>&nbsp;</td>' . "\n" .
                                    '  </tr>' . "\n" .
                                    '</table>';
      } else {
        $horizontal_graph_string .= '<img src="' . $bars[$i] . '" height="10" width="' . ($values[$i] * $vals['scale']) . '">';
      }

      if (!$vals['noshowvals']) {
        $horizontal_graph_string .= '<i><font size="-2" color="' . $vals['valuefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $values[$i] . ')</font></i>';
      }

      $horizontal_graph_string .= '</td>' . "\n" .
                                  '  </tr>' . "\n";
    } // endfor

    return $horizontal_graph_string;
  }

/* -------------------------------------
    功能: 画垂直图表 
    参数: $names(array) 名 
    参数: $values(array) 标记 
    参数: $bars(array) 图片 
    参数: $vals(array) 相应的信息 
    返回值: 垂直图表(string) 
------------------------------------ */
  function vertical_graph($names, $values, $bars, $vals) {
    $vertical_graph_string = '  <tr>' . "\n";

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $vertical_graph_string .= '    <td align="center" valign="bottom"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $vertical_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $vertical_graph_string .= '>';

      if (!$vals['noshowvals']) {
        $vertical_graph_string .= '<i><font size="-2" color="' . $vals['valuefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $values[$i] . ')</font></i><br>';
      }

      $vertical_graph_string .= '<img src="' . $bars[$i] . '" width="5" height="';

// values of zero are displayed wrong because a image height of zero 
// gives a strange behavior in Netscape. For this reason the height 
// is set at 1 pixel if the value is zero. - Jan Diepens
      if ($values[$i] != 0) {
        $vertical_graph_string .= $values[$i] * $vals['scale'];
      } else {
        $vertical_graph_string .= '1';
      } 

      $vertical_graph_string .= '"></td>' . "\n";
    } // endfor

    $vertical_graph_string .= '  </tr>' . "\n" .
                              '  <tr>' . "\n";

    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $vertical_graph_string .= '    <td align="center" valign="top"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $vertical_graph_string .= ' bgcolor="' . $vals['namebgcolor'] . '"';

      $vertical_graph_string .= '><font size="-1" color="' . $vals['namefcolor'] . '" style="' . $vals['namefstyle'] . '">' . $names[$i] . '</font></td>' . "\n";
    } // endfor

    $vertical_graph_string .= '  </tr>' . "\n";

    return $vertical_graph_string;
  }

/* -------------------------------------
    功能: 画双水平图表 
    参数: $names(array) 名 
    参数: $values(array) 标记 
    参数: $bars(array) 图片/背景色 
    参数: $vals(array) 相应的信息 
    参数: $dvalues(array) 宽度值 
    参数: $dbars(array) 背景色 
    返回值: 双水平图表(string) 
------------------------------------ */
  function double_horizontal_graph($names, $values, $bars, $vals, $dvalues, $dbars) {
    $double_horizontal_graph_string = '';
    for($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $double_horizontal_graph_string .= '  <tr>' . "\n" .
                                        '    <td align="right"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_horizontal_graph_string .= ' bgcolor="' . $vals['namebgcolor'] . '"';

      $double_horizontal_graph_string .= '><font size="-1" color="' . $vals['namefcolor'] . '" style="' . $vals['namefstyle'] . '">' . $names[$i] . '</font></td>' . "\n" .
                                         '    <td';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_horizontal_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $double_horizontal_graph_string .= '><table align="left" cellpadding="0" cellspacing="0" width="' . ($dvalues[$i] * $vals['scale']) . '">' . "\n" .
                                         '      <tr>' . "\n" .
                                         '        <td';

// set background to a color if it starts with # or an image otherwise.
      if (ereg('^#', $dbars[$i])) {
        $double_horizontal_graph_string .= ' bgcolor="' . $dbars[$i] . '">';
      } else {
        $double_horizontal_graph_string .= ' background="' . $dbars[$i] . '">';
      }

      $double_horizontal_graph_string .= '<nowrap>';

// decide if the value in bar is a color code or image.
      if (ereg('^#', $bars[$i])) { 
        $double_horizontal_graph_string .= '<table align="left" cellpadding="0" cellspacing="0" bgcolor="' . $bars[$i] . '" width="' . ($values[$i] * $vals['scale']) . '">' . "\n" .
                                           '  <tr>' . "\n" .
                                           '    <td>&nbsp;</td>' . "\n" .
                                           '  </tr>' . "\n" .
                                           '</table>';
      } else {
        $double_horizontal_graph_string .= '<img src="' . $bars[$i] . '" height="10" width="' . ($values[$i] * $vals['scale']) . '">';
      }          

      if (!$vals['noshowvals']) {
        $double_horizontal_graph_string .= '<i><font size="-3" color="' . $vals['valuefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $values[$i] . ')</font></i>';
      }

      $double_horizontal_graph_string .= '</nowrap></td>' . "\n" .
                                         '        </tr>' . "\n" .
                                         '      </table>';

      if (!$vals['noshowvals']) {
        $double_horizontal_graph_string .= '<i><font size="-3" color="' . $vals['doublefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $dvalues[$i] . ')</font></i>';
      }

      $double_horizontal_graph_string .= '</td>' . "\n" .
                                         '  </tr>' . "\n";
    } // endfor

    return $double_horizontal_graph_string;
  }

/* -------------------------------------
    功能: 画双垂直图表 
    参数: $names(array) 名 
    参数: $values(array) 标记 
    参数: $bars(array) 图片
    参数: $vals(array) 相应的信息 
    参数: $dvalues(array) 标记 
    参数: $dbars(array) 图片 
    返回值: 双垂直图表(string) 
------------------------------------ */
  function double_vertical_graph($names, $values, $bars, $vals, $dvalues, $dbars) {
    $double_vertical_graph_string = '  <tr>' . "\n";
    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $double_vertical_graph_string .= '    <td align="center" valign="bottom"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_vertical_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $double_vertical_graph_string .= '><table>' . "\n" .
                                       '      <tr>' . "\n" .
                                       '        <td align="center" valign="bottom"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_vertical_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $double_vertical_graph_string .= '>';

      if (!$vals['noshowvals'] && $values[$i]) {
        $double_vertical_graph_string .= '<i><font size="-2" color="' . $vals['valuefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $values[$i] . ')</font></i><br>';
      }

      $double_vertical_graph_string .= '<img src="' . $bars[$i] . '" width="9" height="';

      if ($values[$i] != 0) {
        $double_vertical_graph_string .= $values[$i] * $vals['scale'];
      } else {
        $double_vertical_graph_string .= '1';
      }

      $double_vertical_graph_string .= '"></td>' . "\n" .
                                       '        <td align="center" valign="bottom"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_vertical_graph_string .= ' bgcolor="' . $vals['valuebgcolor'] . '"';

      $double_vertical_graph_string .= '>';

      if (!$vals['noshowvals'] && $dvalues[$i]) {
        $double_vertical_graph_string .= '<i><font size="-2" color="' . $vals['doublefcolor'] . '" style="' . $vals['valuefstyle'] . '">(' . $dvalues[$i] . ')</font></i><br>';
      }

      $double_vertical_graph_string .= '<img src="' . $dbars[$i] . '" width="9" height="';

      if ($dvalues[$i] != 0) {
        $double_vertical_graph_string .= $dvalues[$i] * $vals['scale'];
      } else {
        $double_vertical_graph_string .= '1';
      }

      $double_vertical_graph_string .= '"></td>' . "\n" .  '      </tr>' . "\n" ;
      $double_vertical_graph_string .= '    <td align="center" valign="top"';

// if a background was choosen don't print cell BGCOLOR
      if (!$vals['background']) $double_vertical_graph_string .= ' bgcolor="' . $vals['namebgcolor'] . '"';
      $double_vertical_graph_string .= '><font size="-1" color="' . $vals['namefcolor'] . '" style="' . $vals['namefstyle'] . '">' . $names[$i] . '</font></td>' . "\n";

      $double_vertical_graph_string .= ' </table></td>' . "\n";
    if($i == 15){
    $double_vertical_graph_string .= '  </tr>' . "\n" ;
    $double_vertical_graph_string .= '  <tr>' . "\n" ;
    }
    } // endfor
    
    $double_vertical_graph_string .= '  </tr>' . "\n" .  '  <tr>' . "\n";

    $double_vertical_graph_string .= '  </tr>' . "\n";

    return $double_vertical_graph_string;
  }

/* -------------------------------------
    功能: 画双垂直柱状图表 
    参数: $banner_id(int) bannerID 
    参数: $days(string) 指定时间 
    返回值: 双垂直柱状图表(string) 
------------------------------------ */
  function tep_banner_graph_infoBox($banner_id, $days) {
    $values = $names = $dvalues = array();
    $banner_stats_query = tep_db_query("select dayofmonth(banners_history_date) as name, banners_shown as value, banners_clicked as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and to_days(now()) - to_days(banners_history_date) < " . $days . " order by banners_history_date");
    while ($banner_stats = tep_db_fetch_array($banner_stats_query)) {
      $names[] = $banner_stats['name'];
      $values[] = $banner_stats['value'];
      $dvalues[] = $banner_stats['dvalue'];
    }
    $largest = @max($values);

    $bars = array();
    $dbars = array();
    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $bars[$i] = DIR_WS_IMAGES . 'graph_hbar_blue.gif';
      $dbars[$i] = DIR_WS_IMAGES . 'graph_hbar_red.gif';
    }

    $graph_vals = @array('vlabel'=>TEXT_BANNERS_DATA,
                        'hlabel'=>TEXT_BANNERS_LAST_3_DAYS,
                        'type'=>'3',
                        'cellpadding'=>'',
                        'cellspacing'=>'1',
                        'border'=>'',
                        'width'=>'',
                        'vfcolor'=>'#ffffff',
                        'hfcolor'=>'#ffffff',
                        'vbgcolor'=>'#81a2b6',
                        'hbgcolor'=>'#81a2b6',
                        'vfstyle'=>'Verdana, Arial, Helvetica',
                        'hfstyle'=>'Verdana, Arial, Helvetica',
                        'scale'=>100/$largest,
                        'namebgcolor'=>'#f3f5fe',
                        'valuebgcolor'=>'#f3f5fe',
                        'namefcolor'=>'',
                        'valuefcolor'=>'#0000d0',
                        'namefstyle'=>'Verdana, Arial, Helvetica',
                        'valuefstyle'=>'',
                        'doublefcolor'=>'#ff7339');

    return html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars);
  }

/* -------------------------------------
    功能: 画双垂直柱状年度图表 
    参数: $banner_id(int) bannerID 
    返回值: 双垂直柱状图表(string) 
------------------------------------ */
  function tep_banner_graph_yearly($banner_id) {
    global $banner, $_GET;

    $banner_stats_query = tep_db_query("select year(banners_history_date) as year, sum(banners_shown) as value, sum(banners_clicked) as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' group by year(banners_history_date)");
    while ($banner_stats = tep_db_fetch_array($banner_stats_query)) {
      $names[] = $banner_stats['year'];
      $values[] = (($banner_stats['value']) ? $banner_stats['value'] : '0');
      $dvalues[] = (($banner_stats['dvalue']) ? $banner_stats['dvalue'] : '0');
    }

    $largest = @max($values);

    $bars = array();
    $dbars = array();
    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $bars[$i] = DIR_WS_IMAGES . 'graph_hbar_blue.gif';
      $dbars[$i] = DIR_WS_IMAGES . 'graph_hbar_red.gif';
    }

    $graph_vals = @array('vlabel'=>TEXT_BANNERS_DATA,
                        'hlabel'=>sprintf(TEXT_BANNERS_YEARLY_STATISTICS, $banner['banners_title']),
                        'type'=>'3',
                        'cellpadding'=>'',
                        'cellspacing'=>'1',
                        'border'=>'',
                        'width'=>'',
                        'vfcolor'=>'#ffffff',
                        'hfcolor'=>'#ffffff',
                        'vbgcolor'=>'#81a2b6',
                        'hbgcolor'=>'#81a2b6',
                        'vfstyle'=>'Verdana, Arial, Helvetica',
                        'hfstyle'=>'Verdana, Arial, Helvetica',
                        'scale'=>100/$largest,
                        'namebgcolor'=>'#f3f5fe',
                        'valuebgcolor'=>'#f3f5fe',
                        'namefcolor'=>'',
                        'valuefcolor'=>'#0000d0',
                        'namefstyle'=>'Verdana, Arial, Helvetica',
                        'valuefstyle'=>'',
                        'doublefcolor'=>'#ff7339');

    return html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars);
  }

/* -------------------------------------
    功能: 画双垂直柱状月度图表 
    参数: $banner_id(int) bannerID 
    返回值: 双垂直柱状图表(string) 
------------------------------------ */
  function tep_banner_graph_monthly($banner_id) {
    global $banner, $_GET;

    $year = (($_GET['year']) ? $_GET['year'] : date('Y'));

    for ($i=1; $i<13; $i++) {
      $names[] = strftime('%b', mktime(0,0,0,$i));
      $values[] = '0';
      $dvalues[] = '0';
    }

    $banner_stats_query = tep_db_query("select month(banners_history_date) as banner_month, sum(banners_shown) as value, sum(banners_clicked) as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and year(banners_history_date) = '" . $year . "' group by month(banners_history_date)");
    while ($banner_stats = tep_db_fetch_array($banner_stats_query)) {
      $names[($banner_stats['banner_month']-1)] = strftime('%b', mktime(0,0,0,$banner_stats['banner_month']));
      $values[($banner_stats['banner_month']-1)] = (($banner_stats['value']) ? $banner_stats['value'] : '0');
      $dvalues[($banner_stats['banner_month']-1)] = (($banner_stats['dvalue']) ? $banner_stats['dvalue'] : '0');
    }

    $largest = @max($values);

    $bars = array();
    $dbars = array();
    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $bars[$i] = DIR_WS_IMAGES . 'graph_hbar_blue.gif';
      $dbars[$i] = DIR_WS_IMAGES . 'graph_hbar_red.gif';
    }

    $graph_vals = @array('vlabel'=>TEXT_BANNERS_DATA,
                        'hlabel'=>sprintf(TEXT_BANNERS_MONTHLY_STATISTICS, $banner['banners_title'], date('Y')),
                        'type'=>'3',
                        'cellpadding'=>'',
                        'cellspacing'=>'1',
                        'border'=>'',
                        'width'=>'',
                        'vfcolor'=>'#ffffff',
                        'hfcolor'=>'#ffffff',
                        'vbgcolor'=>'#81a2b6',
                        'hbgcolor'=>'#81a2b6',
                        'vfstyle'=>'Verdana, Arial, Helvetica',
                        'hfstyle'=>'Verdana, Arial, Helvetica',
                        'scale'=>100/$largest,
                        'namebgcolor'=>'#f3f5fe',
                        'valuebgcolor'=>'#f3f5fe',
                        'namefcolor'=>'',
                        'valuefcolor'=>'#0000d0',
                        'namefstyle'=>'Verdana, Arial, Helvetica',
                        'valuefstyle'=>'',
                        'doublefcolor'=>'#ff7339');

    return html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars);
  }

/* -------------------------------------
    功能: 画双垂直柱状日度图表 
    参数: $banner_id(int) bannerID 
    返回值: 双垂直柱状图表(string) 
------------------------------------ */
  function tep_banner_graph_daily($banner_id) {
    global $banner, $_GET;

    $year = (($_GET['year']) ? $_GET['year'] : date('Y'));
    $month = (($_GET['month']) ? $_GET['month'] : date('n'));

    $days = (date('t', mktime(0,0,0,$month))+1);
    $stats = array();
    for ($i=1; $i<$days; $i++) {
      $names[] = $i;
      $values[] = '0';
      $dvalues[] = '0';
    }

    $banner_stats_query = tep_db_query("select dayofmonth(banners_history_date) as banner_day, banners_shown as value, banners_clicked as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and month(banners_history_date) = '" . $month . "' and year(banners_history_date) = '" . $year . "'");
    while ($banner_stats = tep_db_fetch_array($banner_stats_query)) {
      $names[($banner_stats['banner_day']-1)] = $banner_stats['banner_day'];
      $values[($banner_stats['banner_day']-1)] = (($banner_stats['value']) ? $banner_stats['value'] : '0');
      $dvalues[($banner_stats['banner_day']-1)] = (($banner_stats['dvalue']) ? $banner_stats['dvalue'] : '0');
    }

    $largest = @max($values);

    $bars = array();
    $dbars = array();
    for ($i = 0, $n = sizeof($values); $i < $n; $i++) {
      $bars[$i] = DIR_WS_IMAGES . 'graph_hbar_blue.gif';
      $dbars[$i] = DIR_WS_IMAGES . 'graph_hbar_red.gif';
    }

    $graph_vals = @array('vlabel'=>TEXT_BANNERS_DATA,
                        'hlabel'=>sprintf(TEXT_BANNERS_DAILY_STATISTICS, $banner['banners_title'], strftime('%B', mktime(0,0,0,$month)), $year),
                        'type'=>'3',
                        'cellpadding'=>'',
                        'cellspacing'=>'1',
                        'border'=>'',
                        'width'=>'',
                        'vfcolor'=>'#ffffff',
                        'hfcolor'=>'#ffffff',
                        'vbgcolor'=>'#81a2b6',
                        'hbgcolor'=>'#81a2b6',
                        'vfstyle'=>'Verdana, Arial, Helvetica',
                        'hfstyle'=>'Verdana, Arial, Helvetica',
                        'scale'=>100/$largest,
                        'namebgcolor'=>'#f3f5fe',
                        'valuebgcolor'=>'#f3f5fe',
                        'namefcolor'=>'',
                        'valuefcolor'=>'#0000d0',
                        'namefstyle'=>'Verdana, Arial, Helvetica',
                        'valuefstyle'=>'',
                        'doublefcolor'=>'#ff7339');

    return html_graph($names, $values, $bars, $graph_vals, $dvalues, $dbars);
  }
?>
