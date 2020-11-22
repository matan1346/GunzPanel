<?php

class Signature
{
    private $SignName;
    private $Image;
    private $ImageFullName;
    private $ImageName;
    private $ImageExt;
    private $ImagePathBack = '';
    private $ImageAllowedExt = array();
    private $HoldingData = array();
    private $PasteImages = array();
    private $FunctExt;
    
    public function __construct($name, $Exts)
    {
        $this->SignName = $name;
        $this->ImageAllowedExt = $Exts;
    }
    
    public function setBackground($imagePath)
    {
        $this->Image = $imagePath;
        
        $getPos = strrpos($imagePath,'/');
        
        if($getPos !== FALSE)
        {
            $this->ImagePathBack = mb_substr($imagePath, 0, $getPos);
            $this->ImageFullName = mb_substr($imagePath, $getPos, (mb_strlen($imagePath)-$getPos));
        }
        else
            $this->ImageFullName = $imagePath;
        
        $getPos2 = strrpos($this->ImageFullName, '.');
        
        if($getPos2 !== FALSE)
        {
            $this->ImageName = mb_substr($this->ImageFullName, 0, $getPos2);
            $Ext = strtolower(mb_substr($this->ImageFullName, $getPos2+1, (mb_strlen($this->ImageFullName)-$getPos2)));
        }
        else
            die('Ext does not exist in the file: '.$imagePath);
        
        
        if(array_key_exists($Ext, $this->ImageAllowedExt))
            $this->FunctExt = $this->ImageAllowedExt[$Ext];
        else
            die('Ext is not allowed');
    }
    
    
    public function imagecreatefromfile($path, $user_functions = false)
    {
        $info = @getimagesize($path);
		
		if(!$info)
		{
			return false;
		}
		
		$functions = array(
			IMAGETYPE_GIF => 'imagecreatefromgif',
			IMAGETYPE_JPEG => 'imagecreatefromjpeg',
			IMAGETYPE_PNG => 'imagecreatefrompng',
			IMAGETYPE_WBMP => 'imagecreatefromwbmp',
			IMAGETYPE_XBM => 'imagecreatefromwxbm',
			);
		
		if($user_functions)
		{
			$functions[IMAGETYPE_BMP] = 'imagecreatefrombmp';
		}
		
		if(!$functions[$info[2]])
		{
			return false;
		}
		
		if(!function_exists($functions[$info[2]]))
		{
			return false;
		}
		
		return $functions[$info[2]]($path);
    }
    
    public function AddText($size, $angel, $x, $y, $color, $font, $text, $shadow = false)
    {
        $this->HoldingData[] = array(
        'size' => $size,
        'angel' => $angel,
        'x' => $x,
        'y' => $y,
        'color' => array_values($color),
        'font' => $font,
        'text' => $text,
        'shadow' => $shadow
        );
    }
    
    public function AddImage($path, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w = -1, $src_h = -1)
    {
        
        $image = $this->imagecreatefromfile($path);
        
        if($image === FALSE)
            return false;
        
        
        list($width, $height) = getimagesize($path);
        
        if($src_w < 0)
            $src_w = $width;
        if($src_h < 0)
            $src_h = $height;
        
        
        $this->PasteImages[] = array(
        'src' => $image,
        'dst_x' => $dst_x,
        'dst_y' => $dst_y,
        'src_x' => $src_x,
        'src_y' => $src_y,
        'dst_w' => $dst_w,
        'dst_h' => $dst_h,
        'src_w' => $src_w,
        'src_h' => $src_h,
        );
        
        //list($width, $height) = getimagesize($im2) or die("error34");
		//imagecopyresized($im, $src, 415, 15, 0, 0, 100, 100, $width, $height);
    }
    
    public function MakeImage()
    {
        $image = $this->imagecreatefromfile($this->Image);
        
        if($image === FALSE)
            return false;
        
        $colors = array('000' => imagecolorallocate($image, 0,0,0));
        
        foreach($this->HoldingData as $a)
        {
            if($a['shadow'] === true)
            {
                imagefttext($image, $a['size'], $a['angel'], $a['x']-0.5, $a['y']+0.5, $colors['000'], $a['font'], $a['text']);
            }
            
            
            if(!array_key_exists($a['color'][0].$a['color'][1].$a['color'][2], $colors))
                $colors[$a['color'][0].$a['color'][1].$a['color'][2]] = imagecolorallocate($image, $a['color'][0],$a['color'][1],$a['color'][2]);
            
            imagefttext($image, $a['size'], $a['angel'], $a['x'], $a['y'], $colors[$a['color'][0].$a['color'][1].$a['color'][2]], $a['font'], $a['text']);
        }
        
        foreach($this->PasteImages as $b)
        {
            imagecopyresized($image, $b['src'], $b['dst_x'], $b['dst_y'], $b['src_x'], $b['src_y'], $b['dst_w'], $b['dst_h'], $b['src_w'], $b['src_h']);
        }
        
        
        
        //echo $this->ImageExt;
        //header("Content-Type: image/".$this->ImageExt);
    	//${'image'.$this->ImageExt}($image);
        
        $func = 'image'.$this->FunctExt;
        header("Content-Type: image/".$this->FunctExt);
        $func($image);
    	imagedestroy($image);
        //die();
    }
}