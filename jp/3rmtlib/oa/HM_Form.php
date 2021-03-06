<?php
global $language;
require_once "DbRecord.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Form.php';
class HM_Form extends DbRecord
{
  var $id;
  var $groups;

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
    $this->groups = $this->getGroups();
  }

/* -------------------------------------
    功能: 载入默认值 
    参数: $orders_id(string) 订单id   
    返回值: 无 
------------------------------------ */
  function loadOrderValue($orders_id)
  {
    $id  = $this->id;
    $this->orders_id = $orders_id;
    $sql = 'select orders_status,end_user from '.TABLE_ORDERS . ' where orders_id = "'.$orders_id.'"';
    $status = tep_db_fetch_array(tep_db_query($sql));
    $this->end_user = $status['end_user'];
    $status = $status['orders_status'];
    $this->status = $status;
    foreach ($this->groups as $gk=>$group){
      foreach ($this->groups[$gk]->items as $ikey=>$item){
        $this->groups[$gk]->items[$ikey]->loadDefaultValue($orders_id,$this->id,$this->groups[$gk]->id);
      }
    }
  }

/* -------------------------------------
    功能: 获得组的对象的集合 
    参数: 无   
    返回值: 组的对象的集合(array) 
------------------------------------ */
  function getGroups()
  {
    $sql = "select g.*,$this->id as form_id ";
    $sql .=" from ".TABLE_OA_FORM_GROUP." fg,".TABLE_OA_GROUP." g ";
    $sql .=" where fg.form_id = ".$this->id;
    $sql .=" and fg.group_id= g.id ";
    $sql .=" order by fg.ordernumber ,fg.id ";
    $groups =  $this->getResultObjects($sql,'HM_Group');
    return $groups;
  }

