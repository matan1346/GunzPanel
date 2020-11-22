<?php

class Item
{
    private $GunZ;
    private $ItemData;
    private $Message;
    private $isAllowed = true;
    
    public function __construct($GunZ, $ItemID)
    {
        $this->GunZ = $GunZ;
        
        $select = array('ItemID','Name','Description','WebImgName','Slot','ResSex','ResLevel','Weight','CashPrice','Damage','Delay','Magazine','MaxBullet','Control','HP','AP','MaxWeight','ReloadTime','Duration','FR','PR','CR','LR','Week1','Week2','Week3','Week4','Unlimited','Discount');
        
        $ItemData = $this->GunZ->SelectData('CashShop',$select,array('ItemID' => ':itemid'), array(':itemid' => $ItemID));
        
        if(sizeof($ItemData) > 0)
        {
            //print_r($ItemData);
            //die();
            $this->ItemData = $ItemData[0];
            
            $myTimeOption = array();
            
            if($this->ItemData['Week1'] > 0)
                $myTimeOption[] = array('Name' => '1 Week', 'Cost' => $this->ItemData['Week1'], 'Days' => 7);
            if($this->ItemData['Week2'] > 0)
                $myTimeOption[] = array('Name' => '2 Weeks', 'Cost' => $this->ItemData['Week2'], 'Days' => 14);
            if($this->ItemData['Week3'] > 0)
                $myTimeOption[] = array('Name' => '3 Weeks', 'Cost' => $this->ItemData['Week3'], 'Days' => 21);
            if($this->ItemData['Week4'] > 0)
                $myTimeOption[] = array('Name' => '4 Weeks', 'Cost' => $this->ItemData['Week4'], 'Days' => 28);
             $myTimeOption[] = array('Name' => 'Unlimited', 'Cost' => $this->ItemData['Unlimited'], 'Days' => 0);
            
            $this->ItemData['TimeOptions'] = $myTimeOption;
        }
        else
            $this->isAllowed = false;
        
    }
    
    public function isItemExists()
    {
        return $this->isAllowed;
    }
    
    public function getData()
    {
        return $this->ItemData;
    }
    
    public function HandleForm()
    {
        if(!$this->isAllowed)
            return 'This item does not exist.';
            
        //print_r($_POST);
        
        if(!isset($_POST['SubItem']))
            return '';
        
        /*
        $arrayCost = array(
            'TimeOptions' => array(
                array('Name' => '1 Week', 'Cost' => 20, 'Days' => 7),
                array('Name' => '2 Weeks', 'Cost' => 40, 'Days' => 14),
                array('Name' => '3 Weeks', 'Cost' => 60, 'Days' => 21),
                array('OName' => '1 Month', 'Cost' => 80, 'Days' => 28)
            ),
            'Discount' => 0.8
        );
        */
        
        if(!array_key_exists($_POST['TimeOption'], $this->ItemData['TimeOptions']))
            return 'Time option is not valid.';
            
        $TimeOption = $this->ItemData['TimeOptions'][intval($_POST['TimeOption'])];
        
        
        $User = $GLOBALS['User'];
        
        
        $Cost = (($User->isVIP()) ? round($this->ItemData['Discount']*$TimeOption['Cost']) : $TimeOption['Cost']);
        //return $Cost;
        $CoinsLeft = $User->getUserField('Coins')-$Cost;
        
        if($CoinsLeft < 0)
            return 'You do not have enough coins.';
        
        $InsertArray = array('ItemID' => $this->ItemData['ItemID'],'AID' => $User->getUserField('AID'),'RentDate' => 'GETDATE()');
        
        if($TimeOption['Days'] > 0)
        {
            $TimeToUpdate = 24*$TimeOption['Days'];
            $InsertArray['Cnt'] = 1;
            $InsertArray['RentHourPeriod'] = $TimeToUpdate;
        }
        $this->GunZ->InsertData('AccountItem', $InsertArray);
        $this->GunZ->UpdateData('Account', array('Coins' => ':coins'), array('AID' => ':aid'), array(':coins' => $CoinsLeft,':aid' => $User->getUserField('AID')));
        $User->setUserField('Coins', $CoinsLeft);
        
        return 'The Item `'.$this->ItemData['Name'].'` has been purchased, Enter to your inventory.';
    }
}