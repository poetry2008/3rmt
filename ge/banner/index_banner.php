<?php 
$array = array();
$adv_count = 0;
$adv_sql = "select `banners_title`, `banners_url`, `banners_image` from ".TABLE_BANNERS." where banners_group like 'adv%' 
and site_id ='".SITE_ID."' and status ='1' ";
$adv_query = tep_db_query($adv_sql);
while($adv_row = tep_db_fetch_array($adv_query)){
  if(isset($adv_row['banners_image'])&&$adv_row['banners_image'] !=''
        &&file_exists(DIR_WS_IMAGES.$adv_row['banners_image'])){
    $array[] = $adv_row;
    $adv_count++;
  }
}
if($adv_count>1){
  ?>
    <div class="index_js">
    <div class="divCarouselInfoLt">
    <span style="float:left; position:absolute; left:10%;"><img src="banner/imag/carousel_back_normal.png" alt="previous"  class="prev" ></span>
    <span style="float:right; position:absolute; right:10%;"><img src="banner/imag/carousel_next_normal.png" alt="next" class="next"></span>
    </div>
    </div>
    <?php
}
?>    
<div class="img-scroll">
<div class="img-list">
<ul>
<?php
$adv_count = 0;
foreach($array as $adv_row){         
    if(isset($adv_row['banners_image'])&&$adv_row['banners_image'] !=''
        &&file_exists(DIR_WS_IMAGES.$adv_row['banners_image'])){
      $adv_count++;
    ?>
    <li><a href="<?php echo $adv_row['banners_url']; ?>"><img src="images/<?php echo
    $adv_row['banners_image'];?>" alt="picture"></a></li>
    <?php
    }
} 
if($adv_count==0){
?>
  <li><img style="display:none" alt="none"></li>
<?php } ?>
</ul>
</div>
</div>
  <script type="text/javascript">
function DY_scroll(wraper,prev,next,img,speed,or)
{ 
  var wraper = $(wraper);
  var prev = $(prev);
  var next = $(next);
  var img = $(img).find('ul');
  var w = img.find('li').outerWidth(true);
  var s = speed;
  next.click(function()
      {
      img.animate({'margin-left':-w},function()
        {
        img.find('li').eq(0).appendTo(img);
        img.css({'margin-left':0});
        });
      });
  prev.click(function()
      {
      img.find('li:last').prependTo(img);
      img.css({'margin-left':-w});
      img.animate({'margin-left':0});
      });
  if (or == true)
  {
    ad = setInterval(function() { next.click();},s*1000);
    wraper.hover(function(){clearInterval(ad);},function(){ad = setInterval(function() { next.click();},s*1000);});
  }
}
DY_scroll('.img-scroll','.prev','.next','.img-list',4,true);
</script>
