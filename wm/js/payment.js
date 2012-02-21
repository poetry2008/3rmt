function triggerHide(radio)
{
  $("input[name=payment]").each(function(index){
      if ($(this).parent().parent()[0].className == 'box_content_title box_content_title_selected') {
         $(this).parent().parent().removeClass('box_content_title box_content_title_selected');
         $(this).parent().parent().addClass('box_content_title');
      }
  });
  if ($(radio).attr("checked") == true) {
      $(".rowHide").hide();
      $(".rowHide").find("input").attr("disabled","true");
      $(".rowHide_"+$(radio).val()).show();
      $(".rowHide_"+$(radio).val()).find("input").removeAttr("disabled");
      $(radio).parent().parent().removeClass();
      $(radio).parent().parent().addClass('box_content_title box_content_title_selected');
 }
}
$(document).ready(function(){
    if($("input[name=payment]").length == 1){
      $("input[name=payment]").each(function(index){
	  $(this).attr('checked','true');
	});
    }
    $("input[name=payment]").click(function(index){
	  triggerHide(this);
      });
    $("input[name=payment]").each(function(index){
	if ($(this).attr('checked') == true) {
	  triggerHide(this);
	}
      });
    $(".moduleRow").click(function(){
	triggerHide($(this).find("input:radio")[0]);
      });
    $(".moduleRowSelected").click(function(){
	triggerHide($(this).find("input:radio")[0]);
      });
  });

var selected;
function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_payment.payment[0]) {
    document.checkout_payment.payment[buttonSelect].checked=true;
  } else {
    document.checkout_payment.payment.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}
