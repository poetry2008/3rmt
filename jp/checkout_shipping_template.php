<?php page_head();?>
<script language="javascript">
var shipping_torihiki;
var torihiki_radio_div;
function format_time(str){
  if(str >=10){
    return str+"";
  }else{
    return "0"+str;
  }
}
//下面的方法 需要添加 参数 才能适用于多个配送
function show_address_book(pid){
  //显示 地址本
  address_list = window.document.getElementById('address_book_list'+pid);
  address_list.style.display = 'block';
}
function show_shipping_method(pid){
  shipping_list = window.document.getElementById('shipping_list'+pid);
  shipping_list.style.display = 'block';
}
function create_address_book(){
  //跳转到 创建地址页

}
function show_torihiki_time(_this,radio_name,pid){
  shipping_torihiki = window.document.getElementById('shipping_torihiki'+pid);
  if(_this.value != ''){
  shipping_torihiki.style.display = 'block';
  //通过隐藏 获得 工作时间 和 开始时间
  var work_time = window.document.getElementById('shipping_work_time'+pid).value;
  var start_time = window.document.getElementById('shipping_start_time'+pid).value;
  work_time_arr = work_time.split('-');
  work_start = work_time_arr[0];
  work_end = work_time_arr[1];
  //工作 开始时间
  work_start_arr = work_start.split(':');
  work_start_hour = work_start_arr[0];
  work_start_mim = work_start_arr[1];
  //工作 结束时间
  work_end_arr = work_end.split(':');
  work_end_hour = work_end_arr[0];
  work_end_mim = work_end_arr[1];
  //通过 时间戳 获得可以配送的开始时间
  work_datetime = new Date(parseInt(start_time) * 1000);
  start_hour = work_datetime.getHours();
  start_mim = work_datetime.getMinutes();
  //获得 现在的时间
  now_datetime = new Date();
  now_hour = now_datetime.getHours();
  now_mim = now_datetime.getMinutes();

  select_torihiki_date = _this.value;
  select_torihiki_date_arr = select_torihiki_date.split('-');
  select_year = parseInt(select_torihiki_date_arr[0]);
  select_mon = parseInt(select_torihiki_date_arr[1]);
  select_day = parseInt(select_torihiki_date_arr[2]);
  now_year = parseInt(now_datetime.getFullYear());
  now_mon = parseInt(now_datetime.getMonth())+1;
  now_day = parseInt(now_datetime.getDate());
  // date_flag 选择时间  大于 现在时间是1 等于是2 其他是0
  date_flag=0;
  if(select_year > now_year){
    date_flag=1;
  }else if(select_year == now_year){
    if(select_mon > now_mon){
      date_flag=1;
    }else if(select_mon == now_mon){
      if(select_day > now_day){
        date_flag=1;
      }else if(select_day == now_day){
        date_flag=2;
      }else{
      }
    }else{
    }
  }else{
  }

  if(date_flag == 1){
    s_hour = work_start_hour;
    s_mim = work_start_mim;
  }else if(date_flag == 2){
  //通过 配送的开始时间 和现在时间 确定 显示时间的开始
    //根据结束时间判断
  sub_time = start_mim - work_end_mim;
  if(start_hour > work_end_hour){
    s_hour = work_start_hour;
    s_mim = work_start_mim;
  }else if(start_hour == work_end_hour){
    if(sub_time > 15){
      s_hour = start_hour;
      s_mim = sub_time+(15-sub_time%15);
    }else{
      s_hour = start_hour;
      s_mim = start_mim;
    }
  }else{
    if(sub_time+(15-sub_time%15)==60){
      s_hour = now_hour+1;
      s_mim = 0;
    }else{
      s_hour = now_hour;
      s_mim = sub_time+(15-sub_time%15);
    }
  }

  //根据开始时间判断
  sub_time = start_mim - now_mim;
  if(start_hour > now_hour){
    s_hour = start_hour;
    s_mim = start_mim;
  }else if(start_hour == now_hour){
    if(sub_time > 15){
      s_hour = start_hour;
      s_mim = sub_time+(15-sub_time%15);
    }else{
      s_hour = start_hour;
      s_mim = start_mim;
    }
  }else{
    if(sub_time+(15-sub_time%15)==60){
      s_hour = now_hour+1;
      s_mim = 0;
    }else{
      s_hour = now_hour;
      s_mim = sub_time+(15-sub_time%15);
    }
  }
  }else {
    alert("<?php echo TEXT_SELECT_TORIHIKI_ERROR?>");
  }
  show_row = 0;
  torihiki_time_str = "<ul>";
  row_num = 0;
  for(s_hour;s_hour<=work_end_hour;s_hour++){
    if(s_hour == work_end_hour){
      end_mim = work_end_mim;
    }else{
      end_mim = 60;
    }
    for(s_mim;s_mim<end_mim;){
      row_num++;
      if(show_row == 0){
        if(s_mim<15){
          s_mim = 15;
          torihiki_time_str += "<li></li>";
        }else if(s_mim<30){
          s_mim = 30;
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
        }else if(s_mim<45){
          s_mim = 45;
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
        }else if(s_mim>=45){
          s_mim = 0;
          break;
        }
      }
      s_start = s_mim;
      s_mim+=14;
      e_start = s_mim;
      torihiki_time_str += "<li>";
      torihiki_time_str += "<input type='radio' name='"+radio_name+"' value='";
      torihiki_time_str += s_hour+":"+format_time(s_start)+"-";
      torihiki_time_str += s_hour+":"+format_time(e_start)+"'";
      torihiki_time_str += " >&nbsp;&nbsp;";        
      torihiki_time_str += s_hour+"時"+format_time(s_start)+"分";
      torihiki_time_str += " ～ ";
      torihiki_time_str += s_hour+"時"+format_time(e_start)+"分";
      torihiki_time_str += "</li>";
      show_row ++;
      s_mim++;
      if(row_num%2==1){
        torihiki_time_str += "</ul><ul>";
      }
    }
    s_mim=0;
  }
  torihiki_time_str += "</ul>";
  }else{
    torihiki_time_str = '';
    shipping_torihiki.style.display = 'none';
  }
  torihiki_radio_div = window.document.getElementById('shipping_torihiki_radio'+pid);
  torihiki_radio_div.innerHTML = torihiki_time_str;
  
}
function set_torihiki_date(shipping_code,work_time,start_time,pid){
  //设置 可用 取引日期
  var show_select = window.document.getElementById('shipping_torihiki_date_select'+pid);
  var show_length = show_select.options.length;
  var from_select = window.document.getElementById(shipping_code+pid);
  show_select.options.length = 1;
  for(i=0;i<from_select.length;i++){
    show_select.options.add(new Option(from_select.options[i].text,from_select.options[i].value));
  }
  window.document.getElementById('shipping_work_time'+pid).value = work_time;
  window.document.getElementById('shipping_start_time'+pid).value = start_time;

  s_torihiki = window.document.getElementById('shipping_torihiki'+pid);
  s_torihiki.style.display = 'none';
  torihiki_info_list = window.document.getElementById('torihiki_info_list'+pid);
  torihiki_info_list.style.display = 'block';
}
</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
<tr> 
<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> 
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</td> 
<td valign="top" id="contents"><?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?> 
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 

