<?php

class Register
{
    private $Register;
    private $GunZ;
    private $UserData = array();
    private $IsGenderString = true;
    private $register_allowed = true;
    private $register_succes = false;
    private $register_message = array();
    private $register_system_message = 'The System Didn\'t Find The ';
    
    public function __construct($GunZ, $data = array())
    {
        $this->Register = $GLOBALS['Register_Global_Vars'];
        $this->GunZ = $GunZ;
        foreach($data as $k => $v)
        {
            if(array_key_exists($k, $this->Register))
                $this->UserData[$k] = $v;
        }
            
    }
    
    public function setField($key, $val)
    {
        if(array_key_exists($key, $this->Register))
        {
            $this->UserData[$key] = $val;
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function getField($key)
    {
        if(array_key_exists($key, $this->UserData))
            return $this->UserData[$key];
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function setMessage($field, $value)
    {
        $this->register_message[$field][] = $value;
    }
    
    public function getMessages()
    {
        return $this->register_message;
    }
    
    public function getUserValues()
    {
        return $this->UserData;
    }
    
    
    public function IsNotEmpty($keysNotToCheck = array())
    {
        $flagNotEmpty = true;
        foreach($this->UserData as $key => $v)
        {
            if(!in_array($key, $keysNotToCheck))
                if(empty($v))
                {
                    $flagNotEmpty = false;
                    $this->register_message[$key][] = 'The `'.$key.'` is empty.';
                }
        }
        if(!$flagNotEmpty)
           $this->register_allowed = false;
        return $flagNotEmpty;
    }
    
    public function IsEquals($key1, $key2)
    {
        if(array_key_exists($key1, $this->UserData) && array_key_exists($key2, $this->UserData))
        {
            if(strcasecmp($this->UserData[$key1], $this->UserData[$key2]) != 0)
            {
                $this->register_allowed = false;
                $this->register_message[$key2][] = 'The `'.$key2.'` is not the same as `'.$key1.'`.';
                return false;
            }
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key1.'` And `'.$key2.'` Fields, Try Again Later';
        return false;
    }
    
    public function IsNumber($key)
    {
        if(array_key_exists($key, $this->UserData))
        {
            if(!is_numeric($this->UserData[$key]))
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` is not a number.';
                return false;
            }
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function IsValuesAllowed($keysNotToCheck = array())
    {
        $flagAllowed = true;
        foreach($this->UserData as $k => $v)
        {
            if(!in_array($k, $keysNotToCheck))
                if(!$this->IsAllowed($k))
                    $flagAllowed = false;
        }
        return $flagAllowed;
    }
    
    public function IsRegisterAllowed()
    {
        return $this->register_allowed;
    }
    
    public function IsNumberBetween($key, $options, $array = false)
    {
        if(!is_array($array))
            $array = $this->UserData;
        if(array_key_exists($key, $array))
        {
            $this->UserData[$key] = intval($this->UserData[$key]);
            $minF = array_key_exists('min', $options);
            $maxF = array_key_exists('max', $options);
            if($minF && $maxF && ($array[$key] < $options['min'] || $array[$key] > $options['max']))
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` is not between  '.$options['min'].'-'.$options['max'].' letters.';
                return false;
            }
            if($minF && $array[$key] < $options['min'])
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` has to be at least  '.$options['min'].' letters.';
                return false;
            }
            if($maxF && $array[$key] > $options['max'])
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` cannot pass over  '.$options['min'].' letters.';
                return false;
            }
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function IsLengthBetween($key, $options, $array = false)
    {
        if(!is_array($array))
            $array = $this->UserData;
        if(array_key_exists($key, $array))
        {
            $length = mb_strlen($array[$key]);
            $minF = array_key_exists('min', $options);
            $maxF = array_key_exists('max', $options);
            if($minF && $maxF && ($length < $options['min'] || $length > $options['max']))
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` is not between  '.$options['min'].'-'.$options['max'].' letters.';
                return false;
            }
            if($minF && $length < $options['min'])
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` has to be at least  '.$options['min'].' letters.';
                return false;
            }
            if($maxF && $length > $options['max'])
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` cannot pass over  '.$options['min'].' letters.';
                return false;
            }
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function IsAllowed($key, $array = false)
    {
        if(!is_array($array))
            $array = $this->UserData;
        if(array_key_exists($key, $array))
        {
            $regex = $this->Register[$key]['regex'];
            if(preg_match('#'.$regex['statement'].'#', $array[$key]))
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The `'.$key.'` can contains only  `'.$regex['contains'].'` letters.';
                return false;
            }
            return true;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function IsExists($key)
    {
        $array = $this->UserData;
        if(array_key_exists($key, $array))
        {
            $query_str = 'SELECT TOP 1 1 FROM Account WHERE '.$this->Register[$key]['DatabaseField'].' = :keyvalue';
            $query = $this->GunZ->prepare($query_str);
            $query->bindParam(':keyvalue', $this->UserData[$key], PDO::PARAM_STR);
            $query->execute();
            
            if($a = $query->fetch())
            {
                $this->register_allowed = false;
                $this->register_message[$key][] = 'The '.$key.' `'.$this->UserData[$key].'` is already  exist.';
                return true;
            }
            return false;
        }
        $this->register_allowed = false;
        $this->register_message['System'][] = $this->register_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function Register()
    {
        //Check if Gender is string
        if($this->IsGenderString)
        {
            if($this->UserData['Gender'] == 1)
                $this->UserData['Gender'] = 'Female';
            else
                $this->UserData['Gender'] = 'Male';
        }
        
        
        //Account Table
        $AccountFields = 'UserID,UGradeID,PGradeID,RegDate,Name,Email,Age,Sex,ServerID,SQ,SA';
        $AccountValues = ":userid,'0','0',GETDATE(),:name,:email,:age,:gender,'0',:secret_question,:secret_answer";
        $query = 'INSERT INTO Account ('.$AccountFields.') VALUES ('.$AccountValues.')';
        //echo $query.'<br />';
        $AccountQuery = $this->GunZ->prepare($query);
        $AccountQuery->bindParam(':userid', $this->UserData['Username'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':name', $this->UserData['Name'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':email', $this->UserData['Email'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':age', $this->UserData['Age'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':gender', $this->UserData['Gender'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':secret_question', $this->UserData['Secret_Question'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':secret_answer', $this->UserData['Secret_Answer'], PDO::PARAM_STR);
        $AccountQuery->execute();
        
        
        
        $queryAID = $this->GunZ->prepare('SELECT TOP 1 AID FROM Account WHERE UserID = :userid');
        $queryAID->bindParam(':userid', $this->UserData['Username'], PDO::PARAM_STR);
        $queryAID->execute();
        
        $a = $queryAID->fetch(PDO::FETCH_ASSOC);
        
        $last_insert_id = $a['AID'];
        
        
        //Login Table
        $LoginFields = 'UserID,AID,Password';
        $LoginValues = ":userid,:aid,:password";
        $query = 'INSERT INTO Login ('.$LoginFields.') VALUES ('.$LoginValues.')';
        //echo $query;
        $LoginQuery = $this->GunZ->prepare($query);
        $LoginQuery->bindParam(':userid', $this->UserData['Username'], PDO::PARAM_STR);
        $LoginQuery->bindParam(':aid', $last_insert_id, PDO::PARAM_INT);
        $LoginQuery->bindParam(':password', $this->UserData['Password'], PDO::PARAM_STR);
        $LoginQuery->execute();
        $this->register_succes = true;
        $this->register_message['System'][] = 'Congratulations '.$this->UserData['Name'].'! Your Account is registered to our system.';
        return true;
    }
}