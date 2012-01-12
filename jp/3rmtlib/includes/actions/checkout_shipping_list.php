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
            <div class="box_radio"><input name='address_radio<?php echo "_".$list_pid;?>' type="radio" onClick="create_address_book()"><?php
            echo TEXT_CREATE_ADDRESS_BOOK;?></div>
            <div class="box_radio">
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
    <div class="box_radio_text" style='display:none' id='shipping_list_<?php echo $list_pid;?>'>
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
    <div class="all_torihiki_radio" id="shipping_torihiki_radio<?php echo "_".$list_pid;?>">
    <div class="all_hour_list" id="shipping_torihiki_radio_hour<?php echo "_".$list_pid;?>"></div>
    <div class="all_time_list" id="shipping_torihiki_radio_time<?php echo "_".$list_pid;?>"></div>
    </div>
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
        <div class="box_content">
        <div class="box_radio"><input name='address_radio' type="radio" onClick="create_address_book()"><?php
        echo TEXT_CREATE_ADDRESS_BOOK;?></div>
        <div class="box_radio">
        <div><input name='address_radio' type="radio" onClick="show_address_book('')"><?php
        echo TEXT_USE_ADDRESS_BOOK;?></div>
        <div class="shipping_frame" style="display:none" id="address_book_list">
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
    <div class="box_radio_text" style='display:none' id='shipping_list'>
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
    <div class="box_content">
    <div class="frame_width"><?php echo TEXT_TORIHIKIHOUHOU;?></div>
    <div class="float_left"><?php echo tep_get_torihiki_select_by_products($product_ids);?></div>
    </div>
    <div class="box_content">
    <div class="frame_width"><?php echo TEXT_TORIHIKIKIBOUBI;?></div>
    <div class="float_left"><select name="date" onChange="show_torihiki_time(this,'torihiki_time_radio','')" 
    id='shipping_torihiki_date_select'>
    <option value=""><?php echo TEXT_TORIHIKIBOUBI_DEFAULT_SELECT;?></option>
    <?php echo $s_option;?>
    </select>
    </div>
    </div>
    <div class="box_content" style="display:none" id="shipping_torihiki">
    <div class="frame_width"><?php echo TEXT_TORIHIKIKIBOUJIKAN;?></div>
    <div class="all_torihiki_radio" id="shipping_torihiki_radio">
    <div class="all_hour_list" id="shipping_torihiki_radio_hour"></div>
    <div class="all_time_list" id="shipping_torihiki_radio_time"></div>
    </div>
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

