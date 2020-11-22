<?php

class Global_Variables_Twig_Extension extends Twig_Extension
{
    
    private $SelectKeysData = array('GROUPS' => array(),'OPTIONS' => array());
    
    public function getName()
    {
        return 'project';
    }
    
    
    /*
    
        PanelFormListSelection:
            
            PFLSID
            PFSID
            
        
    
    */
    
    public function getGroupsRules($Options)
    {
        $Rules = array();
        
        foreach($Options as $OptionIndex => $OptionData)
        {
            if(!empty($OptionData['sub_title']))
                $Rules[$OptionData['sub_title']][] = $OptionData['value'];
        }
        
        return $Rules;//$Option['value'];
    }
    
    public function cmp_select($a, $b)
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }
        return ($a['order'] < $b['order']) ? -1 : 1;
    }
    
    public function ORDER_SELECT($Options, $Lang)
    {
        $OptionsValue = array_map(create_function('$ar', 'return $ar["value"];'), $Options);
        
        //print_r($OptionsValue);
        $OptionValueNew = array();
        $Inserted = array();
        
        /*if($OptionsValue[0] == 0)//Normal
        {
            $Options[0]['sub_title'] = '';
            $OptionValueNew[0] = $Options[0];
        }
        */
        
        
        /*
        $Groups = array('Staff','Features','Punishments');
        
        $Rules = array(
            array(1,2,3),
            array(4,5,6),
            array(7,8)
        );
        */
        $Rules = $this->getGroupsRules($Options);
        
        foreach($Rules as $key => $RuleOptions)
        {
            $GetValues = array_intersect($OptionsValue, $RuleOptions);
            
            if(is_array($GetValues) && sizeof($GetValues) > 0)
            {
                $SubOptions = array();
                foreach($GetValues as $Index => $Value)
                {
                    $Inserted[] = $Value;
                    $SubOptions[] = $Options[$Index];
                }
                
                if(!array_key_exists('GROUP_' . $key, $this->SelectKeysData['GROUPS']))
                    $this->SelectKeysData['GROUPS']['GROUP_' . $key] = $Lang->getString('select_options','GROUP_' . $key, 'select_options/groups/')->getText();
                
                $OptionValueNew[] = array(
                    'order' => $SubOptions[0]['order'],
                    'sub_title' => $this->SelectKeysData['GROUPS']['GROUP_' . $key],
                    'sub_options' => $SubOptions
                );
            }
        }
        
        $GetDiff = array_diff($OptionsValue, $Inserted);
        
        
        $Final = array();
        
        foreach($GetDiff as $Index)
            $Final[] = $Options[$Index];
           
        $Final = array_merge($Final, $OptionValueNew);
        
        usort($Final, array($this,'cmp_select'));
        return $Final;    
    }
    
    public function TranslateSelectionForm($Selection, $Lang)
    {
        $root_path = 'select_options';
        
        foreach($Selection as $SelectionKey => $SelectionData)
        {
            foreach($SelectionData['SELECT'] as $key => $Option)
            {
                if(!array_key_exists($Option['text'], $this->SelectKeysData['OPTIONS']))//{
                    $this->SelectKeysData['OPTIONS'][$Option['text']] = $Lang->getString('select_options',$Option['text'], $root_path .'/options')->getText();
                    //$this->SelectKeysData['OPTIONS'][] = $Option['text'];
                //}
                $Selection[$SelectionKey]['SELECT'][$key]['sub_title'] = ((!empty($Option['group']) && $Option['group'] != '0') ? $Option['group'] : '');
                $Selection[$SelectionKey]['SELECT'][$key]['textTranslated'] = $this->SelectKeysData['OPTIONS'][$Option['text']];
            }
            $Selection[$SelectionKey]['SELECT'] = $this->ORDER_SELECT($Selection[$SelectionKey]['SELECT'], $Lang);
        }
        return $Selection;
    }
    
    public function TranslateSelectionForm2($Selection, $Lang)
    {
        $root_path = 'lang/form/select';
        
        foreach($Selection as $SelectionKey => $SelectionData)
        {
            foreach($SelectionData['SELECT'] as $key => $Option)
            {
                $OptionTextData = explode('/', $Option['text']);
                
                $Selection[$SelectionKey]['SELECT'][$key]['sub_title'] = (isset($OptionTextData[1]) ? $OptionTextData[0] : '');
                $Selection[$SelectionKey]['SELECT'][$key]['textTranslated'] = $Lang->getString(
                (isset($OptionTextData[1]) ? $OptionTextData[1] : $OptionTextData[0]), $root_path .'/'.strtolower(str_replace(' ', ',', $SelectionData['CategoryName'])).'/'.$SelectionKey);
            }
                
            $Selection[$SelectionKey]['SELECT'] = $this->OrderGrourpsSelectOptions($SelectionKey, $Selection[$SelectionKey]['SELECT'], $Lang);
        }
        return $Selection;
    } 
    
    
    
    public function getActionsFormLang($Lang, $User, $Permissions)
    {
        
        /**
         * getPanelFormListSelectionID = 0 -> every select of the specific category is avalible.
         * getPanelFormListSelectionID = Number -> just the select number... 
        */
        
        $GunZ = $GLOBALS['GunZ'];
        
        $PanelSelection = intval($User->getUserField('PanelSelection'));
        
        //print_r($Permissions);
        //print_r($User->getUserField('PanelGradeID'));
        //echo $PanelSelection;
        //echo 'lol';
        
        $PGradeID = intval($User->getUserField('PanelGradeID'));
        
        if($PanelSelection == 0)
        {
            //echo 'tet2';
            
            /*
            CategoryName,ActionName,
            
            LEFT JOIN PanelAction ON PanelAction.PAID = PanelFormSelection.PAID

            LEFT JOIN PanelCategory ON PanelCategory.PCID = PanelAction.PCID
            
            --RIGHT JOIN PanelFormSelectionSetting ON (PanelFormSelectionSetting.PFSID = PanelFormOptionCustom.PFSID)
            */
            
            
            $QueryPrepare = getSelectOptionsByGradeQuery($PGradeID);
        }
        else
        {
            //echo 'test1';
            /*
            CategoryName,ActionName,
            
            LEFT JOIN PanelAction ON PanelAction.PAID = PanelFormSelection.PAID

            LEFT JOIN PanelCategory ON PanelCategory.PCID = PanelAction.PCID
            
            
            */
            
            $QueryPrepare = "SELECT PanelFormSelection.*, PanelFormSelectionSetting.DisplayOrder,PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.PFSSID,PanelFormOption2.OptionPath, PanelFormSelectionSetting.OptionValue FROM PanelFormOptionCustom

            RIGHT JOIN PanelFormSelection ON PanelFormSelection.PFSID = PanelFormOptionCustom.PFSID
            
            RIGHT JOIN PanelFormSelectionSetting ON 
            (PanelFormOptionCustom.PFOID <> 0 AND PanelFormSelectionSetting.PFOID = PanelFormOptionCustom.PFOID AND PanelFormSelectionSetting.PFSID = PanelFormOptionCustom.PFSID) 
            OR (PanelFormOptionCustom.PFOID = '0' AND PanelFormSelectionSetting.PFSID = PanelFormOptionCustom.PFSID)
            
            RIGHT JOIN PanelFormOption2 ON PanelFormOption2.PFOID = PanelFormSelectionSetting.PFOID
            
            WHERE PanelSelection = '".$PanelSelection."' AND PanelFormSelectionSetting.Active = '1' AND (PanelFormSelectionSetting.Grades = '".$PGradeID."' OR PanelFormSelectionSetting.Grades LIKE '".$PGradeID.",%' OR PanelFormSelectionSetting.Grades LIKE '%,".$PGradeID."' OR PanelFormSelectionSetting.Grades LIKE '%,".$PGradeID.",%')
            
            ORDER BY PanelFormSelectionSetting.GroupName,PanelFormSelectionSetting.DisplayOrder;";
        }
        
        
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
                $Selections[$r['SelectionName']]['SELECT'][] = array('order' => $r['DisplayOrder'],'group' => $r['GroupName'],'text' => $r['OptionPath'],'value' => $r['OptionValue'],'option_id' => $r['PFSSID']);
                //array('text' => $r['Option_Text'],'value' => $r['Option_Value']);
            }
        }
        
        //print_r($Selections);
        //die();
        $Selections = $this->TranslateSelectionForm($Selections, $Lang);
        
        //die('<pre>'. print_r($Selections, true).'</pre>');
        
        //print_r($this->SelectKeysData);
        
        
        //return;
        //$PanelFSLID = Panel_Permissions::getPanelFormListSelectionID();
        
        
        $form_path = 'actions/form';
        
        return array(
        'GLOBAL' => array(
            'DURATION' => $Lang->getString('actions','DURATION', $form_path.'/global')->getText(),
            'DURATION_INPUT' => $Lang->getString('actions','DURATION_INPUT', $form_path.'/global')->getText()
        ),
        
        'SELECTION' => $Selections,
        'SEND_FORM' => $Lang->getString('actions','SEND', $form_path)->getText(),
        'UPDATE_FORM' => $Lang->getString('actions','UPDATE', $form_path)->getText(),
        'RESET_FORM' => $Lang->getString('actions','RESET', $form_path)->getText(),
        'DISPLAY_RESULTS' => $Lang->getString('actions','DISPLAY_RESULTS', $form_path)->getText(),
        'HIDE_RESULTS' => $Lang->getString('actions','HIDE_RESULTS', $form_path)->getText(),
        'N_DATA' => $Lang->getString('actions','N_DATA', $form_path)->getText(),
        'RESULT' => $Lang->getString('actions','RESULT', $form_path)->getText(),
        'Account' => array(
            'GLOBAL' => array(
                'USER_INPUT' => $Lang->getString('actions','USER_INPUT', $form_path.'/account/global')->getText(),
                'NEW_USER_TEXT' => $Lang->getString('actions','NEW_USER_TEXT', $form_path.'/account/global')->getText(),
                'NEW_USER_INPUT' => $Lang->getString('actions','NEW_USER_INPUT', $form_path.'/account/global')->getText()
            ),
            
            'change_userid' => array(
                'NEW_USERID_TEXT' => $Lang->getString('actions','NEW_USER_TEXT', $form_path.'/account/change_userid')->getText(),
                'NEW_USERID_INPUT' => $Lang->getString('actions','NEW_USER_INPUT', $form_path.'/account/change_userid')->getText()
            ),
            'change_password' => array(
                'NEW_PASSWORD_TEXT' => $Lang->getString('actions','NEW_USER_TEXT', $form_path.'/account/change_password')->getText(),
                'NEW_PASSWORD_INPUT' => $Lang->getString('actions','NEW_USER_INPUT', $form_path.'/account/change_password')->getText()
            ),
            'change_ugradeid' => array(
                'NEW_UGRADEID_TEXT' => $Lang->getString('actions','NEW_USER_TEXT', $form_path.'/account/change_ugradeid')->getText()
            )),
        'Character' => array(
            'GLOBAL' => array(
                'CHARACTER_INPUT' => $Lang->getString('actions','CHARACTER_INPUT', $form_path.'/character/global')->getText()
            ),
                
            'change_nickname' => array(
                'NEW_NICKNAME_TEXT' => $Lang->getString('actions','NEW_CHARACTER_TEXT', $form_path.'/character/change_nickname')->getText(),
                'NEW_NICKNAME_INPUT' => $Lang->getString('actions','NEW_CHARACTER_INPUT', $form_path.'/character/change_nickname')->getText(),
            )),
            
        'Clan' => array(
            'GLOBAL' => array(
                'CLAN_INPUT' => $Lang->getString('actions','CLAN_INPUT', $form_path.'/clan/global')->getText(),
                'NEW_CLAN_TEXT' => $Lang->getString('actions','NEW_CLAN_TEXT', $form_path.'/clan/global')->getText(),
                'NEW_CLAN_INPUT' => $Lang->getString('actions','NEW_CLAN_INPUT', $form_path.'/clan/global')->getText()
            ),
            
            'change_name' => array(
                'NEW_CLAN_NAME_TEXT' => $Lang->getString('actions','NEW_CLAN_TEXT', $form_path.'/clan/change_name')->getText(),
                'NEW_CLAN_NAME_INPUT' => $Lang->getString('actions','NEW_CLAN_INPUT', $form_path.'/clan/change_name')->getText()
            ),
            'change_score' => array(
                'NEW_SCORE_INPUT' => $Lang->getString('actions','NEW_CLAN_INPUT', $form_path.'/clan/change_score')->getText()
            ),
            'change_role' => array(
                'NEW_ROLE_TEXT' => $Lang->getString('actions','NEW_CLAN_TEXT', $form_path.'/clan/change_role')->getText()
            )),
        );
    }
    
    
    public function getGlobals()
    {
        $Lang = $GLOBALS['Lang'];
        $User = $GLOBALS['User'];
        $GunZ = $GLOBALS['GunZ'];
        
        $Global = array('text' => 'lol');
        
        if($User->isUserConnected())
        {
            //$getPermissions = $User->getPermission();
            
            //print_r($getPermissions);
            
            /**
             * 
             * צריך לתקן את דף ההרשאות ולהוסיף את הקטגוריות
             * 
             **/
            
            $getActionsData = getAllPanelActions($GunZ);
            
            $Categories = array();
            
            foreach($getActionsData as $ActionsD)
            {
                if(!isset($Categories[$ActionsD['PCID']]))
                    $Categories[$ActionsD['PCID']] = array(
                    'Name' => $ActionsD['CategoryName'],
                    'PCID' => $ActionsD['PCID'],
                    'Active' => $ActionsD['CategoryActive'],
                    'TranslatedName' => $Lang->getString('navigator', strtoupper($ActionsD['CategoryName']), 'navigator')->getText()
                );
                //echo strtoupper(str_replace(' ','_', $ActionsD['ActionName'])).'<br />';
                $Categories[$ActionsD['PCID']]['sub_menu'][] = array(
                    'Name' => $ActionsD['ActionName'],
                    'PAID' => $ActionsD['PAID'],
                    'Active' => $ActionsD['ActionActive'],
                    'TranslatedName' => $Lang->getString(
                        'navigator',
                        strtoupper(str_replace(' ','_', $ActionsD['ActionName'])),
                        'navigator/'.strtolower($ActionsD['CategoryName']))->getText()
                );
            }
            
            //echo '<pre>'.print_r($Categories, true).'</pre>';
            //die();
            
            $Global['Actions'] = $this->getActionsFormLang($Lang, $User, $User->getPermission());
            $Global['ACTIONS_SETTINGS'] = array(
                'LANG_ROLES' => array(
                    'PERMISSION' => $Lang->getString('permissions_settings', 'PERMISSION', 'permissions_settings')->getText(),
                    'SELECT_SETTINGS' => array(
                        'SELECT_FIRST' => $Lang->getString('permissions_settings', 'SELECT_PER_FIRST', 'permissions_settings')->getText(),
                        'SELECTIONS_ADD' => $Lang->getString('permissions_settings', 'SELECT_ADD', 'permissions_settings')->getText(),
                        'SAVE_ACTIONS' => $Lang->getString('permissions_settings', 'SAVE_ACTIONS', 'permissions_settings')->getText(),
                        'SAVE_SELECTIONS' => $Lang->getString('permissions_settings', 'SAVE_SELECTIONS', 'permissions_settings')->getText(),
                        'SELECTION' => $Lang->getString('permissions_settings', 'SELECTIONS', 'permissions_settings')->getText(),
                        'ACTIONS' => $Lang->getString('permissions_settings', 'ACTIONS', 'permissions_settings')->getText()
                    ),
                    'CATEGORY_NAME' => $Lang->getString('actions', 'CATEGORY_NAME', 'actions/settings')->getText(),
                    'CATEGORY_ROLES' => $Lang->getString('actions', 'CATEGORY_ROLES', 'actions/settings')->getText(),
                    'ACTION_NAME' => $Lang->getString('actions', 'ACTION_NAME', 'actions/settings')->getText(),
                    'ACTIONS_ROLES' => $Lang->getString('actions', 'ACTIONS_ROLES', 'actions/settings')->getText(),
                    'INPUT_EXISTS' => $Lang->getString('actions', 'INPUT_EXISTS', 'actions/settings')->getText(),
                    'INPUT_ACTIVE' => $Lang->getString('actions', 'INPUT_ACTIVE', 'actions/settings')->getText(),
                    'INPUT_DISPLAY' => $Lang->getString('actions', 'INPUT_DISPLAY', 'actions/settings')->getText(),
                    'INPUT_ACTIVE' => $Lang->getString('actions', 'INPUT_ACTIVE', 'actions/settings')->getText(),
                    'ALL_ACTIONS' => $Lang->getString('actions', 'ALL_ACTIONS', 'actions/settings')->getText()
                ),
                'Categories' => $Categories/*Panel_Permissions::getPermission()/*array(
                        1 => array('Name' => $Lang->getString('navigator', 'ACCOUNT', 'navigator')->getText()
                            ,'sub_menu' => $Categories[1]['sub_menu']),//,//'sub_menu' => array(('Name' => 'Change UserID','PAID' => 1)),
                        2 => array('Name' => $Lang->getString('navigator', 'CHARACTER', 'navigator')->getText()
                        ,'sub_menu' => $Categories[2]['sub_menu']),
                        3 => array('Name' => $Lang->getString('navigator', 'CLAN', 'navigator')->getText()
                        ,'sub_menu' => $Categories[3]['sub_menu']))
                    )*/
            );
            $Global['SELECTION_LIST'] = getAllSelectionList($GLOBALS['GunZ'], $Lang, $User);
            //die('<pre>'. print_r($Global['SELECTION_LIST'], true).'</pre>');
        }
        
        return $Global;
    }

    // ...
}
