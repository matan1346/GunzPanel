<?php

class Login
{
    private $Login;
    private $GunZ;
    private $UserData = array();
    private $login_allowed = true;
    private $login_succes = false;
    private $UserAID = array();
    private $login_message = array();
    private $login_system_message = 'The System Didn\'t Find The ';
    
    public function __construct($GunZ, $data = array())
    {
        $this->Login = $GLOBALS['Login_Global_Vars'];
        $this->GunZ = $GunZ;
        foreach($data as $k => $v)
        {
            if(array_key_exists($k, $this->Login))
                $this->UserData[$k] = $v;
        }
    }
    
    public function setField($key, $val)
    {
        if(array_key_exists($key, $this->Login))
        {
            $this->UserData[$key] = $val;
            return true;
        }
        $this->login_allowed = false;
        $this->login_message['System'][] = $this->login_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function getField($key)
    {
        if(array_key_exists($key, $this->UserData))
            return $this->UserData[$key];
        $this->login_allowed = false;
        $this->login_message['System'][] = $this->login_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    public function IsLoginAllowed()
    {
        return $this->login_allowed;
    }
    
    public function getMessages()
    {
        return $this->login_message;
    }
    
    public function getUserAID()
    {
        return $this->UserAID;
    }
    
    public function IsNotEmpty()
    {
        $flagNotEmpty = true;
        foreach($this->UserData as $key => $v)
        {
            if(empty($v))
            {
                $flagNotEmpty = false;
                $this->login_message[$key][] = 'The `'.$key.'` is empty.';
            }
        }
        if(!$flagNotEmpty)
           $this->login_allowed = false;
        return $flagNotEmpty;
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
    
    public function IsAllowed($key, $array = false)
    {
        if(!is_array($array))
            $array = $this->UserData;
        if(array_key_exists($key, $array))
        {
            $regex = $this->Login[$key]['regex'];
            if(preg_match('#'.$regex['statement'].'#', $array[$key]))
            {
                $this->login_allowed = false;
                $this->login_message[$key][] = 'The `'.$key.'` can contains only  `'.$regex['contains'].'` letters.';
                return false;
            }
            return true;
        }
        $this->login_allowed = false;
        $this->login_message['System'][] = $this->login_system_message.' `'.$key.'` Field, Try Again Later';
        return false;
    }
    
    
    public function Login()
    {
        /*$query_str = 'SELECT Account.UserID AS UserID,Account.AID AS AID,Account.UGradeID AS UGradeID,Account.Name AS Name,Account.Email AS Email,Account.RegDate AS RegDate,Account.SQ AS SQ,Account.SA AS SA,Account.Coins AS Coins FROM Account
         LEFT JOIN Login ON Account.AID = Login.AID WHERE Login.UserID = :userid AND Login.Password = :password';
         */
         $query_str = 'SELECT AID FROM Login WHERE UserID = :userid AND Password = :password';
        $LoginQuery = $this->GunZ->prepare($query_str);
        $LoginQuery->bindParam(':userid', $this->UserData['Username'], PDO::PARAM_STR);
        $LoginQuery->bindParam(':password', $this->UserData['Password'], PDO::PARAM_STR);
        $LoginQuery->execute();
        
        if($a = $LoginQuery->fetch(PDO::FETCH_ASSOC))
        {
            
            $this->login_succes = true;
            $this->login_message['System'][] = 'You have been logged in!';
            $this->UserAID = $a['AID'];
            return true;
        }
        $this->login_message['System'][] = 'Username or password are wrong.';
        return false;
    }
}