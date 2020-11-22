<?php
require_once dirname(__FILE__).'/action_category.class.php'; 

class Action_Category_Clan extends Action_Category
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
    
}