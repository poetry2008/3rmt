<?php
/*
  定义 所有浮动div输出 类
*/
class notice_box {
  var $table_border = '0';
  var $table_width = '100%';
  var $table_cellspacing = '0';
  var $table_cellpadding = '2';
  var $table_parameters = '';
  var $table_row_parameters = '';
  var $table_data_parameters = '';
  function notice_box($heading_class='',$contents_class=''){
    $this->heading = array();
    $this->contents = array();
    $this->heading_class = $heading_class;
    $this->contents_class = $contents_class;
    $this->eof = '';
    $this->form = '';
  }
  function get_heading($heading){
    //浮动DIV的 标题
    $this->heading = $heading;
  }
  function get_contents($info,$buttons=array()){
    // info 是内部信息 buttons 是底部按钮 
    $this->contents['info'] = $info;
    $this->contents['buttons'] = $buttons;
  }
  function get_form($form){
    $this->form = $form;
  }
  function get_table($arr,$class='',$param_arr=array()){
    //获得表格头
    if(!empty($param_arr)){
      if(isset($param_arr['border'])&&$param_arr['border']!=''){
        $p_border = $param_arr['border'];
      }else{
        $p_border = 0;
      }
      if(isset($param_arr['cellspacing'])&&$param_arr['cellspacing']!=''){
        $p_cellspacing = $param_arr['cellspacing'];
      }else{
        $p_cellspacing = 0;
      }
      if(isset($param_arr['cellpadding'])&&$param_arr['cellpadding']!=''){
        $p_cellpadding = $param_arr['cellpadding'];
      }else{
        $p_cellpadding = 0;
      }
      if(isset($param_arr['width'])&&$param_arr['width']!=''){
        $p_width = $param_arr['width'];
      }else{
        $p_width = '';
      }
      if(isset($param_arr['parameters'])&&$param_arr['parameters']!=''){
        $p_parameters = $param_arr['parameters'];
      }else{
        $p_parameters = '';
      }
      $table_str = '<table border="' .$p_border. '" '.(!empty($p_width)?'width="'.$p_width.'"':'').' cellspacing="' .$p_cellspacing. 
        '" cellpadding="' .$p_cellpadding .'" ';
      if($p_parameters !=''){
        $table_str .= ' ' .$p_parameters;
      }
    }else{
      $table_str = '<table border="' .$this->table_border. '" width="'
        .$this->table_width. '" cellspacing="' .$this->table_cellspacing. 
        '" cellpadding="' .$this->table_cellpadding .'" ';
      if($this->table_parameters !=''){
        $table_str .= ' ' .$this->table_parameters;
      }
    }
    if($class != ''){
      $table_str .= ' class="' .$class.'" ';
    }
    $table_str .= '>' ."\n";
    foreach($arr as $row){
      $table_str .= '<tr';
      if($row['params']){
        $table_str .= ' ' .$row['params'];
      }
      $table_str .= '>';
      if(isset($row['text'])&&$row['text']){
        $table_str .= $this->get_td_list($row['text']);
      }
      $table_str .= '</tr>';
    }
    $table_str .= '</table>';
    return $table_str;
  }
  function get_td_list($arr){
    $table_str = '';
    if(isset($arr[0])&&is_array($arr[0])){
      for($i = 0 ;$i < sizeof($arr); $i++){
        $table_str .= '<td';
        if($arr[$i]['align'] != ''){
          $table_str .= ' align="' .$arr[$i]['align'] .'" ';
        }
        if($arr[$i]['params']){
          $table_str .= ' ' .$arr[$i]['params'];
        }
        $table_str .= '>';
        $table_str .= $arr[$i]['text'];
        $table_str .= '</td>';
      }
    }else{
        $table_str .= '<td';
        if($arr['align'] != ''){
          $table_str .= ' align="' .$arr['align'] .'" ';
        }
        if($arr['params']){
          $table_str .= ' ' .$arr['params'];
        }
        $table_str .= '>';
        $table_str .= $arr['text'];
        $table_str .= '</td>';
    }
    return $table_str;
  }
  function format_heading(){
    $arr = array();
    $arr[] = array('text'=> $this->heading);
    return $this->get_table($arr,$this->heading_class);
  }
  function format_contents(){
    $table_str = $this->get_table($this->contents['info'],$this->contents_class);
    $table_str .= $this->format_button();
    return $table_str;
  }
  function format_button(){
    // start button 处理 按钮
    $buttons = $this->contents['buttons'];
    $str = '';
    if(isset($buttons['button'])&&is_array($buttons['button'])){
      if($buttons['type'] == 'div'){
        $str .= '<div id="'.$buttons['id'] .'">';
      }
      foreach($buttons['button'] as $key => $value){
          $str .= $value;
      }
      if($buttons['type'] == 'div'){
        $str .= '</div>';
      }
    }else{
      $str = $buttons['button'];
    }
    $arr_str = array('align'=> $buttons['align'],
        'params'=>$buttons['params'],
        'text'=>$str);
    $arr = array();
    $arr[] = array('text'=>$arr_str);
    $table_str = $this->get_table($arr,$this->contents_class);
    // end button 按钮 处理结束
    return $table_str;
  }
  function show_notice(){
    $show_notice_str = '';
    //输出 所有信息
    if(isset($this->heading)&&$this->heading){
      $show_notice_str = $this->format_heading();
    }
    // 输出表格
    $form_set = false;
    if(isset($this->form)&&$this->form){
      $form_set = true;
      $show_notice_str .= $this->form;
    }
    if(isset($this->contents)&&$this->contents){
      $show_notice_str .= $this->format_contents();
    }
    if($form_set){
      if(isset($this->eof)&&$this->eof!=''){
        $show_notice_str .= $this->eof;
      }
      $show_notice_str .= '</form>';
    }
    return $show_notice_str;
  }
  function get_eof($eof){
    $this->eof = $eof;
  }
}
