<?php

define('SERVER_NAME','Gunz Panel');
define('SERVER_ID',1);
define('SITE_TITLE','GunzPanel');
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/PHP%20Projects/GunzPanel/');//'http://localhost/PHP%20Projects/GunzPanel/');
define('SITE_ROOT',$_SERVER['DOCUMENT_ROOT'].'PHP Projects/GunzPanel/');

/*
//CronJobs
*/
define('CRON_LOGS_DIR', 'C:/wamp/www/PHP Projects/GunzPanel/public/cronJobs/logs/');
define('LOG_SPACE', '	');


/*
//reCAPTCHA
*/
define('reCAPTCHA_public_key', '6LejFt8SAAAAAGmfkoHoDg94IjI7IDk6-1CFiI95');
define('reCAPTCHA_private_key', '6LejFt8SAAAAABksApxRFRxcJVaxBRe3aQmUE6-z');


$_CONFIG['CheckServerStatus'] = false;


$NavigatePages = array(
'profile' => array('html' => array('profile'),'php' => array('account_profile'),'css' => array('profile')),
'edit' => array('html' => array('edit_profile'),'php' => array('edit_profile'),'css' => array('edit_profile'),'js' => array('edit_profile')),
'register' => array('html' => array('register'),'php' => array('register'),'css' => array('register')),
'login' => array('html' => array('login'),'php' => array('login'),'css' => array('login')),
'logout' => array('php' => array('logout')),
'transfer' => array('html' => array('transfer'),'php' => array('transfer'),'css' => array('jquery.multiselect','transfer'),'js' => array('jquery.multiselect.min','transfer')),
'players' => array('html' => array('players_rank'),'php' => array('players_rank'),'css' => array('rankings'),'js' => array('rankings')),
'clans' => array('html' => array('clans_rank'),'php' => array('clans_rank'),'css' => array('rankings')),
'player' => array('html' => array('player_view'),'php' => array('player_view'),'css' => array('rankings')),
'clan' => array('html' => array('clan_view'),'php' => array('clan_view'),'css' => array('rankings')),
'my_clans' => array('html' => array('manage_clans'),'php' => array('manage_clans'),'css' => array('manage_clans','forms','radio.checkboxes'),'js' => array('manage_clans')),
'shop' => array('html' => array('shop'),'php' => array('shop'),'css' => array('shop'),'js' => array('shop')),
'signature' => array('php' => array('sign')),
'home' => array('html' => array('home'),'php' =>array('home'),'css' => array('home'),'js' => array('logs'))
);


$NavigatePages = array(
'login' => array(),
'logout' => array(),
'home' => array('js' => 'logs'),
'panel_roles' => array(),
'select_view' => array(),
'403' => array()
);

/*
$NavigatePages = array(
'login' => array('html' => array('login'),'php' => array('login'),'css' => array('login')),
'logout' => array('php' => array('logout')),
'home' => array('html' => array('home'),'php' =>array('home'),'css' => array('home'),'js' => array('logs')),
'panel_roles' => array('html' => array('panel_roles'),'php' => array('panel_roles'),'css' => array('panel_roles'),'js' => array('panel_roles')),
'403' => array('html' => array('403'))
);*/


$MenuPages = array(
'Home' => array('url' => 'home',
                'sub_menu' => array('Logs' => 'logs')),
/*'Account' => array('url' => 'account',
                'sub_menu' => array('Change UserID' => 'accounts#',
                                    'Change Password' => 'accounts#')),
'Character' => array('url' => 'home'),
'Clan' => array('url' => 'home'),*/
'Panel Roles' => array('url' => 'home'),
'Select Views' => array('url' => 'select_views'),
'Shop Logs' => array('url' => 'home'),
'Events' => array('url' => 'home'),
'Rules' => array('url' => 'home'),
'Chat' => array('url' => 'home'),
'Test 1' => array('url' => 'home'),
'Test 2' => array('url' => 'home')
);


