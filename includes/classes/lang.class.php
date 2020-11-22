<?php

class Language2
{
    private $LangType;
    private $langXPath;
    private $LangXmlDoc;
    private static $NO_MSG = 'NO_MSG';
    
    public function __construct($lang = null)
    {
        global $_CONFIG;
            
        if($lang == null) {
            if(!isset($_SESSION['lang'])) {
                $_SESSION['lang'] = $_CONFIG['LANG']['default'];
            }
        } else {
            if(!isset($_CONFIG['LANG'][$lang])) {
                $_SESSION['lang'] = $_CONFIG['LANG']['default'];
            } else {
                $_SESSION['lang'] = $lang;
            }
        }
        
        //$this->lang = $_SESSION['lang'];
        $this->LangType = $_SESSION['lang'];
        
        $this->langDoc = new DOMDocument();
        $this->langDoc->load(SITE_ROOT . 'public/lang/' . $_CONFIG['LANG'][$this->LangType]['file']);
        $this->langXPath = new DOMXPath($this->langDoc);
        
        /*if(is_null($lang) && isset($_SESSION['LANG']))
            $lang = $_SESSION['LANG'];
            
        if(is_null($lang))
            $lang = 'en';
            
        $this->LangType = $lang;
            
        $xml_path = 'public/lang/'.$lang.'.xml';
        if(!is_file($xml_path))
            return false;
            
        $this->LangXmlDoc = new DOMDocument();
        $this->LangXmlDoc->load($xml_path);
        $this->langXPath = new DOMXPath($this->LangXmlDoc);
        */
    }
    
    public function getString($strName, $strCat = null, $retDefault = true, $LangPath = false) {
        $searchQuery = '/str[@name=\'' . $strName . '\']';
        if($strCat) {
            $searchQuery = '/' . $strCat . $searchQuery;
        } $searchQuery = '/' . $searchQuery;
        
        //echo '['.$searchQuery.']<br />';
        
        if($LangPath instanceof DOMXPath)
            $langXPath = $LangPath;
        else
            $langXPath = $this->langXPath;
        
        $resNodes = $langXPath->query($searchQuery);
        if($resNodes->length == 0) {
            if($retDefault) {
                return ($strName != 'STR_NOT_FOUND' ? $this->getString('STR_NOT_FOUND', 'error') : self::$FATAL_NO_STR);
            } else {
                return false;
            }
        }
            
        return $resNodes->item(0)->nodeValue;
    }
    
    public function getLangList()
    {
        
        $LanguagesList = $GLOBALS['_CONFIG']['LANG'];
        unset($LanguagesList['default']);
        
        $Path = SITE_ROOT . 'public/lang/langlist/';
        
        $langDoc = new DOMDocument();
        
        $langDoc->load($Path . $LanguagesList['en']['file']);
        
        $EnLangPath = new DOMXPath($langDoc);
        
        
        foreach($LanguagesList as $key => $value)
        {
            $langDoc->load($Path . $LanguagesList[$key]['file']);
            $langXPath = new DOMXPath($langDoc);
            
            $LanguagesList[$key]['lang'] = array(
                'en' => array(
                'Country' => $this->getString('COUNTRY', 'list/'.$key, true, $EnLangPath),
                'Language' => $this->getString('LANGUAGE', 'list/'.$key, true, $EnLangPath)
                ),
                $key => array(
                'Country' => $this->getString('COUNTRY', 'list/'.$key, true, $langXPath),
                'Language' => $this->getString('LANGUAGE', 'list/'.$key, true, $langXPath)
                )
            );
            
            
        }
        //print_r($LanguagesList);
        return $LanguagesList;
    }
        
    public function directionHTML() {
        $directionStr = $this->getString('DIRECTION', 'langspec');
        
        return '
            <style>
                * {
                    direction: ' . $directionStr . ';
                }
            </style>
        ';
    }
    
    public function getAlignText() {
        return $this->getString('TEXT_ALIGN', 'langspec');
    }
        
    public function panelHTML() {
        global $_CONFIG;
        
        $retHTML = '';
        foreach($_CONFIG['LANG'] as $langKey => $langXml) {
            if($langKey == 'default') continue;
            
            $retHTML .= '<a href="?lang=' . $langKey . '"><img alt="" src="/img/flags/' . $langKey . '.png" /></a>';
        }
        
        return $retHTML;
    }
    
    
    public function isLang()
    {
        if(func_num_args() <= 0)
            return false;
            
        $Args = func_get_args();
        foreach($Args as $lang)
            if($lang == $this->LangType)
                return true;
        return false;
    }
}