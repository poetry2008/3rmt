<script type="text/javascript">
<?php //选中/非选中网站?> 
function change_show_site(site_id,flag,site_list,param_url,current_file){  
  var ele = document.getElementById("site_"+site_id);
  var unshow_list = document.getElementById("unshow_site_list").value;
  var last_site_list = document.getElementById("show_site_id").value;
  $.ajax({
    dataType: 'text',
    type:"POST",
    data:'param_url='+param_url+'&flag='+flag+'&site_list='+site_list+'&site_id='+site_id+'&current_file='+current_file+'&unshow_list='+unshow_list+'&last_site_list='+last_site_list,
    async:false, 
    url: 'ajax_orders.php?action=select_all_site',
    success: function(msg) {
    if (msg != '') {
        if (ele.className == 'site_filter_selected') {
          ele.className='';
        } else {
          ele.className='site_filter_selected';
        }
        window.location.href = msg; 
      }
    }
  });
}
</script>
