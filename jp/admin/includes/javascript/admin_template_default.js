//display items list for the catagories
function change_products(id,products_id){
  
  $.ajax({
         type: "POST",
         data: 'id='+id+'&products_id='+products_id,
         async:false,
         url: 'ajax.php?action=products_list',
         success: function(data) {

           $("#products_list").html(data); 
           $("#c_id").val(id);
         }
  });
}

//store item ID
function save_products_id(value){

  $("#p_id").val(value);
}
