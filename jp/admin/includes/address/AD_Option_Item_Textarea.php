<?php
require_once "AD_Option_Item_Basic.php";
class AD_Option_Item_Textarea extends AD_Option_Item_Basic
{
  var $has_textarea_default = true; 

/*-------------------------------
 功能：显示输出
 参数：$option_error_array(array) 错误选项
 返回值：无
 ------------------------------*/
  function render($option_error_array)
  {
    if (strlen($this->front_title)) {
      echo '<td class="main" width="30%" valign="top">'; 
      echo $this->front_title.':'; 
      echo '</td>'; 
    }
    $options = unserialize($this->option);
    $type_limit = $options['type_limit'];
    if($options['rows'] == 1){

      echo '<td class="main" width="70%">';
      $style_color = isset($_POST['ad_'.$this->formname]) && $_POST['ad_'.$this->formname] != $this->comment ?'color:#000;':'color:#999;';
      $style_color .= $type_limit == 'num' ? 'width:30%;' : 'width:60%;';
      echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
      echo '<input type="hidden" id="l_'.$this->formname.'" value="'.$this->required.'">';
      echo '<input type="text" name="ad_'.$this->formname.'" id="ad_'.$this->formname.'" value="'. (isset($_POST['ad_'.$this->formname])?$_POST['ad_'.$this->formname]:$this->comment) .'" style="'. $style_color .'" onfocus="this.style.color=\'#001\';if(this.value==\''. $this->comment.'\')this.value=\'\'" onblur="if(this.value==\'\'){this.value=\''. $this->comment .'\';this.style.color=\'#999\'}">'; 
      echo '<font id="r_'.$this->formname.'" color="red">';
      if ($this->required == 'true' && !isset($option_error_array[$this->formname]) && !isset($_POST['ad_'.$this->formname])) {
        echo '&nbsp;*必須';
      } 
      echo '</font>';
      echo '<span id="error_'.$this->formname.'" class="option_error"><font color="red">&nbsp;'; 
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</font></span>'; 
     echo '</td>';  
    }else{
    echo '<td class="main" width="70%">'; 
    echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
    echo '<input type="hidden" id="l_'.$this->formname.'" value="'.$this->required.'">';
    echo '<textarea style="width:60%;" 
      name="ad_'.$this->formname.'" id="ad_'.$this->formname.'" rows="'. $options['rows'] .'" onfocus="this.style.color=\'#001\';if(this.value==\''. $this->comment.'\')this.value=\'\'" onblur="if(this.value==\'\'){this.value=\''. $this->comment .'\';this.style.color=\'#999\'}">'.(isset($_POST['ad_'.$this->formname])?$_POST['ad_'.$this->formname]:'').'</textarea>'; 
    echo '<font id="r_'.$this->formname.'" color="red">';
    if ($this->required == 'true' && !isset($option_error_array[$this->formname]) && !isset($_POST['ad_'.$this->formname])) {
      echo '&nbsp;*必須';
    }
    echo '</font>';
    echo '<span id="error_'.$this->formname.'" class="option_error"><font color="red">&nbsp;'; 
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</font></span>'; 
    if (strlen($this->itextarea)) {
      echo '<br>'.$this->itextarea; 
    }
     echo '</td>'; 
    }
  }
/*----------------------------------
 功能：住所地址
 参数：$item_id(bumber) 项目编号
 返回值：格式字符串
 ---------------------------------*/
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }
/*---------------------------------
 功能：检查选项错误数组
 参数：$option_error_array(array) 选项错误数组
 返回值：true/false(boolean)
 --------------------------------*/ 
  function check(&$option_error_array)
  {
     global $_POST;
     $input_text_str = $_POST['ad_'.$this->formname]; 
     $input_text_str = str_replace(' ', '', $input_text_str); 
     $input_text_str = str_replace('　', '', $input_text_str); 
     $input_text_len = mb_strlen($input_text_str, 'UTF-8');

     
     if ($this->required == 'true') {
       if ($input_text_str == '' || $input_text_str == $this->comment) {
         $option_error_array[$this->formname] = ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL; 
         return true; 
       }
        
       if($this->num_limit_min != 0){ 
         if ($input_text_len < $this->num_limit_min) {
           $option_error_array[$this->formname] = "'$this->front_title'".ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN.$this->num_limit_min.ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN_1;  
           return true; 
         }
       }
     }

     if(trim($input_text_str) != '' && $input_text_str != $this->comment){

       if($this->num_limit != 0){ 
         if ($input_text_len > $this->num_limit) {
           $option_error_array[$this->formname] = sprintf(ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MAX, $this->num_limit);  
           return true; 
         }
       }
     }
    
     if ($input_text_str != '' && $input_text_str != $this->comment) {
       
       $item_type_error = false;
       $type_limit_array = unserialize($this->option);
       $type_limit = $type_limit_array['type_limit']; 
       switch ($type_limit) {
         case 'false_name';
           $item_type_error = $this->check_character($input_text_str); 
           break;
         case 'english_num';
           if (!preg_match('/^[0-9a-zA-Z]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 'english';
           if (!preg_match('/^[a-zA-Z]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 'num';
           if (!preg_match('/^[0-9]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 'email';
           if (!preg_match('/^[a-zA-Z0-9_\-\.\+]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         default;
           break;
       }
       
       if ($item_type_error) {
         $option_error_array[$this->formname] = ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG;  
         $_SESSION['error_array'][] =  $this->formname; 
         return true; 
       }
     }
     return false; 
  }
/*-------------------------------
 功能：检查字符
 参数：$c_str(string) 字符
 返回值：true/false(boolean)
 ------------------------------*/
  function check_character($c_str)
  {
    $character_array = array('ア' , 'ｱ', 'ぁ' , 'ァ', 'ｧ' ,'あ', 'イ' , 'ｲ' , 'ぃ' ,
        'ィ' , 'ｨ' , 'い', 'ウ' , 'ｳ' , 'ぅ' , 'ゥ' , 'ｩ' ,'う', 'エ' , 'ｴ' , 'ぇ'
        , 'ェ' , 'ｪ' ,'え', 'オ' , 'ｵ' , 'ぉ' , 'ォ' , 'ｫ' , 'お', 'カ', 'ｶ', 'ヵ' ,
        'か', 'キ' , 'ｷ' ,'き', 'ク' , 'ｸ' , 'く', 'ケ', 'ｹ', 'ヶ' ,'け', 'コ',
        'ｺ','こ', 'サ', 'ｻ','さ', 'シ' , 'ｼ','し', 'ス', 'ｽ','す', 'セ', 'ｾ','せ',
        'ソ', 'ｿ','そ', 'タ', 'ﾀ','た', 'チ' , 'ﾁ' , 'ち', 'ツ', 'ﾂ', 'っ' , 'ッ' ,
        'ｯ','つ', 'テ', 'ﾃ','て', 'ト' , 'ﾄ','と', 'ナ', 'ﾅ','な', 'ニ', 'ﾆ','に',
        'ヌ', 'ﾇ','ぬ', 'ネ', 'ﾈ','ね', 'ノ', 'ﾉ' , 'の', 'ハ', 'ﾊ','は', 'ヒ' ,
        'ﾋ','ひ', 'フ', 'ﾌ', 'ふ', 'ヘ' , 'ﾍ','へ', 'ホ' , 'ﾎ','ほ','マ', 'ﾏ' ,'ま',
        'ミ', 'ﾐ','み', 'ム' , 'ﾑ' ,'む', 'メ', 'ﾒ','め', 'モ' , 'ﾓ','も', 'ヤ',
        'ゃ', 'ゃ', 'ャ' , 'ｬ','や', 'ユ', 'ﾕ', 'ゅ', 'ュ', 'ｭ','ゆ', 'ヨ', 'ﾖ',
        'ょ', 'ョ' , 'ｮ','よ', 'ラ' , 'ﾗ','ら', 'リ', 'ﾘ','り', 'ル', 'ﾙ','る',
        'レ', 'ﾚ','れ', 'ロ' , 'ﾛ' , 'ろ', 'ワ' , 'ﾜ','わ', 'ゎ', 'ヮ', 'ヮ','わ',
        'ン', 'ﾝ' , 'ん', 'ガ', 'ｶﾞ','が', 'ギ', 'ｷﾞ','ぎ', 'グ' , 'ｸﾞ','ぐ', 'ゲ',
        'ｹﾞ','げ', 'ゴ', 'ｺﾞ','ご', 'ザ', 'ｻﾞ','ざ', 'ジ', 'ｼﾞ','じ', 'ズ',
        'ｽﾞ','ず', 'ゼ', 'ｾﾞ','ぜ', 'ゾ', 'ｿﾞ','ぞ', 'ダ', 'ﾀﾞ','だ', 'ヂ',
        'ﾁﾞ','ぢ', 'ヅ', 'ﾂﾞ','づ', 'デ', 'ﾃﾞ','で', 'ド', 'ﾄﾞ','ど', 'バ', 'ﾊﾞ',
        'ば', 'ビ', 'ﾋﾞ','び', 'ブ', 'ﾌﾞ','ぶ', 'ベ', 'ﾍﾞ','べ', 'ボ', 'ﾎﾞ', 'ぼ',
        'パ', 'ﾊﾟ','ぱ', 'ピ', 'ﾋﾟ','ぴ', 'プ', 'ﾌﾟ','ぷ', 'ペ', 'ﾍﾟ','ぺ', 'ポ',
        'ﾎ','ぽ'); 
    
    $c_str_len = mb_strlen($c_str, 'UTF-8');
    if ($c_str_len) {
      for($i=0; $i<$c_str_len; $i++) {
        $trac_str = mb_substr($c_str, $i, 1, 'UTF-8'); 
        if (!in_array($trac_str, $character_array)) {
          return true; 
        }
      }
    }
    
    return false;
  }

}

