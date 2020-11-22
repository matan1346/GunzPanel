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
if(isset($_POST['SelectedData'], $_POST['Grade'], $_POST['SaveType']))
{
    $SaveType = intval($_POST['SaveType']);
    // 0 - SaveActions, 1 - SaveSelections
    if(in_array($SaveType, array(0,1)))
    {
        $Results['Progress'] = true;
            
        $Grade = intval($_POST['Grade']);
            
        $SuccessFlag = false;
        switch($SaveType)
        {
            case 0:
                $SuccessFlag = SaveActions($GunZ, $Grade, $_POST['SelectedData']);
                break;
            case 1:
                $SuccessFlag = SaveSelections($GunZ, $Grade, $_POST['SelectedData']);
                break;
            default:
                break;
        }
        
        if($SuccessFlag === true)
            $Results['Success'] = true;      
    }
    
}

echo json_encode($Results);

?>