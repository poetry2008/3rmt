<?php
require_once "AD_Option_Item_Basic.php";
class AD_Option_Item_Textarea extends AD_Option_Item_Basic
{
  var $has_textarea_default = true; 

/* -------------------------------------
    功能: 输出该元素 
    参数: $option_error_array(array) 错误信息   
    参数: $is_space(array) 是否空行   
    返回值: 无 
------------------------------------ */
  function render($option_error_array, $is_space = false)
  {
    if (!$is_space) {
       if (NEW_STYLE_WEB !== true) {
         echo '<td width="10">'. tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>';
       } 
    } 
    if (strlen($this->front_title)) {
      if (NEW_STYLE_WEB === true) {
        echo '<td class="main" width="20%" valign="top">'; 
      } else {
        echo '<td class="main" width="30%" valign="top">'; 
      }
      echo $this->front_title.':'; 
      echo '</td>'; 
    }
    $options = unserialize($this->option);
    $type_limit = $options['type_limit'];
    $style_color = isset($_POST['op_'.$this->formname]) && $_POST['op_'.$this->formname] != $this->comment ?'color:#000;':'color:#999;';
    //$maxlen = $this->num_limit == 0 ? '' : ' maxlength="'. $this->num_limit .'"';
    if($options['rows'] == 1){
      
      $style_size = $type_limit == 'num' ? 'size="25" ' : 'class="width:75%;" ';
      if (NEW_STYLE_WEB === true) {
        echo '<td class="main">';
      } else {
        echo '<td class="main" width="70%">';
      }
      echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
      echo '<input type="hidden" name="type_'.$this->formname.'" value="'.$type_limit.'">';
      echo '<input type="hidden" id="l_'.$this->formname.'" value="'.$this->required.'">';
      echo '<input '. $style_size .'type="text" name="op_'.$this->formname.'" id="op_'.$this->formname.'" value="'. (isset($_POST['op_'.$this->formname])?$_POST['op_'.$this->formname]:$this->comment) .'" style="'. $style_color .'" onfocus="this.style.color=\'#001\';if(this.value==\''. $this->comment.'\')this.value=\'\'" onblur="if(this.value==\'\'){this.value=\''. $this->comment .'\';this.style.color=\'#999\'}">';
      echo '<font id="r_'.$this->formname.'" color="red">';
      if ($this->required == 'true' && !isset($option_error_array[$this->formname]) && !isset($_POST['op_'.$this->formname])) {

        echo '&nbsp;*必須';
      }
      echo '</font>';
      echo '<br><span id="error_'.$this->formname.'" class="shipping_error"><font color="red">';
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</font></span>'; 
     echo '</td>';  
    }else{
    if (NEW_STYLE_WEB === true) {
      echo '<td class="main">'; 
    } else {
      echo '<td class="main" width="70%">'; 
    }
    echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
    echo '<input type="hidden" name="type_'.$this->formname.'" value="'.$type_limit.'">';
    echo '<input type="hidden" id="l_'.$this->formname.'" value="'.$this->required.'">';
    echo '<textarea name="op_'.$this->formname.'" id="op_'.$this->formname.'" rows="'. $options['rows'] .'"'. $maxlen .' onfocus="this.style.color=\'#001\';if(this.value==\''.  $this->comment.'\')this.value=\'\'" onblur="if(this.value==\'\'){this.value=\''. $this->comment .'\';this.style.color=\'#999\'}" style="'. $style_color .((NEW_STYLE_WEB === true)?' width:80%;':'').'">'.(isset($_POST['op_'.$this->formname])?$_POST['op_'.$this->formname]:'').'</textarea>'; 
     echo '<font id="r_'.$this->formname.'" color="red">';
     if ($this->required == 'true' && !isset($option_error_array[$this->formname]) && !isset($_POST['op_'.$this->formname])) {

        echo '&nbsp;*必須';
     }
     echo '</font>';
     echo '<br><span id="error_'.$this->formname.'" class="shipping_error"><font color="red">';
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
  
/* -------------------------------------
    功能: 指定元素的项目 
    参数: $item_id(int) 元素id  
    返回值: 元素的项目的html(string) 
------------------------------------ */
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }

/* -------------------------------------
    功能: 检查信息是否正确 
    参数: $option_error_array(array) 错误信息  
    返回值: 是否正确(boolean) 
------------------------------------ */
  function check(&$option_error_array)
  {
     global $_POST;
     $input_text_str = $_POST['op_'.$this->formname]; 
     $input_text_str = str_replace(' ', '', $input_text_str); 
     $input_text_str = str_replace('　', '', $input_text_str); 
     $input_text_len = mb_strlen($input_text_str, 'UTF-8');
     
     if ($this->required == 'true') {
       if ($input_text_str == '' || $input_text_str == $this->comment) {
         $option_error_array[$this->formname] = ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL;  
         return true; 
       } 
       
       if($this->num_limit_min != 0){

         if($input_text_len < $this->num_limit_min){
           $option_error_array[$this->formname] = "'$this->front_title'".ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN.$this->num_limit_min.ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MIN_1;  
           return true;   
         }
       }  
     }

       if($this->num_limit != 0){

         if($input_text_len > $this->num_limit){
           $option_error_array[$this->formname] = sprintf(ADDRESS_ERROR_OPTION_ITEM_TEXT_NUM_MAX, $this->num_limit);  
           return true;   
         }
       } 
        
     if ($input_text_str != '') {
       $item_type_error = false;
       $type_limit_array = unserialize($this->option);
       $type_limit = $type_limit_array['type_limit']; 
       switch ($type_limit) {
/* -----------------------------------------------------
   case 'false_name' 片假名    
   case 'english_num' 英文和数字    
   case 'english' 英文    
   case 'num' 数字    
   case 'email' 邮箱    
------------------------------------------------------*/
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
           $mode = array('/\s/','/－/','/－/','/-/');
           $replace = array('','','','');
           $mode_ban = array('1','2','3','4','5','6','7','8','9','0');
           $mode_quan = array('/１/','/２/','/３/','/４/','/５/','/６/','/７/','/８/','/９/','/０/');
           $input_text_str = preg_replace($mode,$replace,$input_text_str);
           $input_text_str = preg_replace($mode_quan,$mode_ban,$input_text_str);
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
         return true; 
       }
     }
     return false; 
  }

/* -------------------------------------
    功能: 检查字符串的字符是否在指定范围里 
    参数: $c_str(string) 字符串  
    返回值: 是否在指定范围里(boolean) 
------------------------------------ */
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

