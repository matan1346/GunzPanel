<?php
require_once dirname(__FILE__).'/action_category.class.php';  

class Action_Category_Account extends Action_Category
{
    public function __construct($GunZ, $User, $Category, $Action, $Data)
    {
        parent::__construct($GunZ, $User, $Category, $Action, $Data);
    }
    
    public function SendAction()
    {
        //return print_r($_POST, true);//$_POST['Submit_'.$this->getAction()];
        /*$this->getAction('Name') == 'Search' || */
        if(isset($_POST['Submit_'.$this->getAction('Name')]))
        {
            if(method_exists($this, 'Action_'.$this->getAction('Name')))
                return $this->{'Action_'.$this->getAction('Name')}();
            else
                $this->setFormMessage('System', 'NO_ACTION_SCRIPT','form_errors', 
                array(
                'action' => $this->Lang->getString('navigator', strtoupper($this->getAction('Name')),'navigator/'.strtolower($this->getCategory('Name')))->getText(),
                'category' => $this->Lang->getString('navigator', strtoupper($this->getCategory('Name')),'navigator')->getText()
                ));
            
        }
            //return (method_exists($this, 'Action_'.$this->getAction('Name'))) ? $this->{'Action_'.$this->getAction('Name')}() : false;
        //echo 'asd';
        
        return false;
        
    }
    
    
    public function Action_Change_UserID()
    {
        
        
        
        //$this->setMessage('System', Action_Category::getActionStatus($this->GunZ, 1, 1));
        
        $this->setSelectData('Select','USER_SELECT', 'SELECT_DATA',intval($_POST['account-change-userid-Select']), $_POST['account-change-userid-Text']);
        
        $this->setField('New-Text', 'LimitedText', $_POST['account-change-userid-New-Text'], '[^0-9a-zA-Z\_]', array(), $this->getLangDisplayPrepare('NEW_USER_TEXT'));
        
        //$this->setMessage('Message',$this->getFieldsValues());
        
        if($this->IsValuesAllowed())
        {
            $this->setResult('NAOR           TESTING !!!!2222222');
            return true;
        }
        
        //$this->setMessage('System', $this->getSelectOptionData('USER_SELECT', 10));
        
        
        //$this->IsLengthBetween('UserData', array('max' => 3));
        
        //$this->IsAllowed('UserData');
        
        //$Return['Messages'] = $this->getSystemMessages();
        //$Return['NoErrors'] = $this->NoErrors();
        
        //return $Return;
        
        //$this->setResult('TESTING STUFF!');
        
        //echo $this->getResult();
        
        //$this->setMessage('System', $this->getResult());
        /*if(!$this->NoErrors())
            return false;*/
        return true;
    }
    
    public function Action_Change_Password()
    {
        $this->setResult('CHANGE PASSWORD!');
        return true;
    }
    
    public function getSearchResult($selectName, $fullData = true)
    {
        $getSelectDB = $this->getField($selectName, 'DB');
        $getFields = getSelectOptions($this->GunZ, 'USER_SEARCH_FIELDS', $this->User->getUserField('PanelGradeID'));
        //print_r($getFields);
        //echo 'asdas';
        
        $Result = array('Success' => $getFields['Status']);
        //var_dump($getFields['Status']);
        if($getFields['Status'] == 1)
        {
            //echo 'asd';
            unset($getFields['Status']);
            //FilterSearchFieldsFromDB($getFields)$Fields = implode(',',FilterSearchFieldsFromDB($getFields));
            //print_r($this->FilterSearchFieldsFromDB($getFields), false);
            
            
            
            $MainTable = 'Account';
            if($getSelectDB['Table'] == 'Character')
                $MainTable = $MainTable.' INNER JOIN Character ON Character.AID = Account.AID';
            
            //echo $getSelectDB['Table'];
            $getData = $this->GunZ->SelectData(
                ($fullData) ? 'Account INNER JOIN Login ON Account.AID = Login.AID' : $MainTable,
                ($fullData) ? $this->FilterSearchFieldsFromDB($getFields) : array('Account.AID','Account.UserID'),
                array($getSelectDB['Table'].'.'.$getSelectDB['Column'] => ':value'),
                array(':value' => $this->getField($selectName, 'Value'))
            );
            //echo '$fullData';
            //print_r($getData);
            if(sizeof($getData) > 0)
                $Result['Data'] = $getData;
            else
                $Result['Success'] = false;
        }
        
        //$this->setResult($getSelectDB['Table'].'.'.$getSelectDB['Column']);
        
        
        return $Result;
    }
    