$MenuPages = array(
array('Name' => 'HOME','url' => 'home','sub_menu' => array(
        array('Name' => 'Logs','url' => 'logs')
    )),
array('Name' => 'PANEL_ROLES','url' => 'logs'),
array('Name' => 'SELECT_VIEW','url' => 'select_view'),
array('Name' => 'SHOP_LOGS','url' => 'logs'),
array('Name' => 'EVENTS','url' => 'logs'),
array('Name' => 'RULES','url' => 'logs'),
array('Name' => 'CHAT','url' => 'logs'),
array('Name' => 'TEST1','url' => 'logs'),
array('Name' => 'TEST2','url' => 'logs')
);


$_CONFIG['LANG'] = array(
        "default" => "en",
        "en" => array('file' => 'en.xml'),//"en.xml",
        "he" => array('file' => 'he.xml')//"he.xml"
    );

//$MenuPages = array_reverse($MenuPages, true);


/*
//Account Status
*/
$User_Status = array(0 => array('GradeName' => 'Normal','GradeColor' => 'black'),
                    2 => array('GradeName' => 'JJang','GradeColor' => '#83FFF5'),
                    104 => array('GradeName' => 'Chat banned','GradeColor' => '#B4B4B4'),
                    105 => array('GradeName' => 'Mac Banned','GradeColor' => '#646464'),
                    168 => array('GradeName' => 'VIP','GradeColor' => '#0077AF'),
                    252 => array('GradeName' => 'Police','GradeColor' => '#097DFA'),
                    253 => array('GradeName' => 'Banned','GradeColor' => '#646464'),
                    254 => array('GradeName' => 'Game Master','GradeColor' => '#3DFF00'),
                    255 => array('GradeName' => 'Administrator','GradeColor' => '#EC9718')
 );

/*
//Navigate Global values
*/
$Navigte_Global_array = array(
'site_url' => SITE_URL,
'title' => SITE_TITLE,
'server_name' => SERVER_NAME,
'menu' => $MenuPages,
'confirmbox' => true
);


$GlobalPaths = array(
'BASE_CLAN_EMBLEM_DIR' => SITE_URL.'public/clans_emblems/',
'DEFAULT_CLAN_NO_EMBLEM' => 'noimg.png',
'BASE_PLAYER_SIGN_PATH' => 'signature/',
'BASE_CLAN_SIGN_PATH' => 'signature/',
'BASE_PLAYER_PROFILE_PATH' => 'player/',
'BASE_CLAN_PROFILE_PATH' => 'clan/',
'BASE_PLAYERS_RANKING_PATH' => 'players/',
'BASE_CLANS_RANKING_PATH' => 'clans/'
);

$Navigte_Global_array += $GlobalPaths;


/*
// RandomToken For Session
*/
$User_Token = '6743sGd6dsSDfse2*(&%$#@23321';



/**
 * Form Selection List
 */
 
$Form_Selection = array(
    'Account' => array(
        'change_userid' => array(
            'USER_INFO' => array(0 > '')
        )
    )
);

/*
//Form Class Errors
*/
$Form_Errors['Fields']['NotFound'] = 'The System Didn\'t Find The `{{ keys }}` Fields, Try Again Later.';
$Form_Errors['Fields']['IsNotEmpty']['Empty'] = 'The `{{ key }}` is empty.';
$Form_Errors['Fields']['IsEquals']['NotSame'] = 'The `{{ key2 }}` is not the same as `{{ key1 }}`.';
$Form_Errors['Fields']['IsNumber']['NotNumber'] = 'The `{{ key }}` is not a number.';
$Form_Errors['Fields']['IsNumberBetween']['NotBetween'] = 'The `{{ key }}` is not between  {{ min }}-{{ max }} characters.';
$Form_Errors['Fields']['IsNumberBetween']['Min'] = 'The `{{ key }}` has to be at least  {{ min }} characters.';
$Form_Errors['Fields']['IsNumberBetween']['Max'] = 'The `{{ key }}` cannot pass over  {{ max }} characters.';
$Form_Errors['Fields']['IsLengthBetween']['NotBetween'] = 'The `{{ key }}` is not between  {{ min }}-{{ max }} characters.';
$Form_Errors['Fields']['IsLengthBetween']['Min'] = 'The `{{ key }}` has to be at least  {{ min }} characters.';
$Form_Errors['Fields']['IsLengthBetween']['Max'] = 'The `{{ key }}` cannot pass over  {{ max }} characters.';
$Form_Errors['Fields']['IsAllowed']['NotAllowed'] = 'The `{{ key }}` can contains only  `{{ contains }}` characters.';
$Form_Errors['Fields']['IsExists']['Exists'] = 'The {{ key }} `{{ keyvalue }}` is already  exist.';

