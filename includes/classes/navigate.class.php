<?php

class Navigate
{
    private $NavigateName;
    private $NavigateAuto = true;
    private $_PHP_FILE_Name = array();
    private $_HTML_FILE_Name = array();
    private $_CSS_FILE_Name = array();
    private $_JS_FILE_Name = array();
    private $Path;
    private $HoldingData = array();
    
    public function __construct($NavigateName, $Path)
    {
        $this->newPage($NavigateName, $Path);
    }
    
    public function newPage($NavigateName, $Path)
    {
        if(array_key_exists($NavigateName, $GLOBALS['NavigatePages']))
        {
            $this->NavigateName = $NavigateName;
            $NavigateArray = $GLOBALS['NavigatePages'][$NavigateName];
            $this->resetDefaultFiles();
            if(array_key_exists('php', $NavigateArray))
                $this->_PHP_FILE_Name = explode('/', $NavigateArray['php']);
            if(array_key_exists('html', $NavigateArray))
                $this->_HTML_FILE_Name = explode('/', $NavigateArray['html']);
            if(array_key_exists('css', $NavigateArray))
                $this->_CSS_FILE_Name = explode('/', $NavigateArray['css']);
            if(array_key_exists('js', $NavigateArray))
                $this->_JS_FILE_Name = explode('/', $NavigateArray['js']);
            $this->setDefaultRequeriredFiles();
            
            $this->Path = $Path;
            $this->HoldingData = $GLOBALS['Navigte_Global_array'];
        }
        else
        {
            //echo 'hey';
            //exit;
            $this->newPage('403', $Path);
            //die("sad");
            //header('Location: 404.php');
            //die();
        }
    }
    
    public function resetDefaultFiles()
    {
        $this->_PHP_FILE_Name = array();
        $this->_HTML_FILE_Name = array();
        $this->_CSS_FILE_Name = array();
        $this->_JS_FILE_Name = array();
    }
    
    public function removeFile($type, $name)
    {
        unset($this->{'_'.$type.'_FILE_NAME'}[array_search($name, $this->{'_'.$type.'_FILE_NAME'})]);
    }
    
    private function setDefaultRequeriredFiles()
    {
        $this->_PHP_FILE_Name[] = $this->NavigateName;
        $this->_HTML_FILE_Name[] = $this->NavigateName;
        $this->_CSS_FILE_Name[] = $this->NavigateName;
        $this->_JS_FILE_Name[] = $this->NavigateName;
    }
    
    public function setNavigateAuto($flag)
    {
        $this->NavigateAuto = $flag;
    }
    
    public function getPages()
    {
        return array('php' => $this->getPHPFiles(),
                    'html' => $this->getHTMLFiles(),
                    'css' => $this->getCSSFiles(),
                    'js' => $this->getJSFiles()
        );
    }
    
    
    public function getFilesByType($TypeName, $RealPath = '')
    {
        $VaribleTypeName = '_'.strtoupper($TypeName).'_FILE_Name';
        $folderName = strtolower($TypeName);
        $ListFiles = array();
        
        for($i = 0,$size = sizeof($this->{$VaribleTypeName});$i < $size;$i++)
        {
            $PathFile = $this->Path.$folderName.'/'.$this->{$VaribleTypeName}[$i].'.'.$folderName;
            $FileName = $RealPath.$this->{$VaribleTypeName}[$i].'.'.$folderName;
            if(file_exists($PathFile))
               $ListFiles[] =  $FileName;
        }
        return $ListFiles;
    }
    
    public function SetData($array)
    {
        foreach($array as $key => $value)
            $this->HoldingData[$key] = $value;
    }
    
    public function GetData($key)
    {
        if(array_key_exists($key, $this->HoldingData))
            return $this->HoldingData[$key];
        return false;
    }
    
    public function getTextLang()
    {
        global $Lang;
        
        $UserConn = $this->HoldingData['UserConnected'];
        
        return array(
        'SELECT_SETTINGS' => array(
            'CHECK_ALL' => $Lang->getString('select_options','CHECK_ALL_TEXT','select_options/selection_settings')->getText(),
            'UNCHECK_ALL' => $Lang->getString('select_options','UNCHECK_ALL_TEXT','select_options/selection_settings')->getText(),
            'NONE_SELECTED' => $Lang->getString('select_options','NONE_SELECTED_TEXT','select_options/selection_settings')->getText(),
            'SELECTED' => $Lang->getString('select_options','SELECTED_TEXT','select_options/selection_settings')->getText()
        ),
        'LANG_MENU' => $Lang->getString('main','LANG_MENU','lang/langspec')->getText(),
        'AUTO_COMPLETE_TEXT' => $Lang->getString('main','AUTO_COMPLETE', 'lang/autocomplete')->getText(),
        //'LANG_MENU2' => $Lang->getString('main','LANG_MENU','lang/langspec')->getText(),
        'ACTIONS_MENU_TITLE' =>  $Lang->getString('actions','ACTIONS_MENU_TITLE','actions')->getText(),
        'ACTIONS_OPEN' => $Lang->getString('actions','ACTIONS_OPEN','actions/buttons')->getText(),
        'ACTIONS_CLOSE' => $Lang->getString('actions','ACTIONS_CLOSE','actions/buttons')->getText(),
        'NO_PERMISSIONS' => $Lang->getString('actions','NO_PERMISSIONS','actions')->getText(),
        'HELLO_MSG' => array(
            'HELLO' => $Lang->getString('main',($UserConn) ? 'HELLO' : 'PLEASE','lang/main')->getText(),
            'USERID' => (($UserConn) ? $GLOBALS['User']->getUserField('UserID') : 'Guest'),
            'ACTION' => $Lang->getString('main',($UserConn) ? 'SIGN_OUT' : 'SIGN_IN','lang/main')->getText()
            )
        );
    }
    
