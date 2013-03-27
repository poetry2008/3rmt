<?php
class Random_code {
    var $hash;
    var $bgimages=array('cottoncandy.png','grass.png','ripple.png','silk.png','whirlpool.png',
                        'bubbles.png','crackle.png','lines.png','sand.png','snakeskin.png');
    var $font = 10;

/*---------------------------------
 功能：构造函数 
 参数：$len(int) 长度
 参数：$font(int) 字的大小
 参数：$bg(string) 背景图片路径 
 返回值：无
 --------------------------------*/
    function Random_code($len=6,$font=7,$bg=''){

        $this->hash = strtoupper(substr(md5(rand(0, 9999)),rand(0, 24),$len));
        $this->font = $font;

        if($bg && !is_dir($bg)){
            $this->bgimg=$bg;
        }else{ 
            $this->bgimg=rtrim($bg,'/').'/'.$this->bgimages[array_rand($this->bgimages, 1)];
        }
    }

/*---------------------------------
 功能：绘图 
 参数：无 
 返回值：无
 --------------------------------*/
    function getImage(){

        if(!extension_loaded('gd') || !function_exists('gd_info')) 
            return;

        $_SESSION['random_code'] =''; 

        list($w,$h) = getimagesize($this->bgimg);
        $x = round(($w/2)-((strlen($this->hash)*imagefontwidth($this->font))/2), 1);
        $y = round(($h/2)-(imagefontheight($this->font)/2));

        $img= imagecreatefrompng($this->bgimg);
        imagestring($img,$this->font, $x, $y,$this->hash,imagecolorallocate($img,0, 0, 0));

        Header ("content-type: image/png");
        imagepng($img);
        imagedestroy($img);
        $_SESSION['random_code'] = md5(strtolower($this->hash));
    }
}
?>
