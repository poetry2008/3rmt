function confirm_session_error(){

  $.ajax({
       url: 'ajax_confirm_session_error.php',
       data: {},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         if(data == 'error'){
           alert('一定時間が経過したか、複数のブラウザによって操作された為、接続が切断されました。お手数ではございますが、再度ご入力いただく必要がございます。');document.location.href='checkout_products.php';
         }else{
         
           document.checkout_confirmation.submit();
         }          
       }
    });
}
