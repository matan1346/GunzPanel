<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

session_start();
//session_regenerate_id(true);
//header('Content-Type: text/html; charset=utf-8');
require_once '../../includes/config.php';
require_once '../../includes/globalvars.php';
require_once '../../includes/functions.php';
require_once '../../includes/classes/text_display.class.php';
require_once '../../includes/classes/language.class.php';
require_once '../../includes/classes/mssql.class.php';
require_once '../../includes/classes/user.class.php';
require_once '../../includes/classes/session.class.php';
require_once '../../includes/classes/panel_permissions.class.php';
require_once '../../includes/classes/form.class.php';
//require_once '../../includes/classes/action_category.class.php';



$GunZ = new sqlGunz($_Data['Host'],$_Data['User'],$_Data['Pass'],$_Data['Base']);

$Lang = new Language();

$Session = new Session($GunZ);
$Session->SetUserSession();

$User = $Session->getUser();


//$AccountSearch = Action_Category_Account::Action_Search_Info();

echo json_encode(Get_Ajax_Search_Info($GunZ, $User, $_POST)/*Send_Ajax_Action($GunZ, $User, $_POST)*/);
?>