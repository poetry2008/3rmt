var _top = 200; 
window.onscroll = scrolldiv;
window.onresize = resizepage;
function scrolldiv(){
  var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
  if(scrolltop){
    $('#popup_info').css({'top':_top+scrolltop});
  }
}
function resizepage(){
  var msg_div_width = $('#popup_info').width();
  var _left = (document.body.clientWidth - msg_div_width)/2;
  $('#popup_info').css({'left':_left});
}
function show_eof_error(){
  var msg_div_height = $('#popup_info').height();
  var msg_div_width = $('#popup_info').width();
  var _left = (document.body.clientWidth - msg_div_width)/2;
  $('#popup_info').css({'left':_left,'top':_top});
  $('#popup_box').css('height',document.body.scrollHeight);
}
function close_eof_error(){
  $('#popup_info').css('display','none');
  $('#popup_box').css('display','none');
}
