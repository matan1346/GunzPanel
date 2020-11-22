<?php
/*
function printHTML($NavigatePage, $data = array())
{
    $css = $NavigatePage->getCSSFiles();
    $js = $NavigatePage->getJSFiles();
    $Page = $NavigatePage->getHTMLFiles();
    
    $top5players = top5players($GLOBALS['GunZ']);
    $top5clans = top5clans($GLOBALS['GunZ']);
    
    $ArrayData = array('site_url' => SITE_URL,'title' => SITE_TITLE,'css_links' => $css,'js_links' => $js,'menu' => $GLOBALS['MenuPages'],'top_players' => $top5players,'top_clans' => $top5clans);
    
    foreach($data as $key => $value)
        $ArrayData[$key] = $value;
    
    echo $GLOBALS['twig']->render($Page[0], $ArrayData);
}
*/

function __autoload($class)
{
    if(file_exists(dirname(__FILE__).'/classes/'.strtolower($class) . '.class.php'))
    {
        require_once dirname(__FILE__).'/classes/'.strtolower($class) . '.class.php';
    }
    /*    echo 'lol';
    else
        echo 'no!';
    */
    //echo dirname(__FILE__);
    

    // Check to see whether the include declared the class
    if (!class_exists($class, false)) {
        trigger_error("Unable to load class: $class", E_USER_WARNING);
    }
}

