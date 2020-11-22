<?php

class Form
{
    protected $GunZ;
    private $Messages;
    private $_Fields;
    protected $Lang;
    private $FieldsData = array();
    private $SystemMessage = array();
    private $NoErrorsFlag = true;
    
    
    public function __construct($GunZ)
    {
        $this->GunZ = $GunZ;
        $this->Lang = $GLOBALS['Lang'];
        //$this->_Fields = $GLOBALS['Register_Global_Vars'];
        $this->Messages = $GLOBALS['Form_Errors'];
    }
    
    public function getField($key, $MODE = DATA)// DATA/TYPE/VALUE
    {
        $MODE = ucfirst(strtolower($MODE));
        if(array_key_exists($key, $this->FieldsData))
            return  (array_key_exists($MODE, $this->FieldsData[$key]) ? $this->FieldsData[$key][$MODE] : $this->FieldsData[$key]);
        return false;
    }
    
    public function setField($keyName, $keyType, $val, $regex = '[^0-9a-zA-Z\_]', $DB = array('Table' => null,'Column' => null), $DefaultDisplay = array())
    {
        $Display = array('xml_file' => 'actions','strName' => 'FORM_FIELD','catName' => 'actions/form/default');
        
        foreach($DefaultDisplay as $k => $v)
        {
            $Display[$k] = $v;
        }
        //print_r($Display);
        $this->FieldsData[$keyName] = array(
            'Type' => $keyType,
            'Value' => $val,
            'Regex' => $regex,
            'Db' => $DB,
            'Display' => $this->Lang->getString($Display['xml_file'], $Display['strName'], $Display['strCat'])->getText());
        return true;
    }
    
    public function setFormMessage($key, $strName, $strCat = null, $data = array())
    {
        
        $this->SystemMessage[$key][] = $this->Lang->getString('form_errors', $strName, $strCat)->replace($data)->getText(true);
        return $this;
    }
    
    public function EqualsTo($key)
    {
        if(array_key_exists($key, $this->FieldsData) && func_num_args() > 1)
        {
            $args = func_get_args();
            unset($args[0]);
            foreach($args as $val)
                if($this->FieldsData[$key]['Value'] === $val)
                    return true;
            //return ($this->FieldsData[$key]['Value'] === $value);
        }
        return false;
    }
    
    public function setError($errorPath,$key,$data = array())
    {
        $ErrorKeys = explode('/', $errorPath);
        $Error = $this->Messages;
        for($i = 0,$size = sizeof($ErrorKeys);$i < $size;$i++)
            if(array_key_exists($ErrorKeys[$i], $Error))
                $Error = $Error[$ErrorKeys[$i]];
        if(!is_string($Error))
            $Error = '';
        
        if(is_array($data) && sizeof($data) > 0)
        {
            $Start = '#{{ ';
            $End = ' }}#';
            
            $patterns = array();
            $replcement = array();
            
            foreach($data as $k => $v)
            {
                $patterns[] = $Start.$k.$End;
                $replcement[] = $v;
            }
            $Error = preg_replace($patterns, $replcement, $Error);
        }
        
        $this->SystemMessage[$key][] = $Error;
        return $this;
    }
    
    public function setMessage($key, $val)
    {
       $this->SystemMessage[$key][] = $val; 
    }
    
    public function TranslateSystemMessages($keys = array('SYSTEM','DATA','VALID'))
    {
        //print_r($this->SystemMessage);
        foreach($keys as $key)
        {
            //echo 'ol';
            if(array_key_exists($key, $this->SystemMessage))
            {
                $temp = $this->SystemMessage[$key];
                $this->SystemMessage[$key] = array(
                    'Translated' => $this->Lang->getString('form_errors', $key.'_MSG', 'form_errors/form_system_messages')->getText(),
                    'List' => $temp
                );
                //echo $this->SystemMessage[$key]['Translated'];
                //echo 'hey';
            }
        }
            
    }
    
    public function getSystemMessages()
    {
        return $this->SystemMessage;
    }
    
    public function getFieldsValues()
    {
        $myData = array();
        foreach($this->FieldsData as $key => $value)
            $myData[$key] = $value['Value'];
        
        return $myData;
    }
    
    public function IsNotEmpty($keysNotToCheck = array())
    {
        $flagNotEmpty = true;
        foreach($this->FieldsData as $key => $v)
        {
            if(!in_array($key, $keysNotToCheck))
                if(empty($v['Value']))
                {
                    $flagNotEmpty = false;
                    $this->setFormMessage($key, 'EMPTY', 'form_errors', array('key' => $key));
                    //$this->setError('Fields/IsNotEmpty/Empty', $key, array('key' => $key));
                }
        }
        if(!$flagNotEmpty)
           $this->NoErrorsFlag = false;
        return $flagNotEmpty;
    }
    
    public function IsEquals($key1, $key2)
    {
        if(array_key_exists($key1, $this->FieldsData) && array_key_exists($key2, $this->FieldsData))
        {
            if(strcasecmp($this->FieldsData[$key1]['Value'], $this->FieldsData[$key2]['Value']) != 0)
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'NOT_SAME', 'form_errors', array('key1' => $key1,'key2' => $key12));
                //$this->setError('Fields/IsEquals/NotSame', $key2, array('key1' => $key1,'key2' => $key2));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('keys' => $key1.' and '.$key2));
        //$this->setError('Fields/NotFound', $key2, array('keys' => $key1.'` and `'.$key2));
        return false;
    }
    
