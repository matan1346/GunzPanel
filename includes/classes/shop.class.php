<?php

class Shop
{
    private $GunZ;
    private $NavBars = array();
    private $Type;
    private $Varibles = array();
    
    public function __construct($GunZ, $type)
    {
        $this->NavBars = $GLOBALS['Shop_nav_bar'];
        if(array_key_exists($type, $this->NavBars))
        {
            $this->Type = $type;
        }
        else
        {
             $this->Type = 'Menu';
        }
        $this->GunZ = $GunZ;
    }
    
    /*
    {% autoescape false %}
        Everything will be outputted as is in this block
    {% endautoescape %}
    */
    
    /*
    public function getNavCategory()
    {
        $Navs = array();
        foreach($this->NavBars as $k => $v)
            $Navs[$k] = $v['class'];
        $Navs[$this->Type] = $Navs[$this->Type].' selectedNav';
        return $Navs;
    }
    */
    public function getVars()
    {
        return $this->Varibles;
    }
    
    public function getTypeName()
    {
        return $this->NavBars[$this->Type]['Name'];
    }
    
    public function getListItems()
    {
        if(!$GLOBALS['Session']->IsConnected())
            return false;
        $func = 'get'.$this->Type;
        if(method_exists(get_class($this), $func))
            return $this->$func();
        return false;
    }
    
    public function getMenu()
    {
        //$this->Varibles = array('shop_Type' => $this->Type);
        
        $getMenu = $this->NavBars;
        $Menu_Keys = array_keys($getMenu);
        $size = sizeof($getMenu);
        $Menu = array();
        
        $row = 0;
        $i = 1;//widthout the menu it self
        $flag = true;
        while($i < $size)
        {
            $count = 1;
            do
            {
                //if(array_key_exists('NotInMenu'))
                $Menu[$row][$Menu_Keys[$i]] = $getMenu[$Menu_Keys[$i]];
                $count++;
                $i++;
            }while($count <= 3 && $i < $size);
            $row++;
        }
        
         $this->Varibles = array('shop_Type' => $this->Type,'MenuShop' => $Menu);
        
        //print_r($Menu);
        return true;
    }
    
    public function getChangeName()
    {
        $User = $GLOBALS['User'];
        
        $Characters = $User->getUserField('Characters');
        $count_chars = sizeof($Characters);
        
        $this->Varibles = array('CharactersExists' => false,'shop_Type' => $this->Type);
        if($count_chars > 0)
        {
            $this->Varibles = array(
            'CharactersExists' => true,
            'shop_Type' => $this->Type,
            'CharacterSelect' => $Characters,
            'Cost' => $this->NavBars[$this->Type]['Cost'],
            'MaxLength' => $this->NavBars[$this->Type]['length']['max']
            );
        }
            
        return true;
    }
    
    public function getColorName()
    {
        $User = $GLOBALS['User'];
            
            
        $Characters = $User->getUserField('Characters');
        $count_chars = sizeof($Characters);
        
        $this->Varibles = array('CharactersExists' => false,'shop_Type' => $this->Type);
        if($count_chars > 0)
        {
            $Color_Name = $this->NavBars[$this->Type];
            $discount = 1;
            if($User->isVIP())
            {
                $discount = $Color_Name['Discount']/100;
            }
            
            //$CharacterCIDS = array_keys($Characters);
            
            $timeoption = array();
            foreach($Color_Name['Time'] as $k => $v)
                $timeoption[$k] = $v['Days'].' Days: '.round($v['Price']*$discount);
            
            $scriptPrice = array();
            foreach($Color_Name['Time'] as $k => $v)
                $scriptPrice[$k] = round($v['Price']*$discount);
                
            $this->Varibles = array(
            'CharactersExists' => true,
            'shop_Type' => $this->Type,
            'CharacterSelect' => $Characters,
            'timeoption' => $timeoption,
            'scriptPrice' => $scriptPrice,
            'colors' => $Color_Name['colors'],
            'first_character' => current($Characters),
            'MaxLength' => $this->NavBars[$this->Type]['length']['max']
            );
        }
        
        return true;
    }
    

