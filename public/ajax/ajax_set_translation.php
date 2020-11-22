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
//var_dump($_POST);
//die();
if(isset($_POST['id_trans'], $_POST['lang_save']))
{
    $Results['Progress'] = true;
    
    $translate_id = intval($_POST['id_trans']);
    $lang_save = $_POST['lang_save'];
    
    
    //$GLOBALS['_CONFIG']['LANG']
    
    
    $getData = $GunZ->SelectData('PanelFormOption2',array('OptionPath'),array('PFOID' => $translate_id));
    
    //print_r($getData);
    if(is_array($getData) && sizeof($getData) > 0)
    {
        $Results['Data'] = Language::setStringOfLang($getData[0]['OptionPath'], $lang_save);//'NORMAL');
        $Results['Success'] = true;
        
    }
    
    
    
    /*
    $XmlFile = './public/lang/en/actions.xml';
if(file_exists($XmlFile))
{
    $xmlFileLoad = simplexml_load_file($XmlFile);
    //print_r($xmlFileLoad->buttons->str['@attributes']);
    
    $getAtions = $xmlFileLoad->xpath('/actions/buttons/str[@name="ACTIONS_CLOSE"]');
    
    $Actions = $getAtions[0]->attributes();
    
    echo $getAtions[0];
    
    //print_r($getAtions[0]->toString());
    
    /*foreach($getAtions[0]->attributes() as $a => $b) {
    echo $a,'="',$b,"\"\n";
}

    
    
    
    
    //$XmlElement = new SimpleXMLElement($xmlFileLoad->);
    //var_dump($XmlElement);
}
*/
}

echo json_encode($Results);

?>