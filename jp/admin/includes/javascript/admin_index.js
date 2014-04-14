//minimized window
function note_min_window(n_id)
{
  $.ajax({
    type: 'POST',
    data:'note_id='+n_id,
    async:false,
    url: 'ajax_orders.php?action=hide_note',
    success: function(msg) {
      msg_note_info = msg.split('|||');
      $('#note_'+n_id).css('display', 'none');
      note_add_str = '<li><a href="javascript:void(0);" onclick="note_revert_window(this, \''+n_id+'\');"><img src="images/icons/note_'+msg_note_info[0]+'_window.gif" title="'+msg_note_info[1]+'" alt="'+msg_note_info[1]+'"></a></li>';
      $('.note_hide_list').append(note_add_str);
    }
  });
}
//restored window
function note_revert_window(n_obj, n_id)
{
  $.ajax({
    type: 'POST',
    data:'note_id='+n_id,
    async:false,
    url: 'ajax_orders.php?action=show_note',
    success: function(msg) {
      $(n_obj).remove();
      $('#note_'+n_id).css('display', 'block');
    }
  });
}
//change layer
function changeLayer(obj) {
  arr = new Array();
  var i = 0
  $('.note').each(function(i) {
    arr[i] = $(this).css("z-index");
    i++;
  });
  arr.sort();
  max = arr[arr.length-1]+1;
  $(obj).css('z-index', max);
}