function getSelectOptions($GunZ, $SelectName, $Grade = -1)
{
    $Grade = intval($Grade);
    $Add = '';
    if($Grade >= 0)
        $Add = " AND (PanelFormGradeSelect.Grade = '".$Grade."')";
    
    $Query = $GunZ->prepare("
        
        SELECT 
        PanelFormSelection.SelectionName, PanelFormSelection.PFSID,
        
        PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.Active AS OptionActive,
        
        PanelFormOption2.BelongTable,PanelFormOption2.BelongColumn
         FROM PanelFormGradeSelect 
        	
        	
        	INNER JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSSID = PanelFormGradeSelect.PFSSID
            LEFT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormSelectionSetting.PFSID
            
            INNER JOIN PanelFormOption2 ON PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID
        	
        	
        WHERE PanelFormSelection.SelectionName = :selectname AND PanelFormSelectionSetting.Active = '1' ".$Add);//.$Data['Where'].";
    //--INNER JOIN PanelPermissions ON PanelPermissions.PCID = :pcid AND PanelPermissions.PAID = :paid
    
    /*$Query->bindParam(':pcid', $this->getCategory('ID'), PDO::PARAM_INT);
    $Query->bindParam(':paid', $this->getAction('ID'), PDO::PARAM_INT);*/
    $Query->bindParam(':selectname', $SelectName, PDO::PARAM_STR);
    //$Query->bindParam(':pgrade_id', intval($this->User->getUserField('PanelGradeID')), PDO::PARAM_STR);
    
    $Query->execute();
    //echo 'Field: '.$field.'<br />Value: '.$value.'<br />AID: '.$this->UserAID;
    if($a = $Query->fetchAll(PDO::FETCH_ASSOC))
    {
        //print_r(sizeof($a));
        $a['Status'] = true;
        return $a;
    }
    return array('Status' => false);
}

function getSelectOptionsByGradeQuery($Grade)
{
    /*return "SELECT  PanelFormSelection.*, PanelFormSelectionSetting.DisplayOrder,PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID,PanelFormOption2.OptionPath,PanelFormSelectionSetting.OptionValue FROM PanelFormSelectionSetting

			LEFT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormSelectionSetting.PFSID	
        
            RIGHT JOIN PanelFormOption2 ON (PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID)
        
			WHERE PanelFormSelectionSetting.Active = '1' AND (PanelFormSelectionSetting.Grades = '".$Grade."' OR PanelFormSelectionSetting.Grades LIKE '".$Grade.",%' OR PanelFormSelectionSetting.Grades LIKE '%,".$Grade."' OR PanelFormSelectionSetting.Grades LIKE '%,".$Grade.",%')
        
            GROUP BY PanelFormSelection.PAID,PanelFormSelection.MorePAID,PanelFormSelection.PFSID,
            PanelFormSelection.SelectionName,PanelFormSelection.Grade,PanelFormSelectionSetting.DisplayOrder,PanelFormOption2.OptionPath,
            PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID
            
            ORDER BY PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.DisplayOrder;";
   /**/
   return "SELECT  PanelFormSelection.*, PanelFormSelectionSetting.DisplayOrder,PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID,PanelFormOption2.OptionPath,PanelFormSelectionSetting.OptionValue FROM PanelFormGradeSelect

			LEFT JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSSID = PanelFormGradeSelect.PFSSID
            
            LEFT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormSelectionSetting.PFSID	
        
            RIGHT JOIN PanelFormOption2 ON (PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID)
        
			WHERE PanelFormSelectionSetting.Active = '1' AND (PanelFormGradeSelect.Grade = '".$Grade."')
        
            GROUP BY PanelFormSelection.PAID,PanelFormSelection.MorePAID,PanelFormSelection.PFSID,
            PanelFormSelection.SelectionName,PanelFormSelection.Grade,PanelFormSelectionSetting.DisplayOrder,PanelFormOption2.OptionPath,
            PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID
            
            ORDER BY PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.DisplayOrder;";
}

function getOptionsByGrade($GunZ, $Grade)
{
    
    
    $Query = $GunZ->prepare(getSelectOptionsByGradeQuery($Grade));
        
    $Query->execute();
    
    
    $Selections = array();
    if($data = $Query->fetchAll(PDO::FETCH_ASSOC))
    {
        foreach($data as $r)
        {
            //if(is_null($r['CategoryName'])) echo 'lol';
            //$Selections[$r['SelectionName']]['CategoryName'] = (is_null($r['CategoryName']) ? 'global' : $r['CategoryName']);
            //$Selections[$r['SelectionName']]['ActionName'] = (is_null($r['ActionName']) ? 'global' : $r['ActionName']);
            $Selections[$r['SelectionName']]['SELECT'][] = array('order' => $r['DisplayOrder'],'group' => $r['GroupName'],'text' => $r['OptionPath'],'value' => $r['OptionValue'],'option_id' => $r['PFSSID']);
            //array('text' => $r['Option_Text'],'value' => $r['Option_Value']);
        }
    }
    
    return $Selections;
}

function SortSelectionByCategory($Selections)
{
    
    $NewSort = array();
    
    foreach($Selections as $SelectName => $Value)
    {
        $NewSort[$Value['PCID']]['Name'] = $Value['CategoryName'];
        $NewSort[$Value['PCID']]['Actions']['Name'] = $Value['ActionName'];
        //Must Change Panel PAID from INT to VARCHAR for multi PAID`s
        $NewSort[$Value['PCID']]['Actions']['SELECTIONS'][$Value['PAID']][] = array(
            'SelectName' => $SelectName,'Select' => $Value['SELECT']
        );
    }
    
    return $NewSort;
}

function getAllSelectionList($GunZ, $Lang, $User)
{
    $QueryPrepare = 'SELECT PanelFormSelection.*,PanelFormSelectionSetting.Grades,PanelFormSelectionSetting.DisplayOrder,PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID,PanelFormOption2.OptionPath,PanelFormSelectionSetting.OptionValue FROM PanelFormSelectionSetting

			LEFT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormSelectionSetting.PFSID
            
            LEFT JOIN PanelFormOption2 ON (PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID)--RIGHT JOIN PanelFormOption2 ON (PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID)
        
            GROUP BY PanelFormSelection.PAID,PanelFormSelection.MorePAID,PanelFormSelection.PFSID,
            PanelFormSelection.SelectionName,PanelFormSelection.Grade,PanelFormSelectionSetting.Grades,PanelFormSelectionSetting.DisplayOrder,PanelFormOption2.OptionPath,
            PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID
            
            ORDER BY PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.DisplayOrder;';
            
   
    $Query = $GunZ->prepare($QueryPrepare);
        
    $Query->execute();
    
    
    $Selections = array();
    if($data = $Query->fetchAll(PDO::FETCH_ASSOC))
    {
        foreach($data as $r)
        {
            //if(is_null($r['CategoryName'])) echo 'lol';
            //$Selections[$r['SelectionName']]['CategoryName'] = (is_null($r['CategoryName']) ? 'global' : $r['CategoryName']);
            //$Selections[$r['SelectionName']]['ActionName'] = (is_null($r['ActionName']) ? 'global' : $r['ActionName']);
            //$Selections[$r['SelectionName']]['PCID'] = $r['PCID'];
            //$Selections[$r['SelectionName']]['PAID'] = $r['PAID'];
            $Selections[$r['SelectionName']]['PFSID'] = $r['PFSID'];
            $Selections[$r['SelectionName']]['SELECTION_NAME_TR'] = $Lang->getString('select_options',$r['SelectionName'],'select_options/selections_name')->getText();
            //$Selections[$r['SelectionName']]['Grades'] = $r['Grades'];
            //$Selections[$r['SelectionName']]['MorePAID'] = $r['MorePAID'];
            $Selections[$r['SelectionName']]['SELECT'][] = array('order' => $r['DisplayOrder'],'group' => $r['GroupName'],'text' => $r['OptionPath'],'value' => $r['OptionValue'],'option_id' => $r['PFSSID']);
            //array('text' => $r['Option_Text'],'value' => $r['Option_Value']);
        }
    }
    
    //print_r($Selections);
    
    $Ext = new Global_Variables_Twig_Extension();
    
    $Selections = $Ext->TranslateSelectionForm($Selections, $Lang);
    
    return $Selections;//SortSelectionByCategory($Selections);
}

function TranslateActionStatus($ActionS)
{
    return ($ActionS == 'true') ? 1 : 0;
}

function SaveActions($GunZ, $Grade, $SelectedActions)
{
    if(is_array($SelectedActions))
    {
        
        if(sizeof($SelectedActions) > 0)
        {
            $Exists = array();
            $ExistsData = array();
            foreach($SelectedActions as $Action)
            {
                //echo $Action['Exists'].',';
                if($Action['Exists'] == 'true')
                {
                    //echo $Action['ActionID'].',';
                    $Action['CategoryID'] = intval($Action['CategoryID']);
                    $Action['ActionID'] = intval($Action['ActionID']);
                    $Action['Display'] = TranslateActionStatus($Action['Display']);
                    $Action['Active'] = TranslateActionStatus($Action['Active']);
                    $Exists[] = $Action['ActionID'];
                    $ExistsData[$Action['ActionID']] = $Action;    
                }
                
            }
            
            $AddConditions = '';
            if(sizeof($Exists) > 0)
            {
                $AddConditions = " AND PAID NOT IN(".implode(",", $Exists).')';
            }
            $QueryPrepare = 'DELETE FROM PanelPermissions WHERE PGradeID = "'.$Grade.'"'.$AddConditions;
            
            $Query = $GunZ->prepare($QueryPrepare);
        
            $Query->execute();
            
            
            $ActionsExists = $GunZ->SelectData('PanelPermissions',array('PAID'), array('PGradeID' => $Grade));
    
            if(is_array($ActionsExists) && sizeof($ActionsExists) > 0)
            {
                $ExistList = array();
                foreach($ActionsExists as $ActionExt)
                    $ExistList[] = $ActionExt['PAID'];
                $needToInsert = array_diff($Exists, $ExistList);   
            }
            else
                $needToInsert = $Exists;
            
                
            foreach($needToInsert as $PAID)
            {
                //print_r($ExistsData[$PAID]);
                $GunZ->InsertData('PanelPermissions',array('PGradeID' => $Grade,'PCID' => $ExistsData[$PAID]['CategoryID'],'PAID' => $PAID,'Display' => $ExistsData[$PAID]['Display'],'Active' => $ExistsData[$PAID]['Active']));
            }
                
                
            
            //$UpdateArray = array_diff($Exists, $needToInsert);
            
            foreach($ExistList as $PAID)
                $GunZ->UpdateData('PanelPermissions',array('Display' => $ExistsData[$PAID]['Display'],'Active' => $ExistsData[$PAID]['Active']),array('PAID' => $PAID));
            
            
            //print_r($Exists);    
        }
        return true;
    }
    
    return false;
}

function SaveSelections($GunZ, $Grade, $SelectedSelections)
{
    //PanelFormGradeSelect
    $AddConditions = '';
    $SelectedFlag = false;
    if(is_array($SelectedSelections) && sizeof($SelectedSelections) > 0)
    {
        array_walk($SelectedSelections, 'intval');
        $AddConditions = " AND PFSSID NOT IN(".implode(",", $SelectedSelections).')';//(PFSSID <> '".implode("' AND PFSSID <> '", $SelectedSelections)."')";
        //echo $AddConditions;
        $SelectedFlag = true;
    }
    $QueryPrepare = 'DELETE FROM PanelFormGradeSelect WHERE Grade = "'.$Grade.'"'.$AddConditions;
    
    
    
    $Query = $GunZ->prepare($QueryPrepare);
        
    $Query->execute();
    
    if($SelectedFlag)
    {
        $SelectionExists = $GunZ->SelectData('PanelFormGradeSelect',array('PFSSID'), array('Grade' => $Grade));
    
        if(is_array($SelectionExists) && sizeof($SelectionExists) > 0)
        {
            $ExistList = array();
            foreach($SelectionExists as $SelectExists)
                $ExistList[] = $SelectExists['PFSSID'];
            $needToInsert = array_diff($SelectedSelections, $ExistList);   
        }
        else
            $needToInsert = $SelectedSelections;
            
        foreach($needToInsert as $PFSSID)
            $GunZ->InsertData('PanelFormGradeSelect',array('Grade' => $Grade,'PFSSID' => $PFSSID));    
    }
    
    return true;
}

function Send_Ajax_Search($GunZ, $User, $DATA)
{
    $Results = array('Progress' => false,'Success' => false,'NoErrors' => true);
    
    if(!isset($DATA['CategoryID']) || !isset($DATA['ActionID']))
        return $Results;
    
    $Form = new Form($GunZ);
    
    $Results['Progress'] = true;
    
    $PCID = intval($DATA['CategoryID']);
    $PAID = intval($DATA['ActionID']);
    
    $ActionStatus = Action_Category::getActionStatus($GunZ, $PCID, $PAID);
    
    
}

/**
 * @param $GunZ
 * @param $Category (Account/Character/Clan)
 * @param @ID (AID/CID/CLID)
 * @param $Type (BASIC/EXPANDED)
 * 
 * @return array
 * 
 * @example 
 *      Get_Ajax_Search_Info($GunZ, 'Character', 12, EXPANDED)
 *      -> array('Data' => array(
 *              'CharacterDetails'  => array(
 *                  'CharacterName'     => 'Activity',
 *                  'CID'               => '12',
 *                  'Sex'               => 'Male'),
 *              'HistoryDetails'    => array(
 *                  'The nickname of `LOL` has been changed to `Activity`')
 *         ));
 */

function Get_Ajax_Search_Info($GunZ, $User, $Data)
{
    $Results = array('Progress' => false,'Success' => false,'NoErrors' => true);
    
    if(!isset($Data['CategoryName']) || !isset($Data['ID']))
        return $Results;
    
    if(!in_array($Data['CategoryName'],array('Account','Character','Clan')))
        return $Results;
        
    $Form = new Form($GunZ);
    
    $Results['Progress'] = true;
    
    $CategoryClass = 'Action_Category_'.$Data['CategoryName'];
    $ID = intval($Data['ID']);
    
    if(!class_exists($CategoryClass))
    {
        $Results['Messages'] = $Form->setFormMessage('System', 'CATEGORY_ACTION_DOES_NOT_EXIST', 'form_errors');
        return $Results;
    }
    //$getData = $CategoryClass::Action_Search_Info($GunZ, $ID, $isExpanded);
    $getData = call_user_func(array($CategoryClass, "Action_Search_Info"), $GunZ, $User, $ID, 
    ((isset($_POST['isExpanded']) && is_bool($_POST['isExpanded'])) ? $_POST['isExpanded'] : false));
    
    $Results['ResultData'] = $getData['Data'][0];
    
    $Results['Messages'] = $Form->getSystemMessages();
    
    return $Results;
}



function Send_Ajax_Action($GunZ, $User, $DATA)
{
    $Results = array('Progress' => false,'Success' => false,'NoErrors' => true);

    if(!isset($DATA['CategoryID']) || !isset($DATA['ActionID']))
        return $Results;
    
    $Form = new Form($GunZ);
    
    $Results['Progress'] = true;
    
    $PCID = intval($DATA['CategoryID']);
    $PAID = intval($DATA['ActionID']);
    
    $ActionStatus = Action_Category::getActionStatus($GunZ, $PCID, $PAID);
    
    if(!$ActionStatus['Status'])
    {
        $Results['Messages'] = $Form->setFormMessage('System','CATEGORY_ACTION_DOES_NOT_EXIST','form_errors');//$Form->setMessage('System', 'Category/Action does not exist.');
        $Results['NoErrors'] = false;
        return $Results;
    }
    
    
    $Results['Active'] = ($ActionStatus['ActionActive'] && $ActionStatus['PermissionActive']);    
        
    if(!$Results['Active'])
    {
        $Results['Messages'] = $Form->setFormMessage('System','INACTIVE', 'form_errors');//$Form->setMessage('System', 'Action/Permission is disabled.');
        $Results['NoErrors'] = false;
        return $Results;
    }
        
        
    $CategoryName = str_replace(' ','_', $ActionStatus['CategoryName']);
    $ActionClass = 'Action_Category_' . $CategoryName;
    
    if (!class_exists($ActionClass))
    {
        $Results['Messages'] = $Form->setFormMessage('System','NO_ACTION_SCRIPT','form_errors');//$Form->setMessage('System', 'Action/Permission is disabled.');
        $Results['NoErrors'] = false;
        return $Results;
    }
    
    $Results['ActionName'] = $ActionStatus['ActionName'];
    //trigger_error("Unable to load action class: " . $CategoryName, E_USER_WARNING);
    $Action = new $ActionClass(
        $GunZ,
        $User,
        array(
            'Name' => $CategoryName,
            'ID' => $PCID
        ),
        array(
            'Name' => str_replace(' ','_', $ActionStatus['ActionName']),
            'ID' => $PAID
        ),
        $DATA
    );

    if($Action->SendAction())
    {
        $Results['Success'] = true;
        
    }
    
    //echo 'asd';
    $Action->TranslateSystemMessages();
    $Results['Result'] = $Action->getResult();
    $Results['NoErrors'] = $Action->NoErrors();
    $Results['Messages'] = $Action->getSystemMessages();
    //print_r($Results['Messages']);
/*if (!class_exists($ActionClass, false)) {
    trigger_error("Unable to load action class: " . $CategoryName, E_USER_WARNING);
}*/
    return $Results;
}


function getAllPanelActions($GunZ)
{
    return $GunZ->SelectData(
    'PanelCategory INNER JOIN PanelAction ON PanelAction.PCID = PanelCategory.PCID',
    array(
        'PanelCategory.PCID',
        'PanelCategory.CategoryName',
        'PanelCategory.Active AS CategoryActive',
        'PanelAction.PAID',
        'PanelAction.ActionName',
        'PanelAction.Active AS ActionActive'
    ));
}

function ClearFetchKeyNum(&$array)
{
    if(is_array($array))
    {
       for($i = 0,$size = sizeof($array);$i < $size;$i++)
        {
            if(is_array($array[$i]))
            {
                foreach($array[$i] as $k => $v)
                {
                    
                    if(is_numeric($k))
                    {
                        unset($array[$i][$k]);
                    }
                }
            }
        } 
    }             
}

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
{
    // Gets a proper hex string
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
    $rgbArray = array();
    
    if (strlen($hexStr) == 6)
    { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    }
    elseif(strlen($hexStr) == 3)
    { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    }
    else
    {
        return false; //Invalid hex color code
    }
    // returns the rgb string or the associative array
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
}

function IsServerUp($host, $port)
{
    if(empty($host) || empty($port))
		return false;


	$addr = gethostbyname($host);

	$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if(!$socket) {
		return false;
	}

	$res = @socket_connect($socket, $addr, $port);
	if(!$res) {
		return false;
	}


	socket_close($socket);
	return true;
}

function IsServerUpStatus($host, $port)
{
    if(IsServerUp($host,$port))
        return 'online';
    return 'offline';
}

function getServerData($GunZ, $server_id)
{
    $_SITE = $GLOBALS['_CONFIG'];
    $ServerData = array();
    $getServer = $GunZ->SelectData('ServerStatus',array('CurrPlayer','MaxPlayer','Port','ServerName','IP'),array('ServerID' => ':server_id'),array(':server_id' => $server_id));
    if(sizeof($getServer) > 0)
    {
        $ServerData = $getServer[0];
        if($_SITE['CheckServerStatus'] && !IsServerUp('localhost', $getServer[0]['Port']))
        {
            $ServerData['CurrPlayer'] = 0;
            $ServerData['Status'] = 'offline';
        }
        else
            $ServerData['Status'] = 'online';
        //$ServerData['Status'] = IsServerUpStatus($getServer[0]['IP'], $getServer[0]['Port']);
    }
    
    $getPlayers = $GunZ->SelectData('Character',array('COUNT(1) AS Players'),array('DeleteFlag' => 0));
    if(sizeof($getPlayers) > 0)
        $ServerData['Players'] = (($getPlayers[0]['Players'] > 0) ? array('Status' => 'online','Amount' => $getPlayers[0]['Players']) : array('Status' => 'offline','Amount' => 0));
        
    $getClans = $GunZ->SelectData('Clan',array('COUNT(1) AS Clans'),array('DeleteFlag' => 0));
    if(sizeof($getClans) > 0)
        $ServerData['Clans'] = (($getClans[0]['Clans'] > 0) ? array('Status' => 'online','Amount' => $getClans[0]['Clans']) : array('Status' => 'offline','Amount' => 0));
    
    return $ServerData;
}

function IsAccessAllowed()
{
    global $isAllowed;
    
    if((!isset($isAllowed) || $isAllowed !== true) && !isset($_GET['allowed']))
    {
        //header('HTTP/1.0 404 Not Found');
        die("Access Denied");
    }
}


function NameToColor($name)
{
    $Colors = $GLOBALS['Shop_nav_bar']['ColorName']['colors'];
    
    $previewName = '';
    $closeSpan = '</span>';
    
    $ColorActiveInName = false;
    
    //$ColorsSize = sizeof($Colors);
    
    for($i = 0,$nameLength = mb_strlen($name);$i < $nameLength;$i++)
    {
        if($name[$i] == '^' && ($i+1) < $nameLength)
        {
            if($ColorActiveInName)
                $previewName .= $closeSpan;
            $Sign = $name[$i+1];
            if(array_key_exists($Sign, $Colors))
                $previewName .= '<span style="color: #'.$Colors[$Sign].'">';
            $ColorActiveInName = true;
            $i++;
        }
        else
        {
            $previewName .= $name[$i];
        }
    }
    if($ColorActiveInName)
        $previewName .= $closeSpan;
    return array('RegularName' => $name,'ColorName' => $previewName);
}

function multiColors(&$value, $path)
{
    
    $NameKeys = explode('/', $path);
    //print_r($NameKeys);
    for($i = 0,$size = sizeof($NameKeys);$i < $size;$i++)
        if(array_key_exists($NameKeys[$i], $value))
            $value = &$value[$NameKeys[$i]];
    if(!is_string($value))
        $value = '';
    $value = NameToColor($value);
}


function multiNameColors(&$value, $key, $path = '')
{
    if(!empty($path))
    {
        //echo $path.' '.'asd<br />';
        
        
        
        $NameKeys = explode(',', $path);
        //print_r($NameKeys);
        for($i = 0,$size = sizeof($NameKeys);$i < $size;$i++)
            multiColors($value, $NameKeys[$i]);
        //print_r($value);
    }
    else if(is_string($value))
        $value = &NameToColor($value);
}

function top5players($GunZ)
{
    $top = 5;
    
    $Players = new Rankings('player', $GunZ, -1, $top);
    
    $getPlayers = $Players->execute()->getData(); 
    
    $num_players = sizeof($getPlayers);
    
    $holdingData = array();
    
    for($i = 0;$i < $num_players;$i++)
        $holdingData[$i] = array('Name' => NameToColor($getPlayers[$i]['Name']),'Level' => $getPlayers[$i]['Level']);
        
    for($i = $num_players;$i < $top;$i++)
        $holdingData[$i] = array('Name' => '--','Level' => '--');
        
    return $holdingData;
}

function top5clans($GunZ)
{
    $top = 5;

    $Clans = new Rankings('clan', $GunZ, -1, $top);
    
    $getClans = $Clans->execute()->getData();
    
    $num_clans = sizeof($getClans);
    
    $holdingData = array();
    
    for($i = 0;$i < $num_clans;$i++)
        $holdingData[$i] = array('Name' => NameToColor($getClans[$i]['Name']),'Point' => $getClans[$i]['Point']);
        
    for($i = $num_clans;$i < $top;$i++)
        $holdingData[$i] = array('Name' => '--','Point' => '--');
        
    return $holdingData;
}

function BestOf($GunZ, $type)
{
    $select = array('Name');
    switch($type)
    {
        case 'EXP':
                $select[] = 'XP';
                $order = array('XP' => 'DESC');
                //$query_str = 'SELECT TOP 1 Name,XP FROM Character WHERE '
                break;
        case 'KDR':
                $select[] = 'KillCount';
                $select[] = 'DeathCount';
                $order = array('KillCount' => 'DESC','DeathCount' => 'ASC');
                break;
        case 'KILL':
                $select[] = 'KillCount';
                $order = array('KillCount' => 'DESC');
                break;
        case 'DEATH':
                $select[] = 'DeathCount';
                $order = array('DeathCount' => 'DESC');
                break;
        case 'PLAYTIME':
                $select[] = 'PlayTime';
                $order = array('PlayTime' => 'DESC');
                break; 
    }
    
    if($Data = $GunZ->SelectData('Character', $select,array(),array(),$order))
    {
        if($type == 'KDR')
        {
            $Data[0]['KDR'] = (($Data[0]['KillCount'] != 0 && $Data[0]['DeathCount'] != 0) ? round(100*$Data[0]['KillCount']/($Data[0]['KillCount']+$Data[0]['DeathCount']), 2) : 0 ).'%';
        }
        array_walk($Data, 'multiNameColors', 'Name');
        return $Data[0];
    }
         
    return array('Name' => array('RegularName' => '','ColorName' => ''),'XP' => 0,'KRD' => 0,'KillCount' => 0,'DeathCount' => 0,'PlayTime' => 0);
    
    //$Data = $GunZ->SelectData('Character', $select,array(),array(),$order);
   
}


function getRankingPage($type, $max_rows, $per_page, $page_number, $where = array())
{
    $max_pages = ceil($max_rows/$per_page);
    if($page_number < 1 || $page_number > $max_pages) 
        $page_number = 1;
    
    
    $start_row = ($page_number - 1) * $per_page;

    $ranking = new Rankings($type, $GLOBALS['GunZ'], $start_row, $per_page, $max_rows, $max_pages);
    
    
    if(sizeof($where) > 0)
    {
        if(array_key_exists('Statement', $where))
            $ranking->setStatements($where['Statement']);
        if(array_key_exists('Bind', $where))
            $ranking->setBindParamss($where['Bind']);
    }
    
    
    $getRankingData = $ranking->execute()->getData();
    return $getRankingData;
}

function getRankingNavigatePages($each_side_pages, $curr_number, $max_pages, $per_page)
{
    $start_page = (($curr_number-$each_side_pages >= 1) ? $curr_number-$each_side_pages : 1);
    $end_page = (($curr_number+$each_side_pages <= $max_pages) ? $curr_number+$each_side_pages : $max_pages);
    
    
    return array('Start_Page_Navigate' => $start_page,'End_Page_Navigate' => $end_page,'Curr_Page_Navigate' => $curr_number,'Per_Page' => $per_page);
}

function UpdateRankPlayers($top = false)
{
    $top = ($top !== false) ? 'TOP '.$top : '';
    
    $query_str = 'UPDATE Character
                    SET Ranking = Char1.NewRank
                    FROM    (
                            SELECT '.$top.' CID
                                , Row_Number() OVER ( ORDER BY XP DESC,Level DESC,KillCount DESC,DeathCount ASC) AS NewRank
                            FROM Character WHERE DeleteFlag = 0
                            ) AS Char1
                        JOIN Character AS Char2
                            ON Char2.CID = Char1.CID';
    $query = $GLOBALS['GunZ']->Query('Update Players Ranking', $query_str);
    $query->execute();
    
    return array('affected' => $query->rowCount());
}

function UpdateRankClans($top = false)
{
    $top = ($top !== false) ? 'TOP '.$top : '';
    
    
    //UPDATE Clan SET Ranking=".$i.", RankIncrease=".($assoc['Ranking']-$i).", LastDayRanking=".$assoc['Ranking']." WHERE CLID=".$assoc['CLID']
    $query_str = 'UPDATE Clan
                    SET Ranking = Clan1.NewRank, RankIncrease = (Clan1.Ranking-Clan1.NewRank), LastDayRanking = Clan1.Ranking
                    FROM    (
                            SELECT '.$top.' CLID,Ranking
                                , Row_Number() OVER ( ORDER BY Point DESC,Wins DESC,Losses ASC) AS NewRank
                            FROM Clan WHERE DeleteFlag = 0
                            ) AS Clan1
                        JOIN Clan AS Clan2
                            ON Clan2.CLID = Clan1.CLID';
    $query = $GLOBALS['GunZ']->Query('Update Clans Ranking', $query_str);
    $query->execute();
    
    return array('affected' => $query->rowCount());
}

function mb_strcasecmp($str1, $str2, $encoding = null) {
    if (null === $encoding) { $encoding = mb_internal_encoding(); }
    return strcmp(mb_strtoupper($str1, $encoding), mb_strtoupper($str2, $encoding));
}


function in_2d_array($needle, $haystack) {
    foreach($haystack as $element) {
        if(in_array($needle, $element))
            return true;
    }
    return false;
}

function getClanMates($GunZ,$clan_id,$clan_user_grade,$page = 1)
{
    $clan_id = intval($clan_id);
    $clan_user_grade = intval($clan_user_grade);
    
    $query_str = 'SELECT COUNT(1) AS Rows FROM ClanMember WHERE CLID = :clan_id';
    $query = $GunZ->prepare($query_str);
    $query->bindValue(':clan_id', $clan_id,PDO::PARAM_INT);
    $query->execute();
    
    $getNumRows = $query->fetch(PDO::FETCH_ASSOC);
    
    
    $per_page = 16;
    
    $max_pages = ceil($getNumRows['Rows']/$per_page);
    
    $page_number = intval($page);
    
    if($page_number < 1 || $page_number > $max_pages)
        $page_number = 1;
    
    $start_from = ($page_number - 1) * $per_page;
    
    //echo 'per_page: '.$per_page.'<br />max_pages: '.$max_pages.'<br />page_number: '.$page_number.'<br />start_from: '.$start_from.'<br />';
    
    $query_str = "SELECT TOP :top_select Chars.Name As CharName,ClanMember.CID,ClanMember.Grade FROM
            ClanMember
             INNER JOIN dbo.Character AS Chars ON Chars.CID = ClanMember.CID
              WHERE ClanMember.CLID = :clan_id ORDER BY ClanMember.Grade ASC,ClanMember.ContPoint DESC";
    $query = $GunZ->prepare($query_str);
    
    
    $query->bindValue(':top_select', $per_page*$page_number, PDO::PARAM_INT);
    $query->bindValue(':clan_id', $clan_id, PDO::PARAM_INT);
    //$query->bindValue(':member_id', $lastRow['CMID'], PDO::PARAM_INT);
    $query->execute();
    
    $MatesData = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $Mates = array();
    $sizeof = sizeof($MatesData);
    
    $endLoop = $sizeof;
    if($sizeof > $per_page)
        $startLoop = (($per_page*$page_number > $sizeof) ? $sizeof-($per_page*$page_number-$sizeof) : $sizeof-($per_page)) ;
    else
        $startLoop = 0;
    
    $myCharacters = $GLOBALS['User']->getUserField('Characters');
    
    for($i = $startLoop;$i < $endLoop;$i++)
    {//#F5FF00
        $isBelongsToUser = ((array_key_exists($MatesData[$i]['CID'], $myCharacters)) ? 'border: 2px solid rgba(255, 255, 0, 0.2);' : '');//((array_key_exists($MatesData[$i]['CID'], $myCharacters)) ? 'color: #F5FF00 !important;' : '');
        $RoleNum = ((isSelectedMateIsAllowed($clan_user_grade, $MatesData[$i]['Grade'])) ? $MatesData[$i]['Grade'] : -1);
        $Role = ((array_key_exists($MatesData[$i]['Grade'], $GLOBALS['Clan_Grades'])) ? $GLOBALS['Clan_Grades'][$MatesData[$i]['Grade']] : 'Unknown');
        $Mates[] = array('Name' => $MatesData[$i]['CharName'],
                        'Role' => $Role,
                        'RoleNum' => $RoleNum,
                        'CID' => $MatesData[$i]['CID'],
                        'colorName' => $isBelongsToUser
        );
    }   
    
    return array('Mates' => $Mates,'Pages' => $max_pages);
}

function arrayDeleteKeys($array)
{
    return array_splice($array,8, sizeof($array)-8);
}


function getClanMatesView($GunZ,$clan_name,$page = 1)
{
    $query_str = 'SELECT COUNT(1) AS Rows FROM ClanMember LEFT JOIN Clan ON ClanMember.CLID = Clan.CLID WHERE Clan.Name = :clan_name';
    $query = $GunZ->prepare($query_str);
    $query->bindValue(':clan_name', $clan_name,PDO::PARAM_STR);
    $query->execute();
    
    $getNumRows = $query->fetch(PDO::FETCH_ASSOC);
    
    
    $per_page = 10;
    
    $max_pages = ceil($getNumRows['Rows']/$per_page);
    
    $page_number = intval($page);
    
    if($page_number < 1 || $page_number > $max_pages)
        $page_number = 1;
    
    $start_from = ($page_number - 1) * $per_page;
    
    //echo 'per_page: '.$per_page.'<br />max_pages: '.$max_pages.'<br />page_number: '.$page_number.'<br />start_from: '.$start_from.'<br />';
    
    $query_str = "SELECT TOP :top_select Clan.Name AS Name,Clan.EmblemUrl AS EmblemUrl,Clan.Ranking AS Rank,Clan.Wins AS Wins,Clan.Losses AS Losses,MasterCID,(SELECT Name FROM dbo.Character WHERE CID = Clan.MasterCID) AS MasterName,Clan.Point AS Points,ClanMember.Grade AS Role,ClanMember.RegDate,ClanMember.ContPoint,Chars.Name AS MemberName FROM dbo.Clan
 INNER JOIN dbo.ClanMember ON ClanMember.CLID = Clan.CLID
  INNER JOIN dbo.Character AS Chars ON Chars.CID = ClanMember.CID
   WHERE Clan.Name = :clan_name ORDER BY ClanMember.Grade ASC,ClanMember.ContPoint DESC";
    $query = $GunZ->prepare($query_str);
    
    
    $query->bindValue(':top_select', $per_page*$page_number, PDO::PARAM_INT);
    $query->bindValue(':clan_name', $clan_name, PDO::PARAM_STR);
    //$query->bindValue(':member_id', $lastRow['CMID'], PDO::PARAM_INT);
    $query->execute();
    
    $MatesData = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $Mates = array();
    $sizeof = sizeof($MatesData);
    
    $endLoop = $sizeof;
    if($sizeof > $per_page)
        $startLoop = (($per_page*$page_number > $sizeof) ? $sizeof-($per_page*$page_number-$sizeof) : $sizeof-($per_page)) ;
    else
        $startLoop = 0;
    
    $count = 0;
    for($i = $startLoop;$i < $endLoop;$i++)
    {//#F5FF00
        $MatesData[$i]['Count'] = $startLoop+(++$count);
        $MatesData[$i]['Role'] = ((array_key_exists($MatesData[$i]['Role'], $GLOBALS['Clan_Grades'])) ? $GLOBALS['Clan_Grades'][$MatesData[$i]['Role']] : 'Unknown'.$MatesData[$i]['Role']);
        $Mates[] = $MatesData[$i];
        //$count++;
    }
    $ClanData = array();
    if($count > 0)
    {
        $ClanData = array('Name' => $Mates[0]['Name'],
                            'EmblemUrl' => $Mates[0]['EmblemUrl'],
                            'MasterName' => $Mates[0]['MasterName'],
                            'Rank' => $Mates[0]['Rank'],
                            'Points' => $Mates[0]['Points'],
                            'Wins' => $Mates[0]['Wins'],
                            'Losses' => $Mates[0]['Losses'],
                            'NumMembers' => $getNumRows['Rows']);
        $Mates = array_map('arrayDeleteKeys',$Mates);
        //echo 'asdas';
    }
    //print_r($ClanData);
    //print_r($Mates);
    
    $each_side_page = 3;
    
    $RankingPage = getRankingNavigatePages($each_side_page, $page_number, $max_pages, $per_page);
    
    $first_top = ($page_number-1)*$per_page;
    
    return array('Mates' => $Mates,'PagesManage' => $RankingPage,'ClanDetails' => $ClanData,'MatesTop'=> array($first_top+1,$first_top+$per_page));
}

function getHighestGradeOfClanWithSameUser($ArrayGrades)
{
   $min = 100;
   foreach($ArrayGrades as $v)
   {
        if($v['Role'] < $min)
            $min = $v['Role'];
   }
   return $min;
}

function getClanChangeRoles($clan_user_role)
{
    $RolesButtonsState = array('Leader' => false,'Administrator' => false,'Member' => false,'Kick' => false);
    switch($clan_user_role)
    {
        case 1://Leader
            $RolesButtonsState = array_fill_keys(array_keys($RolesButtonsState), true);
            break;
        case 2://Admin
            $RolesButtonsState['Kick'] = true;
            break;
    }
    return $RolesButtonsState;
}

function isSelectedMateIsAllowed($player_grade, $mate_grade)
{
    $player_grade = intval($player_grade);
    $mate_grade = intval($mate_grade);
    
    return (($player_grade == 1 && $mate_grade != 1) || ($player_grade == 2 && $mate_grade > 2) || (($player_grade > 9) && $mate_grade == 9));
}