    public function IsNumber($key)
    {
        if(array_key_exists($key, $this->FieldsData))
        {
            if(!is_numeric($this->FieldsData[$key]['Value']))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'NOT_NUMBER', 'form_errors', array('key' => $key));
                //$this->setError('Fields/IsNumber/NotNumber', $key, array('key' => $key));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
    
    public function IsValuesAllowed($keysNotToCheck = array())
    {
        $flagAllowed = true;
        foreach($this->FieldsData as $k => $v)
        {
            if(!in_array($k, $keysNotToCheck))
                if(!$this->IsAllowed($k))
                    $flagAllowed = false;
        }
        return $flagAllowed;
    }
    
    public function NoErrors()
    {
        return $this->NoErrorsFlag;
    }
    
    public function setFlagError($flag)
    {
        $this->NoErrorsFlag = !$flag;
    }
    
    public function IsNumberBetween($key, $options, $array = false)
    {
        if(!is_array($array))
            $array = $this->FieldsData;
        if(array_key_exists($key, $array))
        {
            $this->FieldsData[$key]['Value'] = intval($this->FieldsData[$key]['Value']);
            $minF = array_key_exists('min', $options);
            $maxF = array_key_exists('max', $options);
            if($minF && $maxF && ($array[$key]['Value'] < $options['min'] || $array[$key]['Value'] > $options['max']))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'NOT_BETWEEN', 'form_errors', array('key' => $key,'min' => $options['min'],'max' => $options['max']));
                //$this->setError('Fields/IsNumberBetween/NotBetween', $key, array('key' => $key,'min' => $options['min'],'max' => $options['max']));
                return false;
            }
            if($minF && $array[$key]['Value'] < $options['min'])
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'MIN', 'form_errors', array('key' => $key,'min' => $options['min']));
                //$this->setError('Fields/IsNumberBetween/Min', $key, array('key' => $key,'min' => $options['min']));
                return false;
            }
            if($maxF && $array[$key]['Value'] > $options['max'])
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'MAX', 'form_errors', array('key' => $key,'max' => $options['max']));
                //$this->setError('Fields/IsNumberBetween/Max', $key, array('key' => $key,'max' => $options['max']));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
    
    public function IsLengthBetween($key, $options, $array = false)
    {
        if(!is_array($array))
            $array = $this->FieldsData;
        if(array_key_exists($key, $array))
        {
            $length = mb_strlen($array[$key]['Value']);
            $minF = array_key_exists('min', $options);
            $maxF = array_key_exists('max', $options);
            if($minF && $maxF && ($length < $options['min'] || $length > $options['max']))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'NOT_BETWEEN', 'form_errors', array('key' => $key,'min' => $options['min'],'max' => $options['max']));
                //$this->setError('Fields/IsLengthBetween/NotBetween', $key, array('key' => $key,'min' => $options['min'],'max' => $options['max']));
                return false;
            }
            if($minF && $length < $options['min'])
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'MIN', 'form_errors', array('key' => $key,'min' => $options['min']));
                //$this->setError('Fields/IsLengthBetween/Min', $key, array('key' => $key,'min' => $options['min']));
                return false;
            }
            if($maxF && $length > $options['max'])
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'MAX', 'form_errors', array('key' => $key,'max' => $options['max']));
                //$this->setError('Fields/IsLengthBetween/Max', $key, array('key' => $key,'max' => $options['max']));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
    
    public function IsAllowed2($key, $array = false)
    {
        if(!is_array($array))
            $array = $this->FieldsData;
        if(array_key_exists($key, $array))
        {
            $regex = $this->_Fields[$this->FieldsData[$key]['Type']]['regex'];
            if(preg_match('#'.$regex['statement'].'#', $array[$key]['Value']))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'EMPTY', 'form_errors/regex', array('key' => $key));
                //$this->setError('Fields/IsAllowed/NotAllowed', $key, array('key' => $key,'contains' => $regex['contains']));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
    
    public function IsAllowed($key, $array = false)
    {
        if(!is_array($array))
            $array = $this->FieldsData;
        if(array_key_exists($key, $array))
        {
            //$regex = $this->_Fields[$this->FieldsData[$key]['Type']]['regex'];
            //echo $array[$key]['Regex'].'<br />';
            if(preg_match('#'.$array[$key]['Regex'].'#', $array[$key]['Value']))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'REGEX_'.strtoupper($array[$key]['Type']), 'form_errors/regex', array('key' => $array[$key]['Display']));//$key));
                //$this->setError('Fields/IsAllowed/NotAllowed', $key, array('key' => $key,'contains' => $regex['contains']));
                return false;
            }
            return true;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
    
    public function IsExists($key)
    {
        $FieldsArray = $this->FieldsData;
        if(array_key_exists($key, $FieldsArray))
        {
            if($this->GunZ->IfRowExists('Account', array($this->_Fields[$FieldsArray[$key]['Type']]['DatabaseField'] => ':keyvalue'), array(':keyvalue' => $FieldsArray[$key]['Value'])))
            {
                $this->NoErrorsFlag = false;
                $this->setFormMessage($key, 'EXISTS', 'form_errors', array('key' => $key,'keyvalue' => $FieldsArray[$key]['Value']));
                //$this->setError('Fields/IsExists/Exists', $key, array('key' => $key,'keyvalue' => $FieldsArray[$key]['Value']));
                return true;
            }
            return false;
        }
        $this->NoErrorsFlag = false;
        $this->setFormMessage($key, 'NOT_FOUND', 'form_errors', array('key' => $key));
        //$this->setError('Fields/NotFound', $key, array('key' => $key));
        return false;
    }
}