<?php

class Session
{
    private $GunZ;
    private $User;
    private $UserConnected = false;
    
    public function __construct($GunZ)
    {
        $this->GunZ = $GunZ;
        $this->User = new User($GunZ);
        if(isset($_SESSION['AID'], $_SESSION['RegDate'], $_SESSION['token']))
        {
            if(!empty($_SESSION['token']))
            {
                $new_token = sha1($_SESSION['AID'].$GLOBALS['User_Token'].$_SESSION['RegDate']);
                if($new_token === $_SESSION['token'])
                {
                    $this->User->setUserAID($_SESSION['AID']);
                }
            }
        }
    }
    
    public function SetUserSession($aid = NULL)
    {
        
        if(!is_null($aid))
        {
            $this->User->setUserAID($aid);
        }
        $getSessionData = $this->User->setUserData();
        //print_r($getSessionData);
        //echo 'LOLOLOL';
        if(is_array($getSessionData))
        {
            $this->UserConnected = true;
            foreach($getSessionData as $session_name => $session_value)
                $_SESSION[$session_name] = $session_value;
        }
        else
            $this->UserConnected = false;
        
    }
    
    public function CanAccess($Page)
    {
        /*if(in_array($Page, array('register','login')) && $this->UserConnected)
            return 'home';
        return $Page;*/
        
        return ($this->UserConnected) ? ((!in_array($Page, array('login'))) ? $Page : 'home' ) : 'login';
    }
    
    public function IsConnected()
    {
        return $this->UserConnected;
    }
    
    public function setIsConnected($flag)
    {
        $this->UserConnected = $flag;
    }
    
    public function &getUser()
    {
        return $this->User;
    }
}