/* -------------------------------------
    功能: 输出表单的html 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function render()
  {
    echo "<div >";
    echo "<form id='qa_form' action='".$this->action."' method='post'>";
    echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
    foreach ($this->groups as $group){
      $group->render();
    }
    echo "<tr><td class='main' colspan='3' align='right'>&nbsp;"; 
    //加入EOF标识，用于判断请求是否成功
    echo tep_eof_hidden();
    echo "<input type='hidden' id='stock_value_flag' name='stock_flag' value='0'>";
    echo "<input type='hidden' name='form_id' value='".$this->id."' /><div id='canEndDiv'>";
    echo $this->end_user;
    echo "<button onclick='finishTheOrder()'  id='canEnd' >".OA_FORM_ORDER_FINISH."</button></div>";
    echo "</td>";
    echo "</tr>";
    echo '</form>';
    echo "</div>";
    ?>
    <script type='text/javascript'>
       <?php 

       if (tep_orders_finishqa($this->orders_id)) {
         echo "var finished = true;";
       }else {
         echo "var finished = false;";
       }
    if(check_order_transaction_button($this->status)){
      ?>
      disableQA();
      var canceled = true;
      <?php 
    }else {
      ?>    
      var canceled = false;

      <?php 
    }
    ?>
    　var canEnd = false;
    <?php //是否显示取引完了按钮?> 
    function checkLockOrder()
    {
      if (finished==true){
        return false;
      }
      canEnd = true;
      if($('.require').length ==0 ){
        canEnd = true;
      }else{
        $('.require').each(function(ele){
            if(canEnd == true){
              canEnd = eval($(this)[0].tagName+$(this).attr('type')+'Require(this)');
            }
          });
      }

      $("#qa_form").find("input[type=text]").each(function (){
        if( $(this).val().length >$($("#size_"+$(this).attr('name'))).val() && $($("#size_"+$(this).attr('name'))).val() != ''){
          canEnd = false; 
        }
      });

      if ((canEnd == true ) || (canceled == true)){
        $("#canEndDiv").show();
      }else{
        $("#canEndDiv").hide();
      }
      return false;

    }
    <?php //是否为空?> 
    function SPANRequire(ele)
    {
      return $(ele).text().length>0;
    }
    <?php //是否为空?> 
    function SPANtextRequire(ele)
    {
      return SPANRequire(ele);
    }
        <?php //checkbox被选中?>
        function  INPUTcheckboxRequire(ele)
	{
		return ele.checked;
	}
    <?php //input框的值是否为空?> 
    function INPUTtextRequire(ele)
	
    {
      return INPUThiddenRequire(ele);
    }
    <?php //判断当前元素的值是否为空(过滤空格)?>
    function INPUThiddenRequire(ele)
    {
      var ele_value = $(ele).val();
      ele_value = ele_value.replace(/\s/g,'');
      return ele_value.length > 0;
    }
    <?php //删除元素?> 
    function cleanthisrow(ele){
      $(ele).parent().parent().children().find('input').each(
                                                             function(){
                                                               if($(this).attr("type")=='text' || $(this).attr("type") =='hidden'){
                                                                 if($(this).attr("type") =='hidden'){
                                                                   if($(this).attr("id")){
                                                                     var input_size = $(this).attr("id");
                                                                     if(!(input_size.substr(0,5) == 'size_')){
                                                                       $(this).val('');
                                                                     }
                                                                   }else{
                                                                     $(this).val('');
                                                                   }
                                                                 }else{
                                                                   $(this).val('');
                                                                 }
                                                               }
                                                               if(this.checked ==true ){
                                                                 $(this).removeAttr('checked');
                                                                 $(this).trigger('change');
                                                               }

                                                               
                                                             });
      $(ele).parent().parent().children().find('span').each(
                                                            function (){
                                                              $(this).text('');
                                                            }

                                                            );
      checkLockOrder();
      $("#qa_form").ajaxSubmit();

    }
    <?php //值是否为空?> 
    function INPUTtext(e){
      return jQuery.trim($(e).val()).length > 0;
    }
    <?php //是元素失效?> 
    function disableQA()
    {
      $("#qa_form").find('input').each(function(){
          $(this).attr('disabled',true);
        });
      $("#qa_form").find('button').each(function(){
          if($(this).attr('id')=='canEnd' && !finished){
            return ;
          }
          $(this).attr('disabled',true);
        });
      $("#qa_form").find('.clean').each(function(){
          $(this).hide();
        });


    }
    <?php //判断oa的完整性，如果信息不完整，给出相应的提示?>
    var complete_flag = true;
    var finish_flag = true;
    function finishTheOrder()
    {

      <?php //检测数据库中的数据是否完整?>
      var complete_flag_str = ''; 
      var complete_status = '<?php echo check_order_transaction_button($this->status);?>';
      $.ajax({
        url:'oa_ajax.php?action=complete&oID=<?php echo $_GET["oID"]?>',
            type:'post',    
            async : false,
            success: function(data){
              if(data != '' && complete_status != '1'){
                complete_flag = false; 
                complete_flag_str = data;
              }
            }
        }
        );
    <?php //数据完整，正常提交?>
    if(complete_flag){
      finish_flag = false;
      $.ajax({
        url:'oa_ajax.php?action=finish&oID=<?php echo $_GET["oID"]?>',
            type:'post',    
            beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
            async : false,
            success: function(data){
            $("#canEndDiv").hide();
            $("#wait").hide();
            $('body').css('cursor','');
            disableQA();
            window.location.href='orders.php?page=<?php echo $_GET['page'];?>';
          }
        }
        );
    <?php //数据不完整，提示出错信息?>
    }else{
        alert(complete_flag_str+'<?php echo NOTICE_COMPLETE_ERROR_TEXT;?>');
        window.location.href='orders.php?page=<?php echo $_GET['page'];?>&oID=<?php echo $_GET["oID"];?>&action=edit';
    }
    }
    $(document).ready(
                      function()
                      {
                        <?php 
                        if(tep_orders_finishqa($this->orders_id)) {
                          ?>
                          disableQA();
                          return 0;
                          <?
                        }
                        ?>

                        //bind size fonction 
                        $("#qa_form").find("input[type=text]").each(function(){
                            if($(this).attr('size')){
                              $(this).bind('keyup',function(){
                                  checkLockOrder();
                                  if( $(this).val().length >$($("#size_"+$(this).attr('name'))).val() && $($("#size_"+$(this).attr('name'))).val() != ''){
                                    $(this).parent().parent().find('.alertmsg').remove();
                                    $("<span class='alertmsg'><?php echo
                                      OA_FORM_TEXT_MAX_INPUT;?>"+$($("#size_"+$(this).attr('name'))).val()+"<?php
                                      echo OA_FORM_TEXT_MAX_INPUT_END;?>"+$($("#size_"+$(this).attr('name'))).val()+"<?php
                                      echo OA_FORM_TEXT_MAX_INPUT_IN;?></span>").insertAfter($(this).next());
                                  }else{
          $(this).parent().parent().find('.alertmsg').remove();
          }
          checkLockOrder();
          });
        }
        });
  checkLockOrder();
  $("#qa_form").find("input").each(function (){
      $(this).keyup(function(){
        if($(this).val()!=''){
          checkLockOrder();
        }
        });
      $(this).click(function(ele){
        if(!$(this).attr('checked')){
          if($(this).attr('type')!='text'){
            $("#canEndDiv").hide();
          }
        }
        $("#qa_form").submit();
        });
      $(this).change(function(ele){
        if ($(this).attr('type')!='checkbox') {
          $("#qa_form").submit();
        }
        });
      });
  $("#qa_form").submit(function(){

     var submit_flag = true;
     $("#qa_form").find("input[type=text]").each(function (){
        if( $(this).val().length >$($("#size_"+$(this).attr('name'))).val() && $($("#size_"+$(this).attr('name'))).val() != ''){
          submit_flag = false; 
        }
     });

     if(complete_flag && submit_flag){
        $('body').css('cursor','wait');
        $('#wait').show();
     }

        $(this).find('.outform').each(function(){
          if($(this).attr('name').substr(0,1)!='0'){
          $(this).attr('name','0'+$(this).attr('name'));}});
        <?php //如果请求失败，弹出相应的出错信息?>
        var options = {
          success:function(data){
              if(data == 'eof_error' && finish_flag == true){
                $('#wait').hide();
                $('body').css('cursor','');
                show_error_message();
                $("#popup_info").show();
                $("#popup_box").show(); 
              }else{
                $('#wait').hide();
                $('body').css('cursor','');
                checkLockOrder();
              }
            }
        };
      if(complete_flag && submit_flag){
        $(this).ajaxSubmit(options);
      }

        $(this).find('.outform').each(function(){
          if($(this).attr('name').substr(0,1)=='0'){
          $(this).attr('name',$(this).attr('name').substr(1));}});
        return false;
        });  


      }

  );

  </script>
    <?php


      }
/* -------------------------------------
    功能: 设置跳转页面 
    参数: $actionPage(string) 页面   
    返回值: 无 
------------------------------------ */
function setAction($actionPage)
{
  $this->action = $actionPage;
}
}
