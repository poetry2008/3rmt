<?php require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_OPTION);?>
var box_warp_height = 0;
var origin_offset_symbol = 0;
window.onresize = resize_option_page;
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
  box_warp_height = $(".box_warp").height(); 
}
<?php //全选?>
function all_select_option(option_symbol_str)
{
  var check_flag = document.del_option.all_check.checked;
  if (document.del_option.elements[option_symbol_str]) {
    if (document.del_option.elements[option_symbol_str].length == null) {
      if (check_flag == true) {
        document.del_option.elements[option_symbol_str].checked = true;
      } else {
        document.del_option.elements[option_symbol_str].checked = false;
      }
    } else {
      for (i = 0; i < document.del_option.elements[option_symbol_str].length; i++) {
        if (check_flag == true) {
          document.del_option.elements[option_symbol_str][i].checked = true;
        } else {
          document.del_option.elements[option_symbol_str][i].checked = false;
        }
      }
    }
  }
}
<?php //删除选中项?>
function delete_select_option(option_symbol_str)
{
  sel_num = 0;
  if (document.del_option.elements[option_symbol_str].length == null) {
    if (document.del_option.elements[option_symbol_str].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.del_option.elements[option_symbol_str].length; i++) {
      if (document.del_option.elements[option_symbol_str][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    if (confirm('<?php echo TEXT_DEL_OPTION;?>')) {
      document.forms.del_option.submit(); 
    }
  } else {
    alert('<?php echo TEXT_OPTION_MUST_SELECT;?>'); 
  }
}
<?php //新建组?>
function create_option_group()
{
  $.ajax({
    url: 'ajax.php?action=new_group',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
    }
  });
}
<?php //检测填写组的信息是否为空?>
function check_group_info(gid, type)
{
  var group_name = document.getElementById('name').value; 
  var group_title = document.getElementById('title').value; 
  $.ajax({
    url: 'ajax_orders.php?action=check_group',
    type: 'POST',
    dataType: 'text',
    data:'type='+type+'&gname='+group_name+'&gid='+gid+'&gtitle='+group_title, 
    async:false,
    success: function (data){
      var error_arr = data.split('||'); 
      $('#name_error').html(error_arr[0]); 
      $('#title_error').html(error_arr[1]); 
      if (data == '||') {
        document.forms.option_group.submit(); 
      }
    }
  });
}
<?php //关闭弹出框?>
function close_option_info()
{
  $('#show_popup_info').html(''); 
  $('#show_popup_info').css('display', 'none'); 
  $(".box_warp").height(box_warp_height); 
}
<?php //编辑组的信息?>
function show_group_info(ele, gid, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  $.ajax({
    url: 'ajax.php?action=edit_group',      
    data: 'group_id='+gid+'&'+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#group_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#group_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
    }
  });
  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}
<?php //显示组的信息?>
function show_link_group_info(gid, k_str)
{
  k_str = decodeURIComponent(k_str);
  $.ajax({
    url: 'ajax.php?action=edit_group',
    data:'group_id='+gid+'&'+k_str, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').show(); 
    }
  });
}
<?php //自动搜索?>
$(function() {
      box_warp_height = $(".box_warp").height(); 
      function format(group) {
          return group.name;
      }
      $("#keyword").autocomplete('ajax_orders.php?action=search_group', {
        multipleSeparator: '',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format(item);
        }
      }).result(function(e, item) {
      });
});
<?php //快捷键监听?>
$(document).ready(function() {
  $(document).keyup(function(event) {
    if (event.which == 27) {
      if ($("#show_popup_info").css("display") != "none") {
        close_option_info();     
      }
    }
    if (event.which == 13) {
      if ($("#show_popup_info").css("display") != "none") {
        $("#button_save").trigger("click");  
      }
    }
    
    if (event.ctrlKey && event.which == 37) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      }
    }
    
    if (event.ctrlKey && event.which == 39) {
      if ($("#show_popup_info").css("display") != "none") {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      }
    }
  });    
});

