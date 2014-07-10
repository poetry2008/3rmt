//show attendance info
function show_attendance_info(id){
 $.ajax({
 url: 'ajax.php?action=edit_attendance_info',
 data: 'id='+id,
 type: 'POST',
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_attendance").html(data);

      $('div#show_attendance').css('z-index', 1);
      $('div#show_attendance').css('height', 550);
      $('div#show_attendance').css('left', 200);
$('div#show_attendance').css('top',250);

      $('div#show_attendance').css('display','block');
 }
  }); 

}

//hidden box
function hidden_info_box(){
   $('#show_attendance').css('display','none');
}

//submit
function check_attendance_info(){
 document.forms.attendances.submit();
}

//delect attendance by id
function delete_attendance_info(id){
	if(confirm(attendance_del_confirm)) {
      	
        $.ajax({
            url: 'ajax.php?action=delete_attendance_info',
            data: 'attendance_id='+id,
            type: 'POST',
            dataType: 'text',
            async : false,
            success: function(data){
				if(data){
					alert('delete sucess!');
					 window.location.href = href_attendance;
				
				}
            }
        }); 
	}
}


function add_attendance_approve_person(id){
        $.ajax({
            url: 'ajax.php?action=add_attendance_approve',
            data: 'id='+id,
            type: 'POST',
            dataType: 'text',
            async : false,
            success: function(data){
				if(data){
                  $("#tep_add").append(data);
				}
            }
        }); 
}


function change_type_text(){
	var select_val = $("#type_id").val();

	if(select_val==1){
      $("#src_text_image").css("display","none");	
      $("#upload_file_image").css("display","none");	
      $("#image_div").css("display","none");	

      $("#src_text_color").css("display","block");	
      $("#color_div").css("display","block");	
	}
	if(select_val==0){
      $("#image_div").css("display","block");	
      $("#src_text_image").css("display","block");	
      $("#upload_file_image").css("display","block");	
      $("#src_text_color").css("display","none");	
      $("#color_div").css("display","none");	
	
	}
}