    public function getChangeClanName()
    {
        $User = $GLOBALS['User'];
            
        $Clans = $User->getUserField('ClanLeaders');
        $count_clans = sizeof($Clans);
        
        $this->Varibles = array('ClansExists' => false,'shop_Type' => $this->Type);
        if($count_clans > 0)
        {
            $this->Varibles = array(
            'ClansExists' => true,
            'shop_Type' => $this->Type,
            'ClanSelect' => $Clans,
            'Cost' => $this->NavBars[$this->Type]['Cost'],
            'MaxLength' => $this->NavBars[$this->Type]['length']['max']
            );
        }
            
        return true;
    }
    
    
    public function getItems()
    {
        $getItems = $this->GunZ->SelectData('CashShop',array('ItemID','Name','CashPrice','WebImgName','ResLevel','ResSex','Slot'));
        
        $size = sizeof($getItems);
        
        $this->Varibles = array('ItemsExist' => false,'shop_Type' => $this->Type);
        if($size > 0)
        {
            $Items = array();
        
            $row = 0;
            $i = 0;
            $flag = true;
            while($i < $size)
            {
                $count = 1;
                do
                {
                    $Items[$row][] = $getItems[$i];
                    $count++;
                    $i++;
                }while($count <= 3 && $i < $size);
                $row++;
            }
            
            
            $this->Varibles = array('ItemsExist' => true,'Items' => $Items,'shop_Type' => $this->Type);
        }
        
        return true;
    }
    
    public function getGiftCoins()
    {
        $this->Varibles = array('shop_Type' => $this->Type);
        return true;
    }
    
    public function getVIP()
    {
        $User = $GLOBALS['User'];
        $Characters = $User->getUserField('Characters');
        
        $firstCharacterColorName = current($Characters);
        
        $this->Varibles = array('shop_Type' => $this->Type,'CharactersData' => $Characters,'FirstCharacter' => $firstCharacterColorName['ColorName']);
        return true;
    }
    
    public function CheckListItems()
    {
        if(!$GLOBALS['Session']->IsConnected() || !isset($_POST['Sub'.$this->Type]))
            return false;
        $func = 'Check'.$this->Type;
        if(method_exists(get_class($this), $func))
            return $this->$func();
        return false;
    }
    
    public function CheckChangeName()
    {   
        if(!isset($_POST['CharacterN']) || empty($_POST['CharacterN']))
            return 'Please choose a name.';
            
        $new_name = $_POST['CharacterN'];
        $new_name_len = mb_strlen($new_name);
        
        if($new_name_len < $this->NavBars[$this->Type]['length']['min'] || $new_name_len > $this->NavBars['ChangeName']['length']['max'])
            return 'The name must be between '.$this->NavBars[$this->Type]['length']['min'].'-'.$this->NavBars['ChangeName']['length']['max'].' characters.';
            
        $regex = $this->NavBars[$this->Type]['regex'];
        
        if(preg_match('#'.$regex['statement'].'#', $new_name))
            return 'The new name can contains only `'.$regex['contains'].'` characters.';
            
        $User = $GLOBALS['User'];
        $CharacterCID = intval($_POST['CharacterSelect']);
        
        
        $UserAID = $User->getUserField('AID');
        
        $Characters = $User->getUserField('Characters');
        
        if(!array_key_exists($CharacterCID, $Characters))
            return 'Do not try to change a character name that not belongs to you.';
        
        if($this->GunZ->IfRowExists('Character', array('Name' => ':newname'), array(':newname' => $new_name)))
            return 'This name is already in use, try another name.';
      
        $CoinsLeft = $User->getUserField($GLOBALS['DataBase_Fields']['Coins'])-$this->NavBars[$this->Type]['Cost'];
        
        
        if($CoinsLeft < 0)
            return 'You do not have enough coins to change the name.';
        
        
        $this->GunZ->UpdateData('Character', array('Name' => ':newname'), array('CID' => ':cid'), array(':newname' => $new_name,':cid' => $CharacterCID));
        $this->GunZ->UpdateData('Account', array($GLOBALS['DataBase_Fields']['Coins'] => ':coins'), array('AID' => ':aid'), array(':coins' => $CoinsLeft,':aid' => $UserAID));
        $Characters[$CharacterCID] = NameToColor($new_name);
        $User->setUserField('Characters', $Characters);
        $User->setUserField($GLOBALS['DataBase_Fields']['Coins'], $CoinsLeft);
        
        return 'The character name has been changed to `'.$new_name.'`.';
        
    }
    