    public function setNavigationLang($DataToTranslate)
    {
        $Actions = array('account','character','clan');
        
        $Lang = $GLOBALS['Lang'];
        
        $Data = array('CategoriesIndex' => array());
        
        foreach($DataToTranslate AS $MenuKey => $MenuItem)
        {
            $ItemPath = str_replace(' ','_',$MenuItem['Name']);
            $Data['CategoriesIndex'][strtolower($ItemPath)] = $MenuKey;
            
            $DataToTranslate[$MenuKey]['Name'] = $Lang->getString('navigator',strtoupper($ItemPath),'navigator')->getText();
            $DataToTranslate[$MenuKey]['url'] = strtolower($ItemPath);
            
            if(array_key_exists('sub_menu', $DataToTranslate[$MenuKey]))
            {
                $ItemPath = strtolower($ItemPath);
                foreach($DataToTranslate[$MenuKey]['sub_menu'] as $SubMenuKey => $SubMenuItem)
                {
                    $SubItemPath = str_replace(' ','_',$SubMenuItem['Name']);
                    $DataToTranslate[$MenuKey]['sub_menu'][$SubMenuKey]['Name'] = $Lang->getString('navigator',strtoupper($SubItemPath),'navigator/'.$ItemPath)->getText();
                    $DataToTranslate[$MenuKey]['sub_menu'][$SubMenuKey]['url'] = (in_array($ItemPath, $Actions) ? '' : '').strtolower($SubItemPath);
                }
            }
        }
        $Data['Data'] = $DataToTranslate;
        return $Data;
    }
    
    public function NavigationLangText($menu)
    {
        if(!array_key_exists($menu, $this->HoldingData))
            return false;
        
        $Data = $this->setNavigationLang($this->HoldingData[$menu]);
        
        foreach($Data['Data'] as $key => $val)
            $this->HoldingData[$menu][$key] = $val;
        return $Data['CategoriesIndex'];
    }
    
    public function NavigationLangText2($menu)
    {
        if(!array_key_exists($menu, $this->HoldingData))
            return false;
        
        $Actions = array('account','character','clan');
        
        $CategoriesIndex = array();
        $Lang = $GLOBALS['Lang'];
        
        foreach($this->HoldingData[$menu] AS $MenuKey => $MenuItem)
        {
            $ItemPath = str_replace(' ','_',$MenuItem['Name']);
            $CategoriesIndex[strtolower($ItemPath)] = $MenuKey;
            
            $this->HoldingData[$menu][$MenuKey]['Name'] = $Lang->getString('navigator',strtoupper($ItemPath),'navigator')->getText();
            $this->HoldingData[$menu][$MenuKey]['url'] = strtolower($ItemPath);
            
            if(array_key_exists('sub_menu', $this->HoldingData[$menu][$MenuKey]))
            {
                $ItemPath = strtolower($ItemPath);
                foreach($this->HoldingData[$menu][$MenuKey]['sub_menu'] as $SubMenuKey => $SubMenuItem)
                {
                    $SubItemPath = str_replace(' ','_',$SubMenuItem['Name']);
                    $this->HoldingData[$menu][$MenuKey]['sub_menu'][$SubMenuKey]['Name'] = $Lang->getString('navigator',strtoupper($SubItemPath),'navigator/'.$ItemPath)->getText();
                    $this->HoldingData[$menu][$MenuKey]['sub_menu'][$SubMenuKey]['url'] = (in_array($ItemPath, $Actions) ? '' : '').strtolower($SubItemPath);
                }
            }
        }
        return $CategoriesIndex;
    }
    
    public function NavigateAuto($twig)
    {
        if(!$this->NavigateAuto)
            return false;
            
        $this->Navigate($twig);
        return true;
    }
    