    public function Action_Search()
    {
        
        $this->setSelectData('Select','USER_SELECT', 'SELECT_DATA',intval($_POST['SelectValue']), $_POST['InputValue']);
        
        //$this->setField('New-Text', 'LimitedText', $_POST['SearchAcc'], '[^0-9a-zA-Z\_]', array(), $this->getLangDisplayPrepare('NEW_USER_TEXT'));
        
        //$this->setMessage('Message',$this->getFieldsValues());
        
        //print_r(getSelectOptions($this->GunZ, 'USER_SEARCH_FIELDS', $this->User->getUserField('PanelGradeID')));
        
        if($this->IsValuesAllowed())
        {
            $getSearch = $this->getSearchResult('Select', false);
            
            //var_dump($getSearch['Success']);
            if(!$getSearch['Success'])
            {
                $SelectDB = $this->getField('Select','db');
                /*$this->setFormMessage(
                    'Message',
                    'SEARCH_DOES_NOT_EXIST',
                    'form_errors',
                    array('key' => ':column:','keyvalue' => $this->getField('Select','value'))
                )
                ->setFlagError(true);
                */
                $this->setFlagError(true);
                
                /*$this->setResult(array(
                'NOT_EXISTS' => $this->Lang->getString('form_errors', 'SEARCH_DOES_NOT_EXIST', 'form_errors')->replace(
                    array(
                    'key' => ':column:',
                    'keyvalue' => $this->getField('Select','value'))
                    )->getText(true)
                ));
                */
                $this->setFormMessage('DATA', 'SEARCH_DOES_NOT_EXIST', 'form_errors');
                
                $this->setMessage('DATA', 'I am a message');
                $this->setMessage('DATA', 'I am a message2');
                $this->setMessage('SYSTEM', 'I am a system');
                
                return false;
            }
                
                
            foreach($getSearch['Data'] as $Row)
            {
                $this->setMessage('DATA', '<a class="Result-Get-Search-Info" data-category="Account"  href="ID:'.$Row['AID'].'">'.$Row['UserID'].'</a>');
            }
            
            /*$this->setMessage('DATA', 'I am a message');
            $this->setMessage('DATA', 'I am a message2');
            $this->setMessage('SYSTEM', 'I am a system');*/
            //$this->setMessage('Message', 'sad');
            $this->setResult('SEARCH!');//$this->setResult('TESTING !!!!2222222');
            return true;
        }
        
        /*
        $this->setResult('SEARCH!');
        return true;*/
    }
    
    /**
     * @param $GunZ
     * @param @ID (AID)
     * @param $Type (true/false)
     * 
     * @return array
     * 
     * @example 
     *      Get_Ajax_Search_Info($GunZ, 12, true)
     *      -> array('Data' => array(
     *              'CharacterDetails'  => array(
     *                  'CharacterName'     => 'Activity',
     *                  'CID'               => '12',
     *                  'Sex'               => 'Male'),
     *              'HistoryDetails'    => array(
     *                  'The nickname of `LOL` has been changed to `Activity`')
     *         ));
     */
    public static function Action_Search_Info($GunZ = NULL, $User, $AID, $isExpanded = false)
    {
        //$GunZ->SelectData('Account', )
        $Result = array('Success' => false);
        $getFields = getSelectOptions($GunZ, 'USER_SEARCH_FIELDS', $User->getUserField('PanelGradeID'));
        
        if($getFields['Status'] == 1)
        {
            unset($getFields['Status']);
            $Result['Success'] = true;
            $getData = $GunZ->SelectData(
                'Account INNER JOIN Login ON Account.AID = Login.AID',
                parent::FilterSearchFieldsFromDB($getFields),
                array('Account.AID' => $AID));
            if(sizeof($getData) > 0)
            {
                $Result['Data'] = $getData;
            }
        }
        return $Result;
    }
}