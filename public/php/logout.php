<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

unset($_SESSION['AID']);
unset($_SESSION['RegDate']);
unset($_SESSION['token']);
//unset($_SESSION);
//session_destroy();
$Session->setIsConnected(false);
$User->setSessionExists(false);
$User->setUserPermission(false);

echo 'troll';



$Navigate->newPage('login', SITE_ROOT.'public/');


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
        'LOGIN' => $Lang->getString('actions','LOGIN','actions/form')->getText()
    )
)
);

?>