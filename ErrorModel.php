<?php

/**
 * ErrorModel returns error URL
 *
 * @author Elizabeth Avery
 */

require_once("IError.php");

class ErrorModel  {    
    
    public static function getErrorURL($msg){
        return header(IError::URL_KEY . IError::ERR_MSG_KEY . '=' . urlencode($msg));
    }
}
