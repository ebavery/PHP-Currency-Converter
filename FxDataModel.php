<?php

/**
 * FXDataModel is a model class that works with the database to obtain currencies and rates
 *
 * @author Elizabeth Avery
 */
define('INI_FILE', 'fxCalc.ini');

class FxDataModel {
    
    const RATES_KEY = 'fx.rates.file';
    const DEST_AMT_KEY = 'dst.amt';
    const DEST_CUR_KEY = 'dst.cucy';
    const SRC_AMT_KEY = 'src.amt';
    const SRC_CUR_KEY = 'src.cucy';
    const FX_DATA_MODEL_KEY = 'fx.data.model';
    const DB_USER_KEY = 'dbUsername';
    const DB_PWD_KEY = 'dbPassword';
    const DB_DSN_KEY = 'dsn';
    const QUERY_KEY = 'query';
    const RATE_KEY = 'rate';
    const EMAIL_KEY = 'email.address';
        
    private $fxCurrencies;
    private $fxRates;
    private $iniArray;
    private $result;
    
    function __construct(){
       $this->iniArray = parse_ini_file(INI_FILE, true);
       try{
           if (!isset($this->iniArray[FxDataModel::DB_PWD_KEY])){           
                $db = new PDO($this->iniArray[FxDataModel::DB_DSN_KEY], $this->iniArray[FxDataModel::DB_USER_KEY], NULL);                  
           }
            else {           
                $db = new PDO($this->iniArray[FxDataModel::DB_DSN_KEY], $this->iniArray[FxDataModel::DB_USER_KEY], $this->iniArray[FxDataModel::DB_PWD_KEY]);            
           }  
       }catch (Exception $ex) {
            require_once('ErrorModel.php');    
            ErrorModel::getErrorURL($ex->getMessage());           
            exit();
        }
        $statement = $db->prepare($this->iniArray[FxDataModel::QUERY_KEY]);
        $statement->execute();
        $this->result = $statement->fetchAll();        
        $statement->closeCursor();
                
        $this->fxCurrencies = array_unique(array_column($this->result, $this->iniArray[FxDataModel::SRC_CUR_KEY]));
        foreach ($this->result as $row){
            foreach ($row as $key => $value){
                switch ($key){
                    case $this->iniArray[FxDataModel::SRC_CUR_KEY]:
                          $src = $value;  
                    case $this->iniArray[FxDataModel::DEST_CUR_KEY]:
                        $dst = $value;
                    case $this->iniArray[FxDataModel::RATE_KEY]:
                        $rate = $value;
                }                
                $this->fxRates[$src.$dst] = $rate; 
                if (!in_array($dst, $this->fxCurrencies)){
                    $this->fxCurrencies[] = $dst;
                }                 
            }             
        }        
        $db = NULL; 
    }    
     
    public function getFxCurrencies() {
        return $this->fxCurrencies;
    } 
    
    public function getFxRate($source_currency, $destination_currency){
        
        if (isset($this->fxRates[$source_currency.$destination_currency])){
            return $this->fxRates[$source_currency.$destination_currency]; 
        }       
        else {
            return 1.0 / $this->fxRates[$destination_currency.$source_currency];
        }          
    }
    
    public function getIniArray(){
        return $this->iniArray;
    }    
   
}
