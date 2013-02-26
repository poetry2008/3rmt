var _top = 200; 
window.onscroll = scrolldiv;
window.onresize = resizepage;
//set top height when scoll
function scrolldiv(){
  var scrolltop = document.documentElement.scrollTop || document.body.scrollTop;
  if(scrolltop){
    $('#popup_info').css({'top':_top+scrolltop});
  }
}
//set left value when resize window
function resizepage(){
  var msg_div_width = $('#popup_info').width();
  var _left = (document.body.clientWidth - msg_div_width)/2;
  $('#popup_info').css({'left':_left});
}
//popup content window
function show_error_message(){
  var msg_div_height = $('#popup_info').height();
  var msg_div_width = $('#popup_info').width();
  var _left = (document.body.clientWidth - msg_div_width)/2;
  $('#popup_info').css({'left':_left,'top':_top});
  $('#popup_box').css('height',document.body.scrollHeight);
}
//close popup window
function close_error_message(){
  $('#popup_info').css('display','none');
  $('#popup_box').css('display','none');
}
