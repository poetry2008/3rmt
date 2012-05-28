function select_item_radio(i_obj, t_str, o_str, p_str, r_price)
{
      $(i_obj).parent().parent().parent().parent().parent().find('a').each(function() {
        if ($(this).parent().parent()[0].className == 'option_show_border') {
          $(this).parent().parent()[0].className = 'option_hide_border';
        } 
      });   
      if (t_str == '') {
        t_str = $(i_obj).children("span:first").html(); 
      } else {
        t_str = ''; 
      }
      $(i_obj).parent().parent()[0].className = 'option_show_border'; 
      origin_default_value = $('#'+o_str).val(); 
      $('#'+o_str).parent().html("<input type='hidden' id='"+o_str+"' name='"+p_str+"' value='"+t_str+"'>"); 
}
