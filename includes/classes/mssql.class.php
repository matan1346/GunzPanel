<?php

class sqlGunz extends PDO
{
    private $Connection;
    private $serverName;
    private $dataUser;
    private $dataPass;
    private $dataBase;
    private $error;
    private $queries = array();
    
    public function __construct($ServerName, $Username, $Password, $Database)
    {
        try{
            $this->serverName = $ServerName;
            $this->dataUser = $Username;
            $this->dataPass = $Password;
            $this->dataBase = $Database;
            $Connection = parent::__construct('mssql:host='.$ServerName.';dbname='.$Database, $Username, $Password);
        }catch(PDOException $e){
            print 'Failed to get DB handle: ' . $e->getMessage();
        }
    }
    
    
    public function Query2()
    {
        $args = func_get_args();
        $name = $args[0];
        unset($args[0]);
        $reflector = new ReflectionClass(get_class($this));
        $parent = $reflector->getParentClass();
        $method = $parent->getMethod('prepare');
        $queries[$name] = $method->invokeArgs($this, $args);
        
        /*
        $paramaters = func_get_args();
        $queries[$name] = call_user_func_array('$this->prepare', array($paramaters));*/
        //$queries[$name] = parent::prepare($query);
        return $queries[$name];
    }
    
    public function close()
    {
        $this->Connection = NULL;
    }
    
    public function showQuery($query, $params)
    {
        $keys = array();
        $values = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key=>$value)
        {
            if (is_string($key))
            {
                $keys[] = '/:'.$key.'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
            
            if(is_numeric($value))
            {
                $values[] = intval($value);
            }
            else
            {
                $values[] = '"'.$value .'"';
            }
        }
        
        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }
    
    public function getPARAM($value)
    {
        if(is_int($value))
            return PDO::PARAM_INT;
        if(is_bool($value))
            return PDO::PARAM_BOOL;
        if(is_null($value))
            return PDO::PARAM_NULL;
        if(is_string($value))
            return PDO::PARAM_STR;
        return FALSE;
    }
    
    public function WhereStatement($var)
    {
        if(!is_array($var))
            return $var;
        $size = sizeof($var);
        $count = 1;
        
        $StartLetter = '';
        $EndLetter = '';
        if($size > 0)
        {
           $StartLetter = '(';
           $EndLetter = ')'; 
        }
        $Str = '';
        $isnotArray = 0;
        foreach($var as $key => $val)
        {
            $signQ = ' AND ';
            if(!is_array($val))
            {
                $signQ = ' OR ';
                if($isnotArray == 0)
                    $Str .= $StartLetter;
                $isnotArray++;
            }  
            else
            {
                if($isnotArray > 0)
                    $Str .= $EndLetter;
                $isnotArray = 0;
            }
                
                
            //if($isnotArray )
                
            
            
            //$Str .= '(';
            if(!is_numeric($key))
            {
                $Str .= $key.' = '.$this->WhereStatement($val);
                
            }
            else
            {
                $Str .= $this->WhereStatement($val);
            }
            //$Str .= ')';   
                
            if($count < $size)
                $Str .= $signQ;
            $count++;
        }
        if($isnotArray > 0)
            $Str .= $EndLetter;
        
        return  $Str;
    }
    
    public function InsertData($table,  $insertData, $bind_params = array())
    {
        $Fields = '';
        $Values = '';
        $size = sizeof($insertData);
        if($size > 0)
        {
            foreach($insertData as $FieldName => $FieldValue)
            {
                $Fields .= $FieldName.',';
                $Values .= $FieldValue.',';
                    
            }
            $Fields = mb_substr($Fields, 0, mb_strlen($Fields)-1);
            $Values = mb_substr($Values, 0, mb_strlen($Values)-1);
        }
        
        $query_str = "INSERT INTO ".$table." (".$Fields.") VALUES (".$Values.")";
        //echo $query_str.'<br />';
        //print_r($bind_params);
        //echo '<br /><br />';
        ///
        
        //die($query_str);
        $query = $this->prepare($query_str);
        foreach($bind_params as $paramField => $paramValue)
        {
            $paramType = $this->getPARAM($paramValue);
            $query->bindValue($paramField, $paramValue, $paramType);
        }
        if($query->execute())
            return true;
        return false;
        
    }
    
