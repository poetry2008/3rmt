function close_popup_notice()
{
  $("#popup_notice").css("display", "none");
  $("#greybackground").remove();
}

function update_notice(url)
{
  $.ajax({
    url: 'ajax_notice.php?action=process',    
    type:'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $("#popup_notice").css("display", "none");
      $("#greybackground").remove();
      window.location.href=url;
    }
  });
}
