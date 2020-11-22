<?php

/**
 * @author Matan Omesi
 * @email matanfxp@hotmail.co.il
 * @copyright 2015
 * @project GunzPanel
 */

class Panel_Permissions
{
    private static $PermissionsGrades = array();
    private $UserPermission = array();
    private $UserGradeID;
    private $AID;
    private static $GunZ;
    private static $Access;
    private static $CategoryActive = array();
    private static $PanelFormListSelectionID = array();
    
    public function __construct($AID)
    {
        
    }
    
    public static function getPanelFormListSelectionID()
    {
        return self::$PanelFormListSelectionID;
    }
    
    public static function getCategoryStatus()
    {
        return self::$CategoryActive;
    }
    
    public static function getAccessStatus()
    {
        return self::$Access;
    }
    
    public static function setAccessStatus($flag, $override = false)
    {
        self::$Access = $flag;
        if($override && !self::$Access)
            self::noGradePremisions();
    }
    
    public static function noGradePremisions()
    {
        //self::$Access = false;
        self::$CategoryActive = array();
        self::$PermissionsGrades = array('No_Permissions' => true);
    }
    
    public static function setPermissions($GunZ, $arrayGrades = array(), $custom = 0)
    {
        self::$GunZ = $GunZ;
        self::setAccessStatus(false, true);
        
        $MainTable = 'PanelPermissions';
        
        $Where = '';
        
        $custom = intval($custom);
        if($custom > 0)
        {
            $MainTable = 'PanelCustom';
            $Where = "WHERE (".$MainTable.".PCSID = '".$custom."')";
            
        }
        elseif(is_array($arrayGrades) && sizeof($arrayGrades) > 0)
        {
            array_walk($arrayGrades, 'intval');
            $Where = "WHERE (PanelPermissions.PGradeID = '".implode("' OR PanelPermissions.PGradeID = '", $arrayGrades)."')";
        }
            
        //echo $Where;
        
        $Query = self::$GunZ->prepare("SELECT PanelCategory.CategoryName,PanelCategory.Active AS CategoryActive,
        PanelAction.ActionName,PanelAction.Active AS ActionActive,PanelAction.PAID AS PAIDReal,
        ".$MainTable.".* FROM ".$MainTable."
        LEFT JOIN PanelAction ON (PanelAction.PAID = ".$MainTable.".PAID AND ".$MainTable.".PAID <> '0') OR (PanelAction.PAID > '0' AND ".$MainTable.".PAID = '0') AND PanelAction.PCID = ".$MainTable.".PCID
        LEFT JOIN PanelCategory ON PanelCategory.PCID = ".$MainTable.".PCID
        $Where"); //ORDER BY PanelPermissions.PGradeID DESC");
        
        /*
        echo "SELECT PanelCategory.CategoryName,PanelCategory.Active AS CategoryActive,
        PanelAction.ActionName,PanelAction.Active AS ActionActive,PanelAction.PAID AS PAIDReal,
        ".$MainTable.".* FROM ".$MainTable."
        LEFT JOIN PanelAction ON (PanelAction.PAID = ".$MainTable.".PAID AND ".$MainTable.".PAID <> '0') OR (PanelAction.PAID > '0' AND ".$MainTable.".PAID = '0') AND PanelAction.PCID = ".$MainTable.".PCID
        LEFT JOIN PanelCategory ON PanelCategory.PCID = ".$MainTable.".PCID
        $Where";
        */
        
        $Query->execute();
        
        if($data = $Query->fetchAll(PDO::FETCH_ASSOC))
        {
            foreach($data as $r)
            {
                $CategoryN = strtolower(str_replace(' ','_',$r['CategoryName']));
                
                self::$PanelFormListSelectionID[$r['PCID']]['CategoryName'] = $CategoryN;
                self::$PanelFormListSelectionID[$r['PCID']]['Actions'][] = array('PAID' => $r['PAIDReal'], 'PFLSID' => $r['PFLSID']);//[$r['PAIDReal']] = $r['PFLSID'];
                
                self::$CategoryActive[$CategoryN] = $r['CategoryActive'];
                $GradeField = (($custom > 0) ? 'Custom'.$custom : $r['PGradeID']).'_Permissions';
                
                /*self::$PermissionsGrades[$GradeField][$r['CategoryName']]['Actions'][] = array(
                'Name' => $r['ActionName'],
                'PAID' => $r['PAIDReal'],
                'Active' => (($r['CategoryActive']) ? $r['ActionActive'] : false)
                );
                */
                
                self::$PermissionsGrades[$GradeField][$r['PCID']]['Name'] = $r['CategoryName'];
                self::$PermissionsGrades[$GradeField][$r['PCID']]['PCID'] = $r['PCID'];
                self::$PermissionsGrades[$GradeField][$r['PCID']]['url'] = $r['CategoryName'];
                self::$PermissionsGrades[$GradeField][$r['PCID']]['sub_menu'][] = array(
                'Name' => $r['ActionName'],
                'PAID' => $r['PAIDReal'],
                'Active' => (($r['CategoryActive']) ? ($r['ActionActive'] && $r['Active']) : false),
                'Display' => (bool) $r['Display'],
                'url' => $r['ActionName']
                );
                
                
            }
            /*foreach(self::$PermissionsGrades as $Grade => $Perm)
            {
                foreach($Perm as $Catergory => $Action)
                    self::$PermissionsGrades[$Grade][$Catergory]
            }*/
            self::setAccessStatus(true);
            //echo '<pre>'.print_r(self::$PermissionsGrades, true).'</pre>';
        }
        
        return self;
    }
    
    public static function getPermission($Grade, $Custom)
    {
        if(!self::getAccessStatus())
            return self::$PermissionsGrades['No_Permissions'];
            
        return self::$PermissionsGrades[(($Custom > 0) ? 'Custom'.$Custom : $Grade).'_Permissions'];
        //return self::$PermissionsGrades[$Grade.'_Permissions'];
    } 
}

?>