    public function CheckColorName()
    {   
        if(empty($_POST['CharacterSelect']) || empty($_POST['CharacterC']) || !isset($_POST['PaySelect']))
            return 'please fill all the fields.';
            
        if(!array_key_exists($_POST['PaySelect'], $this->NavBars[$this->Type]['Time']))
            return 'pay option does not exist';
            
        $name = $_POST['CharacterSelect'];
        if(preg_match('#[^a-zA-Z0-9\[\]\_]#', $name))
            return 'Use only a-z,0-9,[,] and _ characters in the currect name.';
           
        $new_name =  $_POST['CharacterC'];
        $new_name_len = mb_strlen($new_name);
        
        
        if($name != preg_replace('/(\^\d)+/i', '', $new_name))
            return 'You may only add valid colors to the name.';
        
        if(preg_match('/\^/i', $name))
            return 'You cannot add color to character that already have one.';

        
        if(!preg_match('/\^\d/i', $new_name))
            return 'Did you forgot adding color?';
            
        
        if($new_name_len < $this->NavBars[$this->Type]['length']['min'] || $new_name_len > $this->NavBars[$this->Type]['length']['max'])
            return 'The name must be between '.$this->NavBars[$this->Type]['length']['min'].'-'.$this->NavBars[$this->Type]['length']['max'].' characters.';
        
        $regex = $this->NavBars[$this->Type]['regex'];
        
        if(preg_match('#'.$regex['statement'].'#', $new_name))
            return 'The color name can contains only `'.$regex['contains'].'` characters.';
        
        $User = $GLOBALS['User'];
        
        $Characters = $User->getUserField('Characters');
        
        
        $isMineCharacter = false;
        $cid_player = 0;
        $FinalName = $name;
        
        foreach($Characters as $cid => $character)
        {
            //echo $character['RegularName'].': '.$player_name.'<br />';
            if(strtolower($character['RegularName']) == strtolower($name))
            {
                //echo 'asdasdsa';
                $isMineCharacter = true;
                $cid_player = $cid;
                $FinalName = $character['RegularName'];
            }
                
        }
        
        if(!$isMineCharacter)
            return 'Do not try to change a character name that not belongs to you.';
        /*
        if(!in_array($name, $Characters))
            return 'Do not try to change a character name that not belongs to you.';
        */
        if($this->GunZ->IfRowExists('Character', array('Name' => ':newname'), array(':newname' => $new_name)))
            return 'This name is already in use, try another name.';
            
        if($this->GunZ->IfRowExists('NameChange', array(array('FinalName' => ':newname'),array("Active = '1'")), array(':newname' => $new_name)))
            return 'This name is already in use, try another name.';

        
        $ColorName = $this->NavBars[$this->Type];
        $time = $ColorName['Time'][$_POST['PaySelect']]['Days']*24*60*60 + time();
        $discount = 1;
        if($User->isVIP())
        {
            $discount = ($ColorName['Discount']/100);
        }
        
        $price = $ColorName['Time'][$_POST['PaySelect']]['Price'];
        $price *= $discount;
        $price = round($price);
        $price *= preg_match_all('/(\^\d)+/i', $new_name, $matches);
        
        $CoinsLeft = $User->getUserField($GLOBALS['DataBase_Fields']['Coins'])-$price;
        if($CoinsLeft < 0)
            return 'You do not have enough coins.';
        
        $this->GunZ->InsertData('NameChange',array('CID' => ':cid','Name' => ':color_name','FinalName' => ':final_name','ExpDate' => ':ends_at'),array(':cid' => $cid_player,':color_name' => $new_name,':final_name' => $FinalName,':ends_at' => $time));
        $this->GunZ->UpdateData('Character',array('Name' => ':color_name'),array('CID' => ':cid'),array(':color_name' => $new_name,':cid' => $cid_player));
        
        $Characters[$cid_player] = NameToColor($new_name);
        $User->setUserField('Characters' , $Characters);
        $User->setUserField($GLOBALS['DataBase_Fields']['Coins'], $CoinsLeft);
        
        /*
        $section = 'Color';
        $log = $_SESSION['User']['UserID']." Bought color: ".$_POST['name']." to ".$_POST['newname'].".";
        
        
        mssql_query("INSERT INTO ShopLog (UserID, Log, Date, Section, Char, Before, After) VALUES('".$_SESSION['User']['UserID']."', '".$log."', GETDATE(), '".$section."', '".$_POST['name']."', '".$prep."', '".($prep-$price)."')");
        mssql_query("UPDATE Character SET Name = '".$_POST['newname']."' WHERE CID='".$assoc['CID']."'");
        mssql_query("UPDATE Account SET prep=prep-".$price." WHERE AID='".$_SESSION['User']['AID']."'");
        mssql_query("INSERT INTO NameChange (CID, Name, FinalName, ExpDate) VALUES ('".$assoc['CID']."', '".$_POST['newname']."', '".$_POST['name']."', '".$time."')");
        
        $_SESSION['HolidayEvent']['ColorAllowed'] = false;*/
        return 'You bought it!';
    }
    
    public function CheckItems()
    {
        
    }
    
