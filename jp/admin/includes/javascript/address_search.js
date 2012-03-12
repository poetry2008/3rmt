function check_search(){

var code = document.getElementById("list_option6").value;
$.ajax({
       url: 'address_search_ajax.php',
       data: {code:code},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         
         if(data == ''){
         
           alert('郵便番号に誤りがあります。');
         }else{
           var arr = new Array();
           arr = data.split(",");
           $("#list_option7").val(arr[0]);
           $("#list_option7").css("color","#000");
           $("#list_option8").val(arr[1]+arr[2]);
           $("#list_option8").css("color","#000");
           fee(arr[0]);
         }
       }
    }); 
}
