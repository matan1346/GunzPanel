<?php

class Transfer extends Login
{
    private $srcConnection;
    private $dstConnection;
    private $InsertUserData = false;
    private $InsertCharactersData = false;
    private $UpdateCharactersData = false;
    private $UpdateClanData = false;
    private $DeleteClanAnyWay = false;
    private $DeleteUserData = false;
    private $DeleteCharactersData = false;
    
    //srcUserID-srcUserPass/dstUserID-dstUserPass
    public function __construct($srcConn,$dstConn)
    {
        parent::__construct($srcConn);
        $this->srcConnection = $srcConn;
        $this->dstConnection = $dstConn;
    }
    
    public function setInsertUserData($flag)
    {
        $this->InsertUserData = $flag;
        return $this;
    }
    
    public function setInsertCharactersData($flag)
    {
        $this->InsertCharactersData = $flag;
        return $this;
    }
    
    public function setUpdateCharactersData($flag)
    {
        $this->UpdateCharactersData = $flag;
        return $this;
    }
    
    public function setUpdateClanData($flag)
    {
        $this->UpdateClanData = $flag;
        return $this;
    }
    
    public function setDeleteClanAnyWay($flag)
    {
        $this->DeleteClanAnyWay = $flag;
        return $this;
    }
    
    public function setDeleteUserData($flag)
    {
        $this->DeleteUserData = $flag;
        return $this;
    }
    
    public function setDeleteCharactersData($flag)
    {
        $this->DeleteCharactersData = $flag;
        return $this;
    }
    
