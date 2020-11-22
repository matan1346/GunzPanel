<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

class TextDisplay
{
    private $Text;
    private $TextReplace;
    private $LastReplaceArgs = array();
    
    public function __construct($Text)
    {
        $this->Text = $Text;
    }
    
    public function replace($Data)
    {
        $Data['newline'] = '<br />';
        
        //print_r($Data);
        $Start = '#{{ ';
        $End = ' }}#';
        
        $patterns = array();
        $replcement = array();
        
        foreach($Data as $k => $v)
        {
            $patterns[] = $Start.$k.$End;
            $replcement[] = $v;
            //echo $v.' ';
        }
        //print_r($patterns);
        //print_r($replcement);
        
        //var_dump($this->Text);
        
        $this->TextReplace = preg_replace($patterns, $replcement, $this->Text);
     
        return $this;
    }
    
    public function getText($Replace = false)
    {
        return ($Replace) ? $this->TextReplace : $this->Text;
    }
    
    public function setText($text)
    {
        $this->Text = $text;
        return $this;
    }
}

?>