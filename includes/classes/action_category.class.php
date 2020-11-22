<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

class Action_Category extends Form
{   
    protected $User;
    private $Category;
    private $Action;
    private $Data;
    private $Result;
    
    public function __construct($GunZ, $User, $CategoryData, $ActionData, $Data)
    {
        parent::__construct($GunZ);
        $this->User = $User;
        $this->Category = $CategoryData;
        $this->Action = $ActionData;
        $this->Data = $Data;
    }
    
    public function RegisterLog()
    {
        
    }
    
    public function getCategory($key = null, $flagLower = false)
    {
        return  (array_key_exists($key, $this->Category) ? (is_string($this->Category[$key]) && $flagLower ? strtolower($this->Category[$key]): $this->Category[$key]) : $this->Category);
    }
    
    public function getAction($key = null, $flagLower = false)
    {
        return  (array_key_exists($key, $this->Action) ? (is_string($this->Action[$key]) && $flagLower ? strtolower($this->Action[$key]): $this->Action[$key]) : $this->Action);
    }
    
    public function getResult()
    {
        return $this->Result;
    }
    
    public function setResult($result)
    {
        $this->Result = $result;
    }
    
    public function getLangDisplayPrepare($strName, $Action = true)
    {
        return array('strName' => $strName,'strCat' => 'actions/form/' . $this->getCategory('Name', true) . '/' . ($Action ? $this->getAction('Name', true) : 'global'));
    }
    
    public function FilterSearchFieldsFromDB($Fields)
    {
        $Filter = array();
        foreach($Fields as $Field)
            $Filter[] = $Field['BelongTable'].'.'.$Field['BelongColumn'];
        return $Filter;
    }
    
    public function getSelectCategoryData($Cat)
    {
        switch($Cat)
        {
            case 'Account':
                return array('text' => array());
        }
    }
    
    public static function getPermissionQueryData()
    {
        global $User;
        
        $Data = array(
        'Table' => 'PanelPermissions',
        'Where' => "PanelPermissions.PGradeID = '".$User->getUserField('PanelGradeID')."'");
        
        $custom = $User->getUserField('ActionsCustomID');
        if($custom > 0)
        {
            $Data['Table'] = 'PanelCustom';
            $Data['Where'] = $Data['Table'].".PCSID = '".$custom."'";
            
        }
        return $Data;
    }
    
    public static function getActionStatus($GunZ, $CategorID, $ActionID)
    {
        $Data = Action_Category::getPermissionQueryData();
        
        $QueryStr = "
        
        SELECT 
            PanelCategory.CategoryName,PanelAction.ActionName,
            PanelAction.Active AS ActionActive,".$Data['Table'].".Active AS PermissionActive
        FROM PanelAction
            INNER JOIN PanelCategory ON PanelCategory.PCID = PanelAction.PCID
            INNER JOIN ".$Data['Table']." ON ".$Data['Table'].".PCID = PanelAction.PCID AND
        	   ((".$Data['Table'].".PAID > 0 AND ".$Data['Table'].".PAID = PanelAction.PAID) OR
        	   (".$Data['Table'].".PAID = 0))
         
        WHERE PanelAction.PCID = :category_id AND PanelAction.PAID = :action_id AND ".$Data['Where'].";
        ";
        //echo $QueryStr.'<br />';
        $Query = $GunZ->prepare($QueryStr);
        
        
        $Query->bindParam(':category_id', $CategorID, PDO::PARAM_INT);
        $Query->bindParam(':action_id', $ActionID, PDO::PARAM_INT);
        
        $Query->execute();
    
        if($a = $Query->fetch(PDO::FETCH_ASSOC))
        {
            $a['Status'] = true;
            return $a;
        }
        return array('Status' => false);
    }
    
