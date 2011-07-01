<?php
require_once "HM_Item_Basic.php";
class HM_Item_Autocalculate extends HM_Item_Basic
{
  /*
必須：○　必須
項目名_____ _____　
   */
  var $hasRequire = true;
  var $hasTheName = true;
  var $must_comment = '*チェックを入れるとこのパーツは取引完了に必要なものになる ';  
  var $project_name_comment = '*○○○○ チェックボックス';
  
  function render()
  {
   if ($this->loaded){
    $this->defaultValue = $this->loadedValue;
}  
    if(strlen($this->thename)){
      echo "<div >";
      echo $this->thename;
      echo "</div>";
    }
    if($this->require){
      $classrequire = 'require';
    }else{
      $classrequire = '';
    }
    //设置 隐藏域用来存值
    echo "<input class='".$classrequire."' id='".$this->formname."real' value =
    '".$this->defaultValue."' type='hidden' name = '".$this->formname."'>";
    //每一个 关联 使用_ 分割
    $loadArray = explode('_',$this->defaultValue);
    //对照 orders 的 关联商品 查找数据
    $orders_products_query = tep_db_query("select
        p.products_id,op.products_quantity,op.products_name,p.relate_products_id
        from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS." p where
        op.products_id=p.products_id and
        op.orders_id='".$this->order_id."' order by op.products_name
        asc");
    $i = 0;
    while ($opp = tep_db_fetch_array($orders_products_query)) {
      $op = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." p,
            ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id=pd.products_id and
            pd.site_id='0' and p.products_id='".$opp['relate_products_id']."'"));
      $oqp = tep_db_fetch_array(tep_db_query("select * from 
            orders_questions_products where 
            orders_id='".$this->order_id."' and 
            products_id='".$op['products_id']."'"));
      //checkbox 和 input 值 使用|分隔
      $checkbox_arr = $loadArray[$i];
      $checkbox_info = explode('|',$checkbox_arr);
      if(is_array($checkbox_info)&&$checkbox_info!=null){
        $_checked = $checkbox_info[0]?$checkbox_info[0]:0;
        $_value = $checkbox_info[1]?$checkbox_info[1]:0;
      }else{
        $_checked = 0;
        $_value = 0;
      }
      //判断是否选中
      if($_checked==$opp['products_id']){
        $check = "checked"; 
      }else{
        $check = "";
      }
      echo "<div class='autocalculate_div'>";
      if($op){
      echo "<input value='".$opp['products_id']."'  
        onclick='".$this->formname."Change_option(".$opp['products_id'].",this)' 
        type='checkbox' ".$check." name='0".$this->formname."' ";
      echo "/>";
      echo $op['products_name'];
        //有关联商品的 输出
        echo " <span id ='quantity_".$opp['products_id']."' >".$opp['products_quantity']."</span> - ";
        echo "<input type='text' value='".$_value."' 
           id ='".$opp['products_id']."_input_".$this->formname."' 
           onchange='".$this->formname."Chage_span(".$opp['products_quantity'].",this,\"relate_product_".$i."\")' ";
      //判断是否 checkbox 选中来确定 是否为只读
      if($_checked==$opp['products_id']){
        echo " readonly='true' ";
      }
      echo " >";
        echo " = <span id='relate_product_".$i."'>".
          intval($opp['products_quantity']-$_value)."</span>";
      }else{
        echo "<input value='".$opp['products_id']."'  
        onclick='".$this->formname."Change_option(".$opp['products_id'].",this)' 
        type='checkbox' ".$check." name='0".$this->formname."' ";
        echo "/>";
        //没有关联商品的输出
        echo $opp['products_name'];
        echo "関連付け商品がないので手動入力してください";
      }
      $i++;
      echo "</div>";
    }
  }
  function renderScript()
  {
  //关联商品的javascript 脚本写在这里
    //$this->formname."Chage_span  方法 是设置 input 后面的Span 
    ?>
      <script type='text/javascript' >
         function <?php echo $this->formname."Chage_span(p_value,e_input,span_id)";?>{
           var v_input = e_input.value;
           if(v_input > p_value){
             e_input.value = 0;
             $("#"+span_id).text(p_value);
           }else{
             $("#"+span_id).text(p_value-v_input);
           }
         }
         function <?php echo $this->formname."Change_option(pid,ele)";?>{
           var <?php echo $this->formname;?>val ='';
           //循环 checkbox 把 checkbox状态 和input 值保存起来
           $("input|[name=0<?php echo $this->formname;?>]").each(function(){
               var check_info = '';
               if($(this).attr('checked')){
                 if($("#"+pid+"_input_<?php echo $this->formname;?>").val()){
                   check_info = $(this).val()+"|"+$("#"+pid+"_input_<?php echo
                     $this->formname;?>").val();
                 }else{
                   check_info = $(this).val()+"|"+"0";
                 }
               }else{
                 if($("#"+pid+"_input_<?php echo $this->formname;?>").val()){
                   check_info = "0|"+$("#"+pid+"_input_<?php echo
                     $this->formname;?>").val();
                 }else{
                   check_info = "0|"+"0";
                 }
               }
               <?php echo $this->formname;?>val += check_info+"_";
           });
           $('#<?php echo $this->formname;?>real').val( <?php echo
               $this->formname;?>val);
    // 增加库存
    if ($(ele).attr('checked')) {
      $("#"+pid+"<?php echo "_input_".$this->formname;?>").attr('readonly', true);
      $.ajax({
        url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count='+($("#quantity_"+pid).html()-$("#"+pid+"<?php echo "_input_".$this->formname;?>").val()),
        async : false,
        success: function(data) {
        }   
      }); 
    } else {
    // 减库存
      $("#"+pid+"<?php echo "_input_".$this->formname;?>").attr('readonly', false);
      $.ajax({
        url: 'ajax_orders.php?action=set_quantity&products_id='+pid+'&count=-'+($("#quantity_"+pid).html()-$("#"+pid+"<?php echo "_input_".$this->formname;?>").val()),
        async : false,
        success: function(data) {
        }   
      }); 
    }   


         }
      </script>
    <?php
  }

  function initDefaultValue($order_id,$form_id,$group_id)
  {
    //    $sql = 'select '.$this->datetype.' dp from orders where orders_id = "'.$order_id.'"';
    //    $result = tep_db_fetch_array(tep_db_query($sql));
    //    $theDate = $result['dp'];
    /*
    $theDate = time();
    $this->defaultValue = date('Y-m-d',$theDate);
    $this->m= date('m',strtotime($theDate));
    $this->d= date('d',strtotime($theDate));
    $this->y= date('Y',strtotime($theDate));
    */
    //天津ORDER id
    $this->order_id = $order_id;
  }


  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    //    $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/>";
    $formString .= "</br>\n";
    return $formString;
  }


}
