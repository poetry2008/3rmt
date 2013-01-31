function confirm_session_error(num,str){

  $.ajax({
       url: 'ajax_confirm_session_error.php',
       data: {ad_num:num,ad_str:str},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
         data_tmp_str = data.substr(0, 13); 
         if (data_tmp_str == 'check success') {
           tmp_data_array = data.split('|||');
           alert(tmp_data_array[1]);
           window.location.href = $('#carturl').val();         
         } else if (data == 'no_count') {
           window.location.href = $('#carturl').val();         
         }else if(data == 'error'){
           alert('一定時間が経過したか、複数のブラウザによって操作された為、接続が切断されました。お手数ではございますが、再度ご入力いただく必要がございます。');document.location.href='checkout_option.php';
         }else if(data == 'weight'){
           alert('一定時間が経過したか、複数のブラウザによって操作された為、接続が切断されました。お手数ではございますが、再度ご入力いただく必要がございます。');document.location.href='shopping_cart.php';
         }else if(data.length > 6){
           var string = '';
           var array = new Array();
           array = data.split('|');
           $("#"+array[0]).html('&nbsp;<a href="open.php"><font color="#CC0033"><b>※要問合※</b></font></a><br><font color="#FF0000">総重量が（'+array[1]+'）の規定の重量を超えました。<br>商品を削除するか、または個数を変更して（'+array[2]+'）kg以内にしてください。</font>');
         }else{
           $.ajax({
             url: 'ajax_process.php?action=new_telecom_option',
             type: 'POST',
             dataType: 'text',
             async : false,
             success: function(data){
               patrn = /^[0-9]{8}-[0-9]{8}$/;
               if(patrn.exec(data)){
                 $(':input[name=option]').val(data);
               }
               document.checkout_confirmation.submit();
             }
           });
         }          
       }
    });
}