<?php //创建option元素?>
function create_option_item(gid, i_param_str)
{
  i_param_str = decodeURIComponent(i_param_str);
  $.ajax({
    url: 'ajax.php?action=new_item',
    data:'group_id='+gid+'&'+i_param_str, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
    }
  });
}

<?php //搜索item?>
function search_item_title(t_obj, t_type, s_item_id)
{
  var search_title = document.getElementById('title').value;
  var del_tr_single = false; 
  while (true) {
    $('#front_title').parent().parent().prev().find('input').each(function(){
      if ($(this).attr('id') == 'title') {
        del_tr_single = true; 
      }
    }); 
    if (del_tr_single == true) {
      break; 
    } else {
      $('#front_title').parent().parent().prev().remove();
    }
  }
  if (search_title) {
    $.ajax({
      url: 'ajax_orders.php?action=search_item_title',     
      data: 'sea_title='+search_title+'&t_type='+t_type+'&s_item_id='+s_item_id, 
      type:'POST',
      dataType: 'text',
      async:false,
      success: function (data) {
        data_array = data.split('|||'); 
        $("#is_more").val(data_array[1]);
        $(t_obj).parent().parent().after(data_array[0]);
      }
    });
  }

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //查看item?>
function preview_item(preview_id, pt_type)
{
  $.ajax({
    url: 'ajax_orders.php?action=preview_title',     
    data: 'preview_id='+preview_id, 
    type:'POST',
    dataType: 'json',
    async:false,
    success: function (data) {
      if (data != '') {
        $('#front_title').val(data.front_title); 
        $('#se_item').html(data.type); 
        $('#price').val(data.price);
        $('#sort_num').val(data.sort_num);
        $('#p_type').html(data.place_type);
        $('#show_select').html(data.item_element);
        $('#is_copy').val('1');
      }
    }
  });

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //改变option类型?>
function change_option_item_type(item_id)
{
  var stype = document.getElementById('type').value;
  $.ajax({
     url: 'ajax_orders.php?action=change_item',   
     data: 'item_id='+item_id+'&stype='+stype, 
     type: 'POST',
     async: false,
     success: function(msg) {
       $('#show_select').html(msg); 
       if (stype == 'Radio') {
         $('#price').parent().parent().css('display', 'none'); 
       } else {
         $('#price').parent().parent().css('display', ''); 
       }
     }
  });
  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //删除选中的元素?>
function del_option_select(ele)
{
  $(ele).parent().parent().remove();    

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //添加元素?>
function add_option_select()
{
  var i_num = 1; 
  var html_str = '';

  $('#show_select').find('input[type=text]').each(function(i) {
    i_num = parseInt($(this).attr('name').substring('3')); 
  });   
  for (i=1; i<=5 ; i++) {
    i_num_add = i_num+i; 
    html_str += '<tr><td align="left"><?php echo TEXT_OPTION_ITEM_SELECT;?></td><td align="left"><input type="text" name="op_'+i_num_add+'" value="" class="option_text">&nbsp;<input type="button" onclick="del_option_select(this);" value="<?php echo IMAGE_DELETE;?>"></td></tr>';   
  }
  $('#add_select').parent().before(html_str);

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //添加元素?>
function add_option_radio()
{
  var i_num = 1; 
  var html_str = '';

  $('#show_select').find('input[type=text]').each(function(i) {
    if ($(this).attr('name').substring(0, '4') == 'rom_') {
      i_num = parseInt($(this).attr('name').substring('4')); 
    }
  });   
  
  for (i=1; i<=5 ; i++) {
    i_num_add = i_num+i; 
    html_str += '<tr><td align="left"><?php echo TEXT_OPTION_ITEM_SELECT;?></td><td align="left"><textarea cols="30" rows="5" class="option_text"name="ro_'+i_num_add+'"></textarea>&nbsp;<a href="javascript:void(0);"onclick="delete_radio(this, '+i_num_add+');"><input type="button" value="<?php echo IMAGE_DELETE;?>" class="element_button" onclick="redirect_new_url(this);"></a></td></tr>';   
    
    html_str += '<tr><td align="left"><?php echo TEXT_ITEM_PIC_NAME;?></td><td align="left"><input type="file" name="rop_'+i_num_add+'[]" value="">&nbsp;<a href="javascript:void(0);" onclick="delete_item_pic(this);"><input type="button" value="<?php echo TEXT_ITEM_DELETE_PIC;?>" class="element_button"></a><a href="javascript:void(0);" onclick="add_item_pic(this, '+i_num_add+');"><input type="button" value="<?php echo BUTTON_ADD_TEXT;?>" class="element_button"></a></td></tr>'; 
   
    html_str += '<tr height="40"><td valign="top" align="left"><?php echo TABLE_HEADING_OPTION_ITEM_PRICE;?></td><td valign="top" align="left"><input type="text" name="rom_'+i_num_add+'" value="" style="width:35%; text-align:right">&nbsp;<?php echo TEXT_MONEY_SYMBOL;?></td></tr>';
  }
  $('#add_radio').parent().before(html_str);

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //删除图片元素?>
function add_item_pic(i_obj, i_num)
{
  html_str = '<tr><td align="left"><?php echo TEXT_ITEM_PIC_NAME;?></td><td align="left"><input type="file" name="rop_'+i_num+'[]" value="">&nbsp;<a href="javascript:void(0);" onclick="delete_item_pic(this);"><input type="button" value="<?php echo TEXT_ITEM_DELETE_PIC;?>" class="element_button"></a><a href="javascript:void(0);" onclick="add_item_pic(this, '+i_num+');"><input type="button" value="<?php echo BUTTON_ADD_TEXT;?>" class="element_button"></a></td></tr>'; 
  
  $(i_obj).parent().parent().after(html_str); 

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //删除元素?>
function delete_radio(i_obj, i_num)
{
  $('#show_select').find('input[type=file]').each(function(i) {
    if ($(this).attr('name') == 'rop_'+i_num+'[]') {
      $(this).parent().parent().remove();
    }
  });
  
  $(i_obj).parent().parent().remove(); 
  $('#show_select').find('input[type=text]').each(function(i) {
    if ($(this).attr('name') == 'rom_'+i_num) {
      $(this).parent().parent().remove();
    }
  });

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //删除上传图片?>
function delete_item_pic(i_obj)
{
  $(i_obj).parent().parent().remove();

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}

<?php //检查item是否输入正确?>
function check_item_info()
{
  var item_title = document.getElementById('title').value; 
  var item_front_title = document.getElementById('front_title').value; 
  var r_str = '';
  var reg = /^ro_[0-9]+$/; 
  
  $('#show_select').find('textarea').each(function() {
     ro_name = $(this).attr('name');
     if (reg.exec(ro_name)) {
       if ($(this).val() != '') {
         r_str += $(this).val()+'<<<|||';    
       }
     }
  });  
  
  $.ajax({
      url: 'ajax_orders.php?action=check_item',
      type: 'POST',
      dataType: 'text',
      data:'ititle='+item_title+'&ifront_title='+item_front_title+'&r_str='+r_str,
      async:false,
      success: function (data) {
        var error_arr = data.split('||');
        $('#title_error').html(error_arr[0]);
        $('#front_error').html(error_arr[1]);
        $('#rname_error').html(error_arr[2]);
        
        if (data == '||||') {
          document.forms.option_item.submit(); 
        } 
      }
  });
}

<?php //编辑item信息?>
function show_item_info(ele, item_id, i_param_str)
{
  ele = ele.parentNode;
  i_param_str = decodeURIComponent(i_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_item',      
    data: 'item_id='+item_id+'&'+i_param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#item_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
          offset = ele.offsetTop+$('#item_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
    }
  });

  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}

<?php //显示item信息?>
function show_link_item_info(item_id, s_param_str)
{
  s_param_str = decodeURIComponent(s_param_str);
  $.ajax({
    url: 'ajax.php?action=edit_item',
    data:'item_id='+item_id+'&'+s_param_str, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data){
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').show(); 
    }
  });

  if (origin_offset_symbol == 1) {
    c_offset = $("#show_popup_info").css("top");
    c_offset = c_offset.replace('px', '');
    tmp_c_offset = parseInt(c_offset, 10); 
    $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
  }
}