$Form_Errors['System']['Register']['Succeed'] = 'Congratulations {{ name }}! Your account has successfully registered to our database..';
$Form_Errors['System']['Register']['Failed'] = 'Try again later.';
$Form_Errors['System']['Login']['Succeed'] = 'You Have Been Logged In.';
$Form_Errors['System']['Login']['Failed'] = 'Username or password are wrong.';
$Form_Errors['System']['Transfer']['Succeed'] = 'Your account has been transferred';
$Form_Errors['System']['Transfer']['Failed'] = 'Try again later.';
//IsExists

/*
//Register Class Global Vars
*/
$Register_Global_Vars = array(
'Username' => array('regex' => array('statement' => '[^0-9a-zA-Z\_]','contains' => '0-9,A-z and _'),'DatabaseField' => 'UserID'),
'AID' => array('regex' => array('statement' => '[^0-9]','contains' => '0-9_'),'DatabaseField' => 'AID'),
'Name' => array('regex' => array('statement' => '[^0-9a-zA-Z\ ]','contains' => '0-9,A-z and space'),'DatabaseField' => 'Name'),
'Password' => array('regex' => array('statement' => '[^0-9a-zA-Z\_]','contains' => '0-9,A-z and _'),'DatabaseField' => 'Password'),
'Email' => array('regex' => array('statement' => '[^0-9a-zA-Z\_\.\@]','contains' => '0-9,A-z,_,.,@ and _'),'DatabaseField' => 'Email'),
'Gender' => array('regex' => array('statement' => '[^0-1]','contains' => '0-1'),'DatabaseField' => 'Sex'),
'Age' => array('regex' => array('statement' => '[^(\d\d?)]','contains' => '1-30'),'DatabaseField' => 'Age'),
'Secret_Question' => array('regex' => array('statement' => '[^0-9a-zA-Z\ ]','contains' => '0-9,A-z and space'),'DatabaseField' => 'SQ'),
'Secret_Answer' => array('regex' => array('statement' => '[^0-9a-zA-Z\ ]','contains' => '0-9,A-z and space'),'DatabaseField' => 'SA'),
'Number' => array('regex' => array('statement' => '[^0-9]','contains' => '0-9'))
);

$Register_SaveValues = array(
'Username' => '',
'Name' => '',
'Password' => '',
'rePassword' => '',
'Email' => '',
'Gender' => '',
'Age' => '',
'Secret_Question' => '',
'Secret_Answer' => ''
);

$DataBase_Fields['Coins'] = 'prep';

/*
//User class global allowed fields
*/
$User_Allowed_Fields = array('UserID','Account.AID','UGradeID','Name','Email','Age','Account.RegDate',$DataBase_Fields['Coins'],'SQ','SA');




