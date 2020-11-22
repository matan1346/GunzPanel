<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

//echo 'lol';

//$queryGetSelects = $GunZ->SelectData('PanelFormSelection INNER JOIN', array(''));




$prepare = $GunZ->query(
    'SELECT PanelFormSelectionSetting.*,PanelFormSelection.* FROM PanelFormSelection 
        LEFT JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSID = PanelFormSelection.PFSID');

/**
 * 
 * [PFSID] => 1
            [SelectionName] => GRADE_SELECT
            [PAID] => 3
            [MorePAID] => 0
            [Grade] => 0
            [PFSSID] => 1
            [PFOID] => 1
            [GroupName] => 0
            [Grades] => 256,255
            [DisplayOrder] => 1
            [OptionValue] => 0
            [PRID] => 1
            [Active] => 1
 * 
 * */

$Selections = array();
$Options = array();

if($prepare)
{
    $getOptionData = $GunZ->query('SELECT * FROM PanelFormOption2');
    $OptionsData = $getOptionData->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($OptionsData as $Option)
    {
        $Options[$Option['PFOID']] = $Option;
    }
    
    $results = $prepare->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($results as $resultItem)
    {
        if(!array_key_exists('PFSID_'.$resultItem['PFSID'], $Selections))
        {
            $Selections['PFSID_'.$resultItem['PFSID']] = array(
                'SelectionName' => $resultItem['SelectionName'],
                'PFSID'         => $resultItem['PFSID'],
                'Options'       => array()
            );
        }
        
        $Selections['PFSID_'.$resultItem['PFSID']]['Options'][] = array(
            'PFOID'         => $resultItem['PFOID'],
            'GroupName'     => $resultItem['GroupName'],
            'Grades'        => $resultItem['Grades'],
            'DisplayOrder'  => $resultItem['DisplayOrder'],
            'OptionValue'   => $resultItem['OptionValue'],
            'PRID'          => $resultItem['PRID'],
            'Active'        => $resultItem['Active']
        );
    }
    
    
    
    /**
     * 
     * TODO list
     * 
     * go to global.variables.twig.php
     * start work on ALL Options List AS a varible
     * get a List of selections that takes data from the options list in view HTML
     * Example $UserSelection['CLAN_SELECT'] = array(
     *      //PFOID..
     *      1,2,3
     * )
     * In View page we use it like this:
     * OPTIONS[$UserSelection['CLAN_SELECT'][]['PFOID']]-> GET Option DATA{
     *      PFOID,Path,Translated,Value,PRID,BelongTable,BelongColumn
     * }
     * 
     * So we have the option of display all the options at once, all the options for each selection
     * without making duplicate data.
     * 
     * an Array for User Selections.
     * an Array of ALL the Options Data Orginized
     * 
     * */
    
    
    
    $Navigate->SetData(array('SELECTION_LIST' => $Selections,'SELECTION_OPTIONS' => $Options));
    
    
    
    //print_r($Options);
}





function setTranslateSelection($data, $Lang)
{
    $root_path = 'select_options';
    
    foreach($data as $SelectionType => $SelectionData)
    {
        foreach($SelectionData as $ItemKey => $ItemValue)
        {
            //echo $root_path.'/'.$SelectionType.'/'.$ItemKey.'<br />';
            $data[$SelectionType][$ItemKey]['Translated'] = $Lang->getString($root_path, $ItemKey, $root_path.'/'.$SelectionType)->getText();
        }    
    }
    return $data;
}


$Selection_Info = array();

$getSelections = $GunZ->query('SELECT PFSID,SelectionName FROM PanelFormSelection');
if($getSelections)
{
    $Selection_Info['selections_name'] = array();
    
    $SelResults = $getSelections->fetchAll(PDO::FETCH_ASSOC);
    foreach($SelResults as $SelItem)
        $Selection_Info['selections_name'][$SelItem['SelectionName']] = $SelItem;
}

$getOptions = $GunZ->query('SELECT PFOID,OptionPath,BelongTable,BelongColumn FROM PanelFormOption2');
if($getOptions)
{
    $Selection_Info['options'] = array();
    
    $OpResults = $getOptions->fetchAll(PDO::FETCH_ASSOC);
    foreach($OpResults as $OpItem)
        $Selection_Info['options'][$OpItem['OptionPath']] = $OpItem;
}

$getGroups = $GunZ->query('SELECT GroupName FROM PanelFormSelectionSetting GROUP BY GroupName');
if($getGroups)
{
    $Selection_Info['groups'] = array();
    
    $GrResults = $getGroups->fetchAll(PDO::FETCH_ASSOC);
    foreach($GrResults as $GrItem)
        $Selection_Info['groups']['GROUP_'.$GrItem['GroupName']] = $GrItem;
}

$Selection_Info = setTranslateSelection($Selection_Info, new Language('he'));


//echo '<pre>'.print_r($Selection_Info, true).'</pre>';
/*
$getAction = Language::getLangsOfString('ACTIONS_OPEN');
var_dump($getAction['en']['@attributes']);

//echo 'asd';
/*
$XmlFile = './public/lang/en/actions.xml';
if(file_exists($XmlFile))
{
    $xmlFileLoad = simplexml_load_file($XmlFile);
    //print_r($xmlFileLoad->buttons->str['@attributes']);
    
    $getAtions = $xmlFileLoad->xpath('/actions/buttons/str[@name="ACTIONS_CLOSE"]');
    
    $Actions = $getAtions[0]->attributes();
    
    
    print_r(Language::getLangsOfString('POLICE'));
    
    //echo $getAtions[0];
    
    //print_r($getAtions[0]->toString());
    
    /*foreach($getAtions[0]->attributes() as $a => $b) {
    echo $a,'="',$b,"\"\n";
}

    
    
    
    
    //$XmlElement = new SimpleXMLElement($xmlFileLoad->);
    //var_dump($XmlElement);
}
*/
//simplexml_load_file

//$xml = new SimpleXMLElement('./public/lang/en/actions.xml');



$Navigate->setNavigateAuto(true);

?>