    public function getSelectOptionData($selectionName, $optionID)
    {
        $Data = Action_Category::getPermissionQueryData();
        /*
        $Query = $this->GunZ->prepare("
        
        SELECT 
        PanelFormSelection.SelectionName, PanelFormSelection.PFSID,
        ".$Data['Table'].".Active AS PermissionActive,
        PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.Active AS OptionActive,
        PanelRegex.Name AS RegexName, PanelRegex.RegexRule
         FROM PanelFormSelection 
        	INNER JOIN ".$Data['Table']." ON ".$Data['Table'].".PCID = :pcid AND
        	(((".$Data['Table'].".PAID > 0 AND ".$Data['Table'].".PAID = :paid) OR
        	(".$Data['Table'].".PAID = 0))) AND
        	((".$Data['Table'].".PFLSID > 0 AND ".$Data['Table'].".PFLSID = PanelFormSelection.PFSID) OR
        	".$Data['Table'].".PFLSID = 0)
        	
        	INNER JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSID = PanelFormSelection.PFSID AND
        	PanelFormSelectionSetting.PFSSID = :optionid
        	INNER JOIN PanelRegex ON PanelRegex.PRID = PanelFormSelectionSetting.PRID
        	
        WHERE PanelFormSelection.SelectionName = :selectname AND ".$Data['Where'].";
        "); //ORDER BY PanelPermissions.PGradeID DESC");
*/

        $PGradeID = intval($this->User->getUserField('PanelGradeID'));
        
        //echo $PGradeID;
        /*
        $Query = $this->GunZ->prepare("
        
        SELECT 
        PanelFormSelection.SelectionName, PanelFormSelection.PFSID,
        ".$Data['Table'].".Active AS PermissionActive,
        PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.Active AS OptionActive,
        PanelRegex.Name AS RegexName, PanelRegex.RegexRule,
        PanelFormOption2.BelongTable,PanelFormOption2.BelongColumn
         FROM PanelFormSelection 
        	INNER JOIN ".$Data['Table']." ON ".$Data['Table'].".PCID = :pcid AND
        	(((".$Data['Table'].".PAID > 0 AND ".$Data['Table'].".PAID = :paid) OR
        	(".$Data['Table'].".PAID = 0))) AND
        	((".$Data['Table'].".PFLSID > 0 AND ".$Data['Table'].".PFLSID = PanelFormSelection.PFSID) OR
        	".$Data['Table'].".PFLSID = 0)
        	
        	INNER JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSID = PanelFormSelection.PFSID AND
        	PanelFormSelectionSetting.PFSSID = :optionid
            INNER JOIN PanelFormOption2 ON PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID
        	LEFT JOIN PanelRegex ON PanelRegex.PRID = PanelFormOption2.PRID
        	
        WHERE PanelFormSelection.SelectionName = :selectname AND (PanelFormSelectionSetting.Grades = '".$PGradeID."' OR PanelFormSelectionSetting.Grades LIKE '".$PGradeID.",%' OR PanelFormSelectionSetting.Grades LIKE '%,".$PGradeID."' OR PanelFormSelectionSetting.Grades LIKE '%,".$PGradeID.",%')");//.$Data['Where'].";
        //"); //ORDER BY PanelPermissions.PGradeID DESC");
        */
        $Query = $this->GunZ->prepare("
        
        SELECT 
        PanelFormSelection.SelectionName, PanelFormSelection.PFSID,
        ".$Data['Table'].".Active AS PermissionActive,
        PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.Active AS OptionActive,
        PanelRegex.Name AS RegexName, PanelRegex.RegexRule,
        PanelFormOption2.BelongTable,PanelFormOption2.BelongColumn
         FROM PanelFormGradeSelect 
        	INNER JOIN ".$Data['Table']." ON ".$Data['Table'].".PCID = :pcid AND
        	 ".$Data['Table'].".PAID = :paid
        	
        	INNER JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSSID = PanelFormGradeSelect.PFSSID AND
        	PanelFormSelectionSetting.PFSSID = :optionid
            LEFT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormSelectionSetting.PFSID
            
            INNER JOIN PanelFormOption2 ON PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID
        	LEFT JOIN PanelRegex ON PanelRegex.PRID = PanelFormOption2.PRID
        	
        WHERE PanelFormSelection.SelectionName = :selectname AND PanelFormSelectionSetting.Active = '1' AND (PanelFormGradeSelect.Grade = '".$PGradeID."')");//.$Data['Where'].";
        //"); //ORDER BY PanelPermissions.PGradeID DESC");
        
/*
        $Query = $this->GunZ->prepare("
        
        SELECT 
        PanelFormSelection.SelectionName, PanelFormSelection.PFSID,
        PanelAction.PCID, PanelAction.ActionName, PanelAction.PAID,
        ".$MainTable.".Active AS PermissionActive,
        PanelFormSelectionSetting.OptionValue, PanelFormSelectionSetting.Active AS OptionActive,
        PanelRegex.Name AS RegexName, PanelRegex.RegexRule
         FROM PanelFormSelection 
        	INNER JOIN PanelAction ON PanelAction.PAID = PanelFormSelection.PAID
        	INNER JOIN ".$MainTable." ON ".$MainTable.".PCID = PanelAction.PCID AND
        	(((".$MainTable.".PAID > 0 AND ".$MainTable.".PAID = PanelAction.PAID) OR
        	(".$MainTable.".PAID = 0))) AND
        	((".$MainTable.".PFLSID > 0 AND ".$MainTable.".PFLSID = PanelFormSelection.PFSID) OR
        	".$MainTable.".PFLSID = 0)
        	
        	INNER JOIN PanelFormSelectionSetting ON PanelFormSelectionSetting.PFSID = PanelFormSelection.PFSID AND
        	PanelFormSelectionSetting.PFSSID = :optionid
        	INNER JOIN PanelRegex ON PanelRegex.PRID = PanelFormSelectionSetting.PRID
        	
        WHERE PanelFormSelection.SelectionName = :selectname AND ($Where);
        "); //ORDER BY PanelPermissions.PGradeID DESC");
*/
        $Query->bindParam(':pcid', $this->getCategory('ID'), PDO::PARAM_INT);
        $Query->bindParam(':paid', $this->getAction('ID'), PDO::PARAM_INT);
        $Query->bindParam(':optionid', $optionID, PDO::PARAM_INT);
        $Query->bindParam(':selectname', $selectionName, PDO::PARAM_STR);
        //$Query->bindParam(':pgrade_id', intval($this->User->getUserField('PanelGradeID')), PDO::PARAM_STR);
        
        $Query->execute();
        //echo 'Field: '.$field.'<br />Value: '.$value.'<br />AID: '.$this->UserAID;
        if($a = $Query->fetch(PDO::FETCH_ASSOC))
        {
            $a['Status'] = true;
            return $a;
        }
        return array('Status' => false);
    }
    
    public function setSelectData($selectName, $SelectionName,$strName,$SelectValue, $FieldValue)
    {
        $FieldData = $this->getSelectOptionData($SelectionName, $SelectValue);
        
        $Display = $this->getLangDisplayPrepare($strName, false);
        
        if($FieldData['Status'])
        {
           $this->setField($selectName, $FieldData['RegexName'], $FieldValue, $FieldData['RegexRule'], array('Table' => $FieldData['BelongTable'],'Column' => $FieldData['BelongColumn']),$Display); 
        }
        else
        {
            $this->setFlagError(true);
            $this->setMessage($selectName, 'The selected option is invalid.');
        }
        /*
        $this->setField('Select', 'Number', intval($_POST['Account-Change-UserID-Select']));
        $this->IsNumber('Select');
        
        //call_user_func_array(array($instance, "MethodName"), $myArgs);
        
        if($this->EqualsTo('Select', 0, 2))
        {
            $this->setField('Text', 'LimitedText', $_POST['Account-Change-UserID-Text']);
            $this->IsLengthBetween('Text', array('max' => 3));
        }
        else
        {
            $this->setField('Text', 'Number', $_POST['Account-Change-UserID-Text']);
            $this->IsNumber('Text');
        }*/
    }
    
    public function SendAction()
    {
        if(isset($_POST['Submit_'.$this->getAction('Name')]))
            return $this->{'Action_'.$this->getAction('Name')}();
        return false;
        
    }
}

?>