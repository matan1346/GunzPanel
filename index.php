<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

session_start();
session_regenerate_id(true);
header('Content-Type: text/html; charset=utf-8');
require_once './includes/config.php';
require_once './includes/globalvars.php';
require_once './includes/functions.php';
require_once './includes/classes/text_display.class.php';
//require_once './includes/classes/lang.class.php';
require_once './includes/classes/language.class.php';
require_once './includes/classes/mssql.class.php';
require_once './includes/classes/navigate.class.php';
require_once './includes/classes/user.class.php';
require_once './includes/classes/session.class.php';
require_once './includes/classes/rankings.class.php';
require_once './includes/classes/panel_permissions.class.php';
//require_once './includes/classes/action_category.class.php';
//require_once './protected/Twig-1.12.3/lib/Twig/Autoloader.php';
require_once './protected/Twig-1.5.0/lib/Twig/Autoloader.php';

require_once './protected/Twig-1.5.0/lib/Twig/ExtensionInterface.php';
require_once './protected/Twig-1.5.0/lib/Twig/Extension.php';
require_once './protected/ext/global.variables.twig.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('./public/html');

$twig = new Twig_Environment($loader,array('debug' => true,'cache' => false));

$twig->addExtension(new Twig_Extension_Debug());








$Page = 'home';


if(isset($_GET['page']))
    $Page = $_GET['page'];



$Lang = new Language((isset($_GET['lang']) ? $_GET['lang'] : null), true);

/*
$DataArray = array(
    'UserID1' => 'asdasd1',
    'UserID2' => 'asdasd2',
    'UserID' => 'asdasd',
    'Pass' => 123,
    'Grade' => 'מנהל משחק',
    'Time' => '3 ימים',
    'Value' => 'Matan',
    'Character' => 'Activity',
    'Value_Name' => 'שם',
    'Level' => 83,
    'Color' => 'Act^2ivity'
);
echo '<span style="color: white;">';

echo '<br /><br />'.$Lang2->getString('actions_logs', 'CHANGE_USERID', 'actionslogs/account')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'CHANGE_PASS', 'actionslogs/account')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'CHANGE_GRADE', 'actionslogs/account')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'BAN_USER', 'actionslogs/account')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'UNBAN_USER', 'actionslogs/account')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'CHANGE_VALUE', 'actionslogs/character')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'CHANGE_LEVEL', 'actionslogs/character')
->replace($DataArray)->getText(true);

echo '<br /><br />'.$Lang2->getString('actions_logs', 'SET_COLOR', 'actionslogs/character')
->replace($DataArray)->getText(true) . '</span>';
*/

$GunZ = new sqlGunz($_Data['Host'],$_Data['User'],$_Data['Pass'],$_Data['Base']);

$isAllowed = true;

/*
//New User Session
*/

//print_r($_SESSION);

$Session = new Session($GunZ);
$Session->SetUserSession();
$User = $Session->getUser();

if($User->isUserConnected())
    $User->setUserPermission();

/*Testing*/

//$UserPermissions = Panel_Permissions::setPermissions($GunZ, array(254,255));

//print_r($UserPermissions);

/*End Testing*/

$Page = $Session->CanAccess($Page);

$Navigate = new Navigate($Page, SITE_ROOT.'public/');

$getPHP = $Navigate->getFilesByType('php', 'public/php/');


foreach($getPHP as $page)
    require_once $page;
    
$Navigate->NavigateAuto($twig);


$GunZ = NULL;

?>