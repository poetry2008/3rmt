$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc
      if ($('#show').css('display') != 'none') {
	hide_text();
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show').css('display') != 'none') {
                $("#button_save").trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      }
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      }
    }
  });
});