<tr> 
<td>

<table border="0" width="97%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
<td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="25%">
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
</tr> 
</table>

</td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>  
<td align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
</tr> 
</table>
</td> 
</tr> 
<tr>
<td>
<?php  //这里写正式的内容?>
<?php
//是否使用 一个商品一个配送 $each_product_shipping 真的时候 一个商品使用一个配送
if($each_product_shipping){
  //这里是 多个配送
?>
<!-- shipping_start -->
    <div>
    <?php
    foreach($cart as $key => $val){
      if($key == 'contents'){
        foreach($val as $key2 => $val2){
          $cp_result = tep_get_product_by_id($key2, SITE_ID, $languages_id);
          $list_pid = $cp_result['products_id'];
          ?>
            <div>
            <div class="box_content">
            <div class="float_left"><?php
            echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .   
            $cp_result['products_id']) . '">'.tep_image(DIR_WS_IMAGES .'products/'. 
            $cp_result['products_image'], $cp_result['products_name'], 
            SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; 
          ?></div>
            <div class="shipping_frame"><?php
            echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .
            $cp_result['products_id']) . '"><b><u>' . $cp_result['products_name'] . '</u></b></a><br>';
          if($cp_result['products_cflag'] == 1){ 
            echo TEXT_CHARACTER . tep_draw_input_field('cname_' . $key2,'','id="cname_' . $key2 . '"');
          }
          ?></div></div>
<?php
//这里是 多个配送 的列表显示
  if(empty($c_address_book)){
    //这里写跳转到 address
  }else{
      ?>
        <div class="box_content">
            <div><input name='address_radio<?php echo "_".$list_pid;?>' type="radio" onClick="create_address_book()"><?php
            echo TEXT_CREATE_ADDRESS_BOOK;?></div>
            <div>
                <div><input name='address_radio<?php echo "_".$list_pid;?>' type="radio"
                onclick="show_address_book('<?php echo "_".$list_pid;?>')"><?php
                echo TEXT_USE_ADDRESS_BOOK;?></div>
                <div class="shipping_frame" style="display:none" id="address_book_list<?php echo "_".$list_pid;?>">
                <?php
                foreach($c_address_book as $book_row){
                  echo "<div>";
                  echo tep_draw_radio_field('shipping_address_'.$list_pid,
                      $book_row['value'],false,'
                      id="shipping_address_'.$list_pid.
                      '" onclick="show_shipping_method(\'_'.$list_pid.'\')" '
                        );
                  echo $book_row['text'];
                  echo "</div>";
                }
              ?>
                </div>
            </div>
        </div>
        <?php

    }
  //这里 输出 配送列表 
  ?>
    <div style='display:none' id='shipping_list_<?php echo $list_pid;?>'>
    <?php
  $shipping_method_count = count($shipping_modules->modules);
  //是否只有一个配送
  $one_shipping = false;
  foreach($shipping_modules->modules as $s_modules){
    //这里输出 每一个模块
    $s_option = $s_modules->get_torihiki_date_select();
    echo "<div >";
    echo "<div>";
    if($shipping_method_count == 1){
      echo tep_draw_radio_field('shipping_method_'.$list_pid,$s_modules->title,true,
          "onclick='set_torihiki_date(\"".$s_modules->code."\",\"".
          $s_modules->work_time."\",\"".$s_modules->start_time."\",\""."_".$list_pid."\")'");
      $one_shipping = true;
    }else{
      echo tep_draw_radio_field('shipping_method_'.$list_pid,$s_modules->title,false,
          "onclick='set_torihiki_date(\"".$s_modules->code."\",\"".
          $s_modules->work_time."\",\"".$s_modules->start_time."\",\""."_".$list_pid."\")'");
    }
    echo $s_modules->title."</div>";
    echo "<p>".$s_modules->description."</p>";
    if(!$one_shipping){
      echo "<div style='display:none'>";
      echo "<select id='".$s_modules->code."_".$list_pid."'>";
      echo $s_option;
      echo "</select>";
    }
    echo "</div>";
    echo "</div>";
  }
  ?>
    </div>
    <?php
    //这里是 区引时间相关的显示
    ?>
    <div style='display:none' id='torihiki_info_list_<?php echo $list_pid;?>' >
    <div class="box_content">
    <div class="frame_width"><?php echo TEXT_TORIHIKIHOUHOU;?></div>
    <div class="float_left"><?php echo tep_get_torihiki_select_by_products($product_ids,
        'torihikihouhou_'.$list_pid);?></div>
    </div>
    <div class="box_content">
    <div class="frame_width"><?php echo TEXT_TORIHIKIKIBOUBI;?></div>
    <div class="float_left"><select name="date<?php echo '_'.$list_pid;
    ?>" onChange="show_torihiki_time(this,'torihiki_time_radio<?php 
    echo "_".$list_pid;?>','<?php echo "_".$list_pid;?>')" 
    id='shipping_torihiki_date_select<?php echo "_".$list_pid;?>'>
    <option value=""><?php echo TEXT_TORIHIKIBOUBI_DEFAULT_SELECT;?></option>
    <?php echo $s_option;?>
    </select>
    </div>
    </div>
    <div class="box_content" style="display:none" id="shipping_torihiki<?php echo "_".$list_pid;?>">
    <div class="frame_width"><?php echo TEXT_TORIHIKIKIBOUJIKAN;?></div>
    <div class="all_torihiki_radio" id="shipping_torihiki_radio<?php echo "_".$list_pid;?>"></div>
    </div>
    <?php
    echo tep_draw_hidden_field('shipping_cost_'.$list_pid,
        $s_modules->calc_fee($list_id,$vale['qty'],$site_id));
    echo tep_draw_hidden_field('work_time_'.$list_pid,'','id="shipping_work_time'.'_'.$list_pid.'"');
    echo tep_draw_hidden_field('start_time_'.$list_pid,'','id="shipping_start_time'.'_'.$list_pid.'"');
    ?>
    </div>
    </div>
            <?php
        }
      }
    }
  ?>
    </div>
<!-- shipping_end -->
<?php
}else{
  //把原来的产品 游戏角色名称 功能导入 下面直接 些配送 和地址
  ?>
    <div>
    <?php
    foreach($cart as $key => $val){
      if($key == 'contents'){
        foreach($val as $key2 => $val2){
          $cp_result = tep_get_product_by_id($key2, SITE_ID, $languages_id);
          ?>
            <div>
            <div><?php
            echo '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .   
            $cp_result['products_id']) . '">'.tep_image(DIR_WS_IMAGES .'products/'. 
            $cp_result['products_image'], $cp_result['products_name'], 
            SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; 
          ?></div>
            <div><?php
            echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .
            $cp_result['products_id']) . '"><b><u>' . $cp_result['products_name'] . '</u></b></a><br>';
          if($cp_result['products_cflag'] == 1){ 
            echo TEXT_CHARACTER . tep_draw_input_field('cname_' . $key2,'','id="cname_' . $key2 . '"');
          }
          ?></div>
            </div>
            <?php
        }
      }
    }
  ?>
    </div>
    <?php
    if(empty($c_address_book)){
      //这里写跳转到 address

    }else{
      ?>
        <div>
        <div><input name='address_radio' type="radio" onClick="create_address_book()"><?php
        echo TEXT_CREATE_ADDRESS_BOOK;?></div>
        <div>
        <div><input name='address_radio' type="radio" onClick="show_address_book('')"><?php
        echo TEXT_USE_ADDRESS_BOOK;?></div>
        <div style="display:none" id="address_book_list">
        <?php
        foreach($c_address_book as $book_row){
          echo "<div>";
          echo tep_draw_radio_field('shipping_address',$book_row['value'],
              false,
              'id="shipping_address_'.$list_pid.
               '" onclick="show_shipping_method(\'\')" '
              );
          echo $book_row['text'];
          echo "</div>";
        }
      ?>
        </div>
        </div>
        </div>
        <?php

    }
  //这里 输出 配送列表 
  ?>
    <div style='display:none' id='shipping_list'>
    <?php
  $shipping_method_count = count($shipping_modules->modules);
  //是否只有一个配送
  $one_shipping = false;
  foreach($shipping_modules->modules as $s_modules){
    //这里输出 每一个模块
    $s_option = $s_modules->get_torihiki_date_select();
    echo "<div>";
    echo "<div>";
    if($shipping_method_count == 1){
      echo tep_draw_radio_field('shipping_method',$s_modules->title,true,
          "onclick='set_torihiki_date(\"".$s_modules->code."\",\"".
          $s_modules->work_time."\",\"".$s_modules->start_time."\",\"\")'");
      $one_shipping = true;
    }else{
      echo tep_draw_radio_field('shipping_method',$s_modules->title,false,
          "onclick='set_torihiki_date(\"".$s_modules->code."\",\"".
          $s_modules->work_time."\",\"".$s_modules->start_time."\",\"\")'");
    }
    echo $s_modules->title."</div>";
    echo "<p>".$s_modules->description."</p>";
    if(!$one_shipping){
      echo "<div style='display:none'>";
      echo "<select id='".$s_modules->code."'>";
      echo $s_option;
      echo "</select>";
    }
    echo "</div>";
    echo "</div>";
  }
  ?>
    </div>
    <?php
    //这里是 区引时间相关的显示
    ?>
    <div style='display:none' id='torihiki_info_list' >
    <div>
    <div><?php echo TEXT_TORIHIKIHOUHOU;?></div>
    <div><?php echo tep_get_torihiki_select_by_products($product_ids);?></div>
    </div>
    <div>
    <div><?php echo TEXT_TORIHIKIKIBOUBI;?></div>
    <div><select name="date" onChange="show_torihiki_time(this,'torihiki_time_radio','')" 
    id='shipping_torihiki_date_select'>
    <option value=""><?php echo TEXT_TORIHIKIBOUBI_DEFAULT_SELECT;?></option>
    <?php echo $s_option;?>
    </select>
    </div>
    </div>
    <div style="display:none" id="shipping_torihiki">
    <div><?php echo TEXT_TORIHIKIKIBOUJIKAN;?></div>
    <div id="shipping_torihiki_radio"></div>
    </div>
    <?php
    echo tep_draw_hidden_field('shipping_cost',
        $s_modules->calc_fee($pid_id_arr,$qty_arr,$site_id));
    echo tep_draw_hidden_field('work_time','','id="shipping_work_time"');
    echo tep_draw_hidden_field('start_time','','id="shipping_start_time"');
    ?>
    </div>
    <?php

}
echo tep_draw_hidden_field('each_product_shipping',$each_product_shipping);
?>

</td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
<td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
</tr> 
</table>
</td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
</table>      </form> 
</td>
<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</table> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
