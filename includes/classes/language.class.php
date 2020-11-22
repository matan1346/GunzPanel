<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

class Language
{
    private $LangType;
    private $langXPathes = array();
    private $TextList = array();
    private static $NO_MSG = 'NO_MSG';
    
    public function __construct($lang = null, $newSession = false)
    {
        global $_CONFIG; 
          
        if($lang == null) {
            if(!isset($_SESSION['lang'])) {
                $lang = $_CONFIG['LANG']['default'];
            }
            else{
                $lang = $_SESSION['lang'];
            }
        } else {
            if(!isset($_CONFIG['LANG'][$lang])) {
                $lang = $_CONFIG['LANG']['default'];
            }
        }
        
        if($newSession)
            $_SESSION['lang'] = $lang; 
        
        $this->LangType = $lang;
    }
    
    
    public function getAutoString($path_to_text)
    {
        $data_path = explode('/', $path_to_text);
        //echo 'lol';
        
        //print_r($path_array);
        //die();
        $TextList = $this->TextList;
        
        foreach($data_path as $value)
        {
            if(!array_key_exists($value, $TextList))
                return false;
            $TextList = $TextList[$value];
        }
        //rint_r($path_array);
        //echo 'YAY!';
        return $TextList;
    }
    
    function set_testlist($key, $value) {
      //global $config;
    
      if (FALSE=== ($levels=explode('/',$key)))
        return;
    
      $pointer = &$this->TextList;
      for ($i=0; $i<sizeof($levels); $i++) {
        if (!isset($pointer[$levels[$i]]))
          $pointer[$levels[$i]]=array();
        $pointer=&$pointer[$levels[$i]];
      } // for
    
      $pointer=$value;
    } // set_config
    
    public function getString($xml_file,$strName, $strCat = null, $retDefault = true) {
        
        $xml_file = trim(strtolower($xml_file));
        
        $path_text = $xml_file . '/' . (is_string($strCat) ? $strCat . '/' : '') . $strName;
        if(!array_key_exists($xml_file, $this->langXPathes))
        {
            $langDoc = new DOMDocument();
            $langDoc->load(SITE_ROOT . 'public/lang/' . $this->LangType . '/' . $xml_file . '.xml');
            $this->langXPathes[$xml_file] = new DOMXPath($langDoc);
        }
        else
        {
            $result = $this->getAutoString($path_text);
            
            if($result !== false)
                return $result;
        }
        
        
        
        $searchQuery = '/str[@name=\'' . $strName . '\']';
        if($strCat) {
            $searchQuery = '/' . $strCat . $searchQuery;
        } $searchQuery = '/' . $searchQuery;
        
        //echo '['.$searchQuery.']<br />';
        $resNodes = $this->langXPathes[$xml_file]->query($searchQuery);
        if($resNodes->length == 0) {
            if($retDefault) {
                return ($strName != 'STR_NOT_FOUND' ? $this->getString('main','STR_NOT_FOUND', 'lang/error') : new TextDisplay(self::$NO_MSG));
            } else {
                return false;
            }
        }
        
        $returnNormal = $resNodes->item(0)->nodeValue;
        
        
        
        if(preg_match_all('/\{([^}]+)\}/',$returnNormal, $matches))
        {
            //print_r($matches);
            foreach($matches[1] as $match)
            {
                
                //$Path_string = preg_replace('#[\{\[\}\]\ ]#', '', $match);
                
                //$getData = explode('',$match[0])
                
                $ResultMatch = $this->getLangPath($match);
                //echo $match.'<br />';
                //print_r($match);
                if($ResultMatch['Done'])
                {
                    $lol = $this->getString($ResultMatch['xml_file'], $ResultMatch['strName'], $ResultMatch['CatName']);
                    //print_r($lol);
                    $returnNormal = str_replace('{'.$match.'}', $lol->getText(), $returnNormal);
                    
                }
                else
                {
                    //echo 'fail';
                }
            }
            //echo '<br />s'.$returnNormal;
            //$matches[0] = preg_replace('#[\{\[\}\]\ ]#', '', $matches[0]);
            //print_r($matches);
        }
        //var_dump($returnNormal);
        
        $return = new TextDisplay($returnNormal);
        
        $this->set_testlist($path_text, $return);
        
        //print_r($this->TextList);
        
        return $return;//new TextDisplay($resNodes->item(0)->nodeValue);
    }
    
    public function getLangPath($string_path)
    {
        $data = explode('/', $string_path);
        
        //print_r($data);
        
        $return = array('Done' => false);
        
        //echo $string_path.'<br />';
        //var_dump($data);
        if(is_array($data) && sizeof($data) > 1)
        {
            $size = sizeof($data);
            $return = array(
            'Done' => true,
            'xml_file' => $data[0],
            'strName' => $data[$size - 1],
            'CatName' => array()
            );
            
            for($i = 1;$i < $size - 1;$i++)
                $return['CatName'][] = $data[$i];
            //print_r($return['CatName']);
            
            $return['CatName'] = implode('/', $return['CatName']);
        }
        return $return;
    }
    
