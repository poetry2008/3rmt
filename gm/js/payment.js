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
 var headID = document.getElementsByTagName("head")[0];
 var newCss = document.createElement('link');
 newCss.type = 'text/css';
 newCss.rel = "stylesheet";
 newCss.href = "css/gm.css";
 headID.appendChild(newCss);
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

function check_confirm_payment(payment_str)
{
   var return_single = true;
   var url_str = '';
   $.ajax({
     url: 'ajax_payment.php?action=check_payment',   
     data: 'payment='+payment_str,
     type: 'POST',
     dataType: 'text',
     async:false,
     success: function (data) {
       if (data == '1') {
         return_single = true; 
       } else {
         return_single = false; 
         url_str = data;   
       }
     }
   });
  
  if (return_single == false) {
    window.location.href = url_str; 
  } else {
    return true; 
  }
  
  return false;
}

//check payment input
function check_payment_input(ele){

  ele_value = ele.value;
  ele_value = ele_value.replace(/\s/g,'');
  ele_value = ele_value.replace(/　/g,'');
  ele_value = ele_value.replace(/－/g,'-');
  ele_value = ele_value.replace(/１/g,'1');
  ele_value = ele_value.replace(/２/g,'2');
  ele_value = ele_value.replace(/３/g,'3');
  ele_value = ele_value.replace(/４/g,'4');
  ele_value = ele_value.replace(/５/g,'5');
  ele_value = ele_value.replace(/６/g,'6');
  ele_value = ele_value.replace(/７/g,'7');
  ele_value = ele_value.replace(/８/g,'8');
  ele_value = ele_value.replace(/９/g,'9');
  ele_value = ele_value.replace(/０/g,'0');
  ele.value = ele_value;
}
