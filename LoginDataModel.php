<?php
/**
 * LoginDataModel is the model class that works with the login data in the database
 * 
 * @author Elizabeth Avery
 */
define('LOGIN', 'login.ini');

class LoginDataModel {
    
    const USER_KEY = 'username';
    const PWD_KEY = 'password';
    const DB_USER_KEY = 'dbUsername';
    const DB_PWD_KEY = 'dbPassword';
    const DB_DSN_KEY = 'dsn';
    const LOGIN_QUERY = 'login.query';
    const BIND = 'bind';
    
    private $login_array; 
    private $db;
    
    function __construct(){
       $this->login_array = parse_ini_file(LOGIN, true);      
    }
    
    public function validateUser($username, $password){

        try{
            if (!isset($this->login_array[LoginDataModel::DB_PWD_KEY])){           
                $this->db = new PDO($this->login_array[LoginDataModel::DB_DSN_KEY], $this->login_array[LoginDataModel::DB_USER_KEY], NULL);                  
            }
            else{
               $this->db = new PDO($this->login_array[LoginDataModel::DB_DSN_KEY], $this->login_array[LoginDataModel::DB_USER_KEY], $this->login_array[LoginDataModel::DB_PWD_KEY]);
            }            
        } catch (Exception $ex) {
            require_once('ErrorModel.php');    
            ErrorModel::getErrorURL($ex->getMessage()); 
            exit();
        }
        
        $statement = $this->db->prepare($this->login_array[LoginDataModel::LOGIN_QUERY]);
        $statement->bindValue($this->login_array[LoginDataModel::BIND], $username);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();   
        $this->__destruct();
        
        if ($result[$this->login_array[LoginDataModel::PWD_KEY]] == $password){
            return true;
        }
        else {
            return false;
        }
        
    }
      
    public function getLoginArray(){
        return $this->login_array;
    }
    
    public function __destruct(){
        $this->db = null;
    }
}
