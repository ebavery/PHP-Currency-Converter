<?php


/**
 * EmailModel is a class that verifies an entered email address is valid and creates a mailer
 *
 * @author Elizabeth Avery
 */

define('EMAIL_INI_FILE', 'email.ini');

class EmailModel {
    
    const FROM_KEY = 'from.address';
    const HOST_KEY = 'host';
    const PORT_KEY = 'port';
    const MAIL_USER_KEY = 'username';
    const MAIL_PWD_KEY = 'password';
    const AUTH = 'auth';
    
    private $emailArray;
    
    function __construct(){
       $this->emailArray = parse_ini_file(EMAIL_INI_FILE, true);       
    }
    //Function below copied from textbook, Murach's PHP and MySQL, per assignment instructions 
    public static function validateEmailAddr($address){ 
        
        $parts = explode("@", $address);
        if (count($parts) != 2) {
            return false;
        }
        if (strlen($parts[0]) > 64) {
            return false;
        }
        if (strlen($parts[1] > 255)) {
            return false;
        }
        
        $atom = '[[:alnum:]_!#$%&\'*+\/=?^`{|}~-]+';
        $dotatom = '(\.' . $atom . ')*';
        $address = '(^' . $atom . $dotatom . '$)';
        $char = '([^\\\\"])';
        $esc = '(\\\\[\\\\"])';
        $text = '(' . $char . '|' . $esc . ')+';
        $quoted = '(^"' . $text . '"$)';
        $local_part = '/' . $address . '|' . $quoted . '/';
        $local_match = preg_match($local_part, $parts[0]);
        if ($local_match === false || $local_match != 1){
            return false;
        }
        
        $hostname = '([[:alnum:]]([-[:alnum:]]{0,62}[[:alnum:]])?)';
        $hostnames = '(' . $hostname . '(\.' . $hostname . ')*)';
        $top = '\.[[:alnum:]]{2,6}';
        $domain_part = '/^' . $hostnames . $top . '$/';
        $domain_match = preg_match($domain_part, $parts[1]);
        if ($domain_match === false | $domain_match != 1){
            return false;
        }
        
        return true;
    }
    
    public function sendMail($address, $body){
        //turn off error reporting
        error_reporting( 0 );
        
        require_once('c:\xampp\php\Mail\Mail.php');
        
        //make sure auth reads as 'true' and not '1' :: Code provided by Prof. Graziano
        if(array_key_exists( self::AUTH, $this->emailArray )) {
            $this->emailArray[ self::AUTH ] = ( bool ) $this->emailArray[ self::AUTH ];
        }
        
        $mailer = Mail::factory('smtp', $this->emailArray);
        
        $headers = array();
        $headers['From'] = 'ebavery@gmail.com';
        $headers['To'] = $address;
        $headers['Subject'] = "F/X Calculation";
               
        $result = $mailer->send($address, $headers, $body);
        
        if (PEAR::isError($result)){
            $err_msg = $result->getMessage();
        }
        else {
            $err_msg = "";
        }
        return $err_msg;
    }
}
