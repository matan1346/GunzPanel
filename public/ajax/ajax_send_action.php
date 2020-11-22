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

/*
$Form = new Form($GunZ);

//print_r($_SESSION);

$Session = new Session($GunZ);
$Session->SetUserSession();

$User = $Session->getUser();


$Results = array('Progress' => false,'Success' => false,'NoErrors' => true);

if(isset($_POST['CategoryID'], $_POST['ActionID']))
{
    $Results['Progress'] = true;
    $Lang = new Language();
    
    $PCID = intval($_POST['CategoryID']);
    $PAID = intval($_POST['ActionID']);
    
    $ActionStatus = Action_Category::getActionStatus($GunZ, $PCID, $PAID);
    
    
    //print_r($ActionStatus);
    if($ActionStatus['Status'])
    {
        $Results['Active'] = ($ActionStatus['ActionActive'] && $ActionStatus['PermissionActive']);
        
        
        
        if($Results['Active'])
        {
            $CategoryName = str_replace(' ','_', $ActionStatus['CategoryName']);
        
            $ActionClass = 'Action_Category_' . $CategoryName;
            
            if (class_exists($ActionClass)) {
                //trigger_error("Unable to load action class: " . $CategoryName, E_USER_WARNING);
                
                $Action = new $ActionClass(
                    $GunZ, 
                    array(
                        'Name' => $CategoryName,
                        'ID' => $PCID
                    ),
                    array(
                        'Name' => str_replace(' ','_', $ActionStatus['ActionName']),
                        'ID' => $PAID
                    ),
                    $_POST
                );
        
                if($Action->SendAction())
                {
                    $Results['Success'] = true;
                    $Results['Result'] = $Action->getResult();
                }
                
                $Results['NoErrors'] = $Action->NoErrors();
                
                $Results['Messages'] = $Action->getSystemMessages();
                //$Results['NoErrors'] = $Action->NoErrors();
                
                //$Results['something'] = $Action->SendAction();
            }
            else
            {
                $Results['Messages'] = $Form->setFormMessage('System','NO_ACTION_SCRIPT','form_errors');//$Form->setMessage('System', 'Action/Permission is disabled.');
                $Results['NoErrors'] = false;
            }
            /*if (!class_exists($ActionClass, false)) {
                trigger_error("Unable to load action class: " . $CategoryName, E_USER_WARNING);
            }
            
            
        }
        else
        {
            $Results['Messages'] = $Form->setFormMessage('System','INACTIVE', 'form_errors');//$Form->setMessage('System', 'Action/Permission is disabled.');
            $Results['NoErrors'] = false;
        }
        
    }
    else
    {
        $Results['Messages'] = $Form->setFormMessage('System','CATEGORY_ACTION_DOES_NOT_EXIST','form_errors');//$Form->setMessage('System', 'Category/Action does not exist.');
        $Results['NoErrors'] = false;
    }
    
    
}
*/
echo json_encode(Send_Ajax_Action($GunZ, $User, $_POST));
?>