<?php

class Rankings
{
    private $rank_type = array('player','clan');
    private $type;
    private $row_start;
    private $max_rows;
    private $max_pages;
    private $per_page;
    private $bind_params = array();
    private $where_statement = '';
    private $_Rank_Data = array();
    private $GunZ;
    
    public function __construct($name, $dbConnection, $start, $per_page, $max_rows = false, $max_pages = false)
    {
        if($max_rows !== false)
        {
            //echo $name.'<br />'.$start.'<br />'.$per_page.'<br />'.$max_rows.'<br />'.$max_pages;
            //die();
        }
        //echo $max_rows;
        $name = strtolower($name);
        if(!in_array($name, $this->rank_type))
            die('Ranking type is not allowed');
        $this->type = $name;
        $this->row_start = $start;
        $this->per_page = $per_page;
        $this->max_rows = $max_rows;
        $this->max_pages = $max_pages;
        
        $this->GunZ = $dbConnection;
        
    }
    
    public function setStatements($statement)
    {
        $this->where_statement = $statement;
    }
    
    public function setBindParamss($bind)
    {
        $this->bind_params = $bind;
    }
    
    
    
    public function execute()
    {
        $rankFunction = 'ranking_'.$this->type;
        $query = $this->{$rankFunction}();
        foreach($this->bind_params as $bindName => $bindValue)
        {
            $bindType = $this->GunZ->getPARAM($bindValue);
            $query->bindValue($bindName,$bindValue ,$bindType);
        }
            
        $query->execute();
        $this->_Rank_Data = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $this;
    }
    
    public function ranking_player()
    {
        //echo $this->max_rows;
        if($this->max_rows === false)
        {
            $name = 'Top '.$this->per_page.' Players';
            //$q = 'SELECT TOP '.$this->per_page.' Name,Level FROM Character ORDER BY Level DESC,XP DESC,KillCount DESC,DeathCount ASC';
            $q = 'SELECT TOP '.$this->per_page.' Name,Level FROM Character WHERE Character.DeleteFlag = 0 AND Character.Ranking <> 0 ORDER BY Ranking ASC';
        }
        else
        {//SELECT TOP '.$this->per_page.' * FROM ( SELECT TOP ".($maxpages1-$start_from)." * FROM QPanelLog WHERE ".$where_type." ".$search_form." ".$mac_hid." ORDER BY ID asc ) AS derivedtable ORDER BY ID desc
            $name = 'Top '.$this->per_page.' from row '.$this->row_start.' Players';
            /*$q = 'SELECT TOP '.$this->per_page.' Chars.Name,Chars.Level,Chars.XP,Chars.KillCount,Chars.DeathCount,ISNULL(Clan.Name, 0) AS "ClanName" FROM 
            ( SELECT TOP '.($this->max_rows-$this->row_start).' Name,Level,XP,KillCount,DeathCount,CID,MIN(XP) AS MinXP FROM
             dbo.Character ORDER BY XP DESC,Level DESC,KillCount DESC,DeathCount ASC ) AS Chars
             LEFT JOIN dbo.ClanMember ON ClanMember.CID = Chars.CID LEFT JOIN dbo.Clan ON Clan.CLID = ClanMember.CLID WHERE Chars.XP > Chars.MinXP ORDER BY Chars.XP DESC,Chars.Level DESC,Chars.KillCount DESC,Chars.DeathCount ASC';*/
             $q = 'SELECT TOP '.$this->per_page.' Chars.CID,Chars.Name,Chars.Level,Chars.XP,Chars.KillCount,Chars.DeathCount,Chars.Ranking,ISNULL(Clan.Name, 0) AS "ClanName" FROM 
            dbo.Character AS Chars
             LEFT JOIN dbo.ClanMember ON ClanMember.CID = Chars.CID LEFT JOIN dbo.Clan ON Clan.CLID = ClanMember.CLID WHERE Chars.Ranking > '.$this->row_start.' AND Chars.DeleteFlag = 0 AND Chars.Ranking <> 0 '.$this->where_statement.' ORDER BY Chars.Ranking ASC';
            //die($q);
        }
        //echo $q.'<br /><br />';
        return $this->GunZ->prepare($q);
    }
    
    public function ranking_clan()
    {
        if($this->max_rows === false)
        {//
            $name = 'Top '.$this->per_page.' Clans';
            $q = 'SELECT TOP '.$this->per_page.' Name,Point FROM Clan WHERE Clan.Ranking <> 0 AND Clan.DeleteFlag = 0 ORDER BY Point DESC,Wins DESC,Losses ASC';
        }
        else
        {
            //echo 'asdas';
            //SELECT TOP $per_page * FROM ( SELECT TOP ".($maxpages1-$start_from)." * FROM QPanelLog WHERE ".$where_type." ".$search_form." ".$mac_hid." ORDER BY ID asc ) AS derivedtable WHERE ".$where_type." ".$search_form." ".$mac_hid." order by ID desc
            $name = 'Top '.$this->per_page.' from row '.$this->row_start.' Clans';
            //$q = 'SELECT TOP '.$this->per_page.' * FROM ( SELECT TOP ".($maxpages1-$start_from)." * FROM QPanelLog WHERE ".$where_type." ".$search_form." ".$mac_hid." ORDER BY ID asc ) AS derivedtable ORDER BY ID desc';
            $q = 'SELECT TOP '.$this->per_page.' Clan.CLID,Clan.Name,Clan.Ranking,Clan.Wins,Clan.Losses,Clan.Point,Chars.Name AS MasterName,Clan.EmblemUrl FROM 
            dbo.Clan
             INNER JOIN dbo.Character AS Chars ON Chars.CID = Clan.MasterCID WHERE Clan.Ranking > '.$this->row_start.' AND Clan.Ranking <> 0 AND Clan.DeleteFlag = 0 ORDER BY Clan.Ranking ASC';
        }
        return $this->GunZ->prepare($q);
    }
    
    
    public function getData()
    {
        return $this->_Rank_Data;
    }
    
}