    static function setStr($lang_type, $file_name, $str_name, $new_value)
    {
        $XmlFile = SITE_ROOT . 'public/lang/'.$lang_type.'/'.$file_name.'.xml';
        //echo $XmlFile.'<br />';
        if(file_exists($XmlFile))
        {
            //echo 'tru!';
            $xmlFileLoad = simplexml_load_file($XmlFile);
            //print_r($xmlFileLoad->buttons->str['@attributes']);
            //echo $XmlFile;
            $getStr = $xmlFileLoad->xpath('//*[@name="'.$str_name.'"]');
            
            $getStr[0][0] = $new_value;
            //$xmlFileLoad->asXml($XmlFile);
            
            
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom_xml = dom_import_simplexml($xmlFileLoad);
            $dom_xml = $dom->importNode($dom_xml, true);
            $dom_xml = $dom->appendChild($dom_xml);
            // DOMDocument method for saving XML file
            $dom->save($XmlFile);
            
            /*
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom_sxe = $dom->importNode($xmlFileLoad, true);
            //$dl = $dom->load($XmlFile); // remove error control operator (@) to print any error message generated while loading.
            if ( !$dl ) die('Error while parsing the document: ' . $XmlFile);
            $dom_sxe->save($XmlFile);
            */
            
            /*
            //echo $str_name;
            //if(!is_object($getAtions))
              //  return NULL;
                //echo 'asd';
                //var_dump($getAtions[0]);
              //var_dump($getAtions);
            $Actions = $getAtions[0]->attributes();
            
            if(!empty($getAtions[0]))
                return (array)$getAtions[0];
            
            return NULL;
            //print_r($getAtions[0]->toString());
            
            /*foreach($getAtions[0]->attributes() as $a => $b) {
            echo $a,'="',$b,"\"\n";
        }*/
        
            
            
            
            
            //$XmlElement = new SimpleXMLElement($xmlFileLoad->);
            //var_dump($XmlElement);
        }
        else
        {
            //echo $XmlFile.' ';
        }
    }
    
    static function getStr($lang_type, $file_name, $str_name)
    {
        $XmlFile = SITE_ROOT . 'public/lang/'.$lang_type.'/'.$file_name.'.xml';
        if(file_exists($XmlFile))
        {
            $xmlFileLoad = simplexml_load_file($XmlFile);
            //print_r($xmlFileLoad->buttons->str['@attributes']);
            //echo $XmlFile;
            $getAtions = $xmlFileLoad->xpath('//*[@name="'.$str_name.'"]');
            //echo $str_name;
            //if(!is_object($getAtions))
              //  return NULL;
                //echo 'asd';
                //var_dump($getAtions[0]);
              //var_dump($getAtions);
            $Actions = $getAtions[0]->attributes();
            
            if(!empty($getAtions[0]))
                return (array)$getAtions[0];
            
            return NULL;
            //print_r($getAtions[0]->toString());
            
            /*foreach($getAtions[0]->attributes() as $a => $b) {
            echo $a,'="',$b,"\"\n";
        }*/
        
            
            
            
            
            //$XmlElement = new SimpleXMLElement($xmlFileLoad->);
            //var_dump($XmlElement);
        }
        else
        {
            //echo $XmlFile.' ';
        }
    }
    
    static function getLangsOfString($strName)
    {
        $LanguagesList = self::getLangList();
        
        foreach($LanguagesList as $key => $value)
        {
            $LanguagesList[$key]['type'] = $key;
            $LanguagesList[$key]['translatedTarget'] = Language::getStr($key,'select_options', $strName);
        }
        
        return array_values($LanguagesList);
    }
    
    static function setStringOfLang($strName, $lang_data)
    {
        $LanguagesList = $GLOBALS['_CONFIG']['LANG'];
        unset($LanguagesList['default']);
        
        foreach($LanguagesList as $key => $value)
        {
            //echo $strName.':'.$lang_data[$key];
            if(array_key_exists($key, $lang_data))
                Language::setStr($key, 'select_options', $strName, $lang_data[$key]);
        }
        
        
    }
    
    static function getLangList()
    {
        $LanguagesList = $GLOBALS['_CONFIG']['LANG'];
        
        $LangEn = new Language($LanguagesList['default']);
        unset($LanguagesList['default']);
        
        
        foreach($LanguagesList as $key => $value)
        {
            $LangSpecific = new Language($key);
            
            $LanguagesList[$key]['lang'] = array(
                'en' => array(
                'Country' => $LangEn->getString('langlist','COUNTRY', 'list/'.$key, true)->getText(),
                'Language' => $LangEn->getString('langlist','LANGUAGE', 'list/'.$key, true)->getText()
                ),
                $key => array(
                'Country' => $LangSpecific->getString('langlist','COUNTRY', 'list/'.$key, true)->getText(),
                'Language' => $LangSpecific->getString('langlist','LANGUAGE', 'list/'.$key, true)->getText()
                )
            );
            
            
        }
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
        return $this->getString('TEXT_ALIGN', 'langspec')->getText();
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

?>