<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */


if($_POST['SubmitL'])
{
    require_once './includes/classes/form.class.php';
    require_once './includes/classes/login.class.php';
    
    $Login = new Login($GunZ);
    
    $Login->setField('UserNameL','Username', $_POST['UserNameL']);
    $Login->setField('PassWordL','Password', $_POST['PassWordL']);
    
    
    /*$Login->IsLengthBetween('UserNameL', array('min' => 1, 'max' => 20));
    $Login->IsLengthBetween('PassWordL', array('min' => 1, 'max' => 20));
    */
    
    
    $Login->IsNotEmpty();
    $Login->IsValuesAllowed();
    if($Login->NoErrors())
    {
        //echo 'asd';
        if($Login->Login(array('Username' => 'UserNameL','Password' => 'PassWordL')))
        {
            $UserAID = $Login->getUserAID();
            $Session->SetUserSession($UserAID);
            //echo $Session->IsConnected() ? 'true' : 'false';
            //$Session->setSessionExists(true);
            $User->setSessionExists(true);
            $User->setNewUserData($UserAID);
            $User->setUserPermission();
            $Navigate->newPage('home', SITE_ROOT.'public/');
        }
        
    }
    
    $LoginMessage = $Login->getSystemMessages();
    //print_r($LoginMessage);
    
}

$Navigate->SetData(array(
    'LOGIN_SETTINGS' => array(
        'FIELDS' => array(
            'USER' => $Lang->getString('actions','USER','actions/form/login')->getText(),
            'PASSWORD' => $Lang->getString('actions','PASSWORD','actions/form/login')->getText(),
            'PANEL_CODE' => $Lang->getString('actions','PANEL_CODE','actions/form/login')->getText()
        ),
        'INPUTS' => array(
            'USER' => $Lang->getString('actions','USER','actions/form/login/inputs')->getText(),
            'PASSWORD' => $Lang->getString('actions','PASSWORD','actions/form/login/inputs')->getText(),
            'PANEL_CODE' => $Lang->getString('actions','PANEL_CODE','actions/form/login/inputs')->getText()
        ),
        'LOGIN_PANEL_TITLE' => $Lang->getString('actions','LOGIN_PANEL_TITLE','actions/form/login')->getText(),
        'LOGIN' => $Lang->getString('actions','LOGIN','actions/form')->getText(),
        'LOGIN_MESSAGES' => $LoginMessage
    )
)
);

?>