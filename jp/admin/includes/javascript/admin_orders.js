if(js_orders_status_keywords){
	$("#keywords").val(js_orders_keywords);	
}

if(js_orders_search_type){
	$(document).ready(function(){
        	$("select[name=search_type]").find("option[value='"+js_orders_option_value+"']").attr("selected", "selected");
        });	
}

if(js_orders_action_isset){
	$(function() {
        	left_show_height = $('#orders_list_table').height();
        	right_show_height = $('#rightinfo').height();
        	if (right_show_height <= left_show_height) {
        		$('#rightinfo').css('height', left_show_height);  
        	}
        });

  	function showRightInfo() {
    		left_show_height = $('#orders_list_table').height();
    		$('#rightinfo').css('height', left_show_height);  
  	}

  	$(window).resize(function() {
      		showRightInfo();
      	});
}
