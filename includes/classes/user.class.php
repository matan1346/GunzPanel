<?php

class User
{
    private $User;
    private $UserAID;
    private $GunZ;
    private $UserData = array();
    private $Permission;
    private $isSessionExists = false;
    
    public function __construct($GunZ)
    {
        $this->User = $GLOBALS['User_Allowed_Fields'];
        $this->GunZ = $GunZ;
        /*if(isset($_SESSION['AID'], $_SESSION['RegDate'], $_SESSION['token']))
        {
            if(!empty($_SESSION['token']))
            {
                $new_token = sha1($_SESSION['AID'].$this->Token.$_SESSION['RegDate']);
                if($new_token === $_SESSION['token'])
                {
                    //echo 'OK';
                    $this->UserAID = intval($_SESSION['AID']);
                    $this->isSessionExists = true;
                }
            }
        }*/
    }
    
    public function setUserAID($val)
    {
        $this->UserAID = intval($val);
    }
    
    public function getActiveCharacters()
    {
        $query_str = "SELECT Name,CID FROM Character WHERE DeleteFlag = '0' AND AID = :aid";
        $ChractersQuery = $this->GunZ->prepare($query_str);
        $ChractersQuery->bindValue(':aid', $this->UserAID, PDO::PARAM_INT);
        $ChractersQuery->execute();
        
        
        $Chracters = array();
        $ChracterAssoc = $ChractersQuery->fetchAll(PDO::FETCH_ASSOC);
        
        for($i = 0, $size = sizeof($ChracterAssoc);$i < $size;$i++)
            $Chracters[$ChracterAssoc[$i]['CID']] = NameToColor($ChracterAssoc[$i]['Name']);
        
        return $Chracters;
    }
    
    public function getClans()
    {
        $query_str = "SELECT Clan.Name AS Name,Clan.CLID AS CLID,Clan.MasterCID AS MasterCID,ClanMember.Grade AS Role,CharsLeader.Name AS MasterName FROM dbo.Character AS Chars RIGHT JOIN ClanMember ON ClanMember.CID = Chars.CID RIGHT JOIN Clan ON Clan.CLID = ClanMember.CLID INNER JOIN dbo.Character AS CharsLeader ON CharsLeader.CID = Clan.MasterCID WHERE Chars.DeleteFlag = '0' AND Chars.AID = :aid";
        
        $ClansQuery = $this->GunZ->prepare($query_str);
        $ClansQuery->bindValue(':aid', $this->UserAID, PDO::PARAM_INT);
        
        $ClansQuery->execute();
        
        $Characters = $this->UserData['Characters'];
        $Clans = array();
        $ClanAssoc = $ClansQuery->fetchAll(PDO::FETCH_ASSOC);
        
        //print_r($ClanAssoc);
        
        for($i = 0, $size = sizeof($ClanAssoc);$i < $size;$i++)
        {
            $ClanName = NameToColor($ClanAssoc[$i]['Name']);
            if(array_key_exists($ClanAssoc[$i]['MasterCID'], $Characters))
                $Clans['ClanLeaders'][$ClanAssoc[$i]['CLID']][] = array('Name' => $ClanName,'MasterCID' => $ClanAssoc[$i]['MasterCID']);
            $Clans['Clans'][$ClanAssoc[$i]['CLID']][] = array('Name' => $ClanName,'Role' => $ClanAssoc[$i]['Role'],'MasterName' => $ClanAssoc[$i]['MasterName']);
            //$Clans[$ClanAssoc[$i]['CLID']] = $ClanName;
        }
            //$Clans[$ClanAssoc[$i]['CLID']] = $ClanName;
        return $Clans;
    }
    
    public function UpdateCharacterField($table, $cid, $field, $value)
    {
        $query_str = 'UPDATE '.$table.' SET '.$field.' = :fieldvalue WHERE CID = :cid';
        $ChracterUpdteQuery = $this->GunZ->prepare($query_str);
        $ChracterUpdteQuery->bindParam(':fieldvalue', $value, PDO::PARAM_STR);
        $ChracterUpdteQuery->bindParam(':cid', $cid, PDO::PARAM_INT);
        $ChracterUpdteQuery->execute();
        
        if($ChracterUpdteQuery->affected_rows > 0)
        {
            return true;
        }
        return false;
    }
    
    public function UpdateUserField($table, $field, $value)
    {
        $query_str = 'UPDATE '.$table.' SET '.$field.' = :fieldvalue WHERE AID = :aid';
        $UserUpdteQuery = $this->GunZ->prepare($query_str);
        $UserUpdteQuery->bindParam(':fieldvalue', $value, PDO::PARAM_STR);
        $UserUpdteQuery->bindParam(':aid', $this->UserAID, PDO::PARAM_INT);
        $UserUpdteQuery->execute();
        
        if($UserUpdteQuery->affected_rows > 0)
        {
            $this->UserData[$field] = $value;
            return true;
        }
        return false;
    }
    
    public function isVIP()
    {
        if(array_key_exists('UGradeID', $this->UserData) && in_array($this->UserData['UGradeID'], array(168,169,170,171)))
            return true;
        return false;
    }
    