    public function Navigate($twig)
    {
        $Page = $this->getFilesByType('html');
        if(!is_array($Page) && sizeof($Page) <= 0)
            return false;
        
        $GunZ = $GLOBALS['GunZ'];
        $User = $GLOBALS['User'];
        $Lang = $GLOBALS['Lang'];
        
        $isRTL = $Lang->isLang('he');
        
        if($isRTL)
            $this->_CSS_FILE_Name[] = 'rtl-style';
            
        
        $this->HoldingData['isRTL'] = $isRTL;
        $this->HoldingData['css_links'] = $this->getFilesByType('css');
        $this->HoldingData['js_links'] = $this->getFilesByType('js');
        /*$this->HoldingData['top_players'] = top5players($GunZ);
        $this->HoldingData['top_clans'] = top5clans($GunZ);
        $this->HoldingData['Best_of_EXP'] = BestOf($GunZ,'EXP');
        $this->HoldingData['Best_of_KDR'] = BestOf($GunZ,'KDR');
        $this->HoldingData['Best_of_KILL'] = BestOf($GunZ,'KILL');
        $this->HoldingData['Best_of_DEATH'] = BestOf($GunZ,'DEATH');
        $this->HoldingData['Best_of_PLAYTIME'] = BestOf($GunZ,'PLAYTIME');
        $this->HoldingData['ServerData'] = getServerData($GunZ, SERVER_ID);*/
        $this->HoldingData['UserConnected'] = $GLOBALS['Session']->IsConnected();
        
        $this->HoldingData['LanguagesList'] = $Lang->getLangList();/*$GLOBALS['_CONFIG']['LANG'];
        unset($this->HoldingData['LanguagesList']['default']);*/
        $this->HoldingData['LANG'] = $this->getTextLang();
        
        //ActionsData
        $this->HoldingData['Permissions'] = $User->getPermission();
        $this->HoldingData['PermissionsAccess'] = Panel_Permissions::getAccessStatus();
        $this->HoldingData['PermissionsCategoryStatus'] = Panel_Permissions::getCategoryStatus();
        //print_r($this->HoldingData['PermissionsCategoryStatus']);
        if($this->HoldingData['PermissionsAccess'])
        {
            /*$ActionsMenu = array_splice($this->HoldingData['menu'], 0, 1);
            $CatID = 1;
            foreach($this->HoldingData['Permissions'] as $CategoryName => $CategoryData)
            {
                $ActionsMenu[$CatID] = array('Name' => $CategoryName,'url' => $CategoryName);
                
                if(is_array($CategoryData['Actions']) && sizeof($CategoryData['Actions']) > 0)
                {
                    foreach($CategoryData['Actions'] as $ActionData)
                    {
                        //$ActionPath = strtolower(str_replace(' ','_',$ActionData['Name']));
                        //$ActionLang = $Lang->getString(strtoupper($ActionPath), 'lang/navigator/'.$CategoryPath);
                        //echo $CategoryPath.'/'.$ActionPath.'<br />';
                        //$ActionsMenu[$CategoryLang]['sub_menu'][$ActionLang] = $ActionPath;
                        $ActionsMenu[$CatID]['sub_menu'][] = array('Name' => $ActionData['Name'],'url' => $ActionData['Name']);
                    }
                }
                
                /*
                $CategoryPath = strtolower(str_replace(' ','_',$CategoryName));
                $CategoryLang = $Lang->getString(strtoupper($CategoryPath),'lang/navigator');
                $ActionsMenu[$CatID] = array('Name' => $CategoryLang,'url' => $CategoryPath);
                
                //echo $CategoryPath.'<br />';
                
                if(is_array($CategoryData['Actions']) && sizeof($CategoryData['Actions']) > 0)
                {
                    foreach($CategoryData['Actions'] as $ActionData)
                    {
                        $ActionPath = strtolower(str_replace(' ','_',$ActionData['Name']));
                        $ActionLang = $Lang->getString(strtoupper($ActionPath), 'lang/navigator/'.$CategoryPath);
                        //echo $CategoryPath.'/'.$ActionPath.'<br />';
                        //$ActionsMenu[$CategoryLang]['sub_menu'][$ActionLang] = $ActionPath;
                        $ActionsMenu[$CatID]['sub_menu'][] = array('Name' => $ActionLang,'url' => $ActionPath);
                    }
                }
                $CatID++;
            }*/
            array_splice($this->HoldingData['menu'], 1, 0, $this->HoldingData['Permissions']);
        }
        
        $CategoriesIndex = $this->NavigationLangText('menu');
        
        $this->HoldingData['Permissions'] = array();
        $Categories = array_keys($this->HoldingData['PermissionsCategoryStatus']);
        
        foreach($Categories as $Category)
            if(array_key_exists($Category, $CategoriesIndex))
                $this->HoldingData['Permissions'][] = $this->HoldingData['menu'][$CategoriesIndex[$Category]];
        
        //die('<pre>'.print_r($this->HoldingData, true).'</pre>');
        
        $twig->addExtension(new Global_Variables_Twig_Extension());
        
        echo $twig->render($Page[0], $this->HoldingData);
        $this->setNavigateAuto(false);
        return true;
    }
}