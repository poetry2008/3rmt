<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Autocalculate.php';
class HM_Item_Autocalculate extends HM_Item_Basic
{
  
  var $hasRequire = true;
  var $hasTheName = true;
  var $must_comment = TEXT_AUTO_MUST_COMMENT;  
  var $project_name_comment = TEXT_AUTO_NAME_COMMENT; 
  
/* -------------------------------------
    功能: 输出该元素信息的html 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function render()
  {
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    echo "<td>";
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;
    }  
 
    if($this->require){
      $classrequire = 'require';
    }else{
      $classrequire = '';
    }
    //设置 隐藏域用来存值
    echo "<input class='".$classrequire."' id='".$this->formname."real' value ='".$this->defaultValue."' type='hidden' name = '".$this->formname."'>";
    //每一个 关联 使用_ 分割
    $loadArray = explode('_',$this->defaultValue);
    //对照 orders 的 关联商品 查找数据
    $orders_products_query = tep_db_query("select op.orders_products_id, 
        p.products_id,op.products_quantity,op.products_name,
        p.relate_products_id,p.products_bflag,
        op.products_rate
        from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where
        op.products_id=p.products_id and
        op.orders_id='".$this->order_id."'and p.products_bflag=1 order by op.products_name
        asc");
    $i = 0;

    // 重新取一次 订单类型
    $order_type =  tep_check_order_type($this->order_id);
    $noproduct = true;
    while ($opp = tep_db_fetch_array($orders_products_query)) {

      //如果是混合订单只选择买取的产品
      $op = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." p,
            ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id=pd.products_id and
            pd.site_id='0' and p.products_id='".$opp['relate_products_id']."'"));

      $checkbox_arr = $loadArray[$i];
      $checkbox_info = explode('|',$checkbox_arr);
      if(is_array($checkbox_info)&&$checkbox_info!=null){
        $_checked = $checkbox_info[0]?$checkbox_info[0]:0;
        $_value = $checkbox_info[1]?$checkbox_info[1]:0;
        $__checked = $checkbox_info[2]?$checkbox_info[2]:0;
        $___checked = $checkbox_info[3]?$checkbox_info[3]:0;
      }else{
        $___checked = 0; 
        $__checked = 0; 
        $_checked = 0;
        $_value = 0;
      }
      //判断是否选中
      if(!$op){
        $currentNull = true;
      }
      if($_checked==$opp['products_id']&&$__checked==$op['products_id']
          &&$___checked==$opp['orders_products_id']){
        $check = "checked"; 
      }else{
        $check = "";
      }
      if($currentNull and $_checked == $opp['products_id']
          &&$___checked==$opp['orders_products_id']){
        $check = 'checked';
      }

      if(!$op && $opp['products_bflag']==1){ 
        //if no products  ,continue;
        echo "<input class='".$classrequire."'
          value='".$opp['products_id']."|".$opp['orders_products_id']."'  
        onchange='".$this->formname."Change_option(".$opp['products_id'].",this,".$i.")' 
        type='checkbox' ".$check." name='0".$this->formname."' ";
        echo "/>";
        echo "".$opp['products_name'].TEXT_AUTO_NO_OP."";
        $i++;
        continue;
      }
      $noproduct = false;
      if ($order_type==3 and $op['products_bflag']==1){
        continue;
      }
      //checkbox 和 input 值 使用|分隔



      echo "<div class='autocalculate_div'>";
      if($op && $opp['products_bflag']==1){
        echo "<input class='".$classrequire."'
          value='".$opp['products_id']."|".$opp['orders_products_id']."'  
        onchange='".$this->formname."Change_option(".$opp['products_id'].",this,".$i.")' 
        type='checkbox' ".$check." name='0".$this->formname."' ";
        echo "id = 'spid_".$op['products_id']."'/>";
        echo $op['products_name'];
        //有关联商品的 输出
        echo "<br>";
        echo " <font id
          ='quantity_".$i."_".$opp['products_id']."_".$opp['orders_products_id']."'
          >".$opp['products_quantity']."</font> - ";
        echo "<input type='text'
          value='".($check=="checked"?intval($opp['products_quantity']-$_value):0)."' 
           id ='".$opp['products_id']."_".$opp['orders_products_id']."_input_".$this->formname."' 
           onchange='".$this->formname."Chage_span(".$opp['products_quantity'].",this,\"span_relate_product_".$opp['products_id']."_".$opp['orders_products_id']."\")' ";
        //判断是否 checkbox 选中来确定 是否为只读

        if($_checked==$opp['products_id']&&$__checked==$op['products_id']
            &&$___checked==$opp['orders_products_id']){
          echo " readonly='true' ";
        }
        echo " >";
        echo " = <font
          id='span_relate_product_".$opp['products_id']."_".$opp['orders_products_id']."'>".
          ($check=="checked"?$_value:intval($opp['products_quantity']))."</font>";
        echo "<input id='span_relate_product_".$opp['products_id']."_".$opp['orders_products_id']."_radices' type='hidden' value='".$opp['products_rate']."'>";
      echo "<br>";
      echo "<br>";
      }
      $i++;
      echo "</div>";
    }
  }
  
/* -------------------------------------
    功能: 输出javascript 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function renderScript()
  {
    //关联商品的javascript 脚本写在这里
    //$this->formname."Chage_span  方法 是设置 input 后面的Span 
    ?>
    <script type='text/javascript' >
      var sum_flag = new Array();
      var sub_flag = new Array();
      <?php //改变指定span元素的内容?> 
      function <?php echo $this->formname."Chage_span(p_value,e_input,span_id)";?>{
      var v_input = e_input.value;
      var re = /^-?[0-9]+$/;
      if(!re.test(v_input) && v_input != ''){
        e_input.value = 0;
        $("#"+span_id).text(p_value);
      }else{
        if(v_input > p_value){
          e_input.value = 0;
          $("#"+span_id).text(p_value);
        }else{
          if(v_input!=''){
            $("#"+span_id).text((p_value-v_input));
          }else{
            $("#"+span_id).text(0);
          }
        }
      }
    }
    <?php //改变指定元素的值?> 
    function <?php echo $this->formname."Change_option(pid,ele,t)";?>{

      <?php //判断当前的操作是否与数据库储存的数据一致?>
      var status = 0;
      if($(ele).attr('checked')){
        status = 1;
      }
      var stock_flag = true;
      $.ajax({
        url:'oa_ajax.php?action=stock&num='+parseInt(1000*Math.random()),
            type:'post',    
            data:'oID=<?php echo $this->order_id;?>&name=<?php echo $this->formname;?>&status='+status+'&pid='+pid+'&n='+t,
            async : false,
            beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
            success: function(data){
              if(data == 'true'){
                stock_flag = false; 
                $("#wait").hide();
                $('body').css('cursor','');
              }
            }
      });
   if(stock_flag){
      $("#stock_value_flag").val('1');
      var <?php echo $this->formname;?>val ='';
      <?php //循环 checkbox 把 checkbox状态 和input 值保存起来?>
      var i =0;
      $("input|[name=0<?php echo $this->formname;?>]").each(function(){
          var check_var_tmp = new Array();
          check_var_tmp = $(this).val().split("|"); 
          var check_info = '';
          var tmp_pid = $(this).val();
          tmp_pid = tmp_pid.replace('|','_');
          var span_value = $("#quantity_"+i+"_"+tmp_pid).html()-$("#"+tmp_pid+"<?php echo
                   "_input_".$this->formname;?>").val();
          var orsers_products_id = '0';
          if($(this).attr('checked')){
            if(span_value){
              check_info = check_var_tmp[0]+"|"+span_value;
            }else{
              check_info = check_var_tmp[0]+"|"+"0";
            }
          }else{
            if(span_value){
              check_info = "0|"+span_value;
            }else{
              check_info = "0|"+"0";
            }
          }
          if(this.id){
            check_info += '|'+this.id.substr(5);
          }else{
            check_info += '|nullvalue';
          }
          if(check_var_tmp[1]){
            check_info += '|'+check_var_tmp[1];
          }else{
            check_info += '|0';
          }
          <?php echo $this->formname;?>val += check_info+"_";
          i++;
        });

      $('#<?php echo $this->formname;?>real').val( <?php echo  $this->formname;?>val);


      <?php //增加库存?>
      var tmp_pid = $(ele).val(); 
      tmp_pid = tmp_pid.replace('|','_');
      if($('#span_relate_product_'+tmp_pid+'_radices')){
        var radices = $('#span_relate_product_'+tmp_pid+'_radices').val();
      }else{
        var radices = 1;
      }
      if ($(ele).attr('checked')) {
        $("#"+tmp_pid+"<?php echo "_input_".$this->formname;?>").attr('readonly',true);
        if(!sum_flag[t]){
        $.ajax({
          url:
          'ajax_orders.php?action=set_quantity&products_id='+pid+'&count='+(($("#quantity_"+t+"_"+tmp_pid).html()-$("#"+tmp_pid+"<?php echo "_input_".$this->formname;?>").val())*radices),
              async : false,
              success: function(data) {
              sum_flag[t] = true;
              sub_flag[t] = false;
            }   
          }); 
        }
      } else {
        $("#"+tmp_pid+"<?php echo "_input_".$this->formname;?>").attr('readonly', false);
        if(!sub_flag[t]){
        <?php //减库存?>
        $.ajax({
          url:
          'ajax_orders.php?action=set_quantity&products_id='+pid+'&count=-'+(($("#quantity_"+t+"_"+tmp_pid).html()-$("#"+tmp_pid+"<?php echo "_input_".$this->formname;?>").val())*radices),
              async : false,
              success: function(data) {
              sum_flag[t] = false;
              sub_flag[t] = true;
            }   
          }); 
        }

      }   
      checkLockOrder();
      $("#qa_form").ajaxSubmit();
      $("#stock_value_flag").val('0');
      $("#wait").hide();
      $('body').css('cursor','');
    }else{ 
      <?php //如果当前的操作与数据库中的数据不一致，给出提示，并跳转?>
      alert('<?php echo NOTICE_STOCK_ERROR_TEXT;?>');
      window.location.href='orders.php?page=<?php echo $_GET['page'];?>&oID=<?php echo $this->order_id;?>&action=edit';
    }
    }
    </script>
        <?php
        }

/* -------------------------------------
    功能: 初始化默认值 
    参数: $order_id(string) 订单id   
    参数: $form_id(string) 表单id   
    参数: $group_id(string) 组id   
    返回值: 无 
------------------------------------ */
  function initDefaultValue($order_id,$form_id,$group_id)
  {
    $this->order_id = $order_id;
  }

/* -------------------------------------
    功能: 删除时会激活这个操作 把加进的数量 再减回去 
    参数: $eid(int) 元素id   
    参数: $gid(string) 组id   
    参数: $form_id(string) 表单id   
    返回值: 无 
------------------------------------ */
  static public function deleteTrigger($eid,$gid=0,$form_id=0)
  {
    $sql = 'select * from oa_formvalue where item_id ='.$eid.' ';
    if($gid){
      $sql .= ' and group_id = '.$gid;
    }
    if($form_id){
      $sql .= ' and form_id = '.$form_id;
    }
    $sqlArray = array();
    $res = tep_db_query($sql);
    while($formvalue_res  = tep_db_fetch_array($res)){
      $oid = $formvalue_res['orders_id'];
      $quaArray = @explode('_',$formvalue_res['value']);
      if(!count($quaArray)){
        continue;
      }else {
        foreach( $quaArray as $key=>$value){
          echo $value;
          $id_to_qua = explode('|',$value);
          $id = $id_to_qua[2];
          $qua = $id_to_qua[1];
          tep_db_query("update ".TABLE_PRODUCTS." set products_real_quantity=`products_real_quantity`-".(int)$qua." where products_id='".$id."'");   
        }
      }
    };
  }

/* -------------------------------------
    功能: 输出信息 
    参数: $item_id(int) 元素id   
    返回值: 信息的html(string) 
------------------------------------ */
  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    $formString .= "</br>\n";
    return $formString;
  }


}