    public function IsCharacterBelongs($field, $value)
    {
        $query_str = "SELECT 1 FROM Character WHERE ".$field." = :value AND DeleteFlag = '0' AND AID = :aid";
        $CharacterQuery = $this->GunZ->prepare($query_str);
        $CharacterQuery->bindParam(':value', $value, PDO::PARAM_INT);
        $CharacterQuery->bindParam(':aid', $this->UserAID, PDO::PARAM_INT);
        $CharacterQuery->execute();
        //echo 'Field: '.$field.'<br />Value: '.$value.'<br />AID: '.$this->UserAID;
        if($a = $CharacterQuery->fetch())
        {
            //echo 'sad';
            return true;
        }
        return false;
        
    }
    
    public function IsCharacterExists($name)
    {
        $query_str = "SELECT 1 FROM Character WHERE Name = :name AND DeleteFlag = '0'";
        $CharacterQuery = $this->GunZ->prepare($query_str);
        $CharacterQuery->bindParam(':name', $name, PDO::PARAM_STR);
        $CharacterQuery->execute();
        //echo 'Field: '.$field.'<br />Value: '.$value.'<br />AID: '.$this->UserAID;
        if($a = $CharacterQuery->fetch())
        {
            //echo 'sad';
            return true;
        }
        return false;
        
    }
    
    public function setUserPermission($flag = true)
    {
        //print_r($this->UserData);
        if($this->UserData['ActionsActive'] && $flag)
            Panel_Permissions::setPermissions($this->GunZ, array($this->UserData['PanelGradeID']), $this->UserData['ActionsCustomID']);
        else
            Panel_Permissions::setAccessStatus(false, true);
        $this->Permission = Panel_Permissions::getPermission($this->UserData['PanelGradeID'], $this->UserData['ActionsCustomID']);
        //echo '<pre>'.print_r($this->Permission, true).'</pre>';
    }
    
    public function getPermission()
    {
        return $this->Permission;
    }
    
    public function setUserData()
    {
        /*
        if(!$this->isSessionExists)
            return false;
        */
        $select = '';
        foreach($this->User as $v)
            $select .= $v.',';
        //echo $this->UserAID;
        $select = substr($select, 0, strlen($select)-1);
        $query_str = 'SELECT '.$select.',PanelStaff.PSID,PanelStaff.PGradeID AS PanelGradeID,
        PanelStaff.ActionsActive,PanelStaff.ActionsCustomID,PanelStaff.PanelSelection
        FROM Account INNER JOIN PanelStaff ON PanelStaff.AID = Account.AID WHERE Account.AID = :aid';
        //var_dump($this->UserAID);
        $UserQuery = $this->GunZ->prepare($query_str);
        $UserQuery->bindParam(':aid', $this->UserAID, PDO::PARAM_INT);
        $UserQuery->execute();
        
        if($a = $UserQuery->fetch(PDO::FETCH_ASSOC))
        {
            $this->UserData = $a;
            $this->UserData['Characters'] = $this->getActiveCharacters();
            $ClansData = $this->getClans();
            $this->UserData['Clans'] = $ClansData['Clans'];
            $this->UserData['ClanLeaders'] = $ClansData['ClanLeaders'];
            
            $this->setSessionExists(true);
            return array('AID' => $this->UserAID,'RegDate' => $a['RegDate'],'token' => sha1($this->UserAID.$GLOBALS['User_Token'].$a['RegDate']));
            /*$_SESSION['AID'] = $this->UserAID;
            $_SESSION['RegDate'] = $a['RegDate'];
            $_SESSION['token'] = sha1($_SESSION['AID'].$this->Token.$_SESSION['RegDate']);*/
        }
        else
        {
            return false;
            //$this->isSessionExists = false;
        }
    }
    
    public function setNewUserData($AID)
    {
        //$this->isSessionExists = true;
        $this->UserAID = intval($AID);
        $this->setUserData();
    }
    
    public function isUserConnected()
    {
        return $this->isSessionExists;
    }
    
    public function setSessionExists($flag)
    {
        $this->isSessionExists = $flag;
    }
    
    public function getUserField($key)
    {
        if(array_key_exists($key, $this->UserData))
            return $this->UserData[$key];
        return false;
    }
    
    public function getMultiUserFields()
    {
        $size = func_num_args();
        if($size > 0)
        {
            $args = func_get_args();
            $Data = array();
            for($i = 0;$i < $size;$i++)
                if(array_key_exists($args[$i],$this->UserData))
                    $Data[$args[$i]] = $this->UserData[$args[$i]];
            return $Data;
        }
        return $this->UserData;
    }
    
    public function getSelectData($name)
    {
        $QuerySelect = $this->GunZ->prepare(
        "SELECT * FROM PanelFormSelection INNER JOIN PanelAction ON PanelAction");
    }
    
    public function setUserField($key, $value)
    {
        if(array_key_exists($key, $this->UserData))
        {
            $this->UserData[$key] = $value;
            return true;
        }
        return false;
    }
}