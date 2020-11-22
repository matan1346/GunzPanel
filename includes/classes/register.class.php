<?php

class Register extends Form
{
    private $GunZ;
    private $IsGenderString = true;
    
    public function __construct($GunZ, $data = array())
    {
        parent::__construct($GunZ);
        $this->GunZ = $GunZ;
        foreach($data as $k => $v)
        {
            if(array_key_exists($k, $this->_Fields))
                $this->FieldsData[$k] = $v;
        }
            
    }
    
    public function Register()
    {
        $Fields = $this->getFieldsValues();
        //Check if Gender is string
        if($this->IsGenderString)
        {
            if($Fields['Gender'] == 1)
                $Fields['Gender'] = 'Female';
            else
                $Fields['Gender'] = 'Male';
        }
        
        
        //Account Table
        $AccountFields = 'UserID,UGradeID,PGradeID,RegDate,Name,Email,Age,Sex,ServerID,SQ,SA';
        $AccountValues = ":userid,'0','0',GETDATE(),:name,:email,:age,:gender,'0',:secret_question,:secret_answer";
        $query = 'INSERT INTO Account ('.$AccountFields.') VALUES ('.$AccountValues.')';
        //echo $query.'<br />';
        $AccountQuery = $this->GunZ->prepare($query);
        $AccountQuery->bindParam(':userid', $Fields['Username'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':name', $Fields['Name'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':email', $Fields['Email'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':age', $Fields['Age'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':gender', $Fields['Gender'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':secret_question', $Fields['Secret_Question'], PDO::PARAM_STR);
        $AccountQuery->bindParam(':secret_answer', $Fields['Secret_Answer'], PDO::PARAM_STR);
        $AccountQuery->execute();
        
        
        
        $queryAID = $this->GunZ->prepare('SELECT TOP 1 AID FROM Account WHERE UserID = :userid');
        $queryAID->bindParam(':userid', $Fields['Username'], PDO::PARAM_STR);
        $queryAID->execute();
        
        $a = $queryAID->fetch(PDO::FETCH_ASSOC);
        
        $last_insert_id = $a['AID'];
        
        
        //Login Table
        $LoginFields = 'UserID,AID,Password';
        $LoginValues = ":userid,:aid,:password";
        $query = 'INSERT INTO Login ('.$LoginFields.') VALUES ('.$LoginValues.')';
        //echo $query;
        $LoginQuery = $this->GunZ->prepare($query);
        $LoginQuery->bindParam(':userid', $Fields['Username'], PDO::PARAM_STR);
        $LoginQuery->bindParam(':aid', $last_insert_id, PDO::PARAM_INT);
        $LoginQuery->bindParam(':password', $Fields['Password'], PDO::PARAM_STR);
        $LoginQuery->execute();
        $this->setError('System/Register/Succeed','System', array('name' => $Fields['Name']));
        //$this->setNewError('System', 'Congratulations '.$Fields['Name'].'! Your Account is registered to our system.');
        //$this->SystemMessage['System'][] = 'Congratulations '.$Fields['Name'].'! Your Account is registered to our system.';
        return true;
    }
}