    public function CheckChangeClanName()
    {
        if(!isset($_POST['ClanN']) || empty($_POST['ClanN']))
            return 'Please choose a name.';
            
        $new_name = $_POST['ClanN'];
        $new_name_len = mb_strlen($new_name);
        
        if($new_name_len < $this->NavBars[$this->Type]['length']['min'] || $new_name_len > $this->NavBars[$this->Type]['length']['max'])
            return 'The name must be between '.$this->NavBars[$this->Type]['length']['min'].'-'.$this->NavBars[$this->Type]['length']['max'].' characters.';
            
        $regex = $this->NavBars[$this->Type]['regex'];
        
        if(preg_match('#'.$regex['statement'].'#', $new_name))
            return 'The new name can contains only `'.$regex['contains'].'` characters.';
            
        $User = $GLOBALS['User'];
        $ClanCLID = intval($_POST['ClanSelect']);
        
        $Clans = $User->getUserField('ClanLeaders');
        
        if(!array_key_exists($ClanCLID, $Clans))
            return 'Do not try to change a clan name that not belongs to you.';
        
        if($this->GunZ->IfRowExists('Clan', array('Name' => ':newname'), array(':newname' => $new_name)))
            return 'This name is already in use, try another name.';
        
        $CoinsLeft = $User->getUserField($GLOBALS['DataBase_Fields']['Coins'])-$this->NavBars[$this->Type]['Cost'];
        
        
        if($CoinsLeft < 0)
            return 'You do not have enough coins to change the name.';
        
        $UserAID = $User->getUserField('AID');
        $this->GunZ->UpdateData('Clan', array('Name' => ':newname'), array('CLID' => ':clid'), array(':newname' => $new_name,':clid' => $ClanCLID));
        $this->GunZ->UpdateData('Account', array($GLOBALS['DataBase_Fields']['Coins'] => ':coins'), array('AID' => ':aid'), array(':coins' => $CoinsLeft,':aid' => $UserAID));
        $Clans[$ClanCLID]['Name'] =  NameToColor($new_name);
        $User->setUserField('ClanLeaders' , $Clans);
        $User->setUserField($GLOBALS['DataBase_Fields']['Coins'], $CoinsLeft);
        
        return 'The clan name has been changed to `'.$new_name.'`.';
    }
    
    public function CheckGiftCoins()
    {
        if(!isset($_POST['CharacterName']) || empty($_POST['CharacterName']))
            return 'Please specific the player name.';
            
        if(!isset($_POST['GiftAmount']) || empty($_POST['GiftAmount']))
            return 'Please specific the amount of coins.';
        
        $player_name = $_POST['CharacterName'];
        $amount = intval($_POST['GiftAmount']);
        
        $regex = $this->NavBars['ChangeName']['regex'];
        
        if(preg_match('#[^0-9a-zA-Z\_\[\]\^]#', $player_name))
            return 'The player name can contains only `0-9,A-z,[,],_ and ^` characters.';
            
        $length = $this->NavBars['ChangeName']['length'];
        $player_length = mb_strlen($_POST['CharacterName']);
        
        if($player_length < $length['min'] || $length['length']['max'])
            return 'The player name must be between '.$length['min'].'-'.$length['max'].' characters.';
        
        if($amount <= 0)
            return 'You can send only 1 coin and more.'; 
          
        $User = $GLOBALS['User'];
        
        $getCharacters = $User->getUserField('Characters');
        
        $isUserSelf = false;
        foreach($getCharacters as $cid => $character)
            if(strtolower($character['RegularName']) == strtolower($player_name))
                $isUserSelf = true;
        
        if($isUserSelf)
            return 'This is your player.';
            
        $statement = array('Name' => ':player_name');
        $bind = array(':player_name' => $player_name);
        
        
        $playerData = $this->GunZ->SelectData('Character',array('AID'), $statement,$bind);
        if(!is_array($playerData) || sizeof($playerData) <= 0)
            return 'The player does not exist.';
            
        $CoinsLeft = $User->getUserField($GLOBALS['DataBase_Fields']['Coins'])-$amount; 
        
        if($CoinsLeft < 0)
            return 'You do not have enough coins to gift.';
        
        $update = array($GLOBALS['DataBase_Fields']['Coins'] => ':coins');
        $bind[':coins'] = $amount;
                
        $this->GunZ->UpdateData('Account', array($GLOBALS['DataBase_Fields']['Coins'] => '(Coins + :coins)'), array('AID' => ':player_aid'), array(':coins' => $amount,':player_aid' => $playerData[0]['AID']));
        $this->GunZ->UpdateData('Account', array($GLOBALS['DataBase_Fields']['Coins'] => ':coins'), array('AID' => ':aid'), array(':coins' => $CoinsLeft,':aid' => $User->getUserField('AID')));
        
        $User->setUserField($GLOBALS['DataBase_Fields']['Coins'], $CoinsLeft);
        
        return 'The coins has been sent.';
    }
    
    public function checkVIP()
    {
        return 'asd';
    }
}