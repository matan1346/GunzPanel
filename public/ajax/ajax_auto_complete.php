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

$Results = array('Progress' => false,'Success' => false,'CompleteSuggestion' => array());

if(isset($_POST['InputValue']))
{
    $Results['Progress'] = true;
    //echo 'INPUT: '.$_POST['InputValue'].'<br />';
    
    //$Action = new Action_Category($GunZ, $_POST['CategoryID']);
    
    $PCID = intval($_POST['CategoryID']);
    $PAID = intval($_POST['ActionID']);
    
    $ActionStatus = Action_Category::getActionStatus($GunZ, $PCID, $PAID);
    
    if($ActionStatus['Status'])
    {
        $Results['Active'] = ($ActionStatus['ActionActive'] && $ActionStatus['PermissionActive']);
        
        if($Results['Active'])
        {
            $Action = new Action_Category(
                $GunZ,
                $User,
                array(
                    'Name' => str_replace(' ','_', $ActionStatus['CategoryName']),
                    'ID' => $PCID
                ),
                array(
                    'Name' => str_replace(' ','_', $ActionStatus['ActionName']),
                    'ID' => $PAID
                ),
                $_POST
            );
            
            $SelectionData = $Action->getSelectOptionData($_POST['SelectName'] . '_SELECT', intval($_POST['SelectValue']));
            
            $getAuto = $GunZ->SelectData($SelectionData['BelongTable'], array('TOP 5 '.$SelectionData['BelongColumn']),array($SelectionData['BelongColumn']." LIKE :auto"),array(':auto' => $_POST['InputValue'].'%'), array($SelectionData['BelongColumn'] => 'ASC'));
    
            if(is_array($getAuto))
            {
                $Results['Success'] = true;
                $Results['CompleteSuggestion'] = $getAuto;
                $Results['ColumnName'] = $SelectionData['BelongColumn'];
            }
            
        }
    }
    
    
    
}



echo json_encode($Results);
?>