/*
//Shop class nav bar
*/
$Shop_nav_bar['Menu']['Name'] = 'Menu';
$Shop_nav_bar['ChangeName']['Name'] = 'Change Player Nickname';
$Shop_nav_bar['ChangeName']['regex']['statement'] = '[^0-9a-zA-Z\_\[\]]';
$Shop_nav_bar['ChangeName']['regex']['contains'] = '0-9,A-z,[,] and _';
$Shop_nav_bar['ChangeName']['length']['min'] = 1;
$Shop_nav_bar['ChangeName']['length']['max'] = 16;
$Shop_nav_bar['ChangeName']['Cost'] = 20;
$Shop_nav_bar['ColorName']['Name'] = 'Player Color Name';
$Shop_nav_bar['ColorName']['colors'] = array('0' => '808080', '1' => 'FF0000', '2' => '00FF00', '3' => '0000FF', '4' => 'FFFF00', '5' => '800000','6' => '008000', '7' => '000080', '8' => '808000', '9' => 'FFFFFF');
$Shop_nav_bar['ColorName']['Time'][0]['Days'] = 7;
$Shop_nav_bar['ColorName']['Time'][0]['Price'] = 15;
$Shop_nav_bar['ColorName']['Time'][1]['Days'] = 14;
$Shop_nav_bar['ColorName']['Time'][1]['Price'] = 25;
$Shop_nav_bar['ColorName']['regex']['statement'] = '[^0-9a-zA-Z\_\[\]\^]';
$Shop_nav_bar['ColorName']['regex']['contains'] = '0-9,A-z,[,],^ and _';
$Shop_nav_bar['ColorName']['length']['min'] = 1;
$Shop_nav_bar['ColorName']['length']['max'] = 16;
$Shop_nav_bar['ColorName']['Discount'] = 50;
$Shop_nav_bar['ColorName']['Cost'] = array(12 => array('TimeString' => '3 Days','Time' => 60*60*24*3),
                                        24 => array('TimeString' => 'Week','Time' => 60*60*24*7),
                                        46 => array('TimeString' => '2 Weeks','Time' => 60*60*24*14),
                                        90 => array('TimeString' => '4 Weeks','Time' => 60*60*24*28));
$Shop_nav_bar['ChangeClanName']['Name'] = 'Change Clan Name';
$Shop_nav_bar['ChangeClanName']['regex']['statement'] = '[^0-9a-zA-Z\_\[\]]';
$Shop_nav_bar['ChangeClanName']['regex']['contains'] = '0-9,A-z,[,] and _';
$Shop_nav_bar['ChangeClanName']['length']['min'] = 4;
$Shop_nav_bar['ChangeClanName']['length']['max'] = 12;
$Shop_nav_bar['ChangeClanName']['Cost'] = 25;
$Shop_nav_bar['Items']['Name'] = 'Buy Items';
$Shop_nav_bar['GiftCoins']['Name'] = 'Gift Coins';
$Shop_nav_bar['VIP']['Name'] = 'Purchase VIP Grade';
/*$Shop_nav_bar = array(
'ChangeName' => array('class' => 'ShopNavItem',
                    'regex' => array('statement' => '[^0-9a-zA-Z\_\[\]]','contains' => '0-9,A-z,[,] and _'),
                    'length' => array('min' => 1,'max' => 16),
                    'Cost' => 20),
'ColorName' => array('class' => 'ShopNavItem',
                    'Colors' => array('0' => '808080', 
                                                    '1' => 'FF0000', 
                                                    '2' => '00FF00', 
                                                    '3' => '0000FF', 
                                                    '4' => 'FFFF00', 
                                                    '5' => '800000',
                                                    '6' => '008000', 
                                                    '7' => '000080', 
                                                    '8' => '808000', 
                                                    '9' => 'FFFFFF'),
                    'Cost' => array(12 => array('TimeString' => '3 Days','Time' => 60*60*24*3), 24 => array('TimeString' => 'Week','Time' => 60*60*24*7), 46 => array('TimeString' => '2 Weeks','Time' => 60*60*24*14), 90 => array('TimeString' => '4 Weeks','Time' => 60*60*24*28))),
'ShopItems' => array('class' => 'ShopNavItem')
);*/

$Clan_Grades = array(1 => 'Leader',2 => 'Administrator',9 => 'Member',10 => 'Kick');


$isAllowed = false;