    public function TransferAccount()
    {
        $Fields = $this->getFieldsValues();
        
        //-------------------------------------- Begin The Transform ----------------------------
        
        $ToLog = '';
        
        
        
        /*------------*/
        //If User Exist
        /*------------*/
        
        if($this->dstConnection->IfRowExists('Account',array('UserID' => ':user_id'),array(':user_id' => $Fields['dstUserID'])))
        {
            //echo 'sadasd';
             $this->setMessage('dstUserID', 'The Username '.$Fields['dstUserID'].' is already exist.');
             return false;
        }
        
        
        
        /*------------*/
        //Account Data.
        /*------------*/
        
        // Get the user data.
        $table = 'Account INNER JOIN Login ON Account.AID = Login.AID';
        $select = array('Account.UGradeID As UGradeID','Account.PGradeID As PGradeID',
                                    'Account.RegDate As RegDate','Account.Name As Name','Account.Email As Email',
                                    'Account.Age As Age','Account.Sex As Sex','Account.Country As Country',
                                    'Account.Coins As Coins',
                                    'Login.Password As Password','Login.LastConnDate As LastConnDate');
        $statements = array('Login.AID' => ':loginAID');
        $bind = array(':loginAID' => $Fields['srcUserAID']);
        
        $getUserData = $this->srcConnection->SelectData($table, $select, $statements, $bind);
        
        // Fetch Data.
        $a = $getUserData[0];
    
        // Change the value of the gender.
        if($a['Sex'] == 'Male')
            $a['Sex'] = 0;
        else
            $a['Sex'] = 1;
        
        
        $insertArray = array('UserID' => ':user_id',
                            'UGradeID' => ':u_Grade_ID',
                            'PGradeID' => ':p_Grade_ID',
                            'RegDate' => ':reg_date',
                            'Name' => ':user_name',
                            'Email' => ':email',
                            'Age' => ':age',
                            'Sex' => ':gender',
                            'Country' => ':country',
                            'ServerID' => ':server_id',
                            'Coins' => ':coins');
                            /*'ActivtionCode' => ':act_code',
                            'Swap' => ':swap');*/
        $insertBind = array(':user_id' => $Fields['dstUserID'],
                        ':u_Grade_ID' => $a['UGradeID'],
                        ':p_Grade_ID' => 0,
                        ':reg_date' => $a['RegDate'],
                        ':user_name' => $a['Name'],
                        ':email' => $a['Email'],
                        ':age' => $a['Age'],
                        ':gender' => $a['Sex'],
                        ':country' => $a['Country'],
                        ':server_id' => 0,
                        ':coins' => $a['Coins']);
                        /*':act_code' => $a['ActCode'],
                        ':swap' => $a['Swap']);*/
        
        // Insert New User To The Server.
        if($this->InsertUserData)
            $this->dstConnection->InsertData('Account', $insertArray, $insertBind);
        
        // Get The Account identity (number).
        $getUserNewData = $this->dstConnection->SelectData('Account',array('AID'),array('UserID' => ':userid'),array(':userid' => $Fields['dstUserID']));
        $newUserData = $getUserNewData[0];
        
        // Insert New User Settings (Password).
        $insertArray = array('AID' => ':user_aid','UserID' => ':user_id','Password' => ':pass','LastConnDate' => ':last_conn_date');
        $insertBind = array(':user_aid' => $newUserData['AID'],':user_id' => $Fields['dstUserID'],':pass' => $Fields['dstUserPassword'],':last_conn_date' => $a['LastConnDate']);
        
        if($this->InsertUserData)
            $this->dstConnection->InsertData('Login', $insertArray,$insertBind);
        
        $ToLog .= 'AccountData,';
        
        // Searching for grade change data.
        $getGradeChange = $this->srcConnection->SelectData('GradeChange',array('TOP 1 Grade','FinalGrade','ExpDate'),array(array('AID' => ':user_aid'),array('Active' => 1)),array(':user_aid' => $Fields['srcUserAID']), array('ID' => 'DESC'));
        
        if(is_array($getGradeChange) && sizeof($getGradeChange) > 0)
        {
            $GetGradeData = $getGradeChange[0];
            
            //Insert The Last Active Row Of User Grades.
            $insertArray = array('AID' => ':user_aid','Grade' => ':grade','FinalGrade' => ':final_grade','ExpDate' => ':exp_date');
            $insertBind = array(':user_aid' => $newUserData['AID'],':grade' => $a['UGradeID'],':final_grade' => $GetGradeData['FinalGrade'],':exp_date' => $GetGradeData['ExpDate']);
            
            if($this->InsertUserData)
                $this->dstConnection->InsertData('GradeChange',$insertArray,$insertBind);
        }
        
        /*-------------*/
        //Account Items.
        /*-------------*/
        
        // Get Donator List.
        $getDonatorItems = $this->srcConnection->SelectData('CashShop',array('ItemID'));
        $statementsItem = array();
        
        // Add Statements for the query.
        foreach($getDonatorItems as $val)
            $statementsItem[] = "ItemID = '".$val['ItemID']."'";
        
        if($this->InsertUserData)
        {
            // Query That Gets User Donator Items Data.
            $getDonatorAccountItems = $this->srcConnection->SelectData('AccountItem',array('ItemID','RentDate','RentHourPeriod','Cnt','BuyType'),array(array('AID' => ':user_aid'), $statementsItem),array(':user_aid' => $Fields['srcUserAID']));
            
            $numOfAccItems = 0;
            if(is_array($getDonatorAccountItems))
            {
                $sizeDonatorAccountItems = sizeof($getDonatorAccountItems);
                for($i = 0;$i < $sizeDonatorAccountItems;$i++)
                {
                    // Insert Donator Items To The Character.
                    $AccountItem = $getDonatorAccountItems[$i];
                    $insertArray = array('AID' => ':user_aid','ItemID' => ':item_id','BuyType' => ':buy_type');
                    $insertBind = array(':user_aid' => $newUserData['AID'],':item_id' => $AccountItem['ItemID'],':buy_type' => $AccountItem['BuyType']);
                    
                    if(!is_null($AccountItem['RentDate']))
                    {
                        $insertArray['RentDate'] = ':rent_date';
                        $insertBind[':rent_date'] = $AccountItem['RentDate'];
                    }
                    if(!is_null($AccountItem['RentHourPeriod']))
                    {
                        $insertArray['RentHourPeriod'] = ':period';
                        $insertBind[':period'] = $AccountItem['RentHourPeriod'];
                    }
                    if(!is_null($AccountItem['Cnt']))
                    {
                        $insertArray['Cnt'] = ':cnt';
                        $insertBind[':cnt'] = $AccountItem['Cnt'];
                    }
                    $this->dstConnection->InsertData('AccountItem',$insertArray,$insertBind);
                    $numOfAccItems++;
                }
            }
            if($numOfAccItems > 0)
                $ToLog .= ' AccountItems('.$numOfAccItems.'),';
        }
        
        
        
        /*---------------*/
        //Characters Data.
        /*---------------*/
        
        // Get all the characters data.
        $select = array('DeleteFlag','CID','Name','Sex','CharNum','Hair','Face','BP','RegDate','PlayTime','GameCount','KillCount','DeathCount');
        $statements = array('AID' => ':user_aid');
        $bind = array(':user_aid' => $Fields['srcUserAID']);
        
        $getUserCharacters = $this->srcConnection->SelectData('Character',$select,$statements,$bind);
        
        // Set num of active character and num of donator items.
        $CountCharacterActive = 0;
        $CountCharacterItems = 0;
        if(is_array($getUserCharacters))
        {   
            /*-----------------*/
            //Begin To Transfer.
            /*-----------------*/
            
            // Setting the query statement for deleting all the data for each character.
            $statementsCID = array();
            $statementsCIDFriends = array();
            
            for($num = 0,$sizeCharacter = sizeof($getUserCharacters);$num < $sizeCharacter;$num++)
            {    
                $CharacterData = $getUserCharacters[$num];
                
                // If this row (character) is active - the system will transfer that character.
                if($CharacterData['DeleteFlag'] == 0)
                {
                    //echo 'Player: '.$CharacterData['Name'].'<br />';
                    
                    
                    
                    if(!in_array($CharacterData['CID'], $Fields['CharactersToTransfer']))
                        continue;
                    
                    $this->setMessage('Characters', 'Player: '.$CharacterData['Name']);
                    
                    $CountCharacterActive++;
                    
                    /*-----------*/
                    //Manage Clan.
                    /*-----------*/
                    
                    // Check if the character is a  leader in some clan. 
                    $getCharClan = $this->srcConnection->SelectData('ClanMember INNER JOIN Clan ON Clan.CLID = ClanMember.CLID',array('ClanMember.CLID','Clan.MasterCID'),array('ClanMember.CID' => ':player_id'),array(':player_id' => $CharacterData['CID']));
                    // If yes, set the TRUE value to the vars: InClan,IsLeader and get the Clan identity.
                    if(is_array($getCharClan) && sizeof($getCharClan) > 0)
                    {
                        if($this->DeleteCharactersData)
                            $this->srcConnection->DeleteData('ClanMember',array('CID' => ':player_id'),array(':player_id' => $CharacterData['CID']));
                        if($getCharClan[0]['MasterCID'] == $CharacterData['CID'])
                        {
                            if($this->DeleteClanAnyWay)
                            {
                                $this->srcConnection->DeleteData('ClanMember',array('CLID' => ':clan_id'),array(':clan_id' => $getCharClan[0]['CLID']));
                                $this->srcConnection->DeleteData('Clan',array('CLID' => ':clan_id'),array(':clan_id' => $getCharClan[0]['CLID']));
                            }
                            else
                            {
                                $getTheBestPlayer = $this->srcConnection->SelectData('ClanMember',array('TOP 1 CID'),array(array('CLID' => ':clan_id'),array('CID <> :player_id')),array(':clan_id' => $getCharClan[0]['CLID'],':player_id' => $CharacterData['CID']),array('Grade' => 'ASC','ContPoint' => 'DESC'));
                                if(is_array($getTheBestPlayer) && sizeof($getTheBestPlayer) > 0)
                                {
                                    // Set New Leader.
                                    if($this->UpdateClanData)
                                    {
                                        $this->srcConnection->UpdateData('ClanMember',array('Grade' => 1),array('CID' => ':player_id'),array(':player_id' => $getTheBestPlayer[0]['CID']));
                                        $this->srcConnection->UpdateData('Clan',array('MasterCID' => ':player_id'),array('CLID' => ':clan_id'),array(':player_id' => $getTheBestPlayer[0]['CID'],':clan_id' => $getCharClan[0]['CLID']));
                                    }
                                }
                                else
                                {
                                    // If not, Delete the Clan .
                                    if($this->DeleteCharactersData)
                                        $this->srcConnection->DeleteData('Clan',array('CLID' => ':clan_id'),array(':clan_id' => $getCharClan[0]['CLID']));
                                }
                            }
                        }
                    }
                    
                    
                    
                    // Check if the chracter has a color.
                    $getNameChangeData = $this->srcConnection->SelectData('NameChange',array('TOP 1 Name','FinalName','ExpDat'),array(array('CID' => ':player_id'),array('Active' => 1)),array(':player_id' => $CharacterData['CID']),array('ID' => 'DESC'));
                        
                    $FromReg = strtotime($CharacterData['RegDate']);
                    $flagNameChange = false;
                    $flagForNameExist = false;
                    $newCharacterName = $CharacterData['Name'];
                    
                    if(is_array($getNameChangeData) && sizeof($getNameChangeData) > 0)
                    {
                        $assocGetNameChange = $getNameChangeData[0];
                        
                        $flagNameChange = true;
                        
                        // Check if the finalName ofthe character (after the color) does exist in IsraelGunz.
                        
                        $CharacterExists = $this->dstConnection->SelectData('Character',array('RegDate','CID','Name'),array(array('Name' => ':player_name'),array('DeleteFlag' => 0)),array(':player_name' => $assocGetNameChange['FinalName']));
                        
                        if(is_array($CharacterExists) && sizeof($CharacterExists) > 0)
                        {
                            // If Yes, Check the Register Date of the character between QualityGunz and IsrelGunz.
                            $getRegDate = $CharacterExists[0];
                            $ToReg = strtotime($getRegDate['RegDate']);
                            
                            if($FromReg < $ToReg)
                            {
                                // If the Character in QualityGunz is not the fake (The first who have been created) - update the Character name in IsraelGunz. 
                                if($this->UpdateCharactersData)
                                    $this->dstConnection->UpdateData('Character',array('Name' => ':player_name'),array('CID' => ':player_id'),array(':player_name' => 'CIDE_'.$getRegDate['CID'],':player_id' => $getRegDate['CID']));
                                $newFinalName = $assocGetNameChange['FinalName'];
                            }
                            else
                            {
                                // Else, if the Character in QualityGunz is  the fake, the final name is CIDE_XXX (for the new character).
                                $flagForNameExist = true;
                                $newFinalName = 'CIDE_'.$CharacterData['CID'];
                            }
                        }
                        else
                        {
                            // If the Character name does not exist in IsraelGunz, the new final name is the orginal final name.
                           $newFinalName = $assocGetNameChange['FinalName']; 
                        }
                    }
                    
                    // If the chracter name still not exist
                    if($flagForNameExist == false)
                    {
                        // Check If The Name Is Already Exist In IsraelGunz.
                        $CharacterExists2 = $this->dstConnection->SelectData('Character',array('RegDate','CID','Name'),array(array('Name' => ':player_name'),array('DeleteFlag' => 0)),array(':player_name' => $CharacterData['Name']));
                        
                        if(is_array($CharacterExists2) && sizeof($CharacterExists2) > 0)
                        {
                            // If Yes, Check the Register Date of the character between QualityGunz and IsrelGunz.
                            $getRegDate2 = $CharacterExists2[0];
                            $ToReg = strtotime($getRegDate2['RegDate']);
                            
                            // If RegisterDate of Character From Quality Less Then The Character In Israel.
                            if($FromReg < $ToReg)
                            {
                                if($this->UpdateCharactersData)
                                    $this->dstConnection->UpdateData('Character',array('Name' => ':player_name'),array('CID' => ':player_id'),array(':player_name' => 'CIDE_'.$getRegDate2['CID'],':player_id' => $getRegDate2['CID']));
                            }
                            else
                            {
                                // Set New Name - CIDE_[NUMBER].
                                $flagForNameExist = true;
                                $newCharacterName = 'CIDE_'.$CharacterData['CID'];   
                            }
                        }
                    }
                    
                    // If the chracter name still not exist
                    if($flagForNameExist == false)
                    {
                        //Check if the chracter in IsrelGunz has color name and the final name is matching to this character name.
                        $CharacterExists3 = $this->dstConnection->SelectData('NameChange',array('TOP 1 CID'),array(array('FinalName' => ':player_final_name'),array('Active' => 1)),array(':player_final_name' => $CharacterData['Name']),array('ID' => 'DESC'));
                        
                        if(is_array($CharacterExists3) && sizeof($CharacterExists3) > 0)
                        {   
                            // If yes, get the Register Date of the character in IsraelGunz and check who is the fiest who created the char name.
                            $GetIsraelNameChange = $CharacterExists3[0];
                            $getCharacterInfoIsrael = $this->dstConnection->SelectData('Character',array('RegDate'),array(array('CID' => ':player_id'),array('DeleteFlag' => 0)),array(':player_id' => $GetIsraelNameChange['CID']));
                            
                            if(is_array($getCharacterInfoIsrael) && sizeof($getCharacterInfoIsrael) > 0)
                            {
                                $getNameChangeData2 = $getCharacterInfoIsrael[0];
                                $ToReg = strtotime($getNameChangeData2['RegDate']);
                                
                                // If RegisterDate of Character From Quality Less Then The Character In Israel.
                                if($FromReg < $ToReg)
                                {
                                    if($this->UpdateCharactersData)
                                        $this->dstConnection->UpdateData('Character',array('Name' => ':player_name'),array('CID' => ':player_id'),array(':player_name' => 'CIDE_'.$GetIsraelNameChange['CID'],':player_id' => $GetIsraelNameChange['CID']));
                                }
                                else
                                {
                                    // Set New Name - CIDE_[NUMBER].
                                    $flagForNameExist = true;
                                    $newCharacterName = 'CIDE_'.$CharacterData['CID'];   
                                }
                            }
                        }
                    }
                    
                    if($this->InsertCharactersData)
                    {
                        // Insert New Active Character To IsraelGunz.
                        $insertArray = array('AID' => ':user_aid',
                                            'Name' => ':user_name',
                                            'Level' => 1,
                                            'Sex' => ':gender',
                                            'CharNum' => ':char_num',
                                            'Hair' => ':hair',
                                            'Face' => ':face',
                                            'XP' => 0,'BP' => 5000000,
                                            'RegDate' => ':reg_date','PlayTime' => 0,'GameCount' => 0,'KillCount' => 0,'DeathCount' => 0,'DeleteFlag' => 0);
                                            
                        $insertBind = array(':user_aid' => $newUserData['AID'],
                                            ':user_name' => $newCharacterName,
                                            ':gender' => $CharacterData['Sex'],
                                            ':char_num' => $CharacterData['CharNum'],
                                            ':hair' => $CharacterData['Hair'],
                                            ':face' => $CharacterData['Face'],
                                            ':reg_date' => $CharacterData['RegDate']);
                        $this->dstConnection->InsertData('Character', $insertArray, $insertBind);
                        
                        // Get The New CID of The Character For The Items.
                        $getCharacterC = $this->dstConnection->SelectData('Character',array('CID'),array('Name' => ':player_name'),array(':player_name' => $newCharacterName));
                        
                        if(!is_array($getCharacterC) || sizeof($getCharacterC) <= 0)
                        {
                            $this->setMessage('System', 'Error Of Get Inserted CID, Name: '.$CharacterData['Name'].' .');
                            return false;
                        }
                        
                        $getCID = $getCharacterC[0];
                        
                        // Show Character (This is important for the client 1.5).
                        $insertArray = array('CID' => ':player_id','SlotID' => 1,'ItemID' => 21001);
                        $insertBind = array(':player_id' => $getCID['CID']);
                        
                        $this->dstConnection->InsertData('CharacterEquipmentSlot',$insertArray,$insertBind);
                        
                        // If the character in QualityGunz had a color, so insert the last color.
                        if($flagNameChange)
                        {
                            // Insert The Last Active NameChange(Color).
                            $insertArray = array('CID' => ':player_id','Name' => ':player_name','FinalName' => ':player_final_name','ExpDate' => ':exp_date');
                            $insertBind = array(':player_id' => $getCID['CID'],':player_name' => $newCharacterName,':player_final_name' => $newFinalName,':exp_date' => $assocGetNameChange['ExpDate']);
                            
                            $this->dstConnection->InsertData('NameChange', $insertArray, $insertBind);
                            
                            $ToLog .= ' NameChange,';
                        }
                        
                        /*--------------*/
                        //CharacterItems.
                        /*--------------*/
                        
                        // Query That Gets Character Donator Items Data.
                        $getDonatorCharacterItems = $this->srcConnection->SelectData('CharacterItem',array('ItemID','RentDate','RentHourPeriod','Cnt','BuyType'),array(array('CID' => ':player_id'), $statementsItem),array(':player_id' => $CharacterData['CID']));
            
                        $CountCharacterItems = 0;
                        if(is_array($getDonatorCharacterItems))
                        {
                            $sizeDonatorCharacterItems = sizeof($getDonatorCharacterItems);
                            
                            for($i = 0;$i < $sizeDonatorCharacterItems;$i++)
                            {
                                // Insert Donator Items To The Character.
                                $ChracterItem = $getDonatorCharacterItems[$i];
                                $insertArray = array('CID' => ':player_id','ItemID' => ':item_id','BuyType' => ':buy_type');
                                $insertBind = array(':player_id' => $getCID['CID'],':item_id' => $ChracterItem['ItemID'],':buy_type' => $ChracterItem['BuyType']);
                                
                                if(!is_null($ChracterItem['RentDate']))
                                {
                                    $insertArray['RentDate'] = ':rent_date';
                                    $insertBind[':rent_date'] = $ChracterItem['RentDate'];
                                }
                                if(!is_null($ChracterItem['RentHourPeriod']))
                                {
                                    $insertArray['RentHourPeriod'] = ':period';
                                    $insertBind[':period'] = $ChracterItem['RentHourPeriod'];
                                }
                                if(!is_null($ChracterItem['Cnt']))
                                {
                                    $insertArray['Cnt'] = ':cnt';
                                    $insertBind[':cnt'] = $ChracterItem['Cnt'];
                                }
                                $this->dstConnection->InsertData('CharacterItem',$insertArray,$insertBind);
                                $CountCharacterItems++;
                            }
                        }
                    }
                }
                
                // Making statement to delete all character data for each one. 
                $statementsCID[] = "CID = '".$CharacterData['CID']."'";
                $statementsCIDFriends[] = "CID = '".$CharacterData['CID']."'";
                $statementsCIDFriends[] = "FriendCID = '".$CharacterData['CID']."'";
            }
            /*---------------------*/
            //Delete Character Data.
            /*---------------------*/
            
            // Delete All the CharacterData: NameChanges logs,Character Items,Items log by Bounty in game And the Character.
            if($this->DeleteCharactersData)
            {
                $this->srcConnection->DeleteData('Friend',$statementsCIDFriends);
                $this->srcConnection->DeleteData('NameChange',$statementsCID);
                $this->srcConnection->DeleteData('CharacterItem',$statementsCID);
                $this->srcConnection->DeleteData('ItemPurchaseLogByBounty',$statementsCID);
                $this->srcConnection->DeleteData('Character',$statementsCID);
            }
        }
        /*-------------------*/
        //Delete Account Data.
        /*-------------------*/
        
        // Delete All AccountData: GradeChanges logs, Account Items and all AccountData.
        $DeleteArray = array('AID' => ':user_aid');
        $DeleteBind = array(':user_id' => $Fields['srcUserAID']);
        
        if($this->DeleteUserData)
        {
            $this->srcConnection->DeleteData('GradeChange',$DeleteArray,$DeleteBind);
            $this->srcConnection->DeleteData('AccountItem',$DeleteArray,$DeleteBind);
            $this->srcConnection->DeleteData('Login',$DeleteArray,$DeleteBind);
            $this->srcConnection->DeleteData('Account',$DeleteArray,$DeleteBind);
        }
        
        
        // If the account had active chracters or/and if the characters had donator items.
        if($CountCharacterActive > 0)
            $ToLog .= ' Characters('.$CountCharacterActive.'),';
        if($CountCharacterItems > 0)
            $ToLog .= ' CharacterItems('.$CountCharacterItems.'),';
        
        $ToLog = substr($ToLog, 0, mb_strlen($ToLog)-1);
        
        // Set that the account has been transfered and the new AID in IsraelGunz.
        //mssql_query("UPDATE AccountMove SET moveNewAID = '".$newUserData['AID']."',Active = '0' WHERE moveAID = '".$USER_INFO['AID']."'", $QualityCon);
        
        $this->dstConnection = NULL;
        
        $logStr = 'Done transferring the account of '.$Fields['srcUserID'].', transferred:<br />'.$ToLog.' .';
        
        // Making the alert message for the user with all the data.
        $logWeb = '<script>alert("Done transferring the account of \''.$Fields['srcUserID'].'\', transferred:\n'.$ToLog.' .\n\nUsername: '.$newUserID.' .\nPassword: The same password .\n\nEnjoy!");isExitLock = false;</script>Disconnecting... <meta http-equiv="refresh" content="0;URL=http://qualitygunz.net/ajax/ajax_fastlogout.php?next='.urlencode('http://igunz.net/').'"/>';
        $title = 'No Hebrew';
        $reason = 'Username confirmed to transfer his account data.';
        $section = 'Transfer Account Data';
        
        // Inserting this tranfer actions as log for save it for important things.
        /*LoggedQuery("INSERT INTO QPanelLog (AID, UserID, Log, Reason, Title, Date, Type, Section)
        VALUES ('0','".$Fields['srcUserID']."','".$logStr."','".$reason."','" . $title . "',convert(varchar, getdate(), 100),'5','".$section."')", "Transfer System");
        */
        // Return the alert message.
        
        $this->setError('System/Transfer/Succeed','System');
        //return $logWeb;  
        return true;  
    }
}