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
require_once '../../includes/classes/action_category.class.php';



$GunZ = new sqlGunz($_Data['Host'],$_Data['User'],$_Data['Pass'],$_Data['Base']);


$Session = new Session($GunZ);
$Session->SetUserSession();
$User = $Session->getUser();

if($User->isUserConnected())
    $User->setUserPermission();

$Results = array('Progress' => false,'Success' => false);
if(isset($_POST['Grade']))
{
    $Results['Progress'] = true;
    
    $Grade = intval($_POST['Grade']);
    
    Panel_Permissions::setPermissions($GunZ, array($Grade), 0);
    
    $GradesData = Panel_Permissions::getPermission($Grade, 0);
    
    //Get Selected Options By Grade
    $Results['Selections'] = getOptionsByGrade($GunZ, $Grade);
            
    $Results['HasActions'] = true;
    if(is_array($GradesData) && sizeof($GradesData) > 0)
    {
        $Results['GradesData'] = $GradesData;
    }
    else
        $Results['HasActions'] = false;


    $Results['Grade'] = $Grade;
}

echo json_encode($Results);

?>