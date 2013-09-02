<?php 
if($p_image_count>1){
?>
<script>
function fnCreate(src){
        
<?php            /* 要创建的div的classname */   ?>
            var ClassName = "thumbviewbox";
        
            if(src == '')
            {
                return false;
            }
            
<?php            /* 优先创建图片，如果图片没有加载成功，回调自己 */?>
            var img = document.createElement('img');
            img.setAttribute('src',src);
            var imgwd = img.width;
            var imghg = img.height;
            
            if(imgwd<1)
            {
                var timer = setTimeout("fnCreate('"+src+"')",100);
                return false;
            }else{
                clearInterval(timer);
            }
            
<?php            /* 清除已经弹出的窗口，防止冒泡 */    ?>
            em = document.getElementsByClassName(ClassName)
            
            for(var i=em.length-1; i>=0; i--){
                var p = em[i];
                p.parentNode.removeChild(p);
            }    
            
<?php            /* 各项参数 */   ?>
            var htmlWidth = window.innerWidth;          <?php  //可见区域宽度 ?>
            var htmlHeight = window.innerHeight;        <?php//可见区域高度 ?>
            var divleft = 0;                            <?php//将要创建的div的左边距 ?>
            var divtop =0;                              <?php  //将要创建的div的右边距 ?>
            var allheight = document.body.scrollHeight 
            var closefunction = 'em=document.getElementsByClassName("'+ClassName+'");for(var i=em.length-1;i>=0;i--){var p=em[i];p.parentNode.removeChild(p);}';
<?php //关闭div的代码 ?>
            
            
<?php            /* 计算通过图片计算div应该在的位置，保证弹窗在页面中央 */ ?>
            if(imgwd>htmlWidth*0.8)
            {
                img.setAttribute('width',htmlWidth*0.8);
                divleft=htmlWidth*0.1;
                if(imghg>htmlHeight*0.8)
                {
                    divtop = htmlHeight*0.1;
                }else{
                    divtop = (htmlHeight-imgwd)/2;
                }
            }else{
                img.setAttribute('width',imgwd);
                divleft= (htmlWidth-imgwd)/2;
                if(imghg>htmlHeight*0.8)
                {
                    divtop = htmlHeight*0.1;
                }
                else
                {
                    divtop = (htmlHeight-imgwd)/2;
                }
            }
    
<?php            /* 创建弹窗 */ ?>
            var element_ground = document.createElement('div');
            element_ground.setAttribute('class',ClassName);
            element_ground.setAttribute('style','position: absolute; top: 0; left: 0; z-index: 150;background-color: rgb(0, 0, 0); opacity: 0.8; width:100%; height: '+allheight+'px;');
            element_ground.setAttribute('onclick',closefunction);
            var element_boder = document.createElement('div');
            element_boder.appendChild(img);
            element_boder.setAttribute('class',ClassName);
            element_boder.setAttribute('style','margin: 0 auto; line-height: 1.4em; overflow: auto; width: 100%; padding: 0 5px 0;');
            element_boder.setAttribute('onclick',closefunction);
            var element = document.createElement('div');
            element.appendChild(element_boder);
            element.setAttribute('class',ClassName);
            element.setAttribute('style','width:100%;position:absolute;z-index:151;text-align:center;line-height:0;top:100px;z-index:151;');
                
            document.body.appendChild(element_ground);
            document.body.appendChild(element);
    }
    </script>

<script language ="javascript" >
var curIndex=0;
<?php
//时间间隔(单位毫秒)，每秒钟显示一张，数组共有5张图片放在Photos文件夹下。图片路径可以是绝对路径或者相对路径
?>
var timeInterval=5000; <?php //时间1秒 ?>

var img_arr=new Array();
var link_arr=new Array();
<?php
$index=0;
$default_img_small = array();
$default_img_large = array();
foreach($p_image_list as $p_image_src){
  if(file_exists3(DIR_WS_IMAGES.'products/'.$p_image_src)&&
    $p_image_src){
    $info_image = array(
        'width' => PRODUCT_INFO_IMAGE_WIDTH,
        'height' => PRODUCT_INFO_IMAGE_HEIGHT
        );
//    $image_small_src = tep_image3(DIR_WS_IMAGES.'products/' . $p_image_src, $product_info['products_name'], '', '', '',$info_image);
    $image_small_src = DIR_WS_IMAGES.'products/'.$p_image_src;
    $image_large_src = DIR_WS_IMAGES.'products/'.$p_image_src;
    $default_img_small[] =  $image_small_src;
    $default_img_large[] =  $image_large_src;
?>
  img_arr[<?php echo $index;?>]= '<?php 
  echo $image_small_src;?>';
  link_arr[<?php echo $index;?>]='<?php
  echo $image_large_src;?>';
<?php
    $index++;
  }
}
?>
var autoInterval = setInterval(changeImg,timeInterval);
function changeImg()
{
    var small_image=document.getElementById("small_image");
    var large_src =document.getElementById("large_src");
    if (curIndex==img_arr.length-1)
    {
        curIndex=0;
    }
    else
    {
        curIndex+=1;
    }
    small_image.src=img_arr[curIndex];
    large_src.value=link_arr[curIndex];
    if(document.getElementById("small_image_left")){
      var small_image_left=document.getElementById("small_image_left");
      var large_src_left =document.getElementById("large_src_left");
      var lastIndex = img_arr.length-1;
      if(curIndex==0)
      {
        lastIndex=img_arr.length-1;
      }
      else
      {
        lastIndex=curIndex-1;
      }
      small_image_left.src=img_arr[lastIndex];
      large_src_left.value=link_arr[lastIndex];
    }
    if(document.getElementById("small_image_right")){
      var small_image_right=document.getElementById("small_image_right");
      var large_src_right =document.getElementById("large_src_right");
      if(curIndex+1==img_arr.length)
      {
        nextIndex=0;
      }
      else
      {
        nextIndex=curIndex+1;
      }
      small_image_right.src=img_arr[nextIndex];
      large_src_right.value=link_arr[nextIndex];
    }
}
function getTop(){
  var img_height = document.getElementById("small_image").height;
  var image = document.getElementById("carousel_left_image");
  var top;
  if(img_height>image.height){
    top = (img_height-image.height)/2
  }else{
    top = 0;
  }
  document.getElementById("carousel-left").style.top = top+'px';
  document.getElementById("carousel-right").style.top = top+'px';
}
function next_img(){
    var small_image=document.getElementById("small_image");
    var large_src =document.getElementById("large_src");
    if (curIndex==img_arr.length-1)
    {
        curIndex=0;
    }
    else
    {
        curIndex+=1;
    }
    small_image.src=img_arr[curIndex];
    large_src.value=link_arr[curIndex];
    if(document.getElementById("small_image_left")){
      var small_image_left=document.getElementById("small_image_left");
      var large_src_left =document.getElementById("large_src_left");
      var lastIndex = img_arr.length-1;
      if(curIndex==0)
      {
        lastIndex=img_arr.length-1;
      }
      else
      {
        lastIndex=curIndex-1;
      }
      small_image_left.src=img_arr[lastIndex];
      large_src_left.value=link_arr[lastIndex];
    }
    if(document.getElementById("small_image_right")){
      var small_image_right=document.getElementById("small_image_right");
      var large_src_right =document.getElementById("large_src_right");
      if(curIndex+1==img_arr.length)
      {
        nextIndex=0;
      }
      else
      {
        nextIndex=curIndex+1;
      }
      small_image_right.src=img_arr[nextIndex];
      large_src_right.value=link_arr[nextIndex];
    }
clearInterval(autoInterval);
autoInterval = setInterval(changeImg,timeInterval);

}
function prev_img(){
    var small_image=document.getElementById("small_image");
    var large_src =document.getElementById("large_src");
    if (curIndex==0)
    {
        curIndex=img_arr.length-1;
    }
    else
    {
        curIndex-=1;
    }
    small_image.src=img_arr[curIndex];
    large_src.value=link_arr[curIndex];
    if(document.getElementById("small_image_left")){
      var small_image_left=document.getElementById("small_image_left");
      var large_src_left =document.getElementById("large_src_left");
      var lastIndex = img_arr.length-1;
      if(curIndex==0)
      {
        lastIndex=img_arr.length-1;
      }
      else
      {
        lastIndex=curIndex-1;
      }
      small_image_left.src=img_arr[lastIndex];
      large_src_left.value=link_arr[lastIndex];
    }
    if(document.getElementById("small_image_right")){
      var small_image_right=document.getElementById("small_image_right");
      var large_src_right =document.getElementById("large_src_right");
      if(curIndex+1==img_arr.length)
      {
        nextIndex=0;
      }
      else
      {
        nextIndex=curIndex+1;
      }
      small_image_right.src=img_arr[nextIndex];
      large_src_right.value=link_arr[nextIndex];
    }

clearInterval(autoInterval);
autoInterval = setInterval(changeImg,timeInterval);
}
</script>
<div class="carousel-container">
<div id="carousel">
<table align="center" width="100%"><tr>
<td width="30%">
<?php if(isset($default_img_small[count($default_img_small)-1])&&$default_img_small[count($default_img_small)-1]!=''){ ?>
<img id="small_image_left" onclick="fnCreate(document.getElementById('large_src_left').value)" src="<?php echo $default_img_small[count($default_img_small)-1];?>" height="<?php echo PRODUCT_INFO_IMAGE_HEIGHT/2;?>" border="0">
<input type="hidden" id="large_src_left" value="<?php echo $default_img_large[count($default_img_small)-1];?>">
<?php }?>
</td>
<td width="40%">
<img id="small_image" onclick="fnCreate(document.getElementById('large_src').value)" src="<?php echo $default_img_small[0];?>" height="<?php echo PRODUCT_INFO_IMAGE_HEIGHT;?>" border="0">
<input type="hidden" id="large_src" value="<?php echo $default_img_large[0];?>">
</td>
<td width="30%">
<img id="small_image_right" onclick="fnCreate(document.getElementById('large_src_right').value)" src="<?php echo $default_img_small[1];?>" height="<?php echo PRODUCT_INFO_IMAGE_HEIGHT/2;?>" border="0">
<input type="hidden" id="large_src_right" value="<?php echo $default_img_large[1];?>">
</td>
</tr></table>
</div>
<div id="carousel-left" onclick="prev_img()"><img id="carousel_left_image" src="banner/imag/carousel_back_normal.png" alt="picture"></div>
<div id="carousel-right" onclick="next_img()"><img id="carousel_right_image" src="banner/imag/carousel_next_normal.png" alt="picture"></div>
<?php 
/*
<div class="image_select_list">
<span class="black">x</span>
<span class="gray">x</span>
<span>x</span>
<span>x</span>
<span>x</span>
</div>
</div>
*/?>
<?php
}else{
  echo "<div >";
  $show_images = false;
  foreach($p_image_list as $p_list_src){
  if(file_exists3(DIR_WS_IMAGES.'products/'.$p_list_src)&&
      $p_list_src){
      echo "<div id='product-carousel'>";
      echo tep_image3(DIR_WS_IMAGES.'products/' . $p_list_src,
        $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT,
        'hspace="2" vspace="2" class="product-carousel-image"');
      echo "</div>";
    }
  }
  echo "</div>";
}
?>
<script language ="javascript" >
getTop();
</script>
