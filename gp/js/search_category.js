function search_top_category(ra_str)
{
  $.ajax({
    type:"POST", 
    url:"search_category.php", 
    data:"ra="+ra_str, 
    success:function(msg){
      $("#showca").html(msg);   
      $("#showca").css('display', 'block');   
    }
  });
}

function close_top_category(close_name)
{
  $("#"+close_name).css('display', 'none');
}

function toggle_index_menu(toggle_num)
{
  if (toggle_num == 0) {
    $('#imenu01').css('display', 'block'); 
    $('#imenu02').css('display', 'none'); 
    
    $('#icontent01').css('display', 'block'); 
    $('#icontent02').css('display', 'none'); 
  } else {
    $('#imenu01').css('display', 'none'); 
    $('#imenu02').css('display', 'block'); 
    
    $('#icontent01').css('display', 'none'); 
    $('#icontent02').css('display', 'block'); 
  }
}
