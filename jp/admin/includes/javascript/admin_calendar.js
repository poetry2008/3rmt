$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_date_edit').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
    if (event.which == 13) {
      //ENTER
      if ($('#show_date_edit').css('display') != 'none') {
        $("#button_save").trigger("click");  
      } 
    } 
  });    
});