    public function DeleteData($table, $statements = array(), $bind_params = array())
    {
        $statement = '';
        if(sizeof($statements) > 0)
            $statement = 'WHERE '.$this->WhereStatement($statements);
        
        $query_str = 'DELETE FROM '.$table.' '.$statement;
        //die($query_str);
        $query = $this->prepare($query_str);
        foreach($bind_params as $paramField => $paramValue)
        {
            $paramType = $this->getPARAM($paramValue);
            $query->bindValue($paramField, $paramValue, $paramType);
        }
        
        if($table[0] == '#')//return query string
            return $query_str;
        
        if($query->execute())
            return true;
        return false;
        
    }
    
    public function IfRowExists($table, $statements = array(), $bind_params = array())
    {
        $statement = '';
        if(sizeof($statements) > 0)
            $statement = 'WHERE '.$this->WhereStatement($statements);
        
        $query_str = 'SELECT 1 FROM '.$table.' '.$statement;
        //echo $query_str;
        $query = $this->prepare($query_str);
        
        foreach($bind_params as $paramField => $paramValue)
        {
            //echo '<br />Field: '.$paramField.', Value: '.$paramValue;
            $paramType = $this->getPARAM($paramValue);
            $query->bindValue($paramField, $paramValue, $paramType);
        }
        
        if($table[0] == '#')//return query string
            return $query_str;
        
        //die();
        $query->execute();
        
        
        if($a = $query->fetch())
        {
            return true;
        }
        //print_r($this->errorInfo());
        return false;
    }
    
    public function SelectData($table, $selectData = array(),$statements = array(), $bind_params = array(), $order = array())
    {
        $size = sizeof($selectData);
        if($size > 0)
        {
            $selects = '';
            for($i = 0;$i < $size;$i++)
                $selects .= $selectData[$i].',';
            $selects = mb_substr($selects, 0, mb_strlen($selects)-1);
        }
        else
            $selects = '*';
            
            
        
        $statement = '';
        if(sizeof($statements) > 0)
            $statement = 'WHERE ('.$this->WhereStatement($statements).')';
        
        
        $order_by = '';
        if(sizeof($order) > 0)
        {
            $order_by = 'ORDER BY ';
            foreach($order as $key => $value)
            {
                $order_by .= $key.' '.$value.',';
            }
            $order_by = mb_substr($order_by, 0, mb_strlen($order_by)-1);
        }
        
        
        $query_str = 'SELECT '.$selects.' FROM '.$table.' '.$statement.' '.$order_by;
        
        if($table[0] == '#')//return query string
            return $query_str;
        
        //echo $query_str.'<br />';
        /*echo $query_str.'<br />';
        print_r($bind_params);
        echo '<br /><br />';*/
        //die($query_str);
        $query = $this->prepare($query_str);
        
        foreach($bind_params as $paramField => $paramValue)
        {
            $paramType = $this->getPARAM($paramValue);
            $query->bindValue($paramField, $paramValue, $paramType);
        }
        $query->execute();
        
        
        if($a = $query->fetchAll(PDO::FETCH_ASSOC))
        {
            return $a;
        }
        return array();
    }
    
    public function UpdateData($table, $updateData, $statements = array(), $bind_params = array())
    {
        $Fields = '';
        $size = sizeof($updateData);
        if($size > 0)
        {
            foreach($updateData as $FieldName => $FieldValue)
            {
                $Fields .= $FieldName.' = '.$FieldValue.',';
                    
            }
            $Fields = mb_substr($Fields, 0, mb_strlen($Fields)-1);
        }
        
        $statement = '';
        if(sizeof($statements) > 0)
            $statement = 'WHERE '.$this->WhereStatement($statements);
        
        $query_str = 'UPDATE '.$table.' SET '.$Fields.' '.$statement;
        
        if($table[0] == '#')//return query string
            return $query_str;
        
        //echo $query_str.'<br />';
        //die();
        $query = $this->prepare($query_str);
        foreach($bind_params as $paramField => $paramValue)
        {
            $paramType = $this->getPARAM($paramValue);
            $query->bindValue($paramField, $paramValue, $paramType);
        }
        
        $query->execute();
        
        
        if($query->affected_rows > 0)
            return true;
        return false;
    }

}